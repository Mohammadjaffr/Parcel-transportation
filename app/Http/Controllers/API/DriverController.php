<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\DriverRepository;

class DriverController extends Controller
{
    public function __construct(private DriverRepository $driverRepository)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Driver $driver)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Driver $driver)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Driver $driver)
    {
        //
    }

    public function updateDeviceToken(Request $request)
    {
        $fields=$request->validate([
            'device_token' => 'required',
        ]);
        try {
            $driver_id=auth('sanctum')->id();
            $driver=$this->driverRepository->update($fields,$driver_id);
            return ApiResponseClass::sendResponse($driver,'Device token updated successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Error updated token.'.$e->getMessage());
        }
        
    }

    public function updateLocation(Request $request)
    {
        $fields=$request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        try {
            $driver_id=auth('sanctum')->id();
            $driver=$this->driverRepository->update($fields,$driver_id);
            return ApiResponseClass::sendResponse($driver,'Location updated successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Error updated location.'.$e->getMessage());
        }
    }

     public function updateOnlineStatus(Request $request)
    {
        try {
            $fields = $request->validate([
                'is_online' => 'required|boolean'
            ]);
            $driver_id = auth('sanctum')->id();
            $this->driverRepository->update($fields, $driver_id);
            $status = $fields['is_online'] ? 'متصل' : 'غير متصل';
            return ApiResponseClass::sendResponse([], "تم التحديث إلى: {$status}");

        } catch (Exception $e) {
            return ApiResponseClass::sendError('فشل في تحديث الحالة: ' . $e->getMessage());
        }
    }
}
