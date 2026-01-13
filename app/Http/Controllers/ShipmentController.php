<?php

namespace App\Http\Controllers;

use App\Models\AdminActivity;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Shipment;
use App\Services\AdminLoggerService;
use App\Services\ShipmentPaymentService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TCPDF;
use App\Classes\WebResponseClass;

class ShipmentController extends Controller
{
    protected $whatsAppService;
    protected $shipmentPaymentService;

    public function __construct(WhatsAppService $whatsAppService, ShipmentPaymentService $shipmentPaymentService)
    {
        $this->whatsAppService = $whatsAppService;
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
            // Default: Outgoing (Sent FROM this branch, regardless of who created it)
            $query->where('sender_branch_code', $branchCode);
        }

        $requests = $query->latest()->paginate(10);

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
    'sender_customer_id'   => 'nullable|exists:customers,id',
    'receiver_customer_id' => 'nullable|exists:customers,id',

    'sender_name'   => 'required_without:sender_customer_id|string|max:255',
    'sender_phone'  => 'required_without:sender_customer_id|string|max:50',

    'receiver_name'  => 'required_without:receiver_customer_id|string|max:255',
    'receiver_phone' => 'required_without:receiver_customer_id|string|max:50',

    'package_type' => 'required|string|max:255',
    'weight'       => 'nullable|numeric|min:0',
    'total_amount' => 'required|numeric|min:0',

    'code'              => 'nullable|string|max:255',
    'no_honey_jars'     => 'nullable|numeric|min:0',
    'no_gallons_honey'  => 'nullable|numeric|min:0',

    'payment_method' => 'required|in:prepaid,cod,partial_payment,customer_credit',

    'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',
    'prepaid_reference'      => 'required_if:prepaid_payment_method,bank_transfer|max:255',

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
                $senderCustomer = Customer::firstOrCreate(
                    [
                        'phone' => $data['sender_phone'],
                        'branch_code' => $data['sender_branch_code']
                    ],
                    [
                        'name' => $data['sender_name']
                    ]
                );

                $data['sender_customer_id'] = $senderCustomer->id;
            }

            // المستلم
            if (empty($data['receiver_customer_id'])) {
                $receiverCustomer = Customer::firstOrCreate(
                    [
                        'phone' => $data['receiver_phone'],
                        'branch_code' => $data['receiver_branch_code']
                    ],
                    [
                        'name' => $data['receiver_name']
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
        $shipment = Shipment::findOrFail($id);
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

        $rules = [];
        $data = [];

        if ($section === 'sender_receiver') {

            $rules = [
                'receiver_branch_code' => 'required|exists:branches,code',

                'sender_customer_id' => 'nullable|exists:customers,id',
                'receiver_customer_id' => 'nullable|exists:customers,id',

                'sender_name' => 'required_without:sender_customer_id|string|max:255',
                'sender_phone' => 'required_without:sender_customer_id|string|max:50',

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
                $sender = $user->branch_code;                        // من المستخدم
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
                'تمت التحديث!',
                'تم تحديث بيانات المرسل والمستلم بنجاح.',
                'حسناً',
                'shipment.index'
            );
        } elseif ($section === 'details') {

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
                'تمت التحديث!',
                'تم تحديث تفاصيل الطرد بنجاح.',
                'حسناً',
                'shipment.index'
            );
        } elseif ($section === 'payment') {

            $rules = [
                'payment_method' => 'required|in:prepaid,cod,partial_payment,customer_credit',
                'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',
                'prepaid_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
                'prepaid_reference' => 'nullable|string|max:255',
                'partial_amount' => 'required_if:payment_method,partial_payment|numeric|min:0.01',
                'customer_debt_status' => 'nullable|in:pending,partially_paid,fully_paid,overdue',
            ];

            $validator = Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($request, $shipment) {

                $paymentMethod = $request->payment_method ?? $shipment->payment_method;

                if (
                    in_array($paymentMethod, ['prepaid', 'partial_payment']) &&
                    $request->prepaid_payment_method === 'bank_transfer' &&
                    ! $request->hasFile('prepaid_attachment')
                ) {
                    $validator->errors()->add(
                        'prepaid_attachment',
                        'في حالة التحويل البنكي، يجب إرفاق سند الدفع.'
                    );
                }

                if ($paymentMethod === 'partial_payment') {

                    $totalAmount = $request->total_amount ?? $shipment->total_amount;
                    $partialAmount = $request->partial_amount;

                    if (! is_null($partialAmount) && is_numeric($partialAmount) && is_numeric($totalAmount)) {
                        $partial = (float) $partialAmount;
                        $total = (float) $totalAmount;

                        if ($partial >= $total) {
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
                $data['customer_debt_status'] = $data['customer_debt_status'] ?? null;
            }

            // نأخذ نسخة قبل الحذف
            $partialAmount = $data['partial_amount'] ?? null;

            // هذه الحقول لا تدخل جدول الشحنات
            unset(
                $data['prepaid_payment_method'],
                $data['prepaid_attachment'],
            );

            $shipment->update($data);

            // معالجة الدفع
            $paymentType = $request->prepaid_payment_method ?? 'cash';
            $paidAmount = null;
            $attachment = $request->file('prepaid_attachment');

            if ($shipment->payment_method === 'partial_payment') {
                $paidAmount = $partialAmount ? (float) $partialAmount : null;
            }

            $this->shipmentPaymentService->handlePaymentForNewShipment(
                $shipment,
                $paymentType,
                $paidAmount,
                $attachment,
                $request->prepaid_reference
            );

            return WebResponseClass::sendResponse(
                'تمت التحديث!',
                'تم تحديث بيانات الدفع بنجاح.',
                'حسناً',
                'shipment.index'
            );
        } else {
            return WebResponseClass::sendError(
                'حدث خطأ أثناء تحديث بيانات الدفع.'
            );
        }
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:prepaid,cod,partial_payment,customer_credit',
            'partial_amount' => 'required_if:payment_method,partial_payment|numeric|min:0.01',
            'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',
            'prepaid_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'prepaid_reference' => 'nullable|string|max:255',
        ]);

        $validator->after(function ($validator) use ($request, $shipment) {
            if ($request->payment_method === 'partial_payment') {
                $partial = (float) $request->partial_amount;
                $total = (float) $shipment->total_amount;

                if ($partial >= $total) {
                    $validator->errors()->add('partial_amount', 'المبلغ المدفوع جزئياً يجب أن يكون أقل من المبلغ الإجمالي للشحنة.');
                }
            }
        });

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        $data = $validator->validated();

        $paidAmount = ($data['payment_method'] === 'partial_payment') ? (float) $data['partial_amount'] : null;

        $shipment->update(['payment_method' => $data['payment_method']]);
        $paymentMethod = $data['payment_method'];
        if (in_array($paymentMethod, ['cod', 'customer_credit'])) {
            $shipment->payments()->delete();
        }
        $this->shipmentPaymentService->handlePaymentForNewShipment(
            $shipment,
            $data['prepaid_payment_method'] ?? 'cash',
            $paidAmount,
            $request->file('prepaid_attachment'),
            $request->prepaid_reference
        );

        return WebResponseClass::sendResponse('تم التحديث!', 'تم تحديث بيانات الدفع بنجاح.', 'حسناً', 'shipment.index');
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



    public function invoice($id)
    {
        $shipment = Shipment::findOrFail($id);

        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetMargins(5, 5, 5);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 12);

        $html = view('pages.shipment.invoice', compact('shipment'))->render();

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('invoice-' . $shipment->id . '.pdf', 'I');
    }

    public function printThermal($id)
    {
        $shipment = Shipment::findOrFail($id);

        $pdf = new \TCPDF('L', 'mm', [70, 100], true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(2, 2, 2);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 10);

        $pdf->AddPage();

        $html = view('pages.shipment.thermal', compact('shipment'))->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('Sticker-' . $shipment->bond_number . '.pdf', 'I');
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
        try {
            $request->validate([
                'status' => 'required|in:pending,in_transit,delivered,cancelled,returned',
            ]);

            $shipment = Shipment::findOrFail($id);
            $shipment->update([
                'status' => $request->status,
            ]);

            if ($request->status === 'delivered' && in_array($shipment->payment_method, ['cod', 'partial_payment'])) {
                $paymentService = app(ShipmentPaymentService::class);
                $paymentService->createCodBranchTransactionOnDelivery($shipment);
            }

            return response()->json([
                'success' => true,
                'success_title' => 'تم التحديث!',
                'success_message' => 'تم تحديث حالة الطرد بنجاح.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error_message' => $e->getMessage(),
            ], 500);
        }
    }

    public function openForSender($id)
    {
        $shipment = Shipment::findOrFail($id);
        $link = $this->whatsAppService->getSenderLink($shipment);

        return $this->openInNewTab($link, 'sender', $shipment);
    }

    public function openForReceiver($id)
    {
        $shipment = Shipment::findOrFail($id);
        $link = $this->whatsAppService->getReceiverLink($shipment);

        return $this->openInNewTab($link, 'receiver', $shipment);
    }

    private function openInNewTab($link, $type, $shipment)
    {
        $title = $type === 'sender' ? 'المرسل' : 'المستلم';

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>واتساب {$title}</title>
    <script>
        
        window.open('{$link}', '_blank');
        
        setTimeout(function() {
            try {
                window.close();
            } catch(e) {
                window.location.href = '/shipments/{$shipment->id}';
            }
        }, 1000);
    </script>
</head>
<body style="text-align: center; padding: 50px;">
    <h2>📱 جاري فتح واتساب {$title}...</h2>
    <p>رقم التتبع: {$shipment->tracking_number}</p>
    <p>سيتم فتح المحادثة في تاب جديد</p>
</body>
</html>
HTML;

        return response($html);
    }
}