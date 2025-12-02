<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Vehicle;
use Mockery\Expectation;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Validation\Rule;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function __construct(private VehicleRepository $vehicleRepository,private ImageService $imageService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = $this->vehicleRepository->index();
        return view('pages.Vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'type' => ['required', 'string', 'max:100',Rule::unique('vehicles','type')],
        'description' => ['nullable', 'string', 'max:1000'],
        'max_passengers' => ['required', 'integer', 'min:1'],
        'image' => ['nullable', 'image', 'max:2048']
    ]);

    if ($validator->fails()) {
        $firstError = $validator->errors()->first();
        return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', true)
                    ->with('error_title', 'حدث خطأ!')
                    ->with('error_message', $firstError)
                    ->with('error_buttonText', 'حسناً');
    }
    try {
        $validatData = $validator->validated();
        if($request->hasFile('image')){
            $image_path=$this->imageService->saveImage($request->file('image'),'vehicles');
            $validatData['image'] = $image_path;
        }        
        $vehicleData = $this->vehicleRepository->store($validatData);
        return redirect()->back()
            ->with('success', true)
            ->with('success_title', 'تم الإضافة!')
            ->with('success_message', 'تم إضافة المركبة بنجاح.')
            ->with('success_buttonText', 'حسناً');
        }
    catch (Exception $e) {
        return redirect()->back()
            ->with('error', true)
            ->with('error_title', 'حدث خطأ!')
            ->with('error_message', $e->getMessage())
            ->with('error_buttonText', 'حسناً');
    }
}
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('pages.Vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $vehicle=$this->vehicleRepository->getById($id);
            return redirect()->back()
                ->with('openModalEdit',true)
                ->with('vehicle', $vehicle);
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', true)
                ->with('error_title', 'حدث خطأ!')
                ->with('error_message', $e->getMessage())
                ->with('error_buttonText', 'حسناً')
                ->with('openModalEdit',false);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'string', 'max:100',Rule::unique('vehicles','type')->ignore($id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'max_passengers' => ['required', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:2048']
        ]);
        if ($validator->fails()) {
            $firstError = $validator->errors()->first();
            return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', true)
                    ->with('error_title', 'حدث خطأ!')
                    ->with('error_message', $firstError)
                    ->with('error_buttonText', 'حسناً');
        }
        try {
            $vehicle = $this->vehicleRepository->getById($id);
            $data = [
                'type' => $request->type,
                'description' => $request->description,
                'max_passengers' => $request->max_passengers,
            ];
            if ($request->hasFile('image')) {
                if ($vehicle->image) {
                    $this->imageService->deleteImage($vehicle->image);
                }
                $image_path=$this->imageService->saveImage($request->file('image'),'vehicles');
                $data['image'] = $image_path;
            }
            $this->vehicleRepository->update($data,$id);
            return redirect()->back()
                    ->with('success', true)
                    ->with('success_title', 'تم التحديث!')
                    ->with('success_message', 'تم تحديث بيانات المركبة بنجاح')
                    ->with('success_buttonText', 'حسناً');

        } catch (Exception $e) {
            return redirect()->back()
                    ->with('error', true)
                    ->with('error_title', 'حدث خطأ!')
                    ->with('error_message', 'فشل في تحديث بيانات المركبة')
                    ->with('error_buttonText', 'حسناً')
                    ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
