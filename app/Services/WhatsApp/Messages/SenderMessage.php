<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class SenderMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Shipment) {
            return null;
        }
        return $entity->senderCustomer->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'sender';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof Shipment) {
            return "بيانات الطرد غير صالحة.";
        }
        $name = $entity->senderCustomer->name ?? 'عميلنا العزيز';
        
        $msg = "مرحباً *$name*، 📦\n\n";
        $msg .= "تم إصدار الطرد بنجاح.\n";
        $msg .= "📌 *رقم الطرد:* {$entity->bond_number}\n";
        $msg .= "💰 *المبلغ:* " . number_format($entity->total_amount) . " ريال\n\n";
        
        if ($receiptUrl) {
            $msg .= "📄 *لعرض سند الاستلام:* \n{$receiptUrl}\n\n";
        }
        
        $msg .= "شكراً لتعاملك معنا!";
        return $msg;
    }
}