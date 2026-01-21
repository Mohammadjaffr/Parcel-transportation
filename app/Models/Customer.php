<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    
    protected $fillable = [
        'name',
        'phone',
        'branch_code',
        'whatsapp_number',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_code', 'code');
    }

    public function sentShipments()
    {
        return $this->hasMany(Shipment::class, 'sender_customer_id');
    }

    public function receivedShipments()
    {
        return $this->hasMany(Shipment::class, 'receiver_customer_id');
    }

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    public function cashTransactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
}
