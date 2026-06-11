@extends('layouts.app')

@section('title', 'استلام إرسالية جديدة')

@section('content')
    {{-- ================= الحاوية الرئيسية (نقلنا x-data هنا ليشمل الشريط العلوي) ================= --}}
    <div class="pb-28 min-h-screen bg-slate-50/50 font-body" dir="rtl" x-data="{
                    isSubmitting: false,
                    activeItem: 0,

                    {{-- ========== Driver Combobox ========== --}}
                    driver_id: '{{ old('driver_id', '') }}',
                    driver_name: '{{ old('driver_name', '') }}',
                    localPhoneNumber: '{{ old('driver_phone') ? preg_replace('/^967/', '', old('driver_phone')) : '' }}',
                    selectedCountry: { name: 'اليمن', code: 'YE', dial_code: '+967' },
                    countryOpen: false,
                    countrySearch: '',
                    countries: [
                        { name: 'اليمن', code: 'YE', dial_code: '+967' },
                        { name: 'السعودية', code: 'SA', dial_code: '+966' },
                        { name: 'الإمارات', code: 'AE', dial_code: '+971' },
                    ],
                    get filteredCountries() {
                        if (this.countrySearch === '') return this.countries;
                        return this.countries.filter(c => c.name.toLowerCase().includes(this.countrySearch.toLowerCase()) || c.dial_code.includes(this.countrySearch));
                    },
                    get fullPhone() {
                        return this.selectedCountry.dial_code.replace('+', '') + this.localPhoneNumber;
                    },
                    driverOpen: false,
                    drivers: @js($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])),
                    get filteredDrivers() {
                        if (this.localPhoneNumber.trim() === '') return this.drivers;
                        const s = this.localPhoneNumber.trim();
                        return this.drivers.filter(d => d.phone && d.phone.includes(s));
                    },
                    selectDriver(driver) {
                        this.driver_id = driver.id;
                        this.driver_name = driver.name;
                        let p = driver.phone || '';
                        const codes = this.countries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                        for (const code of codes) {
                            if (p.startsWith(code)) {
                                this.selectedCountry = this.countries.find(c => c.dial_code === '+' + code);
                                p = p.substring(code.length);
                                break;
                            }
                        }
                        this.localPhoneNumber = p;
                        this.driverOpen = false;
                    },
                    onPhoneInput() {
                        this.driver_id = null;
                        this.driver_name = '';
                        this.driverOpen = true;
                    },

                    {{-- ========== Dynamic Items ========== --}}
                    items: @js(old('items', [['bond_number' => '', 'sender_name' => '', 'sender_phone' => '', 'receiver_name' => '', 'receiver_phone' => '', 'package_type' => 'كرتون', 'item_notes' => '', 'payment_status' => 'unpaid', 'amount' => '']])),
                    errorIndices: @js(collect($errors->keys())->map(fn($key) => preg_match('/^items\.(\d+)/', $key, $m) ? (int) $m[1] : null)->filter(fn($v) => !is_null($v))->unique()->values()),

                    addItem() {
                        this.items.push({ bond_number: '', sender_name: '', sender_phone: '', receiver_name: '', receiver_phone: '', package_type: 'كرتون', item_notes: '', payment_status: 'unpaid', amount: '' });
                        this.activeItem = this.items.length - 1;
                    },
                    removeItem(index) {
                        if (this.items.length > 1) {
                            this.items.splice(index, 1);
                            if (this.activeItem === index) {
                                this.activeItem = Math.max(0, index - 1);
                            } else if (this.activeItem > index) {
                                this.activeItem--;
                            }
                        }
                    }
                }">

        {{-- ================= الشريط العلوي (مطابق للصورة) ================= --}}
        <div class="sticky top-0 z-[60] bg-white border-b border-slate-100 shadow-sm">
            <div class="flex justify-between items-center px-4 mx-auto max-w-7xl h-20 sm:px-6 lg:px-8">

                {{-- اليمين: العنوان --}}
                <div class="flex gap-4 items-center">
                    <a href="{{ route('shipmentpackage.incoming.index') }}"
                        class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:bg-slate-50 active:scale-95">
                        <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
                    </a>
                    <div>
                        <h1 class="text-lg font-black tracking-tight text-slate-800 md:text-xl font-headline">استلام إرسالية
                        </h1>
                        <p class="text-[11px] font-bold text-slate-500 mt-0.5">تسجيل إرسالية واردة جديدة وتحديد الطرود</p>
                    </div>
                </div>

                {{-- اليسار: زر الاعتماد (مرتبط بالفورم) --}}
                <div class="flex gap-4 items-center">
                    <div class="hidden flex-col items-end md:flex">
                        <span class="text-[10px] font-bold text-slate-400">الطرود المحددة</span>
                        <span class="text-sm font-black text-primary" x-text="items.length + ' طرد'"></span>
                    </div>

                    <button type="button" @click="document.getElementById('shipmentForm').requestSubmit()"
                        :disabled="items.length === 0 || isSubmitting"
                        class="flex gap-2 justify-center items-center px-6 h-12 text-sm font-bold rounded-xl transition-all active:scale-95 disabled:cursor-not-allowed"
                        :class="isSubmitting || items.length === 0 ? 'bg-slate-100 text-slate-400' : 'bg-primary shadow-lg shadow-primary/30 text-white hover:bg-primary/90'">

                        <template x-if="isSubmitting">
                            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                        </template>
                        <template x-if="!isSubmitting">
                            <span class="material-symbols-outlined text-[20px]">done_all</span>
                        </template>
                        <span x-text="isSubmitting ? 'جاري الاعتماد...' : 'اعتماد الرحلة'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= محتوى الصفحة (شبكة بعمودين) ================= --}}
        <div class="px-4 pt-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <form id="shipmentForm" action="{{ route('shipmentpackage.incoming.store') }}" method="POST"
                @submit="setTimeout(() => isSubmitting = true, 50)">
                @csrf

                <div class="grid grid-cols-1 gap-6 items-start lg:grid-cols-12">

                    {{-- ================= العمود الأيمن: بيانات السائق والرحلة (col-span-4) ================= --}}
                    <div class="space-y-6 lg:col-span-4 lg:sticky lg:top-28">

                        {{-- 1. بطاقة السائق (مطابقة للصورة بالخلفية الملونة) --}}
                        <div class="relative bg-white rounded-[2rem] border border-slate-100 shadow-sm">
                            {{-- لمسة جمالية للخلفية --}}
                            <div class="absolute top-0 left-0 w-full h-24 rounded-t-[2rem] bg-primary/5"></div>

                            <div class="relative p-6">
                                <div class="flex gap-3 justify-center items-center mb-6">
                                    <h3 class="text-base font-black text-slate-800 font-headline">بيانات السائق المسؤول</h3>
                                    <div
                                        class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                                        <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <input type="hidden" name="driver_id" :value="driver_id">
                                    <input type="hidden" name="driver_phone" :value="fullPhone">

                                    {{-- رقم هاتف السائق --}}
                                    <div>
                                        <label class="block mb-1.5 text-[11px] font-bold text-slate-500">
                                            رقم هاتف السائق <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="flex relative items-center w-full h-12 bg-white rounded-xl border transition-all z-[60] border-slate-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20"
                                            :class="driver_id ? 'border-emerald-400 ring-1 ring-emerald-400 bg-emerald-50/20' : ''">

                                            <input type="tel" x-model="localPhoneNumber" @input="onPhoneInput()"
                                                @focus="driverOpen = true" @click.outside="driverOpen = false"
                                                placeholder="7XXXXXXXX" dir="ltr" autocomplete="off"
                                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                                class="flex-grow px-3 min-w-0 h-full text-sm font-bold tracking-wider text-left bg-transparent border-none outline-none sm:px-4 focus:ring-0 text-slate-700">

                                            <div x-show="driver_id" class="flex items-center px-1 pointer-events-none">
                                                <span
                                                    class="material-symbols-outlined text-[18px] text-emerald-500">check_circle</span>
                                            </div>

                                            <button type="button" @click="countryOpen = !countryOpen"
                                                class="flex gap-1.5 items-center px-3 h-full rounded-l-xl border-r transition-colors bg-slate-50 border-slate-200 hover:bg-slate-100 shrink-0">
                                                <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                                    class="w-5 h-auto rounded-[2px] shadow-sm">
                                                <span class="text-xs font-bold text-slate-600" dir="ltr"
                                                    x-text="selectedCountry.dial_code"></span>
                                                <span
                                                    class="material-symbols-outlined text-[14px] text-slate-400">expand_more</span>
                                            </button>

                                            {{-- Dropdown Countries --}}
                                            <div x-show="countryOpen" @click.outside="countryOpen = false" x-transition
                                                x-cloak
                                                class="absolute left-0 right-0 top-full z-[999] mt-2 max-h-60 overflow-hidden rounded-2xl bg-white border border-slate-100 shadow-xl">
                                                <div class="p-2 border-b border-slate-50">
                                                    <input type="text" x-model="countrySearch" placeholder="بحث..."
                                                        class="px-3 py-2 w-full text-xs font-bold rounded-xl border outline-none bg-slate-50 border-slate-200 focus:border-primary">
                                                </div>
                                                <div class="overflow-y-auto p-1 max-h-40 custom-scrollbar">
                                                    <template x-for="country in filteredCountries" :key="country.code">
                                                        <button type="button"
                                                            @click="selectedCountry = country; countryOpen = false; countrySearch = ''"
                                                            class="flex justify-between items-center px-3 py-2.5 w-full text-sm text-left rounded-lg hover:bg-primary/5">
                                                            <div class="flex gap-3 items-center">
                                                                <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                                                    class="w-5 h-auto shadow-sm">
                                                                <span class="text-xs font-bold text-slate-700"
                                                                    x-text="country.name"></span>
                                                            </div>
                                                            <span class="font-mono text-[10px] font-bold text-slate-400"
                                                                dir="ltr" x-text="country.dial_code"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Dropdown Drivers --}}
                                        <div class="relative z-[50]">
                                            <div x-show="driverOpen && localPhoneNumber.trim().length > 0 && !driver_id"
                                                x-transition x-cloak @click.outside="driverOpen = false"
                                                class="overflow-y-auto absolute right-0 left-0 p-1 mt-1 max-h-56 bg-white rounded-2xl border shadow-xl border-slate-100 custom-scrollbar">
                                                <template x-for="driver in filteredDrivers" :key="driver.id">
                                                    <button type="button" @click="selectDriver(driver)"
                                                        class="flex justify-between items-center px-4 py-3 w-full text-right rounded-lg border-b transition-colors border-slate-50 hover:bg-primary/5 last:border-0">
                                                        <span class="text-xs font-black text-slate-800"
                                                            x-text="driver.name"></span>
                                                        <span class="font-mono text-[10px] font-bold text-slate-400 dir-ltr"
                                                            x-text="driver.phone || '—'"></span>
                                                    </button>
                                                </template>
                                                <div x-show="filteredDrivers.length === 0"
                                                    class="flex gap-2 justify-center items-center p-3 m-1 text-xs font-bold rounded-xl text-slate-500 bg-slate-50">
                                                    <span
                                                        class="material-symbols-outlined text-[16px] text-primary">person_add</span>
                                                    سائق جديد
                                                </div>
                                            </div>
                                        </div>
                                        @error('driver_phone') <p class="mt-1 text-[10px] font-bold text-rose-500">
                                            {{ $message }}
                                        </p> @enderror
                                    </div>

                                    {{-- اسم السائق --}}
                                    <div>
                                        <label class="block mb-1.5 text-[11px] font-bold text-slate-500">اسم السائق <span
                                                class="text-rose-500">*</span></label>
                                        <input type="text" name="driver_name" x-model="driver_name"
                                            placeholder="اسم السائق..." :readonly="!!driver_id"
                                            :class="driver_id ? 'bg-slate-50 text-slate-500 border-slate-200 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700'"
                                            class="px-4 w-full h-12 text-sm font-bold rounded-xl border transition-all outline-none">
                                        @error('driver_name') <p class="mt-1 text-[10px] font-bold text-rose-500">
                                            {{ $message }}
                                        </p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. بطاقة البيانات الأساسية --}}
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6">
                            <h3 class="mb-5 text-sm font-black text-slate-800 font-headline">البيانات الأساسية</h3>

                            <div class="space-y-4">
                                {{-- رقم الإرسالية --}}
                                <div>
                                    <label class="block mb-1.5 text-[11px] font-bold text-slate-500">رقم الإرسالية (التتبع)
                                        <span class="text-rose-500">*</span></label>
                                    <div class="relative group">
                                        <input type="text" name="tracking_number" value="{{ old('tracking_number') }}"
                                            required placeholder="PKG-1001"
                                            class="pr-11 pl-4 w-full h-12 text-sm font-bold rounded-xl border transition-all outline-none bg-slate-50 border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:bg-white text-slate-700">
                                        <div
                                            class="flex absolute inset-y-0 right-0 items-center pr-3.5 pointer-events-none text-slate-400 group-focus-within:text-primary">
                                            <span class="material-symbols-outlined text-[20px]">tag</span>
                                        </div>
                                    </div>
                                    @error('tracking_number') <p class="mt-1 text-[10px] font-bold text-rose-500">
                                        {{ $message }}
                                    </p> @enderror
                                </div>

                                {{-- اختيار المكتب والفرع --}}
                                <div x-data="{
                                                offices: @js($offices),
                                                selectedOffice: '{{ old('office_id', '') }}',
                                                selectedBranch: '{{ old('sender_office_branch_id', '') }}',
                                                get currentBranches() {
                                                    let office = this.offices.find(o => o.id == this.selectedOffice);
                                                    return office ? office.branches : [];
                                                }
                                            }" class="space-y-4">
                                    <div>
                                        <label class="block mb-1.5 text-[11px] font-bold text-slate-500">المكتب الخارجي
                                            <span class="text-rose-500">*</span></label>
                                        <select name="office_id" x-model="selectedOffice" @change="selectedBranch = ''"
                                            required
                                            class="px-4 w-full h-12 text-sm font-bold rounded-xl border transition-all appearance-none outline-none bg-slate-50 border-slate-200 focus:border-primary text-slate-700">
                                            <option value="" disabled>-- اختر --</option>
                                            <template x-for="office in offices" :key="office.id">
                                                <option :value="office.id" x-text="office.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block mb-1.5 text-[11px] font-bold text-slate-500">الفرع <span
                                                class="text-rose-500">*</span></label>
                                        <div class="relative">
                                            <select name="sender_office_branch_id" x-model="selectedBranch" required
                                                :disabled="!selectedOffice"
                                                :class="!selectedOffice ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed' : 'bg-slate-50 focus:bg-white focus:border-primary text-slate-700 border-slate-200'"
                                                class="px-4 w-full h-12 text-sm font-bold rounded-xl border transition-all appearance-none outline-none">
                                                <option value="" disabled>-- اختر --</option>
                                                <template x-for="branch in currentBranches" :key="branch.id">
                                                    <option :value="branch.id"
                                                        x-text="branch.name + (branch.city ? ' (' + branch.city + ')' : '')">
                                                    </option>
                                                </template>
                                            </select>
                                            <div x-show="!selectedOffice"
                                                class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none text-slate-400">
                                                <span class="material-symbols-outlined text-[18px]">lock</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ملاحظات عامة --}}
                                <div>
                                    <label class="block mb-1.5 text-[11px] font-bold text-slate-500">ملاحظات على
                                        الإرسالية</label>
                                    <textarea name="notes" rows="2" placeholder="اختياري..."
                                        class="p-4 w-full text-sm font-bold rounded-xl border transition-all outline-none resize-none bg-slate-50 border-slate-200 focus:bg-white focus:border-primary text-slate-700">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= العمود الأيسر: الطرود (col-span-8) ================= --}}
                    <div class="lg:col-span-8">
                        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm">

                            {{-- رأس قسم الطرود --}}
                            <div class="flex justify-between items-center px-6 py-5 border-b border-slate-50">
                                <div class="flex gap-3 items-center">
                                    <div
                                        class="flex justify-center items-center w-10 h-10 text-emerald-500 bg-emerald-50 rounded-xl shrink-0">
                                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-800 font-headline">الطرود المضافة للشحن</h3>
                                </div>

                                <button type="button" @click="addItem()"
                                    class="flex gap-2 justify-center items-center px-4 h-10 text-xs font-bold text-white bg-emerald-500 rounded-xl shadow-sm transition-all shadow-emerald-500/30 hover:bg-emerald-600 active:scale-95 shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                    إضافة طرد
                                </button>
                            </div>

                            {{-- قائمة الطرود (الأكورديون) --}}
                            <div class="p-6 space-y-4">
                                <template x-for="(item, index) in items" :key="index">
                                    <div :class="errorIndices.includes(index) ? 'border-rose-300 shadow-sm' : (activeItem === index ? 'border-primary/40 shadow-[0_8px_30px_-10px_rgba(var(--color-primary-rgb),0.3)]' : 'border-slate-100 hover:border-slate-200')"
                                        class="relative bg-white rounded-[1.5rem] border transition-all duration-300">

                                        {{-- شريط رأس الطرد --}}
                                        <div @click="activeItem = activeItem === index ? null : index"
                                            class="flex justify-between items-center p-4 transition-colors cursor-pointer select-none"
                                            :class="activeItem === index ? 'bg-slate-50/50 border-b border-slate-100 rounded-t-[1.5rem]' : 'rounded-[1.5rem]'">

                                            <div class="flex gap-4 items-center">
                                                <div class="flex justify-center items-center w-8 h-8 text-xs font-black rounded-lg transition-colors"
                                                    :class="activeItem === index ? 'bg-primary text-white' : 'bg-slate-100 text-slate-500'"
                                                    x-text="index + 1">
                                                </div>
                                                <div class="flex flex-col">
                                                    <h4 class="text-sm font-black transition-colors"
                                                        :class="activeItem === index ? 'text-primary' : 'text-slate-800'"
                                                        x-text="item.bond_number ? 'طرد رقم: ' + item.bond_number : 'طرد جديد (بدون رقم)'">
                                                    </h4>
                                                    <p class="text-[11px] font-bold mt-0.5"
                                                        :class="activeItem === index ? 'text-slate-500' : 'text-slate-400'"
                                                        x-text="item.receiver_name ? 'إلى المستلم: ' + item.receiver_name : 'يرجى إكمال البيانات'">
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="flex gap-3 items-center">
                                                <button type="button" @click.stop="removeItem(index)"
                                                    x-show="items.length > 1"
                                                    class="flex justify-center items-center w-8 h-8 text-rose-400 rounded-lg transition-colors hover:bg-rose-50 active:scale-90">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                                <div class="flex justify-center items-center w-8 h-8 rounded-full transition-colors"
                                                    :class="activeItem === index ? 'bg-primary/10 text-primary' : 'bg-slate-50 text-slate-400'">
                                                    <span
                                                        class="material-symbols-outlined text-[20px] transition-transform duration-300"
                                                        :class="activeItem === index ? 'rotate-180' : ''">expand_more</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- محتوى الطرد الداخلي --}}
                                        <div x-show="activeItem === index" x-collapse>
                                            <div class="p-6 space-y-6">

                                                {{-- رقم السند --}}
                                                <div>
                                                    <label class="block mb-1.5 text-[11px] font-bold text-slate-500">رقم
                                                        السند / الطرد <span class="text-rose-500">*</span></label>
                                                    <input type="text" :name="`items[${index}][bond_number]`"
                                                        x-model="item.bond_number" placeholder="أدخل رقم السند" required
                                                        class="px-4 w-full h-11 font-mono text-sm font-bold rounded-xl border outline-none bg-slate-50 border-slate-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">
                                                </div>

                                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                                    {{-- ================= بطاقة المرسل ================= --}}
                                                    <div class="p-4 space-y-4 rounded-2xl border bg-slate-50/50 border-slate-100"
                                                        x-data="{
                                            sdOpen: false, sdSearch: '', sdLocal: '', sdSelected: null, sdCustomers: @js($customers),
                                            sdCountries: [ { name: 'اليمن', code: 'YE', dial_code: '+967' }, { name: 'السعودية', code: 'SA', dial_code: '+966' } ],
                                            get sdFilteredCustomers() {
                                                if (this.sdLocal.trim() === '') return this.sdCustomers;
                                                return this.sdCustomers.filter(c => c.phone && c.phone.includes(this.sdLocal.trim()));
                                            },
                                            selectCustomer(customer) {
                                                item.sender_name = customer.name;
                                                let p = customer.phone || '';
                                                const codes = this.sdCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                for (const code of codes) {
                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                    if (regex.test(p)) {
                                                        this.sdSelected = this.sdCountries.find(c => c.dial_code.replace('+', '') === code);
                                                        p = p.replace(regex, ''); break;
                                                    }
                                                }
                                                this.sdLocal = p; this.sdOpen = false;
                                            }
                                        }" x-init="
                                            sdSelected = sdCountries.find(c => c.code === 'YE') || sdCountries[0];
                                            if (item.sender_phone) {
                                                let p = item.sender_phone;
                                                for (const code of sdCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length)) {
                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                    if (regex.test(p)) { sdSelected = sdCountries.find(c => c.dial_code.replace('+', '') === code); sdLocal = p.replace(regex, ''); break; }
                                                }
                                                if (!sdLocal && p) sdLocal = p;
                                            }
                                        " x-effect="item.sender_phone = sdLocal.trim() ? (sdSelected?.dial_code.replace('+', '') || '') + sdLocal.trim() : ''">

                                                        <h5
                                                            class="pb-2 text-xs font-black border-b text-slate-700 border-slate-200">
                                                            بيانات المرسل (اختياري)</h5>

                                                        <div class="relative z-[40]">
                                                            <label
                                                                class="block mb-1.5 text-[10px] font-bold text-slate-500">رقم
                                                                الهاتف (اختياري)</label>
                                                            <div
                                                                class="flex overflow-visible items-center w-full h-11 bg-white rounded-lg border border-slate-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                                                                <input type="tel" x-model="sdLocal" @input="sdOpen = true"
                                                                    @focus="sdOpen = true" @click.outside="sdOpen = false"
                                                                    placeholder="7XXXXXXXX" dir="ltr" autocomplete="off"
                                                                    :maxlength="sdSelected?.code === 'YE' ? 9 : 15"
                                                                    class="flex-grow px-3 w-full min-w-0 h-full text-sm font-bold tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 text-slate-700">

                                                                <button type="button" @click="sdOpen = !sdOpen"
                                                                    class="flex gap-1.5 items-center px-2.5 h-full rounded-l-lg border-r bg-slate-50 border-slate-200 shrink-0">
                                                                    <template x-if="sdSelected">
                                                                        <div class="flex gap-1.5 items-center">
                                                                            <img :src="`https://flagcdn.com/w20/${sdSelected.code.toLowerCase()}.png`"
                                                                                class="w-4 h-auto shadow-sm">
                                                                            <span
                                                                                class="text-[11px] font-bold text-slate-600"
                                                                                dir="ltr"
                                                                                x-text="sdSelected.dial_code"></span>
                                                                        </div>
                                                                    </template>
                                                                </button>
                                                            </div>
                                                            <div x-show="sdOpen && sdLocal.trim().length > 0" x-transition
                                                                x-cloak
                                                                class="overflow-y-auto absolute right-0 left-0 p-1 mt-1 max-h-48 bg-white rounded-xl border shadow-xl border-slate-100 custom-scrollbar">
                                                                <template x-for="customer in sdFilteredCustomers"
                                                                    :key="customer.id">
                                                                    <button type="button" @click="selectCustomer(customer)"
                                                                        class="flex justify-between items-center px-3 py-2.5 w-full text-right rounded-lg border-b border-slate-50 hover:bg-primary/5">
                                                                        <span class="text-xs font-black text-slate-800"
                                                                            x-text="customer.name"></span>
                                                                        <span
                                                                            class="font-mono text-[10px] font-bold text-slate-400 dir-ltr"
                                                                            x-text="customer.phone"></span>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label
                                                                class="block mb-1.5 text-[10px] font-bold text-slate-500">اسم
                                                                المرسل (اختياري)</label>
                                                            {{-- حقل مخفي مضاف لإرسال رقم الهاتف الصافي السليم السيرفر --}}
                                                            <input type="hidden" :name="`items[${index}][sender_phone]`"
                                                                x-model="item.sender_phone">

                                                            <input type="text" :name="`items[${index}][sender_name]`"
                                                                x-model="item.sender_name" placeholder="الاسم (اختياري)"
                                                                class="px-3 w-full h-11 text-sm font-bold bg-white rounded-lg border outline-none border-slate-200 focus:border-primary text-slate-700">
                                                        </div>
                                                    </div>

                                                    {{-- ================= بطاقة المستلم ================= --}}
                                                    <div class="p-4 space-y-4 rounded-2xl border bg-slate-50/50 border-slate-100"
                                                        x-data="{
                                            rcOpen: false, rcSearch: '', rcLocal: '', rcSelected: null, rcCustomers: @js($customers),
                                            rcCountries: [ { name: 'اليمن', code: 'YE', dial_code: '+967' }, { name: 'السعودية', code: 'SA', dial_code: '+966' } ],
                                            get rcFilteredCustomers() {
                                                if (this.rcLocal.trim() === '') return this.rcCustomers;
                                                return this.rcCustomers.filter(c => c.phone && c.phone.includes(this.rcLocal.trim()));
                                            },
                                            selectCustomer(customer) {
                                                item.receiver_name = customer.name;
                                                let p = customer.phone || '';
                                                const codes = this.rcCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                for (const code of codes) {
                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                    if (regex.test(p)) {
                                                        this.rcSelected = this.rcCountries.find(c => c.dial_code.replace('+', '') === code);
                                                        p = p.replace(regex, ''); break;
                                                    }
                                                }
                                                this.rcLocal = p; this.rcOpen = false;
                                            }
                                        }" x-init="
                                            rcSelected = rcCountries.find(c => c.code === 'YE') || rcCountries[0];
                                            if (item.receiver_phone) {
                                                let p = item.receiver_phone;
                                                for (const code of rcCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length)) {
                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                    if (regex.test(p)) { rcSelected = rcCountries.find(c => c.dial_code.replace('+', '') === code); rcLocal = p.replace(regex, ''); break; }
                                                }
                                                if (!rcLocal && p) rcLocal = p;
                                            }
                                        " x-effect="item.receiver_phone = (rcSelected?.dial_code.replace('+', '') || '') + rcLocal">

                                                        <h5
                                                            class="pb-2 text-xs font-black border-b text-slate-700 border-slate-200">
                                                            بيانات المستلم</h5>

                                                        <div class="relative z-[30]">
                                                            <label
                                                                class="block mb-1.5 text-[10px] font-bold text-slate-500">رقم
                                                                الهاتف <span class="text-rose-500">*</span></label>
                                                            <div
                                                                class="flex overflow-visible items-center w-full h-11 bg-white rounded-lg border border-slate-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                                                                <input type="tel" x-model="rcLocal" @input="rcOpen = true"
                                                                    @focus="rcOpen = true" @click.outside="rcOpen = false"
                                                                    placeholder="7XXXXXXXX" dir="ltr" autocomplete="off"
                                                                    required :maxlength="rcSelected?.code === 'YE' ? 9 : 15"
                                                                    class="flex-grow px-3 w-full min-w-0 h-full text-sm font-bold tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 text-slate-700">

                                                                <button type="button" @click="rcOpen = !rcOpen"
                                                                    class="flex gap-1.5 items-center px-2.5 h-full rounded-l-lg border-r bg-slate-50 border-slate-200 shrink-0">
                                                                    <template x-if="rcSelected">
                                                                        <div class="flex gap-1.5 items-center">
                                                                            <img :src="`https://flagcdn.com/w20/${rcSelected.code.toLowerCase()}.png`"
                                                                                class="w-4 h-auto shadow-sm">
                                                                            <span
                                                                                class="text-[11px] font-bold text-slate-600"
                                                                                dir="ltr"
                                                                                x-text="rcSelected.dial_code"></span>
                                                                        </div>
                                                                    </template>
                                                                </button>
                                                            </div>
                                                            <div x-show="rcOpen && rcLocal.trim().length > 0" x-transition
                                                                x-cloak
                                                                class="overflow-y-auto absolute right-0 left-0 p-1 mt-1 max-h-48 bg-white rounded-xl border shadow-xl border-slate-100 custom-scrollbar">
                                                                <template x-for="customer in rcFilteredCustomers"
                                                                    :key="customer.id">
                                                                    <button type="button" @click="selectCustomer(customer)"
                                                                        class="flex justify-between items-center px-3 py-2.5 w-full text-right rounded-lg border-b border-slate-50 hover:bg-primary/5">
                                                                        <span class="text-xs font-black text-slate-800"
                                                                            x-text="customer.name"></span>
                                                                        <span
                                                                            class="font-mono text-[10px] font-bold text-slate-400 dir-ltr"
                                                                            x-text="customer.phone"></span>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label
                                                                class="block mb-1.5 text-[10px] font-bold text-slate-500">اسم
                                                                المستلم <span class="text-rose-500">*</span></label>
                                                            {{-- حقل مخفي مضاف لإرسال رقم هاتف المستلم تلقائياً عبر الفورم
                                                            --}}
                                                            <input type="hidden" :name="`items[${index}][receiver_phone]`"
                                                                x-model="item.receiver_phone">

                                                            <input type="text" :name="`items[${index}][receiver_name]`"
                                                                x-model="item.receiver_name" placeholder="الاسم" required
                                                                class="px-3 w-full h-11 text-sm font-bold bg-white rounded-lg border outline-none border-slate-200 focus:border-primary text-slate-700">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- حالة الدفع ونوع الشحنة --}}
                                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                                    {{-- حالة الدفع --}}
                                                    <div class="md:col-span-1">
                                                        <label
                                                            class="block mb-1.5 text-[11px] font-bold text-slate-500">التحصيل
                                                            <span class="text-rose-500">*</span></label>
                                                        <div
                                                            class="flex p-1 rounded-xl border bg-slate-100 border-slate-200/50">
                                                            <label class="flex-1 cursor-pointer">
                                                                <input type="radio" value="unpaid"
                                                                    :name="`items[${index}][payment_status]`"
                                                                    x-model="item.payment_status" class="sr-only peer">
                                                                <div
                                                                    class="flex justify-center items-center w-full h-9 text-xs font-black rounded-lg transition-all text-slate-400 peer-checked:bg-white peer-checked:text-primary peer-checked:shadow-sm">
                                                                    عند الاستلام
                                                                </div>
                                                            </label>
                                                            <label class="flex-1 cursor-pointer">
                                                                <input type="radio" value="paid"
                                                                    :name="`items[${index}][payment_status]`"
                                                                    x-model="item.payment_status" @change="item.amount = 0"
                                                                    class="sr-only peer">
                                                                <div
                                                                    class="flex justify-center items-center w-full h-9 text-xs font-black rounded-lg transition-all text-slate-400 peer-checked:bg-white peer-checked:text-emerald-500 peer-checked:shadow-sm">
                                                                    مدفوع
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    {{-- المبلغ --}}
                                                    <div class="md:col-span-1">
                                                        <label
                                                            class="block mb-1.5 text-[11px] font-bold text-slate-500">المبلغ
                                                            (ر.ي) <span class="text-rose-500">*</span></label>
                                                        <input type="number" :name="`items[${index}][amount]`"
                                                            x-model="item.amount" :readonly="item.payment_status === 'paid'"
                                                            :class="item.payment_status === 'paid' ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-primary text-slate-700'"
                                                            placeholder="0" min="0" step="1"
                                                            class="px-3 w-full h-11 font-mono text-sm font-bold rounded-xl border transition-all outline-none">
                                                    </div>

                                                    {{-- نوع الشحنة --}}
                                                    <div class="relative z-[20] md:col-span-1"
                                                        x-data="{ isOpen: false, options: ['كرتون', 'كيس',] }">
                                                        <label class="block mb-1.5 text-[11px] font-bold text-slate-500">نوع
                                                            الشحنة <span class="text-rose-500">*</span></label>
                                                        <input type="text" :name="`items[${index}][package_type]`"
                                                            x-model="item.package_type" @focus="isOpen = true"
                                                            @click.away="isOpen = false" placeholder="اختر النوع..."
                                                            required
                                                            class="px-4 pl-10 w-full h-11 text-sm font-bold bg-white rounded-xl border transition-all outline-none border-slate-200 focus:border-primary text-slate-700">
                                                        <button type="button" @click="isOpen = !isOpen"
                                                            class="flex absolute bottom-1 left-1 justify-center items-center w-9 h-9 rounded-lg text-slate-400 hover:text-primary">
                                                            <span class="material-symbols-outlined text-[20px]"
                                                                :class="isOpen ? 'rotate-180' : ''">expand_more</span>
                                                        </button>
                                                        <div x-show="isOpen" x-cloak x-transition
                                                            class="overflow-hidden absolute right-0 left-0 py-1 mt-1 bg-white rounded-xl border shadow-xl border-slate-100">
                                                            <template x-for="option in options" :key="option">
                                                                <button type="button"
                                                                    @click="item.package_type = option; isOpen = false"
                                                                    class="flex justify-between items-center px-4 py-2.5 w-full text-xs font-bold text-right text-slate-700 hover:bg-slate-50">
                                                                    <span x-text="option"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ملاحظات الطرد --}}
                                                <div>
                                                    <label class="block mb-1.5 text-[11px] font-bold text-slate-500">ملاحظات
                                                        إضافية على الطرد</label>
                                                    <input type="text" :name="`items[${index}][item_notes]`"
                                                        x-model="item.item_notes" placeholder="محتوى قابل للكسر، إلخ..."
                                                        class="px-4 w-full h-11 text-sm font-bold rounded-xl border outline-none bg-slate-50/50 border-slate-200 focus:bg-white focus:border-primary text-slate-700">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- حالة عدم وجود طرود --}}
                                <div x-show="items.length === 0"
                                    class="flex flex-col justify-center items-center py-12 rounded-[1.5rem] border-2 border-dashed bg-slate-50/50 border-slate-200">
                                    <span class="mb-3 text-5xl text-slate-300 material-symbols-outlined">inventory_2</span>
                                    <p class="text-sm font-bold text-slate-500 font-headline">لا توجد طرود مضافة حالياً</p>
                                    <button type="button" @click="addItem()"
                                        class="px-5 py-2.5 mt-4 text-xs font-black text-emerald-600 bg-emerald-100 rounded-xl transition-colors hover:bg-emerald-200">
                                        إضافة الطرد الأول
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection