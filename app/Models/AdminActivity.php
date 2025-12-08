<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivity extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'model_type',
        'description',
        'model_id',
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

}