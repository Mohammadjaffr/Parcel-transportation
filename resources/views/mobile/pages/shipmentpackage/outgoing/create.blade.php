@extends('mobile.layouts.app')

@section('title', 'إنشاء شحنة جديدة')

@section('content')
@php
    // 1. استخراج الفروع الفريدة للفلترة
    $uniqueBranches = $pendingParcels->map(function($parcel) {
        return $parcel->receiverBranch?->name ?? $parcel->receiverOfficeBranch?->name ?? 'فرع غير محدد';
    })->filter()->unique()->values();

    // 2. تجهيز بيانات الطرود لـ Alpine.js شاملة بيانات الباركود
    $alpineParcels = $pendingParcels->map(function($p) {
        $pkgType = match ($p->package_type) {
            'carton' => 'كرتون',
            'bag' => 'كيس',
            'envelope' => 'مغلف',
            null, '' => 'طرد',
            default => $p->package_type,
        };

        return [
            'id' => $p->id,
            'uuid' => $p->uuid ?? '',
            'bond_number' => $p->bond_number ?? '',
            'code' => $p->code ?? '',
            'weight' => $p->weight ?? 0,
            'package_type' => $pkgType,
            'customer_name' => $p->receiverCustomer->name ?? 'عميل غير محدد',
            'branch_name' => $p->receiverBranch?->name ?? $p->receiverOfficeBranch?->name ?? 'فرع غير محدد'
        ];
    });

    $currentBranchCode = auth()->user()->branch->code ?? 'B' . (auth()->user()->branch_id ?? '1');
@endphp

<div class="flex flex-col gap-6 px-4 pt-4 pb-32" x-data="shipmentPreparation({{ $alpineParcels->toJson() }}, '{{ $currentBranchCode }}')">

    {{-- ================= نافذة تأكيد تحميل الطرد بعد مسح الباركود ================= --}}
    <div x-show="confirmScanModalOpen" x-cloak x-transition.opacity
        @keydown.escape.window="if(confirmScanModalOpen) cancelScannedParcel()"
        class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="cancelScannedParcel()"
            x-show="confirmScanModalOpen"
            x-transition:enter="transform transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative w-full max-w-sm overflow-hidden bg-white rounded-3xl shadow-2xl border border-slate-100 p-6 text-center">

            <div class="flex justify-center items-center mx-auto mb-3 w-14 h-14 rounded-2xl bg-primary/10 text-primary">
                <span class="material-symbols-outlined text-[28px]">inventory_2</span>
            </div>

            <h3 class="text-base font-black font-headline text-slate-800">
                هل تم تحميل "<span class="text-primary" x-text="pendingConfirmParcel?.package_type || 'الطرد'"></span>"؟
            </h3>
            <p class="mt-1 text-xs font-bold text-slate-400">تأكيد إضافة الطرد إلى قائمة الشحنة</p>

            {{-- تفاصيل الطرد بشكل مختصر وبسيط --}}
            <div class="p-3.5 my-4 space-y-2 text-xs text-right rounded-2xl border border-slate-100 bg-slate-50">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-slate-400">رقم السند:</span>
                    <span class="font-mono font-black text-slate-800" x-text="'#' + (pendingConfirmParcel?.bond_number || pendingConfirmParcel?.id || '')"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-slate-400">المستلم:</span>
                    <span class="font-black text-slate-800 truncate max-w-[180px]" x-text="pendingConfirmParcel?.customer_name || 'غير محدد'"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-slate-400">الوجهة:</span>
                    <span class="font-black text-primary" x-text="pendingConfirmParcel?.branch_name || 'غير محدد'"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-slate-400">نوع الطرد:</span>
                    <span class="px-2 py-0.5 font-black rounded-md bg-primary/10 text-primary" x-text="pendingConfirmParcel?.package_type || 'طرد'"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-4">
                <button type="button" @click="confirmScannedParcel()" x-ref="confirmScanBtn"
                    class="flex gap-1.5 justify-center items-center h-12 text-xs font-black text-white rounded-2xl shadow-md transition-all bg-primary hover:bg-primary-hover shadow-primary/20 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>تأكيد التحميل</span>
                </button>
                <button type="button" @click="cancelScannedParcel()"
                    class="flex gap-1.5 justify-center items-center h-12 text-xs font-bold rounded-2xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    <span>إلغاء</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ================= نافذة الكاميرا للموبايل (Camera Scanner Modal) ================= --}}
    <div x-show="isScannerOpen" x-cloak x-transition.opacity
        class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-3">
        
        <div @click.away="stopCameraScanner()"
            class="w-full max-w-md bg-white rounded-[2.5rem] p-5 shadow-2xl border border-slate-100 flex flex-col gap-4">
            
            {{-- هيدر نافذة الكاميرا --}}
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 rounded-2xl bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-[22px]">qr_code_scanner</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 font-headline">مسح باركود السند</h3>
                        <p class="text-[10px] font-bold text-slate-400">وجه الكاميرا نحو رمز الـ QR في أعلى السند</p>
                    </div>
                </div>
                <button type="button" @click="stopCameraScanner()"
                    class="flex justify-center items-center w-9 h-9 rounded-full bg-slate-100 text-slate-500 active:scale-90">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            {{-- شاشة الكاميرا --}}
            <div class="overflow-hidden relative rounded-3xl border-2 shadow-inner bg-slate-950 aspect-square border-slate-100">
                <div id="mobile-qr-reader" class="w-full h-full"></div>
            </div>

            {{-- إشعار فوري داخل نافذة الكاميرا عند المسح --}}
            <template x-if="scanFeedback.message">
                <div x-transition class="flex gap-2.5 items-center p-3.5 text-xs font-bold rounded-2xl"
                    :class="{
                        'bg-emerald-50 text-emerald-700 border border-emerald-200': scanFeedback.type === 'success',
                        'bg-amber-50 text-amber-700 border border-amber-200': scanFeedback.type === 'info',
                        'bg-rose-50 text-rose-700 border border-rose-200': scanFeedback.type === 'error'
                    }">
                    <span class="material-symbols-outlined text-[20px] shrink-0"
                        x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                    <span class="leading-relaxed" x-text="scanFeedback.message"></span>
                </div>
            </template>

            {{-- شريط سفلي داخل المودال يوضح العدد وزر الإغلاق --}}
            <div class="flex justify-between items-center pt-2">
                <div class="flex gap-2 items-center">
                    <span class="text-xs font-bold text-slate-500">تم تحديد:</span>
                    <span class="px-3 py-1 text-xs font-black rounded-full bg-primary/10 text-primary"
                        x-text="selectedParcels.length + ' طرود'"></span>
                </div>
                <button type="button" @click="stopCameraScanner()"
                    class="px-6 py-2.5 text-xs font-black text-white rounded-2xl transition-transform bg-slate-900 active:scale-95">
                    إتمام وإغلاق
                </button>
            </div>
        </div>
    </div>

    {{-- الهيدر --}}
    <div class="flex justify-between items-center mb-2">
        <div class="flex gap-4 items-center">
            <a href="{{ route('shipmentpackage.outgoing.index') }}"
                class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 active:scale-90">
                <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
            </a>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-black font-headline text-slate-800">تجهيز شحنة</h1>
                @if(!empty($nextTrackingNumber))
                    <span class="px-2 py-0.5 text-xs font-mono font-black text-primary bg-primary/10 rounded-lg">
                        #{{ $nextTrackingNumber }}
                    </span>
                @endif
            </div>
        </div>

        {{-- زر اختصار سريع لفتح الكاميرا من الهيدر --}}
        <button type="button" @click="startCameraScanner()"
            class="flex gap-1.5 items-center px-3.5 py-2 text-xs font-black text-white rounded-2xl shadow-sm transition-all bg-primary shadow-primary/30 active:scale-95">
            <span class="material-symbols-outlined text-[18px]">qr_code_scanner</span>
            <span>مسح سند</span>
        </button>
    </div>

    <form action="{{ route('shipmentpackage.outgoing.store') }}" method="POST" id="packageForm">
        @csrf

        {{-- حقول الطرود المختارة المخفية --}}
        <template x-for="parcel in selectedParcels" :key="parcel.id">
            <input type="hidden" name="parcel_ids[]" :value="parcel.id">
        </template>

        <div class="space-y-6">

            {{-- بطاقة البيانات الأساسية --}}
            <div class="bg-white p-6 rounded-[2.5rem] border border-slate-50 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] space-y-6 relative overflow-visible">

                <div class="absolute -top-12 -right-12 w-24 h-24 rounded-full blur-3xl pointer-events-none bg-primary/5"></div>

                {{-- ================= السائق المسؤول ================= --}}
                <div x-data="driverSelect({{ json_encode($drivers ?? []) }}, @js(array_values(config('countries', []))), { id: '{{ old('driver_id') }}', name: '{{ old('driver_name') }}', phone: '{{ old('driver_phone') }}', vehicle_type: '{{ old('driver_vehicle_type') }}' })"
                    class="z-50 p-4 rounded-2xl border bg-slate-50 border-slate-100">
                    <span class="-top-2.5 right-4 bg-slate-50 px-2 text-[10px] font-black text-slate-500">
                        السائق المسؤول <span class="text-red-500">*</span>
                    </span>

                    <div class="grid relative grid-cols-1 gap-3 mt-2">
                        <input type="hidden" name="driver_id" x-model="selectedDriverId">
                        <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                        {{-- 1. اسم السائق مع خاصية البحث بالاسم والرقم --}}
                        <div class="relative" @click.away="showDriverNameDropdown = false">
                            <label class="block mb-1 text-[11px] font-bold text-slate-600">
                                اسم السائق <span class="text-red-500">*</span>
                            </label>
                            <div class="flex relative items-center">
                                <input type="text" name="driver_name" x-model="nameInput"
                                    @input="searchDriverByName"
                                    @focus="if(nameInput.trim().length > 0 && !selectedDriverId) showDriverNameDropdown = true"
                                    :readonly="selectedDriverId !== null"
                                    placeholder="ابحث بالاسم أو اكتب اسماً جديداً..." autocomplete="off" required
                                    class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none"
                                    :class="selectedDriverId ?
                                        'bg-primary/5 border-primary/30 text-primary font-bold pl-20 cursor-not-allowed' :
                                        'bg-white border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700'">

                                {{-- مؤشر السائق المسجل وزر الإلغاء --}}
                                <div x-show="selectedDriverId" class="flex absolute left-2 z-10 gap-1 items-center">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 text-[10px] font-bold text-primary bg-primary/10 rounded-md">
                                        <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                        مسجل
                                    </span>
                                    <button type="button" @click="resetSelection"
                                        class="p-1 text-slate-400 rounded-full hover:text-red-500 hover:bg-red-50 active:scale-95"
                                        title="إلغاء التحديد">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>
                            </div>

                            {{-- قائمة نتائج البحث بالاسم وعرض الرقم --}}
                            <div x-show="showDriverNameDropdown && nameInput.trim().length > 0 && !selectedDriverId"
                                x-transition x-cloak
                                class="absolute top-full right-0 mt-1 w-full bg-white border border-slate-100 rounded-xl shadow-lg z-50 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                <template x-for="driver in filteredDriversByName" :key="driver.id">
                                    <button type="button" @click="selectDriver(driver)"
                                        class="flex justify-between items-center px-4 py-2.5 w-full text-right border-b border-slate-50 transition-colors hover:bg-slate-50 group">
                                        <div class="flex flex-col gap-0.5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-slate-800" x-text="driver.name"></span>
                                                <template x-if="driver.vehicle_type">
                                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-primary/10 text-primary"
                                                        x-text="driver.vehicle_type"></span>
                                                </template>
                                            </div>
                                            <span class="text-[10px] text-slate-500 dir-ltr text-right font-mono"
                                                x-text="driver.phone || 'بدون رقم'"></span>
                                        </div>
                                        <span class="material-symbols-outlined text-slate-300 text-[16px]">arrow_back_ios</span>
                                    </button>
                                </template>
                                <div x-show="filteredDriversByName.length === 0"
                                    class="px-4 py-2.5 text-xs font-bold text-center text-slate-500 bg-slate-50/50">
                                    سائق جديد، سيتم حفظه تلقائياً.
                                </div>
                            </div>
                        </div>

                        {{-- 2. رقم هاتف السائق مع خاصية البحث بالرقم --}}
                        <div class="relative" @click.away="showDriverPhoneDropdown = false">
                            <label class="block mb-1 text-[11px] font-bold text-slate-600">
                                رقم هاتف السائق <span class="text-red-500">*</span>
                            </label>
                            <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 transition-all group ring-slate-200 focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary"
                                :class="selectedDriverId ? 'bg-primary/5 ring-primary/30' : ''" style="direction: ltr;">

                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r transition-colors bg-slate-50 border-slate-200 shrink-0 hover:bg-slate-100">
                                        <div class="w-5 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden"
                                            x-html="selectedCountry?.svg"></div>
                                        <span class="text-xs font-bold text-slate-600"
                                            x-text="selectedCountry?.dial_code"></span>
                                    </button>

                                    <div x-show="openCountryDropdown" x-cloak x-transition
                                        class="absolute top-full left-0 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-100 z-[60] overflow-hidden">
                                        <div class="p-2 border-b border-slate-50">
                                            <input type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                class="px-3 w-full h-8 text-xs rounded-lg outline-none bg-slate-50 focus:ring-1 ring-primary/30"
                                                dir="rtl">
                                        </div>
                                        <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                    @click="selectedCountry = country; openCountryDropdown = false; onCountryChanged()"
                                                    class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-slate-50">
                                                    <div class="w-5 h-auto flex items-center justify-center rounded-[2px] overflow-hidden"
                                                        x-html="country.svg"></div>
                                                    <span class="flex-1 text-xs font-bold text-slate-700"
                                                        x-text="country.name"></span>
                                                    <span class="text-xs text-slate-400 font-mono" x-text="country.dial_code"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <input type="tel" x-model="localPhoneNumber"
                                    @input="searchDriverByPhone"
                                    @focus="if(localPhoneNumber.trim().length > 0 && !selectedDriverId) showDriverPhoneDropdown = true"
                                    :readonly="selectedDriverId !== null"
                                    placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-slate-800"
                                    :class="selectedDriverId ? 'font-bold text-primary cursor-not-allowed' : ''">

                                <div x-show="selectedDriverId" class="flex items-center px-2">
                                    <button type="button" @click="resetSelection"
                                        class="p-1 text-slate-400 rounded-full hover:text-red-500 hover:bg-red-50 active:scale-95"
                                        title="إلغاء التحديد">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>
                            </div>

                            {{-- قائمة نتائج البحث بالرقم --}}
                            <div x-show="showDriverPhoneDropdown && localPhoneNumber.trim().length > 0 && !selectedDriverId"
                                x-transition x-cloak
                                class="absolute top-full right-0 mt-1 w-full bg-white border border-slate-100 rounded-xl shadow-lg z-50 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar" dir="rtl">
                                <template x-for="driver in filteredDriversByPhone" :key="driver.id">
                                    <button type="button" @click="selectDriver(driver)"
                                        class="flex justify-between items-center px-4 py-2.5 w-full text-right border-b border-slate-50 transition-colors hover:bg-slate-50">
                                        <div class="flex flex-col gap-0.5">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-slate-800" x-text="driver.name"></span>
                                                <template x-if="driver.vehicle_type">
                                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-primary/10 text-primary"
                                                        x-text="driver.vehicle_type"></span>
                                                </template>
                                            </div>
                                            <span class="text-[10px] text-slate-500 dir-ltr text-right font-mono"
                                                x-text="driver.phone"></span>
                                        </div>
                                        <span class="material-symbols-outlined text-slate-300 text-[16px]">arrow_back_ios</span>
                                    </button>
                                </template>
                                <div x-show="filteredDriversByPhone.length === 0" class="px-4 py-2.5 text-xs font-bold text-center text-slate-500 bg-slate-50/50">
                                    سائق جديد، سيتم حفظه تلقائياً.
                                </div>
                            </div>
                        </div>

                        {{-- 3. نوع المركبة --}}
                        <div>
                            <label class="block mb-1 text-[11px] font-bold text-slate-600">
                                نوع المركبة <span class="text-[10px] text-slate-400 font-normal">(اختياري)</span>
                            </label>
                            <input type="text" name="driver_vehicle_type" x-model="vehicleTypeInput"
                                :readonly="selectedDriverId !== null && !!vehicleTypeInput"
                                list="mobile_outgoing_package_vehicle_types"
                                placeholder="نوع المركبة (مثلاً: صالون، باص...)"
                                class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none"
                                :class="selectedDriverId && vehicleTypeInput ? 'bg-slate-50 border-transparent text-slate-500 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-700'">
                        </div>
                    </div>
                </div>
            </div>

            {{-- قسم اختيار الطرود مع الفلترة والمسح الضوئي --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center px-2">
                    <h3 class="flex gap-2 items-center font-black text-slate-800 font-headline">
                        الطرود المتاحة
                        <span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full"
                            x-text="selectedParcels.length">0</span>
                    </h3>
                    <div class="flex gap-2">
                        <button type="button" @click="selectAllFiltered()"
                            class="text-[10px] font-bold text-primary bg-primary/10 px-2.5 py-1 rounded-lg active:scale-95 transition-all">تحديد المعروض</button>
                        <button type="button"
                            @click="selectedParcels = []; updateStats()"
                            class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2.5 py-1 rounded-lg active:scale-95 transition-all">إلغاء</button>
                    </div>
                </div>

                {{-- شريط مسح الباركود والفلترة للموبايل --}}
                <div class="flex flex-col gap-2.5 px-2">
                    
                    {{-- 1. حقل مسح الباركود + زر الكاميرا --}}
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-primary text-[20px]">qr_code_scanner</span>
                            <input type="text" x-model="barcodeInput"
                                @keydown.enter.prevent="handleBarcodeScan(barcodeInput)"
                                placeholder="امسح أو اكتب رقم السند ثم Enter..."
                                class="pr-10 pl-3 w-full h-11 text-xs font-bold rounded-xl border-2 transition-all outline-none bg-primary/5 border-primary/20 text-slate-800 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <button type="button" @click="startCameraScanner()"
                            class="flex gap-1.5 justify-center items-center px-4 h-11 text-xs font-bold text-white rounded-xl shadow-sm transition-transform bg-slate-900 shrink-0 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                            <span>الكاميرا</span>
                        </button>
                    </div>

                    {{-- إشعار نتيجة المسح --}}
                    <template x-if="scanFeedback.message && !isScannerOpen">
                        <div x-transition class="flex justify-between items-center p-3 text-xs font-bold rounded-xl"
                            :class="{
                                'bg-emerald-50 text-emerald-700 border border-emerald-200': scanFeedback.type === 'success',
                                'bg-amber-50 text-amber-700 border border-amber-200': scanFeedback.type === 'info',
                                'bg-rose-50 text-rose-700 border border-rose-200': scanFeedback.type === 'error'
                            }">
                            <div class="flex gap-2 items-center">
                                <span class="material-symbols-outlined text-[18px]"
                                    x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                                <span x-text="scanFeedback.message"></span>
                            </div>
                            <button type="button" @click="scanFeedback.message = ''" class="opacity-60">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </div>
                    </template>

                    {{-- 2. البحث النصي وفلتر الوجهة --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div class="relative">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[18px]">search</span>
                            <input type="text" x-model="searchQuery" placeholder="بحث بالاسم أو الرقم..."
                                class="pr-9 pl-3 w-full h-11 text-xs bg-white rounded-xl border transition-all outline-none border-slate-200 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        </div>
                        <div class="relative">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[18px]">location_on</span>
                            <select x-model="selectedBranch"
                                class="pr-9 pl-7 w-full h-11 text-xs bg-white rounded-xl border transition-all appearance-none outline-none border-slate-200 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">كل الوجهات</option>
                                <?php foreach ($uniqueBranches as$branch): ?>
                                    <option value="{{ $branch }}">{{ $branch }}</option>
                                <?php endforeach; ?>
                            </select>
                            <span class="absolute left-2 top-1/2 -translate-y-1/2 pointer-events-none material-symbols-outlined text-slate-400 text-[18px]">expand_more</span>
                        </div>
                    </div>
                </div>

                {{-- قائمة بطاقات الطرود --}}
                <div class="grid grid-cols-1 gap-3">
                    <?php if (count($pendingParcels) > 0): ?>
                        <?php foreach ($pendingParcels as$parcel): ?>
                            @php
                                $branchName = $parcel->receiverBranch?->name ?? $parcel->receiverOfficeBranch?->name ?? 'فرع غير محدد';
                                $customerName =$parcel->receiverCustomer?->name ?? '';
                                $bondNum = $parcel->bond_number ?? $parcel->id;
                            @endphp
                            <label x-show="(searchQuery === '' || ('{{ $parcel->id }} {{$bondNum }} {{ $customerName }}').includes(searchQuery)) && (selectedBranch === '' || '{{$branchName }}' === selectedBranch)"
                                x-transition
                                class="block relative cursor-pointer group">
                                
                                <input type="checkbox" class="hidden peer"
                                    :checked="selectedParcels.some(p => p.id === {{ $parcel->id }})"
                                    @change="toggleParcel({{ $parcel->id }}, {{$parcel->weight ?? 0 }})">

                                <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgb(0,0,0,0.02)] peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent peer-checked:bg-primary/[0.02] transition-all flex items-center gap-4 active:scale-[0.98]">

                                    {{-- أيقونة الطرد --}}
                                    <div class="flex justify-center items-center w-12 h-12 rounded-2xl shadow-sm transition-all bg-slate-50 text-slate-400 peer-checked:bg-primary peer-checked:text-white shrink-0">
                                        <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="block font-mono text-sm font-black tracking-tight truncate text-slate-800">
                                                #{{ $parcel->bond_number ?? $parcel->id }}
                                            </span>

                                            <span class="text-[10px] font-black text-primary bg-primary/10 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">store</span>
                                                {{ $branchName }}
                                            </span>
                                        </div>

                                        <div class="flex flex-col gap-1">
                                            <div class="flex gap-1 items-center">
                                                <span class="material-symbols-outlined text-[12px] text-slate-400">location_on</span>
                                                <span class="text-[10px] font-bold text-slate-500 block truncate">{{ $parcel->receiverBranch->address ?? 'العنوان غير مسجل' }}</span>
                                            </div>
                                            <div class="flex gap-1 items-center">
                                                <span class="material-symbols-outlined text-[12px] text-slate-400">person</span>
                                                <span class="text-[10px] font-bold text-slate-500 block truncate">لـ: {{ $customerName ?: 'عميل غير محدد' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-center items-center w-6 h-6 rounded-full border-2 transition-all border-slate-200 peer-checked:bg-primary peer-checked:border-primary shrink-0">
                                        <span class="material-symbols-outlined text-white text-[16px] hidden peer-checked:block">check</span>
                                    </div>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center text-slate-400">
                            <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-slate-50">
                                <span class="material-symbols-outlined text-[32px] opacity-30">inbox_customize</span>
                            </div>
                            <p class="text-xs font-bold text-slate-500">لا توجد طرود بانتظار الشحن حالياً</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        {{-- قسم الإرسال في أسفل الصفحة --}}
        <div class="mt-8 mb-4 p-6 bg-white rounded-[2.5rem] border border-slate-50 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] flex flex-col sm:flex-row items-center justify-between gap-6"
            x-data="{ isSubmitting: false }">

            <button type="submit"
                @click="if(selectedParcels.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                :disabled="selectedParcels.length === 0 || isSubmitting"
                class="w-full sm:w-auto h-14 px-8 bg-slate-900 text-white rounded-2xl font-black text-sm shadow-[0_10px_25px_rgba(15,23,42,0.3)] disabled:bg-slate-100 disabled:text-slate-400 disabled:shadow-none transition-all active:scale-95 flex items-center justify-center gap-3">

                <template x-if="isSubmitting">
                    <div class="flex gap-2 items-center">
                        <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                        <span>جاري إنشاء الإرسالية...</span>
                    </div>
                </template>

                <template x-if="!isSubmitting">
                    <div class="flex gap-3 items-center">
                        <span>إنشاء الإرسالية</span>
                        <div class="flex justify-center items-center w-7 h-7 rounded-xl bg-white/20">
                            <span class="text-[12px]" x-text="selectedParcels.length">0</span>
                        </div>
                    </div>
                </template>
            </button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('shipmentPreparation', (parcels, currentBranchCode = '') => ({
            selectedParcels: [],
            totalWeight: 0,
            searchQuery: '',
            selectedBranch: '',
            barcodeInput: '',
            isScannerOpen: false,
            confirmScanModalOpen: false,
            pendingConfirmParcel: null,
            html5QrCode: null,
            lastScannedCode: '',
            lastScannedTime: 0,
            scanFeedback: { message: '', type: '' },
            feedbackTimeout: null,
            parcels: parcels || [],
            currentBranchCode: currentBranchCode || '',

            playBeep(isSuccess = true) {
                try {
                    if (navigator.vibrate) {
                        navigator.vibrate(isSuccess ? 80 : [100, 50, 100]);
                    }
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.type = isSuccess ? 'sine' : 'sawtooth';
                    osc.frequency.setValueAtTime(isSuccess ? 880 : 240, ctx.currentTime);
                    gain.gain.setValueAtTime(0.15, ctx.currentTime);
                    osc.start();
                    osc.stop(ctx.currentTime + (isSuccess ? 0.12 : 0.25));
                } catch (e) {}
            },

            showFeedback(message, type = 'success') {
                this.scanFeedback = { message, type };
                if (this.feedbackTimeout) clearTimeout(this.feedbackTimeout);
                this.feedbackTimeout = setTimeout(() => {
                    this.scanFeedback = { message: '', type: '' };
                }, 3500);
            },

            handleBarcodeScan(rawValue) {
                if (this.confirmScanModalOpen) return;
                if (!rawValue || !rawValue.trim()) return;

                let scanned = rawValue.trim();

                const now = Date.now();
                if (scanned === this.lastScannedCode && (now - this.lastScannedTime) < 2000) {
                    return;
                }
                this.lastScannedCode = scanned;
                this.lastScannedTime = now;

                if (scanned.includes('/')) {
                    const parts = scanned.replace(/\/$/, '').split('/');
                    scanned = parts[parts.length - 1].split('?')[0];
                }

                const cleanScanned = scanned.replace(/^#/, '');
                const numericScanned = cleanScanned.replace(/^0+/, '');

                let expectedBondNumber = '';
                if (this.currentBranchCode && /^\d+$/.test(numericScanned)) {
                    expectedBondNumber = this.currentBranchCode + '-' + numericScanned;
                }

                const matchedParcel = this.parcels.find(p => {
                    const pId = String(p.id);
                    const pBond = String(p.bond_number || '').replace(/^#/, '');
                    const pBondNoZeros = pBond.replace(/^0+/, '');
                    const pUuid = String(p.uuid || '');
                    const pCode = String(p.code || '');

                    return (
                        pId === cleanScanned ||
                        pId === numericScanned ||
                        pBond === cleanScanned ||
                        pBondNoZeros === numericScanned ||
                        (expectedBondNumber !== '' && pBond === expectedBondNumber) ||
                        pBond.endsWith('-' + cleanScanned) ||
                        pBond.endsWith('-' + numericScanned) ||
                        (pUuid !== '' && pUuid === cleanScanned) ||
                        (pCode !== '' && pCode === cleanScanned)
                    );
                });

                if (matchedParcel) {
                    const alreadySelected = this.selectedParcels.some(sp => sp.id === matchedParcel.id);
                    let displayNum = String(matchedParcel.bond_number || matchedParcel.id);
                    if (displayNum.includes('-')) {
                        displayNum = displayNum.split('-').pop();
                    }

                    if (!alreadySelected) {
                        this.pendingConfirmParcel = {
                            ...matchedParcel,
                            display_num: displayNum
                        };
                        this.confirmScanModalOpen = true;
                        this.playBeep(true);
                        setTimeout(() => {
                            this.$refs.confirmScanBtn?.focus();
                        }, 50);
                    } else {
                        this.playBeep(true);
                        this.showFeedback(`السند #${displayNum} محدد مسبقاً في القائمة`, 'info');
                    }
                } else {
                    this.playBeep(false);
                    this.showFeedback(`لم يتم العثور على طرد متاح بالكود: (${cleanScanned})`, 'error');
                }

                this.barcodeInput = '';
            },

            confirmScannedParcel() {
                if (!this.pendingConfirmParcel) return;
                const parcel = this.pendingConfirmParcel;
                if (!this.selectedParcels.some(sp => sp.id === parcel.id)) {
                    this.selectedParcels.push({
                        id: parcel.id,
                        weight: parcel.weight
                    });
                    this.updateStats();
                }
                this.confirmScanModalOpen = false;
                this.pendingConfirmParcel = null;
                this.showFeedback(`تم تحديد الطرد #${parcel.display_num} (${parcel.customer_name || 'بدون اسم'}) بنجاح`, 'success');
            },

            cancelScannedParcel() {
                this.confirmScanModalOpen = false;
                this.pendingConfirmParcel = null;
            },

            startCameraScanner() {
                this.isScannerOpen = true;
                this.$nextTick(() => {
                    if (!this.html5QrCode) {
                        this.html5QrCode = new Html5Qrcode("mobile-qr-reader");
                    }
                    this.html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: { width: 220, height: 220 } },
                        (decodedText) => {
                            this.handleBarcodeScan(decodedText);
                        },
                        (errorMessage) => {}
                    ).catch(() => {
                        this.showFeedback('يرجى السماح للمتصفح باستخدام الكاميرا.', 'error');
                    });
                });
            },

            stopCameraScanner() {
                if (this.html5QrCode && this.html5QrCode.isScanning) {
                    this.html5QrCode.stop().then(() => {
                        this.isScannerOpen = false;
                    }).catch(() => {
                        this.isScannerOpen = false;
                    });
                } else {
                    this.isScannerOpen = false;
                }
            },

            updateStats() {
                let weight = 0;
                this.selectedParcels.forEach(p => {
                    weight += parseFloat(p.weight || 0);
                });
                this.totalWeight = weight.toFixed(2);
            },

            toggleParcel(id, weight) {
                const index = this.selectedParcels.findIndex(p => p.id === id);
                if (index > -1) {
                    this.selectedParcels.splice(index, 1);
                } else {
                    this.selectedParcels.push({ id: id, weight: weight });
                }
                this.updateStats();
            },

            selectAllFiltered() {
                this.parcels.forEach(p => {
                    const matchesSearch = this.searchQuery === '' || 
                        (p.id + ' ' + p.bond_number + ' ' + p.customer_name).includes(this.searchQuery);
                    const matchesBranch = this.selectedBranch === '' || 
                        p.branch_name === this.selectedBranch;

                    if (matchesSearch && matchesBranch) {
                        if (!this.selectedParcels.some(sp => sp.id === p.id)) {
                            this.selectedParcels.push({ id: p.id, weight: p.weight });
                        }
                    }
                });
                this.updateStats();
            }
        }));

        // مكون السائق (بحث بالاسم والرقم بنفس أسلوب ومنطق العملاء في الطرود)
        Alpine.data('driverSelect', (initialDrivers, countriesList, initialData) => ({
            drivers: initialDrivers || [],
            countries: countriesList || [],
            filteredDriversByName: [],
            filteredDriversByPhone: [],
            selectedCountry: null,
            openCountryDropdown: false,
            searchCountryQuery: '',
            localPhoneNumber: '',
            nameInput: '',
            vehicleTypeInput: '',
            selectedDriverId: null,
            showDriverNameDropdown: false,
            showDriverPhoneDropdown: false,

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

                    this.selectedCountry = this.countries.find(c => phone.startsWith(c.dial_code)) ||
                        this.countries.find(c => c.code === 'YE') ||
                        this.countries[0];

                    if (this.selectedCountry && phone.startsWith(this.selectedCountry.dial_code)) {
                        this.localPhoneNumber = phone.substring(this.selectedCountry.dial_code.length);
                    } else {
                        this.localPhoneNumber = phone;
                    }

                    this.selectedDriverId = initialData.id ? Number(initialData.id) : null;
                    this.nameInput = initialData.name || '';
                    this.vehicleTypeInput = initialData.vehicle_type || '';
                } else {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                }
            },

            get filteredCountries() {
                if (this.searchCountryQuery === '') return this.countries;
                const query = this.searchCountryQuery.toLowerCase();
                return this.countries.filter(c => c.name.toLowerCase().includes(query) || c.dial_code.includes(query));
            },

            get fullPhoneNumber() {
                if (!this.localPhoneNumber) return '';
                let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';
                let cleanLocal = this.localPhoneNumber.replace(/^0+/, '');
                return dialCode + cleanLocal;
            },

            onCountryChanged() {
                if (this.localPhoneNumber.trim().length > 0 && !this.selectedDriverId) {
                    this.searchDriverByPhone();
                }
            },

            searchDriverByName() {
                this.selectedDriverId = null;
                let rawQuery = (this.nameInput || '').trim();
                if (rawQuery === '') {
                    this.filteredDriversByName = [];
                    this.showDriverNameDropdown = false;
                    return;
                }

                let normQuery = this.normalize(rawQuery);
                let searchWords = normQuery.split(/\s+/);

                this.filteredDriversByName = this.drivers.filter(d => {
                    if (!d) return false;

                    let nameMatch = false;
                    if (d.name) {
                        let normalizedName = this.normalize(d.name);
                        nameMatch = searchWords.every(word => normalizedName.includes(word));
                    }

                    let phoneMatch = d.phone && String(d.phone).includes(rawQuery);

                    return nameMatch || phoneMatch;
                });

                this.showDriverNameDropdown = true;
            },

            searchDriverByPhone() {
                this.selectedDriverId = null;
                let rawQuery = (this.localPhoneNumber || '').trim();
                if (rawQuery === '') {
                    this.filteredDriversByPhone = [];
                    this.showDriverPhoneDropdown = false;
                    return;
                }

                let cleanLocal = rawQuery.replace(/^0+/, '');
                let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';
                let fullTarget = dialCode + cleanLocal;

                let normQuery = this.normalize(rawQuery);
                let searchWords = normQuery.split(/\s+/);

                this.filteredDriversByPhone = this.drivers.filter(d => {
                    if (!d) return false;

                    let phoneMatch = false;
                    if (d.phone) {
                        let dPhone = String(d.phone).replace('+', '');
                        phoneMatch = dPhone.includes(cleanLocal) || dPhone.includes(fullTarget);
                    }

                    let nameMatch = false;
                    if (d.name) {
                        let normalizedName = this.normalize(d.name);
                        nameMatch = searchWords.every(word => normalizedName.includes(word));
                    }

                    return phoneMatch || nameMatch;
                });

                this.showDriverPhoneDropdown = true;
            },

            selectDriver(driver) {
                this.selectedDriverId = driver.id;
                this.nameInput = driver.name || '';
                this.vehicleTypeInput = driver.vehicle_type || '';

                let phone = driver.phone ? String(driver.phone) : '';
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

                this.showDriverNameDropdown = false;
                this.showDriverPhoneDropdown = false;
            },

            resetSelection() {
                this.selectedDriverId = null;
                this.nameInput = '';
                this.vehicleTypeInput = '';
                this.localPhoneNumber = '';
                this.filteredDriversByName = [];
                this.filteredDriversByPhone = [];
                this.showDriverNameDropdown = false;
                this.showDriverPhoneDropdown = false;
            }
        }));
    });
</script>

<datalist id="mobile_outgoing_package_vehicle_types">
    <option value="باص">
    <option value="صالون">
    <option value="دينا">
    <option value="شاص">
    <option value="هايلوكس">
    <option value="حبة وربع">
    <option value="نوها">
    <option value="خصوصي">
</datalist>
@endsection