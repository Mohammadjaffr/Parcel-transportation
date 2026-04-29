<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\Evolution\OTPService;

class OtpController extends Controller
{
    public function __construct(private OTPService $oTPService) {}
    public function showVerifyForm()
    {
        $phone = session('verify_phone');
        if (!$phone) {
            return redirect()->route('register');
        }

        $throttleKey = 'verify-otp:' . $phone;
        $resendKey = 'resend-otp:' . $phone;

        // جلب الوقت المتبقي من أي حظر نشط
        $lockoutSeconds = RateLimiter::availableIn($throttleKey);
        $resendSeconds = RateLimiter::availableIn($resendKey);
        $initialSeconds = max($lockoutSeconds, $resendSeconds);

        $isLockedOut = RateLimiter::tooManyAttempts($throttleKey, 3);

        return view('auth.verify-otp', [
            'phone' => $phone,
            'isLockedOut' => $isLockedOut,
            'initialSeconds' => $initialSeconds
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|numeric|digits:6',
        ]);

        $phone = $request->phone;
        $throttleKey = 'verify-otp:' . $phone;
        $penaltyTierKey = 'otp-tier:' . $phone;

        // 1. التحقق من الحظر
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->with('error', 'تم قفل إدخال الكود. يرجى الانتظار حتى ينتهي الوقت.')
                ->with('locked_out', true)
                ->with('lockout_seconds', $seconds);
        }

        $user = User::where('phone', $phone)->first();

        // 2. إذا الكود خاطئ
        if (!$user || $user->otp_code !== $request->otp) {
            $tier = cache()->get($penaltyTierKey, 1);

            $decayTimes = [
                1 => 60,      // دقيقة
                2 => 180,     // 3 دقائق
                3 => 300,     // 5 دقائق
                4 => 900      // 15 دقيقة
            ];

            $decaySeconds = $decayTimes[$tier] ?? 3600;

            RateLimiter::hit($throttleKey, $decaySeconds);
            $attemptsLeft = 3 - RateLimiter::attempts($throttleKey);

            if ($attemptsLeft <= 0) {
                cache()->put($penaltyTierKey, $tier + 1, now()->addHours(24));
                $seconds = RateLimiter::availableIn($throttleKey);
                return back()->with('error', 'استنفدت جميع المحاولات! تم قفل الإدخال لتجاوز الحد المسموح.')
                    ->with('locked_out', true)
                    ->with('lockout_seconds', $seconds);
            }

            return back()->withErrors(['otp' => "كود التحقق غير صحيح، يتبقى لك {$attemptsLeft} محاولات."]);
        }

        // 3. نجاح التحقق
        RateLimiter::clear($throttleKey);
        RateLimiter::clear('resend-otp:' . $phone);
        cache()->forget($penaltyTierKey);

        $user->update([
            'is_phone_verified' => true,
            'otp_code' => null,
        ]);

        session()->forget('verify_phone');
        Auth::login($user);

        return redirect()->route('dashboard.index')->with('success', 'تم التحقق من رقمك بنجاح!');
    }
    public function resend(Request $request)
    {
        $phone = $request->phone ?? session('verify_phone');

        if (!$phone) {
            return redirect()->route('register')->with('error', 'انتهت الجلسة، يرجى التسجيل مرة أخرى.');
        }

        $resendKey = 'resend-otp:' . $phone;
        $resendTierKey = 'resend-tier:' . $phone;

        if (RateLimiter::tooManyAttempts($resendKey, 1)) {
            $seconds = RateLimiter::availableIn($resendKey);
            return back()->with('error', "يرجى الانتظار {$seconds} ثانية قبل طلب كود جديد.");
        }

        RateLimiter::clear('verify-otp:' . $phone);

        $tier = cache()->get($resendTierKey, 1);
        $delays = [
            1 => 60,
            2 => 180,
            3 => 300,
            4 => 900
        ];
        $delaySeconds = $delays[$tier] ?? 3600;

        $user = User::where('phone', $phone)->first();
        if ($user) {
            $newOtp = (string) random_int(100000, 999999);
            $user->update(['otp_code' => $newOtp]);

            try {
                // 🚀 هنا تم تفعيل إرسال الواتساب الفعلي!
                $this->oTPService->sendOtp($phone, $newOtp);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('فشل إرسال كود الواتساب (إعادة إرسال): ' . $e->getMessage());
            }
        }

        session(['verify_phone' => $phone]);

        cache()->put($resendTierKey, $tier + 1, now()->addHours(12));
        RateLimiter::hit($resendKey, $delaySeconds);

        return back()->with('success', 'تم إرسال كود جديد إلى الواتساب الخاص بك.');
    }
}
