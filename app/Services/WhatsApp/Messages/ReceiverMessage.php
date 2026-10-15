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
        return $entity->receiverCustomer?->phone ?? null;
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

        $name       = $entity->receiverCustomer?->name ?? 'عميلنا العزيز';
        $bondNumber = $entity->bond_number ?? $entity->code ?? $entity->id ?? 'غير متوفر';
        $senderName = $entity->senderCustomer?->name ?? 'عميل نقدي';

        // 💡 جلب اسم ومكتب الإرسال
        $senderOffice = null;
        if ($entity->senderBranch) {
            $appName = $entity->senderBranch->app?->name;
            $senderOffice = ($appName ? $appName . ' / ' : '') . $entity->senderBranch->name;
        } elseif ($entity->senderOfficeBranch) {
            $officeName = $entity->senderOfficeBranch->office?->name;
            $senderOffice = ($officeName ? $officeName . ' / ' : '') . $entity->senderOfficeBranch->name;
        }
        $senderPlace = $senderOffice ?? $entity->senderBranch?->name ?? $entity->senderOfficeBranch?->name ?? 'غير محدد';

        // 💡 جلب اسم فرع / مكتب الاستلام
        $receiverOffice = null;
        if ($entity->receiverBranch) {
            $appName = $entity->receiverBranch->app?->name;
            $receiverOffice = ($appName ? $appName . ' / ' : '') . $entity->receiverBranch->name;
        } elseif ($entity->receiverOfficeBranch) {
            $officeName = $entity->receiverOfficeBranch->office?->name;
            $receiverOffice = ($officeName ? $officeName . ' / ' : '') . $entity->receiverOfficeBranch->name;
        }
        $branchName = $receiverOffice ?? $entity->receiverBranch?->name ?? $entity->receiverOfficeBranch?->name ?? 'الفرع';
        
        // 📍 رابط خرائط جوجل الخاص بفرع الاستلام
        $mapLink = $entity->receiverBranch?->map_link ?? $entity->receiverOfficeBranch?->map_link ?? null;
        
        $amountDue = (float) ($entity->amount_to_collect_from_receiver ?? 0); 

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الاخ / *{$name}* المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";

        // تحديد نص الإشعار بحسب حالة الشحنة
        if ($entity->status === 'received_at_branch') {
            $msg .= "يسعدنا إشعاركم بأن شحنتكم قد وصلت بنجاح وهي الآن *جاهزة للاستلام*.\n\n";
        } elseif ($entity->status === 'delivered') {
            $msg .= "تم تسليم شحنتكم بنجاح.\n\n";
        } else {
            $msg .= "نود إشعاركم بأن لديكم طرد مسجل باسمكم، وجاري تجهيزه وإرساله إليكم.\n\n";
        }

        $msg .= "📦 رقم الشحنة : {$bondNumber}\n";
        $msg .= "👤 المرسل : {$senderName}\n";
        if (!empty($entity->senderCustomer?->phone)) {
            $msg .= "📞 هاتف المرسل : {$entity->senderCustomer->phone}\n";
        }
        $msg .= "📍 من مكتب : {$senderPlace}\n";
        $msg .= "🏢 إلى مكتب : {$branchName}\n";

        if (!empty($entity->package_type)) {
            $msg .= "📋 نوع الطرد : {$entity->package_type}\n";
        }

        if (!empty($entity->parcels_quantity) && $entity->parcels_quantity > 1) {
            $msg .= "🔢 عدد الطرود : {$entity->parcels_quantity}\n";
        }

        // توضيح مالي دقيق
        if (!$entity->is_price_agreed) {
            $msg .= "💰 التكلفة : سيتم الاتفاق عليها لاحقاً\n";
        } elseif ($amountDue > 0) {
            $msg .= "💰 المطلوب سداده عند الاستلام : *" . number_format($amountDue) . " ريال*\n";
        } else {
            $msg .= "✅ حالة الدفع : *خالص* (لا يوجد مبلغ للتحصيل)\n";
        }

        if ($receiptUrl) {
            $msg .= "\n📄 السند الإلكتروني:\n{$receiptUrl}\n";
        }

        if ($mapLink) {
            $msg .= "\n📍 موقع فرع الاستلام (خرائط جوجل) :\n{$mapLink}\n";
        }

        if ($entity->status === 'received_at_branch') {
            $msg .= "\nنرجو منكم التكرم بزيارة الفرع لاستلام الطرد في أقرب وقت.\n";
        } else {
            $msg .= "\nيمكنكم متابعة حالة الطرد باستخدام رقم الشحنة أعلاه، وسيتم إشعاركم فور وصوله للفرع.\n";
        }

        $msg .= "نشكر ثقتكم بخدماتنا. 🌹";

        return $msg;
    }
}