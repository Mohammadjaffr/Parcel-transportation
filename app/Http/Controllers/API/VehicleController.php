<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\VehicleRepository;

class VehicleController extends Controller
{
    public function __construct(private VehicleRepository $vehicleRepository)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $vehicles = $this->vehicleRepository->index();
            return ApiResponseClass::sendResponse($vehicles, 'Vehicles retrieved successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Failed to retrieve vehicles. ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'name'=>['required','string','max:255',Rule::unique('vehicles','name')], 
            'type'=>['required','string','max:255'], 
            'description'=>['nullable','string'],
            'max_passengers'=>['required','integer','min:1'],
        ]);
        try {
            $vehicle=$this->vehicleRepository->store($fields);
            return ApiResponseClass::sendResponse($vehicle, 'Vehicle stored successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Failed to store vehicle. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $vehicle)
    {
        try {
            $fields=$request->validate([
                'name'=>['sometimes','required','string','max:255',Rule::unique('vehicles','name')->ignore($vehicle)], 
                'type'=>['sometimes','required','string','max:255'], 
                'description'=>['sometimes','nullable','string'],
                'max_passengers'=>['sometimes','required','integer','min:1'],
            ]);
            $updatedVehicle=$this->vehicleRepository->update($fields,$vehicle);
            return ApiResponseClass::sendResponse($updatedVehicle, 'Vehicle updated successfully.');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Failed to update vehicle. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($vehicle)
    {
        //
    }
}
