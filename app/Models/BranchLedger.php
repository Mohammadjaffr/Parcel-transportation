<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_code',
        'related_branch_code',
        'shipment_id',
        'type',
        'debit',
        'credit',
        'description',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    /**
     * The branch this ledger entry belongs to
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_code', 'code');
    }

    /**
     * The counterparty branch
     */
    public function relatedBranch()
    {
        return $this->belongsTo(Branch::class, 'related_branch_code', 'code');
    }

    /**
     * The shipment this entry is for
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Scope: Filter by branch code
     */
    public function scopeForBranch($query, string $branchCode)
    {
        return $query->where('branch_code', $branchCode);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Calculate net balance for a branch (Credit - Debit)
     * Positive = branch is owed money, Negative = branch owes money
     */
    public static function getNetBalanceForBranch(string $branchCode): float
    {
        $totals = self::where('branch_code', $branchCode)
            ->selectRaw('SUM(credit) as total_credit, SUM(debit) as total_debit')
            ->first();

        return ($totals->total_credit ?? 0) - ($totals->total_debit ?? 0);
    }

    /**
     * Get balance between two specific branches
     */
    public static function getBalanceBetweenBranches(string $branchCode, string $relatedBranchCode): float
    {
        $totals = self::where('branch_code', $branchCode)
            ->where('related_branch_code', $relatedBranchCode)
            ->selectRaw('SUM(credit) as total_credit, SUM(debit) as total_debit')
            ->first();

        return ($totals->total_credit ?? 0) - ($totals->total_debit ?? 0);
    }
}
