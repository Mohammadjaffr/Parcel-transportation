@extends('layouts.app')
@section('title', 'Cash Box - Transaction Management')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{ filterType: 'all' }">

        {{-- Balance Cards --}}
        <div class="flex gap-6">

            {{-- Current Balance (All) --}}
            <div @click="filterType = 'all'"
                :class="filterType === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">الرصيد
                        الحالي</span>
                    <h4 class="text-xl font-black {{ $balance >= 0 ? 'text-brand-500' : 'text-error-500' }}">
                        {{ number_format($balance) }} <span class="text-xs font-semibold">ر.ي</span>
                    </h4>
                </div>
            </div>

            {{-- Total Income --}}
            <div @click="filterType = 'in'"
                :class="filterType === 'in' ? 'border-success-500 ring-2 ring-success-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 11l5-5m0 0l5 5m-5-5v12" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        الإيرادات</span>
                    <h4 class="text-xl font-black text-success-500">{{ number_format($income) }} <span
                            class="text-xs font-semibold">ر.ي</span></h4>
                </div>
            </div>

            {{-- Total Expenses --}}
            <div @click="filterType = 'out'"
                :class="filterType === 'out' ? 'border-error-500 ring-2 ring-error-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        المصروفات</span>
                    <h4 class="text-xl font-black text-error-500">{{ number_format($expense) }} <span
                            class="text-xs font-semibold">ر.ي</span></h4>
                </div>
            </div>
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

                    {{-- Category Filter Section --}}
                    @php
                        $selectedCategoryId = request('category_id');
                        $selectedCategoryName = 'جميع الفئات';
                        if ($selectedCategoryId) {
                            $selectedCategory = $categories->find($selectedCategoryId);
                            if ($selectedCategory) {
                                $selectedCategoryName = $selectedCategory->name;
                            }
                        }
                    @endphp
                    <div class="flex-1" x-data="{
                        open: false,
                        search: '',
                        selectedCategory: {{ $selectedCategoryId ? $selectedCategoryId : 'null' }},
                        selectedCategoryName: '{{ $selectedCategoryName }}',
                        categories: {{ $categories->toJson() }},
                        get filteredCategories() {
                            if (!this.search) return this.categories;
                            return this.categories.filter(cat =>
                                cat.name.toLowerCase().includes(this.search.toLowerCase())
                            );
                        },
                        selectCategory(category) {
                            if (category) {
                                this.selectedCategory = category.id;
                                this.selectedCategoryName = category.name;
                            } else {
                                this.selectedCategory = null;
                                this.selectedCategoryName = 'جميع الفئات';
                            }
                            this.open = false;
                            this.search = '';
                        }
                    }" @click.outside="open = false">
                        <div class="flex gap-3 items-center mb-3">
                            <div
                                class="flex justify-center items-center w-8 h-8 rounded-lg bg-brand-50 dark:bg-brand-500/10">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">الفئة</span>
                        </div>

                        <div class="relative">
                            {{-- Hidden Input --}}
                            <input type="hidden" name="category_id" :value="selectedCategory"
                                x-show="selectedCategory !== null">

                            {{-- Custom Dropdown Button --}}
                            <button type="button" @click="open = !open"
                                class="flex justify-between items-center px-3 w-full h-11 text-sm text-right rounded-xl border transition-all bg-brand-50 border-brand-200 dark:bg-brand-900 dark:border-brand-700 hover:border-brand-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                                <span x-text="selectedCategoryName" class="font-semibold"></span>
                                <svg class="w-4 h-4 transition-transform text-brand-400" :class="open && 'rotate-180'"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95" style="display: none;"
                                class="overflow-hidden absolute right-0 z-50 mt-2 w-full bg-white rounded-xl border border-gray-200 shadow-xl dark:bg-gray-800 dark:border-gray-700">

                                {{-- Search Input --}}
                                <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                    <input type="text" x-model="search" placeholder="ابحث عن فئة..."
                                        class="px-3 py-2 w-full text-sm bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-900 dark:border-gray-700 dark:text-white focus:outline-none focus:border-brand-400"
                                        @click.stop>
                                </div>

                                {{-- Categories List --}}
                                <div class="overflow-y-auto" style="max-height: 320px; overflow-x: hidden;">
                                    {{-- All Categories Option --}}
                                    <div @click="selectCategory(null)"
                                        :class="selectedCategory === null && 'bg-brand-50 dark:bg-brand-500/10'"
                                        class="flex justify-between items-center px-4 py-2.5 text-sm transition-colors cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 dark:text-white">
                                        <span class="font-semibold">جميع الفئات</span>
                                    </div>

                                    {{-- Category Items --}}
                                    <template x-for="category in filteredCategories" :key="category.id">
                                        <div @click="selectCategory(category)"
                                            :class="selectedCategory === category.id && 'bg-brand-50 dark:bg-brand-500/10'"
                                            class="flex justify-between items-center px-4 py-2.5 text-sm transition-colors cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <span class="font-semibold text-gray-700 dark:text-gray-200"
                                                x-text="category.name"></span>
                                            {{-- Type Indicator --}}
                                            <template x-if="category.type === 'in'">
                                                <span
                                                    class="inline-flex gap-1 items-center px-2 py-1 text-xs font-bold rounded-md bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                                    </svg>
                                                    إيراد
                                                </span>
                                            </template>
                                            <template x-if="category.type === 'out'">
                                                <span
                                                    class="inline-flex gap-1 items-center px-2 py-1 text-xs font-bold rounded-md bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                                    </svg>
                                                    مصروف
                                                </span>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- No Results --}}
                                    <div x-show="filteredCategories.length === 0"
                                        class="px-4 py-6 text-sm text-center text-gray-400 dark:text-gray-500">
                                        لا توجد نتائج
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Filters --}}
                    @php
                        $now = now();

                        // ========== فلاتر يومية ==========
                        $isToday =
                            $startDate->isSameDay($now->copy()->startOfDay()) &&
                            $endDate->isSameDay($now->copy()->endOfDay());

                        $isYesterday =
                            $startDate->isSameDay($now->copy()->subDay()->startOfDay()) &&
                            $endDate->isSameDay($now->copy()->subDay()->endOfDay());

                        // ========== فلاتر أسبوعية ==========
                        $isThisWeek =
                            $startDate->isSameDay($now->copy()->startOfWeek()) &&
                            $endDate->isSameDay($now->copy()->endOfWeek());

                        $isLastWeek =
                            $startDate->isSameDay($now->copy()->subWeek()->startOfWeek()) &&
                            $endDate->isSameDay($now->copy()->subWeek()->endOfWeek());

                        // ========== فلاتر شهرية ==========
                        $isThisMonth =
                            $startDate->isSameDay($now->copy()->startOfMonth()) &&
                            $endDate->isSameDay($now->copy()->endOfMonth());

                        $isLastMonth =
                            $startDate->isSameDay($now->copy()->subMonth()->startOfMonth()) &&
                            $endDate->isSameDay($now->copy()->subMonth()->endOfMonth());

                        $isLast3Months =
                            $startDate->isSameDay($now->copy()->subMonths(3)->startOfMonth()) &&
                            $endDate->isSameDay($now->copy()->endOfMonth());

                        $isLast6Months =
                            $startDate->isSameDay($now->copy()->subMonths(6)->startOfMonth()) &&
                            $endDate->isSameDay($now->copy()->endOfMonth());

                        // ========== فلاتر سنوية ==========
                        $isThisYear =
                            $startDate->isSameDay($now->copy()->startOfYear()) &&
                            $endDate->isSameDay($now->copy()->endOfYear());

                        // ========== فلتر الكل ==========
                        $isAllTime =
                            !$isToday &&
                            !$isYesterday &&
                            !$isThisWeek &&
                            !$isLastWeek &&
                            !$isThisMonth &&
                            !$isLastMonth &&
                            !$isLast3Months &&
                            !$isLast6Months &&
                            !$isThisYear;
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
                            {{-- اليوم --}}
                            <button type="button" onclick="setDateRange('today')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isToday ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                اليوم
                            </button>
                            {{-- أمس --}}
                            <button type="button" onclick="setDateRange('yesterday')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isYesterday ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                أمس
                            </button>
                            {{-- الأسبوع الحالي --}}
                            <button type="button" onclick="setDateRange('thisWeek')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isThisWeek ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                الأسبوع الحالي
                            </button>
                            {{-- الأسبوع الماضي --}}
                            <button type="button" onclick="setDateRange('lastWeek')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isLastWeek ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                الأسبوع الماضي
                            </button>
                            {{-- الشهر الحالي --}}
                            <button type="button" onclick="setDateRange('thisMonth')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isThisMonth ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                الشهر الحالي
                            </button>
                            {{-- الشهر الماضي --}}
                            <button type="button" onclick="setDateRange('lastMonth')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isLastMonth ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                الشهر الماضي
                            </button>
                            {{-- آخر 3 أشهر --}}
                            <button type="button" onclick="setDateRange('last3Months')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isLast3Months ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                آخر 3 أشهر
                            </button>
                            {{-- آخر 6 أشهر --}}
                            <button type="button" onclick="setDateRange('last6Months')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isLast6Months ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                آخر 6 أشهر
                            </button>
                            {{-- هذه السنة --}}
                            <button type="button" onclick="setDateRange('thisYear')"
                                class="px-3 py-2 text-xs font-bold rounded-lg transition-all {{ $isThisYear ? 'text-brand-600 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10' : 'text-gray-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                                هذه السنة
                            </button>
                            {{-- الكل --}}
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
                            {{ $endDate->format('Y-m-d') }}
                        </p>
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
                {{-- <a href="{{ route('transaction-categories.index') }}"
                    class="inline-flex gap-1.5 items-center px-4 h-10 text-xs font-bold text-gray-600 bg-gray-100 rounded-xl transition-all duration-200 dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    الفئات
                </a> --}}
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
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                رقم الإيداع</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                المرفق</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                المستخدم</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($transactions as $transaction)
                            <tr x-show="filterType === 'all' || filterType === '{{ $transaction->category->type }}'"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="transition-colors hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
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

                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $transaction->reference_number ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($transaction->attachment_path)
                                        <a href="{{ asset($transaction->attachment_path) }}" download
                                            class="inline-flex justify-center items-center w-8 h-8 rounded-lg transition-all text-brand-500 bg-brand-50 hover:bg-brand-100 dark:bg-brand-500/10 dark:hover:bg-brand-500/20"
                                            title="تحميل المرفق">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $transaction->user->name }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex gap-2 justify-center">
                                        <a href="{{ route('transactions.receipt', $transaction->id) }}" target="_blank"
                                            class="inline-flex gap-1.5 items-center px-3 py-2 text-xs font-bold text-white rounded-lg shadow-sm transition-all duration-200 bg-brand-500 hover:bg-brand-600 hover:shadow-md"
                                            title="طباعة سند">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            سند
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-16 text-center">
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

        // ========== دالة الفلاتر السريعة ==========
        function setDateRange(preset) {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const today = new Date();
            let startDate, endDate;

            switch (preset) {
                // === فلاتر يومية ===
                case 'today':
                    startDate = new Date(today);
                    endDate = new Date(today);
                    break;

                case 'yesterday':
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    startDate = yesterday;
                    endDate = yesterday;
                    break;

                    // === فلاتر أسبوعية ===
                case 'thisWeek':
                    const dayOfWeek = today.getDay();
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - dayOfWeek); // بداية الأسبوع (الأحد)
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6); // نهاية الأسبوع (السبت)
                    break;

                case 'lastWeek':
                    const lastWeekDay = today.getDay();
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - lastWeekDay - 7); // بداية الأسبوع الماضي
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6); // نهاية الأسبوع الماضي
                    break;

                    // === فلاتر شهرية ===
                case 'thisMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;

                case 'lastMonth':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;

                case 'last3Months':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 3, 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;

                case 'last6Months':
                    startDate = new Date(today.getFullYear(), today.getMonth() - 6, 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;

                    // === فلاتر سنوية ===
                case 'thisYear':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = new Date(today.getFullYear(), 11, 31);
                    break;

                    // === فلتر الكل ===
                case 'allTime':
                    startDate = new Date(today.getFullYear() - 10, 0, 1);
                    endDate = today;
                    break;

                default:
                    return;
            }

            // تنسيق التواريخ وإرسال النموذج
            const formatDate = (date) =>
                `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

            startDateInput.value = formatDate(startDate);
            endDateInput.value = formatDate(endDate);
            document.getElementById('filterForm').submit();
        }
    </script>
@endsection
