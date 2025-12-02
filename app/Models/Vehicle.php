<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'description', 'max_passengers','image'
    ];
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
    public function pricing()
    {
        return $this->hasMany(vehicle_pricing::class);
    }

}
