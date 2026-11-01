<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Model;

class DriverAccountStatementMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Driver) {
            return null;
        }

        return $entity->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'DriverAccountStatementReceipt';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof Driver) {
            return "بيانات السائق غير صالحة.";
        }

        $driverName = $entity->name ?? 'السائق';
        $totalDebit = $entity->total_debit;
        $totalCredit = $entity->total_credit;
        $balance = $entity->balance;

        if ($balance < 0) {
            $balanceText = 'المتبقي عليك: *' . number_format(abs($balance), 0) . ' ر.ي*';
        } elseif ($balance > 0) {
            $balanceText = 'المتبقي لك: *' . number_format($balance, 0) . ' ر.ي*';
        } else {
            $balanceText = 'حسابك مصفى ✅';
        }

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأستاذ / *{$driverName}* المحترم،\n";
        $msg .= "تحية طيبة وبعد،\n\n";

        $msg .= "📋 *كشف حساب*\n";
        $msg .= "━━━━━━━━━━━━━━━\n";
        $msg .= "إجمالي ما عليك: " . number_format($totalDebit, 0) . " ر.ي\n";
        $msg .= "إجمالي ما سددته: " . number_format($totalCredit, 0) . " ر.ي\n";
        $msg .= "━━━━━━━━━━━━━━━\n";
        $msg .= "📊 {$balanceText}\n\n";

        if ($receiptUrl) {
            $msg .= "📄 لعرض كشف الحساب الكامل:\n{$receiptUrl}\n\n";
        }

        if ($balance < 0) {
            $msg .= "نأمل منكم سداد المبلغ المتبقي في أقرب وقت.\n";
        }
        $msg .= "شكراً لتعاونكم معنا 🙏";

        return $msg;
    }
}

