<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Model
{
    
    use HasFactory, HasApiTokens;
    protected $fillable = [
        'vehicle_id', 'name', 'phone', 'vehicle_image', 'driver_image',
        'city', 'plate_number', 'whatsapp_number',
        'device_token', 'latitude', 'longitude', 'is_active', 'is_online'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
