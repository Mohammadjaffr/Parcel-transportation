<?php

namespace App\Models;
use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShipmentPackage extends Model
{
    use HasFactory,BelongsToApp;
    protected $fillable = [
        'uuid','tracking_number', 'app_id', 'driver_id', 'created_by', 
        'sender_branch_id', 'status', 'notes', 'sender_office_branch_id'
    ];

    // ==========================================
    // التوليد التلقائي لرقم التتبع (Boot Method)
    // ==========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->uuid)) {
                $package->uuid = (string) Str::uuid();
            }

            // 💡 التعديل هنا: التوليد التلقائي يتم فقط إذا كان رقم التتبع فارغاً
            if (empty($package->tracking_number)) {
                
                // 1. تحديد هوية المرسل (فرع داخلي أم مكتب خارجي) بناءً على التحديثات الأخيرة
                if ($package->sender_branch_id) {
                    $branch = \App\Models\Branch::find($package->sender_branch_id);
                    $branchIdentifier = $branch && $branch->code ? $branch->code : 'B' . $package->sender_branch_id;
                    $query = static::where('sender_branch_id', $package->sender_branch_id);
                } else {
                    // في حال كان مكتباً خارجياً، نستخدم اختصار OF مع رقمه
                    $branchIdentifier = 'OF' . $package->sender_office_branch_id;
                    $query = static::where('sender_office_branch_id', $package->sender_office_branch_id);
                }

                // 2. التنسيق: سنة، شهر، يوم
                $date = now()->format('ymd'); 

                // 3. البحث عن آخر "إرسالية مجمعة" لنفس المصدر في نفس اليوم
                $lastPackage = $query->whereDate('created_at', today())
                    ->latest('id')
                    ->first();

                // 4. توليد التسلسل الجديد
                $newSeq = $lastPackage && $lastPackage->tracking_number
                    ? str_pad((int) substr($lastPackage->tracking_number, -3) + 1, 3, '0', STR_PAD_LEFT)
                    : '001';

                // إسناد الرقم المولد
                $package->tracking_number = "PKG-{$branchIdentifier}-{$date}{$newSeq}";
            }
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
    public function senderOfficeBranch()
    {
        return $this->belongsTo(OfficeBranch::class, 'sender_office_branch_id');
    }
    public function getSenderEntityAttribute()
    {
        return $this->senderBranch ?? $this->senderOfficeBranch;
    }

    // العلاقة مع الطرود الموجودة داخل هذه الشحنة (بفرض وجود جدول parcels)
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'shipment_package_id'); 
    }
}