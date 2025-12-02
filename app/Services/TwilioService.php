<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;
    protected $whatsappFrom;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
        $this->whatsappFrom = config('services.twilio.whatsapp_from');
    }

    public function sendOTP($to, $otpCode)
    {
        try {
            $message = $this->client->messages->create(
                "whatsapp:+967781152674", 
                [
                "from" => "whatsapp:+14155238886",
                "body" => "رمز التحقق الخاص بك هو: {$otpCode} - صالح لمدة 10 دقائق"
            ]
            );

            return [
                'success' => true,
                'message_id' => $message->sid,
                'status' => $message->status
            ];

        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}