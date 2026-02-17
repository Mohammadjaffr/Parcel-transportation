<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\ReceiptItem;
use Illuminate\Http\Request;
use App\Models\ReceiptHeader;
use App\Classes\WebResponseClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReceiptHeaderController extends Controller
{
    /**
     * عرض صفحة إنشاء بيان استلام جديد
     */
    public function create()
    {
        $branches = Branch::where('code','!=',auth()->user()->branch_code)->get();
        $drivers  = Driver::all();

        return view('pages.receipts.create', compact('branches', 'drivers'));
    }

    /**
     * حفظ بيان الاستلام مع الطرود
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Header
            'number'             => ['required', 'string', 'max:255', 'unique:receipt_headers,number'],
            'source_branch_code' => ['required', 'exists:branches,code'],
            'driver_id'          => ['nullable', 'exists:drivers,id'],
            'driver_name'        => ['required', 'string', 'max:255'],
            'driver_phone'       => ['nullable', 'string', 'max:20'],
            'general_notes'      => ['nullable', 'string', 'max:1000'],

            // Items
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.number'         => ['required', 'string', 'max:255', 'distinct'],
            'items.*.sender_name'    => ['nullable', 'string', 'max:255'],
            'items.*.receiver_name'  => ['required', 'string', 'max:255'],
            'items.*.receiver_phone' => ['required', 'string', 'max:20'],
            'items.*.package_type'   => ['required', 'string', 'max:255'],
            'items.*.item_notes'     => ['nullable', 'string', 'max:500'],
        ], [
            'items.required'              => 'يجب إضافة طرد واحد على الأقل.',
            'items.min'                   => 'يجب إضافة طرد واحد على الأقل.',
            'items.*.number.required'     => 'رقم السند مطلوب لكل طرد.',
            'items.*.number.distinct'     => 'رقم السند مكرر، يجب أن تكون الأرقام مختلفة.',
            'items.*.receiver_name.required'  => 'اسم المستلم مطلوب.',
            'items.*.receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'items.*.package_type.required'   => 'نوع الطرد مطلوب.',
            'items.*.package_type.in'         => 'نوع الطرد غير صالح.',
            'source_branch_code.required'     => 'الفرع المرسل مطلوب.',
            'driver_name.required'            => 'اسم السائق مطلوب.',
            'number.required'                 => 'رقم السند مطلوب.',
            'number.unique'                   => 'رقم السند مستخدم مسبقاً.',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::transaction(function () use ($request) {
                // Resolve driver: existing selection or auto-create
                if ($request->filled('driver_id')) {
                    $driverId = $request->driver_id;
                } else {
                    $driver = Driver::create([
                        'name'  => $request->driver_name,
                        'phone' => $request->driver_phone,
                    ]);
                    $driverId = $driver->id;
                }

                // Create header
                $header = ReceiptHeader::create([
                    'number'                 => $request->number,
                    'source_branch_code'     => $request->source_branch_code,
                    'driver_id'              => $driverId,
                    'destination_branch_code' => auth()->user()->branch_code,
                    'created_by'             => auth()->id(),
                    'general_notes'          => $request->general_notes,
                    'received_at'            => now(),
                ]);

                // Create items
                foreach ($request->items as $item) {
                    $header->items()->create([
                        'number'         => $item['number'],
                        'sender_name'    => $item['sender_name'] ?? null,
                        'receiver_name'  => $item['receiver_name'],
                        'receiver_phone' => $item['receiver_phone'],
                        'package_type'   => $item['package_type'],
                        'item_notes'     => $item['item_notes'] ?? null,
                    ]);
                }
            });

            return WebResponseClass::sendResponse(
                'تم الحفظ!',
                'تم حفظ بيان الاستلام والطرود بنجاح.',
                'حسناً',
                'receipts.index'
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * عرض جميع بيانات الاستلام
     */
    public function index()
{
    $branchCode = auth()->user()->branch_code;

    // 1. الاستعلام الرئيسي (كما هو لأنه جيد)
    $receipts = ReceiptHeader::with(['driver', 'sourceBranch', 'destinationBranch', 'items'])
        ->where('destination_branch_code', $branchCode)
        ->latest()
        ->paginate(15);

    // 2. تجميع الإحصائيات (Optimization)
    // بدلاً من استعلامات Eloquent المنفصلة، نستخدم Query Builder للحصول على الأرقام بشكل أسرع
    
    // عدد الفواتير الكلي
    $totalReceipts = ReceiptHeader::where('destination_branch_code', $branchCode)->count();

    // عدد العناصر الكلي: استخدام Join أسرع بكثير من whereHas
    $totalItems = DB::table('receipt_items')
        ->join('receipt_headers', 'receipt_items.receipt_header_id', '=', 'receipt_headers.id')
        ->where('receipt_headers.destination_branch_code', $branchCode)
        ->count();

    // عدد الفواتير المعلقة (التي تحتوي على عنصر واحد على الأقل غير مسلم)
    $hasPending = ReceiptHeader::where('destination_branch_code', $branchCode)
        ->whereHas('items', function ($q) {
            $q->where('is_delivered', false);
        })->count();

    // التحسين الرياضي:
    // الفواتير المكتملة = العدد الكلي - الفواتير المعلقة
    // (ملاحظة: هذا يفترض أن الفواتير الفارغة لا تحسب كمكتملة، إذا كانت الفواتير الفارغة نادرة فهذا حل ممتاز للأداء)
    $fullyDelivered = $totalReceipts - $hasPending; 
    
    // إذا كنت تحتاج دقة 100% لاستبعاد الفواتير التي ليس بها عناصر أصلاً، يمكنك ترك الاستعلام القديم ولكن التحسين الرياضي أسرع بمراحل.

    return view('pages.receipts.index', compact('receipts', 'totalReceipts', 'totalItems', 'fullyDelivered', 'hasPending'));
}

    /**
     * عرض تفاصيل بيان استلام
     */
    public function show($id)
    {
        $receipt = ReceiptHeader::with(['items', 'driver', 'sourceBranch', 'destinationBranch'])->findOrFail($id);

        return view('pages.receipts.show', compact('receipt'));
    }

    /**
     * عرض صفحة تعديل بيان استلام
     */
    public function edit($id)
    {
        $receipt = ReceiptHeader::with('items', 'driver')->findOrFail($id);
        $branches = Branch::where('code', '!=', auth()->user()->branch_code)->get();
        $drivers = Driver::all();

        return view('pages.receipts.edit', compact('receipt', 'branches', 'drivers'));
    }

    /**
     * تحديث بيان الاستلام مع الطرود
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'number'             => ['required', 'string', 'max:255', 'unique:receipt_headers,number,' . $id],
            'source_branch_code' => ['required', 'exists:branches,code'],
            'driver_id'          => ['nullable', 'exists:drivers,id'],
            'driver_name'        => ['required', 'string', 'max:255'],
            'driver_phone'       => ['nullable', 'string', 'max:20','min:10'],
            'general_notes'      => ['nullable', 'string', 'max:1000'],

            'items'                  => ['required', 'array', 'min:1'],
            'items.*.number'         => ['required', 'string', 'max:255', 'distinct'],
            'items.*.sender_name'    => ['nullable', 'string', 'max:255'],
            'items.*.receiver_name'  => ['required', 'string', 'max:255'],
            'items.*.receiver_phone' => ['required', 'string', 'max:20','min:10'],
            'items.*.package_type'   => ['required', 'string', 'max:255'],
            'items.*.item_notes'     => ['nullable', 'string', 'max:500'],
        ], [
            'items.required'              => 'يجب إضافة طرد واحد على الأقل.',
            'items.min'                   => 'يجب إضافة طرد واحد على الأقل.',
            'items.*.number.required'     => 'رقم السند مطلوب لكل طرد.',
            'items.*.number.distinct'     => 'رقم السند مكرر، يجب أن تكون الأرقام مختلفة.',
            'items.*.receiver_name.required'  => 'اسم المستلم مطلوب.',
            'items.*.receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'items.*.package_type.required'   => 'نوع الطرد مطلوب.',
            'source_branch_code.required'     => 'الفرع المرسل مطلوب.',
            'driver_name.required'            => 'اسم السائق مطلوب.',
            'number.required'                 => 'رقم السند مطلوب.',
            'number.unique'                   => 'رقم السند مستخدم مسبقاً.',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $header = ReceiptHeader::findOrFail($id);

                // Resolve driver
                if ($request->filled('driver_id')) {
                    $driverId = $request->driver_id;
                } else {
                    $driver = Driver::create([
                        'name'  => $request->driver_name,
                        'phone' => $request->driver_phone,
                    ]);
                    $driverId = $driver->id;
                }

                // Update header
                $header->update([
                    'number'             => $request->number,
                    'source_branch_code' => $request->source_branch_code,
                    'driver_id'          => $driverId,
                    'general_notes'      => $request->general_notes,
                ]);

                // Delete old items and re-create
                $header->items()->forceDelete();

                foreach ($request->items as $item) {
                    $header->items()->create([
                        'number'         => $item['number'],
                        'sender_name'    => $item['sender_name'] ?? null,
                        'receiver_name'  => $item['receiver_name'],
                        'receiver_phone' => $item['receiver_phone'],
                        'package_type'   => $item['package_type'],
                        'item_notes'     => $item['item_notes'] ?? null,
                    ]);
                }
            });

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيان الاستلام والطرود بنجاح.',
                'حسناً',
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * تبديل حالة التسليم لطرد معين
     */
    public function toggleDelivery($itemId)
    {
        $item = ReceiptItem::findOrFail($itemId);
        $item->update(['is_delivered' => !$item->is_delivered]);

        return WebResponseClass::sendResponse(
            'تم التحديث!',
            'تم تحديث بيان الاستلام والطرود بنجاح.',
            'حسناً',
        );
    }

    /**
     * إضافة طرد جديد لبيان استلام موجود
     */
    public function addItem(Request $request, $receiptId)
    {
        $receipt = ReceiptHeader::findOrFail($receiptId);

        $validated = $request->validate([
            'number'         => ['required', 'string', 'max:255'],
            'sender_name'    => ['nullable', 'string', 'max:255'],
            'receiver_name'  => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:20'],
            'package_type'   => ['required', 'string', 'max:255'],
            'item_notes'     => ['nullable', 'string', 'max:500'],
        ], [
            'number.required'         => 'رقم السند مطلوب.',
            'receiver_name.required'  => 'اسم المستلم مطلوب.',
            'receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'package_type.required'   => 'نوع الطرد مطلوب.',
        ]);

        try {
            $receipt->items()->create($validated);

            return WebResponseClass::sendResponse(
                'تم الإضافة!',
                'تم إضافة الطرد بنجاح.',
                'حسناً',
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * تحديث بيانات طرد معين
     */
    public function updateItem(Request $request, $itemId)
    {
        $item = ReceiptItem::findOrFail($itemId);

        $validated = $request->validate([
            'number'         => ['required', 'string', 'max:255'],
            'sender_name'    => ['nullable', 'string', 'max:255'],
            'receiver_name'  => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:20'],
            'package_type'   => ['required', 'string', 'max:255'],
            'item_notes'     => ['nullable', 'string', 'max:500'],
        ], [
            'number.required'         => 'رقم السند مطلوب.',
            'receiver_name.required'  => 'اسم المستلم مطلوب.',
            'receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'package_type.required'   => 'نوع الطرد مطلوب.',
        ]);

        try {
            $item->update($validated);

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيانات الطرد بنجاح.',
                'حسناً',
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
