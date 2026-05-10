<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\Channel;

class AdminShipmentCreated extends Notification implements ShouldBroadcastNow
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
        return ['database', 'broadcast']; 
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'admin_new_shipment',
            'message'    => "➕ طرد جديد: قام [{$this->creatorName}] في فرع [{$this->branchName}] بإنشاء الطرد رقم ({$this->bondNumber}).",
            'action_url' => route('shipment.outgoing.show', $this->shipmentId),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type'       => 'admin_new_shipment',
            'message'    => "➕ طرد جديد: قام [{$this->creatorName}] في فرع [{$this->branchName}] بإنشاء الطرد رقم ({$this->bondNumber}).",
            'action_url' => route('shipment.outgoing.show', $this->shipmentId),
        ]);
    }
    public function broadcastOn()
    {
        return new Channel('admin-channel');
    }
    public function broadcastAs()
    {
        return 'new-shipment';
    }
}