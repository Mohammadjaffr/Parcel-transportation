@extends('layouts.app')
@section('title', 'تفاصيل الطرد')
@section('Breadcrumb', 'تفاصيل الطرد')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
        <div
            class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                </svg>
            </div>
            <div class="mt-3 w-full">
                <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلبات</span>
                <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">50</h4>
            </div>
        </div>

        <div
            class=" flex  m:hidden flex-col items-start justify-between rounded-xl p-4  transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>

        <div
            class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>

        <div
            class="flex  m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

        </div>
    </div>
    <!-- بطاقة التفاصيل الرئيسية -->
    <div
        class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 shadow-xl rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
        <!-- الهيدر -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-brand-100 dark:bg-brand-900/30 rounded-xl">
                    <svg class="w-8 h-8 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">الطرد #{{ $shipment->id }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium 
                        @if ($shipment->status == 'pending') rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500
                        @elseif($shipment->status == 'in_transit') rounded-full bg-brand-50 px-2 py-0.5 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-500
                        @elseif($shipment->status == 'deliverd') rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500
                        @elseif($shipment->status == 'cancelled') rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500 @endif">
                            {{ $shipment->status }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                        <span
                            class="text-sm text-gray-500 dark:text-gray-400">{{ $shipment->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('request.invoice', $shipment->id) }}" target="_blank"
                class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 mx-2"
                title="طباعة الفاتورة">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
            </a>
        </div>

        <!-- شبكة المعلومات -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- بطاقة المرسل -->
            <div
                class="relative bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="absolute top-4 left-4">
                    <div class="p-2 bg-brand-50 dark:bg-brand-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="pr-12">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">المرسل</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الاسم</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $shipment->sender_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الهاتف</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $shipment->sender_phone }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الموقع</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $shipment->from_city }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة المستلم -->
            <div
                class="relative bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="absolute top-4 left-4">
                    <div class="p-2 bg-success-50 dark:bg-success-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="pr-12">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">المستلم</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الاسم</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $shipment->receiver_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الهاتف</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $shipment->receiver_phone }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">الموقع</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $shipment->to_city }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات إضافية في شبكة ثلاثية -->
        <div class="mt-6 grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div
                class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow transition-shadow duration-300">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-purple-500 dark:text-brand-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">الفرع</h4>
                </div>
                <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $shipment->branch }}</p>
            </div>

            <div
                class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow transition-shadow duration-300">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-warning-50 dark:bg-warning-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">نوع الطرد</h4>
                </div>
                <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $shipment->package_type }}</p>
            </div>
            <div>
                <div
                    class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 bg-success-50 dark:bg-success-900/20 rounded-lg">
                            <svg class="w-5 h-5 text-success-600 dark:text-success-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">حالة الطرد</h4>
                    </div>
                    <p
                        class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500">
                        {{ $shipment->status }}</p>

                </div>
            </div>

            <div
                class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow transition-shadow duration-300">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-2 bg-warning-50 dark:bg-warning-900/20 rounded-lg">
                        <svg class="w-5 h-5 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">طريقة الدفع</h4>
                </div>
                <p class="text-lg font-bold">
                    @if ($shipment->payment_method == 'prepaid')
                        <span class="text-success-600 dark:text-success-400 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            دفع مقدم
                        </span>
                    @else
                        <span class="text-warning-600 dark:text-warning-400 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            دفع عند التسليم
                        </span>
                    @endif
                </p>
            </div>
        </div>

        <!-- الملاحظات -->
        @if ($shipment->notes)
            <div
                class="mt-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">الملاحظات</h4>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $shipment->notes }}</p>
                </div>
            </div>
        @endif

        <!-- معلومات الوقت -->
        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">تم الإنشاء</p>
                        <p class="font-medium text-gray-800 dark:text-white">
                            {{ $shipment->created_at->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">آخر تحديث</p>
                        <p class="font-medium text-gray-800 dark:text-white">
                            {{ $shipment->updated_at->format('Y/m/d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
