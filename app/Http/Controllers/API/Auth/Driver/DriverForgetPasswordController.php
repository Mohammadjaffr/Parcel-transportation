<?php

namespace App\Http\Controllers\API\Auth\Driver;

use Exception;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use Illuminate\Validation\Rule;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\DriverRepository;

class DriverForgetPasswordController extends Controller
{
    public function __construct(private OtpService $otpService,private DriverRepository $driverRepository,private TwilioService $twilioService)
    {
        //
    }

    public function forgetPassword(Request $request) {
        $fields=$request->validate([
            'phone'=>['required','string',Rule::exists('drivers','phone')],
        ]);
        try {
            $otp = $this->otpService->generateOTP($fields['phone'],'forgetPassword');
            // $this->HypersenderService->sendTextMessage($fields['phone'],strval($otp));
            return ApiResponseClass::sendResponse(null, 'Verification code has been sent to: ' . $fields['phone']);
        } catch (Exception $e) {
            return ApiResponseClass::sendError(null, 'Failed to send verification code. Please try again later.' . $e->getMessage());
        }
    }

    public function resetPassword(Request $request) {
         $fields=$request->validate([
            'phone'=>['required','string',Rule::exists('drivers','phone')],
            'otp' => ['required', 'numeric'],
            'new_password' => ['required', 'string', 'min:8'],
        ]);
        try {
            if (!$this->otpService->verifyOTP($fields['phone'], $fields['otp'])) {
                return ApiResponseClass::sendError('Invalid or expired verification code', [], 400);
            }

            $driver = $this->driverRepository->findByPhone($fields['phone']);
            $this->driverRepository->update(['password' =>$fields['new_password']], $driver->id);
            return ApiResponseClass::sendResponse(null, 'Password has been updated successfully');
            
        } catch (Exception $e) {
            return ApiResponseClass::sendError(null,'Failed to reset password. Please try again later.' . $e->getMessage());
        }
        
    }
}
