<?php

namespace App\Http\Controllers;

use App\Models\AdminActivity;
use Illuminate\Support\Facades\Validator;
use App\Models\Shipment;
use TCPDF;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\Driver;
use App\Services\AdminLoggerService;
use App\Models\BranchTransaction;

class RequestController extends Controller
{
    protected $whatsAppService;
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }
    /* ========== 1- عرض جميع الطردات ========== */
    public function index()
    {
        $requests = Shipment::where('branch_id', auth()->user()->branch_id)
                             ->latest()
                             ->paginate(10);

        return view('pages.request.index', compact('requests'));
    }

    /* ========== 2- صفحة إنشاء طرد ========== */
    public function create()
    {
        $branches = Branch::all();
        $drivers = Driver::where('status', 'active')->get();

        return view('pages.request.create ', compact('branches', 'drivers'));
    }

    /* ========== 3- تخزين طرد جديد ========== */
  public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'sender_name'     => 'required|string',
        'sender_phone'    => 'required|string',
        'from_city'       => 'required|string',
        'receiver_name'   => 'required|string',
        'receiver_phone'  => 'required|string',
        'to_city'         => 'required|string',
        'package_type'    => 'required|string',
        'weight'          => 'nullable|numeric',
        'payment_method'  => 'required|in:prepaid,cod',
        'cod_amount'      => 'required|numeric',
        'notes'           => 'nullable|string',
        'code'            => 'nullable|string|max:255',
        'no_honey_jars'   => 'nullable|numeric',
        'no_gallons_honey' => 'nullable|numeric',
        'driver_id'       => 'required|exists:drivers,id',
    ]);

    if ($validator->fails()) {
        return $this->ValidationError($validator);
    }

    try {

        $data = $validator->validated();
        $data['branch_id'] = auth()->user()->branch_id;

        $shipment = Shipment::create($data);

        /*
        =========================================
        |   تسجيل القيد المحاسبي للشحنات COD   |
        =========================================
        */
        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {

            $from = $shipment->branch_id; // الفرع المرسل
            $to = Branch::where('name', $shipment->to_city)->value('id'); // الفرع المستلم

            if ($to && $to != $from) {

                // قيد واحد فقط: الفرع المستلم عليه مبلغ لصالح الفرع المرسل
                BranchTransaction::create([
                    'shipment_id'    => $shipment->id,
                    'from_branch_id' => $to,   // المستلم (عليه)
                    'to_branch_id'   => $from, // المرسل (له)
                    'amount'         => $shipment->cod_amount,
                    'type'           => 'cod',
                    'description'    => "له مبلغ من العميل {$shipment->receiver_name} على شحنة رقم {$shipment->id}",
                ]);
            }
        }

        AdminLoggerService::log(
            'إنشاء طرد',
            'Shipment',
            $shipment->id,
            "إنشاء طرد من {$shipment->from_city} إلى {$shipment->to_city} - سند {$shipment->bond_number}"
        );

        return $this->SuccessBacktoIndex(
            'تمت الإضافة!',
            'تم إنشاء الطرد بنجاح.'
        );

    } catch (\Exception $e) {
        return $this->ExceptionError($e);
    }
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
        $drivers = Driver::where('status', 'active')->get();
        return view('pages.request.edit', compact('shipment', 'branches', 'drivers'));
    }

    /* ========== 6- تحديث الطرد ========== */
  public function update(Request $request, $id)
{
    $shipment = Shipment::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'sender_name'     => 'required|string|max:255',
        'sender_phone'    => 'required|string|max:20',
        'from_city'       => 'required|string|max:255',
        'receiver_name'   => 'required|string|max:255',
        'receiver_phone'  => 'required|string|max:20',
        'to_city'         => 'required|string|max:255',
        'package_type'    => 'required|string|max:255',
        'payment_method'  => 'required|in:prepaid,cod',
        'notes'           => 'nullable|string',
        'cod_amount'      => 'nullable|numeric',
        'code'            => 'nullable|string|max:255',
        'no_honey_jars'   => 'nullable|numeric',
        'no_gallons_honey' => 'nullable|numeric',
        'driver_id'       => 'required|exists:drivers,id',
    ]);

    if ($validator->fails()) {
        return $this->ValidationError($validator);
    }

    try {

        // تحديث البيانات
        $shipment->update($validator->validated());

        // حذف القيود القديمة
        BranchTransaction::where('shipment_id', $shipment->id)->delete();

        /*
        =========================================
        |   إعادة إنشاء القيد المحاسبي الجديد  |
        =========================================
        */
        if ($shipment->payment_method === 'cod' && $shipment->cod_amount > 0) {

            $from = $shipment->branch_id;
            $to = Branch::where('name', $shipment->to_city)->value('id');

            if ($to && $to != $from) {

                BranchTransaction::create([
                    'shipment_id'    => $shipment->id,
                    'from_branch_id' => $to,   // المستلم = عليه
                    'to_branch_id'   => $from, // المرسل = له
                    'amount'         => $shipment->cod_amount,
                    'type'           => 'cod',
                    'description'    => "تحديث: مبلغ على فرع {$shipment->to_city} لشحنة رقم {$shipment->id}",
                ]);
            }
        }

        AdminLoggerService::log(
            'تحديث طرد',
            'Shipment',
            $shipment->id,
            "تحديث طرد: {$shipment->from_city} → {$shipment->to_city}"
        );

        return $this->SuccessBacktoIndex(
            'تم التحديث!',
            'تم تحديث الطرد بنجاح.'
        );

    } catch (\Exception $e) {
        return $this->ExceptionError($e);
    }
}


    /* ========== 7- حذف الطرد ========== */
    public function destroy($id)
    {
        try {
            Shipment::findOrFail($id)->delete();
            AdminLoggerService::log('حذف طرد', 'Shipment', "تم حذف الطرد بنجاح");

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

        return $pdf->Output('invoice-' . $shipment->id . '.pdf', 'I');
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

        return $pdf->Output('thermal-' . $shipment->id . '.pdf', 'I');
    }

    public function adminlog()
    {
        $logs = AdminActivity::latest()->paginate(20);
        return view('pages.log.logs', compact('logs'));
    }


    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,in_transit,deliverd,cancelled'
            ]);

            $shipment = Shipment::findOrFail($id);
            $shipment->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'success_title' => 'تم التحديث!',
                'success_message' => 'تم تحديث حالة الطرد بنجاح.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error_message' => $e->getMessage()
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