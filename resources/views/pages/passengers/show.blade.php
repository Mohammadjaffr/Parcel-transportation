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

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl">

        {{-- ================= Header ================= --}}
        {{-- <div class="mx-auto w-full max-w-7xl">
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

                            <span
                                class="inline-flex px-3 py-1.5 text-[10px] font-black text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                مكتمل
                            </span>
                        </div>

                        <h1 class="text-2xl font-black tracking-tight md:text-3xl text-on-surface dark:text-white">
                            الراكب
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
                <a href="{{ route('receipt.generate', ['type' => 'passenger', 'id' => $passenger->id]) }}" target="_blank"
                    class="flex gap-3 items-center px-4 py-2.5  border-emerald-100 border-2 rounded-md text-sm font-bold text-green-500 transition-colors hover:bg-emerald-50 hover:text-emerald-500 dark:text-gray-300 dark:hover:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    طباعة الكشف
                </a>
            </div>
        </div> --}}

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

@endsection