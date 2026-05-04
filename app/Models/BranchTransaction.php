<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchTransaction extends Model
{
 protected $fillable = [
        'shipment_id',
        'sender_branch_code',
        'receiver_branch_code',
        'amount',
        'type',
        'description',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'sender_branch_code');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'receiver_branch_code');
    }
}