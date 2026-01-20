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

    <div class="grid grid-cols-1 gap-6 mb-6 xl:grid-cols-1" x-data="{ filterStatus: 'all' }">

        {{-- ===== بطاقات الإحصائيات ===== --}}
        <div class="flex gap-6 mb-6">

            {{-- بطاقة: المسجلة اليوم (الكل) --}}
            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-xl dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">المسجلة
                        اليوم</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $todayShipments }}</h4>
                </div>
            </div>

            {{-- بطاقة: في الطريق --}}
            <div @click="filterStatus = 'in_transit'"
                :class="filterStatus === 'in_transit' ? 'border-blue-light-500 ring-2 ring-blue-light-500/20' :
                    'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-blue-light-50 dark:bg-blue-light-500/10 text-blue-light-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">في
                        الطريق</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $inTransit }}</h4>
                </div>
            </div>

            {{-- بطاقة: المستلمة --}}
            <div @click="filterStatus = 'delivered'"
                :class="filterStatus === 'delivered' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">تم
                        التسليم</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $delivered }}</h4>
                </div>
            </div>

            {{-- بطاقة: الإيرادات --}}
            <div
                class="flex-1 relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.67 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.67-1" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إيرادات
                        COD</span>
                    <h4 class="text-xl font-black dark:text-white">{{ number_format($revenueCOD) }}
                        <small class="text-[10px] font-bold text-gray-400 mr-0.5">ر.ي</small>
                    </h4>
                </div>
            </div>

        </div>


        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ===== جدول آخر 24 ساعة ===== --}}
            <div
                class="flex overflow-hidden flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-xl lg:col-span-2 shadow-gray-200/50 dark:border-gray-800 dark:bg-gray-900 dark:shadow-none">

                {{-- Header --}}
                <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex gap-3 items-center">
                        <div class="p-2 rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/10 dark:text-brand-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            الطرود خلال <span class="text-brand-500">24</span> ساعة
                        </h3>
                    </div>
                    <a href="{{ route('shipment.index') }}"
                        class="flex gap-1 items-center text-sm font-medium text-gray-500 transition-colors group hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-400">
                        عرض الكل
                        <svg class="w-4 h-4 transition-transform text-brand-500 group-hover:-translate-x-1 rtl:rotate-180"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                        </svg>
                    </a>
                </div>

                {{-- Table Container --}}
                <div class="overflow-x-auto flex-1">
                    <table class="min-w-full text-center align-middle">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                            <tr
                                class="text-xs font-bold tracking-wider text-gray-400 uppercase border-b border-gray-100 dark:border-gray-800">
                                <th class="px-6 py-4 text-right">رقم السند</th>
                                <th class="px-6 py-4 text-right">العميل</th>
                                <th class="px-6 py-4">المسار</th>
                                <th class="px-6 py-4">الحالة</th>
                                <th class="px-6 py-4 text-left">المبلغ</th>
                                <th class="px-4 py-4"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse ($last24Shipments as $shipment)
                                <tr class="transition-all group hover:bg-gray-50 dark:hover:bg-gray-800/50"
                                    x-show="filterStatus === 'all' || filterStatus === '{{ $shipment->status }}'"
                                    x-transition>
                                    {{-- ID --}}
                                    <td class="px-6 py-4 text-sm text-right">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 font-mono text-xs font-medium text-gray-500 rounded-md bg-brand-50 dark:bg-gray-800 dark:text-gray-300">
                                            {{ $shipment->bond_number }}
                                        </span>
                                    </td>

                                    {{-- Customer --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex gap-3 items-center">
                                            {{-- Avatar Placeholder --}}
                                            <div
                                                class="flex justify-center items-center w-8 h-8 text-xs font-bold text-white rounded-full bg-brand-500 from-brand-50 to-brand-500 dark:from-brand-900 dark:to-brand-800 dark:text-brand-300">
                                                {{ mb_substr($shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '?'), 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ Str::limit($shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '-'), 20) }}
                                                </span>
                                                <span class="flex gap-1 items-center text-xs text-brand-500">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    {{ optional($shipment->package)->driver_name ?? 'لم يعين' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Route (From -> To) --}}
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 justify-center items-center text-xs">
                                            <div
                                                class="flex gap-2 items-center px-2 py-1 text-gray-500 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                                                <div class="w-2 h-2 rounded-full bg-brand-500"></div>
                                                {{ $shipment->senderBranch->name ?? $shipment->sender_branch_code }}
                                            </div>

                                            {{-- The Arrow (Using the rotate logic) --}}
                                            <svg class="w-3 h-3 text-gray-300 rotate-180 rtl:rotate-0" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>

                                            <div
                                                class="flex gap-2 items-center px-2 py-1 rounded-2xl border bg-brand-50 text-brand-700 border-brand-100 dark:bg-brand-900/20 dark:border-brand-800 dark:text-brand-300">
                                                <div class="w-2 h-2 rounded-full bg-brand-500"></div>
                                                {{ $shipment->receiverBranch->name ?? $shipment->receiver_branch_code }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4">
                                        @php
                                            $statusConfig = [
                                                'pending' => [
                                                    'bg' => 'bg-warning-100',
                                                    'text' => 'text-warning-700',
                                                    'label' => 'انتظار',
                                                    'dot' => 'bg-warning-500',
                                                ],
                                                'in_transit' => [
                                                    'bg' => 'bg-blue-light-100',
                                                    'text' => 'text-blue-light-700',
                                                    'label' => 'جاري التوصيل',
                                                    'dot' => 'bg-blue-light-500',
                                                ],
                                                'delivered' => [
                                                    'bg' => 'bg-success-100',
                                                    'text' => 'text-success-700',
                                                    'label' => 'تم التسليم',
                                                    'dot' => 'bg-success-500',
                                                ],
                                                'returned' => [
                                                    'bg' => 'bg-error-100',
                                                    'text' => 'text-error-700',
                                                    'label' => 'مرتجع',
                                                    'dot' => 'bg-error-500',
                                                ],
                                                'cancelled' => [
                                                    'bg' => 'bg-error-100',
                                                    'text' => 'text-error-700',
                                                    'label' => 'ملغي',
                                                    'dot' => 'bg-error-500',
                                                ],
                                            ];
                                            $currentStatus =
                                                $statusConfig[$shipment->status] ?? $statusConfig['cancelled'];
                                        @endphp
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} dark:bg-opacity-10">
                                            <span class="w-2 h-2 rounded-full {{ $currentStatus['dot'] }}"></span>
                                            {{ $currentStatus['label'] }}
                                        </span>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="px-6 py-4 text-left">
                                        <div class="font-mono text-sm font-bold text-brand-500 dark:text-white">
                                            {{ number_format($shipment->total_amount, 2) }}
                                            <span class="font-sans text-xs font-normal text-gray-400">ر.ي</span>
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-4 py-4 text-right">
                                        <a href="{{ route('shipment.show', $shipment->id) }}"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400"
                                            title="عرض التفاصيل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="p-4 mb-3 bg-gray-50 rounded-full dark:bg-gray-800">
                                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">لا توجد بيانات</h4>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">لم يتم تسجيل شحنات
                                                خلال الـ 24 ساعة الماضية</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- المخطط البياني (متروك كما هو في حال أردت تفعيله) --}}
            {{-- <div class="lg:col-span-1 ..."> ... </div> --}}

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
