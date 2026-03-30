<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // إحصائيات أعلى الصفحة
        $todayShipments = Shipment::whereDate('created_at', today())->count();
        $inTransit = Shipment::where('status', 'in_transit')->count();
        $delivered = Shipment::where('status', 'delivered')->count();

        // الإيرادات COD المحصّلة
        $revenueCOD = Shipment::where('payment_method', 'cod')
            ->where('status', 'delivered')
            ->sum('total_amount');

        // رسوم الشهور (للمخطط البياني)
        $monthlySales = Shipment::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('payment_method', 'cod')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // آخر 10 شحنات خلال آخر 24 ساعة
        $last24Shipments = Shipment::where('created_at', '>=', now()->subHours(24))
            ->latest()
            ->take(10)
            ->get();
        if ($request->isMobile) {
            return view('mobile.pages.dashboard.index');
        }
        return view('pages.dashboard.index', compact(
            'todayShipments',
            'inTransit',
            'delivered',
            'revenueCOD',
            'monthlySales',
            'last24Shipments'
        ));
    }
}
