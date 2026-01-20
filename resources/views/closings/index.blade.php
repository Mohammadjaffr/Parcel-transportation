@extends('layouts.app')
@section('title', 'سجل الإقفال اليومي')

@section('content')

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{ filterStatus: 'all' }">

        {{-- KPI Cards --}}
        <div class="flex gap-6">

            {{-- Total Transferred (All) --}}
            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-success-500 ring-2 ring-success-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        المحول للمركز</span>
                    <h4 class="text-xl font-black text-success-500">{{ number_format($totalTransferred) }} <span
                            class="text-xs font-semibold">ر.ي</span></h4>
                </div>
            </div>

            {{-- Total Shortage --}}
            <div @click="filterStatus = 'shortage'"
                :class="filterStatus === 'shortage' ? 'border-error-500 ring-2 ring-error-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        العجز (النقص)</span>
                    <h4 class="text-xl font-black text-error-500">{{ number_format(abs($totalShortage)) }} <span
                            class="text-xs font-semibold">ر.ي</span></h4>
                </div>
            </div>

            {{-- Total Surplus --}}
            <div @click="filterStatus = 'surplus'"
                :class="filterStatus === 'surplus' ? 'border-brand-500 ring-2 ring-brand-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        الفائض (الزيادة)</span>
                    <h4 class="text-xl font-black text-brand-500">{{ number_format($totalSurplus) }} <span
                            class="text-xs font-semibold">ر.ي</span></h4>
                </div>
            </div>
        </div>
        {{-- Header --}}
        <div
            class="flex flex-col gap-4 justify-between items-start p-6 bg-white rounded-2xl border border-gray-100 md:flex-row md:items-center dark:bg-white/[0.03] dark:border-gray-800 shadow-theme-sm">
            <div class="flex gap-4 items-center">
                <div class="flex justify-center items-center w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-6 h-6 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">سجل الإقفال اليومي</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">مراجعة تقارير جرد الصندوق والتحويلات</p>
                </div>
            </div>
            <a href="{{ route('transactions.index') }}"
                class="inline-flex gap-2 items-center px-5 h-11 text-sm font-semibold rounded-xl transition-all duration-200 text-brand-500 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10 hover:bg-brand-100 dark:hover:bg-brand-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للمعاملات
            </a>
        </div>

        {{-- Filter Toolbar --}}
        <div
            class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm p-5">
            <form method="GET" action="{{ route('closings.index') }}" id="filterForm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end">

                    {{-- Date Range --}}
                    <div class="flex-1">
                        <div class="flex gap-3 items-center mb-3">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">الفترة الزمنية</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="relative">
                                <label for="start_date"
                                    class="absolute -top-2 right-3 px-1 text-[10px] font-bold text-gray-400 bg-white dark:bg-gray-900">من</label>
                                <input type="date" name="start_date" id="start_date"
                                    class="px-3 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                                    value="{{ $startDate->format('Y-m-d') }}">
                            </div>
                            <div class="relative">
                                <label for="end_date"
                                    class="absolute -top-2 right-3 px-1 text-[10px] font-bold text-gray-400 bg-white dark:bg-gray-900">إلى</label>
                                <input type="date" name="end_date" id="end_date"
                                    class="px-3 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                                    value="{{ $endDate->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Quick Filters --}}
                    @php
                        $now = now();
                        $isThisMonth =
                            $startDate->isSameDay($now->copy()->startOfMonth()) &&
                            $endDate->isSameDay($now->copy()->endOfMonth());
                        $isLastMonth =
                            $startDate->isSameDay($now->copy()->subMonth()->startOfMonth()) &&
                            $endDate->isSameDay($now->copy()->subMonth()->endOfMonth());
                        $isThisYear =
                            $startDate->isSameDay($now->copy()->startOfYear()) &&
                            $endDate->isSameDay($now->copy()->endOfYear());
                        $isAllTime = !$isThisMonth && !$isLastMonth && !$isThisYear;
                    @endphp
                    <div class="flex-1">
                        <div class="flex gap-3 items-center mb-3">
                            <div
                                class="flex justify-center items-center w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-500/10">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">عرض سريع</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="setDateRange('thisMonth')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isThisMonth ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                الشهر الحالي
                            </button>
                            <button type="button" onclick="setDateRange('lastMonth')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isLastMonth ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                الشهر الماضي
                            </button>
                            <button type="button" onclick="setDateRange('thisYear')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isThisYear ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                هذه السنة
                            </button>
                            <button type="button" onclick="setDateRange('allTime')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isAllTime ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                الكل
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div>
                        <div class="flex gap-2 items-center">
                            <button type="submit"
                                class="inline-flex gap-2 justify-center items-center px-6 h-11 text-sm font-semibold text-white rounded-xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 focus:ring-4 focus:ring-brand-500/20 shadow-brand-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                عرض النتائج
                            </button>

                            <button type="submit" formaction="{{ route('closings.export') }}"
                                class="inline-flex gap-2 justify-center items-center px-6 h-11 text-sm font-semibold text-white rounded-xl shadow-lg transition-all bg-success-500 hover:bg-success-600 focus:ring-4 focus:ring-success-500/20 shadow-success-500/20">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                تصدير Excel
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        {{-- Data Table --}}
        <div
            class="bg-white rounded-2xl border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px]">
                    <thead class="bg-gray-50/80 dark:bg-gray-900/50">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                #</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                التاريخ والوقت</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                المسؤول</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                الرصيد المتوقع</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                النقد الفعلي</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                الفرق</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                المبلغ المحول</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($closings as $closing)
                            @php
                                $closingStatus =
                                    $closing->difference < 0
                                        ? 'shortage'
                                        : ($closing->difference > 0
                                            ? 'surplus'
                                            : 'balanced');
                            @endphp
                            <tr x-show="filterStatus === 'all' || filterStatus === '{{ $closingStatus }}'"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="transition-colors hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                <td class="px-6 py-4">
                                    <span
                                        class="text-xs font-bold text-gray-500 dark:text-gray-400">{{ $loop->iteration + ($closings->currentPage() - 1) * $closings->perPage() }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-gray-900 dark:text-white">{{ $closing->created_at->format('Y-m-d') }}</span>
                                        <span
                                            class="text-xs text-gray-500">{{ $closing->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $closing->user->name ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="font-mono text-sm text-gray-500 dark:text-gray-400">{{ number_format($closing->expected_balance, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($closing->actual_cash, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($closing->difference < 0)
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-bold rounded-lg text-error-500 bg-error-50 dark:bg-error-500/10 dark:text-error-400">
                                            {{ number_format($closing->difference, 2) }}
                                        </span>
                                    @elseif($closing->difference > 0)
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-bold text-blue-500 bg-blue-50 rounded-lg dark:bg-blue-500/10 dark:text-blue-400">
                                            +{{ number_format($closing->difference, 2) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-bold text-gray-500 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-400">
                                            مطابق
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="font-bold text-success-500 dark:text-success-400">{{ number_format($closing->transferred_amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs text-gray-500 max-w-[200px] truncate"
                                        title="{{ $closing->notes }}">
                                        {{ $closing->notes ?? '—' }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="flex justify-center items-center mb-4 w-16 h-16 bg-gray-100 rounded-2xl dark:bg-gray-800">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">لا يوجد سجلات
                                            للإقفال اليومي في هذه الفترة</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($closings->hasPages())
                <div class="p-6 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
                    {{ $closings->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Quick Filter Script --}}
    <script>
        function setDateRange(preset) {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const today = new Date();
            let startDate, endDate;

            switch (preset) {
                case 'thisMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'lastMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;
                case 'thisYear':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = new Date(today.getFullYear(), 11, 31);
                    break;
                case 'allTime':
                    startDate = new Date(today.getFullYear() - 10, 0, 1);
                    endDate = today;
                    break;
                default:
                    return;
            }

            const formatDate = (date) =>
                `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

            startDateInput.value = formatDate(startDate);
            endDateInput.value = formatDate(endDate);
            document.getElementById('filterForm').submit();
        }
    </script>
@endsection
