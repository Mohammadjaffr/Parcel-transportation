@extends('receipts.layout')

@section('title', 'كشف ترحيل الطرود - ' . ($package_number ?? ''))

@push('styles')
    <style>
        /* تحسينات الطباعة لضمان المظهر المتناسق واحتواء الصفحة */
        @media print {
            @page {
                size: A4 landscape;
                margin: 0.5cm;
            }

            body {
                background: #fff;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-no-shadow {
                box-shadow: none !important;
            }

            .print-compact {
                padding: 1rem !important;
            }

            .print-gap-compact {
                gap: 0.75rem !important;
            }
        }
    </style>
@endpush

@section('content')
    <div dir="rtl"
        class="max-w-7xl w-full mx-auto bg-white sm:rounded-[1.5rem] shadow-[0_4px_24px_rgb(0,0,0,0.06)] print-no-shadow overflow-hidden border border-slate-200 my-8 print:border-none print:my-0 print:rounded-none">

        <div class="relative p-6 border-b border-slate-200 bg-slate-50/50 print:bg-transparent print-compact">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500 opacity-[0.03] rounded-bl-full print:hidden"></div>

            <div class="relative flex items-start justify-between gap-6">
                <div class="flex items-start gap-4 min-w-0 flex-1">
                    @if (!empty($company['logo']))
                        <div
                            class="flex justify-center items-center p-1.5 w-16 h-16 bg-white rounded-xl border border-slate-200 shadow-sm shrink-0">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-full h-full">
                        </div>
                    @endif
                    <div class="min-w-0 flex-1 pt-1">
                        <h1 class="text-xl font-black tracking-tight text-slate-900 leading-tight">
                            {{ $company['name'] ?? 'شركة مرسال' }}</h1>
                        <p class="mt-1.5 text-xs font-semibold text-slate-500 flex items-center gap-1.5 leading-relaxed">
                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2 shrink-0 pt-1">
                    <div
                        class="inline-flex justify-center items-center px-4 py-1.5 text-sm font-black text-indigo-800 bg-indigo-100 rounded-lg border border-indigo-200/60 shadow-sm print:border-slate-300">
                        {{ $title ?? 'كشف حمولة الرسائل' }}
                    </div>
                    <div class="flex gap-1.5 items-center mt-0.5 text-xs font-bold text-slate-400 font-sans">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 print-compact">

            <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 print:grid-cols-2 print-gap-compact">

                <div
                    class="relative p-4 rounded-xl border border-indigo-100 bg-gradient-to-br from-indigo-50/20 to-white shadow-sm">
                    <div class="flex gap-2 items-center mb-3 pb-2 border-b border-indigo-100/60">
                        <div class="flex justify-center items-center w-8 h-8 bg-indigo-100 rounded-md">
                            <svg class="w-4 h-4 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-black tracking-wider text-indigo-900">بيانات الإرسالية</h3>
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="flex gap-1.5 items-center">
                            <span class="font-semibold text-slate-400">رقم الإرسالية:</span>
                            <span
                                class="px-2 py-0.5 font-bold font-mono text-indigo-800 bg-indigo-100/50 rounded">{{ $package_number ?? '---' }}</span>
                        </div>
                        <div class="flex gap-1.5">
                            <span class="font-semibold text-slate-400">فرع الإرسال:</span>
                            <span class="font-bold text-slate-800">{{ $package_sender_branch ?? '---' }}</span>
                        </div>
                        <div class="flex gap-1.5 items-center">
                            <span class="font-semibold text-slate-400">عدد الطرود الإجمالي:</span>
                            <span class="text-sm font-black text-indigo-600">{{ $total_shipments ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="relative p-4 rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50/50 to-white shadow-sm">
                    <div class="flex gap-2 items-center mb-3 pb-2 border-b border-slate-200/60">
                        <div class="flex justify-center items-center w-8 h-8 rounded-md bg-slate-200">
                            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-black tracking-wider text-slate-900">بيانات السائق</h3>
                    </div>
                    <div class="space-y-2 text-xs">
                        <div class="flex gap-1.5">
                            <span class="font-semibold text-slate-400">اسم السائق:</span>
                            <span class="font-bold text-slate-800">{{ $driver_name ?? '---' }}</span>
                        </div>
                        <div class="flex gap-1.5">
                            <span class="font-semibold text-slate-400">رقم الهاتف:</span>
                            <span class="font-bold font-sans text-slate-800" dir="ltr">{{ $driver_phone ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-1.5 h-5 bg-indigo-600 rounded-full"></span>
                    <h2 class="text-base font-black text-slate-900">تفاصيل الطرود المُرحلة</h2>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table class="w-full text-xs text-right bg-white divide-y divide-slate-200">
                        <thead class="bg-slate-50 print:bg-slate-100">
                            <tr>
                                <th class="py-2.5 px-3 w-10 font-bold text-center text-slate-500">#</th>
                                <th class="py-2.5 px-3 w-28 font-bold text-slate-500">رقم السند</th>
                                <th class="py-2.5 px-3 font-bold text-slate-500">المرسل</th>
                                <th class="py-2.5 px-3 font-bold text-slate-500">المستلم</th>
                                <th class="py-2.5 px-3 w-28 font-bold text-slate-500">الوجهة</th>
                                <th class="py-2.5 px-3 w-40 font-bold text-slate-500">النوع / الوزن / تفاصيل</th>
                                <th class="py-2.5 px-3 w-28 font-bold text-slate-500 text-center">طريقة الدفع</th>
                                <th class="py-2.5 px-3 w-24 font-bold text-center text-slate-500">الإجمالي</th>
                                <th class="py-2.5 px-3 w-24 font-bold text-center text-slate-500">المتبقي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($shipments ?? [] as $shipment)
                                <tr class="transition-colors hover:bg-slate-50/30">
                                    <td class="py-2.5 px-3 font-bold text-center text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="py-2.5 px-3 font-black text-slate-700 font-mono" dir="ltr">
                                        {{ $shipment['bond_number'] }}</td>
                                    <td class="py-2.5 px-3">
                                        <div class="font-bold text-slate-900">{{ $shipment['sender_name'] }}</div>
                                        <div class="text-[10px] font-semibold text-slate-400 mt-0.5 font-sans" dir="ltr">
                                            {{ $shipment['sender_phone'] }}</div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="font-bold text-slate-900">{{ $shipment['receiver_name'] }}</div>
                                        <div class="text-[10px] font-semibold text-slate-400 mt-0.5 font-sans" dir="ltr">
                                            {{ $shipment['receiver_phone'] }}</div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded font-bold text-[10px] bg-slate-100 text-slate-700">
                                            {{ $shipment['receiver_branch'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 leading-tight">
                                        <span class="font-bold text-slate-800">{{ $shipment['package_type'] }}</span>
                                        @if (!empty($shipment['weight']))
                                            <span class="text-[10px] font-semibold text-slate-400 mx-1">|</span><span
                                                class="text-[10px] font-semibold text-slate-500 font-sans">{{ $shipment['weight'] }}</span>
                                        @endif
                                        @if (!empty($shipment['honey_details']))
                                            <div
                                                class="text-[10px] font-bold text-amber-700 mt-0.5 bg-amber-50 px-1 py-0.5 rounded inline-block border border-amber-100/50">
                                                {{ $shipment['honey_details'] }}</div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @php
                                            $paymentColors = [
                                                'prepaid' => 'bg-emerald-50 text-emerald-800 border-emerald-200/60',
                                                'cod' => 'bg-blue-50 text-blue-800 border-blue-200/60',
                                                'partial_payment' => 'bg-amber-50 text-amber-800 border-amber-200/60',
                                                'customer_credit' => 'bg-rose-50 text-rose-800 border-rose-200/60',
                                            ];
                                            $colorClass = $paymentColors[$shipment['payment_key']] ?? 'bg-slate-50 text-slate-800 border-slate-200/60';
                                        @endphp
                                        <span
                                            class="inline-flex justify-center min-w-[75px] items-center px-1.5 py-0.5 border rounded-md text-[10px] font-bold {{ $colorClass }}">
                                            {{ $shipment['payment_method'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 font-bold text-center text-slate-900 font-sans" dir="ltr">
                                        {{ $shipment['total_amount'] }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-bold font-sans {{ $shipment['remaining_amount'] !== '0' ? 'text-rose-600' : 'text-emerald-600' }}"
                                        dir="ltr">
                                        {{ $shipment['remaining_amount'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-8 font-semibold text-center text-slate-500 bg-slate-50">
                                        لا توجد طرود في هذه الإرسالية.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if (!empty($shipments))
                        <div
                            class="grid grid-cols-1 md:grid-cols-3 print:grid-cols-3 divide-y md:divide-y-0 print:divide-y-0 md:divide-x md:divide-x-reverse print:divide-x print:divide-x-reverse divide-slate-200 bg-slate-50 border-t border-slate-200">

                            {{-- 1. ملخص الطرود --}}
                            <div class="p-4">
                                <h4 class="mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">ملخص الإرسالية
                                </h4>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 mb-0.5">العدد الإجمالي</p>
                                        <p class="text-lg font-black text-slate-900 font-sans">{{ $total_shipments ?? 0 }}</p>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[10px] font-bold text-slate-400 mb-0.5">إجمالي المبالغ</p>
                                        <p class="text-lg font-black text-indigo-700 font-sans" dir="ltr">
                                            @php
                                                $totalAmounts = collect($shipments)->sum(
                                                    fn($s) => (float) str_replace(',', '', $s['total_amount']),
                                                );
                                            @endphp
                                            {{ number_format($totalAmounts, 0) }} <span
                                                class="text-[10px] font-normal text-slate-400">ر.ي</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. التحصيل المالي --}}
                            <div class="p-4">
                                <h4 class="mb-2 text-[10px] font-black uppercase tracking-wider text-slate-400">التحصيل المالي
                                </h4>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] font-bold text-emerald-600 mb-0.5">المدفوع</p>
                                        <p class="text-lg font-black text-emerald-700 font-sans">
                                            {{-- الحسبة الذكية: إجمالي المبالغ مطروحاً منها ما سيحصله السائق كاش --}}
                                            {{ number_format($totalAmounts - ($totals['expected_cash'] ?? 0), 0) }} <span
                                                class="text-[10px] font-normal text-emerald-500">ر.ي</span>
                                        </p>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[10px] font-bold text-rose-600 mb-0.5">المتبقي للتحصيل</p>
                                        <p class="text-lg font-black text-rose-700 font-sans">
                                            {{-- القيمة القادمة مباشرة من الـ Accessor الخاص بموديل الـ Shipment --}}
                                            {{ number_format($totals['expected_cash'] ?? 0, 0) }} <span
                                                class="text-[10px] font-normal text-rose-500">ر.ي</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. العمولات --}}
                            <div class="p-4 bg-emerald-50/30 print:bg-transparent">
                                <h4 class="mb-2 text-[10px] font-black uppercase tracking-wider text-emerald-800">تفصيل العمولات
                                </h4>
                                <div class="flex justify-between items-end gap-2">
                                    <div>
                                        <p class="text-[10px] font-bold text-emerald-600/80 mb-0.5">عمولة الطرود</p>
                                        <p class="text-sm font-black text-emerald-700 font-sans">
                                            {{ number_format($totals['package_commission'] ?? 0, 0) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-amber-600/80 mb-0.5">عمولة العسل</p>
                                        <p class="text-sm font-black text-amber-700 font-sans">
                                            {{ number_format($totals['honey_commission'] ?? 0, 0) }}
                                        </p>
                                    </div>
                                    <div class="text-left pl-1 border-r pr-2 border-emerald-200/80">
                                        <p class="text-[10px] font-bold text-emerald-800 mb-0.5">الإجمالي</p>
                                        <p class="text-lg font-black text-emerald-900 font-sans">
                                            {{ number_format($totals['grand_commission'] ?? 0, 0) }} <span
                                                class="text-[10px] font-normal text-emerald-700">ر.ي</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6 mt-10 mb-4 print:mt-8">
                <div class="text-center">
                    <p class="mb-6 text-xs font-bold text-slate-500">توقيع مسؤول الفرع</p>
                    <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
                </div>
                <div class="text-center">
                    <p class="mb-6 text-xs font-bold text-slate-500">توقيع السائق</p>
                    <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
                </div>
                <div class="text-center">
                    <p class="mb-6 text-xs font-bold text-slate-500">ختم الفرع المستلم</p>
                    <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border-t border-slate-800 p-4 text-center sm:rounded-b-[1.5rem] print:rounded-none">
            <p class="text-[10px] font-medium text-slate-400">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> |
                بواسطة: <span class="text-slate-300 font-bold">{{ $creator_name ?? 'مسؤول النظام' }}</span> |
                الطباعة: <span class="text-slate-300 font-bold font-sans"
                    dir="ltr">{{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}</span>
            </p>

            <div class="pt-2 mt-2 border-t border-slate-800/80 inline-block min-w-[40%]">
                <p class="text-[9px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-2 text-slate-700">|</span>
                    لطلب النظام: <span dir="ltr" class="font-mono text-slate-400">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection