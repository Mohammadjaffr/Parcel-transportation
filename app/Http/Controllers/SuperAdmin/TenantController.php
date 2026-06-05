<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $apps = App::withCount(['branches', 'users'])
            ->with('currentSubscription.package')
            ->latest()
            ->paginate(15);

        return view('SuperAdmin.offices.index', compact('apps'));
    }

 public function show(App $app)
{
    $app->load([
        'currentSubscription.package',
        'services',
    ]);

    $app->loadCount([
        'branches',
        'users',
        'drivers',
    ]);

    $services = Service::orderBy('name')->get();

    $subscription = $app->currentSubscription;
    $package = $subscription?->package;

    $remainingDays = 0;
    $shipmentsCount = 0;
    $packagesCount = 0;

    if ($subscription && $subscription->starts_at && $subscription->ends_at) {
        $remainingDays = (int) ceil(now()->diffInDays($subscription->ends_at, false));
        $remainingDays = max($remainingDays, 0);

        $shipmentsCount = $app->shipments()
            ->whereBetween('shipments.created_at', [
                $subscription->starts_at,
                $subscription->ends_at,
            ])
            ->count();

        $packagesCount = $app->shipmentPackages()
            ->whereBetween('shipment_packages.created_at', [
                $subscription->starts_at,
                $subscription->ends_at,
            ])
            ->count();
    }

    $usage = [
        'branches' => [
            'used'  => (int) $app->branches_count,
            'limit' => (int) ($package?->max_branches ?? 0),
        ],

        'drivers' => [
            'used'  => (int) ($app->drivers_count ?? $app->users_count),
            'limit' => (int) ($package?->max_drivers ?? 0),
        ],

        'shipments' => [
            'used'  => (int) $shipmentsCount,
            'limit' => (int) ($package?->max_shipments ?? 0),
        ],

        'packages' => [
            'used'  => (int) $packagesCount,
            'limit' => (int) ($package?->max_packages ?? 0),
        ],
    ];

    return view('SuperAdmin.offices.show', compact(
        'app',
        'services',
        'subscription',
        'package',
        'remainingDays',
        'shipmentsCount',
        'packagesCount',
        'usage'
    ));
}
    public function toggleStatus(App $app)
    {
        $app->update([
            'is_active' => !$app->is_active,
        ]);

        return response()->json([
            'status'    => 'success',
            'message'   => $app->is_active ? 'تم تفعيل المكتب بنجاح' : 'تم تعطيل المكتب بنجاح',
            'is_active' => (bool) $app->is_active,
        ]);
    }

    public function toggleService(Request $request, App $app)
    {
        $validated = $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'is_active'  => 'required|boolean',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $app->services()->syncWithoutDetaching([
            $service->id => [
                'is_active' => (bool) $validated['is_active'],
            ],
        ]);

        Cache::forget('app_services_' . $app->id);

        return response()->json([
            'status'  => 'success',
            'message' => $validated['is_active']
                ? "تم تفعيل خدمة {$service->name} بنجاح"
                : "تم تعطيل خدمة {$service->name} بنجاح",
        ]);
    }
    public function toggleAllServices(Request $request, App $app)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $isActive = (bool) $validated['is_active'];

        $serviceIds = Service::pluck('id')->toArray();

        if (empty($serviceIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا توجد خدمات متاحة في النظام.',
            ], 422);
        }

        $syncData = [];

        foreach ($serviceIds as $serviceId) {
            $syncData[$serviceId] = [
                'is_active' => $isActive,
            ];
        }

        $app->services()->syncWithoutDetaching($syncData);

        Cache::forget('app_services_' . $app->id);

        return response()->json([
            'status' => 'success',
            'message' => $isActive
                ? 'تم تفعيل جميع الخدمات بنجاح'
                : 'تم تعطيل جميع الخدمات بنجاح',
            'is_active' => $isActive,
        ]);
    }

    public function resetPassword(Request $request, App $app)
    {
        $request->validate([
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::where('app_id', $app->id)
            ->where('type', 'admin')
            ->first();

        if (!$user) {
            $user = User::where('app_id', $app->id)->first();
        }

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لم يتم العثور على أي مستخدم لهذا المكتب لتغيير كلمة المرور الخاصة به.',
            ], 404);
        }

        $newPassword = $request->password;
        if (empty($newPassword)) {
            $newPassword = Str::random(8);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return response()->json([
            'status'   => 'success',
            'message'  => "تم إعادة تعيين كلمة مرور المسؤول ({$user->name}) بنجاح.",
            'password' => $newPassword,
        ]);
    }
}
