<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentPackage;
use Illuminate\Http\Request;
use App\Classes\WebResponseClass;
use Illuminate\Support\Facades\DB;
class ShipmentPackagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function markAllDelivered($id)
{
    try {
        DB::beginTransaction();

        $package = ShipmentPackage::with('shipments')->findOrFail($id);

        // جلب الطرود التي لم تسلم بعد فقط لتجنب تكرار العمليات
        $shipmentsToUpdate = $package->shipments()->where('status', '!=', 'delivered')->get();

        if ($shipmentsToUpdate->isEmpty()) {
            return back()->with('info', 'جميع الطرود في هذه الرحلة مستلمة بالفعل.');
        }

        foreach ($shipmentsToUpdate as $shipment) {
            $shipment->update([
                'status' => 'delivered',
                // 'delivered_at' => now(), // أضف هذا الحقل إذا كان موجوداً في قاعدة بياناتك
            ]);
            
            // هنا يمكنك إضافة منطق السندات المالية أو كشوفات الفروع إذا لزم الأمر
        }

        DB::commit();

        return WebResponseClass::sendResponse(
            'تم التحديث!',
            ' تم تحويل حالة ' . $shipmentsToUpdate->count() . ' طرد إلى تم الاستلام بنجاح.',
            'حسناً',
            'shipmentpackage.show',
            ['shipmentpackage' => $id]
        );

    } catch (\Exception $e) {
        DB::rollBack();
        return WebResponseClass::sendExceptionError($e);
    }
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
        $request->validate([
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'selected_ids' => 'required|array|min:1',
        ]);

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
        } catch (\Exception $e) {
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
}