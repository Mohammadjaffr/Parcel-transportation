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
    ];

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
            // تحديث رقم السند ليكون B{branch_id}-{id} بناءً على طلبك
            $bondNumber = 'B' . $shipment->sender_branch_id . '-' . $shipment->id;
            
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
        return $this->belongsTo(Customer::class, 'sender_customer_id');
    }

    public function receiverCustomer()
    {
        return $this->belongsTo(Customer::class, 'receiver_customer_id');
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




}
