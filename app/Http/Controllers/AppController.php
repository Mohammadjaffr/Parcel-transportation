<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\OfficeConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

use App\Classes\WebResponseClass;

class AppController extends Controller
{
    public function index(Request $request)
    {
        $myAppId = auth()->user()->app_id;
        $offices = App::with(['branches' => function ($query) {
            $query->withoutGlobalScope('app_id');
        }])
            ->where('id', '!=', $myAppId)
            ->latest()
            ->paginate(15);
        $offices->getCollection()->transform(function ($office) use ($myAppId) {
            $connection = OfficeConnection::where(function ($q) use ($myAppId, $office) {
                $q->where('sender_app_id', $myAppId)->where('receiver_app_id', $office->id);
            })->orWhere(function ($q) use ($myAppId, $office) {
                $q->where('sender_app_id', $office->id)->where('receiver_app_id', $myAppId);
            })->first();

            $office->connection_status = $connection ? $connection->status : 'none';
            return $office;
        });
        if ($request->isMobile) {
            return view('mobile.pages.office.verified.index', compact('offices'));
        }
        return view('pages.office.verified.index', compact('offices'));
    }
    public function settings(Request $request)
{
    $user = Auth::user();

    // جلب بيانات الشركة مع الفروع والاشتراك الحالي وتفاصيل الباقة
    $company = $user->App()
        ->with(['branches', 'currentSubscription.package']) 
        ->withCount(['branches', 'users','drivers'])
        ->first();

    $subscription = $company->currentSubscription;
    $remainingDays = 0;
    $shipmentsCount = 0;
    $packagesCount = 0;

    if ($subscription) {
        $remainingDays = (int) ceil(now()->diffInDays($subscription->ends_at, false));
        $remainingDays = $remainingDays < 0 ? 0 : $remainingDays;
        $shipmentsCount = $company->shipments()
            ->whereBetween('shipments.created_at', [$subscription->starts_at, $subscription->ends_at])
            ->count();
        $packagesCount = $company->shipmentPackages()
            ->whereBetween('shipment_packages.created_at', [$subscription->starts_at, $subscription->ends_at])
            ->count();
    }

    // إرجاع الواجهة مع الاحتفاظ بـ compact بالشكل المعتاد
    if ($request->isMobile) {
        return view('mobile.pages.company.settings', compact('company', 'subscription', 'remainingDays', 'shipmentsCount', 'packagesCount'));
    }
    
    // صفحة الديسكتوب
    return view('pages.company.settings', compact('company', 'subscription', 'remainingDays', 'shipmentsCount', 'packagesCount'));
}

    public function update(Request $request)
    {
        $company = auth()->user()->App;
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/i'],
            'logo'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'terms_and_conditions'   => 'nullable|array',
            'terms_and_conditions.*' => 'nullable|string|max:500',
        ]);

        try {
            $dataToUpdate = [
                'name'  => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'color' => $request->color,
            ];
            if ($request->has('terms_and_conditions')) {
                $cleanTerms = array_filter($request->terms_and_conditions, function ($value) {
                    return !is_null($value) && trim($value) !== '';
                });
                $dataToUpdate['terms_and_conditions'] = array_values($cleanTerms);
            } else {
                $dataToUpdate['terms_and_conditions'] = null;
            }
            if ($request->hasFile('logo')) {
                if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                    Storage::disk('public')->delete($company->logo);
                }
                $path = $request->file('logo')->store('app/logos', 'public');
                $dataToUpdate['logo'] = $path;
            }
            $company->update($dataToUpdate);
            Cache::forget('app_logo_' . auth()->user()->app_id);
            Cache::forget('app_name_' . auth()->user()->app_id);
            Cache::forget('app_terms_' . auth()->user()->app_id);

           
        return WebResponseClass::sendResponse(
            'تم التحديث!',
            'تم تحديث بيانات الشركة بنجاح.',
            'حسناً',
            'app.settings'
        );
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage());
        }
    }
}
