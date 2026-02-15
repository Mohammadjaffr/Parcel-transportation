<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptHeader extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'source_branch_code',
        'driver_id',
        'destination_branch_code',
        'created_by',
        'general_notes',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    /* ========== العلاقات ========== */

    public function items()
    {
        return $this->hasMany(ReceiptItem::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function sourceBranch()
    {
        return $this->belongsTo(Branch::class, 'source_branch_code', 'code');
    }

    public function destinationBranch()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_code', 'code');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
