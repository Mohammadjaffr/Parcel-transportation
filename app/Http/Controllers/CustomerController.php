<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\AdminLoggerService;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /** عرض عملاء الفرع */
    public function index()
    {
        $customers = Customer::where('branch_code', auth()->user()->branch_code)
            ->latest()
            ->paginate(15);

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
        $branchCode = auth()->user()->branch_code;

        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'phone'           => 'required|string|max:20|unique:customers,phone,NULL,id,branch_code,' . $branchCode,
            'whatsapp_number' => 'nullable|string|max:20',

        ], [
            'name.required'  => 'اسم العميل مطلوب',
            'name.max'       => 'يجب أن يكون اسم العميل أقل من 255 حرفًا',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.unique'   => 'رقم الهاتف مسجل بالفعل في هذا الفرع',
        ]);

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        try {
            $data = $validator->validated();
            $data['branch_code'] = $branchCode;

            $customer =  Customer::create($data);

            // AdminLoggerService::log(
            //     'إنشاء عميل',
            //     'Customer',
            //     $customer->id,
            //     "إنشاء عميل جديد: {$customer->name} - الفرع: {$customer->branch_code}"
            // );

            return $this->SuccessBacktoIndex('تمت الإضافة!', 'تم إنشاء العميل بنجاح.');
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /** عرض */
    public function show($id)
    {
        $customer = Customer::where('branch_code', auth()->user()->branch_code)
            ->with(['transactions' => function ($query) {
                $query->latest();
            }])
            ->findOrFail($id);

        $transactions = $customer->transactions()->latest()->paginate(20);

        $debit  = $customer->transactions()->where('type', 'debit')->sum('amount');
        $credit = $customer->transactions()->where('type', 'credit')->sum('amount');
        $balance = $debit - $credit;

        return view('pages.customers.show', compact('customer', 'transactions', 'debit', 'credit', 'balance'));
    }

    /** صفحة تعديل */
    public function edit($id)
    {
        $customer = Customer::where('branch_code', auth()->user()->branch_code)
            ->findOrFail($id);

        return view('pages.customers.edit', compact('customer'));
    }

    /** تحديث */
    public function update(Request $request, $id)
    {
        $branchCode = auth()->user()->branch_code;

        $customer = Customer::where('branch_code', $branchCode)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
        ], [
            'phone.unique' => 'رقم الهاتف مسجل بالفعل في هذا الفرع',
            'name.max'     => 'يجب أن يكون اسم العميل أقل من 255 حرفًا',
        ]);

        if ($validator->fails()) {
            return $this->ValidationError($validator);
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
                    $changes[] = "{$key}: " . ($oldValue ?? 'فارغ') . " → " . ($value ?? 'فارغ');
                }
            }

            AdminLoggerService::log(
                'تحديث عميل',
                'Customer',
                $customer->id,
                "تحديث بيانات العميل: {$customer->name}" .
                    (count($changes) ? "\nالتغييرات: " . implode('، ', $changes) : '')
            );

            return $this->SuccessBacktoIndex('تم التحديث!', 'تم تحديث بيانات العميل بنجاح.');
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /** حذف */
    public function destroy($id)
    {
        $customer = Customer::where('branch_code', auth()->user()->branch_code)
            ->findOrFail($id);

        // if ($customer->()->exists()) {
        //     return redirect()->back()
        //         ->with('error', true)
        //         ->with('error_title', 'لا يمكن الحذف!')
        //         ->with('error_message', 'لا يمكن حذف عميل لديه شحنات مرتبطة.')
        //         ->with('error_buttonText', 'حسناً');
        // }

        if ($customer->transactions()->exists()) {
            return redirect()->back()
                ->with('error', true)
                ->with('error_title', 'لا يمكن الحذف!')
                ->with('error_message', 'لا يمكن حذف عميل لديه حركات مالية.')
                ->with('error_buttonText', 'حسناً');
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

            return redirect()->route('customers.index')
                ->with('success', true)
                ->with('success_title', 'تم الحذف!')
                ->with('success_message', 'تم حذف العميل بنجاح.')
                ->with('success_buttonText', 'حسناً');
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
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
        ->get(['id','name','phone']);

    return response()->json($customers);
}


    /** تصدير */
    public function export()
    {
        $customers = Customer::where('branch_code', auth()->user()->branch_code)
            ->latest()
            ->get();

        return view('pages.customers.export', compact('customers'));
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
        return redirect()->route('customers.index')
            ->with('success', true)
            ->with('success_title', $title)
            ->with('success_message', $msg)
            ->with('success_buttonText', 'حسناً');
    }

    private function ExceptionError($e)
    {
        \Log::error('Customer Controller Error: ' . $e->getMessage(), [
            'exception' => $e,
            'user_id'   => auth()->id(),
            'branch_code' => auth()->user()->branch_code ?? null,
        ]);

        $message = app()->environment('production')
            ? 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.'
            : $e->getMessage();

        return redirect()->back()
            ->with('error', true)
            ->with('error_title', 'خطأ غير متوقع!')
            ->with('error_message', $message)
            ->with('error_buttonText', 'حسناً')
            ->withInput();
    }
}