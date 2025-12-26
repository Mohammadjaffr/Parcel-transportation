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

    <div class="grid grid-cols-1 gap-6 mb-6 xl:grid-cols-1">

        {{-- ===== بطاقات الإحصائيات ===== --}}
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">

            {{-- بطاقة: المسجلة اليوم --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
                <div class="flex justify-center items-center w-12 h-12 bg-gray-100 rounded-lg dark:bg-gray-800">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        class="text-brand-500 dark:text-brand-400" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="14" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">عدد الطرود المسجلة اليوم</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $todayShipments }}</h4>
                </div>
            </div>

            {{-- بطاقة: في الطريق --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
                <div class="flex justify-center items-center w-12 h-12 rounded-lg bg-warning-50 dark:bg-warning-500/10">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24"
                        class="text-warning-600 dark:text-warning-400" stroke="currentColor" stroke-width="2">
                        <path d="M3 13l2.3-6a2 2 0 0 1 1.9-1.4h10.6a2 2 0 0 1 1.9 1.4l2.3 6"></path>
                        <circle cx="7" cy="17" r="2"></circle>
                        <circle cx="17" cy="17" r="2"></circle>
                    </svg>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">عدد الطرود في الطريق</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $inTransit }}</h4>
                </div>
            </div>

            {{-- بطاقة: المستلمة --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
                <div class="flex justify-center items-center w-12 h-12 rounded-lg bg-success-50 dark:bg-success-500/10">
                    <svg fill="none" width="24" height="24" viewBox="0 0 24 24"
                        class="text-success-600 dark:text-success-400" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">عدد الطرود التي تم استلامها</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $delivered }}</h4>
                </div>
            </div>

            {{-- بطاقة: الإيرادات --}}
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
                <div class="flex justify-center items-center w-12 h-12 bg-blue-50 rounded-lg dark:bg-blue-500/10">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24"
                        class="text-blue-600 dark:text-blue-400" stroke="currentColor" stroke-width="2">
                        <path d="M12 1v22M5 6h14M5 18h14"></path>
                    </svg>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">إيرادات COD المحصلة</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($revenueCOD, 2) }}
                        <span class="text-base font-normal text-gray-500 dark:text-gray-400">ر.ي</span></h4>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ===== جدول آخر 24 ساعة ===== --}}
            <div
                class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                        الطرود خلال <span class="text-brand-500">24</span> ساعة
                    </h3>
                    <a href="{{ route('request.index') }}"
                        class="text-sm font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-300">
                        عرض الكل &larr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-center align-middle">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr class="text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400">
                                <th class="px-3 py-3 text-right">المعرف</th>
                                <th class="px-3 py-3 text-right">العميل</th>
                                <th class="px-3 py-3">المبلغ</th>
                                <th class="px-3 py-3">الحالة</th>
                                <th class="px-3 py-3">من / إلى</th>
                                <th class="px-3 py-3"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($last24Shipments as $shipment)
                                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-3 py-4 text-sm font-medium text-right text-gray-900 dark:text-white">
                                        #{{ $shipment->bond_number }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-right text-gray-700 dark:text-gray-300">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '-') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ optional($shipment->driver)->name ?? 'لم يعين' }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm font-bold text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ number_format($shipment->total_amount, 2) }}
                                        <span class="text-xs font-normal text-gray-500">ر.ي</span>
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        @php
                                            $statusClasses = [
                                                'pending' =>
                                                    'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
                                                'in_transit' =>
                                                    'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
                                                'delivered' =>
                                                    'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400',
                                                'returned' =>
                                                    'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400',
                                                'cancelled' =>
                                                    'bg-gray-100 text-gray-600 dark:bg-gray-500/10 dark:text-gray-400',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'انتظار',
                                                'in_transit' => 'جاري التوصيل',
                                                'delivered' => 'تم التسليم',
                                                'returned' => 'مرتجع',
                                                'cancelled' => 'ملغي',
                                            ];
                                            $statusKey = $shipment->status;
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClasses[$statusKey] ?? 'bg-gray-100 text-gray-500' }}">
                                            {{ $statusLabels[$statusKey] ?? $statusKey }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-sm">
                                        <div class="flex gap-2 justify-center items-center">
                                            <span
                                                class="px-2 py-0.5 text-xs font-medium text-gray-600 bg-gray-100 rounded dark:bg-gray-700 dark:text-gray-300">
                                                {{ $shipment->senderBranch->name ?? $shipment->sender_branch_code }}
                                            </span>
                                            <svg class="w-3 h-3 text-gray-400 rtl:rotate-180" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                            <span
                                                class="px-2 py-0.5 text-xs font-medium rounded text-brand-600 bg-brand-50 dark:bg-brand-500/10 dark:text-brand-400">
                                                {{ $shipment->receiverBranch->name ?? $shipment->receiver_branch_code }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-left">
                                        <a href="{{ route('request.show', $shipment->id) }}"
                                            class="p-2 text-gray-500 rounded-lg transition-colors hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                                            title="عرض التفاصيل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col justify-center items-center">
                                            <svg class="mb-3 w-12 h-12 text-gray-300 dark:text-gray-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                </path>
                                            </svg>
                                            <p>لا توجد شحنات مسجلة خلال 24 ساعة الماضية</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== المخطط البياني ===== --}}
            <div
                class="lg:col-span-1 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-bold text-gray-800 dark:text-white">
                    الإيرادات الشهرية
                </h3>
                <div id="chartOne" class="w-full h-80"></div>
                <div class="flex justify-between items-center mt-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>إجمالي السنة:</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ number_format(array_sum($monthlySales), 2) }}
                        ر.ي</span>
                </div>
            </div>

        </div>

    </div>

@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const options = {
                chart: {
                    type: "bar",
                    height: 320,
                    fontFamily: 'inherit',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                    name: "الإيرادات",
                    data: @json(array_values($monthlySales))
                }],
                xaxis: {
                    categories: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس',
                        'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
                    ],
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#9ca3af',
                            fontSize: '11px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#9ca3af',
                            fontSize: '11px',
                        },
                        formatter: (value) => {
                            return value >= 1000 ? (value / 1000).toFixed(1) + 'k' : value
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: "50%",
                        colors: {
                            backgroundBarColors: ['#f3f4f6'],
                            backgroundBarOpacity: 0.5,
                        }
                    }
                },
                colors: ["#dc6803"],
                dataLabels: {
                    enabled: false
                },
                grid: {
                    strokeDashArray: 4,
                    borderColor: "#e5e7eb",
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    }
                },
                theme: {
                    mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    y: {
                        formatter: function(val) {
                            return val + " ر.ي";
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#chartOne"), options);
            chart.render();
        });
    </script>
@endsection
