<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\Customer;
use App\Models\Shipment;
use App\Services\AdminLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /** عرض عملاء الفرع */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customers = Customer::where('branch_code', $user->branch_code)
            ->latest()
            ->paginate(10);

        return view('pages.customers.index', compact('customers'));
    }

    /** صفحة إضافة عميل */
    public function create()
    {
        return view('pages.customers.create');
    }

    /** تخزين عميل */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branchCode = $user->branch_code;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,NULL,id,branch_code,'.$branchCode,
            'whatsapp_number' => 'nullable|string|max:20',

        ], [
            'name.required' => 'اسم العميل مطلوب',
            'name.max' => 'يجب أن يكون اسم العميل أقل من 255 حرفًا',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique' => 'رقم الهاتف مسجل بالفعل في هذا الفرع',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator)->with('isModalOpen', true);
        }

        try {
            $data = $validator->validated();
            $data['branch_code'] = $branchCode;

            $customer = Customer::create($data);

            // AdminLoggerService::log(
            //     'إنشاء عميل',
            //     'Customer',
            //     $customer->id,
            //     "إنشاء عميل جديد: {$customer->name} - الفرع: {$customer->branch_code}"
            // );

            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم إنشاء العميل بنجاح.',
                'حسناً',
                'customers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /** عرض */
    public function show(Request $request, $id)
    {

        $user = auth()->user();
        $customer = Customer::where('branch_code', $user->branch_code)
            ->with(['transactions' => function ($query) {
                $query->latest();
            }])->findOrFail($id);
        // 1. جلب الشحنات مع الدفعات المرتبطة بها
        $shipments = Shipment::with(['senderBranch', 'receiverBranch', 'payments']) // أضفنا payments هنا
            ->where('sender_customer_id', $id) // الشخص هو المرسل
            ->where('payment_method', 'customer_credit') // نوع الدفع جزئي
            ->get();

        // 2. تعريف متغيرات لحساب الإجماليات لكل الشحنات (الملخص العام)
        $grandTotalCost = 0;      // إجمالي قيمة كل الشحنات
        $grandTotalPaid = 0;      // إجمالي ما دفعه العميل حتى الآن (كم له)
        $grandTotalRemaining = 0; // إجمالي المبلغ المتبقي عليه (كم عليه)
        $unpaidShipmentsCount = 0; //  متغير جديد لعدد الشحنات غير المسددة

        // 3. عمل Loop لحساب المبالغ لكل شحنة على حدة
        foreach ($shipments as $shipment) {
            // ملاحظة: افترضت أن عمود سعر الشحنة اسمه 'total_cost' في جدول shipments
            // يرجى تغيير 'total_cost' إلى اسم العمود الصحيح لديك (مثلاً: price, amount, grand_total)
            $shipmentCost = $shipment->total_amount ?? 0;

            // حساب مجموع الدفعات لهذه الشحنة تحديداً
            // نستخدم دالة sum الخاصة بالـ Collection لجمع عمود amount من جدول payments
            $paidAmount = $shipment->payments->sum('amount');

            // حساب المتبقي (قيمة الشحنة - المدفوع)
            $remaining = $shipmentCost - $paidAmount;

            // سنقوم بتخزين هذه القيم داخل كائن الشحنة نفسه لسهولة عرضها في ملف الـ Blade
            $shipment->calculated_paid = $paidAmount;       // تم الدفع
            $shipment->calculated_remaining = $remaining;   // المتبقي

            // إضافة للأجماليات العامة
            $grandTotalCost += $shipmentCost;
            $grandTotalPaid += $paidAmount;
            $grandTotalRemaining += $remaining;
            if ($remaining > 0) {
                $unpaidShipmentsCount++;
            }
        }
        $shipmentsQuery = Shipment::with(['senderBranch', 'receiverBranch']);
        if ($request->get('direction') == 'sent') {
            // إذا اختار "صادرة": يجب أن يكون هو المرسل فقط
            $shipmentsQuery->where('sender_customer_id', $id);
        } elseif ($request->get('direction') == 'received') {
            // إذا اختار "واردة": يجب أن يكون هو المستلم فقط
            $shipmentsQuery->where('receiver_customer_id', $id);
        } else {
            // الافتراضي (الكل): نجلب الحالتين (مرسل أو مستلم)
            $shipmentsQuery->where(function ($q) use ($id) {
                $q->where('sender_customer_id', $id)
                    ->orWhere('receiver_customer_id', $id);

            });
        }
        if (request()->has('payment_method') && request('payment_method') != 'all') {
            $shipmentsQuery->where('payment_method', request('payment_method'));
        }
        $shipments = $shipmentsQuery->latest()->paginate(10);

        return view('pages.customers.show', compact('customer', 'shipments', 'grandTotalCost', 'grandTotalPaid', 'grandTotalRemaining', 'unpaidShipmentsCount'));
    }

    /** صفحة تعديل */
    public function edit($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customer = Customer::where('branch_code', $user->branch_code)
            ->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($customer);
        }

        return view('pages.customers.edit', compact('customer'));
    }

    /** تحديث */
    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branchCode = $user->branch_code;

        $customer = Customer::where('branch_code', $branchCode)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
        ], [
            'phone.unique' => 'رقم الهاتف مسجل بالفعل في هذا الفرع',
            'name.max' => 'يجب أن يكون اسم العميل أقل من 255 حرفًا',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $old = $customer->toArray();
            $validated = $validator->validated();

            // نحمي branch_code من أي تعديل من الفورم (حتى لو أرسل قيمة)
            unset($validated['branch_code']);

            $customer->update($validated);

            $changes = [];
            foreach ($validated as $key => $value) {
                $oldValue = $old[$key] ?? null;
                if ($oldValue != $value) {
                    $changes[] = "{$key}: ".($oldValue ?? 'فارغ').' → '.($value ?? 'فارغ');
                }
            }

            // AdminLoggerService::log(
            //     'تحديث عميل',
            //     'Customer',
            //     $customer->id,
            //     "تحديث بيانات العميل: {$customer->name}" .
            //         (count($changes) ? "\nالتغييرات: " . implode('، ', $changes) : '')
            // );

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث بيانات العميل بنجاح.',
                'حسناً'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /** حذف */
    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customer = Customer::where('branch_code', $user->branch_code)
            ->findOrFail($id);

        // if ($customer->()->exists()) {
        //     return redirect()->back()
        //         ->with('error', true)
        //         ->with('error_title', 'لا يمكن الحذف!')
        //         ->with('error_message', 'لا يمكن حذف عميل لديه شحنات مرتبطة.')
        //         ->with('error_buttonText', 'حسناً');
        // }

        if ($customer->transactions()->exists()) {
            return WebResponseClass::sendError(
                'لا يمكن حذف عميل لديه حركات مالية.',
                'لا يمكن الحذف!'
            );
        }

        try {
            $customerName = $customer->name;
            $customerId = $customer->id;

            $customer->delete();

            // AdminLoggerService::log(
            //     'حذف عميل',
            //     'Customer',
            //     $customerId,
            //     "حذف العميل: {$customerName}"
            // );

            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف العميل بنجاح.',
                'حسناً',
                'customers.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /** البحث */
    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if (mb_strlen($q) < 1) {
            return response()->json([]);
        }

        $customers = Customer::query()
            ->where('name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers);
    }

    /** تصدير */
    public function export()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $customers = Customer::where('branch_code', $user->branch_code)
            ->latest()
            ->get();

        return view('pages.customers.export', compact('customers'));
    }
}
