<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;
class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('subscriptions')
            ->latest()
            ->get();

        return view('SuperAdmin.packages.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'price'            => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'max_branches'     => 'required|integer|min:1',
            'max_drivers'      => 'required|integer|min:1',
            'max_shipments'    => 'required|integer|min:1',
            'max_packages'     => 'required|integer|min:1',
        ]);

        $validated['is_active'] = true;

        $package = Package::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم إنشاء الباقة بنجاح',
            'package' => $package,
        ]);
    }
 public function update(Request $request, Package $package)
{
    $validated = $request->validate([
        'name'             => 'required|string|max:255',
        'price'            => 'required|numeric|min:0',
        'duration_in_days' => 'required|integer|min:1',
        'max_branches'     => 'required|integer|min:1',
        'max_drivers'      => 'required|integer|min:1',
        'max_shipments'    => 'required|integer|min:1',
        'max_packages'     => 'required|integer|min:1',
    ]);

    $package->update($validated);

    $appIds = Subscription::where('package_id', $package->id)
        ->pluck('app_id')
        ->filter()
        ->unique();

    foreach ($appIds as $appId) {
        Cache::forget('app_services_' . $appId);
        Cache::forget('app_limits_' . $appId);
        Cache::forget('app_subscription_' . $appId);
        Cache::forget('subscription_limits_' . $appId);
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'تم تعديل الباقة وتحديث بيانات المكاتب المرتبطة بها بنجاح',
        'package' => $package->fresh(),
    ]);
}
    public function toggleStatus(Package $package)
    {
        $package->update([
            'is_active' => !$package->is_active,
        ]);

        return response()->json([
            'status'    => 'success',
            'message'   => $package->is_active ? 'تم تفعيل الباقة بنجاح' : 'تم تعطيل الباقة بنجاح',
            'is_active' => (bool) $package->is_active,
        ]);
    }
}