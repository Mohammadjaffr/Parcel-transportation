<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Passengers;
use App\Models\PassengerTrip;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PassengerTripController extends Controller
{
    public function index(Request $request)
    {
        $trips = PassengerTrip::with(['driver', 'passengers'])
            ->where('branch_id', auth()->user()->branch_id)
            ->latest()
            ->paginate(10);
        if ($request->isMobile) {
            return view('mobile.pages.passenger.trips.index', compact('trips'));
        }
        //ضبظ حق الدسك توب 
        return view('pages.passenger.trips.index', compact('trips'));
        
    }

    public function create(Request $request)
    {
    $drivers = Driver::all();
    $pendingPassengers = Passengers::where('status', 'pending')
        ->where('branch_id', auth()->user()->branch_id)
        ->latest()
        ->get();
        if ($request->isMobile) {
            return view('mobile.pages.passenger.trips.create', compact('drivers', 'pendingPassengers'));
        }
        // ضبظ حق الدسك توب 
        return view('pages.passenger.trips.create', compact('drivers', 'pendingPassengers'));

    
    }

    public function store(Request $request)
{
    $request->validate([
        'driver_id'     => ['nullable', 'exists:drivers,id'],
        'driver_phone'  => ['required_without:driver_id', 'string'],
        'driver_name'   => ['required_without:driver_id', 'string'],
        'passenger_ids' => ['required', 'array', 'min:1'], // يجب اختيار راكب واحد على الأقل للرحلة
    ]);

    try {
        DB::beginTransaction();
        $driverId = $request->driver_id;
        if (empty($driverId)) {
            $driverId = $this->resolvePassengerDriver(
                $this->normalizePhone($request->driver_phone),
                $request->driver_name
            );
        }
        $trip = PassengerTrip::create([
            'app_id'     => auth()->user()->app_id,
            'branch_id'  => auth()->user()->branch_id,
            'created_by' => auth()->id(),
            'driver_id'  => $driverId,
        ]);

        // تحديث ركاب الرحلة: ربطهم بالـ trip_id وتحويل حالتهم إلى "مؤكد confirmed" أو "in_transit"
        Passengers::whereIn('id', $request->passenger_ids)->update([
            'trip_id' => $trip->id,
            'status'  => 'completed' // تم تأكيد ركوبهم بالرحلة مع السائق
        ]);

        DB::commit();
        return redirect()->route('trips.index')->with('success', 'تم إنشاء الرحلة وتأكيد الركاب بنجاح.');

    } catch (Exception $e) {
        DB::rollBack();
        return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}

    public function show(Request $request, $id)
    {
    $trip = PassengerTrip::with([
        'driver', 
        'passengers', 
    ])->findOrFail($id);
    if ($request->isMobile) {
            return view('mobile.pages.passenger.trips.show', compact('trip'));
        }
        // اكتب مسار الصفحة على الدسك توب
    return view('pages.passenger.trips.show', compact('trip'));
    }





    public function edit(Request $request, $id)
    {
        $trip = PassengerTrip::with('passengers')->findOrFail($id);
        $drivers = Driver::all();
        $pendingPassengers = Passengers::where('status', 'pending')
            ->where('branch_id', auth()->user()->branch_id)
            ->latest()
            ->get();

        if ($request->isMobile) {
            return view('mobile.pages.passenger.trips.edit', compact('trip', 'drivers', 'pendingPassengers'));
        }
        return view('pages.passenger.trips.edit', compact('trip', 'drivers', 'pendingPassengers'));
    }

    public function update(Request $request, $id)
    {
        $trip = PassengerTrip::findOrFail($id);

        $request->validate([
            'driver_id' => ['nullable', 'exists:drivers,id'],
            'passenger_ids' => ['required', 'array', 'min:1'],
        ]);

        try {
            DB::beginTransaction();

            $trip->update([
                'driver_id' => $request->driver_id,
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
                Passengers::whereIn('id', $addedIds)->update([
                    'trip_id' => $trip->id,
                    'status' => 'completed'
                ]);
            }

            DB::commit();
            return redirect()->route('trips.index')->with('success', 'تم تحديث الرحلة بنجاح.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء التحديث.');
        }
    }
    private function resolvePassengerDriver(?string $phone, ?string $name = null): ?int
    {
        $phone = $this->normalizePhone($phone);

        if (!$phone) {
            return null;
        }

        $driver = Driver::query()
            ->where('phone', $phone)
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
            $driver->app_id = $user->app_id ?? null;
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