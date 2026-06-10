<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class SenderMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Shipment) {
            return null;
        }
        return $entity->senderCustomer->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'sender';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof Shipment) {
            return "بيانات الشحنة غير صالحة.";
        }

        $name       = $entity->senderCustomer->name ?? 'عميلنا العزيز';
        $bondNumber = $entity->id ?? 'غير متوفر';
        
        // هنا نعرض الإجمالي، وممكن نضيف المتبقي عليه لو أحببت
        $totalAmount = number_format($entity->total_amount ?? 0);

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأستاذ / {$name} المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";

        $msg .= "تم استلام طلب الشحن الخاص بكم بنجاح، وجاري العمل على معالجته.\n\n";

        $msg .= "📦 رقم الشحنة : {$bondNumber}\n";
        $msg .= "💰 إجمالي تكلفة الشحن : {$totalAmount} ريال\n"; // 👈 تغيير المسمى ليكون دقيقاً

        if ($receiptUrl) {
            $msg .= "\n📄 سند الاستلام الخاص بك:\n{$receiptUrl}\n";
        }

        $msg .= "\nيمكنكم متابعة حالة الشحنة باستخدام رقم الشحنة أعلاه.\n\n";
        $msg .= "نشكر ثقتكم بخدماتنا.\n";

        return $msg;
    }
}
