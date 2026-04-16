@extends('mobile.layouts.app')

@section('title', 'تعديل الطرد - ' . $shipment->bond_number)

@section('content')
    <div x-data="{ paymentMethod: '{{ old('payment_method', $shipment->payment_method) }}' }"
        class="flex flex-col gap-6 px-4 pb-19 relative min-h-screen bg-slate-50/50 pt-6">

        <div class="flex items-center gap-4 mb-2">
            <a href="javascript:history.back()"
                class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 active:scale-90 transition-transform">
                <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
            </a>
            <div>
                <h1 class="text-2xl font-black font-headline text-slate-800">تعديل الطرد</h1>
                <p class="text-xs text-primary font-bold mt-1">{{ $shipment->bond_number }}</p>
            </div>
        </div>

        <form action="{{ route('shipment.outgoing.update', $shipment->id) }}" method="POST" class="space-y-6" x-data="{ isSubmitting: false }"
            @submit="if(!isSubmitting) { isSubmitting = true; $el.submit(); } else { $event.preventDefault(); }">
            @csrf
            @method('PUT')
            
            {{-- بيانات الفروع --}}
            <div x-data='destinationLogic(@json($offices))'
                class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-900/5 group/card">

                {{-- الرأس بشكل مبسط --}}
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="flex items-center justify-center shrink-0 w-10 h-10 bg-slate-50 text-slate-400 rounded-xl group-hover/card:bg-blue-500 group-hover/card:text-white transition-all">
                        <span class="material-symbols-outlined text-[22px]"
                            style="font-variation-settings: 'FILL' 1;">edit_location</span>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 font-headline text-base">وجهة الطرد</h3>
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
                            <select name="office_id" x-model="selectedOfficeId" @change="selectedBranchId = ''; updateBranches()" required
                                class="w-full h-12 pl-8 pr-3 text-xs font-bold text-slate-800 bg-slate-50 border border-slate-200/70 rounded-xl appearance-none outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
                                <option value="" disabled>المكتب...</option>
                                <template x-for="office in offices" :key="office.id">
                                    <option :value="office.id" x-text="office.name"></option>
                                </template>
                            </select>
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
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
                                class="w-full h-12 pl-8 pr-3 text-xs font-bold text-slate-800 bg-white border border-slate-200 rounded-xl appearance-none outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
                                <option value="" disabled>الفرع...</option>
                                <template x-for="branch in availableBranches" :key="branch.id">
                                    <option :value="branch.id" x-text="branch.name"></option>
                                </template>
                            </select>
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-blue-500 pointer-events-none">
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
                <div class="flex items-center gap-2 mb-5">
                    <span
                        class="material-symbols-outlined text-emerald-500 bg-emerald-50 p-1.5 rounded-lg text-[20px]">group</span>
                    <h3 class="font-bold text-slate-700 font-headline">بيانات العملاء</h3>
                </div>

                <div class="space-y-6">

                    {{-- ================= المرسل ================= --}}
                    <div x-data="customerSelect({{ $customers }}, @js(array_values(config('countries', []))), '{{ old('sender_customer_id', $shipment->sender_customer_id) }}', '{{ old('sender_name', $shipment->senderCustomer->name ?? '') }}', '{{ old('sender_phone', $shipment->senderCustomer->phone ?? '') }}')"
                        class="p-4 bg-slate-50 rounded-2xl border border-slate-100 z-50">
                        <span class=" -top-2.5 right-4 bg-slate-50 px-2 text-[10px] font-black text-slate-500">المرسل <span
                                class="text-red-500">*</span></span>

                        <div class="grid grid-cols-1 gap-3 mt-2 relative">
                            <input type="hidden" name="sender_customer_id" x-model="selectedCustomerId">
                            <input type="hidden" name="sender_phone" :value="fullPhoneNumber">

                            <div class="relative group flex items-center rounded-xl ring-1 transition-all bg-white ring-slate-200 focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary overflow-visible"
                                :class="selectedCustomerId ? 'bg-primary/5 ring-primary/30' : ''" style="direction: ltr;">

                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex items-center gap-2 px-3 h-12 bg-slate-50 border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors rounded-l-xl">
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
                                                class="w-full h-8 px-3 text-xs bg-slate-50 rounded-lg outline-none focus:ring-1 ring-primary/30"
                                                dir="ltr">
                                        </div>
                                        <div class="max-h-48 overflow-y-auto" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                    @click="selectedCountry = country; openCountryDropdown = false; searchCustomer()"
                                                    class="w-full flex items-center gap-3 px-3 py-2 text-left hover:bg-slate-50 transition-colors">
                                                    <svg class="w-5 h-auto rounded-[2px]" viewBox="0 0 36 24" fill="none"
                                                        x-html="country.svg"></svg>
                                                    <span class="text-xs font-bold text-slate-700 flex-1"
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
                                    class="absolute right-3 text-slate-400 hover:text-red-500 z-10 bg-white p-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            <div x-show="showCustomerDropdown && localPhoneNumber.length > 0 && !selectedCustomerId"
                                x-transition x-cloak
                                class="absolute top-[3.25rem] right-0 w-full bg-white border border-slate-100 rounded-xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden max-h-48 overflow-y-auto z-50">
                                <template x-for="customer in filteredCustomers" :key="customer.id">
                                    <button type="button" @click="selectCustomer(customer)"
                                        class="w-full text-right px-4 py-3 hover:bg-slate-50 border-b border-slate-50 flex items-center justify-between transition-colors">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-bold text-sm text-slate-800" x-text="customer.name"></span>
                                            <span class="text-xs text-slate-500 dir-ltr text-right"
                                                x-text="customer.phone"></span>
                                        </div>
                                        <span
                                            class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                                    </button>
                                </template>
                                <div x-show="filteredCustomers.length === 0" class="px-4 py-3 bg-slate-50/50 text-center">
                                    <span class="text-xs font-bold text-slate-500">مرسل جديد، سيتم حفظه.</span>
                                </div>
                            </div>

                            <input type="text" name="sender_name" x-model="nameInput"
                                :readonly="selectedCustomerId !== null" placeholder="اسم المرسل (اختياري)..."
                                class="w-full h-12 px-4 text-sm rounded-xl border outline-none transition-colors"
                                :class="selectedCustomerId ? 'bg-slate-50 border-transparent text-slate-500 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-700'">
                        </div>
                    </div>

                    {{-- ================= المستلم ================= --}}
                    <div x-data="customerSelect({{ $customers }}, @js(array_values(config('countries', []))), '{{ old('receiver_customer_id', $shipment->receiver_customer_id) }}', '{{ old('receiver_name', $shipment->receiverCustomer->name ?? '') }}', '{{ old('receiver_phone', $shipment->receiverCustomer->phone ?? '') }}')"
                        class="p-4 bg-slate-50 rounded-2xl border border-slate-100 z-40">
                        <span class=" -top-2.5 right-4 bg-slate-50 px-2 text-[10px] font-black text-slate-500">المستلم
                            <span class="text-red-500">*</span></span>

                        <div class="grid grid-cols-1 gap-3 mt-2 relative">
                            <input type="hidden" name="receiver_customer_id" x-model="selectedCustomerId">
                            <input type="hidden" name="receiver_phone" :value="fullPhoneNumber">

                            <div class="relative group flex items-center rounded-xl ring-1 transition-all bg-white ring-slate-200 focus-within:ring-2 focus-within:ring-emerald-500/50 focus-within:border-emerald-500 overflow-visible"
                                :class="selectedCustomerId ? 'bg-emerald-50/30 ring-emerald-400' : ''"
                                style="direction: ltr;">

                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex items-center gap-2 px-3 h-12 bg-slate-50 border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors rounded-l-xl">
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
                                                class="w-full h-8 px-3 text-xs bg-slate-50 rounded-lg outline-none focus:ring-1 ring-primary/30"
                                                dir="ltr">
                                        </div>
                                        <div class="max-h-48 overflow-y-auto" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                    @click="selectedCountry = country; openCountryDropdown = false; searchCustomer()"
                                                    class="w-full flex items-center gap-3 px-3 py-2 text-left hover:bg-slate-50 transition-colors">
                                                    <svg class="w-5 h-auto rounded-[2px]" viewBox="0 0 36 24" fill="none"
                                                        x-html="country.svg"></svg>
                                                    <span class="text-xs font-bold text-slate-700 flex-1"
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
                                    class="absolute right-3 text-slate-400 hover:text-red-500 z-10 bg-white p-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            <div x-show="showCustomerDropdown && localPhoneNumber.length > 0 && !selectedCustomerId"
                                x-transition x-cloak
                                class="absolute top-[3.25rem] right-0 w-full bg-white border border-slate-100 rounded-xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden max-h-48 overflow-y-auto z-50">
                                <template x-for="customer in filteredCustomers" :key="customer.id">
                                    <button type="button" @click="selectCustomer(customer)"
                                        class="w-full text-right px-4 py-3 hover:bg-slate-50 border-b border-slate-50 flex items-center justify-between transition-colors">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-bold text-sm text-slate-800" x-text="customer.name"></span>
                                            <span class="text-xs text-slate-500 dir-ltr text-right"
                                                x-text="customer.phone"></span>
                                        </div>
                                        <span
                                            class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                                    </button>
                                </template>
                                <div x-show="filteredCustomers.length === 0" class="px-4 py-3 bg-slate-50/50 text-center">
                                    <span class="text-xs font-bold text-slate-500">مستلم جديد، سيتم حفظه.</span>
                                </div>
                            </div>

                            <input type="text" name="receiver_name" required x-model="nameInput"
                                :readonly="selectedCustomerId !== null" placeholder="اسم المستلم..."
                                class="w-full h-12 px-4 text-sm rounded-xl border outline-none transition-colors"
                                :class="selectedCustomerId ? 'bg-slate-50 border-transparent text-slate-500 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 text-slate-700'">
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white p-5 rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.03)]">
                <div class="flex items-center gap-2 mb-4">
                    <span
                        class="material-symbols-outlined text-blue-500 bg-blue-50 p-1.5 rounded-lg text-[20px]">inventory_2</span>
                    <h3 class="font-bold text-slate-700 font-headline">محتويات الطرد</h3>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-[11px] font-bold text-slate-400">النوع</label>
                        <select name="package_type"
                            class="w-full h-12 px-3 text-sm bg-slate-50 rounded-xl border-none ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-slate-700 appearance-none">
                            <option value="carton" {{ old('package_type', $shipment->package_type) == 'carton' ? 'selected' : '' }}>كرتون</option>
                            <option value="bag" {{ old('package_type', $shipment->package_type) == 'bag' ? 'selected' : '' }}>كيس</option>
                            <option value="envelope" {{ old('package_type', $shipment->package_type) == 'envelope' ? 'selected' : '' }}>مغلف</option>
                            <option value="other" {{ old('package_type', $shipment->package_type) == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[11px] font-bold text-slate-400">الوزن (كجم)</label>
                        <input type="number" step="0.1" name="weight" placeholder="مثال: 2.5"
                            value="{{ old('weight', $shipment->weight) }}"
                            class="w-full h-12 px-4 text-sm bg-slate-50 rounded-xl border-none ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-slate-700 text-left"
                            dir="ltr">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4 p-3 bg-amber-50/50 rounded-2xl border border-amber-100/50">
                    <div class="flex flex-col gap-2">
                        <label class="text-[11px] font-bold text-amber-600">دباب العسل</label>
                        <input type="number" name="no_gallons_honey" placeholder="العدد"
                            value="{{ old('no_gallons_honey', $shipment->no_gallons_honey) }}"
                            class="w-full h-12 px-4 text-sm bg-white rounded-xl border border-amber-100 focus:border-amber-400 outline-none text-slate-700">
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-[11px] font-bold text-amber-600">قوارير العسل</label>
                        <input type="number" name="no_honey_jars" placeholder="العدد"
                            value="{{ old('no_honey_jars', $shipment->no_honey_jars) }}"
                            class="w-full h-12 px-4 text-sm bg-white rounded-xl border border-amber-100 focus:border-amber-400 outline-none text-slate-700">
                    </div>

                </div>
                <div class="flex flex-col gap-2 pt-2">
                    <label class="text-[11px] font-bold text-slate-400">ملاحظات إضافية</label>
                    <textarea name="notes" rows="2" placeholder="اكتب أي ملاحظات هنا..."
                        class="w-full p-4 text-sm bg-slate-50 rounded-2xl border-none ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-slate-700 resize-none">{{ old('notes', $shipment->notes) }}</textarea>
                </div>
            </div>

            <div class="bg-white p-5 rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.03)]">
                <div class="flex items-center gap-2 mb-4">
                    <span
                        class="material-symbols-outlined text-rose-500 bg-rose-50 p-1.5 rounded-lg text-[20px]">payments</span>
                    <h3 class="font-bold text-slate-700 font-headline">المالية والدفع</h3>
                </div>

                <div class="space-y-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-[11px] font-bold text-slate-400">طريقة الدفع <span
                                class="text-red-500">*</span></label>
                        <select name="payment_method" x-model="paymentMethod"
                            class="w-full h-14 px-4 text-sm bg-slate-50 rounded-2xl border-none ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-slate-700 appearance-none font-bold">
                            <option value="prepaid">مدفوع مقدماً</option>
                            <option value="cod">الدفع عند الاستلام (على حساب المستلم)</option>
                            <option value="partial_payment">دفع جزئي</option>
                            <option value="customer_credit">آجل (على حساب المرسل)</option>
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[11px] font-bold text-slate-400">المبلغ الإجمالي (ريال) <span
                                class="text-red-500">*</span></label>
                        <input type="number" required name="total_amount" placeholder="0.00"
                            value="{{ old('total_amount', $shipment->total_amount) }}"
                            class="w-full h-14 px-4 text-lg font-black bg-slate-50 rounded-2xl border-none ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-primary text-left"
                            dir="ltr">
                    </div>

                    <div x-show="paymentMethod === 'partial_payment'" x-collapse class="flex flex-col gap-2">
                        <label class="text-[11px] font-bold text-rose-500">المبلغ المدفوع حالياً (ريال)</label>
                        <input type="number" name="partial_amount" placeholder="0.00"
                            value="{{ old('partial_amount', $shipment->partial_amount) }}"
                            class="w-full h-14 px-4 text-lg font-black bg-rose-50/50 rounded-2xl border-none ring-1 ring-rose-200 focus:ring-2 focus:ring-rose-400 outline-none text-rose-600 text-left"
                            dir="ltr">
                    </div>


                </div>
            </div>
            {{-- زر الارسال --}}
            <div class="pt-2 pb-4">
                <button type="submit" :disabled="isSubmitting"
                    class="flex items-center justify-center gap-2 w-full h-14 rounded-2xl font-bold text-sm shadow-[0_8px_20px_rgba(59,130,246,0.25)] transition-all duration-200"
                    :class="isSubmitting ? 'bg-slate-400 text-slate-100 cursor-not-allowed' : 'bg-blue-600 text-white hover:bg-blue-700 active:scale-95'">

                    {{-- الحالة العادية --}}
                    <span x-show="!isSubmitting" class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[22px]">save</span>
                        حفظ التعديلات
                    </span>

                    {{-- حالة التحميل --}}
                    <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
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