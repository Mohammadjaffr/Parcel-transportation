@extends('receipts.layout')

@section('title', 'كشف إرساليات المكتب - ' . ($branchName ?? ''))

@push('styles')
<style>
    @media print {
        @page { size: A4 landscape; }
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">
    
    <!-- Header -->
    <div class="p-6 bg-gradient-to-l to-white border-b from-slate-50 sm:p-8 border-slate-100">
        <div class="flex flex-col gap-6 justify-between items-start sm:flex-row sm:items-center">
            <div class="flex gap-4 items-center">
                @if(!empty($company['logo']))
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
                <div class="inline-flex justify-center items-center px-4 py-2 mb-2 text-sm font-bold text-amber-700 bg-amber-50 rounded-xl border border-amber-100">
                    {{ $title ?? 'كشف حمولة الرسائل' }}
                </div>
                <div class="flex gap-1.5 justify-end items-center mt-1 text-xs font-medium text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 sm:p-8">
        <!-- Driver & Package Info -->
        <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2">
            <!-- Driver Card -->
            <div class="p-5 rounded-2xl border border-blue-100 bg-blue-50/50">
                <div class="flex gap-3 items-center mb-3">
                    <div class="flex justify-center items-center w-10 h-10 bg-blue-100 rounded-xl">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold tracking-wider text-blue-600 uppercase">بيانات السائق</h3>
                </div>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-right text-slate-500">الاسم</span>
                        <span class="font-black text-slate-800">: {{ $driver_name ?? '---' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-right text-slate-500">الهاتف</span>
                        <span class="font-bold text-slate-800" >: {{ $driver_phone ?? '---' }}</span>
                    </div>
                </div>
            </div>

            <!-- Package Card -->
            <div class="p-5 rounded-2xl border border-emerald-100 bg-emerald-50/50">
                <div class="flex gap-3 items-center mb-3">
                    <div class="flex justify-center items-center w-10 h-10 bg-emerald-100 rounded-xl">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold tracking-wider text-emerald-600 uppercase">بيانات الإرسالية</h3>
                </div>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-slate-500">رقم الإرسالية</span>
                        <span class="font-black text-slate-800">: {{ $package_number ?? '---' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">الفرع المسلم للارساليه</span>
                        <span class="font-bold text-slate-800">: {{ $package_sender_branch ?? '---' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">المكتب المستلم للارساليه</span>
                        <span class="font-bold text-slate-800">: {{ $package_receiver_office ?? '---' }}</span>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">رقم التواصل</span>
                        <span class="font-bold text-slate-800" dir="ltr"> {{ $package_receiver_phone ?? '---' }}:</span>
                    </div>
                    <div>
                        <span class="text-sm text-slate-500">عدد الطرود</span>
                        <span class="text-lg font-black text-slate-800">: {{ $total_shipments ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipments Table -->
        <div class="mb-8">
            <h2 class="flex gap-2 items-center mb-4 text-lg font-black text-slate-800">
                <span class="w-2 h-6 bg-amber-500 rounded-full"></span>
                تفاصيل الطرود
            </h2>
            
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm text-right premium-table">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">#</th>
                            <th class="w-25">رقم السند</th>
                            <th>المرسل</th>
                            <th>المستلم</th>
                            <th class="w-27">الوجهة</th>
                            <th class="w-24">نوع الطرد</th>
                            <th class="w-28">طريقة الدفع</th>
                            <th class="w-24 text-center">المبلغ</th>
                            <th class="w-24 text-center">المدفوع</th>
                            <th class="w-24 text-center">المتبقي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments ?? [] as $shipment)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="font-bold text-center text-slate-400">{{ $loop->iteration }}</td>
                                <td class="p-2 font-bold text-slate-700" dir="ltr">{{ $shipment['bond_number'] }}</td>
                                <td>
                                    <div class="font-bold text-slate-800">{{ $shipment['sender_name'] }}</div>
                                    <div class="text-xs text-slate-400" dir="ltr">{{ $shipment['sender_phone'] }}</div>
                                </td>
                                <td>
                                    <div class="font-bold text-slate-800">{{ $shipment['receiver_name'] }}</div>
                                    <div class="text-xs text-slate-400" dir="ltr">{{ $shipment['receiver_phone'] }}</div>
                                </td>
                                <td class="font-medium text-slate-600">{{ $shipment['receiver_branch'] }}</td>
                                <td>
                                    <span class="font-bold text-slate-700">{{ $shipment['package_type'] }}</span>
                                    @if(!empty($shipment['weight']))
                                        <div class="text-xs text-slate-400">{{ $shipment['weight'] }}</div>
                                    @endif
                                    @if(!empty($shipment['honey_details']))
                                        <div class="text-xs text-amber-600">{{ $shipment['honey_details'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $paymentColors = [
                                            'prepaid' => 'bg-emerald-50 text-emerald-700',
                                            'cod' => 'bg-blue-50 text-blue-700',
                                            'partial_payment' => 'bg-amber-50 text-amber-700',
                                            'customer_credit' => 'bg-rose-50 text-rose-700',
                                        ];
                                        $colorClass = $paymentColors[$shipment['payment_key']] ?? 'bg-slate-50 text-slate-700';
                                    @endphp
                                    <span class="inline-flex items-center px-1 py-1 rounded-lg text-xs font-bold {{ $colorClass }}">
                                        {{ $shipment['payment_method'] }}
                                    </span>
                                </td>
                                <td class="font-black text-center text-slate-800" dir="ltr">{{ $shipment['total_amount'] }}</td>
                                <td class="font-bold text-center text-emerald-600" dir="ltr">{{ $shipment['paid_amount'] }}</td>
                                <td class="text-center font-bold {{ $shipment['remaining_amount'] !== '0' ? 'text-rose-600' : 'text-slate-400' }}" dir="ltr">
                                    {{ $shipment['remaining_amount'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="p-8 font-medium text-center text-slate-400 bg-slate-50/50">
                                    لا توجد طرود في هذه الإرسالية.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Totals -->
                @if(!empty($shipments))
                <div class="grid grid-cols-2 gap-4 p-4 text-center border-t divide-x divide-x-reverse bg-slate-50 border-slate-200 sm:grid-cols-4 divide-slate-200">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase text-slate-400">عدد الطرود</p>
                        <p class="text-lg font-black text-slate-800">{{ $total_shipments ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي المبالغ</p>
                        <p class="text-lg font-black text-slate-800" dir="ltr">
                            @php
                                $totalAmounts = collect($shipments)->sum('raw_total_amount');
                            @endphp
                            {{ number_format($totalAmounts, 0) }} ر.ي
                        </p>
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase text-slate-400">المدفوع</p>
                        <p class="text-lg font-black text-emerald-600" dir="ltr">
                            @php
                                $totalPaid = collect($shipments)->sum('raw_paid_amount');
                            @endphp
                            {{ number_format($totalPaid, 0) }} ر.ي
                        </p>
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase text-slate-400">المتبقي للتحصيل</p>
                        <p class="text-lg font-black text-rose-600" dir="ltr">
                            @php
                                $totalRemaining = collect($shipments)->sum('raw_remaining_amount');
                            @endphp
                            {{ number_format($totalRemaining, 0) }} ر.ي
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-3 gap-8 mt-12 mb-4">
            <div class="text-center">
                <p class="mb-8 text-sm font-bold text-slate-500">توقيع مسؤول الفرع</p>
                <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
            </div>
            <div class="text-center">
                <p class="mb-8 text-sm font-bold text-slate-500">توقيع السائق</p>
                <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
            </div>
            <div class="text-center">
                <p class="mb-8 text-sm font-bold text-slate-500">ختم الفرع</p>
                <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
        {{-- بيانات الفاتورة --}}
        <p class="text-xs font-medium text-slate-300">
            تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
            {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة:
            {{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}
        </p>

        {{-- الخط الفاصل التسويقي لشركة تيار --}}
        <div class="pt-3 mt-3 border-t border-slate-700/50">
            <p class="text-[10px] font-bold text-slate-500">
                تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                <span class="mx-1">|</span>
                لطلب النظام: <span dir="ltr" class="font-mono text-slate-400">+967 780 261 952</span>
            </p>
        </div>
    </div>
</div>
@endsection
