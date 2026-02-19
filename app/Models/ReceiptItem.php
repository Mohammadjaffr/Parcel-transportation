<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receipt_header_id',
        'number',
        'sender_name',
        'receiver_name',
        'receiver_phone',
        'package_type',
        'item_notes',
        'is_delivered',
        'payment_status',
        'amount',
    ];

    protected $casts = [
        'is_delivered' => 'boolean',
    ];

    /* ========== العلاقات ========== */

    public function header()
    {
        return $this->belongsTo(ReceiptHeader::class, 'receipt_header_id');
    }
}
