<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSubscriptionController extends Controller
{
    /**
     * تفعيل اشتراك شركة محددة
     * * @param Request $request
     * @param mixed $id (ID الرقمي أو UUID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Request $request, $id)
    {
        $subscription = Subscription::where('id', $id)->firstOrFail();

        if ($subscription->status === 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'هذا الاشتراك مفعل ونشط بالفعل.'
            ], 400);
        }

        try {
            DB::beginTransaction();
            $duration = $subscription->package->duration_in_days ?? 30;

            $subscription->update([
                'status'    => 'active',
                'starts_at' => now(),
                'ends_at'   => now()->addDays($duration),
            ]);
            $subscription->app->update([
                'current_subscription_id' => $subscription->id,
                'is_active'               => true,
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'تم تفعيل الاشتراك والشركة بنجاح.',
                'data'    => [
                    'app_name'     => $subscription->app->name,
                    'package_name' => $subscription->package->name ?? 'N/A',
                    'starts_at'    => $subscription->starts_at->toDateTimeString(),
                    'expires_at'   => $subscription->ends_at->toDateTimeString(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'فشلت عملية التفعيل بسبب خطأ تقني.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب قائمة بكل الاشتراكات المعلقة (التي تنتظر التفعيل)
     * @return \Illuminate\Http\JsonResponse
     */
    public function pendingRequests()
    {
        
        $pendingSubscriptions = Subscription::with(['app', 'package'])
            ->where('status', 'pending')
            ->get();
        $formattedData = $pendingSubscriptions->map(function ($sub) {
            return [
                'subscription_id' => $sub->id,
                'app_name'        => $sub->app->name ?? 'شركة غير معروفة',
                'app_phone'       => $sub->app->phone ?? 'لا يوجد رقم',
                'package_name'    => $sub->package->name ?? 'باقة محذوفة',
                'price_to_pay'    => $sub->price_paid,
                'requested_at'    => $sub->created_at->format('Y-m-d h:i A'),
                'time_waiting'    => $sub->created_at->diffForHumans(), 
            ];
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'تم جلب الطلبات المعلقة بنجاح',
            'count'   => $pendingSubscriptions->count(),
            'data'    => $formattedData
        ]);
    }
}