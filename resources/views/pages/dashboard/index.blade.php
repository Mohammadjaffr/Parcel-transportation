@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'الصفحة الرئيسية')
@section('style')
    <style>
        /* ===== Responsive Layout ===== */
        .chart-container {
            width: 100%;
        }       

        
    </style>
@endsection
@section('content')

    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">

        <div
            class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M21 19.9999C21 18.2583 19.3304 16.7767 17 16.2275M15 20C15 17.7909 12.3137 16 9 16C5.68629 16 3 17.7909 3 20M15 13C17.2091 13 19 11.2091 19 9C19 6.79086 17.2091 5 15 5M9 13C6.79086 13 5 11.2091 5 9C5 6.79086 6.79086 5 9 5C11.2091 5 13 6.79086 13 9C13 11.2091 11.2091 13 9 13Z"
                        stroke="#dc6803" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">عدد الطرود المسجلة اليوم</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">100</h4>
            </div>
        </div>
        <div
            class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M21 19.9999C21 18.2583 19.3304 16.7767 17 16.2275M15 20C15 17.7909 12.3137 16 9 16C5.68629 16 3 17.7909 3 20M15 13C17.2091 13 19 11.2091 19 9C19 6.79086 17.2091 5 15 5M9 13C6.79086 13 5 11.2091 5 9C5 6.79086 6.79086 5 9 5C11.2091 5 13 6.79086 13 9C13 11.2091 11.2091 13 9 13Z"
                        stroke="#dc6803" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">عدد الطرود في الطريق</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">100</h4>
            </div>
        </div>
        <div
            class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M21 19.9999C21 18.2583 19.3304 16.7767 17 16.2275M15 20C15 17.7909 12.3137 16 9 16C5.68629 16 3 17.7909 3 20M15 13C17.2091 13 19 11.2091 19 9C19 6.79086 17.2091 5 15 5M9 13C6.79086 13 5 11.2091 5 9C5 6.79086 6.79086 5 9 5C11.2091 5 13 6.79086 13 9C13 11.2091 11.2091 13 9 13Z"
                        stroke="#dc6803" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">عدد الطرود التي تم استلامها</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">100</h4>
            </div>
        </div>

    </div>

    <!-- ====== Chart One Start -->

    <div class="flex flex-col lg:flex-row mb-4">
        <div class="chart-container">
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6 h-full">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        الطرود خلال <span class="text-warning-500 dark:text-warning/90"> سنة</span>
                    </h3>
                </div>

                <div class="mt-4">
                    <div id="chartOne" class="w-full h-64"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- ====== Chart One End -->

    <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
        <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    الطرود خلال <span class="text-warning-500 dark:text-warning/90">
                        24</span> ساعة
                </h3>
            </div>

            <div class="flex items-center gap-3">

            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="min-w-full">
                <!-- table header start -->
                <thead>
                    <tr class="border-gray-100 border-y dark:border-gray-800">
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    رقم الطلب
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    السائق
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    العميل
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center col-span-2">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    السعر
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center col-span-2">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    حالة الطلب
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center col-span-2">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    حالة الدفع
                                </p>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center justify-center space-x-4">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 ml-1">
                                        من
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 ml-1">
                                        الى
                                    </p>
                                </div>
                            </div>
                        </th>
                        <th class="py-3">
                            <div class="flex items-center justify-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    الإجراءات
                                </p>
                            </div>
                        </th>
                    </tr>
                </thead>
                <!-- table header end -->

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <!-- 1 -->
                    <tr>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    1
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <div class="flex items-center gap-3">
                                    <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                                        <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            أحمد شرجبي
                                        </p>
                                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                            +967780236552
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            عوض لشرم
                                        </p>
                                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                            +967780236551
                                        </span>
                                    </div>
                                </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    9700
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p
                                    class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                                    ملغية
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p
                                    class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">
                                    أجل
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <p
                                    class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                    المنصورة
                                </p>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <p
                                    class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                    التواهي
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center justify-center">
                                <button
                                    class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    تفاصيل
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- 2 -->
                    <tr>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    2
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <div class="flex items-center gap-3">
                                    <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                                        <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            أحمد شرجبي
                                        </p>
                                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                            +967780236552
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            عوض لشرم
                                        </p>
                                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                            +967780236551
                                        </span>
                                    </div>
                                </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    8500
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p
                                    class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                    مكتملة
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p
                                    class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                    نقد
                                </p>
                            </div>
                        </td>

                        <td class="py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <p
                                    class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                    خورمكسر
                                </p>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <p
                                    class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                    المعلا
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center justify-center">
                                <button
                                    class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    تفاصيل
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- 3 -->
                    <tr>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    3
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <div class="flex items-center gap-3">
                                    <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                                        <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            أحمد شرجبي
                                        </p>
                                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                            +967780236552
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                            عوض لشرم
                                        </p>
                                        <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                            +967780236551
                                        </span>
                                    </div>
                                </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                    10000
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p
                                    class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500">
                                    بالانتظار
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center">
                                <p
                                    class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500">
                                    بالانتظار
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <p
                                    class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                    المنصورة
                                </p>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                <p
                                    class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
                                    عدن
                                </p>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center justify-center">
                                <button
                                    class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    تفاصيل
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- table body end -->
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('script')

    <script>
        window.laravelChartData = {
            sales: [
                @json($monthlySales['january'] ?? 0),
                @json($monthlySales['february'] ?? 0),
                @json($monthlySales['march'] ?? 0),
                @json($monthlySales['april'] ?? 0),
                @json($monthlySales['may'] ?? 0),
                @json($monthlySales['june'] ?? 0),
                @json($monthlySales['july'] ?? 0),
                @json($monthlySales['august'] ?? 0),
                @json($monthlySales['september'] ?? 0),
                @json($monthlySales['october'] ?? 0),
                @json($monthlySales['november'] ?? 0),
                @json($monthlySales['december'] ?? 0)
            ],
            months: [
                'يناير', 'فبراير', 'مارس', 'أبريل',
                'مايو', 'يونيو', 'يوليو', 'أغسطس',
                'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
            ],
            lastUpdated: @json(now()->format('Y-m-d H:i:s')),
            yAxis: {
                min: 0,
                max: 500,
                tickAmount: 6,
            }

            colors: ["#dc6803"],
            seriesName: " ",
            chartHeight: 180,
            chartType: "bar",
            columnWidth: "35%",
            borderRadius: 5,
            strokeWidth: 2,
            fontSize: '12px',
            tooltipSuffix: " ",
            showToolbar: false,
            showDataLabels: false,
            showStroke: true,
            showXAxisBorder: false,
            showXAxisTicks: false,
            showLegend: true,
            legendPosition: "top",
            legendHorizontalAlign: "left",
            showGridLines: true,
            fillOpacity: 1,
            showTooltipX: false,
            fontFamily: "Outfit, sans-serif",
            labelRotation: 0,
        };
    </script>

@endsection
