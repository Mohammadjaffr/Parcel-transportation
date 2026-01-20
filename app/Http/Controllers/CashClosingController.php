<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterClosing;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashClosingController extends Controller
{
    /**
     * Show the form for creating a new cash closing
     */
    public function create()
    {
        $branchCode = Auth::user()->branch_code;

        // Calculate current system balance
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

        $systemBalance = $income - $expense;

        return view('closings.create', compact('systemBalance'));
    }

    /**
     * Store a newly created cash closing in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'transferred_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $branchCode = Auth::user()->branch_code;

        // Recalculate system balance
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

        $expectedBalance = $income - $expense;
        $actualCash = $validated['actual_cash'];
        $transferredAmount = $validated['transferred_amount'];
        $difference = $actualCash - $expectedBalance;

        // Validate transferred amount doesn't exceed actual cash
        if ($transferredAmount > $actualCash) {
            return back()->withErrors(['transferred_amount' => 'Transferred amount cannot exceed actual cash counted.']);
        }

        $remainingCash = $actualCash - $transferredAmount;

        DB::beginTransaction();

        try {
            // Step 1: Adjust for cash discrepancy
            if ($difference != 0) {
                if ($difference < 0) {
                    // Cash Shortage
                    $category = TransactionCategory::where('code', 'CASH_SHORTAGE')->first();
                    if (!$category) {
                        throw new \Exception('CASH_SHORTAGE category not found. Please run the seeder.');
                    }

                    Transaction::create([
                        'branch_code' => $branchCode,
                        'transaction_category_id' => $category->id,
                        'amount' => abs($difference),
                        'description' => 'Cash shortage identified during daily closing',
                        'created_by' => Auth::id(),
                    ]);
                } elseif ($difference > 0) {
                    // Cash Surplus
                    $category = TransactionCategory::where('code', 'CASH_SURPLUS')->first();
                    if (!$category) {
                        throw new \Exception('CASH_SURPLUS category not found. Please run the seeder.');
                    }

                    Transaction::create([
                        'branch_code' => $branchCode,
                        'transaction_category_id' => $category->id,
                        'amount' => $difference,
                        'description' => 'Cash surplus identified during daily closing',
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            // Step 2: Record HQ Transfer
            if ($transferredAmount > 0) {
                $hqTransferCategory = TransactionCategory::where('code', 'HQ_TRANSFER')->first();
                if (!$hqTransferCategory) {
                    throw new \Exception('HQ_TRANSFER category not found. Please run the seeder.');
                }

                Transaction::create([
                    'branch_code' => $branchCode,
                    'transaction_category_id' => $hqTransferCategory->id,
                    'amount' => $transferredAmount,
                    'description' => 'Cash transfer to headquarters - Daily closing',
                    'created_by' => Auth::id(),
                ]);
            }

            // Step 3: Log the closing
            CashRegisterClosing::create([
                'branch_code' => $branchCode,
                'closed_by' => Auth::id(),
                'expected_balance' => $expectedBalance,
                'actual_cash' => $actualCash,
                'difference' => $difference,
                'transferred_amount' => $transferredAmount,
                'remaining_cash' => $remainingCash,
                'notes' => $validated['notes'],
            ]);

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Cash register closed successfully. Balance transferred to HQ.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error processing closing: ' . $e->getMessage()]);
        }
    }
}
