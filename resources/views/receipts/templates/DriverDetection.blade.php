@extends('receipts.layout')

@section('title', 'كشف حمولة السائق - ' . ($driver_name ?? ''))

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
    <div class="bg-gradient-to-l from-slate-50 to-white p-6 sm:p-8 border-b border-slate-100">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <div class="flex items-center gap-4">
                @if(!empty($company['logo']))
                <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center p-2 border border-slate-100">
                    <img src="{{ $company['logo'] }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                @endif
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $company['name'] ?? 'شركة مرسال' }}</h1>
                    <p class="text-slate-500 font-medium text-sm mt-1">{{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="inline-flex items-center justify-center px-4 py-2 bg-amber-50 text-amber-700 rounded-xl font-bold text-sm mb-2 border border-amber-100">
                    {{ $title ?? 'كشف حمولة الرسائل' }}
                </div>
                <div class="text-slate-400 text-xs font-medium flex items-center gap-1.5 justify-end mt-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 sm:p-8">
        <!-- Driver & Package Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <!-- Driver Card -->
            <div class="bg-blue-50/50 rounded-2xl border border-blue-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-blue-600 uppercase tracking-wider">بيانات السائق</h3>
                </div>
                <div class="space-y-2 ">
                    <div class="flex justify-between text-left">
                        <span class="text-slate-500 text-sm text-right">الاسم</span>
                        <span class="text-slate-800 font-black">{{ $driver_name ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between text-left">
                        <span class="text-slate-500 text-sm text-right">الهاتف</span>
                        <span class="text-slate-800 font-bold" dir="ltr">{{ $driver_phone ?? '---' }}</span>
                    </div>
                </div>
            </div>

            <!-- Package Card -->
            <div class="bg-emerald-50/50 rounded-2xl border border-emerald-100 p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-sm font-bold text-emerald-600 uppercase tracking-wider">بيانات الإرسالية</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500 text-sm">رقم الإرسالية</span>
                        <span class="text-slate-800 font-black" dir="ltr">{{ $package_number ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 text-sm">فرع الإرسال</span>
                        <span class="text-slate-800 font-bold">{{ $package_sender_branch ?? '---' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500 text-sm">عدد الطرود</span>
                        <span class="text-slate-800 font-black text-lg">{{ $total_shipments ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipments Table -->
        <div class="mb-8">
            <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-6 rounded-full bg-amber-500"></span>
                تفاصيل الطرود
            </h2>
            
            <div class="rounded-2xl border border-slate-200 overflow-x-auto">
                <table class="w-full text-sm text-right premium-table">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">#</th>
                            <th class="w-24">رقم السند</th>
                            <th>المرسل</th>
                            <th>المستلم</th>
                            <th class="w-28">الوجهة</th>
                            <th class="w-24">نوع الطرد</th>
                            <th class="w-28">طريقة الدفع</th>
                            <th class="w-24 text-center">المبلغ</th>
                            <th class="w-20 text-center">المتبقي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments ?? [] as $shipment)
                            <tr class="transition-colors hover:bg-slate-50">
                                <td class="text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                                <td class="font-bold text-slate-700" dir="ltr">{{ $shipment['bond_number'] }}</td>
                                <td>
                                    <div class="text-slate-800 font-bold">{{ $shipment['sender_name'] }}</div>
                                    <div class="text-xs text-slate-400" dir="ltr">{{ $shipment['sender_phone'] }}</div>
                                </td>
                                <td>
                                    <div class="text-slate-800 font-bold">{{ $shipment['receiver_name'] }}</div>
                                    <div class="text-xs text-slate-400" dir="ltr">{{ $shipment['receiver_phone'] }}</div>
                                </td>
                                <td class="text-slate-600 font-medium">{{ $shipment['receiver_branch'] }}</td>
                                <td>
                                    <span class="text-slate-700 font-bold">{{ $shipment['package_type'] }}</span>
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
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold {{ $colorClass }}">
                                        {{ $shipment['payment_method'] }}
                                    </span>
                                </td>
                                <td class="text-center font-black text-slate-800" dir="ltr">{{ $shipment['total_amount'] }}</td>
                                <td class="text-center font-bold {{ $shipment['remaining_amount'] !== '0' ? 'text-rose-600' : 'text-emerald-600' }}" dir="ltr">
                                    {{ $shipment['remaining_amount'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-slate-400 font-medium bg-slate-50/50">
                                    لا توجد طرود في هذه الإرسالية.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Totals -->
                @if(!empty($shipments))
                <div class="bg-slate-50 border-t border-slate-200 p-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-center divide-x divide-x-reverse divide-slate-200">
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">عدد الطرود</p>
                        <p class="text-slate-800 font-black text-lg">{{ $total_shipments ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">إجمالي المبالغ</p>
                        <p class="text-slate-800 font-black text-lg" dir="ltr">
                            @php
                                $totalAmounts = collect($shipments)->sum(function($s) { return (float) str_replace(',', '', $s['total_amount']); });
                            @endphp
                            {{ number_format($totalAmounts, 0) }} ر.ي
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">المدفوع</p>
                        <p class="text-emerald-600 font-black text-lg" dir="ltr">
                            @php
                                $totalPaid = collect($shipments)->sum(function($s) { return (float) str_replace(',', '', $s['partial_amount']); });
                            @endphp
                            {{ number_format($totalPaid, 0) }} ر.ي
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">المتبقي للتحصيل</p>
                        <p class="text-rose-600 font-black text-lg" dir="ltr">
                            {{ number_format($totalAmounts - $totalPaid, 0) }} ر.ي
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-3 gap-8 mt-12 mb-4">
            <div class="text-center">
                <p class="text-sm font-bold text-slate-500 mb-8">توقيع مسؤول الفرع</p>
                <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-slate-500 mb-8">توقيع السائق</p>
                <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-slate-500 mb-8">ختم الفرع</p>
                <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="bg-slate-800 text-slate-400 p-4 text-center text-xs font-medium rounded-b-[2rem]">
        تم الإنشاء إلكترونياً عبر نظام {{ $company['name'] ?? 'مرسال' }} | بواسطة: {{ $creator_name ?? 'مسؤول النظام' }}
    </div>
</div>
@endsection
