<?php

namespace App\Services\WhatsApp;


use App\Services\WhatsApp\Messages\DriverMessage;
use App\Services\WhatsApp\Messages\SenderMessage;
use App\Services\WhatsApp\Messages\DriverDetection;
use App\Services\WhatsApp\Messages\ReceiverMessage;
use Illuminate\Database\Eloquent\Model;


class WhatsAppLinkService
{
    
    public static function generate(Model $entity, string $target): ?string
    {
        $strategy = match ($target) {
            'sender'   => new SenderMessage(),
            'driver'   => new DriverMessage(),
            'DriverDetection'   => new DriverDetection(),
            'receiver'   => new ReceiverMessage(),
            default    => null,
        };

        if (!$strategy) {
            return null;
        }

        $phone = $strategy->getPhoneNumber($entity);
        if (!$phone) {
            return null;
        }

        $receiptUrl = null;
        if ($strategy->getReceiptType()) {
            $receiptUrl = route('receipt.generate', [
                'type' => $strategy->getReceiptType(), 
                'id'   => $entity->uuid
            ]);
        }

        $messageBody = $strategy->getMessageBody($entity, $receiptUrl);
        $encodedMessage = urlencode($messageBody);

        // تنظيف الرقم من أي مسافات أو رموز باستثناء الأرقام وعلامة +
        $cleanPhone = preg_replace('/[^\d\+]/', '', $phone);
        // إزالة علامة + من البداية لأن رابط واتساب لا يحتاجها
        $cleanPhone = ltrim($cleanPhone, '+');

        // إذا كان الرقم يبدأ بـ 00، نزيلها (مثال: 00967 يصبح 967)
        if (str_starts_with($cleanPhone, '00')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        // معالجة تكرار مفتاح اليمن (967967)
        if (str_starts_with($cleanPhone, '967967')) {
            $cleanPhone = substr($cleanPhone, 3);
        }

        // معالجة تكرار مفتاح السعودية (966966)
        if (str_starts_with($cleanPhone, '966966')) {
            $cleanPhone = substr($cleanPhone, 3);
        }

        // معالجة الأرقام المحلية التي تبدأ بصفر (مثلاً 077... تصبح 96777...)
        if (preg_match('/^0(7[0-9]\d{7})$/', $cleanPhone, $matches)) {
            $cleanPhone = '967' . $matches[1];
        }

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}