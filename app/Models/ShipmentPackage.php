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
        'sender_branch_id', 'status', 'notes'
    ];

    // ==========================================
    // التوليد التلقائي لرقم التتبع (Boot Method)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {

            // جلب الفرع المُرسل لاستخدام الكود الخاص به
            $branch = Branch::find($package->sender_branch_id);
            
            // إذا كان للفرع كود نستخدمه، وإلا نستخدم الحرف B مع رقم الفرع
            $branchIdentifier = $branch && $branch->code ? $branch->code : 'B' . $package->sender_branch_id;

            // التنسيق: سنة، شهر، يوم (مثال: 260413)
            $date = now()->format('ymd'); 

            // البحث عن آخر "إرسالية مجمعة" لنفس الفرع في نفس اليوم
            // استخدمنا static بدلاً من اسم الموديل ليكون الكود مرناً
            $lastPackage = static::where('sender_branch_id', $package->sender_branch_id)
                ->whereDate('created_at', today())
                ->latest('id')
                ->first();

            // توليد التسلسل الجديد (001, 002, 003...)
            $newSeq = $lastPackage && $lastPackage->tracking_number
                ? str_pad((int) substr($lastPackage->tracking_number, -3) + 1, 3, '0', STR_PAD_LEFT)
                : '001';

            // 💡 شكل رقم التتبع للإرسالية سيكون مثلاً: PKG-SAN-260413001
            // أضفنا PKG لتمييز الإرسالية المجمعة عن الطرد الفردي
            $package->tracking_number = "PKG-{$branchIdentifier}-{$date}{$newSeq}";
        });
    }

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

    // العلاقة مع الطرود الموجودة داخل هذه الشحنة (بفرض وجود جدول parcels)
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'shipment_package_id'); 
    }
}