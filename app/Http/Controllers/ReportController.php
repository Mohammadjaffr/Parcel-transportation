<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use TCPDF;

class ReportController extends Controller
{
    public function revenue(Request $request)
    {
        $query = Shipment::where('payment_method', 'cod');

        // فلاتر البحث
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->branch) {
            $query->where('from_city', $request->branch)
                ->orWhere('to_city', $request->branch);
        }

        $shipments = $query->latest()->paginate(20);

        $totalRevenue = $query->sum('cod_amount');

        $branches = Shipment::select('from_city')->distinct()->pluck('from_city');

        return view('pages.reports.revenue', compact('shipments', 'branches', 'totalRevenue', 'request'));
    }

    public function exportRevenuePDF(Request $request)
{
    $query = Shipment::where('payment_method', 'cod');

    if ($request->from_date) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }
    if ($request->to_date) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }
    if ($request->branch) {
        // تصحيح الاستعلام - استخدم where مع condition group
        $query->where(function($q) use ($request) {
            $q->where('from_city', $request->branch)
              ->orWhere('to_city', $request->branch);
        });
    }

    $shipments = $query->latest()->get();
    $totalRevenue = $shipments->sum('cod_amount');

    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    $pdf->setRTL(true);
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->SetMargins(10, 10, 10); // زيادة الهوامش قليلاً
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // تمرير المتغير $request إلى الـ view
    $html = view('pages.reports.revenue_pdf', compact('shipments', 'totalRevenue', 'request'))->render();

    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');

    return $pdf->Output('تقرير_الإيرادات_' . date('Y-m-d') . '.pdf', 'I');
}
}