<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_branch_code',
        'receiver_branch_code',
        'sender_customer_id',
        'receiver_customer_id',
        'customer_debt_status',
        'total_amount',
        'package_type',
        'weight',
        'payment_method',
        'status',
        'notes',
        'code',
        'no_honey_jars',
        'no_gallons_honey',
        'bond_number',
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

            $branchCode = $shipment->sender_branch_code ?? 'XXX';
            $date = now()->format('Ymd');

            $lastShipment = Shipment::where('sender_branch_code', $branchCode)
                ->whereDate('created_at', today())
                ->latest('id')
                ->first();

            $newSeq = $lastShipment
                ? str_pad((int) substr($lastShipment->bond_number, -3) + 1, 3, '0', STR_PAD_LEFT)
                : '001';

            $shipment->bond_number = "{$branchCode}-{$date}{$newSeq}";
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function senderBranch()
    {
        return $this->belongsTo(Branch::class, 'sender_branch_code', 'code');
    }

    public function receiverBranch()
    {
        return $this->belongsTo(Branch::class, 'receiver_branch_code', 'code');
    }

    public function senderCustomer()
    {
        return $this->belongsTo(Customer::class, 'sender_customer_id');
    }

    public function receiverCustomer()
    {
        return $this->belongsTo(Customer::class, 'receiver_customer_id');
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }
}
