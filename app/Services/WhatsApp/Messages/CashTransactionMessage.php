<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\CashTransaction;
use Illuminate\Database\Eloquent\Model;

class CashTransactionMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof CashTransaction) {
            return null;
        }

        return $entity->driver_phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'cash_ledger';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof CashTransaction) {
            return "بيانات السند غير صالحة.";
        }

        $appName    = $entity->app->name ?? 'مُرسَل للنقل والترحيل';
        $branchName = $entity->branch->name ?? '-';
        $typeTitle  = $entity->type === 'income' ? 'سند قبض نقدية (وارد)' : 'سند صرف نقدية (منصرف)';
        $actionWord = $entity->type === 'income' ? 'تم استلام وتوثيق السند المالي بنجاح' : 'تم صرف وتوثيق السند المالي بنجاح';
        $amount     = number_format($entity->amount, 2);
        $date       = $entity->transaction_date ? $entity->transaction_date->format('Y-m-d') : now()->format('Y-m-d');
        $payment    = $entity->payment_method === 'cash' ? 'نقداً (كاش الخزينة)' : 'تحويل بنكي / حوالة';
        $category   = $entity->category->name ?? '-';

        $msg  = "السلام عليكم ورحمة الله وبركاته 💐\n\n";
        $msg .= "🏢 *{$appName}* (فرع: {$branchName})\n";
        $msg .= "🧾 *إشعار مالي: {$typeTitle}*\n";
        $msg .= "━━━━━━━━━━━━━━━━━━\n";
        $msg .= "{$actionWord}:\n\n";

        $msg .= "🔢 *رقم السند:* {$entity->receipt_number}\n";
        $msg .= "📅 *التاريخ:* {$date}\n";
        $msg .= "📂 *التصنيف:* {$category}\n";
        $msg .= "💰 *المبلغ:* {$amount} ريال يمني\n";
        $msg .= "💳 *طريقة الدفع:* {$payment}\n";

        if (!empty($entity->driver_name)) {
            $driverText = $entity->driver_name;
            if (!empty($entity->driver_phone)) {
                $driverText .= " ({$entity->driver_phone})";
            }
            $msg .= "🚗 *السائق:* {$driverText}\n";
        }

        if (!empty($entity->reference_number)) {
            $msg .= "🔖 *رقم المرجع:* {$entity->reference_number}\n";
        }

        if (!empty($entity->notes)) {
            $msg .= "📝 *البيان:* {$entity->notes}\n";
        }

        $msg .= "━━━━━━━━━━━━━━━━━━\n";

        if ($receiptUrl) {
            $msg .= "📄 *للاطلاع على السند الرسمي وطباعته:*\n{$receiptUrl}\n\n";
        }

        $msg .= "سعداء بخدمتكم دائماً ✨\n";

        return $msg;
    }
}
