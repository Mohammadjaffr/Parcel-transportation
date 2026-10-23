<?php

namespace App\Services;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Passengers;
use App\Models\PassengerTrip;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use Exception;
use Illuminate\Support\Facades\DB;

class CashTransactionService
{
    /**
     * حساب رصيد الصندوق الفعلي المتوفر بالكاش (الدرج) لفرع معين
     */
    public function getBranchCashBalance(int $branchId, ?string $toDate = null, string $paymentMethod = 'cash'): float
    {
        $query = CashTransaction::where('branch_id', $branchId)
            ->where('payment_method', $paymentMethod);

        if ($toDate) {
            $query->whereDate('transaction_date', '<=', $toDate);
        }

        $balance = $query->selectRaw("
            COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) -
            COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as balance
        ")->value('balance');

        return (float) ($balance ?? 0.00);
    }

    /**
     * تسجيل سند وارد (قبض)
     */
    public function recordIncome(array $data): CashTransaction
    {
        return DB::transaction(function () use ($data) {
            $appId = auth()->user()->app_id ?? $data['app_id'];

            return CashTransaction::create([
                'app_id'           => $appId,
                'branch_id'        => $data['branch_id'],
                'user_id'          => auth()->id() ?? $data['user_id'] ?? 1,
                'cash_category_id' => $data['cash_category_id'],
                'type'             => 'income',
                'payment_method'   => $data['payment_method'] ?? 'cash',
                'receipt_number'   => CashTransaction::generateReceiptNumber($appId, 'income'),
                'amount'           => $data['amount'],
                'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
                'source_type'      => $data['source_type'] ?? null,
                'source_id'        => $data['source_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'attachment_path'  => $data['attachment_path'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ]);
        });
    }

    public function recordShipmentCommission(Shipment $shipment): ?CashTransaction
    {
        $commissionAmount = (float) $shipment->total_commission;

        if ($commissionAmount <= 0 || !$shipment->sender_branch_id) {
            return null;
        }

        // استخراج معرف المستأجر من الشحنة أو الفرع أو المستخدم
        $appId = $shipment->app_id 
            ?? $shipment->senderBranch?->app_id 
            ?? auth()->user()?->app_id;

        if (!$appId) {
            return null;
        }

        // الحماية من التكرار المالي لنفس الشحنة (Idempotency)
        $alreadyRecorded = CashTransaction::withoutGlobalScopes()
            ->where('app_id', $appId)
            ->where('source_type', Shipment::class)
            ->where('source_id', $shipment->id)
            ->where('type', 'income')
            ->where('notes', 'like', '%عمولة الشحنة%')
            ->exists();

        if ($alreadyRecorded) {
            return null;
        }

        // جلب أو إنشاء فئة عمولة الشحنات لهذا المستأجر (بدون fallback عشوائي)
        $category = CashCategory::withoutGlobalScopes()->firstOrCreate(
            [
                'app_id' => $appId,
                'name'   => 'عمولة شحنة',
            ],
            [
                'type'      => 'income',
                'is_active' => true,
            ]
        );

        return $this->recordIncome([
            'app_id'           => $appId,
            'branch_id'        => $shipment->sender_branch_id,
            'user_id'          => auth()->id() ?? $shipment->created_by,
            'cash_category_id' => $category->id,
            'amount'           => $commissionAmount,
            'payment_method'   => 'cash',
            'reference_number' => $shipment->bond_number,
            'source_type'      => Shipment::class,
            'source_id'        => $shipment->id,
            'notes'            => "تسقيط عمولة الشحنة رقم {$shipment->bond_number}",
            'transaction_date' => now()->toDateString(),
        ]);
    }
    public function recordPassengerCommission(Passengers $passenger): ?CashTransaction
    {
        $office_commission = (float) $passenger->office_commission;

        if ($office_commission <= 0 || !$passenger->branch_id) {
            \Log::channel('stack')->warning('[COMMISSION] رجع null — العمولة صفر أو branch_id فارغ', [
                'commission_amount' => $office_commission,
                'branch_id'         => $passenger->branch_id,
            ]);
            return null;
        }

        $appId = $passenger->app_id
            ?? $passenger->branch?->app_id
            ?? optional($passenger->branch()->first())->app_id
            ?? auth()->user()?->app_id;

        // منع التكرار المالي لنفس الراكب (Idempotency)
        $alreadyRecorded = CashTransaction::withoutGlobalScopes()
            ->where('app_id', $appId)
            ->where('source_type', Passengers::class)
            ->where('source_id', $passenger->id)
            ->where('type', 'income')
            ->where('notes', 'like', '%عمولة الراكب%')
            ->exists();


        // فئة عمولة الركاب — إيجاد أو إنشاء تصنيف خاص بالركاب فقط
        $category = CashCategory::withoutGlobalScopes()->firstOrCreate(
            [
                'app_id' => $appId,
                'name'   => 'عمولة ركاب',
            ],
            [
                'type'      => 'income',
                'is_active' => true,
            ]
        );
        try {
            $result = $this->recordIncome([
                'app_id'           => $appId,
                'branch_id'        => $passenger->branch_id,
                'user_id'          => auth()->id() ?? 1,
                'cash_category_id' => $category->id,
                'amount'           => $office_commission,
                'payment_method'   => 'cash',
                'reference_number' => $passenger->passenger_number,
                'source_type'      => Passengers::class,
                'source_id'        => $passenger->id,
                'notes'            => "تسقيط عمولة الراكب رقم {$passenger->passenger_number}",
                'transaction_date' => now()->toDateString(),
            ]);

            return $result;

        } catch (\Exception $e) {
            \log::error($e->getMessage());
            return null;
        }
    }

    /**
     * تسجيل سند منصرف (صرف) مع التحقق الفوري من السيولة
     */
    public function recordExpense(array $data): CashTransaction
    {
        return DB::transaction(function () use ($data) {
            $appId = auth()->user()->app_id ?? $data['app_id'];
            $branchId = $data['branch_id'];
            $paymentMethod = $data['payment_method'] ?? 'cash';

            return CashTransaction::create([
                'app_id'           => $appId,
                'branch_id'        => $branchId,
                'user_id'          => auth()->id() ?? $data['user_id'] ?? 1,
                'cash_category_id' => $data['cash_category_id'],
                'type'             => 'expense',
                'payment_method'   => $paymentMethod,
                'receipt_number'   => CashTransaction::generateReceiptNumber($appId, 'expense'),
                'amount'           => $data['amount'],
                'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
                'source_type'      => $data['source_type'] ?? null,
                'source_id'        => $data['source_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'attachment_path'  => $data['attachment_path'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * تكامل آلي لتحصيل قيمة شحنة وإيداعها في الصندوق
     */
    public function recordShipmentPayment(
        Shipment $shipment,
        float $amount,
        int $branchId,
        string $paymentMethod = 'cash',
        ?string $referenceNumber = null
    ): CashTransaction {
        $appId = auth()->user()->app_id ?? $shipment->app_id;

        $category = CashCategory::where('app_id', $appId)
            ->where('name', 'تحصيل شحنة')
            ->first();

        if (!$category) {
            $category = CashCategory::where('app_id', $appId)->income()->firstOrFail();
        }

        return $this->recordIncome([
            'app_id'           => $appId,
            'branch_id'        => $branchId,
            'cash_category_id' => $category->id,
            'amount'           => $amount,
            'payment_method'   => $paymentMethod,
            'reference_number' => $referenceNumber,
            'source_type'      => Shipment::class,
            'source_id'        => $shipment->id,
            'notes'            => "تحصيل قيمة الشحنة رقم {$shipment->tracking_number}",
            'transaction_date' => now()->toDateString(),
        ]);
    }

    // ======================================================================
    // دوال تسقيط العمولة على مستوى الحمولة (ShipmentPackage)
    // ======================================================================

    /**
     * تسقيط إجمالي عمولة جميع طرود الحمولة في قيد واحد
     * يُستدعى عند تحويل حالة الحمولة إلى in_transit
     */
    public function recordPackageCommission(ShipmentPackage $package): ?CashTransaction
    {
        // حساب إجمالي عمولة جميع الطرود في الحمولة
        $totalCommission = (float) Shipment::where('shipment_package_id', $package->id)
            ->sum('total_commission');

        if ($totalCommission <= 0 || !$package->sender_branch_id) {
            return null;
        }

        $appId = $package->app_id
            ?? $package->senderBranch?->app_id
            ?? auth()->user()?->app_id;

        if (!$appId) {
            return null;
        }

        // الحماية من التكرار المالي (Idempotency)
        $alreadyRecorded = CashTransaction::withoutGlobalScopes()
            ->where('app_id', $appId)
            ->where('source_type', ShipmentPackage::class)
            ->where('source_id', $package->id)
            ->where('type', 'income')
            ->where('notes', 'like', '%عمولة الحمولة%')
            ->exists();

        if ($alreadyRecorded) {
            return null;
        }

        // جلب أو إنشاء فئة عمولة الشحنات (بدون fallback عشوائي لتجنب خلط التصنيفات)
        $category = CashCategory::withoutGlobalScopes()->firstOrCreate(
            [
                'app_id' => $appId,
                'name'   => 'عمولة شحنة',
            ],
            [
                'type'      => 'income',
                'is_active' => true,
            ]
        );

        return $this->recordIncome([
            'app_id'           => $appId,
            'branch_id'        => $package->sender_branch_id,
            'user_id'          => auth()->id() ?? $package->created_by,
            'cash_category_id' => $category->id,
            'amount'           => $totalCommission,
            'payment_method'   => 'cash',
            'reference_number' => $package->tracking_number,
            'source_type'      => ShipmentPackage::class,
            'source_id'        => $package->id,
            'notes'            => "تسقيط عمولة الحمولة رقم {$package->tracking_number}",
            'transaction_date' => now()->toDateString(),
        ]);
    }

    /**
     * تعديل مبلغ قيد عمولة الحمولة (عند إضافة/إزالة طرد)
     * يعيد حساب الإجمالي من الطرود المرتبطة ويحدّث القيد
     */
    public function adjustPackageCommission(ShipmentPackage $package): void
    {
        $appId = $package->app_id
            ?? $package->senderBranch?->app_id
            ?? auth()->user()?->app_id;

        if (!$appId) {
            return;
        }

        // إعادة حساب إجمالي العمولة من الطرود المتبقية
        $newTotal = (float) Shipment::where('shipment_package_id', $package->id)
            ->sum('total_commission');

        DB::transaction(function () use ($package, $appId, $newTotal) {
            $transaction = CashTransaction::withoutGlobalScopes()
                ->where('app_id', $appId)
                ->where('source_type', ShipmentPackage::class)
                ->where('source_id', $package->id)
                ->where('type', 'income')
                ->where('notes', 'like', '%عمولة الحمولة%')
                ->first();

            if ($transaction) {
                if ($newTotal <= 0) {
                    // حذف القيد إذا لم تتبقَ عمولة
                    $transaction->delete();
                } else {
                    $transaction->update(['amount' => $newTotal]);
                }
            } elseif ($newTotal > 0) {
                // إذا لم يكن هناك قيد موجود (تم حذفه سابقاً لوصول العمولة لصفر)، ننشئ قيداً جديداً
                $this->recordPackageCommission($package);
            }
        });
    }

    // ======================================================================
    // دوال تسقيط العمولة على مستوى رحلة الركاب (PassengerTrip)
    // ======================================================================

    /**
     * تسقيط إجمالي عمولة جميع ركاب الرحلة في قيد واحد
     * يُستدعى عند إنشاء الرحلة وربط الركاب
     */
    public function recordTripCommission(PassengerTrip $trip): ?CashTransaction
    {
        // حساب إجمالي عمولة جميع الركاب في الرحلة
        $totalCommission = (float) Passengers::where('trip_id', $trip->id)
            ->sum('office_commission');

        if ($totalCommission <= 0 || !$trip->branch_id) {
            return null;
        }

        $appId = $trip->app_id
            ?? $trip->branch?->app_id
            ?? auth()->user()?->app_id;

        if (!$appId) {
            return null;
        }

        // الحماية من التكرار المالي (Idempotency)
        $alreadyRecorded = CashTransaction::withoutGlobalScopes()
            ->where('app_id', $appId)
            ->where('source_type', PassengerTrip::class)
            ->where('source_id', $trip->id)
            ->where('type', 'income')
            ->where('notes', 'like', '%عمولة الرحلة%')
            ->exists();

        if ($alreadyRecorded) {
            return null;
        }

        // فئة عمولة الركاب — إيجاد أو إنشاء تصنيف خاص بالركاب
        $category = CashCategory::withoutGlobalScopes()->firstOrCreate(
            [
                'app_id' => $appId,
                'name'   => 'عمولة ركاب',
            ],
            [
                'type'      => 'income',
                'is_active' => true,
            ]
        );

        try {
            return $this->recordIncome([
                'app_id'           => $appId,
                'branch_id'        => $trip->branch_id,
                'user_id'          => auth()->id() ?? $trip->created_by,
                'cash_category_id' => $category->id,
                'amount'           => $totalCommission,
                'payment_method'   => 'cash',
                'reference_number' => $trip->id,
                'source_type'      => PassengerTrip::class,
                'source_id'        => $trip->id,
                'notes'            => "تسقيط عمولة الرحلة رقم {$trip->id}",
                'transaction_date' => now()->toDateString(),
            ]);
        } catch (\Exception $e) {
            \Log::error('[TRIP COMMISSION] فشل تسقيط عمولة الرحلة: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * تعديل مبلغ قيد عمولة الرحلة (عند إضافة/إزالة راكب)
     * يعيد حساب الإجمالي من الركاب المرتبطين ويحدّث القيد
     */
    public function adjustTripCommission(PassengerTrip $trip): void
    {
        $appId = $trip->app_id
            ?? $trip->branch?->app_id
            ?? auth()->user()?->app_id;

        if (!$appId) {
            return;
        }

        // إعادة حساب إجمالي العمولة من الركاب المتبقين
        $newTotal = (float) Passengers::where('trip_id', $trip->id)
            ->sum('office_commission');

        DB::transaction(function () use ($trip, $appId, $newTotal) {
            $transaction = CashTransaction::withoutGlobalScopes()
                ->where('app_id', $appId)
                ->where('source_type', PassengerTrip::class)
                ->where('source_id', $trip->id)
                ->where('type', 'income')
                ->where('notes', 'like', '%عمولة الرحلة%')
                ->first();

            if ($transaction) {
                if ($newTotal <= 0) {
                    // حذف القيد إذا لم تتبقَ عمولة
                    $transaction->delete();
                } else {
                    $transaction->update(['amount' => $newTotal]);
                }
            } elseif ($newTotal > 0) {
                // إذا لم يكن هناك قيد موجود (تم حذفه سابقاً لوصول العمولة لصفر)، ننشئ قيداً جديداً
                $this->recordTripCommission($trip);
            }
        });
    }

    /**
     * تحديث سند مالي يدوي بأمان داخل معاملة قاعدة بيانات
     */
    public function updateTransaction(CashTransaction $trx, array $data): CashTransaction
    {
        return DB::transaction(function () use ($trx, $data) {
            $updateData = [
                'cash_category_id' => $data['cash_category_id'],
                'amount'           => $data['amount'],
                'payment_method'   => $data['payment_method'],
                'transaction_date' => $data['transaction_date'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ];

            if (array_key_exists('branch_id', $data) && !empty($data['branch_id'])) {
                $updateData['branch_id'] = $data['branch_id'];
            }

            if (isset($data['attachment_path'])) {
                $updateData['attachment_path'] = $data['attachment_path'];
            }

            $trx->update($updateData);

            return $trx->fresh();
        });
    }
}