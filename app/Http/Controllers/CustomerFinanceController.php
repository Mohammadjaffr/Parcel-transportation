<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\WebResponseClass;

class CustomerFinanceController extends Controller
{
    /**
     * عرض قائمة العملاء مع أرصدتهم.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branchCode = $user->branch_code;

        // استعلام فرعي لحساب الرصيد لكل عميل ديناميكياً
        // (Debit - Credit)
        $balanceSubquery = CustomerTransaction::selectRaw("COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE -amount END), 0)")
            ->whereColumn('customer_id', 'customers.id');

        // بناء الاستعلام الأساسي
        $query = Customer::where('branch_code', $branchCode)
            ->select('customers.*')
            ->addSelect(['balance' => $balanceSubquery]);

        // تطبيق البحث إذا وجد
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // جلب البيانات مع الترتيب (الأعلى مديونية أولاً)
        $customers = $query->orderByDesc('balance') // المديونية (الموجب) تظهر أولاً
            ->paginate(20)
            ->withQueryString(); // للحفاظ على البحث عند الانتقال للصفحة التالية

        // حساب إجمالي المديونيات للفرع (صافي المديونية)
        $totalReceivables = CustomerTransaction::whereHas('customer', function($q) use($branchCode) {
                $q->where('branch_code', $branchCode);
            })
            ->selectRaw('SUM(CASE WHEN type = "debit" THEN amount ELSE -amount END) as balance')
            ->value('balance');

        return view('pages.finance.customers.index', compact('customers', 'totalReceivables'));
    }

    /**
     * عرض نموذج التسوية.
     */
    public function createSettlement(Customer $customer)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($customer->branch_code !== $user->branch_code) {
           abort(403);
        }

        // حساب الرصيد باستخدام SQL بدلاً من تحميل كل العمليات (أسرع)
        $balance = $customer->transactions()
            ->selectRaw("SUM(CASE WHEN type = 'debit' THEN amount ELSE -amount END) as total")
            ->value('total') ?? 0;

        return view('pages.finance.customers.settle', compact('customer', 'balance'));
    }

    /**
     * حفظ عملية التسوية.
     */
    public function storeSettlement(Request $request, Customer $customer)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($customer->branch_code !== $user->branch_code) {
           abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes'  => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $customer) {
            CustomerTransaction::create([
                'customer_id' => $customer->id,
                'amount'      => $request->amount,
                'type'        => 'credit', // التسوية دائماً دائن (سداد)
                'description' => $request->notes ?? 'تسوية حساب / دفعة نقدية',
                'created_at'  => now(),
            ]);
        });

        return WebResponseClass::sendResponse(
            'تم التسجيل',
            'تم تسجيل عملية التسوية بنجاح.',
            'حسناً',
            'finance.customers.index'
        );
    }
    /**
     * عرض تفاصيل العميل وكشف الحساب
     */
    public function show(Customer $customer)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // التحقق من أن العميل يتبع نفس فرع المستخدم
        if ($customer->branch_code !== $user->branch_code) {
            abort(403);
        }

        // جلب الحركات مع تقسيم الصفحات (Pagination)
        $transactions = $customer->transactions()
            ->latest()
            ->paginate(15);

        // حساب الإجماليات باستخدام قاعدة البيانات مباشرة للأداء العالي
        $totals = $customer->transactions()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END), 0) as total_debit,
                COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END), 0) as total_credit
            ")
            ->first();

        // 1. إجمالي المديونية (كم أخذ)
        $totalDebit = $totals->total_debit; 
        
        // 2. إجمالي السداد (كم دفع)
        $totalCredit = $totals->total_credit; 
        
        // 3. صافي الرصيد (الفرق)
        $currentBalance = $totalDebit - $totalCredit; 

        return view('pages.finance.customers.show', compact(
            'customer', 
            'transactions', 
            'totalDebit', 
            'totalCredit', 
            'currentBalance'
        ));
    }
}