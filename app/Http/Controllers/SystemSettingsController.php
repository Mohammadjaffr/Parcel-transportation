<?php

namespace App\Http\Controllers;

use App\Models\app_setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
class SystemSettingsController extends Controller
{
    public function index(){
        return view('pages.systems.index');
    }
    public function updateAutoAssignSetting(Request $request): JsonResponse
    {
        $request->validate([
            'enabled' => 'required|boolean'
        ]);
        
        try {
            $app_setting=app_setting::first();
            $app_setting->update([
                "auto_assign_to_drivers"=>$request->enabled
            ]);
            return response()->json([
                'success' => true,
                'message' => $request->enabled ? 
                    'تم تفعيل النظام التلقائي بنجاح' : 
                    'تم تفعيل النظام اليدوي بنجاح'
            ]);
            
        } catch (\Exception $e) { 
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ الإعدادات'
            ], 500);
        }
    }
    public function getAutoAssignSetting(): JsonResponse
    {
        $autoAssignEnabled = app_setting::first();
        
        return response()->json([
            'auto_assign_enabled' => (bool) $autoAssignEnabled->auto_assign_to_drivers,
        ]);
    }
}
