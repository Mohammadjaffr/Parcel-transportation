<div x-data="{
    isModalOpen: @if (session('closing_modal_open')) true @else false @endif,
    isLoading: false,
    systemBalance: {{ $systemBalance ?? 0 }},
    actualCash: '',
    transferredAmount: '0',
    transferAll: false,
    get difference() {
        if (this.actualCash === '') return null;
        return parseFloat(this.actualCash || 0) - this.systemBalance;
    },
    get remaining() {
        return Math.max(0, parseFloat(this.actualCash || 0) - parseFloat(this.transferredAmount || 0));
    },
    formatNumber(value) {
        return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
    },
    handleTransferAll() {
        if (this.transferAll) {
            this.transferredAmount = this.actualCash || '0';
        } else {
            this.transferredAmount = '0';
        }
    }
}" @open-closing-modal.window="isModalOpen = true">

    {{-- Open Modal Button --}}
    <button @click="isModalOpen = true"
        class="inline-flex gap-2 items-center px-4 h-10 text-xs font-bold text-white rounded-xl transition-all duration-200 bg-brand-500 hover:bg-brand-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        إقفال يومي
    </button>

    {{-- Modal --}}
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
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">إقفال الصندوق اليومي</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">عد النقد وتحويل المبالغ للمركز</p>
                </div>
            </div>

            {{-- System Balance Display --}}
            <div
                class="flex justify-between items-center p-4 mb-5 rounded-xl border bg-brand-50 dark:bg-brand-500/10 border-brand-100 dark:border-brand-500/20">
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">الرصيد المتوقع</p>
                    <p class="text-xl font-black text-brand-500" x-text="formatNumber(systemBalance) + ' ر.ي'"></p>
                </div>
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-100 dark:bg-brand-500/20">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('closings.store') }}" @submit="isLoading = true">
                @csrf

                <div class="space-y-4">

                    {{-- Actual Cash --}}
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            النقد الفعلي المعدود <span class="text-error-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="actual_cash" x-model="actualCash"
                                @input="if(transferAll) transferredAmount = actualCash"
                                class="pr-4 pl-14 my-2 w-full h-12 text-lg font-bold border border-gray-200 transition-all rouded-xl bg-2-gray-50 my dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                                step="0.01" min="0" placeholder="0.00" required>
                            <span
                                class="absolute left-4 top-1/2 text-xs font-bold -translate-y-1/2 text-brand-500">ر.ي</span>
                        </div>
                    </div>

                    {{-- Difference Display --}}
                    <div x-show="difference !== null" x-transition class="p-3 my-2 rounded-xl border"
                        :class="{
                            'bg-error-50 border-error-200 dark:bg-error-500/10 dark:border-error-500/20': difference <
                                0,
                            'bg-success-50 border-success-200 dark:bg-success-500/10 dark:border-success-500/20': difference >
                                0,
                            'bg-brand-50 border-brand-200 dark:bg-brand-500/10 dark:border-brand-500/20': difference ===
                                0
                        }">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs font-bold text-gray-500">الفرق</p>
                                <p class="text-lg font-black"
                                    :class="{
                                        'text-error-500': difference < 0,
                                        'text-success-500': difference > 0,
                                        'text-brand-500': difference === 0
                                    }"
                                    x-text="formatNumber(Math.abs(difference))"></p>
                            </div>
                            <span class="px-2 py-1 text-xs font-bold rounded-lg"
                                :class="{
                                    'bg-error-100 text-error-600 dark:bg-error-500/20': difference < 0,
                                    'bg-success-100 text-success-600 dark:bg-success-500/20': difference > 0,
                                    'bg-brand-100 text-brand-600 dark:bg-brand-500/20': difference === 0
                                }"
                                x-text="difference < 0 ? 'عجز' : (difference > 0 ? 'فائض' : 'مطابق ✓')"></span>
                        </div>
                    </div>

                    {{-- Transfer All --}}
                    <label
                        class="flex gap-3 items-center p-3 rounded-xl border transition-all cursor-pointer bg-brand-50 border-brand-200 dark:bg-brand-500/10 dark:border-brand-500/20 hover:bg-brand-100 dark:hover:bg-brand-500/20">
                        <input type="checkbox" x-model="transferAll" @change="handleTransferAll()"
                            class="w-4 h-4 bg-white rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:bg-gray-800">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">تحويل كامل المبلغ للمركز</span>
                    </label>

                    {{-- Transferred Amount --}}
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            المبلغ المحول <span class="text-error-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="transferred_amount" x-model="transferredAmount"
                                class="pr-4 pl-14 my-2 w-full h-12 text-lg font-bold bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                                step="0.01" min="0" placeholder="0.00" required>
                            <span
                                class="absolute left-4 top-1/2 text-xs font-bold -translate-y-1/2 text-brand-500">ر.ي</span>
                        </div>
                    </div>

                    {{-- Remaining Display --}}
                    <div
                        class="flex justify-between items-center p-3 my-2 rounded-xl border bg-success-50 dark:bg-success-500/10 border-success-200 dark:border-success-500/20">
                        <div>
                            <p class="text-xs font-bold text-gray-500">المتبقي في الصندوق</p>
                            <p class="text-lg font-black text-success-500" x-text="formatNumber(remaining) + ' ر.ي'">
                            </p>
                        </div>
                        <div
                            class="flex justify-center items-center w-8 h-8 rounded-lg bg-success-100 dark:bg-success-500/20">
                            <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            ملاحظات <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <textarea name="notes"
                            class="px-4 py-3 w-full text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all resize-none dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            rows="2" placeholder="ملاحظات إضافية..."></textarea>
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="flex gap-3 pt-4 mt-5 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="isModalOpen = false"
                        class="flex flex-1 gap-2 justify-center items-center h-11 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl transition-all dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        إلغاء
                    </button>
                    <button type="submit" :disabled="isLoading || !actualCash"
                        class="inline-flex flex-1 gap-2 justify-center items-center h-11 text-sm font-bold text-white rounded-xl shadow-lg transition-all duration-200 bg-brand-500 hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-brand-500/20">
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
                        <span x-text="isLoading ? 'جاري الإقفال...' : 'تأكيد الإقفال'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
