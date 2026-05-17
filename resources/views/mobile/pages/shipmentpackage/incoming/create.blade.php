@extends('mobile.layouts.app')

@section('title', 'استلام إرسالية جديدة')

@section('content')
    <div class="min-h-screen bg-surface dark:bg-boxdark-2 font-body" dir="rtl">

        {{-- ================= الشريط العلوي (Sticky Header) ================= --}}
        <div
            class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
            <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
                <div class="flex gap-4 items-center">
                    <a href="{{ route('shipmentpackage.incoming.index') }}"
                        class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                    <div>
                        <h1 class="text-xl font-black md:text-2xl font-headline text-on-surface dark:text-white">استلام
                            إرسالية</h1>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-bodydark">تسجيل إرسالية واردة جديدة مع طرودها</p>
                    </div>
                </div>

                {{-- زر الحفظ يظهر في الديسكتوب في الأعلى --}}
                <button type="submit" form="incomingPackageForm" :disabled="isSubmitting"
                    class="hidden gap-2 justify-center items-center px-6 h-11 text-sm font-bold text-white bg-orange-500 rounded-xl shadow-md transition-all md:flex hover:bg-orange-600 shadow-orange-500/20 active:scale-95 disabled:opacity-70 disabled:shadow-none">
                    <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">save</span>
                    <span x-show="isSubmitting"
                        class="animate-spin material-symbols-outlined text-[18px]">progress_activity</span>
                    <span x-text="isSubmitting ? 'جاري الحفظ...' : 'اعتماد الإرسالية'"></span>
                </button>
            </div>
        </div>

        {{-- ================= الفورم (Grid Layout) ================= --}}
        <div class="p-4 mx-auto mt-4 max-w-7xl md:p-6">

            <form id="incomingPackageForm" action="{{ route('shipmentpackage.incoming.store') }}" method="POST"
                @submit="isSubmitting = true" class="grid grid-cols-1 gap-6 items-start lg:grid-cols-12" x-data="{
                            isSubmitting: false,
                            activeItem: 0,

                            {{-- ========== جلب بيانات الدول من الكونفق بشكل كامل (بالأعلام) ========== --}}
                            countries: @js(array_values(config('countries', []))),

                            {{-- ========== Driver Combobox ========== --}}
                            driver_id: '{{ old('driver_id', '') }}',
                            driver_name: '{{ old('driver_name', '') }}',
                            localPhoneNumber: '{{ old('driver_phone') ? preg_replace('/^967/', '', old('driver_phone')) : '' }}',
                            selectedCountry: null,
                            countryOpen: false,
                            countrySearch: '',

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
                            items: @js(old('items', [['bond_number' => '', 'sender_name' => '', 'sender_phone' => '', 'receiver_name' => '', 'receiver_phone' => '', 'package_type' => '', 'item_notes' => '', 'payment_status' => 'unpaid', 'amount' => '']])),
                            errorIndices: @js(collect($errors->keys())->map(fn($key) => preg_match('/^items\.(\d+)/', $key, $m) ? (int) $m[1] : null)->filter(fn($v) => !is_null($v))->unique()->values()),

                            addItem() {
                                this.items.push({ bond_number: '', sender_name: '', sender_phone: '', receiver_name: '', receiver_phone: '', package_type: '', item_notes: '', payment_status: 'unpaid', amount: '' });
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
                        }" x-init="selectedCountry = countries.find(c => c.code === 'YE') || countries[0];">
                @csrf

                {{-- ======================== الجانب الأيمن (البيانات الأساسية) ======================== --}}
                <div class="lg:col-span-5 flex flex-col gap-6 lg:sticky lg:top-[5.5rem] z-30">

                    <div
                        class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden relative">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 rounded-bl-full pointer-events-none bg-primary/5 dark:bg-primary/10">
                        </div>

                        <div
                            class="flex relative z-10 gap-3 items-center px-5 py-4 border-b border-gray-50 md:px-6 dark:border-boxdark-2 bg-surface dark:bg-boxdark">
                            <div
                                class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                                <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                            </div>
                            <h3 class="text-sm font-black md:text-base text-on-surface dark:text-white font-headline">
                                البيانات الأساسية للإرسالية</h3>
                        </div>

                        <div class="relative z-10 p-5 space-y-6 md:p-6">

                            {{-- رقم الإرسالية --}}
                            <div>
                                <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                                    رقم الإرسالية (التتبع) <span class="text-error">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="text" name="tracking_number" value="{{ old('tracking_number') }}" required
                                        placeholder="مثال: PKG-1001"
                                        class="pr-12 pl-4 w-full h-12 font-mono text-sm font-bold tracking-tight rounded-xl border border-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark-2 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:bg-white dark:focus:bg-boxdark text-on-surface dark:text-white">
                                    <div
                                        class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                        <span class="material-symbols-outlined text-[20px]">tag</span>
                                    </div>
                                </div>
                                @error('tracking_number')
                                    <p class="mt-2 text-xs font-bold text-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- اختيار المكتب والفرع المرسل (ديناميكي) --}}
                            <div x-data="{
                                        offices: @js($offices),
                                        selectedOffice: '{{ old('office_id', '') }}',
                                        selectedBranch: '{{ old('sender_office_branch_id', '') }}',
                                        get currentBranches() {
                                            let office = this.offices.find(o => o.id == this.selectedOffice);
                                            return office ? office.branches : [];
                                        }
                                    }" class="space-y-4">

                                {{-- 1. المكتب الخارجي المرسل --}}
                                <div>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                                        المكتب الخارجي المرسل <span class="text-error">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="office_id" x-model="selectedOffice" @change="selectedBranch = ''"
                                            required
                                            class="px-4 pl-10 w-full h-12 text-sm font-bold rounded-xl border border-gray-200 transition-all appearance-none outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark-2 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:bg-white dark:focus:bg-boxdark text-on-surface dark:text-white">
                                            <option value="" disabled>-- اختر المكتب الخارجي --</option>
                                            <template x-for="office in offices" :key="office.id">
                                                <option :value="office.id" x-text="office.name"></option>
                                            </template>
                                        </select>
                                        <span
                                            class="absolute left-3 top-1/2 text-gray-400 -translate-y-1/2 pointer-events-none">
                                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                        </span>
                                    </div>
                                    @error('office_id')
                                        <p class="mt-2 text-xs font-bold text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- 2. الفرع التابع للمكتب --}}
                                <div x-show="selectedOffice" x-transition:enter.duration.300ms x-cloak>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                                        الفرع المرسل <span class="text-error">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="sender_office_branch_id" x-model="selectedBranch" required
                                            class="px-4 pl-10 w-full h-12 text-sm font-bold bg-white rounded-xl border border-gray-200 transition-all appearance-none outline-none dark:bg-boxdark focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-on-surface dark:text-white dark:border-boxdark-2">
                                            <option value="" disabled>-- اختر الفرع --</option>
                                            <template x-for="branch in currentBranches" :key="branch.id">
                                                <option :value="branch.id"
                                                    x-text="branch.name + (branch.city ? ' (' + branch.city + ')' : '')">
                                                </option>
                                            </template>
                                        </select>
                                        <span
                                            class="absolute left-3 top-1/2 text-emerald-500 -translate-y-1/2 pointer-events-none">
                                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                        </span>
                                    </div>
                                    @error('sender_office_branch_id')
                                        <p class="mt-2 text-xs font-bold text-error">
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- بيانات السائق --}}
                            <div class="pt-4 space-y-4 border-t border-gray-50 dark:border-boxdark-2">
                                <h4 class="flex gap-2 items-center text-sm font-bold text-gray-700 dark:text-gray-200">
                                    <span class="material-symbols-outlined text-[18px] text-gray-400">person</span> بيانات
                                    السائق
                                </h4>

                                <input type="hidden" name="driver_id" :value="driver_id">

                                <div>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">رقم هاتف
                                        السائق <span class="text-error">*</span></label>
                                    <input type="hidden" name="driver_phone" :value="fullPhone">

                                    <div class="flex overflow-visible relative items-center w-full h-12 rounded-xl border border-gray-200 transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 focus-within:bg-white dark:focus-within:bg-boxdark"
                                        :class="driver_id ?
                                                    'border-emerald-400 ring-1 ring-emerald-400 bg-emerald-50/30 dark:bg-emerald-500/5' :
                                                    ''">

                                        {{-- Country Button --}}
                                        <button type="button" @click="countryOpen = !countryOpen"
                                            class="flex gap-2 items-center px-3 h-full bg-white rounded-l-xl border-r border-gray-200 transition-colors dark:bg-boxdark dark:border-boxdark-2 hover:bg-gray-50 dark:hover:bg-boxdark-2 shrink-0">
                                            <template x-if="selectedCountry">
                                                <div class="flex gap-1.5 items-center">
                                                    <div class="w-6 h-4 rounded-[2px] shadow-sm overflow-hidden"
                                                        x-html="selectedCountry.svg"></div>
                                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300 dir-ltr"
                                                        x-text="selectedCountry.dial_code"></span>
                                                </div>
                                            </template>
                                            <span
                                                class="material-symbols-outlined text-[18px] text-gray-400">expand_more</span>
                                        </button>

                                        {{-- Input --}}
                                        <input type="tel" x-model="localPhoneNumber" @input="
                                                        localPhoneNumber = localPhoneNumber.replace(/[^0-9]/g, ''); 
                                                        if(selectedCountry?.dial_code === '+967' && localPhoneNumber.length > 9) {
                                                            localPhoneNumber = localPhoneNumber.substring(0, 9);
                                                        } else if (localPhoneNumber.length > 15) {
                                                            localPhoneNumber = localPhoneNumber.substring(0, 15);
                                                        }
                                                        onPhoneInput();
                                                    " :maxlength="selectedCountry?.dial_code === '+967' ? 9 : 15"
                                            @focus="driverOpen = true" @click.outside="driverOpen = false"
                                            placeholder="7XXXXXXXX" dir="ltr" autocomplete="off"
                                            class="flex-grow px-4 w-full min-w-0 h-full text-sm font-bold tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 text-on-surface dark:text-white">

                                        <div x-show="driver_id" class="flex items-center px-2 pointer-events-none">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-emerald-500">check_circle</span>
                                        </div>

                                        {{-- Country Dropdown --}}
                                        <div x-show="countryOpen" @click.outside="countryOpen = false" x-transition x-cloak
                                            class="absolute z-[60] left-0 right-0 mt-2 top-full max-h-60 bg-white dark:bg-boxdark-2 rounded-xl border border-gray-100 dark:border-boxdark shadow-xl custom-scrollbar overflow-hidden">
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                <input type="text" x-model="countrySearch" placeholder="ابحث عن الدولة..."
                                                    class="px-3 py-2 w-full text-xs font-bold rounded-lg border border-gray-200 outline-none bg-surface dark:bg-boxdark dark:border-boxdark-2 focus:border-primary text-on-surface dark:text-white dir-rtl">
                                            </div>
                                            <div class="overflow-y-auto p-1 max-h-40 custom-scrollbar">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button"
                                                        @click="selectedCountry = country; countryOpen = false; countrySearch = ''"
                                                        class="flex justify-between items-center px-3 py-2.5 w-full text-sm text-left rounded-lg transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="flex gap-3 items-center">
                                                            <div class="w-6 h-4 rounded-[2px] shadow-sm overflow-hidden"
                                                                x-html="country.svg"></div>
                                                            <span
                                                                class="text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                                x-text="country.name"></span>
                                                        </div>
                                                        <span class="font-mono text-[10px] font-bold text-gray-400 dir-ltr"
                                                            x-text="country.dial_code"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Driver Dropdown --}}
                                        <div x-show="driverOpen && localPhoneNumber.trim().length > 0 && !driver_id"
                                            x-transition x-cloak @click.outside="driverOpen = false"
                                            class="absolute z-[50] left-0 right-0 mt-2 top-full max-h-56 bg-white dark:bg-boxdark-2 rounded-xl border border-gray-100 dark:border-boxdark shadow-xl overflow-y-auto custom-scrollbar p-1">
                                            <template x-for="driver in filteredDrivers" :key="driver.id">
                                                <button type="button" @click="selectDriver(driver)"
                                                    class="flex justify-between items-center px-4 py-3 w-full text-right rounded-lg border-b border-gray-50 transition-colors dark:border-boxdark hover:bg-surface dark:hover:bg-boxdark last:border-0">
                                                    <span class="text-xs font-black text-on-surface dark:text-white"
                                                        x-text="driver.name"></span>
                                                    <span
                                                        class="font-mono text-[10px] font-bold text-gray-400 dir-ltr text-right"
                                                        x-text="driver.phone || '—'"></span>
                                                </button>
                                            </template>
                                            <div x-show="filteredDrivers.length === 0"
                                                class="flex gap-2 justify-center items-center px-4 py-3 m-1 text-xs font-bold text-gray-500 rounded-lg dark:text-bodydark bg-surface dark:bg-boxdark">
                                                <span
                                                    class="material-symbols-outlined text-[16px] text-primary">person_add</span>
                                                سائق جديد — سيتم تسجيله تلقائياً
                                            </div>
                                        </div>
                                    </div>
                                    @error('driver_phone')
                                        <p class="mt-2 text-xs font-bold text-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">اسم
                                        السائق <span class="text-error">*</span></label>
                                    <input type="text" name="driver_name" x-model="driver_name"
                                        placeholder="أدخل اسم السائق ثلاثياً" :readonly="!!driver_id"
                                        :class="driver_id ?
                                                    'bg-surface dark:bg-boxdark-2 text-gray-500 dark:text-gray-400 cursor-not-allowed opacity-80' :
                                                    'bg-white dark:bg-boxdark focus:bg-white dark:focus:bg-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white'"
                                        class="px-4 w-full h-12 text-sm font-bold rounded-xl border border-gray-200 transition-all outline-none dark:border-boxdark-2">
                                    @error('driver_name')
                                        <p class="mt-2 text-xs font-bold text-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- ملاحظات --}}
                            <div class="pt-4 border-t border-gray-50 dark:border-boxdark-2">
                                <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">ملاحظات على
                                    الإرسالية</label>
                                <textarea name="notes" rows="2" placeholder="ملاحظات عامة حول الإرسالية (اختياري)..."
                                    class="p-4 w-full text-sm font-medium rounded-xl border border-gray-200 transition-all outline-none resize-none bg-surface dark:bg-boxdark-2 dark:border-boxdark-2 focus:bg-white dark:focus:bg-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ======================== الجانب الأيسر (الطرود الديناميكية) ======================== --}}
                <div
                    class="lg:col-span-7 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible relative">

                    {{-- Header --}}
                    <div
                        class="flex justify-between items-center px-5 md:px-6 py-4 border-b border-gray-50 dark:border-boxdark-2 bg-surface dark:bg-boxdark rounded-t-[2rem]">
                        <div class="flex gap-3 items-center">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-orange-500 bg-orange-50 rounded-xl dark:bg-orange-500/10 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-black md:text-base text-on-surface dark:text-white font-headline">
                                    الطرود المرفقة</h3>
                                <p class="text-[11px] font-bold text-gray-500 dark:text-bodydark mt-0.5">
                                    إجمالي الطرود المضافة: <span class="ml-1 font-black text-orange-500"
                                        x-text="items.length"></span>
                                </p>
                            </div>
                        </div>

                        <button type="button" @click="addItem()"
                            class="flex gap-1.5 items-center px-4 h-10 text-sm font-bold text-white bg-orange-500 rounded-xl shadow-md transition-all hover:bg-orange-600 shadow-orange-500/30 active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">add</span>
                            <span class="hidden sm:inline">إضافة طرد</span>
                        </button>
                    </div>

                    {{-- Items Loop (أكورديون) --}}
                    <div class="p-4 md:p-6 space-y-4 bg-surface dark:bg-boxdark-2 rounded-b-[2rem]">
                        <template x-for="(item, index) in items" :key="index">

                            <div :class="errorIndices.includes(index) ? 'border-error shadow-sm' : (activeItem === index ?
                                        'border-primary shadow-md dark:shadow-none dark:border-primary/50' :
                                        'border-gray-200 dark:border-boxdark hover:border-gray-300 dark:hover:border-gray-600'
                                    )"
                                class="overflow-visible relative bg-white rounded-2xl border transition-all duration-300 dark:bg-boxdark">

                                {{-- شريط رأس الأكورديون --}}
                                <div @click="activeItem = activeItem === index ? null : index"
                                    class="flex justify-between items-center p-4 transition-colors cursor-pointer select-none"
                                    :class="activeItem === index ?
                                                'bg-surface dark:bg-boxdark-2 border-b border-gray-100 dark:border-boxdark' : ''">

                                    <div class="flex gap-4 items-center">
                                        <div class="flex justify-center items-center w-8 h-8 text-xs font-black rounded-full transition-colors shrink-0"
                                            :class="activeItem === index ? 'bg-primary text-white shadow-sm shadow-primary/30' :
                                                        'bg-surface dark:bg-boxdark-2 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-boxdark'"
                                            x-text="index + 1">
                                        </div>
                                        <div class="flex flex-col">
                                            <h4 class="text-sm font-black transition-colors font-headline" :class="activeItem === index ? 'text-primary' :
                                                            'text-on-surface dark:text-white'"
                                                x-text="item.bond_number || 'طرد جديد (بدون رقم)'"></h4>
                                            <p class="text-[10px] font-bold mt-0.5" :class="activeItem === index ? 'text-gray-500 dark:text-bodydark' :
                                                            'text-gray-400 dark:text-gray-500'"
                                                x-text="item.receiver_name ? 'للمستلم: ' + item.receiver_name : 'يرجى إكمال بيانات الطرد...'">
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex gap-2 items-center">
                                        {{-- أيقونة حذف الطرد --}}
                                        <button type="button" @click.stop="removeItem(index)"
                                            x-show="activeItem === index && items.length > 1"
                                            class="flex justify-center items-center w-8 h-8 rounded-lg transition-colors text-error hover:bg-rose-50 dark:hover:bg-rose-500/10 active:scale-90"
                                            title="حذف الطرد">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>

                                        {{-- سهم الفتح والإغلاق --}}
                                        <div class="flex justify-center items-center w-8 h-8 rounded-full transition-colors"
                                            :class="activeItem === index ?
                                                        'bg-primary-container dark:bg-primary/10 text-primary' :
                                                        'bg-surface dark:bg-boxdark-2 text-gray-400 border border-gray-100 dark:border-boxdark'">
                                            <span
                                                class="material-symbols-outlined text-[20px] transition-transform duration-300"
                                                :class="activeItem === index ? 'rotate-180' : ''">expand_more</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- محتوى الطرد (يظهر ويختفي بناءً على activeItem) --}}
                                <div x-show="activeItem === index" x-collapse>
                                    <div class="p-5 space-y-6 md:p-6">

                                        {{-- رقم السند --}}
                                        <div>
                                            <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">رقم
                                                الطرد (السند) <span class="text-error">*</span></label>
                                            <input type="text" :name="`items[${index}][bond_number]`"
                                                x-model="item.bond_number" placeholder="رقم السند"
                                                class="px-4 w-full h-12 font-mono text-sm font-bold tracking-tight rounded-xl border border-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark-2 focus:bg-white dark:focus:bg-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white">
                                        </div>

                                        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

                                            {{-- ================= بيانات المرسل (تصميم Premium SaaS) ================= --}}
                                            <div class="rounded-2xl border transition-all duration-300 dark:border-boxdark"
                                                :class="isSenderExpanded 
                ? 'border-primary/30 shadow-[0_8px_30px_-12px_rgba(99,102,241,0.15)] bg-white dark:bg-boxdark' 
                : 'border-gray-100 bg-surface dark:bg-boxdark-2 hover:border-gray-200 dark:hover:border-gray-700'" x-data="{
                isSenderExpanded: false,
                sdOpen: false,
                sdCountryOpen: false,
                sdSearch: '',
                sdLocal: '',
                sdSelected: null,
                sdCustomers: @js($customers),
                get sdFilteredCountries() {
                    if (this.sdSearch === '') return countries;
                    return countries.filter(c => c.name.toLowerCase().includes(this.sdSearch.toLowerCase()) || c.dial_code.includes(this.sdSearch));
                },
                get sdFilteredCustomers() {
                    if (this.sdLocal.trim() === '') return this.sdCustomers;
                    const s = this.sdLocal.trim();
                    return this.sdCustomers.filter(c => c.phone && c.phone.includes(s));
                },
                selectCustomer(customer) {
                    item.sender_name = customer.name;
                    let p = customer.phone || '';
                    const codes = countries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                    for (const code of codes) {
                        let regex = new RegExp('^(\\+|00)?' + code);
                        if (regex.test(p)) {
                            this.sdSelected = countries.find(c => c.dial_code.replace('+', '') === code);
                            p = p.replace(regex, '');
                            break;
                        }
                    }
                    this.sdLocal = p;
                    this.sdOpen = false;
                },
                onPhoneInput() {
                    this.sdOpen = true;
                }
            }" x-init="
                sdSelected = countries.find(c => c.code === 'YE') || countries[0];
                if (item.sender_phone) {
                    let p = item.sender_phone;
                    const codes = countries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                    for (const code of codes) {
                        let regex = new RegExp('^(\\+|00)?' + code);
                        if (regex.test(p)) {
                            sdSelected = countries.find(c => c.dial_code.replace('+', '') === code);
                            p = p.replace(regex, '');
                            break;
                        }
                    }
                    if (!sdLocal && p) sdLocal = p;
                    if(item.sender_name || item.sender_phone) isSenderExpanded = true;
                }
            " x-effect="item.sender_phone = sdLocal.trim() !== '' ? (sdSelected?.dial_code.replace('+', '') || '').trim() + sdLocal.trim() : ''">

                                                {{-- 💡 زر فتح وإغلاق القسم (مساحة نقر كاملة ومريحة) --}}
                                                <button type="button" @click="isSenderExpanded = !isSenderExpanded"
                                                    class="flex justify-between items-center w-full p-4 group outline-none transition-colors rounded-2xl"
                                                    :class="isSenderExpanded ? 'pb-3' : ''">

                                                    <div class="flex gap-3 items-center">
                                                        <div class="flex justify-center items-center w-8 h-8 rounded-full transition-colors"
                                                            :class="isSenderExpanded ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-gray-400 dark:bg-boxdark dark:text-gray-500 group-hover:bg-primary/5 group-hover:text-primary/70'">
                                                            <span
                                                                class="material-symbols-outlined text-[18px]">person</span>
                                                        </div>
                                                        <div class="flex flex-col text-right">
                                                            <h4
                                                                class="text-sm font-black text-gray-700 transition-colors group-hover:text-primary dark:text-gray-200 font-headline">
                                                                بيانات المرسل
                                                            </h4>
                                                            <span
                                                                class="text-[10px] font-bold text-gray-400 dark:text-gray-500 mt-0.5">
                                                                <span
                                                                    x-show="!isSenderExpanded && !sdLocal && !item.sender_name">اضغط
                                                                    هنا للإضافة (اختياري)</span>
                                                                <span
                                                                    x-show="isSenderExpanded || sdLocal || item.sender_name"
                                                                    class="text-primary/80"
                                                                    x-text="item.sender_name || sdLocal ? 'تم إدخال بيانات' : 'جاري الإدخال...'"></span>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="flex justify-center items-center w-8 h-8 text-gray-400 transition-transform duration-300"
                                                        :class="isSenderExpanded ? 'rotate-180 text-primary' : 'group-hover:translate-y-0.5'">
                                                        <span
                                                            class="material-symbols-outlined text-[20px]">expand_more</span>
                                                    </div>
                                                </button>

                                                {{-- 💡 الحقول المخفية التي تظهر عند الفتح --}}
                                                <div x-show="isSenderExpanded" x-collapse>
                                                    <div
                                                        class="p-4 pt-1 space-y-5 border-t border-gray-50 dark:border-boxdark">

                                                        {{-- هاتف المرسل --}}
                                                        <div class="relative">
                                                            <label
                                                                class="block mb-1.5 text-[11px] font-bold text-gray-500 dark:text-gray-400">رقم
                                                                هاتف المرسل</label>
                                                            <input type="hidden" :name="`items[${index}][sender_phone]`"
                                                                :value="item.sender_phone">

                                                            <div
                                                                class="flex overflow-visible items-center w-full h-12 bg-surface dark:bg-boxdark-2 rounded-xl border border-gray-200 transition-all dark:border-boxdark-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 focus-within:bg-white dark:focus-within:bg-boxdark">

                                                                {{-- Country Btn --}}
                                                                <button type="button"
                                                                    @click="sdCountryOpen = !sdCountryOpen; sdOpen = false"
                                                                    class="flex gap-2 items-center px-3 h-full rounded-l-xl border-r border-gray-200 transition-colors bg-gray-50 dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                                                    <template x-if="sdSelected">
                                                                        <div class="flex gap-1.5 items-center">
                                                                            <div class="w-5 h-[14px] rounded-[2px] shadow-sm overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full"
                                                                                x-html="sdSelected.svg"></div>
                                                                            <span
                                                                                class="text-xs font-bold text-gray-600 dark:text-gray-300 dir-ltr"
                                                                                x-text="sdSelected.dial_code"></span>
                                                                        </div>
                                                                    </template>
                                                                    <span
                                                                        class="material-symbols-outlined text-[16px] text-gray-400">arrow_drop_down</span>
                                                                </button>

                                                                {{-- Input --}}
                                                                <input type="tel" x-model="sdLocal" @input="
                                    sdLocal = sdLocal.replace(/[^0-9]/g, ''); 
                                    if(sdSelected?.dial_code === '+967' && sdLocal.length > 9) {
                                        sdLocal = sdLocal.substring(0, 9);
                                    } else if (sdLocal.length > 15) {
                                        sdLocal = sdLocal.substring(0, 15);
                                    }
                                    onPhoneInput();
                                " :maxlength="sdSelected?.dial_code === '+967' ? 9 : 15" @focus="sdOpen = true"
                                                                    @click.outside="sdOpen = false" placeholder="7XXXXXXXX"
                                                                    dir="ltr" autocomplete="off"
                                                                    class="flex-grow px-4 w-full min-w-0 h-full text-sm font-bold tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 text-on-surface dark:text-white placeholder-gray-300 dark:placeholder-gray-600">

                                                                {{-- Country Dropdown --}}
                                                                <div x-show="sdCountryOpen"
                                                                    @click.outside="sdCountryOpen = false" x-transition
                                                                    x-cloak
                                                                    class="absolute z-[60] left-0 right-0 mt-2 top-full max-h-60 bg-white dark:bg-boxdark-2 rounded-xl border border-gray-100 dark:border-boxdark shadow-xl custom-scrollbar overflow-hidden">
                                                                    <div
                                                                        class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                                        <input type="text" x-model="sdSearch"
                                                                            placeholder="ابحث عن الدولة..."
                                                                            class="px-3 py-2 w-full text-xs font-bold rounded-lg border border-gray-200 outline-none bg-surface dark:bg-boxdark dark:border-boxdark-2 focus:border-primary text-on-surface dark:text-white dir-rtl">
                                                                    </div>
                                                                    <div
                                                                        class="overflow-y-auto p-1 max-h-40 custom-scrollbar">
                                                                        <template x-for="country in sdFilteredCountries"
                                                                            :key="country.code">
                                                                            <button type="button"
                                                                                @click="sdSelected = country; sdCountryOpen = false; sdSearch = ''"
                                                                                class="flex justify-between items-center px-3 py-2.5 w-full text-sm text-left rounded-lg transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                                                <div class="flex gap-3 items-center">
                                                                                    <div class="w-6 h-4 rounded-[2px] shadow-sm overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full"
                                                                                        x-html="country.svg"></div>
                                                                                    <span
                                                                                        class="text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                                                        x-text="country.name"></span>
                                                                                </div>
                                                                                <span
                                                                                    class="font-mono text-[10px] font-bold text-gray-400 dir-ltr"
                                                                                    x-text="country.dial_code"></span>
                                                                            </button>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Customers Dropdown --}}
                                                            <div x-show="sdOpen && sdLocal.trim().length > 0" x-transition
                                                                x-cloak dir="rtl" @click.outside="sdOpen = false"
                                                                class="absolute z-[50] left-0 right-0 mt-2 top-full max-h-48 bg-white dark:bg-boxdark rounded-xl border border-gray-100 dark:border-boxdark-2 shadow-xl overflow-y-auto custom-scrollbar p-1">
                                                                <template x-for="customer in sdFilteredCustomers"
                                                                    :key="customer.id">
                                                                    <button type="button" @click="selectCustomer(customer)"
                                                                        class="flex justify-between items-center px-4 py-3 w-full text-right rounded-lg border-b border-gray-50 transition-colors dark:border-boxdark hover:bg-surface dark:hover:bg-boxdark-2 last:border-0 group/btn">
                                                                        <span
                                                                            class="text-xs font-black text-on-surface dark:text-white font-headline group-hover/btn:text-primary transition-colors"
                                                                            x-text="customer.name"></span>
                                                                        <span
                                                                            class="font-mono text-[10px] font-bold text-gray-400 dark:text-gray-500 dir-ltr text-right"
                                                                            x-text="customer.phone || '—'"></span>
                                                                    </button>
                                                                </template>
                                                                <div x-show="sdFilteredCustomers.length === 0"
                                                                    class="flex gap-2 justify-center items-center px-4 py-3 m-1 text-xs font-bold text-gray-500 rounded-lg dark:text-bodydark bg-surface dark:bg-boxdark-2">
                                                                    <span
                                                                        class="material-symbols-outlined text-[18px] text-primary">person_add</span>
                                                                    سيتم تسجيله كعميل جديد
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- اسم المرسل --}}
                                                        <div>
                                                            <label
                                                                class="block mb-1.5 text-[11px] font-bold text-gray-500 dark:text-gray-400">اسم
                                                                المرسل</label>
                                                            <div class="relative group/input">
                                                                <input type="text" :name="`items[${index}][sender_name]`"
                                                                    x-model="item.sender_name"
                                                                    placeholder="أدخل اسم المرسل (اختياري)"
                                                                    class="px-4 pr-10 w-full h-12 text-sm font-bold bg-surface dark:bg-boxdark-2 rounded-xl border border-gray-200 transition-all outline-none dark:border-boxdark-2 focus:bg-white dark:focus:bg-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white placeholder-gray-300 dark:placeholder-gray-600">

                                                                {{-- أيقونة داخلية --}}
                                                                <div
                                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none transition-colors group-focus-within/input:text-primary">
                                                                    <span
                                                                        class="material-symbols-outlined text-[18px]">badge</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ================= بيانات المستلم (ذكي) ================= --}}
                                            <div class="p-4 space-y-4 rounded-2xl border border-gray-100 md:p-5 bg-surface dark:bg-boxdark-2 dark:border-boxdark"
                                                x-data="{
                                                            rcOpen: false,
                                                            rcCountryOpen: false,
                                                            rcSearch: '',
                                                            rcLocal: '',
                                                            rcSelected: null,
                                                            rcCustomers: @js($customers),
                                                            get rcFilteredCountries() {
                                                                if (this.rcSearch === '') return countries;
                                                                return countries.filter(c => c.name.toLowerCase().includes(this.rcSearch.toLowerCase()) || c.dial_code.includes(this.rcSearch));
                                                            },
                                                            get rcFilteredCustomers() {
                                                                if (this.rcLocal.trim() === '') return this.rcCustomers;
                                                                const s = this.rcLocal.trim();
                                                                return this.rcCustomers.filter(c => c.phone && c.phone.includes(s));
                                                            },
                                                            selectCustomer(customer) {
                                                                item.receiver_name = customer.name;
                                                                let p = customer.phone || '';
                                                                const codes = countries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                                for (const code of codes) {
                                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                                    if (regex.test(p)) {
                                                                        this.rcSelected = countries.find(c => c.dial_code.replace('+', '') === code);
                                                                        p = p.replace(regex, '');
                                                                        break;
                                                                    }
                                                                }
                                                                this.rcLocal = p;
                                                                this.rcOpen = false;
                                                            },
                                                            onPhoneInput() {
                                                                this.rcOpen = true;
                                                            }
                                                        }" x-init="
                                                            rcSelected = countries.find(c => c.code === 'YE') || countries[0];
                                                            if (item.receiver_phone) {
                                                                let p = item.receiver_phone;
                                                                const codes = countries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                                for (const code of codes) {
                                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                                    if (regex.test(p)) {
                                                                        rcSelected = countries.find(c => c.dial_code.replace('+', '') === code);
                                                                        p = p.replace(regex, '');
                                                                        break;
                                                                    }
                                                                }
                                                                if (!rcLocal && p) rcLocal = p;
                                                            }
                                                        "
                                                x-effect="item.receiver_phone = (rcSelected?.dial_code.replace('+', '') || '') + rcLocal">

                                                <h4
                                                    class="pb-2 text-sm font-black text-gray-700 border-b border-gray-200 dark:text-gray-200 dark:border-boxdark font-headline">
                                                    بيانات المستلم</h4>

                                                {{-- هاتف المستلم --}}
                                                <div class="relative">
                                                    <label
                                                        class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">رقم
                                                        الهاتف <span class="text-error">*</span></label>
                                                    <input type="hidden" :name="`items[${index}][receiver_phone]`"
                                                        :value="item.receiver_phone">

                                                    <div
                                                        class="flex overflow-visible items-center w-full h-12 bg-white rounded-xl border border-gray-200 transition-all dark:bg-boxdark dark:border-boxdark-2 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/30">

                                                        {{-- Country Btn --}}
                                                        <button type="button"
                                                            @click="rcCountryOpen = !rcCountryOpen; rcOpen = false"
                                                            class="flex gap-2 items-center px-3 h-full rounded-l-xl border-r border-gray-200 transition-colors outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                                            <template x-if="rcSelected">
                                                                <div class="flex gap-1.5 items-center">
                                                                    <div class="w-6 h-4 rounded-[2px] shadow-sm overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full"
                                                                        x-html="rcSelected.svg"></div>
                                                                    <span
                                                                        class="text-xs font-bold text-gray-600 dark:text-gray-300 dir-ltr"
                                                                        x-text="rcSelected.dial_code"></span>
                                                                </div>
                                                            </template>
                                                            <span
                                                                class="material-symbols-outlined text-[18px] text-gray-400">expand_more</span>
                                                        </button>

                                                        {{-- Input --}}
                                                        <input type="tel" x-model="rcLocal" @input="
                                                                        rcLocal = rcLocal.replace(/[^0-9]/g, ''); 
                                                                        if(rcSelected?.dial_code === '+967' && rcLocal.length > 9) {
                                                                            rcLocal = rcLocal.substring(0, 9);
                                                                        } else if (rcLocal.length > 15) {
                                                                            rcLocal = rcLocal.substring(0, 15);
                                                                        }
                                                                        onPhoneInput();
                                                                    "
                                                            :maxlength="rcSelected?.dial_code === '+967' ? 9 : 15"
                                                            @focus="rcOpen = true" @click.outside="rcOpen = false"
                                                            placeholder="7XXXXXXXX" dir="ltr" autocomplete="off" required
                                                            class="flex-grow px-4 w-full min-w-0 h-full text-sm font-bold tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 text-on-surface dark:text-white">
                                                    </div>

                                                    {{-- Country Dropdown --}}
                                                    <div x-show="rcCountryOpen" @click.outside="rcCountryOpen = false"
                                                        x-transition x-cloak dir="rtl"
                                                        class="absolute z-[60] left-0 right-0 mt-2 top-full max-h-60 bg-white dark:bg-boxdark-2 rounded-xl border border-gray-100 dark:border-boxdark shadow-xl custom-scrollbar overflow-hidden">
                                                        <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                            <input type="text" x-model="rcSearch"
                                                                placeholder="ابحث عن الدولة..."
                                                                class="px-3 py-2 w-full text-xs font-bold rounded-lg border border-gray-200 outline-none bg-surface dark:bg-boxdark dark:border-boxdark-2 focus:border-emerald-500 text-on-surface dark:text-white dir-rtl">
                                                        </div>
                                                        <div class="overflow-y-auto p-1 max-h-40 custom-scrollbar">
                                                            <template x-for="country in rcFilteredCountries"
                                                                :key="country.code">
                                                                <button type="button"
                                                                    @click="rcSelected = country; rcCountryOpen = false; rcSearch = ''"
                                                                    class="flex justify-between items-center px-3 py-2.5 w-full text-sm text-left rounded-lg transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                                    <div class="flex gap-3 items-center">
                                                                        <div class="w-6 h-4 rounded-[2px] shadow-sm overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full"
                                                                            x-html="country.svg"></div>
                                                                        <span
                                                                            class="text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                                            x-text="country.name"></span>
                                                                    </div>
                                                                    <span
                                                                        class="font-mono text-[10px] font-bold text-gray-400 dir-ltr"
                                                                        x-text="country.dial_code"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div x-show="rcOpen && rcLocal.trim().length > 0" x-transition x-cloak
                                                        dir="rtl" @click.outside="rcOpen = false"
                                                        class="absolute z-[60] left-0 right-0 mt-2 top-full max-h-48 bg-white dark:bg-boxdark rounded-xl border border-gray-100 dark:border-boxdark-2 shadow-xl overflow-y-auto custom-scrollbar p-1">
                                                        <template x-for="customer in rcFilteredCustomers"
                                                            :key="customer.id">
                                                            <button type="button" @click="selectCustomer(customer)"
                                                                class="flex justify-between items-center px-4 py-3 w-full text-right rounded-lg border-b border-gray-50 transition-colors dark:border-boxdark hover:bg-surface dark:hover:bg-boxdark-2 last:border-0">
                                                                <span
                                                                    class="text-xs font-black text-on-surface dark:text-white font-headline"
                                                                    x-text="customer.name"></span>
                                                                <span
                                                                    class="font-mono text-[10px] font-bold text-gray-400 dark:text-gray-500 dir-ltr text-right"
                                                                    x-text="customer.phone || '—'"></span>
                                                            </button>
                                                        </template>
                                                        <div x-show="rcFilteredCustomers.length === 0"
                                                            class="flex gap-2 justify-center items-center px-4 py-3 m-1 text-xs font-bold text-gray-500 rounded-lg dark:text-bodydark bg-surface dark:bg-boxdark-2">
                                                            <span
                                                                class="material-symbols-outlined text-[18px] text-emerald-500">person_add</span>
                                                            عميل جديد
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- اسم المستلم --}}
                                                <div>
                                                    <label
                                                        class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">الاسم
                                                        <span class="text-error">*</span></label>
                                                    <input type="text" :name="`items[${index}][receiver_name]`"
                                                        x-model="item.receiver_name" placeholder="اسم المستلم" required
                                                        class="px-4 w-full h-12 text-sm font-bold bg-white rounded-xl border border-gray-200 transition-all outline-none dark:bg-boxdark dark:border-boxdark-2 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-on-surface dark:text-white">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- ================= حالة الدفع وتفاصيل الطرد (Grid Layout) ================= --}}
                                        <div
                                            class="grid grid-cols-1 gap-6 pt-6 border-t border-gray-100 md:grid-cols-12 dark:border-boxdark-2">

                                            {{-- معلومات الطرد (اليمين - 7 أعمدة) --}}
                                            <div class="space-y-4 md:col-span-7">
                                                <div class="grid grid-cols-2 gap-4">
                                                    {{-- حقل النوع (Combobox - اختيار أو كتابة حرة داخل مصفوفة) --}}
                                                    <div class="flex flex-col gap-2" x-data="{
                                                                isOpen: false,
                                                                options: ['كرتون', 'كيس']
                                                            }">
                                                        <label class="text-[11px] font-bold text-slate-400">
                                                            نوع المحتوى <span class="text-error">*</span>
                                                        </label>

                                                        <div class="relative">
                                                            {{-- حقل الإدخال النصي الديناميكي --}}
                                                            <input type="text" :name="`items[${index}][package_type]`"
                                                                x-model="item.package_type" @focus="isOpen = true"
                                                                @click.away="isOpen = false"
                                                                placeholder="اختر أو اكتب النوع..." autocomplete="off"
                                                                class="px-4 w-full h-12 text-sm font-bold rounded-xl border border-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark-2 focus:bg-white dark:focus:bg-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white">

                                                            {{-- أيقونة السهم الجمالية --}}
                                                            <div
                                                                class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                                                <span
                                                                    class="material-symbols-outlined text-[20px] transition-transform duration-300"
                                                                    :class="isOpen ? 'rotate-180' : ''">expand_more</span>
                                                            </div>

                                                            {{-- القائمة المنسدلة الذكية --}}
                                                            <div x-show="isOpen" x-cloak x-transition
                                                                class="absolute z-[60] top-full mt-1.5 right-0 w-full bg-white dark:bg-boxdark rounded-xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] border border-slate-100 dark:border-boxdark-2 overflow-hidden">
                                                                <template x-for="option in options">
                                                                    <button type="button"
                                                                        @click="item.package_type = option; isOpen = false"
                                                                        class="flex justify-between items-center px-4 py-3 w-full text-sm font-bold text-right border-b transition-colors text-slate-700 dark:text-gray-300 hover:bg-primary/5 hover:text-primary dark:hover:bg-boxdark-2 border-slate-50 dark:border-boxdark-2 last:border-none">
                                                                        <span x-text="option"></span>

                                                                        {{-- علامة الصح تظهر فقط إذا كان الخيار مطابقاً
                                                                        للقيمة الحالية --}}
                                                                        <span x-show="item.package_type === option"
                                                                            class="material-symbols-outlined text-[18px] text-primary">check</span>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">ملاحظات
                                                            الطرد (إن وجدت)</label>
                                                        <input type="text" :name="`items[${index}][item_notes]`"
                                                            x-model="item.item_notes" placeholder="..."
                                                            class="px-4 w-full h-12 text-sm font-bold rounded-xl border border-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark-2 focus:bg-white dark:focus:bg-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- معلومات الدفع (اليسار - 5 أعمدة) --}}
                                            <div
                                                class="p-5 space-y-4 rounded-2xl border border-gray-100 md:col-span-5 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                                <div>
                                                    <label
                                                        class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">حالة
                                                        الدفع <span class="text-error">*</span></label>
                                                    <div
                                                        class="flex p-1 bg-gray-200/50 dark:bg-boxdark rounded-[14px] border border-gray-200 dark:border-boxdark-2">
                                                        <label class="flex-1 cursor-pointer">
                                                            <input type="radio" value="unpaid"
                                                                :name="`items[${index}][payment_status]`"
                                                                x-model="item.payment_status" class="sr-only peer">
                                                            <div
                                                                class="flex justify-center items-center w-full h-10 text-xs font-bold text-gray-500 rounded-xl transition-all dark:text-gray-400 peer-checked:bg-white dark:peer-checked:bg-boxdark-2 peer-checked:text-primary dark:peer-checked:text-white peer-checked:shadow-sm">
                                                                عند الاستلام
                                                            </div>
                                                        </label>
                                                        <label class="flex-1 cursor-pointer">
                                                            <input type="radio" value="paid"
                                                                :name="`items[${index}][payment_status]`"
                                                                x-model="item.payment_status" @change="item.amount = 0"
                                                                class="sr-only peer">
                                                            <div
                                                                class="flex justify-center items-center w-full h-10 text-xs font-bold text-gray-500 rounded-xl transition-all dark:text-gray-400 peer-checked:bg-white dark:peer-checked:bg-boxdark-2 peer-checked:text-emerald-500 dark:peer-checked:text-emerald-400 peer-checked:shadow-sm">
                                                                مدفوع
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label
                                                        class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">المبلغ
                                                        (ريال) <span class="text-error">*</span></label>
                                                    <input type="number" :name="`items[${index}][amount]`"
                                                        x-model="item.amount" :readonly="item.payment_status === 'paid'"
                                                        :class="item.payment_status === 'paid' ?
                                                                    'bg-gray-100 dark:bg-boxdark-2 text-gray-400 border-gray-200 dark:border-boxdark cursor-not-allowed opacity-70' :
                                                                    'bg-white dark:bg-boxdark border-gray-200 dark:border-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-primary dark:text-white'"
                                                        placeholder="0" min="0" step="1"
                                                        class="px-4 w-full h-12 font-mono text-lg font-black rounded-xl border transition-all outline-none">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty State (إذا تم حذف كل الطرود) --}}
                        <div x-show="items.length === 0"
                            class="flex flex-col justify-center items-center py-16 bg-surface dark:bg-boxdark-2 rounded-[2rem] border-2 border-gray-200 dark:border-boxdark border-dashed">
                            <div
                                class="flex justify-center items-center mb-4 w-20 h-20 text-gray-300 bg-white rounded-full shadow-sm dark:bg-boxdark dark:text-gray-600">
                                <span class="text-[40px] material-symbols-outlined">inventory_2</span>
                            </div>
                            <p class="mb-4 text-sm font-bold text-gray-500 dark:text-bodydark font-headline">لا توجد طرود
                                مسجلة حالياً في هذه الإرسالية</p>
                            <button type="button" @click="addItem()"
                                class="flex gap-2 items-center px-6 py-3 text-xs font-bold rounded-xl shadow-sm transition-all text-primary bg-primary-container dark:bg-primary/10 hover:bg-primary/20 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">add</span> إضافة طرد جديد
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ================= زر الإرسال للموبايل (Sticky Bottom) ================= --}}
                {{-- زر الاعتماد للشاشات الصغيرة (مدمج في نهاية الصفحة وليس عائماً) --}}
                <div
                    class="md:hidden mt-1 mb-1 p-4 bg-white dark:bg-boxdark-2 border border-gray-100 dark:border-boxdark rounded-[2rem] shadow-sm">
                    <button type="submit"
                        @click="if(items.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                        :disabled="items.length === 0 || isSubmitting"
                        class="flex gap-3 justify-center items-center w-full h-14 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/30 disabled:bg-surface disabled:dark:bg-boxdark disabled:text-gray-400 disabled:shadow-none active:scale-95">

                        <template x-if="isSubmitting">
                            <div class="flex gap-2 items-center">
                                <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                <span>جاري الحفظ...</span>
                            </div>
                        </template>

                        <template x-if="!isSubmitting">
                            <div class="flex gap-2 items-center">
                                <span class="material-symbols-outlined text-[20px]">done_all</span>
                                <span>اعتماد الإرسالية</span>
                                <div class="flex justify-center items-center mr-2 w-7 h-7 rounded-lg bg-white/20">
                                    <span class="text-[12px] font-mono" x-text="items.length">0</span>
                                </div>
                            </div>
                        </template>

                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection