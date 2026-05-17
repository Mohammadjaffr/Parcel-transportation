@extends('mobile.layouts.app')

@section('title', 'تعديل الطرد - ' . $shipment->bond_number)

@section('content')
<div x-data="{ paymentMethod: '{{ old('payment_method', $shipment->payment_method) }}' }"
    class="flex relative flex-col gap-6 px-4 pt-6 min-h-screen pb-19 bg-slate-50/50">

    <div class="flex gap-4 items-center mb-2">
        <a href="javascript:history.back()"
            class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-transform border-slate-100 text-slate-500 active:scale-90">
            <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
        </a>
        <div>
            <h1 class="text-2xl font-black font-headline text-slate-800">تعديل الطرد</h1>
            <p class="mt-1 text-xs font-bold text-primary">{{ $shipment->bond_number }}</p>
        </div>
    </div>

    <form action="{{ route('shipment.outgoing.update', $shipment->id) }}" method="POST" class="space-y-6"
        x-data="{ isSubmitting: false }"
        @submit="if(!isSubmitting) { isSubmitting = true; $el.submit(); } else { $event.preventDefault(); }">
        @csrf
        @method('PUT')

        {{-- بيانات الفروع --}}
        <div x-data='destinationLogic(@json($offices))'
            class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-900/5 group/card">

            {{-- الرأس بشكل مبسط --}}
            <div class="flex gap-3 items-center mb-6">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl transition-all shrink-0 bg-slate-50 text-slate-400 group-hover/card:bg-blue-500 group-hover/card:text-white">
                    <span class="material-symbols-outlined text-[22px]"
                        style="font-variation-settings: 'FILL' 1;">edit_location</span>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 font-headline">وجهة الطرد</h3>
                    <p class="text-[10px] text-slate-500 font-medium">تعديل المكتب والفرع المستلم</p>
                </div>
            </div>

            {{-- شبكة الحقول --}}
            <div class="grid grid-cols-2 gap-3">

                {{-- اختيار المكتب --}}
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 pr-1">المكتب <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="office_id" x-model="selectedOfficeId"
                            @change="selectedBranchId = ''; updateBranches()" required
                            class="pr-3 pl-8 w-full h-12 text-xs font-bold rounded-xl border transition-all appearance-none outline-none text-slate-800 bg-slate-50 border-slate-200/70 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                            <option value="" disabled>المكتب...</option>
                            <template x-for="office in offices" :key="office.id">
                                <option :value="office.id" x-text="office.name"></option>
                            </template>
                        </select>
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                {{-- اختيار الفرع --}}
                <div class="space-y-1.5" x-show="selectedOfficeId" x-transition:enter.duration.300ms>
                    <label class="text-[10px] font-black text-slate-400 pr-1">الفرع <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="receiver_branch_id" x-model="selectedBranchId" required
                            class="pr-3 pl-8 w-full h-12 text-xs font-bold bg-white rounded-xl border transition-all appearance-none outline-none text-slate-800 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                            <option value="" disabled>الفرع...</option>
                            <template x-for="branch in availableBranches" :key="branch.id">
                                <option :value="branch.id" x-text="branch.name"></option>
                            </template>
                        </select>
                        <span class="absolute left-2.5 top-1/2 text-blue-500 -translate-y-1/2 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= بيانات العملاء ================= --}}
        <div class="bg-white p-5 rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.03)]">
            <div class="flex gap-2 items-center mb-5">
                <span
                    class="material-symbols-outlined text-emerald-500 bg-emerald-50 p-1.5 rounded-lg text-[20px]">group</span>
                <h3 class="font-bold text-slate-700 font-headline">بيانات العملاء</h3>
            </div>

            <div class="space-y-6">

                {{-- ================= المرسل ================= --}}
                <div x-data="customerSelect({{ $customers }}, @js(array_values(config('countries', []))), '{{ old('sender_customer_id', $shipment->sender_customer_id) }}', '{{ old('sender_name', $shipment->senderCustomer->name ?? '') }}', '{{ old('sender_phone', $shipment->senderCustomer->phone ?? '') }}')"
                    class="z-50 p-4 rounded-2xl border bg-slate-50 border-slate-100">
                    <span class=" -top-2.5 right-4 bg-slate-50 px-2 text-[10px] font-black text-slate-500">المرسل <span
                            class="text-red-500">*</span></span>

                    <div class="grid relative grid-cols-1 gap-3 mt-2">
                        <input type="hidden" name="sender_customer_id" x-model="selectedCustomerId">
                        <input type="hidden" name="sender_phone" :value="fullPhoneNumber">

                        <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 transition-all group ring-slate-200 focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary"
                            :class="selectedCustomerId ? 'bg-primary/5 ring-primary/30' : ''" style="direction: ltr;">

                            <div class="relative h-full" @click.away="openCountryDropdown = false">
                                <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                    class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r transition-colors bg-slate-50 border-slate-200 shrink-0 hover:bg-slate-100">
                                    <template x-if="selectedCountry?.svg">
                                        <svg class="w-5 h-auto rounded-[2px] shadow-sm" viewBox="0 0 36 24" fill="none"
                                            x-html="selectedCountry.svg"></svg>
                                    </template>
                                    <span class="text-xs font-bold text-slate-600"
                                        x-text="selectedCountry?.dial_code"></span>
                                </button>

                                <div x-show="openCountryDropdown" x-cloak x-transition
                                    class="absolute top-full left-0 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-100 z-[60] overflow-hidden">
                                    <div class="p-2 border-b border-slate-50">
                                        <input type="text" x-model="searchCountryQuery" placeholder="Search country..."
                                            class="px-3 w-full h-8 text-xs rounded-lg outline-none bg-slate-50 focus:ring-1 ring-primary/30"
                                            dir="ltr">
                                    </div>
                                    <div class="overflow-y-auto max-h-48" dir="ltr">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <button type="button"
                                                @click="selectedCountry = country; openCountryDropdown = false; searchCustomer()"
                                                class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-slate-50">
                                                <svg class="w-5 h-auto rounded-[2px]" viewBox="0 0 36 24" fill="none"
                                                    x-html="country.svg"></svg>
                                                <span class="flex-1 text-xs font-bold text-slate-700"
                                                    x-text="country.name"></span>
                                                <span class="text-xs text-slate-400" x-text="country.dial_code"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <input type="tel" x-model="localPhoneNumber" @input="searchCustomer"
                                @focus="showCustomerDropdown = true" @click.away="showCustomerDropdown = false"
                                placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                class="flex-1 px-4 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-slate-800"
                                :class="selectedCustomerId ? 'font-bold text-primary' : ''">

                            <button type="button" x-show="selectedCustomerId" @click="resetSelection"
                                class="absolute right-3 z-10 p-0.5 bg-white rounded-full text-slate-400 hover:text-red-500">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>

                        <div x-show="showCustomerDropdown && localPhoneNumber.length > 0 && !selectedCustomerId"
                            x-transition x-cloak
                            class="absolute top-[3.25rem] right-0 w-full bg-white border border-slate-100 rounded-xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden max-h-48 overflow-y-auto z-50">
                            <template x-for="customer in filteredCustomers" :key="customer.id">
                                <button type="button" @click="selectCustomer(customer)"
                                    class="flex justify-between items-center px-4 py-3 w-full text-right border-b transition-colors hover:bg-slate-50 border-slate-50">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-sm font-bold text-slate-800" x-text="customer.name"></span>
                                        <span class="text-xs text-right text-slate-500 dir-ltr"
                                            x-text="customer.phone"></span>
                                    </div>
                                    <span
                                        class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                                </button>
                            </template>
                            <div x-show="filteredCustomers.length === 0" class="px-4 py-3 text-center bg-slate-50/50">
                                <span class="text-xs font-bold text-slate-500">مرسل جديد، سيتم حفظه.</span>
                            </div>
                        </div>

                        <input type="text" name="sender_name" x-model="nameInput"
                            :readonly="selectedCustomerId !== null" placeholder="اسم المرسل (اختياري)..."
                            class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none"
                            :class="selectedCustomerId ? 'bg-slate-50 border-transparent text-slate-500 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-700'">
                    </div>
                </div>

                {{-- ================= المستلم ================= --}}
                <div x-data="customerSelect({{ $customers }}, @js(array_values(config('countries', []))), '{{ old('receiver_customer_id', $shipment->receiver_customer_id) }}', '{{ old('receiver_name', $shipment->receiverCustomer->name ?? '') }}', '{{ old('receiver_phone', $shipment->receiverCustomer->phone ?? '') }}')"
                    class="z-40 p-4 rounded-2xl border bg-slate-50 border-slate-100">
                    <span class=" -top-2.5 right-4 bg-slate-50 px-2 text-[10px] font-black text-slate-500">المستلم
                        <span class="text-red-500">*</span></span>

                    <div class="grid relative grid-cols-1 gap-3 mt-2">
                        <input type="hidden" name="receiver_customer_id" x-model="selectedCustomerId">
                        <input type="hidden" name="receiver_phone" :value="fullPhoneNumber">

                        <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 transition-all group ring-slate-200 focus-within:ring-2 focus-within:ring-emerald-500/50 focus-within:border-emerald-500"
                            :class="selectedCustomerId ? 'bg-emerald-50/30 ring-emerald-400' : ''"
                            style="direction: ltr;">

                            <div class="relative h-full" @click.away="openCountryDropdown = false">
                                <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                    class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r transition-colors bg-slate-50 border-slate-200 shrink-0 hover:bg-slate-100">
                                    <template x-if="selectedCountry?.svg">
                                        <svg class="w-5 h-auto rounded-[2px] shadow-sm" viewBox="0 0 36 24" fill="none"
                                            x-html="selectedCountry.svg"></svg>
                                    </template>
                                    <span class="text-xs font-bold text-slate-600"
                                        x-text="selectedCountry?.dial_code"></span>
                                </button>

                                <div x-show="openCountryDropdown" x-cloak x-transition
                                    class="absolute top-full left-0 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-100 z-[60] overflow-hidden">
                                    <div class="p-2 border-b border-slate-50">
                                        <input type="text" x-model="searchCountryQuery" placeholder="Search country..."
                                            class="px-3 w-full h-8 text-xs rounded-lg outline-none bg-slate-50 focus:ring-1 ring-primary/30"
                                            dir="ltr">
                                    </div>
                                    <div class="overflow-y-auto max-h-48" dir="ltr">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <button type="button"
                                                @click="selectedCountry = country; openCountryDropdown = false; searchCustomer()"
                                                class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-slate-50">
                                                <svg class="w-5 h-auto rounded-[2px]" viewBox="0 0 36 24" fill="none"
                                                    x-html="country.svg"></svg>
                                                <span class="flex-1 text-xs font-bold text-slate-700"
                                                    x-text="country.name"></span>
                                                <span class="text-xs text-slate-400" x-text="country.dial_code"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <input type="tel" x-model="localPhoneNumber" @input="searchCustomer"
                                @focus="showCustomerDropdown = true" @click.away="showCustomerDropdown = false"
                                placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                class="flex-1 px-4 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-slate-800"
                                :class="selectedCustomerId ? 'font-bold text-emerald-700' : ''">

                            <button type="button" x-show="selectedCustomerId" @click="resetSelection"
                                class="absolute right-3 z-10 p-0.5 bg-white rounded-full text-slate-400 hover:text-red-500">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>

                        <div x-show="showCustomerDropdown && localPhoneNumber.length > 0 && !selectedCustomerId"
                            x-transition x-cloak
                            class="absolute top-[3.25rem] right-0 w-full bg-white border border-slate-100 rounded-xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden max-h-48 overflow-y-auto z-50">
                            <template x-for="customer in filteredCustomers" :key="customer.id">
                                <button type="button" @click="selectCustomer(customer)"
                                    class="flex justify-between items-center px-4 py-3 w-full text-right border-b transition-colors hover:bg-slate-50 border-slate-50">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-sm font-bold text-slate-800" x-text="customer.name"></span>
                                        <span class="text-xs text-right text-slate-500 dir-ltr"
                                            x-text="customer.phone"></span>
                                    </div>
                                    <span
                                        class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                                </button>
                            </template>
                            <div x-show="filteredCustomers.length === 0" class="px-4 py-3 text-center bg-slate-50/50">
                                <span class="text-xs font-bold text-slate-500">مستلم جديد، سيتم حفظه.</span>
                            </div>
                        </div>

                        <input type="text" name="receiver_name" required x-model="nameInput"
                            :readonly="selectedCustomerId !== null" placeholder="اسم المستلم..."
                            class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none"
                            :class="selectedCustomerId ? 'bg-slate-50 border-transparent text-slate-500 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 text-slate-700'">
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white p-5 rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.03)]">
            <div class="flex gap-2 items-center mb-4">
                <span
                    class="material-symbols-outlined text-blue-500 bg-blue-50 p-1.5 rounded-lg text-[20px]">inventory_2</span>
                <h3 class="font-bold text-slate-700 font-headline">محتويات الطرد</h3>
            </div>

            <div class="grid grid-cols-2 gap-4">

                {{-- ================= 1. حقل نوع الشحنة (نفس أسلوب الديسكتوب الذكي) ================= --}}
                <div class="flex flex-col gap-2" x-data="{
            isOpen: false,
            options: ['كرتون', 'كيس', 'مغلف'],
            packageType: '{{ old('package_type', $shipment->package_type ?? 'كرتون') }}'
        }">
                    <label class="text-[11px] font-bold text-slate-400">النوع <span
                            class="text-rose-500">*</span></label>

                    <div class="relative z-[40]">
                        {{-- حقل الإدخال النصي --}}
                        <input type="text" name="package_type" x-model="packageType" @focus="isOpen = true"
                            @click.outside="isOpen = false" placeholder="اختر أو اكتب..." autocomplete="off" required
                            class="px-4 pr-10 w-full h-12 text-sm font-bold rounded-xl border-none ring-1 outline-none bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 text-slate-700">

                        {{-- أيقونة السهم التفاعلية --}}
                        <button type="button" @click="isOpen = !isOpen" tabindex="-1"
                            class="flex absolute inset-y-0 right-0 items-center px-3 h-full rounded-r-xl transition-colors text-slate-400 hover:text-primary">
                            <span class="material-symbols-outlined text-[20px] transition-transform duration-300"
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
                    <label class="text-[11px] font-bold text-slate-400">الوزن (كجم)</label>
                    <input type="number" step="0.1" name="weight" placeholder="مثال: 2.5"
                        value="{{ old('weight', $shipment->weight) }}"
                        class="px-4 w-full h-12 text-sm text-left rounded-xl border-none ring-1 outline-none bg-slate-50 ring-slate-100 focus:ring-2 focus:ring-primary/20 text-slate-700"
                        dir="ltr">
                </div>
            </div>

            {{-- ================= 3. بيانات العسل ================= --}}
            <div x-data="{ showHoneyFields: {{ (old('no_gallons_honey', $shipment->no_gallons_honey) > 0 || old('no_honey_jars', $shipment->no_honey_jars) > 0) ? 'true' : 'false' }} }"
                class="mt-5">
                {{-- زر الإظهار والإخفاء --}}
                <button type="button" @click="showHoneyFields = !showHoneyFields"
                    class="flex justify-between items-center px-4 w-full h-12 text-sm font-bold transition-all rounded-2xl text-amber-600 bg-amber-50 hover:bg-amber-100 dark:text-amber-500 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 active:scale-[0.98]">
                    <div class="flex gap-2 items-center">
                        <span class="text-[20px] material-symbols-outlined">hive</span>
                        <span>إضافة بيانات العسل (جوالين / قروف)</span>
                    </div>
                    <span class="transition-transform duration-300 material-symbols-outlined"
                        :class="showHoneyFields ? 'rotate-180' : ''">expand_more</span>
                </button>

                {{-- الحقول --}}
                <div x-show="showHoneyFields" x-cloak x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2">

                    <div
                        class="grid grid-cols-2 gap-4 p-4 mt-3 rounded-2xl border bg-amber-50/50 dark:bg-amber-500/5 border-amber-100/50 dark:border-amber-500/10">
                        <div>
                            <label class="block mb-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-500">جوالين
                                العسل</label>
                            <input type="number" name="no_gallons_honey" placeholder="العدد"
                                value="{{ old('no_gallons_honey', $shipment->no_gallons_honey) }}"
                                class="px-3 w-full h-11 text-sm bg-white rounded-xl border border-amber-100 transition-all outline-none dark:bg-boxdark dark:border-amber-500/20 focus:border-amber-400 dark:focus:border-amber-500 text-on-surface dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-500">قروف
                                العسل</label>
                            <input type="number" name="no_honey_jars" placeholder="العدد"
                                value="{{ old('no_honey_jars', $shipment->no_honey_jars) }}"
                                class="px-3 w-full h-11 text-sm bg-white rounded-xl border border-amber-100 transition-all outline-none dark:bg-boxdark dark:border-amber-500/20 focus:border-amber-400 dark:focus:border-amber-500 text-on-surface dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= 4. الملاحظات ================= --}}
            <div class="flex flex-col gap-2 pt-2 mt-2">
                <label class="text-[11px] font-bold text-slate-400">ملاحظات إضافية</label>
                <textarea name="notes" rows="2" placeholder="اكتب أي ملاحظات هنا..."
                    class="p-4 w-full text-sm rounded-2xl border-none ring-1 outline-none resize-none bg-slate-50 ring-slate-100 focus:ring-2 focus:ring-primary/20 text-slate-700">{{ old('notes', $shipment->notes) }}</textarea>
            </div>
        </div>

        <div class="bg-white p-5 rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.03)]">
            <div class="flex gap-2 items-center mb-4">
                <span
                    class="material-symbols-outlined text-rose-500 bg-rose-50 p-1.5 rounded-lg text-[20px]">payments</span>
                <h3 class="font-bold text-slate-700 font-headline">المالية والدفع</h3>
            </div>

            <div class="space-y-4">
                <div class="flex flex-col gap-2">
                    <label class="text-[11px] font-bold text-slate-400">طريقة الدفع <span
                            class="text-red-500">*</span></label>
                    <select name="payment_method" x-model="paymentMethod"
                        class="px-4 w-full h-14 text-sm font-bold rounded-2xl border-none ring-1 appearance-none outline-none bg-slate-50 ring-slate-100 focus:ring-2 focus:ring-primary/20 text-slate-700">

                        @hasservice('Payment_Prepaid')
                        <option value="prepaid">مدفوع مقدماً</option>
                        @endhasservice

                        @hasservice('Payment_COD')
                        <option value="cod">الدفع عند الاستلام (على حساب المستلم)</option>
                        @endhasservice

                        @hasservice('Payment_Partial')
                        <option value="partial_payment">دفع جزئي</option>
                        @endhasservice

                        @hasservice('Payment_Credit')
                        <option value="customer_credit">آجل (على حساب المرسل)</option>
                        @endhasservice

                    </select>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-[11px] font-bold text-slate-400">المبلغ الإجمالي (ريال) <span
                            class="text-red-500">*</span></label>
                    <input type="number" required name="total_amount" placeholder="0.00"
                        value="{{ old('total_amount', $shipment->total_amount) }}"
                        class="px-4 w-full h-14 text-lg font-black text-left rounded-2xl border-none ring-1 outline-none bg-slate-50 ring-slate-100 focus:ring-2 focus:ring-primary/20 text-primary"
                        dir="ltr">
                </div>

                {{-- 💡 اللمسة الأمنية: نمنع طباعة حقل (المبلغ الجزئي) في الـ HTML من الأساس إذا لم يكن يملك الصلاحية
                --}}
                @hasservice('Payment_Partial')
                <div x-show="paymentMethod === 'partial_payment'" x-collapse class="flex flex-col gap-2">
                    <label class="text-[11px] font-bold text-rose-500">المبلغ المدفوع حالياً (ريال)</label>
                    <input type="number" name="partial_amount" placeholder="0.00"
                        value="{{ old('partial_amount', $shipment->partial_amount) }}"
                        class="px-4 w-full h-14 text-lg font-black text-left text-rose-600 rounded-2xl border-none ring-1 ring-rose-200 outline-none bg-rose-50/50 focus:ring-2 focus:ring-rose-400"
                        dir="ltr">
                </div>
                @endhasservice

            </div>
        </div>
        {{-- زر الارسال --}}
        <div class="pt-2 pb-4">
            <button type="submit" :disabled="isSubmitting"
                class="flex items-center justify-center gap-2 w-full h-14 rounded-2xl font-bold text-sm shadow-[0_8px_20px_rgba(59,130,246,0.25)] transition-all duration-200"
                :class="isSubmitting ? 'bg-slate-400 text-slate-100 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 active:scale-95'">

                {{-- الحالة العادية --}}
                <span x-show="!isSubmitting" class="flex gap-2 items-center">
                    <span class="material-symbols-outlined text-[22px]">save</span>
                    حفظ التعديلات
                </span>

                {{-- حالة التحميل --}}
                <span x-show="isSubmitting" x-cloak class="flex gap-2 items-center">
                    <svg class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    جاري الحفظ...
                </span>
            </button>
        </div>
    </form>
</div>
@endsection
@section('script')
    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('destinationLogic', (officesList) => ({
                offices: officesList || [],
                // التأكد من تحويل القيم إلى نصوص لضمان المطابقة
                selectedOfficeId: String('{{ old('office_id', $shipment->receiverBranch->office_id ?? '') }}'),
                selectedBranchId: String('{{ old('receiver_branch_id', $shipment->receiver_branch_id ?? '') }}'),
                availableBranches: [],

                init() {
                    // إذا كان هناك فرع محدد ولم يتم تحديد مكتب بعد (كما هو الحال عند التعديل)
                    if ((!this.selectedOfficeId || this.selectedOfficeId === '') && this.selectedBranchId && this.selectedBranchId !== '') {
                        // البحث عن المكتب الذي يحتوي على هذا الفرع
                        const foundOffice = this.offices.find(office => {
                            if (!office.branches) return false;
                            // نستخدم String() لضمان تطابق الأنواع (مثلاً "1" == "1")
                            return office.branches.some(branch => String(branch.id) === this.selectedBranchId);
                        });

                        if (foundOffice) {
                            this.selectedOfficeId = String(foundOffice.id);
                        }
                    }

                    // تحديث قائمة الفروع إذا تم تحديد مكتب
                    if (this.selectedOfficeId && this.selectedOfficeId !== '') {
                        this.updateBranches();
                    } else {
                        // كحالة افتراضية أخيرة (fallback)، تعيين المكتب الداخلي
                        const internalOffice = this.offices.find(o => String(o.id).startsWith('internal_'));
                        if (internalOffice) {
                            this.selectedOfficeId = String(internalOffice.id);
                            this.availableBranches = internalOffice.branches;
                        }
                    }
                },

                updateBranches() {
                    const office = this.offices.find(o => String(o.id) === String(this.selectedOfficeId));
                    this.availableBranches = office ? office.branches : [];

                    // التحقق مما إذا كان الفرع المحدد حالياً ينتمي للمكتب الجديد
                    if (this.selectedBranchId && this.selectedBranchId !== '') {
                        const branchExists = this.availableBranches.some(b => String(b.id) === this.selectedBranchId);
                        if (!branchExists) {
                            this.selectedBranchId = ''; // تفريغ الفرع إذا تم تغيير المكتب
                        }
                    }
                }
            }));


            /* =========================================================
               2. منطق إدارة العملاء (تم تحديثه ليدعم التعبئة المسبقة Prefill)
            ========================================================= */
            Alpine.data('customerSelect', (customersList, countriesList, initId, initName, initPhone) => ({
                // البيانات الأساسية
                customers: customersList || [],
                countries: countriesList || [],
                filteredCustomers: [],

                // متغيرات حالة الحقول (State) مجهزة بالبيانات القديمة
                localPhoneNumber: '',
                nameInput: initName || '',
                selectedCustomerId: initId || null,

                // متغيرات حالة القوائم المنسدلة (UI State)
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                showCustomerDropdown: false,

                init() {
                    // تعيين الدولة بناءً على رقم الهاتف القديم أو الافتراضي (اليمن)
                    let p = initPhone || '';
                    let selected = this.countries.find(c => c.code === 'YE') || this.countries[0];

                    if (p) {
                        const codes = this.countries.map(c => c.dial_code.replace('+', '')).sort((a, b) => b.length - a.length);
                        for (const code of codes) {
                            let regex = new RegExp('^(\\+|00)?' + code);
                            if (regex.test(p)) {
                                selected = this.countries.find(c => c.dial_code.replace('+', '') === code);
                                p = p.replace(regex, '');
                                break;
                            }
                        }
                    }
                    this.selectedCountry = selected;
                    this.localPhoneNumber = p;
                },

                get filteredCountries() {
                    if (this.searchCountryQuery === '') return this.countries;
                    const query = this.searchCountryQuery.toLowerCase();
                    return this.countries.filter(c =>
                        c.name.toLowerCase().includes(query) ||
                        c.dial_code.includes(query)
                    );
                },

                get fullPhoneNumber() {
                    if (!this.localPhoneNumber) return '';
                    let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';
                    return dialCode + this.localPhoneNumber;
                },

                searchCustomer() {
                    this.selectedCustomerId = null; // إعادة تعيين الاختيار إذا قام المستخدم بتعديل الرقم
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
                    let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';

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