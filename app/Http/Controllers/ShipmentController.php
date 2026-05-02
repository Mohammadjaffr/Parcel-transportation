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
use App\Models\ShipmentPackage;
use App\Models\User;
use App\Notifications\AdminShipmentCreated;
use App\Notifications\AdminShipmentStatusUpdated;
use App\Notifications\NewShipmentNotification;
use App\Notifications\PackageReceivedNotification;
use App\Services\AdminLoggerService;
use App\Services\ShipmentPaymentService;
use App\Services\WhatsApp\WhatsAppLinkService;
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
    
    // معتمد
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // استخدام branch_id الخاص بالموظف
        $branchId = $user->branch_id;

        // نستقبل نوع العرض من الرابط، والافتراضي هو المرسلة (outgoing)
        $type = $request->query('type', 'outgoing');

        // 1. بناء الاستعلام الأساسي مع جلب كافة العلاقات المستخدمة في الواجهة
        $query = Shipment::with([
            'receiverBranch',
            'receiverCustomer',
            'senderCustomer',
            'senderBranch',         // أضفناها لتجنب استعلامات إضافية في الواجهة
            'receiverOfficeBranch', // أضفناها لتجنب استعلامات إضافية في الواجهة
            'receiverBranch.app'    // أضفناها للوصول لاسم الشركة المستقبلة
        ]);

        // 2. فلترة حسب نوع الطرد (وارد أم صادر)
        if ($type === 'incoming') {
            // جلب الطرود المستلمة (التي وجهتها هذا الفرع)
            $query->where('receiver_branch_id', $branchId);
        } else {
            // جلب الطرود المرسلة (التي أرسلها هذا الفرع)
            $query->where('sender_branch_id', $branchId);
        }

        // 3. 💡 تطبيق الفلترة الذكية حسب الحالة (إذا تم الضغط على أحد أزرار الفلتر)
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // 4. جلب البيانات مع الترقيم والاحتفاظ بكافة المتغيرات في الرابط (type, status, page)
        $shipments = $query->latest()->paginate(15)->withQueryString();
        $shipments->getCollection()->transform(function ($shipment) {

            // إذا أردت رابط المرسل دائماً (مثلاً في صفحة الصادر)
            $shipment->sender_whatsapp_link = WhatsAppLinkService::generate($shipment, 'sender');

            // إذا أردت تجهيز رابط المستلم أيضاً (مفيد في صفحة الوارد)
            $shipment->receiver_whatsapp_link = WhatsAppLinkService::generate($shipment, 'receiver');
            return $shipment;
        });

        if ($request->isMobile) {
            // تمرير المتغيرات للصفحة
            return view('mobile.pages.shipment.outgoing.index', compact('shipments', 'type'));
        }

        return view('pages.shipment.index', compact('shipments', 'type'));
    }
    // ===========================================================================
    // Start Outgoing methods   

    public function outgoingIndex(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // جلب الطرود المرسلة من فرع المستخدم
        $shipments = Shipment::with(['receiverBranch.app', 'receiverOfficeBranch.office', 'receiverCustomer', 'senderCustomer'])
            ->where('sender_branch_id', $user->branch_id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(5);
        $shipments->getCollection()->transform(function ($shipment) {

            // إذا أردت رابط المرسل دائماً (مثلاً في صفحة الصادر)
            $shipment->sender_whatsapp_link = WhatsAppLinkService::generate($shipment, 'sender');

            // إذا أردت تجهيز رابط المستلم أيضاً (مفيد في صفحة الوارد)
            $shipment->receiver_whatsapp_link = WhatsAppLinkService::generate($shipment, 'receiver');
            return $shipment;
        });

        if ($request->isMobile) {
            return view('mobile.pages.shipment.outgoing.index', compact('shipments'));
        }

        return view('pages.shipment.outgoing.index', compact('shipments'));
    }
    public function outgoingCreate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $currentApp = $user->app;

        // --- 1. جلب المكاتب الموثوقة (Apps المتصلة) ---
        $connectedAppIds = collect();
        $sentAccepted = $currentApp->sentConnections()->where('status', 'accepted')->pluck('receiver_app_id');
        $receivedAccepted = $currentApp->receivedConnections()->where('status', 'accepted')->pluck('sender_app_id');
        $connectedAppIds = $connectedAppIds->merge($sentAccepted)->merge($receivedAccepted)->unique();

        $trustedApps = App::whereIn('id', $connectedAppIds)->with(['branches' => function ($query) {
            $query->withoutGlobalScope('app_id');
        }])->get();

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
        $customers = Customer::get(['id', 'name', 'phone']);

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
    public function outgoingStore(Request $request)
    {
        $rules = [
            'office_id'            => 'required|string',
            'receiver_branch_id'   => 'required|integer',

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
                'created_by'                => $user->id,

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
                    'shipment.outgoing.index'
                );
            }

            return WebResponseClass::sendResponse(
                'تم إضافة الطرد!',
                'تم إنشاء الشحنة بنجاح.',
                'حسناً',
                'shipment.outgoing.index'
            );
        } catch (\Exception $e) {

            DB::rollBack();
            dd($e->getMessage());
            return WebResponseClass::sendExceptionError($e);
        }
    }
    public function outgoingShow(Request $request, $id)
    {
        $shipment = Shipment::with(['senderCustomer', 'receiverCustomer', 'senderBranch', 'receiverBranch'])->findOrFail($id);

        // تجهيز روابط الواتساب
        $shipment->sender_whatsapp_link = WhatsAppLinkService::generate($shipment, 'sender');
        $shipment->receiver_whatsapp_link = WhatsAppLinkService::generate($shipment, 'receiver');


        // التحقق من نوع الجهاز لإرجاع العرض (View) المناسب
        if ($request->isMobile) {
            return view('mobile.pages.shipment.outgoing.show', compact('shipment'));
        }

        return view('pages.shipment.outgoing.show', compact('shipment'));
    }
    public function outgoingEdit(Request $request, $id){
    $shipment = Shipment::with([
        'senderCustomer',
        'receiverCustomer',
        'receiverBranch'
    ])->findOrFail($id);

    if (auth()->user()->type !== 'admin' && $shipment->status !== 'pending') {
        return back()->with('error', 'لا يمكن تعديل هذا الطرد لأن حالته الحالية لا تسمح بذلك.');
    }

    $user = auth()->user();

    $customers = Customer::where('app_id', $user->app_id)
        ->get(['id', 'name', 'phone']);

    $branches = Branch::where('app_id', $user->app_id)->get();

    $currentApp = $user->app;

    $sentAccepted = $currentApp->sentConnections()
        ->where('status', 'accepted')
        ->pluck('receiver_app_id');

    $receivedAccepted = $currentApp->receivedConnections()
        ->where('status', 'accepted')
        ->pluck('sender_app_id');

    $connectedAppIds = collect()
        ->merge($sentAccepted)
        ->merge($receivedAccepted)
        ->unique();

    $trustedApps = App::whereIn('id', $connectedAppIds)
        ->with(['branches' => function ($query) {
            $query->withoutGlobalScope('app_id');
        }])
        ->get();

    $untrustedOffices = Office::where('app_id', $currentApp->id)
        ->with('branches')
        ->get();

    $offices = collect();

    $myBranches = Branch::where('app_id', $currentApp->id)
        ->where('id', '!=', $user->branch_id)
        ->get();

    if ($myBranches->isNotEmpty()) {
        $offices->push([
            'id'       => 'internal_' . $currentApp->id,
            'name'     => '🏠 مكتبنا الحالي',
            'branches' => $myBranches,
        ]);
    }

    foreach ($trustedApps as $tApp) {
        if ($tApp->branches->isNotEmpty()) {
            $offices->push([
                'id'       => 'trusted_' . $tApp->id,
                'name'     => $tApp->name,
                'branches' => $tApp->branches,
            ]);
        }
    }

    foreach ($untrustedOffices as $uOffice) {
        if ($uOffice->branches->isNotEmpty()) {
            $offices->push([
                'id'       => 'untrusted_' . $uOffice->id,
                'name'     => $uOffice->name,
                'branches' => $uOffice->branches,
            ]);
        }
    }

    // مهم: إذا الشحنة خارجية نعتمد receiver_office_branch_id أولاً
    $initialBranchId = old(
        'receiver_branch_id',
        $shipment->receiver_office_branch_id ?: $shipment->receiver_branch_id
    );

    $initialOfficeId = old('office_id', '');

    if (!$initialOfficeId && $initialBranchId) {
        foreach ($offices as $office) {
            $officeId = (string) $office['id'];

            if ($shipment->receiver_office_branch_id && !str_starts_with($officeId, 'untrusted_')) {
                continue;
            }

            if (!$shipment->receiver_office_branch_id && str_starts_with($officeId, 'untrusted_')) {
                continue;
            }

            foreach ($office['branches'] as $branch) {
                if ((int) $branch->id === (int) $initialBranchId) {
                    $initialOfficeId = $office['id'];
                    break 2;
                }
            }
        }
    }

    if ($request->isMobile) {
        return view('mobile.pages.shipment.outgoing.edit', compact(
            'shipment',
            'customers',
            'offices',
            'branches',
            'initialOfficeId'
        ));
    }

    return view('pages.shipment.outgoing.edit', compact(
        'shipment',
        'customers',
        'offices',
        'branches',
        'initialOfficeId'
    ));
    }
    public function outgoingUpdate(Request $request, $id){
        $shipment = Shipment::findOrFail($id);

        if (auth()->user()->type !== 'admin' && $shipment->status !== 'pending') {
            return back()->with('error', 'لا تملك صلاحية تعديل هذا الطرد.');
        }

        $rules = [
        'office_id'             => 'required|string',
        'receiver_branch_id'    => 'required|integer|exists:branches,id',

        'sender_customer_id'    => 'nullable|exists:customers,id',
        'sender_name'           => 'required_without:sender_customer_id|string|max:255',
        'sender_phone'          => 'required_without:sender_customer_id|string|max:50',

        'receiver_customer_id'  => 'nullable|exists:customers,id',
        'receiver_name'         => 'required_without:receiver_customer_id|string|max:255',
        'receiver_phone'        => 'required_without:receiver_customer_id|string|max:50',

        'package_type'          => 'required|string',
        'weight'                => 'nullable|numeric|min:0',
        'no_gallons_honey'      => 'nullable|numeric|min:0',
        'no_honey_jars'         => 'nullable|numeric|min:0',

        'payment_method'        => 'required|in:prepaid,cod,partial_payment,customer_credit',
        'total_amount'          => 'required|numeric|min:0',
        'partial_amount'        => 'required_if:payment_method,partial_payment|nullable|numeric|min:0',
        'notes'                 => 'nullable|string',
    ];

    $validator = Validator::make($request->all(), $rules);

    $validator->after(function ($validator) use ($request) {
        if ($request->payment_method === 'partial_payment') {
            $total = (float) $request->total_amount;
            $partial = (float) $request->partial_amount;

            if ($partial >= $total) {
                $validator->errors()->add('partial_amount', 'المبلغ المدفوع جزئياً يجب أن يكون أقل من الإجمالي.');
            }
        }

        if (
            !str_starts_with((string) $request->office_id, 'untrusted_') &&
            (int) $request->receiver_branch_id === (int) auth()->user()->branch_id
        ) {
            $validator->errors()->add('receiver_branch_id', 'لا يمكن اختيار نفس فرع الإرسال كفرع استلام.');
        }
    });

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput()
            ->with('error', 'يرجى مراجعة الحقول المدخلة والتأكد من صحتها.');
    }

    try {
        DB::beginTransaction();

        $data = $validator->validated();
        $user = auth()->user();

        $senderCustomerId = $data['sender_customer_id'] ?? null;

        if (empty($senderCustomerId) && !empty($data['sender_phone'])) {
            $senderCustomer = Customer::firstOrCreate(
                ['phone' => $data['sender_phone'], 'app_id' => $user->app_id],
                [
                    'name'       => $data['sender_name'],
                    'branch_id'  => $user->branch_id,
                    'created_by' => $user->id,
                ]
            );

            if (!empty($data['sender_name']) && $senderCustomer->name !== $data['sender_name']) {
                $senderCustomer->update(['name' => $data['sender_name']]);
            }

            $senderCustomerId = $senderCustomer->id;
        }

        $receiverCustomerId = $data['receiver_customer_id'] ?? null;

        if (empty($receiverCustomerId) && !empty($data['receiver_phone'])) {
            $receiverCustomer = Customer::firstOrCreate(
                ['phone' => $data['receiver_phone'], 'app_id' => $user->app_id],
                [
                    'name'       => $data['receiver_name'],
                    'branch_id'  => $user->branch_id,
                    'created_by' => $user->id,
                ]
            );

            if (!empty($data['receiver_name']) && $receiverCustomer->name !== $data['receiver_name']) {
                $receiverCustomer->update(['name' => $data['receiver_name']]);
            }

            $receiverCustomerId = $receiverCustomer->id;
        }

        $officeId = (string) $request->office_id;

        $isUntrusted = str_starts_with($officeId, 'untrusted_');

        $shipment->update([
            'receiver_branch_id'        => $isUntrusted ? null : $data['receiver_branch_id'],
            'receiver_office_branch_id' => $isUntrusted ? $data['receiver_branch_id'] : null,

            'sender_customer_id'        => $senderCustomerId,
            'receiver_customer_id'      => $receiverCustomerId,

            'package_type'              => $data['package_type'],
            'weight'                    => $data['weight'] ?? 0,
            'no_gallons_honey'          => $data['no_gallons_honey'] ?? 0,
            'no_honey_jars'             => $data['no_honey_jars'] ?? 0,

            'payment_method'            => $data['payment_method'],
            'total_amount'              => $data['total_amount'],
            'partial_amount'            => $data['payment_method'] === 'partial_payment'
                ? ($data['partial_amount'] ?? 0)
                : 0,

            'notes'                     => $data['notes'] ?? null,

            'customer_debt_status'      => $data['payment_method'] === 'customer_credit'
                ? ($shipment->customer_debt_status ?? 'pending')
                : null,
        ]);

        DB::commit();

        return redirect()
            ->route('shipment.outgoing.index')
            ->with('success', 'تم تعديل بيانات الطرد بنجاح.');
    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', 'حدث خطأ أثناء التعديل: ' . $e->getMessage());
    }
    }

    // End Outgoing methods 
    // ==================================================================================
    
    // معتمد
    
    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branches = Branch::where('id', '!=', $user->branch_id)->get();
        // $drivers = Driver::where('status', 'active')->get();
        $customers = Customer::all();

        return view('pages.shipment.outgoing.edit', compact('shipment', 'branches', 'customers'));
    }
    // ===========================================================================
    // Start incoming methods
    public function incomingIndex(Request $request)
    {
        $user = auth()->user();
        
        $shipments = Shipment::with(['senderBranch', 'senderOfficeBranch', 'senderCustomer', 'receiverCustomer'])
            ->where('receiver_branch_id', $user->branch_id)
            
            // 💡 الإضافة السحرية: الفلترة حسب الحالة القادمة من الرابط
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            
            ->latest()
            ->paginate(10)
            ->withQueryString(); // 💡 مهم جداً: للاحتفاظ بالفلتر عند الانتقال للصفحة 2 و 3

        if ($request->isMobile) {
            return view('mobile.pages.shipment.incoming.index', compact('shipments'));
        }

        return view('pages.shipment.incoming.index', compact('shipments'));
    }
    public function incomingShow(Request $request, $id)
    {
        $user = auth()->user();

        // جلب الطرد مع العلاقات المطلوبة لعرض التفاصيل
        $shipment = Shipment::with([
            'senderBranch', 
            'senderOfficeBranch', 
            'senderCustomer', 
            'receiverCustomer',
            'package' // جلب الإرسالية المجمعة (الرحلة) التابع لها الطرد إن وجدت
        ])
        // 💡 حماية أمنية (Authorization): التأكد أن الطرد فعلاً وارد إلى فرع المستخدم الحالي
        ->where('receiver_branch_id', $user->branch_id)
        ->findOrFail($id);

        // توجيه المستخدم حسب نوع الجهاز
        if ($request->isMobile) {
            return view('mobile.pages.shipment.incoming.show', compact('shipment'));
        }

        // واجهة الكمبيوتر (الدسكتوب)
        return view('pages.shipment.incoming.show', compact('shipment'));
    }

    // end incoming methods
    // ===========================================================================

    /* ========== 6- تحديث الطرد ========== */
    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        // 1. منع تعديل الشحنة الملغية
        if ($shipment->status === 'cancelled') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'عذراً، لا يمكن تعديل شحنة ملغية.'], 403);
            }

            return WebResponseClass::sendError('عذراً، لا يمكن تعديل شحنة ملغية.', 'خطأ!', 'حسناً');
        }

        // يحدد أي جزء نحدثه
        $section = $request->input('section', 'all');

        // ==========================================
        // القسم الأول: تحديث المرسل والمستلم
        // ==========================================
        if ($section === 'sender_receiver') {
            $rules = [
                'receiver_branch_id'   => 'required|integer|exists:branches,id',
                'sender_customer_id'   => 'nullable|exists:customers,id',
                'receiver_customer_id' => 'nullable|exists:customers,id',
                'sender_name'          => 'required_without:sender_customer_id|string|max:255',
                'sender_phone'         => 'required_without:sender_customer_id|string|max:50',
                'receiver_name'        => 'required_without:receiver_customer_id|string|max:255',
                'receiver_phone'       => 'required_without:receiver_customer_id|string|max:50',
                'no_honey_jars'        => 'nullable|numeric|min:0',
                'no_gallons_honey'     => 'nullable|numeric|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            // التحقق من أن فرع الإرسال != فرع الاستقبال
            $validator->after(function ($validator) use ($request, $shipment) {
                /** @var \App\Models\User $user */
                $user = auth()->user();

                $sender = (int) $user->branch_id;
                $receiver = (int) ($request->receiver_branch_id ?? $shipment->receiver_branch_id);

                if ($sender && $receiver && $sender === $receiver) {
                    $validator->errors()->add('receiver_branch_id', 'لا يمكن اختيار نفس جهة الإرسال.');
                }
            });

            // إرجاع الأخطاء كـ JSON إذا كان الطلب AJAX
            if ($validator->fails()) {
                if ($request->wantsJson()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                return WebResponseClass::sendValidationError($validator);
            }

            $data = $validator->validated();
            $user = auth()->user();
            // إنشاء / تحديث العميل المرسل
            if (empty($data['sender_customer_id'])) {
                $senderCustomer = Customer::where('phone', $data['sender_phone'])->first();

                if ($senderCustomer) {
                    $senderCustomer->update([
                        'name' => $data['sender_name'],
                    ]);
                } else {
                    $senderCustomer = Customer::create([
                        'phone'      => $data['sender_phone'],
                        'name'       => $data['sender_name'],
                        'branch_id'  => $user->branch_id,
                        'app_id'     => $user->app_id,
                        'created_by' => $user->id,
                    ]);
                }

                $data['sender_customer_id'] = $senderCustomer->id;
            }

            // إنشاء / تحديث العميل المستلم
            if (empty($data['receiver_customer_id'])) {
                $receiverCustomer = Customer::where('phone', $data['receiver_phone'])->first();

                if ($receiverCustomer) {
                    $receiverCustomer->update([
                        'name' => $data['receiver_name'],
                    ]);
                } else {
                    $receiverCustomer = Customer::create([
                        'phone'      => $data['receiver_phone'],
                        'name'       => $data['receiver_name'],
                        'branch_id'  => $data['receiver_branch_id'],
                        'app_id'     => $user->app_id,
                        'created_by' => $user->id,
                    ]);
                }

                $data['receiver_customer_id'] = $receiverCustomer->id;
            }

            $data['sender_branch_id'] = auth()->user()->branch_id;

            $shipment->update($data);

            // استجابة النجاح (AJAX)
            if ($request->wantsJson()) {
                return WebResponseClass::sendResponse(
                    'تم التحديث!',
                    'تم تحديث بيانات المرسل والمستلم بنجاح.',
                    'حسناً',
                    'shipment.outgoing.index'
                );
            }

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيانات المرسل والمستلم بنجاح.',
                'حسناً',
                'shipment.outgoing.index'
            );
        }

        // ==========================================
        // القسم الثاني: تحديث تفاصيل الطرد
        // ==========================================
        if ($section === 'details') {
            $rules = [
                'code'         => 'nullable|string|max:255',
                'package_type' => 'nullable|string|max:255',
                'weight'       => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'status'       => 'required|in:pending,in_transit,delivered',
                'notes'        => 'nullable|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                if ($request->wantsJson()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                return WebResponseClass::sendValidationError($validator);
            }

            $data = $validator->validated();
            $shipment->update($data);

            // استجابة النجاح (AJAX)
            if ($request->wantsJson()) {
                return WebResponseClass::sendResponse(
                    'تم التحديث!',
                    'تم تحديث تفاصيل الطرد بنجاح.',
                    'حسناً',
                    'shipment.outgoing.index'
                );
            }

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث تفاصيل الطرد بنجاح.',
                'حسناً',
                'shipment.outgoing.index'
            );
        }

        // ==========================================
        // القسم الثالث: تحديث طريقة الدفع
        // ==========================================
        if ($section === 'payment') {
            $rules = [
                'payment_method'         => 'required|in:prepaid,cod,partial_payment,customer_credit',
                'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',
                'prepaid_reference'      => 'required_if:prepaid_payment_method,bank_transfer|nullable|string|max:255',
                'partial_amount'         => 'required_if:payment_method,partial_payment|numeric|min:0.01',
                'customer_debt_status'   => 'nullable|in:pending,partially_paid,fully_paid,overdue',
            ];

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($request, $shipment) {
                if (($request->payment_method ?? $shipment->payment_method) === 'partial_payment') {
                    $totalAmount = $request->total_amount ?? $shipment->total_amount;
                    $partialAmount = $request->partial_amount;

                    if (!is_null($partialAmount) && is_numeric($partialAmount) && is_numeric($totalAmount)) {
                        if ((float) $partialAmount >= (float) $totalAmount) {
                            $validator->errors()->add('partial_amount', 'المبلغ المدفوع جزئيًا يجب أن يكون أقل من المبلغ الإجمالي.');
                        }
                    }
                }
            });

            if ($validator->fails()) {
                if ($request->wantsJson()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                return WebResponseClass::sendValidationError($validator);
            }

            $data = $validator->validated();

            if (($data['payment_method'] ?? null) === 'customer_credit') {
                $data['customer_debt_status'] = $data['customer_debt_status'] ?? 'pending';
            } else {
                $data['customer_debt_status'] = null;
            }

            $partialAmount = $data['partial_amount'] ?? null;
            unset($data['prepaid_payment_method']);

            $shipment->update($data);

            if (in_array($shipment->payment_method, ['cod', 'customer_credit'])) {
                $shipment->payments()->delete();

                if ($request->wantsJson()) {
                    session()->flash('success', true);
                    session()->flash('success_title', 'تم التحديث!');
                    session()->flash('success_message', 'تم تحديث بيانات الدفع بنجاح.');

                    return response()->json(['success' => true]);
                }

                return WebResponseClass::sendResponse(
                    'تم التحديث!',
                    'تم تحديث بيانات الدفع بنجاح.',
                    'حسناً',
                    'shipment.outgoing.index'
                );
            }

            $paymentType = $request->prepaid_payment_method ?? 'cash';
            $paidAmount = null;

            if ($shipment->payment_method === 'partial_payment') {
                $paidAmount = $partialAmount ? (float) $partialAmount : null;
            } elseif ($shipment->payment_method === 'prepaid') {
                $paidAmount = (float) $shipment->total_amount;
            }

            if (isset($this->shipmentPaymentService)) {
                $this->shipmentPaymentService->handlePaymentForNewShipment(
                    $shipment,
                    $paymentType,
                    $paidAmount,
                    $request->prepaid_reference
                );
            }

            if ($request->wantsJson()) {
                session()->flash('success', true);
                session()->flash('success_title', 'تم التحديث!');
                session()->flash('success_message', 'تم تحديث بيانات الدفع بنجاح.');

                return response()->json(['success' => true]);
            }

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيانات الدفع بنجاح.',
                'حسناً',
                'shipment.outgoing.index'
            );
        }

        // إذا لم يتطابق أي قسم
        if ($request->wantsJson()) {
            return response()->json(['message' => 'قسم التحديث غير معروف.'], 400);
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
        $isDayClosed = CashRegisterClosing::where('branch_id', $shipment->sender_branch_id) // أو sender_branch_id حسب منطقك
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
        $customers = Customer::where('branch_id', $user->branch_id)->get();

        return view('pages.shipment.select-customer', compact('customers'));
    }

   public function updateStatus(Request $request, $id)
{
    // 1. Validation
    $request->validate([
        'status' => 'required|string|in:pending,in_transit,received_at_branch,out_for_delivery,delivered,cancelled,returned',
    ]);

    try {
        DB::beginTransaction();

        $shipment = Shipment::findOrFail($id);
        $oldStatus = $shipment->status;
        $newStatus = $request->status;

        // ========================================================
        // 2. الحماية البرمجية المحدثة (Backend State Validation) 🛡️
        // ========================================================
        $validTransitions = [
            'pending'            => ['in_transit', 'cancelled', 'returned'], 
            'in_transit'         => ['received_at_branch', 'delivered', 'returned'], 
            'received_at_branch' => ['out_for_delivery', 'delivered', 'returned'], 
            'out_for_delivery'   => ['delivered', 'returned'], 
        ];

        if (!isset($validTransitions[$oldStatus]) || !in_array($newStatus, $validTransitions[$oldStatus])) {
            return back()->with('error', 'عفواً، لا يمكن تحويل الطرد من حالة (' . $oldStatus . ') إلى (' . $newStatus . ')');
        }

        // 💡 تم إزالة شرط "المنع" الذي كان يطلب من الموظف فك الارتباط يدوياً.

        // ========================================================
        // 3. تحديث الحالة (مع نظام المرتجعات الذكي 🔄)
        // ========================================================
        if ($newStatus === 'returned') {
            
            if (!$shipment->is_returned) {
                // 🔴 المرحلة الأولى: (المستلم يرفض الطرد) -> تبدأ رحلة العودة
                $packageId = $shipment->shipment_package_id;

                $shipment->update([
                    'is_returned'         => true,      
                    'status'              => 'pending', // يعود قيد التجهيز ليركب شاحنة العودة
                    'shipment_package_id' => null,      // فك ارتباطه فوراً 
                ]);

                if ($packageId) {
                    $activeShipmentsCount = Shipment::where('shipment_package_id', $packageId)
                        ->whereNotIn('status', ['delivered', 'cancelled'])->count();

                    if ($activeShipmentsCount === 0) {
                        ShipmentPackage::where('id', $packageId)->update(['status' => 'delivered']);
                    }
                }
                
                $newStatus = 'pending'; // لتشغيل الإشعارات كطرد قيد التجهيز

            } else {
                // 🟢 المرحلة النهائية: (المُرسل يسلم الطرد للتاجر) -> إغلاق الطرد نهائياً كـ (مرتجع)
                $shipment->update([
                    'status' => 'returned'
                ]);
            }
        } else {
            // التحديث الطبيعي لأي حالة أخرى
            $shipment->update([
                'status' => $newStatus
            ]);
        }

        // ========================================================
        // 4. إشعار الإدارة 
        // ========================================================
        $user = auth()->user();
        $admins = User::where('app_id', $user->app_id)->where('type', 'admin')->get();

        if ($admins->isNotEmpty()) {
            $statusNamesAr = [
                'pending'            => 'قيد التجهيز',
                'in_transit'         => 'قيد النقل',
                'received_at_branch' => 'وصل المستودع',
                'out_for_delivery'   => 'خرج للتوصيل',
                'delivered'          => 'تم التسليم',
                'cancelled'          => 'ملغي',
            ];
            
            // إذا كان مرتجعاً وهو الآن pending، نغير النص ليكون مفهوماً للمدير
            $statusText = ($shipment->is_returned && $newStatus === 'pending') 
                            ? 'تم إرجاع الطرد (عاد للمستودع)' 
                            : ($statusNamesAr[$newStatus] ?? $newStatus);

            Notification::send(
                $admins,
                new \App\Notifications\AdminShipmentStatusUpdated(
                    $user->name,
                    $shipment->bond_number,
                    $statusText,
                    $shipment->id
                )
            );
        }

        // ========================================================
        // 5. الإجراءات الجانبية 
        // ========================================================
        if ($shipment->shipment_package_id && in_array($newStatus, ['received_at_branch', 'delivered'])) {
            $packageId = $shipment->shipment_package_id;
            $package = ShipmentPackage::find($packageId);

            if ($package && $package->status === 'in_transit') {
                $senderBranchUsers = User::where('branch_id', $shipment->sender_branch_id)->get();
                if ($senderBranchUsers->isNotEmpty()) {
                    Notification::send(
                        $senderBranchUsers,
                        new \App\Notifications\PackageReceivedNotification(
                            $package->tracking_number, 
                            auth()->user()->branch->name ?? 'الفرع المستلم',
                            $shipment->tracking_number,
                            $shipment->id
                        )
                    );
                }

                $remainingShipments = Shipment::where('shipment_package_id', $packageId)
                    ->whereIn('status', ['pending', 'in_transit'])
                    ->count();

                if ($remainingShipments === 0) {
                    $package->update(['status' => 'delivered']);
                }
            }
        }

        if ($newStatus === 'delivered') {
            if (in_array($shipment->payment_method, ['cod', 'partial_payment'])) {
                // $this->shipmentPaymentService->createCodBranchTransactionOnDelivery($shipment);
            }
        }

        DB::commit();

        $successMessages = [
            'in_transit'         => 'تم تحريك الطرد وبدء الرحلة 🚚',
            'received_at_branch' => 'تم استلام الطرد بالمستودع بنجاح 📦',
            'out_for_delivery'   => 'الطرد الآن مع المندوب للتوصيل 🛵',
            'delivered'          => 'تم التسليم بنجاح ✅',
            'cancelled'          => 'تم إلغاء الطرد 🚫',
            'pending'            => $shipment->is_returned ? 'تم تسجيل الطرد كمرتجع وهو الآن قيد التجهيز للعودة ❌' : 'تم التحديث',
        ];

        return back()->with([
            'success_title' => 'تم التحديث!',
            'success_message' => $successMessages[$newStatus] ?? 'تم تحديث الحالة بنجاح'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage());
    }
}

    /**
     * دالة مساعدة لإرسال إشعار تحرك الطرد للفرع المستلم (إذا كان مسجلاً بالنظام)
     */
    protected function sendDispatchNotification($shipment)
    {
        // 1. الحماية: إذا كان الطرد متجهاً لمكتب خارجي يدوي (غير موثوق)، نوقف العملية فوراً
        if (empty($shipment->receiver_branch_id)) {
            return;
        }

        $user = auth()->user();

        // 2. جلب الفرع المستلم مع الموظفين التابعين له
        // استخدمنا \App\Models\Branch لتجنب مشاكل الـ namespace في الأعلى
        $receiverBranch = Branch::with('users')->find($shipment->receiver_branch_id);

        if ($receiverBranch && $receiverBranch->users->isNotEmpty()) {
            $isInternal = ($receiverBranch->app_id == $user->app_id);
            $senderName = $isInternal ? ($user->branch?->name ?? 'فرع غير محدد') : ($user->App?->name ?? 'مكتب شحن');
            Notification::send(
                $receiverBranch->users,
                new NewShipmentNotification(
                    $senderName,
                    $shipment->bond_number,
                    $shipment->id,
                    $isInternal
                )
            );
        }
    }
}
