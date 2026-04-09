<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_branch_id',    
        'receiver_branch_id',
        'receiver_office_branch_id',  
        'sender_customer_id',
        'receiver_customer_id',
        'customer_debt_status',
        'total_amount',
        'partial_amount',
        'package_type',
        'weight',
        'payment_method',
        'status',
        'notes',
        'code',
        'no_honey_jars',
        'no_gallons_honey',
        'bond_number',
        'shipment_package_id',
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
            
            // جلب الفرع المُرسل لاستخدام الكود الخاص به في رقم السند
            $branch = Branch::find($shipment->sender_branch_id);
            
            // إذا كان للفرع كود نستخدمه، وإلا نستخدم الحرف B مع رقم الفرع كبديل
            $branchIdentifier = $branch && $branch->code ? $branch->code : 'B' . $shipment->sender_branch_id;

            // التعديل هنا: حرف y الصغير يعطي 26 بدلاً من 2026
            // النتيجة ستكون مثلاً: 260408 (سنة 26، شهر 04، يوم 08)
            $date = now()->format('ymd'); 

            // البحث عن آخر شحنة لنفس الفرع في نفس اليوم باستخدام الـ ID
            $lastShipment = Shipment::where('sender_branch_id', $shipment->sender_branch_id)
                ->whereDate('created_at', today())
                ->latest('id')
                ->first();

            $newSeq = $lastShipment
                ? str_pad((int) substr($lastShipment->bond_number, -3) + 1, 3, '0', STR_PAD_LEFT)
                : '001';

            // شكل السند سيكون مثلاً: SAN-260408001
            $shipment->bond_number = "{$branchIdentifier}-{$date}{$newSeq}";
        });
    }

    public function package()
    {
        return $this->belongsTo(ShipmentPackage::class, 'shipment_package_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- تعديل علاقات الفروع ---
    public function senderBranch()
    {
        return $this->belongsTo(Branch::class, 'sender_branch_id');
    }

    public function receiverBranch()
    {
        return $this->belongsTo(Branch::class, 'receiver_branch_id');
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
}