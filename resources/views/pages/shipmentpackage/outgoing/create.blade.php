@extends('layouts.app')

@section('title', 'تجهيز شحنة جديدة')

@section('content')
    @php
        // 1. استخراج الفروع الفريدة للفلترة
        $uniqueBranches = $pendingParcels
            ->map(function ($parcel) {
                return $parcel->receiverBranch?->name ?? ($parcel->receiverOfficeBranch?->name ?? 'فرع غير محدد');
            })
            ->filter()
            ->unique()
            ->values();

        // 2. تجهيز بيانات الطرود لـ Alpine.js شاملة بيانات الباركود (id, bond_number, uuid, code)
        $alpineParcels = $pendingParcels->map(function ($p) {
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
                'sender_name' => $p->senderCustomer->name ?? 'عميل غير محدد',
                'customer_name' => $p->receiverCustomer->name ?? 'عميل غير محدد',
                'branch_name' => $p->receiverBranch?->name ?? ($p->receiverOfficeBranch?->name ?? 'فرع غير محدد'),
            ];
        });
    @endphp
    @php
        // جلب كود الفرع الحالي (مثل ALQ) لتمريره للسكربت
        $currentBranchCode = auth()->user()->branch->code ?? 'B' . auth()->user()->branch_id;
    @endphp

    <div class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl" x-data="shipmentPreparation({{ $alpineParcels->toJson() }}, '{{ $currentBranchCode }}')">
        {{-- ================= نافذة تأكيد تحميل الطرد بعد مسح الباركود ================= --}}
        <div x-show="confirmScanModalOpen" x-cloak x-transition.opacity
            @keydown.escape.window="if(confirmScanModalOpen) cancelScannedParcel()"
            class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div @click.away="cancelScannedParcel()"
                x-show="confirmScanModalOpen"
                x-transition:enter="transform transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="relative w-full max-w-sm overflow-hidden bg-white dark:bg-boxdark rounded-3xl shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 text-center">

                <div class="flex justify-center items-center mx-auto mb-3 w-14 h-14 rounded-2xl bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[28px]">inventory_2</span>
                </div>

                <h3 class="text-base font-black font-headline text-on-surface dark:text-white">
                    هل تم تحميل "<span class="text-primary" x-text="pendingConfirmParcel?.package_type || 'الطرد'"></span>"؟
                </h3>
                <p class="mt-1 text-xs font-bold text-gray-400">تأكيد إضافة الطرد إلى قائمة الشحنة</p>

                {{-- تفاصيل الطرد بشكل مختصر وبسيط --}}
                <div class="p-3.5 my-4 space-y-2 text-xs text-right rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-400">رقم السند:</span>
                        <span class="font-mono font-black text-on-surface dark:text-white" x-text="'#' + (pendingConfirmParcel?.bond_number || pendingConfirmParcel?.id || '')"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-400">المرسل:</span>
                        <span class="font-black text-on-surface dark:text-white truncate max-w-[180px]" x-text="pendingConfirmParcel?.sender_name || 'غير محدد'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-400">المستلم:</span>
                        <span class="font-black text-on-surface dark:text-white truncate max-w-[180px]" x-text="pendingConfirmParcel?.customer_name || 'غير محدد'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-400">الوجهة:</span>
                        <span class="font-black text-primary" x-text="pendingConfirmParcel?.branch_name || 'غير محدد'"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-400">نوع الطرد:</span>
                        <span class="px-2 py-0.5 font-black rounded-md bg-primary/10 text-primary" x-text="pendingConfirmParcel?.package_type || 'طرد'"></span>
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <button type="button"
                        x-ref="confirmScanBtn"
                        @click="confirmScannedParcel()"
                        @keydown.enter.prevent.stop="confirmScannedParcel()"
                        class="flex flex-1 gap-2 justify-center items-center py-3 text-xs font-black text-white rounded-xl shadow-md transition-all bg-primary hover:bg-primary-hover shadow-primary/25 focus:ring-4 focus:ring-primary/30 outline-none active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span>نعم، موافق</span>
                        <span class="px-1.5 py-0.5 text-[10px] rounded bg-white/20 font-mono">Enter ↵</span>
                    </button>

                    <button type="button"
                        @click="cancelScannedParcel()"
                        class="px-4 py-3 text-xs font-black text-gray-600 bg-gray-100 rounded-xl transition-all hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= نافذة مسح الباركود بالكاميرا (Camera Scanner Modal) ================= --}}
        <div x-show="isScannerOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
            <div @click.away="stopCameraScanner()"
                class="relative w-full max-w-md overflow-hidden bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6">

                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100 dark:border-boxdark-2">
                    <div class="flex gap-2.5 items-center">
                        <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-on-surface dark:text-white">مسح باركود السند</h3>
                            <p class="text-[11px] font-bold text-gray-400">وجه الكاميرا نحو رمز QR الموجود في السند</p>
                        </div>
                    </div>
                    <button type="button" @click="stopCameraScanner()"
                        class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-full transition-colors hover:bg-gray-100 dark:hover:bg-boxdark-2 hover:text-error">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                {{-- منطقة الكاميرا --}}
                <div class="overflow-hidden relative bg-black rounded-2xl aspect-square">
                    <div id="qr-reader" class="w-full h-full"></div>
                </div>

                {{-- رسالة الحالة داخل المودال --}}
                <template x-if="scanFeedback.message">
                    <div x-transition class="flex gap-2 items-center p-3 mt-4 text-xs font-bold rounded-xl"
                        :class="{
                            'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400': scanFeedback
                                .type === 'success',
                            'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400': scanFeedback
                                .type === 'info',
                            'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400': scanFeedback
                                .type === 'error'
                        }">
                        <span class="material-symbols-outlined text-[18px]"
                            x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                        <span x-text="scanFeedback.message"></span>
                    </div>
                </template>

                <div class="flex justify-between items-center pt-3 mt-5 border-t border-gray-100 dark:border-boxdark-2">
                    <span class="text-xs font-bold text-gray-500">الطرود المحددة: <strong class="text-primary"
                            x-text="selectedParcels.length"></strong></span>
                    <button type="button" @click="stopCameraScanner()"
                        class="px-5 py-2 text-xs font-black text-white rounded-xl bg-primary hover:bg-primary-hover">
                        إغلاق الكاميرا
                    </button>
                </div>
            </div>
        </div>

        <form action="{{ route('shipmentpackage.outgoing.store') }}" method="POST" id="packageForm"
            class="flex relative flex-col" x-data="{ isSubmitting: false }"
            @submit="if(!isSubmitting) { isSubmitting = true; return true; } else { $event.preventDefault(); return false; }">
            @csrf

            {{-- حقول الطرود المختارة المخفية لترسل إلى السيرفر --}}
            <template x-for="parcel in selectedParcels" :key="parcel.id">
                <input type="hidden" name="parcel_ids[]" :value="parcel.id">
            </template>

            {{-- ================= الشريط العلوي (Sticky Header) ================= --}}
            <div
                class="sticky top-0 z-50 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
                <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
                    <div class="flex gap-4 items-center">
                        <a href="{{ route('shipmentpackage.outgoing.index') }}"
                            class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </a>
                        <div>
                            <div class="flex gap-2.5 items-center">
                                <h1 class="text-xl font-black leading-tight font-headline text-on-surface dark:text-white">
                                    تجهيز رحلة (إرسالية)
                                </h1>
                                @if(!empty($nextTrackingNumber))
                                    <span class="px-2.5 py-0.5 font-mono text-xs font-black rounded-lg border text-primary bg-primary-container dark:bg-primary/10 border-primary/20" title="رقم الرحلة التلقائي">
                                        #{{ $nextTrackingNumber }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] md:text-xs font-bold text-gray-500 dark:text-bodydark mt-0.5">
                                تحديد السائق واختيار الطرود المتجهة معه
                            </p>
                        </div>
                    </div>

                    {{-- أدوات الاعتماد (مدمجة في الهيدر للديسكتوب) --}}
                    <div class="hidden gap-4 items-center md:flex">
                        <div class="flex flex-col items-end pl-4 border-l border-gray-200 dark:border-boxdark-2">
                            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">الطرود المحددة</span>
                            <span class="text-sm font-black text-primary" x-text="selectedParcels.length + ' طرد'"></span>
                        </div>

                        <button type="submit"
                            @click="if(selectedParcels.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                            :disabled="selectedParcels.length === 0 || isSubmitting"
                            class="flex gap-2 items-center px-6 h-11 text-sm font-black text-white rounded-xl shadow-md transition-all bg-primary hover:bg-primary-hover shadow-primary/20 disabled:bg-surface disabled:dark:bg-boxdark-2 disabled:text-gray-400 disabled:shadow-none active:scale-95">
                            <template x-if="isSubmitting">
                                <div class="flex gap-2 items-center">
                                    <span
                                        class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                    <span>جاري الاعتماد...</span>
                                </div>
                            </template>
                            <template x-if="!isSubmitting">
                                <div class="flex gap-2 items-center">
                                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                                    <span>اعتماد الرحلة</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ================= محتوى الصفحة ================= --}}
            <div class="flex flex-col gap-6 p-4 mx-auto w-full max-w-7xl md:p-6">

                {{-- ================= الجزء العلوي: بيانات السائق ================= --}}
                <div class="flex z-30 flex-col gap-6 w-full">
                    <div
                        class="border border-gray-100 dark:border-boxdark-2 bg-white dark:bg-boxdark p-6 rounded-[2rem] shadow-sm relative">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 rounded-bl-full rounded-tr-[2rem] pointer-events-none bg-primary/5 dark:bg-primary/10">
                        </div>

                        <h3
                            class="flex relative z-10 gap-2 items-center pb-4 mb-5 text-sm font-black border-b border-gray-50 font-headline text-on-surface dark:text-white dark:border-boxdark-2">
                            <div
                                class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary-container dark:bg-primary/10 text-primary">
                                <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                            </div>
                            بيانات السائق المسؤول
                        </h3>

                        <div x-data="driverSelect({{ json_encode($drivers ?? []) }}, @js(array_values(config('countries', []))), { id: '{{ old('driver_id') }}', name: '{{ old('driver_name') }}', phone: '{{ old('driver_phone') }}', vehicle_type: '{{ old('driver_vehicle_type') }}' })" class="relative z-30">
                            <input type="hidden" name="driver_id" x-model="selectedDriverId">
                            <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                            <div class="grid grid-cols-1 gap-4 items-start md:grid-cols-2 lg:grid-cols-3 md:gap-6">
                                {{-- 1. اسم السائق مع خاصية البحث بالاسم والرقم --}}
                                <div class="relative" @click.away="showDriverNameDropdown = false">
                                    <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">
                                        اسم السائق <span class="text-error">*</span>
                                    </label>
                                    <div class="flex relative items-center">
                                        <input type="text" name="driver_name" x-model="nameInput"
                                            @input="searchDriverByName"
                                            @focus="if(nameInput.trim().length > 0 && !selectedDriverId) showDriverNameDropdown = true"
                                            :readonly="selectedDriverId !== null"
                                            placeholder="ابحث بالاسم أو اكتب اسماً جديداً..." autocomplete="off" required
                                            class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white"
                                            :class="selectedDriverId ?
                                                'bg-primary-container/20 border-primary/40 text-primary font-bold dark:bg-primary/10 pl-24 cursor-not-allowed' :
                                                'bg-white border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20'">

                                        {{-- مؤشر السائق المسجل وزر الإلغاء --}}
                                        <div x-show="selectedDriverId" class="flex absolute left-2 z-10 gap-1.5 items-center">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-bold text-primary bg-primary/10 rounded-md">
                                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                                مسجل
                                            </span>
                                            <button type="button" @click="resetSelection"
                                                class="p-1 text-gray-400 rounded-full transition-colors hover:text-error hover:bg-red-50 dark:hover:bg-red-900/20"
                                                title="إلغاء التحديد وتغيير السائق">
                                                <span class="material-symbols-outlined text-[18px]">close</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- قائمة نتائج البحث بالاسم وعرض الرقم ونوع المركبة --}}
                                    <div x-show="showDriverNameDropdown && nameInput.trim().length > 0 && !selectedDriverId"
                                        x-transition x-cloak
                                        class="absolute top-full right-0 mt-1.5 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-xl z-[65] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                        <template x-for="driver in filteredDriversByName" :key="driver.id">
                                            <button type="button" @click="selectDriver(driver)"
                                                class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-primary/5 dark:hover:bg-boxdark-2 dark:border-boxdark group">
                                                <div class="flex flex-col gap-0.5">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold transition-colors text-on-surface dark:text-white group-hover:text-primary"
                                                            x-text="driver.name"></span>
                                                        <template x-if="driver.vehicle_type">
                                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-primary/10 text-primary dark:bg-primary/20 dark:text-primary-hover"
                                                                x-text="driver.vehicle_type"></span>
                                                        </template>
                                                    </div>
                                                    <span
                                                        class="flex gap-1 items-center font-mono text-xs font-medium text-right text-gray-500 dark:text-bodydark dir-ltr">
                                                        <span class="material-symbols-outlined text-[14px] text-gray-400">call</span>
                                                        <span x-text="driver.phone || 'بدون رقم'"></span>
                                                    </span>
                                                </div>
                                                <div class="flex gap-1 items-center text-xs font-bold opacity-0 transition-opacity text-primary group-hover:opacity-100">
                                                    <span>اختيار</span>
                                                    <span class="material-symbols-outlined text-[16px]">arrow_back_ios</span>
                                                </div>
                                            </button>
                                        </template>
                                        <div x-show="filteredDriversByName.length === 0"
                                            class="px-4 py-3 text-center bg-surface dark:bg-boxdark-2">
                                            <span class="text-xs font-bold text-gray-500 dark:text-bodydark">سائق جديد، سيتم حفظه تلقائياً.</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. رقم هاتف السائق مع خاصية البحث بالرقم --}}
                                <div class="relative" @click.away="showDriverPhoneDropdown = false">
                                    <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">
                                        رقم هاتف السائق <span class="text-error">*</span>
                                    </label>
                                    <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40 focus-within:border-primary"
                                        :class="selectedDriverId ? 'bg-primary-container/20 dark:bg-primary/10 ring-primary/40' : ''"
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
                                                    <template x-for="country in filteredCountries" :key="country.code">
                                                        <button type="button"
                                                            @click="selectedCountry = country; openCountryDropdown = false; onCountryChanged()"
                                                            class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                            <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                                x-html="country.svg"></div>
                                                            <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                                x-text="country.name"></span>
                                                            <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500"
                                                                x-text="country.dial_code"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="tel" x-model="localPhoneNumber"
                                            @input="searchDriverByPhone"
                                            @focus="if(localPhoneNumber.trim().length > 0 && !selectedDriverId) showDriverPhoneDropdown = true"
                                            :readonly="selectedDriverId !== null"
                                            placeholder="7XXXXXXXX" required
                                            inputmode="numeric" autocomplete="off"
                                            :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                            class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                            :class="selectedDriverId ? 'font-bold text-primary cursor-not-allowed' : ''">

                                        {{-- زر الإلغاء إذا كان السائق مسجلاً يظهر أيضاً على حقل الهاتف --}}
                                        <div x-show="selectedDriverId" class="flex items-center px-2">
                                            <button type="button" @click="resetSelection"
                                                class="p-1 text-gray-400 rounded-full transition-colors hover:text-error hover:bg-red-50 dark:hover:bg-red-900/20"
                                                title="إلغاء التحديد وتغيير السائق">
                                                <span class="material-symbols-outlined text-[18px]">close</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- قائمة نتائج البحث بالرقم وعرض الاسم --}}
                                    <div x-show="showDriverPhoneDropdown && localPhoneNumber.trim().length > 0 && !selectedDriverId"
                                        x-transition x-cloak
                                        class="absolute top-full right-0 mt-1.5 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-xl z-[65] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar" dir="rtl">
                                        <template x-for="driver in filteredDriversByPhone" :key="driver.id">
                                            <button type="button" @click="selectDriver(driver)"
                                                class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-primary/5 dark:hover:bg-boxdark-2 dark:border-boxdark group">
                                                <div class="flex flex-col gap-0.5">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold transition-colors text-on-surface dark:text-white group-hover:text-primary"
                                                            x-text="driver.name"></span>
                                                        <template x-if="driver.vehicle_type">
                                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-primary/10 text-primary dark:bg-primary/20 dark:text-primary-hover"
                                                                x-text="driver.vehicle_type"></span>
                                                        </template>
                                                    </div>
                                                    <span
                                                        class="flex gap-1 items-center font-mono text-xs font-medium text-right text-gray-500 dark:text-bodydark dir-ltr">
                                                        <span class="material-symbols-outlined text-[14px] text-gray-400">call</span>
                                                        <span x-text="driver.phone || 'بدون رقم'"></span>
                                                    </span>
                                                </div>
                                                <div class="flex gap-1 items-center text-xs font-bold opacity-0 transition-opacity text-primary group-hover:opacity-100">
                                                    <span>اختيار</span>
                                                    <span class="material-symbols-outlined text-[16px]">arrow_back_ios</span>
                                                </div>
                                            </button>
                                        </template>
                                        <div x-show="filteredDriversByPhone.length === 0"
                                            class="px-4 py-3 text-center bg-surface dark:bg-boxdark-2">
                                            <span class="text-xs font-bold text-gray-500 dark:text-bodydark">سائق جديد، سيتم حفظه تلقائياً.</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. نوع المركبة --}}
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-600 dark:text-gray-300">
                                        نوع المركبة <span class="text-xs font-normal text-gray-400 dark:text-gray-500">(اختياري)</span>
                                    </label>
                                    <input type="text" name="driver_vehicle_type" x-model="vehicleTypeInput"
                                        list="outgoing_package_vehicle_types"
                                        :readonly="selectedDriverId !== null && !!vehicleTypeInput"
                                        placeholder="مثلاً: صالون، باص، دينا..."
                                        class="px-4 w-full h-12 text-sm rounded-xl border border-gray-200 transition-all outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white"
                                        :class="selectedDriverId && vehicleTypeInput ?
                                            'bg-primary-container/20 border-primary/40 text-primary font-bold dark:bg-primary/10 cursor-not-allowed' :
                                            'bg-white border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20'">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= الجزء السفلي: الطرود ================= --}}
                <div
                    class="w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 p-5 md:p-0 md:pt-5 md:overflow-hidden rounded-[2rem] shadow-sm">

                    <div class="flex flex-wrap gap-3 justify-between items-center px-2 mb-4 md:px-6">
                        <h3
                            class="flex gap-2 items-center text-sm font-black font-headline text-on-surface dark:text-white">
                            <div
                                class="flex justify-center items-center w-8 h-8 text-emerald-500 bg-emerald-50 rounded-lg dark:bg-emerald-500/10">
                                <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                            </div>
                            الطرود المتاحة للشحن
                        </h3>

                        <div class="flex gap-2">
                            <button type="button" @click="selectAllFiltered()"
                                class="flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-colors text-primary bg-primary-container dark:bg-primary/10 hover:bg-primary/20 active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">done_all</span>
                                <span class="hidden sm:inline">تحديد المعروض</span>
                                <span class="sm:hidden">الكل</span>
                            </button>
                            <button type="button" @click="selectedParcels = []; updateStats()"
                                class="flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-colors text-error bg-error/10 hover:bg-error/20 active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">deselect</span>
                                إلغاء (<span x-text="selectedParcels.length"></span>)
                            </button>
                        </div>
                    </div>

                    {{-- ================= شريط مسح الباركود والبحث والفلترة ================= --}}
                    <div class="flex flex-col gap-3 px-2 mb-4 md:px-6 lg:flex-row lg:items-center">

                        {{-- 1. حقل مسح الباركود السريع + زر الكاميرا --}}
                        <div class="flex gap-2 w-full lg:w-2/5">
                            <div class="relative flex-1">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-primary material-symbols-outlined">qr_code_scanner</span>
                                <input type="text" x-model="barcodeInput" x-ref="barcodeField"
                                    @keydown.enter.prevent.stop="handleBarcodeScan(barcodeInput)"
                                    placeholder="امسح باركود السند هنا (أو اكتب الرقم + Enter)..."
                                    class="pr-10 pl-4 w-full h-11 text-sm font-bold rounded-xl border-2 transition-all outline-none border-primary/30 bg-primary/5 dark:bg-primary/10 dark:border-primary/40 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary focus:bg-white">
                            </div>

                            <button type="button" @click="startCameraScanner()"
                                class="flex gap-1.5 justify-center items-center px-4 h-11 text-xs font-black text-white rounded-xl shadow-sm transition-all bg-primary hover:bg-primary-hover shrink-0 active:scale-95"
                                title="مسح باستخدام الكاميرا">
                                <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                                <span class="hidden sm:inline">مسح بالكاميرا</span>
                            </button>
                        </div>

                        {{-- 2. حقل البحث النصي العادي --}}
                        <div class="relative flex-1">
                            <span
                                class="absolute right-3 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined">search</span>
                            <input type="text" x-model="searchQuery" placeholder="فلترة برقم السند أو اسم العميل..."
                                class="pr-10 pl-4 w-full h-11 text-sm rounded-xl border border-gray-200 transition-all outline-none bg-surface dark:bg-boxdark dark:border-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white">
                        </div>

                        {{-- 3. فلتر الوجهة --}}
                        <div class="relative w-full lg:w-52 shrink-0">
                            <span
                                class="absolute right-3 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined">location_on</span>
                            <select x-model="selectedBranch"
                                class="pr-10 pl-4 w-full h-11 text-sm rounded-xl border border-gray-200 transition-all appearance-none cursor-pointer outline-none bg-surface dark:bg-boxdark dark:border-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white">
                                <option value="">كل الوجهات (عرض الكل)</option>
                                @foreach ($uniqueBranches as $branch)
                                    <option value="{{ $branch }}">{{ $branch }}</option>
                                @endforeach
                            </select>
                            <span
                                class="absolute left-3 top-1/2 text-gray-400 -translate-y-1/2 pointer-events-none material-symbols-outlined">expand_more</span>
                        </div>
                    </div>

                    {{-- ================= تنبيه نتيجة المسح الضوئي (Feedback Alert) ================= --}}
                    <template x-if="scanFeedback.message && !isScannerOpen">
                        <div class="px-2 mb-4 md:px-6">
                            <div x-transition
                                class="flex justify-between items-center p-3 text-xs font-bold rounded-xl border"
                                :class="{
                                    'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400': scanFeedback
                                        .type === 'success',
                                    'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400': scanFeedback
                                        .type === 'info',
                                    'bg-red-50 border-red-200 text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400': scanFeedback
                                        .type === 'error'
                                }">
                                <div class="flex gap-2 items-center">
                                    <span class="material-symbols-outlined text-[18px]"
                                        x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                                    <span x-text="scanFeedback.message"></span>
                                </div>
                                <button type="button" @click="scanFeedback.message = ''"
                                    class="opacity-60 hover:opacity-100">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- ================= عرض الديسكتوب (جدول احترافي متطور) ================= --}}
                    <div class="hidden overflow-x-auto pb-6 md:block">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr
                                    class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] bg-surface dark:bg-boxdark-2 dark:text-bodydark border-b border-t border-gray-100 dark:border-boxdark">
                                    <th class="px-4 py-4 w-12 text-center">تحديد</th>
                                    <th class="px-6 py-4">رقم السند</th>
                                    <th class="px-6 py-4">المرسل والمستلم</th>
                                    <th class="px-6 py-4">الوجهة</th>
                                    <th class="px-6 py-4 text-center">نوع الطرود</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-boxdark-2">
                                @forelse($pendingParcels as $parcel)
                                    @php
                                        $branchName =
                                            $parcel->receiverBranch?->name ??
                                            ($parcel->receiverOfficeBranch?->name ?? 'فرع غير محدد');
                                        $customerName = $parcel->receiverCustomer?->name ?? '';
                                        $senderName = $parcel->senderCustomer?->name ?? '';
                                        $bondNum = $parcel->bond_number ?? $parcel->id;
                                    @endphp
                                    <tr id="parcel-row-{{ $parcel->id }}"
                                        x-show="(searchQuery === '' || ('{{ $parcel->id }} {{ $bondNum }} {{ $customerName }} {{ $senderName }}').includes(searchQuery)) && (selectedBranch === '' || '{{ $branchName }}' === selectedBranch)"
                                        x-transition
                                        class="transition-all cursor-pointer group hover:bg-surface/50 dark:hover:bg-boxdark-2/50"
                                        @click="toggleParcel({{ $parcel->id }}, {{ $parcel->weight ?? 0 }})"
                                        :class="selectedParcels.some(p => p.id === {{ $parcel->id }}) ?
                                            'bg-primary/5 dark:bg-primary/10' : ''">

                                        <td class="px-4 py-4 text-center" @click.stop>
                                            <label
                                                class="flex relative justify-center items-center p-2 w-full h-full cursor-pointer">
                                                <input type="checkbox" class="sr-only peer"
                                                    :checked="selectedParcels.some(p => p.id === {{ $parcel->id }})"
                                                    @change.stop="toggleParcel({{ $parcel->id }}, {{ $parcel->weight ?? 0 }})">
                                                <div
                                                    class="flex justify-center items-center w-5 h-5 bg-white rounded border-2 border-gray-300 shadow-sm transition-all dark:border-gray-600 dark:bg-boxdark peer-checked:bg-primary peer-checked:border-primary">
                                                    <span
                                                        class="material-symbols-outlined text-[14px] text-white opacity-0 peer-checked:opacity-100 transition-opacity">check</span>
                                                </div>
                                            </label>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex gap-3 items-center">
                                                <div
                                                    class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark dark:border-boxdark group-hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">package_2</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="font-mono text-sm font-black tracking-tight text-on-surface dark:text-white">
                                                        {{ $parcel->bond_number_only ?? $parcel->id }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <div class="flex gap-1.5 items-center text-gray-800 dark:text-gray-200" title="المستلم">
                                                    <span class="material-symbols-outlined text-[16px] text-primary">person</span>
                                                    <span class="text-xs font-black truncate max-w-[160px]">
                                                        {{ $customerName ?: 'عميل غير محدد' }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-400 font-bold">(المستلم)</span>
                                                </div>
                                                <div class="flex gap-1.5 items-center text-gray-500 dark:text-bodydark" title="المرسل">
                                                    <span class="material-symbols-outlined text-[14px] text-gray-400">outbox</span>
                                                    <span class="text-[11px] font-bold truncate max-w-[160px]">
                                                        من: {{ $senderName ?: 'غير محدد' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex px-2 py-1.5 text-[10px] font-black rounded-lg bg-primary-container dark:bg-primary/10 text-primary truncate max-w-[150px] items-center gap-1.5">
                                                <span class="material-symbols-outlined text-[14px]">store</span>
                                                {{ $branchName }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                                {{ $parcel->package_type ?? 'فاضي' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-20 text-center">
                                            <div class="flex flex-col justify-center items-center">
                                                <div
                                                    class="flex justify-center items-center mb-4 w-16 h-16 rounded-full border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                                    <span
                                                        class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">inbox_customize</span>
                                                </div>
                                                <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا توجد طرود
                                                    بانتظار الشحن حالياً</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ================= عرض الموبايل (شبكة بطاقات Cards) ================= --}}
                    <div class="grid grid-cols-1 gap-4 px-2 pb-6 md:hidden">
                        @forelse($pendingParcels as $parcel)
                            @php
                                $branchName =
                                    $parcel->receiverBranch?->name ??
                                    ($parcel->receiverOfficeBranch?->name ?? 'فرع غير محدد');
                                $customerName = $parcel->receiverCustomer?->name ?? '';
                                $senderName = $parcel->senderCustomer?->name ?? '';
                                $bondNum = $parcel->bond_number ?? $parcel->id;
                            @endphp
                            <label
                                x-show="(searchQuery === '' || ('{{ $parcel->id }} {{ $bondNum }} {{ $customerName }} {{ $senderName }}').includes(searchQuery)) && (selectedBranch === '' || '{{ $branchName }}' === selectedBranch)"
                                x-transition class="block relative h-full cursor-pointer group">
                                <input type="checkbox" class="hidden peer"
                                    :checked="selectedParcels.some(p => p.id === {{ $parcel->id }})"
                                    @change="toggleParcel({{ $parcel->id }}, {{ $parcel->weight ?? 0 }})">

                                <div
                                    class="h-full bg-surface dark:bg-boxdark-2 p-5 rounded-[1.5rem] border border-gray-100 dark:border-boxdark flex flex-col gap-4 shadow-sm peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent peer-checked:bg-primary-container/20 dark:peer-checked:bg-primary/5 transition-all relative overflow-hidden hover:border-primary/30 dark:hover:border-primary/50 active:scale-[0.98]">
                                    <div class="flex relative z-10 justify-between items-start">
                                        <div class="flex gap-3 items-start">
                                            <div
                                                class="flex justify-center items-center w-12 h-12 text-gray-400 bg-white rounded-xl border border-gray-50 shadow-sm transition-colors dark:bg-boxdark peer-checked:bg-primary peer-checked:text-white shrink-0 dark:border-boxdark-2">
                                                <span class="material-symbols-outlined text-[24px]">package_2</span>
                                            </div>
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="font-mono text-sm font-black tracking-tight text-on-surface dark:text-white">
                                                    {{ $parcel->bond_number ?? $parcel->id }}
                                                </span>
                                                <span
                                                    class="text-[10px] font-bold text-primary bg-primary-container dark:bg-primary/10 dark:text-primary px-2 py-0.5 rounded-md flex items-center gap-1 w-max">
                                                    <span class="material-symbols-outlined text-[12px]">store</span>
                                                    {{ $branchName }}
                                                </span>
                                            </div>
                                        </div>

                                        <div
                                            class="flex justify-center items-center mt-1 w-6 h-6 bg-white rounded-full border-2 border-gray-200 shadow-sm transition-all dark:border-boxdark peer-checked:bg-primary peer-checked:border-primary shrink-0 dark:bg-boxdark-2">
                                            <span
                                                class="material-symbols-outlined text-white text-[16px] hidden peer-checked:block">check</span>
                                        </div>
                                    </div>

                                    <div class="relative z-10 pt-3 mt-auto border-t border-gray-100 dark:border-boxdark">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex gap-2 items-center text-xs text-gray-800 dark:text-white">
                                                <span class="material-symbols-outlined text-[16px] text-primary">person</span>
                                                <span class="font-black truncate">إلى: {{ $customerName ?: 'عميل غير محدد' }}</span>
                                            </div>
                                            <div class="flex gap-2 items-center text-[11px] text-gray-500 dark:text-bodydark">
                                                <span class="material-symbols-outlined text-[14px] text-gray-400">outbox</span>
                                                <span class="font-bold truncate">من: {{ $senderName ?: 'غير محدد' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div
                                class="flex flex-col justify-center items-center py-16 text-gray-400 rounded-3xl border-2 border-gray-200 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <p class="text-base font-bold text-gray-500 dark:text-bodydark font-headline">لا توجد طرود
                                    بانتظار الشحن حالياً</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ================= الشريط السفلي للموبايل ================= --}}
            <div
                class="md:hidden fixed bottom-6 left-4 right-4 p-4 bg-white/95 dark:bg-boxdark-2/95 backdrop-blur-md border border-gray-100 dark:border-boxdark rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15)] dark:shadow-black/50 flex justify-between items-center z-40">
                <div class="flex gap-3 items-center">
                    <div
                        class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-[24px]">summarize</span>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 dark:text-bodydark uppercase tracking-widest">
                            الطرود المحددة</div>
                        <div class="text-lg font-black text-on-surface dark:text-white"
                            x-text="selectedParcels.length + ' طرد'"></div>
                    </div>
                </div>

                <button type="submit"
                    @click="if(selectedParcels.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                    :disabled="selectedParcels.length === 0 || isSubmitting"
                    class="flex gap-2 items-center px-6 h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover shadow-primary/30 disabled:bg-surface disabled:dark:bg-boxdark disabled:text-gray-400 disabled:shadow-none active:scale-95">
                    <template x-if="!isSubmitting">
                        <div class="flex gap-2 items-center">
                            <span class="material-symbols-outlined text-[20px]">done_all</span>
                            <span>اعتماد</span>
                        </div>
                    </template>
                </button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    {{-- مكتبة html5-qrcode لمسح الباركود و QR عبر الكاميرا --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
   document.addEventListener('alpine:init', () => {
            // 💡 لاحظ أننا استقبلنا branchCode هنا
            Alpine.data('shipmentPreparation', (parcels, branchCode) => ({
                selectedParcels: [],
                totalWeight: 0,
                searchQuery: '',
                selectedBranch: '',
                barcodeInput: '',
                isScannerOpen: false,
                confirmScanModalOpen: false,
                pendingConfirmParcel: null,
                html5QrCode: null,
                scanFeedback: { message: '', type: '' },
                feedbackTimeout: null,
                parcels: parcels || [],
                branchCode: branchCode || '', // 💡 تخزين كود الفرع

                // ... دالة playBeep و showFeedback تبقى كما هي ...
                playBeep(isSuccess = true) {
                    try {
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
                    }, 4000);
                },

                // 💡 الدالة المحدثة بذكاء الباركود
                handleBarcodeScan(rawValue) {
                    if (this.confirmScanModalOpen) return;
                    if (!rawValue || !rawValue.trim()) return;

                    let scanned = rawValue.trim();

                    // معالجة الروابط
                    if (scanned.includes('/')) {
                        const parts = scanned.replace(/\/$/, '').split('/');
                        scanned = parts[parts.length - 1].split('?')[0];
                    }

                    // تنظيف علامة #
                    const cleanScanned = scanned.replace(/^#/, '');
                    
                    // ==========================================
                    // 💡 اللوجيك الذكي: تحويل 001 أو 1 إلى ALQ-001
                    // ==========================================
                    let expectedBondNumber = cleanScanned;
                    
                    // إذا كان الرقم المدخل لا يحتوي على شرطة (-)
                    if (!cleanScanned.includes('-')) {
                        // إضافة أصفار على اليسار لتصبح 3 خانات (إذا كتب 1 تصير 001)
                        let paddedNumber = cleanScanned.padStart(3, '0');
                        // دمجها مع كود الفرع
                        expectedBondNumber = this.branchCode + '-' + paddedNumber;
                    }

                    const numericScanned = cleanScanned.replace(/^0+/, '');

                    const matchedParcel = this.parcels.find(p => {
                        const pId = String(p.id);
                        const pBond = String(p.bond_number || '').replace(/^#/, ''); 
                        const pUuid = String(p.uuid || '');
                        const pCode = String(p.code || '');

                        return (
                            pId === cleanScanned ||
                            pBond === cleanScanned || // إذا مسح الباركود الكامل ALQ-001
                            pBond === expectedBondNumber || // 💡 إذا كتب 001 ومطابقتها مع ALQ-001
                            pBond.endsWith('-' + cleanScanned) || // 💡 احتياطي للمطابقة
                            pUuid === cleanScanned ||
                            pCode === cleanScanned
                        );
                    });

                    if (matchedParcel) {
                        const alreadySelected = this.selectedParcels.some(sp => sp.id === matchedParcel.id);
                        
                        // لعرض الرقم الصافي للمستخدم في رسالة النجاح
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
                            this.showFeedback(`الطرد رقم #${displayNum} محدد بالفعل في القائمة`, 'info');
                        }
                    } else {
                        this.playBeep(false);
                        this.showFeedback(`لم يتم العثور على طرد متاح يطابق الكود: (${cleanScanned})`, 'error');
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
                    this.showFeedback(`تم تحديد الطرد رقم #${parcel.display_num} (${parcel.customer_name || 'بدون اسم'}) بنجاح`, 'success');
                    this.$nextTick(() => {
                        if (!this.isScannerOpen) {
                            this.$refs.barcodeField?.focus();
                        }
                    });
                },

                cancelScannedParcel() {
                    this.confirmScanModalOpen = false;
                    this.pendingConfirmParcel = null;
                    this.$nextTick(() => {
                        if (!this.isScannerOpen) {
                            this.$refs.barcodeField?.focus();
                        }
                    });
                },

                // تشغيل كاميرا المسح
                startCameraScanner() {
                    this.isScannerOpen = true;
                    this.$nextTick(() => {
                        if (!this.html5QrCode) {
                            this.html5QrCode = new Html5Qrcode("qr-reader");
                        }
                        this.html5QrCode.start({
                                facingMode: "environment"
                            }, {
                                fps: 10,
                                qrbox: {
                                    width: 230,
                                    height: 230
                                }
                            },
                            (decodedText) => {
                                this.handleBarcodeScan(decodedText);
                            },
                            (errorMessage) => {}
                        ).catch((err) => {
                            this.showFeedback(
                                'تعذر الوصول إلى الكاميرا، تأكد من منح الصلاحية للمتصفح.',
                                'error');
                        });
                    });
                },

                // إيقاف الكاميرا
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
                        this.selectedParcels.push({
                            id: id,
                            weight: weight
                        });
                    }
                    this.updateStats();
                },

                selectAllFiltered() {
                    this.parcels.forEach(p => {
                        const matchesSearch = this.searchQuery === '' ||
                            (p.id + ' ' + p.bond_number + ' ' + p.customer_name + ' ' + (p.sender_name || '')).includes(this
                                .searchQuery);
                        const matchesBranch = this.selectedBranch === '' ||
                            p.branch_name === this.selectedBranch;

                        if (matchesSearch && matchesBranch) {
                            if (!this.selectedParcels.some(sp => sp.id === p.id)) {
                                this.selectedParcels.push({
                                    id: p.id,
                                    weight: p.weight
                                });
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

    <datalist id="outgoing_package_vehicle_types">
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
