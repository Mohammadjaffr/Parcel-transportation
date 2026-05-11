<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class IncomingPackageNotification extends Notification
{
    use Queueable;

    public $tracking_number;
    public $shipmentsCount;

    public function __construct($tracking_number, $shipmentsCount)
    {
        $this->tracking_number = $tracking_number;
        $this->shipmentsCount = $shipmentsCount;
    }

    public function via($notifiable)
    {
        $app = $notifiable->app;
        if ($app && !$app->hasService(class_basename($this))) {
            return [];
        }
        return ['database']; 
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "انطلقت الإرسالية رقم ({$this->tracking_number}) وهي تحتوي على {$this->shipmentsCount} طرود مخصصة لفرعكم. يرجى الاستعداد لاستلامها.",
            'action_url'     => route('shipmentpackage.incoming.index'),
            'type'    => 'incoming_package',
        ];
    }
}