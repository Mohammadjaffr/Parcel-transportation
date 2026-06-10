<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\ShipmentPackage;
use Illuminate\Database\Eloquent\Model;

class DriverMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof ShipmentPackage) {
            return null;
        }
        return $entity->driver->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'DriverDetection';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof ShipmentPackage) {
            return "بيانات الشحنة غير صالحة.";
        }

        $driverName   = $entity->driver->name ?? 'السائق';
        $trackingNo   = $entity->id ?? 'غير متوفر';
        $branchName   = $entity->senderBranch->name ?? 'غير محدد';
        $shipmentsCnt = $entity->shipments?->count() ?? 0;

        // ==========================================
        // 💡 السحر هنا: استخراج الفروع الوجهة (بدون تكرار) مع خرائطها
        // ==========================================
        $destinationBranches = [];
        if ($entity->shipments) {
            foreach ($entity->shipments as $shipment) {
                // جلب الفرع المستلم سواء كان فرع رئيسي أو مكتب
                $branch = $shipment->receiverBranch ?? $shipment->receiverOfficeBranch;
                if ($branch) {
                    // استخدام ID الفرع كمفتاح يضمن عدم تكرار الفرع إذا كان هناك أكثر من شحنة له
                    $destinationBranches[$branch->id] = [
                        'name'     => $branch->name,
                        'map_link' => $branch->map_link
                    ];
                }
            }
        }

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخ الكابتن / *{$driverName}* المحترم،\n\n"; // 👈 إضافة تنسيق البولد وكلمة كابتن للاحترام
        
        $msg .= "تم تكليفك بنقل إرسالية جديدة، التفاصيل كالتالي:\n\n";

        $msg .= "📦 رقم الإرسالية : *{$trackingNo}*\n";
        $msg .= "🏢 فرع التحميل : {$branchName}\n";
        
        if ($shipmentsCnt > 0) {
            $msg .= "📋 عدد الطرود : {$shipmentsCnt}\n";
        }

        // ==========================================
        // 📍 طباعة قائمة الفروع وخرائطها للسائق
        // ==========================================
        if (count($destinationBranches) > 0) {
            $msg .= "\n📍 *وجهات التسليم المطلوبة:*\n";
            foreach ($destinationBranches as $branchData) {
                $msg .= "▪️ فرع " . $branchData['name'] . "\n";
                // إذا كان للفرع رابط خريطة، نقوم بإرفاقه تحته مباشرة
                if (!empty($branchData['map_link'])) {
                    $msg .= "🗺️ الخريطة: " . $branchData['map_link'] . "\n";
                }
            }
        }

        if ($receiptUrl) {
            $msg .= "\n📄 *كشف الإرسالية (PDF):*\n{$receiptUrl}\n";
        }

        $msg .= "\nنأمل منكم التوجه للتحميل، رافقتكم السلامة. 🚚\n";
        $msg .= "إدارة الحركة والنقل";

        return $msg;
    }
}