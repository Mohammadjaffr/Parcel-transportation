@extends('layouts.app')
@section('title', 'Add New Transaction')

@section('content')
    <div class="space-y-6 font-outfit" dir="rtl">

        {{-- Header --}}
        <div
            class="flex justify-between items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <h2 class="text-xl font-black text-gray-900 dark:text-white">إضافة معاملة جديدة</h2>
            <a href="{{ route('transactions.index') }}"
                class="flex gap-2 justify-center items-center px-6 h-10 text-sm font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-xl transition-all hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                رجوع
            </a>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm p-8">
            <form method="POST" action="{{ route('transactions.store') }}" id="transactionForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Transaction Type --}}
                    <div class="form-group">
                        <label for="type" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            نوع المعاملة <span class="text-danger-500">*</span>
                        </label>
                        <select id="type"
                            class="w-full h-12 px-4 text-sm font-medium bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            required>
                            <option value="">-- اختر نوع المعاملة --</option>
                            <option value="in">إيراد / دخل</option>
                            <option value="out">مصروف / خروج</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="form-group">
                        <label for="transaction_category_id"
                            class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            الفئة <span class="text-danger-500">*</span>
                        </label>
                        <select name="transaction_category_id" id="transaction_category_id"
                            class="w-full h-12 px-4 text-sm font-medium bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            required disabled>
                            <option value="">-- اختر الفئة --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type }}" {{ old('transaction_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">يرجى اختيار نوع المعاملة أولاً</p>
                        @error('transaction_category_id')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="form-group">
                        <label for="amount" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            المبلغ (ريال يمني) <span class="text-danger-500">*</span>
                        </label>
                        <input type="number" name="amount" id="amount"
                            class="w-full h-12 px-4 text-sm font-medium bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            step="0.01" min="0.01" placeholder="0.00" value="{{ old('amount') }}" required>
                        @error('amount')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="form-group md:col-span-2">
                        <label for="description" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            الوصف (اختياري)
                        </label>
                        <textarea name="description" id="description"
                            class="w-full px-4 py-3 text-sm font-medium bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white resize-none"
                            rows="4" placeholder="أدخل وصف تفصيلي للمعاملة...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="flex gap-4 justify-end mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('transactions.index') }}"
                        class="px-8 h-12 flex items-center justify-center text-sm font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-900 rounded-xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 active:scale-95">
                        إلغاء
                    </a>
                    <button type="submit"
                        class="px-8 h-12 text-sm font-bold text-white bg-brand-500 rounded-xl shadow-lg transition-all hover:bg-brand-600 shadow-brand-500/20 active:scale-95">
                        <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ المعاملة
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Vanilla JavaScript for Dynamic Category Filtering --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const categorySelect = document.getElementById('transaction_category_id');
            const allOptions = Array.from(categorySelect.querySelectorAll('option[data-type]'));

            // Function to filter categories based on selected type
            function filterCategories() {
                const selectedType = typeSelect.value;

                // Reset category selection
                categorySelect.value = '';

                if (!selectedType) {
                    // Disable category if no type selected
                    categorySelect.disabled = true;
                    allOptions.forEach(option => option.style.display = 'none');
                } else {
                    // Enable category
                    categorySelect.disabled = false;

                    // Show/hide options based on type
                    allOptions.forEach(option => {
                        if (option.dataset.type === selectedType) {
                            option.style.display = 'block';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                }
            }

            // Listen to type selection changes
            typeSelect.addEventListener('change', filterCategories);

            // Initialize on page load
            filterCategories();
        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.dispatchEvent(new CustomEvent('open-error-modal', {
                    detail: {
                        message: 'يرجى تصحيح الأخطاء في النموذج'
                    }
                }));
            });
        </script>
    @endif
@endsection