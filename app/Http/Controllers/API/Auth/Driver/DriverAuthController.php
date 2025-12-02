<?php

namespace App\Http\Controllers\API\Auth\Driver;

use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use Illuminate\Validation\Rule;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Repositories\DriverRepository;

class DriverAuthController extends Controller
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DriverRepository $driverRepository, private OtpService $otpService,private TwilioService $TwilioService)
    {
        //
    }

     public function register(Request $request){
        $fields=$request->validate([
            'phone'=>['required','string','min:9','max:15',Rule::unique('drivers','phone')],
            'whatsapp_number'=>['nullable','string','min:9','max:15'],
            'password' => ['required','string','min:6','confirmed',],
            'name'=>['required','string','max:100'],

            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')],
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('drivers', 'plate_number')],

            'vehicle_image' => ['nullable',Rule::image()],
            'driver_image' => ['nullable',Rule::image()],
            'city' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

        ]);
        
        // $user=$this->UserRepository->store($fields);
        $otp=$this->otpService->generateOTP($fields['phone'],'account_creation');
        $this->TwilioService->sendOTP($fields['phone'],strval($otp));
        // $this->HypersenderService->sendTextMessage($fields['phone'],strval($otp));
        return ApiResponseClass::sendResponse($fields['phone'],'تم إرسال رمز التحقق الى رقم الهاتف :'. $fields['phone']);
    }

    public function login(Request $request)
    {

        $fields=$request->validate([
            'phone'=>['required','string'],
            'password' => ['required','string'],
        ],[
            'phone.required' => 'حقل رقم الهاتف مطلوب.',
            'phone.string'   => 'يجب أن يكون رقم الهاتف نصًا صالحًا.',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.string'   => 'يجب أن تكون كلمة المرور نصًا صالحًا.',
        ]);
        $driver=$this->driverRepository->findByPhone($fields['phone']);
        
        if($driver && Hash::check($fields['password'], $driver->password)){

            if (is_null($driver->phone_verified_at)) {
                
                $otp=$this->otpService->generateOTP($driver->phone,'account_creation');

                // $this->HypersenderService->sendTextMessage($driver->phone,strval($otp));
        
                return ApiResponseClass::sendError("حسابك غير مفعل بعد. تم إرسال رمز تحقق جديد إليك.", null, 403);
            }
            if(!$driver->is_active){
                return ApiResponseClass::sendError('الحساب غير مفعل يرجى التواصل مع الادارة', null, 401);
            }
            $driver->tokens()->delete();
            $token = $driver->createToken($driver->name . '-AuthToken')->plainTextToken;
            return ApiResponseClass::sendResponse([
                'driver' => $driver,
                'token' => $token,
                'token_type' => 'Bearer'
            ], 'تم تسجيل الدخول بنجاح');

        }
        return ApiResponseClass::sendError('البيانات المدخلة غير صحيحه', ['error' => 'بيانات الاعتماد غير صالحة'], 401);
    }

    public function logout(Request $request)
    {
        $driver = auth('sanctum')->user();
        $driver->device_token = null;
        $driver->save();
        $driver->tokens()->delete();
        return ApiResponseClass::sendResponse(null, 'تم تسجيل الخروج بنجاح');
    }

}
