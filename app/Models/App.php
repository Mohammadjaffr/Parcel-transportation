<?php

// معتمد
namespace App\Models;

use App\Models\OfficeConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'terms_and_conditions' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function externalOffices()
    {
        return $this->hasMany(Office::class);
    }
    public function sentConnections()
    {
        return $this->hasMany(OfficeConnection::class, 'sender_app_id');
    }
    public function receivedConnections()
    {
        return $this->hasMany(OfficeConnection::class, 'receiver_app_id');
    }
    public function isConnectedWith($targetAppId)
    {
        return OfficeConnection::where(function($query) use ($targetAppId) {
                $query->where('sender_app_id', $this->id)->where('receiver_app_id', $targetAppId);
            })
            ->orWhere(function($query) use ($targetAppId) {
                $query->where('sender_app_id', $targetAppId)->where('receiver_app_id', $this->id);
            })->where('status', 'accepted')->exists();
    }
}