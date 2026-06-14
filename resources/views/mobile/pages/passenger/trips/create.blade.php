@extends('mobile.layouts.app')

@section('title', 'إنشاء رحلة جديدة')

@section('content')

    <div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black" dir="rtl">

        {{-- ================= الهيدر وزر الرجوع ================= --}}
        <div class="flex items-center gap-3 px-4 mb-6">
            <a href="{{ route('trips.index') }}" class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90">
                <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
            </a>
            <div>
                <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white">إنشاء رحلة جديدة</h1>
                <p class="text-xs text-gray-400 mt-0.5">تجميع الركاب وتسيير الرحلة فوراً</p>
            </div>
        </div>

        {{-- ================= حاوية الفورم الأساسية ================= --}}
        <div class="px-4">
            <div class="w-full bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-5 border border-gray-100 dark:border-boxdark-2">

                <form action="{{ route('trips.store') }}" method="POST" 
                    x-data="createTripForm(@js($drivers ?? []), @js(array_values(config('countries', []))), @js($pendingPassengers ?? []))">
                    @csrf

                    {{-- ================= مكون السائق المسؤول ================= --}}
                    <div class="p-4 rounded-2xl border bg-slate-50/50 border-slate-100 dark:bg-boxdark-2/40 dark:border-boxdark-2 relative">
                        <span class="absolute -top-2.5 right-4 bg-white dark:bg-boxdark px-2 text-[10px] font-black text-slate-500 dark:text-gray-400 rounded-md border border-slate-100 dark:border-boxdark-2">
                            السائق المسؤول <span class="text-red-500">*</span>
                        </span>

                        <div class="grid relative grid-cols-1 gap-3 mt-2">
                            <input type="hidden" name="driver_id" x-model="selectedDriverId">
                            <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                            <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 transition-all group ring-slate-200 dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/50"
                                :class="selectedDriverId ? 'bg-primary/5 ring-primary/30 dark:bg-primary/5' : ''" style="direction: ltr;">

                                <div class="relative h-full shrink-0" @click.away="openCountryDropdown = false">
                                    <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r transition-colors bg-slate-50 border-slate-200 dark:bg-boxdark-2 dark:border-boxdark text-slate-600 dark:text-gray-300 min-w-max">
                                        <div class="w-6 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden shrink-0" x-html="selectedCountry?.svg"></div>
                                        <span class="text-xs font-bold font-mono dir-ltr shrink-0" x-text="selectedCountry?.dial_code" style="direction: ltr;"></span>
                                    </button>

                                    <div x-show="openCountryDropdown" x-cloak x-transition
                                        class="absolute top-full left-0 mt-1 w-[260px] max-w-[90vw] bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-xl shadow-xl z-[60] overflow-hidden">
                                        <div class="p-2 border-b border-slate-50 dark:border-boxdark-2">
                                            <input type="text" x-model="searchCountryQuery" placeholder="بحث عن دولة..."
                                                class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-slate-50 dark:bg-boxdark-2 text-slate-800 dark:text-white border border-slate-100 dark:border-boxdark-2 focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all" dir="rtl">
                                        </div>
                                        <div class="overflow-y-auto max-h-56 custom-scrollbar" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button" @click="selectedCountry = country; openCountryDropdown = false; searchDriver()"
                                                    class="flex gap-3 items-center px-3 py-2.5 w-full text-left transition-colors hover:bg-slate-50 dark:hover:bg-boxdark-2 text-slate-700 dark:text-gray-200 border-b border-slate-50 dark:border-boxdark-2 last:border-0">
                                                    <div class="w-6 h-auto flex items-center justify-center rounded-[2px] overflow-hidden shrink-0 shadow-sm" x-html="country.svg"></div>
                                                    <span class="flex-1 text-xs font-bold truncate" x-text="country.name"></span>
                                                    <span class="text-xs text-slate-400 font-mono shrink-0" x-text="country.dial_code" style="direction: ltr;"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <input type="tel" x-model="localPhoneNumber" @input="searchDriver"
                                    @focus="showDriverDropdown = true" @click.away="showDriverDropdown = false"
                                    placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 px-4 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-slate-800 dark:text-white"
                                    :class="selectedDriverId ? 'font-bold text-primary dark:text-primary-hover' : ''">

                                <button type="button" x-show="selectedDriverId" @click="resetSelection"
                                    class="absolute right-3 z-10 p-0.5 bg-white dark:bg-boxdark rounded-full text-slate-400 hover:text-red-500">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            {{-- نتائج البحث المنسدلة للسائقين --}}
                            <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId" x-transition x-cloak
                                class="absolute top-[3.25rem] right-0 w-full bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-xl shadow-lg overflow-hidden max-h-48 overflow-y-auto z-50">
                                <template x-for="driver in filteredDrivers" :key="driver.id">
                                    <button type="button" @click="selectDriver(driver)"
                                        class="flex justify-between items-center px-4 py-3 w-full text-right border-b transition-colors hover:bg-slate-50 dark:hover:bg-boxdark-2 border-slate-50 dark:border-boxdark-2">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-sm font-bold text-slate-800 dark:text-white" x-text="driver.name"></span>
                                            <span class="text-xs text-slate-500 font-mono dir-ltr text-right" x-text="driver.phone"></span>
                                        </div>
                                        <span class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                                    </button>
                                </template>
                                <div x-show="filteredDrivers.length === 0" class="px-4 py-3 text-center bg-slate-50/50 dark:bg-boxdark-2/30">
                                    <span class="text-xs font-bold text-slate-500 dark:text-gray-400">سائق جديد، سيتم حفظه تلقائياً.</span>
                                </div>
                            </div>

                            <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedDriverId !== null"
                                placeholder="اسم السائق..." required
                                class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none font-bold"
                                :class="selectedDriverId ? 'bg-slate-50 border-transparent text-emerald-600 dark:text-emerald-400 dark:bg-boxdark-2/60 cursor-not-allowed' : 'bg-white border-slate-200 dark:bg-boxdark dark:border-boxdark-2 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-700 dark:text-white'">
                        </div>
                    </div>

                    {{-- ================= قسم الركاب المتاحين (قيد الانتظار) ================= --}}
                    <div class="space-y-4 mt-6">
                        
                        {{-- هيدر المكون مع أزرار التحكم السريع --}}
                        <div class="flex justify-between items-center px-2">
                            <h3 class="flex gap-2 items-center font-black text-slate-800 dark:text-white font-headline text-sm">
                                الركاب 
                                <span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full" x-text="selectedPassengers.length">0</span>
                            </h3>
                            <div class="flex gap-2">
                                <button type="button" @click="selectAllFiltered()"
                                    class="text-[10px] font-bold text-primary bg-primary/10 px-2.5 py-1 rounded-lg active:scale-95 transition-all">تحديد الكل</button>
                                <button type="button" @click="selectedPassengers = []"
                                    class="text-[10px] font-bold text-rose-500 bg-rose-50 dark:bg-rose-500/10 px-2.5 py-1 rounded-lg active:scale-95 transition-all">إلغاء</button>
                            </div>
                        </div>


                        {{-- قائمة عرض كروت الركاب المفصلة --}}
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="passenger in filteredPassengers()" :key="passenger.id">
                                <label class="block relative cursor-pointer group">
                                    <input type="checkbox" name="passenger_ids[]" :value="passenger.id" class="hidden peer"
                                        :checked="selectedPassengers.includes(passenger.id)"
                                        @change="togglePassenger(passenger.id)">

                                    <div class="bg-white dark:bg-boxdark p-4 rounded-3xl border border-slate-100 dark:border-boxdark-2 shadow-[0_4px_20px_rgb(0,0,0,0.02)] peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent peer-checked:bg-primary/[0.02] dark:peer-checked:bg-primary/5 transition-all flex items-center justify-between gap-4 active:scale-[0.98]">
                                        
                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                            {{-- العداد الجانبي لعدد الأفراد --}}
                                            <div class="flex flex-col justify-center items-center w-12 h-12 rounded-2xl shadow-sm transition-all bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 peer-checked:bg-primary peer-checked:text-white shrink-0">
                                                <span class="text-base font-black leading-none" x-text="passenger.count"></span>
                                                <span class="text-[9px] font-bold mt-0.5">ركاب</span>
                                            </div>

                                            {{-- تفاصيل الراكب (الرقم والوجهة فقط لمنع التداخل) --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1 text-sm font-black tracking-tight font-headline text-slate-800 dark:text-white" style="direction: ltr; justify-content: flex-end;">
                                                    <span x-text="getPassengerPhoneDetails(passenger.passenger_number).localNumber"></span>
                                                    <div class="w-5 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden shrink-0" 
                                                         x-html="getPassengerPhoneDetails(passenger.passenger_number).flag"></div>
                                                </div>
                                                
                                                <div class="flex gap-1 items-center">
                                                    <span class="material-symbols-outlined text-[14px] text-primary">flag</span>
                                                    <span class="text-xs font-bold text-slate-600 dark:text-gray-300 truncate" x-text="passenger.destination"></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- التشيك الدائري المخصص جهة اليسار --}}
                                        <div class="flex justify-center items-center w-6 h-6 rounded-full border-2 transition-all border-slate-200 dark:border-boxdark-2 peer-checked:bg-primary peer-checked:border-primary shrink-0">
                                            <span class="material-symbols-outlined text-white text-[16px] hidden peer-checked:block">check</span>
                                        </div>
                                    </div>
                                </label>
                            </template>

                            {{-- رسالة فارغة --}}
                            <div x-show="filteredPassengers().length === 0" class="bg-white dark:bg-boxdark p-10 rounded-[2.5rem] border border-slate-100 dark:border-boxdark-2 shadow-sm flex flex-col items-center justify-center text-slate-400">
                                <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-slate-50 dark:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[32px] opacity-30">person_search</span>
                                </div>
                                <p class="text-xs font-bold text-slate-500 dark:text-gray-400">لا يوجد ركاب يطابقون خيارات التصفية حالياً</p>
                            </div>
                        </div>
                    </div>

                    {{-- ================= شريط الإنشاء السفلي ================= --}}
                    <div class="mt-8 mb-4 p-6 bg-white dark:bg-boxdark rounded-[2.5rem] border border-slate-50 dark:border-boxdark-2 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] flex flex-col sm:flex-row items-center justify-between gap-6"
                        x-data="{ isSubmitting: false }">

                        <button type="submit"
                            @click="if(selectedPassengers.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                            :disabled="selectedPassengers.length === 0 || isSubmitting" 
                            class="w-full h-14 px-8 bg-primary text-white rounded-2xl font-black text-sm transition-all active:scale-95 flex items-center justify-center gap-3 disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-boxdark-2 dark:disabled:text-gray-600 disabled:shadow-none">

                            <template x-if="isSubmitting">
                                <div class="flex gap-2 items-center">
                                    <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                    <span>جاري إنشاء وتسيير الرحلة...</span>
                                </div>
                            </template>

                            <template x-if="!isSubmitting">
                                <div class="flex gap-3 items-center">
                                    <span>إنشاء الرحلة وانطلاقها</span>
                                    <div class="flex justify-center items-center w-7 h-7 rounded-xl bg-white/20">
                                        <span class="text-[12px]" x-text="selectedPassengers.length">0</span>
                                    </div>
                                </div>
                            </template>
                        </button>
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
                // بيانات السائق
                drivers: driversList || [],
                countries: countriesList || [],
                localPhoneNumber: '',
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                showDriverDropdown: false,
                selectedDriverId: null,
                nameInput: '',

                // بيانات الركاب المتاحة
                passengers: (initialPassengers || []).map(p => ({
                    id: p.id,
                    passenger_number: p.passenger_number, // رقم الجوال الخاص بالراكب
                    count: p.count || 1,
                    destination: p.destination || ' '
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

                // فلاتر وتحديد الركاب
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