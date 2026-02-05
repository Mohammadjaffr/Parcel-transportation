<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Classes\WebResponseClass;
use App\Models\TransactionCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

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

        // Type filter (in = income, out = expense)
        $typeFilter = $request->input('type');

        // Category filter
        $categoryFilter = $request->input('category_id');

        // Build transactions query
        $transactionsQuery = Transaction::where('branch_code', $branchCode)
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->with(['category', 'user', 'shipment']);

        // Apply type filter if provided
        if ($typeFilter) {
            $transactionsQuery->whereHas('category', function ($query) use ($typeFilter) {
                $query->where('type', $typeFilter);
            });
        }

        // Apply category filter if provided
        if ($categoryFilter) {
            $transactionsQuery->where('transaction_category_id', $categoryFilter);
        }

        $transactions = $transactionsQuery->latest()->paginate(15);

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

        // System balance for closing modal
        $systemBalance = $balance;

        return view('transactions.index', compact(
            'transactions',
            'balance',
            'income',
            'expense',
            'expensesByCategory',
            'dailyTrend',
            'startDate',
            'endDate',
            'categories',
            'systemBalance'
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
        $validator = Validator::make($request->all(), [
            'transaction_category_id' => 'required|exists:transaction_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:50',
            'attachment' => 'nullable|image|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('transaction_modal_open', true);
        }

        $validated = $validator->validated();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->imageService->saveImage($request->file('attachment'), 'transactions');
        }

        // Get the transaction category to determine type (in/out)
        $category = TransactionCategory::findOrFail($validated['transaction_category_id']);
        if ($category->type === 'out'){
            $branchCode = Auth::user()->branch_code;
            // حساب مجموع الداخل (Income)
            $totalIn = Transaction::where('branch_code', $branchCode)
                ->whereHas('category', function ($q) {
                    $q->where('type', 'in');
                })->sum('amount');
            $totalOut = Transaction::where('branch_code', $branchCode)
                ->whereHas('category', function ($q) {
                    $q->where('type', 'out');
                })->sum('amount');
            $currentBalance = $totalIn - $totalOut;
            if ($currentBalance < $validated['amount']) {
                return back()
                    ->withErrors([
                        'amount' => 'عذراً، رصيد الصندوق الحالي (' . number_format($currentBalance, 2) . ') لا يكفي لإتمام عملية الصرف هذه.'
                    ])
                    ->withInput()
                    ->with('transaction_modal_open', true);
            }
        }

        // Generate unique receipt number based on transaction type
        $receiptNumber = Transaction::generateReceiptNumber($category->type);

        // Create transaction for the current branch
        Transaction::create([
            'receipt_number' => $receiptNumber,
            'branch_code' => Auth::user()->branch_code,
            'transaction_category_id' => $validated['transaction_category_id'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'reference_number' => $validated['reference_number'] ?? null,
            'attachment_path' => $attachmentPath,
            'created_by' => Auth::id(),
        ]);

        return WebResponseClass::sendResponse(
            'تمت الإضافة!',
            'تم إضافة المعاملة بنجاح.',
            'حسناً',
            'transactions.index'
        );
    }

    /**
     * Generate PDF Receipt for a transaction
     */
    public function generateReceipt($id)
    {
        $user = Auth::user();

        // Get transaction with relationships
        $transaction = Transaction::with(['category', 'branch', 'customer', 'user', 'shipment'])
            ->where('branch_code', $user->branch_code)
            ->findOrFail($id);

        // Initialize TCPDF
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8');

        // PDF Configuration
        $pdf->SetCreator('نظام إدارة الشحنات');
        $pdf->SetAuthor($user->name);
        $pdf->SetTitle('سند رقم ' . $transaction->id);
        $pdf->SetSubject(($transaction->category && $transaction->category->type == 'in') ? 'سند قبض' : 'سند صرف');

        // RTL Configuration  
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 10);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Margins
        $pdf->SetMargins(15, 15, 15);

        // Add Page
        $pdf->AddPage();

        // Render View
        $html = view('pdf.transaction-receipt', [
            'transaction' => $transaction,
        ])->render();

        // Write HTML to PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF to browser
        $filename = 'receipt_' . $transaction->id . '.pdf';
        return $pdf->Output($filename, 'I'); // 'I' = inline display in browser
    }
}
