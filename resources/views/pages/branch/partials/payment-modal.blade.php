{{-- Payment Modal Component --}}
<div x-show="paymentModalOpen" x-cloak
    class="flex overflow-y-auto fixed inset-0 justify-center items-center p-4 modal z-99999"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    {{-- Overlay --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        @click="paymentModalOpen = false"></div>

    {{-- Modal Content --}}
    <div class="overflow-hidden relative mx-4 w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-2xl dark:bg-gray-900 dark:border-gray-800"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95">

        <form action="{{ route('branch-package-payment.store') }}" method="POST"
            @submit.prevent="submitPayment($event)" class="p-4 space-y-3">
            @csrf

            <input type="hidden" name="branch_shipment_package_id" x-model="selectedPackage.pivot_id">

            {{-- Header with Package Info --}}
            <div class="flex gap-2 items-center pb-3 border-b border-gray-100 dark:border-gray-800">
                <div class="p-1.5 rounded-lg bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white">تسجيل دفعة</h3>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">
                        <span x-text="selectedPackage.tracking_number"></span>
                    </p>
                </div>
            </div>

            {{-- Summary Stats --}}
            <div class="grid grid-cols-3 gap-2 p-2.5 bg-gray-50 rounded-lg dark:bg-gray-800/50">
                <div class="text-center">
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-0.5">المستحق</p>
                    <p class="text-xs font-black text-gray-900 dark:text-white">
                        <span x-text="formatNumber(selectedPackage.total_amount)"></span>
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-0.5">المدفوع</p>
                    <p class="text-xs font-black text-success-600">
                        <span x-text="formatNumber(selectedPackage.paid_amount)"></span>
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mb-0.5">المتبقي</p>
                    <p class="text-xs font-black text-error-500">
                        <span x-text="formatNumber(selectedPackage.remaining_amount)"></span>
                    </p>
                </div>
            </div>

            {{-- Amount and Payment Type in one row --}}
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2" x-data="{ paymentMethod: 'cash' }">
                {{-- Amount Field --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">المبلغ
                        المدفوع</label>
                    <div class="relative">
                        <input type="number" name="paid_amount" step="0.01" min="0" required
                            :max="selectedPackage.remaining_amount"
                            class="pr-4 pl-12 w-full h-12 text-lg font-bold text-gray-900 bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-white"
                            placeholder="أدخل المبلغ">
                        <span
                            class="absolute left-3 top-1/2 text-xs font-bold -translate-y-1/2 text-brand-500">ر.ي</span>
                    </div>
                </div>

                {{-- Payment Type --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">طريقة الدفع</label>
                    <div class="grid grid-cols-2 gap-1.5">
                        {{-- Cash Option --}}
                        <label @click="paymentMethod = 'cash'"
                            :class="paymentMethod === 'cash' ?
                                'border-brand-500 bg-brand-50 dark:bg-brand-500/10 ring-2 ring-brand-500/20' :
                                'border-gray-200 dark:border-gray-700 hover:border-brand-300'"
                            class="flex relative flex-col gap-1 justify-center items-center p-2 rounded-lg border transition-all cursor-pointer">
                            <input type="radio" name="payment_method" value="cash" x-model="paymentMethod"
                                class="sr-only">
                            <div :class="paymentMethod === 'cash' ? 'bg-brand-500 text-white' :
                                'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                                class="p-1.5 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span
                                :class="paymentMethod === 'cash' ? 'text-brand-600 dark:text-brand-400' :
                                    'text-gray-600 dark:text-gray-400'"
                                class="text-[10px] font-bold transition-colors">نقدي</span>
                        </label>

                        {{-- Bank Transfer Option --}}
                        <label @click="paymentMethod = 'bank_transfer'"
                            :class="paymentMethod === 'bank_transfer' ?
                                'border-brand-500 bg-brand-50 dark:bg-brand-500/10 ring-2 ring-brand-500/20' :
                                'border-gray-200 dark:border-gray-700 hover:border-brand-300'"
                            class="flex relative flex-col gap-1 justify-center items-center p-2 rounded-lg border transition-all cursor-pointer">
                            <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod"
                                class="sr-only">
                            <div :class="paymentMethod === 'bank_transfer' ? 'bg-brand-500 text-white' :
                                'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                                class="p-1.5 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <span
                                :class="paymentMethod === 'bank_transfer' ?
                                    'text-brand-600 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400'"
                                class="text-[10px] font-bold transition-colors">تحويل</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- رقم السند --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">رقم السند</label>
                <input type="text" name="bond_number"
                    class="pr-4 pl-12 w-full h-12 text-lg font-bold text-gray-900 bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-white"
                    placeholder="اختياري">
            </div>

            {{-- تاريخ الدفع --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">تاريخ الدفع</label>
                <input type="date" name="payment_date" required :value="new Date().toISOString().split('T')[0]"
                    class="pr-4 pl-12 w-full h-12 text-lg font-bold text-gray-900 bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-white">
            </div>

            {{-- Notes Field --}}
            <div>
                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">ملاحظات
                    (اختياري)</label>
                <textarea name="notes" rows="2"
                    class="px-3 py-2 w-full text-xs font-medium text-gray-900 bg-gray-50 rounded-lg border border-gray-200 transition-all resize-none dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-white"
                    placeholder="أضف ملاحظات..."></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 pt-1">
                <button type="button" @click="paymentModalOpen = false"
                    class="flex flex-1 gap-1.5 justify-center items-center px-3 h-9 text-xs font-bold text-gray-600 rounded-lg border border-gray-200 transition-all dark:border-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    إلغاء
                </button>
                <button type="submit" :disabled="isSubmitting"
                    class="flex flex-1 gap-2 justify-center items-center px-4 h-12 font-bold text-gray-600 rounded-xl border border-gray-200 transition-all dark:border-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <svg x-show="!isSubmitting" class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg x-show="isSubmitting" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isSubmitting ? 'جاري...' : 'تأكيد'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
