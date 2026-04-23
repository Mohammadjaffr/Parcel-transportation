<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::where('is_active', true)->get();
        
        $app = auth()->user()->App ?? null;
        $pendingSubscription = null;
        $activeSubscription = null; 

        if ($app) {
            $pendingSubscription = Subscription::where('app_id', $app->id)
                ->where('status', 'pending')
                ->first();
            $activeSubscription = Subscription::where('app_id', $app->id)
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->first();
        }

        if ($request->isMobile) {
            return view('mobile.pages.pricing.index', compact('packages','pendingSubscription', 'activeSubscription'));
        }
<<<<<<< Updated upstream
        return view('pages.pricing.index', compact('packages','pendingSubscription'));
=======
        return view('mobile.pages.pricing.index', compact('packages','pendingSubscription', 'activeSubscription'));
>>>>>>> Stashed changes
    }
}