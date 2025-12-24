<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $table = 'customer_payments';
    protected $fillable = [
        'shipment_id',
        'customer_id',
        'branch_code',
        'amount',
        'payment_date',
        'payment_method',
        'attachment_path',
        'notes',
    ];

   
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',   
    ];

    

    
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

  
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_code', 'code');
    }
}
