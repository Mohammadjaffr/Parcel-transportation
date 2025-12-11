<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchTransaction extends Model
{
 protected $fillable = [
        'shipment_id',
        'from_branch_id',
        'to_branch_id',
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
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }
}