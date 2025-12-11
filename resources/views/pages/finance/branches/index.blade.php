@extends('layouts.app')
@section('title', 'التقارير المالية للفروع')
@section('Breadcrumb', 'التقارير المالية للفروع')

@section('content')

<div class="space-y-6">

    <!-- عنوان الصفحة -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">التقارير المالية للفروع</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">نظرة عامة على أرصدة الفروع والتسويات والشحنات الآجلة.</p>
        </div>
    </div>

    <!-- بطاقة الجدول -->
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">

        <!-- رأس الجدول -->
        <div class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                ملخص الفروع
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                            الفرع
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                            إجمالي COD
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                            تسويات مستلمة
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                            تسويات مدفوعة
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                            الرصيد النهائي
                        </th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                            الإجراءات
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($branchesSummary as $row)
                        @php
                            $branch = $row['branch'];
                            $net = $row['net_balance'];
                        @endphp

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                            <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $branch->name }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ number_format($row['total_cod'], 2) }} ر.ي
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="text-success-600 dark:text-success-400 font-semibold">
                                    {{ number_format($row['total_settle_in'], 2) }} ر.ي
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                <span class="text-warning-600 dark:text-warning-400 font-semibold">
                                    {{ number_format($row['total_settle_out'], 2) }} ر.ي
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if ($net > 0)
                                    <span class="text-xs bg-success-100 dark:bg-success-800 text-success-700 dark:text-success-300 px-2 py-1 rounded-full">
                                        له {{ number_format($net, 2) }} ر.ي
                                    </span>
                                @elseif ($net < 0)
                                    <span class="text-xs bg-error-100 dark:bg-error-800 text-error-700 dark:text-error-300 px-2 py-1 rounded-full">
                                        عليه {{ number_format(abs($net), 2) }} ر.ي
                                    </span>
                                @else
                                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-full">
                                        متساوي
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('finance.branches.show', $branch->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs rounded-lg 
                                          bg-band-50 dark:bg-band-900/50 border border-band-200 dark:border-band-700 
                                          text-band-700 dark:text-band-300 hover:bg-band-100 dark:hover:bg-band-800 transition">

                                    <svg class="w-4 h-4" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    تفاصيل
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
