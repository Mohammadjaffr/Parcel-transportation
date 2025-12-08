<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Shipment;
use TCPDF;
use Illuminate\Http\Request;
use App\Models\Branch;


class RequestController extends Controller
{
    /* ========== 1- عرض جميع الطلبات ========== */
    public function index()
    {
        $requests = Shipment::latest()->paginate(10);

        return view('pages.request.index', compact('requests'));
    }

    /* ========== 2- صفحة إنشاء طلب ========== */
    public function create()
    {
        $branches = Branch::all();

        return view('pages.request.create ', compact('branches'));
    }

    /* ========== 3- تخزين طلب جديد ========== */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_name'     => 'required|string',
            'sender_phone'    => 'required|string',
            'from_city'       => 'required|string',
            'receiver_name'   => 'required|string',
            'receiver_phone'  => 'required|string',
            'to_city'         => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'package_type'    => 'required|string',
            'weight'          => 'nullable|numeric',
            'payment_method'  => 'required|in:prepaid,cod',
            'cod_amount'      => 'nullable|numeric',
            'notes'           => 'nullable|string',
        ], [
            'sender_name.required'    => 'حقل اسم المرسل مطلوب.',
            'sender_phone.required'   => 'حقل هاتف المرسل مطلوب.',
            'from_city.required'      => 'حقل من المدينة مطلوب.',
            'receiver_name.required'  => 'حقل اسم المستلم مطلوب.',
            'receiver_phone.required' => 'حقل هاتف المستلم مطلوب.',
            'to_city.required'        => 'حقل إلى المدينة مطلوب.',
            'branch_id.required'         => 'حقل الفرع مطلوب.',
            'package_type.required'   => 'حقل نوع الطرد مطلوب.',
            'payment_method.required' => 'حقل طريقة الدفع مطلوب.',
            'payment_method.in'       => 'طريقة الدفع المختارة غير صالحة.',
        ]);

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        try {
            Shipment::create($validator->validated());

            return $this->SuccessBacktoIndex(
                'تمت الإضافة!',
                'تم إنشاء الطرد بنجاح.'
            );
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /* ========== 4- عرض تفاصيل طلب واحد ========== */
    public function show($id)
    {
        $shipment = Shipment::findOrFail($id);
        $countrequests = Shipment::count();
        
        return view('pages.request.show', compact('shipment', 'countrequests'));
    }

    /* ========== 5- صفحة تعديل الطلب ========== */
    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        $branches = Branch::all();
        return view('pages.request.edit', compact('shipment', 'branches'));
    }

    /* ========== 6- تحديث الطلب ========== */
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
            'branch_id' => 'required|exists:branches,id',
            'package_type'    => 'required|string|max:255',
            'payment_method'  => 'required|in:prepaid,cod',
            'notes'           => 'nullable|string',
            'cod_amount'      => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        try {
            $shipment->update($validator->validated());

            return $this->SuccessBacktoIndex(
                'تم التحديث!',
                'تم تحديث الطرد بنجاح.'
            );
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /* ========== 7- حذف الطلب ========== */
    public function destroy($id)
    {
        try {
            Shipment::findOrFail($id)->delete();

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

}