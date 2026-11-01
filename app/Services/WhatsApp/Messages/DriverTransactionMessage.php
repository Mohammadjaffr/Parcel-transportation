<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\DriverTransaction;
use Illuminate\Database\Eloquent\Model;

class DriverTransactionMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof DriverTransaction) {
            return null;
        }
        return $entity->driver->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'driver_transaction';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof DriverTransaction) {
            return "بيانات الحركة غير صالحة.";
        }

        $driver = $entity->driver;
        $name = $driver->name ?? 'السائق';
        $amount = number_format($entity->amount ?? 0, 2);

        // تحديد النص بناءً على نوع الحركة
        if ($entity->type === 'credit') {
            $typeLabel = 'سند قبض (سداد منك)';
            $action = 'تم استلام وتوثيق الدفعة النقدية بنجاح';
        } else {
            $typeLabel = 'قيد عهدة / مديونية (عليك)';
            $action = 'تم تسجيل حركة مالية على حسابك';
        }

        // حساب الرصيد الحالي
        $balance = $driver->balance;
        if ($balance < 0) {
            $balanceText = 'متبقي عليك: *' . number_format(abs($balance), 0) . ' ر.ي*';
        } elseif ($balance > 0) {
            $balanceText = 'متبقي لك: *' . number_format($balance, 0) . ' ر.ي*';
        } else {
            $balanceText = 'حسابك مصفى ✅';
        }

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأستاذ / *{$name}* المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";

        $msg .= "{$action}.\n\n";

        $msg .= "🧾 نوع الحركة: {$typeLabel}\n";
        $msg .= "💰 المبلغ: *{$amount} ر.ي*\n";
        $msg .= "📝 البيان: {$entity->description}\n\n";

        $msg .= "━━━━━━━━━━━━━━━\n";
        $msg .= "📊 {$balanceText}\n";
        $msg .= "━━━━━━━━━━━━━━━\n\n";

        if ($receiptUrl) {
            $msg .= "📄 للاطلاع على السند المالي وطباعته:\n{$receiptUrl}\n\n";
        }

        $msg .= "نشكر تعاونكم معنا 🙏";

        return $msg;
    }
}

