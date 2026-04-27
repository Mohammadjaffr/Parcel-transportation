<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Passengers extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'day',
        'passenger_number',
        'location',
        'count',
        'total_commission',
        'broker',
        'driver_id',
        'note',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
