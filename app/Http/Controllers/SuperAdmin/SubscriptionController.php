<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['app', 'package'])
            ->latest()
            ->paginate(15);

        $totalRevenue  = Subscription::sum('price_paid');
        $activeCount   = Subscription::where('status', 'active')->where('ends_at', '>', now())->count();
        $expiredCount  = Subscription::where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'active')->where('ends_at', '<', now());
            })->count();

        return view('SuperAdmin.subscriptions.index', compact(
            'subscriptions',
            'totalRevenue',
            'activeCount',
            'expiredCount'
        ));
    }

    public function updateStatus(Request $request, Subscription $subscription)
    {
        $request->validate([
            'status'      => 'required|in:active,expired,cancelled',
            'extend_days' => 'nullable|integer|min:1',
        ]);

        $subscription->status = $request->input('status');

        if ($request->filled('extend_days') && $request->input('status') === 'active') {
            $baseDate = $subscription->ends_at && $subscription->ends_at->isFuture()
                ? $subscription->ends_at
                : now();
            $subscription->ends_at = $baseDate->addDays((int) $request->input('extend_days'));
        }

        $subscription->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تحديث حالة الاشتراك بنجاح',
        ]);
    }
}
