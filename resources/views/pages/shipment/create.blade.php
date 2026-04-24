@extends('layouts.app')
@section('title', 'تسجيل طرد جديد')
@section('Breadcrumb', 'تسجيل طرد جديد')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">
        <div x-data="{ activeTab: 'sender' }">

            {{-- شريط التابات --}}
            {{-- <div class="mb-6 border-b border-gray-200 dark:border-gray-700"> --}}
            {{-- <nav class="flex gap-2"> --}}

            {{-- التاب الأول --}}
            {{-- <button type="button" @click="activeTab = 'sender'"
                        :class="activeTab === 'sender'
                            ?
                            'border-b-2 border-brand-500 text-brand-500 dark:text-brand-400' :
                            'border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                        class="px-4 py-2 text-sm font-medium">
                        مرسل الطرد
                    </button> --}}

            {{-- التاب الثاني --}}
            {{-- <button type="button" @click="activeTab = 'receiver'"
                        :class="activeTab === 'receiver'
                            ?
                            'border-b-2 border-brand-500 text-brand-500 dark:text-brand-400' :
                            'border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                        class="px-4 py-2 text-sm font-medium">
                        مستلم الطرد
                    </button> --}}

            {{-- </nav> --}}
            {{-- </div> --}}

            {{-- =================== التاب الأول: مرسل الطرد =================== --}}
            <form x-show="activeTab === 'sender'" x-cloak x-data="{
                payment_method: '{{ old('payment_method', 'prepaid') }}',
                prepaid_method: '{{ old('prepaid_payment_method', 'cash') }}',
                prepaid_reference: '{{ old('prepaid_reference') }}',
                partial_amount: '{{ old('partial_amount') }}',
                partial_method: '{{ old('partial_payment_method', 'cash') }}',
                partial_reference: '{{ old('partial_reference') }}',
                isSubmitting: false
            }"
                x-effect="
                    if (payment_method !== 'partial_payment') partial_amount = '';
                    if (!['prepaid','partial_payment'].includes(payment_method)) prepaid_method = 'cash';
                    if (payment_method !== 'prepaid') prepaid_reference = '';
                    if (payment_method !== 'partial_payment') partial_reference = '';
                "
                action="{{ route('shipment.store') }}" @submit="isSubmitting = true" method="POST">

                @csrf
                <input type="hidden" name="entry_type" value="sender">
                <input type="hidden" name="active_tab" value="sender">
                <div class="my-4 space-y-4">

                    <div class="mt-3">
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الجهة
                            إلى<span class="w-1 h-5 rounded-full bg-brand-500"></span></label>
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

                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">

                    <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}', '{{ old('sender_phone') }}', '{{ old('sender_name') }}', '{{ old('sender_customer_id') }}')">

                        <div
                            class="flex justify-between items-center pb-3 mb-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="flex gap-2 items-center text-base font-bold text-gray-800 dark:text-white">
                                <span class="w-1 h-5 rounded-full bg-brand-500"></span>
                                بيانات المرسل
                            </h3>

                            <button type="button" x-show="isLocked" x-cloak @click="reset()"
                                class="flex gap-2 items-center px-3 py-2 text-xs font-medium rounded-lg transition-all bg-error-50 text-error-500 group hover:bg-error-100 hover:shadow-sm dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20">
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:-rotate-180" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                <span>إعادة تعيين</span>
                            </button>
                        </div>

                        <div class="mt-3">
                            {{-- <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الجهة
                                من</label> --}}
                            <input type="hidden" name="sender_branch_code" value="{{ auth()->user()->branch_code }}">
                            @error('sender_branch_code')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="sender_customer_id" x-model="selectedId">
                        @error('sender_customer_id')
                            <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                        @enderror

                        <div class="relative mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف (ابحث
                                بالرقم)</label>

                            <div class="flex gap-2" dir="ltr">
                                <div class="relative" @click.outside="openCountry = false">
                                    <button type="button" @click="!isLocked && (openCountry = !openCountry)"
                                        :disabled="isLocked"
                                        class="flex gap-2 items-center px-3 py-2.5 h-11 bg-white rounded-lg border border-gray-300 dark:bg-dark-900 dark:border-gray-500 hover:border-brand-500 focus:border-brand-500 disabled:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-75"
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

                                <input type="text" x-model="localNumber" :disabled="isLocked"
                                    @input.debounce.350ms="updatePhone(); query = localNumber; search(); selectedId=''"
                                    @focus="!isLocked && (open = true; query = localNumber; search())"
                                    @keydown.escape="open=false"
                                    class="flex-1 px-4 py-2.5 h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-500"
                                    placeholder="7XXXXXXXX">
                            </div>

                            <div x-show="open && results.length > 0" x-transition @click.outside="open = false"
                                class="overflow-hidden absolute right-0 z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                                <template x-if="loading">
                                    <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                </template>
                                <template x-for="c in results" :key="c.id">
                                    <button type="button" @click="select(c)"
                                        class="px-4 py-3 w-full text-right border-b border-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 last:border-0 dark:border-gray-700">
                                        <div class="text-sm font-semibold text-gray-800 dark:text-white" x-text="c.name">
                                        </div>
                                        <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                    </button>
                                </template>
                            </div>

                            <input type="hidden" name="sender_phone" x-model="selectedPhone">
                            @error('sender_phone')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <p class="flex gap-2 items-center mt-2 text-xs text-warning-500 dark:text-warning/90">
                            <svg class="w-4 h-4 text-warning-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.52 3.48A11.86 11.86 0 0012 0 11.93 11.93 0 000 12a11.88 11.88 0 001.67 6.06L0 24l6.12-1.6A12 12 0 0012 24a11.93 11.93 0 0012-12 11.9 11.9 0 00-3.48-8.52z" />
                            </svg>
                            <span>ملاحظة: سيتم اعتماد هذا الرقم كرقم <span
                                    class="font-semibold text-success-500 dark:text-success-400">واتساب</span>
                                للتواصل.</span>
                        </p>

                        <div class="mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                            <input type="text" name="sender_name" x-model="selectedName" :disabled="isLocked"
                                @input="selectedId=''" value="{{ old('sender_name') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-500"
                                placeholder="اسم المرسل">
                            @error('sender_name')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>

                    <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}', '{{ old('receiver_phone') }}', '{{ old('receiver_name') }}', '{{ old('receiver_customer_id') }}')">

                        <div
                            class="flex justify-between items-center pb-3 mb-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="flex gap-2 items-center text-base font-bold text-gray-800 dark:text-white">
                                <span class="w-1 h-5 rounded-full bg-brand-500"></span>
                                بيانات المستلم
                            </h3>

                            <button type="button" x-show="isLocked" x-cloak @click="reset()"
                                class="flex gap-2 items-center px-3 py-2 text-xs font-medium rounded-lg transition-all bg-error-50 text-error-500 group hover:bg-error-100 hover:shadow-sm dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20">
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:-rotate-180" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                <span>إعادة تعيين</span>
                            </button>
                        </div>

                        <input type="hidden" name="receiver_customer_id" x-model="selectedId">
                        @error('receiver_customer_id')
                            <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                        @enderror



                        <div class="relative mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف (ابحث
                                بالرقم)</label>

                            <div class="flex gap-2" dir="ltr">
                                <div class="relative" @click.outside="openCountry = false">
                                    <button type="button" @click="!isLocked && (openCountry = !openCountry)"
                                        :disabled="isLocked"
                                        class="flex gap-2 items-center px-3 py-2.5 h-11 bg-white rounded-lg border border-gray-300 dark:bg-dark-900 dark:border-gray-500 hover:border-brand-500 focus:border-brand-500 disabled:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-75"
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

                                <input type="text" x-model="localNumber" :disabled="isLocked"
                                    @input.debounce.350ms="updatePhone(); query = localNumber; search(); selectedId=''"
                                    @focus="!isLocked && (open = true; query = localNumber; search())"
                                    @keydown.escape="open=false"
                                    class="flex-1 px-4 py-2.5 h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-500"
                                    placeholder="7XXXXXXXX">
                            </div>

                            <div x-show="open && results.length > 0" x-transition @click.outside="open = false"
                                class="overflow-hidden absolute right-0 z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                                <template x-if="loading">
                                    <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                </template>
                                <template x-for="c in results" :key="c.id">
                                    <button type="button" @click="select(c)"
                                        class="px-4 py-3 w-full text-right border-b border-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 last:border-0 dark:border-gray-700">
                                        <div class="text-sm font-semibold text-gray-800 dark:text-white" x-text="c.name">
                                        </div>
                                        <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                    </button>
                                </template>
                            </div>

                            <input type="hidden" name="receiver_phone" x-model="selectedPhone">
                            @error('receiver_phone')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <p class="flex gap-2 items-center mt-2 text-xs text-warning-500 dark:text-warning/90">
                            <svg class="w-4 h-4 text-warning-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.52 3.48A11.86 11.86 0 0012 0 11.93 11.93 0 000 12a11.88 11.88 0 001.67 6.06L0 24l6.12-1.6A12 12 0 0012 24a11.93 11.93 0 0012-12 11.9 11.9 0 00-3.48-8.52z" />
                            </svg>
                            <span>ملاحظة: سيتم اعتماد هذا الرقم كرقم <span
                                    class="font-semibold text-success-500 dark:text-success-400">واتساب</span>
                                للتواصل.</span>
                        </p>

                        <div class="mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                            <input type="text" name="receiver_name" x-model="selectedName" :disabled="isLocked"
                                @input="selectedId=''" value="{{ old('receiver_name') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-500"
                                placeholder="اسم المستلم">
                            @error('receiver_name')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>


                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2 xl:grid-cols-2">
                    <div class="w-full xl:col-span-2">
                        <h3 class="mb-4 text-sm font-bold text-gray-700 dark:text-gray-400">تفاصيل الطرد</h3>

                        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">نوع
                                    الطرد</label>
                                <input type="text" name="package_type" value="{{ old('package_type') }}"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                    placeholder="مثال: كرتون / شنطة / ...">
                                @error('package_type')
                                    <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">عدد قروف
                                    العسل</label>
                                <input type="number" name="no_honey_jars" value="{{ old('no_honey_jars') }}"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                    placeholder="0">
                                @error('no_honey_jars')
                                    <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">عدد جوالين
                                    العسل</label>
                                <input type="number" name="no_gallons_honey" value="{{ old('no_gallons_honey') }}"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                    placeholder="0">
                                @error('no_gallons_honey')
                                    <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">رمز
                                    الطرد</label>
                                <input type="text" name="code" value="{{ old('code') }}"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                    placeholder="QWR123">
                                @error('code')
                                    <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">إجمالي
                                    المبلغ</label>
                                <input type="number" name="total_amount" value="{{ old('total_amount') }}"
                                    step="0.01" min="0"
                                    class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                    placeholder="0.00">
                                @error('total_amount')
                                    <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                @enderror
                            </div>

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
                                        @change="payment_method='customer_cerrorit'"
                                        {{ old('payment_method') == 'customer_cerrorit' ? 'checked' : '' }}>
                                    <span
                                        :class="payment_method === 'customer_cerrorit' ? 'border-brand-500 bg-brand-500' :
                                            'bg-transparent border-gray-300 dark:border-gray-700'"
                                        class="flex h-5 w-5 items-center justify-center rounded-full border-[1.25px]">
                                        <span :class="payment_method === 'customer_cerrorit' ? 'block' : 'hidden'"
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
                                @error('prepaid_payment_method')
                                    <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                @enderror

                                <template x-if="prepaid_method === 'bank_transfer'">
                                    <div class="mt-4">
                                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">رقم
                                            الإيداع</label>
                                        <input type="text" name="prepaid_reference" x-model="prepaid_reference"
                                            placeholder="أدخل رقم الإيداع"
                                            class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">
                                        @error('prepaid_reference')
                                            <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </template>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method === 'cod'" x-transition>
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    سيتم اعتبار مبلغ التحصيل عند التسليم = <span class="font-semibold">إجمالي
                                        المبلغ</span>.
                                </div>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method==='partial_payment'" x-transition>
                                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">المبلغ
                                    المدفوع من المرسل الآن</label>
                                <input type="number" name="partial_amount" x-model="partial_amount"
                                    :disabled="payment_method !== 'partial_payment'"
                                    :requierror="payment_method === 'partial_payment'" min="0.01" step="0.01"
                                    placeholder="0.00"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-400 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 @error('partial_amount') border-error-500 @enderror">
                                @error('partial_amount')
                                    <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                @enderror

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
                                    @error('partial_payment_method')
                                        <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                    @enderror

                                    <template x-if="partial_method === 'bank_transfer'">
                                        <div class="mt-4">
                                            <label
                                                class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">رقم
                                                الإيداع</label>
                                            <input type="text" name="partial_reference" x-model="partial_reference"
                                                placeholder="أدخل رقم الإيداع"
                                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">
                                            @error('partial_reference')
                                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الملاحظات</label>
                    <textarea placeholder="اكتب ملاحظاتك..." rows="4" name="notes"
                        class="px-4 py-2.5 w-full h-auto text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 resize-none hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                    @enderror
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
            </form>

            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('customerPicker', (url, initialPhone = '', initialName = '', initialId = '') => ({
                        query: '',
                        open: false,
                        loading: false,
                        results: [],

                        selectedId: initialId,
                        selectedName: initialName,
                        selectedPhone: '',

                        isLocked: false,

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
                            // Lock fields if initialId exists (from Laravel validation or edit mode)
                            if (initialId) {
                                this.isLocked = true;
                            }
                        },

                        parsePhone(phone) {
                            if (!phone) {
                                this.setCountry('+967');
                                this.localNumber = '';
                                this.selectedPhone = '';
                                return;
                            }

                            let normalizedPhone = phone;
                            // إذا كان الرقم يبدأ برقم (بدون +)، نضيف + لتسهيل استخراج مفتاح الدولة
                            if (/^\d/.test(phone)) {
                                normalizedPhone = '+' + phone;
                            }

                            const found = this.countries.find(c => normalizedPhone.startsWith(c.code));

                            if (found) {
                                this.countryCode = found.code;
                                this.countryFlag = found.flag;
                                this.localNumber = normalizedPhone.substring(found.code.length);
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
                            this.selectedId = ''; // Reset ID when user types a new number
                        },

                        updateHidden() {
                            this.selectedPhone = this.countryCode + this.localNumber;
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
                            this.results = [];
                            this.isLocked = true; // Lock fields after selection
                        },

                        reset() {
                            this.selectedId = '';
                            this.selectedName = '';
                            this.selectedPhone = '';
                            this.localNumber = '';
                            this.query = '';
                            this.isLocked = false; // Unlock fields
                            this.setCountry('+967');
                        }
                    }));
                });
            </script>

            {{-- =================== التاب الثاني: مستلم الطرد) =================== --}}
            {{-- <form x-show="activeTab === 'receiver'" x-cloak x-data="{
                payment_method: '{{ old('payment_method', 'prepaid') }}',
                prepaid_method: '{{ old('prepaid_payment_method', 'cash') }}',
                partial_amount: '{{ old('partial_amount') }}',
                prepaid_reference: '{{ old('prepaid_reference') }}',
                isSubmitting: false
               }"
                                x-effect="
                        // تنظيف القيم حسب طريقة الدفع
                        if (payment_method !== 'partial_payment') partial_amount = '';
                        if (!['prepaid','partial_payment'].includes(payment_method)) prepaid_method = 'cash';
                        if (payment_method !== 'prepaid' && payment_method !== 'partial_payment') prepaid_reference = '';
                    "
                action="{{ route('shipment.store') }}" @submit="isSubmitting = true" method="POST"
                enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="entry_type" value="receiver">
                <input type="hidden" name="active_tab" value="receiver">

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-2">

                    <div class="space-y-4" x-data="customerPicker('{{ route('customers.search') }}', '{{ old('sender_phone') }}', '{{ old('sender_name') }}', '{{ old('sender_customer_id') }}')">

                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات المرسل</h3>

                        <div class="mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الجهة
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
                            @error('sender_branch_code')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="sender_customer_id" x-model="selectedId">
                        @error('sender_customer_id')
                            <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                        @enderror

                        <div class="mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                            <input type="text" name="sender_name" x-model="selectedName" @input="selectedId=''"
                                value="{{ old('sender_name') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="اسم المرسل">
                            @error('sender_name')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="relative mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف (ابحث
                                بالرقم)</label>
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
                                <input type="text" x-model="localNumber"
                                    @input.debounce.350ms="updatePhone(); query = localNumber; search(); selectedId=''"
                                    @focus="open = true; query = localNumber; search()" @keydown.escape="open=false"
                                    class="flex-1 px-4 py-2.5 h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                    placeholder="7XXXXXXXX">
                            </div>
                            <div x-show="open && results.length > 0" x-transition @click.outside="open = false"
                                class="overflow-hidden absolute right-0 z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                                <template x-if="loading">
                                    <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                </template>
                                <template x-for="c in results" :key="c.id">
                                    <button type="button" @click="select(c); open = false"
                                        class="px-4 py-3 w-full text-right border-b border-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 last:border-0 dark:border-gray-700">
                                        <div class="text-sm font-semibold text-gray-800 dark:text-white" x-text="c.name">
                                        </div>
                                        <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="sender_phone" x-model="selectedPhone">
                            @error('sender_phone')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>
                        <p class="flex gap-2 items-center mt-2 text-xs text-warning-500 dark:text-warning/90">
                            <svg class="w-4 h-4 text-warning-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.52 3.48A11.86 11.86 0 0012 0 11.93 11.93 0 000 12a11.88 11.88 0 001.67 6.06L0 24l6.12-1.6A12 12 0 0012 24a11.93 11.93 0 0012-12 11.9 11.9 0 00-3.48-8.52z" />
                            </svg>
                            <span>
                                ملاحظة: سيتم اعتماد هذا الرقم كرقم
                                <span class="font-semibold text-success-500 dark:text-success-400">واتساب</span>
                                للتواصل.
                            </span>
                        </p>
                        <div class="mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">عدد قروف
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
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الجهة
                                إلى</label>
                            <input type="text" value="{{ auth()->user()->branch->name ?? '' }}"
                                class="px-4 py-2.5 w-full h-11 text-sm bg-gray-100 rounded-lg border dark:text-gray-400 dark:bg-gray-700"
                                disabled>
                            <input type="hidden" name="receiver_branch_code" value="{{ auth()->user()->branch_code }}">
                            @error('receiver_branch_code')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الاسم</label>
                            <input type="text" name="receiver_name" x-model="selectedName" @input="selectedId=''"
                                value="{{ old('receiver_name') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="اسم المستلم">
                            @error('receiver_name')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="relative mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف (ابحث
                                بالرقم)</label>
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
                                <input type="text" x-model="localNumber"
                                    @input.debounce.350ms="updatePhone(); query = localNumber; search(); selectedId=''"
                                    @focus="open = true; query = localNumber; search()" @keydown.escape="open=false"
                                    class="flex-1 px-4 py-2.5 h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                    placeholder="7XXXXXXXX">
                            </div>
                            <div x-show="open && results.length > 0" x-transition @click.outside="open = false"
                                class="overflow-hidden absolute right-0 z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700">
                                <template x-if="loading">
                                    <div class="p-3 text-sm text-gray-500">جاري البحث...</div>
                                </template>
                                <template x-for="c in results" :key="c.id">
                                    <button type="button" @click="select(c); open = false"
                                        class="px-4 py-3 w-full text-right border-b border-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700 last:border-0 dark:border-gray-700">
                                        <div class="text-sm font-semibold text-gray-800 dark:text-white" x-text="c.name">
                                        </div>
                                        <div class="text-xs text-gray-500" x-text="c.phone"></div>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="receiver_phone" x-model="selectedPhone">
                            @error('receiver_phone')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>
                        <p class="flex gap-2 items-center mt-2 text-xs text-warning-500 dark:text-warning/90">
                            <svg class="w-4 h-4 text-warning-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.52 3.48A11.86 11.86 0 0012 0 11.93 11.93 0 000 12a11.88 11.88 0 001.67 6.06L0 24l6.12-1.6A12 12 0 0012 24a11.93 11.93 0 0012-12 11.9 11.9 0 00-3.48-8.52z" />
                            </svg>
                            <span>
                                ملاحظة: سيتم اعتماد هذا الرقم كرقم
                                <span class="font-semibold text-success-500 dark:text-success-400">واتساب</span>
                                للتواصل.
                            </span>
                        </p>
                        <div class="mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">عدد جوالين
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
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">نوع
                                الطرد</label>
                            <input type="text" name="package_type" value="{{ old('package_type') }}"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="مثال: كرتون / شنطة / ...">
                            @error('package_type')
                                <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-3">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">إجمالي
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
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">رمز
                                الطرد</label>
                            <input type="text" name="code" value="{{ old('code') }}" step="0.01"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white"
                                placeholder="QWR123">
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
                                @error('prepaid_payment_method')
                                    <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                @enderror

                                <div class="mt-4" x-show="prepaid_method === 'bank_transfer'" x-transition>
                                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                                        رقم السند / رقم التحويل / رقم الإيداع
                                    </label>
                                    <input type="text" name="prepaid_reference" x-model="prepaid_reference"
                                        placeholder="أدخل رقم السند أو التحويل"
                                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">
                                    @error('prepaid_reference')
                                        <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="p-4 mt-2 rounded-xl border border-gray-200 dark:border-gray-700"
                                x-show="payment_method === 'cod'" x-transition>
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    سيتم اعتبار مبلغ التحصيل = <span class="font-semibold">إجمالي المبلغ</span>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">الملاحظات</label>
                    <textarea placeholder="اكتب ملاحظاتك..." rows="4" name="notes"
                        class="px-4 py-2.5 w-full h-auto text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 resize-none hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-500 dark:text-white">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="mt-1 text-sm text-error-500">{{ $message }}</div>
                    @enderror
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
            {{-- </form> --}}
        @endsection
