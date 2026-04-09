{{-- ======================== Edit User Modal ======================== --}}
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
             class="relative w-full max-w-2xl p-6 bg-white shadow-2xl rounded-[2rem] dark:bg-boxdark sm:p-8" dir="rtl">

            {{-- نموذج الإرسال عبر AJAX --}}
            <form @submit.prevent="submitEditForm" x-data="{ isUpdating: false, editErrors: {} }">
                @csrf

                {{-- Modal Header --}}
                <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100 dark:border-gray-800">
                    <button type="button" @click="editModalOpen = false"
                            class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-full transition-colors hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                    
                    <div class="flex gap-3 items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">تعديل بيانات المستخدم</h3>
                        <div class="flex justify-center items-center w-10 h-10 text-gray-50 bg-orange-300 rounded-xl shadow-inner dark:bg-gray-500/10">
                            <span class="material-symbols-outlined text-[22px]">manage_accounts</span>
                        </div>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="grid grid-cols-1 gap-6 text-right sm:grid-cols-2">
                    
                    {{-- حقل اسم المستخدم --}}
                    <div class="sm:col-span-2">
                        <label for="edit_name" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            اسم المستخدم <span class="text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary-500">
                                <span class="material-symbols-outlined text-[20px]">badge</span>
                            </div>
                            <input type="text" id="edit_name" x-model="editUser.name" required autocomplete="off" 
                                   class="pr-11 pl-4 w-full h-12 text-sm placeholder-gray-400 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        </div>
                        <template x-if="editErrors.name">
                            <p class="mt-1 text-xs font-medium text-red-500" x-text="editErrors.name[0]"></p>
                        </template>
                    </div>

                    {{-- نوع الحساب --}}
                    <div class="sm:col-span-1">
                        <label for="edit_type" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            نوع الحساب <span class="text-red-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary-500">
                                <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                            </div>
                            <select id="edit_type" x-model="editUser.type" required
                                class="pr-11 pl-4 w-full h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all appearance-none cursor-pointer outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <option value="user">مستخدم</option>
                                <option value="admin">مدير نظام</option>
                            </select>
                            <div class="flex absolute inset-y-0 left-0 items-center pl-4 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">expand_more</span>
                            </div>
                        </div>
                        <template x-if="editErrors.type">
                            <p class="mt-1 text-xs font-medium text-red-500" x-text="editErrors.type[0]"></p>
                        </template>
                    </div>

                    {{-- الفرع --}}
                    <div class="sm:col-span-1">
                        <label for="edit_branch_id" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            الفرع <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <div class="relative group">
                            <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary-500">
                                <span class="material-symbols-outlined text-[20px]">apartment</span>
                            </div>
                            <select id="edit_branch_id" x-model="editUser.branch_id"
                                class="pr-11 pl-4 w-full h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all appearance-none cursor-pointer outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <option value="">بدون فرع (الكل)</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <div class="flex absolute inset-y-0 left-0 items-center pl-4 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">expand_more</span>
                            </div>
                        </div>
                        <template x-if="editErrors.branch_id">
                            <p class="mt-1 text-xs font-medium text-red-500" x-text="editErrors.branch_id[0]"></p>
                        </template>
                    </div>

                    {{-- رقم الجوال الأساسي --}}
                    <div class="relative sm:col-span-1" x-data="{ open: false, search: '' }">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            رقم الجوال <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="flex overflow-hidden items-center w-full h-12 bg-gray-50 rounded-xl border border-gray-200 transition-all focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/20 dark:bg-gray-900 dark:border-gray-700 dark:focus-within:border-primary-500">
                            <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-3 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + (editUser.phone_country?.dial_code || '967').replace('+', '')"></span>
                                <template x-if="editUser.phone_country && editUser.phone_country.svg">
                                    <div class="flex items-center justify-center w-6 h-4 overflow-hidden rounded-[2px] shadow-sm border border-gray-100 dark:border-gray-600" x-html="editUser.phone_country.svg"></div>
                                </template>
                            </button>
                            <input type="tel" x-model="editUser.phone_local" placeholder="771234567" autocomplete="off" required
                                   class="px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-none outline-none dark:text-white focus:ring-0" dir="ltr">
                        </div>

                        {{-- Dropdown --}}
                        <div x-cloak x-show="open" @click.outside="open = false" 
                             x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-gray-700">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <input type="text" x-model="search" placeholder="ابحث عن الدولة..." class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                <template x-for="country in countries.filter(c => c.name.toLowerCase().includes(search.toLowerCase()) || c.dial_code.includes(search))" :key="country.code">
                                    <button type="button" @click="editUser.phone_country = country; open = false;" class="flex justify-between items-center px-4 py-2 w-full text-sm transition-colors hover:bg-primary-50 dark:hover:bg-gray-800">
                                        <div class="flex gap-3 items-center">
                                            <template x-if="country.svg"><div class="w-6 h-4 overflow-hidden rounded-[2px] shadow-sm" x-html="country.svg"></div></template>
                                            <span class="font-medium dark:text-gray-300" x-text="country.name"></span>
                                        </div>
                                        <span class="font-mono text-gray-500" dir="ltr" x-text="'+' + country.dial_code.replace('+', '')"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <template x-if="editErrors.phone">
                            <p class="mt-1 text-xs font-medium text-red-500" x-text="editErrors.phone[0]"></p>
                        </template>
                    </div>

                    {{-- رقم الواتساب --}}
                    <div class="relative sm:col-span-1" x-data="{ open: false, search: '' }">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            رقم الواتساب <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        
                        <div class="flex overflow-hidden items-center w-full h-12 bg-gray-50 rounded-xl border border-gray-200 transition-all focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/20 dark:bg-gray-900 dark:border-gray-700 dark:focus-within:border-primary-500">
                            <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-3 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + (editUser.whatsapp_country?.dial_code || '967').replace('+', '')"></span>
                                <template x-if="editUser.whatsapp_country && editUser.whatsapp_country.svg">
                                    <div class="flex items-center justify-center w-6 h-4 overflow-hidden rounded-[2px] shadow-sm border border-gray-100 dark:border-gray-600" x-html="editUser.whatsapp_country.svg"></div>
                                </template>
                            </button>
                            <input type="tel" x-model="editUser.whatsapp_local" placeholder="771234567" autocomplete="off"
                                   class="px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-none outline-none dark:text-white focus:ring-0" dir="ltr">
                        </div>

                        {{-- Dropdown --}}
                        <div x-cloak x-show="open" @click.outside="open = false" 
                             x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-gray-700">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <input type="text" x-model="search" placeholder="ابحث..." class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary-500 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                <template x-for="country in countries.filter(c => c.name.toLowerCase().includes(search.toLowerCase()) || c.dial_code.includes(search))" :key="country.code">
                                    <button type="button" @click="editUser.whatsapp_country = country; open = false;" class="flex justify-between items-center px-4 py-2 w-full text-sm transition-colors hover:bg-primary-50 dark:hover:bg-gray-800">
                                        <div class="flex gap-3 items-center">
                                            <template x-if="country.svg"><div class="w-6 h-4 overflow-hidden rounded-[2px] shadow-sm" x-html="country.svg"></div></template>
                                            <span class="font-medium dark:text-gray-300" x-text="country.name"></span>
                                        </div>
                                        <span class="font-mono text-gray-500" dir="ltr" x-text="'+' + country.dial_code.replace('+', '')"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <template x-if="editErrors.whatsapp_number">
                            <p class="mt-1 text-xs font-medium text-red-500" x-text="editErrors.whatsapp_number[0]"></p>
                        </template>
                    </div>

                    {{-- كلمة السر --}}
                    <div class="sm:col-span-1" x-data="{ showPass: false }">
                        <label for="edit_password" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            كلمة السر الجديدة <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <div class="relative group">
                            <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary-500">
                                <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" id="edit_password" x-model="editUser.password" autocomplete="new-password"
                                   placeholder="اتركها فارغة لعدم التغيير"
                                   class="pr-11 pl-11 w-full h-12 text-sm placeholder-gray-400 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                            
                            <button type="button" @click="showPass = !showPass" class="flex absolute inset-y-0 left-0 items-center pl-4 text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                <span class="material-symbols-outlined text-[20px]" x-text="showPass ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        <template x-if="editErrors.password">
                            <p class="mt-1 text-xs font-medium text-red-500" x-text="editErrors.password[0]"></p>
                        </template>
                    </div>

                    {{-- حالة الحساب (Toggle) --}}
                    <div class="flex flex-col justify-center sm:col-span-1">
                        <label class="block mb-3 text-sm font-bold text-gray-700 dark:text-gray-300">
                            حالة الحساب
                        </label>
                        <div class="flex gap-3 items-center">
                            <button type="button" @click="editUser.is_banned = editUser.is_banned ? 0 : 1"
                                    :class="editUser.is_banned ? 'bg-red-500' : 'bg-green-500'"
                                    class="inline-flex relative w-11 h-6 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out cursor-pointer shrink-0 focus:outline-none">
                                <span :class="editUser.is_banned ? '-translate-x-5' : 'translate-x-0'"
                                      class="inline-block w-5 h-5 bg-white rounded-full ring-0 shadow transition duration-200 ease-in-out transform pointer-events-none"></span>
                            </button>
                            <span class="text-sm font-bold"
                                  :class="editUser.is_banned ? 'text-red-500' : 'text-green-500'"
                                  x-text="editUser.is_banned ? 'حساب محظور' : 'حساب نشط'"></span>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex flex-row-reverse gap-3 justify-start items-center pt-6 mt-8 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" :disabled="isUpdating"
                            class="flex items-center justify-center gap-2 px-8 py-2.5 text-sm font-bold text-white transition-all rounded-xl bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed min-w-[140px]">
                        <template x-if="isUpdating">
                            <svg class="w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!isUpdating">
                            <span>حفظ التعديلات</span>
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