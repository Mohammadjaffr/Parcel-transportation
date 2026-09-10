<?php

namespace App\Models;

use App\Models\CashTransaction;
use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashCategory extends Model
{
    use HasFactory, BelongsToApp;

    protected $table = 'cash_categories';

    protected $fillable = [
        'app_id',
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * الحركات المالية المرتبطة بهذا التصنيف
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class, 'cash_category_id');
    }

    /**
     * نطاقات الاستعلام (Scopes)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    /**
     * زراعة التصنيفات الافتراضية عند تسجيل مكتب أو شركة جديدة
     */
    public static function seedDefaultCategoriesForApp(int $appId): void
    {
        $defaults = [
            ['name' => 'تحصيل شحنة', 'type' => 'income'],
        ];

        foreach ($defaults as $cat) {
            self::firstOrCreate(
                [
                    'app_id' => $appId,
                    'name'   => $cat['name'],
                    'type'   => $cat['type'],
                ],
                [
                    'is_active' => true,
                ]
            );
        }
    }
}