{{-- ======================== Edit Driver Modal ======================== --}}
<template x-teleport="body">
    <div x-cloak x-show="editModalOpen" 
         class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto sm:p-6 z-[2147483647]" 
         @keydown.escape.window="editModalOpen = false">

        {{-- Backdrop (الخلفية المعتمة) --}}
        <div x-show="editModalOpen"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 w-full h-full backdrop-blur-sm bg-gray-900/20"
             @click="editModalOpen = false">
        </div>

        {{-- Modal Panel (النافذة) --}}
        <div x-show="editModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="relative w-full max-w-md p-6 bg-white shadow-2xl rounded-[2rem] dark:bg-boxdark sm:p-8" dir="rtl">

            {{-- ربط الـ action بالرابط الذي جهزناه في الدالة editDriver.url --}}
            <form :action="editDriver.url" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf
                @method('PUT')

                {{-- Modal Header --}}
                <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100 dark:border-gray-800">
                    <button type="button" @click="editModalOpen = false"
                            class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-full transition-colors hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                    
                    <div class="flex gap-3 items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">تعديل بيانات السائق</h3>
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl shadow-inner bg-warning-50 text-warning-500">
                            <span class="material-symbols-outlined text-[22px]">edit_square</span>
                        </div>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="space-y-5 text-right">
                    
                    {{-- حقل اسم السائق --}}
                    <div>
                        <label for="edit_driver_name" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            اسم السائق <span class="text-red-500">*</span>
                        </label>
                        {{-- نستخدم x-model لربطه باسم السائق --}}
                        <input type="text" id="edit_driver_name" name="name" x-model="editDriver.name" required autocomplete="off" 
                               class="px-4 w-full h-12 text-sm placeholder-gray-400 text-right bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- حقل رقم الهاتف (مع ذكاء استخراج الدولة عند الفتح) --}}
                    <div x-data="{
                            open: false,
                            search: '',
                            selectedDialCode: '967',
                            selectedFlag: '🇾🇪',
                            localPhoneNumber: '',
                            countries: [
                                { name: 'اليمن', code: 'YE', dial_code: '967', flag: '🇾🇪' },
                                { name: 'السعودية', code: 'SA', dial_code: '966', flag: '🇸🇦' },
                                { name: 'الإمارات', code: 'AE', dial_code: '971', flag: '🇦🇪' },
                                { name: 'مصر', code: 'EG', dial_code: '20', flag: '🇪🇬' },
                            ],
                            init() {
                                // بمجرد أن يفتح المودال، نقوم بتحليل رقم السائق لاختيار العلم الصحيح
                                this.$watch('editModalOpen', (isOpen) => {
                                    if(isOpen) {
                                        let phone = editDriver.phone || '';
                                        phone = phone.replace('+', '');
                                        let sorted = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);
                                        let found = sorted.find(c => phone.startsWith(c.dial_code));
                                        
                                        if(found) {
                                            this.selectedDialCode = found.dial_code;
                                            this.selectedFlag = found.flag;
                                            this.localPhoneNumber = phone.substring(found.dial_code.length);
                                        } else {
                                            this.selectedDialCode = '967';
                                            this.selectedFlag = '🇾🇪';
                                            this.localPhoneNumber = phone;
                                        }
                                    }
                                });
                            },
                            get filteredCountries() {
                                if (this.search === '') return this.countries;
                                return this.countries.filter(c => c.name.includes(this.search) || c.dial_code.includes(this.search));
                            }
                        }" class="relative text-right">

                        <label for="edit_driver_phone" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            رقم الهاتف <span class="text-red-500">*</span>
                        </label>

                        {{-- الحقل المخفي الذي يذهب للسيرفر --}}
                        <input type="hidden" name="phone" :value="selectedDialCode + localPhoneNumber">

                        <div class="flex overflow-hidden items-center w-full h-12 bg-gray-50 rounded-xl border border-gray-200 transition-all focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:focus-within:border-primary">
                            
                            {{-- زر اختيار الدولة --}}
                            <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-3 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + selectedDialCode"></span>
                                <span class="text-lg leading-none" x-text="selectedFlag"></span>
                            </button>

                            {{-- حقل إدخال الرقم --}}
                            <input id="edit_driver_phone" x-ref="driver_phone" type="tel" x-model="localPhoneNumber" placeholder="771234567" autocomplete="off" required
                                   class="px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-none outline-none dark:text-white focus:ring-0" dir="ltr">
                        </div>

                        {{-- قائمة الدول --}}
                        <div x-cloak x-show="open" @click.outside="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-gray-700">
                            
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                       class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white focus:ring-1 focus:ring-primary">
                            </div>
                            
                            <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                <template x-for="country in filteredCountries" :key="country.code">
                                    <button type="button" @click="selectedDialCode = country.dial_code; selectedFlag = country.flag; open = false; $refs.driver_phone?.focus()"
                                            class="flex justify-between items-center px-4 py-2.5 w-full text-sm transition-colors hover:bg-primary/5 dark:hover:bg-gray-800">
                                        <div class="flex gap-3 items-center">
                                            <span class="text-lg leading-none" x-text="country.flag"></span>
                                            <span class="font-medium text-gray-700 dark:text-gray-300" x-text="country.name"></span>
                                        </div>
                                        <span class="font-mono text-gray-500 dark:text-gray-400" dir="ltr" x-text="'+' + country.dial_code"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        @error('phone')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex flex-row-reverse gap-3 justify-start items-center pt-6 mt-8 border-t border-gray-100 dark:border-gray-800">
                    
                    <button type="submit" :disabled="isSubmitting"
                            class="flex items-center justify-center gap-2 px-8 py-2.5 text-sm font-bold text-white transition-all rounded-xl bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed min-w-[120px]">
                        <template x-if="isSubmitting">
                            <svg class="w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!isSubmitting">
                            <span>تحديث</span>
                        </template>
                    </button>

                    <button type="button" @click="editModalOpen = false"
                            class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        إلغاء
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>