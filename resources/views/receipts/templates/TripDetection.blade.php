@extends('receipts.layout')

@section('title', $title)



@push('styles')
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 2mm;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Aggressively compress vertical spacing to fit 20 rows */
            .p-6, .sm\:p-8, .p-4, .p-8 { padding: 4px !important; }
            .py-4 { padding-top: 4px !important; padding-bottom: 4px !important; }
            .mb-10, .mb-8, .mb-6, .mb-4, .mb-3 { margin-bottom: 4px !important; }
            .mt-4, .mt-6 { margin-top: 4px !important; }
            .gap-4, .gap-3, .gap-2, .gap-1\.5 { gap: 2px !important; }
            
            table th, table td {
                padding-top: 1px !important;
                padding-bottom: 1px !important;
                font-size: 10px !important;
                line-height: 1 !important;
                border-width: 1px !important;
            }
            
            /* Shrink headers and logos */
            .text-3xl { font-size: 16px !important; line-height: 1.2 !important; }
            .w-28 { width: 50px !important; }
            .h-28 { height: 50px !important; }
            
            /* Shrink text */
            .text-xs { font-size: 9px !important; }
            .text-sm { font-size: 10px !important; }
            .text-xl { font-size: 14px !important; }
        }
    </style>
@endpush

@section('content')
@php
    $pages = [];
    foreach ($drivers ?? [] as $driver) {
        if (!empty($driver['passengers'])) {
            $chunks = collect($driver['passengers'])->chunk(20);
            foreach ($chunks as $chunk) {
                $pages[] = [
                    'driver' => $driver,
                    'passengers' => $chunk
                ];
            }
        }
    }
@endphp

@if(empty($pages))
    <div
        class="max-w-6xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">
        <div class="py-12 m-8 font-medium text-center rounded-2xl border border-slate-200 text-slate-400 bg-slate-50/50">
            لا توجد بيانات كشوفات ركاب للسائقين.
        </div>
    </div>
@else
    @php $globalPassengerIndex = 1; @endphp
    @foreach($pages as $pageIndex => $page)
        @php
            $driver = $page['driver'];
            $chunk = $page['passengers'];
        @endphp
    <div
        class="max-w-6xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        {{-- Header --}}
        {{-- الترويسة الرئيسية الأنيقة --}}
        <div class="relative px-6 py-4 border-b border-slate-200 bg-slate-50/50 print:bg-transparent">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500 opacity-[0.03] rounded-bl-full print:hidden"></div>

            <div class="flex relative z-10 justify-between items-center">

                {{-- يمين: بيانات الشركة --}}
                <div class="flex flex-col gap-1 w-1/3 min-w-0 ">
                    <h1 class="text-[14px] font-black tracking-tight leading-tight text-slate-900 truncate mb-0.5" title="{{ $company['name'] ?? 'شركة النقل' }}">
                        {{ $company['name'] ?? 'شركة النقل' }}
                    </h1>

                    <div class="flex flex-col gap-1 border-r-2 border-rose-500 pr-2 ">
                        @if (!empty($company['main_branch']))
                            <div>
                                <p class="text-[10px] font-bold text-slate-800 truncate" title="{{ $company['main_branch']['title'] }}">
                                    {{ $company['main_branch']['title'] }}
                                </p>
                                <p class="text-[9px] font-medium text-slate-600 truncate mt-0.5" dir="rtl">
                                    هاتف: <span class="font-sans font-black text-slate-900 ">{{ $company['main_branch']['phones'] }}</span>
                                </p>
                            </div>
                        @else
                            <div>
                                <p class="text-[10px] font-bold text-slate-800 truncate">
                                    الفرع: {{ $user_branch ?? 'المركز الرئيسي' }}
                                </p>
                                <p class="text-[9px] font-medium text-slate-600 truncate mt-0.5" dir="rtl">
                                    هاتف: <span class="font-sans font-black text-slate-900 ">{{ $company['main_branch']['phones'] ?? '---' }}</span>
                                </p>
                            </div>
                        @endif

                        @if (!empty($company['headquarters']) && ($company['main_branch']['title'] ?? '') !== $company['headquarters']['title'])
                            <div>
                                <p class="text-[9px] font-bold text-slate-600 truncate" title="{{ $company['headquarters']['title'] }}">
                                    {{ $company['headquarters']['title'] }}
                                </p>
                                <p class="text-[8px] font-medium text-slate-500 truncate mt-0.5" dir="rtl">
                                    هاتف: <span class="font-sans font-bold text-slate-800 ">{{ $company['headquarters']['phones'] }}</span>
                                </p>
                            </div>
                        @endif

                        @if (!empty($company['other_phones']))
                            <div>
                                <p class="text-[8px] font-bold text-slate-400">فروع أخرى:</p>
                                <p class="text-[7px] font-medium text-slate-500 line-clamp-2 leading-relaxed mt-0.5" dir="rtl">
                                    <span class="font-sans">{{ $company['other_phones'] }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- وسط: الشعار والبطاقة --}}
                <div class="flex flex-col justify-center items-center px-4 shrink-0">
                    @if (!empty($company['logo']))
                        <div
                            class="flex justify-center items-center mb-3 w-28 h-28 bg-white rounded-3xl border-2 shadow-lg shadow-slate-200/50 border-slate-100 print:w-20 print:h-20 print:shadow-none print:border-slate-200 print:mb-1">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-[80%] h-[80%]">
                        </div>
                    @endif
                    <div
                        class="inline-flex justify-center items-center px-6 py-1.5 text-sm font-black text-indigo-900 bg-indigo-100 rounded-full border border-indigo-200 shadow-sm print-badge">
                        {{ $title ?? 'استمارة رحلة' }}
                    </div>
                </div>

                {{-- يسار: بيانات الكشف --}}
                <div class="flex flex-col flex-1 gap-2 items-end text-left">
                    <div
                        class="p-3 w-4/5 bg-white rounded-xl border shadow-sm border-slate-200 print:w-full print:shadow-none print:bg-transparent print:p-0 print:border-none">
                        <div
                            class="flex justify-between items-center pb-1.5 mb-1.5 text-xs border-b border-dashed border-slate-200">
                            <span class="font-sans font-bold text-slate-800"
                                dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                            <span class="font-semibold text-slate-500">تاريخ الطباعة</span>
                        </div>
                        <div
                            class="flex justify-between items-center pb-1.5 mb-1.5 text-xs border-b border-dashed border-slate-200">
                            <span class="font-bold text-slate-800">{{ $total_passengers ?? 0 }}</span>
                            <span class="font-semibold text-slate-500">إجمالي الركاب</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-800">{{ count($drivers ?? []) }}</span>
                            <span class="font-semibold text-slate-500">عدد الرحلات</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            {{-- Filter Info --}}
            @if (!empty($date_from) || !empty($date_to) || (!empty($status_filter) && $status_filter !== 'all'))
                <div class="p-4 mb-6 rounded-2xl border border-indigo-100 bg-indigo-50/50">
                    <div class="flex gap-3 items-center mb-2">
                        <div class="flex justify-center items-center w-8 h-8 bg-indigo-100 rounded-lg">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-indigo-600">فلاتر التقرير</span>
                    </div>
                    <div class="flex flex-wrap gap-4 text-sm text-slate-600">
                        @if (!empty($date_from))
                            <span>من: <strong dir="ltr">{{ $date_from }}</strong></span>
                        @endif
                        @if (!empty($date_to))
                            <span>إلى: <strong dir="ltr">{{ $date_to }}</strong></span>
                        @endif
                        @if (!empty($status_filter) && $status_filter !== 'all')
                            @php
                                $filterLabels = [
                                    'pending' => 'قيد الانتظار',
                                    'confirmed' => 'مؤكد',
                                    'completed' => 'مكتمل',
                                    'cancel' => 'ملغي',
                                ];
                            @endphp
                            <span>الحالة: <strong>{{ $filterLabels[$status_filter] ?? $status_filter }}</strong></span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Grouped by Drivers --}}
            <div class="mb-10 page-break-inside-avoid">
                    {{-- Driver Card Header --}}
                    <div
                        class="flex flex-col gap-4 justify-between items-start p-4 rounded-t-2xl border border-b-0 sm:flex-row sm:items-center bg-slate-50 border-slate-200">
                        <div class="flex gap-3 items-center">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-white bg-indigo-500 rounded-xl shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800">السائق: {{ $driver['driver_name'] }}</h3>
                                <div class="flex gap-2 items-center mt-0.5">
                                    <span class="text-xs font-bold text-slate-500"
                                        dir="ltr">{{ $driver['driver_phone'] }}</span>

                                </div>
                            </div>
                        </div>
                        <div class="flex gap-4 text-xs font-bold text-slate-600">
                            <span class="px-3 py-1.5 bg-white rounded-lg border border-slate-200">الركاب:
                                <strong>{{ $driver['total_passengers_count'] }}</strong></span>
                            <span class="px-3 py-1.5 bg-white rounded-lg border border-slate-200">العدد الكلي:
                                <strong>{{ $driver['total_count'] }}</strong></span>

                        </div>
                    </div>

                    {{-- Passengers Table --}}
                    <div class="overflow-x-auto rounded-b-2xl border border-slate-200">
                        <table class="w-full text-sm text-right premium-table">
                            <thead>
                                <tr>
                                    <th class="w-10 text-center">#</th>
                                    <th class="w-28">التاريخ</th>

                                    <th>رقم الراكب (الهاتف)</th>

                                    @auth
                                        <th>الوسيط</th>
                                    @endauth
                                    <th class="w-28 text-right">مكان الراكب</th>
                                    <th class="w-28 text-right">الوجهة</th>
                                    <th class="w-12 text-center">العدد</th>

                                    <th class="w-32 text-right">الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($chunk as $passenger)
                                    <tr class="transition-colors hover:bg-slate-50">
                                        <td class="font-bold text-center text-slate-400">{{ $globalPassengerIndex++ }}</td>
                                        <td class="font-bold text-slate-700" dir="ltr">{{ $passenger['date'] }}</td>
                                        <td class="font-bold text-slate-700" dir="ltr">
                                            {{ $passenger['passenger_number'] }}</td>
                                        @auth
                                            <td>{{ $passenger['broker_name'] }}</td>
                                        @endauth
                                        <td class="text-xs font-bold text-slate-600">{{ $passenger['pickup_location'] }}
                                        </td>
                                        <td class="text-xs font-bold text-slate-600">{{ $passenger['destination'] }}</td>
                                        <td class="font-black text-center text-slate-800">{{ $passenger['count'] }}</td>

                                        <td class="max-w-xs text-xs font-medium truncate text-slate-500">
                                            {{ $passenger['note'] ?? '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- Grand Totals --}}
            @if (!empty($drivers) && $loop->last)
                <div class="overflow-hidden mb-8 rounded-2xl border border-slate-200">
                    <div
                        class="grid grid-cols-3 gap-4 p-4 text-center divide-x divide-x-reverse bg-slate-50 sm:grid-cols-3 divide-slate-200">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي الركاب </p>
                            <p class="text-lg font-black text-slate-800">{{ $total_passengers ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي الركاب الكلي</p>
                            <p class="text-lg font-black text-slate-800">{{ number_format($total_count ?? 0, 0) }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي العمولات</p>
                            <p class="text-lg font-black text-slate-800">
                                {{ number_format($totalOfficeCommissionAll ?? 0, 0) }} ر.ي</p>
                        </div>

                    </div>
                </div>
            @endif

        </div>

        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? date('Y-m-d h:i A') }}
            </p>
            <div class="pt-3 mt-3 border-t border-slate-700/50">
                <p class="text-[10px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr"
                        class="font-mono text-slate-400">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
    @if(!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif
    @endforeach
@endif
@endsection
