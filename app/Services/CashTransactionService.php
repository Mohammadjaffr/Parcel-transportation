<?php

namespace App\Services;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Passengers;
use App\Models\Shipment;
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

        // جلب أو إنشاء فئة عمولة الشحنات لهذا المستأجر
        $category = CashCategory::withoutGlobalScopes()
            ->where('app_id', $appId)
            ->where('name', 'عمولة شحنة')
            ->first();

        if (!$category) {
            $category = CashCategory::withoutGlobalScopes()
                ->where('app_id', $appId)
                ->where('type', 'income')
                ->first();
        }

        if (!$category) {
            $category = CashCategory::create([
                'app_id'    => $appId,
                'name'      => 'عمولة شحنة',
                'type'      => 'income',
                'is_active' => true,
            ]);
        }

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

        \Log::channel('stack')->info('[COMMISSION] بدء معالجة عمولة الراكب', [
            'passenger_id'      => $passenger->id,
            'passenger_number'  => $passenger->passenger_number,
            'commission_amount' => $office_commission,
            'branch_id'         => $passenger->branch_id,
            'status'            => $passenger->status,
        ]);

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
                'reference_number' => (string) ($passenger->passenger_number ?? $passenger->uuid),
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
}