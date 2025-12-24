<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'branches';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'code',
    ];

    public function senderBranch()
    {
        return $this->belongsTo(Branch::class, 'sender_branch_code', 'code');
    }

    public function receiverBranch()
    {
        return $this->belongsTo(Branch::class, 'receiver_branch_code', 'code');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
