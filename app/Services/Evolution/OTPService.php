<?php

namespace App\Services\Evolution;

use App\Services\Evolution\EvolutionApiService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
    $greetings = ['مرحباً بك', 'أهلاً بك', 'تحية طيبة', 'أهلاً بك عزيزي العميل', 'يا هلا بك'];
    $systemNames = ['في نظام مرسل', 'عبر منصة مرسل', 'في بوابة مرسل', 'مع خدمات مرسل'];
    $actions = ['كود التحقق الخاص بك هو:', 'رمز الدخول الخاص بك:', 'كود التفعيل هو:', 'الرمز السري الخاص بك:'];
    $warnings = ['لا تشارك هذا الكود مع أحد.', 'يرجى الحفاظ على سرية الكود.', 'هذا الكود سري للغاية.', 'تأكد من عدم مشاركة الرمز.'];

    $greeting = $greetings[array_rand($greetings)];
    $systemName = $systemNames[array_rand($systemNames)];
    $action = $actions[array_rand($actions)];
    $warning = $warnings[array_rand($warnings)];

    $ref = Str::random(5); 
    $time = now()->format('H:i:s'); 

    $message = "{$greeting} {$systemName} 🛡️\n\n";
    $message .= "{$action} *{$otp}*\n";
    $message .= "⏳ صالح لمدة 10 دقائق. {$warning}\n\n";
    
    $message .= "💡 للمساعدة، يمكنك الرد على هذه الرسالة.\n"; 
    
    $message .= "`[Ref: {$ref}-{$time}]`";

    try {
        $response = $this->evolutionApiService->sendText($phone, $message);
        return $response !== false;
    } catch (Exception $e) {
        Log::error('WhatsApp OTP Error: ' . $e->getMessage());
        return false;
    }
}
}