<div x-data="{ isModalOpen: @if (session('isModalOpen')) true @else false @endif, isLoading: false }">

    <button @click="isModalOpen = true"
        class="h-12 px-4 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl transition-all shadow-lg shadow-brand-500/20 active:scale-95 text-sm font-bold w-full md:w-auto">
        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        إضافة عميل جديد
    </button>

    <div x-show="isModalOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
        style="display: none;">
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
        </div>

        <div @click.outside="isModalOpen = false"
            class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

            <form method="POST" action="{{ route('customers.store') }}" @submit="isLoading = true">
                @csrf
                <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90">
                    إضافة عميل جديد
                </h4>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                    <div class="sm:col-span-2">
                        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            اسم العميل <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>
                        <input type="text" id="name" name="name" required placeholder="مثال: محمد علي"
                            value="{{ old('name') }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        <div class="text-xs text-error-600 mt-1">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <!-- Main container for the phone input component -->
                    <div class="col-span-1">
                        <label for="phone_number_display"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            رقم الجوال <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>

                        <x-country-select name="phone" :value="old('phone')" />
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                            <svg class="w-4 h-4 text-success-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.52 3.48A11.86 11.86 0 0012 0 11.93 11.93 0 000 12a11.88 11.88 0 001.67 6.06L0 24l6.12-1.6A12 12 0 0012 24a11.93 11.93 0 0012-12 11.9 11.9 0 00-3.48-8.52z" />
                            </svg>
                            <span>
                                ملاحظة: سيتم اعتماد هذا الرقم كرقم
                                <span class="font-semibold text-success-500 dark:text-success-400">واتساب</span>
                                للتواصل.
                            </span>
                        </p>
                    </div>



                    <div class="col-span-1">
                        <label for="whatsapp_number_display"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            رقم اضافي <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">(اختياري)</span>
                        </label>

                        <x-country-select name="whatsapp_number" :value="old('whatsapp_number')" />
                    </div>

                </div>

                <div class="flex items-center justify-end w-full gap-3 mt-6">
                    <button @click="isModalOpen = false" type="button"
                        class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
                        إغلاق
                    </button>
                    <button type="submit" :disabled="isLoading"
                        class="flex items-center justify-center gap-2 hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed transition-all">
                        <!-- Loading Spinner -->
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isLoading ? 'جاري الإضافة...' : 'إضافة العميل'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
