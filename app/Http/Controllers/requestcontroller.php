<?php

namespace App\Http\Controllers;

use App\Models\AdminActivity;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Shipment;
use App\Services\AdminLoggerService;
use App\Services\ShipmentPaymentService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TCPDF;

class RequestController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService, ShipmentPaymentService $shipmentPaymentService)
    {
        $this->whatsAppService = $whatsAppService;
        $this->shipmentPaymentService = $shipmentPaymentService;
    }

    /* ========== 1- عرض جميع الطردات ========== */
    public function index()
    {
        $requests = Shipment::where('sender_branch_code', auth()->user()->branch_code)
            ->latest()
            ->paginate(10);

        return view('pages.request.index', compact('requests'));
    }

    /* ========== 2- صفحة إنشاء طرد ========== */
    public function create(Request $request)
    {
        $branches = Branch::all();
        $customers = Customer::where('branch_code', auth()->user()->branch_code)->get();

        $customer = null;
        $role = $request->query('role'); // sender | receiver

        if ($request->filled('customer_id')) {
            $customer = Customer::findOrFail($request->customer_id);
        }

        return view('pages.request.create', compact(
            'branches',
            'customers',
            'customer',
            'role'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'receiver_branch_code' => 'required|exists:branches,code',

            'sender_customer_id' => 'nullable|exists:customers,id',
            'receiver_customer_id' => 'nullable|exists:customers,id',

            'sender_name' => 'required_without:sender_customer_id|string|max:255',
            'sender_phone' => 'required_without:sender_customer_id|string|max:50',

            'receiver_name' => 'required_without:receiver_customer_id|string|max:255',
            'receiver_phone' => 'required_without:receiver_customer_id|string|max:50',

            'package_type' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'code' => 'required|string|max:255',
            'no_honey_jars' => 'required|numeric|min:0',
            'no_gallons_honey' => 'required|numeric|min:0',

            'payment_method' => 'required|in:prepaid,cod,partial_payment,customer_credit',

            'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',
            'prepaid_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'partial_amount' => 'nullable|numeric|min:0.01',

            'notes' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            $sender = auth()->user()->branch_code;
            $receiver = $request->receiver_branch_code;

            if ($sender && $receiver && $sender === $receiver) {
                $validator->errors()->add('receiver_branch_code', 'لا يمكن اختيار نفس جهة الإرسال.');
            }
        });

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        try {
            $data = $validator->validated();

            $data['sender_branch_code'] = auth()->user()->branch_code;

            if (empty($data['sender_customer_id'])) {
                $senderCustomer = Customer::firstOrCreate(
                    ['phone' => $data['sender_phone']],
                    [
                        'name' => $data['sender_name'],
                        'branch_code' => auth()->user()->branch_code,
                    ]
                );
                $data['sender_customer_id'] = $senderCustomer->id;
            }

            if (empty($data['receiver_customer_id'])) {
                $receiverCustomer = Customer::firstOrCreate(
                    ['phone' => $data['receiver_phone']],
                    [
                        'name' => $data['receiver_name'],
                        'branch_code' => $data['receiver_branch_code'],
                    ]
                );
                $data['receiver_customer_id'] = $receiverCustomer->id;
            }

            $data['customer_debt_status'] = null;
            $data['status'] = 'pending';

            $partialAmount = $data['partial_amount'] ?? null;
            unset($data['partial_amount']);

            $shipment = Shipment::create($data);

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
                $attachment
            );

            return $this->SuccessBacktoIndex('تمت الإضافة!', 'تم إنشاء الطرد بنجاح.');

        } catch (\Exception $e) {
            dd('خطأ أثناء الإنشاء', $e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    public function createCustomer()
    {
        return view('pages.request.customer.create');
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
            'branch_id' => auth()->user()->branch_id,
            'type' => 'general', // مهم للمستقبل
        ]);

        return redirect()->route('request.create', [
            'customer_id' => $customer->id,
            'role' => $data['role'],
        ]);
    }

    /* ========== 4- عرض تفاصيل طرد واحد ========== */
    public function show($id)
    {
        $shipment = Shipment::findOrFail($id);
        $countrequests = Shipment::count();

        return view('pages.request.show', compact('shipment', 'countrequests'));
    }

    /* ========== 5- صفحة تعديل الطرد ========== */
    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        $branches = Branch::all();
        // $drivers = Driver::where('status', 'active')->get();
        $customers = Customer::all();

        return view('pages.request.edit', compact('shipment', 'branches', 'customers'));
    }

    /* ========== 6- تحديث الطرد ========== */
    public function update(Request $request, $id)
    {
        $shipment = Shipment::findOrFail($id);

        $validator = Validator::make($request->all(), [

            'receiver_branch_code' => 'required|exists:branches,code',

            'sender_customer_id' => 'nullable|exists:customers,id',
            'receiver_customer_id' => 'nullable|exists:customers,id',

            'sender_name' => 'required_without:sender_customer_id|string|max:255',
            'sender_phone' => 'required_without:sender_customer_id|string|max:20',

            'receiver_name' => 'required_without:receiver_customer_id|string|max:255',
            'receiver_phone' => 'required_without:receiver_customer_id|string|max:20',

            'package_type' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',

            'total_amount' => 'required|numeric|min:0',
            'code' => 'required|string|max:255',
            'no_honey_jars' => 'required|numeric|min:0',
            'no_gallons_honey' => 'required|numeric|min:0',
            'payment_method' => 'required|in:prepaid,cod,partial_payment,customer_credit',

            'prepaid_payment_method' => 'nullable|in:cash,bank_transfer',
            'prepaid_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'partial_amount' => 'nullable|numeric|min:0.01',
            'status' => 'required|in:pending,in_transit,delivered',

            'customer_debt_status' => 'nullable|in:pending,partially_paid,fully_paid,overdue',

            'notes' => 'nullable|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            $sender = auth()->user()->branch_code;
            $receiver = $request->receiver_branch_code;

            if ($sender && $receiver && $sender === $receiver) {
                $validator->errors()->add('receiver_branch_code', 'لا يمكن اختيار نفس جهة الإرسال.');
            }

            if (
                in_array($request->payment_method, ['prepaid', 'partial_payment']) &&
                $request->prepaid_payment_method === 'bank_transfer' &&
                ! $request->hasFile('prepaid_attachment')
            ) {
                $validator->errors()->add(
                    'prepaid_attachment',
                    'في حالة التحويل البنكي، يجب إرفاق سند الدفع.'
                );
            }
        });

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        $data = $validator->validated();

        $partialAmount = $data['partial_amount'] ?? null;

        if (empty($data['sender_customer_id'])) {
            $senderCustomer = Customer::firstOrCreate(
                ['phone' => $data['sender_phone']],
                [
                    'name' => $data['sender_name'],
                    'branch_code' => auth()->user()->branch_code,
                ]
            );
            $data['sender_customer_id'] = $senderCustomer->id;
        }

        if (empty($data['receiver_customer_id'])) {
            $receiverCustomer = Customer::firstOrCreate(
                ['phone' => $data['receiver_phone']],
                [
                    'name' => $data['receiver_name'],
                    'branch_code' => $data['receiver_branch_code'],
                ]
            );
            $data['receiver_customer_id'] = $receiverCustomer->id;
        }

        $data['sender_branch_code'] = auth()->user()->branch_code;

        if (($data['payment_method'] ?? null) === 'customer_credit') {
            $data['customer_debt_status'] = $data['customer_debt_status'] ?? 'pending';
        } else {
            $data['customer_debt_status'] = $data['customer_debt_status'] ?? null;
        }

        unset($data['prepaid_payment_method'], $data['prepaid_attachment'], $data['partial_amount']);

        $shipment->update($data);

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
            $attachment
        );

        return $this->SuccessBacktoIndex('تم التحديث!', 'تم تحديث الطرد بنجاح.');
    }

    /* ========== 7- حذف الطرد ========== */
    public function destroy($id)
    {
        try {
            Shipment::findOrFail($id)->delete();
            AdminLoggerService::log('حذف طرد', 'Shipment', 'تم حذف الطرد بنجاح');

            return $this->SuccessBacktoIndex(
                'تم الحذف!',
                'تم حذف الطرد بنجاح.'
            );
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    private function ValidationError($validator)
    {
        $firstError = $validator->errors()->first();

        return redirect()->back()
            ->withErrors($validator)
            ->with('error', true)
            ->with('error_title', 'حدث خطأ!')
            ->with('error_message', $firstError)
            ->with('error_buttonText', 'حسناً')
            ->withInput();
    }

    private function SuccessBacktoIndex($title, $msg)
    {
        return redirect()->route('request.index')
            ->with('success', true)
            ->with('success_title', $title)
            ->with('success_message', $msg)
            ->with('success_buttonText', 'حسناً');
    }

    private function ExceptionError($e)
    {
        return redirect()->back()
            ->with('error', true)
            ->with('error_title', 'خطأ غير متوقع!')
            ->with('error_message', $e->getMessage())
            ->with('error_buttonText', 'حسناً');
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

        $html = view('pages.request.invoice', compact('shipment'))->render();

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('invoice-'.$shipment->id.'.pdf', 'I');
    }

    public function printThermal($id)
    {
        $shipment = Shipment::findOrFail($id);

        $pdf = new TCPDF('P', 'mm', [80, 300], true, 'UTF-8', false);
        $pdf->setRTL(true);
        $pdf->SetFont('aealarabiya', '', 12);
        $pdf->AddPage();

        $html = view('shipments.thermal', compact('shipment'))->render();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('thermal-'.$shipment->id.'.pdf', 'I');
    }

    public function adminlog()
    {
        $logs = AdminActivity::latest()->paginate(20);

        return view('pages.log.logs', compact('logs'));
    }

    public function selectCustomer()
    {
        $customers = Customer::where('branch_id', auth()->user()->branch_id)->get();

        return view('pages.request.select-customer', compact('customers'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,in_transit,deliverd,cancelled',
            ]);

            $shipment = Shipment::findOrFail($id);
            $shipment->update([
                'status' => $request->status,
            ]);

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
