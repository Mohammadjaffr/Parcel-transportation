<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\Shipment;
use Exception;

class TransactionService
{
    /**
     * Record a shipment payment as a transaction
     * 
     * This method is called when a shipment is paid to automatically
     * create an income transaction for the branch.
     * 
     * @param Shipment $shipment The shipment being paid
     * @param float $amount The payment amount
     * @param string $branchCode The branch code receiving the payment
     * @return Transaction
     * @throws Exception
     */
    public static function recordShipmentPayment(Shipment $shipment, float $amount, string $branchCode): Transaction
    {
        // Find the SHIPMENT_PAYMENT category
        $category = TransactionCategory::where('code', 'SHIPMENT_PAYMENT')
            ->where('is_active', true)
            ->first();

        if (!$category) {
            throw new Exception('SHIPMENT_PAYMENT transaction category not found. Please run the TransactionCategorySeeder.');
        }

        // Create the transaction
        $transaction = Transaction::create([
            'branch_code' => $branchCode,
            'transaction_category_id' => $category->id,
            'amount' => $amount,
            'description' => "Payment received for shipment #{$shipment->bond_number}",
            'created_by' => auth()->id() ?? 1, // Fallback to system user if no auth
            'shipment_id' => $shipment->id,
        ]);

        return $transaction;
    }

    /**
     * Calculate the total balance for a specific branch
     * 
     * @param string $branchCode
     * @return float
     */
    public static function calculateBranchBalance(string $branchCode): float
    {
        $income = Transaction::where('branch_code', $branchCode)
            ->whereHas('category', function ($query) {
                $query->where('type', 'in');
            })
            ->sum('amount');

        $expense = Transaction::where('branch_code', $branchCode)
            ->whereHas('category', function ($query) {
                $query->where('type', 'out');
            })
            ->sum('amount');

        return $income - $expense;
    }

    /**
     * Get income total for a branch
     * 
     * @param string $branchCode
     * @return float
     */
    public static function getBranchIncome(string $branchCode): float
    {
        return Transaction::where('branch_code', $branchCode)
            ->whereHas('category', function ($query) {
                $query->where('type', 'in');
            })
            ->sum('amount');
    }

    /**
     * Get expense total for a branch
     * 
     * @param string $branchCode
     * @return float
     */
    public static function getBranchExpense(string $branchCode): float
    {
        return Transaction::where('branch_code', $branchCode)
            ->whereHas('category', function ($query) {
                $query->where('type', 'out');
            })
            ->sum('amount');
    }
}
