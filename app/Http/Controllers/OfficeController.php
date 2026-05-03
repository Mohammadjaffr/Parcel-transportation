<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Office;
use App\Models\Shipment;
use App\Services\AdminLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class OfficeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMobile) {
            return view('mobile.pages.office.index');
        }
        $offices = Office::latest()->paginate(10)->withQueryString();
        return view('pages.office.index', compact('offices'));
    }

    public function unverifiedIndex(Request $request)
    {
        $query = Office::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('branches', function($bQ) use ($search) {
                      $bQ->where('name', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('address', 'like', "%{$search}%");
                  });
            });
        }
        $offices = $query->latest()->paginate(10)->withQueryString();
        if ($request->isMobile) {
            return view('mobile.pages.office.unverified.index', compact('offices'));
        }
        return view('pages.office.unverified.index', compact('offices'));
    }

    public function create(Request $request)
    {
        if ($request->isMobile) {
            return view('mobile.pages.office.unverified.create');
        }
        return view('pages.office.unverified.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'branches' => 'required|array|min:1',
            'branches.*.name' => 'required|string|max:255',
            'branches.*.city' => 'required|string|max:255',
            'branches.*.phone' => 'nullable|string|max:20',
            'branches.*.address' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = auth()->user();

                $office = Office::create([
                    'app_id'     => $user->app_id,
                    'created_by' => $user->id,
                    'name'       => $request->name,
                    'phone'      => $request->phone,
                    'address'    => $request->address,
                ]);

                foreach ($request->branches as $branchData) {
                    $office->branches()->create([
                        'name'    => $branchData['name'],
                        'city'    => $branchData['city'],
                        'phone'   => $branchData['phone'],
                        'address' => $branchData['address'],
                    ]);
                }
            });

            return WebResponseClass::sendResponse(
                'تم الحفظ!',
                'تم إضافة المكتب وفروعه بنجاح.',
                'حسناً',
                'offices.unverified.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function edit(Request $request, $id)
    {
        $office = Office::with('branches')->findOrFail($id);
        if ($request->isMobile) {
            return view('mobile.pages.office.unverified.edit', compact('office'));
        }
        return view('pages.office.unverified.edit', compact('office'));
    }

    public function show(Request $request, $id)
    {

        $office = Office::with('branches')->findOrFail($id);
        $branchIds = $office->branches->pluck('id');

        $shipments = Shipment::with(['senderCustomer', 'receiverCustomer', 'receiverOfficeBranch'])
            ->where('sender_branch_id', auth()->user()->branch_id)
            ->whereIn('receiver_office_branch_id', $branchIds)
            ->latest()
            ->paginate(15);
        if ($request->isMobile) {
            return view('mobile.pages.office.unverified.show', compact('office', 'shipments'));
        }
        // ضبط المسار ليعمل في الديسكتوب
        return view('pages.office.unverified.show', compact('office', 'shipments'));
    }
    public function update(Request $request, $id)
    {
        $office = Office::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'branches' => 'required|array|min:1',
            'branches.*.name' => 'required|string|max:255',
            'branches.*.city' => 'required|string|max:100',
            'branches.*.phone' => 'nullable|string|max:20',
            'branches.*.address' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $office) {
                $office->update([
                    'name'    => $request->name,
                    'phone'   => $request->phone,
                    'address' => $request->address,
                ]);
                $office->branches()->delete();
                foreach ($request->branches as $branchData) {
                    $office->branches()->create([
                        'name'    => $branchData['name'],
                        'city'    => $branchData['city'],
                        'phone'   => $branchData['phone'],
                        'address' => $branchData['address'],
                    ]);
                }
            });

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث المكتب بنجاح.',
                'حسناً',
                'offices.unverified.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function destroy(Office $office)
    {
        try {
            $office->delete();
            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف المكتب بنجاح.',
                'حسناً',
                'offices.unverified.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
