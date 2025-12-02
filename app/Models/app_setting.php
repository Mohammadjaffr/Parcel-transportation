<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class app_setting extends Model
{
    use HasFactory;
    protected $table = 'app_settings';

    protected $fillable = [
        'commission_rate',
        'auto_assign_to_drivers',
        'version',
        'update_url',
    ];
    protected $casts = [
        'auto_assign_to_drivers' => 'boolean',
    ];
}
