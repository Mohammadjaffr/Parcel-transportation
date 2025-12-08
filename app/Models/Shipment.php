<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'driver_id',  'sender_name', 'sender_phone', 'from_city',
        'receiver_name', 'receiver_phone', 'to_city',
        'branch_id', 'package_type', 'weight',
        'payment_method', 'cod_amount', 'status',
        'notes'
    ];
public function logs()
{
    return $this->hasMany(AdminActivity::class, 'model_id')
                ->where('model_type', 'Shipment')
                ->latest();
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