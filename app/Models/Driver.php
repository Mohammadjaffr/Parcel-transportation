<?php

// معتمد
namespace App\Models;

use App\Traits\BelongsToApp;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{

    use HasFactory, BelongsToApp, HasUuid;
    protected $fillable = [
        'name',
        'phone',

        'is_banned',

        'vehicle_type',

        'app_id',
        'branch_id',
        'created_by',
        'uuid',
    ];

    protected $casts = [
        'is_banned' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_banned', false);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ========================
    // العلاقات المالية
    // ========================

    public function transactions()
    {
        return $this->hasMany(DriverTransaction::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    // ========================
    // الـ Accessors المحاسبية
    // ========================

    /**
     * إجمالي ما للسائق / ما سدده (credit)
     */
    public function getTotalCreditAttribute()
    {
        return (float) $this->transactions()->where('type', 'credit')->sum('amount');
    }

    /**
     * إجمالي ما على السائق / ما استلمه (debit)
     */
    public function getTotalDebitAttribute()
    {
        return (float) $this->transactions()->where('type', 'debit')->sum('amount');
    }

    /**
     * الرصيد الصافي = total_credit - total_debit
     * سالب = على السائق (مدين للمكتب)
     * موجب = للسائق (دائن على المكتب)
     */
    public function getBalanceAttribute()
    {
        return $this->total_credit - $this->total_debit;
    }
}
