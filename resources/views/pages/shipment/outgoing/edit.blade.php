@extends('layouts.app')

@section('title', 'تعديل طرد #' . $shipment->bond_number)

@section('content')
    <div class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl" 
        x-data="{ 
            paymentMethod: '{{ old('payment_method', $shipment->payment_method) }}', 
            isSubmitting: false, 
            priceAgreed: {{ old('is_price_agreed', $shipment->is_price_agreed ? '1' : '0') == '1' ? 'true' : 'false' }},
            togglePriceAgreed() {
                this.priceAgreed = !this.priceAgreed;
            }
        }">

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
                        <h1 class="text-xl font-black md:text-2xl font-headline text-on-surface dark:text-white">تعديل طرد
                        </h1>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-bodydark">تعديل بيانات بوليصة الشحن
                            #{{ $shipment->bond_number }}</p>
                    </div>
                </div>

                {{-- زر الحفظ يظهر في الديسكتوب في الأعلى --}}
                <button type="submit" form="shipmentForm" :disabled="isSubmitting"
                    class="hidden gap-2 justify-center items-center px-6 h-11 text-sm font-bold text-white rounded-xl shadow-md transition-all md:flex bg-primary hover:bg-primary-hover shadow-primary/20 active:scale-95 disabled:opacity-70 disabled:shadow-none">
                    <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">save</span>
                    <span x-show="isSubmitting"
                        class="animate-spin material-symbols-outlined text-[18px]">progress_activity</span>
                    <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
                </button>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- ================= محتوى النموذج (Grid Layout) ================= --}}
        <div class="p-4 mx-auto mt-4 max-w-7xl md:p-6">

            <form id="shipmentForm" action="{{ route('shipment.outgoing.update', $shipment->id) }}" method="POST"
                @submit="if(!isSubmitting) { isSubmitting = true; return true; } else { $event.preventDefault(); return false; }"
                class="grid grid-cols-1 gap-6 items-start lg:grid-cols-12">
                @csrf
                @method('PUT')

                {{-- الجانب الأيمن: بيانات الوجهة والعملاء (8 أعمدة) --}}
                <div class="flex flex-col gap-6 lg:col-span-7 xl:col-span-8">

                    {{-- 1. بيانات الفروع (الوجهة) --}}
                    <div x-data='destinationLogic(
                        @json($destinations),
                        @json(old('receiver_branch_id', $initialBranchId)),
                        @json(old('office_id', $initialOfficeId ?? ''))
                    )'
                        class="bg-white dark:bg-boxdark p-6 md:p-8 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm relative group/card"
                        :class="isOpen ? 'relative z-[100]' : 'relative z-20'">

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
                                    <p class="mt-1 text-xs font-medium text-gray-500 dark:text-bodydark">
                                        المكتب الحالي والوجهة المستلمة
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                                {{-- حقل المكتب (يعرض المكتب الحالي فقط - للقراءة فقط) --}}
                                <div>
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                                        المكتب الحالي 
                                    </label>
                                    <div class="relative">
                                        <input type="text" readonly
                                            value="{{ $currentOffice['name'] ?? '🏠 مكتبنا الحالي' }}"
                                            class="pr-4 pl-10 w-full h-12 text-sm font-bold bg-gray-50 rounded-xl border border-gray-200 transition-all cursor-not-allowed outline-none select-none text-on-surface dark:text-white dark:bg-boxdark-2 dark:border-boxdark-2">
                                        <input type="hidden" name="office_id" x-model="selectedOfficeId">
                                        <span class="absolute left-3 top-1/2 text-gray-400 -translate-y-1/2 pointer-events-none">
                                            <span class="material-symbols-outlined text-[20px]">store</span>
                                        </span>
                                    </div>
                                </div>

                                {{-- حقل الفرع أو الوجهة المستلمة (Searchable Combobox) --}}
                                <div class="space-y-1.5 relative z-[101]" @click.outside="onBlurOutside()">
                                    <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                                        الفرع أو الوجهة المستلمة 
                                    </label>
                                    <div class="relative">
                                        <input type="hidden" name="receiver_branch_id" :value="selectedBranchId">

                                        {{-- حقل الإدخال والبحث المرئي --}}
                                        <div class="flex relative items-center">
                                            <input type="text"
                                                x-ref="destinationInput"
                                                x-model="searchQuery"
                                                @focus="onInputFocus($event)"
                                                @input="onInputChange()"
                                                @keydown.enter.prevent="if(filteredDestinations.length > 0) { selectDestination(filteredDestinations[0]); }"
                                                placeholder="ابحث أو اختر الفرع أو الوجهة..."
                                                autocomplete="off"
                                                
                                                class="pr-10 pl-16 w-full h-12 text-sm font-bold bg-white rounded-xl border border-gray-200 transition-all outline-none dark:bg-boxdark dark:border-boxdark-2 text-on-surface dark:text-white focus:border-green-500 focus:ring-2 focus:ring-green-500/20 placeholder:text-gray-400 placeholder:font-normal"
                                                :class="selectedBranchId ? 'border-green-500 bg-green-50/20 text-green-900 dark:text-green-400 font-extrabold' : ''">

                                            {{-- أيقونة الموقع في اليمين --}}
                                            <span class="absolute right-3 top-1/2 transition-colors -translate-y-1/2 pointer-events-none"
                                                :class="selectedBranchId ? 'text-green-600' : 'text-gray-400'">
                                                <span class="material-symbols-outlined text-[20px]">location_on</span>
                                            </span>

                                            {{-- أزرار التحكم في اليسار (مسح وسهم الفتح) --}}
                                            <div class="flex absolute left-3 top-1/2 gap-1 items-center -translate-y-1/2">
                                                {{-- زر المسح السريع --}}
                                                <button type="button" 
                                                    x-show="searchQuery.length > 0 || selectedBranchId" 
                                                    x-cloak
                                                    @click="clearSelection(); $refs.destinationInput.focus()"
                                                    class="p-1 text-gray-400 rounded-full transition-colors hover:text-rose-500 hover:bg-gray-100 dark:hover:bg-boxdark-2">
                                                    <span class="material-symbols-outlined text-[18px] block">close</span>
                                                </button>

                                                {{-- سهم القائمة المنسدلة --}}
                                                <button type="button" 
                                                    @click="toggleDropdown()"
                                                    class="p-1 text-gray-400 rounded-lg transition-colors hover:text-green-600">
                                                    <span class="material-symbols-outlined text-[20px] transition-transform duration-200 block"
                                                        :class="isOpen ? 'rotate-180 text-green-600' : ''">expand_more</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- القائمة المنسدلة للوجهات مع سكرول سلس وترتيب متناسق بدون أي تداخل --}}
                                        <div x-show="isOpen" 
                                            x-cloak 
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-1"
                                            class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-boxdark border border-gray-200 dark:border-boxdark-2 rounded-2xl shadow-2xl max-h-60 overflow-y-auto custom-scrollbar z-[120]">
                                            
                                            {{-- قائمة العناصر المجمعة --}}
                                            <template x-for="(items, groupName) in groupedFilteredDestinations" :key="groupName">
                                                <div class="border-b border-gray-100 dark:border-boxdark-2/60 last:border-b-0">
                                                    {{-- رأس المجموعة --}}
                                                    <div class="flex sticky top-0 z-10 justify-between items-center px-3.5 py-2 border-b border-gray-100 backdrop-blur-md bg-gray-50/95 dark:bg-boxdark-2/95 dark:border-boxdark">
                                                        <div class="flex gap-2 items-center">
                                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                                            <span class="text-xs font-black text-gray-700 dark:text-gray-200" x-text="groupName"></span>
                                                        </div>
                                                        <span class="text-[10px] font-bold text-gray-400 bg-white dark:bg-boxdark px-2 py-0.5 rounded-full shadow-sm border border-gray-100 dark:border-boxdark-2" x-text="items.length"></span>
                                                    </div>

                                                    {{-- عناصر المجموعة --}}
                                                    <div class="p-1.5 space-y-1">
                                                        <template x-for="dest in items" :key="dest.key">
                                                            <button type="button"
                                                                @click="selectDestination(dest)"
                                                                class="flex justify-between items-center px-3 py-2.5 w-full text-right rounded-xl transition-all group/item"
                                                                :class="dest.key === selectedDestinationKey ? 'bg-green-50 dark:bg-green-500/15 text-green-800 dark:text-green-300 font-black ring-1 ring-green-500/30' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-boxdark-2/70'">
                                                                
                                                                <div class="flex gap-2.5 items-center truncate">
                                                                    <div class="flex justify-center items-center w-7 h-7 rounded-lg transition-colors shrink-0"
                                                                        :class="{
                                                                            'bg-green-100/80 text-green-600 dark:bg-green-500/20 dark:text-green-400': dest.type === 'internal',
                                                                            'bg-blue-100/80 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400': dest.type === 'trusted',
                                                                            'bg-amber-100/80 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400': dest.type === 'untrusted'
                                                                        }">
                                                                        <span class="material-symbols-outlined text-[17px]"
                                                                            x-text="dest.type === 'internal' ? 'domain' : (dest.type === 'trusted' ? 'verified' : 'swap_horiz')">
                                                                        </span>
                                                                    </div>
                                                                    <span class="text-xs font-bold truncate transition-colors group-hover/item:text-green-700 dark:group-hover/item:text-green-400" x-text="dest.name"></span>
                                                                </div>

                                                                <span x-show="dest.key === selectedDestinationKey"
                                                                    class="material-symbols-outlined text-green-600 dark:text-green-400 text-[18px] shrink-0 mr-2">
                                                                    check_circle
                                                                </span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- عند عدم وجود نتائج للبحث --}}
                                            <div x-show="!hasResults" class="p-6 text-center">
                                                <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[32px] mb-1 block">search_off</span>
                                                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">لا توجد نتائج مطابقة</p>
                                                <p class="mt-1 text-xs text-gray-400" x-text="'ابحث باسم مختلف عن: ' + searchQuery"></p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. بيانات العملاء --}}
                    <div
                        class="bg-white dark:bg-boxdark p-6 md:p-8 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm relative z-10">
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
                            <div x-data="customerSelect({{ $customers }}, @js(array_values(config('countries', []))), @js([
    'id' => old('sender_customer_id', $shipment->sender_customer_id),
    'name' => old('sender_name', $shipment->senderCustomer->name ?? $shipment->sender_name),
    'phone' => old('sender_phone', $shipment->senderCustomer->phone ?? $shipment->sender_phone),
]))" class="relative z-50">
                                <h4
                                    class="pb-2 mb-3 text-sm font-bold text-gray-700 border-b border-gray-100 dark:text-gray-200 dark:border-boxdark-2">
                                    بيانات المرسل</h4>

                                <div class="grid relative grid-cols-1 gap-4 md:grid-cols-2">
                                    <input type="hidden" name="sender_customer_id" x-model="selectedCustomerId">
                                    <input type="hidden" name="sender_phone" :value="fullPhoneNumber">

                                    {{-- اسم المرسل مع خاصية البحث بالاسم --}}
                                    <div class="relative" @click.away="showCustomerDropdown = false">
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">
                                            اسم المرسل 
                                        </label>
                                        <div class="flex relative items-center">
                                            <input type="text" name="sender_name"  x-model="nameInput"
                                                @input="searchCustomer"
                                                @focus="if(nameInput.trim().length > 0 && !selectedCustomerId) showCustomerDropdown = true"
                                                :readonly="selectedCustomerId !== null"
                                                placeholder="ابحث بالاسم أو اكتب اسماً جديداً..." autocomplete="off"
                                                class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white"
                                                :class="selectedCustomerId ?
                                                    'bg-primary-container/20 border-primary/40 text-primary font-bold dark:bg-primary/10 pl-24 cursor-not-allowed' :
                                                    'bg-white border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20'">

                                            {{-- مؤشر العميل المسجل وزر الإلغاء --}}
                                            <div x-show="selectedCustomerId" class="flex absolute left-2 z-10 gap-1.5 items-center">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold text-primary bg-primary/10 rounded-md">
                                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                                    مسجل
                                                </span>
                                                <button type="button" @click="resetSelection"
                                                    class="p-1 text-gray-400 rounded-full transition-colors hover:text-error hover:bg-red-50 dark:hover:bg-red-900/20"
                                                    title="إلغاء التحديد وتغيير العميل">
                                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- قائمة نتائج البحث بالاسم وعرض الرقم --}}
                                        <div x-show="showCustomerDropdown && nameInput.trim().length > 0 && !selectedCustomerId"
                                            x-transition x-cloak
                                            class="absolute top-full right-0 mt-1.5 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-xl z-[65] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                            <template x-for="customer in filteredCustomers" :key="customer.id">
                                                <button type="button" @click="selectCustomer(customer)"
                                                    class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-primary/5 dark:hover:bg-boxdark-2 dark:border-boxdark group">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-sm font-bold transition-colors text-on-surface dark:text-white group-hover:text-primary"
                                                            x-text="customer.name"></span>
                                                        <span
                                                            class="flex gap-1 items-center font-mono text-xs font-medium text-right text-gray-500 dark:text-bodydark dir-ltr">
                                                            <span class="material-symbols-outlined text-[14px] text-gray-400">call</span>
                                                            <span x-text="customer.phone || 'بدون رقم'"></span>
                                                        </span>
                                                    </div>
                                                    <div class="flex gap-1 items-center text-xs font-bold opacity-0 transition-opacity text-primary group-hover:opacity-100">
                                                        <span>اختيار</span>
                                                        <span class="material-symbols-outlined text-[16px]">arrow_back_ios</span>
                                                    </div>
                                                </button>
                                            </template>
                                            <div x-show="filteredCustomers.length === 0"
                                                class="px-4 py-3 text-center bg-surface dark:bg-boxdark-2">
                                                <span class="text-xs font-bold text-gray-500 dark:text-bodydark">مرسل جديد،
                                                    سيتم حفظه تلقائياً.</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- رقم الهاتف (يعرض الرقم تلقائياً عند الاختيار) --}}
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">رقم
                                            الهاتف <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(اختياري)</span></label>
                                        <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40 focus-within:border-primary"
                                            :class="selectedCustomerId ?
                                                'bg-primary-container/20 dark:bg-primary/10 ring-primary/40' : ''"
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
                                                                @click="selectedCountry = country; openCountryDropdown = false;"
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

                                            <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX"
                                                 inputmode="numeric" autocomplete="off"
                                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                                class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                                :class="selectedCustomerId ? 'font-bold text-primary' : ''">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- المستلم --}}
                            <div x-data="customerSelect({{ $customers }}, @js(array_values(config('countries', []))), @js([
    'id' => old('receiver_customer_id', $shipment->receiver_customer_id),
    'name' => old('receiver_name', $shipment->receiverCustomer->name ?? $shipment->receiver_name),
    'phone' => old('receiver_phone', $shipment->receiverCustomer->phone ?? $shipment->receiver_phone),
]))" class="relative z-40">
                                <h4
                                    class="pb-2 mb-3 text-sm font-bold text-gray-700 border-b border-gray-100 dark:text-gray-200 dark:border-boxdark-2">
                                    بيانات المستلم</h4>

                                <div class="grid relative grid-cols-1 gap-4 md:grid-cols-2">
                                    <input type="hidden" name="receiver_customer_id" x-model="selectedCustomerId">
                                    <input type="hidden" name="receiver_phone" :value="fullPhoneNumber">

                                    {{-- اسم المستلم مع خاصية البحث بالاسم --}}
                                    <div class="relative" @click.away="showCustomerDropdown = false">
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">
                                            اسم المستلم 
                                        </label>
                                        <div class="flex relative items-center">
                                            <input type="text" name="receiver_name"  x-model="nameInput"
                                                @input="searchCustomer"
                                                @focus="if(nameInput.trim().length > 0 && !selectedCustomerId) showCustomerDropdown = true"
                                                :readonly="selectedCustomerId !== null"
                                                placeholder="ابحث بالاسم أو اكتب اسماً جديداً..." autocomplete="off"
                                                class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white"
                                                :class="selectedCustomerId ?
                                                    'bg-green-50/50 border-green-400 text-green-700 font-bold dark:bg-green-500/10 dark:text-green-400 pl-24 cursor-not-allowed' :
                                                    'bg-white border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-500/20'">

                                            {{-- مؤشر العميل المسجل وزر الإلغاء --}}
                                            <div x-show="selectedCustomerId" class="flex absolute left-2 z-10 gap-1.5 items-center">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold text-green-600 bg-green-50 rounded-md dark:bg-green-500/20 dark:text-green-300">
                                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                                    مسجل
                                                </span>
                                                <button type="button" @click="resetSelection"
                                                    class="p-1 text-gray-400 rounded-full transition-colors hover:text-error hover:bg-red-50 dark:hover:bg-red-900/20"
                                                    title="إلغاء التحديد وتغيير العميل">
                                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- قائمة نتائج البحث بالاسم وعرض الرقم --}}
                                        <div x-show="showCustomerDropdown && nameInput.trim().length > 0 && !selectedCustomerId"
                                            x-transition x-cloak
                                            class="absolute top-full right-0 mt-1.5 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-xl z-[65] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                            <template x-for="customer in filteredCustomers" :key="customer.id">
                                                <button type="button" @click="selectCustomer(customer)"
                                                    class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-green-50/50 dark:hover:bg-boxdark-2 dark:border-boxdark group">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-sm font-bold transition-colors text-on-surface dark:text-white group-hover:text-green-600"
                                                            x-text="customer.name"></span>
                                                        <span
                                                            class="flex gap-1 items-center font-mono text-xs font-medium text-right text-gray-500 dark:text-bodydark dir-ltr">
                                                            <span class="material-symbols-outlined text-[14px] text-gray-400">call</span>
                                                            <span x-text="customer.phone || 'بدون رقم'"></span>
                                                        </span>
                                                    </div>
                                                    <div class="flex gap-1 items-center text-xs font-bold text-green-600 opacity-0 transition-opacity group-hover:opacity-100">
                                                        <span>اختيار</span>
                                                        <span class="material-symbols-outlined text-[16px]">arrow_back_ios</span>
                                                    </div>
                                                </button>
                                            </template>
                                            <div x-show="filteredCustomers.length === 0"
                                                class="px-4 py-3 text-center bg-surface dark:bg-boxdark-2">
                                                <span class="text-xs font-bold text-gray-500 dark:text-bodydark">مستلم
                                                    جديد، سيتم حفظه تلقائياً.</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- رقم الهاتف (يعرض الرقم تلقائياً عند الاختيار) --}}
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">رقم
                                            الهاتف <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(اختياري)</span></label>
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
                                                                @click="selectedCountry = country; openCountryDropdown = false;"
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

                                            <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX"
                                                 inputmode="numeric" autocomplete="off"
                                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                                class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                                :class="selectedCustomerId ? 'font-bold text-green-600 dark:text-green-400' : ''">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- الجانب الأيسر: محتويات الطرد والدفع (4 أعمدة) --}}
                {{-- 💡 جلب القيم السابقة من الداتابيز (Edit Mode Binding) --}}
                <div class="flex flex-col gap-6 lg:col-span-5 xl:col-span-4" x-data="{
                    packageFee: {{ old('package_fee', $shipment->package_fee ?? 0) }},
                    packageCommissionRate: {{ old('package_commission_rate', $shipment->package_commission_rate ?? 0) }},
                    honeyFee: {{ old('honey_fee', $shipment->honey_fee ?? 0) }},
                    honeyCommissionRate: {{ old('honey_commission_rate', $shipment->honey_commission_rate ?? 0) }},
                
                    // حساب إجمالي المبلغ (الطرد + العسل)
                    get totalAmount() {
                        return (parseFloat(this.packageFee) || 0) + (parseFloat(this.honeyFee) || 0);
                    },
                    // حساب مبلغ عمولة الطرد
                    get packageCommissionCalc() {
                        return (((parseFloat(this.packageFee) || 0) * (parseFloat(this.packageCommissionRate) || 0)) / 100).toFixed(2);
                    },
                    // حساب مبلغ عمولة العسل
                    get honeyCommissionCalc() {
                        return (((parseFloat(this.honeyFee) || 0) * (parseFloat(this.honeyCommissionRate) || 0)) / 100).toFixed(2);
                    }
                }">

                    {{-- 3. محتويات الطرد --}}
                    <div
                        class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm">
                        <h3
                            class="flex gap-2 items-center mb-6 text-lg font-black text-on-surface dark:text-white font-headline">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-blue-500 bg-blue-50 rounded-lg dark:bg-blue-500/10 shrink-0">
                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                            </div>
                            محتويات الطرد والتسعير
                        </h3>

                        <div class="grid grid-cols-2 gap-4">

                            {{-- ================= 1. حقل نوع الشحنة ================= --}}
                            <div class="flex flex-col col-span-1 gap-2" x-data="{
                                isOpen: false,
                                options: ['كرتون', 'كيس'],
                                packageType: '{{ old('package_type', $shipment->package_type ?? '') }}'
                            }">
                                <label class="text-[11px] font-bold text-slate-500">نوع الشحنة</label>

                                <div class="relative z-[40]" @click.outside="isOpen = false">
                                    <input type="text" name="package_type" x-model="packageType"
                                        @focus="isOpen = true" @click="isOpen = true"
                                        placeholder="اختر أو اكتب النوع..." autocomplete="off" 
                                        class="px-4 w-full h-12 text-sm font-bold rounded-xl border transition-all outline-none border-slate-200 bg-slate-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">

                                    <button type="button" @click="isOpen = !isOpen" tabindex="-1"
                                        class="flex absolute inset-y-0 left-0 items-center px-3 h-full rounded-l-xl transition-colors text-slate-400 hover:text-primary">
                                        <span
                                            class="material-symbols-outlined text-[20px] transition-transform duration-300"
                                            :class="isOpen ? 'rotate-180 text-primary' : ''">expand_more</span>
                                    </button>

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

                            {{-- ================= 2. حقل عدد الطرود ================= --}}
                            <div class="flex flex-col col-span-1 gap-2">
                                <label class="text-[11px] font-bold text-slate-500">عدد الطرود</label>
                                <div class="relative">
                                    <input type="number" name="parcels_quantity" value="{{ old('parcels_quantity', $shipment->parcels_quantity ?? 0) }}"
                                        placeholder="0" min="0" dir="ltr"
                                        class="px-4 w-full h-12 text-sm font-bold text-left rounded-xl border transition-all outline-none border-slate-200 bg-slate-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">
                                </div>
                            </div>

                            {{-- ================= 3. تسعيرة الطرد وعمولته ================= --}}
                            <div class="flex flex-col col-span-1 gap-2 mt-2">
                                <label class="text-[11px] font-bold text-slate-500">أجرة الطرد (ريال) </label>
                                <input type="number" name="package_fee" x-model.number="packageFee" placeholder="0"
                                    min="0" dir="ltr" 
                                    class="px-4 w-full h-12 text-sm font-black text-left text-blue-600 rounded-xl border border-blue-200 transition-all outline-none bg-blue-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                            </div>

                            <div class="flex flex-col col-span-1 gap-2 mt-2">
                                <label class="text-[11px] font-bold text-slate-500">نسبة العمولة (%)</label>
                                <div class="relative">
                                    <input type="number" name="package_commission_rate"
                                        x-model.number="packageCommissionRate" placeholder="0" min="0"
                                        max="100" dir="ltr"
                                        class="px-4 w-full h-12 text-sm font-bold text-left rounded-xl border transition-all outline-none border-slate-200 bg-slate-50/50 focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700">
                                    {{-- عرض مباشر للمبلغ المحسوب --}}
                                    <div
                                        class="absolute -bottom-5 left-1 flex gap-1 items-center text-[10px] font-bold text-emerald-600">
                                        <span class="material-symbols-outlined text-[12px]">payments</span>
                                        المبلغ: <span x-text="packageCommissionCalc"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================= بيانات العسل ================= --}}
                        {{-- <div x-data="{ showHoneyFields: {{ old('no_gallons_honey', $shipment->no_gallons_honey) > 0 || old('no_honey_jars', $shipment->no_honey_jars) > 0 || old('honey_fee', $shipment->honey_fee) > 0 ? 'true' : 'false' }} }" class="mt-8"> --}}

                            {{-- <button type="button" @click="showHoneyFields = !showHoneyFields"
                                class="flex justify-between items-center px-4 w-full h-12 text-sm font-bold transition-all rounded-2xl text-amber-600 bg-amber-50 hover:bg-amber-100 dark:text-amber-500 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 active:scale-[0.98]">
                                <div class="flex gap-2 items-center">
                                    <span class="text-[20px] material-symbols-outlined">hive</span>
                                    <span>إضافة بيانات وتسعيرة العسل</span>
                                </div>
                                <span class="transition-transform duration-300 material-symbols-outlined"
                                    :class="showHoneyFields ? 'rotate-180' : ''">expand_more</span>
                            </button> --}}

                            {{-- <div x-show="showHoneyFields" x-cloak x-collapse>
                                <div
                                    class="grid grid-cols-2 gap-4 p-4 mt-3 rounded-2xl border bg-amber-50/50 dark:bg-amber-500/5 border-amber-100/50 dark:border-amber-500/10"> --}}

                                    {{-- الكميات --}}
                                    {{-- <div>
                                        <label
                                            class="block mb-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-500">جوالين
                                            العسل</label>
                                        <input type="number" name="no_gallons_honey"
                                            value="{{ old('no_gallons_honey', $shipment->no_gallons_honey) }}"
                                            placeholder="العدد" min="0"
                                            class="px-3 w-full h-11 text-sm bg-white rounded-xl border border-amber-100 transition-all outline-none dark:bg-boxdark dark:border-amber-500/20 focus:border-amber-400 text-on-surface dark:text-white">
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-500">قروف
                                            العسل</label>
                                        <input type="number" name="no_honey_jars"
                                            value="{{ old('no_honey_jars', $shipment->no_honey_jars) }}"
                                            placeholder="العدد" min="0"
                                            class="px-3 w-full h-11 text-sm bg-white rounded-xl border border-amber-100 transition-all outline-none dark:bg-boxdark dark:border-amber-500/20 focus:border-amber-400 text-on-surface dark:text-white">
                                    </div> --}}

                                    {{-- تسعيرة وعمولة العسل --}}
                                    {{-- <div class="mt-2">
                                        <label
                                            class="block mb-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-500">أجرة
                                            العسل (ريال)</label>
                                        <input type="number" name="honey_fee" x-model.number="honeyFee" placeholder="0"
                                            min="0" dir="ltr"
                                            class="px-3 w-full h-11 text-sm font-black text-left text-amber-700 bg-white rounded-xl border border-amber-200 transition-all outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20">
                                    </div> --}}
                                    {{-- <div class="relative mt-2">
                                        <label
                                            class="block mb-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-500">نسبة
                                            العمولة (%)</label> --}}
                                        {{-- <input type="number" name="honey_commission_rate" --}}
                                            {{-- x-model.number="honeyCommissionRate" placeholder="0" min="0"
                                            max="100" dir="ltr" --}}
                                            {{-- class="px-3 w-full h-11 text-sm font-bold text-left bg-white rounded-xl border border-amber-100 transition-all outline-none focus:border-amber-400"> --}}
                                        {{-- عرض مباشر للمبلغ المحسوب --}}
                                        {{-- <div
                                            class="absolute -bottom-5 left-1 flex gap-1 items-center text-[10px] font-bold text-emerald-600">
                                            <span class="material-symbols-outlined text-[12px]">payments</span>
                                            المبلغ: <span x-text="honeyCommissionCalc"></span>
                                        </div> --}}
                                    {{-- </div>

                                </div>
                            </div>
                        </div> --}}

                        <div class="mt-8">
                            <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">ملاحظات
                                إضافية</label>
                            <textarea name="notes" rows="2" placeholder="اكتب أي ملاحظات هنا..."
                                class="p-4 w-full text-sm rounded-2xl border border-gray-200 transition-all outline-none resize-none bg-surface dark:bg-boxdark-2 dark:border-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-on-surface dark:text-white">{{ old('notes', $shipment->notes) }}</textarea>
                        </div>
                    </div>

                    {{-- ================= 4. المالية والدفع ================= --}}
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
                                <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">
                                    طريقة الدفع 
                                </label>
                                <div class="relative">
                                    <select name="payment_method" x-model="paymentMethod"
                                        class="px-4 w-full h-14 text-sm font-bold rounded-2xl border border-gray-200 transition-all appearance-none outline-none text-on-surface dark:text-white bg-surface dark:bg-boxdark-2 dark:border-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20">
                                        <option value="prepaid">مدفوع مقدماً</option>
                                        @hasservice('Payment_COD')
                                            <option value="cod">الدفع عند الاستلام (على المستلم)</option>
                                        @endhasservice
                                        <option value="partial_payment">دفع جزئي</option>
                                        @hasservice('Payment_Credit')
                                            <option value="customer_credit">آجل (على حساب المرسل)</option>
                                        @endhasservice
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">المبلغ
                                    الإجمالي (ريال) </label>
                                <input type="number"  name="total_amount" :value="totalAmount" readonly
                                    tabindex="-1"
                                    class="px-4 w-full h-14 text-xl font-black text-left rounded-2xl border border-transparent shadow-inner transition-all cursor-not-allowed outline-none bg-slate-100 dark:bg-boxdark-2 text-primary dark:text-primary"
                                    dir="ltr">
                                <p class="mt-1.5 text-[10px] font-bold text-gray-400">يتم حسابه تلقائياً (أجرة الطرد + أجرة
                                    العسل)</p>
                            </div>

                            <div x-show="paymentMethod === 'partial_payment'" x-collapse>
                                <label class="block text-[11px] font-bold text-rose-500 dark:text-rose-400 mb-1.5">المبلغ
                                    المدفوع حالياً (ريال)</label>
                                <input type="number" name="partial_amount"
                                    value="{{ old('partial_amount', $shipment->partial_amount) }}" placeholder="0.00"
                                    min="0" :max="totalAmount"
                                    class="px-4 w-full h-14 text-xl font-black text-left text-rose-600 rounded-2xl border border-rose-200 transition-all outline-none bg-rose-50/50 dark:bg-rose-500/5 focus:border-rose-400"
                                    dir="ltr">
                            </div>

                            {{-- ================= خيار الاتفاق على السعر ================= --}}
                            <div class="mt-2">
                                <label @click="togglePriceAgreed()" class="flex gap-4 items-center p-4 rounded-2xl border-2 transition-all duration-300 cursor-pointer select-none group/toggle"
                                    :class="priceAgreed
                                        ? 'bg-emerald-50 dark:bg-emerald-500/10 border-emerald-400 dark:border-emerald-500/50 shadow-sm shadow-emerald-500/10'
                                        : 'bg-surface dark:bg-boxdark-2 border-gray-200 dark:border-boxdark hover:border-gray-300 dark:hover:border-gray-600'">

                                    {{-- Hidden Checkbox --}}
                                    <input type="hidden" name="is_price_agreed" :value="priceAgreed ? '1' : '0'">

                                    {{-- Toggle Switch --}}
                                    <div class="relative w-12 h-7 rounded-full transition-colors duration-300 shrink-0"
                                        :class="priceAgreed ? 'bg-emerald-500' : 'bg-gray-300 dark:bg-gray-600'">
                                        <div class="absolute top-0.5 w-6 h-6 bg-white rounded-full shadow-md transition-all duration-300 ease-in-out"
                                            :class="priceAgreed ? 'right-0.5' : 'right-[calc(100%-1.625rem)]'">
                                        </div>
                                    </div>

                                    {{-- Icon --}}
                                    <div class="flex justify-center items-center w-10 h-10 rounded-xl transition-all duration-300 shrink-0"
                                        :class="priceAgreed
                                            ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400'
                                            : 'bg-gray-100 dark:bg-boxdark text-gray-400 dark:text-gray-500'">
                                        <span class="material-symbols-outlined text-[22px] transition-transform duration-300"
                                            :class="priceAgreed ? 'scale-110' : 'scale-100'">
                                            <template x-if="priceAgreed">
                                                <span>handshake</span>
                                            </template>
                                            <template x-if="!priceAgreed">
                                                <span>pending</span>
                                            </template>
                                        </span>
                                    </div>

                                    {{-- Text --}}
                                    <div class="flex-1">
                                        <p class="text-sm font-bold transition-colors duration-300"
                                            :class="priceAgreed
                                                ? 'text-emerald-700 dark:text-emerald-400'
                                                : 'text-gray-700 dark:text-gray-300'"
                                            x-text="priceAgreed ? 'تم الاتفاق على السعر' : 'لم يتم الاتفاق على السعر بعد'">
                                            {{ $shipment->is_price_agreed ? 'تم الاتفاق على السعر' : 'لم يتم الاتفاق على السعر بعد' }}
                                        </p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5"
                                            x-text="priceAgreed ? 'السعر معتمد ومتفق عليه مع العميل' : 'اضغط للتحويل إلى تم الاتفاق على السعر'">
                                            {{ $shipment->is_price_agreed ? 'السعر معتمد ومتفق عليه مع العميل' : 'اضغط للتحويل إلى تم الاتفاق على السعر' }}
                                        </p>
                                    </div>

                                    {{-- Check Badge --}}
                                    <div class="transition-all duration-300"
                                        :class="priceAgreed ? 'opacity-100 scale-100' : 'opacity-0 scale-75'">
                                        <div class="flex justify-center items-center w-8 h-8 text-white bg-emerald-500 rounded-full shadow-sm shadow-emerald-500/30">
                                            <span class="material-symbols-outlined text-[18px]">check</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- زر الحفظ للموبايل --}}
                    <div class="mt-2 md:hidden">
                        <button type="submit" :disabled="isSubmitting"
                            class="flex gap-2 justify-center items-center w-full h-14 text-sm font-bold rounded-2xl shadow-lg transition-all duration-200"
                            :class="isSubmitting ? 'bg-gray-400 dark:bg-gray-600 text-white cursor-not-allowed shadow-none' :
                                'bg-primary text-white hover:bg-primary-hover shadow-primary/30 active:scale-95'">
                            <span x-show="!isSubmitting" class="material-symbols-outlined text-[22px]">save</span>
                            <span x-show="isSubmitting"
                                class="animate-spin material-symbols-outlined text-[22px]">progress_activity</span>
                            <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
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

            // ==========================================
            // 1. منطق تحديد الوجهة (المكتب والفرع)
            // ==========================================
            Alpine.data('destinationLogic', (destinationsList, initialBranchId, initialOfficeId) => ({
                destinations: destinationsList || [],
                selectedOfficeId: initialOfficeId ? String(initialOfficeId) : '',
                selectedBranchId: initialBranchId ? String(initialBranchId) : '',
                selectedDestinationKey: '',
                searchQuery: '',
                isOpen: false,
                isSearching: false,

                init() {
                    // إذا تم تمرير فرع مسبقاً (سواء من old أو من البيانات الأولية)
                    if (this.selectedBranchId) {
                        const matched = this.destinations.find(d => {
                            const matchBranch = String(d.branch_id) === String(this.selectedBranchId);
                            if (this.selectedOfficeId) {
                                return matchBranch && String(d.office_id) === String(this.selectedOfficeId);
                            }
                            return matchBranch;
                        });

                        if (matched) {
                            this.selectedDestinationKey = matched.key;
                            this.selectedOfficeId = String(matched.office_id);
                            this.selectedBranchId = String(matched.branch_id);
                            this.searchQuery = matched.name;
                        }
                    }

                    // في حال لم يتم تحديد مكتب بعد، نعين مكتبنا الداخلي كافتراضي
                    if (!this.selectedOfficeId && this.destinations.length > 0) {
                        const internal = this.destinations.find(d => d.type === 'internal');
                        if (internal) {
                            this.selectedOfficeId = String(internal.office_id);
                        }
                    }

                    this.updateValidity();
                },

                // تسمية الوجهة المحددة حالياً
                get selectedDestinationName() {
                    const found = this.destinations.find(d => d.key === this.selectedDestinationKey);
                    return found ? found.name : '';
                },

                // الوجهات المفلترة بالبحث
                get filteredDestinations() {
                    if (!this.searchQuery || !this.searchQuery.trim() || (!this.isSearching && this.searchQuery === this.selectedDestinationName)) {
                        return this.destinations;
                    }
                    const q = this.searchQuery.trim().toLowerCase();
                    return this.destinations.filter(d => 
                        (d.name && d.name.toLowerCase().includes(q)) ||
                        (d.group && d.group.toLowerCase().includes(q))
                    );
                },

                // تجميع الوجهات المفلترة حسب مجموعاتها
                get groupedFilteredDestinations() {
                    const groups = {};
                    this.filteredDestinations.forEach(dest => {
                        const grp = dest.group || 'أخرى';
                        if (!groups[grp]) {
                            groups[grp] = [];
                        }
                        groups[grp].push(dest);
                    });
                    return groups;
                },

                // هل توجد نتائج للبحث؟
                get hasResults() {
                    return this.filteredDestinations.length > 0;
                },

                updateValidity() {
                    this.$nextTick(() => {
                        if (this.$refs.destinationInput) {
                            if (!this.selectedBranchId) {
                                this.$refs.destinationInput.setCustomValidity('يرجى اختيار الفرع أو الوجهة المستلمة من القائمة');
                            } else {
                                this.$refs.destinationInput.setCustomValidity('');
                            }
                        }
                    });
                },

                // عند فتح أو إغلاق القائمة عبر السهم
                toggleDropdown() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.$nextTick(() => {
                            if (this.$refs.destinationInput) {
                                this.$refs.destinationInput.focus();
                            }
                        });
                    }
                },

                // عند التركيز على الحقل
                onInputFocus(e) {
                    this.isOpen = true;
                    if (e && e.target) {
                        e.target.select();
                    }
                },

                // عند كتابة المستخدم في الحقل
                onInputChange() {
                    this.isSearching = true;
                    this.isOpen = true;
                    if (this.selectedDestinationName && this.searchQuery !== this.selectedDestinationName) {
                        this.selectedDestinationKey = '';
                        this.selectedBranchId = '';
                    }
                    this.updateValidity();
                },

                // اختيار وجهة من القائمة
                selectDestination(dest) {
                    if (dest) {
                        this.selectedDestinationKey = dest.key;
                        this.selectedOfficeId = String(dest.office_id);
                        this.selectedBranchId = String(dest.branch_id);
                        this.searchQuery = dest.name;
                    } else {
                        this.clearSelection();
                    }
                    this.isSearching = false;
                    this.isOpen = false;
                    this.updateValidity();
                },

                // مسح التحديد
                clearSelection() {
                    this.selectedDestinationKey = '';
                    this.selectedBranchId = '';
                    this.searchQuery = '';
                    this.isSearching = false;
                    this.isOpen = true;
                    this.updateValidity();
                },

                // عند النقر خارج القائمة
                onBlurOutside() {
                    this.isOpen = false;
                    this.isSearching = false;
                    if (this.selectedBranchId && this.selectedDestinationName) {
                        this.searchQuery = this.selectedDestinationName;
                    } else {
                        this.searchQuery = '';
                        this.selectedDestinationKey = '';
                        this.selectedBranchId = '';
                    }
                    this.updateValidity();
                }
            }));

            // ==========================================
            // 2. منطق تحديد العميل (المرسل والمستلم)
            // ==========================================
            Alpine.data('customerSelect', (customersList, countriesList, initialData) => ({
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

                normalize(text) {
                    if (!text) return '';
                    return String(text)
                        .replace(/[\u064B-\u065F]/g, '')
                        .replace(/[أإآ]/g, 'ا')
                        .replace(/ة/g, 'ه')
                        .replace(/ى/g, 'ي')
                        .toLowerCase()
                        .trim();
                },

                init() {
                    if (initialData && (initialData.phone || initialData.name || initialData.id)) {
                        let phone = initialData.phone || '';
                        if (phone && /^\d/.test(phone)) phone = '+' + phone;

                        this.selectedCountry = this.countries.find(c => phone.startsWith(c
                            .dial_code)) ||
                            this.countries.find(c => c.code === 'YE') ||
                            this.countries[0];

                        if (this.selectedCountry && phone.startsWith(this.selectedCountry.dial_code)) {
                            this.localPhoneNumber = phone.substring(this.selectedCountry.dial_code
                                .length);
                        } else {
                            this.localPhoneNumber = phone;
                        }

                        this.selectedCustomerId = (initialData.id && initialData.id !== '') ? Number(
                            initialData.id) : null;
                        this.nameInput = initialData.name || '';
                    } else {
                        this.selectedCountry = this.countries.find(c => c.code === 'YE') || this
                            .countries[0];
                    }
                },

                get filteredCountries() {
                    if (this.searchCountryQuery === '') return this.countries;
                    const query = this.searchCountryQuery.toLowerCase();
                    return this.countries.filter(c =>
                        c.name.toLowerCase().includes(query) || c.dial_code.includes(query)
                    );
                },

                get fullPhoneNumber() {
                    if (!this.localPhoneNumber) return '';
                    let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace(
                        '+', '') : '';
                    return dialCode + this.localPhoneNumber;
                },

              searchCustomer() {
                    this.selectedCustomerId = null;
                    let rawQuery = (this.nameInput || '').trim();
                    if (rawQuery === '') {
                        this.filteredCustomers = [];
                        this.showCustomerDropdown = false;
                        return;
                    }

                    // تنظيف نص البحث وتقسيمه إلى مصفوفة من الكلمات
                    let normQuery = this.normalize(rawQuery);
                    let searchWords = normQuery.split(/\s+/); // يفصل الكلمات بناءً على المسافات

                    this.filteredCustomers = this.customers.filter(c => {
                        if (!c) return false;
                        
                        let nameMatch = false;
                        if (c.name) {
                            let normalizedName = this.normalize(c.name);
                            // نتحقق مما إذا كانت *كل* كلمة كتبها المستخدم موجودة داخل اسم العميل
                            nameMatch = searchWords.every(word => normalizedName.includes(word));
                        }

                        let phoneMatch = c.phone && String(c.phone).includes(rawQuery);
                        
                        return nameMatch || phoneMatch;
                    });
                    this.showCustomerDropdown = true;
                },

                selectCustomer(customer) {
                    this.selectedCustomerId = customer.id;
                    this.nameInput = customer.name || '';

                    let phone = customer.phone ? String(customer.phone) : '';
                    if (phone) {
                        let phoneWithPlus = phone;
                        if (/^\d/.test(phoneWithPlus)) phoneWithPlus = '+' + phoneWithPlus;

                        let matchedCountry = this.countries
                            .slice()
                            .sort((a, b) => b.dial_code.length - a.dial_code.length)
                            .find(c => phoneWithPlus.startsWith(c.dial_code));

                        if (matchedCountry) {
                            this.selectedCountry = matchedCountry;
                            this.localPhoneNumber = phoneWithPlus.substring(matchedCountry.dial_code.length);
                        } else {
                            let cleanPhone = phone.replace(/^\+/, '');
                            let matchedCountryClean = this.countries
                                .slice()
                                .sort((a, b) => b.dial_code.length - a.dial_code.length)
                                .find(c => cleanPhone.startsWith(c.dial_code.replace('+', '')));

                            if (matchedCountryClean) {
                                this.selectedCountry = matchedCountryClean;
                                this.localPhoneNumber = cleanPhone.substring(matchedCountryClean.dial_code.replace('+', '').length);
                            } else {
                                this.localPhoneNumber = phone;
                            }
                        }
                    } else {
                        this.localPhoneNumber = '';
                    }

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
