<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;
use App\Classes\WebResponseClass;
use App\Services\AdminLoggerService;
use Illuminate\Support\Facades\Validator;


class OfficeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->isMobile) {
            return view('mobile.pages.office.index');
        }

        return view('pages.office.index');
    }

    public function unverifiedIndex(Request $request)
    {
        $query = Office::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }
        $offices = $query->latest()->paginate(10)->withQueryString();
        if ($request->isMobile) {
            return view('mobile.pages.office.unverified.index', compact('offices'));
        }
        return view('pages.office.unverified.index', compact('offices'));
    }

    public function store(Request $request)
    {
  
         $validator = Validator::make($request->all(), [
              'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);
 $data = $request->only('name', 'phone', 'address');
        if (auth()->check() && auth()->user()->app_id) {
            $data['app_id'] = auth()->user()->app_id;
        }

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        } 

        try {
            $office = Office::create($data);
            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم حفظ المكتب بنجاح.',
                'حسناً'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
        
    }

    public function update(Request $request, Office $office)
    {
      $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()){
            return WebResponseClass::sendValidationError($validator);
        } 

        try {
            $office->update($validator->validated());
            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث المكتب بنجاح.',
                'حسناً'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    public function destroy(Office $office)
    {
       try {
            $office->delete();
            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف المكتب بنجاح.',
                'حسناً'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
