<?php

namespace App\Http\Controllers\API\Auth\User;

use Exception;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\TwilioService;
use App\Classes\ApiResponseClass;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class UserForgetPasswordController extends Controller
{
    public function __construct(private OtpService $otpService,private UserRepository $UserRepository,private TwilioService $TwilioService)
    {
        //
    }

    public function forgetPassword(Request $request) {
        $fields=$request->validate([
            'phone'=>['required','string',Rule::exists('users','phone')],
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
            'phone'=>['required','string',Rule::exists('users','phone')],
            'otp' => ['required', 'numeric'],
            'new_password' => ['required', 'string', 'min:8'],
        ]);
        try {
            if (!$this->otpService->verifyOTP($fields['phone'], $fields['otp'])) {
                return ApiResponseClass::sendError('Invalid or expired verification code', [], 400);
            }

            $user = $this->UserRepository->findByPhone($fields['phone']);
            $this->UserRepository->update(['password' =>$fields['new_password']], $user->id);
            return ApiResponseClass::sendResponse(null, 'Password has been updated successfully');
            
        } catch (Exception $e) {
             return ApiResponseClass::sendError(null,'Failed to reset password. Please try again later.' . $e->getMessage());
        }
        
    }
}
