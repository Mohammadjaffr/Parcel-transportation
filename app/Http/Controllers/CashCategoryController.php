<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\CashCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CashCategoryController extends Controller
{
    /**
     * عرض فئات الصندوق الخاصة بالشركة الحالية
     */
    public function index(Request $request)
    {
        $query = CashCategory::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $categories = $query->orderBy('type')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('pages.cash_categories.index', compact('categories'));
    }

    /**
     * حفظ فئة جديدة مع التحقق المباشر
     */
    public function store(Request $request)
    {
        $appId = auth()->user()->app_id;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cash_categories', 'name')->where(function ($query) use ($appId, $request) {
                    return $query->where('app_id', $appId)
                                 ->where('type', $request->type);
                }),
            ],
            'type'      => ['required', 'in:income,expense'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'يرجى إدخال اسم الفئة.',
            'name.string'   => 'اسم الفئة يجب أن يكون نصاً صالحاً.',
            'name.max'      => 'اسم الفئة لا يجب أن يتجاوز 100 حرف.',
            'name.unique'   => 'هذه الفئة مسجلة مسبقاً لنفس نوع الحركة.',
            'type.required' => 'نوع الحركة المالية مطلوب.',
            'type.in'       => 'نوع الحركة المحدد غير صالح (قبض/صرف).',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }

            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $category = DB::transaction(function () use ($request, $appId) {
                return CashCategory::create([
                    'app_id'    => $appId,
                    'name'      => trim($request->name),
                    'type'      => $request->type,
                    'is_active' => $request->boolean('is_active', true),
                ]);
            });

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'تم إضافة الفئة بنجاح.',
                    'category' => [
                        'id'   => $category->id,
                        'name' => $category->name,
                        'type' => $category->type,
                    ],
                ], 201);
            }

            return WebResponseClass::sendResponse(
                'تمت الإضافة!',
                'تم حفظ فئة الصندوق بنجاح.',
                'حسناً',
                'cash-categories.index'
            );
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'تعذر حفظ الفئة: ' . $e->getMessage(),
                ], 500);
            }

            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * تحديث بيانات الفئة أو تبديل حالتها
     */
    public function update(Request $request, CashCategory $cashCategory)
    {
        // تبديل حالة التفعيل بشكل مباشر
        if ($request->has('toggle_status')) {
            $cashCategory->update(['is_active' => !$cashCategory->is_active]);

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تغيير حالة الفئة بنجاح.',
                'حسناً',
                'cash-categories.index'
            );
        }

        $appId = auth()->user()->app_id;

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('cash_categories', 'name')
                    ->ignore($cashCategory->id)
                    ->where(fn ($q) => $q->where('app_id', $appId)->where('type', $cashCategory->type)),
            ],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'اسم الفئة مطلوب.',
            'name.unique'   => 'اسم الفئة مسجل مسبقاً لهذا النوع.',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $cashCategory->update([
                'name'      => trim($request->name),
                'is_active' => $request->boolean('is_active', $cashCategory->is_active),
            ]);

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تعديل الفئة بنجاح.',
                'حسناً',
                'cash-categories.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * حذف الفئة مع التأكد من عدم ارتباطها بحركات مالية
     */
    public function destroy(CashCategory $cashCategory)
    {
        if ($cashCategory->transactions()->exists()) {
            return WebResponseClass::sendError(
                'لا يمكن حذف هذه الفئة لوجود معاملات مالية مسجلة عليها. يمكنك تعطيلها بدلاً من ذلك.',
                'تعذر الحذف'
            );
        }

        try {
            $cashCategory->delete();

            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف فئة الصندوق بنجاح.',
                'حسناً',
                'cash-categories.index'
            );
        } catch (\Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}