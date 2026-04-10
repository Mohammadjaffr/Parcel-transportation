<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\AdminActivity;
use App\Models\App;
use App\Models\Branch;
use App\Models\CashRegisterClosing;
use App\Models\Customer;
use App\Models\Office;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\AdminShipmentCreated;
use App\Services\AdminLoggerService;
use App\Services\ShipmentPaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class ShipmentController extends Controller
{
    protected $shipmentPaymentService;

    public function __construct(ShipmentPaymentService $shipmentPaymentService)
    {
        $this->shipmentPaymentService = $shipmentPaymentService;
    }

    /* ========== 1- عرض جميع الطردات ========== */
    // معتمد
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // استخدمنا branch_id بدلاً من branch_code كما اتفقنا في التعديلات السابقة
        $branchId = $user->branch_id;

        // نستقبل نوع العرض من الرابط، والافتراضي هو المرسلة (outgoing)
        $type = $request->query('type', 'outgoing');

        $query = Shipment::with(['receiverBranch', 'receiverCustomer', 'senderCustomer']);

        if ($type === 'incoming') {
            // جلب الطرود المستلمة (التي وجهتها هذا الفرع)
            $query->where('receiver_branch_id', $user->branch_id);
        } else {
            // جلب الطرود المرسلة (التي أرسلها هذا الفرع)
            $query->where('sender_branch_id', $user->branch_id);
        }

        // استخدمنا اسم المتغير $shipments ليتوافق مع ملف الـ Blade الذي صممناه
        $shipments = $query->latest()->paginate(10)->withQueryString();

        if ($request->isMobile) {
            // تمرير المتغيرات للصفحة
            return view('mobile.pages.shipment.outgoing.index', compact('shipments', 'type'));
        }

        return view('pages.shipment.index', compact('shipments', 'type'));
    }

    /* ========== 2- صفحة إنشاء طرد ========== */
    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $currentApp = $user->app;

        // --- 1. جلب المكاتب الموثوقة (Apps المتصلة) ---
        $connectedAppIds = collect();
        $sentAccepted = $currentApp->sentConnections()->where('status', 'accepted')->pluck('receiver_app_id');
        $receivedAccepted = $currentApp->receivedConnections()->where('status', 'accepted')->pluck('sender_app_id');
        $connectedAppIds = $connectedAppIds->merge($sentAccepted)->merge($receivedAccepted)->unique();

        $trustedApps = App::whereIn('id', $connectedAppIds)->with('branches')->get();

        // --- 2. جلب المكاتب الخارجية (غير الموثوقة - Model Office) ---
        // هذه المكاتب تابعة لتطبيقك عبر BelongsToApp
        $untrustedOffices = Office::where('app_id', $currentApp->id)->with('branches')->get();

        // --- 3. تجهيز مصفوفة الوجهات الشاملة ---
        $officesData = collect();

        // أ- إضافة فروعنا الداخلية
        $myBranches = Branch::where('app_id', $currentApp->id)
            ->where('id', '!=', $user->branch_id)
            ->get();
        if ($myBranches->isNotEmpty()) {
            $officesData->push([
                'id' => 'internal_' . $currentApp->id,
                'name' => '🏠 مكتبنا الحالي',
                'branches' => $myBranches
            ]);
        }
        // ب- إضافة المكاتب الموثوقة (Apps)
        foreach ($trustedApps as $tApp) {
            if ($tApp->branches->isNotEmpty()) {
                $officesData->push([
                    'id' => 'trusted_' . $tApp->id,
                    'name' => $tApp->name, // اسم نظيف
                    'branches' => $tApp->branches
                ]);
            }
        }

        // ج- إضافة المكاتب غير الموثوقة (External Offices)
        foreach ($untrustedOffices as $uOffice) {
            if ($uOffice->branches->isNotEmpty()) {
                $officesData->push([
                    'id' => 'untrusted_' . $uOffice->id,
                    'name' => $uOffice->name, // اسم نظيف
                    'branches' => $uOffice->branches
                ]);
            }
        }

        // --- 3. جلب العملاء كالمعتاد ---
        $customers = Customer::where('branch_id', $user->branch_id)->get(['id', 'name', 'phone']);

        $customer = null;
        $role = $request->query('role');

        if ($request->filled('customer_id')) {
            $customer = Customer::findOrFail($request->customer_id);
        }


        // التوجيه إلى الواجهة وتمرير المتغيرات
        if ($request->isMobile) {
            return view('mobile.pages.shipment.outgoing.create', [
                'offices' => $officesData, // نمرر البيانات المهيأة
                'customers' => $customers,
                'customer' => $customer,
                'role' => $role
            ]);
        }

        return view('pages.shipment.outgoing.create', [
            'offices' => $officesData,
            'customers' => $customers,
            'customer' => $customer,
            'role' => $role
        ]);
    }
    public function outgoing(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // جلب الطرود المرسلة من فرع المستخدم
        $shipments = Shipment::with(['receiverBranch.app', 'receiverOfficeBranch.office', 'receiverCustomer', 'senderCustomer'])
            ->where('sender_branch_id', $user->branch_id)
            ->latest()
            ->paginate(10);

        if ($request->isMobile) {
            return view('mobile.pages.shipment.outgoing.index', compact('shipments'));
        }

        return view('pages.shipment.outgoing.index', compact('shipments'));
    }

    public function incoming(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // جلب الطرود المستلمة في فرع المستخدم
        $shipments = Shipment::with(['senderBranch.app', 'senderCustomer', 'receiverCustomer'])
            ->where('receiver_branch_id', $user->branch_id)
            ->latest()
            ->paginate(10);

        if ($request->isMobile) {
            return view('mobile.pages.shipment.incoming.index', compact('shipments'));
        }

        return view('pages.shipment.incoming.index', compact('shipments'));
    }
    public function store(Request $request)
    {

        $rules = [
            'office_id'            => 'required|string', // مثال: internal_1 أو untrusted_5
            'receiver_branch_id'   => 'required|integer', // المعرف (ID) القادم من قائمة الفروع

            'sender_customer_id'   => 'nullable|exists:customers,id',
            'sender_name'          => 'required_without:sender_customer_id|string|max:255',
            'sender_phone'         => 'required_without:sender_customer_id|string|max:50',

            'receiver_customer_id' => 'nullable|exists:customers,id',
            'receiver_name'        => 'required_without:receiver_customer_id|string|max:255',
            'receiver_phone'       => 'required_without:receiver_customer_id|string|max:50',

            'package_type'         => 'required|string',
            'weight'               => 'nullable|numeric|min:0',
            'no_gallons_honey'     => 'nullable|numeric|min:0',
            'no_honey_jars'        => 'nullable|numeric|min:0',

            'payment_method'       => 'required|in:prepaid,cod,partial_payment,customer_credit',
            'total_amount'         => 'required|numeric|min:0',
            'partial_amount'       => 'required_if:payment_method,partial_payment|nullable|numeric|min:0',
            'notes'                => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        // تحقق إضافي للدفع الجزئي
        $validator->after(function ($validator) use ($request) {
            if ($request->payment_method === 'partial_payment') {
                $total = (float) $request->total_amount;
                $partial = (float) $request->partial_amount;
                if ($partial >= $total) {
                    $validator->errors()->add('partial_amount', 'المبلغ المدفوع جزئياً يجب أن يكون أقل من الإجمالي.');
                }
            }
        });

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();
            $user = auth()->user();

            $senderBranchId = $user->branch_id;

            // ========================================================
            // اللوجيك الذكي: تحديد مسار الفرع الوجهة
            // ========================================================
            $officeIdParts = explode('_', $data['office_id']);
            $officeType = $officeIdParts[0]; // (internal, trusted, untrusted)

            $finalReceiverBranchId = null;
            $finalReceiverOfficeBranchId = null;

            if ($officeType === 'untrusted') {
                // المسار الثاني: فرع لمكتب خارجي يدوي
                $finalReceiverOfficeBranchId = $data['receiver_branch_id'];
            } else {
                // المسار الأول: فرع داخلي أو موثوق مسجل بالنظام
                $finalReceiverBranchId = $data['receiver_branch_id'];
            }

            // ========================================================
            // معالجة العملاء الصامتة (إنشاء إذا لم يكن موجوداً)
            // ========================================================

            // 1. المرسل
            $senderCustomerId = $data['sender_customer_id'];
            if (empty($senderCustomerId)) {
                $senderCustomer = Customer::firstOrCreate(
                    ['phone' => $data['sender_phone']],
                    [
                        'name' => $data['sender_name'],
                        'app_id' => $user->app_id,
                        'branch_id' => $senderBranchId, // يتبع لفرع الموظف الحالي
                        'created_by' => $user->id
                    ]
                );
                $senderCustomerId = $senderCustomer->id;
            }

            // 2. المستلم
            $receiverCustomerId = $data['receiver_customer_id'];
            if (empty($receiverCustomerId)) {
                $receiverCustomer = Customer::firstOrCreate(
                    ['phone' => $data['receiver_phone']],
                    [
                        'name' => $data['receiver_name'],
                        'app_id' => $user->app_id,
                        'branch_id' => $senderBranchId,
                        'created_by' => $user->id
                    ]
                );
                $receiverCustomerId = $receiverCustomer->id;
            }

            // ========================================================
            // إنشاء الشحنة (الطرد)
            // ========================================================
            $shipment = Shipment::create([
                'sender_branch_id'          => $senderBranchId,

                // الحقول المفصولة للوجهة:
                'receiver_branch_id'        => $finalReceiverBranchId,
                'receiver_office_branch_id' => $finalReceiverOfficeBranchId,

                'sender_customer_id'        => $senderCustomerId,
                'receiver_customer_id'      => $receiverCustomerId,

                'package_type'              => $data['package_type'],
                'weight'                    => $data['weight'] ?? 0,
                'no_gallons_honey'          => $data['no_gallons_honey'] ?? 0,
                'no_honey_jars'             => $data['no_honey_jars'] ?? 0,

                'payment_method'            => $data['payment_method'],
                'total_amount'              => $data['total_amount'],
                'partial_amount'            => $data['payment_method'] === 'partial_payment' ? $data['partial_amount'] : 0,
                'notes'                     => $data['notes'],

                'status'                    => 'pending',
                'customer_debt_status'      => $data['payment_method'] === 'customer_credit' ? 'pending' : null,
            ]);

            // ========================================================
            // إشعار الإدارة (Admins Notification)
            // ========================================================
            // نجلب مستخدمي النظام الذين يحملون دور "admin" في نفس التطبيق (App)
            $admins = User::where('app_id', $user->app_id)
                ->where('type', 'admin')
                ->get();
            if ($admins->isNotEmpty()) {
                Notification::send(
                    $admins,
                    new AdminShipmentCreated(
                        $user->name,
                        $user->branch->name ?? 'غير محدد الفرع',
                        $shipment->bond_number,
                        $shipment->id
                    )
                );
            }

            // ========================================================
            // معالجة المدفوعات (عبر Service)
            // ========================================================
            // $paidAmount = null;
            // if ($shipment->payment_method === 'prepaid') {
            //     $paidAmount = (float) $shipment->total_amount;
            // } elseif ($shipment->payment_method === 'partial_payment') {
            //     $paidAmount = (float) $shipment->partial_amount;
            // }

            // // نفترض أن الدفع النقدي هو الافتراضي للموبايل
            // $paymentType = 'cash'; 

            // $this->shipmentPaymentService->handlePaymentForNewShipment(
            //     $shipment,
            //     $paymentType,
            //     $paidAmount,
            //     null
            // );

            DB::commit();

            // التوجيه بعد النجاح
            if ($request->isMobile) {
                return WebResponseClass::sendResponse(
                    'تم اعتماد الطرد!',
                    'رقم السند: ' . $shipment->bond_number,
                    'عرض الطرود',
                    'shipment.index'
                );
            }

            return WebResponseClass::sendResponse(
                'تم إضافة الطرد!',
                'تم إنشاء بوليصة الشحن بنجاح.',
                'حسناً',
                'shipment.outgoing'
            );
        } catch (\Exception $e) {

            DB::rollBack();
            dd($e->getMessage());
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function createCustomer()
    {
        return view('pages.shipment.customer.create');
    }

    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:sender,receiver'],
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        $data = $validator->validated();

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'branch_code' => auth()->user()->branch_code,
            'type' => 'general', // مهم للمستقبل
        ]);

        return redirect()->route('shipment.create', [
            'customer_id' => $customer->id,
            'role' => $data['role'],
        ]);
    }

    /* ========== 4- عرض تفاصيل طرد واحد ========== */
    public function show($id)
    {
        $shipment = Shipment::with('payments')->findOrFail($id);
        $countrequests = Shipment::count();

        return view('pages.shipment.show', compact('shipment', 'countrequests'));
    }

    /* ========== 5- صفحة تعديل الطرد ========== */
    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branches = Branch::where('code', '!=', $user->branch_code)->get();
        // $drivers = Driver::where('status', 'active')->get();
        $customers = Customer::all();

        return view('pages.shipment.edit', compact('shipment', 'branches', 'customers'));
    }

    /* ========== 6- تحديث الطرد ========== */
    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        if ($shipment->status === 'cancelled') {
            return WebResponseClass::sendError(
                'عذراً، لا يمكن تعديل شحنة ملغية.',
                'خطأ!',
                'حسناً'
            );
        }

        // يحدد أي جزء نحدثه
        $section = $request->input('section', 'all');

        if ($section === 'sender_receiver') {

            $rules = [
                'receiver_branch_code' => 'required|exists:branches,code',

                'sender_customer_id' => 'nullable|exists:customers,id',
                'receiver_customer_id' => 'nullable|exists:customers,id',

                'sender_name' => 'required_without:sender_customer_id|string|max:255',
                'sender_phone' => 'required_without:sender_customer_id|string|max:50',
                'code' => 'nullable|string|max:255',

                'receiver_name' => 'required_without:receiver_customer_id|string|max:255',
                'receiver_phone' => 'required_without:receiver_customer_id|string|max:50',

                'no_honey_jars' => 'nullable|numeric|min:0',
                'no_gallons_honey' => 'nullable|numeric|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            // التحقق من أن فرع الإرسال != فرع الاستقبال
            $validator->after(function ($validator) use ($request, $shipment) {
                /** @var \App\Models\User $user */
                $user = auth()->user();

                $sender = $user->branch_code;
                $receiver = $request->receiver_branch_code ?? $shipment->receiver_branch_code;

                if ($sender && $receiver && $sender === $receiver) {
                    $validator->errors()->add('receiver_branch_code', 'لا يمكن اختيار نفس جهة الإرسال.');
                }
            });

            if ($validator->fails()) {
                return WebResponseClass::sendValidationError($validator);
            }

            $data = $validator->validated();

            // إنشاء / تحديث العميل المرسل
            if (empty($data['sender_customer_id'])) {
                $senderCustomer = Customer::where('phone', $data['sender_phone'])->first();

                if ($senderCustomer) {
                    $senderCustomer->update(['name' => $data['sender_name']]);
                } else {
                    $senderCustomer = Customer::create([
                        'phone' => $data['sender_phone'],
                        'name' => $data['sender_name'],
                        'branch_code' => auth()->user()->branch_code,
                    ]);
                }

                $data['sender_customer_id'] = $senderCustomer->id;
            }

            // إنشاء / تحديث العميل المستلم
            if (empty($data['receiver_customer_id'])) {
                $receiverCustomer = Customer::where('phone', $data['receiver_phone'])->first();

                if ($receiverCustomer) {
                    $receiverCustomer->update(['name' => $data['receiver_name']]);
                } else {
                    $receiverCustomer = Customer::create([
                        'phone' => $data['receiver_phone'],
                        'name' => $data['receiver_name'],
                        'branch_code' => $data['receiver_branch_code'],
                    ]);
                }

                $data['receiver_customer_id'] = $receiverCustomer->id;
            }

            // فرع الإرسال من المستخدم
            $data['sender_branch_code'] = auth()->user()->branch_code;

            $shipment->update($data);

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيانات المرسل والمستلم بنجاح.',
                'حسناً',
                'shipment.index'
            );
        }

        if ($section === 'details') {

            $rules = [
                'code' => 'nullable|string|max:255',
                'package_type' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'status' => 'required|in:pending,in_transit,delivered',
                'notes' => 'nullable|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return WebResponseClass::sendValidationError($validator);
            }

            $data = $validator->validated();
            $shipment->update($data);

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث تفاصيل الطرد بنجاح.',
                'حسناً',
                'shipment.index'
            );
        }

        if ($section === 'payment') {

            $rules = [
                'payment_method' => 'required|in:prepaid,cod,partial_payment,customer_credit',

                // تظهر عند prepaid أو partial_payment
                'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',

                // ✅ رقم الإيداع إلزامي عند التحويل البنكي فقط
                'prepaid_reference' => 'required_if:prepaid_payment_method,bank_transfer|nullable|string|max:255',

                // ✅ لا نستخدم الصورة نهائياً (حتى لو موجودة بالواجهة)
                'prepaid_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',

                'partial_amount' => 'required_if:payment_method,partial_payment|numeric|min:0.01',
                'customer_debt_status' => 'nullable|in:pending,partially_paid,fully_paid,overdue',
            ];

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($request, $shipment) {

                // ✅ تحقق الدفع الجزئي أقل من الإجمالي
                if (($request->payment_method ?? $shipment->payment_method) === 'partial_payment') {
                    $totalAmount = $request->total_amount ?? $shipment->total_amount;
                    $partialAmount = $request->partial_amount;

                    if (! is_null($partialAmount) && is_numeric($partialAmount) && is_numeric($totalAmount)) {
                        if ((float) $partialAmount >= (float) $totalAmount) {
                            $validator->errors()->add(
                                'partial_amount',
                                'المبلغ المدفوع جزئيًا يجب أن يكون أقل من المبلغ الإجمالي.'
                            );
                        }
                    }
                }
            });

            if ($validator->fails()) {
                return WebResponseClass::sendValidationError($validator);
            }

            $data = $validator->validated();

            // ضبط حالة المديونية
            if (($data['payment_method'] ?? null) === 'customer_credit') {
                $data['customer_debt_status'] = $data['customer_debt_status'] ?? 'pending';
            } else {
                $data['customer_debt_status'] = null;
            }

            // نأخذ partial_amount قبل أي عمليات
            $partialAmount = $data['partial_amount'] ?? null;

            // هذه الحقول لا تدخل جدول الشحنات
            unset(
                $data['prepaid_payment_method'],
                $data['prepaid_attachment'],
            );

            $shipment->update($data);

            // ✅ لو COD أو آجل: نحذف أي دفعات سابقة (مهم جداً)
            if (in_array($shipment->payment_method, ['cod', 'customer_credit'])) {
                $shipment->payments()->delete();

                return WebResponseClass::sendResponse(
                    'تم التحديث!',
                    'تم تحديث بيانات الدفع بنجاح.',
                    'حسناً',
                    'shipment.index'
                );
            }

            // حساب المبلغ المدفوع الآن
            $paymentType = $request->prepaid_payment_method ?? 'cash';
            $paidAmount = null;

            if ($shipment->payment_method === 'partial_payment') {
                $paidAmount = $partialAmount ? (float) $partialAmount : null;
            } elseif ($shipment->payment_method === 'prepaid') {
                $paidAmount = (float) $shipment->total_amount;
            }

            // ✅ لا تمرر أي ملف
            $this->shipmentPaymentService->handlePaymentForNewShipment(
                $shipment,
                $paymentType,
                $paidAmount,
                $request->prepaid_reference
            );

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيانات الدفع بنجاح.',
                'حسناً',
                'shipment.index'
            );
        }

        return WebResponseClass::sendError('قسم التحديث غير معروف.');
    }

    public function updatePaymentMethod(Request $request, $id)
    {

        $shipment = Shipment::findOrFail($id);

        if ($shipment->status === 'cancelled') {
            return WebResponseClass::sendError(
                'عذراً، لا يمكن تعديل طريقة دفع لشحنة ملغية.',
                'خطأ!',
                'حسناً'
            );
        }
        $shipmentDate = $shipment->created_at->format('Y-m-d');
        $isDayClosed = CashRegisterClosing::where('branch_code', $shipment->created_branch_code) // أو sender_branch_code حسب منطقك
            ->whereDate('created_at', $shipmentDate)
            ->exists();
        if ($isDayClosed) {
            return WebResponseClass::sendError(
                'لقد تم "إقفال الصندوق" (Daily Closing) لتاريخ هذه الشحنة (' . $shipmentDate . '). ',
                'عذراً، لا يمكن تعديل طريقة الدفع.',

            );
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:prepaid,cod,partial_payment,customer_credit',
            'partial_amount' => 'required_if:payment_method,partial_payment|numeric|min:0.01',
            'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',
            'prepaid_reference' => 'nullable|string|max:255',
            'customer_debt_status' => 'nullable|in:pending,partially_paid,fully_paid,overdue',
        ]);

        $validator->after(function ($validator) use ($request, $shipment) {

            $paymentMethod = $request->payment_method ?? $shipment->payment_method;
            $payType = $request->prepaid_payment_method ?? null;

            // ✅ إلزام رقم الإيداع فقط لو (تحويل بنكي) ومع (prepaid أو partial_payment)
            $needsReference = in_array($paymentMethod, ['prepaid', 'partial_payment'])
                && $payType === 'bank_transfer';

            if ($needsReference && blank($request->prepaid_reference)) {
                $validator->errors()->add('prepaid_reference', 'رقم الإيداع مطلوب عند اختيار تحويل بنكي.');
            }

            // ✅ تحقق الدفع الجزئي أقل من الإجمالي
            if ($paymentMethod === 'partial_payment') {
                $partial = (float) ($request->partial_amount ?? 0);
                $total = (float) $shipment->total_amount;

                if ($partial >= $total) {
                    $validator->errors()->add(
                        'partial_amount',
                        'المبلغ المدفوع جزئيًا يجب أن يكون أقل من المبلغ الإجمالي.'
                    );
                }
            }
        });


        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        $data = $validator->validated();
        try {
            DB::beginTransaction();

            // مهما كان النوع القديم، نحذفه من الصندوق ومن مدفوعات العملاء
            $this->shipmentPaymentService->voidShipmentTransactions($shipment);
            // ✅ تحديث حقول الشحنة الأساسية
            $shipment->payment_method = $data['payment_method'];
            if ($shipment->payment_method === 'customer_credit') {
                $shipment->customer_debt_status = $data['customer_debt_status'] ?? 'pending';
            } else {
                $shipment->customer_debt_status = null;
            }
            $shipment->save();
            // ✅ إذا COD أو آجل: احذف أي دفعات مسجلة
            if (in_array($shipment->payment_method, ['cod', 'customer_credit'])) {
                DB::commit();
                return WebResponseClass::sendResponse(
                    'تم التحديث!',
                    'تم تحديث بيانات الدفع بنجاح.',
                    'حسناً',
                    'shipment.index'
                );
            }
            // ✅ تحديد المبلغ المدفوع الآن
            $paidAmount = null;
            if ($shipment->payment_method === 'prepaid') {
                $paidAmount = (float) $shipment->total_amount;
            }

            if ($shipment->payment_method === 'partial_payment') {
                $paidAmount = (float) $data['partial_amount'];
            }

            $paymentType = $data['prepaid_payment_method'] ?? 'cash';
            // ✅ الاستدعاء الصحيح حسب Service (4 باراميترات)
            $this->shipmentPaymentService->handlePaymentForNewShipment(
                $shipment,
                $paymentType,
                $paidAmount,
                $data['prepaid_reference'] ?? null
            );
            DB::commit();
            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيانات الدفع بنجاح.',
                'حسناً',
                'shipment.index'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /* ========== 7- حذف الطرد ========== */
    public function destroy($id)
    {
        try {
            Shipment::findOrFail($id)->delete();
            AdminLoggerService::log('حذف طرد', 'Shipment', 'تم حذف الطرد بنجاح');

            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف الطرد بنجاح.',
                'حسناً',
                'shipment.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function adminlog()
    {
        $logs = AdminActivity::latest()->paginate(20);

        return view('pages.log.logs', compact('logs'));
    }

    public function selectCustomer()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customers = Customer::where('branch_code', $user->branch_code)->get();

        return view('pages.shipment.select-customer', compact('customers'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_transit,delivered,cancelled,returned',
        ]);
        try {
            DB::beginTransaction();
            $shipment = Shipment::findOrFail($id);
            $shipment->update([
                'status' => $request->status,
            ]);

            if ($request->status === 'delivered' && in_array($shipment->payment_method, ['cod', 'partial_payment'])) {
                $paymentService = app(ShipmentPaymentService::class);
                $paymentService->createCodBranchTransactionOnDelivery($shipment);
            }
            DB::commit();
            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث حالة الطرد بنجاح.',
                'حسناً',
                'shipment.index'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * ========== إرجاع شحنة ==========
     * Return a shipment (after delivery attempt failed)
     */
    public function returnShipment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::beginTransaction();

            $shipment = Shipment::findOrFail($id);

            // التحقق من أن الحالة تسمح بالإرجاع
            if (!in_array($shipment->status, ['delivered', 'in_transit'])) {
                return WebResponseClass::sendError(
                    'لا يمكن إرجاع شحنة في هذه الحالة. الحالة الحالية: ' . $shipment->status,
                    'خطأ',
                    'حسناً'
                );
            }

            // عكس قيود BranchLedger إذا كانت الشحنة مسلمة
            if ($shipment->status === 'delivered') {
                $this->shipmentPaymentService->reverseShipmentLedger($shipment, $request->reason);
            }

            // تحديث حالة الشحنة
            $shipment->status = 'returned';
            $shipment->notes = ($shipment->notes ? $shipment->notes . "\n" : '') . 'سبب الإرجاع: ' . $request->reason;
            $shipment->save();

            // تسجيل النشاط
            AdminLoggerService::log('إرجاع شحنة', 'Shipment', 'تم إرجاع الشحنة رقم ' . $shipment->bond_number . ' - السبب: ' . $request->reason, $shipment->id);

            DB::commit();

            return WebResponseClass::sendResponse(
                'تم الإرجاع!',
                'تم إرجاع الشحنة بنجاح وعكس القيود المالية.',
                'حسناً',
                'shipment.index'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * ========== إلغاء شحنة ==========
     * Cancel a shipment (before or during transit)
     */
    public function cancelShipment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::beginTransaction();

            $shipment = Shipment::findOrFail($id);

            // التحقق من أن الحالة تسمح بالإلغاء
            if (!in_array($shipment->status, ['pending'])) {
                return WebResponseClass::sendError(
                    'لا يمكن إلغاء شحنة في هذه الحالة. الحالة الحالية: ' . $shipment->status,
                    'خطأ',
                    'حسناً'
                );
            }

            // إلغاء جميع المعاملات المالية
            $this->shipmentPaymentService->cancelShipmentTransactions($shipment);

            // تحديث حالة الشحنة
            $shipment->status = 'cancelled';
            $shipment->notes = ($shipment->notes ? $shipment->notes . "\n" : '') . 'سبب الإلغاء: ' . $request->reason;
            $shipment->save();

            // تسجيل النشاط
            AdminLoggerService::log('إلغاء شحنة', 'Shipment', 'تم إلغاء الشحنة رقم ' . $shipment->bond_number . ' - السبب: ' . $request->reason, $shipment->id);

            DB::commit();

            return WebResponseClass::sendResponse(
                'تم الإلغاء!',
                'تم إلغاء الشحنة بنجاح وحذف المعاملات المالية.',
                'حسناً',
                'shipment.index'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
