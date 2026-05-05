<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;

class CustomerAccountStatementMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Customer) {
            return null;
        }

        return $entity->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'CustomerAccountStatementReceipt';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof Customer) {
            return "بيانات العميل غير صالحة.";
        }

        $customerName = $entity->name ?? 'العميل';

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخ / {$customerName} المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";

        $msg .= "نرفق لكم كشف الحساب الخاص بكم، والذي يوضح تفاصيل الحركات المالية والطرود المرتبطة بالحساب.\n\n";

        if ($receiptUrl) {
            $msg .= "رابط كشف الحساب:\n";
            $msg .= "{$receiptUrl}\n\n";
        }

        $msg .= "يرجى مراجعة الكشف، وفي حال وجود أي ملاحظات يمكنكم التواصل معنا.\n\n";
        $msg .= "شاكرين لكم ثقتكم وتعاملكم معنا.\n\n";
        $msg .= "إدارة النظام";

        return $msg;
    }
}