@extends('layouts.app')

@section('title', 'إرسال طرد جديد')

@section('content')
    <div class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl" x-data="{ paymentMethod: 'prepaid', isSubmitting: false }">

        {{-- ================= الشريط العلوي (Sticky Header) ================= --}}
        <div
            class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
            <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
                <div class="flex gap-4 items-center">
                    <a href="{{ url()->previous() }}"
                        class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                    <div>
                        <h1 class="text-xl font-black md:text-2xl font-headline text-on-surface dark:text-white">إرسال طرد
                        </h1>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-bodydark">تعبئة بيانات بوليصة الشحن الجديدة</p>
                    </div>
                </div>

                {{-- زر الحفظ يظهر في الديسكتوب في الأعلى --}}
                <button type="submit" form="shipmentForm" :disabled="isSubmitting"
                    class="hidden gap-2 justify-center items-center px-6 h-11 text-sm font-bold text-white rounded-xl shadow-md transition-all md:flex bg-primary hover:bg-primary-hover shadow-primary/20 active:scale-95 disabled:opacity-70 disabled:shadow-none">
                    <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">send</span>
                    <span x-show="isSubmitting"
                        class="animate-spin material-symbols-outlined text-[18px]">progress_activity</span>
                    <span x-text="isSubmitting ? 'جاري الاعتماد...' : 'اعتماد وإصدار السند'"></span>
                </button>
            </div>
        </div>

        {{-- ================= محتوى النموذج (Grid Layout) ================= --}}
        <div class="p-4 mx-auto mt-4 max-w-7xl md:p-6">

            <form id="shipmentForm" action="{{ route('shipment.outgoing.store') }}" method="POST"
                @submit="if(!isSubmitting) { isSubmitting = true; return true; } else { $event.preventDefault(); return false; }"
                class="grid grid-cols-1 gap-6 items-start lg:grid-cols-12">
                @csrf

                {{-- الجانب الأيمن: بيانات الوجهة والعملاء (8 أعمدة) --}}
                <div class="flex flex-col gap-6 lg:col-span-7 xl:col-span-8">

                    {{-- 1. بيانات الفروع (الوجهة) --}}
                    <div x-data='destinationLogic(@json($offices))'
                        class="bg-white dark:bg-boxdark p-6 md:p-8 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm relative overflow-hidden group/card">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-primary/5 dark:bg-primary/10 rounded-bl-[100px] pointer-events-none">
                        </div>

                        <div class="relative z-10">
                            <div class="flex gap-3 items-center mb-6">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-gray-500 rounded-xl shadow-sm transition-all shrink-0 bg-surface dark:bg-boxdark-2 dark:text-bodydark group-hover/card:bg-primary group-hover/card:text-white">
                                    <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-on-surface dark:text-white font-headline">وجهة الطرد
                                    </h3>
                                    <p class="mt-1 text-xs font-medium text-gray-500 dark:text-bodydark">اختر المكتب والفرع
                                        المستلم</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                                {{-- اختيار المكتب --}}
                                <div>

                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">المكتب
                                        <span class="text-error">*</span></label>
                                    <div class="relative">
                                        <select name="office_id" x-model="selectedOfficeId" @change="updateBranches"
                                            required
                                            class="pr-4 pl-10 w-full h-12 text-sm font-bold rounded-xl border border-gray-200 transition-all appearance-none outline-none text-on-surface dark:text-white bg-surface dark:bg-boxdark-2 dark:border-boxdark-2 focus:bg-white dark:focus:bg-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20">
                                            <option value="" disabled selected>اختر المكتب...</option>
                                            <template x-for="office in offices" :key="office.id">
                                                <option :value="office.id" x-text="office.name"></option>
                                            </template>
                                        </select>
                                        <span
                                            class="absolute left-3 top-1/2 text-gray-400 -translate-y-1/2 pointer-events-none">
                                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- اختيار الفرع --}}
                                <div x-show="selectedOfficeId" x-transition:enter.duration.300ms x-cloak>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">الفرع <span
                                            class="text-error">*</span></label>
                                    <div class="relative">
                                        <select name="receiver_branch_id" required
                                            class="pr-4 pl-10 w-full h-12 text-sm font-bold bg-white rounded-xl border border-gray-200 transition-all appearance-none outline-none text-on-surface dark:text-white dark:bg-boxdark dark:border-boxdark-2 focus:border-green-500 focus:ring-2 focus:ring-green-500/20">
                                            <option value="" disabled selected>اختر الفرع...</option>
                                            <template x-for="branch in availableBranches" :key="branch.id">
                                                <option :value="branch.id" x-text="branch.name"></option>
                                            </template>
                                        </select>
                                        <span
                                            class="absolute left-3 top-1/2 text-green-500 -translate-y-1/2 pointer-events-none">
                                            <span class="material-symbols-outlined text-[20px]">expand_more</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. بيانات العملاء --}}
                    <div
                        class="bg-white dark:bg-boxdark p-6 md:p-8 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm">
                        <h3
                            class="flex gap-2 items-center mb-6 text-lg font-black text-on-surface dark:text-white font-headline">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-green-500 bg-green-50 rounded-lg dark:bg-green-500/10 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">group</span>
                            </div>
                            بيانات العملاء
                        </h3>

                        <div class="space-y-8">
                            {{-- المرسل --}}
                            <div x-data="customerSelect({{ $customers }}, @js(array_values(config('countries', []))))" class="relative z-50">
                                <h4
                                    class="pb-2 mb-3 text-sm font-bold text-gray-700 border-b border-gray-100 dark:text-gray-200 dark:border-boxdark-2">
                                    بيانات المرسل</h4>

                                <div class="grid relative grid-cols-1 gap-4 md:grid-cols-2">
                                    <input type="hidden" name="sender_customer_id" x-model="selectedCustomerId">
                                    <input type="hidden" name="sender_phone" :value="fullPhoneNumber">

                                    {{-- رقم الهاتف --}}
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">رقم
                                            الهاتف <span class="text-error">*</span></label>
                                        <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40 focus-within:border-primary"
                                            :class="selectedCustomerId ?
                                                'bg-primary-container dark:bg-primary/10 ring-primary/30' : ''"
                                            style="direction: ltr;">

                                            <div class="relative h-full" @click.away="openCountryDropdown = false">
                                                <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                                    class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                                    <template x-if="selectedCountry?.svg">
                                                        <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden"
                                                            x-html="selectedCountry.svg"></div>
                                                    </template>
                                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300"
                                                        x-text="selectedCountry?.dial_code"></span>
                                                </button>

                                                {{-- Country Dropdown --}}
                                                <div x-show="openCountryDropdown" x-cloak x-transition
                                                    class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                                    <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                        <input type="text" x-model="searchCountryQuery"
                                                            placeholder="بحث..."
                                                            class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                            dir="rtl">
                                                    </div>
                                                    <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                        <template x-for="country in filteredCountries"
                                                            :key="country.code">
                                                            <button type="button"
                                                                @click="selectedCountry = country; openCountryDropdown = false; searchCustomer()"
                                                                class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                                <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                                    x-html="country.svg"></div>
                                                                <span
                                                                    class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                                    x-text="country.name"></span>
                                                                <span
                                                                    class="text-[10px] font-mono text-gray-400 dark:text-gray-500"
                                                                    x-text="country.dial_code"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="tel" x-model="localPhoneNumber" @input="searchCustomer"
                                                @focus="showCustomerDropdown = true"
                                                @click.away="showCustomerDropdown = false" placeholder="7XXXXXXXX"
                                                required inputmode="numeric" autocomplete="off"
                                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                                class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                                :class="selectedCustomerId ? 'font-bold text-primary' : ''">

                                            <button type="button" x-show="selectedCustomerId" @click="resetSelection"
                                                class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                                <span class="material-symbols-outlined text-[16px]">close</span>
                                            </button>
                                        </div>

                                        {{-- Customer Search Dropdown --}}
                                        <div x-show="showCustomerDropdown && localPhoneNumber.length > 0 && !selectedCustomerId"
                                            x-transition x-cloak
                                            class="absolute top-[4.5rem] right-0 w-full md:w-[calc(50%-0.5rem)] bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                            <template x-for="customer in filteredCustomers" :key="customer.id">
                                                <button type="button" @click="selectCustomer(customer)"
                                                    class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-sm font-bold text-on-surface dark:text-white"
                                                            x-text="customer.name"></span>
                                                        <span
                                                            class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                            x-text="customer.phone"></span>
                                                    </div>
                                                    <span
                                                        class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                                </button>
                                            </template>
                                            <div x-show="filteredCustomers.length === 0"
                                                class="px-4 py-3 text-center bg-surface dark:bg-boxdark-2">
                                                <span class="text-xs font-bold text-gray-500 dark:text-bodydark">مرسل جديد،
                                                    سيتم حفظه تلقائياً.</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- الاسم --}}
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">اسم
                                            المرسل (اختياري)</label>
                                        <input type="text" name="sender_name" x-model="nameInput"
                                            :readonly="selectedCustomerId !== null" placeholder="الاسم..."
                                            class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white"
                                            :class="selectedCustomerId ?
                                                'bg-surface border-transparent text-gray-500 cursor-not-allowed opacity-80' :
                                                'bg-white border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20'">
                                    </div>
                                </div>
                            </div>

                            {{-- المستلم --}}
                            <div x-data="customerSelect({{ $customers }}, @js(array_values(config('countries', []))))" class="relative z-40">
                                <h4
                                    class="pb-2 mb-3 text-sm font-bold text-gray-700 border-b border-gray-100 dark:text-gray-200 dark:border-boxdark-2">
                                    بيانات المستلم</h4>

                                <div class="grid relative grid-cols-1 gap-4 md:grid-cols-2">
                                    <input type="hidden" name="receiver_customer_id" x-model="selectedCustomerId">
                                    <input type="hidden" name="receiver_phone" :value="fullPhoneNumber">

                                    {{-- رقم الهاتف --}}
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">رقم
                                            الهاتف <span class="text-error">*</span></label>
                                        <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-green-500/40 focus-within:border-green-500"
                                            :class="selectedCustomerId ?
                                                'bg-green-50/30 dark:bg-green-500/10 ring-green-400 dark:ring-green-500/50' :
                                                ''"
                                            style="direction: ltr;">

                                            <div class="relative h-full" @click.away="openCountryDropdown = false">
                                                <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                                    class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                                    <template x-if="selectedCountry?.svg">
                                                        <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden"
                                                            x-html="selectedCountry.svg"></div>
                                                    </template>
                                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300"
                                                        x-text="selectedCountry?.dial_code"></span>
                                                </button>

                                                {{-- Country Dropdown --}}
                                                <div x-show="openCountryDropdown" x-cloak x-transition
                                                    class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                                    <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                        <input type="text" x-model="searchCountryQuery"
                                                            placeholder="بحث..."
                                                            class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-green-500/30 text-on-surface dark:text-white"
                                                            dir="rtl">
                                                    </div>
                                                    <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                        <template x-for="country in filteredCountries"
                                                            :key="country.code">
                                                            <button type="button"
                                                                @click="selectedCountry = country; openCountryDropdown = false; searchCustomer()"
                                                                class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                                <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                                    x-html="country.svg"></div>
                                                                <span
                                                                    class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                                    x-text="country.name"></span>
                                                                <span
                                                                    class="text-[10px] font-mono text-gray-400 dark:text-gray-500"
                                                                    x-text="country.dial_code"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="tel" x-model="localPhoneNumber" @input="searchCustomer"
                                                @focus="showCustomerDropdown = true"
                                                @click.away="showCustomerDropdown = false" placeholder="7XXXXXXXX"
                                                required inputmode="numeric" autocomplete="off"
                                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                                class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                                :class="selectedCustomerId ? 'font-bold text-green-600 dark:text-green-400' : ''">

                                            <button type="button" x-show="selectedCustomerId" @click="resetSelection"
                                                class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                                <span class="material-symbols-outlined text-[16px]">close</span>
                                            </button>
                                        </div>

                                        {{-- Customer Search Dropdown --}}
                                        <div x-show="showCustomerDropdown && localPhoneNumber.length > 0 && !selectedCustomerId"
                                            x-transition x-cloak
                                            class="absolute top-[4.5rem] right-0 w-full md:w-[calc(50%-0.5rem)] bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                            <template x-for="customer in filteredCustomers" :key="customer.id">
                                                <button type="button" @click="selectCustomer(customer)"
                                                    class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-sm font-bold text-on-surface dark:text-white"
                                                            x-text="customer.name"></span>
                                                        <span
                                                            class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                            x-text="customer.phone"></span>
                                                    </div>
                                                    <span
                                                        class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                                </button>
                                            </template>
                                            <div x-show="filteredCustomers.length === 0"
                                                class="px-4 py-3 text-center bg-surface dark:bg-boxdark-2">
                                                <span class="text-xs font-bold text-gray-500 dark:text-bodydark">مستلم
                                                    جديد، سيتم حفظه تلقائياً.</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- الاسم --}}
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">اسم
                                            المستلم <span class="text-error">*</span></label>
                                        <input type="text" name="receiver_name" required x-model="nameInput"
                                            :readonly="selectedCustomerId !== null" placeholder="الاسم..."
                                            class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white"
                                            :class="selectedCustomerId ?
                                                'bg-surface border-transparent text-gray-500 cursor-not-allowed opacity-80' :
                                                'bg-white border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-500/20'">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- الجانب الأيسر: محتويات الطرد والدفع (4 أعمدة) --}}
                <div class="flex flex-col gap-6 lg:col-span-5 xl:col-span-4">

                    {{-- 3. محتويات الطرد --}}
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm">
                        <h3
                            class="flex gap-2 items-center mb-6 text-lg font-black text-on-surface dark:text-white font-headline">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-blue-500 bg-blue-50 rounded-lg dark:bg-blue-500/10 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                            </div>
                            محتويات الطرد
                        </h3>
                        <div class="grid grid-cols-2 gap-4">

                            {{-- ================= 1. حقل نوع الشحنة (Combobox) ================= --}}
                            <div class="flex flex-col gap-2" x-data="{
                                isOpen: false,
                                options: ['كرتون', 'كيس'],
                                packageType: 'كرتون'
                            }">
                                <label class="text-[11px] font-bold text-slate-500">نوع الشحنة <span
                                        class="text-rose-500">*</span></label>

                                <div class="relative z-[40]">
                                    {{-- حقل الإدخال النصي --}}
                                    <input type="text" name="package_type" x-model="packageType"
                                        @focus="isOpen = true" @click.outside="isOpen = false"
                                        placeholder="اختر أو اكتب النوع..." autocomplete="off" required
                                        class="px-4 w-full h-12 text-sm font-bold rounded-xl border transition-all outline-none border-slate-200 bg-slate-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">

                                    {{-- أيقونة السهم التفاعلية --}}
                                    <button type="button" @click="isOpen = !isOpen" tabindex="-1"
                                        class="flex absolute inset-y-0 left-0 items-center px-3 h-full rounded-l-xl transition-colors text-slate-400 hover:text-primary">
                                        <span
                                            class="material-symbols-outlined text-[20px] transition-transform duration-300"
                                            :class="isOpen ? 'rotate-180 text-primary' : ''">expand_more</span>
                                    </button>

                                    {{-- القائمة المنسدلة الذكية --}}
                                    <div x-show="isOpen" x-cloak x-transition.opacity.translate.y.-10px
                                        class="absolute left-0 right-0 top-full z-[60] mt-1.5 overflow-hidden rounded-xl bg-white border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] py-1.5">
                                        <template x-for="option in options" :key="option">
                                            <button type="button" @click="packageType = option; isOpen = false"
                                                class="flex justify-between items-center px-4 py-2.5 w-full text-xs font-bold text-right transition-colors text-slate-700 hover:bg-slate-50 hover:text-primary">
                                                <span x-text="option"></span>
                                                <span x-show="packageType === option"
                                                    class="material-symbols-outlined text-[18px] text-primary">check</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- ================= 2. حقل الوزن ================= --}}
                            <div class="flex flex-col gap-2">
                                <label class="text-[11px] font-bold text-slate-500">الوزن (كجم)</label>

                                <div class="relative">
                                    <input type="number" step="0.1" name="weight" placeholder="مثال: 2.5"
                                        min="0" dir="ltr"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold text-left rounded-xl border transition-all outline-none border-slate-200 bg-slate-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">

                                    {{-- أيقونة الوزن الجمالية --}}
                                    <div
                                        class="flex absolute inset-y-0 right-0 items-center pr-3 pointer-events-none text-slate-400">
                                        <span class="material-symbols-outlined text-[18px]">scale</span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- منطقة بيانات العسل مخفية افتراضياً وتظهر عند الضغط --}}
                        <div x-data="{ showHoneyFields: false }" class="mt-5">

                            {{-- زر الإظهار والإخفاء --}}
                            <button type="button" @click="showHoneyFields = !showHoneyFields"
                                class="flex justify-between items-center px-4 w-full h-12 text-sm font-bold transition-all rounded-2xl text-amber-600 bg-amber-50 hover:bg-amber-100 dark:text-amber-500 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 active:scale-[0.98]">
                                <div class="flex gap-2 items-center">
                                    <span class="text-[20px] material-symbols-outlined">hive</span>
                                    <span>إضافة بيانات العسل (دباب / قروف)</span>
                                </div>
                                {{-- سهم يتحرك عند الفتح والإغلاق --}}
                                <span class="transition-transform duration-300 material-symbols-outlined"
                                    :class="showHoneyFields ? 'rotate-180' : ''">expand_more</span>
                            </button>

                            {{-- الحقول (تظهر بتأثير انسيابي) --}}
                            <div x-show="showHoneyFields" x-cloak x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2">

                                <div
                                    class="grid grid-cols-2 gap-4 p-4 mt-3 rounded-2xl border bg-amber-50/50 dark:bg-amber-500/5 border-amber-100/50 dark:border-amber-500/10">
                                    <div>
                                        <label
                                            class="block mb-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-500">دباب
                                            العسل</label>
                                        <input type="number" name="no_gallons_honey" placeholder="العدد"
                                            class="px-3 w-full h-11 text-sm bg-white rounded-xl border border-amber-100 transition-all outline-none dark:bg-boxdark dark:border-amber-500/20 focus:border-amber-400 dark:focus:border-amber-500 text-on-surface dark:text-white">
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-500">قروف
                                            العسل</label>
                                        <input type="number" name="no_honey_jars" placeholder="العدد"
                                            class="px-3 w-full h-11 text-sm bg-white rounded-xl border border-amber-100 transition-all outline-none dark:bg-boxdark dark:border-amber-500/20 focus:border-amber-400 dark:focus:border-amber-500 text-on-surface dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">ملاحظات
                                إضافية</label>
                            <textarea name="notes" rows="2" placeholder="اكتب أي ملاحظات هنا..."
                                class="p-4 w-full text-sm rounded-2xl border border-gray-200 transition-all outline-none resize-none bg-surface dark:bg-boxdark-2 dark:border-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white"></textarea>
                        </div>
                    </div>

                    {{-- 4. المالية والدفع --}}
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm">
                        <h3
                            class="flex gap-2 items-center mb-6 text-lg font-black text-on-surface dark:text-white font-headline">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-rose-500 bg-rose-50 rounded-lg dark:bg-rose-500/10 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">payments</span>
                            </div>
                            المالية والدفع
                        </h3>

                        <div class="space-y-5">
                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">طريقة الدفع
                                    <span class="text-error">*</span></label>
                                <select name="payment_method" x-model="paymentMethod"
                                    class="px-4 w-full h-14 text-sm font-bold rounded-2xl border border-gray-200 transition-all appearance-none outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white">
                                    <option value="prepaid">مدفوع مقدماً</option>
                                    <option value="cod">الدفع عند الاستلام (على المستلم)</option>
                                    <option value="partial_payment">دفع جزئي</option>
                                    <option value="customer_credit">آجل (على حساب المرسل)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">المبلغ
                                    الإجمالي (ريال) <span class="text-error">*</span></label>
                                <input type="number" required name="total_amount" placeholder="0.00"
                                    class="px-4 w-full h-14 text-xl font-black text-left rounded-2xl border border-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-primary dark:text-primary"
                                    dir="ltr">
                            </div>

                            <div x-show="paymentMethod === 'partial_payment'" x-collapse>
                                <label class="block text-[11px] font-bold text-rose-500 dark:text-rose-400 mb-1.5">المبلغ
                                    المدفوع حالياً (ريال)</label>
                                <input type="number" name="partial_amount" placeholder="0.00"
                                    class="px-4 w-full h-14 text-xl font-black text-left text-rose-600 rounded-2xl border border-rose-200 transition-all outline-none bg-rose-50/50 dark:bg-rose-500/5 dark:border-rose-500/20 focus:border-rose-400 dark:text-rose-400"
                                    dir="ltr">
                            </div>
                        </div>
                    </div>

                    {{-- زر الحفظ للموبايل --}}
                    <div class="mt-2 md:hidden">
                        <button type="submit" :disabled="isSubmitting"
                            class="flex gap-2 justify-center items-center w-full h-14 text-sm font-bold rounded-2xl shadow-lg transition-all duration-200"
                            :class="isSubmitting ? 'bg-gray-400 dark:bg-gray-600 text-white cursor-not-allowed shadow-none' :
                                'bg-primary text-white hover:bg-primary-hover shadow-primary/30 active:scale-95'">
                            <span x-show="!isSubmitting" class="material-symbols-outlined text-[22px]">send</span>
                            <span x-show="isSubmitting"
                                class="animate-spin material-symbols-outlined text-[22px]">progress_activity</span>
                            <span x-text="isSubmitting ? 'جاري الاعتماد...' : 'اعتماد وإصدار السند'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('destinationLogic', (officesList) => ({
                offices: officesList || [],
                selectedOfficeId: '',
                availableBranches: [],
                init() {
                    const internalOffice = this.offices.find(o => String(o.id).startsWith('internal_'));
                    if (internalOffice) {
                        this.selectedOfficeId = internalOffice.id;
                        this.availableBranches = internalOffice.branches;
                    }
                },
                updateBranches() {
                    const office = this.offices.find(o => o.id == this.selectedOfficeId);
                    this.availableBranches = office ? office.branches : [];
                }
            }));

            Alpine.data('customerSelect', (customersList, countriesList) => ({
                customers: customersList || [],
                countries: countriesList || [],
                filteredCustomers: [],
                localPhoneNumber: '',
                nameInput: '',
                selectedCustomerId: null,
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                showCustomerDropdown: false,

                init() {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[
                        0];
                },
                get filteredCountries() {
                    if (this.searchCountryQuery === '') return this.countries;
                    const query = this.searchCountryQuery.toLowerCase();
                    return this.countries.filter(c => c.name.toLowerCase().includes(query) || c
                        .dial_code.includes(query));
                },
                get fullPhoneNumber() {
                    if (!this.localPhoneNumber) return '';
                    let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace(
                        '+', '') : '';
                    return dialCode + this.localPhoneNumber;
                },
                searchCustomer() {
                    this.selectedCustomerId = null;
                    let query = this.fullPhoneNumber.trim();
                    if (this.localPhoneNumber.trim() === '') {
                        this.filteredCustomers = [];
                        this.showCustomerDropdown = false;
                        return;
                    }
                    this.filteredCustomers = this.customers.filter(c => {
                        return c.phone && String(c.phone).includes(query);
                    });
                    this.showCustomerDropdown = true;
                },
                selectCustomer(customer) {
                    this.selectedCustomerId = customer.id;
                    let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+',
                        '') : '';
                    if (customer.phone && customer.phone.startsWith(dialCode)) {
                        this.localPhoneNumber = customer.phone.substring(dialCode.length);
                    } else {
                        this.localPhoneNumber = customer.phone;
                    }
                    this.nameInput = customer.name;
                    this.showCustomerDropdown = false;
                },
                resetSelection() {
                    this.selectedCustomerId = null;
                    this.localPhoneNumber = '';
                    this.nameInput = '';
                    this.showCustomerDropdown = false;
                }
            }));
        });
    </script>
@endsection
