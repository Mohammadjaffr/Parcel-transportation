<?php

namespace App\Services\Evolution;

use App\Services\Evolution\EvolutionApiService;
use Illuminate\Support\Facades\Log;

class OTPService
{
    public function __construct(private EvolutionApiService $evolutionApiService)
    {
        
    }

    public function hasWhatsApp(string $phone): bool
    {
        return $this->evolutionApiService->checkNumberExists($phone);
    }
    public function sendOtp($phone, string $otp): bool
    {
        $message = "مرحباً بك في نظام مرسل \n";
        $message .= "كود التحقق الخاص بك هو: *{$otp}*\n";
        $message .= "صالح لمدة 10 دقائق. لا تشارك هذا الكود مع أحد.";

        try {
            $response = $this->evolutionApiService->sendText($phone,$message);
            return $response !== false;
        } catch (\Exception $e) {
            Log::error('WhatsApp OTP Error: ' . $e->getMessage());
            return false;
        }
    }
}