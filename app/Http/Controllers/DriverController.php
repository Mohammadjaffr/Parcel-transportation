<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Services\AdminLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    /* ========== 1- عرض جميع السائقين ========== */
    public function index()
    {
        $drivers = Driver::latest()->paginate(10);

        return view('pages.drivers.index', compact('drivers'));
    }

    /* ========== 2- صفحة إنشاء سائق ========== */
    public function create()
    {

        return view('pages.drivers.create');
    }

    /* ========== 3- حفظ سائق جديد ========== */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'phone'  => 'required|string|max:20',
            'city'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'اسم السائق مطلوب',
            'status.required' => 'حالة السائق مطلوبة',
            'phone.required' => 'رقم الهاتف مطلوب',
            'city.required' => 'مدينة السائق مطلوبة',
        ]);

        if ($validator->fails()) return $this->ValidationError($validator);

        try {
            $driver = Driver::create($validator->validated());

            AdminLoggerService::log(
                'إضافة سائق',
                'Driver',
                $driver->id,
                "تم إضافة السائق {$driver->name}"
            );

            return $this->SuccessBacktoIndex('تمت الإضافة!', 'تم حفظ السائق بنجاح.');
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /* ========== 4- عرض تفاصيل سائق ========== */
    public function show($id)
    {
        $driver = Driver::findOrFail($id);
        return view('pages.drivers.show', compact('driver'));
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
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:20',
            'city'   => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) return $this->ValidationError($validator);

        try {
            $driver->update($validator->validated());

            AdminLoggerService::log(
                'تحديث سائق',
                'Driver',
                $driver->id,
                "تحديث بيانات السائق {$driver->name}"
            );

            return $this->SuccessBacktoIndex('تم التحديث!', 'تم تعديل بيانات السائق بنجاح.');
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
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

            return $this->SuccessBacktoIndex('تم الحذف!', 'تم حذف السائق بنجاح.');
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }
    public function shipments($id)
    {
        $driver = Driver::findOrFail($id);
        $shipments = $driver->shipments()->latest()->paginate(20);

        return view('pages.drivers.shipments', compact('driver', 'shipments'));
    }
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

    /* ========== دوال مساعدة  ========== */

    private function ValidationError($validator)
    {
        return redirect()->back()
            ->withErrors($validator)
            ->with('error', true)
            ->with('error_title', 'خطأ في البيانات!')
            ->with('error_message', $validator->errors()->first())
            ->with('error_buttonText', 'حسناً')
            ->withInput();
    }

    private function SuccessBacktoIndex($title, $msg)
    {
        return redirect()->route('drivers.index')
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
}
