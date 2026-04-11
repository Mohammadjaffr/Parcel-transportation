@extends('layouts.app')
@section('title', 'تعديل بيان الاستلام')
@section('Breadcrumb', 'تعديل بيان الاستلام')
@section('content')
    <div x-data="{
        isSubmitting: false,
        errors: {},
        
        {{-- ========== Form Data ========== --}}
        receipt_number: '{{ $receipt->number }}',
        source_branch_code: '{{ $receipt->source_branch_code }}',
        general_notes: '{{ $receipt->general_notes }}',

        {{-- ========== Driver Combobox ========== --}}
        driver_id: '{{ $receipt->driver_id }}',
        driver_name: '{{ $receipt->driver->name ?? '' }}',
        localPhoneNumber: '{{ preg_replace('/^(967|966)/', '', $receipt->driver->phone ?? '') }}',
        selectedCountry: null,
        countryOpen: false,
        countrySearch: '',
        countries: @js(array_values(config('countries'))),
        
        init() {
            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
            let rawPhone = '{{ $receipt->driver->phone ?? '' }}';
            if (rawPhone.startsWith('966') || rawPhone.startsWith('+966')) {
                this.selectedCountry = this.countries.find(c => c.code === 'SA');
            } else if (rawPhone.startsWith('968') || rawPhone.startsWith('+968')) {
                this.selectedCountry = this.countries.find(c => c.code === 'OM');
            }
        },
        
        get filteredCountries() {
            if (this.countrySearch === '') return this.countries;
            return this.countries.filter(c => c.name.toLowerCase().includes(this.countrySearch.toLowerCase()) || c.dial_code.includes(this.countrySearch));
        },
        
        get fullPhone() {
            return this.selectedCountry ? (this.selectedCountry.dial_code.replace('+', '') + this.localPhoneNumber) : '';
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
                if (p.startsWith(code) || p.startsWith('+' + code)) {
                    this.selectedCountry = this.countries.find(c => c.dial_code === '+' + code);
                    p = p.replace(new RegExp('^(\\+)?' + code), '');
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
        items: @js(
            $receipt->items->map(
                fn($item) => [
                    'number' => $item->number,
                    'sender_name' => $item->sender_name ?? '',
                    'receiver_name' => $item->receiver_name,
                    'receiver_phone' => $item->receiver_phone,
                    'package_type' => $item->package_type,
                    'item_notes' => $item->item_notes ?? '',
                    'payment_status' => $item->payment_status ?? 'unpaid',
                    'amount' => $item->amount ?? 0,
                    'prevAmount' => $item->amount ?? 0,
                ],
            )
        ),
        
        addItem() {
            this.items.push({ number: '', sender_name: '', receiver_name: '', receiver_phone: '', package_type: '', item_notes: '', payment_status: 'unpaid', amount: '', prevAmount: 0 });
        },
        
        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },

        {{-- ========== Submit Logic (Fetch API) ========== --}}
        async submitForm() {
            this.isSubmitting = true;
            this.errors = {};
            
            try {
                const response = await fetch('{{ route('receipts.update', $receipt->id) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({
                        driver_id: this.driver_id,
                        driver_name: this.driver_name,
                        driver_phone: this.fullPhone,
                        number: this.receipt_number,
                        source_branch_code: this.source_branch_code,
                        general_notes: this.general_notes,
                        items: this.items
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = result.errors;
                        // تمرير الشاشة لأول خطأ (اختياري لتحسين الـ UX)
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        alert(result.message || 'حدث خطأ غير متوقع.');
                    }
                } else {
                    // Success Redirect
                    window.location.href = '{{ route('receipts.index') }}';
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('فشل الاتصال بالخادم. يرجى التحقق من اتصالك بالإنترنت.');
            } finally {
                this.isSubmitting = false;
            }
        }
    }">
        <form @submit.prevent="submitForm" class="space-y-6">

            {{-- ======================== Header Section ======================== --}}
            <div class="overflow-hidden bg-white border border-gray-200 rounded-[2rem] shadow-theme-xs dark:bg-boxdark dark:border-gray-700">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 dark:border-gray-700 dark:bg-gray-900/50">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-500/10 text-primary-500">
                            <span class="material-symbols-outlined">edit_document</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">تعديل بيانات الاستلام</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">

                    {{-- السائق --}}
                    <div class="md:col-span-2">
                        <label class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            السائق <span class="text-error-500">*</span>
                        </label>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                            {{-- رقم الهاتف --}}
                            <div class="relative">
                                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                    رقم الهاتف <span class="text-error-500">*</span>
                                </label>

                                <div class="flex overflow-visible w-full h-11 bg-gray-50 rounded-2xl border border-gray-200 dark:border-gray-600 dark:bg-gray-900"
                                    :class="driver_id ? 'border-success-300 dark:border-success-600 bg-success-50 dark:bg-success-900/20' : ''">

                                    <button type="button" @click="countryOpen = !countryOpen"
                                        class="flex gap-1.5 items-center px-2.5 bg-gray-100 rounded-r-2xl border-l border-gray-200 dark:bg-gray-800 dark:border-gray-600 shrink-0">

                                        <template x-if="selectedCountry">
                                            <span class="text-lg" x-html="selectedCountry.svg"></span>
                                        </template>

                                        <span class="text-xs font-bold text-gray-500 dir-ltr" x-text="selectedCountry?.dial_code"></span>
                                        <span class="text-sm text-gray-400 material-symbols-outlined">expand_more</span>
                                    </button>

                                    <input type="tel" x-model="localPhoneNumber" @input="onPhoneInput()"
                                        @focus="driverOpen = true" @click.outside="driverOpen = false"
                                        placeholder="780236551" dir="ltr" autocomplete="off"
                                        class="flex-grow px-3 text-sm text-left text-gray-800 bg-transparent rounded-l-2xl border-none dark:text-white focus:outline-none focus:ring-0">

                                    <div x-show="driver_id" class="flex items-center px-2 pointer-events-none">
                                        <span class="text-lg material-symbols-outlined text-success-500">check_circle</span>
                                    </div>
                                </div>

                                {{-- Country dropdown --}}
                                <div x-show="countryOpen" @click.outside="countryOpen = false" x-transition
                                    class="overflow-hidden absolute z-40 mt-1 w-full max-h-60 bg-white rounded-2xl border border-gray-200 shadow-theme-xs dark:bg-boxdark dark:border-gray-700"
                                    style="top: 100%; display: none;">
                                    <input type="text" x-model="countrySearch" placeholder="ابحث عن الدولة..."
                                        class="px-4 py-2 w-full text-sm border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; countryOpen = false; countrySearch = ''"
                                                class="flex gap-3 items-center p-2 px-4 transition-colors cursor-pointer hover:bg-primary-50 dark:hover:bg-gray-700">
                                                <span class="text-lg" x-html="country.svg"></span>
                                                <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100" x-text="country.name"></span>
                                                <span class="font-mono text-xs text-gray-500 dark:text-gray-400 dir-ltr" x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Driver search dropdown --}}
                                <div x-show="driverOpen && localPhoneNumber.trim().length > 0 && !driver_id" x-transition
                                    @click.outside="driverOpen = false"
                                    class="overflow-y-auto absolute z-30 mt-1 w-full max-h-56 bg-white rounded-2xl border border-gray-200 shadow-theme-xs dark:bg-boxdark dark:border-gray-700"
                                    style="top: 100%;">

                                    <template x-for="driver in filteredDrivers" :key="driver.id">
                                        <div @click="selectDriver(driver)"
                                            class="flex justify-between items-center px-4 py-2.5 transition-colors cursor-pointer hover:bg-primary-50 dark:hover:bg-gray-700">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="driver.name"></span>
                                            <span class="font-mono text-xs text-gray-400" dir="ltr" x-text="driver.phone || '—'"></span>
                                        </div>
                                    </template>

                                    <div x-show="filteredDrivers.length === 0"
                                        class="flex gap-2 items-center px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="text-lg material-symbols-outlined text-primary-500">info</span>
                                        لا يوجد سائق بهذا الرقم — سيتم إنشاء سائق جديد
                                    </div>
                                </div>

                                <p x-show="errors.driver_phone" x-text="errors.driver_phone ? errors.driver_phone[0] : ''" class="mt-1 text-xs text-error-500"></p>
                            </div>

                            {{-- اسم السائق --}}
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                    اسم السائق <span class="text-error-500">*</span>
                                </label>
                                <input type="text" x-model="driver_name" placeholder="اسم السائق"
                                    :readonly="!!driver_id"
                                    :class="driver_id ? 'bg-gray-100 dark:bg-gray-900/60 cursor-not-allowed' : ''"
                                    class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                <p x-show="errors.driver_name" x-text="errors.driver_name ? errors.driver_name[0] : ''" class="mt-1 text-xs text-error-500"></p>
                            </div>
                        </div>

                        <p x-show="localPhoneNumber.trim().length > 0 && !driver_id"
                            class="flex gap-1 items-center mt-1.5 text-xs text-primary-500 dark:text-primary-400">
                            <span class="text-sm material-symbols-outlined">person_add</span>
                            وضع الإضافة — سيتم إنشاء سائق جديد عند الحفظ
                        </p>
                        
                        <p x-show="errors.driver_id" x-text="errors.driver_id ? errors.driver_id[0] : ''" class="mt-1 text-xs text-error-500"></p>
                    </div>

                    {{-- رقم السند + المكتب المرسل --}}
                    <div class="grid grid-cols-1 gap-3 md:col-span-2 sm:grid-cols-2">
                        <div>
                            <label for="receipt_number" class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                رقم السند <span class="text-error-500">*</span>
                            </label>
                            <input type="text" id="receipt_number" x-model="receipt_number" placeholder="أدخل رقم السند" required
                                class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                            <p x-show="errors.number" x-text="errors.number ? errors.number[0] : ''" class="mt-1 text-xs text-error-500"></p>
                        </div>

                        <div>
                            <label for="source_branch_code" class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                المكتب المرسل <span class="text-error-500">*</span>
                            </label>
                            <select id="source_branch_code" x-model="source_branch_code"
                                class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                <option value="">-- اختر الفرع المرسل --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->code }}">{{ $branch->name }} ({{ $branch->code }})</option>
                                @endforeach
                            </select>
                            <p x-show="errors.source_branch_code" x-text="errors.source_branch_code ? errors.source_branch_code[0] : ''" class="mt-1 text-xs text-error-500"></p>
                        </div>
                    </div>

                    {{-- ملاحظات عامة --}}
                    <div class="md:col-span-2">
                        <label for="general_notes" class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            ملاحظات عامة
                        </label>
                        <textarea id="general_notes" x-model="general_notes" rows="3" placeholder="ملاحظات إضافية على بيان الاستلام..."
                            class="px-4 py-2.5 w-full text-sm rounded-2xl border border-gray-200 transition-all outline-none resize-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500"></textarea>
                        <p x-show="errors.general_notes" x-text="errors.general_notes ? errors.general_notes[0] : ''" class="mt-1 text-xs text-error-500"></p>
                    </div>
                </div>
            </div>

            {{-- ======================== Items Section ======================== --}}
            <div class="overflow-hidden bg-white border border-gray-200 rounded-[2rem] shadow-theme-xs dark:bg-boxdark dark:border-gray-700">
                <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-b border-gray-100 dark:border-gray-700 dark:bg-gray-900/50">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                            <span class="material-symbols-outlined">inventory_2</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">الطرود</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                عدد الطرود: <span class="font-bold text-primary-500" x-text="items.length"></span>
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="addItem()"
                        class="inline-flex gap-2 items-center px-4 py-2 text-sm font-semibold text-white rounded-2xl shadow-sm transition-all bg-success-500 hover:bg-success-600 active:scale-95">
                        <span class="text-sm material-symbols-outlined">add</span>
                        إضافة طرد
                    </button>
                </div>

                <div class="p-6" style="display: flex; flex-wrap: wrap; gap: 16px;">
                    <template x-for="(item, index) in items" :key="index">
                        <div style="flex: 0 0 calc(50% - 8px);"
                            class="relative p-5 bg-gray-50 rounded-[2rem] border border-gray-200 dark:bg-gray-900/50 dark:border-gray-700">

                            {{-- Item number badge --}}
                            <div class="flex justify-between items-center mb-4">
                                <span class="inline-flex gap-1.5 items-center px-3 py-1 text-xs font-bold rounded-full bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400">
                                    <span class="text-sm material-symbols-outlined">package</span>
                                    طرد #<span x-text="index + 1"></span>
                                </span>
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                    class="p-1.5 text-gray-400 rounded-xl transition-all hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10"
                                    title="حذف الطرد">
                                    <span class="text-lg material-symbols-outlined">delete</span>
                                </button>
                            </div>

                            <div>
                                {{-- رقم السند + اسم المرسل --}}
                                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">رقم السند <span class="text-error-500">*</span></label>
                                        <input type="text" x-model="item.number" placeholder="مثال: 1001"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                        <p x-show="errors['items.' + index + '.number']" x-text="errors['items.' + index + '.number'] ? errors['items.' + index + '.number'][0] : ''" class="mt-1 text-xs text-error-500"></p>
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">اسم المرسل</label>
                                        <input type="text" x-model="item.sender_name" placeholder="اسم المرسل"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                        <p x-show="errors['items.' + index + '.sender_name']" x-text="errors['items.' + index + '.sender_name'] ? errors['items.' + index + '.sender_name'][0] : ''" class="mt-1 text-xs text-error-500"></p>
                                    </div>
                                </div>

                                {{-- اسم المستلم + رقم هاتف المستلم --}}
                                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">اسم المستلم <span class="text-error-500">*</span></label>
                                        <input type="text" x-model="item.receiver_name" placeholder="اسم المستلم"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                        <p x-show="errors['items.' + index + '.receiver_name']" x-text="errors['items.' + index + '.receiver_name'] ? errors['items.' + index + '.receiver_name'][0] : ''" class="mt-1 text-xs text-error-500"></p>
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">رقم هاتف المستلم <span class="text-error-500">*</span></label>
                                        <x-country-select dynamic-name="`items[${index}][receiver_phone]`" model="item.receiver_phone" />
                                        <p x-show="errors['items.' + index + '.receiver_phone']" x-text="errors['items.' + index + '.receiver_phone'] ? errors['items.' + index + '.receiver_phone'][0] : ''" class="mt-1 text-xs text-error-500"></p>
                                    </div>
                                </div>

                                {{-- نوع الطرد + ملاحظات --}}
                                <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">نوع الطرد <span class="text-error-500">*</span></label>
                                        <input type="text" x-model="item.package_type" placeholder="مثال: كرتون، كيس، ظرف"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                        <p x-show="errors['items.' + index + '.package_type']" x-text="errors['items.' + index + '.package_type'] ? errors['items.' + index + '.package_type'][0] : ''" class="mt-1 text-xs text-error-500"></p>
                                    </div>
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">ملاحظات</label>
                                        <input type="text" x-model="item.item_notes" placeholder="ملاحظات إضافية"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:text-gray-400 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                    </div>
                                </div>

                                {{-- Payment Status + Amount --}}
                                <div style="display: flex; gap: 12px; margin-top: 12px; align-items: flex-start;">
                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">حالة الدفع <span class="text-error-500">*</span></label>
                                        <div class="flex gap-2">
                                            {{-- Unpaid --}}
                                            <label class="relative w-full cursor-pointer group">
                                                <input type="radio" value="unpaid" x-model="item.payment_status" @change="item.amount = item.prevAmount || 0" class="sr-only peer">
                                                <div class="flex justify-center items-center px-4 w-full h-11 rounded-2xl border transition-all duration-200"
                                                    :class="item.payment_status === 'unpaid' ? 'border-primary-500 bg-primary-50 text-primary-600 font-bold ring-1 ring-primary-500 shadow-sm dark:bg-primary-500/15 dark:text-primary-400 dark:border-primary-500 dark:ring-primary-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 group-hover:border-primary-300 group-hover:bg-primary-50/50'">
                                                    عند الاستلام
                                                </div>
                                            </label>
                                            {{-- Paid --}}
                                            <label class="relative w-full cursor-pointer group">
                                                <input type="radio" value="paid" x-model="item.payment_status" @change="item.prevAmount = (item.amount > 0 ? item.amount : (item.prevAmount || 0)); item.amount = 0" class="sr-only peer">
                                                <div class="flex justify-center items-center px-4 w-full h-11 rounded-2xl border transition-all duration-200"
                                                    :class="item.payment_status === 'paid' ? 'border-primary-500 bg-primary-50 text-primary-600 font-bold ring-1 ring-primary-500 shadow-sm dark:bg-primary-500/15 dark:text-primary-400 dark:border-primary-500 dark:ring-primary-500' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 group-hover:border-primary-300 group-hover:bg-primary-50/50'">
                                                    مدفوع
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div style="flex: 1;">
                                        <label class="block mb-1 text-xs font-semibold text-gray-600 dark:text-gray-400">المبلغ <span class="text-error-500">*</span></label>
                                        <input type="number" x-model="item.amount" :readonly="item.payment_status === 'paid'"
                                            @input="if(item.payment_status === 'unpaid') item.prevAmount = item.amount"
                                            :class="item.payment_status === 'paid' ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-70 text-gray-500' : 'bg-transparent dark:text-white'"
                                            placeholder="0"
                                            class="px-4 py-2.5 w-full h-11 text-sm rounded-2xl border border-gray-200 transition-all outline-none dark:border-gray-600 dark:bg-gray-900 focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                        <p x-show="errors['items.' + index + '.amount']" x-text="errors['items.' + index + '.amount'] ? errors['items.' + index + '.amount'][0] : ''" class="mt-1 text-xs text-error-500"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty state --}}
                    <div x-show="items.length === 0" style="width: 100%;" class="py-10 text-center text-gray-400 dark:text-gray-500">
                        <span class="mb-2 text-5xl opacity-50 material-symbols-outlined">package</span>
                        <p class="text-sm">لا توجد طرود. اضغط "إضافة طرد" للبدء.</p>
                    </div>
                </div>
            </div>

            {{-- ======================== Submit ======================== --}}
            <div class="flex gap-3 justify-end items-center">
                <a href="{{ route('receipts.index') }}"
                    class="px-6 py-2.5 text-sm font-semibold text-gray-700 bg-white rounded-2xl border border-gray-200 transition-all dark:text-gray-300 dark:bg-boxdark dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                    إلغاء
                </a>
                <button type="submit" :disabled="isSubmitting"
                    class="flex gap-2 items-center px-6 py-2.5 text-sm font-bold text-white rounded-2xl shadow-sm transition-all bg-primary-500 hover:bg-primary-600 hover:shadow-md active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="isSubmitting" class="text-sm animate-spin material-symbols-outlined">progress_activity</span>
                    <span x-text="isSubmitting ? 'جاري التحديث...' : 'تحديث بيان الاستلام'"></span>
                </button>
            </div>
        </form>
    </div>
@endsection