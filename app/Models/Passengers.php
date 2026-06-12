<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Passengers extends Model 
{
    use HasFactory ;
    protected $fillable = [
        'date',
        'broker_id',
        'passenger_number',
        'pickup_location',
        'destination',
        'count',
        'office_commission',
        'other_office_commission',
        'branch_id',
        'driver_id',
        'note',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class , 'branch_id');
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }
    public function trip()
    {
        return $this->belongsTo(PassengerTrip::class, 'trip_id');
    }
    public function getTotalCommissionAttribute()
    {  
        return $this->office_commission + $this->other_office_commission;
    }

  

   
}