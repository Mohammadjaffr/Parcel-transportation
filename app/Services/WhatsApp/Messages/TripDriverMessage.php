<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\PassengerTrip;
use Illuminate\Database\Eloquent\Model;

class TripDriverMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof PassengerTrip) {
            return null;
        }
        return $entity->driver->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'trip';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl = null): string
    {
        // 1. التحقق من نوع الكائن (موديل الركاب) وتصحيح رسالة الخطأ
        if (!$entity instanceof PassengerTrip) {
            return "بيانات الركاب غير صالحة.";
        }

        // 2. استخدام $entity بدلاً من $this لجلب البيانات
        $driverName = $entity->driver->name ?? 'السائق';
        
        // الأولوية للرابط الممرر في الدالة، وإلا نستخدم الموجود في الموديل
        $pdfLink    = $receiptUrl ?? ($entity->driver_pdf_link ?? 'غير متوفر');

        // 3. بناء نص الرسالة
        $msg  = "السلام عليكم ورحمة الله وبركاته 🤝\n";
        $msg .= "الأخ الكابتن / *{$driverName}* المحترم،\n\n";
        $msg .= "إليك تفاصيل رحلتك رقم *#{$entity->id}* 🚐:\n";
        $msg .= "------------------------------------------\n";

        $i = 1;
        $tripTotalCount = 0;

        foreach ($entity->passengers as $p) {
            $pNum    = $p->passenger_number ?? '---';
            $pPickup = $p->pickup_location ?? '---';
            $pDest   = $p->destination ?? '---';
            $pCnt    = $p->count ?? 0;
            $pNote   = $p->note ?: '---';

            $tripTotalCount += $pCnt;

            $msg .= "👤 *الراكب ({$i}):* {$pNum}\n";
            $msg .= "📍 *من:* {$pPickup}\n";
            $msg .= "🏁 *إلى:* {$pDest}\n";
            $msg .= "👥 *العدد:* {$pCnt}\n";

            if ($pNote !== '---') {
                $msg .= "📝 *ملاحظات:* {$pNote}\n";
            }
            $msg .= "------------------------------------------\n";
            $i++;
        }

        $msg .= "\n📊 *إجمالي الركاب:* {$tripTotalCount} راكب\n\n";
        $msg .= "📄 *رابط كشف الرحلة (PDF):*\n{$pdfLink}\n\n";
        $msg .= "رافقتكم السلامة 🚚";

        return $msg;
    }
}