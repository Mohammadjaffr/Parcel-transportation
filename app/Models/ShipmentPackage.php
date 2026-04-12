<?php

namespace App\Models;
use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class ShipmentPackage extends Model
{
    use HasFactory,BelongsToApp;
    protected $fillable = [
        'tracking_number', 'app_id', 'driver_id', 'created_by', 
        'sender_branch_id', 'receiver_branch_id', 'status', 'notes', 'total_weight'
    ];

    // العلاقة مع الشركة/التطبيق
    public function app()
    {
        return $this->belongsTo(App::class);
    }

    // العلاقة مع السائق
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // العلاقة مع الموظف الذي أنشأ الشحنة
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // العلاقة مع فرع الإرسال
    public function senderBranch()
    {
        return $this->belongsTo(Branch::class, 'sender_branch_id');
    }

    // العلاقة مع فرع الاستلام
    public function receiverBranch()
    {
        return $this->belongsTo(Branch::class, 'receiver_branch_id');
    }

    // العلاقة مع الطرود الموجودة داخل هذه الشحنة (بفرض وجود جدول parcels)
    public function parcels()
    {
        return $this->hasMany(Shipment::class, 'package_id'); 
    }
}