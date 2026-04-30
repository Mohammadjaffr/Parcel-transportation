<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminShipmentStatusUpdated extends Notification
{
    use Queueable;

    protected $updaterName;
    protected $bondNumber;
    protected $statusName;
    protected $shipmentId;

    public function __construct($updaterName, $bondNumber, $statusName, $shipmentId)
    {
        $this->updaterName = $updaterName;
        $this->bondNumber = $bondNumber;
        $this->statusName = $statusName;
        $this->shipmentId = $shipmentId;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        // تخصيص الأيقونة واللون بناءً على الحالة
        $icon = 'update';
        if (str_contains($this->statusName, 'تم التسليم')) $icon = 'check_circle';
        if (str_contains($this->statusName, 'مرتجع')) $icon = 'assignment_return';
        if (str_contains($this->statusName, 'النقل')) $icon = 'local_shipping';

        return [
            'type'       => 'admin_status_updated',
            'message'    => "🔄 تحديث طرد: قام الموظف [{$this->updaterName}] بتحديث حالة الطرد رقم ({$this->bondNumber}) إلى: {$this->statusName}.",
            'action_url' => route('shipment.outgoing.show', $this->shipmentId), // تأكد من اسم راوت التفاصيل لديك
            
        ];
    }
}