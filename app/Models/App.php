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
    
    public function getThemeAttribute(): array
    {
        $primary = $this->color ?: '#fb6514'; 
        return [
            'primary'   => $primary,
            'secondary' => $this->adjustBrightness($primary, -30),
            'bg_light'  => $this->adjustBrightness($primary, 220), 
        ];
    }

    private function adjustBrightness($hex, $steps): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }
        $r = max(0, min(255, hexdec(substr($hex, 0, 2)) + $steps));
        $g = max(0, min(255, hexdec(substr($hex, 2, 2)) + $steps));
        $b = max(0, min(255, hexdec(substr($hex, 4, 2)) + $steps));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}