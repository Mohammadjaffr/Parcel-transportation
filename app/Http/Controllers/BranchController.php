<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Shipment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\AdminLoggerService;
use App\Classes\WebResponseClass;


class BranchController extends Controller
{
    /* ========== 1- عرض جميع الفروع ========== */
    public function index(Request $request)
    {
        $allBranches = Branch::where('code', '!=', auth()->user()->branch_code)->get();
        $totalBranches = $allBranches->count();
        $totalCities = $allBranches->pluck('city')->unique()->count();
        $branches = Branch::where('code', '!=', auth()->user()->branch_code)
            ->withCount(['receivingPackages'])
            ->withSum('ledgers as total_debit', 'debit')
            ->withSum('ledgers as total_credit', 'credit')
            ->latest()
            ->paginate(10);
        if ($request->isMobile) {
            return view('mobile.pages.branch.index');
        }
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
    // 1. جلب المستخدم الحالي لمعرفة رقم شركته (app_id) أولاً
    $user = auth()->user();

    // 2. التحقق من المدخلات
    $request->validate([
        'name'     => 'required|string|max:255',
        'code'     => ['nullable','string','max:50',
            // Rule::unique('branches', 'code')->where(function ($query) use ($user) {
            //     return $query->where('app_id', $user->app_id);
            // })
        ],
        'city'     => 'required|string|max:100',
        'phone'    => 'nullable|string|max:20',
        'address'  => 'required|string|max:255',
        'map_link' => 'nullable|url|max:500',
        'is_main'  => 'nullable|boolean',
    ], [
        'code.unique' => 'كود الفرع هذا مستخدم مسبقاً في شركتك، يرجى اختيار كود آخر.',
        'map_link.url' => 'صيغة رابط خريطة جوجل غير صحيحة.',
        'address.required' => 'العنوان مطلوب.',
    ]);

    try {
        $isMain = $request->boolean('is_main');

        DB::transaction(function () use ($request, $user, $isMain) {
            
            if ($isMain) {
                Branch::where('app_id', $user->app_id)->update(['is_main' => false]);
            }

            Branch::create([
                'app_id'   => $user->app_id,
                'name'     => $request->name,
                'code'     => strtoupper($request->code)?? null,
                'city'     => $request->city,
                'phone'    => $request->phone,
                'address'  => $request->address,
                'map_link' => $request->map_link,
                'is_main'  => $isMain,
            ]);
        });

        return back()->with([
            'success_title' => 'تم الإضافة!',
            'success_message' => 'تم إضافة الفرع الجديد لشركتك بنجاح.'
        ]);

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ الفرع: ' . $e->getMessage());
    }
}

    /* ========== 4- عرض تفاصيل فرع واحد ========== */
    public function show(Request $request, $code)
    {
        // Get the branch by code
        $branch = Branch::where('code', $code)->firstOrFail();

        // Get the authenticated user's branch code
        $userBranchCode = auth()->user()->branch_code;

        // Calculate statistics for sent/received shipments
        $totalSentShipments = Shipment::where('sender_branch_code', $userBranchCode)
            ->where('receiver_branch_code', $code)
            ->count();

        $totalReceivedShipments = Shipment::where('sender_branch_code', $code)
            ->where('receiver_branch_code', $userBranchCode)
            ->count();

        // ========== معلومات الحزم المرسلة لهذا الفرع ==========

        // الحصول على IDs الحزم المرتبطة بهذا الفرع من جدول الـ pivot
        $branchPackagePivotIds = DB::table('branch_shipment_package')
            ->where('branch_code', $code)
            ->pluck('id', 'shipment_package_id')
            ->toArray();

        $packageCount = count($branchPackagePivotIds);

        // حساب عدد الشحنات داخل هذه الحزم المتجهة لهذا الفرع
        $shipmentCount = 0;
        $totalAmount = 0;

        if (!empty($branchPackagePivotIds)) {
            $packageIds = array_keys($branchPackagePivotIds);

            // عدد الشحنات في هذه الحزم المتجهة لهذا الفرع
            $shipmentCount = Shipment::whereIn('shipment_package_id', $packageIds)
                ->where('receiver_branch_code', $code)
                ->count();

            // المبلغ المستحق على الفرع (COD + Partial فقط)
            $shipments = Shipment::whereIn('shipment_package_id', $packageIds)
                ->where('receiver_branch_code', $code)
                ->get();

            foreach ($shipments as $shipment) {
                if ($shipment->payment_method === 'cod') {
                    $totalAmount += $shipment->total_amount;
                } elseif ($shipment->payment_method === 'partial_payment') {
                    $totalAmount += ($shipment->total_amount - ($shipment->partial_amount ?? 0));
                }
            }
        }

        // حساب المبلغ المدفوع
        $paidAmount = 0;
        if (!empty($branchPackagePivotIds)) {
            $paidAmount = DB::table('branch_package_payments')
                ->whereIn('branch_shipment_package_id', array_values($branchPackagePivotIds))
                ->sum('paid_amount');
        }

        // حساب المبلغ المتبقي
        $remainingAmount = $totalAmount - $paidAmount;
        $isPaid = $remainingAmount <= 0 && $totalAmount > 0;

        // ========== جلب الحزم للعرض في الجدول ==========

        // جلب الحزم المرتبطة بهذا الفرع مع معلومات الدفعات
        $packagesQuery = \App\Models\ShipmentPackage::query()
            ->whereHas('receiverBranches', function ($query) use ($code) {
                $query->where('branch_code', $code);
            })
            ->with(['shipments' => function ($query) use ($code) {
                $query->where('receiver_branch_code', $code);
            }]);

        // حساب معلومات إضافية لكل حزمة
        $packages = $packagesQuery->latest()->paginate(10);

        // إضافة معلومات الدفع لكل حزمة
        foreach ($packages as $package) {
            // الحصول على pivot id لهذه الحزمة
            $pivotId = DB::table('branch_shipment_package')
                ->where('shipment_package_id', $package->id)
                ->where('branch_code', $code)
                ->value('id');

            // حساب المبلغ المستحق على الفرع بناءً على نوع الدفع
            $amountDue = 0;
            foreach ($package->shipments as $shipment) {
                // COD: الفرع مديون بالمبلغ الكامل
                if ($shipment->payment_method === 'cod') {
                    $amountDue += $shipment->total_amount;
                }
                // Partial Payment: الفرع مديون بالباقي (المبلغ الكامل - المبلغ الجزئي)
                elseif ($shipment->payment_method === 'partial_payment') {
                    $amountDue += ($shipment->total_amount - ($shipment->partial_amount ?? 0));
                }
                // Prepaid & Customer Credit: لا يوجد مبلغ مستحق على الفرع
                // (المبلغ مدفوع مسبقاً أو على حساب العميل)
            }

            $package->branch_total_amount = $amountDue;

            // حساب المبلغ المدفوع لهذه الحزمة
            $package->branch_paid_amount = DB::table('branch_package_payments')
                ->where('branch_shipment_package_id', $pivotId)
                ->sum('paid_amount');

            // حساب المتبقي
            $package->branch_remaining_amount = $package->branch_total_amount - $package->branch_paid_amount;

            // حالة الدفع
            $package->branch_is_paid = $package->branch_remaining_amount <= 0 && $package->branch_total_amount > 0;

            // عدد الشحنات في هذا الفرع
            $package->branch_shipment_count = $package->shipments->count();
        }

        return view('pages.branch.show', compact(
            'branch',
            'packages',
            'totalSentShipments',
            'totalReceivedShipments',
            'packageCount',
            'shipmentCount',
            'totalAmount',
            'paidAmount',
            'remainingAmount',
            'isPaid'
        ));
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
    $user = auth()->user();

    // تأمين لمنع تعديل فروع لشركات أخرى
    if ($branch->app_id !== $user->app_id) {
        abort(403);
    }

    $request->validate([
        'name'     => 'required|string|max:255',
        'code'     => ['nullable', 'string', 'max:50',Rule::unique('branches', 'code')->where('app_id', $user->app_id)->ignore($branch->id)],
        'city'     => 'required|string|max:100',
        'phone'    => 'nullable|string|max:20',
        'address'  => 'nullable|string|max:255',
        'map_link' => 'nullable|url|max:500',
        'is_main'  => 'nullable|boolean',
    ], [
        'code.unique' => 'كود الفرع هذا مستخدم مسبقاً في فرع آخر، يرجى اختيار كود مختلف.',
    ]);

    try {
        $isMain = $request->boolean('is_main');

        DB::transaction(function () use ($request, $branch, $isMain, $user) {
            if ($isMain && !$branch->is_main) {
                Branch::where('app_id', $user->app_id)->update(['is_main' => false]);
            }

            $branch->update([
                'name'     => $request->name,
                'code'     => strtoupper($request->code)?? null,
                'city'     => $request->city,
                'phone'    => $request->phone,
                'address'  => $request->address,
                'map_link' => $request->map_link,
                'is_main'  => $isMain,
            ]);
        });

        return back()->with([
            'success_title' => 'تم التحديث!',
            'success_message' => 'تم تحديث بيانات الفرع بنجاح.'
        ]);

    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
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
