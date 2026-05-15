<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['app', 'package'])
            ->latest()
            ->paginate(15);

        $packages = Package::where('is_active', true)
            ->orderBy('price')
            ->get();

        $totalRevenue = Subscription::sum('price_paid');

        $activeCount = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '>', now())
            ->count();

        $pendingCount = Subscription::where('status', 'pending')->count();

        $expiredCount = Subscription::where(function ($q) {
            $q->where('status', 'expired')
                ->orWhere(function ($q2) {
                    $q2->where('status', 'active')
                        ->whereNotNull('ends_at')
                        ->where('ends_at', '<=', now());
                });
        })->count();

        return view('SuperAdmin.subscriptions.index', compact(
            'subscriptions',
            'packages',
            'totalRevenue',
            'activeCount',
            'pendingCount',
            'expiredCount'
        ));
    }

    public function updateStatus(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'status'      => 'required|in:active,pending,expired,cancelled',
            'extend_days' => 'nullable|integer|min:1|max:3650',
            'package_id'  => 'nullable|integer|exists:packages,id',
            'price_paid'  => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($subscription, $validated) {
            $subscription->load(['app', 'package']);

            $data = [
                'status' => $validated['status'],
            ];

            $package = $subscription->package;

            if (!empty($validated['package_id'])) {
                $package = Package::findOrFail($validated['package_id']);

                $data['package_id'] = $package->id;
                $data['price_paid'] = $validated['price_paid'] ?? $package->price;
            } elseif (array_key_exists('price_paid', $validated) && $validated['price_paid'] !== null) {
                $data['price_paid'] = $validated['price_paid'];
            }

            if ($validated['status'] === 'active') {
                $duration = (int) ($validated['extend_days'] ?? $package?->duration_in_days ?? 30);

                $baseDate = $subscription->ends_at && $subscription->ends_at->isFuture()
                    ? $subscription->ends_at->copy()
                    : now();

                $data['starts_at'] = $subscription->starts_at ?: now();
                $data['ends_at'] = $baseDate->addDays($duration);

                Subscription::where('app_id', $subscription->app_id)
                    ->where('id', '!=', $subscription->id)
                    ->where('status', 'active')
                    ->update(['status' => 'expired']);
            }

            $subscription->update($data);

            if ($subscription->app) {
                if ($validated['status'] === 'active') {
                    $subscription->app->update([
                        'current_subscription_id' => $subscription->id,
                        'is_active' => true,
                    ]);
                }

                if (in_array($validated['status'], ['expired', 'cancelled'], true)) {
                    if ((int) $subscription->app->current_subscription_id === (int) $subscription->id) {
                        $subscription->app->update([
                            'is_active' => false,
                        ]);
                    }
                }
            }
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تحديث الاشتراك والصلاحيات بنجاح.',
        ]);
    }
}