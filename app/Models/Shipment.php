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
        'other_fee',
        'other_fee_description',

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
        'other_fee' => 'decimal:2',
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
            // 1. توليد UUID
            if (empty($shipment->uuid)) {
                $shipment->uuid = (string) Str::uuid();
            }

            // 💡 جلب بيانات الفرع
            $branchId = $shipment->sender_branch_id ?? auth()->user()->branch_id;
            $branch = \Illuminate\Support\Facades\DB::table('branches')->where('id', $branchId)->first();

            // تحديد كود الفرع (ALQ أو SYW ..الخ). إذا كان فارغاً نستخدم B1 كاحتياطي.
            $branchCode = ($branch && !empty($branch->code)) ? $branch->code : 'B' . $branchId;

            // 2. تعبئة عمود code تلقائياً بـ (ALQ)
            if (empty($shipment->code)) {
                $shipment->code = $branchCode;
            }

            // 3. توليد رقم السند (bond_number) بالصيغة المخفية (مثال: ALQ-001)
            if (empty($shipment->bond_number) || str_starts_with($shipment->bond_number, 'temp-')) {

                $bondPrefix = $branchCode . '-'; // البادئة المخفية (مثال: ALQ-)

                // جلب أعلى رقم سند لهذا الفرع تحديداً
                $lastShipment = static::where('bond_number', 'LIKE', $bondPrefix . '%')
                    ->orderByRaw('CAST(SUBSTRING_INDEX(bond_number, "-", -1) AS UNSIGNED) DESC')
                    ->first();

                // توليد الرقم التالي
                if ($lastShipment) {
                    $parts = explode('-', $lastShipment->bond_number);
                    $nextNumber = (int) end($parts) + 1;
                } else {
                    $nextNumber = 1; // البداية دائماً من 1
                }

                // الحفظ النهائي في قاعدة البيانات (مثال: ALQ-001)
                $shipment->bond_number = $bondPrefix . $nextNumber;
            }
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
    public function getPriceAgreementMessage(?string $receiptUrl = null): string
    {
        return (new \App\Services\WhatsApp\Messages\PriceAgreementMessage())->getMessageBody($this, $receiptUrl);
    }

    /**
     * رابط واتساب لطلب الاتفاق على السعر وتحديد تسعيرة الطرد
     */
    public function getPriceAgreementWhatsappLinkAttribute(): ?string
    {
        return \App\Services\WhatsApp\WhatsAppLinkService::generate($this, 'price_agreement');
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

    public function getBondNumberOnlyAttribute()
    {
        $parts = explode('-', $this->bond_number);
        // إرجاع الجزء الثاني إذا كان موجوداً، وإلا إرجاع القيمة الأصلية
        return isset($parts[1]) ? $parts[1] : $this->bond_number;
    }
}
