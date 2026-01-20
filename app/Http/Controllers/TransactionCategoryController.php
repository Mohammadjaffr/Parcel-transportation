<?php

namespace App\Http\Controllers;

use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
    /**
     * Display a listing of transaction categories
     */
    public function index()
    {
        $categories = TransactionCategory::orderBy('type')->orderBy('name')->get();

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

        return redirect()->route('transaction-categories.index')
            ->with('success', 'Category created successfully.');
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

            return redirect()->route('transaction-categories.index')
                ->with('success', 'Category status updated successfully.');
        }

        // If updating name/type
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:in,out',
        ]);

        $transactionCategory->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
        ]);

        return redirect()->route('transaction-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage
     */
    public function destroy(TransactionCategory $transactionCategory)
    {
        // Check if category has transactions
        if ($transactionCategory->transactions()->count() > 0) {
            return redirect()->route('transaction-categories.index')
                ->with('error', 'Cannot delete category. It has ' . $transactionCategory->transactions()->count() . ' transaction(s) linked to it.');
        }

        $transactionCategory->delete();

        return redirect()->route('transaction-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
