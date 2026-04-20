<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\ShipmentPackage;
use Illuminate\Database\Eloquent\Model;
class DriverMessage implements WhatsAppMessageInterface
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
        $driverName = $entity->driver->name ?? 'كابتن';
        
        $msg = "مرحباً كابتن *$driverName*، 🚚\n\n";
        $msg .= "تم تكليفك بتوصيل شحنة جديده.\n";
        $msg .= "📌 *رقم الشحنة:* {$entity->bond_number}\n";
        $msg .= "📍 *وجهة التسليم:* " . ($entity->receiverBranch->name ?? 'غير محدد') . "\n";
        
        if ($entity->payment_method === 'cod') {
            $msg .= "⚠️ *تنبيه:* يرجى تحصيل مبلغ " . number_format($entity->total_amount) . " ريال من المستلم.\n\n";
        }

        if ($receiptUrl) {
            $msg .= "📄 *بوليصة الشحن للسائق:* \n{$receiptUrl}";
        }

        return $msg;
    }
}