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
        'reference_number',
        'notes',
        'created_by',
    ];

   
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',   
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($payment) {
            if (auth()->check() && empty($payment->created_by)) {
                $payment->created_by = auth()->id();
            }
        });
    }

    
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

  
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_code', 'code');
    }
}
