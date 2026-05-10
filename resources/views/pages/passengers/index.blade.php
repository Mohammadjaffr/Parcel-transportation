@extends('layouts.app')

@section('title', 'إدارة الركاب')
@section('Breadcrumb', 'إدارة الركاب')

@section('content')

    <div x-data="passengerRegistry()" class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl">

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

                <button type="button" @click="openCreateModal()"
                    class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>إضافة راكب جديد</span>
                </button>
            </div>
        </div>

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-2 mx-auto max-w-7xl xl:grid-cols-3 md:gap-6">
            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border ring-2 shadow-sm transition-all border-primary ring-primary/20 dark:bg-boxdark">
                <div
                    class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">groups</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">إجمالي
                        الركاب</span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">{{ $passengers->total() }}</h4>
                </div>
            </div>

            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all dark:bg-boxdark border-r-emerald-500 dark:border-r-emerald-500 dark:border-boxdark-2">
                <div
                    class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">group_add</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">إجمالي العدد</span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($passengers->getCollection()->sum('count'), 0) }}</h4>
                </div>
            </div>

            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all dark:bg-boxdark border-r-amber-500 dark:border-r-amber-500 dark:border-boxdark-2">
                <div
                    class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-amber-500 uppercase">إجمالي العمولة</span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($passengers->getCollection()->sum('total_commission'), 0) }}</h4>
                </div>
            </div>
        </div>

        {{-- ====================== Search & Table Section ====================== --}}
        <div
            class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="flex flex-col gap-4 justify-between items-stretch md:flex-row md:items-center">
                    <div class="flex flex-col gap-3 w-full md:flex-row md:items-center">
                        <div
                            class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-[420px] dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="updateVisibility()"
                                placeholder="ابحث برقم الراكب، المكان، السائق، العميل..."
                                class="pr-12 pl-12 w-full h-12 text-sm font-bold placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">
                            <div
                                class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                                <span class="material-symbols-outlined text-[22px]">search</span>
                            </div>
                            <button type="button" x-show="searchQuery.length > 0"
                                @click="searchQuery = ''; updateVisibility()" x-cloak
                                class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all -translate-y-1/2 dark:bg-boxdark dark:border-boxdark-2 hover:text-error active:scale-95">
                                <span class="text-[18px] material-symbols-outlined">close</span>
                            </button>
                        </div>

                        <div
                            class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-56 dark:border-boxdark-2 bg-surface dark:bg-boxdark-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                            <select x-model="statusFilter" @change="updateVisibility()"
                                class="pr-11 pl-9 w-full h-12 text-sm font-black bg-transparent rounded-2xl border-none appearance-none outline-none focus:ring-0 text-on-surface dark:text-white">
                                <option value="">كل الحالات</option>
                                <option value="pending">قيد الانتظار</option>
                                <option value="completed">مكتمل</option>
                                <option value="cancel">ملغي</option>
                            </select>
                            <div
                                class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">tune</span></div>
                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">expand_more</span></div>
                        </div>
                    </div>

                    <div class="flex gap-2 items-center text-xs font-black text-gray-500 dark:text-bodydark">
                        <span
                            class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        </span>
                        <span>النتائج المعروضة: <span class="text-primary" x-text="visibleCount"></span> من
                            <span>{{ $passengers->count() }}</span></span>
                    </div>
                </div>
            </div>

            {{-- Desktop View Table --}}
            <div class="hidden overflow-visible w-full lg:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr
                            class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
                            <th class="px-6 py-4">التاريخ</th>
                            <th class="px-6 py-4">رقم الراكب</th>
                            <th class="px-6 py-4">رقم واسم العميل</th>
                            <th class="px-6 py-4 text-center">السائق</th>
                            <th class="px-6 py-4 text-center">المكان</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">العدد والعمولة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse ($passengers as $passenger)
                            @php
                                $dayName = $passenger->date
                                    ? \Carbon\Carbon::parse($passenger->date)->translatedFormat('l')
                                    : '---';
                                $statusKey = strtolower($passenger->status ?? 'pending');

                                $statusLabel = match ($statusKey) {
                                    'completed' => 'مكتمل',
                                    'cancel' => 'ملغي',
                                    default => 'قيد الانتظار',
                                };
                                $statusClass = match ($statusKey) {
                                    'completed'
                                        => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
                                    'cancel' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
                                    default => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
                                };
                                $statusIcon = match ($statusKey) {
                                    'completed' => 'check_circle',
                                    'cancel' => 'cancel',
                                    default => 'schedule',
                                };
                            @endphp

                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group passenger-row"
                                x-show="showRow(@js($passenger->passenger_number), @js($passenger->location), @js($passenger->driver->name ?? ''), @js($passenger->customer->name ?? ''), @js($statusKey))">

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-sm font-black text-gray-800 dark:text-white">{{ optional($passenger->date)->format('Y-m-d') }}</span>
                                        <span
                                            class="text-[11px] font-bold text-gray-500 dark:text-bodydark">{{ $dayName }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm font-black text-primary dir-ltr">
                                        {{ $passenger->passenger_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex gap-3 items-center">
                                        <div
                                            class="flex justify-center items-center w-10 h-10 text-lg font-black text-white rounded-lg shadow-inner bg-slate-800 dark:bg-slate-700">
                                            {{ mb_substr($passenger->customer->name ?? 'ع', 0, 1, 'UTF-8') }}
                                        </div>
                                        <div class="flex flex-col gap-1 min-w-0">
                                            <span
                                                class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $passenger->customer->name ?? 'غير محدد' }}</span>
                                            <x-phone-number :value="$passenger->customer->phone ?? ''"
                                                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span
                                            class="text-xs font-black text-primary">{{ $passenger->driver->name ?? 'غير محدد' }}</span>
                                        <x-phone-number :value="$passenger->driver->phone ?? ''"
                                            class="text-[10px] font-bold text-gray-500 dark:text-bodydark" />
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">{{ $passenger->location ?: 'غير محدد' }}</span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex gap-1.5 items-center justify-center px-3 py-1.5 rounded-xl text-[11px] font-black {{ $statusClass }}">
                                        <span class="material-symbols-outlined text-[15px]">{{ $statusIcon }}</span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span
                                            class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">العدد:
                                            {{ $passenger->count ?? 0 }}</span>
                                        <span
                                            class="text-xs font-black text-amber-600 dark:text-amber-400">{{ number_format($passenger->total_commission ?? 0, 0) }}</span>
                                    </div>
                                </td>

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
        date: @js($passenger->date ? date('Y-m-d', strtotime($passenger->date)) : ''),
        passenger_number: @js($passenger->passenger_number),
        passenger_name: @js($passenger->passenger_name ?? ''),
        status: @js($statusKey),
        location: @js($passenger->location),
        count: @js($passenger->count),
        total_commission: @js($passenger->total_commission),
        customer_id: @js($passenger->customer_id),
        customer_name: @js($passenger->customer->name ?? ''),
        customer_phone: @js($passenger->customer->phone ?? ''),
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

                                                {{-- زر تعيين الحالة يظهر فقط إذا كانت الحالة قيد الانتظار --}}
                                                @if ($statusKey === 'pending')
                                                    <button type="button"
                                                        @click="open = false; openStatusModal({
            id: {{ $passenger->id }},
            date: @js($passenger->date ? date('Y-m-d', strtotime($passenger->date)) : ''),
            status: @js($statusKey),
            passenger_number: @js($passenger->passenger_number),
            location: @js($passenger->location),
            count: @js($passenger->count),
            total_commission: @js($passenger->total_commission),
            customer_phone: @js($passenger->customer->phone ?? ''),
            customer_name: @js($passenger->customer->name ?? ''),
            driver_phone: @js($passenger->driver->phone ?? ''),
            driver_name: @js($passenger->driver->name ?? ''),
            note: @js($passenger->note)
        })"
                                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-boxdark-2 dark:hover:text-emerald-400">
                                                        <span
                                                            class="material-symbols-outlined text-[18px]">fact_check</span>
                                                        تعيين الحالة
                                                    </button>
                                                    <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>
                                                @endif





                                                <button type="button"
                                                    @click="open = false; openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span> حذف
                                                    الراكب
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div
                                            class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span
                                                class="material-symbols-outlined text-[28px] text-gray-400">group_off</span>
                                        </div>
                                        <div>
                                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">لا توجد
                                                بيانات للركاب</h3>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">لم نعثر على أي
                                                ركاب مسجلين في النظام حالياً.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $passengers->count() }} > 0" x-cloak>
                            <td colspan="8" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div
                                        class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[28px] text-gray-400">search_off</span>
                                    </div>
                                    <div>
                                        <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">لا توجد نتائج
                                            مطابقة</h3>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">لم نعثر على ركاب
                                            يطابقون بحثك أو الحالة المحددة.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($passengers->hasPages())
                <div
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $passengers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- ====================== Create Modal ====================== --}}
        <div x-show="showCreateModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-4xl bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-6 border-b border-gray-50 dark:border-boxdark-2">
                    <div>
                        <h3 class="text-xl font-black text-on-surface dark:text-white">إضافة راكب جديد</h3>
                    </div>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('passengers.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-5">

                        <div
                            class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-2 bg-gray-50/50 dark:bg-boxdark-2/50 dark:border-boxdark-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">التاريخ <span
                                        class="text-error">*</span></label>
                                <input type="date" name="date" required value="{{ now()->format('Y-m-d') }}"
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">المكان <span
                                        class="text-error">*</span></label>
                                <input type="text" name="location" required placeholder="مثلاً: عدن - كريتر"
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-1">
                            <div class="relative" x-data="passengerPhonePicker(@js(array_values(config('countries', []))))">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم الراكب
                                    <span class="text-error">*</span></label>
                                <input type="hidden" name="passenger_number" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40"
                                    style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown"
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
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input
                                                    type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button"
                                                        @click="selectedCountry = country; openCountryDropdown = false"
                                                        class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                            x-html="country.svg"></div>
                                                        <span
                                                            class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                            x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX" required
                                        inputmode="numeric" autocomplete="off"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-2 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
                            x-data="recordPhonePicker(@js($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone])->values()), @js(array_values(config('countries', []))))">
                            <div class="relative">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم hg
                                    <span class="text-error">*</span></label>
                                <input type="hidden" name="customer_id" x-model="selectedRecordId">
                                <input type="hidden" name="customer_phone" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark focus-within:ring-2 focus-within:ring-primary/40"
                                    :class="selectedRecordId ?
                                        'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' :
                                        ''"
                                    style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                            class="flex gap-2 items-center px-3 h-12 bg-gray-50 rounded-l-xl border-r border-gray-200 transition-colors dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                            <template x-if="selectedCountry?.svg">
                                                <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden"
                                                    x-html="selectedCountry.svg"></div>
                                            </template>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300"
                                                x-text="selectedCountry?.dial_code"></span>
                                        </button>
                                        <div x-show="openCountryDropdown" x-cloak x-transition
                                            class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input
                                                    type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button"
                                                        @click="selectedCountry = country; openCountryDropdown = false; searchRecord()"
                                                        class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                            x-html="country.svg"></div>
                                                        <span
                                                            class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                            x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" @input="searchRecord"
                                        @focus="showDropdown = true" @click.away="showDropdown = false"
                                        placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                        :class="selectedRecordId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">
                                    <button type="button" x-show="selectedRecordId" @click="resetSelection"
                                        class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error"><span
                                            class="material-symbols-outlined text-[16px]">close</span></button>
                                </div>
                                <div x-show="showDropdown && localPhoneNumber.length > 0 && !selectedRecordId" x-transition
                                    x-cloak
                                    class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                    <template x-for="record in filteredRecords" :key="record.id">
                                        <button type="button" @click="selectRecord(record)"
                                            class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <div class="flex flex-col gap-0.5"><span
                                                    class="text-sm font-bold text-on-surface dark:text-white"
                                                    x-text="record.name"></span><span
                                                    class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                    x-text="record.phone"></span></div>
                                            <span
                                                class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اسم العميل
                                    <span class="text-error">*</span></label>
                                <div class="relative">
                                    <input type="text" name="customer_name"x-model="nameInput" :readonly="selectedRecordId !== ''" :required="selectedRecordId === ''"
                                        placeholder="اسم العميل"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30"
                                        :class="selectedRecordId ?
                                            'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' :
                                            ''">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]"
                                        :class="selectedRecordId ? 'text-emerald-500' : 'text-gray-400'">person</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-2 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
                            x-data="recordPhonePicker(@js($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values()), @js(array_values(config('countries', []))))">
                            <div class="relative">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم السائق
                                    <span class="text-error">*</span></label>
                                <input type="hidden" name="driver_id" x-model="selectedRecordId">
                                <input type="hidden" name="driver_phone" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark focus-within:ring-2 focus-within:ring-primary/40"
                                    :class="selectedRecordId ?
                                        'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' :
                                        ''"
                                    style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                            class="flex gap-2 items-center px-3 h-12 bg-gray-50 rounded-l-xl border-r border-gray-200 transition-colors dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                            <template x-if="selectedCountry?.svg">
                                                <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden"
                                                    x-html="selectedCountry.svg"></div>
                                            </template>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300"
                                                x-text="selectedCountry?.dial_code"></span>
                                        </button>
                                        <div x-show="openCountryDropdown" x-cloak x-transition
                                            class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input
                                                    type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button"
                                                        @click="selectedCountry = country; openCountryDropdown = false; searchRecord()"
                                                        class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                            x-html="country.svg"></div>
                                                        <span
                                                            class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                            x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" @input="searchRecord"
                                        @focus="showDropdown = true" @click.away="showDropdown = false"
                                        placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                        :class="selectedRecordId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">
                                    <button type="button" x-show="selectedRecordId" @click="resetSelection"
                                        class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error"><span
                                            class="material-symbols-outlined text-[16px]">close</span></button>
                                </div>
                                <div x-show="showDropdown && localPhoneNumber.length > 0 && !selectedRecordId" x-transition
                                    x-cloak
                                    class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                    <template x-for="record in filteredRecords" :key="record.id">
                                        <button type="button" @click="selectRecord(record)"
                                            class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <div class="flex flex-col gap-0.5"><span
                                                    class="text-sm font-bold text-on-surface dark:text-white"
                                                    x-text="record.name"></span><span
                                                    class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                    x-text="record.phone"></span></div>
                                            <span
                                                class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اسم السائق
                                    <span class="text-error">*</span></label>
                                <div class="relative">
                                    <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedRecordId !== ''" :required="selectedRecordId === ''"
                                        placeholder="اسم السائق"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30"
                                        :class="selectedRecordId ?
                                            'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' :
                                            ''">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]"
                                        :class="selectedRecordId ? 'text-emerald-500' : 'text-gray-400'">local_taxi</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عدد الركاب
                                    <span class="text-error">*</span></label>
                                <input type="number" name="count" min="1" value="1" required
                                    class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">إجمالي العمولة
                                    <span class="text-error">*</span></label>
                                <input type="number" name="total_commission" min="0" step="0.01" required
                                    placeholder="0.00"
                                    class="px-4 w-full h-12 font-black text-amber-600 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">ملاحظات</label>
                            <textarea name="note" rows="2" placeholder="أي ملاحظات إضافية..."
                                class="px-4 py-3 w-full rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40"></textarea>
                        </div>

                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="flex gap-2 justify-center items-center w-full h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover active:scale-95">
                            <span class="material-symbols-outlined">save</span> حفظ البيانات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================== Edit Modal ====================== --}}
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-4xl bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-6 border-b border-gray-50 dark:border-boxdark-2">
                    <div>
                        <h3 class="text-xl font-black text-on-surface dark:text-white">تعديل بيانات الراكب</h3>
                    </div>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="editPassengerData.url" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-5">
                        <div
                            class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-3 bg-gray-50/50 dark:bg-boxdark-2/50 dark:border-boxdark-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">التاريخ <span
                                        class="text-error">*</span></label>
                                <input type="date" name="date" x-model="editPassengerData.date" required
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">المكان <span
                                        class="text-error">*</span></label>
                                <input type="text" name="location" x-model="editPassengerData.location" required
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">الحالة <span
                                        class="text-error">*</span></label>
                                <select name="status" x-model="editPassengerData.status" required
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="cancel">ملغي</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="relative" x-data="passengerPhonePicker(@js(array_values(config('countries', []))))"
                                x-effect="loadInitial(editPassengerData.passenger_number)">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم الراكب
                                    <span class="text-error">*</span></label>
                                <input type="hidden" name="passenger_number" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40"
                                    style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown"
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
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input
                                                    type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button"
                                                        @click="selectedCountry = country; openCountryDropdown = false"
                                                        class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                            x-html="country.svg"></div>
                                                        <span
                                                            class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                            x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX" required
                                        inputmode="numeric" autocomplete="off"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-2 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
                            x-data="recordPhonePicker(@js($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone])->values()), @js(array_values(config('countries', []))))"
                            x-effect="loadInitial({ id: editPassengerData.customer_id, name: editPassengerData.customer_name, phone: editPassengerData.customer_phone })">
                            <div class="relative">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم العميل
                                    <span class="text-error">*</span></label>
                                <input type="hidden" name="customer_id" x-model="selectedRecordId">
                                <input type="hidden" name="customer_phone" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark focus-within:ring-2 focus-within:ring-primary/40"
                                    :class="selectedRecordId ?
                                        'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' :
                                        ''"
                                    style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                            class="flex gap-2 items-center px-3 h-12 bg-gray-50 rounded-l-xl border-r border-gray-200 transition-colors dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                            <template x-if="selectedCountry?.svg">
                                                <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden"
                                                    x-html="selectedCountry.svg"></div>
                                            </template>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300"
                                                x-text="selectedCountry?.dial_code"></span>
                                        </button>
                                        <div x-show="openCountryDropdown" x-cloak x-transition
                                            class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input
                                                    type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button"
                                                        @click="selectedCountry = country; openCountryDropdown = false; searchRecord()"
                                                        class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                            x-html="country.svg"></div>
                                                        <span
                                                            class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                            x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" @input="searchRecord"
                                        @focus="showDropdown = true" @click.away="showDropdown = false"
                                        placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                        :class="selectedRecordId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">
                                    <button type="button" x-show="selectedRecordId" @click="resetSelection"
                                        class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error"><span
                                            class="material-symbols-outlined text-[16px]">close</span></button>
                                </div>
                                <div x-show="showDropdown && localPhoneNumber.length > 0 && !selectedRecordId" x-transition
                                    x-cloak
                                    class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                    <template x-for="record in filteredRecords" :key="record.id">
                                        <button type="button" @click="selectRecord(record)"
                                            class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <div class="flex flex-col gap-0.5"><span
                                                    class="text-sm font-bold text-on-surface dark:text-white"
                                                    x-text="record.name"></span><span
                                                    class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                    x-text="record.phone"></span></div>
                                            <span
                                                class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اسم العميل
                                    <span class="text-error">*</span></label>
                                <div class="relative">
                                    <input type="text" name="customer_name" x-model="nameInput" :readonly="selectedRecordId !== ''" :required="selectedRecordId === ''"
                                        placeholder="اسم العميل"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30"
                                        :class="selectedRecordId ?
                                            'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' :
                                            ''">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]"
                                        :class="selectedRecordId ? 'text-emerald-500' : 'text-gray-400'">person</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-2 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
                            x-data="recordPhonePicker(@js($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values()), @js(array_values(config('countries', []))))"
                            x-effect="loadInitial({ id: editPassengerData.driver_id, name: editPassengerData.driver_name, phone: editPassengerData.driver_phone })">
                            <div class="relative">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم السائق
                                    <span class="text-error">*</span></label>
                                <input type="hidden" name="driver_id" x-model="selectedRecordId">
                                <input type="hidden" name="driver_phone" :value="fullPhoneNumber">
                                <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:ring-boxdark focus-within:ring-2 focus-within:ring-primary/40"
                                    :class="selectedRecordId ?
                                        'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' :
                                        ''"
                                    style="direction: ltr;">
                                    <div class="relative h-full" @click.away="openCountryDropdown = false">
                                        <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                            class="flex gap-2 items-center px-3 h-12 bg-gray-50 rounded-l-xl border-r border-gray-200 transition-colors dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                            <template x-if="selectedCountry?.svg">
                                                <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden"
                                                    x-html="selectedCountry.svg"></div>
                                            </template>
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300"
                                                x-text="selectedCountry?.dial_code"></span>
                                        </button>
                                        <div x-show="openCountryDropdown" x-cloak x-transition
                                            class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark"><input
                                                    type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl"></div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                <template x-for="country in filteredCountries" :key="country.code">
                                                    <button type="button"
                                                        @click="selectedCountry = country; openCountryDropdown = false; searchRecord()"
                                                        class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                        <div class="w-5 h-auto rounded-[2px] overflow-hidden"
                                                            x-html="country.svg"></div>
                                                        <span
                                                            class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200"
                                                            x-text="country.name"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="tel" x-model="localPhoneNumber" @input="searchRecord"
                                        @focus="showDropdown = true" @click.away="showDropdown = false"
                                        placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                        :class="selectedRecordId ? 'font-bold text-emerald-600 dark:text-emerald-400' : ''">
                                    <button type="button" x-show="selectedRecordId" @click="resetSelection"
                                        class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error"><span
                                            class="material-symbols-outlined text-[16px]">close</span></button>
                                </div>
                                <div x-show="showDropdown && localPhoneNumber.length > 0 && !selectedRecordId" x-transition
                                    x-cloak
                                    class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                    <template x-for="record in filteredRecords" :key="record.id">
                                        <button type="button" @click="selectRecord(record)"
                                            class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <div class="flex flex-col gap-0.5"><span
                                                    class="text-sm font-bold text-on-surface dark:text-white"
                                                    x-text="record.name"></span><span
                                                    class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                    x-text="record.phone"></span></div>
                                            <span
                                                class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اسم السائق
                                    <span class="text-error">*</span></label>
                                <div class="relative">
                                    <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedRecordId !== ''" :required="selectedRecordId === ''"
                                        placeholder="اسم السائق"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30"
                                        :class="selectedRecordId ?
                                            'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' :
                                            ''">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]"
                                        :class="selectedRecordId ? 'text-emerald-500' : 'text-gray-400'">local_taxi</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عدد الركاب
                                    <span class="text-error">*</span></label>
                                <input type="number" name="count" min="1" x-model="editPassengerData.count"
                                    required
                                    class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">إجمالي العمولة
                                    <span class="text-error">*</span></label>
                                <input type="number" name="total_commission" min="0" step="0.01"
                                    x-model="editPassengerData.total_commission" required
                                    class="px-4 w-full h-12 font-black text-amber-600 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">ملاحظات</label>
                            <textarea name="note" rows="2" x-model="editPassengerData.note"
                                class="px-4 py-3 w-full rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40"></textarea>
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit"
                            class="flex gap-2 justify-center items-center w-full h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover active:scale-95">
                            <span class="material-symbols-outlined">update</span> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================== Status Modal (Bypass 404) ====================== --}}
        <div x-show="showStatusModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-md bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto text-center">
                <div
                    class="flex justify-between items-center pb-4 mb-6 text-right border-b border-gray-50 dark:border-boxdark-2">
                    <h3 class="text-xl font-black text-on-surface dark:text-white">تعيين حالة الراكب</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="statusPassengerData.url" method="POST" class="space-y-6 text-right">
                    @csrf
                    @method('PUT')

                    {{-- Hidden Fields Required By Controller to avoid 404 and Validation Errors --}}
                    <input type="hidden" name="date" :value="statusPassengerData.date">
                    <input type="hidden" name="passenger_number" :value="statusPassengerData.passenger_number">
                    <input type="hidden" name="location" :value="statusPassengerData.location">
                    <input type="hidden" name="count" :value="statusPassengerData.count">
                    <input type="hidden" name="total_commission" :value="statusPassengerData.total_commission">
                    <input type="hidden" name="customer_phone" :value="statusPassengerData.customer_phone">
                    <input type="hidden" name="customer_name" :value="statusPassengerData.customer_name">
                    <input type="hidden" name="driver_phone" :value="statusPassengerData.driver_phone">
                    <input type="hidden" name="driver_name" :value="statusPassengerData.driver_name">
                    <input type="hidden" name="note" :value="statusPassengerData.note">

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">تحديث الحالة
                            إلى:</label>
                        <select name="status" x-model="statusPassengerData.status" required
                            class="px-4 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            <option value="pending">قيد الانتظار</option>
                            <option value="completed">مكتمل</option>
                            <option value="cancel">ملغي</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-2 w-full">
                        <button type="button" @click="closeModals()"
                            class="flex-1 h-12 text-sm font-bold text-gray-600 rounded-xl transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-boxdark active:scale-95">إلغاء</button>
                        <button type="submit"
                            class="flex-1 h-12 text-sm font-bold text-white bg-emerald-500 rounded-xl shadow-lg transition-all hover:bg-emerald-600 shadow-emerald-500/30 active:scale-95">حفظ
                            الحالة</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ====================== Delete Modal ====================== --}}
        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-md bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-8 text-center pointer-events-auto">
                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-error rounded-[1.5rem] shadow-sm">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>
                <h3 class="mb-3 text-2xl font-black text-on-surface dark:text-white">تأكيد الحذف</h3>
                <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                    هل أنت متأكد من حذف الراكب رقم:<br>
                    <span class="text-base font-bold text-on-surface dark:text-white"
                        x-text="deletePassengerData.passenger_number"></span>؟<br>
                    <span class="inline-block mt-2 text-error/80">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>
                <form :action="deletePassengerData.url" method="POST" class="flex gap-3 w-full">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="closeModals()"
                        class="flex-1 h-12 text-sm font-bold text-gray-600 rounded-xl transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-boxdark active:scale-95">تراجع</button>
                    <button type="submit"
                        class="flex-1 h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-error hover:bg-error/90 shadow-error/30 active:scale-95">نعم،
                        احذف</button>
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
                showStatusModal: false,

                searchQuery: '',
                statusFilter: '',
                visibleCount: {{ $passengers->count() }},

                editPassengerData: {
                    id: '',
                    date: '',
                    passenger_number: '',
                    status: 'pending',
                    location: '',
                    count: 1,
                    total_commission: 0,
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
                statusPassengerData: {
                    id: '',
                    status: 'pending',
                    url: '',
                    date: '',
                    passenger_number: '',
                    location: '',
                    count: 1,
                    total_commission: 0,
                    customer_phone: '',
                    customer_name: '',
                    driver_phone: '',
                    driver_name: '',
                    note: ''
                },

                init() {
                    this.updateVisibility();
                },

                openCreateModal() {
                    this.showCreateModal = true;
                },

                openEditModal(passenger) {
                    this.editPassengerData = {
                        ...passenger,
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

                openStatusModal(passenger) {
                    // نمرر كل البيانات كمدخلات مخفية حتى ينجح الـ Validation في الكنترولر بدون 404
                    this.statusPassengerData = {
                        ...passenger,
                        url: '{{ route('passengers.index') }}/' + passenger.id
                    };
                    this.showStatusModal = true;
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showDeleteModal = false;
                    this.showStatusModal = false;
                },

                showRow(passengerNumber, location, driverName, customerName, statusKey) {
                    const query = this.searchQuery.toLowerCase().trim();
                    const cleanQuery = query.replace(/^(\+967|967|00967|0)/, '');

                    const check = (str) => {
                        if (!str) return false;
                        const cleanStr = String(str).toLowerCase();
                        return cleanStr.includes(query) || (cleanQuery !== '' && cleanStr.includes(cleanQuery));
                    };

                    const statusMap = {
                        'pending': 'قيد الانتظار',
                        'completed': 'مكتمل',
                        'cancel': 'ملغي'
                    };
                    const arabicStatus = statusMap[statusKey] || statusKey;

                    const matchesSearch = !query || check(passengerNumber) || check(location) || check(driverName) || check(
                        customerName) || check(arabicStatus);
                    const matchesStatus = !this.statusFilter || String(statusKey || '') === this.statusFilter;

                    return matchesSearch && matchesStatus;
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll(
                            '.passenger-row:not([style*="display: none"])').length;
                    });
                }
            }
        }

        function passengerPhonePicker(countriesList) {
            return {
                countries: countriesList || [],
                localPhoneNumber: '',
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                initializedPhone: null,
                init() {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0] || null;
                },
                get filteredCountries() {
                    const query = this.searchCountryQuery.toLowerCase().trim();
                    if (!query) return this.countries;
                    return this.countries.filter(country => String(country.name || '').toLowerCase().includes(query) ||
                        String(country.code || '').toLowerCase().includes(query) || String(country.dial_code || '')
                        .toLowerCase().includes(query));
                },
                get fullPhoneNumber() {
                    const dial = String(this.selectedCountry?.dial_code || '').replace('+', '');
                    const local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '').replace(/^0+/, '');
                    if (!local) return '';
                    return dial + local;
                },
                loadInitial(phone) {
                    if (!phone || this.initializedPhone === phone) return;
                    this.initializedPhone = phone;
                    const clean = String(phone).replace(/[^\d]/g, '');
                    const sortedCountries = [...this.countries].sort((a, b) => String(b.dial_code || '').length - String(a
                        .dial_code || '').length);
                    const country = sortedCountries.find(c => {
                        const dial = String(c.dial_code || '').replace('+', '');
                        return dial && clean.startsWith(dial);
                    });
                    this.selectedCountry = country || this.countries.find(c => c.code === 'YE') || this.countries[0] ||
                    null;
                    const dial = String(this.selectedCountry?.dial_code || '').replace('+', '');
                    this.localPhoneNumber = clean.startsWith(dial) ? clean.substring(dial.length) : clean;
                }
            }
        }

      function recordPhonePicker(recordsList, countriesList) {
            return {
                records: recordsList || [], countries: countriesList || [], localPhoneNumber: '', selectedCountry: null, openCountryDropdown: false, searchCountryQuery: '',
                showDropdown: false, selectedRecordId: '', nameInput: '', initializedRecordKey: '',
                
                init() { this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0] || null; },
                
                get filteredCountries() {
                    const query = this.searchCountryQuery.toLowerCase().trim();
                    if (!query) return this.countries;
                    return this.countries.filter(country => String(country.name || '').toLowerCase().includes(query) || String(country.code || '').toLowerCase().includes(query) || String(country.dial_code || '').toLowerCase().includes(query));
                },
                
                get fullPhoneNumber() {
                    const dial = String(this.selectedCountry?.dial_code || '').replace('+', '');
                    const local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '').replace(/^0+/, '');
                    if (!local) return ''; return dial + local;
                },
                
                get filteredRecords() {
                    const phone = this.fullPhoneNumber; const local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '');
                    if (!local) return [];
                    return this.records.filter(record => {
                        const recordPhone = String(record.phone || '').replace(/[^\d]/g, '');
                        return recordPhone.includes(local) || recordPhone.includes(phone);
                    });
                },
                
                searchRecord() {
                    const exact = this.records.find(record => String(record.phone || '').replace(/[^\d]/g, '') === String(this.fullPhoneNumber || '').replace(/[^\d]/g, ''));
                    if (exact) { 
                        this.selectRecord(exact); 
                    } else { 
                        this.selectedRecordId = ''; 
                        // 🌟 التعديل هنا: قمنا بإزالة تفريغ الاسم (this.nameInput = '') حتى لا يمسح ما كتبته! 🌟
                        this.showDropdown = true; 
                    }
                },
                
                selectRecord(record) {
                    this.selectedRecordId = record.id || ''; 
                    this.nameInput = record.name || '';
                    this.applyPhone(record.phone || this.fullPhoneNumber);
                    this.showDropdown = false;
                },
                
                resetSelection() { 
                    this.selectedRecordId = ''; 
                    this.nameInput = ''; 
                },
                
                applyPhone(phone) {
                    const clean = String(phone || '').replace(/[^\d]/g, '');
                    const sortedCountries = [...this.countries].sort((a, b) => String(b.dial_code || '').length - String(a.dial_code || '').length);
                    const country = sortedCountries.find(c => {
                        const dial = String(c.dial_code || '').replace('+', ''); return dial && clean.startsWith(dial);
                    });
                    this.selectedCountry = country || this.countries.find(c => c.code === 'YE') || this.countries[0] || null;
                    const dial = String(this.selectedCountry?.dial_code || '').replace('+', '');
                    this.localPhoneNumber = clean.startsWith(dial) ? clean.substring(dial.length) : clean;
                },
                
                loadInitial(record) {
                    if (!record || (!record.id && !record.phone && !record.name)) return;
                    const key = JSON.stringify(record);
                    if (this.initializedRecordKey === key) return;
                    this.initializedRecordKey = key;
                    this.selectedRecordId = record.id || ''; this.nameInput = record.name || '';
                    if (record.phone) { this.applyPhone(record.phone); }
                }
            }
        }
    </script>
@endsection
