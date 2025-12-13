@extends('layouts.app')
@section('title', 'التقارير')
@section('Breadcrumb', 'التقارير')
@section('content')

    <div class="space-y-6">

        <!-- رأس الصفحة -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">التقارير والإحصائيات</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    عرض وتحليل البيانات المالية والإدارية
                </p>
            </div>

            <div class="flex items-center gap-3">
                {{-- <button onclick="window.print()"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 
                           dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                    طباعة التقرير
                </button> --}}
            </div>
        </div>

        <!-- بطاقات التقارير -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

            <!-- لوحة التحكم -->
            <a href="{{ route('reports.dashboard') }}"
                class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm 
                      hover:border-brand-500 dark:hover:border-brand-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-brand-50 dark:bg-brand-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs px-3 py-1 rounded-full bg-brand-100 text-brand-800 dark:bg-brand-900/30 dark:text-brand-300">
                        جديد
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">لوحة التحكم</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    نظرة عامة على الإحصائيات والأداء الرئيسية
                </p>
                <div
                    class="text-sm text-brand-500 dark:text-brand-400 font-medium flex items-center gap-2 group-hover:gap-3 transition-all">
                    عرض التقرير
                     <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </div>
            </a>

            <!-- كشف حساب عميل -->
            <a href="{{ route('customers.index') }}"
                class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm 
                      hover:border-brand-500 dark:hover:border-brand-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div
                        class="w-12 h-12 rounded-full bg-success-50 dark:bg-success-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-success-500 dark:text-success-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">كشف حساب عميل</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    عرض حركات العملاء المالية والمدفوعات
                </p>
                <div
                    class="text-sm text-brand-500 dark:text-brand-400 font-medium flex items-center gap-2 group-hover:gap-3 transition-all">
                      <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                    عرض التقارير

                  
                </div>
            </a>

            <!-- كشف حساب فرع -->
            <a href="{{ route('finance.branches.index') }}"
                class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm 
                      hover:border-brand-500 dark:hover:border-brand-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">كشف حساب فرع</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    تقارير الأداء المالي والإداري للفروع
                </p>
                <div
                    class="text-sm text-brand-500 dark:text-brand-400 font-medium flex items-center gap-2 group-hover:gap-3 transition-all">
                       <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'rotate-180' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>

                    عرض التقارير


                </div>
            </a>

            <!-- إقفال شهري PDF -->
            <a href="{{ route('reports.monthly.closing.pdf') }}" target="_blank"
                class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm 
                      hover:border-brand-500 dark:hover:border-brand-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-error-50 dark:bg-error-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-error-500 dark:text-error-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs px-3 py-1 rounded-full bg-error-100 text-error-800 dark:bg-error-900/30 dark:text-error-300">
                        PDF
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">إقفال شهري PDF</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    تحميل تقرير الإقفال الشهري بصيغة PDF للطباعة
                </p>
                <div
                    class="text-sm text-error-500 dark:text-error-400 font-medium flex items-center gap-2 group-hover:gap-3 transition-all">
                    تحميل الملف
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
            </a>

            <!-- تقارير الشحنات -->
            <a href="{{ route('reports.shipments') }}"
                class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm 
                      hover:border-brand-500 dark:hover:border-brand-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div
                        class="w-12 h-12 rounded-full bg-warning-50 dark:bg-warning-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-warning-500 dark:text-warning-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">تقارير الشحنات</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    تحليل أداء الشحنات والتوزيع حسب الفروع
                </p>
                <div
                    class="text-sm text-brand-500 dark:text-brand-400 font-medium flex items-center gap-2 group-hover:gap-3 transition-all">
                    عرض التقارير
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
            </a>

            <!-- تقارير الإيرادات -->
            <a href="{{ route('reports.revenue') }}"
                class="group bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm 
                      hover:border-brand-500 dark:hover:border-brand-500 hover:shadow-md transition-all duration-200">
                <div class="flex items-start justify-between mb-4">
                    <div
                        class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs px-3 py-1 rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                        مالي
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">تقارير الإيرادات</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    تحليل الإيرادات والمصروفات والأرباح الشهرية
                </p>
                <div
                    class="text-sm text-brand-500 dark:text-brand-400 font-medium flex items-center gap-2 group-hover:gap-3 transition-all">
                    عرض التقارير
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
            </a>

        </div>

        <!-- إحصائيات سريعة -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">إحصائيات سريعة</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">15</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">تقارير متاحة</p>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">24</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">عملاء نشطين</p>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">8</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">فروع</p>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">1,250</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">شحنة هذا الشهر</p>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('styles')
    <style>
        /* تأثيرات إضافية للبطاقات */
        .group:hover .group-hover\:gap-3 {
            gap: 0.75rem;
        }

        /* تحسين الطباعة */
        @media print {
            button {
                display: none !important;
            }

            .group:hover {
                border-color: inherit !important;
                box-shadow: none !important;
            }
        }
    </style>
@endpush
