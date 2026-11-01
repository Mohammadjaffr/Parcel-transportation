<?php

namespace App\Services;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Driver;
use App\Models\DriverTransaction;
use Exception;
use Illuminate\Support\Facades\DB;

class DriverTransactionService
{
    protected CashTransactionService $cashService;

    public function __construct(CashTransactionService $cashService)
    {
        $this->cashService = $cashService;
    }

    /**
     * تسجيل قيد كشف على السائق (مثل عهدة، مديونية) مع دفعة جزئية اختيارية
     *
     * مثال: على السائق 30,000 ولديه 10,000 فقط
     * → يتم تسجيل 30,000 كقيد debit + 10,000 كقيد credit مرتبط بسند قبض في الصندوق
     */
    public function recordManifestEntry(
        Driver $driver,
        float $totalAmount,
        string $type,
        string $description,
        float $paidNow = 0,
        string $paymentMethod = 'cash',
        ?int $shipmentPackageId = null,
        ?int $passengerTripId = null,
        ?string $referenceNumber = null
    ): DriverTransaction {
        return DB::transaction(function () use ($driver, $totalAmount, $type, $description, $paidNow, $paymentMethod, $shipmentPackageId, $passengerTripId, $referenceNumber) {
            $user = auth()->user();

            // 1. تسجيل المبلغ الكامل كقيد رئيسي
            $mainTransaction = DriverTransaction::create([
                'app_id' => $user->app_id,
                'branch_id' => $user->branch_id,
                'driver_id' => $driver->id,
                'shipment_package_id' => $shipmentPackageId,
                'passenger_trip_id' => $passengerTripId,
                'type' => $type,
                'amount' => $totalAmount,
                'description' => $description,
                'created_by' => $user->id,
            ]);

            // 2. إذا كان هناك مبلغ مدفوع الآن والقيد الرئيسي من نوع debit (عليه)
            if ($paidNow > 0 && $type === 'debit') {
                $this->receivePaymentFromDriver(
                    $driver,
                    $paidNow,
                    $paymentMethod,
                    'سداد جزئي مقابل: ' . $description,
                    $referenceNumber
                );
            }

            return $mainTransaction;
        });
    }

    /**
     * قبض مبلغ من السائق (سداد مديونية)
     * → ينشئ سند قبض (income) في الصندوق + قيد دائن (credit) في ذمم السائق
     */
    public function receivePaymentFromDriver(
        Driver $driver,
        float $amount,
        string $paymentMethod = 'cash',
        ?string $notes = null,
        ?string $referenceNumber = null
    ): DriverTransaction {
        return DB::transaction(function () use ($driver, $amount, $paymentMethod, $notes, $referenceNumber) {
            $user = auth()->user();
            $appId = $user->app_id;

            // إيجاد أو إنشاء تصنيف "توريد / سداد سائق"
            $category = CashCategory::withoutGlobalScopes()->firstOrCreate(
                [
                    'app_id' => $appId,
                    'name' => 'توريد / سداد سائق',
                ],
                [
                    'type' => 'income',
                    'is_active' => true,
                ]
            );

            // إنشاء سند قبض في الصندوق
            $cashTransaction = $this->cashService->recordIncome([
                'app_id' => $appId,
                'branch_id' => $user->branch_id,
                'user_id' => $user->id,
                'cash_category_id' => $category->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'driver_id' => $driver->id,
                'notes' => 'سداد من السائق: ' . $driver->name . ($notes ? ' - ' . $notes : ''),
                'transaction_date' => now()->toDateString(),
            ]);

            // إنشاء قيد دائن (credit) في ذمم السائق
            $driverTransaction = DriverTransaction::create([
                'app_id' => $appId,
                'branch_id' => $user->branch_id,
                'driver_id' => $driver->id,
                'cash_transaction_id' => $cashTransaction->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => $notes ?? 'سداد من السائق',
                'created_by' => $user->id,
            ]);

            return $driverTransaction;
        });
    }

    /**
     * صرف مبلغ للسائق (مستحقات)
     * → يتحقق من رصيد الصندوق أولاً
     * → ينشئ سند صرف (expense) في الصندوق + قيد مدين (debit) في ذمم السائق
     */
    public function payBalanceToDriver(
        Driver $driver,
        float $amount,
        string $paymentMethod = 'cash',
        ?string $notes = null,
        ?string $referenceNumber = null
    ): DriverTransaction {
        return DB::transaction(function () use ($driver, $amount, $paymentMethod, $notes, $referenceNumber) {
            $user = auth()->user();
            $appId = $user->app_id;
            $branchId = $user->branch_id;

            // التحقق من رصيد الصندوق عند الدفع النقدي
            if ($paymentMethod === 'cash') {
                $branchBalance = $this->cashService->getBranchCashBalance($branchId);

                if ($branchBalance < $amount) {
                    throw new Exception(
                        'رصيد الصندوق غير كافٍ. الرصيد الحالي: ' . number_format($branchBalance, 0) .
                        ' ر.ي والمطلوب صرفه: ' . number_format($amount, 0) . ' ر.ي'
                    );
                }
            }

            // إيجاد أو إنشاء تصنيف "صرف مستحقات سائق"
            $category = CashCategory::withoutGlobalScopes()->firstOrCreate(
                [
                    'app_id' => $appId,
                    'name' => 'صرف مستحقات سائق',
                ],
                [
                    'type' => 'expense',
                    'is_active' => true,
                ]
            );

            // إنشاء سند صرف في الصندوق
            $cashTransaction = $this->cashService->recordExpense([
                'app_id' => $appId,
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'cash_category_id' => $category->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'driver_id' => $driver->id,
                'notes' => 'صرف مستحقات للسائق: ' . $driver->name . ($notes ? ' - ' . $notes : ''),
                'transaction_date' => now()->toDateString(),
            ]);

            // إنشاء قيد مدين (debit) في ذمم السائق (يسجّل الصرف)
            $driverTransaction = DriverTransaction::create([
                'app_id' => $appId,
                'branch_id' => $branchId,
                'driver_id' => $driver->id,
                'cash_transaction_id' => $cashTransaction->id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => $notes ?? 'صرف مستحقات للسائق',
                'created_by' => $user->id,
            ]);

            return $driverTransaction;
        });
    }
}

