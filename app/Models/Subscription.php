<?php
// معتمد
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'package_id',
        'price_paid',
        'allowed_branches',
        'allowed_drivers',
        'allowed_shipments',
        'allowed_packages',
        'starts_at',
        'ends_at',
        'status',
    ];
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}