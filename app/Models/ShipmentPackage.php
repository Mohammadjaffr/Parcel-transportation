<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentPackage extends Model
{
    protected $table = 'shipment_packages';

    protected $fillable = [
        'tracking_number',
        'driver_name',
        'driver_phone',
    ];
    protected static function booted()
    {
        static::creating(function ($package) {
            // توليد رقم فريد: بادئة + التاريخ + رقم عشوائي
            // مثال النتيجة: TRIP-251230-A8B2
            $package->tracking_number = 'TRIP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            // التأكد من عدم تكراره في قاعدة البيانات (نادراً ما يحدث)
            while (static::where('tracking_number', $package->tracking_number)->exists()) {
                $package->tracking_number = 'TRIP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
            }
        });
    }
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'shipment_package_id');
    }

    /**
     * العلاقة many-to-many مع الفروع المستقبلة
     */
    public function receiverBranches()
    {
        return $this->belongsToMany(Branch::class, 'branch_shipment_package', 'shipment_package_id', 'branch_code')
            ->withPivot('status', 'arrival_date', 'notes')
            ->withTimestamps();
    }
}
