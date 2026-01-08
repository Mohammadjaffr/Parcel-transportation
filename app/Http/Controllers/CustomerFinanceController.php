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
     * Display a listing of customers with their balances.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $branchCode = $user->branch_code;

        $balanceSubquery = CustomerTransaction::selectRaw("COALESCE(SUM(CASE WHEN type = 'debit' THEN amount ELSE -amount END), 0)")
            ->whereColumn('customer_id', 'customers.id');

        $customers = Customer::where('branch_code', $branchCode)
            ->select('customers.*')  
            ->addSelect(['balance' => $balanceSubquery]) 
            ->latest()
            ->paginate(20);

        $totalReceivables = CustomerTransaction::whereHas('customer', function($q) use($branchCode) {
                $q->where('branch_code', $branchCode);
            })
            ->selectRaw('SUM(CASE WHEN type = "debit" THEN amount ELSE -amount END) as balance')
            ->value('balance');

        return view('pages.finance.customers.index', compact('customers', 'totalReceivables'));
    }

    /**
     * Show the form for settling a customer account.
     */
    public function createSettlement(Customer $customer)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($customer->branch_code !== $user->branch_code) {
           abort(403);
        }
        $debit = $customer->transactions()->where('type', 'debit')->sum('amount');
        $credit = $customer->transactions()->where('type', 'credit')->sum('amount');
        $balance = $debit - $credit;

        return view('pages.finance.customers.settle', compact('customer', 'balance'));
    }

    /**
     * Store a settlement transaction.
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

        CustomerTransaction::create([
            'customer_id' => $customer->id,
            'amount'      => $request->amount,
            'type'        => 'credit', 
            'description' => $request->notes ?? 'تسوية حساب / دفعة نقدية',
        ]);

        return WebResponseClass::sendResponse(
            'تم تسجيل الدفعة!',
            'تم تسجيل الدفعة بنجاح.',
            'حسناً',
            'finance.customers.index'
        );
    }
}
