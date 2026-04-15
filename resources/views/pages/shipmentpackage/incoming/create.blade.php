@extends('layouts.app')

@section('title', 'استلام إرسالية جديدة')

@section('content')
    <div class="flex flex-col gap-5 px-4 pt-4 pb-24 min-h-screen bg-slate-50/50">

        {{-- ================= الهيدر ================= --}}
        <div class="flex justify-between items-center mb-2">
            <div class="flex gap-3 items-center">
                <a href="{{ route('shipmentpackage.incoming.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-xl font-black font-headline text-slate-800">استلام إرسالية</h1>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">تسجيل إرسالية واردة جديدة</p>
                </div>
            </div>
        </div>

        {{-- ================= الفورم ================= --}}
        {{-- 💡 لاحظ إضافة activeItem: 0 للتحكم بالأكورديون --}}
        <form action="{{ route('shipmentpackage.incoming.store') }}" method="POST" class="space-y-5" dir="rtl" @submit="isSubmitting = true" 
            x-data="{
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
                items: @js(old('items', [['bond_number' => '', 'sender_name' => '', 'sender_phone' => '', 'receiver_name' => '', 'receiver_phone' => '', 'package_type' => 'carton', 'item_notes' => '', 'payment_status' => 'unpaid', 'amount' => '']])),
                errorIndices: @js(collect($errors->keys())->map(fn($key) => preg_match('/^items\.(\d+)/', $key, $m) ? (int) $m[1] : null)->filter(fn($v) => !is_null($v))->unique()->values()),

                addItem() {
                    this.items.push({ bond_number: '', sender_name: '', sender_phone: '', receiver_name: '', receiver_phone: '', package_type: 'carton', item_notes: '', payment_status: 'unpaid', amount: '' });
                    this.activeItem = this.items.length - 1; // 💡 فتح الطرد الجديد تلقائياً
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        // إذا حذفنا الطرد المفتوح، نفتح الذي قبله
                        if (this.activeItem === index) {
                            this.activeItem = Math.max(0, index - 1);
                        } else if (this.activeItem > index) {
                            this.activeItem--;
                        }
                    }
                }
            }">
            @csrf

            {{-- ======================== القسم الأول: البيانات الأساسية ======================== --}}
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] overflow-hidden">
                <div class="flex gap-3 items-center px-5 py-4 border-b border-slate-50 bg-slate-50/50">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary/10 text-primary shrink-0">
                        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 font-headline">البيانات الأساسية للإرسالية</h3>
                </div>

                <div class="p-5 space-y-4">
                    {{-- رقم الإرسالية --}}
                    <div>
                        <label class="block mb-1.5 text-[11px] font-bold text-slate-500">
                            رقم الإرسالية (التتبع) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <input type="text" name="tracking_number" value="{{ old('tracking_number') }}" required
                                placeholder="مثال: PKG-1001"
                                class="pr-11 pl-4 w-full h-12 text-sm font-bold rounded-xl border transition-all outline-none bg-slate-50/50 border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:bg-white text-slate-700">
                            <div class="flex absolute inset-y-0 right-0 items-center pr-3.5 transition-colors pointer-events-none text-slate-400 group-focus-within:text-primary">
                                <span class="material-symbols-outlined text-[20px]">tag</span>
                            </div>
                        </div>
                        @error('tracking_number') <p class="mt-1 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- ================= اختيار المكتب والفرع المرسل (ديناميكي) ================= --}}
                    <div x-data="{
                            offices: @js($offices),
                            selectedOffice: '{{ old('office_id', '') }}',
                            selectedBranch: '{{ old('sender_office_branch_id', '') }}',
                            get currentBranches() {
                                let office = this.offices.find(o => o.id == this.selectedOffice);
                                return office ? office.branches : [];
                            }
                        }" class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        {{-- 1. اختيار المكتب الرئيسي --}}
                        <div>
                            <label class="block mb-1.5 text-[11px] font-bold text-slate-500">
                                المكتب الخارجي المرسل <span class="text-rose-500">*</span>
                            </label>
                            <select name="office_id" x-model="selectedOffice" @change="selectedBranch = ''" required
                                class="px-4 w-full h-12 text-sm font-bold rounded-xl border transition-all appearance-none outline-none bg-slate-50/50 border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:bg-white text-slate-700">
                                <option value="" disabled>-- اختر المكتب الخارجي --</option>
                                <template x-for="office in offices" :key="office.id">
                                    <option :value="office.id" x-text="office.name"></option>
                                </template>
                            </select>
                            @error('office_id') <p class="mt-1 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- 2. اختيار الفرع التابع للمكتب --}}
                        <div>
                            <label class="block mb-1.5 text-[11px] font-bold text-slate-500">
                                الفرع المرسل <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="sender_office_branch_id" x-model="selectedBranch" required
                                    :disabled="!selectedOffice"
                                    :class="!selectedOffice ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed' : 'bg-slate-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700 border-slate-200'"
                                    class="px-4 w-full h-12 text-sm font-bold rounded-xl border transition-all appearance-none outline-none">
                                    <option value="" disabled>-- اختر الفرع --</option>
                                    <template x-for="branch in currentBranches" :key="branch.id">
                                        <option :value="branch.id" x-text="branch.name + (branch.city ? ' (' + branch.city + ')' : '')"></option>
                                    </template>
                                </select>

                                {{-- أيقونة القفل تظهر عندما يكون الحقل معطلاً --}}
                                <div x-show="!selectedOffice" class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[18px]">lock</span>
                                </div>
                            </div>
                            @error('sender_office_branch_id') <p class="mt-1 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- السائق (Combobox المحسّن) --}}
                    <div class="p-4 space-y-4 rounded-2xl border bg-slate-50 border-slate-100">
                        <div class="flex gap-2 items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">بيانات السائق</span>
                        </div>

                        <input type="hidden" name="driver_id" :value="driver_id">

                        <div>
                            <label class="block mb-1.5 text-[11px] font-bold text-slate-500">رقم هاتف السائق <span class="text-rose-500">*</span></label>
                            <input type="hidden" name="driver_phone" :value="fullPhone">

                            <div class="flex overflow-visible relative items-center w-full h-12 bg-white rounded-xl border transition-all border-slate-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20"
                                :class="driver_id ? 'border-emerald-400 ring-1 ring-emerald-400 bg-emerald-50/30' : ''">

                                {{-- الإدخال (على اليمين) --}}
                                <input type="tel" x-model="localPhoneNumber" @input="onPhoneInput()"
                                    @focus="driverOpen = true" @click.outside="driverOpen = false" placeholder="7XXXXXXXX" dir="ltr" autocomplete="off"
                                    class="flex-grow px-3 min-w-0 h-full text-sm font-bold tracking-wider text-left bg-transparent border-none outline-none sm:px-4 focus:ring-0 text-slate-700">

                                {{-- علامة صح --}}
                                <div x-show="driver_id" class="flex items-center px-1 pointer-events-none">
                                    <span class="material-symbols-outlined text-[18px] text-emerald-500">check_circle</span>
                                </div>

                                {{-- زر الدولة (على اليسار) --}}
                                <button type="button" @click="countryOpen = !countryOpen"
                                    class="flex gap-1.5 items-center px-2 h-full rounded-l-xl border-r transition-colors sm:px-3 bg-slate-50 border-slate-200 hover:bg-slate-100 shrink-0">
                                    <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`" class="w-4 sm:w-5 h-auto rounded-[2px] shadow-sm">
                                    <span class="text-[11px] sm:text-xs font-bold text-slate-600" dir="ltr" x-text="selectedCountry.dial_code"></span>
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">expand_more</span>
                                </button>

                                {{-- قائمة الدول --}}
                                <div x-show="countryOpen" @click.outside="countryOpen = false" x-transition x-cloak
                                    class="absolute z-[60] left-0 right-0 mt-2 top-full max-h-60 bg-white rounded-2xl border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.2)] custom-scrollbar overflow-hidden">
                                    <div class="p-2 border-b border-slate-50">
                                        <input type="text" x-model="countrySearch" placeholder="ابحث عن الدولة..."
                                            class="px-3 py-2 w-full text-xs font-bold rounded-xl border outline-none bg-slate-50 border-slate-200 focus:border-primary text-slate-700">
                                    </div>
                                    <div class="overflow-y-auto p-1 max-h-40 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <button type="button" @click="selectedCountry = country; countryOpen = false; countrySearch = ''" 
                                                class="flex justify-between items-center px-3 py-2.5 w-full text-sm text-left rounded-lg transition-colors hover:bg-primary/5">
                                                <div class="flex gap-3 items-center">
                                                    <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`" class="w-5 h-auto rounded-[2px] shadow-sm">
                                                    <span class="text-xs font-bold text-slate-700" x-text="country.name"></span>
                                                </div>
                                                <span class="font-mono text-[10px] font-bold text-slate-400 dir-ltr" x-text="country.dial_code"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                {{-- قائمة السائقين المقترحين --}}
                                <div x-show="driverOpen && localPhoneNumber.trim().length > 0 && !driver_id" x-transition x-cloak @click.outside="driverOpen = false"
                                    class="absolute z-[50] left-0 right-0 mt-2 top-full max-h-56 bg-white rounded-2xl border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.2)] overflow-y-auto custom-scrollbar p-1">
                                    <template x-for="driver in filteredDrivers" :key="driver.id">
                                        <button type="button" @click="selectDriver(driver)"
                                            class="flex justify-between items-center px-4 py-3 w-full text-right rounded-lg border-b transition-colors border-slate-50 hover:bg-primary/5 last:border-0">
                                            <span class="text-xs font-black text-slate-800" x-text="driver.name"></span>
                                            <span class="font-mono text-[10px] font-bold text-slate-400 dir-ltr" x-text="driver.phone || '—'"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredDrivers.length === 0" class="flex gap-2 justify-center items-center px-4 py-4 m-1 text-xs font-bold rounded-xl text-slate-500 bg-slate-50">
                                        <span class="material-symbols-outlined text-[16px] text-primary">person_add</span>
                                        سائق جديد — سيتم تسجيله تلقائياً
                                    </div>
                                </div>
                            </div>
                            @error('driver_phone') <p class="mt-1 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block mb-1.5 text-[11px] font-bold text-slate-500">اسم السائق <span class="text-rose-500">*</span></label>
                            <input type="text" name="driver_name" x-model="driver_name"
                                placeholder="أدخل اسم السائق ثلاثياً" :readonly="!!driver_id"
                                :class="driver_id ? 'bg-slate-100/50 text-slate-400 border-slate-200 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700'"
                                class="px-4 w-full h-12 text-sm font-bold rounded-xl transition-all outline-none">
                            @error('driver_name') <p class="mt-1 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- ملاحظات عامة --}}
                    <div>
                        <label class="block mb-1.5 text-[11px] font-bold text-slate-500">ملاحظات على الإرسالية</label>
                        <textarea name="notes" rows="2" placeholder="اختياري..."
                            class="p-4 w-full text-sm font-bold rounded-xl border transition-all outline-none resize-none bg-slate-50/50 border-slate-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ======================== القسم الثاني: الطرود الديناميكية (أكورديون) ======================== --}}
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] overflow-visible relative">

                {{-- Header --}}
                <div class="flex justify-between items-center px-5 py-4 border-b border-slate-50 bg-slate-50/50 rounded-t-[2rem]">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-secondary/10 text-secondary shrink-0">
                            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 font-headline">الطرود المرفقة</h3>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5">
                                العدد الإجمالي: <span class="ml-1 font-black text-primary" x-text="items.length"></span>
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="addItem()"
                        class="flex justify-center items-center w-10 h-10 text-white rounded-xl shadow-sm transition-all bg-primary shadow-primary/30 active:scale-90 shrink-0">
                        <span class="material-symbols-outlined text-[24px]">add</span>
                    </button>
                </div>

                {{-- Items Loop (نظام الأكورديون) --}}
                <div class="p-5 space-y-4">
                    <template x-for="(item, index) in items" :key="index">

                        <div :class="errorIndices.includes(index) ? 'border-rose-300 shadow-sm' : (activeItem === index ? 'border-primary/40 shadow-[0_8px_30px_-10px_rgba(var(--color-primary-rgb),0.3)]' : 'border-slate-100 hover:border-slate-200')"
                            class="relative bg-white rounded-[1.5rem] border transition-all duration-300 overflow-hidden">

                            {{-- شريط رأس الأكورديون --}}
                            <div @click="activeItem = activeItem === index ? null : index"
                                class="flex justify-between items-center p-4 transition-colors cursor-pointer select-none"
                                :class="activeItem === index ? 'bg-slate-50/50 border-b border-slate-100' : ''">
                                
                                <div class="flex gap-3 items-center">
                                    <div class="flex justify-center items-center w-8 h-8 text-xs font-black rounded-full transition-colors"
                                         :class="activeItem === index ? 'bg-primary text-white' : 'bg-slate-100 text-slate-500'" x-text="index + 1">
                                    </div>
                                    <div class="flex flex-col">
                                        <h4 class="text-sm font-black transition-colors" 
                                            :class="activeItem === index ? 'text-primary' : 'text-slate-800'"
                                            x-text="item.bond_number || 'طرد جديد (بدون رقم)'"></h4>
                                        <p class="text-[10px] font-bold mt-0.5"
                                           :class="activeItem === index ? 'text-slate-500' : 'text-slate-400'"
                                           x-text="item.receiver_name ? 'للمستلم: ' + item.receiver_name : 'يرجى إكمال البيانات...'"></p>
                                    </div>
                                </div>

                                <div class="flex gap-2 items-center">
                                    {{-- أيقونة حذف الطرد (تظهر فقط إذا كان مفتوحاً وهناك أكثر من طرد) --}}
                                    <button type="button" @click.stop="removeItem(index)" x-show="activeItem === index && items.length > 1"
                                        class="flex justify-center items-center w-8 h-8 text-rose-400 rounded-lg transition-colors hover:bg-rose-50 active:scale-90">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    
                                    {{-- سهم الفتح والإغلاق --}}
                                    <div class="flex justify-center items-center w-8 h-8 rounded-full transition-colors"
                                         :class="activeItem === index ? 'bg-primary/10 text-primary' : 'bg-slate-50 text-slate-400'">
                                        <span class="material-symbols-outlined text-[20px] transition-transform duration-300"
                                              :class="activeItem === index ? 'rotate-180' : ''">expand_more</span>
                                    </div>
                                </div>
                            </div>

                            {{-- محتوى الطرد (يظهر ويختفي بناءً على activeItem) --}}
                            <div x-show="activeItem === index" x-collapse>
                                <div class="p-5 space-y-5">
                                    
                                    {{-- رقم السند --}}
                                    <div>
                                        <label class="block mb-1.5 text-[10px] font-bold text-slate-500">رقم البوليصة (السند) <span class="text-rose-500">*</span></label>
                                        <input type="text" :name="`items[${index}][bond_number]`" x-model="item.bond_number" placeholder="رقم السند"
                                            class="px-3 w-full h-11 font-mono text-xs font-bold rounded-xl border outline-none bg-slate-50/50 border-slate-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">
                                    </div>

                                    {{-- ================= بيانات المرسل (ذكي) ================= --}}
                                    <div class="p-3 space-y-3 rounded-xl border bg-slate-50/80 border-slate-100" x-data="{
                                            sdOpen: false, sdSearch: '', sdLocal: '', sdSelected: null,
                                            sdCustomers: @js($customers),
                                            sdCountries: [
                                                { name: 'اليمن', code: 'YE', dial_code: '+967' },
                                                { name: 'السعودية', code: 'SA', dial_code: '+966' }
                                            ],
                                            get sdFilteredCountries() {
                                                if (this.sdSearch === '') return this.sdCountries;
                                                return this.sdCountries.filter(c => c.name.toLowerCase().includes(this.sdSearch.toLowerCase()) || c.dial_code.includes(this.sdSearch));
                                            },
                                            get sdFilteredCustomers() {
                                                if (this.sdLocal.trim() === '') return this.sdCustomers;
                                                const s = this.sdLocal.trim();
                                                return this.sdCustomers.filter(c => c.phone && c.phone.includes(s));
                                            },
                                            selectCustomer(customer) {
                                                item.sender_name = customer.name;
                                                let p = customer.phone || '';
                                                const codes = this.sdCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                for (const code of codes) {
                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                    if (regex.test(p)) {
                                                        this.sdSelected = this.sdCountries.find(c => c.dial_code.replace('+', '') === code);
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
                                            sdSelected = sdCountries.find(c => c.code === 'YE') || sdCountries[0];
                                            if (item.sender_phone) {
                                                let p = item.sender_phone;
                                                const codes = sdCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                for (const code of codes) {
                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                    if (regex.test(p)) {
                                                        sdSelected = sdCountries.find(c => c.dial_code.replace('+', '') === code);
                                                        sdLocal = p.replace(regex, '');
                                                        break;
                                                    }
                                                }
                                                if (!sdLocal && p) sdLocal = p;
                                            }
                                        " x-effect="item.sender_phone = (sdSelected?.dial_code.replace('+', '') || '') + sdLocal">

                                        {{-- هاتف المرسل --}}
                                        <div class="relative">
                                            <label class="block mb-1.5 text-[10px] font-bold text-slate-500">رقم هاتف المرسل</label>
                                            <input type="hidden" :name="`items[${index}][sender_phone]`" :value="item.sender_phone">

                                            <div class="flex overflow-visible items-center w-full h-11 bg-white rounded-lg border border-slate-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                                                
                                                {{-- الإدخال (على اليمين) --}}
                                                <input type="tel" x-model="sdLocal" @input="onPhoneInput()" @focus="sdOpen = true" @click.outside="sdOpen = false" placeholder="7XXXXXXXX" dir="ltr" autocomplete="off"
                                                    class="flex-grow px-3 w-full min-w-0 h-full text-xs font-bold tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 text-slate-700">

                                                {{-- زر الدولة (على اليسار) --}}
                                                <button type="button" @click="sdOpen = !sdOpen" class="flex gap-1.5 items-center px-2.5 h-full rounded-l-lg border-r transition-colors bg-slate-50 border-slate-200 shrink-0 hover:bg-slate-100">
                                                    <template x-if="sdSelected">
                                                        <div class="flex gap-1.5 items-center">
                                                            <img :src="`https://flagcdn.com/w20/${sdSelected.code.toLowerCase()}.png`" class="w-4 h-auto rounded-[2px] shadow-sm">
                                                            <span class="text-[10px] font-bold text-slate-600" dir="ltr" x-text="sdSelected.dial_code"></span>
                                                        </div>
                                                    </template>
                                                </button>
                                            </div>

                                            {{-- قائمة العملاء المقترحين للمرسل --}}
                                            <div x-show="sdOpen && sdLocal.trim().length > 0" x-transition x-cloak
                                                class="absolute z-[60] left-0 right-0 mt-1 top-full max-h-48 bg-white rounded-xl border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.2)] overflow-y-auto custom-scrollbar p-1">
                                                <template x-for="customer in sdFilteredCustomers" :key="customer.id">
                                                    <button type="button" @click="selectCustomer(customer)"
                                                        class="flex justify-between items-center px-3 py-2.5 w-full text-right rounded-lg border-b transition-colors border-slate-50 hover:bg-primary/5 last:border-0">
                                                        <span class="text-[11px] font-black text-slate-800" x-text="customer.name"></span>
                                                        <span class="font-mono text-[10px] font-bold text-slate-400 dir-ltr" x-text="customer.phone || '—'"></span>
                                                    </button>
                                                </template>
                                                <div x-show="sdFilteredCustomers.length === 0" class="flex gap-1.5 items-center justify-center px-3 py-3 text-[10px] font-bold text-slate-500 bg-slate-50 rounded-lg m-1">
                                                    <span class="material-symbols-outlined text-[14px] text-primary">person_add</span>
                                                    عميل جديد — سيتم تسجيله تلقائياً
                                                </div>
                                            </div>
                                        </div>

                                        {{-- اسم المرسل --}}
                                        <div>
                                            <label class="block mb-1.5 text-[10px] font-bold text-slate-500">اسم المرسل</label>
                                            <input type="text" :name="`items[${index}][sender_name]`" x-model="item.sender_name" placeholder="اختياري"
                                                class="px-3 w-full h-11 text-xs font-bold bg-white rounded-lg border outline-none border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">
                                        </div>
                                    </div>

                                    {{-- ================= بيانات المستلم (ذكي) ================= --}}
                                    <div class="p-3 space-y-3 rounded-xl border bg-slate-50/80 border-slate-100" x-data="{
                                            rcOpen: false, rcSearch: '', rcLocal: '', rcSelected: null,
                                            rcCustomers: @js($customers),
                                            rcCountries: [
                                                { name: 'اليمن', code: 'YE', dial_code: '+967' },
                                                { name: 'السعودية', code: 'SA', dial_code: '+966' }
                                            ],
                                            get rcFilteredCountries() {
                                                if (this.rcSearch === '') return this.rcCountries;
                                                return this.rcCountries.filter(c => c.name.toLowerCase().includes(this.rcSearch.toLowerCase()) || c.dial_code.includes(this.rcSearch));
                                            },
                                            get rcFilteredCustomers() {
                                                if (this.rcLocal.trim() === '') return this.rcCustomers;
                                                const s = this.rcLocal.trim();
                                                return this.rcCustomers.filter(c => c.phone && c.phone.includes(s));
                                            },
                                            selectCustomer(customer) {
                                                item.receiver_name = customer.name;
                                                let p = customer.phone || '';
                                                const codes = this.rcCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                for (const code of codes) {
                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                    if (regex.test(p)) {
                                                        this.rcSelected = this.rcCountries.find(c => c.dial_code.replace('+', '') === code);
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
                                            rcSelected = rcCountries.find(c => c.code === 'YE') || rcCountries[0];
                                            if (item.receiver_phone) {
                                                let p = item.receiver_phone;
                                                const codes = rcCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                for (const code of codes) {
                                                    let regex = new RegExp('^(\\+|00)?' + code);
                                                    if (regex.test(p)) {
                                                        rcSelected = rcCountries.find(c => c.dial_code.replace('+', '') === code);
                                                        rcLocal = p.replace(regex, '');
                                                        break;
                                                    }
                                                }
                                                if (!rcLocal && p) rcLocal = p;
                                            }
                                        " x-effect="item.receiver_phone = (rcSelected?.dial_code.replace('+', '') || '') + rcLocal">

                                        {{-- هاتف المستلم --}}
                                        <div class="relative">
                                            <label class="block mb-1.5 text-[10px] font-bold text-slate-500">رقم هاتف المستلم <span class="text-rose-500">*</span></label>
                                            <input type="hidden" :name="`items[${index}][receiver_phone]`" :value="item.receiver_phone">

                                            <div class="flex overflow-visible items-center w-full h-11 bg-white rounded-lg border border-slate-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                                                
                                                {{-- الإدخال (على اليمين) --}}
                                                <input type="tel" x-model="rcLocal" @input="onPhoneInput()" @focus="rcOpen = true" @click.outside="rcOpen = false" placeholder="7XXXXXXXX" dir="ltr" autocomplete="off" required
                                                    class="flex-grow px-3 w-full min-w-0 h-full text-xs font-bold tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 text-slate-700">

                                                {{-- زر الدولة (على اليسار) --}}
                                                <button type="button" @click="rcOpen = !rcOpen" class="flex gap-1.5 items-center px-2.5 h-full rounded-l-lg border-r transition-colors bg-slate-50 border-slate-200 shrink-0 hover:bg-slate-100">
                                                    <template x-if="rcSelected">
                                                        <div class="flex gap-1.5 items-center">
                                                            <img :src="`https://flagcdn.com/w20/${rcSelected.code.toLowerCase()}.png`" class="w-4 h-auto rounded-[2px] shadow-sm">
                                                            <span class="text-[10px] font-bold text-slate-600" dir="ltr" x-text="rcSelected.dial_code"></span>
                                                        </div>
                                                    </template>
                                                </button>
                                            </div>

                                            {{-- قائمة العملاء المقترحين للمستلم --}}
                                            <div x-show="rcOpen && rcLocal.trim().length > 0" x-transition x-cloak
                                                class="absolute z-[60] left-0 right-0 mt-1 top-full max-h-48 bg-white rounded-xl border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.2)] overflow-y-auto custom-scrollbar p-1">
                                                <template x-for="customer in rcFilteredCustomers" :key="customer.id">
                                                    <button type="button" @click="selectCustomer(customer)"
                                                        class="flex justify-between items-center px-3 py-2.5 w-full text-right rounded-lg border-b transition-colors border-slate-50 hover:bg-primary/5 last:border-0">
                                                        <span class="text-[11px] font-black text-slate-800" x-text="customer.name"></span>
                                                        <span class="font-mono text-[10px] font-bold text-slate-400 dir-ltr" x-text="customer.phone || '—'"></span>
                                                    </button>
                                                </template>
                                                <div x-show="rcFilteredCustomers.length === 0" class="flex gap-1.5 items-center justify-center px-3 py-3 text-[10px] font-bold text-slate-500 bg-slate-50 rounded-lg m-1">
                                                    <span class="material-symbols-outlined text-[14px] text-primary">person_add</span>
                                                    عميل جديد — سيتم تسجيله تلقائياً
                                                </div>
                                            </div>
                                        </div>

                                        {{-- اسم المستلم --}}
                                        <div>
                                            <label class="block mb-1.5 text-[10px] font-bold text-slate-500">اسم المستلم <span class="text-rose-500">*</span></label>
                                            <input type="text" :name="`items[${index}][receiver_name]`" x-model="item.receiver_name" placeholder="الاسم" required
                                                class="px-3 w-full h-11 text-xs font-bold bg-white rounded-lg border outline-none border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">
                                        </div>
                                    </div>

                                    {{-- حالة الدفع والمبلغ --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block mb-1.5 text-[10px] font-bold text-slate-500">حالة الدفع <span class="text-rose-500">*</span></label>
                                            <div class="flex p-1 rounded-xl border bg-slate-100 border-slate-200/50">
                                                <label class="flex-1 cursor-pointer">
                                                    <input type="radio" value="unpaid" :name="`items[${index}][payment_status]`" x-model="item.payment_status" class="sr-only peer">
                                                    <div class="flex justify-center items-center w-full h-9 text-[10px] font-black text-slate-400 rounded-lg transition-all peer-checked:bg-white peer-checked:text-primary peer-checked:shadow-sm">
                                                        عند الاستلام
                                                    </div>
                                                </label>
                                                <label class="flex-1 cursor-pointer">
                                                    <input type="radio" value="paid" :name="`items[${index}][payment_status]`" x-model="item.payment_status" @change="item.amount = 0" class="sr-only peer">
                                                    <div class="flex justify-center items-center w-full h-9 text-[10px] font-black text-slate-400 rounded-lg transition-all peer-checked:bg-white peer-checked:text-emerald-500 peer-checked:shadow-sm">
                                                        مدفوع
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block mb-1.5 text-[10px] font-bold text-slate-500">المبلغ (ر.ي) <span class="text-rose-500">*</span></label>
                                            <input type="number" :name="`items[${index}][amount]`" x-model="item.amount"
                                                :readonly="item.payment_status === 'paid'"
                                                :class="item.payment_status === 'paid' ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed' : 'bg-slate-50/50 border-slate-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700'"
                                                placeholder="0" min="0" step="1"
                                                class="px-3 w-full h-11 font-mono text-xs font-bold rounded-xl transition-all outline-none">
                                        </div>
                                    </div>

                                    {{-- نوع الطرد وملاحظات --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block mb-1.5 text-[10px] font-bold text-slate-500">النوع <span class="text-rose-500">*</span></label>
                                            <select :name="`items[${index}][package_type]`" x-model="item.package_type"
                                                class="px-3 w-full h-11 text-xs font-bold rounded-xl border appearance-none outline-none bg-slate-50/50 border-slate-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">
                                                <option value="carton">كرتون</option>
                                                <option value="bag">كيس</option>
                                                <option value="envelope">مغلف</option>
                                                <option value="other">أخرى</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block mb-1.5 text-[10px] font-bold text-slate-500">ملاحظات الطرد</label>
                                            <input type="text" :name="`items[${index}][item_notes]`" x-model="item.item_notes" placeholder="..."
                                                class="px-3 w-full h-11 text-xs font-bold rounded-xl border outline-none bg-slate-50/50 border-slate-200 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State (إذا تم حذف كل الطرود) --}}
                    <div x-show="items.length === 0" class="flex flex-col justify-center items-center py-10 rounded-2xl border border-dashed bg-slate-50 border-slate-200">
                        <span class="mb-2 text-4xl text-slate-300 material-symbols-outlined">inventory_2</span>
                        <p class="text-xs font-bold text-slate-500 font-headline">لا توجد طرود مسجلة حالياً</p>
                        <button type="button" @click="addItem()"
                            class="px-4 py-2 mt-3 text-[10px] font-black rounded-xl text-primary bg-primary/10 hover:bg-primary/20 active:scale-95 transition-transform shadow-sm">
                            اضغط هنا لإضافة طرد
                        </button>
                    </div>
                </div>
            </div>

            {{-- ======================== Footer (Submit) ======================== --}}
            <div class="mt-8 mb-4 p-5 bg-white rounded-[2.5rem] border border-slate-50 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] flex flex-col items-center justify-center">
                <button type="submit" @click="if(items.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                    :disabled="items.length === 0 || isSubmitting"
                    class="w-full h-14 px-8 bg-slate-900 text-white rounded-2xl font-black text-sm shadow-[0_10px_25px_rgba(15,23,42,0.3)] disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none transition-all active:scale-95 flex items-center justify-center gap-3">
                    <template x-if="isSubmitting">
                        <div class="flex gap-2 items-center">
                            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                            <span>جاري حفظ الإرسالية...</span>
                        </div>
                    </template>
                    <template x-if="!isSubmitting">
                        <div class="flex gap-3 items-center">
                            <span>اعتماد وحفظ الإرسالية</span>
                            <div class="flex justify-center items-center w-7 h-7 rounded-xl bg-white/20">
                                <span class="text-[12px] font-mono" x-text="items.length">0</span>
                            </div>
                        </div>
                    </template>
                </button>
            </div>

        </form>
    </div>
@endsection