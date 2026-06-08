@extends('layouts.app')

@section('title', 'إنشاء رحلة جديدة')
@section('Breadcrumb', 'إنشاء رحلة جديدة')

@section('content')

<div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl">

    {{-- الهيدر --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('trips.index') }}" class="flex justify-center items-center w-12 h-12 bg-white dark:bg-boxdark rounded-2xl border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary hover:border-primary/30 active:scale-95">
                <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
            </a>
            <div>
                <h1 class="text-2xl font-black font-headline text-slate-800 dark:text-white">إنشاء رحلة جديدة</h1>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mt-1">قم باختيار السائق وتحديد الركاب لتسيير الرحلة فوراً</p>
            </div>
        </div>
    </div>

    {{-- حاوية الفورم --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-6 md:p-10 border border-gray-100 dark:border-boxdark-2">

            <form action="{{ route('trips.store') }}" method="POST" 
                x-data="createTripForm(@js($drivers ?? []), @js(array_values(config('countries', []))), @js($pendingPassengers ?? []))">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {{-- العمود الأول: تفاصيل السائق وزر الحفظ --}}
                    <div class="space-y-6 lg:col-span-1 border-b pb-8 lg:border-b-0 lg:pb-0 lg:border-l border-slate-100 dark:border-boxdark-2 lg:pl-8">
                        <div>
                            <h2 class="text-lg font-black text-slate-800 dark:text-white font-headline mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">local_taxi</span>
                                بيانات السائق المسؤول
                            </h2>
                            <p class="text-xs text-gray-500 mb-6 font-bold leading-relaxed">يرجى كتابة رقم الجوال لاختيار سائق مسجل، أو كتابة رقمه واسمه لإضافته كسائق جديد.</p>
                        </div>

                        {{-- إدخال رقم السائق --}}
                        <div class="space-y-4">
                            <input type="hidden" name="driver_id" x-model="selectedDriverId">
                            <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                            <div class="relative">
                                <label class="block text-xs font-bold text-slate-500 dark:text-gray-400 mb-2">رقم الجوال <span class="text-rose-500">*</span></label>
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 transition-all group ring-slate-200 dark:bg-boxdark-2 dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/50 h-14"
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
                                                        class="flex gap-3 items-center px-4 py-3 w-full text-left transition-colors hover:bg-slate-50 dark:hover:bg-boxdark-2 text-slate-700 dark:text-gray-200 border-b border-slate-50 dark:border-boxdark-2 last:border-0">
                                                        <div class="w-6 h-auto flex items-center justify-center rounded-[2px] overflow-hidden shadow-sm" x-html="country.svg"></div>
                                                        <span class="flex-1 text-sm font-bold" x-text="country.name"></span>
                                                        <span class="text-xs text-slate-400 font-mono" x-text="country.dial_code"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="tel" x-model="localPhoneNumber" @input="searchDriver"
                                        @focus="showDriverDropdown = true" @click.away="showDriverDropdown = false"
                                        placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-4 w-full h-full text-base text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-slate-800 dark:text-white tracking-widest"
                                        :class="selectedDriverId ? 'font-black text-primary dark:text-primary-hover' : ''">

                                    <button type="button" x-show="selectedDriverId" @click="resetSelection" x-cloak
                                        class="absolute right-4 z-10 p-1 bg-white dark:bg-boxdark rounded-full text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors border border-slate-100 dark:border-boxdark-2 shadow-sm">
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
                                                <span class="text-sm font-black text-slate-800 dark:text-white group-hover:text-primary transition-colors" x-text="driver.name"></span>
                                                <span class="text-xs text-slate-500 font-mono dir-ltr text-right" x-text="driver.phone"></span>
                                            </div>
                                            <span class="material-symbols-outlined text-slate-300 text-[20px] group-hover:text-primary transition-colors transform group-hover:-translate-x-1">arrow_back</span>
                                        </button>
                                    </template>
                                    <div x-show="filteredDrivers.length === 0" class="px-5 py-4 text-center bg-slate-50/50 dark:bg-boxdark-2/30">
                                        <div class="flex items-center justify-center mb-2 w-10 h-10 mx-auto rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-500">
                                            <span class="material-symbols-outlined text-[20px]">person_add</span>
                                        </div>
                                        <span class="text-sm font-bold text-slate-600 dark:text-gray-300">سائق جديد</span>
                                        <p class="text-xs text-slate-400 mt-1">سيتم إنشاء حساب للسائق تلقائياً.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- إدخال اسم السائق --}}
                            <div class="mt-4">
                                <label class="block text-xs font-bold text-slate-500 dark:text-gray-400 mb-2">اسم السائق <span class="text-rose-500">*</span></label>
                                <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedDriverId !== null"
                                    placeholder="أدخل الاسم الثلاثي للسائق..." required
                                    class="px-4 w-full h-14 text-sm rounded-xl border transition-colors outline-none font-bold"
                                    :class="selectedDriverId ? 'bg-slate-50 border-transparent text-emerald-600 dark:text-emerald-400 dark:bg-boxdark-2/60 cursor-not-allowed' : 'bg-white border-slate-200 dark:bg-boxdark-2 dark:border-boxdark-2 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-800 dark:text-white'">
                            </div>
                        </div>

                        {{-- زر الحفظ للديسكتوب --}}
                        <div class="pt-8 mt-8 border-t border-slate-100 dark:border-boxdark-2" x-data="{ isSubmitting: false }">
                            <button type="submit"
                                @click="if(selectedPassengers.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                                :disabled="selectedPassengers.length === 0 || isSubmitting" 
                                class="w-full h-14 bg-primary text-white rounded-xl font-black text-sm transition-all hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/30 active:scale-95 flex items-center justify-center gap-3 disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-boxdark-2 dark:disabled:text-gray-600 disabled:shadow-none disabled:cursor-not-allowed group">

                                <template x-if="isSubmitting">
                                    <div class="flex gap-2 items-center">
                                        <span class="material-symbols-outlined animate-spin text-[22px]">progress_activity</span>
                                        <span>جاري التنفيذ...</span>
                                    </div>
                                </template>

                                <template x-if="!isSubmitting">
                                    <div class="flex gap-3 items-center w-full px-6 justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[22px] transform group-hover:-translate-y-1 transition-transform">flight_takeoff</span>
                                            <span>إنشاء وتسيير الرحلة</span>
                                        </div>
                                        <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-white/20 font-mono text-sm" title="إجمالي الركاب المحددين">
                                            <span x-text="selectedPassengers.length">0</span>
                                        </div>
                                    </div>
                                </template>
                            </button>
                            
                            <p class="text-xs text-center text-rose-500 mt-4 font-bold" x-show="selectedPassengers.length === 0" x-cloak>
                                يرجى تحديد راكب واحد على الأقل للمتابعة.
                            </p>
                        </div>

                    </div>

                    {{-- العمود الثاني والثالث: الركاب المتاحين (Grid Layout for Desktop) --}}
                    <div class="lg:col-span-2 space-y-6">
                        
                        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                            <div>
                                <h3 class="flex gap-3 items-center font-black text-slate-800 dark:text-white font-headline text-lg">
                                    الركاب قيد الانتظار 
                                    <span class="flex items-center justify-center bg-primary/10 text-primary px-3 py-1 rounded-lg text-sm font-bold" x-text="selectedPassengers.length + ' محددين'">0</span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-1 font-bold">حدد الركاب الذين سينضمون لهذه الرحلة بالضغط على بطاقاتهم.</p>
                            </div>

                            <div class="flex gap-3 items-center shrink-0">
                                <div class="relative w-full md:w-64">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400 text-[20px]">search</span>
                                    <input type="text" x-model="searchQuery" placeholder="بحث برقم الجوال..."
                                        class="w-full pl-4 pr-10 py-2.5 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-boxdark-2 rounded-xl text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary text-slate-700 dark:text-white transition-all">
                                </div>
                                
                                <div class="flex gap-2 bg-slate-100 p-1 rounded-xl dark:bg-boxdark-2 shrink-0">
                                    <button type="button" @click="selectAllFiltered()"
                                        class="px-4 py-2 rounded-lg text-xs font-bold transition-colors bg-white shadow-sm text-primary dark:bg-boxdark hover:text-primary-hover active:scale-95">
                                        تحديد الكل
                                    </button>
                                    <button type="button" @click="selectedPassengers = []"
                                        class="px-4 py-2 rounded-lg text-xs font-bold transition-colors text-slate-600 hover:bg-slate-200 dark:text-gray-300 dark:hover:bg-boxdark active:scale-95">
                                        إلغاء التحديد
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- شبكة عرض الركاب (Grid) لسطح المكتب --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 auto-rows-max custom-scrollbar" style="max-height: 600px; overflow-y: auto; padding-right: 4px;">
                            <template x-for="passenger in filteredPassengers()" :key="passenger.id">
                                <label class="block relative cursor-pointer group h-full">
                                    <input type="checkbox" name="passenger_ids[]" :value="passenger.id" class="hidden peer"
                                        :checked="selectedPassengers.includes(passenger.id)"
                                        @change="togglePassenger(passenger.id)">

                                    <div class="h-full bg-white dark:bg-boxdark p-5 rounded-2xl border border-slate-200 dark:border-boxdark-2 shadow-sm peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent peer-checked:bg-primary/5 transition-all flex items-center justify-between gap-4 hover:border-primary/30">
                                        
                                        <div class="flex items-center gap-4 min-w-0 flex-1">
                                            {{-- العداد --}}
                                            <div class="flex flex-col justify-center items-center w-14 h-14 rounded-xl transition-all bg-slate-50 dark:bg-boxdark-2 text-slate-600 dark:text-gray-300 peer-checked:bg-primary peer-checked:text-white peer-checked:shadow-md shrink-0 border border-slate-100 dark:border-boxdark-2 peer-checked:border-transparent">
                                                <span class="text-xl font-black leading-none font-headline" x-text="passenger.count"></span>
                                                <span class="text-[10px] font-bold mt-1">ركاب</span>
                                            </div>

                                            {{-- التفاصيل --}}
                                            <div class="flex-1 min-w-0">
                                                <span class="block font-headline text-base font-black text-slate-800 dark:text-white tracking-tight truncate mb-1.5" style="direction: ltr; text-align: right;" x-text="passenger.passenger_number"></span>
                                                
                                                <div class="flex gap-1.5 items-center bg-slate-50 dark:bg-boxdark-2 w-max px-2.5 py-1 rounded-md">
                                                    <span class="material-symbols-outlined text-[14px] text-primary">location_on</span>
                                                    <span class="text-xs font-bold text-slate-600 dark:text-gray-300 truncate" x-text="passenger.destination"></span>
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
                                <div class="flex justify-center items-center mb-4 w-20 h-20 rounded-full bg-white dark:bg-boxdark-2 shadow-sm text-slate-300">
                                    <span class="material-symbols-outlined text-[40px]">person_off</span>
                                </div>
                                <h3 class="text-base font-black text-slate-600 dark:text-white mb-1 font-headline">لا يوجد ركاب متاحين</h3>
                                <p class="text-sm font-bold text-slate-400 dark:text-gray-500 text-center max-w-sm">
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
                }
            }
        }
    </script>
@endsection
