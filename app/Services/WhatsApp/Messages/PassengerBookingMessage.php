<?php

namespace App\Services\WhatsApp\Messages;

use App\Interfaces\WhatsAppMessageInterface;
use App\Models\Passengers;
use Illuminate\Database\Eloquent\Model;

class PassengerBookingMessage implements WhatsAppMessageInterface
{
    public function getPhoneNumber(Model $entity): ?string
    {
        if (!$entity instanceof Passengers) {
            return null;
        }
        return $entity->passenger_number ?? null;
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

        $passengerName = $entity->passenger_name ? trim($entity->passenger_name) : '';
        $date        = $entity->date ? $entity->date->format('Y-m-d') : '---';
        $dayName     = $entity->date ? $entity->date->translatedFormat('l') : '';
        $pickup      = $entity->pickup_location ?? '---';
        $destination = $entity->destination ?? '---';
        $count       = $entity->count ?? 1;
        $note        = $entity->note ?: '';
        $branchName  = $entity->branch->name ?? '';

        $msg  = "السلام عليكم ورحمة الله وبركاته\n";
        if ($passengerName) {
            $msg .= "أهلاً بك : *{$passengerName}*\n";
        }
        $msg .= "\nتم تأكيد حجزك بنجاح ✅\n\n";
        $msg .= "*تفاصيل الحجز:*\n";
        $msg .= "━━━━━━━━━━━━━━━━━━\n";
        if ($passengerName) {
            $msg .= "👤 اسم الراكب: {$passengerName}\n";
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
        $msg .= "━━━━━━━━━━━━━━━━━━\n";

        if ($note) {
            $msg .= "\n📝 ملاحظات: {$note}\n";
        }

        $msg .= "\nنتمنى لكم رحلة آمنة وسعيدة 🚌\n";

        if ($branchName) {
            $msg .= "فرع: {$branchName}\n";
        }

        return $msg;
    }
}
