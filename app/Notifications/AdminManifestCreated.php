<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminManifestCreated extends Notification
{
    use Queueable;

    protected $manifest;

    public function __construct($manifest)
    {
        $this->manifest = $manifest;
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
        $creatorName = $this->manifest->creator->name ?? 'موظف';
        $branchName = $this->manifest->senderBranch->name ?? 'الفرع';
        $driverName = $this->manifest->driver->name ?? 'غير محدد';
        $parcelsCount = $this->manifest->shipments()->count();
        $trackingNumber = $this->manifest->tracking_number;

        return [
            'type'       => 'admin_new_manifest',
            'message'    => "🚚 إرسالية جديدة: قام [{$creatorName}] في [{$branchName}] بإنشاء الإرسالية ({$trackingNumber})، تحتوي على ({$parcelsCount}) طرود، مع السائق [{$driverName}].",
            'action_url' => route('shipmentpackage.outgoing.show', $this->manifest->id),
        ];
    }
}