<?php

namespace App\Http\Controllers;

use App\Models\{
    Shipment,
    Customer,
    Branch,
    CustomerTransaction,
    BranchTransaction
};
use Illuminate\Http\Request;
use TCPDF;

class ReportController extends Controller
{
    /* ===============================
       صفحة التقارير الرئيسية
    =============================== */
    public function index()
    {
        return view('pages.reports.index');
    }

    /* ===============================
       تقارير الشحنات - الجديدة
    =============================== */
    public function shipments(Request $request)
    {
        // فلترة الشحنات
        $query = Shipment::with(['customer', 'fromBranch', 'toBranch', 'driver']);

        // فلترة حسب الفرع
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // فلترة حسب تاريخ البداية
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // فلترة حسب تاريخ النهاية
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $shipments = $query->latest()->paginate(25);
        $branches = Branch::all();

        // إحصائيات
        $totalShipments = $shipments->total();
        $totalCod = $shipments->where('payment_method', 'cod')->sum('cod_amount');
        $totalPrepaid = $shipments->where('payment_method', 'prepaid')->count();

        return view('pages.reports.shipments', compact(
            'shipments',
            'branches',
            'totalShipments',
            'totalCod',
            'totalPrepaid'
        ));
    }

    /* ===============================
       Dashboard التقارير
    =============================== */
    public function dashboard()
    {
        // إجمالي الشحنات
        $shipmentsCount = Shipment::count();

        // إجمالي الإيرادات (COD فقط)
        $revenue = Shipment::where('payment_method', 'cod')->sum('cod_amount');

        // ديون العملاء (مدين - دائن)
        $customersDebit  = CustomerTransaction::where('type', 'debit')->sum('amount');
        $customersCredit = CustomerTransaction::where('type', 'credit')->sum('amount');
        $customersDebt   = $customersDebit - $customersCredit;

        // صافي ديون الفروع
        $branchesNet = 0;
        $branchTransactions = BranchTransaction::all();

        foreach ($branchTransactions as $t) {
            if ($t->type === 'cod') {
                $branchesNet += ($t->to_branch_id ? -$t->amount : $t->amount);
            } else { // settlement
                $branchesNet += ($t->to_branch_id ? $t->amount : -$t->amount);
            }
        }

        return view('pages.reports.dashboard', compact(
            'shipmentsCount',
            'revenue',
            'customersDebt',
            'branchesNet'
        ));
    }

    /* ===============================
       كشف حساب عميل
    =============================== */
    public function customerStatement($id)
    {
        $customer = Customer::findOrFail($id);

        $transactions = CustomerTransaction::where('customer_id', $id)
            ->orderBy('created_at')
            ->get();

        $debit  = $transactions->where('type', 'debit')->sum('amount');
        $credit = $transactions->where('type', 'credit')->sum('amount');
        $balance = $debit - $credit;

        return view('pages.reports.customers.statement', compact(
            'customer',
            'transactions',
            'debit',
            'credit',
            'balance'
        ));
    }

    /* ===============================
       PDF كشف حساب عميل
    =============================== */
    public function customerStatementPDF($id)
    {
        $customer = Customer::findOrFail($id);
        $transactions = CustomerTransaction::where('customer_id', $id)->get();

        $debit  = $transactions->where('type', 'debit')->sum('amount');
        $credit = $transactions->where('type', 'credit')->sum('amount');
        $balance = $debit - $credit;

        $pdf = new TCPDF();
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 11);
        $pdf->AddPage();

        $html = view('pages.reports.customers.statement_pdf', compact(
            'customer',
            'transactions',
            'debit',
            'credit',
            'balance'
        ))->render();

        $pdf->writeHTML($html);
        return $pdf->Output("customer_statement_{$customer->id}.pdf", 'I');
    }

    /* ===============================
       كشف حساب فرع
    =============================== */
    public function branchStatement($id)
    {
        $branch = Branch::findOrFail($id);

        $transactions = BranchTransaction::where('from_branch_id', $id)
            ->orWhere('to_branch_id', $id)
            ->orderBy('created_at')
            ->get();

        $net = 0;

        foreach ($transactions as $t) {
            if ($t->type === 'cod') {
                // COD: المستلم عليه
                $net += ($t->to_branch_id == $id) ? -$t->amount : $t->amount;
            } else {
                // Settlement
                $net += ($t->to_branch_id == $id) ? $t->amount : -$t->amount;
            }
        }

        return view('pages.reports.branches.statement', compact(
            'branch',
            'transactions',
            'net'
        ));
    }

    /* ===============================
       PDF كشف حساب فرع
    =============================== */
    public function branchStatementPDF($id)
    {
        $branch = Branch::findOrFail($id);
        $transactions = BranchTransaction::where('from_branch_id', $id)
            ->orWhere('to_branch_id', $id)
            ->get();

        $net = 0;
        foreach ($transactions as $t) {
            if ($t->type === 'cod') {
                $net += ($t->to_branch_id == $id) ? -$t->amount : $t->amount;
            } else {
                $net += ($t->to_branch_id == $id) ? $t->amount : -$t->amount;
            }
        }

        $pdf = new TCPDF();
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 11);
        $pdf->AddPage();

        $html = view('pages.reports.branches.statement_pdf', compact(
            'branch',
            'transactions',
            'net'
        ))->render();

        $pdf->writeHTML($html);
        return $pdf->Output("branch_statement_{$branch->id}.pdf", 'I');
    }

    /* ===============================
       الإقفال الشهري PDF
    =============================== */
    public function monthlyClosingPDF(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        $shipments = Shipment::whereMonth('created_at', substr($month, 5, 2))
            ->whereYear('created_at', substr($month, 0, 4))
            ->get();

        $revenue = $shipments->where('payment_method', 'cod')->sum('cod_amount');

        $customersDebit  = CustomerTransaction::whereMonth('created_at', substr($month, 5, 2))->sum('amount');

        $pdf = new TCPDF();
        $pdf->setRTL(true);
        $pdf->SetFont('dejavusans', '', 11);
        $pdf->AddPage();

        $html = view('pages.reports.monthly.closing_pdf', compact(
            'month',
            'shipments',
            'revenue',
            'customersDebit'
        ))->render();


        $pdf->writeHTML($html);
        return $pdf->Output("monthly_closing_{$month}.pdf", 'I');
    }
    public function revenue()
{
    $revenue = Shipment::where('payment_method', 'cod')->sum('cod_amount');

    return view('pages.reports.revenue', compact('revenue'));
}

}