<?php

namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broker extends Model
{
    use BelongsToApp;

    protected $fillable = ['name', 'app_id'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->app_id && !$model->app_id) {
                $model->app_id = auth()->user()->app_id;
            }
        });
    }
    public function passengers(): HasMany
    {
        return $this->hasMany(Passengers::class);
    }
}