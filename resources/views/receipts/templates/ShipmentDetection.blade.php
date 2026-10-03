@extends('receipts.layout')

@section('title', 'كشف ترحيل الطرود - ' . ($package_number ?? ''))

@push('styles')
    <style>
        /* تحسينات الطباعة لضمان الاحتواء (50 طرد) مع الحفاظ على الأناقة */
        @media print {
            @page {
                size: A4 landscape;
                margin: 4mm;
            }

            body {
                background: #fff;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-no-shadow {
                box-shadow: none !important;
            }

            .print-hidden {
                display: none !important;
            }

            table {
                page-break-inside: avoid;
                width: 100% !important;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            /* الحفاظ على الكثافة مع أناقة */
            table th,
            table td {
                padding: 2px 4px !important;
                font-size: 9px !important;
                height: 16px !important;
                border-color: #e2e8f0 !important;
            }

            table th {
                background-color: #f8fafc !important;
                color: #475569 !important;
                font-weight: 900 !important;
            }

            table td {
                color: #0f172a !important;
                font-weight: 700 !important;
            }

            .print-badge {
                padding: 4px 12px !important;
                font-size: 11px !important;
            }
        }
    </style>
@endpush

@section('content')
    <div dir="rtl"
        class="w-full bg-white overflow-hidden print-no-shadow print:border-none print:my-0 print:rounded-none {{ !empty($is_pdf) ? 'max-w-none my-0 rounded-none shadow-none border-none' : 'max-w-7xl mx-auto sm:rounded-[1.5rem] shadow-2xl shadow-indigo-100/50 border border-slate-200 my-8' }}">

        {{-- الترويسة الرئيسية الأنيقة --}}
        <div class="relative px-6 py-4 border-b border-slate-200 bg-slate-50/50 print:bg-transparent">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500 opacity-[0.03] rounded-bl-full print:hidden"></div>

            <div class="flex relative z-10 justify-between items-start">
                
                {{-- يمين: بيانات الشركة --}}
                <div class="flex flex-col flex-1 gap-2 text-right">
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 print:text-xl print:mb-0">
                        {{ $company['name'] ?? 'شركة النقل' }}
                    </h1>
                    
                    <div class="inline-flex flex-col gap-1.5 text-xs font-bold text-slate-600 print:gap-1 print:text-[10px]">
                        @if (!empty($company['main_branch']))
                            <span class="flex gap-1.5 items-center px-2 py-1 rounded-md border bg-slate-100 border-slate-200 print:bg-transparent print:border-none print:p-0">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full print:hidden"></span>
                                {{ $company['main_branch']['title'] }} 
                                <span dir="rtl" class="text-slate-500 font-semibold mr-1">هاتف: <span dir="ltr" class="text-slate-800 font-bold">{{ $company['main_branch']['phones'] }}</span></span>
                            </span>
                        @else
                            <span class="flex gap-1.5 items-center px-2 py-1 rounded-md border bg-slate-100 border-slate-200 print:bg-transparent print:border-none print:p-0">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full print:hidden"></span>
                                الفرع: {{ $user_branch ?? 'المركز الرئيسي' }}
                            </span>
                        @endif

                        @if (!empty($company['headquarters']) && ($company['main_branch']['title'] ?? '') !== $company['headquarters']['title'])
                            <span class="flex gap-1.5 items-center px-2 py-1 rounded-md border bg-slate-100 border-slate-200 print:bg-transparent print:border-none print:p-0">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full print:hidden"></span>
                                {{ $company['headquarters']['title'] }}
                                <span dir="rtl" class="text-slate-500 font-semibold mr-1">هاتف: <span dir="ltr" class="text-slate-800 font-bold">{{ $company['headquarters']['phones'] }}</span></span>
                            </span>
                        @endif

                        <span class="flex gap-1.5 items-center px-2 py-1 rounded-md border bg-slate-100 border-slate-200 print:bg-transparent print:border-none print:p-0">
                            <span class="w-2 h-2 bg-teal-500 rounded-full print:hidden"></span>
                            فرع الترحيل: {{ $package_sender_branch ?? ($user_branch ?? 'الفرع الرئيسي') }}
                        </span>
                        
                        @if (!empty($company['other_phones']))
                            <span class="flex gap-1.5 items-center px-2 py-1 rounded-md border bg-slate-100 border-slate-200 print:bg-transparent print:border-none print:p-0">
                                <span class="w-2 h-2 bg-slate-500 rounded-full print:hidden"></span>
                                فروع أخرى:
                                <span dir="ltr" class="text-slate-800 font-bold text-[10px] print:text-[9px]">{{ $company['other_phones'] }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                {{-- وسط: الشعار والبطاقة --}}
                <div class="flex flex-col justify-center items-center px-4 shrink-0 print:mt-0">
                    @if (!empty($company['logo']))
                        <div
                            class="flex justify-center items-center mb-2 w-28 h-28 bg-white rounded-3xl border-2 shadow-lg shadow-slate-200/50 border-slate-100 print:w-20 print:h-20 print:shadow-none print:border-slate-200 print:mb-1">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-[80%] h-[80%]">
                        </div>
                    @endif
                    <div
                        class="inline-flex justify-center items-center px-6 py-1.5 text-sm font-black text-indigo-900 bg-indigo-100 rounded-full border border-indigo-200 shadow-sm print-badge print:bg-transparent print:border-slate-300 print:text-black">
                        {{ $title ?? 'استمارة نقل يومي' }}
                    </div>
                </div>

                {{-- يسار: بيانات الكشف والسائق --}}
                <div class="flex flex-col flex-1 gap-2 items-end text-left">
                    <div
                        class="p-3 w-full max-w-[240px] bg-white rounded-xl border shadow-sm border-slate-200 print:w-[200px] print:shadow-none print:bg-transparent print:p-0 print:border-none">
                        <div
                            class="flex justify-between items-center pb-1.5 mb-1.5 text-xs border-b border-dashed border-slate-200 print:text-[10px]">
                            <span class="font-bold text-slate-800 print:text-black">{{ $driver_name ?? '---' }}</span>
                            <span class="font-semibold text-slate-500 print:text-black">اسم السائق</span>
                        </div>
                        <div
                            class="flex justify-between items-center pb-1.5 mb-1.5 text-xs border-b border-dashed border-slate-200 print:text-[10px]">
                            <span class="font-sans font-bold text-slate-800 print:text-black"
                                dir="ltr">{{ $driver_phone ?? '---' }}</span>
                            <span class="font-semibold text-slate-500 print:text-black">رقم الهاتف</span>
                        </div>
                        <div
                            class="flex justify-between items-center pb-1.5 mb-1.5 text-xs border-b border-dashed border-slate-200 print:text-[10px]">
                            <span class="font-sans text-sm font-black text-indigo-700 print:text-black"
                                dir="ltr">{{ $package_number ?? '---' }}</span>
                            <span class="font-semibold text-slate-500 print:text-black">رقم الكشف</span>
                        </div>
                        <div class="flex justify-between items-center text-xs print:text-[10px]">
                            <span class="font-sans font-bold text-slate-800 print:text-black"
                                dir="ltr">{{ $print_date ?? date('Y-m-d') }}</span>
                            <span class="font-semibold text-slate-500 print:text-black">التاريخ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- الجدول العصري والأنيق --}}
        <div class="p-4 print:p-0">
            <div
                class="overflow-x-auto rounded-xl border shadow-sm border-slate-200 print:rounded-none print:shadow-none print:border-t-2 print:border-b-2 print:border-l-0 print:border-r-0 print:border-slate-800">
                <table class="w-full text-xs text-center bg-white divide-y divide-slate-200">
                    <thead class="bg-slate-50 print:bg-slate-100">
                        <tr>
                            <th class="px-2 py-3 w-8 font-black border-l text-slate-500 border-slate-200">#</th>
                            <th class="px-2 py-3 font-black border-l text-slate-500 border-slate-200">المرسل</th>
                            <th class="px-2 py-3 w-24 font-black border-l text-slate-500 border-slate-200">رقم المرسل</th>
                            <th class="px-2 py-3 font-black border-l text-slate-500 border-slate-200">المستلم</th>
                            <th class="px-2 py-3 w-24 font-black border-l text-slate-500 border-slate-200">رقم المستلم</th>
                            <th class="px-2 py-3 w-20 font-black border-l text-slate-500 border-slate-200">النوع</th>
                            <th class="px-2 py-3 w-24 font-black border-l text-slate-500 border-slate-200">مكان التسليم</th>
                            <th class="px-2 py-3 w-20 font-black border-l text-slate-500 border-slate-200">المحاسب</th>
                            <th class="px-2 py-3 w-20 font-black border-l text-slate-500 border-slate-200">آجل</th>
                            <th class="px-2 py-3 w-24 font-black text-slate-500">رقم السند</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 print:divide-slate-200">
                        @forelse($shipments ?? [] as $shipment)
                            <tr class="transition-colors hover:bg-slate-50/50">
                                <td
                                    class="px-2 py-2 font-black border-l text-slate-400 border-slate-100 print:border-slate-200">
                                    {{ $loop->iteration }}</td>
                                <td
                                    class="px-2 py-2 font-bold border-l text-slate-800 border-slate-100 print:border-slate-200">
                                    {{ $shipment['sender_name'] }}</td>
                                <td class="px-2 py-2 font-sans font-bold border-l text-slate-600 border-slate-100 print:border-slate-200"
                                    dir="ltr">
                                    {{ $shipment['sender_phone'] !== '---' ? $shipment['sender_phone'] : '' }}</td>
                                <td
                                    class="px-2 py-2 font-bold border-l text-slate-800 border-slate-100 print:border-slate-200">
                                    {{ $shipment['receiver_name'] }}</td>
                                <td class="px-2 py-2 font-sans font-bold border-l text-slate-600 border-slate-100 print:border-slate-200"
                                    dir="ltr">{{ $shipment['receiver_phone'] }}</td>
                                <td
                                    class="px-2 py-2 font-bold border-l text-slate-700 border-slate-100 print:border-slate-200">
                                    <span
                                        class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">{{ $shipment['package_type'] }}</span>
                                </td>
                                <td
                                    class="px-2 py-2 font-bold border-l text-slate-700 border-slate-100 print:border-slate-200">
                                    {{ $shipment['receiver_branch'] }}</td>
                                <td class="px-2 py-2 font-sans font-black text-emerald-600 border-l border-slate-100 print:border-slate-200 print:text-black"
                                    dir="ltr">
                                    {{ str_replace(',', '', $shipment['total_amount']) > 0 ? $shipment['total_amount'] : '' }}
                                </td>
                                <td class="px-2 py-2 font-sans font-black text-rose-600 border-l border-slate-100 print:border-slate-200 print:text-black"
                                    dir="ltr">
                                    {{ str_replace(',', '', $shipment['remaining_amount']) > 0 ? $shipment['remaining_amount'] : '' }}
                                </td>
                                <td class="px-2 py-2 font-mono font-black text-indigo-700 print:text-black" dir="ltr">
                                    {{ $shipment['bond_number'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-6 font-bold text-center text-slate-500 bg-slate-50">لا توجد طرود
                                    في هذه الإرسالية.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- التذييل (ملخص وتواقيع) بناءً على طلبك الأخير --}}
            @if (!empty($shipments))
                <div class="flex flex-col gap-6 mt-6 print:gap-4 print:mt-4">
                    {{-- جدول الملخص العصري في الأسفل (محاذاة لليمين كما في الصورة) --}}
                    @php
                        $totalAmounts = collect($shipments)->sum(
                            fn($s) => (float) str_replace(',', '', $s['total_amount']),
                        );
                        $totalRemaining = collect($shipments)->sum(
                            fn($s) => (float) str_replace(',', '', $s['remaining_amount']),
                        );
                    @endphp
                    <div class="mt-4 w-full print:mt-2">
                        <div
                            class="overflow-x-auto rounded-xl border shadow-sm border-slate-200 print:rounded-none print:shadow-none print:border-t-2 print:border-b-2 print:border-l-0 print:border-r-0 print:border-slate-800">
                            <table class="w-full text-xs text-center bg-white divide-y divide-slate-200">
                                <thead class="bg-indigo-50/50 print:bg-slate-100">
                                    <tr>
                                        <th
                                            class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800">
                                            إجمالي الرسائل</th>
                                        <th
                                            class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800">
                                            إجمالي العمولة</th>
                                        <th
                                            class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800">
                                            المبلغ المدفوع</th>
                                        <th class="px-2 py-2.5 font-black text-indigo-900 print:text-slate-800">المتبقي
                                            (آجل)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 print:divide-slate-200">
                                    <tr>
                                        <td
                                            class="px-2 py-3 text-lg font-black border-l text-slate-800 border-slate-100 print:border-slate-200">
                                            {{ $total_shipments ?? 0 }}</td>
                                        <td
                                            class="px-2 py-3 font-sans text-lg font-black text-emerald-600 border-l border-slate-100 print:border-slate-200 print:text-slate-800">
                                            {{ number_format($totals['grand_commission'] ?? 0, 0) }}</td>
                                        <td
                                            class="px-2 py-3 font-sans text-lg font-black text-indigo-600 border-l border-slate-100 print:border-slate-200 print:text-slate-800">
                                            {{ number_format($totalAmounts - $totalRemaining, 0) }}</td>
                                        <td
                                            class="px-2 py-3 font-sans text-lg font-black text-rose-600 print:text-slate-800">
                                            {{ number_format($totalRemaining, 0) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- فاصل أسود عريض --}}
                    <div class="mt-2 w-full border-t-2 border-slate-900 print:border-black"></div>

                    {{-- منطقة التواقيع --}}
                    <div class="flex justify-between items-start px-8 mt-2 text-sm font-bold text-slate-500 print:px-4">
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


                </div>
            @endif

        </div>

        <div class="bg-slate-900 p-4 text-center sm:rounded-b-[1.5rem] print:hidden">
            <p class="text-xs font-medium text-slate-400">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> |
                بواسطة: <span class="font-bold text-slate-300">{{ $creator_name ?? 'مسؤول النظام' }}</span>
            </p>
        </div>
    </div>
@endsection
