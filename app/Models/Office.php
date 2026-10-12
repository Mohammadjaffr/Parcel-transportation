<?php

namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory, BelongsToApp;

    protected $fillable = [
        'app_id',
        'created_by',
        'name',
        'is_banned',
    ];

    protected $casts = [
        'is_banned' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branches()
    {
        return $this->hasMany(OfficeBranch::class);
    }
}
