@extends('mobile.layouts.app')

@section('title', 'تفاصيل الراكب')

@section('content')

    @php
        $passengerDate = $passenger->date ? \Carbon\Carbon::parse($passenger->date) : null;
        $dayName = $passengerDate ? $passengerDate->translatedFormat('l') : 'غير محدد';

        $driverName = $passenger->driver->name ?? 'لم يحدد بعد';
        $branchName = $passenger->branch->name ?? 'غير محدد';
        $driverInitial = $passenger->driver ? mb_substr($driverName, 0, 1, 'UTF-8') : 'س';
        $brokerName = $passenger->broker?->name ?? 'وسيط غير محدد';
        $brokerInitial = mb_substr($brokerName, 0, 1, 'UTF-8');

        // تحديد الحالة الديناميكية
        $rawStatus = strtolower($passenger->status ?? 'pending');
        if ($rawStatus == 'completed' || $rawStatus == 'مكتمل') {
            $statusLabel = 'مكتمل';
            $statusClass =
                'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
            $statusIcon = 'task_alt';
        } elseif ($rawStatus == 'cancel' || $rawStatus == 'ملغي') {
            $statusLabel = 'ملغي';
            $statusClass =
                'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
            $statusIcon = 'cancel';
        } else {
            $statusLabel = 'قيد التجهيز';
            $statusClass =
                'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
            $statusIcon = 'schedule';
        }
    @endphp

    <div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black font-headline" dir="rtl">

        {{-- ================= الهيدر وزر الرجوع ================= --}}
        <div class="flex justify-between items-center px-4 mb-5">
            <div class="flex gap-3 items-center">
                <a href="{{ route('passengers.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
                </a>
                <a href="{{ route('receipt.generate', ['type' => 'passenger', 'id' => $passenger->id]) }}" target="_blank"
                    class="flex gap-3 items-center px-2 py-1 text-md font-bold   text-green-500 transition-colors hover:bg-emerald-50 hover:text-emerald-500 dark:text-gray-300 dark:hover:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                </a>
                <h1 class="text-lg font-black text-slate-800 dark:text-white">تفاصيل الراكب</h1>
            </div>

        </div>

        <div class="px-4 space-y-4 w-full">

            {{-- ================= 1️⃣ البطاقة الرئيسية (رقم السند والهاتف) ================= --}}
            <div
                class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex justify-between items-start mb-4">
                    <div
                        class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black rounded-xl border shadow-sm {{ $statusClass }}">
                        <span class="material-symbols-outlined text-[15px]">{{ $statusIcon }}</span>
                        {{ $statusLabel }}
                    </div>
                    <span
                        class="text-[10px] font-black font-mono text-slate-400 bg-slate-50 px-3 py-1.5 rounded-xl dark:bg-boxdark-2 dark:text-slate-500">
                        #{{ $passenger->id }}
                    </span>
                </div>

                <div class="flex gap-4 items-center">
                    <div
                        class="flex justify-center items-center w-14 h-14 text-2xl font-black bg-primary/10 rounded-[1.25rem] border shadow-inner text-primary border-primary/5 shrink-0 dark:bg-primary/20">
                        ر
                    </div>
                    <div class="min-w-0">
                        <span class="block mb-0.5 text-[10px] font-bold text-slate-400 dark:text-gray-500">رقم هاتف
                            الراكب</span>
                        <div class="inline-block px-3 py-1 bg-slate-50 dark:bg-boxdark-2 rounded-xl">
                            <x-phone-number :value="$passenger->passenger_number"
                                class="text-sm font-black font-mono text-slate-700 dir-ltr inline-block dark:text-white" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= 2️⃣ خط السير والمسار (Timeline ستايل) ================= --}}
            @if (!empty($passenger->destination) || !empty($passenger->pickup_location))
                <div
                    class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                    <h3
                        class="flex items-center gap-2 text-xs font-black text-slate-400 mb-4 dark:text-gray-500 uppercase tracking-wider">
                        <span class="material-symbols-outlined text-[18px]">alt_route</span> خط سير الرحلة
                    </h3>

                    <div
                        class="relative pr-6 border-r-2 border-dashed border-slate-200 dark:border-slate-800 space-y-4 my-2">
                        <div class="relative">
                            <span
                                class="absolute right-[-29px] top-1 w-2.5 h-2.5 rounded-full bg-slate-300 border-2 border-white ring-4 ring-slate-100 dark:border-boxdark dark:ring-slate-900"></span>
                            <div class="text-xs">
                                <span class="text-slate-400 font-bold block text-[10px] mb-0.5 dark:text-gray-500">مكان
                                    الصعود / الانطلاق:</span>
                                <span
                                    class="text-sm font-black text-slate-700 dark:text-white">{{ $passenger->pickup_location ?? ($passenger->location ?? 'غير محدد') }}</span>
                            </div>
                        </div>

                        <div class="relative">
                            <span
                                class="absolute right-[-29px] top-1 w-2.5 h-2.5 rounded-full bg-amber-500 border-2 border-white ring-4 ring-amber-100 dark:border-boxdark dark:ring-amber-500/10"></span>
                            <div class="text-xs">
                                <span class="text-amber-500 font-bold block text-[10px] mb-0.5">الوجهة المقصودة:</span>
                                <span
                                    class="text-sm font-black text-amber-600 dark:text-amber-400">{{ $passenger->destination ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ================= 3️⃣ معلومات الوقت والعدد والفرع ================= --}}
            <div class="grid grid-cols-2 gap-3">
                {{-- التاريخ اليومي --}}
                <div
                    class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center dark:bg-boxdark dark:border-boxdark-2">
                    <div
                        class="flex justify-center items-center mb-2 w-9 h-9 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">تاريخ الرحلة</span>
                    <span
                        class="text-xs font-black text-slate-800 dark:text-white font-mono">{{ $passengerDate ? $passengerDate->format('Y-m-d') : 'غير محدد' }}</span>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mt-0.5">{{ $dayName }}</span>
                </div>

                {{-- عدد الركاب --}}
                <div
                    class="p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center dark:bg-boxdark dark:border-boxdark-2">
                    <div
                        class="flex justify-center items-center mb-2 w-9 h-9 text-purple-500 bg-purple-50 rounded-xl dark:bg-purple-500/10">
                        <span class="material-symbols-outlined text-[18px]">groups</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">إجمالي الركاب</span>
                    <span
                        class="text-sm font-black text-slate-800 dark:text-white">{{ number_format($passenger->count ?? 1, 0) }}
                        ركاب</span>
                </div>
            </div>

            {{-- ================= 4️⃣ القسم المالي (العمولات وعمولة المكتب) ================= --}}
            <div
                class="p-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 relative overflow-hidden">
                <h3
                    class="flex items-center gap-2 text-xs font-black text-slate-400 mb-4 dark:text-gray-500 uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[18px]">payments</span> البيانات المالية للطلب
                </h3>

                <div class="grid grid-cols-2 gap-3">
                    <div
                        class="p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100 dark:bg-emerald-500/5 dark:border-emerald-500/10">
                        <span class="block text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-1">عمولة المكتب</span>
                        <span class="text-base font-black text-emerald-600 dark:text-emerald-400">
                            {{ number_format((float) ($passenger->office_commission ?? 0), 0) }} <span
                                class="text-[10px] font-bold">ر.ي</span>
                        </span>
                    </div>

                    <div
                        class="p-3 rounded-2xl bg-amber-50/60 border border-amber-100 dark:bg-amber-500/5 dark:border-amber-500/10">
                        <span class="block text-[10px] font-bold text-slate-400 dark:text-gray-500 mb-1">عمولة مكاتب
                            أخرى</span>
                        <span class="text-base font-black text-amber-500 dark:text-amber-400">
                            {{ number_format((float) ($passenger->other_office_commission ?? 0), 0) }} <span
                                class="text-[10px] font-bold">ر.ي</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- ================= 5️⃣ جهات التوصيل (السائق والوسيط بالتوازي في صف واحد) ================= --}}
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="p-4 bg-white rounded-[1.75rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 flex flex-col justify-between">
                    <div>
                        <span
                            class="block text-[9px] font-bold text-slate-400 dark:text-gray-500 mb-2.5 uppercase tracking-wider">الكابتن
                            المسؤول</span>
                        <div class="flex gap-2 items-center min-w-0">
                            <div
                                class="flex justify-center items-center w-8 h-8 text-[11px] font-black text-white rounded-xl shadow-sm bg-primary shrink-0">
                                {{ $driverInitial }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-black truncate text-slate-800 dark:text-white">
                                    {{ $driverName }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-3 pt-2 border-t border-slate-50 dark:border-boxdark-2 flex items-center justify-between gap-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            @if ($passenger->driver?->phone)
                                <x-phone-number :value="$passenger->driver->phone"
                                    class="text-[10px] font-bold text-slate-400 font-mono dir-ltr inline-block truncate w-full dark:text-bodydark" />
                            @else
                                <span class="text-[9px] font-bold text-slate-300 dark:text-gray-600 truncate block">لا يوجد
                                    رقم</span>
                            @endif
                        </div>
                        @if ($passenger->driver?->phone)
                            <a href="https://wa.me/{{ $passenger->driver->phone }}" target="_blank"
                                class="flex justify-center items-center w-6 h-6 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 shrink-0">
                                <span class="material-symbols-outlined text-[13px]">chat</span>
                            </a>
                        @endif
                        </span>
                    </div>

                    <div
                        class="p-4 bg-white rounded-[1.75rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 flex flex-col justify-start">
                        <span
                            class="block text-[9px] font-bold text-slate-400 dark:text-gray-500 mb-2.5 uppercase tracking-wider">الوسيط
                            المعتمد</span>
                        <div class="flex gap-2 items-center min-w-0">
                            <div
                                class="flex justify-center items-center w-8 h-8 text-[11px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-xl shrink-0">
                                {{ $brokerInitial }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-black truncate text-slate-800 dark:text-white"
                                    title="{{ $brokerName }}">
                                    {{ $brokerName }}
                                </h4>
                                <span class="block text-[9px] font-bold text-slate-300 dark:text-gray-600 mt-0.5">موجّه
                                    السند</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= 6️⃣ الملاحظات السند المكتوبة ================= --}}
                @if ($passenger->note)
                    <div
                        class="p-5 bg-amber-50/40 rounded-[2rem] border border-amber-100/60 shadow-sm dark:bg-amber-500/5 dark:border-amber-500/10">
                        <h3 class="flex items-center gap-1.5 text-xs font-black text-amber-600 mb-2.5">
                            <span class="material-symbols-outlined text-[18px]">description</span>
                            ملاحظات السند الحالية
                        </h3>
                        <p class="text-xs font-medium leading-relaxed text-slate-600 dark:text-gray-300 pr-1">
                            {{ $passenger->note }}
                        </p>
                    </div>
                @endif

            </div>
        </div>

        <style>
            @media print {
                body {
                    background: white !important;
                }

                a,
                button,
                alert,
                nav,
                header {
                    display: none !important;
                }

                .bg-white,
                .dark\:bg-boxdark,
                .bg-slate-50\/50 {
                    background: white !important;
                    border-color: #f1f5f9 !important;
                }

                .text-slate-800,
                .dark\:text-white {
                    color: #1e293b !important;
                }

                .shadow-sm {
                    box-shadow: none !important;
                }
            }
        </style>

    @endsection
