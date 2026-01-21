@extends('layouts.app')
@section('title', 'تفاصيل رحلة الشحن #' . $package->tracking_number)

@section('content')
    <div class="flex gap-2 justify-end my-4 w-full">
        <a href="{{ route('shipmentpackage.index') }}"
            class="flex gap-3 justify-center items-center px-3 h-12 text-sm font-black bg-white rounded-2xl border border-gray-200 shadow-sm transition-all duration-300 text-brand-500 group dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 hover:shadow-md hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-brand-500 dark:hover:text-brand-400 active:scale-95">

            <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none"
                stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>

            <span>العودة لقائمة الشحنات</span>
        </a>
    </div>
    <div class="space-y-6 font-outfit" dir="rtl">

        <div
            class="bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="flex flex-col gap-6 justify-between items-start md:flex-row md:items-center">
                <div class="flex gap-5 items-center">
                    <div
                        class="flex justify-center items-center w-16 h-16 text-white rounded-2xl shadow-xl bg-brand-500 shadow-brand-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1m-6 0a1 1 0 001-1m-6 0H4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex gap-3 items-center">
                            <h2 class="text-2xl font-black text-brand-500 bg-brand-50 dark:text-white">رحلة رقم:
                                {{ $package->tracking_number }}</h2>
                            @php
                                $statusColors = [
                                    'pending' =>
                                        'bg-warning-50 text-warning-500 border-warning-200 dark:bg-warning-500/10 dark:text-warning-400 dark:border-warning-500/20',
                                    'in_transit' =>
                                        'bg-blue-light-500 shadow-blue-500/20 text-white border-blue-light-200 dark:bg-blue-light-500/10 dark:text-blue-light-400 dark:border-blue-light-500/20',
                                    'delivered' =>
                                        'bg-success-50 text-success-500 border-success-200 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/20',
                                    'cancelled' =>
                                        'bg-error-50 text-error-500 border-error-200 dark:bg-error-500/10 dark:text-error-400 dark:border-error-500/20',
                                    'returned' =>
                                        'bg-gray-50 text-gray-500 border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20',
                                ];
                                $statusText = [
                                    'pending' => 'قيد الانتظار',
                                    'in_transit' => 'في الطريق',
                                    'delivered' => 'تم التسليم',
                                ];
                            @endphp
                            <span
                                class="px-3 py-1 {{ $statusColors[$package->shipments->first()->status] }} rounded-lg text-[10px] font-black uppercase tracking-widest animate-pulse">{{ $statusText[$package->shipments->first()->status] }}</span>
                        </div>
                        <p class="mt-1 text-sm font-bold tracking-tighter text-gray-500 uppercase">تاريخ الإنشاء:
                            {{ $package->created_at->format('Y-m-d H:i') }}</p>
                    </div>

                </div>

                <div class="flex flex-wrap gap-4 w-full md:w-auto">
                    <div
                        class="flex-1 px-6 py-3 text-center rounded-2xl border border-gray-100 bg-brand-50 md:flex-none dark:bg-gray-800 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">إجمالي الطرود</p>
                        <p class="text-xl font-black text-brand-500">{{ $package->shipments->count() }}</p>
                    </div>
                    <div
                        class="flex-1 px-6 py-3 text-center rounded-2xl border border-gray-100 bg-brand-50 md:flex-none dark:bg-gray-800 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400 uppercase mb-1">إجمالي المبالغ</p>
                        <p class="text-xl font-black text-brand-500 dark:text-white">
                            {{ number_format($package->shipments->sum('total_amount')) }} <small class="text-xs">ر.ي</small>
                        </p>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 gap-4 pt-6 mt-8 border-gray-50 md:grid-cols-2 dark:border-gray-800">
                <div
                    class="flex gap-4 items-center p-4 rounded-2xl border bg-brand-50/50 dark:bg-brand-500/5 border-brand-100/50">
                    <div class="flex justify-center items-center w-10 h-10 text-white rounded-full bg-brand-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                stroke-width="2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-brand-500 uppercase">اسم السائق المسؤول</p>
                        <p class="text-base font-black text-gray-900 dark:text-white">{{ $package->driver_name }}</p>
                    </div>
                </div>
                <div
                    class="flex gap-4 items-center p-4 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                    <div
                        class="flex justify-center items-center w-10 h-10 text-white rounded-full bg-success-500 dark:bg-gray-700 dark:text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                stroke-width="2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-success-500 uppercase">رقم التواصل</p>
                        <p class="font-mono text-base font-black text-gray-900 dark:text-white">{{ $package->driver_phone }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between items-center px-4 my-4">
                <h3 class="text-lg font-black tracking-widest text-gray-900 uppercase dark:text-white">قائمة الطرود الملحقة
                </h3>
                <a href="{{ route('shipmentpackage.printD', $package->id) }}" target="_blank"
                    class="flex gap-2 items-center px-6 h-12 font-black text-white rounded-xl shadow-lg transition-all bg-success-500 hover:bg-black active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    طباعة كشف الحمولة للفرع
                </a>
                <a href="{{ route('shipmentpackage.print', $package->id) }}" target="_blank"
                    class="flex gap-2 items-center px-6 h-12 font-black text-white rounded-xl shadow-lg transition-all bg-brand-500 hover:bg-black active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    طباعة كشف الحمولة لسائق
                </a>

            </div>


            <div
                class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
                <div class="overflow-x-auto px-4 pb-4">
                    <table class="w-full text-right border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                                <th class="px-6 py-4">رقم السند</th>
                                <th class="px-6 py-4">الأطراف (مرسل/مستلم)</th>
                                <th class="px-6 py-4 text-center">الوجهة</th>
                                <th class="px-6 py-4 text-center">النوع</th>
                                <th class="px-6 py-4 text-left">التكلفة</th>
                                <th class="px-6 py-4 text-left">الحالة</th>
                                <th class="px-6 py-4 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0">
                            @foreach ($package->shipments as $shipment)
                                <tr
                                    class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800 group">
                                    <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                        <span
                                            class="px-3 py-1.5 text-xs font-black bg-gray-50 rounded-lg border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                                            #{{ $shipment->bond_number }}
                                        </span>
                                    </td>

                                    <td
                                        class="py-5 px-6 border-y dark:border-gray-800/50 text-center text-[10px] font-black uppercase text-gray-500">
                                        {{ $shipment->senderCustomer->name ?? '-' }} ⇠
                                        {{ $shipment->receiverCustomer->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                        <span
                                            class="px-3 py-1 bg-brand-50 dark:bg-brand-500/10 text-brand-500 rounded-lg text-[10px] font-black uppercase">
                                            {{ $shipment->receiverBranch->name }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                        <span
                                            class="text-[10px] font-black text-gray-500 uppercase">{{ $shipment->package_type }}</span>
                                    </td>

                                    <td class="px-6 py-5 text-left border-y dark:border-gray-800/50">
                                        <span class="text-base font-black text-gray-900 dark:text-white">
                                            {{ number_format($shipment->total_amount) }}
                                            <small class="text-[10px] font-bold text-gray-400 mr-0.5 uppercase">ر.ي</small>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                        @php
                                            $colors = [
                                                'pending' => 'bg-warning-500 shadow-warning-500/20',
                                                'in_transit' => 'bg-blue-light-500 shadow-blue-500/20',
                                                'delivered' => 'bg-success-500 shadow-success-500/20',
                                            ];
                                            $labels = [
                                                'pending' => 'قيد الانتظار',
                                                'in_transit' => 'في الطريق',
                                                'delivered' => 'تم التسليم',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-lg text-[10px] font-black text-white uppercase shadow-lg {{ $colors[$shipment->status] ?? 'bg-gray-500' }}">
                                            {{ $labels[$shipment->status] ?? $shipment->status }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-6 py-5 text-center border-l last:rounded-l-2xl border-y dark:border-gray-800/50">
                                        <a href="{{ route('shipment.show', $shipment->id) }}"
                                            class="inline-flex p-2 text-gray-400 rounded-xl transition-all hover:text-brand-500 hover:bg-brand-50" title="عرض تفاصيل الطرد">
                                            <svg class="w-5 h-5 text-gray-400 transition-colors group-hover:text-brand-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>  
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
@endsection
