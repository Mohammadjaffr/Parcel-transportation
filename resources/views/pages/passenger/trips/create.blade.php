@extends('layouts.app')

@section('title', 'إنشاء رحلة جديدة')
@section('Breadcrumb', 'إنشاء رحلة جديدة')

@section('content')

<div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl">

    {{-- الهيدر --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="flex gap-4 items-center mb-6">
            <a href="{{ route('trips.index') }}" class="flex justify-center items-center w-12 h-12 bg-white rounded-2xl border shadow-sm transition-all dark:bg-boxdark border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary hover:border-primary/30 active:scale-95">
                <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
            </a>
            <div>
                <h1 class="text-2xl font-black font-headline text-slate-800 dark:text-white">إنشاء رحلة جديدة</h1>
                <p class="mt-1 text-sm font-bold text-gray-500 dark:text-gray-400">قم باختيار السائق وتحديد الركاب لتسيير الرحلة فوراً</p>
            </div>
        </div>
    </div>

    {{-- حاوية الفورم --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-6 md:p-10 border border-gray-100 dark:border-boxdark-2">

            <form action="{{ route('trips.store') }}" method="POST" 
                x-data="createTripForm(@js($drivers ?? []), @js(array_values(config('countries', []))), @js($pendingPassengers ?? []))">
                @csrf

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    
                    {{-- العمود الأول: تفاصيل السائق وزر الحفظ --}}
                    <div class="pb-8 space-y-6 border-b lg:col-span-1 lg:border-b-0 lg:pb-0 lg:border-l border-slate-100 dark:border-boxdark-2 lg:pl-8">
                        <div>
                            <h2 class="flex gap-2 items-center mb-4 text-lg font-black text-slate-800 dark:text-white font-headline">
                                <span class="material-symbols-outlined text-primary">local_taxi</span>
                                بيانات السائق المسؤول
                            </h2>
                            <p class="mb-6 text-xs font-bold leading-relaxed text-gray-500">يرجى كتابة رقم الجوال لاختيار سائق مسجل، أو كتابة رقمه واسمه لإضافته كسائق جديد.</p>
                        </div>

                        {{-- إدخال رقم السائق --}}
                        <div class="space-y-4">
                            <input type="hidden" name="driver_id" x-model="selectedDriverId">
                            <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                            <div class="relative">
                                <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-gray-400">رقم الجوال <span class="text-rose-500">*</span></label>
                                <div class="flex overflow-visible relative items-center h-14 bg-white rounded-xl ring-1 transition-all group ring-slate-200 dark:bg-boxdark-2 dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/50"
                                    :class="selectedDriverId ? 'bg-primary/5 ring-primary/30 dark:bg-primary/5' : ''" style="direction: ltr;">

                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                            class="flex gap-2 items-center px-4 h-full rounded-l-xl border-r transition-colors bg-slate-50 border-slate-200 dark:bg-boxdark dark:border-boxdark-2 text-slate-600 dark:text-gray-300">
                                            <div class="w-6 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden" x-html="selectedCountry?.svg"></div>
                                            <span class="text-sm font-bold" x-text="selectedCountry?.dial_code"></span>
                                        </button>

                                        <div x-show="openCountryDropdown" x-cloak x-transition
                                            class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-2xl shadow-xl z-[60] overflow-hidden">
                                            <div class="p-3 border-b border-slate-50 dark:border-boxdark-2">
                                                <input type="text" x-model="searchCountryQuery" placeholder="بحث عن دولة..."
                                                    class="px-4 w-full h-10 text-sm rounded-xl outline-none bg-slate-50 dark:bg-boxdark-2 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30" dir="rtl">
                                            </div>
                                            <div class="overflow-y-auto max-h-60 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button" @click="selectedCountry = country; openCountryDropdown = false; searchDriver()"
                                                        class="flex gap-3 items-center px-4 py-3 w-full text-left border-b transition-colors hover:bg-slate-50 dark:hover:bg-boxdark-2 text-slate-700 dark:text-gray-200 border-slate-50 dark:border-boxdark-2 last:border-0">
                                                        <div class="w-6 h-auto flex items-center justify-center rounded-[2px] overflow-hidden shadow-sm" x-html="country.svg"></div>
                                                        <span class="flex-1 text-sm font-bold" x-text="country.name"></span>
                                                        <span class="font-mono text-xs text-slate-400" x-text="country.dial_code"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="tel" x-model="localPhoneNumber" @input="searchDriver"
                                        @focus="showDriverDropdown = true" @click.away="showDriverDropdown = false"
                                        placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-4 w-full h-full text-base tracking-widest text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-slate-800 dark:text-white"
                                        :class="selectedDriverId ? 'font-black text-primary dark:text-primary-hover' : ''">

                                    <button type="button" x-show="selectedDriverId" @click="resetSelection" x-cloak
                                        class="absolute right-4 z-10 p-1 bg-white rounded-full border shadow-sm transition-colors dark:bg-boxdark text-slate-400 hover:text-red-500 hover:bg-red-50 border-slate-100 dark:border-boxdark-2">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </div>

                                {{-- نتائج البحث المنسدلة للسائقين --}}
                                <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId" x-transition x-cloak
                                    class="absolute top-[4.5rem] right-0 w-full bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-2xl shadow-xl overflow-hidden max-h-60 overflow-y-auto z-50">
                                    <template x-for="driver in filteredDrivers" :key="driver.id">
                                        <button type="button" @click="selectDriver(driver)"
                                            class="flex justify-between items-center px-5 py-3.5 w-full text-right border-b transition-colors hover:bg-primary/5 dark:hover:bg-boxdark-2 border-slate-50 dark:border-boxdark-2 group">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-sm font-black transition-colors text-slate-800 dark:text-white group-hover:text-primary" x-text="driver.name"></span>
                                                <span class="font-mono text-xs text-right text-slate-500 dir-ltr" x-text="driver.phone"></span>
                                            </div>
                                            <span class="material-symbols-outlined text-slate-300 text-[20px] group-hover:text-primary transition-colors transform group-hover:-translate-x-1">arrow_back</span>
                                        </button>
                                    </template>
                                    <div x-show="filteredDrivers.length === 0" class="px-5 py-4 text-center bg-slate-50/50 dark:bg-boxdark-2/30">
                                        <div class="flex justify-center items-center mx-auto mb-2 w-10 h-10 text-blue-500 bg-blue-50 rounded-full dark:bg-blue-500/10">
                                            <span class="material-symbols-outlined text-[20px]">person_add</span>
                                        </div>
                                        <span class="text-sm font-bold text-slate-600 dark:text-gray-300">سائق جديد</span>
                                        <p class="mt-1 text-xs text-slate-400">سيتم إنشاء حساب للسائق تلقائياً.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- إدخال اسم السائق --}}
                            <div class="mt-4">
                                <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-gray-400">اسم السائق <span class="text-rose-500">*</span></label>
                                <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedDriverId !== null"
                                    placeholder="أدخل الاسم الثلاثي للسائق..." required
                                    class="px-4 w-full h-14 text-sm font-bold rounded-xl border transition-colors outline-none"
                                    :class="selectedDriverId ? 'bg-slate-50 border-transparent text-emerald-600 dark:text-emerald-400 dark:bg-boxdark-2/60 cursor-not-allowed' : 'bg-white border-slate-200 dark:bg-boxdark-2 dark:border-boxdark-2 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-800 dark:text-white'">
                            </div>
                        </div>

                        {{-- زر الحفظ للديسكتوب --}}
                        <div class="pt-8 mt-8 border-t border-slate-100 dark:border-boxdark-2" x-data="{ isSubmitting: false }">
                            <button type="submit"
                                @click="if(selectedPassengers.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                                :disabled="selectedPassengers.length === 0 || isSubmitting" 
                                class="flex gap-3 justify-center items-center w-full h-14 text-sm font-black text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/30 active:scale-95 disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-boxdark-2 dark:disabled:text-gray-600 disabled:shadow-none disabled:cursor-not-allowed group">

                                <template x-if="isSubmitting">
                                    <div class="flex gap-2 items-center">
                                        <span class="material-symbols-outlined animate-spin text-[22px]">progress_activity</span>
                                        <span>جاري التنفيذ...</span>
                                    </div>
                                </template>

                                <template x-if="!isSubmitting">
                                    <div class="flex gap-3 justify-between items-center px-6 w-full">
                                        <div class="flex gap-2 items-center">
                                            <span class="material-symbols-outlined text-[22px] transform group-hover:-translate-y-1 transition-transform">flight_takeoff</span>
                                            <span>إنشاء وتسيير الرحلة</span>
                                        </div>
                                        <div class="flex justify-center items-center w-8 h-8 font-mono text-sm rounded-lg bg-white/20" title="إجمالي الركاب المحددين">
                                            <span x-text="selectedPassengers.length">0</span>
                                        </div>
                                    </div>
                                </template>
                            </button>
                            
                            <p class="mt-4 text-xs font-bold text-center text-rose-500" x-show="selectedPassengers.length === 0" x-cloak>
                                يرجى تحديد راكب واحد على الأقل للمتابعة.
                            </p>
                        </div>

                    </div>

                    {{-- العمود الثاني والثالث: الركاب المتاحين (Grid Layout for Desktop) --}}
                    <div class="space-y-6 lg:col-span-2">
                        
                        <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-end">
                            <div>
                                <h3 class="flex gap-3 items-center text-lg font-black text-slate-800 dark:text-white font-headline">
                                    الركاب قيد الانتظار 
                                    <span class="flex justify-center items-center px-3 py-1 text-sm font-bold rounded-lg bg-primary/10 text-primary" x-text="selectedPassengers.length + ' محددين'">0</span>
                                </h3>
                                <p class="mt-1 text-xs font-bold text-gray-500">حدد الركاب الذين سينضمون لهذه الرحلة بالضغط على بطاقاتهم.</p>
                            </div>

                            <div class="flex gap-3 items-center shrink-0">
                                <div class="relative w-full md:w-64">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400 text-[20px]">search</span>
                                    <input type="text" x-model="searchQuery" placeholder="بحث برقم الجوال..."
                                        class="py-2.5 pr-10 pl-4 w-full text-sm rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark-2 focus:border-primary focus:ring-1 focus:ring-primary text-slate-700 dark:text-white">
                                </div>
                                
                                <div class="flex gap-2 p-1 rounded-xl bg-slate-100 dark:bg-boxdark-2 shrink-0">
                                    <button type="button" @click="selectAllFiltered()"
                                        class="px-4 py-2 text-xs font-bold bg-white rounded-lg shadow-sm transition-colors text-primary dark:bg-boxdark hover:text-primary-hover active:scale-95">
                                        تحديد الكل
                                    </button>
                                    <button type="button" @click="selectedPassengers = []"
                                        class="px-4 py-2 text-xs font-bold rounded-lg transition-colors text-slate-600 hover:bg-slate-200 dark:text-gray-300 dark:hover:bg-boxdark active:scale-95">
                                        إلغاء التحديد
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- شبكة عرض الركاب (Grid) لسطح المكتب --}}
                        <div class="grid grid-cols-1 auto-rows-max gap-4 md:grid-cols-2 custom-scrollbar" style="max-height: 600px; overflow-y: auto; padding-right: 4px;">
                            <template x-for="passenger in filteredPassengers()" :key="passenger.id">
                                <label class="block relative h-full cursor-pointer group">
                                    <input type="checkbox" name="passenger_ids[]" :value="passenger.id" class="hidden peer"
                                        :checked="selectedPassengers.includes(passenger.id)"
                                        @change="togglePassenger(passenger.id)">

                                    <div class="flex gap-4 justify-between items-center p-5 h-full bg-white rounded-2xl border shadow-sm transition-all dark:bg-boxdark border-slate-200 dark:border-boxdark-2 peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent peer-checked:bg-primary/5 hover:border-primary/30">
                                        
                                        <div class="flex flex-1 gap-4 items-center min-w-0">
                                            {{-- العداد --}}
                                            <div class="flex flex-col justify-center items-center w-14 h-14 rounded-xl border transition-all bg-slate-50 dark:bg-boxdark-2 text-slate-600 dark:text-gray-300 peer-checked:bg-primary peer-checked:text-white peer-checked:shadow-md shrink-0 border-slate-100 dark:border-boxdark-2 peer-checked:border-transparent">
                                                <span class="text-xl font-black leading-none font-headline" x-text="passenger.count"></span>
                                                <span class="text-[10px] font-bold mt-1">ركاب</span>
                                            </div>

                                            {{-- التفاصيل --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1.5 text-base font-black tracking-tight font-headline text-slate-800 dark:text-white" style="direction: ltr; justify-content: flex-end;">
                                                    <span x-text="getPassengerPhoneDetails(passenger.passenger_number).localNumber"></span>
                                                    <div class="w-5 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden shrink-0" 
                                                         x-html="getPassengerPhoneDetails(passenger.passenger_number).flag"></div>
                                                </div>
                                                
                                                <div class="flex gap-1.5 items-center px-2.5 py-1 w-max rounded-md bg-slate-50 dark:bg-boxdark-2">
                                                    <span class="material-symbols-outlined text-[14px] text-primary">location_on</span>
                                                    <span class="text-xs font-bold truncate text-slate-600 dark:text-gray-300" x-text="passenger.destination"></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- علامة الصح --}}
                                        <div class="flex justify-center items-center w-7 h-7 rounded-full border-2 transition-all border-slate-300 dark:border-boxdark-2 peer-checked:bg-primary peer-checked:border-primary shrink-0 peer-checked:shadow-sm">
                                            <span class="material-symbols-outlined text-white text-[18px] hidden peer-checked:block">check</span>
                                        </div>
                                    </div>
                                </label>
                            </template>

                            {{-- حالة عدم وجود ركاب --}}
                            <div x-show="filteredPassengers().length === 0" class="col-span-1 md:col-span-2 bg-slate-50 dark:bg-boxdark p-12 rounded-[2rem] border border-dashed border-slate-200 dark:border-boxdark-2 flex flex-col items-center justify-center text-slate-400">
                                <div class="flex justify-center items-center mb-4 w-20 h-20 bg-white rounded-full shadow-sm dark:bg-boxdark-2 text-slate-300">
                                    <span class="material-symbols-outlined text-[40px]">person_off</span>
                                </div>
                                <h3 class="mb-1 text-base font-black text-slate-600 dark:text-white font-headline">لا يوجد ركاب متاحين</h3>
                                <p class="max-w-sm text-sm font-bold text-center text-slate-400 dark:text-gray-500">
                                    لم يتم العثور على ركاب قيد الانتظار أو لا توجد نتائج تطابق بحثك الحالي.
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script>
        function createTripForm(driversList, countriesList, initialPassengers) {
            return {
                drivers: driversList || [],
                countries: countriesList || [],
                localPhoneNumber: '',
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                showDriverDropdown: false,
                selectedDriverId: null,
                nameInput: '',

                passengers: (initialPassengers || []).map(p => ({
                    id: p.id,
                    passenger_number: p.passenger_number,
                    count: p.count || 1,
                    destination: p.destination || 'غير محدد'
                })),
                searchQuery: '',
                selectedBranch: '',
                selectedPassengers: [],

                init() {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0] || null;
                },

                get filteredCountries() {
                    const query = this.searchCountryQuery.toLowerCase().trim();
                    if (!query) return this.countries;
                    return this.countries.filter(c => String(c.name || '').toLowerCase().includes(query) || String(c.dial_code || '').includes(query));
                },
                get fullPhoneNumber() {
                    const dial = String(this.selectedCountry?.dial_code || '').replace('+', '');
                    const local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '').replace(/^0+/, '');
                    return local ? dial + local : '';
                },
                get filteredDrivers() {
                    const local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '');
                    if (!local) return [];
                    return this.drivers.filter(d => String(d.phone || '').includes(local));
                },
                searchDriver() {
                    const exact = this.drivers.find(d => String(d.phone || '').replace(/[^\d]/g, '') === String(this.fullPhoneNumber).replace(/[^\d]/g, ''));
                    if (exact) {
                        this.selectDriver(exact);
                    } else {
                        this.selectedDriverId = null;
                        this.showDriverDropdown = true;
                    }
                },
                selectDriver(driver) {
                    this.selectedDriverId = driver.id;
                    this.nameInput = driver.name;
                    this.showDriverDropdown = false;
                    
                    const clean = String(driver.phone || '').replace(/[^\d]/g, '');
                    const sorted = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);
                    const country = sorted.find(c => clean.startsWith(c.dial_code.replace('+', '')));
                    if (country) {
                        this.selectedCountry = country;
                        this.localPhoneNumber = clean.substring(country.dial_code.replace('+', '').length);
                    }
                },
                resetSelection() {
                    this.selectedDriverId = null;
                    this.nameInput = '';
                    this.localPhoneNumber = '';
                    this.showDriverDropdown = false;
                },

                filteredPassengers() {
                    return this.passengers.filter(p => {
                        const matchSearch = this.searchQuery === '' || 
                            String(p.passenger_number).includes(this.searchQuery);
                        
                        const matchBranch = this.selectedBranch === '' || 
                            p.branch_name === this.selectedBranch;

                        return matchSearch && matchBranch;
                    });
                },
                togglePassenger(id) {
                    if (this.selectedPassengers.includes(id)) {
                        this.selectedPassengers = this.selectedPassengers.filter(pId => pId !== id);
                    } else {
                        this.selectedPassengers.push(id);
                    }
                },
                selectAllFiltered() {
                    const visibleIds = this.filteredPassengers().map(p => p.id);
                    this.selectedPassengers = [...new Set([...this.selectedPassengers, ...visibleIds])];
                },
                getPassengerPhoneDetails(number) {
                    if (!number) return { flag: '', localNumber: '' };
                    const cleanNumber = String(number).replace(/[^\d]/g, '');
                    const sorted = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);
                    for (const country of sorted) {
                        const dial = country.dial_code.replace('+', '');
                        if (cleanNumber.startsWith(dial)) {
                            const local = cleanNumber.substring(dial.length);
                            return {
                                flag: country.svg,
                                localNumber: local
                            };
                        }
                    }
                    const defaultCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                    return {
                        flag: defaultCountry ? defaultCountry.svg : '',
                        localNumber: number
                    };
                }
            }
        }
    </script>
@endsection
