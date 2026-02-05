<?php

namespace App\Http\Controllers;

use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use App\Classes\WebResponseClass;

class TransactionCategoryController extends Controller
{
    /**
     * Display a listing of transaction categories
     */
    public function index()
    {
        $categories = TransactionCategory::orderBy('type')->orderBy('name')->paginate(10);

        return view('transaction_categories.index', compact('categories'));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:in,out',
            'code' => 'nullable|string|max:50|unique:transaction_categories,code',
        ]);

        TransactionCategory::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'code' => $validated['code'] ?? null,
            'is_active' => true,
        ]);

        return WebResponseClass::sendResponse(
            'تمت الإضافة!',
            'تم إضافة التصنيف بنجاح.',
            'حسناً',
            'transaction-categories.index'
        );
    }

    /**
     * Update the specified category (toggle active status or edit name)
     */
    public function update(Request $request, TransactionCategory $transactionCategory)
    {
        // If toggling status
        if ($request->has('toggle_status')) {
            $transactionCategory->update([
                'is_active' => !$transactionCategory->is_active,
            ]);

            return WebResponseClass::sendResponse(
                'تم التحديث!',
                'تم تحديث التصنيف بنجاح.',
                'حسناً',
                'transaction-categories.index'
            );
        }

        // If updating name - validate uniqueness excluding current category
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:transaction_categories,name,' . $transactionCategory->id,
        ]);

        $transactionCategory->update([
            'name' => $validated['name'],
        ]);

        return WebResponseClass::sendResponse(
            'تم التحديث!',
            'تم تحديث التصنيف بنجاح.',
            'حسناً',
            'transaction-categories.index'
        );
    }

    /**
     * Remove the specified category from storage
     */
    public function destroy(TransactionCategory $transactionCategory)
    {
        // Check if category has transactions
        if ($transactionCategory->transactions()->count() > 0) {
            return WebResponseClass::sendResponse(
                'خطأ!',
                'لا يمكن حذف التصنيف لوجود معاملات مرتبطة به.',
                'حسناً',
                null,
                false,
                'error'
            );
        }

        $transactionCategory->delete();

        return WebResponseClass::sendResponse(
            'تم الحذف!',
            'تم حذف التصنيف بنجاح.',
            'حسناً',
            'transaction-categories.index'
        );
    }
}
