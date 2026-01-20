<div x-data="{ isModalOpen: @if ($errors->any() || session('category_modal_open')) true @else false @endif, isLoading: false, selectedType: '{{ old('type', '') }}' }">

    {{-- Open Modal Button --}}
    <button @click="isModalOpen = true"
        class="inline-flex gap-2 items-center px-5 h-11 text-sm font-semibold text-white rounded-xl transition-all duration-200 bg-brand-500 hover:bg-brand-600 focus:ring-4 focus:ring-brand-500/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        إضافة فئة جديدة
    </button>

    {{-- Modal Backdrop & Container --}}
    <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 z-99999" style="display: none;">

        {{-- Backdrop --}}
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="isModalOpen = false"></div>

        {{-- Modal Content --}}
        <div @click.outside="isModalOpen = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative p-6 w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-2xl dark:bg-gray-900 dark:border-gray-800">

            {{-- Modal Header --}}
            <div class="flex gap-3 items-center pb-4 mb-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10">
                    <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">إضافة فئة جديدة</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">تصنيف جديد للمعاملات المالية</p>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('transaction-categories.store') }}" @submit="isLoading = true">
                @csrf

                <div class="space-y-5">

                    {{-- Category Name --}}
                    <div>
                        <label for="modal_name"
                            class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            اسم الفئة <span class="text-error-500">*</span>
                        </label>
                        <input type="text" name="name" id="modal_name"
                            class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                            placeholder="مثال: رواتب الموظفين" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type Selection with Alpine.js --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            النوع <span class="text-error-500">*</span>
                        </label>

                        {{-- Hidden input for form submission --}}
                        <input type="hidden" name="type" :value="selectedType" required>

                        <div class="grid grid-cols-2 gap-3">
                            {{-- Income Option --}}
                            <button type="button" @click="selectedType = 'in'"
                                :class="selectedType === 'in'
                                    ?
                                    'bg-success-50 border-success-500 dark:bg-success-500/15 dark:border-success-400' :
                                    'bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                                class="flex flex-col gap-2 items-center p-4 rounded-xl border-2 transition-all duration-200 cursor-pointer">
                                <div :class="selectedType === 'in' ? 'bg-success-100 dark:bg-success-500/20' :
                                    'bg-gray-100 dark:bg-gray-700'"
                                    class="flex justify-center items-center w-12 h-12 rounded-xl transition-colors">
                                    <svg class="w-6 h-6 text-success-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                    </svg>
                                </div>
                                <span
                                    :class="selectedType === 'in' ? 'text-success-600 dark:text-success-400' :
                                        'text-gray-600 dark:text-gray-300'"
                                    class="text-sm font-bold transition-colors">إيراد / دخل</span>
                            </button>

                            {{-- Expense Option --}}
                            <button type="button" @click="selectedType = 'out'"
                                :class="selectedType === 'out'
                                    ?
                                    'bg-error-50 border-error-500 dark:bg-error-500/15 dark:border-error-400' :
                                    'bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                                class="flex flex-col gap-2 items-center p-4 rounded-xl border-2 transition-all duration-200 cursor-pointer">
                                <div :class="selectedType === 'out' ? 'bg-error-100 dark:bg-error-500/20' :
                                    'bg-gray-100 dark:bg-gray-700'"
                                    class="flex justify-center items-center w-12 h-12 rounded-xl transition-colors">
                                    <svg class="w-6 h-6 text-error-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                    </svg>
                                </div>
                                <span
                                    :class="selectedType === 'out' ? 'text-error-600 dark:text-error-400' :
                                        'text-gray-600 dark:text-gray-300'"
                                    class="text-sm font-bold transition-colors">مصروف / خروج</span>
                            </button>
                        </div>

                        @error('type')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- System Code --}}
                    <div>
                        <label for="modal_code"
                            class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            الرمز البرمجي <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <input type="text" name="code" id="modal_code"
                            class="px-4 w-full h-11 font-mono text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                            placeholder="SYSTEM_CODE" value="{{ old('code') }}">
                        <p class="mt-1.5 text-xs text-gray-400">للربط مع النظام فقط - اتركه فارغاً للاستخدام العادي</p>
                        @error('code')
                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Form Actions --}}
                <div
                    class="flex gap-3 justify-end items-center pt-4 mt-6 border-t border-gray-100 dark:border-gray-800">
                    <button @click="isModalOpen = false" type="button"
                        class="flex justify-center items-center px-5 h-11 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl transition-all dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                        إغلاق
                    </button>
                    <button type="submit" :disabled="isLoading || !selectedType"
                        class="inline-flex gap-2 justify-center items-center px-5 h-11 text-sm font-semibold text-white rounded-xl transition-all duration-200 bg-brand-500 hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{-- Loading Spinner --}}
                        <svg x-show="isLoading" class="w-5 h-5 text-white animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-text="isLoading ? 'جاري الإضافة...' : 'إضافة الفئة'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
