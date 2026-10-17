<?php

namespace App\Models;

use App\Traits\BelongsToApp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShipmentPackage extends Model
{
    use HasFactory, BelongsToApp;
    protected $fillable = [
        'uuid',
        'tracking_number',
        'app_id',
        'driver_id',
        'created_by',
        'sender_branch_id',
        'status',
        'notes',
        'sender_office_branch_id'
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

            // التوليد التلقائي لرقم الإرسالية بالتسلسل البسيط: PKG-{BRANCH}-1, 2, 3...
            if (empty($package->tracking_number)) {
                $package->tracking_number = static::generateNextTrackingNumber(
                    $package->sender_branch_id,
                    $package->sender_office_branch_id,
                    $package->app_id
                );
            }
        });
    }

    /**
     * توليد الرقم التسلسلي القادم للإرسالية بصيغة PKG-{BRANCH}-{SEQ}
     */
    public static function generateNextTrackingNumber($senderBranchId = null, $senderOfficeBranchId = null, $appId = null)
    {
        $appId = $appId ?? (auth()->check() ? auth()->user()->app_id : null);

        if ($senderBranchId) {
            $branch = \App\Models\Branch::find($senderBranchId);
            $branchIdentifier = ($branch && !empty($branch->code)) ? strtoupper(trim($branch->code)) : 'B' . $senderBranchId;
            $query = static::withoutGlobalScopes()->where('sender_branch_id', $senderBranchId);
        } else {
            $branchIdentifier = 'OF' . $senderOfficeBranchId;
            $query = static::withoutGlobalScopes()->where('sender_office_branch_id', $senderOfficeBranchId);
        }

        if ($appId) {
            $query->where('app_id', $appId);
        }

        $prefix = "PKG-{$branchIdentifier}-";

        // استخراج أعلى رقم تسلسلي مسجل لهذا الفرع
        $existingNumbers = $query->where('tracking_number', 'like', "{$prefix}%")
            ->pluck('tracking_number');

        $maxSeq = 0;
        foreach ($existingNumbers as $tn) {
            $parts = explode('-', $tn);
            $lastPart = end($parts);
            if (is_numeric($lastPart)) {
                $val = (int) $lastPart;
                // تجاوز الأرقام القديمة الكبيرة كالتي احتوت على تاريخ (مثل 261017001)
                if ($val > $maxSeq && $val < 1000000) {
                    $maxSeq = $val;
                }
            }
        }

        $nextSeq = $maxSeq + 1;
        $trackingNumber = "{$prefix}{$nextSeq}";

        // ضمان عدم التكرار كإجراء وقائي
        while (static::withoutGlobalScopes()->where('tracking_number', $trackingNumber)
            ->when($appId, fn($q) => $q->where('app_id', $appId))
            ->exists()) {
            $nextSeq++;
            $trackingNumber = "{$prefix}{$nextSeq}";
        }

        return $trackingNumber;
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
