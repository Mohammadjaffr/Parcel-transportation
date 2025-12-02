<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'driver_id', 'service_id', 'discount_code_id',
        'start_latitude', 'start_longitude', 'start_address',
        'end_latitude', 'end_longitude', 'end_address', 'status',
        'original_price', 'discount_amount', 'final_price',
        'distance_km', 'notes', 'payment_method'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class, 'discount_code_id');
    }

}
