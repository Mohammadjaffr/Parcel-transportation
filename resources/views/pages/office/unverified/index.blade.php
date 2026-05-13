@extends('layouts.app')

@section('title', 'المكاتب الخارجية')

@section('content')

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl" x-data="officesRegistry()">

        {{-- ====================== Header ====================== --}}
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex gap-4 justify-between items-start">
                <div class="text-right">
                    <h1 class="text-2xl font-black md:text-3xl text-on-surface dark:text-white">
                        المكاتب الخارجية
                    </h1>
                    <p class="mt-1 text-sm font-bold text-gray-500 dark:text-bodydark">
                        إجمالي {{ $offices->total() ?? 0 }} مكتب
                    </p>
                </div>

                <a href="{{ route('offices.create') }}"
                    class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-[20px]">add_business</span>
                    <span>إضافة مكتب جديد</span>
                </a>
            </div>
        </div>

        @php
            $pageOffices = $offices->getCollection();

            $totalBranches = $pageOffices->sum(fn($office) => $office->branches->count());

            $officesWithBranches = $pageOffices->filter(fn($office) => $office->branches->count() > 0)->count();

            $citiesCount = $pageOffices
                ->flatMap(fn($office) => $office->branches->pluck('city'))
                ->filter()
                ->unique()
                ->count();
        @endphp

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-2 mx-auto max-w-7xl xl:grid-cols-4 md:gap-6">

            {{-- إجمالي المكاتب --}}
            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-gray-100 shadow-sm transition-all cursor-default dark:bg-boxdark hover:shadow-md hover:border-primary/50 dark:border-boxdark-2">

                <div
                    class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">storefront</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        إجمالي المكاتب
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $offices->total() ?? 0 }}
                    </h4>
                </div>
            </div>

            {{-- إجمالي الفروع --}}
            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all cursor-default dark:bg-boxdark hover:shadow-md border-r-indigo-500 dark:border-r-indigo-500 hover:border-indigo-300 dark:border-boxdark-2">

                <div
                    class="flex justify-center items-center w-12 h-12 text-indigo-500 bg-indigo-50 rounded-xl dark:bg-indigo-500/10">
                    <span class="material-symbols-outlined text-[24px]">account_tree</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-indigo-500 uppercase">
                        إجمالي الفروع
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $totalBranches }}
                    </h4>
                </div>
            </div>

            {{-- مكاتب لديها فروع --}}
            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all cursor-default dark:bg-boxdark hover:shadow-md border-r-emerald-500 dark:border-r-emerald-500 hover:border-emerald-300 dark:border-boxdark-2">

                <div
                    class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">domain_add</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">
                        مكاتب لديها فروع
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $officesWithBranches }}
                    </h4>
                </div>
            </div>

            {{-- المدن --}}
            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all cursor-default dark:bg-boxdark hover:shadow-md border-r-blue-500 dark:border-r-blue-500 hover:border-blue-300 dark:border-boxdark-2">

                <div
                    class="flex justify-center items-center w-12 h-12 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                    <span class="material-symbols-outlined text-[24px]">public</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-blue-500 uppercase">
                        المدن المسجلة
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $citiesCount }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- ====================== Search & Table Section ====================== --}}
        <div
            class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div
                    class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-96 dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">

                    <input type="text" x-model="searchQuery" @input.debounce.300ms="updateVisibility()"
                        placeholder="ابحث باسم المكتب، المدينة، رقم الهاتف أو العنوان..."
                        class="pr-12 pl-12 w-full h-12 text-sm font-medium placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                    <div
                        class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>

                    <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; updateVisibility()"
                        x-cloak
                        class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-gray-100 rounded-xl transition-all -translate-y-1/2 dark:bg-boxdark hover:text-error active:scale-95">
                        <span class="text-[18px] material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            {{-- ====================== Mobile View ====================== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse($offices as $office)
                    @php
                        $cities = $office->branches->pluck('city')->unique()->filter()->join('، ');
                        $branchesNames = $office->branches->pluck('name')->filter()->join('، ');
                        $phones = $office->branches->pluck('phone')->filter()->join(' ');
                        $addresses = $office->branches->pluck('address')->filter()->join(' ');

                        $searchText =
                            $office->name . ' ' .
                            $cities . ' ' .
                            $branchesNames . ' ' .
                            $phones . ' ' .
                            $addresses . ' ' .
                            ($office->creator->name ?? '');
                    @endphp

                    <div class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all office-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm"
                        x-show="showRow(@js($searchText))">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center min-w-0">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-white rounded-xl shadow-inner bg-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">corporate_fare</span>
                                </div>

                                <div class="flex flex-col gap-1 min-w-0">
                                    <span
                                        class="text-sm font-black truncate text-on-surface dark:text-white font-headline">
                                        {{ $office->name }}
                                    </span>

                                    <div
                                        class="flex gap-1.5 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">person</span>
                                        <span>أُضيف بواسطة: {{ $office->creator->name ?? 'النظام' }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile Actions --}}
                            <div x-data="{ menuOpen: false }" class="relative shrink-0">
                                <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false"
                                    class="p-2 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="menuOpen" x-transition x-cloak
                                    class="absolute left-0 top-full z-[999] py-1.5 mt-2 w-56 rounded-2xl border border-gray-100 shadow-lg backdrop-blur-md bg-white/95 dark:bg-boxdark-2/95 dark:border-boxdark overflow-hidden">

                                    <a href="{{ route('offices.show', $office->id) }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark dark:hover:text-blue-400">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        التفاصيل
                                    </a>

                                    <a href="{{ route('offices.edit', $office->id) }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark dark:hover:text-primary">
                                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                        تعديل البيانات
                                    </a>

                                    <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                    <button type="button"
                                        @click="
                                            menuOpen = false;
                                            openDeleteModal({ id: {{ $office->id }}, name: @js($office->name) });
                                        "
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold transition-colors text-error hover:bg-error/10 dark:hover:bg-error/10">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                        حذف المكتب
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">النطاق الجغرافي</span>
                                <span class="text-xs font-bold text-primary">
                                    {{ $cities ?: 'غير محدد' }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">عدد الفروع</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $office->branches->count() }} فرع
                                </span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex gap-2 items-center mb-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                <span class="text-[10px] font-black tracking-widest text-gray-400 uppercase">
                                    الفروع التابعة
                                </span>
                            </div>

                            <div class="flex flex-col gap-2">
                                @forelse($office->branches as $branch)
                                    <div
                                        class="flex justify-between items-center p-3 bg-white rounded-xl border border-gray-100 dark:bg-boxdark dark:border-boxdark-2">
                                        <div class="flex gap-3 items-center min-w-0">
                                            <div
                                                class="flex justify-center items-center w-9 h-9 rounded-lg bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                                                <span class="material-symbols-outlined text-[18px]">location_city</span>
                                            </div>

                                            <div class="min-w-0">
                                                <p class="text-xs font-black truncate text-on-surface dark:text-white">
                                                    {{ $branch->name }}
                                                </p>
                                                <p class="mt-0.5 text-[10px] font-bold text-gray-500 dark:text-bodydark truncate">
                                                    {{ $branch->city ?: 'مدينة غير محددة' }}
                                                </p>
                                            </div>
                                        </div>

                                        @if($branch->phone)
                                            <a href="tel:{{ $branch->phone }}"
                                                class="flex justify-center items-center w-9 h-9 text-emerald-600 bg-emerald-50 rounded-xl transition-all dark:bg-emerald-500/10 dark:text-emerald-400 active:scale-95">
                                                <span class="material-symbols-outlined text-[17px]">call</span>
                                            </a>
                                        @endif
                                    </div>
                                @empty
                                    <div
                                        class="py-6 text-xs font-bold text-center text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:border-boxdark dark:text-gray-500">
                                        لا توجد فروع مسجلة لهذا المكتب
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col gap-3 items-center py-16 text-center text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:text-bodydark dark:border-boxdark-2 bg-surface dark:bg-boxdark-2">
                        <span class="material-symbols-outlined text-[40px] opacity-30">storefront</span>
                        <p class="text-sm font-bold">لا توجد مكاتب مضافة حالياً.</p>
                    </div>
                @endforelse

                <div x-show="visibleCount === 0 && {{ $offices->count() }} > 0" x-cloak
                    class="py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex flex-col justify-center items-center">
                        <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">
                            search_off
                        </span>
                        <h4 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            لا توجد مكاتب تطابق بحثك في هذه الصفحة.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ====================== Desktop View ====================== --}}
            <div class="hidden overflow-visible w-full lg:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr
                            class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
                            <th class="px-6 py-4">المكتب الرئيسي</th>
                            <th class="px-6 py-4">النطاق الجغرافي</th>
                            <th class="px-6 py-4 text-center">الفروع</th>
                            <th class="px-6 py-4 text-center">أُضيف بواسطة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse($offices as $office)
                            @php
                                $cities = $office->branches->pluck('city')->unique()->filter()->join('، ');
                                $branchesNames = $office->branches->pluck('name')->filter()->join('، ');
                                $phones = $office->branches->pluck('phone')->filter()->join(' ');
                                $addresses = $office->branches->pluck('address')->filter()->join(' ');

                                $searchText =
                                    $office->name . ' ' .
                                    $cities . ' ' .
                                    $branchesNames . ' ' .
                                    $phones . ' ' .
                                    $addresses . ' ' .
                                    ($office->creator->name ?? '');
                            @endphp

                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group office-row"
                                x-show="showRow(@js($searchText))">

                                {{-- المكتب الرئيسي --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-4 items-center">
                                        <div
                                            class="flex justify-center items-center w-11 h-11 text-white rounded-lg shadow-inner bg-primary">
                                            <span class="material-symbols-outlined text-[20px]">corporate_fare</span>
                                        </div>

                                        <div class="flex flex-col gap-1 min-w-0">
                                            <span class="text-sm font-black text-gray-800 dark:text-white truncate max-w-[220px]">
                                                {{ $office->name }}
                                            </span>
                                            <span
                                                class="flex gap-1 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                                <span class="material-symbols-outlined text-[13px]">storefront</span>
                                                مكتب خارجي غير موثوق
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- النطاق الجغرافي --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-black text-gray-800 dark:text-white truncate max-w-[180px]">
                                            {{ $cities ?: 'غير محدد' }}
                                        </span>

                                        <span class="text-[11px] font-bold text-primary truncate max-w-[220px]">
                                            {{ $branchesNames ?: 'لا توجد فروع مسجلة' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- الفروع --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1.5 items-center">
                                        <span
                                            class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                            {{ $office->branches->count() }} فرع
                                        </span>

                                        @if ($office->branches->count() > 0)
                                            <span
                                                class="flex items-center gap-1 text-[10px] font-black text-indigo-600 dark:text-indigo-400">
                                                <span class="material-symbols-outlined text-[14px]">account_tree</span>
                                                فروع مسجلة
                                            </span>
                                        @else
                                            <span
                                                class="flex items-center gap-1 text-[10px] font-black text-gray-400 dark:text-gray-500">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                بدون فروع
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- أُضيف بواسطة --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="text-sm font-black text-gray-800 dark:text-white">
                                            {{ $office->creator->name ?? 'النظام' }}
                                        </span>

                                        <span
                                            class="px-2.5 py-1 text-[10px] font-black rounded-lg bg-gray-50 text-gray-500 dark:bg-boxdark-2 dark:text-bodydark">
                                            إدارة المكاتب
                                        </span>
                                    </div>
                                </td>

                                {{-- الإجراءات --}}
                                <td class="relative px-6 py-4 text-center">
                                    <div x-data="{ open: false }" class="inline-block relative text-right"
                                        @click.away="open = false">

                                        <button @click="open = !open" type="button" title="خيارات"
                                            class="inline-flex justify-center items-center w-9 h-9 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-all hover:bg-gray-100 hover:text-gray-600 hover:border-gray-200 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2 dark:hover:text-gray-300 active:scale-95">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute left-0 top-full mt-2 z-[999] w-56 bg-white/95 backdrop-blur-md rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark/95 dark:border-boxdark-2 focus:outline-none origin-top-left overflow-hidden"
                                            style="display: none;">

                                            <div class="py-1" role="menu">
                                                <a href="{{ route('offices.show', $office->id) }}"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark-2 dark:hover:text-blue-400">
                                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                    التفاصيل
                                                </a>

                                                <a href="{{ route('offices.edit', $office->id) }}"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark-2 dark:hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                                    تعديل البيانات
                                                </a>

                                                <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                                <button type="button"
                                                    @click="
                                                        open = false;
                                                        openDeleteModal({ id: {{ $office->id }}, name: @js($office->name) });
                                                    "
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold transition-colors text-error hover:bg-error/10 dark:hover:bg-error/10">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    حذف المكتب
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div
                                            class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span
                                                class="material-symbols-outlined text-[28px] text-gray-400">storefront</span>
                                        </div>

                                        <div>
                                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                                لا توجد مكاتب
                                            </h3>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                لم نعثر على أي مكاتب غير موثوقة في النظام حالياً.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $offices->count() }} > 0" x-cloak>
                            <td colspan="5" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div
                                        class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[28px] text-gray-400">search_off</span>
                                    </div>

                                    <div>
                                        <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                            لا توجد نتائج مطابقة
                                        </h3>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            لم نعثر على مكاتب تطابق كلمة البحث المدخلة.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($offices->hasPages())
                <div
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $offices->links() }}
                </div>
            @endif
        </div>

        {{-- ====================== Delete Modal ====================== --}}
        <div x-show="showDeleteModal" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-md p-7 text-center bg-white border border-gray-100 shadow-2xl pointer-events-auto dark:bg-boxdark rounded-[2rem] dark:border-boxdark-2">
                <div
                    class="flex items-center justify-center w-20 h-20 mx-auto mb-5 rounded-[1.5rem] bg-error/10 text-error">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-2 text-2xl font-black font-headline text-on-surface dark:text-white">
                    تأكيد الحذف
                </h3>

                <p class="mb-7 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                    هل أنت متأكد من حذف المكتب
                    <span class="block mt-1 text-base font-black text-on-surface dark:text-white"
                        x-text="deleteOfficeData.name"></span>
                    <span class="block mt-2 text-xs text-error">
                        سيتم حذف الفروع التابعة له أيضاً.
                    </span>
                </p>

                <form :action="deleteOfficeData.url" method="POST" @submit="isSubmitting = true"
                    class="grid grid-cols-2 gap-3">
                    @csrf
                    @method('DELETE')

                    <button type="button" @click="closeModals()"
                        class="h-12 text-sm font-black text-gray-600 rounded-xl transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-300 active:scale-95">
                        تراجع
                    </button>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex gap-2 justify-center items-center h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-error shadow-error/20 active:scale-95 disabled:opacity-70">
                        <span x-show="!isSubmitting">نعم، احذف</span>
                        <span x-show="isSubmitting"
                            class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        function officesRegistry() {
            return {
                showDeleteModal: false,
                searchQuery: @js(request('search', '')),
                visibleCount: {{ $offices->count() }},
                isSubmitting: false,
                deleteOfficeData: {
                    id: '',
                    name: '',
                    url: ''
                },

                openDeleteModal(office) {
                    this.deleteOfficeData = {
                        id: office.id,
                        name: office.name,
                        url: '{{ url('/offices') }}/' + office.id
                    };

                    this.showDeleteModal = true;
                },

                closeModals() {
                    this.showDeleteModal = false;
                },

                showRow(searchText) {
                    const query = this.searchQuery.toLowerCase().trim();

                    if (!query) {
                        return true;
                    }

                    return String(searchText || '').toLowerCase().includes(query);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.office-row:not([style*="display: none"])')
                            .length;
                    });
                }
            }
        }
    </script>
@endsection