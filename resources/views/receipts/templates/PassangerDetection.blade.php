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

        /* كلاسات مخصصة لكسر النصوص الطويلة داخل الجدول */
        .text-wrap-custom {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            line-height: 1.6;
        }
    </style>
@endpush

@section('content')
@php
    $allPassengers = collect();
    foreach ($drivers ?? [] as $driver) {
        if (!empty($driver['passengers'])) {
            foreach ($driver['passengers'] as $passenger) {
                $allPassengers->push($passenger);
            }
        }
    }
    $chunks = $allPassengers->chunk(20);
@endphp

@if($chunks->isEmpty())
    <div dir="rtl" class="w-full bg-white overflow-hidden print-no-shadow print:border-none print:my-0 print:rounded-none {{ !empty($is_pdf) ? 'max-w-none my-0 rounded-none shadow-none border-none' : 'max-w-7xl mx-auto sm:rounded-[1.5rem] shadow-2xl shadow-indigo-100/50 border border-slate-200 my-8' }}">
        <div class="py-12 m-8 font-medium text-center rounded-2xl border border-slate-200 text-slate-400 bg-slate-50/50">
            لا توجد بيانات ركاب لعرضها.
        </div>
    </div>
@else
    @php $globalPassengerIndex = 1; @endphp
    @foreach($chunks as $chunkIndex => $chunk)
    <div dir="rtl"
        class="w-full bg-white overflow-hidden print-no-shadow print:border-none print:my-0 print:rounded-none {{ !empty($is_pdf) ? 'max-w-none my-0 rounded-none shadow-none border-none' : 'max-w-7xl mx-auto sm:rounded-[1.5rem] shadow-2xl shadow-indigo-100/50 border border-slate-200 my-8' }}">

        <div class="px-6 py-4 border-b border-slate-200 bg-white">
            @include('receipts.partials.manifest_header')
        </div>

        <div class="p-6 sm:p-8">
            <div class="mb-10 page-break-inside-avoid">
                    {{-- Passengers Table (Driver Version) --}}
                    <div
                        class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm print:rounded-none print:shadow-none print:border-t-2 print:border-b-2 print:border-l-0 print:border-r-0 print:border-slate-800">
                        <table class="w-full text-xs text-center bg-white divide-y divide-slate-200">
                            <thead class="bg-slate-50 print:bg-slate-100">
                                <tr>
                                    <th class="px-2 py-3 w-10 font-black border-l text-slate-500 border-slate-200">#</th>
                                    <th class="px-2 py-3 w-24 font-black border-l text-slate-500 border-slate-200">التاريخ
                                    </th>
                                    <th class="px-2 py-3 w-20 font-black border-l text-slate-500 border-slate-200">اليوم
                                    </th>
                                    <th class="px-2 py-3 w-28 font-black border-l text-slate-500 border-slate-200">اسم
                                        الراكب</th>
                                    <th class="px-2 py-3 w-28 font-black border-l text-slate-500 border-slate-200">رقم
                                        الراكب</th>
                                    <th class="px-2 py-3 w-20 font-black border-l text-slate-500 border-slate-200">نوع المركبة
                                    </th>
                                    <th class="px-2 py-3 w-16 font-black border-l text-slate-500 border-slate-200">العدد
                                    </th>
                                    <th class="px-2 py-3 font-black border-l text-slate-500 border-slate-200 w-[18%]">مكان
                                        الركوب</th>
                                    <th class="px-2 py-3 font-black border-l text-slate-500 border-slate-200 w-[18%]">الوجهة
                                    </th>
                                    <th class="px-2 py-3 font-black text-slate-500 w-[20%]">الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 print:divide-slate-200">
                                @foreach ($chunk as $passenger)
                                    <tr class="transition-colors hover:bg-slate-50/50">
                                        <td
                                            class="px-2 py-2 font-black border-l text-slate-400 border-slate-100 print:border-slate-200">
                                            {{ $globalPassengerIndex++ }}</td>
                                        <td class="px-2 py-2 font-sans font-bold border-l text-slate-700 border-slate-100 print:border-slate-200"
                                            dir="ltr">{{ $passenger['date'] }}</td>
                                        <td
                                            class="px-2 py-2 font-bold border-l text-indigo-600 border-slate-100 print:border-slate-200">
                                            {{ $passenger['day'] }}</td>
                                        <td
                                            class="px-2 py-2 font-bold border-l text-slate-800 border-slate-100 print:border-slate-200">
                                            {{ $passenger['passenger_name'] ?? '---' }}</td>
                                        <td class="px-2 py-2 font-sans font-bold border-l text-slate-800 border-slate-100 print:border-slate-200"
                                            dir="ltr">{{ $passenger['passenger_number'] }}</td>
                                        <td
                                            class="px-2 py-2 font-bold border-l text-slate-700 border-slate-100 print:border-slate-200">
                                            {{ $passenger['vehicle_type'] ?? '-' }}</td>
                                        <td
                                            class="px-2 py-2 font-black border-l text-slate-800 bg-slate-50/50 border-slate-100 print:border-slate-200">
                                            {{ $passenger['count'] }}</td>

                                        {{-- تم استخدام كلاس text-wrap-custom لتوسيع الحقل والسماح بكسر النص --}}
                                        <td
                                            class="px-2 py-2 font-bold border-l text-slate-700 text-wrap-custom border-slate-100 print:border-slate-200">
                                            {{ $passenger['pickup_location'] }}</td>
                                        <td
                                            class="px-2 py-2 font-bold border-l text-slate-700 text-wrap-custom border-slate-100 print:border-slate-200">
                                            {{ $passenger['destination'] }}</td>
                                        <td class="px-2 py-2 text-xs font-medium text-slate-600 text-wrap-custom">
                                            {{ $passenger['note'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @if (!empty($drivers) && $loop->last)
                <div class="mt-6 w-full print:mt-4">
                    <div
                        class="overflow-hidden rounded-xl border shadow-sm border-slate-200 print:rounded-none print:shadow-none print:border-t-2 print:border-b-2 print:border-l-0 print:border-r-0 print:border-slate-800">
                        <div class="grid grid-cols-2 text-center divide-x divide-x-reverse divide-slate-200">

                            <div class="p-3 bg-slate-50 print:bg-slate-100">
                                <p class="mb-1 text-xs font-bold uppercase text-slate-500">إجمالي الركاب</p>
                                <p class="text-xl font-black text-slate-800">{{ $total_passengers ?? 0 }}</p>
                            </div>

                            <div class="p-3 bg-slate-50 print:bg-slate-100">
                                <p class="mb-1 text-xs font-bold uppercase text-slate-500">إجمالي العمولات</p>
                                <p class="text-xl font-black text-emerald-600">
                                    {{ number_format($totalCommissionall ?? 0, 0) }} ر.ي</p>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- منطقة التواقيع --}}
        @if (!empty($drivers) && $loop->last)
            <div class="w-full border-t-2 border-slate-900 print:border-black"></div>
            <div class="flex justify-between items-start px-8 mt-4 mb-8 text-sm font-bold text-slate-500 print:px-4">
                <div class="flex flex-col items-center text-center">
                    <span class="mb-6 font-black text-slate-700 print:text-black">توقيع السائق</span>
                    <span class="w-32 border-b-2 border-dashed border-slate-300 print:border-slate-400"></span>
                </div>
                <div class="flex flex-col items-center text-center">
                    <span class="mb-6 font-black text-slate-700 print:text-black">ختم المكتب</span>
                    <span class="w-32 border-b-2 border-dashed border-slate-300 print:border-slate-400"></span>
                </div>
                <div class="flex flex-col items-center text-center">
                    <span class="mb-6 font-black text-slate-700 print:text-black">الموظف المختص</span>
                    <span class="w-32 border-b-2 border-dashed border-slate-300 print:border-slate-400"></span>
                </div>
            </div>
        @endif

        <div class="p-4 text-center bg-slate-800 sm:rounded-b-[1.5rem] print:hidden">
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
