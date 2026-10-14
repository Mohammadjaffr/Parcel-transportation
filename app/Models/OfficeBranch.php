<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'name',
        'phone',
        'address',
        'city',
        'map_link',
    ];
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function senderShipments()
    {
        return $this->hasMany(Shipment::class, 'sender_office_branch_id');
    }

    public function receiverShipments()
    {
        return $this->hasMany(Shipment::class, 'receiver_office_branch_id');
    }

    public function senderShipmentPackages()
    {
        return $this->hasMany(ShipmentPackage::class, 'sender_office_branch_id');
    }

    public function getCanDeleteAttribute(): bool
    {
        $senderShipmentsCount = $this->sender_shipments_count ?? $this->senderShipments()->count();
        $receiverShipmentsCount = $this->receiver_shipments_count ?? $this->receiverShipments()->count();
        $packagesCount = $this->sender_shipment_packages_count ?? $this->senderShipmentPackages()->count();

        return ($senderShipmentsCount + $receiverShipmentsCount + $packagesCount) === 0;
    }
}
