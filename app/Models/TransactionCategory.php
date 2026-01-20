<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all transactions for this category
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Scope: Filter active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter income categories (type = 'in')
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope: Filter expense categories (type = 'out')
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'out');
    }
}
