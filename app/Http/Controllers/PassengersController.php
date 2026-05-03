<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Passengers;
use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use App\Classes\WebResponseClass;
use App\Services\AdminLoggerService;
use Illuminate\Support\Facades\Validator;

class PassengersController extends Controller
{
    /* ========== 1- عرض جميع الركاب مع البحث والفلترة ========== */
    public function index(Request $request)
    {
        $query = Passengers::with('driver')->latest();

        // تفعيل ميزة البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('passenger_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('broker', 'like', "%{$search}%");
        }

        // استخدام withQueryString للحفاظ على كلمة البحث عند التنقل
        $passengers = $query->paginate(15)->withQueryString();
        $drivers = Driver::all();

      if ($request->isMobile)  {
            return view('mobile.pages.passengers.index', compact('passengers', 'drivers'));
        }

        return view('pages.passengers.index', compact('passengers', 'drivers'));
    }

    /* ========== 2- صفحة إنشاء راكب ========== */
    public function create()
    {
        $drivers = Driver::all();
        return view('pages.passengers.create', compact('drivers'));
    }

    /* ========== 3- حفظ راكب جديد ========== */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'day' => 'required|string|max:255',
            'passenger_number' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'count' => 'required|integer|min:0',
            'total_commission' => 'required|numeric|min:0',
            'broker' => 'nullable|string|max:255',
            'driver_id' => 'nullable|exists:drivers,id',
            'driver_name' => 'required_without:driver_id|string|max:255',
            'driver_phone' => 'required_without:driver_id|string|max:50',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();

            $driverId = $data['driver_id'] ?? null;
            if (empty($driverId) && !empty($data['driver_phone'])) {
                $driver = Driver::firstOrCreate(
                    ['phone' => $data['driver_phone']],
                    [
                        'name' => $data['driver_name'],
                        'app_id' => auth()->user()->app_id ?? null,
                        'branch_id' => auth()->user()->branch_id ?? null,
                        'created_by' => auth()->id()
                    ]
                );
                
                if (!empty($data['driver_name']) && $driver->name !== $data['driver_name']) {
                    $driver->update(['name' => $data['driver_name']]);
                }
                $driverId = $driver->id;
            }
            
            $data['driver_id'] = $driverId;
            unset($data['driver_name'], $data['driver_phone']);

            $passenger = Passengers::create($data);

            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم حفظ الراكب بنجاح.',
                'حسناً',
                'passengers.index'
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 4- عرض تفاصيل راكب ========== */
    public function show(Request $request, $id)
    {
        $passenger = Passengers::with('driver')->findOrFail($id);
        
        if ($request->isMobile) {
            return view('mobile.pages.passengers.model.show', compact('passenger'));
        }

        return view('pages.passengers.show', compact('passenger'));
    }

    /* ========== 5- صفحة تعديل راكب ========== */
    public function edit($id)
    {
        $passenger = Passengers::with('driver')->findOrFail($id);
        $drivers = Driver::all();
        return view('pages.passengers.edit', compact('passenger', 'drivers'));
    }

    /* ========== 6- تحديث راكب ========== */
    public function update(Request $request, $id)
    {
        $passenger = Passengers::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'day' => 'required|string|max:255',
            'passenger_number' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'count' => 'required|integer|min:0',
            'total_commission' => 'required|numeric|min:0',
            'broker' => 'nullable|string|max:255',
            'driver_id' => 'nullable|exists:drivers,id',
            'driver_name' => 'required_without:driver_id|string|max:255',
            'driver_phone' => 'required_without:driver_id|string|max:50',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();

            $driverId = $data['driver_id'] ?? null;
            if (empty($driverId) && !empty($data['driver_phone'])) {
                $driver = Driver::firstOrCreate(
                    ['phone' => $data['driver_phone']],
                    [
                        'name' => $data['driver_name'],
                        'app_id' => auth()->user()->app_id ?? null,
                        'branch_id' => auth()->user()->branch_id ?? null,
                        'created_by' => auth()->id()
                    ]
                );
                
                if (!empty($data['driver_name']) && $driver->name !== $data['driver_name']) {
                    $driver->update(['name' => $data['driver_name']]);
                }
                $driverId = $driver->id;
            }
            
            $data['driver_id'] = $driverId;
            unset($data['driver_name'], $data['driver_phone']);

            $passenger->update($data);

            AdminLoggerService::log(
                'تحديث راكب',
                'Passengers',
                $passenger->id,
                "تحديث بيانات الراكب {$passenger->passenger_number}"
            );

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تعديل بيانات الراكب بنجاح.',
                'حسناً',
                'passengers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 7- حذف راكب ========== */
    public function destroy($id)
    {
        try {
            $passenger = Passengers::findOrFail($id);
            $passenger->delete();

            AdminLoggerService::log(
                'حذف راكب',
                'Passengers',
                $id,
                "تم حذف الراكب رقم {$passenger->passenger_number}"
            );

            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف الراكب بنجاح.',
                'حسناً',
                'passengers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
