<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchLedger;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Classes\WebResponseClass;

class FinanceSettlementController extends Controller
{
    /**
     * Store a new branch settlement transaction.
     */
    public function store(Request $request)
    {
        // 1. التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'branch_code' => 'required|exists:branches,code',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:in,out', // in=قبض، out=صرف
            'payment_method' => 'required|in:cash,bank_transfer',
            'reference_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        
        // فرعك أنت (الذي بيده الكاش)
        $myBranchCode = Auth::user()->branch_code; 
        
        // الفرع الآخر (الذي عليه الدين)
        $targetBranchCode = $validated['branch_code'];
        if ($validated['type'] === 'out') {
            // حساب مجموع المقبوضات (In)
            $totalIn = Transaction::where('branch_code', $myBranchCode)
                ->whereHas('category', function ($q) {
                    $q->where('type', 'in');
                })->sum('amount');

            // حساب مجموع المصروفات (Out)
            $totalOut = Transaction::where('branch_code', $myBranchCode)
                ->whereHas('category', function ($q) {
                    $q->where('type', 'out');
                })->sum('amount');

            $currentBalance = $totalIn - $totalOut;

            // إذا الرصيد أقل من المبلغ المراد صرفه -> أوقف العملية
            if ($currentBalance < $validated['amount']) {
                return WebResponseClass::sendError('عذراً، رصيد الصندوق الحالي (' . number_format($currentBalance, 2) . ') لا يكفي لإتمام عملية التسوية.');
            }
        }

        DB::beginTransaction();

        try {
            // ====================================================
            // 🛠️ إصلاح 1: ضمان التصنيف الصحيح للصندوق
            // ====================================================
            $categoryName = $validated['type'] == 'in' ? 'تصفية حسابات (قبض)' : 'تصفية حسابات (صرف)';
            
            // نبحث عن التصنيف أو ننشئه مع إجبار النوع (type) أن يكون صحيحاً
            $category = TransactionCategory::firstOrCreate(
                [
                    'code' => 'SETTLEMENT_' . strtoupper($validated['type']) // نعتمد على الكود لمنع التكرار
                ],
                [
                    'name' => $categoryName,
                    'type' => $validated['type'], // مهم جداً لحساب الصندوق
                    'is_active' => true
                ]
            );

            // ====================================================
            // 🛠️ إصلاح 2: التأثير على الصندوق (Transaction)
            // ====================================================
            // المبلغ يدخل/يخرج من صندوقك "أنت"
            $awad=Transaction::create([
                'branch_code' => Auth::user()->branch_code, // ✅ صندوقي أنا
                'transaction_category_id' => $category->id,
                'amount' => $validated['amount'],
                'description' => "تصفية حساب الفرع: $targetBranchCode - " . ($validated['notes'] ?? ''),
                'reference_number' => $validated['reference_number'],
                'created_by' => Auth::id(),
            ]);
            // dd($awad);

            // ====================================================
            // 🛠️ إصلاح 3: تصفير الدين (Branch Ledger)
            // ====================================================
            // 🛑 هنا التغيير الجوهري: القيد يسجل باسم الفرع المستهدف
            
            $ledgerEntry = [
                'branch_code' => $targetBranchCode, // ✅ نذهب لصفحة الفرع المستهدف
                'related_branch_code' => $myBranchCode, // الطرف الثاني هو أنا
                'type' => 'settlement',
                'description' => "تسوية مالية (رقم القيد: $categoryName)",
                'debit' => 0,
                'credit' => 0,
            ];

            if ($validated['type'] === 'in') {
                // (قبض) in: أخذنا منه فلوس -> يعني سدد الدين
                // نسجل له (Credit) لينقص الرصيد الذي عليه
                $ledgerEntry['credit'] = $validated['amount'];
            } else {
                // (صرف) out: أعطيناه فلوس -> يعني سددنا له
                // نسجل عليه (Debit) لينقص الرصيد الذي له
                $ledgerEntry['debit'] = $validated['amount'];
            }

            BranchLedger::create($ledgerEntry);

            DB::commit();

            return back()->with('success', 'تمت التسوية بنجاح: تم تحديث الصندوق وتصفير رصيد الفرع.');

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}
