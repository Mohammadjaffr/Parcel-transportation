{{-- Modal إضافة دفعة للحزمة --}}
<div x-show="paymentModalOpen" x-cloak
    class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
    @click.self="paymentModalOpen = false">
    <div @click.away="paymentModalOpen = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="w-full max-w-md bg-white rounded-2xl shadow-2xl dark:bg-gray-900">

        <form action="{{ route('branch-package-payment.store') }}" method="POST"
            @submit.prevent="submitPayment($event)">
            @csrf

            <input type="hidden" name="branch_shipment_package_id" x-model="selectedPackage.pivot_id">

            {{-- Header --}}
            <div class="flex gap-3 justify-between items-center p-4 border-b border-gray-100 dark:border-gray-800">
                <div class="flex gap-2 items-center">
                    <div class="p-2 bg-brand-50 rounded-xl dark:bg-brand-500/10">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 dark:text-white">تسجيل دفعة</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <span x-text="selectedPackage.tracking_number"></span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="paymentModalOpen = false"
                    class="p-1.5 text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-4 space-y-3">
                {{-- Summary --}}
                <div class="grid grid-cols-3 gap-2 p-3 bg-gray-50 rounded-xl dark:bg-gray-800/50">
                    <div class="text-center">
                        <p class="text-[10px] text-gray-500">المستحق</p>
                        <p class="text-xs font-black text-gray-900 dark:text-white">
                            <span x-text="formatNumber(selectedPackage.total_amount)"></span>
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-gray-500">المدفوع</p>
                        <p class="text-xs font-black text-success-600">
                            <span x-text="formatNumber(selectedPackage.paid_amount)"></span>
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-gray-500">المتبقي</p>
                        <p class="text-xs font-black text-rose-600">
                            <span x-text="formatNumber(selectedPackage.remaining_amount)"></span>
                        </p>
                    </div>
                </div>

                {{-- المبلغ المدفوع --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">المبلغ المدفوع <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="paid_amount" step="0.01" required :max="selectedPackage.remaining_amount"
                        class="w-full px-3 h-10 text-sm bg-gray-50 rounded-lg border-none dark:bg-gray-800 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                        placeholder="المبلغ">
                </div>

                {{-- طريقة الدفع --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">طريقة الدفع <span
                            class="text-red-500">*</span></label>
                    <select name="payment_method" required
                        class="w-full px-3 h-10 text-sm bg-gray-50 rounded-lg border-none dark:bg-gray-800 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                        <option value="cash">نقدي</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="check">شيك</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                {{-- رقم السند --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">رقم السند</label>
                    <input type="text" name="bond_number"
                        class="w-full px-3 h-10 text-sm bg-gray-50 rounded-lg border-none dark:bg-gray-800 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                        placeholder="اختياري">
                </div>

                {{-- تاريخ الدفع --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">تاريخ الدفع <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" required :value="new Date().toISOString().split('T')[0]"
                        class="w-full px-3 h-10 text-sm bg-gray-50 rounded-lg border-none dark:bg-gray-800 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                </div>

                {{-- ملاحظات --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-300">ملاحظات</label>
                    <textarea name="notes" rows="2"
                        class="w-full px-3 py-2 text-sm bg-gray-50 rounded-lg border-none dark:bg-gray-800 focus:ring-2 focus:ring-brand-500/20 dark:text-white resize-none"
                        placeholder="اختياري"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div
                class="flex gap-2 justify-end p-4 bg-gray-50 border-t border-gray-100 dark:bg-gray-800/50 dark:border-gray-800">
                <button type="button" @click="paymentModalOpen = false"
                    class="px-4 h-9 text-xs font-bold text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-100 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-800">
                    إلغاء
                </button>
                <button type="submit" :disabled="isSubmitting"
                    class="flex gap-1.5 items-center px-5 h-9 text-xs font-bold text-white rounded-lg bg-brand-500 hover:bg-brand-600 disabled:opacity-50">
                    <svg x-show="isSubmitting" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isSubmitting ? 'جاري...' : 'حفظ'"></span>
                </button>
            </div>
        </form>
    </div>
</div>