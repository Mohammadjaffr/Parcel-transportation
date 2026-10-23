<?php

namespace App\Services\WhatsApp;

use App\Services\WhatsApp\Messages\DriverMessage;
use App\Services\WhatsApp\Messages\SenderMessage;
use App\Services\WhatsApp\Messages\DriverDetection;
use App\Services\WhatsApp\Messages\ExternalOfficeDetection;
use App\Services\WhatsApp\Messages\ReceiverMessage;
use App\Services\WhatsApp\Messages\CustomerAccountStatementMessage;
use App\Services\WhatsApp\Messages\CustomerTransactionMessage; 
use App\Services\WhatsApp\Messages\PassengerDriverMessage;
use App\Services\WhatsApp\Messages\PassengerBookingMessage;
use Illuminate\Database\Eloquent\Model;
use App\Models\PassengerTrip;
use App\Services\WhatsApp\Messages\TripDriverMessage;
use App\Services\WhatsApp\Messages\PriceAgreementMessage;
use App\Services\WhatsApp\Messages\PassengerCancelMessage;
use App\Services\WhatsApp\Messages\PassengerRebookMessage;


class WhatsAppLinkService
{
    public static function generate(Model $entity, string $target): ?string
    {
        $strategy = match ($target) {
            'sender'   => new SenderMessage(),
            'driver'   => new DriverMessage(),
            'DriverDetection' => new DriverDetection(),
            'ExternalOfficeDetection' => new ExternalOfficeDetection(),
            'receiver' => new ReceiverMessage(),
            'price_agreement' => new PriceAgreementMessage(),
            'CustomerAccountStatement' => new CustomerAccountStatementMessage(),
            'transaction' => new CustomerTransactionMessage(), 
            'passengerDriver' => new PassengerDriverMessage(),
            'tripDriver' => new TripDriverMessage(),
            'passengerBooking' => new PassengerBookingMessage(),
            'passengerCancel' => new PassengerCancelMessage(),
            'passengerRebook' => new PassengerRebookMessage(),
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
            $entityId = $entity->uuid ?? $entity->id;
            
            $receiptUrl = route('receipt.generate', [
                'type' => $strategy->getReceiptType(), 
                'id'   => $entityId
            ]);
        }

        $messageBody = $strategy->getMessageBody($entity, $receiptUrl);
        $encodedMessage = urlencode($messageBody);

        // تنظيف الرقم من أي مسافات أو رموز باستثناء الأرقام
        $cleanPhone = preg_replace('/[^\d]/', '', $phone);

        // إذا كان الرقم يبدأ بـ 00، نزيلها (مثال: 00967 يصبح 967)
        while (str_starts_with($cleanPhone, '00')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        // معالجة تكرار مفتاح اليمن (967967)
        while (str_starts_with($cleanPhone, '967967')) {
            $cleanPhone = substr($cleanPhone, 3);
        }

        // معالجة تكرار مفتاح السعودية (966966)
        while (str_starts_with($cleanPhone, '966966')) {
            $cleanPhone = substr($cleanPhone, 3);
        }

        // معالجة 967 متبوعة بصفر محلي
        if (str_starts_with($cleanPhone, '9670')) {
            $cleanPhone = '967' . substr($cleanPhone, 4);
        }

        // معالجة 966 متبوعة بصفر محلي
        if (str_starts_with($cleanPhone, '9660')) {
            $cleanPhone = '966' . substr($cleanPhone, 4);
        }

        // معالجة الأرقام السعودية التي تبدأ بـ 05
        if (str_starts_with($cleanPhone, '05')) {
            $cleanPhone = '966' . substr($cleanPhone, 1);
        }
        // معالجة الأرقام المحلية التي تبدأ بصفر (مثلاً 07... أو 0...)
        elseif (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '967' . substr($cleanPhone, 1);
        }
        // أي رقم يبدأ بـ 7 ولا يبدأ بـ 967 فهو رقم يمني محلي
        elseif (str_starts_with($cleanPhone, '7') && !str_starts_with($cleanPhone, '967')) {
            $cleanPhone = '967' . $cleanPhone;
        }
        // أي رقم يبدأ بـ 5 ولا يبدأ بـ 966 وطوله حتى 10 أرقام فهو رقم سعودي محلي
        elseif (str_starts_with($cleanPhone, '5') && !str_starts_with($cleanPhone, '966') && strlen($cleanPhone) <= 10) {
            $cleanPhone = '966' . $cleanPhone;
        }
        // إذا كان الرقم لا يبدأ بـ 967 ولا 966 وطوله أقل أو يساوي 9
        elseif (!str_starts_with($cleanPhone, '967') && !str_starts_with($cleanPhone, '966') && strlen($cleanPhone) <= 9) {
            $cleanPhone = '967' . $cleanPhone;
        }

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}