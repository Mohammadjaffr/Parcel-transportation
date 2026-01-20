@extends('layouts.app')
@section('title', 'Cash Box - Transaction Management')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl">

        {{-- Balance Cards --}}
        @php
            $typeFilter = request('type');
        @endphp
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            {{-- Current Balance --}}
            <a href="{{ route('transactions.index', array_merge(request()->query(), ['type' => null])) }}"
                class="relative flex flex-col rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all shadow-theme-sm cursor-pointer hover:shadow-lg {{ !$typeFilter ? 'border-brand-500 ring-2 ring-brand-500' : 'border-gray-100 dark:border-gray-800' }}">
               
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span
                        class="text-sm font-bold text-gray-700 dark:text-gray-300 {{ !$typeFilter ? 'pe-10' : '' }}">الرصيد
                        الحالي</span>
                </div>
                <h4 class="mt-3 text-2xl font-black {{ $balance >= 0 ? 'text-brand-500' : 'text-error-500' }}">
                    {{ number_format($balance) }} <span class="text-sm font-semibold">ر.ي</span>
                </h4>
            </a>

            {{-- Total Income --}}
            <a href="{{ route('transactions.index', array_merge(request()->query(), ['type' => 'in'])) }}"
                class="relative flex flex-col rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all shadow-theme-sm cursor-pointer hover:shadow-lg {{ $typeFilter === 'in' ? 'border-success-500 ring-2 ring-success-500' : 'border-gray-100 dark:border-gray-800' }}">
             
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10">
                        <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                    </div>
                    <span
                        class="text-sm font-bold text-gray-700 dark:text-gray-300 {{ $typeFilter === 'in' ? 'pe-16' : '' }}">إجمالي
                        الإيرادات</span>
                </div>
                <h4 class="mt-3 text-2xl font-black text-success-500">
                    {{ number_format($income) }} <span class="text-sm font-semibold">ر.ي</span>
                </h4>
            </a>

            {{-- Total Expenses --}}
            <a href="{{ route('transactions.index', array_merge(request()->query(), ['type' => 'out'])) }}"
                class="relative flex flex-col rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all shadow-theme-sm cursor-pointer hover:shadow-lg {{ $typeFilter === 'out' ? 'border-error-500 ring-2 ring-error-500' : 'border-gray-100 dark:border-gray-800' }}">
               
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10">
                        <svg class="w-5 h-5 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                    </div>
                    <span
                        class="text-sm font-bold text-gray-700 dark:text-gray-300 {{ $typeFilter === 'out' ? 'pe-16' : '' }}">إجمالي
                        المصروفات</span>
                </div>
                <h4 class="mt-3 text-2xl font-black text-error-500">
                    {{ number_format($expense) }} <span class="text-sm font-semibold">ر.ي</span>
                </h4>
            </a>
        </div>

        {{-- Date Range Filter Toolbar --}}
        <div
            class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm p-5">
            <form method="GET" action="{{ route('transactions.index') }}" id="filterForm">
                {{-- Filter Header --}}
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                    {{-- Date Range Section --}}
                    <div class="flex-1">
                        <div class="flex gap-3 items-center mb-3">
                            <div
                                class="flex justify-center items-center w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-500/10">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
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
                                class="flex justify-center items-center w-8 h-8 rounded-lg bg-warning-50 dark:bg-warning-500/10">
                                <svg class="w-4 h-4 text-warning-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">فلاتر سريعة</span>
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

                    {{-- Submit Button --}}
                    <button type="submit"
                        class="inline-flex gap-2 justify-center items-center px-6 h-11 text-sm font-semibold text-white rounded-xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 focus:ring-4 focus:ring-brand-500/20 shadow-brand-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        عرض النتائج
                    </button>
                </div>
            </form>
        </div>

        {{-- Analytics Charts --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Expenses Breakdown (Pie Chart) --}}
            <div
                class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm p-6">
                <div class="flex gap-3 items-center mb-6">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10">
                        <svg class="w-5 h-5 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">تحليل المصروفات حسب الفئة</h3>
                </div>
                <div class="relative" style="height: 280px;">
                    <canvas id="expensesChart"></canvas>
                </div>
            </div>

            {{-- Daily Movement (Bar Chart) --}}
            <div
                class="bg-white dark:bg-white/[0.03] rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm p-6">
                <div class="flex gap-3 items-center mb-6">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">الحركة اليومية</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $startDate->format('Y-m-d') }} -
                            {{ $endDate->format('Y-m-d') }}</p>
                    </div>
                </div>
                <div class="relative" style="height: 280px;">
                    <canvas id="dailyTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Actions Bar --}}
        <div
            class="flex flex-col gap-4 justify-between items-start p-5 bg-white rounded-2xl border border-gray-100 md:flex-row md:items-center dark:bg-white/[0.03] dark:border-gray-800 shadow-theme-sm">
            <div class="flex gap-3 items-center">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">قائمة المعاملات</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <span class="font-bold text-brand-500">{{ $transactions->total() }}</span> معاملة
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('transaction-categories.index') }}"
                    class="inline-flex gap-1.5 items-center px-4 h-10 text-xs font-bold text-gray-600 bg-gray-100 rounded-xl transition-all duration-200 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    الفئات
                </a>
                @include('closings.create-closing-modal')
                @include('transactions.create-transaction-modal')
            </div>
        </div>

        {{-- Transactions Table --}}
        <div
            class="bg-white rounded-2xl border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead class="bg-gray-50/80 dark:bg-gray-900/50">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                #</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                التاريخ</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                الفئة</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                النوع</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                المبلغ</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                الوصف</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                المستخدم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($transactions as $transaction)
                            <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex justify-center items-center w-8 h-8 text-xs font-bold text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                                        {{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $transaction->created_at->format('Y-m-d') }}</span>
                                        <span
                                            class="mt-0.5 text-xs text-gray-400">{{ $transaction->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white">{{ $transaction->category->name }}</span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($transaction->category->type === 'in')
                                        <span
                                            class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-full bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                            </svg>
                                            إيراد
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-full bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                            </svg>
                                            مصروف
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-lg font-black {{ $transaction->category->type === 'in' ? 'text-success-500' : 'text-error-500' }}">
                                        {{ $transaction->category->type === 'in' ? '+' : '-' }}{{ number_format($transaction->amount) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->description ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $transaction->user->name }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="flex justify-center items-center mb-4 w-16 h-16 bg-gray-100 rounded-2xl dark:bg-gray-800">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">لا توجد معاملات
                                            مسجلة حالياً</p>
                                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">ابدأ بإضافة معاملة جديدة
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="p-6 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.dispatchEvent(new CustomEvent('open-success-modal', {
                    detail: {
                        message: '{{ session('success') }}'
                    }
                }));
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.dispatchEvent(new CustomEvent('open-error-modal', {
                    detail: {
                        message: '{{ session('error') }}'
                    }
                }));
            });
        </script>
    @endif

    {{-- Chart.js Local Library --}}
    <script src="{{ asset('js/chart.min.js') }}"></script>

    {{-- Charts Initialization --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === 1. Expenses Breakdown Pie Chart ===
            const expensesCtx = document.getElementById('expensesChart');
            if (expensesCtx) {
                const expensesData = @json($expensesByCategory);

                const expensesLabels = expensesData.map(item => item.category_name);
                const expensesValues = expensesData.map(item => parseFloat(item.total));

                const backgroundColors = [
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(249, 1152, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(132042, 0.8)',
                    'rgba(14, 16533, 0.8)',
                    'rgba(139, 9246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                ];

                new Chart(expensesCtx, {
                    type: 'pie',
                    data: {
                        labels: expensesLabels,
                        datasets: [{
                            label: 'المبلغ',
                            data: expensesValues,
                            backgroundColor: backgroundColors.slice(0, expensesLabels.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        family: 'Outfit'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) label += ': ';
                                        label += new Intl.NumberFormat('en-US', {
                                            minimumFractionDigits: 2
                                        }).format(context.parsed) + ' YER';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // === 2. Daily Trend Bar Chart ===
            const dailyCtx = document.getElementById('dailyTrendChart');
            if (dailyCtx) {
                const dailyData = @json($dailyTrend);
                const dates = Object.keys(dailyData).sort();
                const incomeData = [];
                const expenseData = [];

                dates.forEach(date => {
                    const dayData = dailyData[date];
                    let incomeValue = 0,
                        expenseValue = 0;
                    dayData.forEach(item => {
                        if (item.type === 'in') incomeValue += parseFloat(item.total);
                        else if (item.type === 'out') expenseValue += parseFloat(item.total);
                    });
                    incomeData.push(incomeValue);
                    expenseData.push(expenseValue);
                });

                const formattedDates = dates.map(date => new Date(date).getDate());

                new Chart(dailyCtx, {
                    type: 'bar',
                    data: {
                        labels: formattedDates,
                        datasets: [{
                                label: 'إيرادات',
                                data: incomeData,
                                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'مصروفات',
                                data: expenseData,
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => new Intl.NumberFormat('en-US').format(value)
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        family: 'Outfit'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        label += new Intl.NumberFormat('en-US', {
                                            minimumFractionDigits: 2
                                        }).format(context.parsed.y) + ' YER';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });

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
