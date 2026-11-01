<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Classes\WebResponseClass;
use App\Services\AdminLoggerService;
use App\Services\DriverTransactionService;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function __construct(
        protected DriverTransactionService $transactionService
    ) {}

    /* ========== 1- عرض جميع السائقين مع البحث والفلترة ========== */
    public function index(Request $request)
    {
        // حساب الإجماليات
        $totalCount  = Driver::count();
        $activeCount = Driver::where('is_banned', false)->count();
        $bannedCount = Driver::where('is_banned', true)->count();

        $query = Driver::latest();

        // الفلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_banned', false);
            } elseif ($request->status === 'banned') {
                $query->where('is_banned', true);
            }
        }

        // البحث بالاسم ورقم الهاتف ونوع المركبة
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('vehicle_type', 'like', "%{$search}%");
            });
        }

        $drivers = $query->paginate(10)->withQueryString();

        if ($request->isMobile) {
            return view('mobile.pages.people.drivers.index', compact('drivers', 'totalCount', 'activeCount', 'bannedCount'));
        }

        return view('pages.drivers.index', compact('drivers', 'totalCount', 'activeCount', 'bannedCount'));
    }

    /* ========== 2- صفحة إنشاء سائق ========== */
    public function create()
    {
        return view('pages.drivers.modals.create');
    }

    /* ========== 3- حفظ سائق جديد ========== */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'vehicle_type' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        } 

        try {
            $data = $validator->validated();
            $data['app_id']     = Auth::user()->app_id;    
            $data['branch_id']  = Auth::user()->branch_id; 
            $data['created_by'] = Auth::id();              
            $driver = Driver::create($data);
            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم حفظ السائق بنجاح.',
                'حسناً',
                'drivers.index'
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 4- عرض تفاصيل سائق وكشف حسابه ========== */
    public function show(Request $request, $id)
    {
        $driver = Driver::with('branch')->findOrFail($id);

        $query = $driver->transactions()->with(['creator', 'cashTransaction'])->latest();

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $totalDebit = $driver->total_debit;
        $totalCredit = $driver->total_credit;
        $balance = $driver->balance;

        if ($request->isMobile) {
            return view('mobile.pages.people.drivers.show', compact('driver', 'transactions', 'totalDebit', 'totalCredit', 'balance'));
        }

        return view('pages.drivers.show', compact('driver', 'transactions', 'totalDebit', 'totalCredit', 'balance'));
    }

    /* ========== 4.1- تسجيل قيد جديد أو تصفية حساب للسائق ========== */
    public function storeTransaction(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);
        $action = $request->input('action', 'new_entry');

        try {
            if ($action === 'clear_amount') {
                $validator = Validator::make($request->all(), [
                    'clear_type'       => ['required', 'in:receive,pay'],
                    'amount'           => ['required', 'numeric', 'min:0.01'],
                    'payment_method'   => ['required', 'in:cash,bank_transfer'],
                    'reference_number' => ['nullable', 'string', 'max:100'],
                    'notes'            => ['nullable', 'string', 'max:500'],
                ], [
                    'clear_type.required'     => 'نوع التصفية مطلوب.',
                    'amount.required'         => 'المبلغ مطلوب.',
                    'amount.min'              => 'المبلغ يجب أن يكون أكبر من صفر.',
                    'payment_method.required' => 'طريقة الدفع مطلوبة.',
                ]);

                if ($validator->fails()) {
                    return WebResponseClass::sendValidationError($validator);
                }

                $validated = $validator->validated();

                if ($validated['clear_type'] === 'receive') {
                    $this->transactionService->receivePaymentFromDriver(
                        $driver,
                        (float) $validated['amount'],
                        $validated['payment_method'],
                        $validated['notes'] ?? null,
                        $validated['reference_number'] ?? null
                    );
                    $msg = 'تم تسجيل سند القبض وتوريد المبلغ إلى الصندوق بنجاح.';
                    $logAction = 'سند قبض من سائق';
                } else {
                    $this->transactionService->payBalanceToDriver(
                        $driver,
                        (float) $validated['amount'],
                        $validated['payment_method'],
                        $validated['notes'] ?? null,
                        $validated['reference_number'] ?? null
                    );
                    $msg = 'تم تسجيل سند الصرف ودفع المستحق للسائق بنجاح.';
                    $logAction = 'سند صرف لسائق';
                }

                AdminLoggerService::log(
                    $logAction,
                    'Driver',
                    $driver->id,
                    "{$logAction} بمبلغ {$validated['amount']} للسائق {$driver->name}"
                );

                return WebResponseClass::sendResponse(
                    'تمت العملية بنجاح!',
                    $msg,
                    'حسناً'
                );

            } else {
                // new_entry (كشف / قيد جديد)
                $validator = Validator::make($request->all(), [
                    'type'             => ['required', 'in:debit,credit'],
                    'total_amount'     => ['required', 'numeric', 'min:0.01'],
                    'paid_now'         => ['nullable', 'numeric', 'min:0'],
                    'payment_method'   => ['required', 'in:cash,bank_transfer'],
                    'description'      => ['required', 'string', 'max:500'],
                    'reference_number' => ['nullable', 'string', 'max:100'],
                ], [
                    'type.required'           => 'نوع القيد مطلوب.',
                    'total_amount.required'   => 'المبلغ الكلي مطلوب.',
                    'total_amount.min'        => 'المبلغ يجب أن يكون أكبر من صفر.',
                    'payment_method.required' => 'طريقة الدفع مطلوبة.',
                    'description.required'    => 'البيان مطلوب.',
                ]);

                if ($validator->fails()) {
                    return WebResponseClass::sendValidationError($validator);
                }

                $validated = $validator->validated();
                $totalAmount = (float) $validated['total_amount'];
                $paidNow = (float) ($validated['paid_now'] ?? 0);

                if ($paidNow > $totalAmount) {
                    return WebResponseClass::sendError('المبلغ المدفوع حالياً لا يمكن أن يتجاوز المبلغ الكلي.');
                }

                $this->transactionService->recordManifestEntry(
                    $driver,
                    $totalAmount,
                    $validated['type'],
                    $validated['description'],
                    $paidNow,
                    $validated['payment_method'],
                    null,
                    null,
                    $validated['reference_number'] ?? null
                );

                AdminLoggerService::log(
                    'تسجيل قيد مالي للسائق',
                    'Driver',
                    $driver->id,
                    "تسجيل قيد بمبلغ {$totalAmount} للسائق {$driver->name}"
                );

                return WebResponseClass::sendResponse(
                    'تم الحفظ بنجاح!',
                    'تم تسجيل القيد في كشف حساب السائق بنجاح.',
                    'حسناً'
                );
            }
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 5- صفحة تعديل سائق ========== */
    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        return view('pages.drivers.edit', compact('driver'));
    }

    /* ========== 6- تحديث سائق ========== */
    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'vehicle_type' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        } 

        try {
            $driver->update($validator->validated());

            AdminLoggerService::log(
                'تحديث سائق',
                'Driver',
                $driver->id,
                "تحديث بيانات السائق {$driver->name}"
            );

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تعديل بيانات السائق بنجاح.',
                'حسناً',
                'drivers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 7- حذف سائق ========== */
    public function destroy($id)
    {
        try {
            $driver = Driver::findOrFail($id);
            $driver->delete();

            AdminLoggerService::log(
                'حذف سائق',
                'Driver',
                $id,
                "تم حذف السائق"
            );

            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف السائق بنجاح.',
                'حسناً',
                'drivers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 7.1- حظر / فك حظر سائق ========== */
    public function toggleBan($id)
    {
        try {
            $driver = Driver::findOrFail($id);
            $driver->is_banned = !$driver->is_banned;
            $driver->save();

            $actionText = $driver->is_banned ? 'حظر سائق' : 'إلغاء حظر سائق';
            $details = $driver->is_banned ? "تم حظر السائق {$driver->name}" : "تم إلغاء حظر السائق {$driver->name}";

            AdminLoggerService::log(
                $actionText,
                'Driver',
                $driver->id,
                $details
            );

            $message = $driver->is_banned ? 'تم حظر السائق بنجاح.' : 'تم إلغاء حظر السائق بنجاح.';

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success'   => true,
                    'is_banned' => (bool) $driver->is_banned,
                    'message'   => $message,
                ]);
            }

            return WebResponseClass::sendResponse(
                'تمت العملية بنجاح!',
                $message,
                'حسناً',
                'drivers.index'
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

    /* ========== 8- شحنات السائق ========== */
    public function shipments($id)
    {
        $driver = Driver::findOrFail($id);
        $shipments = $driver->shipments()->latest()->paginate(20);

        return view('pages.drivers.shipments', compact('driver', 'shipments'));
    }

    /* ========== 9- طباعة شحنات السائق ========== */
    public function printShipments($id)
    {
        $driver = Driver::findOrFail($id);
        $shipments = $driver->shipments;

        $totalCOD = $shipments->where('payment_method', 'cod')->sum('cod_amount');

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(5, 5, 5);
        $pdf->setRTL(true);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetFont('dejavusans', '', 11);

        $html = view('pages.drivers.print-shipments', compact('driver', 'shipments', 'totalCOD'))->render();

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('Driver-Shipments-' . $driver->name . '.pdf', 'I');
    }
}
