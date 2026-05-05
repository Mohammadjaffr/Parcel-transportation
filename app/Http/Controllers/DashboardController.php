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
    $customersWithDebtCount = \App\Models\Customer::query()
    ->select('id')
    // حساب الرصيد الصافي (مجموع credit ناقص مجموع debit)
    ->selectRaw('(
        COALESCE((SELECT SUM(amount) FROM customer_transactions WHERE customer_id = customers.id AND type = "credit"), 0) 
        - 
        COALESCE((SELECT SUM(amount) FROM customer_transactions WHERE customer_id = customers.id AND type = "debit"), 0)
    ) as balance')
    // تصفية العملاء الذين رصيدهم بالسالب (عليهم دين)
    ->having('balance', '<', 0)
    ->get()
    ->count();
    
    // 1. استقبال الفلتر من الرابط (الافتراضي هو 'today' اليوم)
    $period = $request->query('period', 'today');

    // 2. تجهيز الاستعلام الأساسي لطرود فرع الموظف الحالي
    // 2. تجهيز الاستعلام الأساسي لطرود فرع الموظف الحالي (مغلفة بأقواس لحماية المنطق)
    $query = Shipment::where(function ($q) use ($branchId) {
        $q->where('sender_branch_id', $branchId)
          ->orWhere('receiver_branch_id', $branchId);
    });

    // 3. تطبيق فلتر الوقت بذكاء باستخدام Carbon ⏱️
    switch ($period) {
        case 'today':
            $query->whereDate('created_at', Carbon::today());
            $periodName = 'اليوم';
            break;
        case 'this_week':
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            $periodName = 'هذا الأسبوع';
            break;
        case 'this_month':
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
            $periodName = 'هذا الشهر';
            break;
        case 'last_month':
            $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                  ->whereYear('created_at', Carbon::now()->subMonth()->year);
            $periodName = 'الشهر الماضي';
            break;
        case 'all':
            $periodName = 'طوال الوقت';
            break; // لا نضيف فلتر وقت
        default:
            $query->whereDate('created_at', Carbon::today());
            $periodName = 'اليوم';
            break;
    }

    // 4. استخراج الإحصائيات بعد تطبيق فلتر الوقت
    // (استخدمنا clone لكي لا يتأثر الاستعلام الأساسي عند كل عملية عد)
    $stats = [
        'pending'          => (clone $query)->where('status', 'pending')->count(),
        'with_driver'      => (clone $query)->whereIn('status', ['out_for_delivery', 'in_transit'])->count(),
        'delivered'        => (clone $query)->where('status', 'delivered')->count(),
        'returned'         => (clone $query)->whereIn('status', ['returned'])->count(),
    ];

    // 5. جلب آخر 5 تحديثات في الفرع (للقائمة السفلية)
    $latestShipments = Shipment::where('sender_branch_id', $branchId)
                               ->orWhere('receiver_branch_id', $branchId)
                               ->latest()
                               ->take(5)
                               ->get();
if ($request->isMobile) {
     return view('mobile.pages.dashboard.index', compact('stats', 'latestShipments', 'period', 'periodName','customersWithDebtCount'));
}

    return view('pages.dashboard.index', compact('stats', 'latestShipments', 'period', 'periodName', 'customersWithDebtCount'));
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
