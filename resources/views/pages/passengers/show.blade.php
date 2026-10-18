@extends('layouts.app')

@section('title', 'تفاصيل الراكب')
@section('Breadcrumb', 'تفاصيل الراكب')

@section('content')

    @php
        $passengerDate = $passenger->date ? \Carbon\Carbon::parse($passenger->date) : null;
        $dayName = $passengerDate ? $passengerDate->translatedFormat('l') : 'غير محدد';

        $brokerName = $passenger->broker?->name ?? 'بدون وسيط';
        $driverName = $passenger->driver->name ?? 'سائق غير محدد';
        $branchName = $passenger->branch->name ?? 'غير محدد';

        $officeCommission = (float) ($passenger->office_commission ?? 0);
        $otherOfficeCommission = (float) ($passenger->other_office_commission ?? 0);

        $brokerInitial = mb_substr($brokerName, 0, 1, 'UTF-8');
        $driverInitial = mb_substr($driverName, 0, 1, 'UTF-8');
    @endphp

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" x-data="{ showStatusModal: false, showDeleteModal: false, isSubmitting: false }" dir="rtl">

        {{-- ================= Header ================= --}}
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex flex-col gap-4 justify-between items-start md:flex-row md:items-center">

                <div class="flex gap-4 items-center min-w-0">
                    <a href="{{ route('passengers.index') }}"
                        class="inline-flex justify-center items-center w-11 h-11 text-gray-500 bg-white rounded-2xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 hover:text-primary hover:border-primary/30 hover:shadow-md active:scale-95 shrink-0">
                        <span class="material-symbols-outlined text-[20px] rtl:rotate-180">arrow_back</span>
                    </a>

                    <div class="min-w-0">
                        <div class="flex flex-wrap gap-2 items-center mb-2">
                            <span
                                class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black text-primary bg-primary-container rounded-xl dark:bg-primary/10">
                                <span class="material-symbols-outlined text-[15px]">confirmation_number</span>
                                سجل راكب
                            </span>

                            @php
                                $statusKey = strtolower($passenger->status ?? 'pending');
                                $statusLabel = match ($statusKey) {
                                    'completed' => 'مكتمل',
                                    'cancel' => 'ملغي',
                                    default => 'قيد الانتظار',
                                };
                                $statusClass = match ($statusKey) {
                                    'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                    'cancel' => 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
                                    default => 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                };
                            @endphp

                            <span class="inline-flex px-3 py-1.5 text-[10px] font-black rounded-xl border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <h1 class="text-2xl font-black tracking-tight md:text-3xl text-on-surface dark:text-white">
                            الراكب #{{ $passenger->id }}
                        </h1>

                        <div class="flex flex-wrap gap-3 items-center mt-2">
                            <x-phone-number :value="$passenger->passenger_number"
                                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />

                            <span class="hidden w-1 h-1 bg-gray-300 rounded-full md:inline-block dark:bg-gray-600"></span>

                            <span class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                {{ $passengerDate ? $passengerDate->format('Y-m-d') : 'غير محدد' }}
                                -
                                {{ $dayName }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-2.5 items-center w-full md:w-auto">
                    <!-- <a href="{{ route('receipt.generate', ['type' => 'passenger', 'id' => $passenger->id]) }}" target="_blank"
                        class="flex gap-2.5 items-center px-4 py-2.5 bg-white border border-gray-100 rounded-2xl text-xs font-black text-slate-700 shadow-sm transition-all hover:bg-gray-50 hover:shadow-md dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-300 dark:hover:bg-boxdark-2 active:scale-95">
                        <span class="material-symbols-outlined text-[18pٍx]">print</span>
                        طباعة الكشف
                    </a> -->

                    @if ($statusKey == 'pending')
                        @php
                            $whatsappUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerBooking');
                        @endphp
                        @if ($whatsappUrl)
                            <button type="button"
                                onclick="sendPassengerBookingDirect({{ $passenger->id }}, this, '{{ $whatsappUrl }}')"
                                class="flex gap-2.5 items-center px-4 py-2.5 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs font-black text-emerald-600 shadow-sm transition-all hover:bg-emerald-100/50 hover:shadow-md dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400 dark:hover:bg-emerald-500/20 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">send</span>
                                <span>إرسال الحجز (واتساب)</span>
                            </button>
                        @endif

                        <button type="button" @click="showStatusModal = true"
                            class="flex gap-2.5 items-center px-4 py-2.5 bg-amber-50 border border-amber-100 rounded-2xl text-xs font-black text-amber-600 shadow-sm transition-all hover:bg-amber-100/50 hover:shadow-md dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400 dark:hover:bg-amber-500/20 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">block</span>
                            إلغاء الراكب
                        </button>

                        <button type="button" @click="showDeleteModal = true"
                            class="flex gap-2.5 items-center px-4 py-2.5 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-black text-rose-600 shadow-sm transition-all hover:bg-rose-100/50 hover:shadow-md dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400 dark:hover:bg-rose-500/20 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            حذف الراكب
                        </button>
                    @endif

                    @if ($statusKey == 'cancel')
                        @php
                            $whatsappCancelUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerCancel');
                        @endphp
                        @if ($whatsappCancelUrl)
                            <button type="button"
                                onclick="sendPassengerCancelDirect({{ $passenger->id }}, this, '{{ $whatsappCancelUrl }}')"
                                class="flex gap-2.5 items-center px-4 py-2.5 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-black text-rose-600 shadow-sm transition-all hover:bg-rose-100/50 hover:shadow-md dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400 dark:hover:bg-rose-500/20 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">send</span>
                                <span>إرسال الإلغاء للوسيط (واتساب)</span>
                            </button>
                        @endif
                    @endif
                </div>

            </div>
        </div>

        {{-- ================= Top Summary Cards ================= --}}
        <div class="grid grid-cols-1 gap-4 mx-auto w-full max-w-7xl md:grid-cols-4">

            {{-- Date --}}
            {{-- <div
                class="p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex gap-4 items-center">
                    <div
                        class="flex justify-center items-center w-12 h-12 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10 shrink-0">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>

                    <div class="min-w-0">
                        <span class="block mb-1 text-[10px] font-black text-gray-400 dark:text-gray-500">
                            التاريخ
                        </span>

                        <span class="text-sm font-black text-on-surface dark:text-white">
                            {{ $passengerDate ? $passengerDate->format('Y-m-d') : 'غير محدد' }}
                        </span>
                    </div>
                </div>
            </div> --}}

            {{-- Location --}}
            {{-- <div
                class="p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex gap-4 items-center">
                    <div
                        class="flex justify-center items-center w-12 h-12 text-rose-500 bg-rose-50 rounded-xl dark:bg-rose-500/10 shrink-0">
                        <span class="material-symbols-outlined">location_on</span>
                    </div>

                    <div class="min-w-0">
                        <span class="block mb-1 text-[10px] font-black text-gray-400 dark:text-gray-500">
                            المكان
                        </span>

                        <span class="block text-sm font-black truncate text-on-surface dark:text-white">
                            {{ $passenger->location ?? 'غير محدد' }}
                        </span>
                    </div>
                </div>
            </div> --}}

            {{-- Count --}}
            {{-- <div
                class="p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex gap-4 items-center">
                    <div
                        class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10 shrink-0">
                        <span class="material-symbols-outlined">groups</span>
                    </div>

                    <div>
                        <span class="block mb-1 text-[10px] font-black text-gray-400 dark:text-gray-500">
                            عدد الركاب
                        </span>

                        <span class="text-sm font-black text-on-surface dark:text-white">
                            {{ number_format($passenger->count ?? 0, 0) }}
                        </span>
                    </div>
                </div>
            </div> --}}

            {{-- Commission --}}
            {{-- <div
                class="relative overflow-hidden p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <div class="absolute inset-y-0 right-0 w-1.5 bg-amber-500"></div>

                <div class="flex gap-4 items-center">
                    <div
                        class="flex justify-center items-center w-12 h-12 text-amber-600 bg-amber-50 rounded-xl dark:bg-amber-500/10 dark:text-amber-400 shrink-0">
                        <span class="material-symbols-outlined">payments</span>
                    </div>

                    <div>
                        <span class="block mb-1 text-[10px] font-black text-gray-400 dark:text-gray-500">
                            إجمالي العمولة
                        </span>

                        <span class="text-lg font-black text-on-surface dark:text-white">
                            {{ number_format($commission, 0) }}
                            <span class="text-xs font-bold text-gray-400">ر.ي</span>
                        </span>
                    </div>
                </div>
            </div> --}}
        </div>

        {{-- ================= Main Content ================= --}}
        <div class="grid grid-cols-1 gap-6 mx-auto w-full max-w-7xl lg:grid-cols-12">

            {{-- ================= Right / Main Details ================= --}}
            <div class="space-y-6 lg:col-span-8">

                {{-- Trip Details --}}
                <div
                    class="overflow-hidden bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">

                    <div
                        class="flex justify-between items-center px-6 py-5 border-b border-gray-100 dark:border-boxdark-2 bg-gray-50/60 dark:bg-boxdark-2/50">
                        <div class="flex gap-3 items-center">
                            <div
                                class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                                <span class="material-symbols-outlined text-[20px]">route</span>
                            </div>

                            <div>
                                <h2 class="text-lg font-black text-on-surface dark:text-white">
                                    تفاصيل الراكب
                                </h2>
                                <p class="mt-0.5 text-xs font-bold text-gray-500 dark:text-bodydark">
                                    بيانات الراكب الأساسية
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                            <div
                                class="flex gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <div
                                    class="flex justify-center items-center w-12 h-12 rounded-xl text-primary bg-primary-container dark:bg-primary/10 shrink-0">
                                    <span class="material-symbols-outlined">call</span>
                                </div>

                                <div class="min-w-0">
                                    <span class="block mb-1 text-[11px] font-black text-gray-400 dark:text-gray-500">
                                        رقم الراكب
                                    </span>

                                    <div class="flex flex-wrap gap-2 items-center">
                                        <x-phone-number :value="$passenger->passenger_number"
                                            class="text-sm font-black text-on-surface dark:text-white" />

                                        <span class="text-xs text-gray-400">
                                            ({{ $passengerDate ? $passengerDate->format('Y-m-d') : 'غير محدد' }})
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10 shrink-0">
                                    <span class="material-symbols-outlined">calendar_today</span>
                                </div>

                                <div class="min-w-0">
                                    <span class="block mb-1 text-[11px] font-black text-gray-400 dark:text-gray-500">
                                        اليوم والتاريخ
                                    </span>

                                    <span class="text-sm font-black text-on-surface dark:text-white">
                                        {{ $dayName }}
                                        <span class="text-gray-300">-</span>
                                        {{ $passengerDate ? $passengerDate->format('Y-m-d') : 'غير محدد' }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="flex gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-rose-500 bg-rose-50 rounded-xl dark:bg-rose-500/10 shrink-0">
                                    <span class="material-symbols-outlined">pin_drop</span>
                                </div>

                                <div class="min-w-0">
                                    <span class="block mb-1 text-[11px] font-black text-gray-400 dark:text-gray-500">
                                        مكان الانطلاق
                                    </span>

                                    <span class="block text-sm font-black truncate text-on-surface dark:text-white">
                                        {{ $passenger->pickup_location ?? 'غير محدد' }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="flex gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-indigo-500 bg-indigo-50 rounded-xl dark:bg-indigo-500/10 shrink-0">
                                    <span class="material-symbols-outlined">directions_car</span>
                                </div>

                                <div class="min-w-0">
                                    <span class="block mb-1 text-[11px] font-black text-gray-400 dark:text-gray-500">
                                        نوع المركبة
                                    </span>

                                    <span class="text-sm font-black text-on-surface dark:text-white">
                                        {{ $passenger->vehicle_type ?: 'غير محدد' }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="flex gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10 shrink-0">
                                    <span class="material-symbols-outlined">groups</span>
                                </div>

                                <div class="min-w-0">
                                    <span class="block mb-1 text-[11px] font-black text-gray-400 dark:text-gray-500">
                                        عدد الركاب
                                    </span>

                                    <span class="text-sm font-black text-on-surface dark:text-white">
                                        {{ number_format($passenger->count ?? 0, 0) }} راكب
                                    </span>
                                </div>
                            </div>

                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div
                                class="flex gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <div
                                    class="flex justify-center items-center w-12 h-12 rounded-xl text-emerald-500 bg-emerald-50 shrink-0">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>

                                <div class="min-w-0">
                                    <span class="block mb-1 text-[11px] font-black text-gray-400 dark:text-gray-500">
                                        عمولة المكتب
                                    </span>

                                    <span class="block text-sm font-black truncate text-on-surface dark:text-white">
                                        {{ number_format($officeCommission, 0) }} ر.ي
                                    </span>
                                </div>
                            </div>

                            <div
                                class="flex gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <div
                                    class="flex justify-center items-center w-12 h-12 rounded-xl text-amber-500 bg-amber-50 shrink-0">
                                    <span class="material-symbols-outlined">payments</span>
                                </div>

                                <div class="min-w-0">
                                    <span class="block mb-1 text-[11px] font-black text-gray-400 dark:text-gray-500">
                                        عمولة المكاتب الأخرى
                                    </span>

                                    <span class="block text-sm font-black truncate text-on-surface dark:text-white">
                                        {{ number_format($otherOfficeCommission, 0) }} ر.ي
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Note --}}
                <div
                    class="overflow-hidden bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                    <div
                        class="flex items-center px-6 py-5 border-b border-gray-100 dark:border-boxdark-2 bg-amber-50/50 dark:bg-amber-500/10">
                        <div
                            class="flex justify-center items-center ml-3 w-10 h-10 text-amber-500 bg-white rounded-xl shadow-sm dark:bg-boxdark shrink-0">
                            <span class="material-symbols-outlined text-[20px]">notes</span>
                        </div>

                        <div>
                            <h2 class="text-lg font-black text-on-surface dark:text-white">
                                الملاحظات
                            </h2>
                            <p class="mt-0.5 text-xs font-bold text-gray-500 dark:text-bodydark">
                                أي تفاصيل إضافية مرتبطة بالرحلة
                            </p>
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($passenger->note)
                            <p class="text-sm font-semibold leading-7 text-gray-600 dark:text-gray-300">
                                {{ $passenger->note }}
                            </p>
                        @else
                            <div
                                class="flex gap-3 items-center p-4 text-gray-400 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <span class="material-symbols-outlined text-[22px]">info</span>
                                <span class="text-sm font-bold">
                                    لا توجد ملاحظات مسجلة لهذا الراكب.
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($passenger->status === 'cancel' && !empty($passenger->cancel_reason))
                    {{-- سبب الإلغاء --}}
                    <div
                        class="overflow-hidden bg-white rounded-[2rem] border border-rose-100 shadow-sm dark:bg-boxdark dark:border-rose-900/30">
                        <div
                            class="flex items-center px-6 py-5 border-b border-rose-100 dark:border-rose-900/30 bg-rose-50/50 dark:bg-rose-500/10">
                            <div
                                class="flex justify-center items-center ml-3 w-10 h-10 text-rose-500 bg-white rounded-xl shadow-sm dark:bg-boxdark shrink-0">
                                <span class="material-symbols-outlined text-[20px]">block</span>
                            </div>

                            <div>
                                <h2 class="text-lg font-black text-rose-600 dark:text-rose-400">
                                    سبب الإلغاء
                                </h2>
                                <p class="mt-0.5 text-xs font-bold text-gray-500 dark:text-bodydark">
                                    السبب المسجل عند إلغاء هذا الحجز
                                </p>
                            </div>
                        </div>

                        <div class="p-6">
                            <p class="text-sm font-semibold leading-7 text-gray-700 dark:text-gray-300">
                                {{ $passenger->cancel_reason }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ================= Left / Side Info ================= --}}
            <div class="space-y-6 lg:col-span-4">

                {{-- People Card --}}
                <div
                    class="overflow-hidden bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">

                    <div
                        class="flex items-center px-6 py-5 border-b border-gray-100 dark:border-boxdark-2 bg-gray-50/60 dark:bg-boxdark-2/50">
                        <div
                            class="flex justify-center items-center ml-3 w-10 h-10 rounded-xl text-primary bg-primary-container dark:bg-primary/10 shrink-0">
                            <span class="material-symbols-outlined text-[20px]">badge</span>
                        </div>

                        <div>
                            <h2 class="text-lg font-black text-on-surface dark:text-white">
                                أطراف الرحلة
                            </h2>
                            <p class="mt-0.5 text-xs font-bold text-gray-500 dark:text-bodydark">
                                الوسيط والسائق فقط
                            </p>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">

                        {{-- Broker --}}
                        <div
                            class="flex gap-4 items-center p-4 rounded-2xl border border-emerald-100 bg-emerald-50/40 dark:bg-emerald-500/10 dark:border-emerald-500/20">
                            <div
                                class="flex justify-center items-center w-14 h-14 text-xl font-black text-white bg-emerald-500 rounded-2xl shadow-inner shrink-0">
                                {{ $brokerInitial ?: 'و' }}
                            </div>

                            <div class="min-w-0">
                                <span class="block mb-1 text-[10px] font-black text-emerald-600 dark:text-emerald-400">
                                    الوسيط
                                </span>

                                <h3 class="text-base font-black truncate text-on-surface dark:text-white">
                                    {{ $brokerName }}
                                </h3>
                                @if($passenger->broker?->phone)
                                    <span class="block text-xs font-bold text-gray-500 dark:text-gray-400 mt-0.5" dir="ltr">
                                        {{ $passenger->broker->phone }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- 1. تجهيز بيانات السائق النشط ذكياً قبل الكرت --}}
                        @php
                            // التحقق أولاً من وجود سائق للرحلة، وإلا نعتمد سائق السند المباشر
                            $activeDriver = $passenger->trip->driver ?? $passenger->driver;

                            $driverName = $activeDriver->name ?? 'غير محدد';
                            $driverPhone = $activeDriver->phone ?? null;
                            $driverInitial = $activeDriver ? mb_substr($driverName, 0, 1, 'UTF-8') : 'س';
                            $isTripDriver = isset($passenger->trip->driver); // للتحقق إن كان سائق رحلة
                        @endphp

                        {{-- ================= كرت السائق المسؤول ================= --}}
                        <div
                            class="flex gap-4 items-center p-4 rounded-2xl border border-primary/15 bg-primary-container/40 dark:bg-primary/10 dark:border-primary/20">

                            {{-- الحرف الأول من الاسم --}}
                            <div
                                class="flex justify-center items-center w-14 h-14 text-xl font-black text-white rounded-2xl shadow-inner bg-primary shrink-0">
                                {{ $driverInitial }}
                            </div>

                            {{-- تفاصيل السائق --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="block text-[10px] font-black text-primary">
                                        السائق
                                    </span>
                                    {{-- شارة تميز سائق الرحلة متناسقة مع ألوان الكرت الرئيسي --}}
                                    @if($isTripDriver)
                                        <span class="text-[8px] bg-primary/10 text-primary px-1.5 py-0.5 rounded-md font-black">
                                            سائق الرحلة
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-black truncate text-on-surface dark:text-white"
                                    title="{{ $driverName }}">
                                    {{ $driverName }}
                                </h3>

                                {{-- رقم الهاتف المحدث --}}
                                @if ($driverPhone)
                                    <x-phone-number :value="$driverPhone"
                                        class="mt-1 text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                @else
                                    <span class="mt-1 text-[11px] font-bold text-gray-400 block">لا يوجد رقم</span>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Record Info --}}
                {{-- <div
                    class="overflow-hidden bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">

                    <div
                        class="flex items-center px-6 py-5 border-b border-gray-100 dark:border-boxdark-2 bg-gray-50/60 dark:bg-boxdark-2/50">
                        <div
                            class="flex justify-center items-center ml-3 w-10 h-10 text-gray-500 bg-white rounded-xl shadow-sm dark:bg-boxdark dark:text-gray-400 shrink-0">
                            <span class="material-symbols-outlined text-[20px]">schedule</span>
                        </div>

                        <h2 class="text-lg font-black text-on-surface dark:text-white">
                            معلومات السجل
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">

                        <div class="flex gap-4 justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                رقم السجل
                            </span>

                            <span
                                class="inline-flex px-3 py-1 text-xs font-black rounded-xl text-primary bg-primary-container dark:bg-primary/10">
                                #{{ $passenger->id }}
                            </span>
                        </div>

                        <div class="w-full h-px bg-gray-100 dark:bg-boxdark-2"></div>

                        <div class="flex gap-4 justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                تاريخ الإضافة
                            </span>

                            <span class="text-xs font-black text-on-surface dark:text-white">
                                {{ $passenger->created_at ? $passenger->created_at->format('Y-m-d h:i A') : 'غير متوفر' }}
                            </span>
                        </div>

                        <div class="w-full h-px bg-gray-100 dark:bg-boxdark-2"></div>

                        <div class="flex gap-4 justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                آخر تحديث
                            </span>

                            <span class="text-xs font-black text-on-surface dark:text-white">
                                {{ $passenger->updated_at ? $passenger->updated_at->format('Y-m-d h:i A') : 'غير متوفر' }}
                            </span>
                        </div>
                    </div>
                </div> --}}

                {{-- Status --}}
                {{-- <div
                    class="p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                    <div class="flex gap-3 items-center">
                        <div
                            class="flex justify-center items-center w-11 h-11 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10 shrink-0">
                            <span class="material-symbols-outlined text-[22px]">verified</span>
                        </div>

                        <div>
                            <h3 class="text-sm font-black text-on-surface dark:text-white">
                                السجل منظم
                            </h3>

                            <p class="mt-0.5 text-xs font-bold text-gray-500 dark:text-bodydark">
                                تم عرض كل معلومة مرة واحدة بدون تكرار.
                            </p>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>

        {{-- ====================== Status Modal (Cancel Only) ====================== --}}
        <template x-teleport="body">
            <div x-show="showStatusModal" x-cloak
                class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body"
                dir="rtl">

                {{-- الخلفية المظللة --}}
                <div x-show="showStatusModal" x-transition.opacity.duration.300ms
                    class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                    @click="showStatusModal = false"></div>

                {{-- المودال نفسه --}}
                <div x-show="showStatusModal"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-[2rem] shadow-2xl p-8 text-center border border-gray-100 dark:border-boxdark-2">

                    {{-- الأيقونة --}}
                    <div
                        class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-[1.5rem] border border-amber-100 dark:border-amber-500/20">
                        <span class="text-4xl material-symbols-outlined">block</span>
                    </div>

                    <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">
                        تأكيد إلغاء الراكب</h3>

                    <p class="mb-4 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                        هل أنت متأكد من إلغاء الراكب رقم <br>
                        <span class="text-base font-black text-on-surface dark:text-white font-headline">
                            {{ $passenger->passenger_number }}
                        </span>؟<br>
                        <span class="text-amber-500/80">سيتم تغيير حالة الراكب إلى ملغي.</span>
                    </p>

                    @if ($passenger->broker)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 mb-4 text-xs font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-full border border-emerald-200 dark:border-emerald-500/20">
                            <span class="material-symbols-outlined text-[16px]">handshake</span>
                            <span>الوسيط:</span>
                            <span>{{ $passenger->broker->name }}</span>
                        </div>
                    @endif

                    {{-- فورم الإرسال --}}
                    <form action="{{ route('passengers.updateStatus', $passenger->id) }}" method="POST" @submit="isSubmitting = true"
                        class="space-y-4">
                        @csrf
                        <input type="hidden" name="status" value="cancel">

                        {{-- حقل رقم هاتف الوسيط (واتساب) --}}
                        <div class="text-right">
                            <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>رقم هاتف الوسيط (واتساب)</span>
                                <span class="text-xs font-normal text-gray-400">(لإرسال إشعار الإلغاء عبر الواتساب)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute right-3.5 top-3.5 material-symbols-outlined text-[18px] text-gray-400">call</span>
                                <input type="text" name="broker_phone" value="{{ $passenger->broker->phone ?? '' }}"
                                    placeholder="أدخل رقم الوسيط (مثال: 771234567)"
                                    dir="ltr"
                                    class="p-3.5 pr-10 w-full text-sm font-bold text-gray-700 bg-gray-50 rounded-2xl border border-gray-200 transition-all outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-body placeholder:text-gray-400 text-right">
                            </div>
                        </div>

                        <div class="text-right">
                            <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>سبب الإلغاء</span>
                                <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                            </label>
                            <textarea name="cancel_reason" rows="3"
                                placeholder="اكتب سبب إلغاء الحجز..."
                                class="p-3.5 w-full text-sm font-bold text-gray-700 bg-gray-50 rounded-2xl border border-gray-200 transition-all outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-body placeholder:text-gray-400"></textarea>
                        </div>

                        {{-- خيار إرسال واتساب للوسيط --}}
                        <div class="flex items-center gap-2.5 p-3 text-right bg-emerald-50/70 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20">
                            <input type="checkbox" id="send_whatsapp_cancel_show" name="send_whatsapp" value="1" 
                                checked
                                class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer">
                            <label for="send_whatsapp_cancel_show" class="text-xs font-bold text-gray-700 dark:text-gray-200 cursor-pointer select-none flex-1">
                                إرسال إشعار الإلغاء للوسيط عبر واتساب 💬
                            </label>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="showStatusModal = false"
                                class="flex-1 py-3.5 text-sm font-black rounded-xl transition-all text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                                تراجع
                            </button>

                            <button type="submit" :disabled="isSubmitting"
                                class="flex flex-1 gap-2 justify-center items-center py-3.5 text-sm font-black text-white bg-amber-500 rounded-xl shadow-lg transition-all hover:bg-amber-600 shadow-amber-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting">نعم، إلغاء الراكب</span>
                                <span x-show="isSubmitting"
                                    class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ====================== Delete Modal ====================== --}}
        <template x-teleport="body">
            <div x-show="showDeleteModal" x-cloak
                class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body"
                dir="rtl">

                {{-- الخلفية المظللة --}}
                <div x-show="showDeleteModal" x-transition.opacity.duration.300ms
                    class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                    @click="showDeleteModal = false"></div>

                {{-- المودال نفسه --}}
                <div x-show="showDeleteModal"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-[2rem] shadow-2xl p-8 text-center border border-gray-100 dark:border-boxdark-2">

                    {{-- الأيقونة --}}
                    <div
                        class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-[1.5rem] border border-rose-100 dark:border-rose-500/20">
                        <span class="text-4xl material-symbols-outlined">delete_forever</span>
                    </div>

                    <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">
                        تأكيد الحذف</h3>

                    <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                        هل أنت متأكد من حذف الراكب رقم <br>
                        <span class="text-base font-black text-on-surface dark:text-white font-headline">
                            {{ $passenger->passenger_number }}
                        </span>؟<br>
                        <span class="text-rose-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
                    </p>

                    {{-- فورم الإرسال --}}
                    <form action="{{ route('passengers.destroy', $passenger->id) }}" method="POST" @submit="isSubmitting = true"
                        class="flex gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-3.5 text-sm font-black rounded-xl transition-all text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                            تراجع
                        </button>

                        <button type="submit" :disabled="isSubmitting"
                            class="flex flex-1 gap-2 justify-center items-center py-3.5 text-sm font-black text-white bg-rose-500 rounded-xl shadow-lg transition-all hover:bg-rose-600 shadow-rose-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">نعم، احذف</span>
                            <span x-show="isSubmitting"
                                class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
            }

            a,
            button,
            aside,
            nav,
            header {
                display: none !important;
            }

            .bg-surface,
            .dark\:bg-boxdark-2,
            .dark\:bg-boxdark,
            .bg-white {
                background: white !important;
                border-color: #e5e7eb !important;
            }

            .text-on-surface,
            .dark\:text-white {
                color: #111827 !important;
            }

            .shadow-sm,
            .shadow-md,
            .shadow-lg {
                box-shadow: none !important;
            }

            .rounded-\[2rem\],
            .rounded-2xl {
                border-radius: 14px !important;
            }
        }
    </style>

    <script>
        async function sendPassengerBookingDirect(passengerId, btn, fallbackLink = '') {
            if (!passengerId || btn.disabled) return;

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                <span>جاري الإرسال...</span>
            `;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const url = "{{ route('whatsapp.passengerBooking', ':id') }}".replace(':id', passengerId);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                        <span class="text-emerald-600">تم الإرسال بنجاح ✅</span>
                    `;
                    showNotificationToast(data.message || 'تم إرسال تفاصيل الحجز للراكب بنجاح ✅', 'success');
                } else if (data.fallback && data.whatsapp_link) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">جاري فتح الواتساب...</span>
                    `;
                    showNotificationToast(data.message || 'خدمة الواتساب غير متصلة، جاري فتح الواتساب كرسالة...', 'warning');
                    window.open(data.whatsapp_link, '_blank');
                } else {
                    if (fallbackLink) {
                        window.open(fallbackLink, '_blank');
                    }
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">تم التحويل للواتساب</span>
                    `;
                    showNotificationToast(data.message || 'تم فتح الواتساب للإرسال اليدوي', 'warning');
                }
            } catch (error) {
                console.error('WhatsApp direct booking send error:', error);
                if (fallbackLink) {
                    window.open(fallbackLink, '_blank');
                    showNotificationToast('تعذر الاتصال بالسيرفر، تم فتح الواتساب للإرسال كرسالة.', 'warning');
                } else {
                    showNotificationToast('حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
                }
                btn.innerHTML = `
                    <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                    <span class="text-amber-500">تم التحويل للواتساب</span>
                `;
            } finally {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = originalHtml;
                }, 3000);
            }
        }

        async function sendPassengerCancelDirect(passengerId, btn, fallbackLink = '') {
            if (!passengerId || btn.disabled) return;

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                <span>جاري الإرسال...</span>
            `;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const url = "{{ route('whatsapp.passengerCancel', ':id') }}".replace(':id', passengerId);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                        <span class="text-emerald-600">تم الإرسال بنجاح ✅</span>
                    `;
                    showNotificationToast(data.message || 'تم إرسال إشعار الإلغاء للوسيط بنجاح ✅', 'success');
                } else if (data.fallback && data.whatsapp_link) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">جاري فتح الواتساب...</span>
                    `;
                    showNotificationToast(data.message || 'خدمة الواتساب غير متصلة، جاري فتح الواتساب كرسالة...', 'warning');
                    window.open(data.whatsapp_link, '_blank');
                } else {
                    if (fallbackLink) {
                        window.open(fallbackLink, '_blank');
                    }
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">تم التحويل للواتساب</span>
                    `;
                    showNotificationToast(data.message || 'تم فتح الواتساب للإرسال اليدوي', 'warning');
                }
            } catch (error) {
                console.error('WhatsApp direct cancel send error:', error);
                if (fallbackLink) {
                    window.open(fallbackLink, '_blank');
                    showNotificationToast('تعذر الاتصال بالسيرفر، تم فتح الواتساب للإرسال كرسالة.', 'warning');
                } else {
                    showNotificationToast('حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
                }
                btn.innerHTML = `
                    <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                    <span class="text-amber-500">تم التحويل للواتساب</span>
                `;
            } finally {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = originalHtml;
                }, 3000);
            }
        }

        function showNotificationToast(message, type = 'success') {
            const existingToast = document.getElementById('direct-whatsapp-toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.id = 'direct-whatsapp-toast';
            let bgClass = 'bg-emerald-600 shadow-emerald-600/25';
            let icon = 'check_circle';

            if (type === 'error') {
                bgClass = 'bg-red-600 shadow-red-600/25';
                icon = 'error';
            } else if (type === 'warning') {
                bgClass = 'bg-amber-600 shadow-amber-600/25';
                icon = 'open_in_new';
            }

            toast.className = `fixed bottom-6 left-6 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-white font-bold text-sm transition-all duration-300 transform translate-y-0 ${bgClass}`;

            toast.innerHTML = `
                <span class="material-symbols-outlined text-[20px]">${icon}</span>
                <span>${message}</span>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 400);
            }, 3500);
        }
    </script>
@endsection