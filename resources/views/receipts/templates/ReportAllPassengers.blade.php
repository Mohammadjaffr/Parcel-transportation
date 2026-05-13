@extends('receipts.layout')

@section('title', 'تقرير جميع الركاب')

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
                        <p class="text-slate-500 font-medium text-sm mt-1">{{ $title ?? 'تقرير جميع الركاب' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div
                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-sm mb-2 border border-indigo-100">
                        {{ $title ?? 'تقرير جميع الركاب' }}
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
                                $filterLabels = ['pending' => 'قيد الانتظار', 'completed' => 'مكتمل', 'cancel' => 'ملغي'];
                            @endphp
                            <span>الحالة: <strong>{{ $filterLabels[$status_filter] ?? $status_filter }}</strong></span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Passengers Table --}}
            <div class="mb-8">
                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-2 h-6 rounded-full bg-indigo-500"></span>
                    بيانات الركاب
                </h2>

                <div class="rounded-2xl border border-slate-200 overflow-x-auto">
                    <table class="w-full text-sm text-right premium-table">
                        <thead>
                            <tr>
                                <th class="w-10 text-center">#</th>
                                <th class="w-24">التاريخ</th>
                                <th>رقم الراكب</th>
                                <th>العميل</th>
                                <th>السائق</th>
                                <th class="w-28">المكان</th>
                                <th class="w-16 text-center">العدد</th>
                                <th class="w-24 text-center">العمولة</th>
                                <th class="w-24 text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($passengers ?? [] as $passenger)
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="font-bold text-slate-700" dir="ltr">{{ $passenger['date'] }}</td>
                                    <td class="font-bold text-slate-700" dir="ltr">{{ $passenger['passenger_number'] }}</td>
                                    <td>
                                        <div class="text-slate-800 font-bold">{{ $passenger['customer_name'] }}</div>
                                        <div class="text-xs text-slate-400" dir="ltr">{{ $passenger['customer_phone'] }}</div>
                                    </td>
                                    <td>
                                        <div class="text-slate-800 font-bold">{{ $passenger['driver_name'] }}</div>
                                        <div class="text-xs text-slate-400" dir="ltr">{{ $passenger['driver_phone'] }}</div>
                                    </td>
                                    <td class="text-slate-600 font-medium">{{ $passenger['location'] }}</td>
                                    <td class="text-center font-black text-slate-800">{{ $passenger['count'] }}</td>
                                    <td class="text-center font-black text-amber-600" dir="ltr">{{ $passenger['total_commission'] }}</td>
                                    <td class="text-center">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-50 text-amber-700',
                                                'completed' => 'bg-emerald-50 text-emerald-700',
                                                'cancel' => 'bg-rose-50 text-rose-700',
                                            ];
                                            $colorClass = $statusColors[$passenger['status_key']] ?? 'bg-slate-50 text-slate-700';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold {{ $colorClass }}">
                                            {{ $passenger['status_label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-8 text-center text-slate-400 font-medium bg-slate-50/50">
                                        لا توجد بيانات ركاب.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(!empty($passengers))
                        <div
                            class="bg-slate-50 border-t border-slate-200 p-4 grid grid-cols-2 sm:grid-cols-3 gap-4 text-center divide-x divide-x-reverse divide-slate-200">
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">عدد الركاب</p>
                                <p class="text-slate-800 font-black text-lg">{{ $total_passengers ?? 0 }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">إجمالي العدد</p>
                                <p class="text-slate-800 font-black text-lg">{{ number_format($total_count ?? 0, 0) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase mb-1">إجمالي العمولة</p>
                                <p class="text-amber-600 font-black text-lg" dir="ltr">{{ number_format($total_commission ?? 0, 0) }} ر.ي</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

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
                    لطلب النظام: <span dir="ltr" class="text-slate-400 font-mono">+967 780 261 952</span>
                </p>
            </div>
        </div>
    </div>
@endsection
