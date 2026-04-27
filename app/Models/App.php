<?php

    // معتمد
    namespace App\Models;

use App\Models\OfficeConnection;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

    class App extends Model
    {
        use HasFactory;
        protected static function booted()
        {
            static::created(function ($app) {
                $trialPackage = Package::where('price', 0)->first();
                if ($trialPackage) {
                    $subscription = Subscription::create([
                        'app_id'            => $app->id,
                        'package_id'        => $trialPackage->id,
                        'price_paid'        => 0.00,
                        'allowed_branches'  => $trialPackage->max_branches,
                        'allowed_drivers'   => $trialPackage->max_drivers,
                        'allowed_shipments' => $trialPackage->max_shipments,
                        'allowed_packages'  => $trialPackage->max_packages,
                        
                        'status'            => 'active', 
                        'starts_at'         => now(),
                        'ends_at'           => now()->addDays($trialPackage->duration_in_days),
                    ]);
                    $app->current_subscription_id = $subscription->id;
                    $app->is_active = false;
                    $app->saveQuietly(); 
                }
            });
        }

        protected $guarded = [];
        protected $casts = [
            'terms_and_conditions' => 'array',
        ];

        public function users()
        {
            return $this->hasMany(User::class);
        }
        public function services()
        {
            return $this->belongsToMany(Service::class)->withPivot('is_active')->withTimestamps();
        }

        public function hasService($serviceSlug)
        {
            $activeServices = Cache::remember('app_services_' . $this->id, 86400, function () {
                return $this->services()
                            ->where('services.is_global_active', true) 
                            ->wherePivot('is_active', true) 
                            ->pluck('services.slug')
                            ->toArray();
            });

        return in_array($serviceSlug, $activeServices);
        }

        public function branches()
        {
            return $this->hasMany(Branch::class);
        }
        public function drivers()
        {
            return $this->hasMany(Driver::class);
        }

        public function shipments()
        {
            return $this->hasManyThrough(
                Shipment::class,      
                Branch::class,       
                'app_id',             
                'sender_branch_id',   
                'id',                
                'id'         
            );
        }
        public function shipmentPackages()
        {
            return $this->hasMany(ShipmentPackage::class);
        }

        public function externalOffices()
        {
            return $this->hasMany(Office::class);
        }
        public function sentConnections()
        {
            return $this->hasMany(OfficeConnection::class, 'sender_app_id');
        }
        public function subscriptions()
        {
            return $this->hasMany(Subscription::class);
        }
        public function currentSubscription()
        {
            return $this->belongsTo(Subscription::class, 'current_subscription_id');
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
        public function hasActiveSubscription(): bool
        {
            return $this->is_active && 
                $this->currentSubscription && 
                $this->currentSubscription->status === 'active' &&
                $this->currentSubscription->ends_at->isFuture();
        }
    }