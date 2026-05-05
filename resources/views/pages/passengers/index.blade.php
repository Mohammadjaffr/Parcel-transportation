@extends('layouts.app')

@section('title', 'إدارة الركاب')
@section('Breadcrumb', 'إدارة الركاب')

@section('content')

    <div x-data="passengerRegistry()"
         class="pb-24 space-y-6 min-h-screen font-body lg:pb-12"
         dir="rtl">

        {{-- ================= Header Section ================= --}}
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex gap-4 justify-between items-start">
                <div class="text-right">
                    <h1 class="text-2xl font-black md:text-3xl text-on-surface dark:text-white">
                        إدارة الركاب
                    </h1>

                    <p class="mt-1 text-sm font-bold text-gray-500 dark:text-bodydark">
                        إجمالي {{ $passengers->total() }} راكب مسجل
                    </p>
                </div>

                <button type="button"
                    @click="openCreateModal()"
                    class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>إضافة راكب جديد</span>
                </button>
            </div>
        </div>

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-2 mx-auto max-w-7xl xl:grid-cols-3 md:gap-6">

            <div class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border ring-2 shadow-sm transition-all border-primary ring-primary/20 dark:bg-boxdark">
                <div class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">groups</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        إجمالي الركاب
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $passengers->total() }}
                    </h4>
                </div>
            </div>

            <div class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all dark:bg-boxdark border-r-emerald-500 dark:border-r-emerald-500 dark:border-boxdark-2">
                <div class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">group_add</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">
                        إجمالي العدد
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($passengers->getCollection()->sum('count'), 0) }}
                    </h4>
                </div>
            </div>

            <div class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all dark:bg-boxdark border-r-amber-500 dark:border-r-amber-500 dark:border-boxdark-2">
                <div class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-amber-500 uppercase">
                        إجمالي العمولة
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($passengers->getCollection()->sum('total_commission'), 0) }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- ====================== Search & Table Section ====================== --}}
        <div class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="flex flex-col gap-4 justify-between items-stretch md:flex-row md:items-center">

                    <div class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-[420px] dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">

                        <input type="text"
                            x-model="searchQuery"
                            @input.debounce.300ms="updateVisibility()"
                            placeholder="ابحث برقم الراكب، المكان، السائق، العميل، أو الفرع..."
                            class="pr-12 pl-12 w-full h-12 text-sm font-bold placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                        <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-outlined text-[22px]">search</span>
                        </div>

                        <button type="button"
                            x-show="searchQuery.length > 0"
                            @click="searchQuery = ''; updateVisibility()"
                            x-cloak
                            class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all -translate-y-1/2 dark:bg-boxdark dark:border-boxdark-2 hover:text-error active:scale-95">
                            <span class="text-[18px] material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="flex gap-2 items-center text-xs font-black text-gray-500 dark:text-bodydark">
                        <span class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        </span>

                        <span>
                            النتائج المعروضة:
                            <span class="text-primary" x-text="visibleCount"></span>
                            من
                            <span>{{ $passengers->count() }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Mobile View --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse ($passengers as $passenger)
                    @php
                        $dayName = $passenger->date ? \Carbon\Carbon::parse($passenger->date)->translatedFormat('l') : '---';
                    @endphp

                    <div class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all passenger-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm"
                        x-show="showRow(
                            @js($passenger->passenger_number),
                            @js($passenger->location),
                            @js($passenger->driver->name ?? ''),
                            @js($passenger->customer->name ?? ''),
                            @js($passenger->branch->name ?? '')
                        )">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center min-w-0">
                                <div class="flex justify-center items-center w-12 h-12 text-lg font-black text-white rounded-xl shadow-inner bg-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">person</span>
                                </div>

                                <div class="flex flex-col gap-1 min-w-0">
                                    <span class="text-sm font-black truncate text-on-surface dark:text-white font-headline">
                                        {{ $passenger->passenger_number }}
                                    </span>

                                    <div class="flex gap-1.5 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                        <span>{{ optional($passenger->date)->format('Y-m-d') }} - {{ $dayName }}</span>
                                    </div>
                                </div>
                            </div>

                            <div x-data="{ menuOpen: false }" class="relative shrink-0">
                                <button @click="menuOpen = !menuOpen"
                                    @click.away="menuOpen = false"
                                    class="p-2 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="menuOpen"
                                    x-transition
                                    x-cloak
                                    class="absolute left-0 top-full z-[999] py-1.5 mt-2 w-52 rounded-2xl border border-gray-100 shadow-lg backdrop-blur-md bg-white/95 dark:bg-boxdark-2/95 dark:border-boxdark overflow-hidden">

                                    <a href="{{ route('passengers.show', $passenger->id) }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark dark:hover:text-blue-400">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        عرض التفاصيل
                                    </a>

                                    <button type="button"
                                        @click="menuOpen = false; openEditModal({
                                            id: {{ $passenger->id }},
                                            date: @js(optional($passenger->date)->format('Y-m-d')),
                                            passenger_number: @js($passenger->passenger_number),
                                            location: @js($passenger->location),
                                            count: @js($passenger->count),
                                            total_commission: @js($passenger->total_commission),
                                            customer_id: @js($passenger->customer_id),
                                            customer_name: @js($passenger->customer->name ?? ''),
                                            customer_phone: @js($passenger->customer->phone ?? $passenger->passenger_number),
                                            driver_id: @js($passenger->driver_id),
                                            driver_name: @js($passenger->driver->name ?? ''),
                                            driver_phone: @js($passenger->driver->phone ?? ''),
                                            note: @js($passenger->note)
                                        })"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                        تعديل البيانات
                                    </button>

                                    <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                    <button type="button"
                                        @click="menuOpen = false; openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                        حذف الراكب
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المكان</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $passenger->location ?: 'غير محدد' }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">السائق</span>
                                <span class="text-xs font-bold text-primary">
                                    {{ $passenger->driver->name ?? 'غير محدد' }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">العدد</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $passenger->count ?? 0 }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">العمولة</span>
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400">
                                    {{ number_format($passenger->total_commission ?? 0, 0) }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">العميل</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $passenger->customer->name ?? 'غير محدد' }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">الفرع</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $passenger->branch->name ?? 'غير محدد' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col gap-3 items-center py-16 text-center text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:text-bodydark dark:border-boxdark-2 bg-surface dark:bg-boxdark-2">
                        <span class="material-symbols-outlined text-[40px] opacity-30">group_off</span>
                        <p class="text-sm font-bold">لا توجد بيانات ركاب مطابقة.</p>
                    </div>
                @endforelse

                <div x-show="visibleCount === 0 && {{ $passengers->count() }} > 0"
                    x-cloak
                    class="py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex flex-col justify-center items-center">
                        <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                        <h4 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            لا توجد نتائج تطابق بحثك في هذه الصفحة.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Desktop View --}}
            <div class="hidden overflow-visible w-full lg:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
                            <th class="px-6 py-4">التاريخ</th>
                            <th class="px-6 py-4">رقم الراكب</th>
                            <th class="px-6 py-4 text-center">المكان</th>
                            <th class="px-6 py-4 text-center">العدد والعمولة</th>
                            <th class="px-6 py-4 text-center">السائق</th>
                            <th class="px-6 py-4 text-center">العميل / الفرع</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse ($passengers as $passenger)
                            @php
                                $dayName = $passenger->date ? \Carbon\Carbon::parse($passenger->date)->translatedFormat('l') : '---';
                            @endphp

                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group passenger-row"
                                x-show="showRow(
                                    @js($passenger->passenger_number),
                                    @js($passenger->location),
                                    @js($passenger->driver->name ?? ''),
                                    @js($passenger->customer->name ?? ''),
                                    @js($passenger->branch->name ?? '')
                                )">

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-black text-gray-800 dark:text-white">
                                            {{ optional($passenger->date)->format('Y-m-d') }}
                                        </span>
                                        <span class="text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                            {{ $dayName }}
                                        </span>
                                    </div>
                                </td>

                               <td class="px-6 py-4">
    <div class="flex gap-4 items-center">
        <div class="flex justify-center items-center w-11 h-11 text-lg font-black text-white rounded-lg shadow-inner bg-primary">
            {{ mb_substr($passenger->customer->name ?? 'ر', 0, 1, 'UTF-8') }}
        </div>

        <div class="flex flex-col gap-1 min-w-0">
            <span class="text-sm font-black text-gray-800 truncate dark:text-white">
                {{ $passenger->customer->name ?? 'راكب' }}
            </span>

            <x-phone-number 
                :value="$passenger->passenger_number"
                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
        </div>
    </div>
</td>

                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                        {{ $passenger->location ?: 'غير محدد' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            العدد: {{ $passenger->count ?? 0 }}
                                        </span>

                                        <span class="text-xs font-black text-amber-600 dark:text-amber-400">
                                            {{ number_format($passenger->total_commission ?? 0, 0) }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-black text-primary">
                                        {{ $passenger->driver->name ?? 'غير محدد' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                            {{ $passenger->customer->name ?? 'غير محدد' }}
                                        </span>

                                        <span class="px-2.5 py-1 text-[10px] font-bold text-gray-500 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                            {{ $passenger->branch->name ?? 'غير محدد' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="relative px-6 py-4 text-center">
                                    <div x-data="{ open: false }"
                                         class="inline-block relative text-right"
                                         @click.away="open = false">

                                        <button @click="open = !open"
                                            type="button"
                                            title="خيارات"
                                            class="inline-flex justify-center items-center w-9 h-9 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-all hover:bg-gray-100 hover:text-gray-600 hover:border-gray-200 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2 dark:hover:text-gray-300 active:scale-95">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        <div x-show="open"
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute left-0 top-full mt-2 z-[999] w-52 bg-white/95 backdrop-blur-md rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark/95 dark:border-boxdark-2 focus:outline-none origin-top-left overflow-hidden"
                                            style="display: none;">

                                            <div class="py-1" role="menu">
                                                <a href="{{ route('passengers.show', $passenger->id) }}"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark-2 dark:hover:text-blue-400">
                                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                    عرض التفاصيل
                                                </a>

                                                <button type="button"
                                                    @click="open = false; openEditModal({
                                                        id: {{ $passenger->id }},
                                                        date: @js(optional($passenger->date)->format('Y-m-d')),
                                                        passenger_number: @js($passenger->passenger_number),
                                                        location: @js($passenger->location),
                                                        count: @js($passenger->count),
                                                        total_commission: @js($passenger->total_commission),
                                                        customer_id: @js($passenger->customer_id),
                                                        customer_name: @js($passenger->customer->name ?? ''),
                                                        customer_phone: @js($passenger->customer->phone ?? $passenger->passenger_number),
                                                        driver_id: @js($passenger->driver_id),
                                                        driver_name: @js($passenger->driver->name ?? ''),
                                                        driver_phone: @js($passenger->driver->phone ?? ''),
                                                        note: @js($passenger->note)
                                                    })"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark-2 dark:hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                    تعديل البيانات
                                                </button>

                                                <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                                <button type="button"
                                                    @click="open = false; openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    حذف الراكب
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span class="material-symbols-outlined text-[28px] text-gray-400">group_off</span>
                                        </div>

                                        <div>
                                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                                لا توجد بيانات للركاب
                                            </h3>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                لم نعثر على أي ركاب مسجلين في النظام حالياً.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $passengers->count() }} > 0" x-cloak>
                            <td colspan="7" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[28px] text-gray-400">search_off</span>
                                    </div>

                                    <div>
                                        <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                            لا توجد نتائج مطابقة
                                        </h3>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            لم نعثر على ركاب يطابقون كلمة البحث المدخلة.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($passengers->hasPages())
                <div class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $passengers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- Create Modal --}}
        <div x-show="showCreateModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div class="relative w-full max-w-3xl bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-6 border-b border-gray-50 dark:border-boxdark-2">
                    <div>
                        <h3 class="text-xl font-black text-on-surface dark:text-white">
                            إضافة راكب جديد
                        </h3>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            رقم الراكب يربط العميل تلقائياً، ورقم السائق يربط أو ينشئ السائق تلقائياً.
                        </p>
                    </div>

                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-4 mb-6 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">الفرع الحالي</span>
                    <div class="mt-1 text-sm font-black text-gray-800 dark:text-white">
                        {{ $currentBranch->name ?? 'غير محدد' }}
                    </div>
                </div>

                <form action="{{ route('passengers.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                التاريخ <span class="text-error">*</span>
                            </label>
                            <input type="date" name="date" required
                                value="{{ now()->format('Y-m-d') }}"
                                class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                        </div>

                        {{-- رقم الراكب + اسم الراكب --}}
                        <div class="relative"
                            x-data="customerPhonePicker(
                                @js($customers->map(fn($c) => [
                                    'id' => $c->id,
                                    'name' => $c->name,
                                    'phone' => $c->phone,
                                ])->values()),
                                @js(array_values(config('countries', [])))
                            )">

                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                رقم الراكب <span class="text-error">*</span>
                            </label>

                            <input type="hidden" name="passenger_number" :value="fullPhoneNumber">
                            <input type="hidden" name="customer_id" x-model="selectedCustomerId">

                            <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40"
                                :class="selectedCustomerId ? 'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' : ''"
                                style="direction: ltr;">

                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button"
                                        @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex justify-center items-center px-3 w-14 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                        <template x-if="selectedCountry?.svg">
                                            <div class="flex overflow-hidden justify-center items-center w-7 h-5 rounded-md ring-1 ring-gray-200 shadow-sm dark:ring-boxdark"
                                                x-html="selectedCountry.svg"></div>
                                        </template>
                                    </button>

                                    <div x-show="openCountryDropdown" x-cloak x-transition
                                        class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                        <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                            <input type="text"
                                                x-model="searchCountryQuery"
                                                placeholder="بحث..."
                                                class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                dir="rtl">
                                        </div>

                                        <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                    @click="selectedCountry = country; openCountryDropdown = false; searchCustomer()"
                                                    class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                    <div class="flex overflow-hidden justify-center items-center w-6 h-4 rounded-sm shadow-sm"
                                                        x-html="country.svg"></div>
                                                    <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                        x-text="country.name"></span>
                                                    <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500"
                                                        x-text="country.dial_code"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <input type="tel"
                                    x-model="localPhoneNumber"
                                    @input="searchCustomer"
                                    @focus="showCustomerDropdown = true"
                                    @click.away="showCustomerDropdown = false"
                                    placeholder="7XXXXXXXX"
                                    required
                                    inputmode="numeric"
                                    autocomplete="off"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                    :class="selectedCustomerId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">

                                <button type="button"
                                    x-show="selectedCustomerId"
                                    @click="resetSelection"
                                    class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            <div x-show="showCustomerDropdown && localPhoneNumber.length > 0 && !selectedCustomerId"
                                x-transition
                                x-cloak
                                class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">

                                <template x-for="customer in filteredCustomers" :key="customer.id">
                                    <button type="button"
                                        @click="selectCustomer(customer)"
                                        class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-sm font-bold text-on-surface dark:text-white" x-text="customer.name"></span>
                                            <span class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right" x-text="customer.phone"></span>
                                        </div>
                                        <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                    </button>
                                </template>

                                <div x-show="filteredCustomers.length === 0"
                                    class="px-4 py-3 text-xs font-bold text-gray-500 bg-surface dark:bg-boxdark-2 dark:text-bodydark">
                                    الرقم غير موجود. اكتب اسم الراكب في الحقل أسفل الرقم وسيتم حفظه تلقائياً.
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="block mb-2 text-xs font-black text-gray-500 dark:text-gray-400">
                                    اسم الراكب
                                    <span class="text-[10px] font-bold text-gray-400">(يتعبأ تلقائياً إذا الرقم موجود)</span>
                                </label>

                                <div class="relative">
                                    <input type="text"
                                        name="customer_name"
                                        x-model="nameInput"
                                        :readonly="selectedCustomerId"
                                        :required="!selectedCustomerId"
                                        placeholder="اكتب اسم الراكب"
                                        class="px-4 pr-10 w-full h-11 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/30"
                                        :class="selectedCustomerId ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' : ''">

                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]"
                                        :class="selectedCustomerId ? 'text-emerald-500' : 'text-gray-400'">person</span>
                                </div>

                                <template x-if="selectedCustomerId">
                                    <p class="mt-1.5 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                        تم العثور على الراكب وربطه بهذا الرقم.
                                    </p>
                                </template>

                                <template x-if="!selectedCustomerId && localPhoneNumber.length > 0">
                                    <p class="mt-1.5 text-[11px] font-bold text-gray-400">
                                        إذا كان الرقم جديداً، سيتم إنشاء الراكب بهذا الاسم.
                                    </p>
                                </template>
                            </div>
                        </div>

                        {{-- رقم السائق --}}
                        <div class="relative"
                            x-data="driverSelect(
                                @js($drivers->map(fn($d) => [
                                    'id' => $d->id,
                                    'name' => $d->name,
                                    'phone' => $d->phone,
                                ])->values()),
                                @js(array_values(config('countries', [])))
                            )">

                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                رقم السائق <span class="text-error">*</span>
                            </label>

                            <input type="hidden" name="driver_id" x-model="selectedDriverId">
                            <input type="hidden" name="driver_phone" :value="fullPhoneNumber">
                            <input type="hidden" name="driver_name" :value="nameInput">

                            <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40"
                                :class="selectedDriverId ? 'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' : ''"
                                style="direction: ltr;">

                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button"
                                        @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">

                                        <template x-if="selectedCountry?.svg">
                                            <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden"
                                                x-html="selectedCountry.svg"></div>
                                        </template>

                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300"
                                            x-text="selectedCountry?.dial_code"></span>
                                    </button>

                                    <div x-show="openCountryDropdown"
                                        x-cloak
                                        x-transition
                                        class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">

                                        <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                            <input type="text"
                                                x-model="searchCountryQuery"
                                                placeholder="بحث..."
                                                class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                dir="rtl">
                                        </div>

                                        <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                    @click="selectedCountry = country; openCountryDropdown = false; searchDriver()"
                                                    class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">

                                                    <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                        x-html="country.svg"></div>

                                                    <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                        x-text="country.name"></span>

                                                    <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500"
                                                        x-text="country.dial_code"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <input type="tel"
                                    x-model="localPhoneNumber"
                                    @input="searchDriver"
                                    @focus="showDriverDropdown = true"
                                    @click.away="showDriverDropdown = false"
                                    placeholder="7XXXXXXXX"
                                    required
                                    inputmode="numeric"
                                    autocomplete="off"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                    :class="selectedDriverId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">

                                <button type="button"
                                    x-show="selectedDriverId"
                                    @click="resetSelection"
                                    class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId"
                                x-transition
                                x-cloak
                                class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">

                                <template x-for="driver in filteredDrivers" :key="driver.id">
                                    <button type="button"
                                        @click="selectDriver(driver)"
                                        class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">

                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-sm font-bold text-on-surface dark:text-white"
                                                x-text="driver.name"></span>

                                            <span class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                x-text="driver.phone"></span>
                                        </div>

                                        <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">
                                            arrow_back_ios
                                        </span>
                                    </button>
                                </template>

                                <div x-show="filteredDrivers.length === 0"
                                    class="px-4 py-3 bg-surface dark:bg-boxdark-2">
                                    <span class="block mb-2 text-xs font-bold text-gray-500 dark:text-bodydark">
                                        سائق جديد، أدخل اسمه وسيتم حفظه تلقائياً.
                                    </span>

                                    <input type="text"
                                        x-model="nameInput"
                                        placeholder="اسم السائق الجديد"
                                        class="px-3 w-full h-10 text-xs bg-white rounded-lg border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30">
                                </div>
                            </div>

                            <template x-if="selectedDriverId">
                                <div class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    تم اختيار السائق:
                                    <span x-text="nameInput"></span>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                المكان <span class="text-error">*</span>
                            </label>
                            <input type="text" name="location" required
                                placeholder="مثلاً: عدن - كريتر"
                                class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                عدد الركاب <span class="text-error">*</span>
                            </label>
                            <input type="number" name="count" min="1" value="1" required
                                class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                إجمالي العمولة <span class="text-error">*</span>
                            </label>
                            <input type="number" name="total_commission" min="0" step="0.01" required
                                placeholder="0.00"
                                class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                            ملاحظات
                        </label>
                        <textarea name="note" rows="3"
                            placeholder="أي ملاحظات إضافية..."
                            class="px-4 py-3 w-full rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40"></textarea>
                    </div>

                    <button type="submit"
                        class="flex gap-2 justify-center items-center w-full h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover active:scale-95">
                        <span class="material-symbols-outlined">save</span>
                        حفظ الراكب
                    </button>
                </form>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div class="relative w-full max-w-3xl bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-6 border-b border-gray-50 dark:border-boxdark-2">
                    <div>
                        <h3 class="text-xl font-black text-on-surface dark:text-white">
                            تعديل بيانات الراكب
                        </h3>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            تغيير الرقم يربط أو ينشئ العميل والسائق تلقائياً.
                        </p>
                    </div>

                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-4 mb-6 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">الفرع الحالي</span>
                    <div class="mt-1 text-sm font-black text-gray-800 dark:text-white">
                        {{ $currentBranch->name ?? 'غير محدد' }}
                    </div>
                </div>

                <form :action="editPassengerData.url" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                التاريخ <span class="text-error">*</span>
                            </label>
                            <input type="date" name="date" x-model="editPassengerData.date" required
                                class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                        </div>

                        {{-- رقم الراكب + اسم الراكب --}}
                        <div class="relative"
                            x-data="customerPhonePicker(
                                @js($customers->map(fn($c) => [
                                    'id' => $c->id,
                                    'name' => $c->name,
                                    'phone' => $c->phone,
                                ])->values()),
                                @js(array_values(config('countries', []))),
                                {
                                    id: editPassengerData.customer_id,
                                    name: editPassengerData.customer_name,
                                    phone: editPassengerData.customer_phone || editPassengerData.passenger_number
                                }
                            )"
                            x-effect="loadInitial({
                                id: editPassengerData.customer_id,
                                name: editPassengerData.customer_name,
                                phone: editPassengerData.customer_phone || editPassengerData.passenger_number
                            })">

                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                رقم الراكب <span class="text-error">*</span>
                            </label>

                            <input type="hidden" name="passenger_number" :value="fullPhoneNumber">
                            <input type="hidden" name="customer_id" x-model="selectedCustomerId">

                            <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40"
                                :class="selectedCustomerId ? 'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' : ''"
                                style="direction: ltr;">

                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button"
                                        @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex justify-center items-center px-3 w-14 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                        <template x-if="selectedCountry?.svg">
                                            <div class="flex overflow-hidden justify-center items-center w-7 h-5 rounded-md ring-1 ring-gray-200 shadow-sm dark:ring-boxdark"
                                                x-html="selectedCountry.svg"></div>
                                        </template>
                                    </button>

                                    <div x-show="openCountryDropdown" x-cloak x-transition
                                        class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                        <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                            <input type="text"
                                                x-model="searchCountryQuery"
                                                placeholder="بحث..."
                                                class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                dir="rtl">
                                        </div>

                                        <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                    @click="selectedCountry = country; openCountryDropdown = false; searchCustomer()"
                                                    class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                    <div class="flex overflow-hidden justify-center items-center w-6 h-4 rounded-sm shadow-sm"
                                                        x-html="country.svg"></div>
                                                    <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                        x-text="country.name"></span>
                                                    <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500"
                                                        x-text="country.dial_code"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <input type="tel"
                                    x-model="localPhoneNumber"
                                    @input="searchCustomer"
                                    @focus="showCustomerDropdown = true"
                                    @click.away="showCustomerDropdown = false"
                                    placeholder="7XXXXXXXX"
                                    required
                                    inputmode="numeric"
                                    autocomplete="off"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                    :class="selectedCustomerId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">

                                <button type="button"
                                    x-show="selectedCustomerId"
                                    @click="resetSelection"
                                    class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            <div x-show="showCustomerDropdown && localPhoneNumber.length > 0 && !selectedCustomerId"
                                x-transition
                                x-cloak
                                class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">

                                <template x-for="customer in filteredCustomers" :key="customer.id">
                                    <button type="button"
                                        @click="selectCustomer(customer)"
                                        class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-sm font-bold text-on-surface dark:text-white" x-text="customer.name"></span>
                                            <span class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right" x-text="customer.phone"></span>
                                        </div>
                                        <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                    </button>
                                </template>

                                <div x-show="filteredCustomers.length === 0"
                                    class="px-4 py-3 text-xs font-bold text-gray-500 bg-surface dark:bg-boxdark-2 dark:text-bodydark">
                                    الرقم غير موجود. اكتب اسم الراكب في الحقل أسفل الرقم وسيتم حفظه تلقائياً.
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="block mb-2 text-xs font-black text-gray-500 dark:text-gray-400">
                                    اسم الراكب
                                    <span class="text-[10px] font-bold text-gray-400">(يتعبأ من بيانات الراكب الحالية)</span>
                                </label>

                                <div class="relative">
                                    <input type="text"
                                        name="customer_name"
                                        x-model="nameInput"
                                        :readonly="selectedCustomerId"
                                        :required="!selectedCustomerId"
                                        placeholder="اكتب اسم الراكب"
                                        class="px-4 pr-10 w-full h-11 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/30"
                                        :class="selectedCustomerId ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' : ''">

                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]"
                                        :class="selectedCustomerId ? 'text-emerald-500' : 'text-gray-400'">person</span>
                                </div>

                                <template x-if="selectedCustomerId">
                                    <p class="mt-1.5 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                        الراكب مرتبط بعميل موجود.
                                    </p>
                                </template>

                                <template x-if="!selectedCustomerId && localPhoneNumber.length > 0">
                                    <p class="mt-1.5 text-[11px] font-bold text-gray-400">
                                        إذا كان الرقم جديداً، سيتم إنشاء الراكب بهذا الاسم.
                                    </p>
                                </template>
                            </div>
                        </div>

                        {{-- رقم السائق --}}
                        <div class="relative"
                            x-data="driverSelect(
                                @js($drivers->map(fn($d) => [
                                    'id' => $d->id,
                                    'name' => $d->name,
                                    'phone' => $d->phone,
                                ])->values()),
                                @js(array_values(config('countries', []))),
                                {
                                    id: editPassengerData.driver_id,
                                    name: editPassengerData.driver_name,
                                    phone: editPassengerData.driver_phone
                                }
                            )"
                            x-effect="loadInitial({
                                id: editPassengerData.driver_id,
                                name: editPassengerData.driver_name,
                                phone: editPassengerData.driver_phone
                            })">

                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                رقم السائق <span class="text-error">*</span>
                            </label>

                            <input type="hidden" name="driver_id" x-model="selectedDriverId">
                            <input type="hidden" name="driver_phone" :value="fullPhoneNumber">
                            <input type="hidden" name="driver_name" :value="nameInput">

                            <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40"
                                :class="selectedDriverId ? 'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' : ''"
                                style="direction: ltr;">

                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button"
                                        @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                        <template x-if="selectedCountry?.svg">
                                            <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden"
                                                x-html="selectedCountry.svg"></div>
                                        </template>
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300"
                                            x-text="selectedCountry?.dial_code"></span>
                                    </button>

                                    <div x-show="openCountryDropdown" x-cloak x-transition
                                        class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                        <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                            <input type="text"
                                                x-model="searchCountryQuery"
                                                placeholder="بحث..."
                                                class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                dir="rtl">
                                        </div>

                                        <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                    @click="selectedCountry = country; openCountryDropdown = false; searchDriver()"
                                                    class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                    <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                        x-html="country.svg"></div>
                                                    <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                        x-text="country.name"></span>
                                                    <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500"
                                                        x-text="country.dial_code"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <input type="tel"
                                    x-model="localPhoneNumber"
                                    @input="searchDriver"
                                    @focus="showDriverDropdown = true"
                                    @click.away="showDriverDropdown = false"
                                    placeholder="7XXXXXXXX"
                                    required
                                    inputmode="numeric"
                                    autocomplete="off"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                    :class="selectedDriverId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">

                                <button type="button"
                                    x-show="selectedDriverId"
                                    @click="resetSelection"
                                    class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId"
                                x-transition
                                x-cloak
                                class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">

                                <template x-for="driver in filteredDrivers" :key="driver.id">
                                    <button type="button"
                                        @click="selectDriver(driver)"
                                        class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">

                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-sm font-bold text-on-surface dark:text-white"
                                                x-text="driver.name"></span>
                                            <span class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                x-text="driver.phone"></span>
                                        </div>

                                        <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">
                                            arrow_back_ios
                                        </span>
                                    </button>
                                </template>

                                <div x-show="filteredDrivers.length === 0"
                                    class="px-4 py-3 bg-surface dark:bg-boxdark-2">
                                    <span class="block mb-2 text-xs font-bold text-gray-500 dark:text-bodydark">
                                        سائق جديد، أدخل اسمه وسيتم حفظه تلقائياً.
                                    </span>

                                    <input type="text"
                                        x-model="nameInput"
                                        placeholder="اسم السائق الجديد"
                                        class="px-3 w-full h-10 text-xs bg-white rounded-lg border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30">
                                </div>
                            </div>

                            <template x-if="selectedDriverId">
                                <div class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                    تم اختيار السائق:
                                    <span x-text="nameInput"></span>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                المكان <span class="text-error">*</span>
                            </label>
                            <input type="text" name="location" x-model="editPassengerData.location" required
                                class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                عدد الركاب <span class="text-error">*</span>
                            </label>
                            <input type="number" name="count" x-model="editPassengerData.count" min="1" required
                                class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                إجمالي العمولة <span class="text-error">*</span>
                            </label>
                            <input type="number" name="total_commission" x-model="editPassengerData.total_commission" min="0" step="0.01" required
                                class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                            ملاحظات
                        </label>
                        <textarea name="note" rows="3" x-model="editPassengerData.note"
                            class="px-4 py-3 w-full rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40"></textarea>
                    </div>

                    <button type="submit"
                        class="flex gap-2 justify-center items-center w-full h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover active:scale-95">
                        <span class="material-symbols-outlined">update</span>
                        حفظ التعديلات
                    </button>
                </form>
            </div>
        </div>

        {{-- Delete Modal --}}
        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div class="relative w-full max-w-md bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-8 text-center pointer-events-auto">

                <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-error rounded-[1.5rem] shadow-sm">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black text-on-surface dark:text-white">
                    تأكيد الحذف
                </h3>

                <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                    هل أنت متأكد من حذف الراكب رقم:
                    <br>
                    <span class="text-base font-bold text-on-surface dark:text-white"
                        x-text="deletePassengerData.passenger_number"></span>؟
                    <br>
                    <span class="inline-block mt-2 text-error/80">
                        لا يمكن التراجع عن هذا الإجراء.
                    </span>
                </p>

                <form :action="deletePassengerData.url" method="POST" class="flex gap-3 w-full">
                    @csrf
                    @method('DELETE')

                    <button type="button" @click="closeModals()"
                        class="flex-1 h-12 text-sm font-bold text-gray-600 rounded-xl transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-boxdark active:scale-95">
                        تراجع
                    </button>

                    <button type="submit"
                        class="flex-1 h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-error hover:bg-error/90 shadow-error/30 active:scale-95">
                        نعم، احذف
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
        function passengerRegistry() {
            return {
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                searchQuery: '',
                visibleCount: {{ $passengers->count() }},

                editPassengerData: {
                    id: '',
                    date: '',
                    passenger_number: '',
                    location: '',
                    count: '',
                    total_commission: '',
                    customer_id: '',
                    customer_name: '',
                    customer_phone: '',
                    driver_id: '',
                    driver_name: '',
                    driver_phone: '',
                    note: '',
                    url: ''
                },

                deletePassengerData: {
                    id: '',
                    passenger_number: '',
                    url: ''
                },

                init() {
                    this.updateVisibility();
                },

                openCreateModal() {
                    this.showCreateModal = true;
                },

                openEditModal(passenger) {
                    this.editPassengerData = {
                        id: passenger.id || '',
                        date: passenger.date || '',
                        passenger_number: passenger.passenger_number || '',
                        location: passenger.location || '',
                        count: passenger.count || 1,
                        total_commission: passenger.total_commission || 0,
                        customer_id: passenger.customer_id || '',
                        customer_name: passenger.customer_name || '',
                        customer_phone: passenger.customer_phone || passenger.passenger_number || '',
                        driver_id: passenger.driver_id || '',
                        driver_name: passenger.driver_name || '',
                        driver_phone: passenger.driver_phone || '',
                        note: passenger.note || '',
                        url: '{{ route('passengers.index') }}/' + passenger.id
                    };

                    this.showEditModal = true;
                },

                openDeleteModal(id, passengerNumber) {
                    this.deletePassengerData = {
                        id: id,
                        passenger_number: passengerNumber,
                        url: '{{ route('passengers.index') }}/' + id
                    };

                    this.showDeleteModal = true;
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showDeleteModal = false;
                },

                showRow(passengerNumber, location, driverName, customerName, branchName) {
                    const query = this.searchQuery.toLowerCase().trim();

                    if (!query) {
                        return true;
                    }

                    return String(passengerNumber || '').toLowerCase().includes(query)
                        || String(location || '').toLowerCase().includes(query)
                        || String(driverName || '').toLowerCase().includes(query)
                        || String(customerName || '').toLowerCase().includes(query)
                        || String(branchName || '').toLowerCase().includes(query);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.passenger-row:not([style*="display: none"])').length;
                    });
                }
            }
        }

        function phoneSearchBase(recordsList, countriesList, initialData = null, type = 'record') {
            return {
                records: recordsList || [],
                countries: countriesList || [],
                filteredRecords: [],
                localPhoneNumber: '',
                nameInput: '',
                selectedRecordId: null,
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                showDropdown: false,
                loadedInitialKey: null,

                init() {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0] || null;

                    if (initialData && (initialData.phone || initialData.id)) {
                        this.loadInitial(initialData);
                    }
                },

                get filteredCountries() {
                    if (this.searchCountryQuery === '') {
                        return this.countries;
                    }

                    const query = this.searchCountryQuery.toLowerCase();

                    return this.countries.filter(c =>
                        String(c.name || '').toLowerCase().includes(query)
                        || String(c.dial_code || '').includes(query)
                    );
                },

                get fullPhoneNumber() {
                    if (!this.localPhoneNumber) {
                        return '';
                    }

                    let dialCode = this.selectedCountry
                        ? String(this.selectedCountry.dial_code || '').replace('+', '')
                        : '';

                    return dialCode + String(this.localPhoneNumber || '').replace(/[^0-9]/g, '');
                },

                cleanPhone(value) {
                    return String(value || '').replace(/[^\d]/g, '');
                },

                detectCountry(phone) {
                    const clean = this.cleanPhone(phone);

                    const matchedCountry = [...this.countries]
                        .sort((a, b) => String(b.dial_code || '').length - String(a.dial_code || '').length)
                        .find(country => {
                            const code = String(country.dial_code || '').replace('+', '');
                            return clean.startsWith(code);
                        });

                    return matchedCountry || this.countries.find(c => c.code === 'YE') || this.countries[0] || null;
                },

                setPhone(phone) {
                    const clean = this.cleanPhone(phone);
                    const country = this.detectCountry(clean);

                    this.selectedCountry = country;

                    const dialCode = country
                        ? String(country.dial_code || '').replace('+', '')
                        : '';

                    this.localPhoneNumber = clean.startsWith(dialCode)
                        ? clean.substring(dialCode.length)
                        : clean;
                },

                loadInitial(data) {
                    const key = JSON.stringify(data || {});

                    if (!data || this.loadedInitialKey === key) {
                        return;
                    }

                    this.loadedInitialKey = key;
                    this.selectedRecordId = data.id || null;
                    this.nameInput = data.name || '';

                    if (data.phone) {
                        this.setPhone(data.phone);
                    }
                },

                searchRecord() {
                    this.localPhoneNumber = this.cleanPhone(this.localPhoneNumber);
                    this.selectedRecordId = null;

                    const query = this.fullPhoneNumber.trim();

                    if (this.localPhoneNumber.trim() === '') {
                        this.filteredRecords = [];
                        this.showDropdown = false;
                        this.nameInput = '';
                        return;
                    }

                    this.filteredRecords = this.records.filter(record => {
                        const phone = this.cleanPhone(record.phone);
                        return phone.includes(query) || query.includes(phone);
                    });

                    const exact = this.filteredRecords.find(record => {
                        return this.cleanPhone(record.phone) === query;
                    });

                    if (exact) {
                        this.selectRecord(exact);
                        return;
                    }

                    this.nameInput = '';
                    this.showDropdown = true;
                },

                selectRecord(record) {
                    this.selectedRecordId = record.id;
                    this.nameInput = record.name;
                    this.setPhone(record.phone);
                    this.showDropdown = false;
                },

                resetSelection() {
                    this.selectedRecordId = null;
                    this.localPhoneNumber = '';
                    this.nameInput = '';
                    this.filteredRecords = [];
                    this.showDropdown = false;
                }
            }
        }

        function customerPhonePicker(customersList, countriesList, initialData = null) {
            const picker = phoneSearchBase(customersList, countriesList, initialData, 'customer');

            Object.defineProperties(picker, {
                selectedCustomerId: {
                    get() {
                        return this.selectedRecordId;
                    },
                    set(value) {
                        this.selectedRecordId = value;
                    }
                },
                filteredCustomers: {
                    get() {
                        return this.filteredRecords;
                    }
                },
                showCustomerDropdown: {
                    get() {
                        return this.showDropdown;
                    },
                    set(value) {
                        this.showDropdown = value;
                    }
                }
            });

            picker.searchCustomer = function () {
                this.searchRecord();
            };

            picker.selectCustomer = function (customer) {
                this.selectRecord(customer);
            };

            return picker;
        }

        function driverSelect(driversList, countriesList, initialData = null) {
            const picker = phoneSearchBase(driversList, countriesList, initialData, 'driver');

            Object.defineProperties(picker, {
                selectedDriverId: {
                    get() {
                        return this.selectedRecordId;
                    },
                    set(value) {
                        this.selectedRecordId = value;
                    }
                },
                filteredDrivers: {
                    get() {
                        return this.filteredRecords;
                    }
                },
                showDriverDropdown: {
                    get() {
                        return this.showDropdown;
                    },
                    set(value) {
                        this.showDropdown = value;
                    }
                }
            });

            picker.searchDriver = function () {
                this.searchRecord();
            };

            picker.selectDriver = function (driver) {
                this.selectRecord(driver);
            };

            return picker;
        }
    </script>
@endsection