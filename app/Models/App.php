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
            'branch_addresses'     => 'array',
        ];

        /**
         * عناوين الفروع المعتمدة للسندات والكشوفات
         * تأخذ من الإعدادات المخصصة، أو تستخرج تلقائياً من جدول الفروع كبديل
         */
        public function getEffectiveBranchAddressesAttribute(): array
        {
            if (!empty($this->branch_addresses) && is_array($this->branch_addresses) && count($this->branch_addresses) > 0) {
                $filtered = array_values(array_filter($this->branch_addresses, function ($item) {
                    return is_array($item) && (!empty(trim($item['name'] ?? '')) || !empty(trim($item['address'] ?? '')));
                }));
                if (count($filtered) > 0) {
                    return $filtered;
                }
            }

            $branches = $this->branches;
            if ($branches && $branches->count() > 0) {
                return $branches->map(function ($b) {
                    $name = $b->is_main ? 'المركز الرئيسي' : $b->name;
                    $addressParts = [];
                    if (!empty($b->city)) {
                        $addressParts[] = trim($b->city);
                    }
                    if (!empty($b->address)) {
                        $addressParts[] = trim($b->address);
                    }
                    return [
                        'name'    => $name,
                        'address' => implode(' - ', $addressParts) ?: 'العنوان غير مسجل',
                        'phone'   => $b->phone ?? null,
                    ];
                })->toArray();
            }

            return [];
        }

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
            $hasKey = !empty($this->whatsapp_api_key) || !empty(config('whatsapp.global_api_key'));
            return !empty($this->whatsapp_instance_id) && $hasKey;
        }

        public function getMaskedWhatsAppTokenAttribute(): ?string
        {
            $token = $this->whatsapp_api_key;
            if (empty($token)) {
                $token = config('whatsapp.global_api_key');
            }
            if (empty($token)) {
                return null;
            }
            $len = strlen($token);
            if ($len <= 8) {
                return str_repeat('•', $len);
            }
            return substr($token, 0, 4) . str_repeat('•', max(4, $len - 8)) . substr($token, -4);
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