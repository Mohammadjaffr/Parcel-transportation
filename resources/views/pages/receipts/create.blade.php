@extends('layouts.app')
@section('title', 'استلام شحنات')
@section('Breadcrumb', 'بيان استلام شحنات')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <form action="{{ route('receipts.store') }}" method="POST" x-data="{
                                                                                        isSubmitting: false,

                                                                                        {{-- ========== Driver Combobox (Select or Auto-Create) ========== --}}
                                                                                        driver_id: '{{ old('driver_id', '') }}',
                                                                                        driver_name: '{{ old('driver_name', '') }}',
                                                                                        localPhoneNumber: '{{ old('driver_phone') ? preg_replace('/^967/', '', old('driver_phone')) : '' }}',
                                                                                        selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                                                                                        countryOpen: false,
                                                                                        countrySearch: '',
                                                                                        countries: [
                                                                                            { name: 'Yemen', code: 'YE', dial_code: '+967' },
                                                                                            { name: 'Saudi Arabia', code: 'SA', dial_code: '+966' },
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
                                                                                            // Parse phone: strip known dial codes to fill localPhoneNumber
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
                                                                                        items: @js(old('items', [['number' => '', 'sender_name' => '', 'receiver_name' => '', 'receiver_phone' => '', 'package_type' => '', 'item_notes' => '']])),
                                                                                errorIndices: @js(collect($errors->keys())->map(function ($key) {
                                                                                    return preg_match('/^items\.(\d+)/', $key, $m) ? (int) $m[1] : null;
                                                                                })->filter(fn($v) => !is_null($v))->unique()->values()),
                                                                                        addItem() {
                                                                                            this.items.push({ number: '', sender_name: '', receiver_name: '', receiver_phone: '', package_type: 'carton', item_notes: '' });
                                                                                        },
                                                                                        removeItem(index) {
                                                                                            if (this.items.length > 1) this.items.splice(index, 1);
                                                                                        }
                                                                                    }" @submit="isSubmitting = true">
        @csrf

        <div class="space-y-6">

            {{-- ======================== Header Section ======================== --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">بيانات الاستلام</h3>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- السائق (Combobox: بحث برقم الهاتف) --}}
                    <div class="md:col-span-2">
                        <label class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            السائق <span class="text-error-500">*</span>
                        </label>

                        {{-- Hidden input: sends driver_id (null if new) --}}
                        <input type="hidden" name="driver_id" :value="driver_id">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            {{-- رقم الهاتف (Searchable Combobox with Country Code) --}}
                            <div class="relative">
                                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                    رقم الهاتف <span class="text-error-500">*</span>
                                </label>

                                {{-- Hidden input: full phone number (dial_code + local) --}}
                                <input type="hidden" name="driver_phone" :value="fullPhone">

                                <div class="flex h-11 w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 overflow-visible"
                                    :class="driver_id ? 'border-green-300 dark:border-green-600 bg-green-50 dark:bg-green-900/20' : ''">

                                    {{-- Country code button --}}
                                    <button type="button" @click="countryOpen = !countryOpen"
                                        class="flex items-center gap-1.5 px-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-600 rounded-r-lg shrink-0">
                                        <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                            alt="Flag" class="w-5 h-auto">
                                        <span class="text-xs font-bold text-gray-500"
                                            x-text="selectedCountry.dial_code"></span>
                                        <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    {{-- Phone number input --}}
                                    <input type="tel" x-model="localPhoneNumber" @input="onPhoneInput()"
                                        @focus="driverOpen = true" @click.outside="driverOpen = false"
                                        placeholder="780236551" dir="ltr" autocomplete="off"
                                        class="flex-grow bg-transparent px-3 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none rounded-l-lg text-left">

                                    {{-- Selected indicator --}}
                                    <div x-show="driver_id" class="flex items-center px-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd"
                                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Country dropdown --}}
                                <div x-show="countryOpen" @click.outside="countryOpen = false" x-transition
                                    class="absolute z-40 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-gray-800 dark:border-gray-700 max-h-60"
                                    style="top: 100%;">
                                    <input type="text" x-model="countrySearch" placeholder="ابحث عن الدولة..."
                                        class="w-full px-4 py-2 border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500 text-sm">
                                    <div class="overflow-y-auto max-h-48">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; countryOpen = false; countrySearch = ''"
                                                class="flex items-center gap-3 p-2 px-4 cursor-pointer hover:bg-brand-50 dark:hover:bg-gray-700 transition-colors">
                                                <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                                    alt="" class="w-5">
                                                <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                                                    x-text="country.name"></span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400"
                                                    x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Driver search dropdown --}}
                                <div x-show="driverOpen && localPhoneNumber.trim().length > 0 && !driver_id" x-transition
                                    @click.outside="driverOpen = false"
                                    class="absolute z-30 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg max-h-56 overflow-y-auto"
                                    style="top: 100%;">

                                    <template x-for="driver in filteredDrivers" :key="driver.id">
                                        <div @click="selectDriver(driver)"
                                            class="flex items-center justify-between px-4 py-2.5 cursor-pointer hover:bg-brand-50 dark:hover:bg-gray-700 transition-colors">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100"
                                                x-text="driver.name"></span>
                                            <span class="text-xs text-gray-400 font-mono" dir="ltr"
                                                x-text="driver.phone || '—'"></span>
                                        </div>
                                    </template>

                                    {{-- No match hint --}}
                                    <div x-show="filteredDrivers.length === 0"
                                        class="flex items-center gap-2 px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        لا يوجد سائق بهذا الرقم — سيتم إنشاء سائق جديد
                                    </div>
                                </div>

                                @error('driver_phone')
                                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- اسم السائق --}}
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                    اسم السائق <span class="text-error-500">*</span>
                                </label>
                                <input type="text" name="driver_name" x-model="driver_name" placeholder="اسم السائق"
                                    :readonly="!!driver_id"
                                    :class="driver_id ? 'bg-gray-100 dark:bg-gray-900/60 cursor-not-allowed' : ''"
                                    class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                                @error('driver_name')
                                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- New driver badge --}}
                        <p x-show="localPhoneNumber.trim().length > 0 && !driver_id"
                            class="mt-1.5 text-xs text-brand-500 dark:text-brand-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z"
                                    clip-rule="evenodd" />
                            </svg>
                            وضع الإضافة — سيتم إنشاء سائق جديد عند الحفظ
                        </p>

                        @error('driver_id')
                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- رقم السند + الفرع المرسل (نفس الصف) --}}
                    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- رقم السند --}}
                        <div>
                            <label for="receipt_number"
                                class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                رقم السند <span class="text-error-500">*</span>
                            </label>
                            <input type="text" name="number" id="receipt_number" value="{{ old('number') }}"
                                placeholder="أدخل رقم السند" required
                                class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                            @error('number')
                                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- المكتب المرسل --}}
                        <div>
                            <label for="source_branch_code"
                                class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                المكتب المرسل <span class="text-error-500">*</span>
                            </label>
                            <select name="source_branch_code" id="source_branch_code"
                                class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                                <option value="">-- اختر الفرع المرسل --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->code }}" {{ old('source_branch_code') == $branch->code ? 'selected' : '' }}>
                                        {{ $branch->name }} ({{ $branch->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('source_branch_code')
                                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- ملاحظات عامة --}}
                    <div class="md:col-span-2">
                        <label for="general_notes"
                            class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            ملاحظات عامة
                        </label>
                        <textarea name="general_notes" id="general_notes" rows="3"
                            placeholder="ملاحظات إضافية على بيان الاستلام..."
                            class="px-4 py-2.5 w-full text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all resize-none">{{ old('general_notes') }}</textarea>
                        @error('general_notes')
                            <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ======================== Items Section ======================== --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">الطرود</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                عدد الطرود: <span class="font-bold text-brand-500" x-text="items.length"></span>
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-success-500 rounded-lg hover:bg-success-600 transition-all active:scale-95 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        إضافة طرد
                    </button>
                </div>

                <div class="p-6" style="display: flex; flex-wrap: wrap; gap: 16px;">
                    <template x-for="(item, index) in items" :key="index">
                        <div style="flex: 0 0 calc(50% - 8px);"
                            :class="errorIndices.includes(index) ? 'border-error-500 ring-1 ring-error-500 bg-error-50 dark:bg-error-500/10' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50'"
                            class="relative p-5 rounded-xl border">

                            {{-- Item number badge --}}
                            <div class="flex items-center justify-between mb-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    طرد #<span x-text="index + 1"></span>
                                </span>
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                    class="p-1.5 text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 rounded-lg transition-all"
                                    title="حذف الطرد">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <div>
                                {{-- رقم السند + اسم المرسل --}}
                                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                            رقم السند <span class="text-error-500">*</span>
                                        </label>
                                        <input type="text" :name="`items[${index}][number]`" x-model="item.number"
                                            placeholder="مثال: 1001"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                            اسم المرسل
                                        </label>
                                        <input type="text" :name="`items[${index}][sender_name]`" x-model="item.sender_name"
                                            placeholder="اسم المرسل"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                                    </div>
                                </div>

                                {{-- اسم المستلم + رقم هاتف المستلم --}}
                                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                            اسم المستلم <span class="text-error-500">*</span>
                                        </label>
                                        <input type="text" :name="`items[${index}][receiver_name]`"
                                            x-model="item.receiver_name" placeholder="اسم المستلم"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                                    </div>
                                    <div style="flex: 1;" x-data="{
                                                rcOpen: false,
                                                rcSearch: '',
                                                rcCountries: @js(array_values(config('countries'))),
                                                rcSelected: null,
                                                rcLocal: '',
                                                get rcFiltered() {
                                                    if (this.rcSearch === '') return this.rcCountries;
                                                    return this.rcCountries.filter(c => c.name.toLowerCase().includes(this.rcSearch.toLowerCase()) || c.dial_code.includes(this.rcSearch));
                                                }
                                            }" x-init="
                                                // Initialize selected country (default Yemen)
                                                rcSelected = rcCountries.find(c => c.code === 'YE') || rcCountries[0];

                                                if (item.receiver_phone) {
                                                    let p = item.receiver_phone;
                                                    // Sort codes by length desc to match longest prefix first
                                                    const codes = rcCountries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                                                    for (const code of codes) {
                                                        // Check matches with or without + or 00
                                                        let regex = new RegExp('^(\\+|00)?' + code);
                                                        if (regex.test(p)) {
                                                            rcSelected = rcCountries.find(c => c.dial_code.replace('+', '') === code);
                                                            rcLocal = p.replace(regex, '');
                                                            break;
                                                        }
                                                    }
                                                    // If no match found, keep p as local and default country
                                                    if (!rcLocal && p) rcLocal = p; 
                                                }
                                            "
                                        x-effect="item.receiver_phone = (rcSelected?.dial_code.replace('+', '') || '') + rcLocal"
                                        class="relative">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                            رقم هاتف المستلم <span class="text-error-500">*</span>
                                        </label>

                                        {{-- Hidden input: full phone --}}
                                        <input type="hidden" :name="`items[${index}][receiver_phone]`"
                                            :value="item.receiver_phone">

                                        <div
                                            class="flex h-11 w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 overflow-visible">
                                            {{-- Country code button --}}
                                            <button type="button" @click="rcOpen = !rcOpen"
                                                class="flex items-center gap-1.5 px-2.5 bg-gray-100 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-600 rounded-r-lg shrink-0">

                                                <template x-if="rcSelected">
                                                    <svg class="w-5 h-auto rounded-sm" viewBox="0 0 36 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg" x-html="rcSelected.svg"></svg>
                                                </template>

                                                
                                            </button>

                                            {{-- Phone number input --}}
                                            <input type="tel" x-model="rcLocal" placeholder="780236551" dir="ltr"
                                                autocomplete="off"
                                                class="flex-grow bg-transparent px-3 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none rounded-l-lg text-left">
                                        </div>

                                        {{-- Country dropdown --}}
                                        <div x-show="rcOpen" @click.outside="rcOpen = false" x-transition
                                            class="absolute z-40 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-gray-800 dark:border-gray-700 max-h-60">
                                            <input type="text" x-model="rcSearch" placeholder="ابحث عن الدولة..."
                                                class="w-full px-4 py-2 border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500 text-sm">
                                            <div class="overflow-y-auto max-h-20 custom-scrollbar">
                                                <template x-for="country in rcFiltered" :key="country.code">
                                                    <div @click="rcSelected = country; rcOpen = false; rcSearch = ''"
                                                        class="flex items-center gap-3 p-2 px-4 cursor-pointer hover:bg-brand-50 dark:hover:bg-gray-700 transition-colors">

                                                        <svg class="w-5 h-auto rounded-sm" viewBox="0 0 36 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>

                                                        <span
                                                            class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                                                            x-text="country.name"></span>
                                                        <span
                                                            class="text-xs text-gray-500 dark:text-gray-400 dir-ltr font-mono"
                                                            x-text="country.dial_code"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- نوع الطرد + ملاحظات --}}
                                <div style="display: flex; gap: 12px;">
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                            نوع الطرد <span class="text-error-500">*</span>
                                        </label>
                                        <input type="text" :name="`items[${index}][package_type]`"
                                            x-model="item.package_type" placeholder="مثال: كرتون، كيس، ظرف"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                            ملاحظات
                                        </label>
                                        <input type="text" :name="`items[${index}][item_notes]`" x-model="item.item_notes"
                                            placeholder="ملاحظات إضافية"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border border-gray-200 dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <div x-show="items.length === 0" style="width: 100%;"
                        class="text-center py-10 text-gray-400 dark:text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="text-sm">لا توجد طرود. اضغط "إضافة طرد" للبدء.</p>
                    </div>
                </div>
            </div>

            {{-- ======================== Submit ======================== --}}
            <div class="flex items-center justify-end gap-3">
                <button type="submit" :disabled="isSubmitting"
                    class="px-6 py-2.5 text-sm font-bold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm hover:shadow-md transition-all active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ بيان الاستلام'"></span>
                </button>
            </div>

        </div>
    </form>
@endsection