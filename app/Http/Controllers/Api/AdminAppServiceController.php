<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\App;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class AdminAppServiceController extends Controller
{
    /**
     * تفعيل أو إيقاف خدمة معينة لشركة (مكتب) محدد
     * * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleService(Request $request)
    {
        $request->validate([
            'app_id'     => 'required|exists:apps,id',
            'service_id' => 'required|exists:services,id',
            'is_active'  => 'required|boolean',
        ], [
            'app_id.exists'     => 'الشركة المحددة غير موجودة.',
            'service_id.exists' => 'الخدمة المحددة غير موجودة.',
            'is_active.boolean' => 'حالة التفعيل يجب أن تكون true أو false.'
        ]);

        $app = App::findOrFail($request->app_id);
        $service = Service::findOrFail($request->service_id);

        try {
            $app->services()->syncWithoutDetaching([
                $service->id => ['is_active' => $request->is_active]
            ]);

            Cache::forget('app_services_' . $app->id);
            
            $statusText = $request->is_active ? 'تفعيل' : 'إيقاف';

            return response()->json([
                'status'  => 'success',
                'message' => "تم {$statusText} خدمة '{$service->name}' للشركة '{$app->name}' بنجاح.",
                'data'    => [
                    'app_id'     => $app->id,
                    'app_name'   => $app->name,
                    'service_id' => $service->id,
                    'service'    => $service->name,
                    'is_active'  => (bool) $request->is_active
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ أثناء تحديث حالة الخدمة.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تفعيل أو إيقاف خدمة بشكل كلي على مستوى النظام بأكمله (Global Kill Switch)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleGlobalService(Request $request)
    {
        
        $request->validate([
            'service_id'       => 'required|exists:services,id',
            'is_global_active' => 'required|boolean',
        ], [
            'service_id.exists' => 'الخدمة المحددة غير موجودة.',
            'is_global_active.boolean' => 'الحالة يجب أن تكون true أو false.'
        ]);

        try {
            $service = Service::findOrFail($request->service_id);
            $service->update([
                'is_global_active' => $request->is_global_active
            ]);

            $appIds = App::pluck('id'); 
            foreach ($appIds as $appId) {
                Cache::forget('app_services_' . $appId);
            }

            $statusText = $request->is_global_active ? 'تفعيل' : 'إيقاف';

            return response()->json([
                'status'  => 'success',
                'message' => "تم {$statusText} الخدمة '{$service->name}' على مستوى النظام بالكامل بنجاح.",
                'data'    => [
                    'service_id'       => $service->id,
                    'service_name'     => $service->name,
                    'is_global_active' => (bool) $request->is_global_active
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ أثناء تحديث حالة الخدمة العالمية.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * الاستعلام عن جميع خدمات مكتب/شركة محددة وحالتها
     * @param int $app_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppServices($app_id)
    {
        try {
            
            $app = App::with('services')->findOrFail($app_id);

            $servicesList = $app->services->map(function ($service) {
                
                $isGlobalActive = (bool) $service->is_global_active;
                $isLocalActive  = (bool) $service->pivot->is_active;
                
                return [
                    'service_id'       => $service->id,
                    'name'             => $service->name,
                    'slug'             => $service->slug,
                    'description'      => $service->description,
                    'is_global_active' => $isGlobalActive,
                    'is_local_active'  => $isLocalActive,  
                    'is_usable'        => ($isGlobalActive && $isLocalActive) 
                ];
            });

            return response()->json([
                'status'  => 'success',
                'message' => "تم جلب بيانات الخدمات للشركة '{$app->name}' بنجاح.",
                'data'    => [
                    'app_id'   => $app->id,
                    'app_name' => $app->name,
                    'services' => $servicesList
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'الشركة المحددة غير موجودة في النظام.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'حدث خطأ أثناء جلب البيانات.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
