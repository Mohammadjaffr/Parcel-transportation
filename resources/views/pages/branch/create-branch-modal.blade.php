{{-- Modal إضافة فرع جديد --}}
<div x-show="createModalOpen" x-cloak
    class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    {{-- Overlay --}}
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        @click="createModalOpen = false"></div>

    {{-- Modal Content --}}
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden border border-gray-100 dark:border-gray-800"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95">

        {{-- Header --}}
        <div
            class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-brand-500 to-brand-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-xl">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">إضافة فرع جديد</h3>
                </div>
                <button @click="createModalOpen = false" class="p-2 hover:bg-white/20 rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <form action="{{ route('branch.store') }}" method="POST" class="p-6 space-y-5" x-data="{ isSubmitting: false }"
            @submit="isSubmitting = true">
            @csrf

            {{-- اسم الفرع --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اسم الفرع</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full h-12 px-4 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                    placeholder="ادخل اسم الفرع">
                @error('name')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- رمز الفرع --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">رمز الفرع</label>
                <input type="text" name="code" value="{{ old('code') }}"
                    class="w-full h-12 px-4 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                    placeholder="ادخل رمز الفرع">
                @error('code')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- المدينة --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المدينة</label>
                <input type="text" name="city" value="{{ old('city') }}"
                    class="w-full h-12 px-4 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                    placeholder="ادخل المدينة">
                @error('city')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- العنوان --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">العنوان</label>
                <input type="text" name="address" value="{{ old('address') }}"
                    class="w-full h-12 px-4 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                    placeholder="ادخل العنوان">
                @error('address')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- الهاتف --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full h-12 px-4 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                    placeholder="رقم هاتف الفرع">
                @error('phone')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="button" @click="createModalOpen = false"
                    class="flex-1 h-12 px-4 flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    إلغاء
                </button>
                <button type="submit" :disabled="isSubmitting"
                    class="flex-1 h-12 px-4 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl shadow-lg shadow-brand-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="!isSubmitting">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            إضافة الفرع
                        </span>
                    </template>
                    <template x-if="isSubmitting">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            جاري الإضافة...
                        </span>
                    </template>
                </button>
            </div>
        </form>
    </div>
</div>
