<?php

namespace App\Models;

use App\Traits\BelongsToApp;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverTransaction extends Model
{
    use HasFactory, BelongsToApp, HasUuid;

    protected $fillable = [
        'app_id',
        'branch_id',
        'uuid',
        'driver_id',
        'shipment_package_id',
        'passenger_trip_id',
        'cash_transaction_id',
        'type',
        'amount',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ========================
    // Relationships
    // ========================

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function shipmentPackage()
    {
        return $this->belongsTo(ShipmentPackage::class);
    }

    public function passengerTrip()
    {
        return $this->belongsTo(PassengerTrip::class);
    }

    public function cashTransaction()
    {
        return $this->belongsTo(CashTransaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

