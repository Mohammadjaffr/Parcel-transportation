<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ConnectionAcceptedNotification extends Notification
{
    use Queueable;
    protected $responderApp;

    public function __construct($responderApp)
    {
        $this->responderApp = $responderApp; // المكتب الذي وافق على الطلب
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
            'type'            => 'connection_accepted',
            'message'         => 'وافق مكتب ' . $this->responderApp->name . ' على طلب الربط الخاص بك. يمكنكما الآن تبادل الشحنات!',
            'action_url'      => route('app.index'), 
        ];
    }
}