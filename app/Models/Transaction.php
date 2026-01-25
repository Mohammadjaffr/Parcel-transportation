<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'branch_code',
        'transaction_category_id',
        'amount',
        'description',
        'created_by',
        'customer_id',
        'shipment_id',
        'reference_number',
        'attachment_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the branch this transaction belongs to
     * CRITICAL: Uses custom foreign key 'branch_code' -> 'code'
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_code', 'code');
    }

    /**
     * Get the transaction category
     */
    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id');
    }

    /**
     * Get the user who created this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the related customer (optional)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the related shipment (optional)
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Scope: Filter by branch code
     */
    public function scopeForBranch($query, $branchCode)
    {
        return $query->where('branch_code', $branchCode);
    }

    /**
     * Scope: Filter income transactions
     */
    public function scopeIncome($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('type', 'in');
        });
    }

    /**
     * Scope: Filter expense transactions
     */
    public function scopeExpense($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('type', 'out');
        });
    }

    /**
     * Generate unique receipt number based on transaction type
     * Format: REC-YYYY-NNNNN for income, PAY-YYYY-NNNNN for expense
     */
    public static function generateReceiptNumber($transactionType)
    {
        $year = now()->year;
        $prefix = $transactionType === 'in' ? 'REC' : 'PAY';
        
        // Get the last receipt number for this type and year
        $lastTransaction = self::whereHas('category', function ($q) use ($transactionType) {
            $q->where('type', $transactionType);
        })
        ->where('receipt_number', 'like', "{$prefix}-{$year}-%")
        ->orderBy('receipt_number', 'desc')
        ->first();

        if ($lastTransaction && $lastTransaction->receipt_number) {
            // Extract the sequence number and increment
            $parts = explode('-', $lastTransaction->receipt_number);
            $sequence = intval($parts[2] ?? 0) + 1;
        } else {
            // Start from 1
            $sequence = 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }
}
