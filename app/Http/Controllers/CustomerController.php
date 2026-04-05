<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Services\AdminLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    // معتمد
    public function index(Request $request)
    {
        $query = Customer::with(['branch', 'creator']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('whatsapp_number', 'like', "%{$search}%"); 
            });
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        if ($request->isMobile) {
            return view('mobile.pages.people.customers.index', compact('customers'));
        }

        return view('pages.customers.index', compact('customers'));
    }
    public function create()
    {
        return view('pages.customers.create');
    }
    // معتمد
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $appId = $user->app_id;
        $branchId = $user->branch_id; 
        $userId = $user->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,NULL,id,branch_id,' . $branchId,
        ], [
            'name.required' => 'اسم العميل مطلوب',
            'name.max' => 'يجب أن يكون اسم العميل أقل من 255 حرفًا',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مسجل بالفعل في هذا الفرع',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return WebResponseClass::sendValidationError($validator)->with('isModalOpen', true);
        }

        try {
            $data = $validator->validated();
            $data['app_id']     = $appId;
            $data['branch_id']  = $branchId;
            $data['created_by'] = $userId;

            $customer = Customer::create($data);

            if ($request->wantsJson()) {
                session()->flash('success', true);
                session()->flash('success_title', 'تمت الإضافة!');
                session()->flash('success_message', 'تم إنشاء العميل بنجاح.');
                return response()->json(['success' => true]);
            }

            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم إنشاء العميل بنجاح.',
                'حسناً',
                'customers.index'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'حدث خطأ في السيرفر'], 500);
            }
            return WebResponseClass::sendExceptionError($e);
        }
    }
    /** عرض */
    public function show(Request $request, $id)
    {

        $user = auth()->user();
        $customer = Customer::where('branch_code', $user->branch_code)
            ->with(['transactions' => function ($query) {
                $query->latest();
            }])->findOrFail($id);
        // 1. جلب الشحنات مع الدفعات المرتبطة بها
        $shipments = Shipment::with(['senderBranch', 'receiverBranch', 'payments']) // أضفنا payments هنا
            ->where('sender_customer_id', $id) // الشخص هو المرسل
            ->where('payment_method', 'customer_credit') // نوع الدفع جزئي
            ->get();

        // 2. تعريف متغيرات لحساب الإجماليات لكل الشحنات (الملخص العام)
        $grandTotalCost = 0;      // إجمالي قيمة كل الشحنات
        $grandTotalPaid = 0;      // إجمالي ما دفعه العميل حتى الآن (كم له)
        $grandTotalRemaining = 0; // إجمالي المبلغ المتبقي عليه (كم عليه)
        $unpaidShipmentsCount = 0; //  متغير جديد لعدد الشحنات غير المسددة

        // 3. عمل Loop لحساب المبالغ لكل شحنة على حدة
        foreach ($shipments as $shipment) {
            // ملاحظة: افترضت أن عمود سعر الشحنة اسمه 'total_cost' في جدول shipments
            // يرجى تغيير 'total_cost' إلى اسم العمود الصحيح لديك (مثلاً: price, amount, grand_total)
            $shipmentCost = $shipment->total_amount ?? 0;

            // حساب مجموع الدفعات لهذه الشحنة تحديداً
            // نستخدم دالة sum الخاصة بالـ Collection لجمع عمود amount من جدول payments
            $paidAmount = $shipment->payments->sum('amount');

            // حساب المتبقي (قيمة الشحنة - المدفوع)
            $remaining = $shipmentCost - $paidAmount;

            // سنقوم بتخزين هذه القيم داخل كائن الشحنة نفسه لسهولة عرضها في ملف الـ Blade
            $shipment->calculated_paid = $paidAmount;       // تم الدفع
            $shipment->calculated_remaining = $remaining;   // المتبقي

            // إضافة للأجماليات العامة
            $grandTotalCost += $shipmentCost;
            $grandTotalPaid += $paidAmount;
            $grandTotalRemaining += $remaining;
            if ($remaining > 0) {
                $unpaidShipmentsCount++;
            }
        }

        // Store branch code in a variable for use in closures
        $branchCode = auth()->user()->branch_code;

        $shipmentsQuery = Shipment::with(['senderBranch', 'receiverBranch'])
            ->where(function ($query) use ($branchCode, $id) {

                // الحالة الأولى: الفرع هو المرسل + العميل هو المرسل
                $query->where(function ($q) use ($branchCode, $id) {
                    $q->where('sender_branch_code', $branchCode)
                        ->where('sender_customer_id', $id);
                })

                    // أو (OR)

                    // الحالة الثانية: الفرع هو المستقبل + العميل هو المستقبل
                    ->orWhere(function ($q) use ($branchCode, $id) {
                        $q->where('receiver_branch_code', $branchCode)
                            ->where('receiver_customer_id', $id);
                    });
            });
        if ($request->get('direction') == 'sent') {
            // إذا اختار "صادرة": يجب أن يكون هو المرسل فقط
            $shipmentsQuery->where('sender_customer_id', $id);
        } elseif ($request->get('direction') == 'received') {
            // إذا اختار "واردة": يجب أن يكون هو المستلم فقط
            $shipmentsQuery->where('receiver_customer_id', $id);
        } else {
            // الافتراضي (الكل): نجلب الحالتين (مرسل أو مستلم)
            $shipmentsQuery->where(function ($q) use ($id) {
                $q->where('sender_customer_id', $id)
                    ->orWhere('receiver_customer_id', $id);
            });
        }
        if (request()->has('payment_method') && request('payment_method') != 'all') {
            $shipmentsQuery->where('payment_method', request('payment_method'));
        }
        $shipments = $shipmentsQuery->latest()->paginate(10);

        return view('pages.customers.show', compact('customer', 'shipments', 'grandTotalCost', 'grandTotalPaid', 'grandTotalRemaining', 'unpaidShipmentsCount'));
    }

    /** صفحة تعديل */
    public function edit($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customer = Customer::where('branch_code', $user->branch_code)
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($customer);
        }

        return view('pages.customers.edit', compact('customer'));
    }

    // معتمد
   public function update(Request $request, $id)
{
    $user = auth()->user();
    $branchId = $user->branch_id;

    $customer = Customer::where('branch_id', $branchId)->findOrFail($id);

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id . ',id,branch_id,' . $branchId,
    ], [
        'name.required' => 'اسم العميل مطلوب',
        'name.max' => 'يجب أن يكون اسم العميل أقل من 255 حرفًا',
        'phone.required' => 'رقم الهاتف مطلوب',
        'phone.unique' => 'رقم الهاتف مسجل بالفعل لعميل آخر في هذا الفرع',
    ]);

    if ($validator->fails()) {
        if ($request->wantsJson()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        return WebResponseClass::sendValidationError($validator);
    }

    try {
        $validated = $validator->validated();
        $customer->update($validated);

        if ($request->wantsJson()) {
            session()->flash('success', true);
            session()->flash('success_title', 'تم التحديث!');
            session()->flash('success_message', 'تم تحديث بيانات العميل بنجاح.');
            return response()->json(['success' => true]);
        }

        return WebResponseClass::sendResponse(
            'تم التحديث!',
            'تم تحديث بيانات العميل بنجاح.',
            'حسناً'
        );
    } catch (\Exception $e) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'حدث خطأ في السيرفر'], 500);
        }
        return WebResponseClass::sendExceptionError($e);
    }
}
    // معتمد
    public function destroy(Request $request, $id)
{

    $user = auth()->user();
    
    $customer = Customer::where('branch_id', $user->branch_id)->findOrFail($id);

    if ($customer->transactions()->exists()) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'لا يمكن حذف عميل لديه حركات مالية.'], 400);
        }
        return WebResponseClass::sendError('لا يمكن حذف عميل لديه حركات مالية.', 'لا يمكن الحذف!');
    }

    try {
        $customer->delete();

        if ($request->wantsJson()) {
            session()->flash('success', true);
            session()->flash('success_title', 'تم الحذف!');
            session()->flash('success_message', 'تم حذف العميل بنجاح.');
            return response()->json(['success' => true]);
        }

        return WebResponseClass::sendResponse(
            'تم الحذف!',
            'تم حذف العميل بنجاح.',
            'حسناً',
            'customers.index'
        );
    } catch (\Exception $e) {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'حدث خطأ في السيرفر أثناء الحذف'], 500);
        }
        return WebResponseClass::sendExceptionError($e);
    }
}
    /** البحث */
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 1) {
            return response()->json([]);
        }

        $customers = Customer::query()
            ->where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }

    /** تقرير شامل للعميل PDF */
    public function comprehensiveReport($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branchCode = $user->branch_code;

        // جلب بيانات العميل والتأكد من أنه ينتمي للفرع
        $customer = Customer::where('branch_code', $branchCode)
            ->with(['branch'])
            ->findOrFail($id);

        // جلب الشحنات المرسلة من الفرع الحالي
        $sentShipments = Shipment::with(['receiverBranch', 'payments'])
            ->where('sender_customer_id', $id)
            ->where('sender_branch_code', $branchCode)
            ->latest()
            ->get();

        // جلب الشحنات المستقبلة في الفرع الحالي
        $receivedShipments = Shipment::with(['senderBranch', 'payments'])
            ->where('receiver_customer_id', $id)
            ->where('receiver_branch_code', $branchCode)
            ->latest()
            ->get();

        // حساب الديون والرصيد من خلال الشحنات والدفعات
        // نحسب فقط الشحنات الآجلة والجزئية (نستثني COD لأن المستلم سيدفعها)
        $totalShipmentsCost = 0;  // إجمالي قيمة كل الشحنات
        $totalPaid = 0;           // إجمالي المدفوع

        // حساب من الشحنات المرسلة - فقط customer_credit و partial_payment
        foreach ($sentShipments as $shipment) {
            // نحسب فقط الشحنات الآجلة والجزئية
            if (in_array($shipment->payment_method, ['customer_credit', 'partial_payment'])) {
                $shipmentCost = $shipment->total_amount ?? 0;
                $paidAmount = $shipment->payments->sum('amount');

                $totalShipmentsCost += $shipmentCost;
                $totalPaid += $paidAmount;
            }
        }

        // حساب من الشحنات المستقبلة - فقط customer_credit و partial_payment
        foreach ($receivedShipments as $shipment) {
            // نحسب فقط الشحنات الآجلة والجزئية
            if (in_array($shipment->payment_method, ['customer_credit', 'partial_payment'])) {
                $shipmentCost = $shipment->total_amount ?? 0;
                $paidAmount = $shipment->payments->sum('amount');

                $totalShipmentsCost += $shipmentCost;
                $totalPaid += $paidAmount;
            }
        }

        // الرصيد = إجمالي قيمة الشحنات - إجمالي المدفوع
        $balance = $totalShipmentsCost - $totalPaid;
        $isDebtor = $balance > 0;

        // للعرض في التقرير
        $debit = $totalShipmentsCost;  // إجمالي المستحقات (قيمة الشحنات)
        $credit = $totalPaid;           // إجمالي المدفوع

        // إحصائيات الشحنات المرسلة
        $sentTotal = $sentShipments->sum('total_amount');
        $sentCount = $sentShipments->count();
        $sentPrepaid = $sentShipments->where('payment_method', 'prepaid')->count();
        $sentCod = $sentShipments->where('payment_method', 'cod')->count();
        $sentCustomerCredit = $sentShipments->where('payment_method', 'customer_credit')->count();

        // إحصائيات الشحنات المستقبلة
        $receivedTotal = $receivedShipments->sum('total_amount');
        $receivedCount = $receivedShipments->count();
        $receivedPrepaid = $receivedShipments->where('payment_method', 'prepaid')->count();
        $receivedCod = $receivedShipments->where('payment_method', 'cod')->count();
        $receivedCustomerCredit = $receivedShipments->where('payment_method', 'customer_credit')->count();

        // توليد PDF
        $pdf = new \TCPDF;
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->AddPage();

        $html = view('pages.customers.comprehensive_report_pdf', compact(
            'customer',
            'sentShipments',
            'receivedShipments',
            'debit',
            'credit',
            'balance',
            'isDebtor',
            'sentTotal',
            'sentCount',
            'sentPrepaid',
            'sentCod',
            'sentCustomerCredit',
            'receivedTotal',
            'receivedCount',
            'receivedPrepaid',
            'receivedCod',
            'receivedCustomerCredit'
        ))->render();

        $pdf->writeHTML($html);

        return $pdf->Output("customer_comprehensive_report_{$customer->id}.pdf", 'I');
    }

    /** تصدير */
    public function export()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customers = Customer::where('branch_code', $user->branch_code)
            ->latest()
            ->get();

        return view('pages.customers.export', compact('customers'));
    }

    /** تصفية حساب العميل */
    public function clearBalance(Request $request, Customer $customer)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // التحقق من أن العميل ينتمي للفرع
        if ($customer->branch_code !== $user->branch_code) {
            return WebResponseClass::sendError(
                'لا يمكنك تصفية حساب عميل من فرع آخر.',
                'خطأ!'
            );
        }

        // حساب إجمالي دين العميل للتحقق
        $totalDebt = Shipment::where('sender_customer_id', $customer->id)
            ->where('sender_branch_code', $user->branch_code)
            ->where('payment_method', 'customer_credit')
            ->get()
            ->sum(function ($shipment) {
                $paidAmount = $shipment->payments->sum('amount');
                return max(0, $shipment->total_amount - $paidAmount);
            });

        $validator = Validator::make($request->all(), [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($totalDebt) {
                    if ($value > $totalDebt) {
                        $fail('المبلغ المدخل (' . number_format($value, 0) . ' ر.ي) أكبر من إجمالي دين العميل (' . number_format($totalDebt, 0) . ' ر.ي)');
                    }
                },
            ],
            'payment_method' => 'required|in:cash,bank_transfer',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'المبلغ مطلوب',
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صالحة',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();

            // جلب الشحنات الآجلة غير المسددة بالكامل - مرتبة من الأقدم للأحدث (Water-Filling Algorithm)
            $shipments = Shipment::with(['payments'])
                ->where('sender_customer_id', $customer->id)
                ->where('sender_branch_code', $user->branch_code)
                ->where('payment_method', 'customer_credit')
                ->orderBy('created_at', 'ASC') // الأقدم أولاً
                ->get();

            $remainingAmount = $data['amount'];
            $totalPaidAmount = 0; // لتسجيل المبلغ الإجمالي المدفوع

            // توزيع المبلغ على الشحنات
            foreach ($shipments as $shipment) {
                if ($remainingAmount <= 0) break;

                // حساب المبلغ المدفوع سابقاً والمتبقي
                $paidAmount = $shipment->payments->sum('amount');
                $shipmentRemaining = $shipment->total_amount - $paidAmount;

                if ($shipmentRemaining > 0) {
                    // المبلغ الذي سندفعه لهذه الشحنة (الأقل بين المتبقي من الدفعة أو المتبقي على الشحنة)
                    $paymentAmount = min($remainingAmount, $shipmentRemaining);

                    // إنشاء دفعة جديدة
                    $shipment->payments()->create([
                        'amount' => $paymentAmount,
                        'payment_method' => $data['payment_method'],
                        'notes' => $data['notes'] ?? 'تصفية حساب',
                        'reference_number' => $data['reference_number'] ?? null,
                        'created_by' => $user->id,
                        'customer_id' => $customer->id,
                        'branch_code' => $user->branch_code,
                        'payment_date' => now(),
                    ]);

                    // تحديث المبلغ المدفوع الكلي
                    $newPaidTotal = $paidAmount + $paymentAmount;

                    // تحديث حالة الدفع للشحنة
                    if ($newPaidTotal >= $shipment->total_amount) {
                        // تم الدفع بالكامل
                        $shipment->update([
                            'payment_status' => 'paid',
                            'partial_amount' => $shipment->total_amount,
                        ]);
                    } else {
                        // دفع جزئي
                        $shipment->update([
                            'payment_status' => 'partial',
                            'partial_amount' => $newPaidTotal,
                        ]);
                    }

                    $totalPaidAmount += $paymentAmount;
                    $remainingAmount -= $paymentAmount;
                }
            }

            // إنشاء أو جلب فئة "تحصيل ديون"
            $debtCategory = TransactionCategory::firstOrCreate(
                ['code' => 'DEBT_COLLECT'],
                [
                    'name' => 'تحصيل ديون',
                    'type' => 'in', // إيراد
                    'is_active' => true,
                ]
            );

            // إنشاء سجل في جدول المعاملات (Cash Box Integration)
            Transaction::create([
                'branch_code' => $user->branch_code,
                'transaction_category_id' => $debtCategory->id,
                'amount' => $totalPaidAmount,
                'description' => 'تحصيل دين من العميل: ' . $customer->name,
                'created_by' => $user->id,
                'customer_id' => $customer->id,
                'reference_number' => $data['reference_number'] ?? null,
            ]);

            DB::commit();

            return WebResponseClass::sendResponse(
                'تمت التصفية!',
                'تم تصفية حساب العميل بنجاح بمبلغ ' . number_format($totalPaidAmount, 0) . ' ر.ي',
                'حسناً',
                'customers.index'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
