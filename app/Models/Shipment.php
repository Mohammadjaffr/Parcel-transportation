<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'sender_name',
        'sender_phone',
        'from_city',
        'receiver_name',
        'receiver_phone',
        'to_city',
        'branch_id',
        'package_type',
        'weight',
        'payment_method',
        'cod_amount',
        'status',
        'notes',
        'bond_number',
        'code',
        'no_honey_jars',
        'no_gallons_honey',
    ];
    public function logs()
    {
        return $this->hasMany(AdminActivity::class, 'model_id')
            ->where('model_type', 'Shipment')
            ->latest();
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shipment) {
            $shipment->bond_number = 'BND-' . date('ymd') . '-' . str_pad(Shipment::max('id') + 1, 5, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class, 'discount_code_id');
    }
}
