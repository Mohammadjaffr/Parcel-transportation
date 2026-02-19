<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\ReceiptItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ReceiptHeader;
use App\Classes\WebResponseClass;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionCategory;

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
            'number'             => ['required', 'string', 'max:255', Rule::unique('receipt_headers')->where(function ($query) use ($request) {
                return $query->where('source_branch_code', $request->source_branch_code);
            })],
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
            'items.*.payment_status' => ['required', 'in:paid,unpaid'],
            'items.*.amount'         => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.required'              => 'يجب إضافة طرد واحد على الأقل.',
            'items.min'                   => 'يجب إضافة طرد واحد على الأقل.',
            'items.*.number.required'     => 'رقم السند مطلوب لكل طرد.',
            'items.*.number.distinct'     => 'رقم السند مكرر، يجب أن تكون الأرقام مختلفة.',
            'items.*.receiver_name.required'  => 'اسم المستلم مطلوب.',
            'items.*.receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'items.*.package_type.required'   => 'نوع الطرد مطلوب.',
            'items.*.package_type.in'         => 'نوع الطرد غير صالح.',
            'items.*.payment_status.required' => 'حالة الدفع مطلوبة لكل طرد.',
            'items.*.payment_status.in'       => 'حالة الدفع المحددة غير صالحة.',
            'items.*.amount.numeric'          => 'يجب أن يكون المبلغ رقماً صحيحاً.',
            'items.*.amount.min'              => 'لا يمكن أن يكون المبلغ بالسالب.',
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
                    $paymentStatus = $item['payment_status'] ?? 'unpaid';
                    $amount = ($paymentStatus === 'paid') ? 0 : ($item['amount'] ?? 0);
                    $header->items()->create([
                        'number'         => $item['number'],
                        'sender_name'    => $item['sender_name'] ?? null,
                        'receiver_name'  => $item['receiver_name'],
                        'receiver_phone' => $item['receiver_phone'],
                        'package_type'   => $item['package_type'],
                        'item_notes'     => $item['item_notes'] ?? null,
                        'payment_status' => $paymentStatus,
                        'amount'         => $amount,
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

    // 3. جلب قائمة الفروع للفلترة
    $branches = Branch::all();

    return view('pages.receipts.index', compact('receipts', 'totalReceipts', 'totalItems', 'fullyDelivered', 'hasPending', 'branches'));
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
            'number'             => ['required', 'string', 'max:255', Rule::unique('receipt_headers')->ignore($id)->where(function ($query) use ($request) {
                return $query->where('source_branch_code', $request->source_branch_code);
            })],
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
            'items.*.payment_status' => ['required', 'in:paid,unpaid'],
            'items.*.amount'         => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.required'              => 'يجب إضافة طرد واحد على الأقل.',
            'items.min'                   => 'يجب إضافة طرد واحد على الأقل.',
            'items.*.number.required'     => 'رقم السند مطلوب لكل طرد.',
            'items.*.number.distinct'     => 'رقم السند مكرر، يجب أن تكون الأرقام مختلفة.',
            'items.*.receiver_name.required'  => 'اسم المستلم مطلوب.',
            'items.*.receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'items.*.package_type.required'   => 'نوع الطرد مطلوب.',
            'items.*.payment_status.required' => 'حالة الدفع مطلوبة لكل طرد.',
            'items.*.amount.numeric'          => 'يجب أن يكون المبلغ رقماً صحيحاً.',
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
                    $paymentStatus = $item['payment_status'] ?? 'unpaid';
                    $amount = ($paymentStatus === 'paid') ? 0 : ($item['amount'] ?? 0);
                    $header->items()->create([
                        'number'         => $item['number'],
                        'sender_name'    => $item['sender_name'] ?? null,
                        'receiver_name'  => $item['receiver_name'],
                        'receiver_phone' => $item['receiver_phone'],
                        'package_type'   => $item['package_type'],
                        'item_notes'     => $item['item_notes'] ?? null,
                        'payment_status' => $paymentStatus,
                        'amount'         => $amount,
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
    public function toggleDelivery(Request $request, $itemId)
    {
        $item = ReceiptItem::findOrFail($itemId);

        // If amount is provided, mark as paid and delivered
        if ($request->has('received_amount')) {
            $request->validate([
                'received_amount' => 'required|numeric|min:0',
            ]);

            $amount = $request->received_amount;
            
            // 1. Update Receipt Item
            $item->update([
                'is_delivered' => true,
                'payment_status' => 'paid',
                'amount' => 0, // Clear the debt/due amount
            ]);

            // 2. Create Transaction (Income) for the current branch
            // Assuming the current user belongs to a branch or we use destination branch
            // Here we use the header's destination branch or fallback to user's branch
            $branchCode = $item->header->destination_branch_code ?? auth()->user()->branch_code;
            
            Transaction::create([
                'receipt_number' => Transaction::generateReceiptNumber('in'),
                'branch_code' => $branchCode,
                'transaction_category_id' => TransactionCategory::where('code', 'SHIPMENT_PAYMENT')->value('id'),
                'amount' => $amount,
                'description' => "تحصيل مبلغ طرد رقم {$item->number} - {$item->sender_name}",
                'created_by' => auth()->id(),
                'reference_number' => $item->number, // Link to item number
            ]);

            // 3. Update Branch Ledger (Debt between branches)
            // Destination Branch (Collector) owes Source Branch (Sender)
            $sourceBranch = $item->header->source_branch_code;
            
            if ($sourceBranch && $sourceBranch !== $branchCode) {
                 // A. Debit Destination (Liability/Payable)
                 \App\Models\BranchLedger::create([
                    'branch_code' => $branchCode,
                    'related_branch_code' => $sourceBranch,
                    'type' => 'delivery_collection',
                    'debit' => $amount,
                    'credit' => 0,
                    'description' => "تحصيل طرد رقم {$item->number} لصالح فرع {$sourceBranch}",
                ]);

                // B. Credit Source (Asset/Receivable)
                \App\Models\BranchLedger::create([
                    'branch_code' => $sourceBranch,
                    'related_branch_code' => $branchCode, // Owed by this branch
                    'type' => 'delivery_collection',
                    'debit' => 0,
                    'credit' => $amount,
                    'description' => "مستحقات تحصيل طرد رقم {$item->number} من فرع {$branchCode}",
                ]);
            }

            return WebResponseClass::sendResponse(
                'تم التسليم والدفع!',
                'تم إضافة المبلغ للصندوق وتحديث حالة الطرد.',
                'حسناً',
            );
        }

        // Standard toggle behavior
        $item->update(['is_delivered' => !$item->is_delivered]);

        $message = $item->is_delivered ? 'تم وضع علامة "تم التسليم"' : 'تم وضع علامة "لم يسلم"';

        return WebResponseClass::sendResponse(
            'تم التحديث!',
            $message,
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
            'payment_status' => ['required', 'in:paid,unpaid'],
            'amount'         => ['nullable', 'numeric', 'min:0'],
        ], [
            'number.required'         => 'رقم السند مطلوب.',
            'receiver_name.required'  => 'اسم المستلم مطلوب.',
            'receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'package_type.required'   => 'نوع الطرد مطلوب.',
            'payment_status.required' => 'حالة الدفع مطلوبة.',
            'amount.numeric'          => 'يجب أن يكون المبلغ رقماً.',
        ]);

        if ($validated['payment_status'] === 'paid') {
            $validated['amount'] = 0;
        }

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
            'payment_status' => ['required', 'in:paid,unpaid'],
            'amount'         => ['nullable', 'numeric', 'min:0'],
        ], [
            'number.required'         => 'رقم السند مطلوب.',
            'receiver_name.required'  => 'اسم المستلم مطلوب.',
            'receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'package_type.required'   => 'نوع الطرد مطلوب.',
            'payment_status.required' => 'حالة الدفع مطلوبة.',
            'amount.numeric'          => 'يجب أن يكون المبلغ رقماً.',
        ]);

        if ($validated['payment_status'] === 'paid') {
            $validated['amount'] = 0;
        }

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

    /**
     * حذف طرد معين
     */
    public function destroyItem($itemId)
    {
        try {
             $item = ReceiptItem::findOrFail($itemId);
             $item->delete();
 
             return WebResponseClass::sendResponse(
                 'تم الحذف!',
                 'تم حذف الطرد بنجاح.',
                 'حسناً',
             );
        } catch (Exception $e) {
             return WebResponseClass::sendExceptionError($e);
        }
    }
}
