<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index(Request $request)
{
    $user = auth()->user();
    $branchId = $user->branch_id;

    // ========================================================
    // 1. إحصائيات العملاء (ديون وأرصدة) - استعلام واحد صاروخي ⚡
    // ========================================================
    // بدلاً من جلب البيانات للرام، نجعل SQL يحسبها بالكامل ويُرجع رقمين فقط!
    $customerStats = \Illuminate\Support\Facades\DB::table(function ($query) use ($branchId) {
        $query->selectRaw('SUM(CASE WHEN type = "credit" THEN amount ELSE -amount END) as balance')
              ->from('customer_transactions')
              ->join('customers', 'customers.id', '=', 'customer_transactions.customer_id')
              ->where('customers.branch_id', $branchId)
              ->groupBy('customer_transactions.customer_id');
    }, 'balances')
    ->selectRaw('
        COUNT(CASE WHEN balance < 0 THEN 1 END) as debtors,
        COUNT(CASE WHEN balance > 0 THEN 1 END) as creditors
    ')->first();

    $debtorsCount = $customerStats->debtors ?? 0;
    $creditorsCount = $customerStats->creditors ?? 0;


    // ========================================================
    // 2. إحصائيات الركاب
    // ========================================================
    $pendingPassengersCount = \App\Models\Passengers::where('branch_id', $branchId)
        ->where('status', 'pending')
        ->count();


    // ========================================================
    // 3. تجهيز استعلام الطرود (مع فلتر الوقت)
    // ========================================================
    $period = $request->query('period', 'today');

    $query = \App\Models\Shipment::where(function ($q) use ($branchId) {
        $q->where('sender_branch_id', $branchId)
          ->orWhere('receiver_branch_id', $branchId);
    });

    switch ($period) {
        case 'today':
            $query->whereDate('created_at', \Carbon\Carbon::today());
            $periodName = 'اليوم';
            break;
        case 'this_week':
            $query->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
            $periodName = 'هذا الأسبوع';
            break;
        case 'this_month':
            $query->whereMonth('created_at', \Carbon\Carbon::now()->month)
                  ->whereYear('created_at', \Carbon\Carbon::now()->year);
            $periodName = 'هذا الشهر';
            break;
        case 'last_month':
            $query->whereMonth('created_at', \Carbon\Carbon::now()->subMonth()->month)
                  ->whereYear('created_at', \Carbon\Carbon::now()->subMonth()->year);
            $periodName = 'الشهر الماضي';
            break;
        case 'all':
            $periodName = 'طوال الوقت';
            break; 
        default:
            $query->whereDate('created_at', \Carbon\Carbon::today());
            $periodName = 'اليوم';
            break;
    }

    // ========================================================
    // 4. إحصائيات الطرود - استعلام واحد فقط ⚡
    // ========================================================
    // بدلاً من 4 استعلامات (clone)، نستخدم استعلام واحد يجلب كل الحالات!
    $shipmentStats = (clone $query)->selectRaw('
        COUNT(CASE WHEN status = "delivered" THEN 1 END) as delivered,
        COUNT(CASE WHEN status IN ("returned") THEN 1 END) as returned,
        COUNT(CASE WHEN status = "pending" THEN 1 END) as pending,
        COUNT(CASE WHEN status IN ("out_for_delivery", "in_transit") THEN 1 END) as with_driver
    ')->first();

    $stats = [
        'delivered'   => $shipmentStats->delivered ?? 0,
        'returned'    => $shipmentStats->returned ?? 0,
        'pending'     => $shipmentStats->pending ?? 0,
        'with_driver' => $shipmentStats->with_driver ?? 0,
    ];

    // ========================================================
    // 5. أحدث النشاطات في الفرع
    // ========================================================
    $latestShipments = \App\Models\Shipment::where(function ($q) use ($branchId) {
            $q->where('sender_branch_id', $branchId)
              ->orWhere('receiver_branch_id', $branchId);
        })
        ->latest()
        ->take(5)
        ->get();

    // ========================================================
    // 6. توجيه البيانات
    // ========================================================
    $view = $request->isMobile ? 'mobile.pages.dashboard.index' : 'pages.dashboard.index';
    
    return view($view, compact(
        'stats', 'latestShipments', 'period', 'periodName', 
        'debtorsCount', 'creditorsCount', 'pendingPassengersCount'
    ));
}
    // public function index(Request $request)
    // {
    //     // إحصائيات أعلى الصفحة
    //     $todayShipments = Shipment::whereDate('created_at', today())->count();
    //     $inTransit = Shipment::where('status', 'in_transit')->count();
    //     $delivered = Shipment::where('status', 'delivered')->count();

    //     // الإيرادات COD المحصّلة
    //     $revenueCOD = Shipment::where('payment_method', 'cod')
    //         ->where('status', 'delivered')
    //         ->sum('total_amount');

    //     // رسوم الشهور (للمخطط البياني)
    //     $monthlySales = Shipment::select(
    //         DB::raw('MONTH(created_at) as month'),
    //         DB::raw('SUM(total_amount) as total')
    //     )
    //         ->where('payment_method', 'cod')
    //         ->groupBy('month')
    //         ->orderBy('month')
    //         ->pluck('total', 'month')
    //         ->toArray();

    //     // آخر 10 شحنات خلال آخر 24 ساعة
    //     $last24Shipments = Shipment::where('created_at', '>=', now()->subHours(24))
    //         ->latest()
    //         ->take(10)
    //         ->get();
    //     if ($request->isMobile) {
    //         return view('mobile.pages.dashboard.index');
    //     }
    //     return view('pages.dashboard.index', compact(
    //         'todayShipments',
    //         'inTransit',
    //         'delivered',
    //         'revenueCOD',
    //         'monthlySales',
    //         'last24Shipments'
    //     ));
    // }
}
