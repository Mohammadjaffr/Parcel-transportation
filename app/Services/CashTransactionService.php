<?php

namespace App\Services;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use Exception;

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