@extends('layouts.app')
@section('title', 'إضافة مكتب جديد')
@section('Breadcrumb')
    <a href="{{ route('offices.unverified.index') }}" class="text-gray-500 transition-colors hover:text-primary">إدارة المكاتب
        غير الموثوقة</a>
    <span class="text-gray-400">/</span>
    <span class="font-medium text-on-surface dark:text-gray-100">إضافة مكتب</span>
@endsection

@section('content')
    <div class="space-y-6 font-body" dir="rtl">
        {{-- الهيدر الرئيسي للصفحة --}}
        <div class="flex gap-4 items-center mb-8">
            <a href="{{ route('offices.unverified.index') }}"
                class="flex justify-center items-center w-12 h-12 text-gray-500 bg-white rounded-2xl border border-gray-200 shadow-sm transition-all hover:bg-surface hover:text-primary dark:bg-boxdark-2 dark:border-boxdark dark:text-bodydark">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-on-surface dark:text-white">إضافة مكتب جديد</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-bodydark">قم بإدخال البيانات الأساسية للمكتب والفروع التابعة
                    له.</p>
            </div>
        </div>

        <form action="{{ route('offices.store') }}" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf

            <div class="space-y-6">

                {{-- ================= كارد البيانات الأساسية ================= --}}
                <div
                    class="overflow-hidden bg-white rounded-3xl border border-gray-200 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                    <div
                        class="flex gap-3 items-center px-6 py-5 border-b border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark md:px-7">
                        <div
                            class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary-container text-primary-hover dark:bg-primary/10 dark:text-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-on-surface dark:text-white">البيانات الأساسية للمكتب</h3>
                    </div>

                    <div class="p-6 md:p-8">

                        {{-- اسم المكتب --}}
                        <div class="l">
                            <label for="office_name"
                                class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                اسم المكتب <span class="text-red">*</span>
                            </label>
                            <div class="relative">
                                <div
                                    class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 pointer-events-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="text" id="office_name" name="name" value="{{ old('name') }}" required
                                    placeholder="مثال: مكتب الإنجاز السريع"
                                    class="block pr-11 pl-4 w-full h-12 text-sm text-gray-800 bg-white rounded-xl border border-gray-300 transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-boxdark-2 dark:border-boxdark dark:text-white dark:focus:border-primary">
                            </div>
                            @error('name')
                                <p class="mt-1 text-xs text-red">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ================= كارد الفروع ================= --}}
                <div class="overflow-hidden bg-white rounded-3xl border border-gray-200 shadow-sm dark:bg-boxdark dark:border-boxdark-2"
                    x-data="{ branches: @js(old('branches', [['name' => '', 'city' => '', 'phone' => '', 'address' => '']])) }">

                    <div
                        class="flex justify-between items-center px-6 py-5 border-b border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark md:px-7">
                        <div class="flex gap-3 items-center">
                            <div
                                class="flex justify-center items-center w-10 h-10 rounded-xl text-primary-hover bg-primary-container dark:bg-primary/10 dark:text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-on-surface dark:text-white">فروع المكتب <span
                                    class="text-red">*</span></h3>
                        </div>
                        <button type="button" @click="branches.push({ name: '', city: '', phone: '', address: '' })"
                            class="inline-flex gap-2 items-center px-6 py-3 text-sm font-bold rounded-xl transition-all text-primary-hover bg-primary-container hover:bg-primary/20 dark:bg-primary/10 dark:text-primary hover:scale-95">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            إضافة فرع
                        </button>
                    </div>

                    <div class="p-6 space-y-6 md:p-8 bg-surface dark:bg-boxdark-2/50">
                        <template x-for="(branch, index) in branches" :key="index">
                            <div
                                class="relative p-6 bg-white rounded-2xl border border-gray-200 shadow-sm transition-all md:p-8 dark:bg-boxdark dark:border-boxdark hover:shadow-md hover:border-primary/30 dark:hover:border-primary/50">

                                {{-- زر الحذف --}}
                                <button type="button" @click="branches.splice(index, 1)" x-show="branches.length > 1"
                                    class="absolute top-4 left-4 p-2.5 text-gray-400 rounded-xl transition-all bg-surface hover:text-red hover:bg-error/10 dark:bg-boxdark-2 dark:hover:bg-error/20 dark:hover:text-red"
                                    title="حذف الفرع">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <div class="flex gap-3 items-center mb-6">
                                    <span
                                        class="flex justify-center items-center w-8 h-8 text-sm font-bold rounded-full text-primary-hover bg-primary-container dark:bg-primary/20 dark:text-primary"
                                        x-text="index + 1"></span>
                                    <h4 class="text-lg font-bold text-gray-700 dark:text-gray-300">بيانات الفرع</h4>
                                </div>

                                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-2">
                                    {{-- اسم الفرع --}}
                                    <div>
                                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">اسم
                                            الفرع <span class="text-red">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 pointer-events-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <input type="text" x-model="branch.name" :name="`branches[${index}][name]`"
                                                required placeholder="مثال: الفرع الرئيسي"
                                                class="block pr-11 pl-4 w-full h-12 text-sm rounded-xl border-gray-300 transition-colors bg-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-boxdark-2 dark:border-boxdark dark:text-white dark:focus:bg-boxdark-2 dark:focus:border-primary">
                                        </div>
                                    </div>

                                    {{-- المدينة --}}
                                    <div>
                                        <label
                                            class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">المدينة
                                            <span class="text-red">*</span></label>
                                        <div class="relative">
                                            <div
                                                class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 pointer-events-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <input type="text" x-model="branch.city" :name="`branches[${index}][city]`"
                                                required placeholder="مثال: صنعاء"
                                                class="block pr-11 pl-4 w-full h-12 text-sm rounded-xl border-gray-300 transition-colors bg-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-boxdark-2 dark:border-boxdark dark:text-white dark:focus:bg-boxdark-2 dark:focus:border-primary">
                                        </div>
                                    </div>

                                    {{-- هاتف الفرع --}}
                                    <div>
    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">رقم هاتف الفرع</label>
    
    {{-- لقد قمت بتبديل phoneComponent ليستخدم خصائص x-data مباشرة لسهولة التحكم إذا أردت، 
         أو يمكنك الاستمرار بـ phoneComponent ولكن يجب أن يحتوي على هذه المتغيرات --}}
    <div x-data="{
        open: false,
        search: '',
        localPhoneNumber: '{{ old('phone') ? preg_replace('/^967/', '', old('phone')) : '' }}',
        selectedCountry: null,
        countries: @js(array_values(config('countries', []))),
        get filteredCountries() {
            if (this.search === '') return this.countries;
            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
        }
    }" 
    x-init="
        selectedCountry = countries.find(c => c.code === 'YE') || countries[0];
        // إذا كان هناك رقم قديم لفرع، نقوم بتحليله
        if (branch.phone) {
            let p = branch.phone;
            const codes = countries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
            for (const code of codes) {
                let regex = new RegExp('^(\\+|00)?' + code);
                if (regex.test(p)) {
                    selectedCountry = countries.find(c => c.dial_code.replace('+', '') === code);
                    p = p.replace(regex, '');
                    break;
                }
            }
            localPhoneNumber = p;
        }
    "
    x-effect="branch.phone = (selectedCountry?.dial_code.replace('+', '') || '') + localPhoneNumber"
    class="relative">
        
        {{-- الحقل المخفي الذي سيُرسل للسيرفر --}}
        <input type="hidden" :name="`branches[${index}][phone]`" :value="branch.phone">
        
        <div dir="ltr" class="flex overflow-visible w-full h-12 rounded-xl border border-gray-300 transition-all bg-surface focus-within:bg-white focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/30 dark:border-boxdark dark:bg-boxdark-2 dark:focus-within:bg-boxdark-2 dark:focus-within:border-primary">
            
            {{-- Country Btn (يعرض العلم فقط مع السهم) --}}
            <button type="button" @click="open = !open"
                class="flex gap-2 items-center px-3 h-full rounded-l-xl border-r border-gray-200 transition-colors outline-none shrink-0 hover:bg-gray-100 dark:border-boxdark dark:hover:bg-boxdark">
                <template x-if="selectedCountry">
                    <div class="w-6 h-4 rounded-[2px] shadow-sm overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full"
                        x-html="selectedCountry.svg"></div>
                </template>
                <span class="material-symbols-outlined text-[18px] text-gray-400">expand_more</span>
            </button>

            {{-- Input المرئي --}}
            <input type="tel" x-model="localPhoneNumber" 
                @input="
                    localPhoneNumber = localPhoneNumber.replace(/[^0-9]/g, ''); 
                    if(selectedCountry?.code === 'YE' && localPhoneNumber.length > 9) {
                        localPhoneNumber = localPhoneNumber.substring(0, 9);
                    } else if (localPhoneNumber.length > 15) {
                        localPhoneNumber = localPhoneNumber.substring(0, 15);
                    }
                "
                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                @focus="open = true" @click.outside="open = false"
                placeholder="7XXXXXXXX" autocomplete="off"
                class="flex-1 px-4 w-full h-full font-mono text-sm font-bold tracking-widest text-left text-gray-800 bg-transparent border-none outline-none focus:ring-0 dark:text-white">
        </div>

        {{-- Dropdown الخاصة باختيار الدولة للفرع --}}
        <div x-show="open" @click.outside="open = false" x-transition x-cloak dir="rtl"
            class="overflow-hidden absolute left-0 z-50 mt-1 w-[200px] max-h-60 bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-boxdark dark:border-boxdark-2">
            <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                    class="px-3 py-2 w-full text-xs font-bold rounded-lg border border-gray-200 outline-none bg-surface dark:bg-boxdark dark:border-boxdark-2 focus:border-emerald-500 text-on-surface dark:text-white dir-rtl">
            </div>
            <div class="overflow-y-auto p-1 max-h-40 custom-scrollbar">
                <template x-for="country in filteredCountries" :key="country.code">
                    <button type="button" @click="selectedCountry = country; open = false; search = ''; localPhoneNumber = '';"
                        class="flex justify-between items-center px-3 py-2.5 w-full text-sm text-left rounded-lg transition-colors hover:bg-surface dark:hover:bg-boxdark">
                        <div class="flex gap-3 items-center">
                            <div class="w-6 h-4 flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden [&>svg]:w-full [&>svg]:h-full" x-html="country.svg"></div>
                            <span class="text-xs font-bold text-gray-700 truncate dark:text-gray-200" x-text="country.name"></span>
                        </div>
                        <span class="font-mono text-[10px] font-bold text-gray-400 dir-ltr" x-text="country.dial_code"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

                                    {{-- عنوان الفرع --}}
                                    <div>
                                        <label
                                            class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">العنوان
                                            التفصيلي</label>
                                        <div class="relative">
                                            <div
                                                class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 pointer-events-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                </svg>
                                            </div>
                                            <input type="text" x-model="branch.address"
                                                :name="`branches[${index}][address]`" placeholder="الشارع، تقاطع..."
                                                class="block pr-11 pl-4 w-full h-12 text-sm rounded-xl border-gray-300 transition-colors bg-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-boxdark-2 dark:border-boxdark dark:text-white dark:focus:bg-boxdark-2 dark:focus:border-primary">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- منطقة الأزرار --}}
                <div
                    class="flex flex-wrap gap-5 justify-end items-center pt-8 mt-8 border-t border-gray-100 dark:border-boxdark">
                    <a href="{{ route('offices.unverified.index') }}"
                        class="px-7 py-3 text-sm font-bold text-center text-gray-700 bg-white rounded-xl border border-gray-300 shadow-sm transition-all hover:bg-surface hover:text-on-surface dark:bg-boxdark-2 dark:border-boxdark dark:text-gray-300 dark:hover:bg-boxdark min-w-[160px]">
                        إلغاء
                    </a>
                    <button type="submit" :disabled="isSubmitting"
                        class="flex gap-3 justify-center items-center px-7 py-3 min-w-[240px] text-sm font-bold text-white rounded-xl shadow-md transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/30 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                        <svg x-show="isSubmitting" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"
                            style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                            </path>
                        </svg>
                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ المكتب والفروع'"></span>
                    </button>
                </div>

            </div>
        </form>
    </div>

    <script>
        function phoneComponent(initialPhone) {
            return {
                open: false,
                search: '',
                countries: @js(array_values(config('countries'))),
                selectedCountry: null,
                localPhoneNumber: '',
                init() {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                    if (initialPhone) this.parsePhone(initialPhone);
                    
                },
                parsePhone(phone) {
                    let p = phone || '';
                    let found = null;
                    const sorted = this.countries.slice().sort((a, b) => b.dial_code.length - a.dial_code.length);
                    for (const c of sorted) {
                        const code = c.dial_code.replace('+', '');
                        const regex = new RegExp(`^(\\+|00)?${code}`);
                        if (regex.test(p)) {
                            found = c;
                            p = p.replace(regex, '');
                            break;
                        }
                    }
                    if (found) this.selectedCountry = found;
                    this.localPhoneNumber = p;
                },
                get filteredCountries() {
                    if (this.search === '') return this.countries;
                    return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c
                        .dial_code.includes(this.search));
                }
            }
        }
    </script>
@endsection
