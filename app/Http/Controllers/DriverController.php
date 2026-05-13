<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Classes\WebResponseClass;
use App\Services\AdminLoggerService;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    /* ========== 1- عرض جميع السائقين مع البحث والفلترة ========== */
  public function index(Request $request)
{
    // حساب الإجمالي فقط
    $totalCount = Driver::count();

    $query = Driver::latest();

    // البحث بالاسم ورقم الهاتف
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    $drivers = $query->paginate(10)->withQueryString();

    if ($request->isMobile) {
        return view('mobile.pages.people.drivers.index', compact('drivers', 'totalCount'));
    }

    return view('pages.drivers.index', compact('drivers', 'totalCount'));
}
    /* ========== 2- صفحة إنشاء سائق ========== */
    public function create()
    {
        return view('pages.drivers.modals.create');
    }

    /* ========== 3- حفظ سائق جديد ========== */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => ['required', 'string', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        } 

        try {
            $data = $validator->validated();
            $data['app_id']     = Auth::user()->app_id;    
            $data['branch_id']  = Auth::user()->branch_id; 
            $data['created_by'] = Auth::id();              
            $driver = Driver::create($data);
            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم حفظ السائق بنجاح.',
                'حسناً',
                'drivers.index'
            );
        } catch (Exception $e) {
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

    /* ========== 8- شحنات السائق ========== */
    public function shipments($id)
    {
        $driver = Driver::findOrFail($id);
        $shipments = $driver->shipments()->latest()->paginate(20);

        return view('pages.drivers.shipments', compact('driver', 'shipments'));
    }

    /* ========== 9- طباعة شحنات السائق ========== */
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
}