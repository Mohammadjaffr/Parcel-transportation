@extends('layouts.app')
@section('title', 'إنشاء تسوية مالية')
@section('Breadcrumb', 'تسوية مالية')

@section('content')
    <div class="max-w-xl mx-auto space-y-8 font-outfit p-6">

        <!-- Header -->
        <div class="text-center my-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center justify-center gap-2">
                <span class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-xl text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                تسوية مالية جديدة
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                تسجيل عملية دفع نقدي لفرع آخر لتسوية المديونيات المستحقة.
            </p>
        </div>

        <div
            class="bg-white dark:bg-gray-dark rounded-3xl border border-gray-100 dark:border-gray-800 shadow-theme-lg overflow-hidden">

            <!-- Branch Info Banner -->
            <div
                class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-800 p-6 flex.items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                        الفرع المرسل (فرعك الحالي)
                    </span>
                    <div class="text-base font-bold text-gray-800 dark:text-white.mt-1">
                        {{ auth()->user()->branch->name ?? 'غير محدد' }}
                    </div>
                </div>
                <div
                    class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-sm text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
            </div>

            <div class="p-8">
                <form action="{{ route('finance.settlements.store') }}" method="POST" class="space-y-6" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                    @csrf

                    {{-- الفرع المرسل (الكود) --}}
                    <input type="hidden" name="sender_branch_code" value="{{ $currentBranchId }}">

                    <!-- Receiver Branch Select -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 dark:text-gray-300">
                            الفرع المستلم (الداين)
                        </label>
                        <div class="relative">
                            <select name="receiver_branch_code" required
                                    class="w-full h-14 pr-4 pl-10 rounded-xl bg-gray-50 dark:bg-gray-900 border-none focus:ring-2 focus:ring-brand-500/20 text-gray-800 dark:text-white font-medium appearance-none transition-all cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800">
                                <option disabled value="">
                                    اختر الفرع المستحق للدفع...
                                </option>
                                @foreach ($branchesOwed as $otherBranchId => $row)
                                    @php $net = abs($row['net']); @endphp
                                    <option value="{{ $otherBranchId }}"
                                            data-amount="{{ $net }}"
                                            {{ old('receiver_branch_code') == $otherBranchId ? 'selected' : '' }}>
                                        {{ $row['branch']->name ?? $otherBranchId }} — مستحق له:
                                        {{ number_format($net, 2) }} ر.ي
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('receiver_branch_code')
                            <p class="text-xs text-error-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount Input -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 dark:text-gray-300 flex justify-between">
                            <span>مبلغ التسوية</span>
                            <span id="max-amount-label" class="text-xs text-brand-500 hidden"></span>
                        </label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pr-4 flex.items-center pointer-events-none text-gray-400">
                                <span class="text-sm font-bold">ر.ي</span>
                            </div>
                            <input type="number"
                                   step="0.01"
                                   min="0.01"
                                   name="amount"
                                   value="{{ old('amount') }}"
                                   class="w-full h-14 pr-4 pl-4 rounded-xl bg-gray-50 dark:bg-gray-900 border-none focus:ring-2 focus:ring-brand-500/20 text-xl font-bold text-gray-800 dark:text-white placeholder-gray-300 transition-all font-mono"
                                   placeholder="0.00">
                        </div>
                        @error('amount')
                            <p class="text-xs text-error-500 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description Input -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700.dark:text-gray-300">
                            ملاحظات (اختياري)
                        </label>
                        <textarea name="description" rows="3"
                                  class="w-full p-4 rounded-xl bg-gray-50.dark:bg-gray-900 border-none focus:ring-2 focus:ring-brand-500/20 text-gray-800.dark:text-white placeholder-gray-400 transition-all resize-none"
                                  placeholder="مثال: تسليم نقد يد بيد...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" :disabled="isSubmitting"
                            class="w-full h-14 bg-brand-500 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/40 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7" />
                            </svg>
                            تأكيد وإرسال التسوية
                        </span>
                        <span x-show="isSubmitting" class="flex gap-2 items-center">
                            <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            جاري الإرسال...
                        </span>
                    </button>

                    <p class="text-xs text-center text-gray-400">
                        سيتم تسجيل هذه العملية كـ <span class="font-bold">صادر نقد</span> من فرعك و
                        <span class="font-bold">وارد نقد</span> للفرع المستلم.
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const selectBranch = document.querySelector("select[name='receiver_branch_code']");
            const amountInput = document.querySelector("input[name='amount']");
            const maxLabel = document.getElementById('max-amount-label');

            function updateAmountFromSelected() {
                const option = selectBranch.options[selectBranch.selectedIndex];
                if (!option || !option.dataset.amount) {
                    amountInput.removeAttribute('max');
                    maxLabel.classList.add('hidden');
                    maxLabel.textContent = '';
                    return;
                }

                const net = parseFloat(option.dataset.amount) || 0;

                if (net > 0) {
                    // عيّن القيمة القصوى وقيمة الحقل
                    amountInput.value = amountInput.value || net.toFixed(2);
                    amountInput.max = net.toFixed(2);
                    amountInput.placeholder = net.toFixed(2);

                    maxLabel.textContent = "المستحق: " + net.toFixed(2) + " ر.ي (كحد أقصى)";
                    maxLabel.classList.remove('hidden');

                    // تأثير بصري بسيط
                    amountInput.classList.add('ring-2', 'ring-brand-500/50');
                    setTimeout(() => {
                        amountInput.classList.remove('ring-2', 'ring-brand-500/50');
                    }, 400);
                }
            }

            // تحديث عند تغيير الفرع
            selectBranch.addEventListener("change", updateAmountFromSelected);

            // في حال كان فيه قيمة قديمة (بعد فاليديشن) نحاول نعيد الضبط
            if (selectBranch.value) {
                updateAmountFromSelected();
            }
        });
    </script>
@endsection
