<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConnectionRequestNotification extends Notification
{
    use Queueable;
    protected $senderApp;
    protected $connection;

    /**
     * Create a new notification instance.
     */
    public function __construct($senderApp,$connection)
    {
        $this->senderApp = $senderApp;
        $this->connection = $connection;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sender_id' => $this->senderApp->id,
            'connection_id'   => $this->connection->id,
            'sender_name' => $this->senderApp->name,
            'message' => 'يرغب مكتب ' . $this->senderApp->name . ' في الربط مع مكتبك.',
            'action_url' => route('app.index') ,
            'type' => 'connection_request'
        ];
    }
}
