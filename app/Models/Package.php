<?php
// معتمد
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'duration_in_days',
        'max_branches',
        'max_drivers',
        'max_shipments',
        'max_packages',
        'is_active',
    ];
    protected $casts = [
    'is_active' => 'boolean',
    'price' => 'decimal:2',
];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}