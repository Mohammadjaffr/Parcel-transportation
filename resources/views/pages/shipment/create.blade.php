@extends('layouts.app')
@section('title', 'تسجيل طرد جديد')
@section('Breadcrumb', 'تسجيل طرد جديد')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
        <div x-data="{ activeTab: 'sender' }">

            {{-- شريط التابات --}}
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="flex gap-2">

                    {{-- التاب الأول --}}
                    <button type="button" @click="activeTab = 'sender'"
                        :class="activeTab === 'sender'
                            ?
                            'border-b-2 border-brand-500 text-brand-500 dark:text-brand-400' :
                            'border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                        class="px-4 py-2 text-sm font-medium">
                        مرسل الطرد
                    </button>

                    {{-- التاب الثاني --}}
                    <button type="button" @click="activeTab = 'receiver'"
                        :class="activeTab === 'receiver'
                            ?
                            'border-b-2 border-brand-500 text-brand-500 dark:text-brand-400' :
                            'border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                        class="px-4 py-2 text-sm font-medium">
                        مستلم الطرد
                    </button>

                </nav>
            </div>

            {{-- =================== التاب الأول: مرسل الطرد =================== --}}
            <form x-show="activeTab === 'sender'" x-cloak x-data="{
                payment_method: '{{ old('payment_method', 'prepaid') }}',
                prepaid_method: '{{ old('prepaid_payment_method', 'cash') }}',
                prepaid_reference: '{{ old('prepaid_reference') }}',
                partial_amount: '{{ old('partial_amount') }}',
                partial_method: '{{ old('partial_payment_method', 'cash') }}',
                partial_reference: '{{ old('partial_reference') }}',
                isSubmitting: false,
                showConfirmModal: false,
                isConfirmed: false, // Flag للتحقق من التأكيد
            
                // كائن لتخزين بيانات المودال
                modalData: {
                    sender_name: '',
                    sender_phone: '',
                    no_honey_jars: '',
                    receiver_name: '',
                    receiver_phone: '',
                    receiver_branch: '',
                    no_gallons_honey: '',
                    package_type: '',
                    code: '',
                    total_amount: '',
                    payment_method_text: '',
                    payment_details_text: '',
                    paid_amount: ''
                },
            
                init() {
                    this.$watch('payment_method', value => {
                        if (value !== 'partial_payment') this.partial_amount = '';
                        if (!['prepaid', 'partial_payment'].includes(value)) this.prepaid_method = 'cash';
                        if (value !== 'prepaid') this.prepaid_reference = '';
                        if (value !== 'partial_payment') this.partial_reference = '';
                    });
                },
            
                // دالة التحقق وجمع البيانات قبل فتح المودال
                validateAndShowModal(event) {
                    // إذا تم التأكيد مسبقاً، اسمح بالإرسال
                    if (this.isConfirmed) {
                        return true; // السماح بالإرسال
                    }
            
                    // منع الإرسال لعرض Modal
                    event.preventDefault();
            
                    // التحقق من صحة الحقول (Required)
                    if (!this.$el.checkValidity()) {
                        this.$el.reportValidity();
                        return false;
                    }
            
                    // جمع البيانات وعرض المودال
                    this.collectFormData();
                    this.showConfirmModal = true;
                    return false;
                },
            
                // دالة جمع البيانات من الفورم لعرضها في المودال
                collectFormData() {
                    const formData = new FormData(this.$el);
            
                    // بيانات المرسل
                    this.modalData.sender_name = formData.get('sender_name') || '-';
                    const sHiddenPhone = this.$el.querySelector('input[name=sender_phone]')?.value;
                    this.modalData.sender_phone = sHiddenPhone || formData.get('sender_local_number') || '-';
            
                    this.modalData.no_honey_jars = formData.get('no_honey_jars') || '0';
            
                    // بيانات المستلم
                    this.modalData.receiver_name = formData.get('receiver_name') || '-';
                    const rHiddenPhone = this.$el.querySelector('input[name=receiver_phone]')?.value;
                    this.modalData.receiver_phone = rHiddenPhone || formData.get('receiver_local_number') || '-';
            
                    const branchSelect = this.$el.querySelector('[name=receiver_branch_code]');
                    this.modalData.receiver_branch = branchSelect ? (branchSelect.options[branchSelect.selectedIndex]?.text || '-') : '-';
                    this.modalData.no_gallons_honey = formData.get('no_gallons_honey') || '0';
            
                    // تفاصيل الطرد
                    this.modalData.package_type = formData.get('package_type') || '-';
                    this.modalData.code = formData.get('code') || '-';
                    this.modalData.total_amount = formData.get('total_amount') || '0';
            
                    // الدفع
                    const pMethod = this.payment_method;
                    this.modalData.payment_method_text =
                        pMethod === 'prepaid' ? 'دفع مقدم' :
                        pMethod === 'cod' ? 'دفع عند التسليم (COD)' :
                        pMethod === 'partial_payment' ? 'دفع جزئي' :
                        pMethod === 'customer_credit' ? 'آجل على حساب العميل' : '-';
            
                    if (pMethod === 'prepaid') {
                        this.modalData.payment_details_text = this.prepaid_method === 'cash' ? 'كاش' : 'تحويل بنكي';
                    } else if (pMethod === 'partial_payment') {
                        this.modalData.payment_details_text = this.partial_method === 'cash' ? 'كاش' : 'تحويل بنكي';
                        this.modalData.paid_amount = this.partial_amount;
                    } else {
                        this.modalData.payment_details_text = '';
                        this.modalData.paid_amount = '';
                    }
                },
            
                // الإرسال النهائي
                confirmAndSubmit() {
                    this.showConfirmModal = false;
                    this.isSubmitting = true;
                    this.isConfirmed = true; // تعيين التأكيد
            
                    // إرسال الفورم في الدورة التالية
                    this.$nextTick(() => {
                        // استخدام requestSubmit إذا كان متاحاً
                        if (this.$el.requestSubmit) {
                            this.$el.requestSubmit();
                        } else {
                            // Fallback - إنشاء زر submit والضغط عليه
                            const submitBtn = document.createElement('button');
                            submitBtn.type = 'submit';
                            submitBtn.style.display = 'none';
                            this.$el.appendChild(submitBtn);
                            submitBtn.click();
                            this.$el.removeChild(submitBtn);
                        }
                    });
                }
            }" action="{{ route('shipment.store') }}"
                @submit="validateAndShowModal($event)" method="POST">

                @csrf
                <input type="hidden" name="entry_type" value="sender">
                <input type="hidden" name="active_tab" value="sender">

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">

                    <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}', '{{ old('sender_phone') }}', '{{ old('sender_name') }}', '{{ old('sender_customer_id') }}')">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المرسل</h3>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الجهة
                                من</label>
                            <input type="text" value="{{ auth()->user()->branch->name ?? '' }}"
                                class="px-4 py-2.5 w-full h-11 text-sm bg-gray-100 rounded-lg border dark:text-gray-400 dark:bg-gray-700"
                                disabled>
                            <input type="hidden" name="sender_branch_code" value="{{ auth()->user()->branch_code }}">
                            @error('sender_branch_code')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="sender_customer_id" x-model="selectedId">
                        @error('sender_customer_id')
                            <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                        @enderror

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                            <input type="text" name="sender_name" x-model="selectedName" @input="selectedId=''"
                                value="{{ old('sender_name') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="اسم المرسل">
                            @error('sender_name')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="relative mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                            <div class="flex gap-2" dir="ltr">
                                <div class="relative" @click.outside="openCountry = false">
                                    <button type="button" @click="openCountry = !openCountry"
                                        class="flex gap-2 items-center px-3 py-2.5 h-11 bg-white rounded-lg border border-gray-300 dark:bg-dark-900 dark:border-gray-500 hover:border-brand-500 focus:border-brand-500"
                                        style="min-width: 100px;">
                                        <img :src="`https://flagcdn.com/w20/${countryFlag}.png`"
                                            class="w-5 h-auto rounded-sm">
                                        <span class="text-sm text-gray-700 dark:text-gray-300" x-text="countryCode"></span>
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="openCountry" x-transition
                                        class="overflow-y-auto absolute left-0 top-full z-20 mt-1 w-40 max-h-60 bg-white rounded-lg border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                                        <template x-for="country in countries" :key="country.code">
                                            <button type="button" @click="setCountry(country.code)"
                                                class="flex justify-between items-center px-3 py-2 w-full text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <div class="flex gap-2 items-center">
                                                    <img :src="`https://flagcdn.com/w20/${country.flag}.png`"
                                                        class="w-5 h-auto rounded-sm">
                                                    <span class="text-gray-700 dark:text-gray-300"
                                                        x-text="country.code"></span>
                                                </div>
                                                <span x-show="countryCode === country.code" class="text-brand-500">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="relative flex-1">
                                    <input type="text" x-model="localNumber" @input.debounce.350ms="searchByPhone()"
                                        @focus="openSearchResults = true" @keydown.escape="openSearchResults=false"
                                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                        placeholder="7XXXXXXXX">
                                    <div x-show="openSearchResults && localNumber.length >= 2" x-transition
                                        class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700"
                                        dir="rtl">
                                        <template x-if="loading">
                                            <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                        </template>
                                        <template x-if="!loading && results.length === 0 && localNumber.trim().length >= 2">
                                            <div class="p-3 text-sm text-gray-500">لا توجد نتائج — يمكنك إدخال البيانات
                                                يدويًا</div>
                                        </template>
                                        <template x-for="c in results" :key="c.id">
                                            <button type="button" @click="select(c)"
                                                class="px-4 py-3 w-full text-right hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <div class="text-sm font-semibold text-gray-800 dark:text-white"
                                                    x-text="c.name"></div>
                                                <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="sender_phone" x-model="selectedPhone">
                            @error('sender_phone')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">عدد قروف
                                العسل</label>
                            <input type="number" name="no_honey_jars" value="{{ old('no_honey_jars') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="0">
                            @error('no_honey_jars')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}', '{{ old('receiver_phone') }}', '{{ old('receiver_name') }}', '{{ old('receiver_customer_id') }}')">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المستلم</h3>

                        <input type="hidden" name="receiver_customer_id" x-model="selectedId">
                        @error('receiver_customer_id')
                            <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                        @enderror

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الجهة
                                إلى</label>
                            <select name="receiver_branch_code"
                                class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border dark:text-gray-400 dark:bg-dark-900 dark:border-gray-500">
                                <option value="" {{ old('receiver_branch_code') ? '' : 'selected' }}>اختر الجهة
                                </option>
                                @foreach ($branches as $branch)
                                    @continue($branch->code === auth()->user()->branch_code)
                                    <option value="{{ $branch->code }}"
                                        {{ old('receiver_branch_code') == $branch->code ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('receiver_branch_code')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                            <input type="text" name="receiver_name" x-model="selectedName" @input="selectedId=''"
                                value="{{ old('receiver_name') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="اسم المستلم">
                            @error('receiver_name')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="relative mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                            <div class="flex gap-2" dir="ltr">
                                <div class="relative" @click.outside="openCountry = false">
                                    <button type="button" @click="openCountry = !openCountry"
                                        class="flex gap-2 items-center px-3 py-2.5 h-11 bg-white rounded-lg border border-gray-300 dark:bg-dark-900 dark:border-gray-500 hover:border-brand-500 focus:border-brand-500"
                                        style="min-width: 100px;">
                                        <img :src="`https://flagcdn.com/w20/${countryFlag}.png`"
                                            class="w-5 h-auto rounded-sm">
                                        <span class="text-sm text-gray-700 dark:text-gray-300"
                                            x-text="countryCode"></span>
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="openCountry" x-transition
                                        class="overflow-y-auto absolute left-0 top-full z-20 mt-1 w-40 max-h-60 bg-white rounded-lg border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                                        <template x-for="country in countries" :key="country.code">
                                            <button type="button" @click="setCountry(country.code)"
                                                class="flex justify-between items-center px-3 py-2 w-full text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <div class="flex gap-2 items-center">
                                                    <img :src="`https://flagcdn.com/w20/${country.flag}.png`"
                                                        class="w-5 h-auto rounded-sm">
                                                    <span class="text-gray-700 dark:text-gray-300"
                                                        x-text="country.code"></span>
                                                </div>
                                                <span x-show="countryCode === country.code"
                                                    class="text-brand-500">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="relative flex-1">
                                    <input type="text" x-model="localNumber" @input.debounce.350ms="searchByPhone()"
                                        @focus="openSearchResults = true" @keydown.escape="openSearchResults=false"
                                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                        placeholder="7XXXXXXXX">
                                    <div x-show="openSearchResults && localNumber.length >= 2" x-transition
                                        class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700"
                                        dir="rtl">
                                        <template x-if="loading">
                                            <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                        </template>
                                        <template
                                            x-if="!loading && results.length === 0 && localNumber.trim().length >= 2">
                                            <div class="p-3 text-sm text-gray-500">لا توجد نتائج — يمكنك إدخال البيانات
                                                يدويًا</div>
                                        </template>
                                        <template x-for="c in results" :key="c.id">
                                            <button type="button" @click="select(c)"
                                                class="px-4 py-3 w-full text-right hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <div class="text-sm font-semibold text-gray-800 dark:text-white"
                                                    x-text="c.name"></div>
                                                <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="receiver_phone" x-model="selectedPhone">
                            @error('receiver_phone')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">عدد جوالين
                                العسل</label>
                            <input type="number" name="no_gallons_honey" value="{{ old('no_gallons_honey') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="0">
                            @error('no_gallons_honey')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2 xl:grid-cols-2">
                    <div class="space-y-4 w-full md:col-span-2">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">تفاصيل الطرد</h3>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">نوع
                                الطرد</label>
                            <input type="text" name="package_type" value="{{ old('package_type') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="مثال: كرتون / شنطة / ...">
                            @error('package_type')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">إجمالي
                                المبلغ</label>
                            <input type="number" name="total_amount" value="{{ old('total_amount') }}" step="0.01"
                                min="0"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="0.00">
                            @error('total_amount')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الرمز</label>
                            <input type="text" name="code" value="{{ old('code') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="مثال: QWE">
                            @error('code')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-2 md:col-span-2">
                        <h3 class="my-6 mb-3 text-sm font-bold text-gray-700 dark:text-gray-400">طريقة الدفع</h3>
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-wrap gap-6">
                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="prepaid"
                                        @change="payment_method='prepaid'"
                                        {{ old('payment_method', 'prepaid') == 'prepaid' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'prepaid' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'prepaid' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع مقدم
                                </label>
                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="cod"
                                        @change="payment_method='cod'"
                                        {{ old('payment_method') == 'cod' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'cod' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'cod' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع عند التسليم (COD)
                                </label>
                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="partial_payment"
                                        @change="payment_method='partial_payment'"
                                        {{ old('payment_method') == 'partial_payment' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'partial_payment' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'partial_payment' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع جزئي
                                </label>
                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="customer_credit"
                                        @change="payment_method='customer_credit'"
                                        {{ old('payment_method') == 'customer_credit' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'customer_credit' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'customer_credit' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    آجل على حساب العميل
                                </label>
                            </div>
                            @error('payment_method')
                                <div class="text-sm text-error-500">{{ $message }}</div>
                            @enderror

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method === 'prepaid'" x-transition>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">طريقة الدفع
                                    (للدفع المقدم)</label>
                                <div class="flex flex-wrap gap-6">
                                    <label
                                        class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                        <input class="sr-only" type="radio" name="prepaid_payment_method"
                                            value="cash" x-model="prepaid_method">
                                        <span
                                            :class="prepaid_method === 'cash' ? 'border-brand-500 bg-brand-500' :
                                                'bg-transparent border-gray-300 dark:border-gray-700'"
                                            class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                            <span :class="prepaid_method === 'cash' ? 'block' : 'hidden'"
                                                class="w-2 h-2 bg-white rounded-full"></span>
                                        </span>
                                        كاش
                                    </label>
                                    <label
                                        class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                        <input class="sr-only" type="radio" name="prepaid_payment_method"
                                            value="bank_transfer" x-model="prepaid_method">
                                        <span
                                            :class="prepaid_method === 'bank_transfer' ? 'border-brand-500 bg-brand-500' :
                                                'bg-transparent border-gray-300 dark:border-gray-700'"
                                            class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                            <span :class="prepaid_method === 'bank_transfer' ? 'block' : 'hidden'"
                                                class="w-2 h-2 bg-white rounded-full"></span>
                                        </span>
                                        تحويل بنكي
                                    </label>
                                </div>
                                <template x-if="prepaid_method === 'bank_transfer'">
                                    <div class="mt-4">
                                        <label
                                            class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">رقم
                                            الإيداع</label>
                                        <input type="text" name="prepaid_reference" x-model="prepaid_reference"
                                            placeholder="أدخل رقم الإيداع"
                                            class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">
                                    </div>
                                </template>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method === 'cod'" x-transition>
                                <div class="text-sm text-gray-700 dark:text-gray-300">سيتم اعتبار مبلغ التحصيل عند التسليم
                                    = <span class="font-semibold">إجمالي المبلغ</span>.</div>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method==='partial_payment'" x-transition>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">المبلغ
                                    المدفوع من المرسل الآن</label>
                                <input type="number" name="partial_amount" x-model="partial_amount"
                                    :disabled="payment_method !== 'partial_payment'"
                                    :required="payment_method === 'partial_payment'" min="0.01" step="0.01"
                                    placeholder="0.00"
                                    class="px-4 py-2 w-full text-gray-700 bg-white rounded-lg border border-gray-300 dark:border-gray-400 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500">

                                <div class="mt-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">طريقة
                                        الدفع (للدفع الجزئي)</label>
                                    <div class="flex flex-wrap gap-6">
                                        <label
                                            class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                            <input class="sr-only" type="radio" name="partial_payment_method"
                                                value="cash" x-model="partial_method">
                                            <span
                                                :class="partial_method === 'cash' ? 'border-brand-500 bg-brand-500' :
                                                    'bg-transparent border-gray-300 dark:border-gray-700'"
                                                class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                                <span :class="partial_method === 'cash' ? 'block' : 'hidden'"
                                                    class="w-2 h-2 bg-white rounded-full"></span>
                                            </span>
                                            كاش
                                        </label>
                                        <label
                                            class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                            <input class="sr-only" type="radio" name="partial_payment_method"
                                                value="bank_transfer" x-model="partial_method">
                                            <span
                                                :class="partial_method === 'bank_transfer' ? 'border-brand-500 bg-brand-500' :
                                                    'bg-transparent border-gray-300 dark:border-gray-700'"
                                                class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                                <span :class="partial_method === 'bank_transfer' ? 'block' : 'hidden'"
                                                    class="w-2 h-2 bg-white rounded-full"></span>
                                            </span>
                                            تحويل بنكي
                                        </label>
                                    </div>
                                    <template x-if="partial_method === 'bank_transfer'">
                                        <div class="mt-4">
                                            <label
                                                class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">رقم
                                                الإيداع</label>
                                            <input type="text" name="partial_reference" x-model="partial_reference"
                                                placeholder="أدخل رقم الإيداع"
                                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method==='customer_credit'" x-transition>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">حالة
                                    مديونية العميل</label>
                                <select name="customer_debt_status"
                                    class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border dark:text-gray-400 dark:bg-dark-900 dark:border-gray-500">
                                    <option value="pending" @selected(old('customer_debt_status', 'pending') == 'pending')>قيد الانتظار</option>
                                    <option value="overdue" @selected(old('customer_debt_status') == 'overdue')>مديون</option>
                                    <option value="partially_paid" @selected(old('customer_debt_status') == 'partially_paid')>مدفوع جزئيا</option>
                                    <option value="fully_paid" @selected(old('customer_debt_status') == 'fully_paid')>مدفوع بالكامل</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الملاحظات</label>
                    <textarea placeholder="اكتب ملاحظاتك..." rows="4" name="notes"
                        class="px-4 py-2.5 w-full h-auto text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 resize-none hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">{{ old('notes') }}</textarea>
                </div>

                <div class="mt-6">
                    <button type="submit" :disabled="isSubmitting"
                        class="flex justify-center items-center px-4 py-2 w-full font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-500 md:w-auto disabled:opacity-75 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">تسجيل الطرد</span>
                        <span x-show="isSubmitting" class="flex gap-2 items-center">
                            <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            جاري التسجيل...
                        </span>
                    </button>
                </div>

                <div x-show="showConfirmModal"
                    class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 z-99999 modal" x-cloak
                    x-transition style="display: none;">
                    <div class="fixed inset-0 w-full h-full bg-gray-400/50 backdrop-blur-[32px]"
                        @click="showConfirmModal = false"></div>
                    <div @click.away="showConfirmModal = false"
                        class="relative w-full max-w-[600px] rounded-3xl bg-white dark:bg-gray-900 p-6 lg:p-10">

                        <!-- Header -->
                        <div
                            class="p-8 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-white/[0.02]">
                            <div>
                                <h3 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">تأكيد بيانات
                                    الشحنة</h3>
                                <p class="mt-1 text-xs font-black tracking-widest uppercase text-brand-500">يرجى المراجعة
                                    قبل الإرسال</p>
                            </div>
                            <button type="button" @click="showConfirmModal = false"
                                class="flex justify-center items-center w-12 h-12 text-gray-400 bg-white rounded-2xl shadow-sm transition-all dark:bg-gray-800 hover:text-error-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12" stroke-width="3" />
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-8 space-y-4">
                            <div class="overflow-y-auto pr-2 space-y-3 max-h-96">

                                <!-- بيانات المرسل -->
                                <div
                                    class="p-4 bg-gray-50 dark:bg-white/[0.03] rounded-2xl border-2 border-transparent hover:border-brand-500/20 transition-all">
                                    <h4
                                        class="mb-3 text-sm font-black tracking-wider text-gray-800 uppercase dark:text-white">
                                        بيانات المرسل</h4>
                                    <div class="space-y-2 text-sm">
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">الاسم:</span>
                                            <span class="font-medium" x-text="modalData.sender_name"></span>
                                        </p>
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">الهاتف:</span>
                                            <span class="font-medium" x-text="modalData.sender_phone"></span>
                                        </p>
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">عدد قروف
                                                العسل:</span> <span class="font-medium"
                                                x-text="modalData.no_honey_jars"></span></p>
                                    </div>
                                </div>

                                <!-- بيانات المستلم -->
                                <div
                                    class="p-4 bg-gray-50 dark:bg-white/[0.03] rounded-2xl border-2 border-transparent hover:border-brand-500/20 transition-all">
                                    <h4
                                        class="mb-3 text-sm font-black tracking-wider text-gray-800 uppercase dark:text-white">
                                        بيانات المستلم</h4>
                                    <div class="space-y-2 text-sm">
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">الاسم:</span>
                                            <span class="font-medium" x-text="modalData.receiver_name"></span>
                                        </p>
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">الهاتف:</span>
                                            <span class="font-medium" x-text="modalData.receiver_phone"></span>
                                        </p>
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">الجهة:</span>
                                            <span class="font-medium" x-text="modalData.receiver_branch"></span>
                                        </p>
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">عدد جوالين
                                                العسل:</span> <span class="font-medium"
                                                x-text="modalData.no_gallons_honey"></span></p>
                                    </div>
                                </div>

                                <!-- تفاصيل الطرد -->
                                <div
                                    class="p-4 bg-gray-50 dark:bg-white/[0.03] rounded-2xl border-2 border-transparent hover:border-brand-500/20 transition-all">
                                    <h4
                                        class="mb-3 text-sm font-black tracking-wider text-gray-800 uppercase dark:text-white">
                                        تفاصيل الطرد</h4>
                                    <div class="space-y-2 text-sm">
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">نوع
                                                الطرد:</span> <span class="font-medium"
                                                x-text="modalData.package_type"></span></p>
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">الرمز:</span>
                                            <span class="font-medium" x-text="modalData.code"></span>
                                        </p>
                                        <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">إجمالي
                                                المبلغ:</span> <span class="font-medium text-brand-500"
                                                x-text="modalData.total_amount + ' ر.ي'"></span></p>
                                    </div>
                                </div>

                                <!-- طريقة الدفع -->
                                <div
                                    class="p-4 bg-gray-50 dark:bg-white/[0.03] rounded-2xl border-2 border-transparent hover:border-brand-500/20 transition-all">
                                    <h4
                                        class="mb-3 text-sm font-black tracking-wider text-gray-800 uppercase dark:text-white">
                                        طريقة الدفع</h4>
                                    <div class="space-y-2 text-sm">
                                        <p class="text-gray-700 dark:text-gray-300"><span
                                                class="font-bold">الطريقة:</span> <span class="font-medium"
                                                x-text="modalData.payment_method_text"></span></p>
                                        <template x-if="modalData.payment_details_text">
                                            <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">وسيلة
                                                    الدفع:</span> <span class="font-medium"
                                                    x-text="modalData.payment_details_text"></span></p>
                                        </template>
                                        <template x-if="modalData.paid_amount">
                                            <p class="text-gray-700 dark:text-gray-300"><span class="font-bold">المبلغ
                                                    المدفوع:</span> <span class="font-medium text-brand-500"
                                                    x-text="modalData.paid_amount + ' ر.ي'"></span></p>
                                        </template>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="p-8 bg-gray-50/50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex gap-3">
                            <button type="button" @click="confirmAndSubmit()"
                                class="flex flex-1 gap-3 justify-center items-center h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/25 active:scale-95">
                                تأكيد وإرسال
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button type="button" @click="showConfirmModal = false"
                                class="px-6 h-14 font-bold text-gray-700 bg-white rounded-2xl border-2 border-gray-200 transition-all dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600">
                                إلغاء
                            </button>
                        </div>

                    </div>
                </div>
            </form>

            {{-- =================== التاب الثاني: مستلم الطرد =================== --}}
            <form x-show="activeTab === 'receiver'" x-cloak x-data="{
                payment_method: '{{ old('payment_method', 'prepaid') }}',
                prepaid_method: '{{ old('prepaid_payment_method', 'cash') }}',
                prepaid_reference: '{{ old('prepaid_reference') }}',
                partial_amount: '{{ old('partial_amount') }}',
                partial_method: '{{ old('partial_payment_method', 'cash') }}',
                partial_reference: '{{ old('partial_reference') }}',
                isSubmitting: false,
                showConfirmModal: false,
            
                modalData: {
                    sender_name: '',
                    sender_phone: '',
                    sender_branch: '',
                    no_honey_jars: '',
                    receiver_name: '',
                    receiver_phone: '',
                    no_gallons_honey: '',
                    package_type: '',
                    code: '',
                    total_amount: '',
                    payment_method_text: '',
                    payment_details_text: '',
                    paid_amount: ''
                },
            
                init() {
                    this.$watch('payment_method', value => {
                        if (value !== 'partial_payment') this.partial_amount = '';
                        if (!['prepaid', 'partial_payment'].includes(value)) this.prepaid_method = 'cash';
                        if (value !== 'prepaid') this.prepaid_reference = '';
                        if (value !== 'partial_payment') this.partial_reference = '';
                    });
                },
            
                validateAndShowModal(event) {
                    if (!this.$el.checkValidity()) {
                        this.$el.reportValidity();
                        return;
                    }
                    event.preventDefault();
                    this.collectFormData();
                    this.showConfirmModal = true;
                },
            
                collectFormData() {
                    const formData = new FormData(this.$el);
            
                    // بيانات المرسل
                    this.modalData.sender_name = formData.get('sender_name') || '-';
                    const sHiddenPhone = this.$el.querySelector('input[name=sender_phone]')?.value;
                    this.modalData.sender_phone = sHiddenPhone || formData.get('sender_local_number') || '-';
            
                    const branchSelect = this.$el.querySelector('[name=sender_branch_code]');
                    this.modalData.sender_branch = branchSelect ? (branchSelect.options[branchSelect.selectedIndex]?.text || '-') : '-';
                    this.modalData.no_honey_jars = formData.get('no_honey_jars') || '0';
            
                    // بيانات المستلم
                    this.modalData.receiver_name = formData.get('receiver_name') || '-';
                    const rHiddenPhone = this.$el.querySelector('input[name=receiver_phone]')?.value;
                    this.modalData.receiver_phone = rHiddenPhone || formData.get('receiver_local_number') || '-';
                    this.modalData.no_gallons_honey = formData.get('no_gallons_honey') || '0';
            
                    // تفاصيل الطرد
                    this.modalData.package_type = formData.get('package_type') || '-';
                    this.modalData.code = formData.get('code') || '-';
                    this.modalData.total_amount = formData.get('total_amount') || '0';
            
                    const pMethod = this.payment_method;
                    this.modalData.payment_method_text =
                        pMethod === 'prepaid' ? 'دفع مقدم' :
                        pMethod === 'cod' ? 'دفع عند التسليم (COD)' :
                        pMethod === 'partial_payment' ? 'دفع جزئي' :
                        pMethod === 'customer_credit' ? 'آجل على حساب العميل' : '-';
            
                    if (pMethod === 'prepaid') {
                        this.modalData.payment_details_text = this.prepaid_method === 'cash' ? 'كاش' : 'تحويل بنكي';
                    } else if (pMethod === 'partial_payment') {
                        this.modalData.payment_details_text = this.partial_method === 'cash' ? 'كاش' : 'تحويل بنكي';
                        this.modalData.paid_amount = this.partial_amount;
                    } else {
                        this.modalData.payment_details_text = '';
                        this.modalData.paid_amount = '';
                    }
                },
            
                confirmAndSubmit() {
                    this.showConfirmModal = false;
                    this.isSubmitting = true;
                    this.$el.submit();
                }
            }"
                action="{{ route('shipment.store') }}" @submit="validateAndShowModal($event)" method="POST"
                enctype="multipart/form-data">

                @csrf
                <input type="hidden" name="entry_type" value="receiver">
                <input type="hidden" name="active_tab" value="receiver">

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">

                    <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}', '{{ old('sender_phone') }}', '{{ old('sender_name') }}', '{{ old('sender_customer_id') }}')">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المرسل</h3>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الجهة
                                من</label>
                            <select name="sender_branch_code"
                                class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border dark:text-gray-400 dark:bg-dark-900 dark:border-gray-500">
                                <option value="" {{ old('sender_branch_code') ? '' : 'selected' }}>اختر الجهة
                                </option>
                                @foreach ($branches as $branch)
                                    @continue($branch->code === auth()->user()->branch_code)
                                    <option value="{{ $branch->code }}"
                                        {{ old('sender_branch_code') == $branch->code ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <input type="hidden" name="sender_customer_id" x-model="selectedId">
                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                            <input type="text" name="sender_name" x-model="selectedName" @input="selectedId=''"
                                value="{{ old('sender_name') }}" required
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="اسم المرسل">
                        </div>

                        <div class="relative mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                            <div class="flex gap-2" dir="ltr">
                                <div class="relative" @click.outside="openCountry = false">
                                    <button type="button" @click="openCountry = !openCountry"
                                        class="flex gap-2 items-center px-3 py-2.5 h-11 bg-white rounded-lg border border-gray-300 dark:bg-dark-900 dark:border-gray-500 hover:border-brand-500 focus:border-brand-500"
                                        style="min-width: 100px;">
                                        <img :src="`https://flagcdn.com/w20/${countryFlag}.png`"
                                            class="w-5 h-auto rounded-sm">
                                        <span class="text-sm text-gray-700 dark:text-gray-300"
                                            x-text="countryCode"></span>
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="openCountry" x-transition
                                        class="overflow-y-auto absolute left-0 top-full z-20 mt-1 w-40 max-h-60 bg-white rounded-lg border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                                        <template x-for="country in countries" :key="country.code">
                                            <button type="button" @click="setCountry(country.code)"
                                                class="flex justify-between items-center px-3 py-2 w-full text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <div class="flex gap-2 items-center">
                                                    <img :src="`https://flagcdn.com/w20/${country.flag}.png`"
                                                        class="w-5 h-auto rounded-sm">
                                                    <span class="text-gray-700 dark:text-gray-300"
                                                        x-text="country.code"></span>
                                                </div>
                                                <span x-show="countryCode === country.code"
                                                    class="text-brand-500">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="relative flex-1">
                                    <input type="text" x-model="localNumber" @input.debounce.350ms="searchByPhone()"
                                        @focus="openSearchResults = true" @keydown.escape="openSearchResults=false"
                                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                        placeholder="7XXXXXXXX">
                                    <div x-show="openSearchResults && localNumber.length >= 2" x-transition
                                        class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700"
                                        dir="rtl">
                                        <template x-if="loading">
                                            <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                        </template>
                                        <template
                                            x-if="!loading && results.length === 0 && localNumber.trim().length >= 2">
                                            <div class="p-3 text-sm text-gray-500">لا توجد نتائج — يمكنك إدخال البيانات
                                                يدويًا</div>
                                        </template>
                                        <template x-for="c in results" :key="c.id">
                                            <button type="button" @click="select(c)"
                                                class="px-4 py-3 w-full text-right hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <div class="text-sm font-semibold text-gray-800 dark:text-white"
                                                    x-text="c.name"></div>
                                                <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="sender_phone" x-model="selectedPhone">
                            @error('sender_phone')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">عدد قروف
                                العسل</label>
                            <input type="number" name="no_honey_jars" value="{{ old('no_honey_jars') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}', '{{ old('receiver_phone') }}', '{{ old('receiver_name') }}', '{{ old('receiver_customer_id') }}')">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المستلم</h3>

                        <input type="hidden" name="receiver_customer_id" x-model="selectedId">

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الجهة
                                إلى</label>
                            <input type="text" value="{{ auth()->user()->branch->name ?? '' }}"
                                class="px-4 py-2.5 w-full h-11 text-sm bg-gray-100 rounded-lg border dark:text-gray-400 dark:bg-gray-700"
                                disabled>
                            <input type="hidden" name="receiver_branch_code" value="{{ auth()->user()->branch_code }}">
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                            <input type="text" name="receiver_name" x-model="selectedName" @input="selectedId=''"
                                value="{{ old('receiver_name') }}" required
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="اسم المستلم">
                        </div>

                        <div class="relative mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                            <div class="flex gap-2" dir="ltr">
                                <div class="relative" @click.outside="openCountry = false">
                                    <button type="button" @click="openCountry = !openCountry"
                                        class="flex gap-2 items-center px-3 py-2.5 h-11 bg-white rounded-lg border border-gray-300 dark:bg-dark-900 dark:border-gray-500 hover:border-brand-500 focus:border-brand-500"
                                        style="min-width: 100px;">
                                        <img :src="`https://flagcdn.com/w20/${countryFlag}.png`"
                                            class="w-5 h-auto rounded-sm">
                                        <span class="text-sm text-gray-700 dark:text-gray-300"
                                            x-text="countryCode"></span>
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="openCountry" x-transition
                                        class="overflow-y-auto absolute left-0 top-full z-20 mt-1 w-40 max-h-60 bg-white rounded-lg border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                                        <template x-for="country in countries" :key="country.code">
                                            <button type="button" @click="setCountry(country.code)"
                                                class="flex justify-between items-center px-3 py-2 w-full text-sm text-left hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <div class="flex gap-2 items-center">
                                                    <img :src="`https://flagcdn.com/w20/${country.flag}.png`"
                                                        class="w-5 h-auto rounded-sm">
                                                    <span class="text-gray-700 dark:text-gray-300"
                                                        x-text="country.code"></span>
                                                </div>
                                                <span x-show="countryCode === country.code"
                                                    class="text-brand-500">✓</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="relative flex-1">
                                    <input type="text" x-model="localNumber" @input.debounce.350ms="searchByPhone()"
                                        @focus="openSearchResults = true" @keydown.escape="openSearchResults=false"
                                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                        placeholder="7XXXXXXXX">
                                    <div x-show="openSearchResults && localNumber.length >= 2" x-transition
                                        class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700"
                                        dir="rtl">
                                        <template x-if="loading">
                                            <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                        </template>
                                        <template
                                            x-if="!loading && results.length === 0 && localNumber.trim().length >= 2">
                                            <div class="p-3 text-sm text-gray-500">لا توجد نتائج — يمكنك إدخال البيانات
                                                يدويًا</div>
                                        </template>
                                        <template x-for="c in results" :key="c.id">
                                            <button type="button" @click="select(c)"
                                                class="px-4 py-3 w-full text-right hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <div class="text-sm font-semibold text-gray-800 dark:text-white"
                                                    x-text="c.name"></div>
                                                <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="receiver_phone" x-model="selectedPhone">
                            @error('receiver_phone')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">عدد جوالين
                                العسل</label>
                            <input type="number" name="no_gallons_honey" value="{{ old('no_gallons_honey') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2 xl:grid-cols-2">
                    <div class="space-y-4 w-full md:col-span-2">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">تفاصيل الطرد</h3>
                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">نوع
                                الطرد</label>
                            <input type="text" name="package_type" value="{{ old('package_type') }}" required
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="مثال: كرتون / شنطة / ...">
                        </div>
                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الرمز</label>
                            <input type="text" name="code" value="{{ old('code') }}" required
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="مثال: QWE">
                        </div>
                        <div class="mt-3">
                            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">إجمالي
                                المبلغ</label>
                            <input type="number" name="total_amount" value="{{ old('total_amount') }}" step="0.01"
                                min="0" required
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="0.00">
                        </div>
                    </div>

                    <div class="mt-2 md:col-span-2">
                        <h3 class="my-6 mb-3 text-sm font-bold text-gray-700 dark:text-gray-400">طريقة الدفع</h3>
                        <div class="flex flex-col gap-4">
                            <div class="flex flex-wrap gap-6">
                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="prepaid"
                                        @change="payment_method='prepaid'"
                                        {{ old('payment_method', 'prepaid') == 'prepaid' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'prepaid' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'prepaid' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع مقدم
                                </label>
                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="cod"
                                        @change="payment_method='cod'"
                                        {{ old('payment_method') == 'cod' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'cod' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'cod' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع عند التسليم (COD)
                                </label>
                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="partial_payment"
                                        @change="payment_method='partial_payment'"
                                        {{ old('payment_method') == 'partial_payment' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'partial_payment' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'partial_payment' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    دفع جزئي
                                </label>
                                <label
                                    class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                    <input class="sr-only" type="radio" name="payment_method" value="customer_credit"
                                        @change="payment_method='customer_credit'"
                                        {{ old('payment_method') == 'customer_credit' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'customer_credit' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'customer_credit' ? 'block' : 'hidden'"
                                            class="w-2 h-2 bg-white rounded-full"></span>
                                    </span>
                                    آجل على حساب العميل
                                </label>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method === 'prepaid'" x-transition>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">طريقة الدفع
                                    (للدفع المقدم)</label>
                                <div class="flex flex-wrap gap-6">
                                    <label
                                        class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                        <input class="sr-only" type="radio" name="prepaid_payment_method"
                                            value="cash" x-model="prepaid_method">
                                        <span
                                            :class="prepaid_method === 'cash' ? 'border-brand-500 bg-brand-500' :
                                                'bg-transparent border-gray-300 dark:border-gray-700'"
                                            class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                            <span :class="prepaid_method === 'cash' ? 'block' : 'hidden'"
                                                class="w-2 h-2 bg-white rounded-full"></span>
                                        </span>
                                        كاش
                                    </label>
                                    <label
                                        class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                        <input class="sr-only" type="radio" name="prepaid_payment_method"
                                            value="bank_transfer" x-model="prepaid_method">
                                        <span
                                            :class="prepaid_method === 'bank_transfer' ? 'border-brand-500 bg-brand-500' :
                                                'bg-transparent border-gray-300 dark:border-gray-700'"
                                            class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                            <span :class="prepaid_method === 'bank_transfer' ? 'block' : 'hidden'"
                                                class="w-2 h-2 bg-white rounded-full"></span>
                                        </span>
                                        تحويل بنكي
                                    </label>
                                </div>
                                <template x-if="prepaid_method === 'bank_transfer'">
                                    <div class="mt-4">
                                        <label
                                            class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">رقم
                                            الإيداع</label>
                                        <input type="text" name="prepaid_reference" x-model="prepaid_reference"
                                            placeholder="أدخل رقم الإيداع"
                                            class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">
                                    </div>
                                </template>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method === 'cod'" x-transition>
                                <div class="text-sm text-gray-700 dark:text-gray-300">سيتم اعتبار مبلغ التحصيل عند التسليم
                                    = <span class="font-semibold">إجمالي المبلغ</span>.</div>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method==='partial_payment'" x-transition>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">المبلغ
                                    المدفوع من المرسل الآن</label>
                                <input type="number" name="partial_amount" x-model="partial_amount"
                                    :disabled="payment_method !== 'partial_payment'"
                                    :required="payment_method === 'partial_payment'" min="0.01" step="0.01"
                                    placeholder="0.00"
                                    class="px-4 py-2 w-full text-gray-700 bg-white rounded-lg border border-gray-300 dark:border-gray-400 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500">

                                <div class="mt-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">طريقة
                                        الدفع (للدفع الجزئي)</label>
                                    <div class="flex flex-wrap gap-6">
                                        <label
                                            class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                            <input class="sr-only" type="radio" name="partial_payment_method"
                                                value="cash" x-model="partial_method">
                                            <span
                                                :class="partial_method === 'cash' ? 'border-brand-500 bg-brand-500' :
                                                    'bg-transparent border-gray-300 dark:border-gray-700'"
                                                class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                                <span :class="partial_method === 'cash' ? 'block' : 'hidden'"
                                                    class="w-2 h-2 bg-white rounded-full"></span>
                                            </span>
                                            كاش
                                        </label>
                                        <label
                                            class="flex relative gap-3 items-center text-sm font-medium cursor-pointer select-none">
                                            <input class="sr-only" type="radio" name="partial_payment_method"
                                                value="bank_transfer" x-model="partial_method">
                                            <span
                                                :class="partial_method === 'bank_transfer' ? 'border-brand-500 bg-brand-500' :
                                                    'bg-transparent border-gray-300 dark:border-gray-700'"
                                                class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                                <span :class="partial_method === 'bank_transfer' ? 'block' : 'hidden'"
                                                    class="w-2 h-2 bg-white rounded-full"></span>
                                            </span>
                                            تحويل بنكي
                                        </label>
                                    </div>
                                    <template x-if="partial_method === 'bank_transfer'">
                                        <div class="mt-4">
                                            <label
                                                class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">رقم
                                                الإيداع</label>
                                            <input type="text" name="partial_reference" x-model="partial_reference"
                                                placeholder="أدخل رقم الإيداع"
                                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method==='customer_credit'" x-transition>
                                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">حالة
                                    مديونية العميل</label>
                                <select name="customer_debt_status"
                                    class="px-4 py-2.5 w-full h-11 text-sm rounded-lg border dark:text-gray-400 dark:bg-dark-900 dark:border-gray-500">
                                    <option value="pending" @selected(old('customer_debt_status', 'pending') == 'pending')>قيد الانتظار</option>
                                    <option value="overdue" @selected(old('customer_debt_status') == 'overdue')>مديون</option>
                                    <option value="partially_paid" @selected(old('customer_debt_status') == 'partially_paid')>مدفوع جزئيا</option>
                                    <option value="fully_paid" @selected(old('customer_debt_status') == 'fully_paid')>مدفوع بالكامل</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" :disabled="isSubmitting"
                        class="flex justify-center items-center px-4 py-2 w-full font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-500 md:w-auto disabled:opacity-75 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">تسجيل الطرد</span>
                        <span x-show="isSubmitting" class="flex gap-2 items-center">
                            <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            جاري التسجيل...
                        </span>
                    </button>
                </div>

                <div x-show="showConfirmModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="overflow-y-auto fixed inset-0 z-50" style="display: none;"
                    @click.self="showConfirmModal = false">
                    <div class="flex justify-center items-center px-4 pt-4 pb-20 min-h-screen text-center sm:block sm:p-0">
                        <div
                            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity dark:bg-gray-900 dark:bg-opacity-75">
                        </div>
                        <div x-show="showConfirmModal"
                            class="inline-block overflow-hidden text-right align-bottom bg-white rounded-lg shadow-xl transition-all transform dark:bg-gray-800 sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                            <div class="px-4 pt-5 pb-4 bg-white dark:bg-gray-800 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="flex flex-shrink-0 justify-center items-center mx-auto w-12 h-12 bg-blue-100 rounded-full sm:mx-0 sm:h-10 sm:w-10 dark:bg-blue-900">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 w-full text-center sm:mt-0 sm:mr-4 sm:text-right">
                                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">تأكيد
                                            بيانات الشحنة</h3>
                                        <div class="mt-4">
                                            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">يرجى مراجعة البيانات
                                                التالية قبل التسجيل النهائي:</p>
                                            <div class="overflow-y-auto space-y-4 max-h-96">

                                                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                                                    <h4 class="mb-2 font-semibold text-gray-800 dark:text-white">بيانات
                                                        المرسل</h4>
                                                    <div class="space-y-1 text-sm">
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">الجهة:</span> <span
                                                                x-text="modalData.sender_branch"></span>
                                                        </p>
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">الاسم:</span> <span
                                                                x-text="modalData.sender_name"></span>
                                                        </p>
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">الهاتف:</span> <span
                                                                x-text="modalData.sender_phone"></span>
                                                        </p>
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">عدد قروف العسل:</span> <span
                                                                x-text="modalData.no_honey_jars"></span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                                                    <h4 class="mb-2 font-semibold text-gray-800 dark:text-white">بيانات
                                                        المستلم</h4>
                                                    <div class="space-y-1 text-sm">
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">الاسم:</span> <span
                                                                x-text="modalData.receiver_name"></span>
                                                        </p>
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">الهاتف:</span> <span
                                                                x-text="modalData.receiver_phone"></span>
                                                        </p>
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">عدد جوالين العسل:</span> <span
                                                                x-text="modalData.no_gallons_honey"></span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                                                    <h4 class="mb-2 font-semibold text-gray-800 dark:text-white">تفاصيل
                                                        الطرد</h4>
                                                    <div class="space-y-1 text-sm">
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">نوع الطرد:</span> <span
                                                                x-text="modalData.package_type"></span>
                                                        </p>
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">الرمز:</span> <span
                                                                x-text="modalData.code"></span>
                                                        </p>
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">إجمالي المبلغ:</span> <span
                                                                x-text="modalData.total_amount"></span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                                                    <h4 class="mb-2 font-semibold text-gray-800 dark:text-white">طريقة
                                                        الدفع</h4>
                                                    <div class="space-y-1 text-sm">
                                                        <p class="text-gray-700 dark:text-gray-300">
                                                            <span class="font-medium">الطريقة:</span> <span
                                                                x-text="modalData.payment_method_text"></span>
                                                        </p>
                                                        <template x-if="modalData.payment_details_text">
                                                            <p class="text-gray-700 dark:text-gray-300">
                                                                <span class="font-medium">وسيلة الدفع:</span> <span
                                                                    x-text="modalData.payment_details_text"></span>
                                                            </p>
                                                        </template>
                                                        <template x-if="modalData.paid_amount">
                                                            <p class="text-gray-700 dark:text-gray-300">
                                                                <span class="font-medium">المبلغ المدفوع:</span> <span
                                                                    x-text="modalData.paid_amount"></span>
                                                            </p>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" @click="confirmAndSubmit()"
                                    class="inline-flex justify-center px-4 py-2 w-full text-base font-medium text-white rounded-md border border-transparent shadow-sm bg-brand-500 hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    تأكيد وإرسال
                                </button>
                                <button type="button" @click="showConfirmModal = false"
                                    class="inline-flex justify-center px-4 py-2 mt-3 w-full text-base font-medium text-gray-700 bg-white rounded-md border border-gray-300 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 sm:mt-0 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                    إلغاء
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customerPicker', (url, initialPhone = '', initialName = '', initialId = '') => ({
                query: '',
                open: false,
                openSearchResults: false, // للبحث عبر رقم الهاتف
                loading: false,
                results: [],

                selectedId: initialId,
                selectedName: initialName,
                selectedPhone: '',

                countryCode: '+967',
                countryFlag: 'ye',
                localNumber: '',

                openCountry: false,
                countries: [{
                        code: '+967',
                        flag: 'ye'
                    },
                    {
                        code: '+966',
                        flag: 'sa'
                    },
                    {
                        code: '+971',
                        flag: 'ae'
                    },
                    {
                        code: '+965',
                        flag: 'kw'
                    },
                    {
                        code: '+974',
                        flag: 'qa'
                    },
                    {
                        code: '+968',
                        flag: 'om'
                    }
                ],

                init() {
                    if (initialPhone) {
                        this.parsePhone(initialPhone);
                    }
                },

                parsePhone(phone) {
                    if (!phone) {
                        this.setCountry('+967');
                        this.localNumber = '';
                        this.selectedPhone = '';
                        return;
                    }

                    const found = this.countries.find(c => phone.startsWith(c.code));

                    if (found) {
                        this.countryCode = found.code;
                        this.countryFlag = found.flag;
                        this.localNumber = phone.substring(found.code.length);
                    } else {
                        this.setCountry('+967');
                        this.localNumber = phone;
                    }
                    this.updateHidden();
                },

                setCountry(code) {
                    const country = this.countries.find(c => c.code === code);
                    if (country) {
                        this.countryCode = country.code;
                        this.countryFlag = country.flag;
                    }
                    this.updatePhone();
                    this.openCountry = false;
                },

                updatePhone() {
                    this.updateHidden();
                    this.selectedId = '';
                },

                updateHidden() {
                    this.selectedPhone = this.countryCode + this.localNumber;
                },

                // البحث عن طريق رقم الهاتف
                async searchByPhone() {
                    this.updateHidden(); // تحديث الرقم الكامل

                    const phoneNumber = this.localNumber.trim();
                    this.openSearchResults = true;

                    if (phoneNumber.length < 2) {
                        this.results = [];
                        this.loading = false;
                        return;
                    }

                    this.loading = true;

                    try {
                        const res = await fetch(`${url}?q=${encodeURIComponent(phoneNumber)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!res.ok) throw new Error('Search failed');
                        this.results = await res.json();
                    } catch (e) {
                        console.error(e);
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                },

                async search() {
                    const q = (this.query || '').trim();
                    this.open = true;

                    if (q.length < 2) {
                        this.results = [];
                        this.loading = false;
                        return;
                    }

                    this.loading = true;

                    try {
                        const res = await fetch(`${url}?q=${encodeURIComponent(q)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!res.ok) throw new Error('Search failed');
                        this.results = await res.json();
                    } catch (e) {
                        console.error(e);
                        this.results = [];
                    } finally {
                        this.loading = false;
                    }
                },

                select(c) {
                    this.selectedId = c.id;
                    this.selectedName = c.name ?? '';
                    this.parsePhone(c.phone ?? '');

                    this.query = this.selectedName;
                    this.open = false;
                    this.openSearchResults = false; // إغلاق قائمة البحث عبر الهاتف
                    this.results = [];
                }
            }));
        });
    </script>

@endsection
