<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function showVerifyForm()
    {
        $phone = session('verify_phone');
        if (!$phone) {
            return redirect()->route('register');
        }
        return view('auth.verify-otp', compact('phone'));
    }

    // معالجة التحقق
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|numeric|digits:6',
        ]);

        $user = User::where('phone', $request->phone)->first();

        // التحقق من صحة الكود
        if (!$user || $user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'كود التحقق غير صحيح، يرجى المحاولة مرة أخرى.']);
        }

        // 💡 نجاح التحقق! نقوم بتحديث حالة المستخدم
        $user->update([
            'is_phone_verified' => true, // تم التحقق من الرقم!
            'otp_code' => null, // تصفير الكود لأسباب أمنية
        ]);

        // مسح الجلسة
        session()->forget('verify_phone');

        // الآن يمكننا تسجيل دخوله
        Auth::login($user);

        // توجيهه للوحة التحكم
        return redirect()->route('dashboard.index')->with('success', 'تم التحقق من رقمك بنجاح!');
    }
}