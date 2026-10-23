<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Passengers;
use Illuminate\Database\Eloquent\Model;

class PassengerRebookMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Passengers) {
            return null;
        }
        return $entity->broker?->phone ?? null;
    }

    public function getReceiptType(): ?string
    {
        return null;
    }

    public function getMessageBody(Model $entity, ?string $receiptUrl = null): string
    {
        if (!$entity instanceof Passengers) {
            return "بيانات الراكب غير صالحة.";
        }

        $brokerName    = $entity->broker->name ?? 'الوسيط';
        $passengerName = $entity->passenger_name ? trim($entity->passenger_name) : '';
        $passengerNo   = $entity->passenger_number ?? '---';
        $date          = $entity->date ? $entity->date->format('Y-m-d') : '---';
        $dayName       = $entity->date ? $entity->date->translatedFormat('l') : '';
        $pickup        = $entity->pickup_location ?? '---';
        $destination   = $entity->destination ?? '---';
        $count         = $entity->count ?? 1;
        $note          = $entity->note ?: '';
        $branchName    = $entity->branch->name ?? '';

        $msg  = "السلام عليكم ورحمة الله وبركاته\n\n";
        $msg .= "الأخ / *{$brokerName}* المحترم 🌷\n\n";
        $msg .= "نود إشعاركم بأنه تم *تأكيد حجز الراكب* ✅\n\n";
        $msg .= "*تفاصيل الحجز:*\n";
        $msg .= "━━━━━━━━━━━━━━━━━━\n";
        if ($passengerName) {
            $msg .= "👤 اسم الراكب: {$passengerName}\n";
            $msg .= "📱 رقم الراكب: {$passengerNo}\n";
        } else {
            $msg .= "👤 رقم الراكب: {$passengerNo}\n";
        }
        $msg .= "📅 التاريخ: {$date}";
        if ($dayName) {
            $msg .= " ({$dayName})";
        }
        $msg .= "\n";
        $msg .= "📍 مكان الصعود: {$pickup}\n";

        if ($destination && $destination !== '---') {
            $msg .= "🏁 الوجهة: {$destination}\n";
        }

        if (!empty($entity->vehicle_type)) {
            $msg .= "🚗 نوع المركبة: {$entity->vehicle_type}\n";
        }

        $msg .= "👥 عدد الركاب: {$count}\n";

        if ($note) {
            $msg .= "📝 ملاحظات: {$note}\n";
        }

        $msg .= "━━━━━━━━━━━━━━━━━━\n";

        if ($branchName) {
            $msg .= "\n🏢 فرع: {$branchName}\n";
        }

        return $msg;
    }
}
