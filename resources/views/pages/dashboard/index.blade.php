@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'الصفحة الرئيسية')

@section('style')
    <style>
        .chart-container {
            width: 100%;
        }
    </style>
@endsection

@section('content')

    {{-- ===== التصميم الرئيسي للوحة التحكم ===== --}}

    <div class="grid grid-cols-1 xl:grid-cols-1 gap-4 mb-6">

        {{-- ===== بطاقات الإحصائيات ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            {{-- بطاقة: المسجلة اليوم --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc6803" stroke-width="2">
                        <rect x="3" y="3" width="18" height="14" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-xs text-gray-500 dark:text-gray-400">عدد الطرود المسجلة اليوم</span>
                    <h4 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $todayShipments }}</h4>
                </div>
            </div>

            {{-- بطاقة: في الطريق --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#dc6803" stroke-width="2">
                        <path d="M3 13l2.3-6a2 2 0 0 1 1.9-1.4h10.6a2 2 0 0 1 1.9 1.4l2.3 6"></path>
                        <circle cx="7" cy="17" r="2"></circle>
                        <circle cx="17" cy="17" r="2"></circle>
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-xs text-gray-500 dark:text-gray-400">عدد الطرود في الطريق</span>
                    <h4 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $inTransit }}</h4>
                </div>
            </div>

            {{-- بطاقة: المستلمة --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg fill="none" width="22" height="22" viewBox="0 0 24 24" stroke="#dc6803" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-xs text-gray-500 dark:text-gray-400">عدد الطرود التي تم استلامها</span>
                    <h4 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $delivered }}</h4>
                </div>
            </div>

            {{-- بطاقة: الإيرادات --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#dc6803" stroke-width="2">
                        <path d="M12 1v22M5 6h14M5 18h14"></path>
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إيرادات COD المحصلة</span>
                    <h4 class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ number_format($revenueCOD, 2) }} ر.ي
                    </h4>
                </div>
            </div>

        </div>
        {{-- ===== جدول آخر 24 ساعة ===== --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    الطرود خلال <span class="text-warning-500">24</span> ساعة
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-center">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/40 text-xs text-gray-500 dark:text-gray-300">
                            <th class="py-3 px-2">#</th>
                            <th class="py-3 px-2">السائق</th>
                            <th class="py-3 px-2">العميل</th>
                            <th class="py-3 px-2">السعر</th>
                            <th class="py-3 px-2">الحالة</th>
                            <th class="py-3 px-2">الدفع</th>
                            <th class="py-3 px-2">من → إلى</th>
                            <th class="py-3 px-2">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-white/80">

                        @forelse ($last24Shipments as $shipment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                <td class="py-3 px-2 font-medium">{{ $shipment->id }}</td>

                                <td class="py-3 px-2">{{ optional($shipment->driver)->name ?? '--' }}</td>

                                <td class="py-3 px-2">{{ $shipment->receiver_name }}</td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-warning-600 dark:text-warning-400">
                                {{ $shipment->cod_amount ? number_format($shipment->cod_amount, 2) . ' ر.ي' : '0.00 ر.ي' }}
                                </td>

                                {{-- حالة الطلب --}}
                                <td class="py-3 px-2">
                                    @php
                                        $statusColors = [
                                            'deliverd' =>
                                                'rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500',
                                            'in_transit' =>
                                                'rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500',
                                            'pending' =>
                                                'rounded-full bg-brand-50 px-2 py-0.5 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-500',
                                            'cancelled' =>
                                                'rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500',
                                        ];
                                        $translatedStatus =
                                            [
                                                'deliverd' => 'تم التسليم',
                                                'pending' => 'قيد الانتظار',
                                                'in_transit' => 'في الطريق',
                                                'cancelled' => 'ملغي',
                                            ][$shipment->status] ?? $shipment->status;
                                    @endphp

                                    <span class=" {{ $statusColors[$shipment->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $translatedStatus }}
                                    </span>
                                </td>

                                {{-- حالة الدفع --}}
                                <td class="py-3 px-2">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $shipment->payment_method == 'cod' ? 'rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500' : 'rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500' }}">
                                        {{ $shipment->payment_method == 'cod' ? 'آجل' : 'نقد' }}
                                    </span>
                                </td>

                                {{-- من – إلى --}}
                               <td class="py-3">
                                <div class="flex items-center justify-center space-x-2">
                                    <p
                                        class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                        {{ $shipment->from_city }}
                                    </p>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                    <p
                                        class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                        {{ $shipment->to_city }}
                                    </p>
                                </div>
                            </td>

                                {{-- زر التفاصيل --}}
                                <td class="py-3 px-2">
                                    <a href="{{ route('request.show', $shipment->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-xs hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12A9 9 0 1112 3a9 9 0 010 18z">
                                            </path>
                                        </svg>
                                        تفاصيل
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-gray-500 dark:text-gray-400">
                                    لا توجد شحنات خلال آخر 24 ساعة
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>

        {{-- ===== المخطط البياني ===== --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                الإيرادات خلال <span class="text-warning-500">سنة</span>
            </h3>
            <div id="chartOne" class="mt-4 w-full h-64"></div>
        </div>

    </div>



@endsection


@section('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        new ApexCharts(document.querySelector("#chartOne"), {
            chart: {
                type: "bar",
                height: 240,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: "الإيرادات",
                data: @json(array_values($monthlySales))
            }],
            xaxis: {
                categories: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر',
                    'أكتوبر', 'نوفمبر', 'ديسمبر'
                ],
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: "35%"
                }
            },
            colors: ["#dc6803"],
            dataLabels: {
                enabled: false
            },
            grid: {
                strokeDashArray: 4,
                borderColor: "#e5e7eb"
            }
        }).render();
    </script>
@endsection
