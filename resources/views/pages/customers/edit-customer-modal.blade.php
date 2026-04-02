<div x-show="editModalOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
    x-cloak style="display: none;">
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
    </div>

    <div @click.outside="editModalOpen = false"
        class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10" dir="rtl">

        <form method="POST" :action="`/customers/${editCustomer.id}`" @submit="isUpdating = true">
            @csrf
            @method('PUT')
            <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90 font-outfit">
                تعديل بيانات العميل
            </h4>

            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label for="edit_name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        اسم العميل <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                    </label>
                    <input type="text" id="edit_name" name="name" required x-model="editCustomer.name"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                </div>

                {{-- رقم الجوال مع العلم --}}
                <div class="sm:col-span-1">
                    <label for="edit_phone_display"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        رقم الجوال <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                    </label>
                    <x-country-select name="phone" model="editCustomer.phone" :value="old('phone')" />
                </div>

                {{-- رقم الواتساب مع العلم --}}
                <div class="sm:col-span-1">
                    <label for="edit_whatsapp_display"
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        رقم الواتساب <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">(اختياري)</span>
                    </label>
                    <x-country-select name="whatsapp_number" model="editCustomer.whatsapp_number" :value="old('whatsapp_number')" />
                </div>

            </div>

            <div class="flex items-center justify-end w-full gap-3 mt-6">
                <button @click="editModalOpen = false" type="button"
                    class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
                    إغلاق
                </button>
                <button type="submit" :disabled="isUpdating"
                    class="flex items-center justify-center gap-2 hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed transition-all">
                    <svg x-show="isUpdating" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    تاكيد
                    <span x-text="isUpdating ? 'جاري التحديث...' : 'تحديث البيانات'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
