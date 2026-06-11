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
        $bondNumber = $entity->code ?? $entity->id ?? 'غير متوفر';
        
        // 💡 جلب اسم فرع الاستلام
        $branchName = $entity->receiverBranch->name ?? $entity->receiverOfficeBranch->name ?? 'الفرع';
        
        // 📍 السحر الجديد: جلب رابط خرائط جوجل الخاص بالفرع
        $mapLink    = $entity->receiverBranch->map_link ?? $entity->receiverOfficeBranch->map_link ?? null;
        
        $amountDue  = $entity->amount_to_collect_from_receiver; 

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأستاذ / *{$name}* المحترم،\n\n";

        $msg .= "يسعدنا إشعاركم بأن شحنتكم قد وصلت بنجاح وهي الآن *جاهزة للاستلام*.\n\n";
        
        $msg .= "🏢 الفرع : {$branchName}\n";
        
        // 💡 إضافة الرابط للرسالة إذا كان متوفراً في الداتابيز
        if ($mapLink) {
            $msg .= "📍 موقع الفرع (خرائط جوجل) :\n{$mapLink}\n";
        }
        
        $msg .= "📦 رقم الشحنة : {$bondNumber}\n";
        
        // توضيح مالي دقيق في الواتساب
        if ($amountDue > 0) {
            $msg .= "💰 المطلوب سداده عند الاستلام : *" . number_format($amountDue) . " ريال*\n";
        } else {
            $msg .= "✅ حالة الدفع : *خالص* (لا يوجد مبلغ للتحصيل)\n";
        }

        if ($receiptUrl) {
            $msg .= "\n📄 السند الإلكتروني:\n{$receiptUrl}\n";
        }

        $msg .= "\nنرجو منكم التكرم بزيارة الفرع لاستلام الطرد في أقرب وقت.\n";
        $msg .= "نشكر ثقتكم بخدماتنا. 🌹";

        return $msg;
    }
}