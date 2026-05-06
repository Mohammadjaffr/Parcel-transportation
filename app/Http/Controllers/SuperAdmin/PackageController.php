<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

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

    public function toggleStatus(Package $package)
    {
        $package->is_active = !$package->is_active;
        $package->save();

        return response()->json([
            'status'    => 'success',
            'message'   => $package->is_active ? 'تم تفعيل الباقة بنجاح' : 'تم تعطيل الباقة بنجاح',
            'is_active' => (bool) $package->is_active,
        ]);
    }
}
