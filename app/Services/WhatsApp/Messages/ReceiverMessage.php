<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Model;

class ReceiverMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Shipment) {
            return null;
        }
        return $entity->receiverCustomer->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'receiver';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof Shipment) {
            return "بيانات الشحنة غير صالحة.";
        }
        $name = $entity->receiverCustomer->name ?? 'عميلنا العزيز';
        
        $msg = "مرحباً *$name*، 📦\n\n";
        $msg .= "تم استلام الشحنة بنجاح.\n";
        $msg .= "📌 *رقم الطرد:* {$entity->bond_number}\n";
        $msg .= "💰 *المبلغ:* " . number_format($entity->total_amount) . " ريال\n\n";
        
        if ($receiptUrl) {
            $msg .= "📄 *لعرض سند الاستلام:* \n{$receiptUrl}\n\n";
        }
        
        $msg .= "شكراً لتعاملك معنا!";
        return $msg;
    }
}