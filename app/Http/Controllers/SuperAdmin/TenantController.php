<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $app->loadCount(['branches', 'users']);
        $app->load(['currentSubscription.package', 'services']);

        $services = Service::all();

        return view('SuperAdmin.offices.show', compact('app', 'services'));
    }

    public function toggleStatus(App $app)
    {
        $app->is_active = !$app->is_active;
        $app->save();

        return response()->json([
            'status'    => 'success',
            'message'   => $app->is_active ? 'تم تفعيل المكتب بنجاح' : 'تم تعطيل المكتب بنجاح',
            'is_active' => (bool) $app->is_active,
        ]);
    }

    public function toggleService(Request $request, App $app)
    {
        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
            'is_active'  => 'required|boolean',
        ]);

        $serviceId = $request->input('service_id');
        $isActive  = $request->boolean('is_active');
        $service   = Service::findOrFail($serviceId);

        $app->services()->syncWithoutDetaching([
            $serviceId => ['is_active' => $isActive],
        ]);

        // Clear service cache for this app
        Cache::forget('app_services_' . $app->id);

        return response()->json([
            'status'  => 'success',
            'message' => $isActive
                ? "تم تفعيل خدمة {$service->name} بنجاح"
                : "تم تعطيل خدمة {$service->name} بنجاح",
        ]);
    }
}
