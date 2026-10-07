<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SubscriptionController extends Controller
{
   public function requestSubscription(Request $request)
{
    $request->validate(['package_id' => 'required|exists:packages,id']);
    
    $app = auth()->user()->App;

    if (!$app) {
        return back()->with('error', 'لا يوجد شركة مرتبطة بحسابك حالياً.');
    }

    DB::beginTransaction();
    
    try {
        Subscription::where('app_id', $app->id)
            ->where('status', 'pending')
            ->delete();

        $package = Package::findOrFail($request->package_id);

        Subscription::create([
            'app_id' => $app->id,
            'package_id' => $package->id,
            'price_paid' => $package->price,
            'allowed_branches' => $package->max_branches,
            'allowed_drivers' => $package->max_drivers,
            'allowed_shipments' => $package->max_shipments,
            'allowed_packages' => $package->max_packages,
            'status' => 'pending', 
            // أعدنا التواريخ هنا لتجنب خطأ قاعدة البيانات
            'starts_at' => now(), 
            'ends_at' => now()->addDays($package->duration_in_days),
        ]);

        DB::commit();
        return back()->with('info', 'تم إرسال طلب الاشتراك، يرجى التواصل مع الإدارة للتفعيل.');

    } catch (\Exception $e) {
        DB::rollBack();
        // قمنا بطباعة الخطأ الحقيقي بدلاً من الرسالة العامة لتعرف المشكلة بالضبط
        return back()->with('error', 'خطأ فني: ' . $e->getMessage());
    }
}
}
