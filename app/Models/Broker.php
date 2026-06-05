<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broker extends Model
{
    protected $fillable = ['name'];

    public function passengers(): HasMany
    {
        return $this->hasMany(Passengers::class);
    }
}