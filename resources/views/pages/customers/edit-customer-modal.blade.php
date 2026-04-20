{{-- ======================== Edit Customer Modal ======================== --}}
<template x-teleport="body">
    <div x-cloak x-show="editModalOpen" 
         class="fixed inset-0 z-[999999] flex items-center justify-center p-4 overflow-y-auto sm:p-6" 
         @keydown.escape.window="editModalOpen = false">

        {{-- Backdrop --}}
        <div x-show="editModalOpen"
             x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 w-full h-full backdrop-blur-sm bg-gray-900/60"
             @click="editModalOpen = false">
        </div>

        {{-- Modal Panel --}}
        <div x-show="editModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="relative w-full max-w-md p-6 bg-white shadow-2xl rounded-[2rem] dark:bg-boxdark-2 sm:p-8" dir="rtl">

            <form :action="editCustomer.url" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf
                @method('PUT')
                {{-- Modal Header --}}
                <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100 dark:border-gray-800">
                    <button type="button" @click="editModalOpen = false"
                            class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-full transition-colors hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                    
                    <div class="flex gap-3 items-center">
                        <h3 class="text-lg font-bold text-primary dark:text-white">تعديل بيانات العميل</h3>
                        <div class="flex justify-center items-center w-10 h-10 text-white rounded-xl shadow-inner bg-primary">
                            <span class="material-symbols-outlined text-[22px]">edit_square</span>
                        </div>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="space-y-5 text-right">
                    
                    {{-- حقل اسم العميل --}}
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            اسم العميل <span class="text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </div>
                            <input type="text" name="name" x-model="editCustomer.name" required autocomplete="off" 
                                   placeholder="أدخل اسم العميل ثلاثياً"
                                   class="pr-11 pl-4 w-full h-12 text-sm placeholder-gray-400 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        </div>
                        @error('name')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- حقل رقم الهاتف الاحترافي --}}
                    <div class="relative" x-data="{ phoneOpen: false, searchCountry: '' }">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            رقم الجوال <span class="text-red-500">*</span>
                        </label>
                        
                        {{-- الحقل المخفي الذي يذهب للسيرفر --}}
                        <input type="hidden" name="phone" :value="(editCustomer.phone_country?.dial_code || '967').replace('+', '') + editCustomer.phone_local">

                        <div class="flex overflow-hidden items-center w-full h-12 bg-gray-50 rounded-xl border border-gray-200 transition-all focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:focus-within:border-primary">
                            <button type="button" @click="phoneOpen = !phoneOpen"
                                    class="flex gap-2 items-center px-3 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="phoneOpen ? 'rotate-180' : ''">expand_more</span>
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + (editCustomer.phone_country?.dial_code || '967').replace('+', '')"></span>
                                <template x-if="editCustomer.phone_country && editCustomer.phone_country.svg">
                                    <div class="flex items-center justify-center w-6 h-4 overflow-hidden rounded-[2px] shadow-sm border border-gray-100 dark:border-gray-600" x-html="editCustomer.phone_country.svg"></div>
                                </template>
                                <template x-if="!editCustomer.phone_country || !editCustomer.phone_country.svg">
                                    <span class="material-symbols-outlined text-[18px] text-gray-400">language</span>
                                </template>
                            </button>
                            <input type="tel" x-ref="edit_customer_phone" x-model="editCustomer.phone_local" placeholder="771234567" autocomplete="off" required
                                   class="px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-none outline-none dark:text-white focus:ring-0" dir="ltr">
                        </div>

                        {{-- Dropdown الدول --}}
                        <div x-cloak x-show="phoneOpen" @click.outside="phoneOpen = false" 
                             x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-gray-700">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <input type="text" x-model="searchCountry" placeholder="ابحث عن الدولة..." class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                <template x-for="c in (searchCountry === '' ? countries : countries.filter(x => (x.name && x.name.toLowerCase().includes(searchCountry.toLowerCase())) || (x.dial_code && x.dial_code.includes(searchCountry))))" :key="c.code">
                                    <button type="button" @click="editCustomer.phone_country = c; phoneOpen = false; $refs.edit_customer_phone?.focus()" class="flex justify-between items-center px-4 py-2.5 w-full text-sm transition-colors hover:bg-primary/5 dark:hover:bg-gray-800">
                                        <div class="flex gap-3 items-center">
                                            <template x-if="c.svg"><div class="w-6 h-4 overflow-hidden rounded-[2px] shadow-sm border border-gray-100 dark:border-gray-600" x-html="c.svg"></div></template>
                                            <template x-if="!c.svg"><span class="material-symbols-outlined text-[16px] text-gray-400">language</span></template>
                                            <span class="font-medium dark:text-gray-300" x-text="c.name"></span>
                                        </div>
                                        <span class="font-mono text-gray-500" dir="ltr" x-text="'+' + (c.dial_code || '').replace('+', '')"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('phone')
                            <p class="mt-1 text-xs font-medium text-error-500">{{ $message }}</p>
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
