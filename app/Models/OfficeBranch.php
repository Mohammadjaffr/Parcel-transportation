<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'name',
        'phone',
        'address',
        'city'
    ];
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
