{{-- Payment Modal Component --}}
<div x-show="paymentModalOpen" x-cloak
    class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    {{-- Overlay --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        @click="closePaymentModal()"></div>

    {{-- Modal Content --}}
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 dark:border-gray-800"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95">
        {{-- Body --}}
        <form :action="`{{ url('/shipments') }}/${paymentData.shipmentId}/payment`" method="POST"
            enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf


            {{-- Amount and Payment Type in one row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Amount Field --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المبلغ
                        المستحق</label>
                    <div class="relative">
                        <input type="number" name="amount" x-model="paymentData.amount" step="0.01" min="0"
                            :max="paymentData.maxAmount"
                            class="w-full h-12 pr-4 pl-12 text-lg font-bold bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                            placeholder="أدخل المبلغ" required>
                        <span
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">ر.ي</span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        المبلغ المتبقي: <span class="font-bold text-error-500"
                            x-text="paymentData.maxAmount.toLocaleString()"></span> ر.ي
                    </p>
                </div>

                {{-- Payment Type --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">طريقة
                        الدفع</label>
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Cash Option --}}
                        <label :class="paymentData.paymentType === 'cash' ?
                                'border-brand-500 bg-brand-50 dark:bg-brand-500/10 ring-2 ring-brand-500/20' :
                                'border-gray-200 dark:border-gray-700 hover:border-brand-300'"
                            class="relative flex flex-row items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="cash" x-model="paymentData.paymentType"
                                class="sr-only">
                            <div :class="paymentData.paymentType === 'cash' ? 'bg-brand-500 text-white' :
                                'bg-gray-100 dark:bg-gray-800 text-gray-500'" class="p-2 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span :class="paymentData.paymentType === 'cash' ? 'text-brand-600 dark:text-brand-400' :
                                'text-gray-600 dark:text-gray-400'"
                                class="text-sm font-bold transition-colors">كاش</span>
                        </label>

                        {{-- Bank Transfer Option --}}
                        <label :class="paymentData.paymentType === 'bank_transfer' ?
                                'border-brand-500 bg-brand-50 dark:bg-brand-500/10 ring-2 ring-brand-500/20' :
                                'border-gray-200 dark:border-gray-700 hover:border-brand-300'"
                            class="relative flex flex-row items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="bank_transfer"
                                x-model="paymentData.paymentType" class="sr-only">
                            <div :class="paymentData.paymentType === 'bank_transfer' ? 'bg-brand-500 text-white' :
                                'bg-gray-100 dark:bg-gray-800 text-gray-500'" class="p-2 rounded-xl transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <span :class="paymentData.paymentType === 'bank_transfer' ?
                                    'text-brand-600 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400'"
                                class="text-sm font-bold transition-colors">تحويل بنكي</span>
                        </label>
                    </div>
                </div>
            </div>


            {{-- Reference Number (for bank transfer) --}}
            <div x-show="paymentData.paymentType === 'bank_transfer'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">رقم الإيداع /
                    المرجع</label>
                <div class="relative">
                    <input type="text" name="reference_number" x-model="paymentData.referenceNumber"
                        class="w-full h-12 pr-12 pl-4 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                        placeholder="أدخل رقم الإيداع أو المرجع">
                    <div class="absolute px-2 right-4 top-1/2 -translate-y-1/2">
                    </div>
                </div>
            </div>

            {{-- Attachment (for bank transfer) --}}
            <div x-show="paymentData.paymentType === 'bank_transfer'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">إيصال التحويل
                    (اختياري)</label>
                <input type="file" name="attachment" accept="image/*,.pdf"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
            </div>

            {{-- Notes Field --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات
                    (اختياري)</label>
                <textarea name="notes" x-model="paymentData.notes" rows="2"
                    class="w-full px-4 py-3 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all resize-none"
                    placeholder="أضف ملاحظات إن وجدت..."></textarea>
            </div>

            {{-- Hidden Fields --}}
            <input type="hidden" name="shipment_id" :value="paymentData.shipmentId">

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="button" @click="closePaymentModal()"
                    class="flex-1 h-12 px-4 flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    إلغاء
                </button>
                <button type="submit" :disabled="!paymentData.amount || paymentData.amount <= 0"
                    class="flex-1 h-12 px-4 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl shadow-lg shadow-brand-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    تأكيد الدفع
                </button>
            </div>
        </form>
    </div>
</div>