<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Passengers;
use Illuminate\Database\Eloquent\Model;

class PassengerDriverMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Passengers) {
            return null;
        }
        return $entity->driver->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'passengerDriver';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl = null): string
    {
        // 1. التحقق من نوع الكائن (موديل الركاب) وتصحيح رسالة الخطأ
        if (!$entity instanceof Passengers) {
            return "بيانات الركاب غير صالحة.";
        }

        // 2. استخدام $entity بدلاً من $this لجلب البيانات
        $driverName = $entity->driver->name ?? 'السائق';
        $pNum       = $entity->passenger_number ?? '---';
        $pLoc       = $entity->location ?? '---';
        $pCnt       = $entity->count ?? 0;
        $pNote      = $entity->note ?: '---';

        // الأولوية للرابط الممرر في الدالة، وإلا نستخدم الموجود في الموديل
        $pdfLink    = $receiptUrl ?? ($entity->driver_pdf_link ?? 'غير متوفر');

        // 3. بناء نص الرسالة
        $msg  = "السلام عليكم ورحمة الله وبركاته\n";
        $msg .= "الأخ الكابتن / *{$driverName}* المحترم،\n\n";
        $msg .= "تم تكليفك بنقل الراكب التالي:\n\n";
        $msg .= "*تفاصيل الراكب:*\n";
        $msg .= "------------------------------------------\n";
        $msg .= "الراكب: {$pNum}\n";
        $msg .= "📍 المكان: {$pLoc}\n";
        $msg .= "👥 العدد: {$pCnt} راكب\n";

        if ($entity->note) {
            $msg .= "📝 ملاحظات: {$pNote}\n";
        }

        $msg .= "------------------------------------------\n\n";
        $msg .= "📄 رابط كشف الـ PDF للراكب:\n{$pdfLink}\n\n";
        $msg .= "رافقتكم السلامة. 🚚";

        // إذا لم يكن هناك رقم سائق، نعيد نص الرسالة فقط لتجنب الأخطاء
        if (!$entity->driver || !$entity->driver->phone) {
            return $msg;
        }

        // 4. ترميز الرسالة وتنظيف رقم الهاتف لإنشاء رابط واتساب
        $encodedMessage = urlencode($msg);

        // إزالة أي رموز غير الأرقام وإزالة الصفر من البداية إن وجد
        $cleanPhone = preg_replace('/[^\d]/', '', $entity->driver->phone);
        $cleanPhone = ltrim($cleanPhone, '0');

        // معالجة التكرارات في مفاتيح الدول
        if (str_starts_with($cleanPhone, '00')) {
            $cleanPhone = substr($cleanPhone, 2);
        }
        if (str_starts_with($cleanPhone, '967967')) {
            $cleanPhone = substr($cleanPhone, 3);
        }
        if (str_starts_with($cleanPhone, '966966')) {
            $cleanPhone = substr($cleanPhone, 3);
        }

        // تحويل الرقم اليمني المحلي إلى دولي (شامل لجميع الشبكات 77, 73, 71, 70, 78)
        if (strlen($cleanPhone) === 9 && preg_match('/^(77|73|71|70|78)/', $cleanPhone)) {
            $cleanPhone = '967' . $cleanPhone;
        }

        // إرجاع رابط الواتساب الجاهز
        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}