<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount_rate', 'is_active', 'max_uses', 'current_uses'
    ];

    public function requests()
    {
        return $this->hasMany(Request::class, 'discount_code_id');
    }
}
