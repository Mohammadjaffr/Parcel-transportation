{{-- Modal: تعديل الطرد --}}
<div x-show="showEditItemModal" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
    style="display: none;">

    {{-- Overlay --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        @click="showEditItemModal = false">
    </div>

    {{-- Modal Content --}}
    <div x-show="showEditItemModal" @click.outside="showEditItemModal = false"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10 shadow-2xl overflow-hidden z-10">

        <template x-if="editingItem">
            <form method="POST"
                :action="'{{ route('receipt-items.update', ['item' => 'ITEM_ID']) }}'.replace('ITEM_ID', editingItem.id)"
                x-data="{ isLoading: false, payment_status: editingItem.payment_status || 'unpaid', amount: editingItem.amount, prevAmount: editingItem.amount || 0 }"
                @submit="isLoading = true">
                @csrf
                @method('PUT')

                <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90">
                    تعديل الطرد
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400 mx-2"
                        x-text="'#' + editingItem.number"></span>
                </h4>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                    {{-- رقم السند --}}
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            رقم السند <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="number" :value="editingItem.number" required placeholder="رقم السند"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- نوع الطرد --}}
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            نوع الطرد <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="package_type" :value="editingItem.package_type" required
                            placeholder="صندوق، مغلف، ..."
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- اسم المرسل --}}
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            اسم المرسل
                        </label>
                        <input type="text" name="sender_name" :value="editingItem.sender_name" placeholder="اسم المرسل"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- اسم المستلم --}}
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            اسم المستلم <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="receiver_name" :value="editingItem.receiver_name" required
                            placeholder="اسم المستلم"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- رقم هاتف المستلم (x-country-select) --}}
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            رقم هاتف المستلم <span class="text-error-500">*</span>
                        </label>
                        {{-- Note: we use x-effect to update the component when editingItem changes --}}
                        <x-country-select name="receiver_phone" model="editingItem.receiver_phone" />
                    </div>

                    {{-- ملاحظات --}}
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            ملاحظات
                        </label>
                        <input type="text" name="item_notes" :value="editingItem.item_notes"
                            placeholder="ملاحظات (اختياري)"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    </div>

                    {{-- Payment Status --}}
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            حالة الدفع <span class="text-error-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            {{-- Unpaid Option --}}
                            <div class="relative w-full">
                                <label class="cursor-pointer w-full group">
                                    <input type="radio" name="payment_status" value="unpaid" x-model="payment_status"
                                        @change="amount = prevAmount" class="peer sr-only">
                                    <div class="w-full h-11 flex items-center justify-center px-4 rounded-xl border transition-all duration-200 ease-in-out"
                                        :class="payment_status === 'unpaid' ? 'border-brand-500 bg-brand-50 text-brand-600 font-bold ring-1 ring-brand-500 shadow-sm dark:bg-brand-500/15 dark:text-brand-400 dark:border-brand-500 dark:ring-brand-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 group-hover:border-brand-300 group-hover:bg-brand-50/50 dark:group-hover:border-brand-500/30 dark:group-hover:bg-brand-500/10'">
                                        <span class="flex items-center gap-2">
                                            عند الاستلام
                                        </span>
                                    </div>
                                </label>
                            </div>
                            {{-- Paid Option --}}
                            <div class="relative w-full">
                                <label class="cursor-pointer w-full group">
                                    <input type="radio" name="payment_status" value="paid" x-model="payment_status"
                                        @change="prevAmount = (amount > 0 ? amount : prevAmount); amount = 0"
                                        class="peer sr-only">
                                    <div class="w-full h-11 flex items-center justify-center px-4 rounded-xl border transition-all duration-200 ease-in-out"
                                        :class="payment_status === 'paid' ? 'border-brand-500 bg-brand-50 text-brand-600 font-bold ring-1 ring-brand-500 shadow-sm dark:bg-brand-500/15 dark:text-brand-400 dark:border-brand-500 dark:ring-brand-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 group-hover:border-brand-300 group-hover:bg-brand-50/50 dark:group-hover:border-brand-500/30 dark:group-hover:bg-brand-500/10'">
                                        <span class="flex items-center gap-2">
                                            مدفوع
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            المبلغ <span class="text-error-500">*</span>
                        </label>
                        <input type="number" name="amount" x-model="amount" :readonly="payment_status === 'paid'"
                            @input="if(payment_status === 'unpaid') prevAmount = amount"
                            :class="payment_status === 'paid' ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-70 text-gray-500' : 'bg-transparent dark:text-white'"
                            placeholder="0"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 outline-none transition-all">
                    </div>

                </div>

                <div class="flex items-center justify-end w-full gap-3 mt-6">
                    <button @click="showEditItemModal = false" type="button"
                        class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto hover:bg-gray-50 transition-all">
                        إلغاء
                    </button>
                    <button type="submit" :disabled="isLoading"
                        class="flex items-center justify-center gap-2 hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed transition-all shadow-lg shadow-brand-500/20 active:scale-95 sm:w-auto">
                        <!-- Loading Spinner -->
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <!-- Default Icon -->
                        <svg x-show="!isLoading" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span x-text="isLoading ? 'جاري التحديث...' : 'حفظ التعديلات'"></span>
                    </button>
                </div>
            </form>
        </template>
    </div>
</div>