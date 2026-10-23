<?php

// معتمد
namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{

    use HasFactory,BelongsToApp;
    protected $fillable = [
        'name',
        'phone',

        'is_banned',

        'vehicle_type',

        'app_id',
        'branch_id',
        'created_by'
    ];

    protected $casts = [
        'is_banned' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_banned', false);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


}