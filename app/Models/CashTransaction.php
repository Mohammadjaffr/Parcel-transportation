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

    /**
     * معلومات الكيان المرتبط بالسند (شحنة، رحلة، راكب، أو مرجع عام)
     * يُرجع مصفوفة تحتوي على: type, id, label, url
     */
    public function getRelatedEntityAttribute(): ?array
    {
        $source = $this->relationLoaded('source') ? $this->source : ($this->source_type && $this->source_id ? $this->source : null);
        $sourceType = $this->source_type ?? ($source ? get_class($source) : null);
        $sourceId = $this->source_id ?? $source?->id;
        $ref = $this->reference_number ? trim($this->reference_number) : null;
        $notes = $this->notes ? (string) $this->notes : '';

        // 1. رحلة ركاب (PassengerTrip)
        if ($source instanceof PassengerTrip || $sourceType === PassengerTrip::class || str_contains($notes, 'عمولة الرحلة')) {
            $tripId = $sourceId ?? ($source?->id ?? ($ref && is_numeric($ref) ? (int) $ref : null));
            if (!$tripId && preg_match('/الرحلة\s*(?:رقم\s*)?(\d+)/u', $notes, $matches)) {
                $tripId = (int) $matches[1];
            }
            if ($tripId) {
                return [
                    'type'   => 'trip',
                    'id'     => $tripId,
                    'label'  => 'رحلة رقم ' . $tripId,
                    'url'    => \Illuminate\Support\Facades\Route::has('trips.show') ? route('trips.show', $tripId) : null,
                ];
            }
        }

        // 2. إرسالية / حمولة طرود (ShipmentPackage)
        if ($source instanceof ShipmentPackage || $sourceType === ShipmentPackage::class || str_contains($notes, 'عمولة الحمولة') || ($ref && str_starts_with($ref, 'PKG-'))) {
            $packageId = $sourceId ?? $source?->id;
            if (!$packageId && $ref) {
                $parts = explode('-', $ref);
                $lastPart = end($parts);
                if (is_numeric($lastPart)) {
                    $packageId = (int) $lastPart;
                }
            }
            if (!$packageId && preg_match('/الحمولة\s*(?:رقم\s*)?PKG-[A-Za-z0-9]+-(\d+)/u', $notes, $matches)) {
                $packageId = (int) $matches[1];
            }
            if ($packageId) {
                return [
                    'type'   => 'shipment',
                    'id'     => $packageId,
                    'label'  => 'شحنة رقم ' . $packageId,
                    'url'    => \Illuminate\Support\Facades\Route::has('shipmentpackage.outgoing.show')
                                ? route('shipmentpackage.outgoing.show', $packageId)
                                : (\Illuminate\Support\Facades\Route::has('shipmentpackage.show') ? route('shipmentpackage.show', $packageId) : null),
                ];
            }
        }

        // 3. شحنة فردية (Shipment)
        if ($source instanceof Shipment || $sourceType === Shipment::class || str_contains($notes, 'عمولة الشحنة') || str_contains($notes, 'قيمة الشحنة')) {
            $shipmentNumber = $source?->bond_number_only ?? ($source?->bond_number ?? $sourceId ?? $ref);
            if (!$shipmentNumber && preg_match('/الشحنة\s*(?:رقم\s*)?([A-Za-z0-9\-]+)/u', $notes, $matches)) {
                $shipmentNumber = $matches[1];
            }
            if ($shipmentNumber) {
                $shipmentId = $source?->id ?? $sourceId;
                return [
                    'type'   => 'shipment',
                    'id'     => $shipmentNumber,
                    'label'  => 'شحنة رقم ' . $shipmentNumber,
                    'url'    => $shipmentId && \Illuminate\Support\Facades\Route::has('shipments.show') ? route('shipments.show', $shipmentId) : null,
                ];
            }
        }

        // 4. راكب (Passengers)
        if ($source instanceof Passengers || $sourceType === Passengers::class || str_contains($notes, 'عمولة الراكب')) {
            $passengerNumber = $source?->passenger_number ?? $ref ?? $sourceId;
            if ($passengerNumber) {
                return [
                    'type'   => 'passenger',
                    'id'     => $passengerNumber,
                    'label'  => 'راكب رقم ' . $passengerNumber,
                    'url'    => null,
                ];
            }
        }

        // 5. مرجع عام
        if ($ref) {
            return [
                'type'   => 'reference',
                'id'     => $ref,
                'label'  => 'مرجع: ' . $ref,
                'url'    => null,
            ];
        }

        return null;
    }
}