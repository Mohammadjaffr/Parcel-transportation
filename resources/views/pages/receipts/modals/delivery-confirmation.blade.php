{{-- Delivery Confirmation Modal --}}
<div x-cloak x-show="deliveryModal.open"
    class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999" style="display: none;">

    {{-- Overlay --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px] transition-opacity duration-300"
        x-show="deliveryModal.open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="closeDeliveryModal()">
    </div>

    {{-- Modal Content --}}
    <div x-show="deliveryModal.open"
        class="relative w-full max-w-md rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10 shadow-2xl overflow-hidden z-10"
        @click.outside="closeDeliveryModal()" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4">

        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">تأكيد الاستلام والدفع</h3>
            <button @click="closeDeliveryModal()"
                class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            هذا الطرد <strong>غير مدفوع</strong>. يرجى تأكيد استلام المبلغ من العميل لإتمام عملية التسليم.
        </p>

        <form :action="`/receipt-items/${deliveryModal.itemId}/toggle-delivery`" method="POST"
            x-data="{ isLoading: false }" @submit="isLoading = true">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                    المبلغ المستلم <span class="text-error-500">*</span>
                </label>
                <div class="relative">
                    <input type="number" name="received_amount" x-model="deliveryModal.amount" required readonly
                        class="w-full px-4 py-3 text-lg font-bold text-center border border-gray-300 rounded-xl bg-gray-100 text-gray-500 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 placeholder-gray-400 focus:outline-none"
                        placeholder="0">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">ر.ي</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="closeDeliveryModal()"
                    class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto hover:bg-gray-50 transition-all">
                    إلغاء
                </button>
                <button type="submit" :disabled="isLoading"
                    class="flex items-center justify-center gap-2 hover:bg-success-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-success-500 disabled:opacity-75 disabled:cursor-not-allowed transition-all shadow-lg shadow-success-500/20 active:scale-95 sm:w-auto">
                    <!-- Loading Spinner -->
                    <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isLoading ? 'جاري التأكيد...' : 'تأكيد الاستلام'"></span>
                </button>
            </div>
        </form>
    </div>
</div>