<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewShipmentNotification extends Notification
{
    use Queueable;

    protected $senderName;
    protected $bondNumber;
    protected $shipmentId;
    protected $isInternal;

    /**
     * @param string $senderName اسم المكتب أو الفرع المرسل
     * @param string $bondNumber رقم بوليصة الشحن
     * @param int $shipmentId معرف الشحنة للرابط
     * @param bool $isInternal هل الطرد من فرع تابع لنفس الشركة؟
     */
    public function __construct($senderName, $bondNumber, $shipmentId, $isInternal = true)
    {
        $this->senderName = $senderName;
        $this->bondNumber = $bondNumber;
        $this->shipmentId = $shipmentId;
        $this->isInternal = $isInternal;
    }

    public function via(object $notifiable): array
    {
        return ['database']; 
    }

    public function toArray(object $notifiable): array
    {
        if ($this->isInternal) {
            $message = 'طرد داخلي جديد 📦: تم إرسال طرد من فرع [' . $this->senderName . '] برقم السند (' . $this->bondNumber . ').';
        } else {
            $message = 'طرد وارد 🚚: طرد قادم إليكم من مكتب [' . $this->senderName . '] برقم السند (' . $this->bondNumber . ').';
        }

        return [
            'type'       => 'new_shipment',
            'message'    => $message,
            'action_url' => route('shipment.outgoing.show', $this->shipmentId),
        ];
    }
}