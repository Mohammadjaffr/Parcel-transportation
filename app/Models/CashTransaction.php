<?php

namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class CashTransaction extends Model
{
    use HasFactory, BelongsToApp;

    protected $table = 'cash_transactions';

    protected $fillable = [
        'app_id',
        'branch_id',
        'user_id',
        'cash_category_id',
        'type',
        'payment_method',
        'receipt_number',
        'amount',
        'transaction_date',
        'source_type',
        'source_id',
        'reference_number',
        'attachment_path',
        'notes',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
    ];

    /**
     * علاقات النموذج
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CashCategory::class, 'cash_category_id');
    }

    /**
     * علاقة بوليمورفية بمصدر المعاملة (شحنة، دفعة عميل، طرد بيان استلام)
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * نطاقات الاستعلام (Scopes)
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    public function scopeCash(Builder $query): Builder
    {
        return $query->where('payment_method', 'cash');
    }

    public function scopeBankTransfer(Builder $query): Builder
    {
        return $query->where('payment_method', 'bank_transfer');
    }

    /**
     * توليد رقم سند متسلسل ذري لكل شركة يمنع التكرار وسباق التزامن
     */
    public static function generateReceiptNumber(int $appId, string $type): string
    {
        $year = now()->year;
        $prefix = ($type === 'income') ? 'REC' : 'PAY';

        $lastNumber = DB::table('cash_transactions')
            ->where('app_id', $appId)
            ->where('receipt_number', 'like', "{$prefix}-{$year}-%")
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('receipt_number');

        $sequence = 1;
        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $sequence = intval(end($parts)) + 1;
        }

        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }
}