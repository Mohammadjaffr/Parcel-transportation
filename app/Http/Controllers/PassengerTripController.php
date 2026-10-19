<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Passengers;
use App\Models\PassengerTrip;
use App\Models\Broker;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\WhatsApp\WhatsAppLinkService;
use App\Classes\WebResponseClass;


use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PassengerTripController extends Controller
{
    public function index(Request $request)
    {
        $tripsQuery = PassengerTrip::with(['driver', 'passengers']);
        if (auth()->user()->branch_id) {
            $tripsQuery->where('branch_id', auth()->user()->branch_id);
        }
        $trips = $tripsQuery->latest()->paginate(10);

        $trips->getCollection()->transform(function ($trip) {
            $trip->driver_pdf_link = WhatsAppLinkService::generate($trip, 'tripDriver');
            return $trip;
        });
        if ($request->isMobile) {
            return view('mobile.pages.passenger.trips.index', compact('trips'));
        }
        //ضبظ حق الدسك توب 
        return view('pages.passenger.trips.index', compact('trips'));
    }

    public function create(Request $request)
    {
        $drivers = Driver::all();
        $pendingPassengersQuery = Passengers::with(['broker', 'driver'])
            ->where('status', 'pending');
        if (auth()->user()->branch_id) {
            $pendingPassengersQuery->where('branch_id', auth()->user()->branch_id);
        }
        $pendingPassengers = $pendingPassengersQuery->latest()->get();

        $brokers = Broker::select('id', 'name', 'phone')->orderBy('name')->get();
        if ($request->isMobile) {
            return view('mobile.pages.passenger.trips.create', compact('drivers', 'pendingPassengers', 'brokers'));
        }
        // ضبظ حق الدسك توب 
        return view('pages.passenger.trips.create', compact('drivers', 'pendingPassengers', 'brokers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id'     => ['nullable', 'exists:drivers,id'],
            'driver_phone'  => ['required_without:driver_id', 'nullable', 'string'],
            'driver_name'   => ['required_without:driver_id', 'nullable', 'string'],
            'passenger_ids' => ['required', 'array', 'min:1'], // يجب اختيار راكب واحد على الأقل للرحلة
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::beginTransaction();
            $driverId = $request->driver_id;
            if (empty($driverId)) {
                $driverId = $this->resolvePassengerDriver(
                    $this->normalizePhone($request->driver_phone),
                    $request->driver_name
                );
            }
            $user = auth()->user();
            $branchId = $user->branch_id;
            if (!$branchId && !empty($request->passenger_ids)) {
                $firstPassenger = Passengers::find($request->passenger_ids[0]);
                $branchId = $firstPassenger?->branch_id;
            }
            $appId = $user->app_id;
            if (!$appId) {
                if ($branchId) {
                    $branch = \App\Models\Branch::find($branchId);
                    $appId = $branch?->app_id;
                }
                $appId = $appId ?: (\App\Models\App::first()?->id ?? 1);
            }

            $trip = PassengerTrip::create([
                'app_id'     => $appId,
                'branch_id'  => $branchId,
                'created_by' => $user->id,
                'driver_id'  => $driverId,
            ]);

            // تحديث ركاب الرحلة: ربطهم بالـ trip_id وتحويل حالتهم إلى "مكتمل completed"
            $passengers = Passengers::whereIn('id', $request->passenger_ids)->get();
            foreach ($passengers as $passenger) {
                $passenger->update([
                    'trip_id' => $trip->id,
                    'status'  => 'completed',
                ]);
            }

            DB::commit();
            return WebResponseClass::sendResponse(
                'تم الإضافة!',
                'تم حفظ الرحلة وتحديث الصندوق المالي بنجاح.',
                'حسناً',
                'trips.index'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function show(Request $request, $id)
    {
        $trip = PassengerTrip::with(['driver', 'passengers.broker', 'passengers.branch'])->findOrFail($id);

        // جلب قائمة الركاب قيد الانتظار في النظام لإتاحة إضافتهم
        $pendingPassengersQuery = Passengers::where('status', 'pending');
        if (auth()->user()->branch_id) {
            $pendingPassengersQuery->where('branch_id', auth()->user()->branch_id);
        }
        $pendingPassengers = $pendingPassengersQuery->latest()->get();

        if ($request->isMobile) {
            return view('mobile.pages.passenger.trips.show', compact('trip', 'pendingPassengers'));
        }

        return view('pages.passenger.trips.show', compact('trip', 'pendingPassengers'));
    }

    // دالة جديدة لإضافة راكب للرحلة مباشرة
    public function addPassenger(Request $request, $tripId)
    {
        $validator = Validator::make($request->all(), [
            'passenger_id' => ['required', 'exists:passengers,id'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $trip = PassengerTrip::findOrFail($tripId);
            $passenger = Passengers::findOrFail($request->passenger_id);

            // ربط الراكب بالرحلة وتحديث حالته
            $passenger->trip_id = $trip->id;
            $passenger->status  = 'completed';
            $passenger->save();
            return WebResponseClass::sendResponse(
                'تم الإضافة!',
                'تم إضافة الراكب للرحلة بنجاح.',
                'حسناً',
                'trips.show',
                ['trip' => $tripId]
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function edit(Request $request, $id)
    {
        $trip = PassengerTrip::with(['driver', 'passengers.broker', 'passengers.driver'])->findOrFail($id);
        $drivers = Driver::all();
        $pendingPassengersQuery = Passengers::with(['broker', 'driver'])
            ->where('status', 'pending');
        if (auth()->user()->branch_id) {
            $pendingPassengersQuery->where('branch_id', auth()->user()->branch_id);
        }
        $pendingPassengers = $pendingPassengersQuery->latest()->get();
        $brokers = Broker::select('id', 'name', 'phone')->orderBy('name')->get();

        if ($request->isMobile) {
            return view('mobile.pages.passenger.trips.edit', compact('trip', 'drivers', 'pendingPassengers', 'brokers'));
        }

        return view('pages.passenger.trips.edit', compact('trip', 'drivers', 'pendingPassengers', 'brokers'));
    }

    public function update(Request $request, $id)
    {
        $trip = PassengerTrip::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'driver_id'     => ['nullable', 'exists:drivers,id'],
            'driver_phone'  => ['required_without:driver_id', 'nullable', 'string'],
            'driver_name'   => ['required_without:driver_id', 'nullable', 'string'],
            'passenger_ids' => ['required', 'array', 'min:1'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::beginTransaction();

            $driverId = $request->driver_id;
            if (empty($driverId)) {
                $driverId = $this->resolvePassengerDriver(
                    $this->normalizePhone($request->driver_phone),
                    $request->driver_name
                );
            }

            $trip->update([
                'driver_id' => $driverId,
            ]);

            // الركاب الحاليين في هذه الرحلة
            $currentPassengerIds = $trip->passengers()->pluck('id')->toArray();
            $newPassengerIds = $request->passenger_ids;

            // ركاب تم إزالتهم من الرحلة (إعادتهم إلى قيد الانتظار)
            $removedIds = array_diff($currentPassengerIds, $newPassengerIds);
            if (!empty($removedIds)) {
                Passengers::whereIn('id', $removedIds)->update([
                    'trip_id' => null,
                    'status' => 'pending'
                ]);
            }

            // ركاب جدد تمت إضافتهم للرحلة
            $addedIds = array_diff($newPassengerIds, $currentPassengerIds);
            if (!empty($addedIds)) {
                $addedPassengers = Passengers::whereIn('id', $addedIds)->get();
                foreach ($addedPassengers as $passenger) {
                    $passenger->update([
                        'trip_id' => $trip->id,
                        'status' => 'completed'
                    ]);
                }
            }

            DB::commit();
            return WebResponseClass::sendResponse(
                'تم التعديل!',
                'تم تعديل بيانات الرحلة بنجاح.',
                'حسناً',
                'trips.index'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }
    public function removePassenger($tripId, $passengerId)
    {
        $passenger = Passengers::findOrFail($passengerId);
        if ($passenger->trip_id != $tripId) {
            return  WebResponseClass::sendExceptionError('error', 'الراكب غير مرتبط بهذه الرحلة بالأساس.');
        }
        $passenger->trip_id = null;
        $passenger->status  = 'pending';
        $passenger->save();
        return WebResponseClass::sendResponse(
            'تم التعديل!',
            'تم فك ارتباط الراكب بنجاح وإعادته إلى قائمة الانتظار.',
            'حسناً',
            'trips.index'
        );
    }
    private function resolvePassengerDriver(?string $phone, ?string $name = null): ?int
    {
        $phone = $this->normalizePhone($phone);

        if (!$phone) {
            return null;
        }

        $rawLocal = preg_replace('/^(967|966)/', '', $phone);
        $driver = Driver::query()
            ->where(function ($q) use ($phone, $rawLocal) {
                $q->where('phone', $phone)
                  ->orWhere('phone', $rawLocal)
                  ->orWhere('phone', '0' . $rawLocal)
                  ->orWhere('phone', '+' . $phone);
            })
            ->first();

        if ($driver) {
            if ($name && $driver->name !== $name) {
                $driver->name = $name;
                $driver->save();
            }

            return $driver->id;
        }

        $user = auth()->user();

        $driver = new Driver();
        $driver->name = $name ?: 'سائق ' . $phone;
        $driver->phone = $phone;

        if (Schema::hasColumn('drivers', 'app_id')) {
            $driver->app_id = $user->app_id ?? \App\Models\Branch::find($user->branch_id)?->app_id ?? \App\Models\App::first()?->id ?? 1;
        }

        if (Schema::hasColumn('drivers', 'branch_id')) {
            $driver->branch_id = $user->branch_id ?? null;
        }

        if (Schema::hasColumn('drivers', 'created_by')) {
            $driver->created_by = $user->id ?? null;
        }

        $driver->save();

        return $driver->id;
    }
    private function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $phone = preg_replace('/[^\d\+]/', '', $phone);
        $phone = ltrim($phone, '+');

        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2);
        }

        if (str_starts_with($phone, '967967')) {
            $phone = substr($phone, 3);
        }

        if (str_starts_with($phone, '966966')) {
            $phone = substr($phone, 3);
        }

        if (preg_match('/^0(7\d{8})$/', $phone, $matches)) {
            return '967' . $matches[1];
        }

        if (preg_match('/^(7\d{8})$/', $phone, $matches)) {
            return '967' . $matches[1];
        }

        return $phone;
    }
}
