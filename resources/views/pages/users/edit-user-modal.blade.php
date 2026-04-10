{{-- ======================== Edit User Modal ======================== --}}
<template x-teleport="body">
    <div x-cloak x-show="editModalOpen" 
         class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto sm:p-6 z-[2147483647]" 
         @keydown.escape.window="editModalOpen = false">

        {{-- Backdrop --}}
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

        {{-- Modal Panel --}}
        <div x-show="editModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="relative w-full max-w-2xl p-6 bg-white shadow-2xl rounded-[2rem] dark:bg-boxdark sm:p-8" dir="rtl">

            <form :action="editUser.url" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf
                @method('PUT')

                {{-- Modal Header --}}
                <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100 dark:border-gray-800">
                    <button type="button" @click="editModalOpen = false"
                            class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-full transition-colors hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                    
                    <div class="flex gap-3 items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">تعديل بيانات المستخدم</h3>
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl shadow-inner bg-warning-50 text-warning-500">
                            <span class="material-symbols-outlined text-[22px]">edit_square</span>
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
                            <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                <span class="material-symbols-outlined text-[20px]">badge</span>
                            </div>
                            <input type="text" id="edit_name" name="name" x-model="editUser.name" required autocomplete="off" 
                                   class="pr-11 pl-4 w-full h-12 text-sm placeholder-gray-400 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                        </div>
                        @error('name')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- رقم الهاتف الأساسي --}}
                    <div class="relative" x-data="{
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
                                this.$watch('editModalOpen', (isOpen) => {
                                    if(isOpen) {
                                        let phone = editUser.phone || '';
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
                        }">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            رقم الجوال <span class="text-red-500">*</span>
                        </label>

                        <input type="hidden" name="phone" :value="localPhoneNumber ? selectedDialCode + localPhoneNumber : ''">

                        <div class="flex overflow-hidden items-center w-full h-12 bg-gray-50 rounded-xl border border-gray-200 transition-all focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:focus-within:border-primary">
                            <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-3 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + selectedDialCode"></span>
                                <span class="text-lg leading-none" x-text="selectedFlag"></span>
                            </button>
                            <input type="tel" x-model="localPhoneNumber" placeholder="771234567" autocomplete="off" required
                                   class="px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-none outline-none dark:text-white focus:ring-0" dir="ltr">
                        </div>

                        <div x-cloak x-show="open" @click.outside="open = false" 
                             x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-gray-700">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <input type="text" x-model="search" placeholder="ابحث..." class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                <template x-for="c in filteredCountries" :key="c.code">
                                    <button type="button" @click="selectedDialCode = c.dial_code; selectedFlag = c.flag; open = false;" class="flex justify-between items-center px-4 py-2.5 w-full text-sm transition-colors hover:bg-primary/5 dark:hover:bg-gray-800">
                                        <div class="flex gap-3 items-center">
                                            <span class="text-lg leading-none" x-text="c.flag"></span>
                                            <span class="font-medium dark:text-gray-300" x-text="c.name"></span>
                                        </div>
                                        <span class="font-mono text-gray-500" dir="ltr" x-text="'+' + c.dial_code"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('phone')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- رقم هاتف إضافي (واتساب) --}}
                    <div class="relative" x-data="{
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
                                this.$watch('editModalOpen', (isOpen) => {
                                    if(isOpen) {
                                        let phone = editUser.whatsapp_number || '';
                                        if(!phone) {
                                            this.localPhoneNumber = '';
                                            return;
                                        }
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
                        }">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            رقم إضافي <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>

                        <input type="hidden" name="whatsapp_number" :value="localPhoneNumber ? selectedDialCode + localPhoneNumber : ''">

                        <div class="flex overflow-hidden items-center w-full h-12 bg-gray-50 rounded-xl border border-gray-200 transition-all focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:focus-within:border-primary">
                            <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-3 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                                <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + selectedDialCode"></span>
                                <span class="text-lg leading-none" x-text="selectedFlag"></span>
                            </button>
                            <input type="tel" x-model="localPhoneNumber" placeholder="771234567" autocomplete="off"
                                   class="px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-none outline-none dark:text-white focus:ring-0" dir="ltr">
                        </div>

                        <div x-cloak x-show="open" @click.outside="open = false" 
                             x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-gray-700">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                <input type="text" x-model="search" placeholder="ابحث..." class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                <template x-for="c in filteredCountries" :key="c.code">
                                    <button type="button" @click="selectedDialCode = c.dial_code; selectedFlag = c.flag; open = false;" class="flex justify-between items-center px-4 py-2.5 w-full text-sm transition-colors hover:bg-primary/5 dark:hover:bg-gray-800">
                                        <div class="flex gap-3 items-center">
                                            <span class="text-lg leading-none" x-text="c.flag"></span>
                                            <span class="font-medium dark:text-gray-300" x-text="c.name"></span>
                                        </div>
                                        <span class="font-mono text-gray-500" dir="ltr" x-text="'+' + c.dial_code"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        @error('whatsapp_number')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- الفرع --}}
                    <div>
                        <label for="edit_branch_id" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            الفرع التابع له <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <div class="relative group">
                            <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                <span class="material-symbols-outlined text-[20px]">apartment</span>
                            </div>
                            <select name="branch_id" id="edit_branch_id" x-model="editUser.branch_id"
                                class="pr-11 pl-4 w-full h-12 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all appearance-none cursor-pointer outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <option value="">اختر الفرع...</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <div class="flex absolute inset-y-0 left-0 items-center pl-4 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">expand_more</span>
                            </div>
                        </div>
                        @error('branch_id')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- كلمة السر --}}
                    <div x-data="{ showPass: false }">
                        <label for="edit_password" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            تغيير كلمة السر <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <div class="relative group">
                            <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" id="edit_password" name="password" autocomplete="new-password"
                                   placeholder="اتركه فارغاً إذا لا تريد التغيير"
                                   class="pr-11 pl-11 w-full h-12 text-sm placeholder-gray-400 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                            
                            <button type="button" @click="showPass = !showPass" class="flex absolute inset-y-0 left-0 items-center pl-4 text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                <span class="material-symbols-outlined text-[20px]" x-text="showPass ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- حالة الحظر --}}
                    <div class="sm:col-span-2 pt-4 mt-2 border-t border-gray-100 dark:border-gray-800">
                        <label class="inline-flex items-center cursor-pointer group">
                            <div class="relative">
                                <input type="checkbox" name="is_banned" value="1" class="sr-only peer" :checked="editUser.is_banned == 1">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:bg-red-500 peer-focus:ring-4 peer-focus:ring-red-500/20 transition-all duration-300"></div>
                                <div class="absolute top-[2px] start-[2px] w-5 h-5 bg-white border border-gray-300 rounded-full transition-all duration-300 peer-checked:translate-x-5 rtl:peer-checked:-translate-x-5"></div>
                            </div>
                            <span class="mr-3 text-sm font-bold text-gray-700 dark:text-gray-300 group-hover:text-red-600 transition-colors">حظر الحساب</span>
                        </label>
                        <p class="mt-2 text-xs text-gray-500 leading-relaxed pr-[3.25rem]">عند تفعيل هذا الخيار، لن يتمكن المستخدم من تسجيل الدخول إلى النظام بأي شكل من الأشكال.</p>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex flex-row-reverse gap-3 justify-start items-center pt-6 mt-8 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" :disabled="isSubmitting"
                            class="flex items-center justify-center gap-2 px-8 py-2.5 text-sm font-bold text-white transition-all rounded-xl bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed min-w-[140px]">
                        <template x-if="isSubmitting">
                            <svg class="w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!isSubmitting">
                            <span>تحديث البيانات</span>
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