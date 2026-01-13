<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchTransaction;
use App\Classes\WebResponseClass;
use Illuminate\Http\Request;

class BranchFinanceController extends Controller
{
    /**
     * صفحة: ملخص كل الفروع
     */
    public function index()
    {
        $currentBranchCode = auth()->user()->branch_code;

        $counterparties = $this->calculateBranchCounterparties($currentBranchCode);

        $branchesSummary = collect($counterparties)->map(function ($row) {
            $summary = $this->calculateBranchSummary($row['branch']->code);

            return [
                'branch'           => $row['branch'],
                'total_cod'        => $summary['total_cod'],
                'total_settle_in'  => $summary['total_settle_in'],
                'total_settle_out' => $summary['total_settle_out'],
                'net_balance'      => $row['net'],
            ];
        });

        return view('pages.finance.branches.index', compact('branchesSummary'));
    }

    /**
     * صفحة: تقرير تفصيلي لفرع محدد
     */
    public function show($branchCode)
    {
        $branch = Branch::where('code', $branchCode)->firstOrFail();

        $summary = $this->calculateBranchSummary($branchCode);

        $transactions = BranchTransaction::with(['fromBranch', 'toBranch', 'shipment'])
            ->where(function ($q) use ($branchCode) {
                $q->where('sender_branch_code', $branchCode)
                    ->orWhere('receiver_branch_code', $branchCode);
            })
            ->latest()
            ->paginate(20);

        $sentCod = BranchTransaction::with(['toBranch', 'shipment'])
            ->where('type', 'cod')
            ->where('sender_branch_code', $branchCode)
            ->latest()
            ->get();

        $receivedCod = BranchTransaction::with(['fromBranch', 'shipment'])
            ->where('type', 'cod')
            ->where('receiver_branch_code', $branchCode)
            ->latest()
            ->get();

        $settlements = BranchTransaction::with(['fromBranch', 'toBranch'])
            ->where('type', 'settlement')
            ->where(function ($q) use ($branchCode) {
                $q->where('sender_branch_code', $branchCode)
                    ->orWhere('receiver_branch_code', $branchCode);
            })
            ->latest()
            ->get();

        $byCounterparty = $this->calculateBranchCounterparties($branchCode);

        return view('pages.finance.branches.show', compact(
            'branch',
            'summary',
            'transactions',
            'byCounterparty',
            'sentCod',
            'receivedCod',
            'settlements'
        ));
    }

    /**
     * صفحة فورم التسوية بين فرعين
     */
    public function createSettlement(Request $request)
    {
        $currentBranchCode = $request->query('branch_code', auth()->user()->branch_code);

        $counterparties = $this->calculateBranchCounterparties($currentBranchCode);

        $branchesOwed = array_filter($counterparties, function ($item) {
            return $item['net'] < 0;
        });

        if (empty($branchesOwed)) {
            return back()->with('error', 'لا توجد مبالغ متبقية لتصفينها على هذا الفرع.');
        }

        return view('pages.finance.settlements.create', [
            'branchesOwed'    => $branchesOwed,
            'currentBranchId' => $currentBranchCode,
        ]);
    }

    /**
     * تخزين تسوية بين فرعين
     */
    public function storeSettlement(Request $request)
    {
        $data = $request->validate([
            'sender_branch_code'   => 'required|different:receiver_branch_code|exists:branches,code',
            'receiver_branch_code' => 'required|exists:branches,code',
            'amount'               => 'required|numeric|min:0.01',
            'description'          => 'nullable|string|max:255',
        ]);

        $currentBranchId = $data['sender_branch_code'];
        $counterparties  = $this->calculateBranchCounterparties($currentBranchId);

        if (! isset($counterparties[$data['receiver_branch_code']])) {
            return back()->withErrors(['receiver_branch_code' => 'لا يوجد تعامل مالي بين الفرعين.']);
        }

        $net = $counterparties[$data['receiver_branch_code']]['net'];

        if ($net >= 0) {
            return back()->withErrors(['receiver_branch_code' => 'ليس عليك أي مبلغ لهذا الفرع.']);
        }

        $maxAmount = abs($net);

        if ($data['amount'] > $maxAmount) {
            return back()->withErrors([
                'amount' => 'لا يمكنك دفع أكثر من مبلغ الدين: ' . number_format($maxAmount, 2) . ' ر.ي',
            ]);
        }

        BranchTransaction::create([
            'shipment_id'          => null,
            'sender_branch_code'   => $data['sender_branch_code'],
            'receiver_branch_code' => $data['receiver_branch_code'],
            'amount'               => $data['amount'],
            'type'                 => 'settlement',
            'description'          => $data['description'] ?? 'تصفية يدوية بين الفروع',
        ]);

        return WebResponseClass::sendResponse(
            'تمت عمل التسوية!',
            'تم عمل التسوية بنجاح.',
            'حسناً',
            'finance.branches.show',
            ['branch' => $data['sender_branch_code']]
        );
    }

    /**
     * API: إرجاع الرصيد النهائي لفرع (مقابل جميع الفروع)
     */
    public function apiBranchBalance($branchId)
    {
        $summary = $this->calculateBranchSummary($branchId);

        return response()->json([
            'branch_id'        => (int) $branchId,
            'net_balance'      => $summary['net_balance'],
            'total_cod'        => $summary['total_cod'],
            'total_settle_in'  => $summary['total_settle_in'],
            'total_settle_out' => $summary['total_settle_out'],
        ]);
    }

    /**
     * حساب ملخص فرع واحد (COD + تسويات)
     */
    private function calculateBranchSummary($branchId)
    {
        $transactions = BranchTransaction::where('sender_branch_code', $branchId)
            ->orWhere('receiver_branch_code', $branchId)
            ->get();

        $totalCod       = 0;
        $totalSettleIn  = 0;
        $totalSettleOut = 0;
        $net            = 0;

        foreach ($transactions as $t) {

            if ($t->type === 'cod') {

                if ($t->sender_branch_code == $branchId) {
                    $net -= $t->amount;
                }

                if ($t->receiver_branch_code == $branchId) {
                    $net      += $t->amount;
                    $totalCod += $t->amount;
                }
            } elseif ($t->type === 'settlement') {

                if ($t->sender_branch_code == $branchId) {
                    $net            += $t->amount;
                    $totalSettleOut += $t->amount;
                }

                if ($t->receiver_branch_code == $branchId) {
                    $net           -= $t->amount;
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
     * ملخص الفرع مقابل كل الفروع الأخرى
     */
    private function calculateBranchCounterparties($branchId)
    {
        $transactions = BranchTransaction::with(['fromBranch', 'toBranch'])
            ->where('sender_branch_code', $branchId)
            ->orWhere('receiver_branch_code', $branchId)
            ->get();

        $result = [];

        foreach ($transactions as $t) {
            $otherId = $t->sender_branch_code == $branchId
                ? $t->receiver_branch_code
                : $t->sender_branch_code;

            if (! isset($result[$otherId])) {
                $result[$otherId] = [
                    'branch' => $t->sender_branch_code == $branchId ? $t->toBranch : $t->fromBranch,
                    'net'    => 0,
                ];
            }

            if ($t->type === 'cod') {

                if ($t->sender_branch_code == $branchId) {
                    $result[$otherId]['net'] -= $t->amount;
                } else {
                    $result[$otherId]['net'] += $t->amount;
                }
            } elseif ($t->type === 'settlement') {

                if ($t->sender_branch_code == $branchId) {
                    $result[$otherId]['net'] += $t->amount;
                } else {
                    $result[$otherId]['net'] -= $t->amount;
                }
            }
        }

        return $result;
    }

    // ====== رسائل مساعدة ======


}
