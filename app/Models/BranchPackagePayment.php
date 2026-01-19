<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchPackagePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_shipment_package_id',
        'paid_amount',
        'payment_method',
        'bond_number',
        'payment_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * العلاقة مع المستخدم الذي أنشأ الدفعة
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الحصول على معلومات الحزمة والفرع من خلال جدول الربط
     * ملاحظة: هذه علاقة مع الـ pivot table
     */
    public function branchShipmentPackage()
    {
        return $this->belongsTo('App\Models\BranchShipmentPackage', 'branch_shipment_package_id');
    }
}
