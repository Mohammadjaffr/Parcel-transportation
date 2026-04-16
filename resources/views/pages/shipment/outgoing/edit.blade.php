@extends('layouts.app')
@section('title', 'تعديل طرد #' . $shipment->bond_number)
@section('Breadcrumb', 'تعديل طرد')

@section('content')
    @include('components.modals.error-modal')
    @include('components.modals.green-modal')

    <div class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800" 
         x-data="{
            payment_method: @js(old('payment_method', $shipment->payment_method)),
            prepaid_method: @js(old('prepaid_payment_method', 'cash')),
        
            isSenderReceiverModalOpen: @js($errors->has('sender_name') || $errors->has('sender_phone') || $errors->has('sender_customer_id') || $errors->has('sender_branch_code') || $errors->has('receiver_name') || $errors->has('receiver_phone') || $errors->has('receiver_customer_id') || $errors->has('receiver_branch_code') || $errors->has('no_honey_jars') || $errors->has('no_gallons_honey')),
        
            isDetailsModalOpen: @js($errors->has('code') || $errors->has('package_type') || $errors->has('weight') || $errors->has('total_amount') || $errors->has('status') || $errors->has('notes')),
        
            isPaymentModalOpen: @js(
                $errors->has('payment_method') ||
                $errors->has('partial_amount') ||
                $errors->has('prepaid_payment_method') ||
                $errors->has('prepaid_reference') ||
                $errors->has('prepaid_attachment') ||
                $errors->has('customer_debt_status')
            ),
        
            activeTab: @js($errors->has('payment_method') || $errors->has('partial_amount') || $errors->has('prepaid_payment_method') || $errors->has('prepaid_reference') || $errors->has('prepaid_attachment') || $errors->has('customer_debt_status') ? 'payment' : ($errors->has('code') || $errors->has('package_type') || $errors->has('weight') || $errors->has('total_amount') || $errors->has('status') || $errors->has('notes') ? 'details' : 'sender_receiver'))
        }">

        <div class="max-w-[1200px] mx-auto space-y-6 font-outfit" dir="rtl">

            {{-- ===== التابات ===== --}}
            <div class="flex overflow-x-auto items-center p-1.5 mb-6 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner dark:bg-gray-900/50 dark:border-gray-800 w-fit">
                <button type="button" @click="activeTab = 'sender_receiver'"
                    :class="activeTab === 'sender_receiver'
                        ? 'bg-white text-primary shadow-sm dark:bg-gray-800 dark:text-primary ring-1 ring-gray-200 dark:ring-gray-700' 
                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="flex gap-2 items-center px-6 py-2.5 text-sm font-bold whitespace-nowrap rounded-xl transition-all duration-300">
                    <span class="material-symbols-outlined text-[18px]">group</span>
                    بيانات المرسل والمستلم
                </button>

                <button type="button" @click="activeTab = 'details'"
                    :class="activeTab === 'details'
                        ? 'bg-white text-primary shadow-sm dark:bg-gray-800 dark:text-primary ring-1 ring-gray-200 dark:ring-gray-700' 
                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="flex gap-2 items-center px-6 py-2.5 text-sm font-bold whitespace-nowrap rounded-xl transition-all duration-300">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    تفاصيل الطرد
                </button>

                <button type="button" @click="activeTab = 'payment'"
                    :class="activeTab === 'payment'
                        ? 'bg-white text-primary shadow-sm dark:bg-gray-800 dark:text-primary ring-1 ring-gray-200 dark:ring-gray-700' 
                        : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                    class="flex gap-2 items-center px-6 py-2.5 text-sm font-bold whitespace-nowrap rounded-xl transition-all duration-300">
                    <span class="material-symbols-outlined text-[18px]">payments</span>
                    طريقة الدفع
                </button>
            </div>

            {{-- ===== تبويب: المرسل/المستلم ===== --}}
            <div x-show="activeTab === 'sender_receiver'" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-6 bg-gray-50 border border-gray-100 rounded-[2rem] dark:bg-gray-900/30 dark:border-gray-800">

                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex gap-2 items-center mb-4 text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            بيانات الأطراف الحالية
                        </div>

                        <div class="grid grid-cols-1 gap-y-3 gap-x-12 text-sm font-medium text-gray-700 sm:grid-cols-2 dark:text-gray-300">
                            <div class="flex gap-2 items-center">
                                <span class="text-gray-400 material-symbols-outlined text-[18px]">person</span>
                                <span class="font-bold text-gray-500">المرسل:</span>
                                <span class="font-black text-gray-900 dark:text-white">{{ $shipment->senderCustomer->name ?? $shipment->sender_name }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="text-gray-400 material-symbols-outlined text-[18px]">person</span>
                                <span class="font-bold text-gray-500">المستلم:</span>
                                <span class="font-black text-gray-900 dark:text-white">{{ $shipment->receiverCustomer->name ?? $shipment->receiver_name }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="text-gray-400 material-symbols-outlined text-[18px]">storefront</span>
                                <span class="font-bold text-gray-500">من الفرع:</span>
                                <span class="font-black text-gray-900 dark:text-white">{{ auth()->user()->branch->name ?? '-' }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="text-gray-400 material-symbols-outlined text-[18px]">storefront</span>
                                <span class="font-bold text-gray-500">إلى الفرع:</span>
                                <span class="font-black text-gray-900 dark:text-white">{{ $shipment->receiverBranch->name ?? '-' }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="text-gray-400 material-symbols-outlined text-[18px]">hive</span>
                                <span class="font-bold text-gray-500">عدد القروف:</span>
                                <span class="font-black text-gray-900 dark:text-white">{{ $shipment->no_honey_jars }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="text-gray-400 material-symbols-outlined text-[18px]">water_drop</span>
                                <span class="font-bold text-gray-500">عدد الجوالين:</span>
                                <span class="font-black text-gray-900 dark:text-white">{{ $shipment->no_gallons_honey }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="isSenderReceiverModalOpen = true"
                        class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-bold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 shrink-0">
                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                        تعديل البيانات
                    </button>
                </div>
            </div>

            {{-- ===== تبويب: تفاصيل الطرد ===== --}}
            <div x-show="activeTab === 'details'" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-6 bg-gray-50 border border-gray-100 rounded-[2rem] dark:bg-gray-900/30 dark:border-gray-800" style="display: none;">

                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex gap-2 items-center mb-4 text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            تفاصيل الطرد الحالية
                        </div>

                        <div class="grid grid-cols-1 gap-y-3 gap-x-12 text-sm font-medium text-gray-700 sm:grid-cols-2 dark:text-gray-300">
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-gray-500">الرمز:</span>
                                <span class="font-mono font-black text-gray-900 dark:text-white">{{ $shipment->code }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-gray-500">نوع الطرد:</span>
                                <span class="font-black text-gray-900 dark:text-white">{{ $shipment->package_type }}</span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-gray-500">إجمالي المبلغ:</span>
                                <span class="font-mono font-black text-primary">{{ number_format($shipment->total_amount, 0) }} <span class="font-sans text-[10px] text-gray-500">ر.ي</span></span>
                            </div>
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-gray-500">الحالة:</span>
                                @php
                                    $statusText = [
                                        'pending' => 'قيد الانتظار', 'in_transit' => 'في الطريق', 'delivered' => 'تم التسليم',
                                        'cancelled' => 'ملغي', 'returned' => 'مرتجع',
                                    ];
                                @endphp
                                <span class="font-black text-gray-900 dark:text-white">{{ $statusText[$shipment->status] ?? $shipment->status }}</span>
                            </div>
                            <div class="flex gap-2 sm:col-span-2">
                                <span class="font-bold text-gray-500 shrink-0">الملاحظات:</span>
                                <span class="text-gray-900 dark:text-gray-300">{{ $shipment->notes ?: 'لا توجد ملاحظات مسجلة' }}</span>
                            </div>
                        </div>
                    </div>

                    <button type="button" @click="isDetailsModalOpen = true"
                        class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-bold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 shrink-0">
                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                        تعديل التفاصيل
                    </button>
                </div>
            </div>

            {{-- ===== تبويب: طريقة الدفع ===== --}}
            <div x-show="activeTab === 'payment'" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4" 
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="p-6 bg-gray-50 border border-gray-100 rounded-[2rem] dark:bg-gray-900/30 dark:border-gray-800" style="display: none;">

                <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex gap-2 items-center mb-4 text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            معلومات الدفع الحالية
                        </div>

                        <div class="grid grid-cols-1 gap-y-3 gap-x-12 text-sm font-medium text-gray-700 sm:grid-cols-2 dark:text-gray-300">
                            <div class="flex gap-2 items-center">
                                <span class="font-bold text-gray-500">طريقة الدفع:</span>
                                <span class="font-black text-gray-900 dark:text-white">
                                    @switch($shipment->payment_method)
                                        @case('prepaid') دفع مقدم @break
                                        @case('cod') عند التسليم (COD) @break
                                        @case('partial_payment') دفع جزئي @break
                                        @case('customer_credit') آجل على حساب العميل @break
                                        @default {{ $shipment->payment_method }}
                                    @endswitch
                                </span>
                            </div>
                            
                            @if ($shipment->customer_debt_status)
                                <div class="flex gap-2 items-center">
                                    <span class="font-bold text-gray-500">حالة المديونية:</span>
                                    <span class="font-black text-gray-900 dark:text-white">
                                        @switch($shipment->customer_debt_status)
                                            @case('fully_paid') مدفوع بالكامل @break
                                            @case('partially_paid') مدفوع جزئياً @break
                                            @case('pending') غير مدفوع @break
                                            @case('overdue') متأخر @break
                                            @default {{ $shipment->customer_debt_status }}
                                        @endswitch
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <button type="button" @click="isPaymentModalOpen = true"
                        class="flex gap-2 justify-center items-center px-5 py-2.5 text-sm font-bold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 shrink-0">
                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                        تعديل الدفع
                    </button>
                </div>
            </div>

        </div>

        {{-- ====================== المودالات (Modals) ====================== --}}
        
        {{-- 1. مودال المرسل والمستلم --}}
        <template x-teleport="body">
            <div x-show="isSenderReceiverModalOpen" x-cloak class="fixed inset-0 z-[999999] flex items-center justify-center p-4 sm:p-6" @keydown.escape.window="isSenderReceiverModalOpen = false">
                <div x-show="isSenderReceiverModalOpen" x-transition.opacity class="fixed inset-0 backdrop-blur-sm bg-gray-900/60" @click="isSenderReceiverModalOpen = false"></div>
                <div x-show="isSenderReceiverModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6 bg-white shadow-2xl rounded-[2rem] dark:bg-boxdark sm:p-8 custom-scrollbar" dir="rtl">
                    
                    <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100 dark:border-gray-800">
                        <button type="button" @click="isSenderReceiverModalOpen = false" class="text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300"><span class="material-symbols-outlined text-[24px]">close</span></button>
                        <div class="flex gap-3 items-center">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">تعديل بيانات المرسل والمستلم</h2>
                            <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 text-warning-500 dark:bg-warning-500/10"><span class="material-symbols-outlined text-[22px]">manage_accounts</span></div>
                        </div>
                    </div>

                    {{-- الفورم الكلاسيكي --}}
                    <form action="{{ route('shipment.update', $shipment->id) }}" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="sender_receiver">

                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            {{-- بيانات المرسل --}}
                            <div class="p-5 space-y-4 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800/30 dark:border-gray-700" 
                                 x-data="customerPicker('{{ route('customers.search') }}', @js(['id' => old('sender_customer_id', $shipment->sender_customer_id), 'name' => old('sender_name', $shipment->senderCustomer->name ?? $shipment->sender_name), 'phone' => old('sender_phone', $shipment->senderCustomer->phone ?? $shipment->sender_phone)]))">
                                
                                <div class="flex gap-2 items-center mb-2 text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>بيانات المرسل
                                </div>

                                <div class="relative">
                                    <input type="text" x-model="query" @input.debounce.350ms="search()" @focus="open = true" @keydown.escape="open = false" placeholder="بحث عن عميل مسجل..." class="px-4 w-full h-11 text-sm bg-white rounded-xl border border-gray-200 outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    <div x-show="open" x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-gray-800 dark:border-gray-700">
                                        <template x-if="loading"><div class="p-3 text-sm text-gray-500">جاري البحث...</div></template>
                                        <template x-if="!loading && results.length === 0 && query.trim().length >= 2"><div class="p-3 text-sm text-gray-500">لا توجد نتائج. سيتم التسجيل كعميل جديد.</div></template>
                                        <template x-for="c in results" :key="c.id">
                                            <button type="button" @click="select(c)" class="px-4 py-3 w-full text-right transition-colors hover:bg-primary/5 dark:hover:bg-gray-700">
                                                <div class="text-sm font-bold text-gray-800 dark:text-white" x-text="c.name"></div>
                                                <div class="text-xs text-right text-gray-500 dir-ltr" x-text="c.phone"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <input type="hidden" name="sender_customer_id" x-model="selectedId">
                                
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">اسم المرسل</label>
                                    <input type="text" name="sender_name" x-model="selectedName" @input="selectedId=''" class="px-4 w-full h-11 text-sm bg-white rounded-xl border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>

                                <div class="relative">
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">رقم الهاتف</label>
                                    <input type="hidden" name="sender_phone" :value="selectedPhone">
                                    <div class="flex overflow-hidden items-center h-11 bg-white rounded-xl border border-gray-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-600">
                                        <button type="button" @click.stop="openCountry = !openCountry" class="flex gap-2 items-center px-3 h-full bg-gray-50 border-l border-gray-200 shrink-0 dark:bg-gray-800 dark:border-gray-700">
                                            <img :src="`https://flagcdn.com/w20/${countryFlag}.png`" class="w-5 h-auto rounded-[2px]">
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="countryCode"></span>
                                        </button>
                                        <input type="tel" x-model="localNumber" @input="updatePhone()" placeholder="7XXXXXXXX" class="px-3 w-full h-full text-sm tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 dark:text-white" dir="ltr">
                                    </div>
                                    <div x-show="openCountry" @click.outside="openCountry = false" x-transition class="overflow-y-auto absolute z-50 mt-2 w-full max-h-48 bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-gray-800 dark:border-gray-700 custom-scrollbar">
                                        <template x-for="country in countries" :key="country.code">
                                            <button type="button" @click="setCountry(country.code)" class="flex justify-between items-center px-4 py-2 w-full text-sm text-left transition-colors hover:bg-primary/5 dark:hover:bg-gray-700">
                                                <div class="flex gap-2 items-center">
                                                    <img :src="`https://flagcdn.com/w20/${country.flag}.png`" class="w-5 h-auto rounded-[2px]">
                                                    <span class="text-gray-700 dark:text-gray-300" x-text="country.code"></span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">عدد القروف</label>
                                        <input type="number" name="no_honey_jars" value="{{ old('no_honey_jars', $shipment->no_honey_jars) }}" class="px-4 w-full h-11 text-sm bg-white rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">عدد الجوالين</label>
                                        <input type="number" name="no_gallons_honey" value="{{ old('no_gallons_honey', $shipment->no_gallons_honey) }}" class="px-4 w-full h-11 text-sm bg-white rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>

                            {{-- بيانات المستلم --}}
                            <div class="p-5 space-y-4 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800/30 dark:border-gray-700" 
                                 x-data="customerPicker('{{ route('customers.search') }}', @js(['id' => old('receiver_customer_id', $shipment->receiver_customer_id), 'name' => old('receiver_name', $shipment->receiverCustomer->name ?? $shipment->receiver_name), 'phone' => old('receiver_phone', $shipment->receiverCustomer->phone ?? $shipment->receiver_phone)]))">
                                
                                <div class="flex gap-2 items-center mb-2 text-sm font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>بيانات المستلم
                                </div>

                                <div class="relative">
                                    <input type="text" x-model="query" @input.debounce.350ms="search()" @focus="open = true" @keydown.escape="open = false" placeholder="بحث عن عميل مسجل..." class="px-4 w-full h-11 text-sm bg-white rounded-xl border border-gray-200 outline-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    <div x-show="open" x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-gray-800 dark:border-gray-700">
                                        <template x-if="loading"><div class="p-3 text-sm text-gray-500">جاري البحث...</div></template>
                                        <template x-if="!loading && results.length === 0 && query.trim().length >= 2"><div class="p-3 text-sm text-gray-500">لا توجد نتائج. سيتم التسجيل كعميل جديد.</div></template>
                                        <template x-for="c in results" :key="c.id">
                                            <button type="button" @click="select(c)" class="px-4 py-3 w-full text-right transition-colors hover:bg-primary/5 dark:hover:bg-gray-700">
                                                <div class="text-sm font-bold text-gray-800 dark:text-white" x-text="c.name"></div>
                                                <div class="text-xs text-right text-gray-500 dir-ltr" x-text="c.phone"></div>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <input type="hidden" name="receiver_customer_id" x-model="selectedId">
                                
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">الجهة إلى (فرع الاستلام)</label>
                                    <select name="receiver_branch_code" required class="px-4 w-full h-11 text-sm bg-white rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        <option value="" disabled {{ old('receiver_branch_code', $shipment->receiver_branch_code) ? '' : 'selected' }}>اختر فرع الاستلام</option>
                                        @foreach ($branches as $branch)
                                            @continue($branch->code === auth()->user()->branch_code)
                                            <option value="{{ $branch->code }}" @selected(old('receiver_branch_code', $shipment->receiver_branch_code) == $branch->code)>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">اسم المستلم</label>
                                    <input type="text" name="receiver_name" x-model="selectedName" @input="selectedId=''" class="px-4 w-full h-11 text-sm bg-white rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>

                                <div class="relative">
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">رقم الهاتف</label>
                                    <input type="hidden" name="receiver_phone" :value="selectedPhone">
                                    <div class="flex overflow-hidden items-center h-11 bg-white rounded-xl border border-gray-200 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:bg-gray-900 dark:border-gray-600">
                                        <button type="button" @click.stop="openCountry = !openCountry" class="flex gap-2 items-center px-3 h-full bg-gray-50 border-l border-gray-200 shrink-0 dark:bg-gray-800 dark:border-gray-700">
                                            <img :src="`https://flagcdn.com/w20/${countryFlag}.png`" class="w-5 h-auto rounded-[2px]">
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="countryCode"></span>
                                        </button>
                                        <input type="tel" x-model="localNumber" @input="updatePhone()" placeholder="7XXXXXXXX" class="px-3 w-full h-full text-sm tracking-wider text-left bg-transparent border-none outline-none focus:ring-0 dark:text-white" dir="ltr">
                                    </div>
                                    <div x-show="openCountry" @click.outside="openCountry = false" x-transition class="overflow-y-auto absolute z-50 mt-2 w-full max-h-48 bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-gray-800 dark:border-gray-700 custom-scrollbar">
                                        <template x-for="country in countries" :key="country.code">
                                            <button type="button" @click="setCountry(country.code)" class="flex justify-between items-center px-4 py-2 w-full text-sm text-left transition-colors hover:bg-primary/5 dark:hover:bg-gray-700">
                                                <div class="flex gap-2 items-center">
                                                    <img :src="`https://flagcdn.com/w20/${country.flag}.png`" class="w-5 h-auto rounded-[2px]">
                                                    <span class="text-gray-700 dark:text-gray-300" x-text="country.code"></span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-row-reverse gap-3 justify-start items-center pt-6 mt-8 border-t border-gray-100 dark:border-gray-800">
                            <button type="submit" :disabled="isSubmitting" class="flex items-center gap-2 px-8 py-2.5 text-sm font-bold text-white rounded-xl bg-primary hover:bg-primary-hover active:scale-95 disabled:opacity-70 min-w-[140px] justify-center">
                                <span x-show="!isSubmitting">حفظ التعديلات</span>
                                <span x-show="isSubmitting" class="flex gap-2 items-center">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                            <button type="button" @click="isSenderReceiverModalOpen = false" class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- 2. مودال تفاصيل الطرد --}}
        <template x-teleport="body">
            <div x-show="isDetailsModalOpen" x-cloak class="fixed inset-0 z-[999999] flex items-center justify-center p-4 sm:p-6" @keydown.escape.window="isDetailsModalOpen = false">
                <div x-show="isDetailsModalOpen" x-transition.opacity class="fixed inset-0 backdrop-blur-sm bg-gray-900/60" @click="isDetailsModalOpen = false"></div>
                <div x-show="isDetailsModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="relative w-full max-w-2xl bg-white shadow-2xl rounded-[2rem] dark:bg-boxdark p-6 sm:p-8" dir="rtl">
                    
                    <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100 dark:border-gray-800">
                        <button type="button" @click="isDetailsModalOpen = false" class="text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300"><span class="material-symbols-outlined text-[24px]">close</span></button>
                        <div class="flex gap-3 items-center">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">تعديل تفاصيل الطرد</h2>
                            <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 text-warning-500 dark:bg-warning-500/10"><span class="material-symbols-outlined text-[22px]">inventory_2</span></div>
                        </div>
                    </div>

                    <form action="{{ route('shipment.update', $shipment->id) }}" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="details">

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">الرمز الخاص</label>
                                <input type="text" name="code" value="{{ old('code', $shipment->code) }}" class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                            </div>
                            
                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">رقم السند</label>
                                <input type="text" value="{{ $shipment->bond_number }}" class="px-4 w-full h-11 text-sm text-gray-400 bg-gray-100 rounded-xl border border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700" disabled>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">نوع الطرد</label>
                                <input type="text" name="package_type" value="{{ old('package_type', $shipment->package_type) }}" placeholder="مثال: كرتون / أكياس..." class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">إجمالي المبلغ (ر.ي)</label>
                                <input type="number" name="total_amount" value="{{ old('total_amount', $shipment->total_amount) }}" step="0.01" min="0" class="px-4 w-full h-11 font-mono text-sm bg-gray-50 rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">حالة الطلب الأساسية</label>
                                <select name="status" class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    <option value="pending" @selected(old('status', $shipment->status) == 'pending')>قيد الانتظار</option>
                                    <option value="in_transit" @selected(old('status', $shipment->status) == 'in_transit')>في الطريق</option>
                                    <option value="delivered" @selected(old('status', $shipment->status) == 'delivered')>تم التسليم</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">الملاحظات</label>
                                <textarea name="notes" rows="3" class="p-4 w-full text-sm bg-gray-50 rounded-xl border border-gray-200 resize-none focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">{{ old('notes', $shipment->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="flex flex-row-reverse gap-3 justify-start items-center pt-6 mt-8 border-t border-gray-100 dark:border-gray-800">
                            <button type="submit" :disabled="isSubmitting" class="flex items-center gap-2 px-8 py-2.5 text-sm font-bold text-white rounded-xl bg-primary hover:bg-primary-hover active:scale-95 disabled:opacity-70 min-w-[140px] justify-center">
                                <span x-show="!isSubmitting">حفظ التعديلات</span>
                                <span x-show="isSubmitting" class="flex gap-2 items-center">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                            <button type="button" @click="isDetailsModalOpen = false" class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- 3. مودال طريقة الدفع --}}
        <template x-teleport="body">
            <div x-show="isPaymentModalOpen" x-cloak class="fixed inset-0 z-[999999] flex items-center justify-center p-4 sm:p-6" @keydown.escape.window="isPaymentModalOpen = false">
                <div x-show="isPaymentModalOpen" x-transition.opacity class="fixed inset-0 backdrop-blur-sm bg-gray-900/60" @click="isPaymentModalOpen = false"></div>
                <div x-show="isPaymentModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="relative w-full max-w-3xl bg-white shadow-2xl rounded-[2rem] dark:bg-boxdark p-6 sm:p-8" dir="rtl">
                    
                    <div class="flex justify-between items-center pb-5 mb-6 border-b border-gray-100 dark:border-gray-800">
                        <button type="button" @click="isPaymentModalOpen = false" class="text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300"><span class="material-symbols-outlined text-[24px]">close</span></button>
                        <div class="flex gap-3 items-center">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">تعديل طريقة الدفع</h2>
                            <div class="flex justify-center items-center w-10 h-10 text-green-500 bg-green-50 rounded-xl dark:bg-green-500/10"><span class="material-symbols-outlined text-[22px]">payments</span></div>
                        </div>
                    </div>

                    <form action="{{ route('shipment.update', $shipment->id) }}" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                        @csrf @method('PUT')
                        <input type="hidden" name="section" value="payment">

                        <div class="space-y-6">
                            {{-- أنواع الدفع ككروت --}}
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                {{-- Prepaid --}}
                                <label class="flex relative flex-col justify-center items-center p-4 rounded-2xl border-2 transition-all cursor-pointer group"
                                       :class="payment_method === 'prepaid' ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-100 bg-white dark:bg-gray-900 dark:border-gray-700 hover:border-primary/50'">
                                    <input type="radio" name="payment_method" value="prepaid" x-model="payment_method" class="sr-only">
                                    <span class="material-symbols-outlined text-[28px] mb-2" :class="payment_method === 'prepaid' ? 'text-primary' : 'text-gray-400 group-hover:text-primary'">account_balance</span>
                                    <span class="text-sm font-bold" :class="payment_method === 'prepaid' ? 'text-primary' : 'text-gray-600 dark:text-gray-300'">دفع مقدم</span>
                                </label>

                                {{-- COD --}}
                                <label class="flex relative flex-col justify-center items-center p-4 rounded-2xl border-2 transition-all cursor-pointer group"
                                       :class="payment_method === 'cod' ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-100 bg-white dark:bg-gray-900 dark:border-gray-700 hover:border-primary/50'">
                                    <input type="radio" name="payment_method" value="cod" x-model="payment_method" class="sr-only">
                                    <span class="material-symbols-outlined text-[28px] mb-2" :class="payment_method === 'cod' ? 'text-primary' : 'text-gray-400 group-hover:text-primary'">local_shipping</span>
                                    <span class="text-sm font-bold" :class="payment_method === 'cod' ? 'text-primary' : 'text-gray-600 dark:text-gray-300'">عند التسليم (COD)</span>
                                </label>

                                {{-- Partial --}}
                                <label class="flex relative flex-col justify-center items-center p-4 rounded-2xl border-2 transition-all cursor-pointer group"
                                       :class="payment_method === 'partial_payment' ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-100 bg-white dark:bg-gray-900 dark:border-gray-700 hover:border-primary/50'">
                                    <input type="radio" name="payment_method" value="partial_payment" x-model="payment_method" class="sr-only">
                                    <span class="material-symbols-outlined text-[28px] mb-2" :class="payment_method === 'partial_payment' ? 'text-primary' : 'text-gray-400 group-hover:text-primary'">donut_large</span>
                                    <span class="text-sm font-bold" :class="payment_method === 'partial_payment' ? 'text-primary' : 'text-gray-600 dark:text-gray-300'">دفع جزئي</span>
                                </label>

                                {{-- Credit --}}
                                <label class="flex relative flex-col justify-center items-center p-4 rounded-2xl border-2 transition-all cursor-pointer group"
                                       :class="payment_method === 'customer_credit' ? 'border-primary bg-primary/5 dark:bg-primary/10' : 'border-gray-100 bg-white dark:bg-gray-900 dark:border-gray-700 hover:border-primary/50'">
                                    <input type="radio" name="payment_method" value="customer_credit" x-model="payment_method" class="sr-only">
                                    <span class="material-symbols-outlined text-[28px] mb-2" :class="payment_method === 'customer_credit' ? 'text-primary' : 'text-gray-400 group-hover:text-primary'">assignment_ind</span>
                                    <span class="text-sm font-bold text-center" :class="payment_method === 'customer_credit' ? 'text-primary' : 'text-gray-600 dark:text-gray-300'">آجل (على الحساب)</span>
                                </label>
                            </div>

                            {{-- إعدادات إضافية بناءً على الاختيار --}}
                            <div class="p-5 mt-4 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800/50 dark:border-gray-700" 
                                 x-show="['prepaid', 'partial_payment'].includes(payment_method)" x-transition>
                                
                                <div x-show="payment_method === 'partial_payment'" class="mb-5">
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">المبلغ المدفوع مقدمًا <span class="text-error-500">*</span></label>
                                    <input type="number" :name="payment_method === 'partial_payment' ? 'partial_amount' : null" :disabled="payment_method !== 'partial_payment'" value="{{ old('partial_amount') }}" step="0.01" class="px-4 w-full h-11 font-mono text-sm bg-white rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>

                                <div>
                                    <label class="block mb-3 text-xs font-bold text-gray-700 dark:text-gray-400">طريقة تحصيل الدفعة المقدمة</label>
                                    <div class="flex gap-4">
                                        <label class="flex gap-2 items-center cursor-pointer">
                                            <input type="radio" :name="(['prepaid', 'partial_payment'].includes(payment_method) ? 'prepaid_payment_method' : null)" value="cash" x-model="prepaid_method" class="w-4 h-4 text-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-600">
                                            <span class="text-sm font-bold text-gray-800 dark:text-white">كاش (نقدي)</span>
                                        </label>
                                        <label class="flex gap-2 items-center cursor-pointer">
                                            <input type="radio" :name="(['prepaid', 'partial_payment'].includes(payment_method) ? 'prepaid_payment_method' : null)" value="bank_transfer" x-model="prepaid_method" class="w-4 h-4 text-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-600">
                                            <span class="text-sm font-bold text-gray-800 dark:text-white">تحويل بنكي / محفظة</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-4" x-show="prepaid_method === 'bank_transfer'" x-transition>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-700 dark:text-gray-400">رقم الإيداع / السند <span class="text-error-500">*</span></label>
                                    <input type="text" :name="prepaid_method === 'bank_transfer' ? 'prepaid_reference' : null" value="{{ old('prepaid_reference') }}" class="px-4 w-full h-11 text-sm bg-white rounded-xl border border-gray-200 focus:border-primary dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-row-reverse gap-3 justify-start items-center pt-6 mt-8 border-t border-gray-100 dark:border-gray-800">
                            <button type="submit" :disabled="isSubmitting" class="flex items-center gap-2 px-8 py-2.5 text-sm font-bold text-white rounded-xl bg-primary hover:bg-primary-hover active:scale-95 disabled:opacity-70 min-w-[140px] justify-center">
                                <span x-show="!isSubmitting">حفظ الدفع</span>
                                <span x-show="isSubmitting" class="flex gap-2 items-center">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </span>
                            </button>
                            <button type="button" @click="isPaymentModalOpen = false" class="px-6 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>

@endsection

@section('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customerPicker', (url, initial = null) => ({
                query: '', open: false, loading: false, results: [],
                selectedId: '', selectedName: '', selectedPhone: '',
                countryCode: '+967', countryFlag: 'ye', localNumber: '', openCountry: false,
                countries: [
                    { code: '+967', flag: 'ye' }, { code: '+966', flag: 'sa' },
                    { code: '+971', flag: 'ae' }, { code: '+965', flag: 'kw' },
                    { code: '+974', flag: 'qa' }, { code: '+968', flag: 'om' },
                ],

                init() {
                    if (initial && typeof initial === 'object') {
                        this.selectedId = initial.id ?? '';
                        this.selectedName = initial.name ?? '';
                        this.parsePhone(initial.phone ?? '');
                        this.query = this.selectedName;
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
                    this.selectedPhone = (this.localNumber || '').trim() ? this.countryCode + this.localNumber.trim() : '';
                },
                async search() {
                    const q = (this.query || '').trim();
                    this.open = true;
                    if (q.length < 2) { this.results = []; this.loading = false; return; }
                    this.loading = true;
                    try {
                        const res = await fetch(`${url}?q=${encodeURIComponent(q)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (!res.ok) throw new Error('Search failed');
                        this.results = await res.json();
                    } catch (e) {
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
                },
            }));
        });
    </script>
@endsection