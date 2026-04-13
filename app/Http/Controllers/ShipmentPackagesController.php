<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Notifications\AdminManifestCreated;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\User;
use App\Models\ShipmentPackage;
use App\Services\ShipmentPaymentService;
use Exception;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShipmentPackagesController extends Controller
{
    //معتمد
    public function sentIndex(Request $request)
    {
        $user = auth()->user();

        $query = ShipmentPackage::with([
            'senderBranch',     
            'shipments',         
            'driver',       
            'creator'
        ])->where('sender_branch_id', $user->branch_id);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $packages = $query->latest()->paginate(15);
        $packages->appends($request->all());

        // 4. توجيه الواجهات (موبايل / ويب)
        if ($request->isMobile){    
            return view('mobile.pages.shipmentpackage.outgoing.index', compact('packages'));
        }
        
        // السعدي: تم تصحيح المسار ليكون خاص بالديسكتوب بدلاً من الموبايل
        return view('pages.shipmentpackage.outgoing.index', compact('packages'));
    }
    //معتمد
    public function sentCreate(Request $request){
        $user = auth()->user();
        $drivers = Driver::get();
        $pendingParcels = Shipment::with(['receiverCustomer', 'receiverBranch'])
        ->where('sender_branch_id', $user->branch_id)
        ->where('status', 'pending')
        ->whereNull('shipment_package_id') 
        ->latest()
        ->get();
        if ($request->isMobile){
            return view('mobile.pages.shipmentpackage.outgoing.create', compact('drivers', 'pendingParcels'));
        }
        // السعدي حط المسار حق الدسك توب 
        return view('mobile.pages.shipmentpackage.outgoing.create', compact('drivers', 'pendingParcels'));
    }
    //معتمد
    public function sentStore(Request $request){
        $request->validate([
            'parcel_ids'         => 'required|array|min:1',
            'parcel_ids.*'       => 'exists:shipments,id',
            'tracking_number'    => 'nullable|string|max:50',
            'driver_id'          => 'nullable|exists:drivers,id',
            'driver_phone'       => 'required_without:driver_id|string|max:20',
            'driver_name'        => 'required_without:driver_id|string|max:100',
        ]);
        $user = auth()->user();
        try {
            DB::beginTransaction();

            $driverId = $request->driver_id;
            if (!$driverId && $request->driver_phone) {
                $driver = Driver::firstOrCreate(
                    [
                        'phone'  => $request->driver_phone, 
                        'app_id' => $user->app_id
                    ],
                    [
                        'name'       => $request->driver_name,
                        'branch_id'  => $user->branch_id, 
                        'created_by' => $user->id,        
                    ]
                );
                $driverId = $driver->id;
            }
            $package = ShipmentPackage::create([
                'app_id'             => $user->app_id,
                'created_by'         => $user->id,
                'sender_branch_id'   => $user->branch_id,
                'driver_id'          => $driverId,
            ]);
            Shipment::whereIn('id', $request->parcel_ids)->update([
                'shipment_package_id' => $package->id,
            ]);

            DB::commit();
            $admins = User::where('app_id', $user->app_id)
                      ->where('type', 'admin')
                      ->get();
            
            $package->load(['creator', 'senderBranch', 'driver']);
            Notification::send($admins, new AdminManifestCreated($package));

            $message = "تم إنشاء الإرسالية ( {$package->tracking_number} ) بنجاح، وربط " . count($request->parcel_ids) . " طرود بها.";

            if ($request->isMobile) {
                return redirect()->route('shipmentpackage.outgoing.index')->with('success', $message);
            }
            // السعدي غير المسار الى الصفحه تبع الدسك توب 
            return redirect()->route('shipmentpackage.outgoing.index')->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الإرسالية: ' . $e->getMessage());
        }
    }
    //معتمد
    public function sentShow(Request $request,$id)
    {
        $package = ShipmentPackage::with([
            'driver', 
            'shipments.receiverCustomer', 
            'shipments.receiverBranch', 
            'senderBranch'
        ])->findOrFail($id);

        // تعريف الحالات المتاحة للإرسالية (مثل نظام الطرود)
        $statusMap = [
            'pending' => [
                'label' => 'قيد التجهيز',
                'icon' => 'inventory_2',
                'bg_color' => 'bg-amber-50',
                'text_color' => 'text-amber-600',
                'next' => ['in_transit', 'returned']
            ],
            'in_transit' => [
                'label' => 'في الطريق',
                'icon' => 'local_shipping',
                'bg_color' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
                'next' => ['delivered', 'returned']
            ],
            'delivered' => [
                'label' => 'تمت بنجاح',
                'icon' => 'check_circle',
                'bg_color' => 'bg-emerald-50',
                'text_color' => 'text-emerald-600',
                'next' => [] 
            ],
            'returned' => [
                'label' => 'ملغاة/مرتجعة',
                'icon' => 'cancel',
                'bg_color' => 'bg-rose-50',
                'text_color' => 'text-rose-600',
                'next' => [] 
            ]
        ];
         if ($request->isMobile) {
            return view('mobile.pages.shipmentpackage.outgoing.show', compact('package', 'statusMap'));
        }
        // السعدي غير المسار الى صفحة الدسك توب 
        return view('mobile.pages.shipmentpackage.outgoing.show', compact('package', 'statusMap'));
    }
    //معتمد
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $package = ShipmentPackage::findOrFail($id);
        try {
            DB::beginTransaction();
            $newStatus = $request->status;
            $package->update(['status' => $newStatus]);

            if ($newStatus === 'returned') {
                Shipment::where('shipment_package_id', $package->id)->update([
                    'status'              => 'pending',
                    'shipment_package_id' => null 
                ]);
            
                $message = 'تم إغلاق الرحلة كمرتجعة، وتم إعادة جميع الطرود إلى المستودع (قيد الانتظار).';
            } else {
                
                Shipment::where('shipment_package_id', $package->id)->update([
                    'status' => $newStatus
                ]);
            
                $message = 'تم تحديث حالة الإرسالية وجميع الطرود التابعة لها بنجاح.';
            }

            DB::commit();
            return back()->with('success',  $message);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'فشل التحديث: ' . $e->getMessage());
        }
    }
    // معتمد
    public function removeShipment(Request $request, $packageId, $shipmentId)
    {
        $package = ShipmentPackage::findOrFail($packageId);
        if (in_array($package->status, ['delivered'])) {
            return back()->with('error', 'لا يمكن فك ارتباط الطرود من إرسالية مغلقة (تم التسليم أو مرتجعة).');
        }
        try {
            DB::beginTransaction();

            // 1. جلب الطرد والتأكد من انتمائه للإرسالية
            $shipment = Shipment::where('id', $shipmentId)
                                ->where('shipment_package_id', $packageId)
                                ->firstOrFail();

            // 2. فك الارتباط وتحديث حالة الطرد
            $shipment->update([
                'shipment_package_id' => null,
                'status'              => 'pending',
            ]);

            // 3. التحقق من عدد الطرود المتبقية في هذه الإرسالية
            $remainingShipmentsCount = Shipment::where('shipment_package_id', $packageId)->count();

            $message = 'تم فك ارتباط الطرد (' . $shipment->bond_number . ') بنجاح.';

            // 4. إذا أصبحت الإرسالية فارغة تماماً
            if ($remainingShipmentsCount === 0) {
                // نحذف الإرسالية من قاعدة البيانات
                $package = ShipmentPackage::find($packageId);
                if ($package) {
                    $package->delete();
                }

                DB::commit();

                // 💡 نوجهه لقائمة الإرساليات لأن صفحة العرض الحالية لم تعد موجودة!
                return redirect()->route('shipmentpackage.outgoing.index')
                                 ->with('success', $message . ' وتم حذف الإرسالية تلقائياً لأنها أصبحت فارغة.');
            }

            // 5. إذا كان هناك طرود أخرى، نحفظ ونعيده لنفس الصفحة
            DB::commit();

            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء فك ارتباط الطرد: ' . $e->getMessage());
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
