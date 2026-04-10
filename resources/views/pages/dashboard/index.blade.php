@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'الصفحة الرئيسية')

@section('style')
    <style>
        .chart-container {
            width: 100%;
            min-height: 320px;
        }
        /* تعديل الـ Tooltip ليتوافق مع الـ Dark Mode */
        .apexcharts-tooltip {
            background: #ffffff !important;
            border: 1px solid #f3f4f6 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            color: #1f2937 !important;
        }
        .dark .apexcharts-tooltip {
            background: #1f2937 !important;
            border: 1px solid #374151 !important;
            color: #f3f4f6 !important;
        }
        .dark .apexcharts-tooltip-title {
            background: #111827 !important;
            border-bottom: 1px solid #374151 !important;
        }
    </style>
@endsection

@section('content')

    {{-- ===== التصميم الرئيسي للوحة التحكم ===== --}}
    <div class="mb-6 space-y-6 font-outfit" dir="rtl" x-data="{ filterStatus: 'all' }">

        {{-- ===== بطاقات الإحصائيات التفاعلية ===== --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

            {{-- 1. المسجلة اليوم (الكل) --}}
            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-gray-800'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border transition-all cursor-pointer dark:bg-boxdark hover:shadow-md shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        الطرود اليوم
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-gray-900 dark:text-white">{{ $todayShipments ?? 0 }}</h4>
                </div>
            </div>

            {{-- 2. في الطريق --}}
            <div @click="filterStatus = 'in_transit'"
                :class="filterStatus === 'in_transit' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-100 hover:border-blue-300 dark:border-gray-800'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border border-r-4 transition-all cursor-pointer dark:bg-boxdark hover:shadow-md shadow-theme-sm border-r-blue-500">
                <div class="flex justify-center items-center w-10 h-10 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                    <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        في الطريق
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-gray-900 dark:text-white">{{ $inTransit ?? 0 }}</h4>
                </div>
            </div>

            {{-- 3. المستلمة --}}
            <div @click="filterStatus = 'delivered'"
                :class="filterStatus === 'delivered' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100 hover:border-success-300 dark:border-gray-800'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border border-r-4 transition-all cursor-pointer dark:bg-boxdark hover:shadow-md shadow-theme-sm border-r-success-500">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <span class="material-symbols-outlined text-[22px]">task_alt</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        تم التسليم
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-gray-900 dark:text-white">{{ $delivered ?? 0 }}</h4>
                </div>
            </div>

            {{-- 4. الإيرادات (غير قابلة للفلترة عادةً، للعرض فقط) --}}
            <div class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border border-gray-100 transition-all dark:bg-boxdark dark:border-gray-800 hover:shadow-md shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500">
                    <span class="material-symbols-outlined text-[22px]">account_balance_wallet</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        إيرادات COD
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-gray-900 dark:text-white">
                        {{ number_format($revenueCOD ?? 0) }}
                        <small class="text-[11px] font-bold text-gray-400 mr-0.5">ر.ي</small>
                    </h4>
                </div>
            </div>

        </div>

        {{-- ===== قسم الجدول والمخطط البياني ===== --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ===== جدول آخر 24 ساعة (يأخذ مساحة 2 من 3) ===== --}}
            <div class="flex overflow-hidden flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm lg:col-span-2 dark:border-gray-800 dark:bg-boxdark">

                {{-- Header --}}
                <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[18px]">history</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            الطرود خلال <span class="text-primary">24</span> ساعة
                        </h3>
                    </div>
                    <a href="{{ route('shipment.index') }}"
                        class="flex gap-1 items-center text-sm font-bold text-gray-500 transition-colors group hover:text-primary dark:text-gray-400">
                        عرض الكل
                        <span class="material-symbols-outlined text-[16px] transition-transform group-hover:-translate-x-1 rtl:rotate-180">arrow_forward</span>
                    </a>
                </div>

                {{-- Mobile View (Cards) --}}
                <div class="flex flex-col gap-4 p-4 lg:hidden">
                    @forelse ($last24Shipments as $shipment)
                        <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 transition-opacity bg-gray-50/50 dark:bg-gray-800/50 dark:border-gray-800"
                            x-show="filterStatus === 'all' || filterStatus === '{{ $shipment->status }}'" x-transition>

                            <div class="flex justify-between items-start">
                                <div class="flex flex-col gap-2">
                                    <span class="inline-block px-2.5 py-1 font-mono text-xs font-bold text-gray-600 bg-white rounded-md border border-gray-200 shadow-sm w-fit dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                        #{{ $shipment->bond_number }}
                                    </span>
                                    
                                    @php
                                        $statusConfig = [
                                            'pending'    => ['bg' => 'bg-warning-50 dark:bg-warning-500/10', 'text' => 'text-warning-600 dark:text-warning-400', 'label' => 'انتظار', 'dot' => 'bg-warning-500'],
                                            'in_transit' => ['bg' => 'bg-blue-50 dark:bg-blue-500/10', 'text' => 'text-blue-600 dark:text-blue-400', 'label' => 'جاري التوصيل', 'dot' => 'bg-blue-500'],
                                            'delivered'  => ['bg' => 'bg-success-50 dark:bg-success-500/10', 'text' => 'text-success-600 dark:text-success-400', 'label' => 'تم التسليم', 'dot' => 'bg-success-500'],
                                            'returned'   => ['bg' => 'bg-error-50 dark:bg-error-500/10', 'text' => 'text-error-600 dark:text-error-400', 'label' => 'مرتجع', 'dot' => 'bg-error-500'],
                                            'cancelled'  => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-600 dark:text-gray-300', 'label' => 'ملغي', 'dot' => 'bg-gray-500'],
                                        ];
                                        $currentStatus = $statusConfig[$shipment->status] ?? $statusConfig['cancelled'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 w-fit rounded-full text-[10px] font-black uppercase {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus['dot'] }}"></span>
                                        {{ $currentStatus['label'] }}
                                    </span>
                                </div>
                                
                                @if ($shipment->status !== 'cancelled')
                                    <a href="{{ route('shipment.show', $shipment->id) }}"
                                        class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-gray-900 dark:border-gray-800 dark:hover:text-primary">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                @endif
                            </div>

                            <div class="flex gap-3 items-center mt-2">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full shadow-inner bg-primary">
                                    {{ mb_substr($shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '?'), 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ Str::limit($shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '-'), 30) }}
                                    </span>
                                    <span class="flex gap-1 items-center mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-[14px]">directions_car</span>
                                        {{ optional($shipment->package)->driver_name ?? 'لم يعين سائق' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-between items-end pt-3 mt-1 border-t border-gray-100 dark:border-gray-800">
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">المسار</span>
                                    <div class="flex gap-1.5 items-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                        <span>{{ $shipment->senderBranch->name ?? $shipment->sender_branch_code }}</span>
                                        <span class="material-symbols-outlined text-[14px] text-gray-300 rtl:rotate-180">arrow_right_alt</span>
                                        <span class="text-primary">{{ $shipment->receiverBranch->name ?? $shipment->receiver_branch_code }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 items-end">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">المبلغ</span>
                                    <div class="font-mono text-sm font-black text-gray-900 dark:text-white">
                                        {{ number_format($shipment->total_amount, 0) }}
                                        <span class="font-sans text-[10px] font-bold text-gray-400">ر.ي</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                            <span class="text-3xl text-gray-400 material-symbols-outlined">inbox</span>
                            <h4 class="mt-2 text-sm font-bold text-gray-900 dark:text-white">لا توجد بيانات</h4>
                            <p class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">لم يتم تسجيل شحنات خلال الـ 24 ساعة الماضية</p>
                        </div>
                    @endempty
                </div>

                {{-- Table Container (Desktop View) --}}
                <div class="hidden overflow-x-auto flex-1 lg:block">
                    <table class="min-w-full text-center align-middle">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                            <tr class="text-[11px] font-black tracking-wider text-gray-400 uppercase border-b border-gray-100 dark:border-gray-800">
                                <th class="px-6 py-4 text-right">رقم السند</th>
                                <th class="px-6 py-4 text-right">العميل المستلم</th>
                                <th class="px-6 py-4">المسار</th>
                                <th class="px-6 py-4">الحالة</th>
                                <th class="px-6 py-4 text-left">المبلغ</th>
                                <th class="px-4 py-4"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                            @forelse ($last24Shipments as $shipment)
                                <tr class="transition-all group hover:bg-gray-50 dark:hover:bg-gray-800/30"
                                    x-show="filterStatus === 'all' || filterStatus === '{{ $shipment->status }}'" x-transition>
                                    
                                    {{-- ID --}}
                                    <td class="px-6 py-4 text-sm text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 font-mono text-xs font-black text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                                            #{{ $shipment->bond_number }}
                                        </span>
                                    </td>

                                    {{-- Customer --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex gap-3 items-center">
                                            <div class="flex justify-center items-center w-8 h-8 text-xs font-bold text-white rounded-full shadow-inner bg-primary">
                                                {{ mb_substr($shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '?'), 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ Str::limit($shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '-'), 20) }}
                                                </span>
                                                <span class="flex gap-1 items-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                                    <span class="material-symbols-outlined text-[14px]">directions_car</span>
                                                    {{ optional($shipment->package)->driver_name ?? 'لم يعين' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Route --}}
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 justify-center items-center text-xs font-bold">
                                            <span class="text-gray-600 dark:text-gray-300">{{ $shipment->senderBranch->name ?? $shipment->sender_branch_code }}</span>
                                            <span class="material-symbols-outlined text-[16px] text-gray-300 rtl:rotate-180">arrow_right_alt</span>
                                            <span class="text-primary">{{ $shipment->receiverBranch->name ?? $shipment->receiver_branch_code }}</span>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4">
                                        @php
                                            $currentStatus = $statusConfig[$shipment->status] ?? $statusConfig['cancelled'];
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus['dot'] }}"></span>
                                            {{ $currentStatus['label'] }}
                                        </span>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="px-6 py-4 text-left">
                                        <div class="font-mono text-sm font-black text-gray-900 dark:text-white">
                                            {{ number_format($shipment->total_amount, 0) }}
                                            <span class="font-sans text-xs font-bold text-gray-400">ر.ي</span>
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-4 py-4 text-right">
                                        @if ($shipment->status !== 'cancelled')
                                            <a href="{{ route('shipment.show', $shipment->id) }}" title="عرض التفاصيل"
                                                class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-primary hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-primary">
                                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="p-4 mb-3 bg-gray-50 rounded-full dark:bg-gray-800/50">
                                                <span class="text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">inbox</span>
                                            </div>
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">لا توجد بيانات</h4>
                                            <p class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">لم يتم تسجيل شحنات خلال الـ 24 ساعة الماضية</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ===== المخطط البياني للإيرادات (تمت استعادته وضبطه) ===== --}}
            <div class="flex flex-col h-full bg-white rounded-2xl border border-gray-100 shadow-sm lg:col-span-1 dark:border-gray-800 dark:bg-boxdark">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-warning-50 text-warning-500 dark:bg-warning-500/10">
                            <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">الإيرادات الشهرية</h3>
                    </div>
                </div>
                
                {{-- حاوية المخطط التي سيبحث عنها الجافاسكربت --}}
                <div class="flex flex-1 justify-center items-center p-4">
                    <div id="chartOne" class="chart-container"></div>
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
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            series: [{
                name: "الإيرادات",
                data: @json(array_values($monthlySales ?? []))
            }],
            xaxis: {
                categories: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600,
                        fontFamily: 'inherit'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontSize: '11px',
                        fontWeight: 600,
                        fontFamily: 'inherit'
                    },
                    formatter: (value) => {
                        return value >= 1000 ? (value / 1000).toFixed(1) + 'k' : value
                    }
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: "45%",
                    colors: {
                        backgroundBarColors: ['transparent'],
                    }
                }
            },
            // استخدام اللون الأساسي البرتقالي الخاص بك
            colors: ["#dc6803"],
            dataLabels: { enabled: false },
            grid: {
                strokeDashArray: 4,
                borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#f3f4f6',
                padding: { top: 0, right: 0, bottom: 0, left: 10 }
            },
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val.toLocaleString() + " ر.ي";
                    }
                }
            }
        };

        if(document.querySelector("#chartOne")) {
            const chart = new ApexCharts(document.querySelector("#chartOne"), options);
            chart.render();
        }
    });
</script>
@endsection