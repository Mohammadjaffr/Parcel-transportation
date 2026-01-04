<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchTransaction;
use Illuminate\Http\Request;

class BranchFinanceController extends Controller
{
  public function index()
{
    $currentBranchCode = auth()->user()->branch_code;

    $counterparties = $this->calculateBranchCounterparties($currentBranchCode);

    $branchesSummary = collect($counterparties)->map(function ($row) use ($currentBranchCode) {
        $summary = $this->calculateBranchSummary($row['branch']->code);

        return [
            'branch' => $row['branch'],
            'total_cod' => $summary['total_cod'],
            'total_settle_in' => $summary['total_settle_in'],
            'total_settle_out' => $summary['total_settle_out'],
            'net_balance' => $row['net'],
        ];
    });

    return view('pages.finance.branches.index', compact('branchesSummary'));
}


    /**
     * صفحة: تقرير تفصيلي لفرع محدد
     */
    public function show($branchCode)
    {
        $branch = Branch::findOrFail($branchCode);

        $summary = $this->calculateBranchSummary($branchCode);

        // كل الحركات المتعلقة بهذا الفرع
        $transactions = BranchTransaction::with(['fromBranch', 'toBranch', 'shipment'])
            ->where('sender_branch_code', $branchCode)
            ->orWhere('receiver_branch_code', $branchCode)
            ->latest()
            ->paginate(20);

        // ملخص حسب الفرع المقابل
        $byCounterparty = $this->calculateBranchCounterparties($branchCode);

        return view('pages.finance.branches.show', compact(
            'branch',
            'summary',
            'transactions',
            'byCounterparty'
        ));
    }

    /**
     * صفحة فورم التسوية بين فرعين
     */
    public function createSettlement(Request $request)
    {
        $currentBranchCode = $request->query('branch_code', auth()->user()->branch_code);

        // احسب صافي التعامل مع كل الفروع
        $counterparties = $this->calculateBranchCounterparties($currentBranchCode);

        // الفروع التي على الفرع الحالي دين لها (Liability -> Net < 0)
        $branchesOwed = array_filter($counterparties, function ($item) {
            return $item['net'] < 0; 
        });

        if (empty($branchesOwed)) {
            return back()->with('error', 'لا توجد مبالغ متبقية لتصفينها على هذا الفرع.');
        }

        return view('pages.finance.settlements.create', [
            'branchesOwed' => $branchesOwed,
            'currentBranchId' => $currentBranchCode
        ]);
    }


    /**
     * تخزين تسوية بين فرعين
     */
    public function storeSettlement(Request $request)
    {
        $data = $request->validate([
            'sender_branch_code' => 'required|different:receiver_branch_code|exists:branches,code',
            'receiver_branch_code'   => 'required|exists:branches,code',
            'amount'         => 'required|numeric|min:0.01',
            'description'    => 'nullable|string|max:255',
        ]);

        // ============================
        // 1) تحقق من أن الفرع الحالي عليه دين للفرع الآخر
        // ============================
        $currentBranchId = $data['sender_branch_code'];
        $counterparties = $this->calculateBranchCounterparties($currentBranchId);

        if (!isset($counterparties[$data['receiver_branch_code']])) {
            return back()->withErrors(['receiver_branch_code' => 'لا يوجد تعامل مالي بين الفرعين.']);
        }

        $net = $counterparties[$data['receiver_branch_code']]['net'];

        // Liability is Negative. So if Net >= 0, I don't owe them.
        if ($net >= 0) {
            return back()->withErrors(['receiver_branch_code' => 'ليس عليك أي مبلغ لهذا الفرع.']);
        }

        $maxAmount = abs($net);

        if ($data['amount'] > $maxAmount) {
            return back()->withErrors(['amount' => "لا يمكنك دفع أكثر من مبلغ الدين: " . number_format($maxAmount, 2) . " ر.ي"]);
        }

        // ============================
        // 2) إنشاء قيد التسوية (بعد نجاح التحقق)
        // ============================
        BranchTransaction::create([
            'shipment_id'    => null,
            'sender_branch_code' => $data['sender_branch_code'], // الفرع الدافع
            'receiver_branch_code'   => $data['receiver_branch_code'],   // الفرع المستلم
            'amount'         => $data['amount'],
            'type'           => 'settlement',
            'description'    => $data['description'] ?? 'تصفية يدوية بين الفروع',
        ]);

        return redirect()->route('finance.branches.show', $data['sender_branch_code'])
            ->with('success', 'تم تسجيل التسوية المالية بنجاح.');
    }

    /**
     * API: إرجاع الرصيد النهائي لفرع (مقابل جميع الفروع)
     */
    public function apiBranchBalance($branchId)
    {
        $summary = $this->calculateBranchSummary($branchId);

        return response()->json([
            'branch_id'   => (int) $branchId,
            'net_balance' => $summary['net_balance'],
            'total_cod'   => $summary['total_cod'],
            'total_settle_in'  => $summary['total_settle_in'],
            'total_settle_out' => $summary['total_settle_out'],
        ]);
    }

    /**
     * حساب ملخص فرع واحد (إجمالي COD و تسويات + الرصيد النهائي)
     */
    private function calculateBranchSummary($branchId)
    {
        $transactions = BranchTransaction::where('sender_branch_code', $branchId)
            ->orWhere('receiver_branch_code', $branchId)
            ->get();

        $totalCod = 0;
        $totalSettleIn = 0;  // تسويات داخلة (استلم)
        $totalSettleOut = 0; // تسويات خارجة (دفع)

        $net = 0; // موجب = للفرع، سالب = عليه

        foreach ($transactions as $t) {
            if ($t->type === 'cod') {

    // هذا الفرع عليه المبلغ
    if ($t->sender_branch_code == $branchId) {
        $net -= $t->amount;
    }

    // هذا الفرع له المبلغ
    if ($t->receiver_branch_code == $branchId) {
        $net += $t->amount;
        $totalCod += $t->amount;
    }


            } elseif ($t->type === 'settlement') {
                // تسوية:
                // إذا هذا الفرع دافع (from) -> دفع للآخر -> يقل ما عليه (Liability decreases -> Net increases towards 0)
                if ($t->sender_branch_code == $branchId) {
                    $net += $t->amount;
                    $totalSettleOut += $t->amount;
                }
                // إذا هذا الفرع مستلم (to) -> استلم -> يقل ما له (Asset decreases -> Net decreases towards 0)
                if ($t->receiver_branch_code == $branchId) {
                    $net -= $t->amount;
                    $totalSettleIn += $t->amount;
                }
            }
        }

        return [
            'total_cod'        => $totalCod,
            'total_settle_in'  => $totalSettleIn,
            'total_settle_out' => $totalSettleOut,
            'net_balance'      => $net,
        ];
    }

    /**
     * ملخص فرع مقابل كل الفروع الأخرى (للجدول في صفحة show)
     */
    private function calculateBranchCounterparties($branchId)
    {
        $transactions = BranchTransaction::with(['fromBranch', 'toBranch'])
            ->where('sender_branch_code', $branchId)
            ->orWhere('receiver_branch_code', $branchId)
            ->get();

        $result = [];

        foreach ($transactions as $t) {
            $otherId = $t->sender_branch_code == $branchId ? $t->receiver_branch_code : $t->sender_branch_code;

            if (!isset($result[$otherId])) {
                $result[$otherId] = [
                    'branch' => $t->sender_branch_code == $branchId ? $t->toBranch : $t->fromBranch,
                    'net'    => 0,
                ];
            }

            // Standard Convention:
            // Net > 0: Asset (They Owe Me)
            // Net < 0: Liability (I Owe Them)

            if ($t->type === 'cod') {
                // Sender = Collector (Debtor). Receiver = Sent Goods (Creditor).
                
                if ($t->sender_branch_code == $branchId) {
                    // I Collected -> I Owe -> Liability -> Decrease Net
                    $result[$otherId]['net'] -= $t->amount;
                } else {
                    // They Collected -> They Owe Me -> Asset -> Increase Net
                    $result[$otherId]['net'] += $t->amount;
                }
            } elseif ($t->type === 'settlement') {
                // Sender = Payer. Receiver = Payee.

                if ($t->sender_branch_code == $branchId) {
                    // I Paid -> My Liability Decreases (Net Increases toward 0) or Prepaid Asset Increases 
                    $result[$otherId]['net'] += $t->amount;
                } else {
                    // They Paid -> My Asset Decreases (Net Decreases toward 0) or Liability Increases
                    $result[$otherId]['net'] -= $t->amount;
                }
            }
        }

        return $result;
    }
}