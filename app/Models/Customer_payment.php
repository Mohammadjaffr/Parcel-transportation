<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer_payment extends Model
{
       protected $fillable = [  
        'customer_id',
        'amount',
        'payment_method',
        'notes',
        'transaction_type',
    ];
}