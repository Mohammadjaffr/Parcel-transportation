@extends('receipts.layout')

@section('title', 'كشف ترحيل الطرود - ' . ($package_number ?? ''))

@push('styles')
    <style>
        /* تحسينات الطباعة لضمان ظهور الألوان والخلفيات بدقة */
        @media print {
            @page {
                size: A4 landscape;
                margin: 0.5cm;
            }
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-no-shadow {
                box-shadow: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div dir="rtl" class="max-w-7xl w-full mx-auto bg-white sm:rounded-[1.5rem] shadow-[0_4px_24px_rgb(0,0,0,0.06)] print-no-shadow overflow-hidden border border-slate-200 my-8 print:border-none print:my-0">

        <!-- Header Section -->
        <div class="relative p-6 sm:p-8 border-b border-slate-200 bg-slate-50/50 print:bg-transparent">
            <!-- ديكور خلفي بسيط -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500 opacity-[0.03] rounded-bl-full print:hidden"></div>
            
            <div class="relative flex flex-col gap-6 justify-between items-start sm:flex-row sm:items-center">
                <!-- بيانات الشركة -->
                <div class="flex gap-4 items-center">
                    @if (!empty($company['logo']))
                        <div class="flex justify-center items-center p-2 w-16 h-16 bg-white rounded-xl border shadow-sm border-slate-200">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-full h-full">
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $company['name'] ?? 'شركة مرسال' }}</h1>
                        <p class="mt-1 text-sm font-semibold text-slate-500 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}
                        </p>
                    </div>
                </div>

                <!-- بيانات المستند -->
                <div class="text-right sm:text-left">
                    <div class="inline-flex justify-center items-center px-4 py-2 mb-2 text-sm font-black text-indigo-800 bg-indigo-100 rounded-lg border border-indigo-200 print:border-slate-300">
                        {{ $title ?? 'كشف حمولة الرسائل' }}
                    </div>
                    <div class="flex gap-1.5 justify-start sm:justify-end items-center mt-1 text-xs font-bold text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <!-- Package & Driver Info (Cards) -->
            <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2">
                
                <!-- بطاقة بيانات الإرسالية -->
                <div class="relative p-5 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/80 to-white shadow-sm">
                    <div class="flex gap-3 items-center mb-4 pb-3 border-b border-indigo-100">
                        <div class="flex justify-center items-center w-10 h-10 bg-indigo-100 rounded-lg">
                            <svg class="w-5 h-5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-black tracking-wider text-indigo-900">بيانات الإرسالية</h3>
                    </div>
                    <div class="space-y-3">
                        <div >
                            <span class="text-sm font-semibold text-slate-500 text-left">رقم الإرسالية</span>
                            <span class="px-2 py-1 text-sm font-black text-indigo-800 bg-indigo-100/50 rounded-md text-left">{{ $package_number ?? '---' }}</span>    
                        </div>
                        <div >
                            <span class="text-sm font-semibold text-slate-500">فرع الإرسال</span>
                            <span class="font-bold text-slate-800">{{ $package_sender_branch ?? '---' }}</span>
                        </div>
                        <div >
                            <span class="text-sm font-semibold text-slate-500">عدد الطرود الإجمالي</span>
                            <span class="text-lg font-black text-indigo-600">{{ $total_shipments ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- بطاقة بيانات السائق -->
                <div class="relative p-5 rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white shadow-sm">
                    <div class="flex gap-3 items-center mb-4 pb-3 border-b border-slate-200">
                        <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-slate-200">
                            <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-black tracking-wider text-slate-900">بيانات السائق</h3>
                    </div>
                    <div class="space-y-3">
                        <div >
                            <span class="text-sm font-semibold text-slate-500 text-left">اسم السائق</span>
                            <span class="font-black text-slate-800 text-left">{{ $driver_name ?? '---' }}</span>
                        </div>
                        <div >
                            <span class="text-sm font-semibold text-slate-500 text-left">رقم الهاتف</span>
                            <span class="font-bold text-slate-800 text-left">{{ $driver_phone ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipments Table -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
                    <h2 class="text-xl font-black text-slate-900">تفاصيل الطرود المُرحلة</h2>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                    <table class="w-full text-sm text-right bg-white divide-y divide-slate-200">
                        <thead class="bg-slate-50 print:bg-slate-100">
                            <tr>
                                <th class="py-3 px-4 w-12 font-bold text-center text-slate-500">#</th>
                                <th class="py-3 px-4 w-32 font-bold text-slate-500">رقم السند</th>
                                <th class="py-3 px-4 font-bold text-slate-500">المرسل</th>
                                <th class="py-3 px-4 font-bold text-slate-500">المستلم</th>
                                <th class="py-3 px-4 w-32 font-bold text-slate-500">الوجهة</th>
                                <th class="py-3 px-4 w-32 font-bold text-slate-500">النوع / الوزن</th>
                                <th class="py-3 px-4 w-32 font-bold text-slate-500">طريقة الدفع</th>
                                <th class="py-3 px-4 w-28 font-bold text-center text-slate-500">الإجمالي</th>
                                <th class="py-3 px-4 w-24 font-bold text-center text-slate-500">المتبقي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($shipments ?? [] as $shipment)
                                <tr class="transition-colors hover:bg-slate-50/50">
                                    <td class="py-3 px-4 font-bold text-center text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="py-3 px-4 font-black text-slate-700" dir="ltr">{{ $shipment['bond_number'] }}</td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900">{{ $shipment['sender_name'] }}</div>
                                        <div class="text-xs font-semibold text-slate-500 mt-0.5" dir="ltr">{{ $shipment['sender_phone'] }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900">{{ $shipment['receiver_name'] }}</div>
                                        <div class="text-xs font-semibold text-slate-500 mt-0.5" dir="ltr">{{ $shipment['receiver_phone'] }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700">
                                            {{ $shipment['receiver_branch'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-slate-800">{{ $shipment['package_type'] }}</span>
                                        @if (!empty($shipment['weight']))
                                            <div class="text-[11px] font-semibold text-slate-500 mt-0.5">{{ $shipment['weight'] }}</div>
                                        @endif
                                        @if (!empty($shipment['honey_details']))
                                            <div class="text-[11px] font-bold text-amber-600 mt-0.5">{{ $shipment['honey_details'] }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $paymentColors = [
                                                'prepaid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'cod' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'partial_payment' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                'customer_credit' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            ];
                                            $colorClass = $paymentColors[$shipment['payment_key']] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                                        @endphp
                                        <span class="inline-flex justify-center min-w-[80px] items-center px-2 py-1.5 border rounded-lg text-[11px] font-black {{ $colorClass }}">
                                            {{ $shipment['payment_method'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-black text-center text-slate-900" dir="ltr">
                                        {{ $shipment['total_amount'] }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-black {{ $shipment['remaining_amount'] !== '0' ? 'text-rose-600' : 'text-emerald-600' }}" dir="ltr">
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

                    <!-- Summary Area -->
                    @if (!empty($shipments))
                        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-slate-200 bg-slate-50 border-t border-slate-200">
                            
                            {{-- 1. ملخص الطرود --}}
                            <div class="p-4 sm:p-5">
                                <h4 class="mb-3 text-xs font-black uppercase tracking-wider text-slate-500">ملخص الإرسالية</h4>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-500 mb-1">العدد الإجمالي</p>
                                        <p class="text-xl font-black text-slate-900">{{ $total_shipments ?? 0 }}</p>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[11px] font-bold text-slate-500 mb-1">إجمالي المبالغ</p>
                                        <p class="text-xl font-black text-indigo-700" dir="ltr">
                                            @php
                                                $totalAmounts = collect($shipments)->sum(
                                                    fn($s) => (float) str_replace(',', '', $s['total_amount']),
                                                );
                                            @endphp
                                            {{ number_format($totalAmounts, 0) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. التحصيل المالي --}}
                            <div class="p-4 sm:p-5">
                                <h4 class="mb-3 text-xs font-black uppercase tracking-wider text-slate-500">التحصيل المالي</h4>
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[11px] font-bold text-emerald-600 mb-1">المدفوع سلفاً</p>
                                        <p class="text-xl font-black text-emerald-700">
                                            @php $totalPaid = collect($shipments)->sum(fn($s) => (float) str_replace(',', '', $s['partial_amount'])); @endphp
                                            {{ number_format($totalPaid, 0) }}
                                        </p>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[11px] font-bold text-rose-600 mb-1">المتبقي للتحصيل</p>
                                        <p class="text-xl font-black text-rose-700">
                                            {{ number_format($totalAmounts - $totalPaid, 0) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. العمولات --}}
                            <div class="p-4 sm:p-5 bg-emerald-50/50 print:bg-transparent">
                                <h4 class="mb-3 text-xs font-black uppercase tracking-wider text-emerald-800">تفصيل العمولات</h4>
                                <div class="flex justify-between items-end gap-2">
                                    <div>
                                        <p class="text-[11px] font-bold text-emerald-600/80 mb-1">عمولة الطرود</p>
                                        <p class="text-base font-black text-emerald-700">
                                            {{ number_format($totals['package_commission'], 0) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-amber-600/80 mb-1">عمولة العسل</p>
                                        <p class="text-base font-black text-amber-700">
                                            {{ number_format($totals['honey_commission'], 0) }}
                                        </p>
                                    </div>
                                    <div class="text-left pl-2 border-r pr-3 border-emerald-200">
                                        <p class="text-[11px] font-bold text-emerald-800 mb-1">الإجمالي</p>
                                        <p class="text-xl font-black text-emerald-900">
                                            {{ number_format($totals['grand_commission'], 0) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                </div>
            </div>

            <!-- Signatures -->
            <div class="grid grid-cols-3 gap-8 mt-16 mb-8 print:mt-12">
                <div class="text-center">
                    <p class="mb-10 text-sm font-bold text-slate-600">توقيع مسؤول الفرع</p>
                    <div class="mx-auto w-4/5 border-b-2 border-dashed border-slate-300"></div>
                </div>
                <div class="text-center">
                    <p class="mb-10 text-sm font-bold text-slate-600">توقيع السائق</p>
                    <div class="mx-auto w-4/5 border-b-2 border-dashed border-slate-300"></div>
                </div>
                <div class="text-center">
                    <p class="mb-10 text-sm font-bold text-slate-600">ختم الفرع المستلم</p>
                    <div class="mx-auto w-4/5 border-b-2 border-dashed border-slate-300"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 border-t border-slate-200 p-5 text-center sm:rounded-b-[1.5rem]">
            <p class="text-xs font-semibold text-slate-500">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-indigo-700">مُرسَل</span> | 
                بواسطة: <span class="text-slate-700 font-bold">{{ $creator_name ?? 'مسؤول النظام' }}</span> | 
                الطباعة: <span class="text-slate-700 font-bold" dir="ltr">{{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}</span>
            </p>
            
            <div class="pt-3 mt-3 border-t border-slate-200/60 inline-block min-w-[50%]">
                <p class="text-[11px] font-bold text-slate-400">
                    تطوير <span class="text-slate-600">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-2 text-slate-300">|</span>
                    لطلب النظام: <span dir="ltr" class="font-mono text-slate-500">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection