<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function index()
    {
        $salesData = $this->getRealSalesData();
        
        return view('pages.dashboard.index', [
            'monthlySales' => $salesData,
            'chartTitle' => 'المبيعات الشهرية الحقيقية'
        ]);
        // return view('');
    }
    private function getRealSalesData()
    {
        // هنا تجلب البيانات الحقيقية من قاعدة البيانات
        // مثال:
        return [
            'january' => 1250,
            'february' => 1320,
            'march' => 1180,
            'april' => 1400,
            'may' => 1350,
            'june' => 1280,
            'july' => 1450,
            'august' => 1380,
            'september' => 1420,
            'october' => 1500,
            'november' => 1480,
            'december' => 1600
        ];

        // أو من قاعدة البيانات:
        // return Sale::selectRaw('MONTH(sale_date) as month, SUM(amount) as total')
        //     ->whereYear('sale_date', date('Y'))
        //     ->groupBy('month')
        //     ->pluck('total', 'month')
        //     ->toArray();
    }
}