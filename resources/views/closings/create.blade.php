@extends('layouts.app')
@section('title', 'Daily Cash Closing')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl">

        {{-- Header --}}
        <div
            class="flex justify-between items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white">إقفال الصندوق اليومي</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">عد النقد الفعلي وتحويل المبالغ إلى المركز الرئيسي
                </p>
            </div>
            <a href="{{ route('transactions.index') }}"
                class="flex gap-2 justify-center items-center px-6 h-10 text-sm font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-xl transition-all hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                إلغاء
            </a>
        </div>

        {{-- Cash Closing Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm p-8">

            {{-- System Balance Display --}}
            <div class="mb-8 p-6 bg-brand-50 dark:bg-brand-500/10 rounded-xl border-r-4 border-brand-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-gray-600 dark:text-gray-400">الرصيد المتوقع (من النظام)</p>
                        <h3 class="text-3xl font-black text-brand-600 dark:text-brand-400 mt-1" id="systemBalance">
                            {{ number_format($systemBalance, 2) }} YER
                        </h3>
                    </div>
                    <svg class="w-16 h-16 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <form method="POST" action="{{ route('closings.store') }}" id="closingForm">
                @csrf

                <input type="hidden" id="systemBalanceValue" value="{{ $systemBalance }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Actual Cash Counted --}}
                    <div class="form-group md:col-span-2">
                        <label for="actual_cash" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            النقد الفعلي المعدود <span class="text-danger-500">*</span>
                        </label>
                        <input type="number" name="actual_cash" id="actual_cash"
                            class="w-full h-14 px-4 text-lg font-bold bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            step="0.01" min="0" placeholder="0.00" value="{{ old('actual_cash') }}" required>
                        <p class="mt-2 text-xs text-gray-500">قم بعد النقد الموجود في الصندوق فعلياً</p>
                        @error('actual_cash')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Difference Display --}}
                    <div class="md:col-span-2">
                        <div class="p-4 rounded-xl border-2 transition-all" id="differenceCard">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold text-gray-600 dark:text-gray-400">الفرق (الفعلي - المتوقع)
                                    </p>
                                    <p class="text-2xl font-black mt-1" id="differenceAmount">—</p>
                                    <p class="text-xs mt-1" id="differenceLabel">—</p>
                                </div>
                                <svg class="w-12 h-12" id="differenceIcon" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Transfer All Checkbox --}}
                    <div class="md:col-span-2">
                        <label
                            class="flex items-center p-4 bg-purple-50 dark:bg-purple-500/10 rounded-xl border border-purple-200 dark:border-purple-500/20 cursor-pointer hover:bg-purple-100 dark:hover:bg-purple-500/20 transition-all">
                            <input type="checkbox" id="transferAll"
                                class="w-5 h-5 text-brand-500 bg-gray-100 border-gray-300 rounded focus:ring-brand-500 dark:focus:ring-brand-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <span class="mr-3 text-sm font-bold text-gray-900 dark:text-white">تحويل كامل المبلغ إلى المركز
                                (إفراغ الصندوق بالكامل)</span>
                        </label>
                    </div>

                    {{-- Transferred Amount --}}
                    <div class="form-group md:col-span-2">
                        <label for="transferred_amount"
                            class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            المبلغ المحول إلى المركز الرئيسي <span class="text-danger-500">*</span>
                        </label>
                        <input type="number" name="transferred_amount" id="transferred_amount"
                            class="w-full h-14 px-4 text-lg font-bold bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            step="0.01" min="0" placeholder="0.00" value="{{ old('transferred_amount', '0') }}" required>
                        <p class="mt-2 text-xs text-gray-500">المبلغ الذي سيتم تحويله فعلياً للمركز</p>
                        @error('transferred_amount')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remaining Cash Display --}}
                    <div class="md:col-span-2">
                        <div
                            class="p-4 bg-success-50 dark:bg-success-500/10 rounded-xl border border-success-200 dark:border-success-500/20">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold text-gray-600 dark:text-gray-400">الرصيد المتبقي في الصندوق
                                    </p>
                                    <p class="text-2xl font-black text-success-600 dark:text-success-400 mt-1"
                                        id="remainingCash">0.00 YER</p>
                                    <p class="text-xs text-gray-500 mt-1">النقد الفعلي - المبلغ المحول</p>
                                </div>
                                <svg class="w-12 h-12 text-success-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="form-group md:col-span-2">
                        <label for="notes" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            ملاحظات (اختياري)
                        </label>
                        <textarea name="notes" id="notes"
                            class="w-full px-4 py-3 text-sm font-medium bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white resize-none"
                            rows="3" placeholder="أي ملاحظات إضافية حول الإقفال...">{{ old('notes') }}</textarea>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        تأكيد الإقفال
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Vanilla JavaScript for Dynamic Calculations --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const systemBalanceValue = parseFloat(document.getElementById('systemBalanceValue').value) || 0;
            const actualCashInput = document.getElementById('actual_cash');
            const transferredAmountInput = document.getElementById('transferred_amount');
            const transferAllCheckbox = document.getElementById('transferAll');

            const differenceCard = document.getElementById('differenceCard');
            const differenceAmount = document.getElementById('differenceAmount');
            const differenceLabel = document.getElementById('differenceLabel');
            const differenceIcon = document.getElementById('differenceIcon');
            const remainingCashDisplay = document.getElementById('remainingCash');

            function formatCurrency(value) {
                return new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(value) + ' YER';
            }

            function updateDifference() {
                const actualCash = parseFloat(actualCashInput.value) || 0;
                const difference = actualCash - systemBalanceValue;

                if (actualCashInput.value === '') {
                    differenceAmount.textContent = '—';
                    differenceLabel.textContent = '—';
                    differenceCard.className = 'p-4 rounded-xl border-2 transition-all border-gray-200 dark:border-gray-700';
                    differenceIcon.className = 'w-12 h-12 text-gray-400';
                } else if (difference < 0) {
                    // Shortage (negative)
                    differenceAmount.textContent = formatCurrency(Math.abs(difference));
                    differenceAmount.className = 'text-2xl font-black mt-1 text-danger-500';
                    differenceLabel.textContent = 'نقص في الصندوق (عجز)';
                    differenceLabel.className = 'text-xs mt-1 text-danger-600 dark:text-danger-400';
                    differenceCard.className = 'p-4 rounded-xl border-2 transition-all border-danger-200 dark:border-danger-500/20 bg-danger-50 dark:bg-danger-500/10';
                    differenceIcon.className = 'w-12 h-12 text-danger-500';
                    differenceIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />';
                } else if (difference > 0) {
                    // Surplus (positive)
                    differenceAmount.textContent = formatCurrency(difference);
                    differenceAmount.className = 'text-2xl font-black mt-1 text-success-500';
                    differenceLabel.textContent = 'زيادة في الصندوق (فائض)';
                    differenceLabel.className = 'text-xs mt-1 text-success-600 dark:text-success-400';
                    differenceCard.className = 'p-4 rounded-xl border-2 transition-all border-success-200 dark:border-success-500/20 bg-success-50 dark:bg-success-500/10';
                    differenceIcon.className = 'w-12 h-12 text-success-500';
                    differenceIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                } else {
                    // Exact match
                    differenceAmount.textContent = formatCurrency(0);
                    differenceAmount.className = 'text-2xl font-black mt-1 text-gray-700 dark:text-gray-300';
                    differenceLabel.textContent = 'مطابق تماماً (لا يوجد فرق)';
                    differenceLabel.className = 'text-xs mt-1 text-gray-600 dark:text-gray-400';
                    differenceCard.className = 'p-4 rounded-xl border-2 transition-all border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800';
                    differenceIcon.className = 'w-12 h-12 text-gray-500';
                    differenceIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                }

                updateRemainingCash();
            }

            function updateRemainingCash() {
                const actualCash = parseFloat(actualCashInput.value) || 0;
                const transferred = parseFloat(transferredAmountInput.value) || 0;
                const remaining = actualCash - transferred;

                remainingCashDisplay.textContent = formatCurrency(remaining >= 0 ? remaining : 0);
            }

            function handleTransferAllChange() {
                if (transferAllCheckbox.checked) {
                    const actualCash = parseFloat(actualCashInput.value) || 0;
                    transferredAmountInput.value = actualCash.toFixed(2);
                } else {
                    transferredAmountInput.value = '0.00';
                }
                updateRemainingCash();
            }

            // Event listeners
            actualCashInput.addEventListener('input', function () {
                updateDifference();
                if (transferAllCheckbox.checked) {
                    transferredAmountInput.value = (parseFloat(this.value) || 0).toFixed(2);
                }
            });

            transferredAmountInput.addEventListener('input', updateRemainingCash);
            transferAllCheckbox.addEventListener('change', handleTransferAllChange);

            // Initialize
            updateDifference();
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