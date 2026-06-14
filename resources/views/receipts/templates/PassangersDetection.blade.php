@extends('receipts.layout')

@section('title', $title)



@push('styles')
    <style>
        @media print {
            @page {
                size: A4 landscape;
            }
        }
    </style>
@endpush

@section('content')
    <div
        class="max-w-6xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        {{-- Header --}}
        <div class="p-6 bg-gradient-to-l to-white border-b from-indigo-50/50 sm:p-8 border-slate-100">
            <div class="flex flex-col gap-6 justify-between items-start sm:flex-row sm:items-center">
                <div class="flex gap-4 items-center">
                    @if(!empty($company['logo']))
                        <div
                            class="flex justify-center items-center p-2 w-16 h-16 bg-white rounded-2xl border shadow-sm border-slate-100">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-full h-full">
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-slate-800">{{ $company['name'] ?? 'شركة مرسال' }}</h1>
                        {{-- <p class="mt-1 text-sm font-medium text-slate-500">{{ $title ?? "كشف الراكب {$driver_name}" }}</p> --}}
                    </div>
                </div>
                <div class="text-right">
                    <div
                        class="inline-flex justify-center items-center px-4 py-2 mb-2 text-sm font-bold text-indigo-700 bg-indigo-50 rounded-xl border border-indigo-100">
                        {{ $title }}
                    </div>
                    <div class="flex gap-1.5 justify-end items-center mt-1 text-xs font-medium text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            {{-- Filter Info --}}
            @if(!empty($date_from) || !empty($date_to) || (!empty($status_filter) && $status_filter !== 'all'))
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
                        @if(!empty($date_from))
                            <span>من: <strong dir="ltr">{{ $date_from }}</strong></span>
                        @endif
                        @if(!empty($date_to))
                            <span>إلى: <strong dir="ltr">{{ $date_to }}</strong></span>
                        @endif
                        @if(!empty($status_filter) && $status_filter !== 'all')
                            @php
                                $filterLabels = ['pending' => 'قيد الانتظار', 'confirmed' => 'مؤكد', 'completed' => 'مكتمل', 'cancel' => 'ملغي'];
                            @endphp
                            <span>الحالة: <strong>{{ $filterLabels[$status_filter] ?? $status_filter }}</strong></span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Grouped by Drivers --}}
            @forelse($drivers ?? [] as $driver)
                <div class="mb-10 page-break-inside-avoid">
                    {{-- Driver Card Header --}}
                    {{-- <div class="flex flex-col gap-4 justify-between items-start p-4 rounded-t-2xl border border-b-0 sm:flex-row sm:items-center bg-slate-50 border-slate-200">
                        <div class="flex gap-3 items-center">
                            <div class="flex justify-center items-center w-10 h-10 text-white bg-indigo-500 rounded-xl shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800">السائق: {{ $driver['driver_name'] }}</h3>
                                <div class="flex gap-2 items-center mt-0.5">
                                    <span class="text-xs font-bold text-slate-500" dir="ltr">{{ $driver['driver_phone'] }}</span>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-4 text-xs font-bold text-slate-600">
                            <span class="px-3 py-1.5 bg-white rounded-lg border border-slate-200">الركاب: <strong>{{ $driver['total_passengers_count'] }}</strong></span>
                            <span class="px-3 py-1.5 bg-white rounded-lg border border-slate-200">العدد الكلي: <strong>{{ $driver['total_count'] }}</strong></span>
                            <span class="px-3 py-1.5 text-emerald-700 bg-emerald-50 rounded-lg border border-emerald-100">عمولة المكتب: <strong>{{ number_format($driver['total_office_commission'], 0) }}</strong></span>
                            <span class="px-3 py-1.5 text-amber-700 bg-amber-50 rounded-lg border border-amber-100">عمولة أخرى: <strong>{{ number_format($driver['total_other_office_commission'], 0) }}</strong></span>
                        </div>
                    </div> --}}

                    {{-- Passengers Table --}}
                    <div class="overflow-x-auto rounded-b-2xl border border-slate-200">
                        <table class="w-full text-sm text-right premium-table">
                            <thead>
                                <tr>
                                    <th class="w-10 text-center">#</th>
                                    <th class="w-28">التاريخ</th>
                                    
                                    <th>رقم الراكب (الهاتف)</th>
                                    <th>الوسيط</th>
                                    <th class="w-32">المكان</th>
                                    <th class="w-16 text-center">العدد</th>
                                    <th class="w-24 text-center">عمولة المكتب</th>
                                    <th class="w-24 text-center">عمولة أخرى</th>
                                    <th class="w-24 text-center">الحالة</th>
                                    <th>الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($driver['passengers'] as $passenger)
                                    <tr class="transition-colors hover:bg-slate-50">
                                        <td class="font-bold text-center text-slate-400">{{ $loop->iteration }}</td>
                                        <td class="font-bold text-slate-700" dir="ltr">{{ $passenger['date'] }}</td>
                                        <td class="font-bold text-slate-700" dir="ltr">{{ $passenger['passenger_number'] }}</td>
                                        <td class="font-bold text-slate-800">{{ $passenger['broker_name'] }}</td>
                                        <td class="font-medium text-slate-600">{{ $passenger['pickup_location'] }}</td>
                                        <td class="font-black text-center text-slate-800">{{ $passenger['count'] }}</td>
                                        <td class="font-black text-center text-emerald-600" dir="ltr">{{ $passenger['office_commission'] }}</td>
                                        <td class="font-black text-center text-amber-600" dir="ltr">{{ $passenger['other_office_commission'] }}</td>
                                        <td class="text-center">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'bg-amber-50 text-amber-700',
                                                    'confirmed' => 'bg-blue-50 text-blue-700',
                                                    'completed' => 'bg-emerald-50 text-emerald-700',
                                                    'cancel' => 'bg-rose-50 text-rose-700',
                                                ];
                                                $colorClass = $statusColors[$passenger['status_key']] ?? 'bg-slate-50 text-slate-700';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold {{ $colorClass }}">
                                                {{ $passenger['status_label'] }}
                                            </span>
                                        </td>
                                        <td class="max-w-xs text-xs font-medium truncate text-slate-500">{{ $passenger['note'] ?? '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-12 font-medium text-center rounded-2xl border border-slate-200 text-slate-400 bg-slate-50/50">
                    لا توجد بيانات كشوفات ركاب للسائقين.
                </div>
            @endforelse

            {{-- Grand Totals --}}
            @if(!empty($drivers))
                <div class="overflow-hidden mb-8 rounded-2xl border border-slate-200">
                    <div class="grid grid-cols-2 gap-4 p-4 text-center divide-x divide-x-reverse bg-slate-50 sm:grid-cols-4 divide-slate-200">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي الركاب </p>
                            <p class="text-lg font-black text-slate-800">{{ $total_passengers ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي الركاب الكلي</p>
                            <p class="text-lg font-black text-slate-800">{{ number_format($total_count ?? 0, 0) }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي عمولة المكتب</p>
                            <p class="text-lg font-black text-emerald-600" dir="ltr">{{ number_format($total_office_commission ?? 0, 0) }} ر.ي</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي عمولات أخرى</p>
                            <p class="text-lg font-black text-amber-600" dir="ltr">{{ number_format($total_other_office_commission ?? 0, 0) }} ر.ي</p>
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
                    لطلب النظام: <span dir="ltr" class="font-mono text-slate-400">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection
