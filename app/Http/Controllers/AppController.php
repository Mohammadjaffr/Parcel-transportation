<?php

namespace App\Http\Controllers;

use App\Models\App; 
use App\Models\OfficeConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
        $connection = OfficeConnection::where(function($q) use ($myAppId, $office) {
            $q->where('sender_app_id', $myAppId)->where('receiver_app_id', $office->id);
        })->orWhere(function($q) use ($myAppId, $office) {
            $q->where('sender_app_id', $office->id)->where('receiver_app_id', $myAppId);
        })->first();

        $office->connection_status = $connection ? $connection->status : 'none';
        return $office;
    });
    if ($request->isMobile){
        return view('mobile.pages.office.verified.index', compact('offices'));
    }
    return view('pages.office.verified.index', compact('offices'));
}
    public function settings(Request $request)
    {
        $user = Auth::user();

        $company = $user->App()
            ->with('branches') // تأكد أن اسم العلاقة في موديل App هو branches (أو offices حسب تسميتك)
            ->withCount(['branches', 'users']) 
            ->first();
        if ($request->isMobile){
            return view('mobile.pages.company.settings', compact('company'));
        }
         // عدل الصفحه الخاصه ب الدسك توب  ي السعدي
        return view('pages.company.settings', compact('company'));
        
    }

    public function update(Request $request)
    {
        $company = auth()->user()->App;
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'logo'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'terms_and_conditions'   => 'nullable|array',
            'terms_and_conditions.*' => 'nullable|string|max:500',
        ]);

        try {
            $dataToUpdate = [
                'name'  => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
            ];
            if ($request->has('terms_and_conditions')) {
                $cleanTerms = array_filter($request->terms_and_conditions, function($value) {
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

            return back()->with([
                'success_title' => 'تم التحديث!',
                'success_message' => 'تم تحديث بيانات الشركة بنجاح.'
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage());
        }
    }
}
