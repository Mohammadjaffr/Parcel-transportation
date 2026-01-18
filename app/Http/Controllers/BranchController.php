<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Shipment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Services\AdminLoggerService;
use App\Classes\WebResponseClass;


class BranchController extends Controller
{
    /* ========== 1- عرض جميع الفروع ========== */
    public function index()
    {
        $allBranches = Branch::where('code', '!=', auth()->user()->branch_code)->get();
        $totalBranches = $allBranches->count();
        $totalCities = $allBranches->pluck('city')->unique()->count();
        $branches = Branch::where('code', '!=', auth()->user()->branch_code)
            ->withCount(['sentShipments', 'receivedShipments'])
            ->latest()
            ->paginate(10);

        return view('pages.branch.index', compact('branches', 'totalBranches', 'totalCities'));
    }

    /* ========== 2- صفحة إنشاء فرع ========== */
    public function create()
    {
        return view('pages.branch.create');
    }

    /* ========== 3- تخزين فرع جديد ========== */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city'   => 'required|string|max:255',
            'phone'  => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:branches,code',
        ], [
            'name.required'   => 'حقل اسم الفرع مطلوب.',
            'address.required' => 'حقل المنطقة مطلوب.',
            'city.required'   => 'حقل المدينة مطلوب.',
            'phone.required'  => 'حقل الهاتف مطلوب.',
            'code.required'     => 'رمز الفرع مطلوب'
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $branch = Branch::create($validator->validated());
            AdminLoggerService::log(
                'إنشاء فرع',
                Branch::class,
                $branch->id,
                'تم إنشاء الفرع بنجاح'
            );


            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم إضافة الفرع بنجاح.',
                'حسناً',
                'branch.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 4- عرض تفاصيل فرع واحد ========== */
    public function show(Request $request, $code)
    {
        // Get the branch by code
        $branch = Branch::where('code', $code)->firstOrFail();

        // Get the authenticated user's branch code
        $userBranchCode = auth()->user()->branch_code;

        // Query shipments between the two branches
        $shipmentsQuery = Shipment::with(['senderBranch', 'receiverBranch', 'senderCustomer', 'receiverCustomer'])
            ->where(function ($query) use ($userBranchCode, $code) {
                // Sent shipments: user's branch is sender AND selected branch is receiver
                $query->where(function ($q) use ($userBranchCode, $code) {
                    $q->where('sender_branch_code', $userBranchCode)
                        ->where('receiver_branch_code', $code);
                })
                    // OR Received shipments: selected branch is sender AND user's branch is receiver
                    ->orWhere(function ($q) use ($userBranchCode, $code) {
                        $q->where('sender_branch_code', $code)
                            ->where('receiver_branch_code', $userBranchCode);
                    });
            });

        // Apply direction filter if provided
        if ($request->get('direction') == 'sent') {
            // Only sent shipments
            $shipmentsQuery->where('sender_branch_code', $userBranchCode)
                ->where('receiver_branch_code', $code);
        } elseif ($request->get('direction') == 'received') {
            // Only received shipments
            $shipmentsQuery->where('sender_branch_code', $code)
                ->where('receiver_branch_code', $userBranchCode);
        }

        // Calculate statistics
        $totalSentShipments = Shipment::where('sender_branch_code', $userBranchCode)
            ->where('receiver_branch_code', $code)
            ->count();

        $totalReceivedShipments = Shipment::where('sender_branch_code', $code)
            ->where('receiver_branch_code', $userBranchCode)
            ->count();

        // Paginate shipments
        $shipments = $shipmentsQuery->latest()->paginate(10);

        return view('pages.branch.show', compact('branch', 'shipments', 'totalSentShipments', 'totalReceivedShipments'));
    }

    /* ========== 5- صفحة تعديل الفرع ========== */
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('pages.branch.edit', compact('branch'));
    }

    /* ========== 6- تحديث الفرع ========== */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city'   => 'required|string|max:255',
            'phone'  => 'required|string|max:50',
            'code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $branch->update($validator->validated());
            // AdminLoggerService::log('تحديث فرع', 'Branch', $branch->code, "تم تحديث بيانات الفرع بنجاح");

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيانات الفرع بنجاح.',
                'حسناً',
                'branch.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 7- حذف الفرع ========== */
    public function destroy($id)
    {
        try {
            $branch = Branch::findOrFail($id);

            // Check for related records before deletion
            if ($branch->users()->count() > 0) {
                return WebResponseClass::sendResponse(
                    'خطأ!',
                    'لا يمكن حذف الفرع لوجود مستخدمين مرتبطين به.',
                    'حسناً',
                    null,
                    false
                );
            }

            if ($branch->senderBranch()->count() > 0 || $branch->receiverBranch()->count() > 0) {
                return WebResponseClass::sendResponse(
                    'خطأ!',
                    'لا يمكن حذف الفرع لوجود شحنات مرتبطة به.',
                    'حسناً',
                    null,
                    false
                );
            }

            // Check if branch has customers
            $customerCount = \App\Models\Customer::where('branch_code', $branch->code)->count();
            if ($customerCount > 0) {
                return WebResponseClass::sendResponse(
                    'خطأ!',
                    'لا يمكن حذف الفرع لوجود عملاء مسجلين فيه.',
                    'حسناً',
                    null,
                    false
                );
            }

            $branch->delete();
            // AdminLoggerService::log('حذف فرع', 'Branch', $branch->code, "تم حذف الفرع بنجاح");


            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف الفرع بنجاح.',
                'حسناً',
                'branch.index'
            );
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                return WebResponseClass::sendResponse(
                    'خطأ!',
                    'لا يمكن حذف الفرع لوجود بيانات مرتبطة به (مثل المعاملات المالية أو العملاء).',
                    'حسناً',
                    null,
                    false,
                    'error'
                );
            }
            return WebResponseClass::sendExceptionError($e);
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
