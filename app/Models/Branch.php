<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'code',
    ];

    public function sentShipments()
    {
        return $this->hasMany(Shipment::class, 'sender_branch_code', 'code');
    }

    public function receivedShipments()
    {
        return $this->hasMany(Shipment::class, 'receiver_branch_code', 'code');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * العلاقة many-to-many مع حزم الشحن المستقبلة
     */
    public function receivingPackages()
    {
        return $this->belongsToMany(ShipmentPackage::class, 'branch_shipment_package', 'branch_code', 'shipment_package_id')
            ->withPivot('status', 'arrival_date', 'notes')
            ->withTimestamps();
    }
}
