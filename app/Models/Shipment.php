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
        'driver_id'
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

            // جلب كود الفرع بناءً على from_city
            $branchCode = Branch::where('name', $shipment->from_city)->value('code') ?? 'XXX';

            // التاريخ بصيغة YYYYMMDD -> مثال: 20251210
            $date = date('Ymd');

            // جلب آخر سند من نفس الفرع في نفس اليوم
            $lastShipment = Shipment::where('from_city', $shipment->from_city)
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();

            if ($lastShipment) {
                // استخراج رقم التسلسل (آخر 3 أرقام)
                $lastSeq = intval(substr($lastShipment->bond_number, -3));
                $newSeq = str_pad($lastSeq + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newSeq = '001';
            }

            // رقم السند النهائي
            $shipment->bond_number = "{$branchCode}-{$date}{$newSeq}";
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
