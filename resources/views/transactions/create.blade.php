@extends('layouts.app')
@section('title', 'Add New Transaction')

@section('content')
    <div class="space-y-6 font-outfit" dir="rtl">

        {{-- Header --}}
        <div
            class="flex flex-col gap-4 justify-between items-start p-6 bg-white rounded-2xl border border-gray-100 md:flex-row md:items-center dark:bg-white/[0.03] dark:border-gray-800 shadow-theme-sm">
            <div class="flex gap-4 items-center">
                <div class="flex justify-center items-center w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">إضافة معاملة جديدة</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">تسجيل حركة مالية جديدة في صندوق النقدية</p>
                </div>
            </div>
            <a href="{{ route('transactions.index') }}"
                class="inline-flex gap-2 items-center px-5 h-11 text-sm font-semibold rounded-xl transition-all duration-200 text-brand-500 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10 hover:bg-brand-100 dark:hover:bg-brand-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للقائمة
            </a>
        </div>

        {{-- Form Card --}}
        <div
            class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm p-6">
            <div class="flex gap-3 items-center pb-4 mb-6 border-b border-gray-100 dark:border-gray-800">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10">
                    <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">بيانات المعاملة</h3>
            </div>

            <form method="POST" action="{{ route('transactions.store') }}" id="transactionForm">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                    {{-- Transaction Type --}}
                    <div>
                        <label for="type" class="block mb-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            نوع المعاملة <span class="text-error-500">*</span>
                        </label>
                        <select id="type"
                            class="px-4 w-full h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            required>
                            <option value="">-- اختر نوع المعاملة --</option>
                            <option value="in">إيراد / دخل</option>
                            <option value="out">مصروف / خروج</option>
                        </select>
                        @error('type')
                            <p class="mt-2 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="transaction_category_id"
                            class="block mb-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            الفئة <span class="text-error-500">*</span>
                        </label>
                        <select name="transaction_category_id" id="transaction_category_id"
                            class="px-4 w-full h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                            required disabled>
                            <option value="">-- اختر الفئة --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type }}"
                                    {{ old('transaction_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-400">يرجى اختيار نوع المعاملة أولاً</p>
                        @error('transaction_category_id')
                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label for="amount" class="block mb-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            المبلغ (ريال يمني) <span class="text-error-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" id="amount"
                                class="pr-4 pl-16 w-full h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                                step="0.01" min="0.01" placeholder="0.00" value="{{ old('amount') }}" required>
                            <span
                                class="absolute left-4 top-1/2 text-xs font-bold text-gray-400 -translate-y-1/2">YER</span>
                        </div>
                        @error('amount')
                            <p class="mt-2 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Empty cell for grid alignment --}}
                    <div class="hidden md:block"></div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description"
                            class="block mb-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            الوصف <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <textarea name="description" id="description"
                            class="px-4 py-3 w-full text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all resize-none dark:bg-gray-900 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                            rows="4" placeholder="أدخل وصف تفصيلي للمعاملة...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="flex gap-3 justify-start mt-6">
                    <button type="submit"
                        class="inline-flex gap-2 items-center px-6 h-12 text-sm font-semibold text-white rounded-xl transition-all duration-200 bg-brand-500 hover:bg-brand-600 focus:ring-4 focus:ring-brand-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ المعاملة
                    </button>
                    <a href="{{ route('transactions.index') }}"
                        class="inline-flex justify-center items-center px-6 h-12 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl transition-all dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>

        {{-- Quick Tips Card --}}
        <div class="p-5 rounded-2xl border bg-brand-50/50 dark:bg-brand-500/5 border-brand-100 dark:border-brand-500/20">
            <div class="flex gap-3 items-start">
                <div
                    class="flex flex-shrink-0 justify-center items-center w-10 h-10 rounded-xl bg-brand-100 dark:bg-brand-500/20">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-brand-700 dark:text-brand-400">نصائح سريعة</h4>
                    <ul class="mt-2 space-y-1 text-xs text-brand-600 dark:text-brand-300">
                        <li>• اختر <strong>إيراد</strong> للأموال الداخلة (مبيعات، تحصيلات، إلخ)</li>
                        <li>• اختر <strong>مصروف</strong> للأموال الخارجة (مشتريات، رواتب، إلخ)</li>
                        <li>• أضف وصفاً مفصلاً لتسهيل المراجعة لاحقاً</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Dynamic Category Filtering --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const categorySelect = document.getElementById('transaction_category_id');
            const categoryHelp = categorySelect.nextElementSibling;
            const allOptions = Array.from(categorySelect.querySelectorAll('option[data-type]'));

            function filterCategories() {
                const selectedType = typeSelect.value;
                categorySelect.value = '';

                if (!selectedType) {
                    categorySelect.disabled = true;
                    categoryHelp.textContent = 'يرجى اختيار نوع المعاملة أولاً';
                    allOptions.forEach(option => option.style.display = 'none');
                } else {
                    categorySelect.disabled = false;
                    categoryHelp.textContent = selectedType === 'in' ? 'اختر فئة الإيراد' : 'اختر فئة المصروف';
                    allOptions.forEach(option => {
                        option.style.display = option.dataset.type === selectedType ? 'block' : 'none';
                    });
                }
            }

            typeSelect.addEventListener('change', filterCategories);
            filterCategories();
        });
    </script>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.dispatchEvent(new CustomEvent('open-error-modal', {
                    detail: {
                        message: 'يرجى تصحيح الأخطاء في النموذج'
                    }
                }));
            });
        </script>
    @endif
@endsection
