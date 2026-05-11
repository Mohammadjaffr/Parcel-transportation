<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminShipmentCreated extends Notification 
{
    use Queueable;

    protected $creatorName;
    protected $branchName;
    protected $bondNumber;
    protected $shipmentId;

    public function __construct($creatorName, $branchName, $bondNumber, $shipmentId)
    {
        $this->creatorName = $creatorName;
        $this->branchName = $branchName;
        $this->bondNumber = $bondNumber;
        $this->shipmentId = $shipmentId;
    }

    public function via(object $notifiable): array
    {
        $app = $notifiable->app;
        if ($app && !$app->hasService(class_basename($this))) {
            return [];
        }
        return ['database']; 
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'admin_new_shipment',
            'message'    => "➕ طرد جديد: قام [{$this->creatorName}] في فرع [{$this->branchName}] بإنشاء الطرد رقم ({$this->bondNumber}).",
            'action_url' => route('shipment.outgoing.show', $this->shipmentId),
        ];
    }


}