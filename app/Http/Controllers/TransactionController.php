<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions with balance calculation and analytics
     */
    public function index()
    {
        $branchCode = Auth::user()->branch_code;

        // Fetch transactions for the current branch
        $transactions = Transaction::where('branch_code', $branchCode)
            ->with(['category', 'user', 'shipment'])
            ->latest()
            ->paginate(15);

        // Calculate balance (Income - Expense)
        $income = Transaction::where('branch_code', $branchCode)
            ->whereHas('category', function ($query) {
                $query->where('type', 'in');
            })
            ->sum('amount');

        $expense = Transaction::where('branch_code', $branchCode)
            ->whereHas('category', function ($query) {
                $query->where('type', 'out');
            })
            ->sum('amount');

        $balance = $income - $expense;

        // === Analytics Data for Charts ===

        // 1. Expenses Breakdown by Category
        $expensesByCategory = Transaction::where('branch_code', $branchCode)
            ->whereHas('category', function ($query) {
                $query->where('type', 'out');
            })
            ->join('transaction_categories', 'transactions.transaction_category_id', '=', 'transaction_categories.id')
            ->selectRaw('transaction_categories.name as category_name, SUM(transactions.amount) as total')
            ->groupBy('transaction_categories.name')
            ->get();

        // 2. Daily Trend for Current Month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $dailyTrend = Transaction::where('branch_code', $branchCode)
            ->whereBetween('transactions.created_at', [$startOfMonth, $endOfMonth])
            ->join('transaction_categories', 'transactions.transaction_category_id', '=', 'transaction_categories.id')
            ->selectRaw('DATE(transactions.created_at) as date, 
                         transaction_categories.type,
                         SUM(transactions.amount) as total')
            ->groupBy('date', 'transaction_categories.type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        return view('transactions.index', compact('transactions', 'balance', 'income', 'expense', 'expensesByCategory', 'dailyTrend'));
    }

    /**
     * Show the form for creating a new transaction
     */
    public function create()
    {
        $categories = TransactionCategory::active()->get();

        return view('transactions.create', compact('categories'));
    }

    /**
     * Store a newly created transaction in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_category_id' => 'required|exists:transaction_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
        ]);

        // Create transaction for the current branch
        Transaction::create([
            'branch_code' => Auth::user()->branch_code,
            'transaction_category_id' => $validated['transaction_category_id'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }
}
