<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\CustomerTransaction;
use Illuminate\Database\Eloquent\Model;

class CustomerTransactionMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        // التأكد من أن المودل الممرر هو حركة مالية
        if (!$entity instanceof CustomerTransaction) {
            return null;
        }
        return $entity->customer->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        // هذا الاسم يجب أن يتطابق مع الاسم في ReceiptFactory
        return 'transaction'; 
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof CustomerTransaction) {
            return "بيانات الحركة غير صالحة.";
        }

        $name   = $entity->customer->name ?? 'عميلنا العزيز';
        $amount = number_format($entity->amount ?? 0, 2);
        
        // تحديد النص بناءً على نوع الحركة (قبض أم صرف)
        $typeLabel = $entity->type === 'credit' ? 'سند قبض (إيداع)' : 'سند صرف (قيد مديونية)';
        $action    = $entity->type === 'credit' ? 'تم استلام الدفعة النقدية بنجاح' : 'تم تسجيل حركة مالية على حسابكم';

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأستاذ / {$name} المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";

        $msg .= "{$action}.\n\n";

        $msg .= "🧾 نوع الحركة : {$typeLabel}\n";
        $msg .= "💰 المبلغ : {$amount} ريال\n";
        $msg .= "📝 البيان : {$entity->description}\n";

        if ($receiptUrl) {
            $msg .= "\n📄 للاطلاع على السند المالي وطباعته، يرجى زيارة الرابط التالي:\n{$receiptUrl}\n";
        }

        $msg .= "\nنشكر ثقتكم بخدماتنا.\n";

        return $msg;
    }
}