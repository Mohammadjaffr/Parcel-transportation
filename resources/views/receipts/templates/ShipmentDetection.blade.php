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

            .summary-table td {
                height: auto !important;
                padding: 4px 4px !important;
            }

            .summary-table td.total-val {
                font-size: 13px !important;
                font-weight: 900 !important;
            }

            .settlement-cell-label {
                font-size: 8.5px !important;
                font-weight: 900 !important;
                line-height: 1.1 !important;
            }

            .settlement-cell-amount {
                font-size: 11.5px !important;
                font-weight: 900 !important;
                padding: 0px 4px !important;
            }
        }
    </style>
@endpush

@section('content')
    <div dir="rtl"
        class="w-full bg-white overflow-hidden print-no-shadow print:border-none print:my-0 print:rounded-none {{ !empty($is_pdf) ? 'max-w-none my-0 rounded-none shadow-none border-none' : 'max-w-7xl mx-auto sm:rounded-[1.5rem] shadow-2xl shadow-indigo-100/50 border border-slate-200 my-8' }}">

        <div class="px-6 py-4 bg-white border-b border-slate-200">
            @include('receipts.partials.manifest_header')
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
                            <th class="px-2 py-3 w-20 font-black border-l text-slate-500 border-slate-200">اجور اخرى</th>
                            <th class="px-2 py-3 w-24 font-black border-l text-slate-500 border-slate-200">الإجمالي</th>
                            <!-- العمود الجديد -->
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
                                    {{ $shipment['accountant_amount'] }}
                                </td>
                                <td class="px-2 py-2 font-sans font-black text-rose-600 border-l border-slate-100 print:border-slate-200 print:text-black"
                                    dir="ltr">
                                    {{ $shipment['deferred_amount'] }}
                                </td>
                                <td class="px-2 py-2 font-sans font-black border-l text-slate-700 border-slate-100 print:border-slate-200 print:text-black"
                                    dir="ltr">
                                    {{ $shipment['other_fee'] }}
                                    @if (!empty($shipment['show_other_fee_in_receipt']) && !empty($shipment['other_fee_description']))
                                        <div class="text-[9px] font-normal text-slate-500 font-sans" dir="rtl">
                                            ({{ $shipment['other_fee_description'] }})
                                        </div>
                                    @endif
                                </td>
                                <td class="px-2 py-2 font-sans font-black border-l text-slate-900 border-slate-100 print:border-slate-200 print:text-black bg-slate-50/50"
                                    dir="ltr">
                                    <div class="text-sm">{{ $shipment['row_total'] }}</div>
                                    <div class="text-[8px] font-normal text-slate-500 mt-0.5 flex flex-col gap-0.5 leading-tight"
                                        dir="rtl">
                                        @if ($shipment['has_accountant'])
                                            <span class="text-emerald-600">محاسب
                                            </span>
                                        @endif
                                        @if ($shipment['has_deferred'])
                                            <span class="text-rose-600">آجل</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 py-2 font-mono font-black text-indigo-700 print:text-black" dir="ltr">
                                    {{ $shipment['bond_number'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="p-6 font-bold text-center text-slate-500 bg-slate-50">لا توجد
                                    طرود
                                    في هذه الإرسالية.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- التذييل (ملخص وتواقيع) --}}
            @if (!empty($shipments))
                <div class="flex flex-col gap-6 mt-6 print:gap-4 print:mt-4">
                    {{-- جدول الملخص العصري في الأسفل (تم ربطه بـ $totals من الكونترولر لضمان دقة الحساب) --}}
                    @php
                        $settlementColor = $totals['settlement_color'] ?? '#4b5563';
                        $settlementResult = $totals['settlement_result'] ?? 0;
                        $settlementAmount = $totals['settlement_amount'] ?? 0;
                        $settlementLabel = $totals['settlement_label'] ?? 'خالص (لا يوجد مبالغ لأي طرف)';
                    @endphp

                    <div class="mt-4 w-full print:mt-2">
                        <div
                            class="overflow-x-auto rounded-xl border shadow-sm border-slate-200 print:rounded-none print:shadow-none print:border-t-2 print:border-b-2 print:border-l-0 print:border-r-0 print:border-slate-800">
                            <table class="w-full text-xs text-center bg-white divide-y divide-slate-200 summary-table"
                                style="width: 100%; border-collapse: collapse;">
                                <thead class="bg-indigo-50/50 print:bg-slate-100">
                                    <tr>
                                        <th class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800"
                                            style="padding: 6px; font-weight: 900; border-left: 1px solid #e2e8f0;">
                                            إجمالي الرسائل</th>
                                        <th class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800"
                                            style="padding: 6px; font-weight: 900; border-left: 1px solid #e2e8f0;">
                                            إجمالي المبالغ
                                        </th>
                                        <th class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800"
                                            style="padding: 6px; font-weight: 900; border-left: 1px solid #e2e8f0;">
                                            إجمالي العمولة
                                        </th>

                                        <th class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800"
                                            style="padding: 6px; font-weight: 900; border-left: 1px solid #e2e8f0;">
                                            <div>التصفية مع السائق</div>
                                        </th>
                                        <th class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800"
                                            style="padding: 6px; font-weight: 900; border-left: 1px solid #e2e8f0;">
                                            إجمالي الآجل
                                        </th>
                                        <th class="px-2 py-2.5 font-black text-indigo-900 border-l border-slate-200 print:text-slate-800"
                                            style="padding: 6px; font-weight: 900; border-left: 1px solid #e2e8f0;">
                                            إجمالي الآجور الاخرى
                                        </th>
                                        <!-- الحقل الجديد في الترويسة -->
                                        <th class="px-2 py-2.5 font-black text-indigo-900 print:text-slate-800 bg-slate-100"
                                            style="padding: 6px; font-weight: 900;">
                                            إجمالي (محاسب + آجل + الآجور الاخرى)
                                        </th>
                                    </tr>

                                </thead>
                                <tbody class="divide-y divide-slate-100 print:divide-slate-200">
                                    <tr>
                                        <td class="px-2 py-3 text-lg font-black border-l text-slate-800 border-slate-100 print:border-slate-200 total-val"
                                            style="padding: 6px; font-size: 15px; font-weight: 900; border-left: 1px solid #f1f5f9;">
                                            {{ $total_shipments ?? 0 }}</td>
                                        <td class="px-2 py-3 text-lg font-black border-l text-slate-800 border-slate-100 print:border-slate-200 total-val"
                                            style="padding: 6px; font-size: 15px; font-weight: 900; border-left: 1px solid #f1f5f9;">
                                            {{ number_format($totals['total_amounts'] ?? 0, 0) }}</td>
                                        <td class="px-2 py-3 font-sans text-lg font-black text-emerald-600 border-l border-slate-100 print:border-slate-200 print:text-slate-800 total-val"
                                            style="padding: 6px; font-size: 15px; font-weight: 900; color: #16a34a; border-left: 1px solid #f1f5f9;">
                                            {{ number_format($totals['grand_commission'] ?? 0, 0) }}</td>

                                        <td class="px-2 py-3 font-sans text-lg font-black border-l border-slate-100 print:border-slate-200 print:text-slate-800 total-val"
                                            style="padding: 6px; font-size: 15px; font-weight: 900; border-left: 1px solid #f1f5f9;">
                                            <div dir="ltr"
                                                style="color: {{ $settlementColor }}; font-size: 15px; font-weight: 900;">
                                                {{ number_format($settlementAmount ?? 0, 0) }}
                                            </div>
                                            <div class="settlement-cell-label"
                                                style="font-size: 9px; font-weight: 700; color: #64748b; margin-top: 1px;">
                                                {{ $settlementLabel }}
                                            </div>
                                        </td>
                                        <td class="px-2 py-3 font-sans text-lg font-black text-rose-600 border-l print:text-slate-800 total-val border-slate-100 print:border-slate-200"
                                            style="padding: 6px; font-size: 15px; font-weight: 900; color: #dc2626; border-left: 1px solid #f1f5f9;">
                                            {{ number_format($totals['deferred_amount'] ?? 0, 0) }}
                                        </td>
                                        <td class="px-2 py-3 font-sans text-lg font-black text-emerald-600 border-l print:text-slate-800 total-val border-slate-100 print:border-slate-200"
                                            style="padding: 6px; font-size: 15px; font-weight: 900; color: #166534; border-left: 1px solid #f1f5f9;">
                                            {{ number_format($totals['other_fee'] ?? 0, 0) }}
                                        </td>
                                        <!-- الخلية الجديدة لجمع المحاسب والآجل -->
                                        <td class="px-2 py-3 font-sans text-lg font-black text-slate-800 bg-slate-50 print:bg-slate-50 total-val"
                                            style="padding: 6px; font-size: 15px; font-weight: 900;">
                                            {{ number_format(($totals['accountant_amount'] ?? 0) + ($totals['deferred_amount'] ?? 0) + ($totals['other_fee'] ?? 0), 0) }}
                                        </td>
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
                            <span class="w-32 border-slate-300 print:border-slate-400">{{ $creator_name ?? 'مسؤول النظام' }}</span>
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
