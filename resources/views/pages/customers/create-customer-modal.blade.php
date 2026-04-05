<div x-data="{ isModalOpen: @if (session('isModalOpen')) true @else false @endif, isLoading: false }">

    <button @click="isModalOpen = true"
        class="flex gap-2 justify-center items-center px-4 w-full h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/20 active:scale-95 md:w-auto">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        إضافة عميل جديد
    </button>

    <div x-show="isModalOpen" class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999"
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

                <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-2">

                    <div class="sm:col-span-2">
                        <label for="name" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                            اسم العميل <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>
                        <input type="text" id="name" name="name" required placeholder="مثال: محمد علي"
                            value="{{ old('name') }}"
                            class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        <div class="mt-1 text-xs text-error-600">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <!-- Main container for the phone input component -->
                    <div class="col-span-1">
                        <label for="phone_number_display"
                            class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                            رقم الجوال <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>

                        <x-country-select name="phone" :value="old('phone')" />
                        <p class="flex gap-2 items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
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


{{-- 
                    <div class="col-span-1">
                        <label for="whatsapp_number_display"
                            class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                            رقم اضافي <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">(اختياري)</span>
                        </label>

                        <x-country-select name="whatsapp_number" :value="old('whatsapp_number')" />
                    </div> --}}

                </div>

                <div class="flex gap-3 justify-end items-center mt-6 w-full">
                    <button @click="isModalOpen = false" type="button"
                        class="flex justify-center px-4 py-3 w-full text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:border-brand-500 sm:w-auto">
                        إغلاق
                    </button>
                    <button type="submit" :disabled="isLoading"
                        class="flex gap-2 justify-center items-center px-4 py-3 w-full text-sm font-medium text-white rounded-lg transition-all hover:bg-brand-600 bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed">
                        <!-- Loading Spinner -->
                        <svg x-show="isLoading" class="w-5 h-5 text-white animate-spin"
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
