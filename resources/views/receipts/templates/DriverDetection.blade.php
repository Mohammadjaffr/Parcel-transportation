@extends('receipts.layout')

@section('title', 'استمارة نقل يومي - ' . ($package_number ?? ''))

@push('styles')
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            .print-border { border: none !important; }
            .print-bg-white { background-color: white !important; }
        }
    </style>
@endpush

@php
    $safeTotals = $totals ?? [
        'package_commission' => 0,
        'honey_commission'   => 0,
        'grand_commission'   => 0,
        'expected_cash'      => 0,
    ];
@endphp

@section('content')
    <div class="max-w-[1400px] w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0 print-bg-white">

        <div class="p-6 bg-gradient-to-l to-white border-b from-indigo-50/50 sm:p-8 border-slate-100">
            <div class="flex flex-col gap-6 justify-between items-start sm:flex-row sm:items-center">
                <div class="flex gap-4 items-center">
                    @if (!empty($company['logo']))
                        <div class="flex justify-center items-center p-2 w-16 h-16 bg-white rounded-2xl border shadow-sm border-slate-100">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-full h-full">
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-slate-800">{{ $company['name'] ?? 'شركة مرسال' }}</h1>
                        <p class="mt-1 text-sm font-medium text-slate-500">{{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="inline-flex justify-center items-center px-6 py-2 mb-2 text-base font-black tracking-wide text-indigo-700 bg-indigo-50 rounded-xl border border-indigo-100">
                        استمارة نقل يومي
                    </div>
                    <div class="flex gap-1.5 justify-end items-center mt-1 text-xs font-medium text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-3">
                <div class="flex justify-between items-center p-4 rounded-2xl border border-indigo-100 bg-indigo-50/30">
                    <div>
                        <span class="block mb-1 text-xs font-bold text-slate-500">اسم صاحب الباص (السائق)</span>
                        <span class="text-lg font-black text-slate-800">{{ $driver_name ?? '---' }}</span>
                    </div>
                    <div class="text-left">
                        <span class="block mb-1 text-xs font-bold text-slate-500">رقم التواصل</span>
                        <span class="text-sm font-bold text-slate-700" dir="ltr">{{ $driver_phone ?? '---' }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <div>
                        <span class="block mb-1 text-xs font-bold text-slate-500">رقم الرحلة / الكشف</span>
                        <span class="text-lg font-black text-slate-800" dir="ltr">{{ $package_number ?? '---' }}</span>
                    </div>
                    <div class="text-left">
                        <span class="block mb-1 text-xs font-bold text-slate-500">فرع الانطلاق</span>
                        <span class="text-sm font-bold text-slate-700">{{ $package_sender_branch ?? '---' }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center p-4 rounded-2xl border border-emerald-100 bg-emerald-50/30">
                    <div>
                        <span class="block mb-1 text-xs font-bold text-emerald-600">إجمالي الطرود</span>
                        <span class="text-2xl font-black text-emerald-800">{{ $total_shipments ?? 0 }}</span>
                    </div>
                    <div class="flex justify-center items-center w-12 h-12 bg-emerald-100 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <div class="overflow-x-auto rounded-2xl border shadow-sm border-slate-200">
                    <table class="w-full text-sm text-right divide-y premium-table divide-slate-200">
                        <thead class="bg-slate-100/70 text-slate-700">
                            <tr>
                                <th class="py-3 w-8 text-center border-l border-slate-200">الرقم</th>
                                <th class="w-20 text-center border-l border-slate-200">رقم السند</th>
                                <th class="w-28 border-l border-slate-200">نوع الطرد</th>
                                <th class="w-24 border-l border-slate-200">مكان التسليم</th>
                                <th class="w-32 border-l border-slate-200">المرسل</th>
                                <th class="w-24 text-center border-l border-slate-200">رقم المرسل</th>
                                <th class="w-32 border-l border-slate-200">المستلم</th>
                                <th class="w-24 text-center border-l border-slate-200">رقم المستلم</th>
                                <th class="w-28 text-center border-l border-slate-200">المحاسب (المبلغ)</th>
                                <th class="w-24 font-bold text-center text-rose-700 bg-rose-50/50">آجل (المتبقي)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($shipments ?? [] as $shipment)
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="py-3 font-bold text-center border-l text-slate-400 border-slate-100">{{ $loop->iteration }}</td>
                                    
                                    <td class="font-black text-center border-l text-slate-800 border-slate-100" dir="ltr">{{ $shipment['bond_number'] }}</td>
                                    
                                    <td class="border-l border-slate-100">
                                        <div class="font-bold text-slate-700">{{ $shipment['package_type'] }}</div>
                                        @if (!empty($shipment['weight']))
                                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $shipment['weight'] }}</div>
                                        @endif
                                        @if (!empty($shipment['honey_details']))
                                            <div class="text-[10px] text-amber-600 mt-0.5">{{ $shipment['honey_details'] }}</div>
                                        @endif
                                    </td>
                                    
                                    <td class="font-bold border-l text-slate-600 border-slate-100">{{ $shipment['receiver_branch'] }}</td>
                                    
                                    <td class="font-bold text-slate-800 border-l border-slate-100 truncate max-w-[120px]">{{ $shipment['sender_name'] }}</td>
                                    <td class="text-xs font-medium text-center border-l text-slate-500 border-slate-100" dir="ltr">{{ $shipment['sender_phone'] }}</td>
                                    
                                    <td class="font-bold text-slate-800 border-l border-slate-100 truncate max-w-[120px]">{{ $shipment['receiver_name'] }}</td>
                                    <td class="text-xs font-medium text-center border-l text-slate-500 border-slate-100" dir="ltr">{{ $shipment['receiver_phone'] }}</td>
                                    
                                    <td class="text-center border-l border-slate-100">
                                        <div class="font-black text-slate-800">{{ $shipment['total_amount'] }}</div>
                                        <div class="text-[9px] font-bold text-indigo-600 mt-0.5">{{ $shipment['payment_method'] }}</div>
                                    </td>
                                    
                                    <td class="text-center bg-rose-50/20">
                                        @if($shipment['remaining_amount'] !== '0')
                                            <span class="text-base font-black text-rose-600" dir="ltr">{{ $shipment['remaining_amount'] }}</span>
                                        @else
                                            <span class="text-xs font-bold text-slate-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="p-8 font-medium text-center text-slate-400 bg-slate-50/50">
                                        لا توجد طرود في هذه الرحلة.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if (!empty($shipments))
                        <div class="grid grid-cols-5 border-t-2 divide-x divide-x-reverse divide-slate-200 border-slate-200 bg-slate-50">
                            <div class="p-4 text-center">
                                <p class="mb-1 text-[10px] font-bold text-slate-500">إجمالي الرسائل (الطرود)</p>
                                <p class="text-xl font-black text-slate-800">{{ $total_shipments ?? 0 }}</p>
                            </div>
                            
                            <div class="p-4 text-center">
                                <p class="mb-1 text-[10px] font-bold text-slate-500">إجمالي العمولات</p>
                                <p class="text-xl font-black text-emerald-600" dir="ltr">{{ number_format($safeTotals['grand_commission'], 0) }}</p>
                            </div>

                            <div class="p-4 text-center">
                                <p class="mb-1 text-[10px] font-bold text-slate-500">المبلغ المحاسب (الإجمالي)</p>
                                <p class="text-xl font-black text-indigo-600" dir="ltr">
                                    @php
                                        $totalAmounts = collect($shipments)->sum(fn($s) => (float) str_replace(',', '', $s['total_amount']));
                                    @endphp
                                    {{ number_format($totalAmounts, 0) }}
                                </p>
                            </div>

                            <div class="col-span-2 p-4 text-center bg-rose-50/50">
                                <p class="mb-1 text-[11px] font-black uppercase text-rose-600">المتبقي (الآجل المطلوب تحصيله)</p>
                                <p class="text-2xl font-black text-rose-700" dir="ltr">
                                    {{ number_format($safeTotals['expected_cash'], 0) }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            
        </div>

        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem] print:rounded-none">
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة:
                {{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}
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