{{-- Payments List Modal Component --}}
<div x-show="paymentsListModalOpen" x-cloak
    class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto modal z-99999"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    {{-- Overlay --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        @click="closePaymentsListModal()"></div>

    {{-- Modal Content --}}
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 dark:border-gray-800"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95">

        {{-- Header --}}
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-brand-50 dark:bg-brand-500 rounded-2xl">
                        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 dark:text-white">سجل الدفعات</h3>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400">عرض جميع الدفعات المسجلة</p>
                    </div>
                </div>
                <button @click="closePaymentsListModal()"
                    class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-2xl transition-colors">
                    <svg class="w-6 h-6 text-brand-500 dark:text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-4 max-h-[65vh] overflow-y-auto">
            <template x-if="paymentsList.length === 0">
                <div class="text-center py-12">
                    <div
                        class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 dark:text-white font-bold text-base">لا توجد دفعات مسجلة</h3>
                    <p class="text-xs text-gray-400 mt-1">لم يتم تسجيل أي دفعات لهذه الشحنة حتى الآن</p>
                </div>
            </template>

            <template x-if="paymentsList.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <template x-for="(payment, index) in paymentsList" :key="payment.id">
                        <div
                            class="group p-3 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-brand-300 dark:hover:border-brand-500/50 hover:shadow-md transition-all">
                            <div class="flex items-start gap-2.5">
                                {{-- Number Badge --}}
                                <div
                                    class="flex-shrink-0 w-7 h-7 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-500 flex items-center justify-center text-white font-bold text-xs shadow-md">
                                    <span x-text="index + 1"></span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    {{-- Amount --}}
                                    <div class="flex items-baseline gap-2 mb-1">
                                        <span class="text-lg font-black text-gray-900 dark:text-white"
                                            x-text="parseFloat(payment.amount).toLocaleString('ar-YE')"></span>
                                        <span class="text-xs font-bold text-gray-500">ر.ي</span>
                                    </div>

                                    {{-- Date --}}
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-3 h-3 text-gray-400 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-[10px] font-bold text-gray-500 dark:text-gray-300 truncate"
                                            x-text="new Date(payment.payment_date).toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric' })"></span>
                                    </div>

                                    {{-- Payment Method & Reference --}}
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-2xl text-[10px] font-bold"
                                            :class="payment.payment_method === 'cash' ? 'bg-success-50 text-success-700 border border-success-200 dark:bg-success-500/10 dark:text-success-400' : 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400'">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <span x-text="payment.payment_method === 'cash' ? 'كاش' : 'تحويل'"></span>
                                        </span>

                                        <template x-if="payment.reference_number">
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-2xl text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 truncate max-w-[120px]">
                                                <svg class="w-2.5 h-2.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                                </svg>
                                                <span class="truncate" x-text="payment.reference_number"></span>
                                            </span>
                                        </template>
                                    </div>

                                    {{-- Notes --}}
                                    <template x-if="payment.notes">
                                        <div
                                            class="mt-2 p-2 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700">
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400 line-clamp-2"
                                                x-text="payment.notes"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <button @click="closePaymentsListModal()"
                class="w-full h-10 px-4 flex items-center justify-center gap-2 bg-brand-500 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-white dark:text-gray-300 font-bold text-sm rounded-2xl hover:bg-brand-100 dark:hover:bg-gray-800 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                إغلاق
            </button>
        </div>
    </div>
</div>