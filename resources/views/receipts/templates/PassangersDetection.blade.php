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
        <div class="bg-gradient-to-l from-indigo-50/50 to-white p-6 sm:p-8 border-b border-slate-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                <div class="flex items-center gap-4">
                    @if(!empty($company['logo']))
                        <div
                            class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center p-2 border border-slate-100">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $company['name'] ?? 'شركة مرسال' }}</h1>
                        <p class="text-slate-500 font-medium text-sm mt-1">{{ $title ?? "كشف الراكب {$driver_name}" }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-sm mb-2 border border-indigo-100">
                        {{ $title }}
                    </div>
                    <div class="text-slate-400 text-xs font-medium flex items-center gap-1.5 justify-end mt-1">
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
                <div class="bg-indigo-50/50 rounded-2xl border border-indigo-100 p-4 mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
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
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-slate-50 border border-slate-200 rounded-t-2xl gap-4 border-b-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-800 text-base">السائق: {{ $driver['driver_name'] }}</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-slate-500 font-bold" dir="ltr">{{ $driver['driver_phone'] }}</span>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-4 text-xs font-bold text-slate-600">
                            <span class="bg-white px-3 py-1.5 rounded-lg border border-slate-200">الركاب: <strong>{{ $driver['total_passengers_count'] }}</strong></span>
                            <span class="bg-white px-3 py-1.5 rounded-lg border border-slate-200">العدد الكلي: <strong>{{ $driver['total_count'] }}</strong></span>
                            <span class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg border border-emerald-100">عمولة المكتب: <strong>{{ number_format($driver['total_office_commission'], 0) }}</strong></span>
                            <span class="bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg border border-amber-100">عمولة أخرى: <strong>{{ number_format($driver['total_other_office_commission'], 0) }}</strong></span>
                        </div>
                    </div>

                    {{-- Passengers Table --}}
                    <div class="border border-slate-200 rounded-b-2xl overflow-x-auto">
                        <table class="w-full text-sm text-right premium-table">
                            <thead>
                                <tr>
                                    <th class="w-10 text-center">#</th>
                                    <th class="w-24">التاريخ</th>
                                    
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
                                        <td class="text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                                        <td class="font-bold text-slate-700" dir="ltr">{{ $passenger['date'] }}</td>
                                        <td class="font-bold text-slate-700" dir="ltr">{{ $passenger['passenger_number'] }}</td>
                                        <td class="font-bold text-slate-800">{{ $passenger['broker_name'] }}</td>
                                        <td class="text-slate-600 font-medium">{{ $passenger['location'] }}</td>
                                        <td class="text-center font-black text-slate-800">{{ $passenger['count'] }}</td>
                                        <td class="text-center font-black text-emerald-600" dir="ltr">{{ $passenger['office_commission'] }}</td>
                                        <td class="text-center font-black text-amber-600" dir="ltr">{{ $passenger['other_office_commission'] }}</td>
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
                                        <td class="text-xs text-slate-500 font-medium max-w-xs truncate">{{ $passenger['note'] ?? '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-12 border border-slate-200 rounded-2xl text-center text-slate-400 font-medium bg-slate-50/50">
                    لا توجد بيانات كشوفات ركاب للسائقين.
                </div>
            @endforelse

            {{-- Grand Totals --}}
            @if(!empty($drivers))
                <div class="rounded-2xl border border-slate-200 overflow-hidden mb-8">
                    <div class="bg-slate-50 p-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center divide-x divide-x-reverse divide-slate-200">
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1">إجمالي الركاب </p>
                            <p class="text-slate-800 font-black text-lg">{{ $total_passengers ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1">إجمالي الركاب الكلي</p>
                            <p class="text-slate-800 font-black text-lg">{{ number_format($total_count ?? 0, 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1">إجمالي عمولة المكتب</p>
                            <p class="text-emerald-600 font-black text-lg" dir="ltr">{{ number_format($total_office_commission ?? 0, 0) }} ر.ي</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1">إجمالي عمولات أخرى</p>
                            <p class="text-amber-600 font-black text-lg" dir="ltr">{{ number_format($total_other_office_commission ?? 0, 0) }} ر.ي</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Signatures --}}
            <div class="grid grid-cols-2 gap-8 mt-12 mb-4">
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 mb-8">توقيع مسؤول الفرع</p>
                    <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 mb-8">توقيع المدير</p>
                    <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? date('Y-m-d h:i A') }}
            </p>
            <div class="mt-3 pt-3 border-t border-slate-700/50">
                <p class="text-[10px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr" class="text-slate-400 font-mono">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection
