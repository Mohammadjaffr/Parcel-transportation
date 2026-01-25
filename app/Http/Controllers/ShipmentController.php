<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\Models\AdminActivity;
use App\Classes\WebResponseClass;
use Illuminate\Support\Facades\DB;
use App\Models\CashRegisterClosing;
use App\Services\AdminLoggerService;
use Illuminate\Support\Facades\Auth;
use App\Services\ShipmentPaymentService;
use Illuminate\Support\Facades\Validator;

class ShipmentController extends Controller
{
    protected $shipmentPaymentService;

    public function __construct(ShipmentPaymentService $shipmentPaymentService)
    {
        $this->shipmentPaymentService = $shipmentPaymentService;
    }

    /* ========== 1- عرض جميع الطردات ========== */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branchCode = $user->branch_code;
        $type = $request->query('type', 'outgoing');

        $query = Shipment::query();

        if ($type === 'incoming') {
            $query->where('receiver_branch_code', $branchCode);
        } else {
            $query->where('sender_branch_code', $branchCode);
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        return view('pages.shipment.index', compact('requests', 'type'));
    }

    /* ========== 2- صفحة إنشاء طرد ========== */
    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branches = Branch::where('code', '!=', $user->branch_code)->get();
        $customers = Customer::where('branch_code', $user->branch_code)->get();

        $customer = null;
        $role = $request->query('role'); // sender | receiver

        if ($request->filled('customer_id')) {
            $customer = Customer::findOrFail($request->customer_id);
        }

        return view('pages.shipment.create', compact(
            'branches',
            'customers',
            'customer',
            'role'
        ));
    }

    public function store(Request $request)
    {
        $entryType = $request->input('entry_type', 'sender'); // sender أو receiver

        $rules = [
            'sender_customer_id' => 'nullable|exists:customers,id',
            'receiver_customer_id' => 'nullable|exists:customers,id',

            'sender_name' => 'required_without:sender_customer_id|string|max:255',
            'sender_phone' => 'required_without:sender_customer_id|string|max:50',

            'receiver_name' => 'required_without:receiver_customer_id|string|max:255',
            'receiver_phone' => 'required_without:receiver_customer_id|string|max:50',

            'package_type' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',

            'code' => 'nullable|string|max:255',
            'no_honey_jars' => 'nullable|numeric|min:0',
            'no_gallons_honey' => 'nullable|numeric|min:0',

            'payment_method' => 'required|in:prepaid,cod,partial_payment,customer_credit',

            'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',
            'prepaid_reference' => 'required_if:prepaid_payment_method,bank_transfer|max:255',

            'partial_amount' => 'required_if:payment_method,partial_payment|numeric|min:0.01',

            'customer_debt_status' => 'nullable|in:pending,partially_paid,fully_paid,overdue',
            'notes' => 'nullable|string',
        ];

        if ($entryType === 'sender') {
            $rules['receiver_branch_code'] = 'required|exists:branches,code';
        } else {
            $rules['sender_branch_code'] = 'required|exists:branches,code';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();

            /** @var \App\Models\User $user */
            $user = auth()->user();
            $currentBranchCode = $user->branch_code;

            if ($entryType === 'sender') {
                $data['sender_branch_code'] = $currentBranchCode;
            } else {
                $data['receiver_branch_code'] = $currentBranchCode;
            }

            $data['created_branch_code'] = $currentBranchCode;

            /* ================= إنشاء / ربط العملاء ================= */

            // المرسل
            if (empty($data['sender_customer_id'])) {
                $senderCustomer = Customer::create([
                    'phone' => $data['sender_phone'],
                    'branch_code' => Auth::user()->branch_code,
                    'name' => $data['sender_name'],
                ]);

                $data['sender_customer_id'] = $senderCustomer->id;
            }

            // المستلم
            if (empty($data['receiver_customer_id'])) {
                $receiverCustomer = Customer::create(
                    [
                        'phone' => $data['receiver_phone'],
                        'branch_code' => Auth::user()->branch_code,
                        'name' => $data['receiver_name'],
                    ]
                );

                $data['receiver_customer_id'] = $receiverCustomer->id;
            }

            /* ================= حالات إضافية ================= */

            // حالة مديونية العميل
            if ($data['payment_method'] === 'customer_credit') {
                $data['customer_debt_status'] = $data['customer_debt_status'] ?? 'pending';
            } else {
                $data['customer_debt_status'] = null;
            }

            // $data['status'] = 'pending';
            if ($data['payment_method'] === 'prepaid') {
                $data['status'] = 'pending';
            } else {
                $data['status'] = 'pending';
            }

            // حفظ مبلغ الدفع الجزئي مؤقتاً قبل الحذف
            $partialAmount = $data['partial_amount'] ?? null;

            // هذه القيم لا نريد تخزينها في جدول الشحنات
            unset(
                $data['sender_name'],
                $data['sender_phone'],
                $data['receiver_name'],
                $data['receiver_phone']
            );

            /* ================= إنشاء الشحنة ================= */

            $shipment = Shipment::create($data);

            /* ================= معالجة الدفع ================= */

            $paymentType = $request->prepaid_payment_method ?? 'cash';
            $paidAmount = null;
            // $attachment = $request->file('prepaid_attachment');

            if ($shipment->payment_method === 'partial_payment') {
                $paidAmount = $partialAmount ? (float) $partialAmount : null;
            } elseif ($shipment->payment_method === 'prepaid') {
                $paidAmount = (float) $shipment->total_amount;
            } else {
                // COD أو customer_credit → لا يوجد مبلغ مدفوع الآن
                $paidAmount = null;
            }

            $this->shipmentPaymentService->handlePaymentForNewShipment(
                $shipment,
                $paymentType,
                $paidAmount,
                $request->prepaid_reference
            );

            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم إنشاء الطرد بنجاح.',
                'حسناً',
                'shipment.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function createCustomer()
    {
        return view('pages.shipment.customer.create');
    }

    public function storeCustomer(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:sender,receiver',
        ]);

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
            return response()->json([
                'success' => true,
                'success_title' => 'تم التحديث!',
                'success_message' => 'تم تحديث حالة الطرد بنجاح.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error_message' => $e->getMessage(),
            ], 500);
        }
    }
}
