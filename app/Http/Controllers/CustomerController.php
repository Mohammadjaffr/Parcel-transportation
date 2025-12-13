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
        $customers = Customer::where('branch_id', auth()->user()->branch_id)
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
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:20|unique:customers,phone,NULL,id,branch_id,' . auth()->user()->branch_id,
            'type'         => 'required|in:individual,company',
            'credit_limit' => 'nullable|numeric|min:0',
        ], [
            'phone.unique' => 'رقم الهاتف مسجل بالفعل في هذا الفرع',
            'name.max' => 'يجب أن يكون اسم العميل أقل من 255 حرفًا',
            'credit_limit.min' => 'يجب أن يكون حد الائتمان أكبر من أو يساوي صفر',
            'type.in' => 'يجب أن يكون النوع إما فرد أو شركة',
        ]);

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        try {
            $data = $validator->validated();
            $data['branch_id'] = auth()->user()->branch_id;

            $customer = Customer::create($data);

            AdminLoggerService::log(
                'إنشاء عميل',
                'Customer',
                $customer->id,
                "إنشاء عميل جديد: {$customer->name} - النوع: " . ($customer->type == 'individual' ? 'فرد' : 'شركة')
            );

            return $this->SuccessBacktoIndex(
                'تمت الإضافة!',
                'تم إنشاء العميل بنجاح.'
            );
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /** عرض كشف حساب عميل */
    public function show($id)
    {
        $customer = Customer::where('branch_id', auth()->user()->branch_id)
            ->with(['transactions' => function($query) {
                $query->latest();
            }])
            ->findOrFail($id);

        $transactions = $customer->transactions()->latest()->paginate(20);

        $debit = $customer->transactions()->where('type', 'debit')->sum('amount');
        $credit = $customer->transactions()->where('type', 'credit')->sum('amount');
        $balance = $debit - $credit;

        return view('pages.customers.show', compact(
            'customer',
            'transactions',
            'debit',
            'credit',
            'balance'
        ));
    }

    /** صفحة تعديل */
    public function edit($id)
    {
        $customer = Customer::where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        return view('pages.customers.edit', compact('customer'));
    }

    /** تحديث */
    public function update(Request $request, $id)
    {
        $customer = Customer::where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'phone'        => 'required|string|max:20|unique:customers,phone,' . $id . ',id,branch_id,' . auth()->user()->branch_id,
            'type'         => 'required|in:individual,company',
            'credit_limit' => 'nullable|numeric|min:0',
        ], [
            'phone.unique' => 'رقم الهاتف مسجل بالفعل في هذا الفرع',
            'name.max' => 'يجب أن يكون اسم العميل أقل من 255 حرفًا',
            'credit_limit.min' => 'يجب أن يكون حد الائتمان أكبر من أو يساوي صفر',
            'type.in' => 'يجب أن يكون النوع إما فرد أو شركة',
        ]);

        if ($validator->fails()) {
            return $this->ValidationError($validator);
        }

        try {
            $oldData = $customer->toArray();
            $customer->update($validator->validated());

            // تسجيل التغييرات
            $changes = [];
            foreach ($validator->validated() as $key => $value) {
                if (isset($oldData[$key]) && $oldData[$key] != $value) {
                    $oldValue = $oldData[$key] ?? 'فارغ';
                    $newValue = $value ?? 'فارغ';
                    $changes[] = "$key: $oldValue → $newValue";
                }
            }

            $typeArabic = $customer->type == 'individual' ? 'فرد' : 'شركة';
            AdminLoggerService::log(
                'تحديث عميل',
                'Customer',
                $customer->id,
                "تحديث بيانات العميل: {$customer->name}" . 
                (count($changes) > 0 ? "\nالتغييرات: " . implode('، ', $changes) : '') .
                "\nالنوع: $typeArabic"
            );

            return $this->SuccessBacktoIndex(
                'تم التحديث!',
                'تم تحديث بيانات العميل بنجاح.'
            );
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /** حذف */
    public function destroy($id)
    {
        $customer = Customer::where('branch_id', auth()->user()->branch_id)
            ->findOrFail($id);

        // التحقق إذا كان للعميل شحنات مرتبطة
        if ($customer->shipments()->exists()) {
            return redirect()->back()
                ->with('error', true)
                ->with('error_title', 'لا يمكن الحذف!')
                ->with('error_message', 'لا يمكن حذف عميل لديه شحنات مرتبطة.')
                ->with('error_buttonText', 'حسناً');
        }

        // التحقق إذا كان للعميل حركات مالية
        if ($customer->transactions()->exists()) {
            return redirect()->back()
                ->with('error', true)
                ->with('error_title', 'لا يمكن الحذف!')
                ->with('error_message', 'لا يمكن حذف عميل لديه حركات مالية.')
                ->with('error_buttonText', 'حسناً');
        }

        try {
            $customerName = $customer->name;
            $customer->delete();

            AdminLoggerService::log(
                'حذف عميل',
                'Customer',
                $customer->id,
                "حذف العميل: $customerName"
            );

            return redirect()->route('customers.index')
                ->with('success', true)
                ->with('success_title', 'تم الحذف!')
                ->with('success_message', 'تم حذف العميل بنجاح.')
                ->with('success_buttonText', 'حسناً');
        } catch (\Exception $e) {
            return $this->ExceptionError($e);
        }
    }

    /** البحث عن العملاء */
    public function search(Request $request)
    {
        $search = $request->get('search');

        $customers = Customer::where('branch_id', auth()->user()->branch_id)
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('pages.customers.index', compact('customers', 'search'));
    }

    /** تصدير العملاء */
    public function export()
    {
        $customers = Customer::where('branch_id', auth()->user()->branch_id)
            ->latest()
            ->get();

        // هنا يمكن إضافة منطق التصدير لـ Excel أو PDF
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
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch_id ?? null,
        ]);

        $message = app()->environment('production') 
            ? 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.'
            : $e->getMessage();

        return redirect()->back()
            ->with('error', true)
            ->with('error_title', 'خطأ غير متوقع!')
            ->with('error_message', $message)
            ->with('error_buttonText', 'حسناً');
    }
}