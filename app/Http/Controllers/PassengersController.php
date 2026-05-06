<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Passengers;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Classes\WebResponseClass;
use App\Services\AdminLoggerService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class PassengersController extends Controller
{
    public function index(Request $request)
    {
        $query = Passengers::with(['driver', 'customer', 'branch'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('passenger_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhereHas('driver', function ($driverQuery) use ($search) {
                        $driverQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('branch', function ($branchQuery) use ($search) {
                        $branchQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $passengers = $query->paginate(15)->withQueryString();

        $drivers = Driver::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        $currentBranch = $this->currentBranchId()
            ? Branch::find($this->currentBranchId())
            : null;

        if ($request->isMobile) {
            return view('mobile.pages.passengers.index', compact(
                'passengers',
                'drivers',
                'customers',
                'branches',
                'currentBranch'
            ));
        }

        return view('pages.passengers.index', compact(
            'passengers',
            'drivers',
            'customers',
            'branches',
            'currentBranch'
        ));
    }

    public function create()
    {
        $drivers = Driver::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        $currentBranch = $this->currentBranchId()
            ? Branch::find($this->currentBranchId())
            : null;

        return view('pages.passengers.create', compact(
            'drivers',
            'customers',
            'branches',
            'currentBranch'
        ));
    }

public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:pending,confirmed,completed,cancel'],
            'passenger_number' => ['required', 'string', 'max:255'],
            
            'customer_id'    => ['nullable', 'exists:customers,id'],
            'customer_phone' => ['required_without:customer_id', 'string', 'max:255'],
            'customer_name'  => ['required_without:customer_id', 'string', 'max:255'],
            'driver_id'    => ['nullable', 'exists:drivers,id'],
            'driver_phone' => ['required_without:driver_id', 'string', 'max:255'],
            'driver_name'  => ['required_without:driver_id', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:1'],
            'total_commission' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();

            $data['passenger_number'] = $this->normalizePhone($data['passenger_number']);
            $customerPhone = $this->normalizePhone($data['customer_phone'] ?? '');
            $driverPhone = $this->normalizePhone($data['driver_phone'] ?? '');
            $data['branch_id'] = $this->currentBranchId();

            // الحل هنا: استخدام (?? null) لتفادي الخطأ
            $data['customer_id'] = ($data['customer_id'] ?? null)
                ?: $this->resolvePassengerCustomer($customerPhone, $data['customer_name'] ?? null);

            // الحل هنا أيضاً للسائق
            $data['driver_id'] = ($data['driver_id'] ?? null)
                ?: $this->resolvePassengerDriver($driverPhone, $data['driver_name'] ?? null);

            unset($data['customer_name'], $data['customer_phone'], $data['driver_phone'], $data['driver_name']);

            $passenger = Passengers::create($data);

            return WebResponseClass::sendResponse('تمت الإضافة!', 'تم حفظ الراكب بنجاح.', 'حسناً', 'passengers.index');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
    public function show(Request $request, $id)
    {
        $passenger = Passengers::with(['driver', 'customer', 'branch'])->findOrFail($id);

        if ($request->isMobile) {
            return view('mobile.pages.passengers.model.show', compact('passenger'));
        }

        return view('pages.passengers.show', compact('passenger'));
    }

    public function edit($id)
    {
        $passenger = Passengers::with(['driver', 'customer', 'branch'])->findOrFail($id);
        $drivers = Driver::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        $currentBranch = $this->currentBranchId()
            ? Branch::find($this->currentBranchId())
            : null;

        return view('pages.passengers.edit', compact(
            'passenger',
            'drivers',
            'customers',
            'branches',
            'currentBranch'
        ));
    }

  public function update(Request $request, $id)
    {
        $passenger = Passengers::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:pending,confirmed,completed,cancel'],
            'passenger_number' => ['required', 'string', 'max:255'],
            
            'customer_id'    => ['nullable', 'exists:customers,id'],
            'customer_phone' => ['required_without:customer_id', 'string', 'max:255'],
            'customer_name'  => ['required_without:customer_id', 'string', 'max:255'],
            'driver_id'    => ['nullable', 'exists:drivers,id'],
            'driver_phone' => ['required_without:driver_id', 'string', 'max:255'],
            'driver_name'  => ['required_without:driver_id', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'count' => ['required', 'integer', 'min:1'],
            'total_commission' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();

            $data['passenger_number'] = $this->normalizePhone($data['passenger_number']);
            $customerPhone = $this->normalizePhone($data['customer_phone'] ?? '');
            $driverPhone = $this->normalizePhone($data['driver_phone'] ?? '');
            $data['branch_id'] = $this->currentBranchId();

            // الحل هنا: استخدام (?? null) لتفادي الخطأ
            $data['customer_id'] = ($data['customer_id'] ?? null)
                ?: $this->resolvePassengerCustomer($customerPhone, $data['customer_name'] ?? null);

            $data['driver_id'] = ($data['driver_id'] ?? null)
                ?: $this->resolvePassengerDriver($driverPhone, $data['driver_name'] ?? null);

            unset($data['customer_name'], $data['customer_phone'], $data['driver_phone'], $data['driver_name']);

            $passenger->update($data);

            return WebResponseClass::sendResponse('تم التحديث!', 'تم تعديل بيانات الراكب بنجاح.', 'حسناً', 'passengers.index');
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function destroy($id)
    {
        try {
            $passenger = Passengers::findOrFail($id);
            $passengerNumber = $passenger->passenger_number;

            $passenger->delete();

            AdminLoggerService::log(
                'حذف راكب',
                'Passengers',
                $id,
                "تم حذف الراكب رقم {$passengerNumber}"
            );

            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف الراكب بنجاح.',
                'حسناً',
                'passengers.index'
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    private function currentBranchId(): ?int
    {
        return auth()->user()->branch_id ?? null;
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

    private function resolvePassengerCustomer(?string $phone, ?string $name = null): ?int
    {
        $phone = $this->normalizePhone($phone);

        if (!$phone) {
            return null;
        }

        $user = auth()->user();

        $customer = Customer::query()
            ->where('phone', $phone)
            ->when($user?->app_id, fn($q) => $q->where('app_id', $user->app_id))
            ->first();

        if ($customer) {
            if ($name && $customer->name !== $name) {
                $customer->update([
                    'name' => $name,
                ]);
            }

            return $customer->id;
        }

        $customer = Customer::create([
            'name' => $name ?: 'راكب ' . $phone,
            'phone' => $phone,
            'app_id' => $user->app_id ?? null,
            'branch_id' => $user->branch_id ?? null,
            'created_by' => $user->id ?? null,
        ]);

        return $customer->id;
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
}
