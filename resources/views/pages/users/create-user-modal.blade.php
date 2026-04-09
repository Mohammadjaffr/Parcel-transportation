{{-- ======================== Create User Modal ======================== --}}
<div x-data="createUserForm()">

    {{-- الزر لفتح المودال --}}
    <button @click="isModalOpen = true"
        class="flex gap-2 justify-center items-center px-4 py-2.5 w-full text-sm font-semibold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 md:w-auto">
        <span class="material-symbols-outlined text-[20px]">add</span>
        إضافة مستخدم جديد
    </button>

    {{-- المودال --}}
    <template x-teleport="body">
        <div x-cloak x-show="isModalOpen" 
             class="fixed inset-0 z-[999999] flex items-center justify-center p-4 overflow-y-auto sm:p-6" 
             @keydown.escape.window="isModalOpen = false">

            {{-- Backdrop --}}
            <div x-show="isModalOpen"
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 w-full h-full backdrop-blur-sm bg-gray-900/60"
                 @click="isModalOpen = false">
            </div>

            {{-- Modal Panel --}}
            <div x-show="isModalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 class="relative w-full max-w-2xl p-6 bg-white shadow-2xl rounded-[2rem] dark:bg-boxdark sm:p-8" dir="rtl">

                {{-- تم تغيير submit ليعمل عبر الدالة المخصصة لمنع الـ Refresh --}}
                <form @submit.prevent="submitForm">
                    @csrf

                    {{-- Modal Header --}}
                    <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100 dark:border-gray-800">
                        <button type="button" @click="isModalOpen = false"
                                class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-full transition-colors hover:bg-gray-200 hover:text-gray-600 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                        
                        <div class="flex gap-3 items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">إضافة مستخدم جديد</h3>
                            <div class="flex justify-center items-center w-10 h-10 rounded-xl shadow-inner bg-primary/10 text-primary">
                                <span class="material-symbols-outlined text-[22px]">person_add</span>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Body --}}
                    <div class="grid grid-cols-1 gap-6 text-right sm:grid-cols-2">
                        
                        {{-- حقل اسم المستخدم --}}
                        <div class="sm:col-span-2">
                            <label for="name" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                                اسم المستخدم <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                    <span class="material-symbols-outlined text-[20px]">badge</span>
                                </div>
                                <input type="text" id="name" x-model="newUser.name" required autocomplete="off" 
                                       placeholder="مثال: أحمد شرجبي"
                                       class="pr-11 pl-4 w-full h-12 text-sm placeholder-gray-400 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                            </div>
                            {{-- عرض الخطأ ديناميكياً --}}
                            <template x-if="errors.name">
                                <p class="mt-1 text-xs font-medium text-red-500" x-text="errors.name[0]"></p>
                            </template>
                        </div>

                        {{-- رقم الهاتف الأساسي --}}
                        <div class="relative">
                            <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                                رقم الجوال <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="flex overflow-hidden items-center w-full h-12 bg-gray-50 rounded-xl border border-gray-200 transition-all focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:focus-within:border-primary">
                                <button type="button" @click="phone.open = !phone.open"
                                        class="flex gap-2 items-center px-3 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                    <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="phone.open ? 'rotate-180' : ''">expand_more</span>
                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + (phone.country?.dial_code || '967').replace('+', '')"></span>
                                    <template x-if="phone.country && phone.country.svg">
                                        <div class="flex items-center justify-center w-6 h-4 overflow-hidden rounded-[2px] shadow-sm border border-gray-100 dark:border-gray-600" x-html="phone.country.svg"></div>
                                    </template>
                                    <template x-if="!phone.country || !phone.country.svg">
                                        <span class="material-symbols-outlined text-[18px] text-gray-400">language</span>
                                    </template>
                                </button>
                                <input type="tel" x-model="phone.local" placeholder="771234567" autocomplete="off" required
                                       class="px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-none outline-none dark:text-white focus:ring-0" dir="ltr">
                            </div>

                            {{-- Dropdown الهاتف --}}
                            <div x-cloak x-show="phone.open" @click.outside="phone.open = false" 
                                 x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-gray-700">
                                <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                    <input type="text" x-model="phone.search" placeholder="ابحث..." class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>
                                <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                    <template x-for="c in getFilteredCountries(phone.search)" :key="c.code">
                                        <button type="button" @click="phone.country = c; phone.open = false" class="flex justify-between items-center px-4 py-2 w-full text-sm transition-colors hover:bg-primary/5 dark:hover:bg-gray-800">
                                            <div class="flex gap-3 items-center">
                                                <template x-if="c.svg"><div class="w-6 h-4 overflow-hidden rounded-[2px] shadow-sm" x-html="c.svg"></div></template>
                                                <template x-if="!c.svg"><span class="material-symbols-outlined text-[16px] text-gray-400">language</span></template>
                                                <span class="font-medium dark:text-gray-300" x-text="c.name"></span>
                                            </div>
                                            <span class="font-mono text-gray-500" dir="ltr" x-text="'+' + (c.dial_code || '').replace('+', '')"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            
                            <p class="flex gap-1.5 items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span class="material-symbols-outlined text-[14px] text-success-500">info</span>
                                <span>يُعتمد كرقم <span class="font-bold text-success-500">واتساب</span>.</span>
                            </p>
                            <template x-if="errors.phone">
                                <p class="mt-1 text-xs font-medium text-red-500" x-text="errors.phone[0]"></p>
                            </template>
                        </div>

                        {{-- رقم هاتف إضافي (واتساب) --}}
                        <div class="relative">
                            <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                                رقم إضافي <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                            </label>
                            
                            <div class="flex overflow-hidden items-center w-full h-12 bg-gray-50 rounded-xl border border-gray-200 transition-all focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:focus-within:border-primary">
                                <button type="button" @click="whatsapp.open = !whatsapp.open"
                                        class="flex gap-2 items-center px-3 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                                    <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="whatsapp.open ? 'rotate-180' : ''">expand_more</span>
                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + (whatsapp.country?.dial_code || '967').replace('+', '')"></span>
                                    <template x-if="whatsapp.country && whatsapp.country.svg">
                                        <div class="flex items-center justify-center w-6 h-4 overflow-hidden rounded-[2px] shadow-sm border border-gray-100 dark:border-gray-600" x-html="whatsapp.country.svg"></div>
                                    </template>
                                    <template x-if="!whatsapp.country || !whatsapp.country.svg">
                                        <span class="material-symbols-outlined text-[18px] text-gray-400">language</span>
                                    </template>
                                </button>
                                <input type="tel" x-model="whatsapp.local" placeholder="771234567" autocomplete="off"
                                       class="px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-none outline-none dark:text-white focus:ring-0" dir="ltr">
                            </div>

                            {{-- Dropdown الواتساب --}}
                            <div x-cloak x-show="whatsapp.open" @click.outside="whatsapp.open = false" 
                                 x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-gray-700">
                                <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                    <input type="text" x-model="whatsapp.search" placeholder="ابحث..." class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>
                                <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                    <template x-for="c in getFilteredCountries(whatsapp.search)" :key="c.code">
                                        <button type="button" @click="whatsapp.country = c; whatsapp.open = false;" class="flex justify-between items-center px-4 py-2 w-full text-sm transition-colors hover:bg-primary/5 dark:hover:bg-gray-800">
                                            <div class="flex gap-3 items-center">
                                                <template x-if="c.svg"><div class="w-6 h-4 overflow-hidden rounded-[2px] shadow-sm" x-html="c.svg"></div></template>
                                                <template x-if="!c.svg"><span class="material-symbols-outlined text-[16px] text-gray-400">language</span></template>
                                                <span class="font-medium dark:text-gray-300" x-text="c.name"></span>
                                            </div>
                                            <span class="font-mono text-gray-500" dir="ltr" x-text="'+' + (c.dial_code || '').replace('+', '')"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <template x-if="errors.whatsapp_number">
                                <p class="mt-1 text-xs font-medium text-red-500" x-text="errors.whatsapp_number[0]"></p>
                            </template>
                        </div>

                        {{-- الفرع --}}
                        <div>
                            <label for="branch_id" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                                الفرع التابع له <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                            </label>
                            <div class="relative group">
                                <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                    <span class="material-symbols-outlined text-[20px]">apartment</span>
                                </div>
                                <select x-model="newUser.branch_id" id="branch_id"
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
                            <template x-if="errors.branch_id">
                                <p class="mt-1 text-xs font-medium text-red-500" x-text="errors.branch_id[0]"></p>
                            </template>
                        </div>

                        {{-- كلمة السر --}}
                        <div x-data="{ showPass: false }">
                            <label for="password" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                                كلمة السر <span class="text-red-500">*</span>
                            </label>
                            <div class="relative group">
                                <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </div>
                                <input :type="showPass ? 'text' : 'password'" id="password" x-model="newUser.password" required autocomplete="new-password"
                                       class="pr-11 pl-11 w-full h-12 text-sm placeholder-gray-400 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                
                                <button type="button" @click="showPass = !showPass" class="flex absolute inset-y-0 left-0 items-center pl-4 text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                    <span class="material-symbols-outlined text-[20px]" x-text="showPass ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            <p class="flex gap-1.5 items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span class="material-symbols-outlined text-[14px]">shield</span>
                                <span>تُستخدم لتسجيل الدخول إلى النظام.</span>
                            </p>
                            <template x-if="errors.password">
                                <p class="mt-1 text-xs font-medium text-red-500" x-text="errors.password[0]"></p>
                            </template>
                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex flex-row-reverse gap-3 justify-start items-center pt-6 mt-8 border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" :disabled="isLoading"
                                class="flex items-center justify-center gap-2 px-8 py-2.5 text-sm font-bold text-white transition-all rounded-xl bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed min-w-[140px]">
                            <template x-if="isLoading">
                                <svg class="w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <template x-if="!isLoading">
                                <span>إنشاء الحساب</span>
                            </template>
                        </button>

                        <button type="button" @click="isModalOpen = false"
                                class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            إلغاء
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </template>
</div>

{{-- سكريبت المودال المجمع للبيانات والإرسال عبر AJAX --}}
<script>
    function createUserForm() {
        return {
            isModalOpen: false,
            isLoading: false,
            errors: {}, // كائن لتخزين الأخطاء
            countries: @json(array_values(config('countries') ?? [])),
            
            // بيانات المستخدم
            newUser: {
                name: '',
                password: '',
                branch_id: ''
            },

            // بيانات الهاتف الأساسي
            phone: {
                local: '',
                country: null,
                open: false,
                search: ''
            },

            // بيانات هاتف الواتساب
            whatsapp: {
                local: '',
                country: null,
                open: false,
                search: ''
            },

            init() {
                if (!this.countries || this.countries.length === 0) {
                    this.countries = [
                        { name: 'اليمن', code: 'YE', dial_code: '967', svg: '<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 900 600\"><rect width=\"900\" height=\"600\" fill=\"#000\"/><rect width=\"900\" height=\"200\" fill=\"#ce1126\"/><rect y=\"400\" width=\"900\" height=\"200\" fill=\"#fff\"/></svg>' }
                    ];
                }
                const defaultCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                this.phone.country = defaultCountry;
                this.whatsapp.country = defaultCountry;
            },

            getFilteredCountries(searchTerm) {
                if (!searchTerm) return this.countries;
                const term = searchTerm.toLowerCase();
                return this.countries.filter(c => 
                    (c.name && c.name.toLowerCase().includes(term)) || 
                    (c.dial_code && c.dial_code.includes(term))
                );
            },

            // دالة الإرسال الذكية (AJAX)
            async submitForm() {
                this.isLoading = true;
                this.errors = {}; // تصفير الأخطاء السابقة

                // تجهيز أرقام الهواتف المدمجة
                const phoneDial = (this.phone.country?.dial_code || '967').replace('+', '');
                const fullPhone = this.phone.local ? phoneDial + this.phone.local : '';

                const whatsappDial = (this.whatsapp.country?.dial_code || '967').replace('+', '');
                const fullWhatsapp = this.whatsapp.local ? whatsappDial + this.whatsapp.local : '';

                const payload = {
                    name: this.newUser.name,
                    password: this.newUser.password,
                    branch_id: this.newUser.branch_id,
                    phone: fullPhone,
                    whatsapp_number: fullWhatsapp
                };

                try {
                    const response = await fetch('{{ route("users.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // نجاح: إغلاق المودال وتحديث الصفحة لظهور رسالة النجاح
                        this.isModalOpen = false;
                        window.location.reload();
                    } else if (response.status === 422) {
                        // خطأ: تعبئة الأخطاء بدون إغلاق المودال
                        this.errors = data.errors;
                    } else {
                        alert('حدث خطأ غير متوقع في السيرفر.');
                    }
                } catch(e) {
                    alert('تعذر الاتصال بالخادم. تأكد من اتصالك بالإنترنت.');
                } finally {
                    this.isLoading = false;
                }
            }
        }
    }
</script>