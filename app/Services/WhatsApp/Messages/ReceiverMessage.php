<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class ReceiverMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Shipment) {
            return null;
        }
        return $entity->receiverCustomer->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'receiver';
    }

  public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof Shipment) {
            return "بيانات الشحنة غير صالحة.";
        }

        $name       = $entity->receiverCustomer->name ?? 'عميلنا العزيز';
        $bondNumber = $entity->bond_number ?? 'غير متوفر';
        $amount     = number_format($entity->total_amount ?? 0);

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأستاذ / {$name} المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";

        $msg .= "نود إشعاركم بأنه تم تسجيل الشحنة الخاصة بكم بنجاح، والبيانات كما يلي:\n\n";

        $msg .= "📦 رقم الشحنة : {$bondNumber}\n";
        $msg .= "💰 المبلغ : {$amount} ريال\n";

        if ($receiptUrl) {
            $msg .= "\n📄 سند الاستلام:\n{$receiptUrl}\n";
        }

        $msg .= "\nيرجى الاحتفاظ برقم الشحنة للمتابعة عند الحاجة.\n\n";
        $msg .= "نشكر ثقتكم بخدماتنا.\n";
        $msg .= "إدارة النظام";

        return $msg;
    }
}