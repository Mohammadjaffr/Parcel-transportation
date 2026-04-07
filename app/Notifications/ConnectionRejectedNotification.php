<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConnectionRejectedNotification extends Notification
{
    use Queueable;
    protected $responderApp;

    public function __construct($responderApp)
    {
        $this->responderApp = $responderApp; // المكتب الذي رفض الطلب
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'            => 'connection_rejected',
            'message'         => 'عذراً، قام مكتب ' . $this->responderApp->name . ' برفض طلب الربط الخاص بك.',
            'action_url'      => route('app.index'), 
        ];
    }
}