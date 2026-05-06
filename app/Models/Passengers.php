<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Passengers extends Model 
{
    use HasFactory ;
    protected $fillable = [
        'date',
        'customer_id',
        'passenger_number',
        'location',
        'count',
        'total_commission',
        'branch_id',
        'driver_id',
        'note',
        'status'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
