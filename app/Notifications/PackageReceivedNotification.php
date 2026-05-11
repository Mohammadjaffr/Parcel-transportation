<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PackageReceivedNotification extends Notification
{
    use Queueable;

    public $packageTrackingNumber;
    public $receiverBranchName;
    public $shipmentTrackingNumber;
    public $shipmentId;

    /**
     * استقبال البيانات من الكنترولر
     */
    public function __construct($packageTrackingNumber, $receiverBranchName, $shipmentTrackingNumber, $shipmentId)
    {
        $this->packageTrackingNumber = $packageTrackingNumber;
        $this->receiverBranchName = $receiverBranchName;
        $this->shipmentTrackingNumber = $shipmentTrackingNumber;
        $this->shipmentId = $shipmentId;
    }

    /**
     * تحديد قناة الإرسال (قاعدة البيانات فقط لعرضها في الواجهة)
     */
    public function via($notifiable): array
    {
        $app = $notifiable->app;
        if ($app && !$app->hasService(class_basename($this))) {
            return [];
        }
        return ['database'];
    }

    /**
     * هيكلة البيانات التي سيتم حفظها في قاعدة البيانات وقراءتها في الـ Header
     */
    public function toDatabase($notifiable): array
    {
        return [
            'message' => "تم استلام الطرد رقم ({$this->shipmentTrackingNumber}) التابع للإرسالية ({$this->packageTrackingNumber}) من قِبل [{$this->receiverBranchName}].",
            'action_url'     => route('shipment.outgoing.show', $this->shipmentId), // يمكنك تغيير الراوت ليوجه المستخدم لصفحة الطرود المرسلة
            'type'    => 'package_received', // النوع الذي برمجناه في الواجهة (Header) ليعطيه اللون الأخضر
        ];
    }
}