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

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
}
