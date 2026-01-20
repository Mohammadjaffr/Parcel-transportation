{{-- Clear Amount Modal - يظهر فقط للعملاء المديونين --}}
@props(['customer'])

@php
    $customerBalance = ($customer->debit_sum ?? 0) - ($customer->credit_sum ?? 0);
@endphp

<div x-data="{
    isModalOpen: false,
    isLoading: false
}">
    {{-- Open Modal Button - أيقونة تصفية الحساب --}}
    <button @click="isModalOpen = true"
        class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-success-50 hover:text-success-600 hover:shadow-sm dark:hover:bg-success-500/10 dark:hover:text-success-400"
        title="تصفية حساب العميل">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>

    {{-- Modal Backdrop & Container --}}
    <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 z-99999" style="display: none;">

        {{-- Backdrop --}}
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
            @click="isModalOpen = false"></div>

        {{-- Modal Content --}}
        <div @click.outside="isModalOpen = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative p-6 w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-2xl dark:bg-gray-900 dark:border-gray-800">

            {{-- Modal Header --}}
            <div class="flex gap-3 items-center pb-4 mb-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10">
                    <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">تصفية حساب العميل</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">تسجيل دفعة لتصفية الديون المستحقة</p>
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="p-4 mb-5 bg-gray-50 rounded-xl border border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                <div class="flex gap-3 items-center">
                    <div
                        class="flex justify-center items-center w-12 h-12 text-lg font-bold rounded-xl text-brand-500 bg-brand-50 dark:bg-brand-500/10">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <h5 class="font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h5>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->phone }}</p>
                    </div>
                    <div class="text-left">
                        <span class="text-xs text-gray-500 dark:text-gray-400">المبلغ المستحق</span>
                        <p class="text-lg font-black text-error-500">{{ number_format($customerBalance) }} <span
                                class="text-xs">ر.ي</span></p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('customers.clear-balance', $customer->id) }}"
                @submit="isLoading = true">
                @csrf

                <div class="space-y-5">

                    {{-- Amount --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            المبلغ المدفوع <span class="text-error-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="amount"
                                class="pr-4 pl-14 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                                step="0.01" min="0.01" placeholder="0.00" value="{{ $customerBalance }}"
                                required>
                            <span
                                class="absolute left-4 top-1/2 text-xs font-bold -translate-y-1/2 text-brand-500">ر.ي</span>
                        </div>
                        @error('amount')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            طريقة الدفع <span class="text-error-500">*</span>
                        </label>
                        <select name="payment_method"
                            class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            required>
                            <option value="cash">نقداً</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                            <option value="check">شيك</option>
                        </select>
                    </div>

                    {{-- Reference Number --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            رقم السند/المرجع <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <input type="text" name="reference_number"
                            class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                            placeholder="أدخل رقم السند أو المرجع">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            ملاحظات <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <textarea name="notes"
                            class="px-4 py-3 w-full text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all resize-none dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                            rows="2" placeholder="أضف ملاحظات إن وجدت..."></textarea>
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="flex gap-3 pt-4 mt-6 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="isModalOpen = false"
                        class="flex flex-1 gap-2 justify-center items-center h-11 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl transition-all dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        إلغاء
                    </button>
                    <button type="submit" :disabled="isLoading"
                        class="inline-flex flex-1 gap-2 justify-center items-center h-11 text-sm font-semibold text-white rounded-xl shadow-lg transition-all duration-200 bg-success-500 hover:bg-success-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-success-500/20">
                        {{-- Loading Spinner --}}
                        <svg x-show="isLoading" class="w-4 h-4 text-white animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-text="isLoading ? 'جاري الحفظ...' : 'تأكيد الدفع'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
