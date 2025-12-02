<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class vehicle_pricing extends Model
{
    use HasFactory;
     protected $fillable = [
        'vehicle_id',
        'base_price',
        'min_distance_km',
        'max_distance_km',
    ];
    protected $casts = [
        'base_price' => 'decimal:2',
        'min_distance_km' => 'decimal:2',
        'max_distance_km' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeForDistance($query, $distance)
    {
        return $query->where('min_distance_km', '<=', $distance)
                    ->where('max_distance_km', '>=', $distance);
    }

}
