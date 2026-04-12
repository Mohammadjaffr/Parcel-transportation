<?php

// معتمد
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'password',
        'phone',
        'whatsapp_number',
        'type',
        'is_banned',
        'branch_code',
        'app_id',
        'branch_id'
    ];
    


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
        ];
    }

    public function getCachedAppLogoAttribute()
{
    
    if (!$this->app_id) {
        return asset('assets/image/icon_4K.png');
    }

    return Cache::remember('app_logo_' . $this->app_id, 86400, function () {
        $app = $this->app;
        return $app && $app->logo 
            ? asset('storage/' . $app->logo) 
            : asset('assets/image/icon_4K.png');
    });
}
public function getCachedAppNameAttribute()
{
    
    if (!$this->app_id) {
        return 'اسم الشركة';
    }

    return \Illuminate\Support\Facades\Cache::remember('app_name_' . $this->app_id, 86400, function () {
        $app = $this->app;
        return $app && $app->name ? $app->name : 'اسم الشركة';
    });
}


    public function isAdmin()
    {
        return $this->type === 'admin';
    }
    public function app()
{
    return $this->belongsTo(App::class, 'app_id');
}

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    public function shipments()
{
    return $this->hasMany(Shipment::class, 'sender_phone', 'phone');
}


    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}