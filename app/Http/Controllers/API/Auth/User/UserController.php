<?php

namespace App\Http\Controllers\API\Auth\User;

use Exception;
use Illuminate\Http\Request;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class UserController extends Controller
{
     /**
     * Create a new class instance.
     */
    public function __construct(private UserRepository $UserRepository)
    {
        //
    }

    public function updateDeviceToken(Request $request)
    {
        $fields=$request->validate([
            'device_token' => 'required',
        ]);
        try {
            $user_id=auth('sanctum')->id();
            $user=$this->UserRepository->update($fields,$user_id);
            return ApiResponseClass::sendResponse($user,'Device token updated successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Error updated token.');
        }
        
    }
}
