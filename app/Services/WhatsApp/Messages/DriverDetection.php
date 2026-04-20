<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\ShipmentPackage;
use Illuminate\Database\Eloquent\Model;

class DriverDetection implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof ShipmentPackage) {
            return null;
        }
        return $entity->driver->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return 'DriverDetection';
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl): string
    {
        if (!$entity instanceof ShipmentPackage) {
            return "بيانات الشحنة غير صالحة.";
        }
        $name = $entity->driver->name ?? 'عميلنا العزيز';
        
        $msg = "مرحباً *$name*، 📦\n\n";
        $msg .= "تم إصدار الشحنة بنجاح.\n";
        $msg .= "📌 *رقم الطرد:* {$entity->tracking_number}\n";
        
        if ($receiptUrl) {
            $msg .= "📄 *لعرض سند الاستلام:* \n{$receiptUrl}\n\n";
        }
        
        $msg .= "شكراً لتعاملك معنا!";
        return $msg;
    }
}