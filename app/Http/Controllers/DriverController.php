<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Services\AdminLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Classes\WebResponseClass;

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
            'name'   => ['required', 'string', 'max:255'],
            'phone'  => ['required', 'string', 'max:20'],
            'city'   => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        } 

        try {
            $driver = Driver::create($validator->validated());

            AdminLoggerService::log(
                'إضافة سائق',
                'Driver',
                $driver->id,
                "تم إضافة السائق {$driver->name}"
            );

            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم حفظ السائق بنجاح.',
                'حسناً',
                'drivers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
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
            'name'   => ['required', 'string', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:20'],
            'city'   => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        } 

        try {
            $driver->update($validator->validated());

            AdminLoggerService::log(
                'تحديث سائق',
                'Driver',
                $driver->id,
                "تحديث بيانات السائق {$driver->name}"
            );

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تعديل بيانات السائق بنجاح.',
                'حسناً',
                'drivers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
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

            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف السائق بنجاح.',
                'حسناً',
                'drivers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
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


}
