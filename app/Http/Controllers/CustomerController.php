<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Shipment;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Services\AdminLoggerService;
use App\Services\CustomerTransactionService;
use App\Services\WhatsApp\WhatsAppLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    // معتمد
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->query('filter', 'all');

        // ========================================================
        // 📊 جلب العملاء مع الإحصائيات (مخصصة للفرع الحالي)
        // ========================================================
        $query = Customer::with(['branch', 'creator'])
            
            // 💡 1. جعل العميل مرئياً لجميع فروع المكتب (حذفنا شرط branch_id من هنا)
            ->where('app_id', $user->app_id)

            // 2. عدد الشحنات التي أرسلها من (هذا الفرع فقط)
            ->withCount(['sentShipments' => function($q) use ($user) {
                 $q->where('sender_branch_id', $user->branch_id);
            }])

            // 3. كم له؟ رصيد (في هذا الفرع فقط 🎯)
            ->withSum(['transactions as sum_credit' => function ($q) use ($user) {
                $q->where('type', 'credit')
                  ->where('branch_id', $user->branch_id); // 👈 الفلتر السحري
            }], 'amount')

            // 4. كم عليه؟ ديون (في هذا الفرع فقط 🎯)
            ->withSum(['transactions as sum_debit' => function ($q) use ($user) {
                $q->where('type', 'debit')
                  ->where('branch_id', $user->branch_id); // 👈 الفلتر السحري
            }], 'amount');


        // ========================================================
        // 💰 فلترة المديونيات (بناءً على الأزرار)
        // ========================================================
        if ($filter === 'debtors') {
            // عليهم ديون لفرعنا:
            $query->havingRaw('(COALESCE(sum_credit, 0) - COALESCE(sum_debit, 0)) < 0');
        } elseif ($filter === 'creditors') {
            // مصفرين أو لهم رصيد في فرعنا:
            $query->havingRaw('(COALESCE(sum_credit, 0) - COALESCE(sum_debit, 0)) >= 0');
        }

        // ========================================================
        // 🔍 البحث (يبحث في كل عملاء المكتب)
        // ========================================================
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
            return view('mobile.pages.people.customers.index', compact('customers', 'filter'));
        }

        return view('pages.customers.index', compact('customers', 'filter'));
    }
    public function create()
    {
        return view('pages.customers.create-customer-modal');
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
    /** معتمد */
    public function show(Request $request, $id)
    {
        $user = auth()->user();

        // ==========================================
        // 1. جلب العميل مع مجاميع أرصدته (مخصصة لفرعنا فقط 🎯)
        // ==========================================
        $customer = Customer::where('app_id', $user->app_id)
            ->withSum(['transactions as sum_credit' => function ($q) use ($user) {
                $q->where('type', 'credit')
                  ->where('branch_id', $user->branch_id);
            }], 'amount')
            ->withSum(['transactions as sum_debit' => function ($q) use ($user) {
                $q->where('type', 'debit')
                  ->where('branch_id', $user->branch_id);
            }], 'amount')
            ->findOrFail($id);

        $customer->waUrl = WhatsAppLinkService::generate($customer, 'CustomerAccountStatement');
        $customer->printUrl = route('receipt.generate', ['type' => 'CustomerAccountStatement', 'id' => $customer->id]);

        // ==========================================
        // 2. جلب كشف الحساب التفصيلي (حركات فرعنا فقط 🧾)
        // ==========================================
        $transactions = CustomerTransaction::with('shipment')
            ->where('customer_id', $id)
            ->where('branch_id', $user->branch_id)
            ->latest()
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'trans_page')
            ->withQueryString();

        $transactions->getCollection()->transform(function ($trans) {
            $trans->waUrl = WhatsAppLinkService::generate($trans, 'transaction');
            $trans->printUrl = route('receipt.generate', ['type' => 'transaction', 'id' => $trans->id]);
            return $trans;
        });

        // ==========================================
        // 3. سجل الشحنات (History) مع الفلترة الذكية 📦
        // ==========================================
        $branchCode = $user->branch_id;
        $direction = $request->query('direction', 'all'); // استقبال الفلتر من الرابط

        $shipmentsQuery = Shipment::with(['senderBranch', 'receiverBranch']);

        // 💡 السحر هنا: دمجنا شرط الفرع مع شرط صفة العميل (مرسل/مستلم) بدقة لا تقبل الخطأ
        if ($direction == 'sent') {
            // إذا اختار "صادرة": نجلب فقط التي أُرسلت من (فرعنا) وكان (العميل) هو المرسل
            $shipmentsQuery->where('sender_branch_id', $branchCode)
                           ->where('sender_customer_id', $id);
                           
        } elseif ($direction == 'received') {
            // إذا اختار "واردة": نجلب فقط التي وصلت إلى (فرعنا) وكان (العميل) هو المستلم
            $shipmentsQuery->where('receiver_branch_id', $branchCode)
                           ->where('receiver_customer_id', $id);
                           
        } else {
            // "الكل": نجلب الصادرة من فرعنا (والعميل مرسل) + الواردة لفرعنا (والعميل مستلم)
            $shipmentsQuery->where(function ($query) use ($branchCode, $id) {
                $query->where(function ($q) use ($branchCode, $id) {
                    $q->where('sender_branch_id', $branchCode)
                      ->where('sender_customer_id', $id);
                })
                ->orWhere(function ($q) use ($branchCode, $id) {
                    $q->where('receiver_branch_id', $branchCode)
                      ->where('receiver_customer_id', $id);
                });
            });
        }

        // فلتر طريقة الدفع (إن وجد)
        if ($request->has('payment_method') && $request->payment_method != 'all') {
            $shipmentsQuery->where('payment_method', $request->payment_method);
        }

        $shipments = $shipmentsQuery->latest()->paginate(10, ['*'], 'ship_page')->withQueryString();

        // ==========================================
        // 4. إرسال البيانات للواجهة
        // ==========================================
        if ($request->isMobile) {
            return view('mobile.pages.people.customers.show', compact(
                'customer',
                'transactions',
                'shipments',
                'direction'
            ));
        }

        return view('pages.customers.show', compact(
            'customer',
            'transactions',
            'shipments',
            'direction'
        ));
    }

    /** صفحة تعديل */
    public function edit($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        try {
            // استخدم branch_id ليتوافق مع بقية الكود الخاص بك
            $customer = Customer::where('branch_id', $user->branch_id)
                ->findOrFail($id);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json($customer);
            }

            return view('pages.customers.edit-customer-modal', compact('customer'));
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'العميل غير موجود أو لا تملك صلاحية الوصول إليه'], 404);
            }
            abort(404);
        }
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
    // معتمد
    public function addPayment(Request $request, $id)
    {
        // 1. التحقق من المدخلات الأساسية + نوع العملية المخفي
        $request->validate([
            'amount'             => 'required|numeric|min:1',
            'notes'              => 'nullable|string|max:255',
            'transaction_action' => 'required|in:pay_debt,withdraw_balance', // نتحقق من نوع العملية
        ]);

        try {
            DB::beginTransaction();

            // 2. جلب العميل وحساب الرصيد الصافي
            $customer = Customer::withSum(['transactions as sum_credit' => function ($q) {
                $q->where('type', 'credit');
            }], 'amount')
                ->withSum(['transactions as sum_debit' => function ($q) {
                    $q->where('type', 'debit');
                }], 'amount')
                ->findOrFail($id);

            $credit = $customer->sum_credit ?? 0;
            $debit = $customer->sum_debit ?? 0;
            $balance = $credit - $debit;

            $transactionService = new CustomerTransactionService();

            // =========================================================
            // 3. التفريع المنطقي بناءً على نوع العملية
            // =========================================================
            if ($request->transaction_action === 'pay_debt') {

                // --- الحالة الأولى: العميل يسدد ديونه ---
                if ($balance >= 0) {
                    return back()->with('error', 'عفواً، هذا العميل ليس عليه أي مديونية لسدادها.');
                }

                $maxAmountToPay = abs($balance);
                if ($request->amount > $maxAmountToPay) {
                    return back()->with('error', 'لا يمكن سداد مبلغ أكبر من المديونية! المتبقي عليه هو: ' . number_format($maxAmountToPay, 2) . ' ريال.');
                }

                // استدعاء دالة السداد
                $transactionService->addPayment($customer, $request->amount, $request->notes);
                $successMsg = 'تم تسجيل الدفعة بنجاح وإضافتها لكشف الحساب.';
            } else {

                // --- الحالة الثانية: الفرع يصرف رصيد للعميل ---
                if ($balance <= 0) {
                    return back()->with('error', 'عفواً، هذا العميل ليس له أي رصيد لصرفه.');
                }

                if ($request->amount > $balance) {
                    return back()->with('error', 'لا يمكن صرف مبلغ أكبر من رصيد العميل! رصيده الحالي هو: ' . number_format($balance, 2) . ' ريال.');
                }

                // استدعاء دالة الصرف التي أنشأناها قبل قليل
                $transactionService->withdrawBalance($customer, $request->amount, $request->notes);
                $successMsg = 'تم صرف الرصيد للعميل بنجاح وخصمه من حسابه.';
            }

            DB::commit();

            return WebResponseClass::sendResponse('تمت العملية!', $successMsg, 'حسناً');
        } catch (\Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }
    /** البحث */
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $user = auth()->user();

        if (mb_strlen($q) < 1) {
            return response()->json([]);
        }

        $customers = Customer::query()
            ->where('branch_id', $user->branch_id)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }

    /** تقرير شامل للعميل PDF */
    public function accountStatement($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branchId = $user->branch_id;
        $branchCode = $user->branch_code;

        // بيانات العميل
        $customer = Customer::where('branch_id', $branchId)
            ->with('branch')
            ->findOrFail($id);

        // جلب الشحنات المرتبطة بالعميل في هذا الفرع
        $sentShipments = Shipment::with(['receiverBranch', 'payments'])
            ->where('sender_customer_id', $id)
            ->where('sender_branch_id', $branchId)
            ->get();

        $receivedShipments = Shipment::with(['senderBranch', 'payments'])
            ->where('receiver_customer_id', $id)
            ->where('receiver_branch_id', $branchId)
            ->get();

        // تجهيز الحركات
        $entries = collect();

        // الشحنات المرسلة
        foreach ($sentShipments as $shipment) {
            $paid = $shipment->payments->sum('amount');
            $shipmentAmount = (float) ($shipment->total_amount ?? 0);

            // فقط الآجل والجزئي تدخل في الذمم
            if (in_array($shipment->payment_method, ['customer_credit', 'partial_payment'])) {
                $entries->push([
                    'date' => $shipment->created_at,
                    'type' => 'shipment',
                    'description' => 'شحنة مرسلة - سند رقم ' . ($shipment->bond_number ?? '---'),
                    'reference' => $shipment->bond_number ?? '---',
                    'debit' => $shipmentAmount,
                    'credit' => 0,
                    'payment_method' => $shipment->payment_method,
                    'notes' => $shipment->notes,
                ]);

                if ($paid > 0) {
                    $entries->push([
                        'date' => $shipment->created_at,
                        'type' => 'payment',
                        'description' => 'دفعة على الشحنة - سند رقم ' . ($shipment->bond_number ?? '---'),
                        'reference' => $shipment->bond_number ?? '---',
                        'debit' => 0,
                        'credit' => $paid,
                        'payment_method' => $shipment->payment_method,
                        'notes' => null,
                    ]);
                }
            }
        }

        // الشحنات المستقبلة
        foreach ($receivedShipments as $shipment) {
            $paid = $shipment->payments->sum('amount');
            $shipmentAmount = (float) ($shipment->total_amount ?? 0);

            if (in_array($shipment->payment_method, ['customer_credit', 'partial_payment'])) {
                $entries->push([
                    'date' => $shipment->created_at,
                    'type' => 'shipment',
                    'description' => 'شحنة مستقبلة - سند رقم ' . ($shipment->bond_number ?? '---'),
                    'reference' => $shipment->bond_number ?? '---',
                    'debit' => $shipmentAmount,
                    'credit' => 0,
                    'payment_method' => $shipment->payment_method,
                    'notes' => $shipment->notes,
                ]);

                if ($paid > 0) {
                    $entries->push([
                        'date' => $shipment->created_at,
                        'type' => 'payment',
                        'description' => 'دفعة على الشحنة - سند رقم ' . ($shipment->bond_number ?? '---'),
                        'reference' => $shipment->bond_number ?? '---',
                        'debit' => 0,
                        'credit' => $paid,
                        'payment_method' => $shipment->payment_method,
                        'notes' => null,
                    ]);
                }
            }
        }

        // ترتيب الحركات حسب التاريخ
        $entries = $entries->sortBy('date')->values();

        // حساب الرصيد الجاري
        $runningBalance = 0;
        $entries = $entries->map(function ($entry) use (&$runningBalance) {
            $runningBalance += ($entry['debit'] - $entry['credit']);
            $entry['balance'] = $runningBalance;
            return $entry;
        });

        // المجاميع
        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');
        $finalBalance = $totalDebit - $totalCredit;
        $isDebtor = $finalBalance > 0;

        // إحصائيات إضافية
        $sentCount = $sentShipments->count();
        $receivedCount = $receivedShipments->count();
        $totalShipments = $sentCount + $receivedCount;

        return view('receipts.templates.CustomerAccountStatement', compact(
            'customer',
            'entries',
            'totalDebit',
            'totalCredit',
            'finalBalance',
            'isDebtor',
            'sentCount',
            'receivedCount',
            'totalShipments'
        ));
    }


    /** تصدير */
    public function export()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customers = Customer::where('branch_id', $user->branch_id)
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
            ->where('sender_branch_id', $user->branch_code)
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
                ->where('sender_branch_id', $user->branch_code)
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
