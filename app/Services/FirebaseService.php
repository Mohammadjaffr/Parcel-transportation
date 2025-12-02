<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
      //
    }

    public function sendNotification($deviceToken, $title, $body, $data = [])
{
    try {
        if (!$this->messaging) {
            $credentialsPath = config('firebase.projects.app.credentials') ?? env('FIREBASE_CREDENTIALS');
            
            if (empty($credentialsPath)) {
                throw new \RuntimeException('مسار ملف اعتماد Firebase غير محدد');
            }
          

            if (!file_exists($credentialsPath)) {
                throw new \RuntimeException('ملف اعتماد Firebase غير موجود في: ' . $credentialsPath);
            }

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath);

            $this->messaging = $factory->createMessaging();
        }
        if (empty($deviceToken) || !is_string($deviceToken)) {
            throw new \InvalidArgumentException('Device token غير صالح');
        }
        
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body))
            ->withData($data);
        return $this->messaging->send($message);

        
    } catch (\Kreait\Firebase\Exception\MessagingException $e) {
        \Log::error('FCM Messaging Error: ' . $e->getMessage(), [
            'device_token' => $deviceToken,
            'error' => $e->errors()
        ]);
        throw $e;
    } catch (\Exception $e) {
        \Log::error('General FCM Error: ' . $e->getMessage(), [
            'device_token' => $deviceToken,
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}
}