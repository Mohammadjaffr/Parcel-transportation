<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivity extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_name',
        'description',
        // 'model_id', // Currently missing in DB
    ];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}