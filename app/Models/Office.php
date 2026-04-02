<?php

namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory,BelongsToApp;

    protected $guarded = [];

    public function branches()
    {
        return $this->hasMany(OfficeBranch::class);
    }
}
