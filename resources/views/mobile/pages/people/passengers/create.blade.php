@extends('mobile.layouts.app')

@section('title', 'إضافة راكب')

@section('content')

    <div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black">

        {{-- ================= الهيدر وزر الرجوع ================= --}}
        <div class="flex items-center gap-3 px-4 mb-6">
            <a href="{{ route('passengers.index') }}" class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90">
                <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
            </a>
            <div>
                <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white">إضافة راكب جديد</h1>
            </div>
        </div>

        {{-- ================= حاوية الصفحة الأساسية ================= --}}
        <div class="px-4">
            <div class="w-full bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-5 border border-gray-100 dark:border-boxdark-2">

                {{-- 👇 الفورم الخاص بك بنفس اللوجيك والأسماء حرفياً 👇 --}}
                <form action="{{ route('passengers.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-5">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl bg-gray-50/50 dark:bg-boxdark-2/50 border border-gray-100 dark:border-boxdark-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">التاريخ <span class="text-error">*</span></label>
                                <input type="date" name="date" required value="{{ now()->format('Y-m-d') }}" class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-white dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40 transition-all">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">المكان <span class="text-error">*</span></label>
                                <input type="text" name="location" required placeholder="مثلاً: عدن - كريتر" class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-white dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                            <div class="relative" x-data="passengerPhonePicker(@js(array_values(config('countries', []))))">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم الراكب <span class="text-error">*</span></label>
                                <input type="hidden" name="passenger_number" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40" style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown" class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                            <template x-if="selectedCountry?.svg"><div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden" x-html="selectedCountry.svg"></div></template>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300" x-text="selectedCountry?.dial_code"></span>
                                        </button>
                                        <div x-show="openCountryDropdown" x-cloak x-transition class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input type="text" x-model="searchCountryQuery" placeholder="بحث..." class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white" dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button" @click="selectedCountry = country; openCountryDropdown = false" class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden" x-html="country.svg"></div>
                                                        <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200" x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off" :maxlength="selectedCountry?.code === 'YE' ? 9 : 15" class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
                            x-data="recordPhonePicker(@js($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone])->values()), @js(array_values(config('countries', []))))">
                            <div class="relative">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم العميل <span class="text-error">*</span></label>
                                <input type="hidden" name="customer_id" x-model="selectedRecordId">
                                <input type="hidden" name="customer_phone" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark focus-within:ring-2 focus-within:ring-primary/40" :class="selectedRecordId ? 'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' : ''" style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown" class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-gray-50 dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                            <template x-if="selectedCountry?.svg"><div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden" x-html="selectedCountry.svg"></div></template>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300" x-text="selectedCountry?.dial_code"></span>
                                        </button>
                                        <div x-show="openCountryDropdown" x-cloak x-transition class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input type="text" x-model="searchCountryQuery" placeholder="بحث..." class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white" dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button" @click="selectedCountry = country; openCountryDropdown = false; searchRecord()" class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden" x-html="country.svg"></div>
                                                        <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200" x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" @input="searchRecord" @focus="showDropdown = true" @click.away="showDropdown = false" placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off" :maxlength="selectedCountry?.code === 'YE' ? 9 : 15" class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white" :class="selectedRecordId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">
                                    <button type="button" x-show="selectedRecordId" @click="resetSelection" class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error"><span class="material-symbols-outlined text-[16px]">close</span></button>
                                </div>
                                <div x-show="showDropdown && localPhoneNumber.length > 0 && !selectedRecordId" x-transition x-cloak class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                    <template x-for="record in filteredRecords" :key="record.id">
                                        <button type="button" @click="selectRecord(record)" class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <div class="flex flex-col gap-0.5"><span class="text-sm font-bold text-on-surface dark:text-white" x-text="record.name"></span><span class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right" x-text="record.phone"></span></div>
                                            <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اسم العميل <span class="text-error">*</span></label>
                                <div class="relative">
                                    <input type="text" name="customer_name" x-model="nameInput" :readonly="selectedRecordId" :required="!selectedRecordId" placeholder="اسم العميل" class="px-4 pr-10 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30 transition-all" :class="selectedRecordId ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' : ''">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]" :class="selectedRecordId ? 'text-emerald-500' : 'text-gray-400'">person</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
                            x-data="recordPhonePicker(@js($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values()), @js(array_values(config('countries', []))))">
                            <div class="relative">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم السائق <span class="text-error">*</span></label>
                                <input type="hidden" name="driver_id" x-model="selectedRecordId">
                                <input type="hidden" name="driver_phone" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark focus-within:ring-2 focus-within:ring-primary/40" :class="selectedRecordId ? 'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' : ''" style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown" class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-gray-50 dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                            <template x-if="selectedCountry?.svg"><div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden" x-html="selectedCountry.svg"></div></template>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300" x-text="selectedCountry?.dial_code"></span>
                                        </button>
                                        <div x-show="openCountryDropdown" x-cloak x-transition class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input type="text" x-model="searchCountryQuery" placeholder="بحث..." class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white" dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button" @click="selectedCountry = country; openCountryDropdown = false; searchRecord()" class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden" x-html="country.svg"></div>
                                                        <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200" x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" @input="searchRecord" @focus="showDropdown = true" @click.away="showDropdown = false" placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off" :maxlength="selectedCountry?.code === 'YE' ? 9 : 15" class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white" :class="selectedRecordId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">
                                    <button type="button" x-show="selectedRecordId" @click="resetSelection" class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error"><span class="material-symbols-outlined text-[16px]">close</span></button>
                                </div>
                                <div x-show="showDropdown && localPhoneNumber.length > 0 && !selectedRecordId" x-transition x-cloak class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                    <template x-for="record in filteredRecords" :key="record.id">
                                        <button type="button" @click="selectRecord(record)" class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <div class="flex flex-col gap-0.5"><span class="text-sm font-bold text-on-surface dark:text-white" x-text="record.name"></span><span class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right" x-text="record.phone"></span></div>
                                            <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اسم السائق <span class="text-error">*</span></label>
                                <div class="relative">
                                    <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedRecordId" :required="!selectedRecordId" placeholder="اسم السائق" class="px-4 pr-10 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30 transition-all" :class="selectedRecordId ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' : ''">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]" :class="selectedRecordId ? 'text-emerald-500' : 'text-gray-400'">local_taxi</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عدد الركاب <span class="text-error">*</span></label>
                                <input type="number" name="count" min="1" value="1" required class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">إجمالي العمولة <span class="text-error">*</span></label>
                                <input type="number" name="total_commission" min="0" step="0.01" required placeholder="0.00" class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40 text-amber-600 font-black">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">ملاحظات</label>
                            <textarea name="note" rows="2" placeholder="أي ملاحظات إضافية..." class="px-4 py-3 w-full rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40"></textarea>
                        </div>

                    </div>
                    <div class="pt-2">
                        <button type="submit" class="flex gap-2 justify-center items-center w-full h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover active:scale-95">
                            <span class="material-symbols-outlined">save</span> حفظ البيانات
                        </button>
                    </div>
                </form>
                {{-- 👆 نهاية الفورم الخاص بك 👆 --}}
                
            </div>
        </div>
    </div>

@endsection