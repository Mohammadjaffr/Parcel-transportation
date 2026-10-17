<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\Evolution\OTPService;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter;

class RegisteredUserController extends Controller
{
    public function __construct(private OTPService $oTPService)
    {
    }
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            'office_name' => ['required', 'string', 'max:255'],

            'branch_name' => ['required', 'string', 'max:255'],
            'branch_code' => ['nullable', 'string', 'max:50'],
            'branch_address' => ['required', 'string', 'max:255'],
            'branch_phone' => ['required', 'string', 'max:20'],
            'branch_city' => ['required', 'string', 'max:255'],
        ]);
        
        $throttleKey = 'check-whatsapp-ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 4)) {
        $seconds = RateLimiter::availableIn($throttleKey);
        return back()
            ->withInput()
            ->withErrors([
                'phone' => "لقد قمت بمحاولات كثيرة. يرجى الانتظار {$seconds} ثانية قبل المحاولة مرة أخرى."
            ]);
        }
        
        // if (!$this->oTPService->hasWhatsApp($request->phone)) {
        //     return back()
        //         ->withInput() 
        //         ->withErrors([
        //             'phone' => 'هذا الرقم غير مسجل في الواتساب. يرجى إدخال رقم واتساب فعال لاستلام كود التحقق.'
        //         ]);
        // }
        RateLimiter::clear($throttleKey);
        $otpCode = '123456';
        $user = DB::transaction(function () use ($request,$otpCode) {
            
            $app = App::create([
                'name' =>  $request->office_name, 
                'is_active' => false,
                'terms_and_conditions' => [
                    'نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا.',
                    'نحن غير مسؤولين عن الأشياء الثمينة الممنوع إرسالها في الطرود.',
                    'نحن غير مسؤولين عن بقاء الطرود أكثر من شهر.',
                    'نحن غير مسؤولين عن الحريق وحوادث السير.',
                    'الرجاء التأكد من بيانات السند قبل المغادرة.'
                ],
            ]);

            $branchCode = $request->filled('branch_code') ? strtoupper(trim($request->branch_code)) : null;
            if (!$branchCode) {
                $sourceText = $request->branch_city ?: $request->branch_name;
                $cityMap = [
                    'عدن' => 'ADN', 'صنعاء' => 'SNA', 'تعز' => 'TAI', 'حضرموت' => 'HAD',
                    'المكلا' => 'MUK', 'الحديدة' => 'HUD', 'إب' => 'IBB', 'لحج' => 'LAJ',
                    'أبين' => 'ABY', 'شبوة' => 'SHA', 'مأرب' => 'MAR', 'المهرة' => 'MAH',
                    'البيضاء' => 'BAY', 'حجة' => 'HAJ', 'صعدة' => 'SAD', 'الضالع' => 'DAL',
                    'عمران' => 'AMR', 'المحويت' => 'MAW', 'الجوف' => 'JAW', 'ذمار' => 'DHA',
                    'ريمة' => 'RAY', 'سقطرى' => 'SOC', 'الرياض' => 'RUH', 'جدة' => 'JED',
                    'الدمام' => 'DMM', 'مكة' => 'MAK', 'المدينة' => 'MED', 'الطائف' => 'TIF'
                ];
                foreach ($cityMap as $city => $c) {
                    if (str_contains($sourceText, $city)) {
                        $branchCode = $c;
                        break;
                    }
                }
                if (!$branchCode) {
                    $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $sourceText));
                    $branchCode = substr($clean, 0, 3) ?: 'HQ1';
                }
            }

            $branch = Branch::create([
                'app_id' => $app->id,
                'name' => $request->branch_name,
                'code' => $branchCode,
                'address' => $request->branch_address,
                'phone' => $request->branch_phone,
                'is_main' => true, 
                'city' => $request->branch_city, 
            ]);

            $serviceIds = Service::pluck('id');
            if ($serviceIds->isNotEmpty()) {
                $app->services()->syncWithPivotValues($serviceIds, ['is_active' => true]);
            }
           
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'app_id' => $app->id,
                'branch_id' => $branch->id,
                'type' => 'admin',
                'otp_code' => $otpCode,
                'is_phone_verified' => false, 
            ]);
            return $user;
        });
        $this->oTPService->sendOtp($user->phone, $otpCode);
        event(new Registered($user));

        // Auth::login($user);
        session(['verify_phone' => $user->phone]);
        return redirect()->route('otp.verify.form');
        // return redirect(route('dashboard.index', absolute: false));
    }
}