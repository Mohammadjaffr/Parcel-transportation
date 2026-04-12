<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\ShipmentPackage;
use App\Services\ShipmentPaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShipmentPackagesController extends Controller
{
    //معتمد
    public function sentIndex(Request $request)
    {
    $user = auth()->user();

    // جلب الشحنات مع كافة العلاقات (القديمة والجديدة)
    $packages = ShipmentPackage::with([
        'senderBranch',    // الفرع المرسل
        'receiverBranch',  // الفرع المستلم
        'parcels',         // الطرود بداخل الشحنة
        'driver',          // السائق المسؤول (العلاقة الجديدة)
        'creator'          // الموظف الذي أنشأ الشحنة (العلاقة الجديدة)
    ])
    ->where('app_id', $user->app_id) // تأمين البيانات على مستوى الشركة
    ->where('sender_branch_id', $user->branch_id) // فلترة حسب فرع الموظف الحالي
    ->latest()
    ->paginate(15);
        if ($request->isMobile){    
            return view('mobile.pages.shipmentpackage.outgoing.index', compact('packages'));
        }
        // السعدي حط المسار حق الدسك توب 
        return view('mobile.pages.shipmentpackage.outgoing.index', compact('packages'));
    }
    public function sentCreate(Request $request){
        $user = auth()->user();
        $drivers = Driver::get();
        $branches = Branch::where('id', '!=', $user->branch_id)->get();
        $pendingParcels = Shipment::where('sender_branch_id', $user->branch_id)
        ->where('status', 'pending')
        ->whereNull('shipment_package_id')
        ->latest()
        ->get();
        if ($request->isMobile){
            return view('mobile.pages.shipmentpackage.outgoing.create', compact('drivers', 'branches', 'pendingParcels'));
        }
        // السعدي حط المسار حق الدسك توب 
        return view('mobile.pages.shipmentpackage.outgoing.create', compact('drivers', 'branches', 'pendingParcels'));
    }
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branchCode = $user->branch_code;

        $pendingShipments = Shipment::where('sender_branch_code', $branchCode)
            ->where('status', 'pending')
            ->whereNull('shipment_package_id')
            ->with(['receiverBranch', 'senderBranch'])
            ->get();

        $packages = ShipmentPackage::whereHas('shipments', function ($query) use ($branchCode) {
            $query->where('sender_branch_code', $branchCode);
        })
            ->with([
                'shipments' => function ($query) use ($branchCode) {
                    $query->where('sender_branch_code', $branchCode);
                },
            ])
            ->withCount([
                'shipments as shipments_count' => function ($query) use ($branchCode) {
                    $query->where('sender_branch_code', $branchCode);
                },
            ])
            ->latest()
            ->paginate(10);

        return view('pages.shipmentpackage.index', compact('pendingShipments', 'packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['required', 'string', 'min:9', 'max:20'],
            'selected_ids' => ['required', 'array', 'min:1'],
        ]);
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $package = ShipmentPackage::create([
                'driver_name' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
            ]);

            Shipment::whereIn('id', $request->selected_ids)
                ->update([
                    'shipment_package_id' => $package->id,
                    'status' => 'in_transit',
                ]);

            // استخراج الفروع المستقبلة من الطرود وربطها بالحزمة
            $receiverBranches = Shipment::whereIn('id', $request->selected_ids)
                ->pluck('receiver_branch_code')
                ->unique()
                ->filter() // إزالة القيم الفارغة
                ->toArray();

            if (!empty($receiverBranches)) {
                // ربط الحزمة بالفروع المستقبلة
                foreach ($receiverBranches as $branchCode) {
                    $package->receiverBranches()->attach($branchCode, [
                        'status' => 'pending',
                        'arrival_date' => null,
                        'notes' => null,
                    ]);
                }
            }

            return WebResponseClass::sendResponse(
                'تمت الرحلة!',
                'تم إنشاء رحلة الشحن وربط الطرود بنجاح.',
                'حسناً',
                'shipmentpackage.index'
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = ShipmentPackage::with(['shipments.senderCustomer', 'shipments.receiverCustomer', 'shipments.receiverBranch'])
            ->findOrFail($id);

        return view('pages.shipmentpackage.show', compact('package'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function printManifest($id)
    {
        $package = ShipmentPackage::with(['shipments.senderCustomer', 'shipments.receiverCustomer', 'shipments.receiverBranch', 'shipments.senderBranch'])
            ->findOrFail($id);

        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 10);

        $html = view('pages.shipmentpackage.manifest_pdf', compact('package'))->render();

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('Manifest-' . $package->tracking_number . '.pdf', 'I');
    }
    public function printManifestD($id)
    {
        $package = ShipmentPackage::with(['shipments.senderCustomer', 'shipments.receiverCustomer', 'shipments.receiverBranch', 'shipments.senderBranch'])
            ->findOrFail($id);

        $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 10);

        $html = view('pages.shipmentpackage.manifest_driver_pdf', compact('package'))->render();

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('Manifest-' . $package->tracking_number . '.pdf', 'I');
    }

    /**
     * Unlink a shipment from its package (return to pending status)
     */
    public function unlinkFromPackage(Request $request, $shipmentId)
    {
        try {
            $shipment = Shipment::findOrFail($shipmentId);

            // التحقق من أن الطرد مرتبط برحلة
            if (!$shipment->shipment_package_id) {
                return WebResponseClass::sendError(
                    'هذا الطرد غير مرتبط بأي رحلة',
                    'خطأ',
                    'حسناً'
                );
            }

            // التحقق من أن الطرد ليس مسلماً أو ملغياً أو مرتجعاً
            if (in_array($shipment->status, ['delivered', 'cancelled', 'returned'])) {
                return WebResponseClass::sendError(
                    'لا يمكن فك ربط طرد في حالة: ' . $shipment->status,
                    'خطأ',
                    'حسناً'
                );
            }

            // حفظ معرف الرحلة قبل فك الربط
            $packageId = $shipment->shipment_package_id;

            // حساب عدد الطرود المتبقية في الرحلة (باستثناء الطرد الحالي)
            $remainingCount = Shipment::where('shipment_package_id', $packageId)
                ->where('id', '!=', $shipmentId)
                ->count();

            // فك ربط الطرد من الرحلة وإرجاعه لحالة قيد الانتظار
            $shipment->shipment_package_id = null;
            $shipment->status = 'pending';
            $shipment->save();

            // تحديد الرابط للتوجيه
            $redirectUrl = $remainingCount > 0
                ? null
                : 'shipmentpackage.index';

            return WebResponseClass::sendResponse(
                'تم فك الربط',
                'تم فك ربط الطرد #' . $shipment->bond_number . ' من الرحلة وإعادته لحالة قيد الانتظار',
                'حسناً',
                $redirectUrl
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
