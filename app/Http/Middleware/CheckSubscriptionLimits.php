<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimits
{
    /**
     * @param string $resource 
     */
    public function handle(Request $request, Closure $next, string $resource): Response
    {
        $app = auth()->user()->App;

        if (!$app || !$app->is_active || !$app->currentSubscription) {
            return $this->reject($request, 'حسابك غير فعال أو لا تملك اشتراكاً.');
        }

        $subscription = $app->currentSubscription;
        if ($subscription->status !== 'active' || $subscription->ends_at->isPast()) {
            return $this->reject($request, 'انتهت صلاحية اشتراكك. يرجى التجديد.');
        }

        $limit = 0;
        $currentCount = 0;
        $resourceName = '';

        switch ($resource) {
            case 'branches':
                $limit = $subscription->allowed_branches;
                $currentCount = $app->branches()->count();
                $resourceName = 'الفروع';
                break;

            case 'drivers':
                $limit = $subscription->allowed_drivers;
                $currentCount = $app->drivers()->count();
                $resourceName = 'السائقين';
                break;

            case 'shipments':
                $limit = $subscription->allowed_shipments;
                $currentCount = $app->shipments()
                    ->whereBetween('shipments.created_at', [$subscription->starts_at, $subscription->ends_at])
                    ->count();
                $resourceName = 'الطرود';
                break;

            case 'packages':
                $limit = $subscription->allowed_packages;
                $currentCount = $app->shipmentPackages()
                    ->whereBetween('created_at', [$subscription->starts_at, $subscription->ends_at])
                    ->count();
                $resourceName = 'الرحلات المجمعة';
                break;

            default:
                return $this->reject($request, 'نوع المورد غير معروف.');
        }

        if ($limit > 0 && $currentCount >= $limit) {
            return $this->reject($request, "لقد وصلت للحد الأقصى المسموح به من {$resourceName} ({$limit}). يرجى ترقية باقتك للاستمرار.");
        }

        return $next($request);
    }

    private function reject(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['status' => 'error', 'message' => $message], 403);
        }
        return back()->with('error', $message);
    }
}