<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Office;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Services\AdminLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\WhatsAppLinkService;

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
                ]);

                foreach ($request->branches as $branchData) {
                    $office->branches()->create([
                        'name'    => $branchData['name'],
                        'city'    => $branchData['city'],
                        'phone'   => $branchData['phone'] ?? null,
                        'address' => $branchData['address'] ?? null,
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
        $office = Office::with(['branches' => function ($q) {
            $q->withCount(['senderShipments', 'receiverShipments', 'senderShipmentPackages']);
        }])->findOrFail($id);

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
            'branches' => 'required|array|min:1',
            'branches.*.id' => 'nullable|integer',
            'branches.*.name' => 'required|string|max:255',
            'branches.*.city' => 'required|string|max:100',
            'branches.*.phone' => 'nullable|string|max:20',
            'branches.*.address' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $office) {
                $office->update([
                    'name' => $request->name,
                ]);

                // 1. جمع معرفات الفروع القائمة التي أرسلها المستخدم في الطلب
                $submittedIds = collect($request->branches)
                    ->pluck('id')
                    ->filter()
                    ->map(fn($val) => (int) $val)
                    ->all();

                $existingBranches = $office->branches()->get();

                // 2. التحقق من أي فرع تم حذفه من القائمة: هل مرتبط بشحنات أو طرود؟
                foreach ($existingBranches as $branch) {
                    if (!in_array($branch->id, $submittedIds)) {
                        $hasShipments = Shipment::where('receiver_office_branch_id', $branch->id)
                            ->orWhere('sender_office_branch_id', $branch->id)
                            ->exists();

                        $hasPackages = ShipmentPackage::where('sender_office_branch_id', $branch->id)->exists();

                        if ($hasShipments || $hasPackages) {
                            throw new \Exception("لا يمكن حذف الفرع ({$branch->name}) لوجود شحنات أو طرود مرتبطة به في النظام.");
                        }

                        $branch->delete();
                    }
                }

                // 3. تحديث الفروع القائمة أو إضافة فروع جديدة
                foreach ($request->branches as $branchData) {
                    $branchPayload = [
                        'name'    => $branchData['name'],
                        'city'    => $branchData['city'],
                        'phone'   => $branchData['phone'] ?? null,
                        'address' => $branchData['address'] ?? null,
                    ];

                    $branchId = !empty($branchData['id']) ? (int) $branchData['id'] : null;

                    if ($branchId) {
                        $branch = $existingBranches->firstWhere('id', $branchId);
                        if ($branch) {
                            $branch->update($branchPayload);
                            continue;
                        }
                    }

                    // فرع جديد للمكتب
                    $office->branches()->create($branchPayload);
                }
            });

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث المكتب وفروعه بنجاح.',
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
            $branchIds = $office->branches()->pluck('id');
            $hasShipments = Shipment::whereIn('receiver_office_branch_id', $branchIds)
                ->orWhereIn('sender_office_branch_id', $branchIds)
                ->exists();
            $hasPackages = ShipmentPackage::whereIn('sender_office_branch_id', $branchIds)->exists();

            if ($hasShipments || $hasPackages) {
                return WebResponseClass::sendResponse(
                    'لا يمكن الحذف!',
                    'لا يمكن حذف هذا المكتب لوجود شحنات أو طرود مسجلة تابعة لفروعه.',
                    'حسناً',
                    'offices.unverified.index'
                );
            }

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

    public function toggleBan($id)
    {
        try {
            $office = Office::findOrFail($id);
            $office->is_banned = !$office->is_banned;
            $office->save();

            $message = $office->is_banned ? 'تم حظر المكتب بنجاح.' : 'تم إلغاء حظر المكتب بنجاح.';

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success'   => true,
                    'is_banned' => (bool) $office->is_banned,
                    'message'   => $message,
                ]);
            }

            return WebResponseClass::sendResponse(
                'تمت العملية بنجاح!',
                $message,
                'حسناً',
                'offices.unverified.index'
            );
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            return WebResponseClass::sendExceptionError($e);
        }
    }
}

