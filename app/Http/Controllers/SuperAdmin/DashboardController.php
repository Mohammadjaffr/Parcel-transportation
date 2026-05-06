<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Shipment;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $totalApps   = App::count();
        $activeApps  = App::where('is_active', true)->count();
        $totalShipments = Shipment::count();
        $totalRevenue = Subscription::where('status', 'active')->sum('price_paid');

        $latestApps = App::with('currentSubscription.package')
            ->latest()
            ->take(5)
            ->get();

        return view('SuperAdmin.dashboard.index', compact(
            'totalApps',
            'activeApps',
            'totalShipments',
            'totalRevenue',
            'latestApps'
        ));
    }
}
