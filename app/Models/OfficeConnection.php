<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_app_id',
        'receiver_app_id',
        'status'
    ];

    public function sender()
    {
        return $this->belongsTo(App::class, 'sender_app_id');
    }

    public function receiver()
    {
        return $this->belongsTo(App::class, 'receiver_app_id');
    }
}