<?php

// معتمد
namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory,BelongsToApp;
    protected $table = 'branches';
    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'code',
        'app_id',
        'map_link',
        'is_main'
    ];
    // معتمد
    public function outgoingShipments()
    {
        return $this->hasMany(Shipment::class, 'sender_branch_id');
    }
    // معتمد
    public function incomingShipments()
    {
        return $this->hasMany(Shipment::class, 'receiver_branch_id');
    }
    // معتمد
    public function sentPackages()
    {
        return $this->hasMany(ShipmentPackage::class, 'sender_branch_id');
    }
    // معتمد
    public function receivedPackages()
    {
        return $this->hasMany(ShipmentPackage::class, 'receiver_branch_id');
    }

    // معتمد
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
