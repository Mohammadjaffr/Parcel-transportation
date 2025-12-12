<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchTransaction;
use Illuminate\Http\Request;

class BranchFinanceController extends Controller
{
  public function index()
{
    $currentBranchId = auth()->user()->branch_id;

    $counterparties = $this->calculateBranchCounterparties($currentBranchId);

    $branchesSummary = collect($counterparties)->map(function ($row) use ($currentBranchId) {
        $summary = $this->calculateBranchSummary($row['branch']->id);

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
    public function show($branchId)
    {
        $branch = Branch::findOrFail($branchId);

        $summary = $this->calculateBranchSummary($branchId);

        // كل الحركات المتعلقة بهذا الفرع
        $transactions = BranchTransaction::with(['fromBranch', 'toBranch', 'shipment'])
            ->where('from_branch_id', $branchId)
            ->orWhere('to_branch_id', $branchId)
            ->latest()
            ->paginate(20);

        // ملخص حسب الفرع المقابل
        $byCounterparty = $this->calculateBranchCounterparties($branchId);

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
    public function createSettlement()
    {
        $currentBranchId = auth()->user()->branch_id;

        // احسب صافي التعامل مع كل الفروع
        $counterparties = $this->calculateBranchCounterparties($currentBranchId);

        // الفروع التي على الفرع الحالي دين لها
        $branchesOwed = array_filter($counterparties, function ($item) {
            return $item['net'] < 0; // سالب = الفرع الحالي عليه مبلغ
        });

        if (empty($branchesOwed)) {
            return back()->with('error', 'لا توجد مبالغ متبقية لتسويتها على هذا الفرع.');
        }

        return view('pages.finance.settlements.create', [
            'branchesOwed' => $branchesOwed,
            'currentBranchId' => $currentBranchId
        ]);
    }


    /**
     * تخزين تسوية بين فرعين
     */
    public function storeSettlement(Request $request)
    {
        $data = $request->validate([
            'from_branch_id' => 'required|different:to_branch_id|exists:branches,id',
            'to_branch_id'   => 'required|exists:branches,id',
            'amount'         => 'required|numeric|min:0.01',
            'description'    => 'nullable|string|max:255',
        ]);

        // ============================
        // 1) تحقق من أن الفرع الحالي عليه دين للفرع الآخر
        // ============================
        $currentBranchId = $data['from_branch_id'];
        $counterparties = $this->calculateBranchCounterparties($currentBranchId);

        if (!isset($counterparties[$data['to_branch_id']])) {
            return back()->withErrors(['to_branch_id' => 'لا يوجد تعامل مالي بين الفرعين.']);
        }

        $net = $counterparties[$data['to_branch_id']]['net'];

        if ($net >= 0) {
            return back()->withErrors(['to_branch_id' => 'ليس عليك أي مبلغ لهذا الفرع.']);
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
            'from_branch_id' => $data['from_branch_id'], // الفرع الدافع
            'to_branch_id'   => $data['to_branch_id'],   // الفرع المستلم
            'amount'         => $data['amount'],
            'type'           => 'settlement',
            'description'    => $data['description'] ?? 'تسوية يدوية بين الفروع',
        ]);

        return redirect()->route('finance.branches.show', $data['from_branch_id'])
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
        $transactions = BranchTransaction::where('from_branch_id', $branchId)
            ->orWhere('to_branch_id', $branchId)
            ->get();

        $totalCod = 0;
        $totalSettleIn = 0;  // تسويات داخلة (استلم)
        $totalSettleOut = 0; // تسويات خارجة (دفع)

        $net = 0; // موجب = للفرع، سالب = عليه

        foreach ($transactions as $t) {
            if ($t->type === 'cod') {

    // هذا الفرع عليه المبلغ
    if ($t->from_branch_id == $branchId) {
        $net -= $t->amount;
    }

    // هذا الفرع له المبلغ
    if ($t->to_branch_id == $branchId) {
        $net += $t->amount;
        $totalCod += $t->amount;
    }


            } elseif ($t->type === 'settlement') {
                // تسوية:
                // إذا هذا الفرع دافع (from) => دفع للآخر => يقل ما للفرع / يزيد ما عليه
                if ($t->from_branch_id == $branchId) {
                    $net -= $t->amount;
                    $totalSettleOut += $t->amount;
                }
                // إذا هذا الفرع مستلم (to) => استلم => يزيد ما له
                if ($t->to_branch_id == $branchId) {
                    $net += $t->amount;
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
            ->where('from_branch_id', $branchId)
            ->orWhere('to_branch_id', $branchId)
            ->get();

        $result = [];

        foreach ($transactions as $t) {
            $otherId = $t->from_branch_id == $branchId ? $t->to_branch_id : $t->from_branch_id;

            if (!isset($result[$otherId])) {
                $result[$otherId] = [
                    'branch' => $t->from_branch_id == $branchId ? $t->toBranch : $t->fromBranch,
                    'net'    => 0,
                ];
            }

            // نفس منطق الحساب: موجب = الآخر مديون لهذا الفرع
            if ($t->type === 'cod') {
                if ($t->from_branch_id == $branchId) {
                    $result[$otherId]['net'] += $t->amount;
                } else {
                    $result[$otherId]['net'] -= $t->amount;
                }
            } elseif ($t->type === 'settlement') {

                // الفرع الحالي دفع → يقل الدين
                if ($t->from_branch_id == $branchId) {
                    $result[$otherId]['net'] += $t->amount;
                }

                // الفرع الحالي استلم → يقل المبلغ له
                else {
                    $result[$otherId]['net'] -= $t->amount;
                }
            }
        }

        return $result;
    }
}