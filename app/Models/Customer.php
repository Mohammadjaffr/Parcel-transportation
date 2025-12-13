<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $fillable = [
        'name',
        'phone',
        'branch_id',
        'type',
        'is_cod',
        'credit_limit',
    ];
        public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class);
    }
}