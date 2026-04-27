<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_global_active'
    ];

    protected $casts = [
        'is_global_active' => 'boolean',
    ];

    // علاقة الخدمة بالشركات
    public function apps()
    {
        return $this->belongsToMany(App::class)->withPivot('is_active')->withTimestamps();
    }
}