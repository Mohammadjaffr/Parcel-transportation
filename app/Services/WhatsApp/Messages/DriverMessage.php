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
        $trackingNo   = $entity->tracking_number ?? 'غير متوفر';
        $branchName   = $entity->senderBranch->name ?? 'غير محدد';
        $shipmentsCnt = $entity->shipments?->count() ?? 0;

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";

        $msg .= "الأخ / {$driverName} المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";

        $msg .= "نود إشعاركم بأنه تم إصدار إرسالية جديدة بنجاح، وقد تم تعيينكم مسؤولاً عنها ضمن عمليات النقل والتسليم.\n\n";

        $msg .= "تفاصيل الإرسالية:\n";
        $msg .= "رقم الإرسالية : {$trackingNo}\n";
        $msg .= "فرع الإرسال   : {$branchName}\n";

        if ($shipmentsCnt > 0) {
            $msg .= "عدد الشحنات  : {$shipmentsCnt}\n";
        }

        if ($receiptUrl) {
            $msg .= "\nرابط سند الاستلام:\n{$receiptUrl}\n";
        }

        $msg .= "\nنأمل منكم الاطلاع على التفاصيل واتخاذ الإجراءات اللازمة وفق النظام.\n\n";

        $msg .= "وتفضلوا بقبول فائق الاحترام والتقدير.\n";
        $msg .= "إدارة النظام";

        return $msg;
    }
}
