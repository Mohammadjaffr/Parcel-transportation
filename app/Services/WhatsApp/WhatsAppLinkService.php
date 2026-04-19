<?php

namespace App\Services\WhatsApp;


use App\Services\WhatsApp\Messages\DriverMessage;
use App\Services\WhatsApp\Messages\SenderMessage;
use Illuminate\Database\Eloquent\Model;


class WhatsAppLinkService
{
    
    public static function generate(Model $entity, string $target): ?string
    {
        $strategy = match ($target) {
            'sender'   => new SenderMessage(),
            'driver'   => new DriverMessage(),
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

        $cleanPhone = ltrim($phone, '+');
        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}