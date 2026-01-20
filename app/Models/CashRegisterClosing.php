<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_code',
        'closed_by',
        'expected_balance',
        'actual_cash',
        'difference',
        'transferred_amount',
        'remaining_cash',
        'notes',
    ];

    protected $casts = [
        'expected_balance' => 'decimal:2',
        'actual_cash' => 'decimal:2',
        'difference' => 'decimal:2',
        'transferred_amount' => 'decimal:2',
        'remaining_cash' => 'decimal:2',
    ];

    /**
     * Get the branch this closing belongs to
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_code', 'code');
    }

    /**
     * Get the user who performed the closing
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
