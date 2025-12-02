<?php

namespace App\Services;

use App\Repositories\OtpRepository;

class OtpService{
    public function __construct(private OtpRepository $OtpRepository)
    {
        
    }
    public function generateOTP($phone,$purpose)
    {
        $existingOtp=$this->OtpRepository->findByPhone($phone);
        if($existingOtp){
            $this->OtpRepository->delete($existingOtp->id);
        }
        $otp = rand(100000, 999999); 
        $expiresAt = now()->addMinutes(10); 
        $data=[
            'phone' => $phone,
            'code' => $otp,
            'expires_at' => $expiresAt,
            'purpose' => $purpose,
        ];
        $this->OtpRepository->store($data);
        return $otp; 
    }

    public function verifyOTP($phone, $code)
    {
        $otp = $this->OtpRepository->verifyOTP($phone, $code);

        if ($otp) {
            $otp->is_used = true;
            $otp->save();
            return true;
        }

        return false; 
    }
}