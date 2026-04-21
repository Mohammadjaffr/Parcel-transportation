@extends('layouts.app')
@section('title', 'إضافة بيان استلام')
@section('Breadcrumb', 'إدارة الرحلات والشحنات المستلمة')

@section('addButton')
    {{-- زر الرجوع بدلاً من إضافة (لأننا في صفحة الإضافة بالفعل) --}}
    <a href="{{ route('receipts.index') }}"
        class="flex gap-2 justify-center items-center px-4 h-12 text-sm font-bold text-gray-600 bg-white rounded-xl border border-gray-200 shadow-sm transition-all hover:bg-gray-50 hover:text-primary dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-primary active:scale-95">
        <span class="material-symbols-outlined text-[20px] rtl:rotate-180">arrow_back</span>
        رجوع للقائمة
    </a>
@endsection

@section('style')
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
        [x-cloak] { display: none !important; }
        
        /* إخفاء أسهم الأرقام في حقول Number */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] { -moz-appearance: textfield; }
    </style>
@endsection

@section('content')

    <form action="{{ route('receipts.store') }}" method="POST" class="space-y-6 font-outfit" dir="rtl" 
          @submit="isSubmitting = true"
          x-data="{
            isSubmitting: false,
        
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
            items: @js(old('items', [['number' => '', 'sender_name' => '', 'receiver_name' => '', 'receiver_phone' => '', 'package_type' => 'carton', 'item_notes' => '', 'payment_status' => 'unpaid', 'amount' => '']])),
            errorIndices: @js(collect($errors->keys())->map(fn($key) => preg_match('/^items\.(\d+)/', $key, $m) ? (int) $m[1] : null)->filter(fn($v) => !is_null($v))->unique()->values()),
            
            addItem() {
                this.items.push({ number: '', sender_name: '', receiver_name: '', receiver_phone: '', package_type: 'carton', item_notes: '', payment_status: 'unpaid', amount: '' });
            },
            removeItem(index) {
                if (this.items.length > 1) this.items.splice(index, 1);
            }
        }">
        @csrf

        {{-- ======================== القسم الأول: بيانات الاستلام ======================== --}}
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800">
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/30">
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">بيانات الاستلام الأساسية</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                {{-- السائق (Combobox) --}}
                <div class="p-5 bg-white rounded-2xl border border-gray-100 md:col-span-2 dark:bg-gray-900/50 dark:border-gray-700/50">
                    <div class="flex gap-2 items-center mb-4 text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                        بيانات السائق
                    </div>

                    <input type="hidden" name="driver_id" :value="driver_id">

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        {{-- رقم الهاتف --}}
                        <div class="relative">
                            <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">
                                رقم هاتف السائق <span class="text-red-500">*</span>
                            </label>
                            <input type="hidden" name="driver_phone" :value="fullPhone">

                            <div class="flex overflow-visible items-center w-full h-12 bg-white rounded-xl border border-gray-200 transition-all focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:border-gray-700 dark:bg-gray-900"
                                :class="driver_id ? 'border-green-400 ring-1 ring-green-400 bg-green-50 dark:bg-green-900/20 dark:border-green-600' : ''">
                                
                                {{-- زر الدولة --}}
                                <button type="button" @click="countryOpen = !countryOpen"
                                    class="flex gap-2 items-center px-3 h-full bg-gray-50 rounded-r-xl border-l border-gray-200 transition-colors dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 shrink-0">
                                    <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`" class="w-5 h-auto rounded-[2px]">
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="selectedCountry.dial_code"></span>
                                    <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                                </button>

                                {{-- الإدخال --}}
                                <input type="tel" x-model="localPhoneNumber" @input="onPhoneInput()" @focus="driverOpen = true" @click.outside="driverOpen = false"
                                    placeholder="7XXXXXXXX" dir="ltr" autocomplete="off"
                                    class="flex-grow px-4 h-full text-sm tracking-wider text-left bg-transparent border-none outline-none dark:text-white focus:ring-0">

                                {{-- علامة صح إذا تم التحديد --}}
                                <div x-show="driver_id" class="flex items-center px-3 pointer-events-none">
                                    <span class="material-symbols-outlined text-[20px] text-green-500">check_circle</span>
                                </div>
                            </div>

                            {{-- قائمة الدول --}}
                            <div x-show="countryOpen" @click.outside="countryOpen = false" x-transition x-cloak
                                class="overflow-hidden absolute z-10 mt-1 w-full max-h-60 bg-white rounded-xl border border-gray-100 shadow-lg dark:bg-gray-800 dark:border-gray-700 custom-scrollbar" style="top: 100%;">
                                <div class="p-2 border-b border-gray-50 dark:border-gray-700">
                                    <input type="text" x-model="countrySearch" placeholder="ابحث عن الدولة..." class="px-3 py-2 w-full text-xs bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>
                                <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                    <template x-for="country in filteredCountries" :key="country.code">
                                        <button type="button" @click="selectedCountry = country; countryOpen = false; countrySearch = ''" class="flex justify-between items-center px-4 py-2.5 w-full text-sm text-left transition-colors hover:bg-primary/5 dark:hover:bg-gray-700">
                                            <div class="flex gap-3 items-center">
                                                <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`" class="w-5 h-auto rounded-[2px]">
                                                <span class="font-medium text-gray-700 dark:text-gray-300" x-text="country.name"></span>
                                            </div>
                                            <span class="font-mono text-xs text-gray-500 dark:text-gray-400 dir-ltr" x-text="country.dial_code"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- قائمة السائقين المقترحين --}}
                            <div x-show="driverOpen && localPhoneNumber.trim().length > 0 && !driver_id" x-transition x-cloak @click.outside="driverOpen = false"
                                class="overflow-y-auto absolute z-30 mt-1 w-full max-h-56 bg-white rounded-xl border border-gray-100 shadow-lg dark:bg-gray-800 dark:border-gray-700 custom-scrollbar" style="top: 100%;">
                                <template x-for="driver in filteredDrivers" :key="driver.id">
                                    <button type="button" @click="selectDriver(driver)" class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-primary/5 dark:hover:bg-gray-700 last:border-0 dark:border-gray-700/50">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="driver.name"></span>
                                        <span class="font-mono text-xs text-gray-500 dir-ltr" x-text="driver.phone || '—'"></span>
                                    </button>
                                </template>
                                <div x-show="filteredDrivers.length === 0" class="flex gap-2 items-center px-4 py-4 text-sm text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-800/50">
                                    <span class="material-symbols-outlined text-[18px] text-primary">person_add</span>
                                    لا يوجد سائق بهذا الرقم — سيتم إنشاؤه تلقائياً
                                </div>
                            </div>
                            @error('driver_phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- اسم السائق --}}
                        <div>
                            <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">
                                اسم السائق <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="driver_name" x-model="driver_name" placeholder="أدخل اسم السائق ثلاثياً"
                                :readonly="!!driver_id"
                                :class="driver_id ? 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 cursor-not-allowed border-gray-200 dark:border-gray-700' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white'"
                                class="px-4 w-full h-12 text-sm rounded-xl transition-all outline-none">
                            @error('driver_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- رقم السند + الفرع --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">
                        رقم سند الاستلام <span class="text-red-500">*</span>
                    </label>
                    <div class="relative group">
                        <input type="text" name="number" value="{{ old('number') }}" required placeholder="مثال: REC-1001"
                            class="pr-11 pl-4 w-full h-12 text-sm font-medium bg-white rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:text-gray-300 dark:bg-gray-900">
                        <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                            <span class="material-symbols-outlined text-[20px]">tag</span>
                        </div>
                    </div>
                    @error('number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">
                        المكتب المرسل <span class="text-red-500">*</span>
                    </label>
                    <select name="source_branch_code" required
                        class="px-4 w-full h-12 text-sm font-medium bg-white rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:text-gray-300 dark:bg-gray-900">
                        <option value="" disabled selected>-- اختر الفرع المرسل --</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->code }}" {{ old('source_branch_code') == $branch->code ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('source_branch_code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- ملاحظات عامة --}}
                <div class="md:col-span-2">
                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">ملاحظات إضافية</label>
                    <textarea name="general_notes" rows="2" placeholder="أي ملاحظات عامة على بيان الاستلام..."
                        class="p-4 w-full text-sm bg-white rounded-xl border border-gray-200 transition-all outline-none resize-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:text-gray-300 dark:bg-gray-900">{{ old('general_notes') }}</textarea>
                    @error('general_notes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ======================== القسم الثاني: الطرود ======================== --}}
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800">
            
            {{-- Header --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50/50 dark:border-gray-700 dark:bg-gray-900/30">
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary-50 text-primary dark:bg-primary-500/10">
                        <span class="material-symbols-outlined text-[22px]">inventory</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">الطرود المستلمة</h3>
                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400">
                            العدد الإجمالي: <span class="px-2 py-0.5 text-white bg-green-500 rounded-md" x-text="items.length"></span>
                        </p>
                    </div>
                </div>

                <button type="button" @click="addItem()"
                    class="flex gap-2 items-center px-4 py-2.5 text-sm font-bold text-white rounded-xl shadow-sm transition-all bg-primary hover:bg-primary-600 active:scale-95">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span class="hidden sm:block">إضافة طرد</span>
                </button>
            </div>

            {{-- Items Grid --}}
            <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-2">
                <template x-for="(item, index) in items" :key="index">
                    
                    <div :class="errorIndices.includes(index) ? 'border-red-300 bg-red-50 dark:bg-red-500/10 dark:border-red-500/30' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/30 hover:border-primary/30'"
                         class="relative p-5 rounded-2xl border transition-all shadow-theme-xs">

                        {{-- Badge & Delete --}}
                        <div class="flex justify-between items-center pb-3 mb-5 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-black text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[16px]">package_2</span>
                                طرد رقم <span x-text="index + 1"></span>
                            </span>
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" title="حذف الطرد"
                                class="flex justify-center items-center w-8 h-8 text-gray-400 bg-white rounded-lg border border-gray-200 transition-colors hover:text-red-500 hover:bg-red-50 hover:border-red-200 dark:bg-gray-800 dark:border-gray-600 dark:hover:bg-red-500/10">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            {{-- رقم السند واسم المرسل --}}
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">رقم السند <span class="text-red-500">*</span></label>
                                    <input type="text" :name="`items[${index}][number]`" x-model="item.number" placeholder="رقم بوليصة الطرد"
                                        class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">اسم المرسل</label>
                                    <input type="text" :name="`items[${index}][sender_name]`" x-model="item.sender_name" placeholder="اختياري"
                                        class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>
                            </div>

                            {{-- بيانات المستلم --}}
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">اسم المستلم <span class="text-red-500">*</span></label>
                                    <input type="text" :name="`items[${index}][receiver_name]`" x-model="item.receiver_name" placeholder="اسم المستلم"
                                        class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>

                                {{-- هاتف المستلم (مكون مخصص داخل الطرد) --}}
                                <div x-data="{
                                        rcOpen: false, rcSearch: '', rcLocal: '', rcSelected: null,
                                        rcCountries: [
                                            { name: 'اليمن', code: 'YE', dial_code: '+967' },
                                            { name: 'السعودية', code: 'SA', dial_code: '+966' }
                                        ],
                                        get rcFiltered() {
                                            if (this.rcSearch === '') return this.rcCountries;
                                            return this.rcCountries.filter(c => c.name.toLowerCase().includes(this.rcSearch.toLowerCase()) || c.dial_code.includes(this.rcSearch));
                                        }
                                    }" 
                                    x-init="
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
                                    "
                                    x-effect="item.receiver_phone = (rcSelected?.dial_code.replace('+', '') || '') + rcLocal"
                                    class="relative">
                                    
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">رقم المستلم <span class="text-red-500">*</span></label>
                                    <input type="hidden" :name="`items[${index}][receiver_phone]`" :value="item.receiver_phone">

                                    <div class="flex overflow-visible items-center w-full h-11 bg-gray-50 rounded-xl border border-gray-200 focus-within:bg-white focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-700">
                                        <button type="button" @click="rcOpen = !rcOpen" class="flex gap-1.5 items-center px-2.5 h-full bg-gray-100 rounded-r-xl border-l border-gray-200 transition-colors shrink-0 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-200">
                                            <template x-if="rcSelected">
                                                <div class="flex gap-1 items-center">
                                                    <img :src="`https://flagcdn.com/w20/${rcSelected.code.toLowerCase()}.png`" class="w-5 h-auto rounded-sm">
                                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="rcSelected.dial_code"></span>
                                                </div>
                                            </template>
                                        </button>
                                        <input type="tel" x-model="rcLocal" placeholder="7XXXXXXXX" dir="ltr" autocomplete="off"
                                            class="px-3 w-full h-full text-sm tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 dark:text-white">
                                    </div>

                                    {{-- قائمة الدول للمستلم --}}
                                    <div x-show="rcOpen" @click.outside="rcOpen = false" x-transition x-cloak class="overflow-hidden absolute z-40 mt-1 w-full max-h-48 bg-white rounded-xl border border-gray-100 shadow-lg dark:bg-gray-800 dark:border-gray-700 custom-scrollbar">
                                        <div class="p-2 border-b border-gray-50 dark:border-gray-700">
                                            <input type="text" x-model="rcSearch" placeholder="ابحث..." class="px-3 py-2 w-full text-xs bg-gray-50 rounded-lg border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div class="overflow-y-auto max-h-32 custom-scrollbar">
                                            <template x-for="country in rcFiltered" :key="country.code">
                                                <button type="button" @click="rcSelected = country; rcOpen = false; rcSearch = ''" class="flex justify-between items-center px-4 py-2 w-full text-sm text-left transition-colors hover:bg-primary/5 dark:hover:bg-gray-700">
                                                    <div class="flex gap-2 items-center">
                                                        <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`" class="w-5 h-auto rounded-sm">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300" x-text="country.name"></span>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- حالة الدفع والمبلغ --}}
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">حالة الدفع <span class="text-red-500">*</span></label>
                                    
                                    {{-- Segmented Control UI --}}
                                    <div class="flex p-1 bg-gray-100 rounded-xl dark:bg-gray-900/50 dark:border dark:border-gray-700">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" value="unpaid" :name="`items[${index}][payment_status]`" x-model="item.payment_status" class="sr-only peer">
                                            <div class="flex justify-center items-center w-full h-9 text-sm font-bold text-gray-500 rounded-lg transition-all peer-checked:bg-white peer-checked:text-primary peer-checked:shadow-sm dark:peer-checked:bg-gray-700 dark:peer-checked:text-white dark:text-gray-400">
                                                عند الاستلام
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" value="paid" :name="`items[${index}][payment_status]`" x-model="item.payment_status" @change="item.amount = 0" class="sr-only peer">
                                            <div class="flex justify-center items-center w-full h-9 text-sm font-bold text-gray-500 rounded-lg transition-all peer-checked:bg-white peer-checked:text-green-600 peer-checked:shadow-sm dark:peer-checked:bg-gray-700 dark:peer-checked:text-green-400 dark:text-gray-400">
                                                مدفوع
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">مبلغ التحصيل (ر.ي) <span class="text-red-500">*</span></label>
                                    <input type="number" :name="`items[${index}][amount]`" x-model="item.amount"
                                        :readonly="item.payment_status === 'paid'"
                                        :class="item.payment_status === 'paid' ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed dark:bg-gray-900 dark:border-gray-700' : 'bg-white border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-800 dark:border-gray-600 dark:text-white'"
                                        placeholder="0" min="0" step="1"
                                        class="px-4 w-full h-11 font-mono text-sm rounded-xl transition-all outline-none">
                                </div>
                            </div>

                            {{-- نوع الطرد والملاحظات --}}
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">النوع <span class="text-red-500">*</span></label>
                                    <input type="text" :name="`items[${index}][package_type]`" x-model="item.package_type" placeholder="كرتون، كيس..."
                                        class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">ملاحظات على الطرد</label>
                                    <input type="text" :name="`items[${index}][item_notes]`" x-model="item.item_notes" placeholder="اختياري"
                                        class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty State --}}
                <div x-show="items.length === 0" class="flex flex-col justify-center items-center py-12 text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:border-gray-700 xl:col-span-2">
                    <span class="mb-2 text-4xl opacity-40 material-symbols-outlined">package_2</span>
                    <p class="text-sm font-bold text-gray-500">لا توجد طرود مسجلة حالياً</p>
                    <button type="button" @click="addItem()" class="px-4 py-2 mt-3 text-xs font-bold rounded-xl text-primary bg-primary/10 hover:bg-primary/20">اضغط هنا لإضافة الطرد الأول</button>
                </div>
            </div>
        </div>

        {{-- ======================== Footer (Submit) ======================== --}}
        <div class="flex gap-3 justify-end items-center pt-4">
            <button type="submit" :disabled="isSubmitting || items.length === 0"
                class="flex items-center justify-center gap-2 px-8 py-3 text-sm font-bold text-white transition-all rounded-xl bg-primary hover:bg-primary-hover active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed shadow-primary/30 shadow-lg min-w-[160px]">
                <span x-show="!isSubmitting" class="material-symbols-outlined text-[20px]">save</span>
                <span x-show="isSubmitting" class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                <span x-text="isSubmitting ? 'جاري الحفظ...' : 'اعتماد وحفظ البيانات'"></span>
            </button>
        </div>

    </form>

@endsection