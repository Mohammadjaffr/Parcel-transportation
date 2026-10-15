<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        // 1. المعرّفات
        'uuid',
        'bond_number',
        'code',
        'shipment_package_id',

        // 2. التوجيه والفروع
        'sender_branch_id',
        'sender_office_branch_id',
        'receiver_branch_id',
        'receiver_office_branch_id',

        // 3. العملاء
        'sender_customer_id',
        'receiver_customer_id',

        // 4. تفاصيل الطرد العادي
        'package_type',
        'parcels_quantity',
        'weight',
        'package_fee',
        'package_commission_rate',
        'package_commission_amount',

        // 5. تفاصيل العسل
        'no_gallons_honey',
        'no_honey_jars',
        'honey_fee',
        'honey_commission_rate',
        'honey_commission_amount',

        // 6. الإجماليات والمالية
        'payment_method',
        'total_amount',
        'partial_amount',
        'total_commission',
        'is_price_agreed',
        'customer_debt_status',

        // 7. الحالة والملاحظات
        'status',
        'is_returned',
        'notes',
        
        // 8. معلومات النظام
        'created_by',
    ];
    protected $casts = [
        'is_returned' => 'boolean',
        'is_price_agreed' => 'boolean',
        'parcels_quantity' => 'integer',
    ];

    public function getParcelsCountAttribute()
    {
        return $this->parcels_quantity ?? 1;
    }

    public function logs()
    {
        return $this->hasMany(AdminActivity::class, 'model_id')
            ->where('model_type', 'Shipment')
            ->latest();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shipment) {
            if (empty($shipment->uuid)) {
                $shipment->uuid = (string) Str::uuid();
            }
            
            // تعيين قيمة مؤقتة لرقم السند لتمرير قاعدة البيانات
            $shipment->bond_number = 'temp-' . Str::random(8);
        });

        static::created(function ($shipment) {
            $branchName = 'فرع';
            
            if ($shipment->sender_branch_id) {
                $branch = \Illuminate\Support\Facades\DB::table('branches')->where('id', $shipment->sender_branch_id)->first();
                if ($branch) {
                    $branchName = trim(str_replace('فرع', '', $branch->name));
                }
            } elseif ($shipment->sender_office_branch_id) {
                $office = \Illuminate\Support\Facades\DB::table('office_branches')->where('id', $shipment->sender_office_branch_id)->first();
                if ($office) {
                    $branchName = trim(str_replace('مكتب', '', $office->name));
                }
            }

            // استخدام اسم الفرع كبادئة
            $bondPrefix = '';

            $existingBonds = \Illuminate\Support\Facades\DB::table('shipments')
                ->select('bond_number')
                ->where('bond_number', 'LIKE', $bondPrefix . '%')
                ->where('id', '!=', $shipment->id)
                ->pluck('bond_number');

            $maxSeq = 0;
            foreach ($existingBonds as $bond) {
                $parts = explode('-', $bond);
                if (count($parts) > 1) {
                    $seq = (int) end($parts);
                    if ($seq > $maxSeq) {
                        $maxSeq = $seq;
                    }
                }
            }
            
            $sequence = $maxSeq + 1;
            $sequenceStr = str_pad($sequence, 3, '0', STR_PAD_LEFT);
            $bondNumber = $bondPrefix . $sequenceStr;

            // التأكد من عدم وجود تكرار كإجراء احتياطي
            while (\Illuminate\Support\Facades\DB::table('shipments')->where('bond_number', $bondNumber)->exists()) {
                $sequence++;
                $sequenceStr = str_pad($sequence, 3, '0', STR_PAD_LEFT);
                $bondNumber = $bondPrefix . $sequenceStr;
            }
            
            $shipment->bond_number = $bondNumber;
            \Illuminate\Support\Facades\DB::table('shipments')
                ->where('id', $shipment->id)
                ->update(['bond_number' => $bondNumber]);
        });
    }

    public function package()
    {
        return $this->belongsTo(ShipmentPackage::class, 'shipment_package_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- تعديل علاقات الفروع ---
    public function senderBranch()
    {
        return $this->belongsTo(Branch::class, 'sender_branch_id')->withoutGlobalScopes();
    }
    public function getSenderAttribute()
    {
        return $this->senderBranch ?? $this->senderOfficeBranch;
    }
    public function getReceiverAttribute()
    {
        return $this->receiverBranch ?? $this->receiverOfficeBranch;
    }

    public function receiverBranch()
    {
        return $this->belongsTo(Branch::class, 'receiver_branch_id')->withoutGlobalScopes();
    }
    public function senderOfficeBranch()
    {
        return $this->belongsTo(OfficeBranch::class, 'sender_office_branch_id');
    }

    public function receiverOfficeBranch()
    {
        return $this->belongsTo(OfficeBranch::class, 'receiver_office_branch_id');
    }
    // ---------------------------

    public function senderCustomer()
    {
        return $this->belongsTo(Customer::class, 'sender_customer_id')->withoutGlobalScopes();
    }

    public function receiverCustomer()
    {
        return $this->belongsTo(Customer::class, 'receiver_customer_id')->withoutGlobalScopes();
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function customerPayments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    /**
     * Get the transaction associated with this shipment
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
    /**
     * حساب المبلغ المتبقي المطلوب تحصيله كاش من (المُستلم)
     */
    public function getAmountToCollectFromReceiverAttribute()
    {
        $total = (float) ($this->total_amount ?? 0);
        $partial = (float) ($this->partial_amount ?? 0);

        return match ($this->payment_method) {
            'prepaid'         => 0, 
            'customer_credit' => 0, 
            'partial_payment' => max(0, $total - $partial), 
            'cod'             => $total, 
            default           => $total,
        };
    }

    /**
     * حساب المبلغ المطلوب تحصيله كاش من (المُرسل)
     */
    public function getAmountToCollectFromSenderAttribute()
    {
        $total = (float) ($this->total_amount ?? 0);
        $partial = (float) ($this->partial_amount ?? 0);

        return match ($this->payment_method) {
            'prepaid'         => $total,   
            'partial_payment' => $partial, 
            'cod'             => 0,       
            'customer_credit' => 0,        
            default           => 0,
        };
    }

    /**
     * تسمية طريقة الدفع مع مراعاة حالة الاتفاق على السعر
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        if (!$this->is_price_agreed) {
            return 'سيتم الاتفاق عليه لاحقاً';
        }

        return match ($this->payment_method) {
            'prepaid'         => 'مدفوع مقدماً',
            'cod'             => 'الدفع عند الاستلام',
            'partial_payment' => 'دفع جزئي',
            'customer_credit' => 'آجل (ذمة)',
            default           => 'غير محدد',
        };
    }

    /**
     * نص رسالة واتساب لطلب الاتفاق على السعر وتحديد تسعيرة الطرد
     */
    public function getPriceAgreementMessage(): string
    {
        $clientName = $this->receiverCustomer?->name ?? $this->senderCustomer?->name ?? 'عميلنا العزيز';
        $senderName = $this->senderCustomer?->name ?? 'غير محدد';
        $senderPhone = $this->senderCustomer?->phone ?? 'غير متوفر';
        $packageType = $this->package_type ?? 'طرد';

        // تحديد مكتب الإرسال ومكتب التسليم ديناميكياً
        $senderOffice = null;
        if ($this->senderBranch) {
            $appName = $this->senderBranch->app?->name;
            $senderOffice = ($appName ? $appName . ' / ' : '') . $this->senderBranch->name;
        } elseif ($this->senderOfficeBranch) {
            $officeName = $this->senderOfficeBranch->office?->name;
            $senderOffice = ($officeName ? $officeName . ' / ' : '') . $this->senderOfficeBranch->name;
        }
        $senderPlace = $senderOffice ?? 'غير محدد';

        $receiverOffice = null;
        if ($this->receiverBranch) {
            $appName = $this->receiverBranch->app?->name;
            $receiverOffice = ($appName ? $appName . ' / ' : '') . $this->receiverBranch->name;
        } elseif ($this->receiverOfficeBranch) {
            $officeName = $this->receiverOfficeBranch->office?->name;
            $receiverOffice = ($officeName ? $officeName . ' / ' : '') . $this->receiverOfficeBranch->name;
        }
        $deliveryPlace = $receiverOffice ?? 'غير محدد';

        $cost = ($this->is_price_agreed && $this->total_amount > 0) ? number_format($this->total_amount, 0) . ' ريال' : 'لم يتفق عليها بعد';
        $bondNumber = $this->bond_number ?? $this->id;
        $receiptUrl = route('receipt.generate', ['type' => 'sender', 'id' => $this->uuid ?? $this->id]);

        // تحديد اسم المكتب والفرع ديناميكياً
        $appName    = $this->senderBranch?->app?->name ?? auth()->user()?->app?->name ?? '';
        $branch     = $this->senderBranch ?? auth()->user()?->branch;
        $branchName = $branch?->name ? (str_starts_with(trim($branch->name), 'فرع') ? trim($branch->name) : 'فرع ' . trim($branch->name)) : '';
        $officeName = trim('مكتب ' . ($appName ? $appName . ' ' : '') . $branchName);
        if (empty($officeName)) {
            $officeName = 'مكتبنا الرئيسي';
        }

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخ / {$clientName} المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";
        $msg .= "تم استلام طردك بنجاح\n";
        $msg .= "المرسل/ {$senderName}\n";
        $msg .= "رقم المرسل/ {$senderPhone}\n";
        $msg .= "نوع الطرد/ {$packageType}\n";
        $msg .= "مكتب الإرسال/ {$senderPlace}\n";
        $msg .= "مكتب التسليم/ {$deliveryPlace}\n";
        $msg .= "تكلفة النقل/ {$cost}\n\n";
        $msg .= "📦 رقم سند الارسال : {$bondNumber}\n\n";
        $msg .= "تواصل معنا لتسعيرها بالاتصال او على خدمة الواتساب على الارقام \n";
        $msg .= "{$officeName}\n";
        $msg .= "الهاتف:782947557\n";
        $msg .= "الهاتف:778132137\n";
        $msg .= "الهاتف:02383444 \n\n";
        $msg .= "او سنتواصل معك في اقرب وقت\n\n";
        $msg .= "📄 سند الاستلام الخاص بك:\n";
        $msg .= "{$receiptUrl}\n\n";
        $msg .= "يمكنكم متابعة حالة الطرد باستخدام رقم الشحنة أعلاه.";

        return $msg;
    }

    /**
     * رابط واتساب لطلب الاتفاق على السعر وتحديد تسعيرة الطرد
     */
    public function getPriceAgreementWhatsappLinkAttribute(): ?string
    {
        $phone = $this->receiverCustomer?->phone ?? $this->senderCustomer?->phone;
        if (!$phone) {
            return null;
        }

        $msg = $this->getPriceAgreementMessage();

        // تنظيف الرقم من الرموز والمسافات
        $cleanPhone = preg_replace('/[^\d\+]/', '', $phone);
        $cleanPhone = ltrim($cleanPhone, '+');

        if (str_starts_with($cleanPhone, '00')) {
            $cleanPhone = substr($cleanPhone, 2);
        }
        if (str_starts_with($cleanPhone, '967967')) {
            $cleanPhone = substr($cleanPhone, 3);
        }
        if (str_starts_with($cleanPhone, '966966')) {
            $cleanPhone = substr($cleanPhone, 3);
        }
        if (preg_match('/^0(7[0-9]\d{7})$/', $cleanPhone, $matches)) {
            $cleanPhone = '967' . $matches[1];
        }

        return "https://wa.me/{$cleanPhone}?text=" . urlencode($msg);
    }

    /**
     * رابط واتساب المباشر لإشعار المستلم
     */
    public function getReceiverWhatsappLinkAttribute(): ?string
    {
        return \App\Services\WhatsApp\WhatsAppLinkService::generate($this, 'receiver');
    }

    /**
     * رابط واتساب المباشر لإشعار المرسل
     */
    public function getSenderWhatsappLinkAttribute(): ?string
    {
        return \App\Services\WhatsApp\WhatsAppLinkService::generate($this, 'sender');
    }
}

