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
    public function index(Request $request)
    {
        $branchCode = Auth::user()->branch_code;

        // === Date Range Filter ===
        // Default to current month if no dates provided
        $startDate = $request->input('start_date') 
            ? \Carbon\Carbon::parse($request->input('start_date'))->startOfDay() 
            : now()->startOfMonth();
        
        $endDate = $request->input('end_date') 
            ? \Carbon\Carbon::parse($request->input('end_date'))->endOfDay() 
            : now()->endOfMonth();

        // Fetch transactions for the current branch with date range
        $transactions = Transaction::where('branch_code', $branchCode)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->with(['category', 'user', 'shipment'])
            ->latest()
            ->paginate(15);

        // Calculate balance (Income - Expense) for the date range
        $income = Transaction::where('branch_code', $branchCode)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->whereHas('category', function ($query) {
                $query->where('type', 'in');
            })
            ->sum('amount');

        $expense = Transaction::where('branch_code', $branchCode)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->whereHas('category', function ($query) {
                $query->where('type', 'out');
            })
            ->sum('amount');

        $balance = $income - $expense;

        // === Analytics Data for Charts ===

        // 1. Expenses Breakdown by Category (Filtered by date range)
        $expensesByCategory = Transaction::where('branch_code', $branchCode)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->whereHas('category', function ($query) {
                $query->where('type', 'out');
            })
            ->join('transaction_categories', 'transactions.transaction_category_id', '=', 'transaction_categories.id')
            ->selectRaw('transaction_categories.name as category_name, SUM(transactions.amount) as total')
            ->groupBy('transaction_categories.name')
            ->get();


        // 2. Daily Trend for the selected date range
        $dailyTrend = Transaction::where('branch_code', $branchCode)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->join('transaction_categories', 'transactions.transaction_category_id', '=', 'transaction_categories.id')
            ->selectRaw('DATE(transactions.created_at) as date, 
                         transaction_categories.type,
                         SUM(transactions.amount) as total')
            ->groupBy('date', 'transaction_categories.type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        // Get active categories for the modal
        $categories = TransactionCategory::active()->get();

        return view('transactions.index', compact(
            'transactions', 
            'balance', 
            'income', 
            'expense', 
            'expensesByCategory', 
            'dailyTrend',
            'startDate',
            'endDate',
            'categories'
        ));
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
