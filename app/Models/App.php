<?php

// معتمد
namespace App\Models;

use App\Models\OfficeConnection;
use App\Models\Service;
use App\Traits\HasServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

    class App extends Model
    {
        use HasFactory, HasServices;
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

        public function hasSmsConfigured(): bool
        {
            return !empty($this->sms_api_key) && !empty($this->sms_from_phone);
        }

        public function getMaskedSmsApiKeyAttribute(): ?string
        {
            if (empty($this->sms_api_key)) {
                return null;
            }
            $len = strlen($this->sms_api_key);
            if ($len <= 8) {
                return str_repeat('•', $len);
            }
            return substr($this->sms_api_key, 0, 4) . str_repeat('•', max(4, $len - 8)) . substr($this->sms_api_key, -4);
        }

        public function hasWhatsAppConfigured(): bool
        {
            return !empty($this->whatsapp_instance_id) && !empty($this->whatsapp_instance_token);
        }

        public function getMaskedWhatsAppTokenAttribute(): ?string
        {
            if (empty($this->whatsapp_instance_token)) {
                return null;
            }
            $len = strlen($this->whatsapp_instance_token);
            if ($len <= 8) {
                return str_repeat('•', $len);
            }
            return substr($this->whatsapp_instance_token, 0, 4) . str_repeat('•', max(4, $len - 8)) . substr($this->whatsapp_instance_token, -4);
        }

        public function getEffectiveWhatsAppBaseUrlAttribute(): string
        {
            return !empty($this->whatsapp_base_url)
                ? rtrim($this->whatsapp_base_url, '/')
                : rtrim(config('whatsapp.base_url', 'http://195.35.24.73:4000'), '/');
        }

        public function getEffectiveWhatsAppTimeoutAttribute(): int
        {
            return !empty($this->whatsapp_timeout) ? (int)$this->whatsapp_timeout : (int)config('whatsapp.timeout', 30);
        }


        public function users()
        {
            return $this->hasMany(User::class);
        }
        public function services()
        {
        return $this->belongsToMany(Service::class, 'app_service')
                    ->withPivot('is_active')
                    ->withTimestamps();
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
            $primary = $this->color ?: '#059669'; 
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