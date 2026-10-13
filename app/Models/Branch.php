<?php

// معتمد
namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory,BelongsToApp;
    protected $table = 'branches';
    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'code',
        'app_id',
        'map_link',
        'is_main',
        // WhatsApp Settings (per branch)
        'whatsapp_base_url',
        'whatsapp_instance_id',
        'whatsapp_instance_name',
        'whatsapp_api_key',
        'whatsapp_webhook_url',
        'whatsapp_timeout',
    ];

    // ================= علاقة الشركة =================
    public function app()
    {
        return $this->belongsTo(App::class, 'app_id');
    }

    // ================= دوال WhatsApp المساعدة =================

    /**
     * التحقق من اكتمال إعدادات WhatsApp للفرع
     */
    public function hasWhatsAppConfigured(): bool
    {
        $hasKey = !empty($this->whatsapp_api_key) || !empty(config('whatsapp.global_api_key'));
        return !empty($this->whatsapp_instance_id) && $hasKey;
    }

    /**
     * عرض التوكن مقنّعاً للأمان
     */
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

    /**
     * عنوان API الفعّال (إعداد الفرع أو الافتراضي)
     */
    public function getEffectiveWhatsAppBaseUrlAttribute(): string
    {
        return !empty($this->whatsapp_base_url)
            ? rtrim($this->whatsapp_base_url, '/')
            : rtrim(config('whatsapp.base_url', 'http://195.35.24.73:4000'), '/');
    }

    /**
     * المهلة الزمنية الفعّالة
     */
    public function getEffectiveWhatsAppTimeoutAttribute(): int
    {
        return !empty($this->whatsapp_timeout) ? (int)$this->whatsapp_timeout : (int)config('whatsapp.timeout', 30);
    }

    // معتمد
    public function outgoingShipments()
    {
        return $this->hasMany(Shipment::class, 'sender_branch_id');
    }
    // معتمد
    public function incomingShipments()
    {
        return $this->hasMany(Shipment::class, 'receiver_branch_id');
    }
    // معتمد
    public function sentPackages()
    {
        return $this->hasMany(ShipmentPackage::class, 'sender_branch_id');
    }
    // معتمد
    public function receivedPackages()
    {
        return $this->hasMany(ShipmentPackage::class, 'receiver_branch_id');
    }

    // معتمد
    public function users()
    {
        return $this->hasMany(User::class);
    }
    // معتمد
    public function cashTransactions()
    {
        return $this->hasMany(CashTransaction::class, 'branch_id');
    }
}
