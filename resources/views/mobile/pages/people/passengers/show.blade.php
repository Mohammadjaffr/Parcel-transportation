@extends('mobile.layouts.app')

@section('title', 'تفاصيل الراكب')

@section('content')

@php
    $passengerDate = $passenger->date ? \Carbon\Carbon::parse($passenger->date) : null;
    $dayName = $passengerDate ? $passengerDate->translatedFormat('l') : 'غير محدد';

    $customerName = $passenger->customer->name ?? 'راكب غير محدد';
    $driverName = $passenger->driver->name ?? 'سائق غير محدد';
    $branchName = $passenger->branch->name ?? 'غير محدد';

    $commission = (float) ($passenger->total_commission ?? 0);

    $customerInitial = mb_substr($customerName, 0, 1, 'UTF-8');
    $driverInitial = mb_substr($driverName, 0, 1, 'UTF-8');

    // تحديد الحالة الديناميكية
    $rawStatus = strtolower($passenger->status ?? 'pending');
    if ($rawStatus == 'completed' || $rawStatus == 'مكتمل') {
        $statusLabel = 'مكتمل'; $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20'; $statusIcon = 'task_alt';
    } elseif ($rawStatus == 'cancel' || $rawStatus == 'ملغي') {
        $statusLabel = 'ملغي'; $statusClass = 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20'; $statusIcon = 'cancel';
    } else {
        $statusLabel = 'قيد الانتظار'; $statusClass = 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'; $statusIcon = 'schedule';
    }
@endphp

<div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black font-headline" dir="rtl">

    {{-- ================= الهيدر وزر الرجوع ================= --}}
    <div class="flex justify-between items-center px-4 mb-6">
        <div class="flex gap-3 items-center">
            <a href="{{ route('passengers.index') }}" class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90">
                <span class="material-symbols-outlined text-[20px] mr-1 rtl:rotate-180">arrow_forward_ios</span>
            </a>
            <h1 class="text-lg font-black text-slate-800 dark:text-white">تفاصيل الراكب</h1>
        </div>
        <button type="button" onclick="window.print()" class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90 print:hidden">
            <span class="material-symbols-outlined text-[20px]">print</span>
        </button>
    </div>

    <div class="px-4 space-y-4 w-full">

        {{-- ================= البطاقة الرئيسية (العميل والحالة) ================= --}}
        <div class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden dark:bg-boxdark dark:border-boxdark-2">
            <div class="flex justify-between items-start mb-4">
                <div class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black rounded-xl border shadow-sm {{ $statusClass }}">
                    <span class="material-symbols-outlined text-[15px]">{{ $statusIcon }}</span>
                    {{ $statusLabel }}
                </div>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2.5 py-1.5 rounded-lg dark:bg-boxdark-2">
                    #{{ $passenger->id }}
                </span>
            </div>

            <div class="flex gap-4 items-center">
                <div class="flex justify-center items-center w-14 h-14 text-2xl font-black bg-primary/10 rounded-[1.25rem] border shadow-inner text-primary border-primary/5 shrink-0">
                    {{ $customerInitial ?: 'ع' }}
                </div>
                <div class="min-w-0">
                    <span class="block mb-1 text-[10px] font-black text-slate-400 dark:text-gray-500">العميل / الراكب</span>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white truncate">
                        {{ $customerName }}
                    </h2>
                    <x-phone-number :value="$passenger->passenger_number" class="mt-0.5 text-xs font-bold font-mono text-slate-500 dir-ltr inline-block dark:text-bodydark" />
                </div>
            </div>
        </div>

        {{-- ================= شبكة الإحصائيات (2x2) ================= --}}
        <div class="grid grid-cols-2 gap-3">
            {{-- التاريخ --}}
            <div class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex justify-center items-center mb-2 w-10 h-10 text-blue-500 bg-blue-50 rounded-full dark:bg-blue-500/10">
                    <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                </div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">التاريخ</span>
                <span class="text-xs font-black text-slate-800 dark:text-white">{{ $passengerDate ? $passengerDate->format('Y-m-d') : 'غير محدد' }}</span>
                <span class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $dayName }}</span>
            </div>

            {{-- المكان --}}
            <div class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex justify-center items-center mb-2 w-10 h-10 text-rose-500 bg-rose-50 rounded-full dark:bg-rose-500/10">
                    <span class="material-symbols-outlined text-[20px]">location_on</span>
                </div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">المكان</span>
                <span class="text-sm font-black text-slate-800 dark:text-white truncate w-full">{{ $passenger->location ?? 'غير محدد' }}</span>
            </div>

            {{-- العدد --}}
            <div class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex justify-center items-center mb-2 w-10 h-10 text-emerald-500 bg-emerald-50 rounded-full dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[20px]">groups</span>
                </div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">عدد الركاب</span>
                <span class="text-sm font-black text-slate-800 dark:text-white">{{ number_format($passenger->count ?? 0, 0) }}</span>
            </div>

            {{-- العمولة --}}
            <div class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center relative overflow-hidden dark:bg-boxdark dark:border-boxdark-2">
                <div class="absolute inset-x-0 bottom-0 h-1 bg-amber-500"></div>
                <div class="flex justify-center items-center mb-2 w-10 h-10 text-amber-500 bg-amber-50 rounded-full dark:bg-amber-500/10">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                </div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">العمولة</span>
                <span class="text-sm font-black text-amber-500">{{ number_format($commission, 0) }} <span class="text-[10px]">ر.ي</span></span>
            </div>
        </div>

        {{-- ================= بيانات السائق ================= --}}
        <div class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
            <h3 class="flex items-center gap-2 text-sm font-black text-slate-800 mb-4 dark:text-white">
                <span class="material-symbols-outlined text-primary text-[20px]">local_taxi</span>
                بيانات السائق
            </h3>
            
            <div class="flex gap-4 items-center p-4 rounded-2xl border border-primary/15 bg-primary-container/40 dark:bg-primary/10 dark:border-primary/20">
                <div class="flex justify-center items-center w-12 h-12 text-lg font-black text-white rounded-[1rem] shadow-inner bg-primary shrink-0">
                    {{ $driverInitial ?: 'س' }}
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-black truncate text-slate-800 dark:text-white">
                        {{ $driverName }}
                    </h3>
                    @if($passenger->driver?->phone)
                        <x-phone-number :value="$passenger->driver->phone" class="mt-0.5 text-xs font-bold text-slate-500 font-mono dir-ltr inline-block dark:text-bodydark" />
                    @else
                        <span class="text-[10px] font-bold text-slate-400">لا يوجد رقم</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= الملاحظات ================= --}}
        @if($passenger->note)
        <div class="p-5 bg-amber-50/50 rounded-[2rem] border border-amber-100/50 shadow-sm dark:bg-amber-500/5 dark:border-amber-500/10">
            <h3 class="flex items-center gap-2 text-sm font-black text-amber-600 mb-3">
                <span class="material-symbols-outlined text-[20px]">notes</span>
                الملاحظات
            </h3>
            <p class="text-xs font-semibold leading-relaxed text-slate-600 dark:text-gray-300">
                {{ $passenger->note }}
            </p>
        </div>
        @endif

        {{-- ================= تفاصيل السجل ================= --}}
        {{-- <div class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-boxdark">
                    <span class="flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-gray-400">
                        <span class="material-symbols-outlined text-[16px]">store</span> الفرع
                    </span>
                    <span class="text-xs font-black text-slate-800 dark:text-white">{{ $branchName }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-slate-50 dark:border-boxdark">
                    <span class="flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-gray-400">
                        <span class="material-symbols-outlined text-[16px]">add_circle</span> تاريخ الإضافة
                    </span>
                    <span class="text-xs font-black text-slate-800 dark:text-white font-mono dir-ltr">{{ $passenger->created_at ? $passenger->created_at->format('Y-m-d h:i A') : 'غير متوفر' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-1.5 text-xs font-bold text-slate-500 dark:text-gray-400">
                        <span class="material-symbols-outlined text-[16px]">update</span> آخر تحديث
                    </span>
                    <span class="text-xs font-black text-slate-800 dark:text-white font-mono dir-ltr">{{ $passenger->updated_at ? $passenger->updated_at->format('Y-m-d h:i A') : 'غير متوفر' }}</span>
                </div>
            </div>
        </div> --}}

    </div>
</div>

<style>
    @media print {
        body { background: white !important; }
        a, button, aside, nav, header { display: none !important; }
        .bg-surface, .dark\:bg-boxdark-2, .dark\:bg-boxdark, .bg-white {
            background: white !important;
            border-color: #e5e7eb !important;
        }
        .text-on-surface, .dark\:text-white { color: #111827 !important; }
        .shadow-sm, .shadow-md, .shadow-lg { box-shadow: none !important; }
        .rounded-\[2rem\], .rounded-2xl, .rounded-\[1\.5rem\] { border-radius: 14px !important; }
    }
</style>

@endsection