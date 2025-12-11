@extends('layouts.app')
@section('title', 'طرود السائق: ' . $driver->name)
@section('Breadcrumb', 'طرود السائق')

@section('content')

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 md:p-6">
        <!-- العنوان وعدد الطرود -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    طرود السائق: {{ $driver->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    عرض جميع الطرود الموكلة للسائق
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 text-sm font-medium">
                    <svg class="w-4 h-4 ml-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $shipments->count() }} طرد
                </span>
            </div>
        </div>

        <!-- جدول الطرود -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            رقم السند
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            المرسل
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            المستلم
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            المنطقة
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            نوع الطرد
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            ملاحظات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($shipments as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <!-- رقم السند -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-white font-mono">
                                    {{ $request->bond_number }}
                                </span>
                            </td>

                            <!-- المرسل -->
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                    {{ $request->sender_name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $request->sender_phone }}
                                </div>
                            </td>

                            <!-- المستلم -->
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                    {{ $request->receiver_name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $request->receiver_phone }}
                                </div>
                            </td>

                            <!-- المنطقة -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-warning-100 text-warning-500 dark:bg-warning-900/30 dark:text-warning-300">
                                    <svg class="w-3 h-3 ml-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $request->from_city }}
                                </span>
                            </td>

                            <!-- نوع الطرد -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $request->package_type }}
                                </span>
                            </td>

                            <!-- الملاحظات -->
                            <td class="px-4 py-4">
                                @if ($request->notes)
                                    <div class="max-w-xs">
                                        <div class="text-sm text-gray-500 dark:text-gray-500 line-clamp-2">
                                            {{ $request->notes }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-lg font-medium mb-2">لا توجد طرود مسجلة حالياً</p>
                                    <p class="text-sm">لم يتم تعيين أي طرود لهذا السائق بعد</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- التذييل -->
        @if ($shipments->count() > 0)
            <div
                class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                <div class="mb-2 sm:mb-0">
                    عرض {{ $shipments->count() }} طرد
                </div>
                <div class="flex items-center gap-2">
                    <button
                        class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        تصدير PDF
                    </button>
                    <a href="{{ route('drivers.shipments.print', $driver->id) }}"
                        class="bg-brand-500 hover:bg-brand-600 text-white text-sm px-4 py-2 rounded-lg" target="_blank">
                        طباعة كشف الطرود
                    </a>

                </div>
            </div>
        @endif
    </div>

@endsection
