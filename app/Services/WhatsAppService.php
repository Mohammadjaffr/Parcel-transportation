<?php
// app/Services/WhatsAppService.php

namespace App\Services;

use App\Models\Shipment;

class WhatsAppService
{
    public function createWhatsAppLink($phone, $message)
    {
        $formattedPhone = $this->formatPhone($phone);
        $encodedMessage = urlencode($message);
        
        return "https://web.whatsapp.com/send?phone={$formattedPhone}&text={$encodedMessage}";
    }
    
    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '966' . substr($phone, 1);
        }
        
        return $phone;
    }
    
    public function getSenderLink(Shipment $shipment)
    {
        $message = $this->createSenderMessage($shipment);
        return $this->createWhatsAppLink($shipment->sender_phone, $message);
    }
    
    public function getReceiverLink(Shipment $shipment)
    {
        $message = $this->createReceiverMessage($shipment);
        return $this->createWhatsAppLink($shipment->receiver_phone, $message);
    }
    
    public function createSenderMessage(Shipment $shipment)
    {
        $codText = '';
        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {
            $codText = "\n💰 مبلغ الدفع عند الاستلام: " . number_format($shipment->cod_amount, 2) . " ريال";
        }
        
        $branchName = $shipment->branch ? $shipment->branch->name : 'الفرع الرئيسي';
        
        return "📦 *تأكيد شحن الطرد*

📮 *رقم التتبع:* {$shipment->id}
👤 *المستلم:* {$shipment->receiver_name}
📍 *مدينة المستلم:* {$shipment->to_city}
🏢 *الفرع:* {$branchName}
📊 *نوع الطرد:* {$shipment->package_type}
⚖️ *الوزن:* {$shipment->weight} كجم" . 
        ($shipment->notes ? "\n📝 *ملاحظات:* {$shipment->notes}" : "") . "
🕒 *تاريخ الشحن:* " . now()->format('Y-m-d H:i') . "
✅ *الحالة:* تم استلام الطرد{$codText}

شكراً لثقتك بنا! 🌟";
    }

   
    public function createReceiverMessage(Shipment $shipment)
    {
        $codText = '';
        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {
            $codText = "\n💰 *مطلوب منك:* " . number_format($shipment->cod_amount, 2) . " ريال (دفع عند الاستلام)";
        }
        
        $paymentMethodText = [
            'cash' => '💳 مدفوع',
            'cod' => '💵 دفع عند الاستلام',
            'online' => '💻 مدفوع إلكترونياً'
        ][$shipment->payment_method] ?? '💳 مدفوع';
        
        $branchName = $shipment->branch ? $shipment->branch->name : 'الفرع الرئيسي';
        
        return "📦 *إشعار استلام طرد*

مرحباً {$shipment->receiver_name} 👋

📮 *رقم التتبع:* {$shipment->tracking_number}
👤 *المرسل:* {$shipment->sender_name}
📍 *مدينة المرسل:* {$shipment->from_city}
🏢 *الفرع:* {$branchName}
📊 *نوع الطرد:* {$shipment->package_type}
⚖️ *الوزن:* {$shipment->weight} كجم
💸 *طريقة الدفع:* {$paymentMethodText}{$codText}" .
        ($shipment->notes ? "\n📝 *ملاحظات:* {$shipment->notes}" : "") . "
⏰ *التوقيت:* سيتم التوصيل خلال 24-48 ساعة عمل

يرجى التأكد من وجود شخص لاستلام الطرد.
يمكنك تتبع شحنتك عبر الرقم أعلاه. 🔍";
    }
}