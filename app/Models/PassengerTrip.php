<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PassengerTrip extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'app_id',
        'branch_id',
        'created_by',
        'driver_id',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function passengers()
    {
        return $this->hasMany(Passengers::class, 'trip_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}