<?php

namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Models\PassengerTrip;
use App\Models\Passengers;
use App\Models\Driver;

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

    /**
     * رقم السند المالي بدون البادئة والأصفار (عرض السنة والرقم فقط، مثل 2026-1 أو 2026-8)
     */
    public function getFormattedReceiptNumberAttribute(): string
    {
        if (empty($this->receipt_number)) {
            return '';
        }

        $parts = explode('-', $this->receipt_number);
        if (count($parts) >= 3) {
            return $parts[1] . '-' . intval(end($parts));
        }

        if (count($parts) === 2) {
            return $parts[0] . '-' . intval($parts[1]);
        }

        return (string) $this->receipt_number;
    }

    /**
     * كائن السائق المرتبط بالسند المالي إن وجد (إذا كان السند مرتبطاً بشحنة أو حمولة أو رحلة ركاب)
     */
    public function getDriverAttribute(): ?Driver
    {
        // 1. الفحص عبر العلاقة البوليمورفية المباشرة (source)
        $source = $this->relationLoaded('source') ? $this->source : ($this->source_type && $this->source_id ? $this->source : null);
        if ($source) {
            $driver = $this->resolveDriverFromSource($source);
            if ($driver) {
                return $driver;
            }
        }

        // 2. كاحتياطي ذكي: البحث عبر رقم المرجع (reference_number)
        if (!empty($this->reference_number)) {
            $ref = trim($this->reference_number);

            // أ) إذا كان المرجع لحمولة طرود (يبدأ بـ PKG-)
            if (str_starts_with($ref, 'PKG-')) {
                $pkg = ShipmentPackage::withoutGlobalScopes()->where('tracking_number', $ref)->with('driver')->first();
                if ($pkg?->driver) {
                    return $pkg->driver;
                }
            }

            // ب) إذا كان المرجع لشحنة / طرد
            $shipment = Shipment::withoutGlobalScopes()->where('bond_number', $ref)->with('package.driver')->first();
            if ($shipment?->package?->driver) {
                return $shipment->package->driver;
            }

            // ج) إذا كان المرجع لرحلة ركاب
            if (is_numeric($ref)) {
                $trip = PassengerTrip::withoutGlobalScopes()->where('id', $ref)->with('driver')->first();
                if ($trip?->driver) {
                    return $trip->driver;
                }
            }

            // د) إذا كان المرجع لراكب
            $passenger = Passengers::withoutGlobalScopes()->where('passenger_number', $ref)->with(['driver', 'trip.driver'])->first();
            if ($passenger) {
                return $passenger->driver ?? $passenger->trip?->driver;
            }
        }

        return null;
    }

    /**
     * استخراج كائن السائق من كائن المصدر
     */
    protected function resolveDriverFromSource($source): ?Driver
    {
        if ($source instanceof ShipmentPackage) {
            return $source->driver;
        }

        if ($source instanceof Shipment) {
            return $source->package?->driver;
        }

        if ($source instanceof PassengerTrip) {
            return $source->driver;
        }

        if ($source instanceof Passengers) {
            return $source->driver ?? $source->trip?->driver;
        }

        return null;
    }

    /**
     * اسم السائق
     */
    public function getDriverNameAttribute(): ?string
    {
        return $this->driver?->name;
    }

    /**
     * رقم هاتف السائق
     */
    public function getDriverPhoneAttribute(): ?string
    {
        return $this->driver?->phone;
    }
}