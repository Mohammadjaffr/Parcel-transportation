<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class PriceAgreementMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Shipment) {
            return null;
        }

        // إشعار تسعير الطرد يوجه للمستلم بالدرجة الأولى للاتفاق على السعر، أو المرسل كبديل
        return $entity->receiverCustomer?->phone ?? $entity->senderCustomer?->phone;
    }

    public function getReceiptType(): ?string
    {
        return 'sender';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof Shipment) {
            return "بيانات الشحنة غير صالحة.";
        }

        return $entity->getPriceAgreementMessage();
    }
}
