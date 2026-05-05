@extends('layouts.app')

@section('title', 'إدارة السائقين')
@section('content')

    <div x-data="driversRegistry()"
        class="pb-24 space-y-6 min-h-screen font-body lg:pb-12"
        dir="rtl">

        {{-- ================= Header Section ================= --}}
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex gap-4 justify-between items-start">
                <div class="text-right">
                    <h1 class="text-2xl font-black md:text-3xl text-on-surface dark:text-white">
                        السائقين
                    </h1>

                    <p class="mt-1 text-sm font-bold text-gray-500 dark:text-bodydark">
                        إجمالي {{ $drivers->total() }} سائق مسجل
                    </p>
                </div>

                <button type="button"
                    @click="showCreateModal = true"
                    class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">
                        person_add
                    </span>
                    <span>إضافة سائق جديد</span>
                </button>
            </div>
        </div>

        {{-- ================= Search & Data Section ================= --}}
        <div class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search Bar --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="flex flex-col gap-4 justify-between items-stretch md:flex-row md:items-center">

                    {{-- Search Input --}}
                    <div class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-[420px] dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">
                        <input type="text"
                            x-model="searchQuery"
                            @input.debounce.300ms="updateVisibility()"
                            placeholder="ابحث باسم السائق أو رقم الهاتف..."
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

                    {{-- Results Counter --}}
                    <div class="flex gap-2 items-center text-xs font-black text-gray-500 dark:text-bodydark">
                        <span class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        </span>

                        <span>
                            النتائج المعروضة:
                            <span class="text-primary" x-text="visibleCount"></span>
                            من
                            <span>{{ $drivers->count() }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- ================= Mobile View ================= --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse ($drivers as $driver)
                    <div x-show="showRow(@js($driver->name), @js($driver->phone))"
                        x-transition
                        class="overflow-hidden relative p-5 rounded-2xl border border-gray-100 shadow-sm transition-all driver-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center min-w-0">
                                <div class="flex justify-center items-center w-12 h-12 text-lg font-black rounded-xl bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                                    @php
                                        $words = explode(' ', $driver->name);
                                        echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') .
                                            (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                                    @endphp
                                </div>

                                <div class="flex flex-col gap-1 min-w-0">
                                    <h3 class="text-sm font-black truncate text-on-surface dark:text-white font-headline">
                                        {{ $driver->name }}
                                    </h3>

                                    <x-phone-number :value="$driver->phone"
                                        class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                </div>
                            </div>

                            {{-- Mobile Actions --}}
                            <div x-data="{ menuOpen: false }" class="relative shrink-0">
                                <button type="button"
                                    @click="menuOpen = !menuOpen"
                                    @click.away="menuOpen = false"
                                    class="p-2 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="menuOpen"
                                    x-transition
                                    x-cloak
                                    class="absolute left-0 top-full z-[999] py-1.5 mt-2 w-52 rounded-2xl border border-gray-100 shadow-lg backdrop-blur-md bg-white/95 dark:bg-boxdark-2/95 dark:border-boxdark overflow-hidden">

                                    <a href="tel:{{ $driver->phone }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark dark:hover:text-blue-400">
                                        <span class="material-symbols-outlined text-[18px]">call</span>
                                        اتصال
                                    </a>

                                    <a href="https://wa.me/{{ ltrim($driver->phone, '+') }}"
                                        target="_blank"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                        <span class="material-symbols-outlined text-[18px]">chat_bubble</span>
                                        واتساب
                                    </a>

                                    <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                    <button type="button"
                                        @click="menuOpen = false; openEditModal({{ $driver->id }}, {{ json_encode($driver->name) }}, {{ json_encode($driver->phone) }})"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                        تعديل البيانات
                                    </button>

                                    <button type="button"
                                        @click="menuOpen = false; openDeleteModal({{ $driver->id }}, {{ json_encode($driver->name) }})"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                        حذف السائق
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4 mt-4 border-t border-gray-100 dark:border-boxdark">
                            <a href="tel:{{ $driver->phone }}"
                                class="flex gap-2 justify-center items-center px-3 py-2 text-xs font-bold rounded-xl transition-all bg-primary-container dark:bg-primary/10 text-primary hover:bg-primary/20 active:scale-95">
                                <span class="text-[16px] material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">call</span>
                                اتصال
                            </a>

                            <a href="https://wa.me/{{ ltrim($driver->phone, '+') }}"
                                target="_blank"
                                class="flex gap-2 justify-center items-center px-3 py-2 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl transition-all dark:bg-emerald-500/10 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 active:scale-95">
                                <span class="text-[16px] material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">chat_bubble</span>
                                واتساب
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col gap-4 justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                            <span class="material-symbols-outlined text-[28px] text-gray-400">no_accounts</span>
                        </div>

                        <div>
                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                لا توجد بيانات للسائقين
                            </h3>

                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                لم نعثر على أي سائقين مسجلين في النظام حالياً.
                            </p>
                        </div>
                    </div>
                @endforelse

                <div x-show="visibleCount === 0 && {{ $drivers->count() }} > 0"
                    x-cloak
                    class="flex flex-col gap-4 justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                        <span class="material-symbols-outlined text-[28px] text-gray-400">search_off</span>
                    </div>

                    <div>
                        <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                            لا توجد نتائج مطابقة
                        </h3>

                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            لا يوجد نتائج تطابق بحثك.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ================= Desktop View ================= --}}
            <div class="hidden overflow-visible w-full lg:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
                            <th class="px-6 py-4">السائق</th>
                            <th class="px-6 py-4 text-center">رقم الهاتف</th>
                            <th class="px-6 py-4 text-center">تواصل سريع</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse ($drivers as $driver)
                            <tr x-show="showRow(@js($driver->name), @js($driver->phone))"
                                x-transition
                                class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group driver-row">

                                {{-- Driver --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-4 items-center">
                                        <div class="flex justify-center items-center w-11 h-11 text-lg font-black rounded-lg bg-primary-container dark:bg-primary/10 text-primary">
                                            @php
                                                $words = explode(' ', $driver->name);
                                                echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') .
                                                    (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                                            @endphp
                                        </div>

                                        <span class="text-sm font-black text-gray-800 dark:text-white">
                                            {{ $driver->name }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Phone --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex justify-center">
                                        <x-phone-number :value="$driver->phone"
                                            class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                    </div>
                                </td>

                                {{-- Quick Contact --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex gap-3 justify-center items-center">
                                        <a href="tel:{{ $driver->phone }}"
                                            title="اتصال"
                                            class="flex justify-center items-center w-9 h-9 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors dark:bg-boxdark dark:border-boxdark-2 text-primary hover:bg-primary-container dark:hover:bg-primary/10 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">call</span>
                                        </a>

                                        <a href="https://wa.me/{{ ltrim($driver->phone, '+') }}"
                                            target="_blank"
                                            title="واتساب"
                                            class="flex justify-center items-center w-9 h-9 text-emerald-500 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors dark:bg-boxdark dark:border-boxdark-2 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">chat_bubble</span>
                                        </a>
                                    </div>
                                </td>

                                {{-- Actions --}}
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
                                                <button type="button"
                                                    @click="open = false; openEditModal({{ $driver->id }}, {{ json_encode($driver->name) }}, {{ json_encode($driver->phone) }})"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark-2 dark:hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                    تعديل البيانات
                                                </button>

                                                <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                                <button type="button"
                                                    @click="open = false; openDeleteModal({{ $driver->id }}, {{ json_encode($driver->name) }})"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    حذف السائق
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span class="material-symbols-outlined text-[28px] text-gray-400">no_accounts</span>
                                        </div>

                                        <div>
                                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                                لا توجد بيانات للسائقين
                                            </h3>

                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                لم نعثر على أي سائقين مسجلين في النظام حالياً.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $drivers->count() }} > 0"
                            x-cloak>
                            <td colspan="4" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[28px] text-gray-400">search_off</span>
                                    </div>

                                    <div>
                                        <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                            لا توجد نتائج مطابقة
                                        </h3>

                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            لم نعثر على أي سائق يطابق كلمة البحث.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($drivers->hasPages())
                <div class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $drivers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- ======================== Modals ======================== --}}

        {{-- 1. Create Driver Modal --}}
        <div x-show="showCreateModal"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div class="relative w-full max-w-lg bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto flex flex-col max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-8 border-b border-gray-50 dark:border-boxdark-2">
                    <h3 class="flex gap-2 items-center text-xl font-black font-headline text-on-surface dark:text-white">
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[20px]">person_add</span>
                        </div>
                        إضافة سائق جديد
                    </h3>

                    <button type="button"
                        @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('drivers.store') }}"
                    method="POST"
                    class="space-y-6"
                    x-data="{ isSubmitting: false }"
                    @submit="isSubmitting = true">
                    @csrf

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                            الاسم الكامل <span class="text-error">*</span>
                        </label>

                        <div class="relative">
                            <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">
                                person
                            </span>

                            <input type="text"
                                name="name"
                                required
                                placeholder="مثلاً: أحمد السعيدي"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                        </div>
                    </div>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries', []))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;

                            return this.countries.filter(c =>
                                String(c.name || '').toLowerCase().includes(this.search.toLowerCase()) ||
                                String(c.dial_code || '').includes(this.search)
                            );
                        }
                    }">
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                            رقم الهاتف <span class="text-error">*</span>
                        </label>

                        <div class="relative">
                            <input type="hidden"
                                name="phone"
                                :value="(selectedCountry?.dial_code.replace('+', '') || '') + localPhoneNumber">

                            <div class="flex relative items-center rounded-xl ring-1 ring-gray-200 transition-all group bg-surface dark:bg-boxdark-2 focus-within:bg-white dark:focus-within:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40">

                                <button type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="flex gap-2 items-center px-4 h-14 bg-white rounded-r-xl border-l border-gray-200 transition-colors dark:bg-boxdark dark:border-boxdark-2 shrink-0 hover:bg-gray-50 dark:hover:bg-boxdark-2">

                                    <span x-show="selectedCountry?.svg"
                                        x-html="selectedCountry?.svg"
                                        class="inline-flex overflow-hidden justify-center items-center w-6 h-4 rounded-sm shadow-sm shrink-0">
                                    </span>

                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300 dir-ltr"
                                        x-text="selectedCountry?.dial_code"></span>

                                    <span class="material-symbols-outlined text-[18px] text-gray-400">expand_more</span>
                                </button>

                                <input type="tel"
                                    x-model="localPhoneNumber"
                                    placeholder="7XXXXXXXX"
                                    required
                                    inputmode="numeric"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    @input="localPhoneNumber = localPhoneNumber.replace(/[^0-9]/g, '')"
                                    class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 text-on-surface dark:text-white dir-ltr">

                                <div x-show="open"
                                    x-transition
                                    x-cloak
                                    class="absolute top-[calc(100%+8px)] right-0 z-50 w-full bg-white dark:bg-boxdark rounded-xl border border-gray-100 dark:border-boxdark-2 shadow-xl overflow-hidden">

                                    <div class="p-2 border-b border-gray-50 dark:border-boxdark-2">
                                        <input type="text"
                                            x-model="search"
                                            placeholder="ابحث عن الدولة أو الرمز..."
                                            class="px-4 py-2 w-full text-sm rounded-lg transition-colors outline-none bg-surface dark:bg-boxdark-2 dark:text-white focus:bg-white dark:focus:bg-boxdark">
                                    </div>

                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false; search = ''"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary-container dark:hover:bg-boxdark-2">

                                                <span x-html="country.svg"
                                                    class="inline-flex overflow-hidden justify-center items-center w-5 h-4 rounded-sm shadow-sm shrink-0">
                                                </span>

                                                <span class="flex-grow text-sm font-medium text-gray-700 truncate dark:text-gray-200"
                                                    x-text="country.name"></span>

                                                <span class="font-mono text-xs font-bold text-gray-500 shrink-0 dir-ltr"
                                                    x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        :disabled="isSubmitting"
                        :class="isSubmitting ? 'opacity-80 cursor-wait' : 'active:scale-95 hover:shadow-xl'"
                        class="flex gap-2 justify-center items-center mt-8 w-full h-14 font-black text-white rounded-xl shadow-lg transition-all duration-300 bg-primary shadow-primary/30">

                        <span x-show="!isSubmitting" class="transition-transform material-symbols-outlined">save</span>

                        <svg x-cloak
                            x-show="isSubmitting"
                            class="w-6 h-6 text-white animate-spin"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>

                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ السائق'"></span>
                    </button>
                </form>
            </div>
        </div>

        {{-- 2. Edit Driver Modal --}}
        <div x-show="showEditModal"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div class="relative w-full max-w-lg bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto flex flex-col max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-8 border-b border-gray-50 dark:border-boxdark-2">
                    <h3 class="flex gap-2 items-center text-xl font-black font-headline text-on-surface dark:text-white">
                        <div class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl shadow-sm bg-surface dark:bg-boxdark-2 dark:text-bodydark">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </div>
                        تعديل بيانات السائق
                    </h3>

                    <button type="button"
                        @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="editDriverData.url" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                            الاسم الكامل <span class="text-error">*</span>
                        </label>

                        <div class="relative">
                            <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">
                                person
                            </span>

                            <input type="text"
                                name="name"
                                x-model="editDriverData.name"
                                required
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                        </div>
                    </div>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries', []))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];

                            const countryCodes = this.countries
                                .map(c => String(c.dial_code || '').replace('+', ''))
                                .sort((a, b) => b.length - a.length);

                            this.$watch('editDriverData.phone', newValue => {
                                if (!newValue) {
                                    this.localPhoneNumber = '';
                                    return;
                                }

                                const currentConstructed = (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhoneNumber;

                                if (newValue !== currentConstructed) {
                                    let matched = false;

                                    for (let code of countryCodes) {
                                        if (String(newValue).startsWith(code)) {
                                            this.selectedCountry = this.countries.find(c => String(c.dial_code || '').replace('+', '') === code);
                                            this.localPhoneNumber = String(newValue).substring(code.length);
                                            matched = true;
                                            break;
                                        }
                                    }

                                    if (!matched) {
                                        this.localPhoneNumber = newValue;
                                    }
                                }
                            });

                            this.$watch('localPhoneNumber', value => {
                                editDriverData.phone = (this.selectedCountry?.dial_code.replace('+', '') || '') + value;
                            });

                            this.$watch('selectedCountry', value => {
                                editDriverData.phone = (value?.dial_code.replace('+', '') || '') + this.localPhoneNumber;
                            });
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;

                            return this.countries.filter(c =>
                                String(c.name || '').toLowerCase().includes(this.search.toLowerCase()) ||
                                String(c.dial_code || '').includes(this.search)
                            );
                        }
                    }">
                        <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                            رقم الهاتف <span class="text-error">*</span>
                        </label>

                        <div class="relative">
                            <input type="hidden" name="phone" :value="editDriverData.phone">

                            <div class="flex relative items-center rounded-xl ring-1 ring-gray-200 transition-all group bg-surface dark:bg-boxdark-2 focus-within:bg-white dark:focus-within:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40">

                                <button type="button"
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="flex gap-2 items-center px-4 h-14 bg-white rounded-r-xl border-l border-gray-200 transition-colors dark:bg-boxdark dark:border-boxdark-2 shrink-0 hover:bg-gray-50 dark:hover:bg-boxdark-2">

                                    <span x-show="selectedCountry?.svg"
                                        x-html="selectedCountry?.svg"
                                        class="inline-flex overflow-hidden justify-center items-center w-6 h-4 rounded-sm shadow-sm shrink-0">
                                    </span>

                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300 dir-ltr"
                                        x-text="selectedCountry?.dial_code"></span>

                                    <span class="material-symbols-outlined text-[18px] text-gray-400">expand_more</span>
                                </button>

                                <input type="tel"
                                    x-model="localPhoneNumber"
                                    placeholder="7XXXXXXXX"
                                    required
                                    inputmode="numeric"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    @input="localPhoneNumber = localPhoneNumber.replace(/[^0-9]/g, '')"
                                    class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 text-on-surface dark:text-white dir-ltr">

                                <div x-show="open"
                                    x-transition
                                    x-cloak
                                    class="absolute top-[calc(100%+8px)] right-0 z-50 w-full bg-white dark:bg-boxdark rounded-xl border border-gray-100 dark:border-boxdark-2 shadow-xl overflow-hidden">

                                    <div class="p-2 border-b border-gray-50 dark:border-boxdark-2">
                                        <input type="text"
                                            x-model="search"
                                            placeholder="ابحث عن الدولة أو الرمز..."
                                            class="px-4 py-2 w-full text-sm rounded-lg transition-colors outline-none bg-surface dark:bg-boxdark-2 dark:text-white focus:bg-white dark:focus:bg-boxdark">
                                    </div>

                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false; search = ''"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary-container dark:hover:bg-boxdark-2">

                                                <span x-html="country.svg"
                                                    class="inline-flex overflow-hidden justify-center items-center w-5 h-4 rounded-sm shadow-sm shrink-0">
                                                </span>

                                                <span class="flex-grow text-sm font-medium text-gray-700 truncate dark:text-gray-200"
                                                    x-text="country.name"></span>

                                                <span class="font-mono text-xs font-bold text-gray-500 shrink-0 dir-ltr"
                                                    x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="flex gap-2 justify-center items-center mt-8 w-full h-14 font-black text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95">
                        <span class="material-symbols-outlined">update</span>
                        حفظ التعديلات
                    </button>
                </form>
            </div>
        </div>

        {{-- 3. Delete Confirmation Modal --}}
        <div x-show="showDeleteModal"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div class="relative w-full max-w-md bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-8 text-center pointer-events-auto flex flex-col">

                <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-error rounded-[1.5rem] shadow-sm">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">
                    تأكيد الحذف
                </h3>

                <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                    هل أنت متأكد من أنك تريد حذف السائق <br>
                    <span class="text-base font-bold text-on-surface dark:text-white font-headline"
                        x-text="deleteDriverData.name"></span>؟<br>
                    <span class="inline-block mt-2 text-error/80 dark:text-error">
                        لا يمكن التراجع عن هذا الإجراء.
                    </span>
                </p>

                <form :action="deleteDriverData.url" method="POST" class="flex gap-3 w-full">
                    @csrf
                    @method('DELETE')

                    <button type="button"
                        @click="closeModals()"
                        class="flex-1 h-12 text-sm font-bold text-gray-600 rounded-xl transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-boxdark font-headline active:scale-95">
                        تراجع
                    </button>

                    <button type="submit"
                        class="flex-1 h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-error hover:bg-error/90 shadow-error/30 active:scale-95 font-headline">
                        نعم، احذف
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function driversRegistry() {
            return {
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                searchQuery: '',
                visibleCount: {{ $drivers->count() }},

                editDriverData: {
                    id: '',
                    name: '',
                    phone: '',
                    url: ''
                },

                deleteDriverData: {
                    id: '',
                    name: '',
                    url: ''
                },

                init() {
                    this.updateVisibility();
                },

                showRow(name, phone) {
                    const query = this.searchQuery.toLowerCase().trim();

                    if (!query) {
                        return true;
                    }

                    return String(name || '').toLowerCase().includes(query)
                        || String(phone || '').toLowerCase().includes(query);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.driver-row:not([style*="display: none"])').length;
                    });
                },

                openEditModal(id, name, phone) {
                    this.editDriverData = {
                        id: id,
                        name: name,
                        phone: phone,
                        url: '{{ route('drivers.index') }}/' + id
                    };

                    this.showEditModal = true;
                },

                openDeleteModal(id, name) {
                    this.deleteDriverData = {
                        id: id,
                        name: name,
                        url: '{{ route('drivers.index') }}/' + id
                    };

                    this.showDeleteModal = true;
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showDeleteModal = false;
                }
            }
        }
    </script>
@endsection