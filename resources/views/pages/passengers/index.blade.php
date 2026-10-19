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
                    <div class="flex flex-wrap gap-2 items-center mt-1">
                        <p class="text-sm font-bold text-gray-500 dark:text-bodydark">
                            إجمالي {{ $passengers->total() }} راكب مسجل
                        </p>
                    </div>
                </div>

                <div class="flex gap-2 items-center shrink-0">
                    <a :href="'{{ route('receipt.generate', ['type' => 'all_passenger', 'id' => '__ID__']) }}'.replace('__ID__', getPrintUrl())"
                        target="_blank"
                        class="inline-flex gap-2 items-center px-5 h-12 text-sm font-black rounded-2xl border-2 transition-all border-primary text-primary hover:bg-primary hover:text-white active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        <span>طباعة الكشف للمكتب</span>
                    </a>
                    <button type="button" @click="openCreateModal()"
                        class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span>إضافة راكب جديد</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-4 mx-auto max-w-7xl md:grid-cols-4 md:gap-6">
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
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all dark:bg-boxdark border-r-indigo-500 dark:border-r-indigo-500 dark:border-boxdark-2">
                <div
                    class="flex justify-center items-center w-12 h-12 text-indigo-500 bg-indigo-50 rounded-xl dark:bg-indigo-500/10">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-indigo-500 uppercase">إجمالي عمولة المكتب</span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($passengers->getCollection()->sum('office_commission'), 0) }}</h4>
                </div>
            </div>

            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all dark:bg-boxdark border-r-amber-500 dark:border-r-amber-500 dark:border-boxdark-2">
                <div
                    class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-amber-500 uppercase">عمولات المكاتب الأخرى</span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($passengers->getCollection()->sum('other_office_commission'), 0) }}</h4>
                </div>
            </div>
        </div>

        {{-- ====================== Search & Table Section ====================== --}}
        <div
            class="overflow-visible mx-auto my-4 max-w-7xl bg-white rounded-2xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark dark:border-boxdark-2">

            {{-- Search & Filters Toolbar --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <form method="GET" action="{{ route('passengers.index') }}" id="passengerFilterForm"
                    class="flex flex-col gap-4 justify-between items-stretch lg:flex-row lg:items-center">
                    
                    <div class="flex flex-wrap flex-1 gap-3 items-center">
                        {{-- البحث النصي --}}
                        <div
                            class="relative flex flex-row items-center px-3 w-full sm:w-[260px] lg:w-[280px] gap-3 rounded-2xl border border-gray-200 transition-all dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">
                            <input type="text" name="search" value="{{ request('search') }}" x-model="searchQuery"
                                @input.debounce.300ms="updateVisibility()"
                                placeholder="ابحث برقم الراكب، المكان، السائق..."
                                class="flex-1 px-3 w-full h-12 text-sm text-right text-gray-600 bg-transparent border-0 outline-none focus:ring-0 font-body dark:text-gray-300">
                            <div
                                class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                                <span class="material-symbols-outlined text-[22px]">search</span>
                            </div>
                            <button type="button" x-show="searchQuery.length > 0"
                                @click="searchQuery = ''; if('{{ request('search') }}') { $nextTick(() => $el.closest('form').submit()); } else { updateVisibility(); }"
                                x-cloak
                                class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all -translate-y-1/2 dark:bg-boxdark dark:border-boxdark-2 hover:text-error active:scale-95">
                                <span class="text-[18px] material-symbols-outlined">close</span>
                            </button>
                        </div>

                        {{-- قائمة اختيار التاريخ واليوم --}}
                        <div class="relative w-full sm:w-60" x-data="{ openDateMenu: false }" @click.away="openDateMenu = false">
                            {{-- الزر الرئيسي لعرض القائمة --}}
                            <button type="button" @click="openDateMenu = !openDateMenu"
                                class="flex justify-between items-center px-3.5 w-full h-12 text-sm font-bold text-gray-700 rounded-2xl border border-gray-200 transition-all dark:border-boxdark-2 bg-surface dark:bg-boxdark-2 hover:border-primary focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-gray-200">
                                <div class="flex gap-2 items-center min-w-0">
                                    <span class="material-symbols-outlined text-[20px] text-primary shrink-0">calendar_today</span>
                                    <span class="truncate">
                                        @if (request('date') === 'all')
                                            كل الأيام
                                        @elseif (request('date') === now()->format('Y-m-d') || request('date') === 'today')
                                            اليوم ({{ now()->translatedFormat('l') }})
                                        @elseif (request('date') === now()->subDay()->format('Y-m-d') || request('date') === 'yesterday')
                                            أمس ({{ now()->subDay()->translatedFormat('l') }})
                                        @elseif (request('date'))
                                            {{ request('date') }}
                                        @else
                                            اليوم ({{ now()->translatedFormat('l') }})
                                        @endif
                                    </span>
                                </div>
                                <div class="flex gap-1 items-center shrink-0">
                                    @if (request('date') && request('date') !== 'all')
                                        <a href="{{ route('passengers.index', array_merge(request()->except('page'), ['date' => 'all'])) }}"
                                            @click.stop
                                            title="عرض كل الأيام"
                                            class="flex justify-center items-center w-5 h-5 text-gray-400 rounded-full transition-colors hover:text-error hover:bg-gray-100 dark:hover:bg-boxdark-2">
                                            <span class="material-symbols-outlined text-[15px]">close</span>
                                        </a>
                                    @endif
                                    <span class="material-symbols-outlined text-[20px] text-gray-400 transition-transform duration-200"
                                        :class="openDateMenu ? 'rotate-180 text-primary' : ''">expand_more</span>
                                </div>
                            </button>

                            {{-- القائمة المنسدلة --}}
                            <div x-show="openDateMenu" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-boxdark rounded-2xl shadow-xl border border-gray-100 dark:border-boxdark-2 z-[80] p-2 space-y-1">
                                
                                {{-- خيار كل الأيام --}}
                                <button type="button"
                                    @click="dateFilter = 'all'; const f = document.getElementById('filter_date'); if(f) f.value = 'all'; openDateMenu = false; $nextTick(() => document.getElementById('passengerFilterForm').submit())"
                                    class="flex items-center justify-between w-full px-3 py-2.5 text-xs font-bold rounded-xl transition-all {{ request('date') === 'all' ? 'bg-primary/10 text-primary' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-boxdark-2' }}">
                                    <div class="flex gap-2.5 items-center">
                                        <span class="material-symbols-outlined text-[18px]">calendar_view_day</span>
                                        <span>كل الأيام</span>
                                    </div>
                                    @if (request('date') === 'all')
                                        <span class="material-symbols-outlined text-[18px] text-primary">check</span>
                                    @endif
                                </button>

                                {{-- خيار اليوم --}}
                                <button type="button"
                                    @click="dateFilter = '{{ now()->format('Y-m-d') }}'; const f = document.getElementById('filter_date'); if(f) f.value = '{{ now()->format('Y-m-d') }}'; openDateMenu = false; $nextTick(() => document.getElementById('passengerFilterForm').submit())"
                                    class="flex items-center justify-between w-full px-3 py-2.5 text-xs font-bold rounded-xl transition-all {{ request('date') === now()->format('Y-m-d') || request('date') === 'today' || (!request('date') && !request()->has('date')) ? 'bg-primary/10 text-primary' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-boxdark-2' }}">
                                    <div class="flex gap-2.5 items-center">
                                        <span class="material-symbols-outlined text-[18px]">today</span>
                                        <span>اليوم ({{ now()->translatedFormat('l - Y/m/d') }})</span>
                                    </div>
                                    @if (request('date') === now()->format('Y-m-d') || request('date') === 'today' || (!request('date') && !request()->has('date')))
                                        <span class="material-symbols-outlined text-[18px] text-primary">check</span>
                                    @endif
                                </button>

                                {{-- خيار أمس --}}
                                <button type="button"
                                    @click="dateFilter = '{{ now()->subDay()->format('Y-m-d') }}'; const f = document.getElementById('filter_date'); if(f) f.value = '{{ now()->subDay()->format('Y-m-d') }}'; openDateMenu = false; $nextTick(() => document.getElementById('passengerFilterForm').submit())"
                                    class="flex items-center justify-between w-full px-3 py-2.5 text-xs font-bold rounded-xl transition-all {{ request('date') === now()->subDay()->format('Y-m-d') || request('date') === 'yesterday' ? 'bg-primary/10 text-primary' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-boxdark-2' }}">
                                    <div class="flex gap-2.5 items-center">
                                        <span class="material-symbols-outlined text-[18px]">history</span>
                                        <span>أمس ({{ now()->subDay()->translatedFormat('l - Y/m/d') }})</span>
                                    </div>
                                    @if (request('date') === now()->subDay()->format('Y-m-d') || request('date') === 'yesterday')
                                        <span class="material-symbols-outlined text-[18px] text-primary">check</span>
                                    @endif
                                </button>

                                <div class="my-1.5 border-t border-gray-100 dark:border-boxdark-2"></div>

                                {{-- تحديد يوم مخصص بالتقويم --}}
                                <div class="p-2.5 rounded-xl border border-gray-100 bg-gray-50/80 dark:bg-boxdark-2/70 dark:border-boxdark-2">
                                    <label class="block text-[11px] font-black text-gray-600 dark:text-gray-300 mb-1.5">
                                        تحديد يوم مخصص بالتقويم:
                                    </label>
                                    <div class="relative">
                                        <input type="hidden" name="date" id="filter_date" value="{{ request('date', now()->format('Y-m-d')) }}">
                                        <input type="date" id="filter_date_picker"
                                            value="{{ in_array(request('date'), ['all', 'today', 'yesterday', '']) ? (request('date') === 'today' ? now()->format('Y-m-d') : (request('date') === 'yesterday' ? now()->subDay()->format('Y-m-d') : '')) : request('date', now()->format('Y-m-d')) }}"
                                            @change="const f = document.getElementById('filter_date'); if(f) f.value = $el.value; dateFilter = $el.value; openDateMenu = false; $nextTick(() => document.getElementById('passengerFilterForm').submit())"
                                            onclick="try { this.showPicker(); } catch(e) {}"
                                            class="px-3 w-full h-10 text-xs font-bold text-gray-700 bg-white rounded-xl border border-gray-200 cursor-pointer outline-none dark:bg-boxdark dark:border-boxdark focus:ring-2 focus:ring-primary/30 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- فلتر الحالة --}}
                        <div
                            class="relative w-full rounded-2xl border border-gray-200 transition-all sm:w-44 dark:border-boxdark-2 bg-surface dark:bg-boxdark-2 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                            <select name="status" x-model="statusFilter" @change="updateVisibility(); $nextTick(() => document.getElementById('passengerFilterForm').submit())"
                                class="pr-10 pl-9 w-full h-12 text-sm font-bold text-gray-600 bg-transparent rounded-2xl border-none appearance-none cursor-pointer outline-none focus:ring-0 dark:text-gray-300 font-body">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>كل الحالات</option>
                                <option value="pending" {{ request('status') === 'pending' || !request()->has('status') ? 'selected' : '' }}>قيد الانتظار</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancel" {{ request('status') === 'cancel' ? 'selected' : '' }}>ملغي</option>
                            </select>
                            <div
                                class="flex absolute inset-y-0 right-0 items-center pr-3.5 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">tune</span>
                            </div>
                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 text-gray-400 pointer-events-none">
                                <span class="material-symbols-outlined text-[20px]">expand_more</span>
                            </div>
                        </div>

                        {{-- زر إعادة التعيين إذا تم تغيير الفلتر عن الوضع الافتراضي --}}
                        @if (request('search') || (request()->has('date') && request('date') !== now()->format('Y-m-d') && request('date') !== 'today') || (request()->has('status') && request('status') !== 'pending'))
                            <a href="{{ route('passengers.index') }}"
                                class="inline-flex gap-1 items-center px-3 h-12 text-xs font-bold text-gray-500 rounded-2xl border border-gray-300 border-dashed transition-all hover:text-error dark:border-gray-700 hover:border-error"
                                title="إعادة تعيين كافة الفلاتر للوضع الافتراضي">
                                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                                <span>إعادة تعيين</span>
                            </a>
                        @endif
                    </div>

                    <div class="flex gap-2 items-center text-xs font-bold text-gray-500 dark:text-bodydark font-body shrink-0">
                        <span class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        </span>
                        <span>النتائج المعروضة: <span class="font-bold text-primary" x-text="visibleCount"></span> من
                            <span>{{ $passengers->count() }}</span></span>
                    </div>
                </form>
            </div>

            {{-- Desktop View Table --}}
            <div class="hidden overflow-visible w-full lg:block">
                <table class="w-full text-right border-collapse table-auto">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/80 dark:bg-boxdark-2 dark:border-boxdark-2">
                            <th class="px-6 py-4 text-sm font-black text-gray-600 font-headline dark:text-gray-300">التاريخ
                                واليوم</th>
                            <th class="px-6 py-4 text-sm font-black text-gray-600 font-headline dark:text-gray-300">رقم الراكب
                            </th>
                            <th
                                class="px-6 py-4 text-sm font-black text-center text-gray-600 font-headline dark:text-gray-300">
                                المكان</th>
                            <th
                                class="px-6 py-4 text-sm font-black text-center text-gray-600 font-headline dark:text-gray-300">
                                العدد والعمولة</th>
                            <th class="px-6 py-4 text-sm font-black text-gray-600 font-headline dark:text-gray-300">الوسيط
                            </th>
                            <th class="px-6 py-4 text-sm font-black text-gray-600 font-headline dark:text-gray-300">السائق
                            </th>
                            <th
                                class="px-6 py-4 text-sm font-black text-center text-gray-600 font-headline dark:text-gray-300">
                                الإجراءات</th>
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

                                // Avatar generator
                                $avatarLetters = mb_substr(
                                    preg_replace('/[^0-9]/', '', $passenger->passenger_number),
                                    0,
                                    2,
                                );
                                if (empty($avatarLetters)) {
                                    $avatarLetters = '00';
                                }
                            @endphp

                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group passenger-row"
                                x-show="showRow(@js($passenger->passenger_number), @js($passenger->pickup_location), @js($passenger->trip?->driver?->name ?? ''), @js($passenger->broker->name ?? ''), @js($statusKey), @js($passenger->date ? date('Y-m-d', strtotime($passenger->date)) : ''))">

                                {{-- التاريخ واليوم --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col gap-1.5">
                                        <span
                                            class="text-sm font-bold text-gray-800 font-body dark:text-white">{{ optional($passenger->date)->format('Y-m-d') }}</span>
                                        <span
                                            class="text-xs text-gray-500 font-body dark:text-gray-400">{{ $dayName }}</span>
                                        <span
                                            class="inline-flex gap-1 items-center justify-center px-2.5 py-1 mt-1 rounded-md text-[10px] font-bold {{ $statusClass }} w-max">
                                            <span class="material-symbols-outlined text-[14px]">{{ $statusIcon }}</span>
                                            {{ $statusLabel }}
                                        </span>
                                        @if ($statusKey == 'cancel' && !empty($passenger->cancel_reason))
                                            <span class="text-[11px] text-rose-500 font-body block mt-0.5 max-w-[150px] truncate" title="سبب الإلغاء: {{ $passenger->cancel_reason }}">
                                                <span class="font-bold">السبب:</span> {{ $passenger->cancel_reason }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- الراكب --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex gap-3 items-center">
                                        {{-- <div
                                            class="flex justify-center items-center w-11 h-11 font-bold rounded-full shadow-sm bg-primary/10 text-primary shrink-0">
                                            {{ $avatarLetters }}
                                        </div> --}}
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-sm font-bold text-right text-gray-800 font-body dark:text-white dir-ltr">
                                                <x-phone-number :value="$passenger->passenger_number" class="font-bold text-primary" />
                                            </span>
                                            @if ($passenger->note)
                                                <span
                                                    class="text-xs text-gray-500 font-body dark:text-gray-400 line-clamp-1"
                                                    title="{{ $passenger->note }}">
                                                    {{ $passenger->note }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- المكان --}}
                                <td class="px-6 py-4 text-center align-top">
                                    <span
                                        class="inline-block px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-50 rounded-lg border border-gray-100 shadow-sm font-body dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                        {{ $passenger->pickup_location ?: 'غير محدد' }}
                                    </span>
                                    @if ($passenger->destination)
                                        <div class="mt-1">
                                            <span
                                                class="inline-flex gap-1 items-center px-2 py-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 rounded-md dark:bg-emerald-500/10 dark:text-emerald-400">
                                                <span class="material-symbols-outlined text-[12px]">flag</span>
                                                {{ $passenger->destination }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                {{-- العدد والعمولة --}}
                                <td class="px-6 py-4 text-center align-top">
                                    <div class="flex flex-col gap-2 items-center">
                                        @if($passenger->vehicle_type)
                                            <span class="inline-flex gap-1 items-center px-2 py-0.5 text-[11px] font-bold rounded-md bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                                <span class="material-symbols-outlined text-[13px]">directions_car</span>
                                                {{ $passenger->vehicle_type }}
                                            </span>
                                        @endif
                                        <span
                                            class="inline-flex gap-1.5 items-center px-3 py-1 text-xs font-bold rounded-lg bg-primary/10 text-primary">
                                            <span class="material-symbols-outlined text-[16px]">group</span>
                                            العدد: {{ $passenger->count ?? 0 }}
                                        </span>
                                        <div class="flex flex-col gap-1 text-[11px] font-body text-center">
                                            <span class="font-bold text-gray-600 dark:text-gray-300">المكتب: <span
                                                    class="text-emerald-600 dark:text-emerald-400">{{ number_format($passenger->office_commission ?? 0, 0) }}</span></span>
                                            <span class="font-bold text-gray-600 dark:text-gray-300">أخرى: <span
                                                    class="text-amber-600 dark:text-amber-400">{{ number_format($passenger->other_office_commission ?? 0, 0) }}</span></span>
                                        </div>
                                    </div>
                                </td>

                                {{-- الوسيط --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col gap-3">
                                        <div class="flex gap-2 items-center">
                                            <span
                                                class="flex justify-center items-center w-6 h-6 rounded bg-slate-100 dark:bg-boxdark text-slate-500 dark:text-slate-400">
                                                <span class="material-symbols-outlined text-[14px]">handshake</span>
                                            </span>
                                            <span
                                                class="text-xs font-bold text-gray-700 font-body dark:text-gray-300">{{ $passenger->broker->name ?? 'بدون وسيط' }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- السائق --}}
                                <td class="px-6 py-4 align-top">
                                    @if ($passenger->trip && $passenger->trip->driver)
                                        <div class="flex gap-2 items-center">
                                            <span
                                                class="flex justify-center items-center w-6 h-6 rounded bg-slate-100 dark:bg-boxdark text-slate-500 dark:text-slate-400">
                                                <span class="material-symbols-outlined text-[14px]">local_taxi</span>
                                            </span>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-gray-700 font-body dark:text-gray-300">
                                                    {{ $passenger->trip->driver->name ?? '' }}
                                                </span>
                                                @if (!empty($passenger->trip->driver->phone))
                                                    <span class="font-body text-[10px] text-gray-500 dark:text-gray-400 dir-ltr text-right">
                                                        <x-phone-number :value="$passenger->trip->driver->phone" />
                                                    </span>
                                                    
                                                @endif
                                            </div>
                                        </div>
                                       
                                       
                                    @endif
                                </td>

                                {{-- الإجراءات --}}
                                <td class="px-6 py-4 text-center align-middle" x-data="{ openOptions: false }">
                                    <div class="flex relative justify-center items-center">
                                        <button @click="openOptions = !openOptions" @click.away="openOptions = false"
                                            class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-boxdark-2 dark:hover:text-gray-300">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        <div x-show="openOptions" x-transition.opacity.duration.200ms x-cloak
                                            class="absolute left-1/2 top-full z-[60] py-2 mt-1 w-48 bg-white rounded-xl shadow-xl border border-gray-100 transform -translate-x-1/2 dark:bg-boxdark dark:border-boxdark-2">

                                            <a href="{{ route('passengers.show', $passenger->id) }}"
                                                class="flex gap-3 items-center px-4 py-2.5 text-sm font-bold text-gray-600 transition-colors hover:bg-gray-50 hover:text-primary dark:text-gray-300 dark:hover:bg-boxdark-2">
                                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                عرض التفاصيل
                                            </a>

                                           
                                                <button type="button"
                                                    @click="openEditModal({
                                                    id: {{ $passenger->id }},
                                                    date: @js($passenger->date ? date('Y-m-d', strtotime($passenger->date)) : ''),
                                                    passenger_number: @js($passenger->passenger_number),
                                                    status: @js($statusKey),
                                                    pickup_location: @js($passenger->pickup_location),
                                                    destination: @js($passenger->destination),
                                                    vehicle_type: @js($passenger->vehicle_type),
                                                    count: @js($passenger->count),
                                                    office_commission: @js($passenger->office_commission),
                                                    other_office_commission: @js($passenger->other_office_commission),
                                                    broker_id: @js($passenger->broker_id),
                                                    broker_name: @js($passenger->broker->name ?? ''),
                                                    broker_phone: @js($passenger->broker->phone ?? ''),
                                                    driver_id: @js($passenger->driver_id),
                                                    driver_name: @js($passenger->driver->name ?? ''),
                                                    driver_phone: @js($passenger->driver->phone ?? ''),
                                                    note: @js($passenger->note)
                                                }); openOptions = false"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-sm font-bold text-right text-gray-600 transition-colors hover:bg-amber-50 hover:text-amber-500 dark:text-gray-300 dark:hover:bg-amber-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                    تعديل البيانات
                                                </button>

                                                @if ($statusKey == 'pending')
                                                    @php
                                                        $whatsappUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerBooking');
                                                    @endphp
                                                    @if ($whatsappUrl)
                                                        <button type="button"
                                                            onclick="sendPassengerBookingDirect({{ $passenger->id }}, this, '{{ $whatsappUrl }}')"
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-sm font-bold text-right text-emerald-600 transition-colors hover:bg-emerald-50 hover:text-emerald-700 dark:text-emerald-400 dark:hover:bg-emerald-500/10">
                                                            <span class="material-symbols-outlined text-[18px]">send</span>
                                                            <span>إرسال الحجز (واتساب)</span>
                                                        </button>
                                                    @endif
                                                @endif

                                                @if ($statusKey == 'cancel')
                                                    @php
                                                        $whatsappCancelUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate($passenger, 'passengerCancel');
                                                    @endphp
                                                    @if ($whatsappCancelUrl)
                                                        <button type="button"
                                                            onclick="sendPassengerCancelDirect({{ $passenger->id }}, this, '{{ $whatsappCancelUrl }}')"
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-sm font-bold text-right text-rose-600 transition-colors hover:bg-rose-50 hover:text-rose-700 dark:text-rose-400 dark:hover:bg-rose-500/10">
                                                            <span class="material-symbols-outlined text-[18px]">send</span>
                                                            <span>إرسال الإلغاء للوسيط (واتساب)</span>
                                                        </button>
                                                    @endif
                                                @endif

                                                @if ($statusKey != 'cancel' && $statusKey != 'completed')
                                                <button type="button"
                                                    @click="openStatusModal({
                                                    id: {{ $passenger->id }},
                                                    date: @js($passenger->date ? date('Y-m-d', strtotime($passenger->date)) : ''),
                                                    status: 'cancel',
                                                    passenger_number: @js($passenger->passenger_number),
                                                    pickup_location: @js($passenger->pickup_location),
                                                    destination: @js($passenger->destination),
                                                    count: @js($passenger->count),
                                                    office_commission: @js($passenger->office_commission),
                                                    other_office_commission: @js($passenger->other_office_commission),
                                                    broker_name: @js($passenger->broker->name ?? ''),
                                                    broker_phone: @js($passenger->broker->phone ?? ''),
                                                    driver_phone: @js($passenger->driver->phone ?? ''),
                                                    driver_name: @js($passenger->driver->name ?? ''),
                                                    cancel_reason: @js($passenger->cancel_reason ?? ''),
                                                    note: @js($passenger->note)
                                                }); openOptions = false"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-sm font-bold text-right text-gray-600 transition-colors hover:bg-rose-50 hover:text-rose-500 dark:text-gray-300 dark:hover:bg-rose-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">block</span>
                                                    إلغاء الراكب
                                                </button>
                                                @endif
                                          


                                            <div class="my-1 border-t border-gray-50 dark:border-boxdark-2"></div>
                                            @if ($statusKey != 'completed')
                                                <button type="button"
                                                    @click="openDeleteModal({{ $passenger->id }}, @js($passenger->passenger_number)); openOptions = false"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-sm font-bold text-right text-gray-600 transition-colors hover:bg-error/10 hover:text-error dark:text-gray-300 dark:hover:bg-error/20">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    حذف الراكب
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div
                                            class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span
                                                class="material-symbols-outlined text-[28px] text-gray-400">group_off</span>
                                        </div>
                                        <div>
                                            <h3
                                                class="mb-1 text-base font-bold text-gray-800 font-headline dark:text-white">
                                                لا توجد بيانات للركاب</h3>
                                            <p class="text-sm font-medium text-gray-500 font-body dark:text-gray-400">لم
                                                نعثر على أي ركاب مسجلين في النظام حالياً.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $passengers->count() }} > 0" x-cloak>
                            <td colspan="7" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div
                                        class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[28px] text-gray-400">search_off</span>
                                    </div>
                                    <div>
                                        <h3 class="mb-1 text-base font-bold text-gray-800 font-headline dark:text-white">لا
                                            توجد نتائج مطابقة</h3>
                                        <p class="text-sm font-medium text-gray-500 font-body dark:text-gray-400">لم نعثر
                                            على ركاب يطابقون بحثك أو الحالة المحددة.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($passengers->hasPages())
                {{ $passengers->links('vendor.pagination.custom-minimal') }}
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

                <form action="{{ route('passengers.store') }}" method="POST" @submit="isSubmitting = true" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-5">
                        <div
                            class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-3 bg-gray-50/50 dark:bg-boxdark-2/50 dark:border-boxdark-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">التاريخ <span
                                        class="text-error">*</span></label>
                                <input type="date" name="date" required value="{{ now()->format('Y-m-d') }}"
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">مكان الراكب
                                    <span class="text-error">*</span></label>
                                <input type="text" name="pickup_location" required placeholder="من أين سيركب؟ "
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">وجهة
                                    الراكب</label>
                                <input type="text" name="destination" placeholder="إلى أين يسافر؟ "
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
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                <input type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl">
                                            </div>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
                            x-data="{
                                searchQuery: '',
                                selectedId: '',
                                brokerPhone: '',
                                showDropdown: false,
                                brokers: @js($brokers->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'phone' => $b->phone])->values()),
                                get filteredBrokers() {
                                    if (!this.searchQuery) return [];
                                    return this.brokers.filter(b => b.name.includes(this.searchQuery));
                                },
                                selectBroker(broker) {
                                    this.selectedId = broker.id;
                                    this.searchQuery = broker.name;
                                    this.brokerPhone = broker.phone || '';
                                    this.showDropdown = false;
                                },
                                resetSelection() {
                                    this.selectedId = '';
                                    this.searchQuery = '';
                                    this.brokerPhone = '';
                                }
                            }">

                            <div class="relative text-right">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                    اسم الوسيط أو المكتب <span class="text-error">*</span>
                                </label>

                                <input type="hidden" name="broker_id" :value="selectedId">

                                <div class="flex relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark focus-within:ring-2 focus-within:ring-primary/40"
                                    :class="selectedId ?
                                        'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' :
                                        ''">

                                    <span class="absolute right-3 material-symbols-outlined text-[20px]"
                                        :class="selectedId ? 'text-emerald-500' : 'text-gray-400'">handshake</span>

                                    <input type="text" name="broker_name" x-model="searchQuery"
                                        @input="selectedId = ''; showDropdown = true" @focus="showDropdown = true"
                                        @click.away="showDropdown = false"
                                        placeholder="اكتب اسم الوسيط للبحث أو للإضافة الجديدة..." required
                                        autocomplete="off"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-transparent border-none outline-none focus:ring-0 text-on-surface dark:text-white"
                                        :class="selectedId ? 'text-emerald-600 dark:text-emerald-400 font-black' : ''">

                                    <button type="button" x-show="searchQuery.length > 0" @click="resetSelection()"
                                        class="absolute left-2 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>

                                <div x-show="showDropdown && filteredBrokers.length > 0" x-transition x-cloak
                                    class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                    <template x-for="broker in filteredBrokers" :key="broker.id">
                                        <button type="button" @click="selectBroker(broker)"
                                            class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <span class="text-sm font-bold text-on-surface dark:text-white"
                                                x-text="broker.name"></span>
                                            <span class="flex gap-1 items-center text-xs font-medium text-emerald-500">
                                                اختيار <span class="material-symbols-outlined text-[14px]">done</span>
                                            </span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div class="relative text-right">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                    رقم هاتف الوسيط (واتساب) <span class="text-xs text-gray-400 font-normal">(اختياري)</span>
                                </label>

                                <div class="flex relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark focus-within:ring-2 focus-within:ring-primary/40">
                                    <span class="absolute right-3 material-symbols-outlined text-[20px] text-gray-400">call</span>
                                    <input type="text" name="broker_phone" x-model="brokerPhone"
                                        placeholder="77xxxxxxxx"
                                        dir="ltr"
                                        autocomplete="off"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-transparent border-none outline-none focus:ring-0 text-on-surface dark:text-white text-right">
                                </div>
                            </div>
                        </div>

                        {{-- <div class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-2 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
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
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                <input type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl">
                                            </div>
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="lzr">
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
                                            <div class="flex flex-col gap-0.5">
                                                <span class="text-sm font-bold text-on-surface dark:text-white"
                                                    x-text="record.name"></span>
                                                <span
                                                    class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                    x-text="record.phone"></span>
                                            </div>
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
                                    <input type="text" name="driver_name" x-model="nameInput"
                                        :readonly="selectedRecordId !== ''"
                                        :required="selectedRecordId === ''" placeholder="اسم السائق"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30"
                                        :class="selectedRecordId ?
                                            'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' :
                                            ''">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]"
                                        :class="selectedRecordId ? 'text-emerald-500' : 'text-gray-400'">local_taxi</span>
                                </div>
                            </div>
                        </div> --}}

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">نوع المركبة</label>
                                <input type="text" name="vehicle_type" list="vehicle_types_list" placeholder="مثلاً: صالون، باص..."
                                    class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عدد الركاب
                                    <span class="text-error">*</span></label>
                                <input type="number" name="count" min="1" value="1" required
                                    class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عمولة المكتب
                                    <span class="text-error">*</span></label>
                                <input type="number" name="office_commission"  min="0" required
                                    step="0.01" placeholder="0.00"
                                    class="px-4 w-full h-12 font-black text-emerald-600 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عمولة المكاتب
                                    الأخرى <span class="text-error">*</span></label>
                                <input type="number" name="other_office_commission"  min="0" required
                                    step="0.01" placeholder="0.00"
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
                        <button type="submit" :disabled="isSubmitting"
                            class="flex gap-2 justify-center items-center w-full h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                            <template x-if="!isSubmitting">
                                <span class="flex gap-2 justify-center items-center">
                                    <span class="material-symbols-outlined">save</span> حفظ البيانات
                                </span>
                            </template>
                            <template x-if="isSubmitting">
                                <span class="flex gap-2 justify-center items-center">
                                    <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> جاري الحفظ...
                                </span>
                            </template>
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

                <form :action="editPassengerData.url" method="POST" @submit="isSubmitting = true" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-5">

                        <div
                            class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-4 bg-gray-50/50 dark:bg-boxdark-2/50 dark:border-boxdark-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">التاريخ <span
                                        class="text-error">*</span></label>
                                <input type="date" name="date" x-model="editPassengerData.date" required
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">مكان الراكب
                                    <span class="text-error">*</span></label>
                                <input type="text" name="pickup_location" x-model="editPassengerData.pickup_location"
                                    required
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">وجهة
                                    الراكب</label>
                                <input type="text" name="destination" x-model="editPassengerData.destination"
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            {{-- <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">الحالة <span
                                        class="text-error">*</span></label>
                                <select name="status" x-model="editPassengerData.status" required
                                    class="px-4 w-full h-12 bg-white rounded-xl border-none ring-1 ring-gray-200 dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/40">
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="cancel">ملغي</option>
                                </select>
                            </div> --}}
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-1">
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
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                <input type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl">
                                            </div>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
                            x-data="{
                                searchQuery: '',
                                selectedId: '',
                                showDropdown: false,
                                brokers: @js($brokers->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'phone' => $b->phone])->values()),
                                get filteredBrokers() {
                                    if (!this.searchQuery || this.selectedId) return [];
                                    return this.brokers.filter(b => b.name.includes(this.searchQuery));
                                },
                                selectBroker(broker) {
                                    this.selectedId = broker.id;
                                    this.searchQuery = broker.name;
                                    editPassengerData.broker_id = broker.id;
                                    editPassengerData.broker_name = broker.name;
                                    editPassengerData.broker_phone = broker.phone || '';
                                    this.showDropdown = false;
                                },
                                resetSelection() {
                                    this.selectedId = '';
                                    this.searchQuery = '';
                                    editPassengerData.broker_id = '';
                                    editPassengerData.broker_name = '';
                                    editPassengerData.broker_phone = '';
                                }
                            }"
                            x-effect="
                        if (editPassengerData.broker_id && selectedId !== editPassengerData.broker_id) {
                            selectedId = editPassengerData.broker_id;
                            searchQuery = editPassengerData.broker_name;
                        } else if (!editPassengerData.broker_id && !selectedId) {
                            searchQuery = editPassengerData.broker_name || '';
                        }
                     ">

                            <div class="relative text-right">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اسم الوسيط أو
                                    المكتب <span class="text-error">*</span></label>
                                <input type="hidden" name="broker_id" :value="selectedId">

                                <div class="flex relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark focus-within:ring-2 focus-within:ring-primary/40"
                                    :class="selectedId ?
                                        'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' :
                                        ''">

                                    <span class="absolute right-3 material-symbols-outlined text-[20px]"
                                        :class="selectedId ? 'text-emerald-500' : 'text-gray-400'">handshake</span>

                                    <input type="text" name="broker_name" x-model="searchQuery"
                                        @input="selectedId = ''; editPassengerData.broker_id = ''; editPassengerData.broker_name = searchQuery; showDropdown = true"
                                        @focus="showDropdown = true" @click.away="showDropdown = false"
                                        placeholder="اكتب اسم الوسيط للبحث أو للتعيين الجديد..." required
                                        autocomplete="off"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-transparent border-none outline-none focus:ring-0 text-on-surface dark:text-white"
                                        :class="selectedId ? 'text-emerald-600 dark:text-emerald-400 font-black' : ''">

                                    <button type="button" x-show="searchQuery.length > 0" @click="resetSelection()"
                                        class="absolute left-2 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                    </button>
                                </div>

                                <div x-show="showDropdown && filteredBrokers.length > 0" x-transition x-cloak
                                    class="absolute top-[4.7rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-56 overflow-y-auto custom-scrollbar">
                                    <template x-for="broker in filteredBrokers" :key="broker.id">
                                        <button type="button" @click="selectBroker(broker)"
                                            class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <span class="text-sm font-bold text-on-surface dark:text-white"
                                                x-text="broker.name"></span>
                                            <span
                                                class="flex gap-1 items-center text-xs font-medium text-emerald-500">تعديل
                                                إلى <span class="material-symbols-outlined text-[14px]">done</span></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div class="relative text-right">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">
                                    رقم هاتف الوسيط (واتساب) <span class="text-xs text-gray-400 font-normal">(اختياري)</span>
                                </label>

                                <div class="flex relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all dark:bg-boxdark focus-within:ring-2 focus-within:ring-primary/40">
                                    <span class="absolute right-3 material-symbols-outlined text-[20px] text-gray-400">call</span>
                                    <input type="text" name="broker_phone" x-model="editPassengerData.broker_phone"
                                        placeholder="77xxxxxxxx"
                                        dir="ltr"
                                        autocomplete="off"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-transparent border-none outline-none focus:ring-0 text-on-surface dark:text-white text-right">
                                </div>
                            </div>
                        </div>

                        {{-- <div class="grid grid-cols-1 gap-4 p-4 rounded-2xl border border-gray-100 md:grid-cols-2 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2"
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
                                            <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                <input type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                    class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                    dir="rtl">
                                            </div>
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
                                            <div class="flex flex-col gap-0.5">
                                                <span class="text-sm font-bold text-on-surface dark:text-white"
                                                    x-text="record.name"></span>
                                                <span
                                                    class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right"
                                                    x-text="record.phone"></span>
                                            </div>
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
                                    <input type="text" name="driver_name" x-model="nameInput"
                                        :readonly="selectedRecordId !== ''"
                                        :required="selectedRecordId === ''" placeholder="اسم السائق"
                                        class="px-4 pr-10 w-full h-12 text-sm font-bold bg-white rounded-xl border-none ring-1 ring-gray-200 transition-all dark:bg-boxdark dark:text-white focus:ring-2 focus:ring-primary/30"
                                        :class="selectedRecordId ?
                                            'text-emerald-600 dark:text-emerald-400 bg-emerald-50/40 dark:bg-emerald-500/10 ring-emerald-200 dark:ring-emerald-500/20' :
                                            ''">
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px]"
                                        :class="selectedRecordId ? 'text-emerald-500' : 'text-gray-400'">local_taxi</span>
                                </div>
                            </div>
                        </div> --}}

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">نوع المركبة</label>
                                <input type="text" name="vehicle_type" x-model="editPassengerData.vehicle_type" list="vehicle_types_list" placeholder="مثلاً: صالون، باص..."
                                    class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عدد الركاب
                                    <span class="text-error">*</span></label>
                                <input type="number" name="count" min="1" x-model="editPassengerData.count"
                                    required
                                    class="px-4 w-full h-12 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عمولة المكتب
                                    <span class="text-error">*</span></label>
                                <input type="number" name="office_commission" min="0" step="0.01"
                                    x-model="editPassengerData.office_commission" required placeholder="0.00"
                                    class="px-4 w-full h-12 font-black text-emerald-600 rounded-xl border-none ring-1 ring-gray-200 bg-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/40">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">عمولة المكاتب
                                    الأخرى <span class="text-error">*</span></label>
                                <input type="number" name="other_office_commission" min="0" step="0.01"
                                    x-model="editPassengerData.other_office_commission" required placeholder="0.00"
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
                        <button type="submit" :disabled="isSubmitting"
                            class="flex gap-2 justify-center items-center w-full h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                            <template x-if="!isSubmitting">
                                <span class="flex gap-2 justify-center items-center">
                                    <span class="material-symbols-outlined">update</span> حفظ التعديلات
                                </span>
                            </template>
                            <template x-if="isSubmitting">
                                <span class="flex gap-2 justify-center items-center">
                                    <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span> جاري الحفظ...
                                </span>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- ====================== Status Modal (Cancel Only) ====================== --}}
        <template x-teleport="body">
            <div x-show="showStatusModal" x-cloak
                class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body"
                dir="rtl">

                {{-- الخلفية المظللة --}}
                <div x-show="showStatusModal" x-transition.opacity.duration.300ms
                    class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                    @click="showStatusModal = false"></div>

                {{-- المودال نفسه --}}
                <div x-show="showStatusModal"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-[2rem] shadow-2xl p-8 text-center border border-gray-100 dark:border-boxdark-2">

                    {{-- الأيقونة --}}
                    <div
                        class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-[1.5rem] border border-amber-100 dark:border-amber-500/20">
                        <span class="text-4xl material-symbols-outlined">block</span>
                    </div>

                    <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">
                        تأكيد إلغاء الراكب</h3>

                    <p class="mb-4 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                        هل أنت متأكد من إلغاء الراكب رقم <br>
                        <span class="text-base font-black text-on-surface dark:text-white font-headline"
                            x-text="statusPassengerData.passenger_number"></span>؟<br>
                        <span class="text-amber-500/80">سيتم تغيير حالة الراكب إلى ملغي.</span>
                    </p>

                    {{-- شارة الوسيط --}}
                    <template x-if="statusPassengerData.broker_name">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 mb-4 text-xs font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-full border border-emerald-200 dark:border-emerald-500/20">
                            <span class="material-symbols-outlined text-[16px]">handshake</span>
                            <span>الوسيط:</span>
                            <span x-text="statusPassengerData.broker_name"></span>
                        </div>
                    </template>

                    {{-- فورم الإرسال --}}
                    <form :action="statusPassengerData.url" method="POST" @submit="isSubmitting = true"
                        class="space-y-4">
                        @csrf
                        <input type="hidden" name="status" value="cancel">

                        {{-- حقل رقم هاتف الوسيط (واتساب) --}}
                        <div class="text-right">
                            <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>رقم هاتف الوسيط (واتساب)</span>
                                <span class="text-xs font-normal text-gray-400">(لإرسال إشعار الإلغاء عبر الواتساب)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute right-3.5 top-3.5 material-symbols-outlined text-[18px] text-gray-400">call</span>
                                <input type="text" name="broker_phone" x-model="statusPassengerData.broker_phone"
                                    placeholder="أدخل رقم الوسيط (مثال: 771234567)"
                                    dir="ltr"
                                    class="p-3.5 pr-10 w-full text-sm font-bold text-gray-700 bg-gray-50 rounded-2xl border border-gray-200 transition-all outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-body placeholder:text-gray-400 text-right">
                            </div>
                        </div>

                        {{-- حقل سبب الإلغاء --}}
                        <div class="text-right">
                            <label class="block mb-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <span>سبب الإلغاء</span>
                                <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                            </label>
                            <textarea name="cancel_reason" x-model="statusPassengerData.cancel_reason" rows="3"
                                placeholder="اكتب سبب إلغاء الحجز..."
                                class="p-3.5 w-full text-sm font-bold text-gray-700 bg-gray-50 rounded-2xl border border-gray-200 transition-all outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-body placeholder:text-gray-400"></textarea>
                        </div>

                        {{-- خيار إرسال واتساب للوسيط --}}
                        <div class="flex items-center gap-2.5 p-3 text-right bg-emerald-50/70 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20">
                            <input type="checkbox" id="send_whatsapp_cancel" name="send_whatsapp" value="1" 
                                x-model="statusPassengerData.send_whatsapp"
                                class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer">
                            <label for="send_whatsapp_cancel" class="text-xs font-bold text-gray-700 dark:text-gray-200 cursor-pointer select-none flex-1">
                                إرسال إشعار الإلغاء للوسيط عبر واتساب 💬
                            </label>
                        </div>

                        <div class="flex gap-3">
                            <button type="button" @click="showStatusModal = false"
                                class="flex-1 py-3.5 text-sm font-black text-gray-600 bg-gray-100 rounded-xl transition-all hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                                تراجع
                            </button>

                            <button type="submit" :disabled="isSubmitting"
                                class="flex flex-1 gap-2 justify-center items-center py-3.5 text-sm font-black text-white bg-amber-500 rounded-xl shadow-lg transition-all hover:bg-amber-600 shadow-amber-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                                <span x-show="!isSubmitting">نعم، إلغاء الراكب</span>
                                <span x-show="isSubmitting"
                                    class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ====================== Delete Modal ====================== --}}
        <template x-teleport="body">
            <div x-show="showDeleteModal" x-cloak
                class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body"
                dir="rtl">

                {{-- الخلفية المظللة --}}
                <div x-show="showDeleteModal" x-transition.opacity.duration.300ms
                    class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                    @click="showDeleteModal = false"></div>

                {{-- المودال نفسه --}}
                <div x-show="showDeleteModal"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-[2rem] shadow-2xl p-8 text-center border border-gray-100 dark:border-boxdark-2">

                    {{-- الأيقونة --}}
                    <div
                        class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-[1.5rem] border border-rose-100 dark:border-rose-500/20">
                        <span class="text-4xl material-symbols-outlined">delete_forever</span>
                    </div>

                    <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">
                        تأكيد الحذف</h3>

                    <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                        هل أنت متأكد من حذف الراكب رقم <br>
                        <span class="text-base font-black text-on-surface dark:text-white font-headline"
                            x-text="deletePassengerData.passenger_number"></span>؟<br>
                        <span class="text-rose-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
                    </p>

                    {{-- فورم الإرسال --}}
                    <form :action="deletePassengerData.url" method="POST" @submit="isSubmitting = true"
                        class="flex gap-3">
                        @csrf
                        @method('DELETE')

                        <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-3.5 text-sm font-black text-gray-600 bg-gray-100 rounded-xl transition-all hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                            تراجع
                        </button>

                        <button type="submit" :disabled="isSubmitting"
                            class="flex flex-1 gap-2 justify-center items-center py-3.5 text-sm font-black text-white bg-rose-500 rounded-xl shadow-lg transition-all hover:bg-rose-600 shadow-rose-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">نعم، احذف</span>
                            <span x-show="isSubmitting"
                                class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                        </button>
                    </form>
                </div>
            </div>
        </template>

    </div>
<datalist id="vehicle_types_list">
    <option value="صالون">
    <option value="باص">
    <option value="نوها">
    <option value="هايلوكس">
    <option value="فوكسي">
    <option value="خصوصي">
</datalist>
@endsection

@section('script')
    <script>
        function passengerRegistry() {
            return {
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                showStatusModal: false,
                isSubmitting: false,

                searchQuery: '{{ request('search', '') }}',
                statusFilter: '{{ request('status', 'pending') }}',
                dateFilter: '{{ request('date', now()->format('Y-m-d')) }}',
                visibleCount: {{ $passengers->count() }},

                getPrintUrl() {
                    let parts = [];
                    if (this.dateFilter && this.dateFilter !== 'all') {
                        const d = this.dateFilter === 'today' ? '{{ now()->format('Y-m-d') }}' : this.dateFilter;
                        parts.push('from:' + d);
                        parts.push('to:' + d);
                    }
                    if (this.statusFilter && this.statusFilter !== 'all') parts.push('status:' + this.statusFilter);
                    parts.push('branch_id:{{ auth()->user()->branch_id }}');
                    return parts.join('|');
                },

                editPassengerData: {
                    id: '',
                    date: '',
                    passenger_number: '',
                    status: 'pending',
                    pickup_location: '',
                    destination: '',
                    vehicle_type: '',
                    count: 1,
                    office_commission: 0,
                    other_office_commission: 0,
                    broker_id: '',
                    broker_name: '',
                    broker_phone: '',
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
                    pickup_location: '',
                    destination: '',
                    count: 1,
                    office_commission: 0,
                    other_office_commission: 0,
                    broker_name: '',
                    broker_phone: '',
                    send_whatsapp: true,
                    driver_phone: '',
                    driver_name: '',
                    cancel_reason: '',
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
                        broker_id: passenger.broker_id || '',
                        broker_name: passenger.broker_name || '',
                        broker_phone: passenger.broker_phone || '',
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
                    this.statusPassengerData = {
                        ...passenger,
                        cancel_reason: passenger.cancel_reason || '',
                        note: passenger.note || '',
                        broker_name: passenger.broker_name || '',
                        broker_phone: passenger.broker_phone || '',
                        send_whatsapp: true,
                        url: '{{ route('passengers.index') }}/' + passenger.id + '/status'
                    };
                    this.showStatusModal = true;
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showDeleteModal = false;
                    this.showStatusModal = false;
                },

                showRow(passengerNumber, location, driverName, brokerName, statusKey, passengerDate) {
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
                        brokerName) || check(arabicStatus);
                    const matchesStatus = !this.statusFilter || this.statusFilter === 'all' || String(statusKey || '') === this.statusFilter;
                    const matchesDate = !this.dateFilter || this.dateFilter === 'all' || String(passengerDate || '') === this.dateFilter || (this.dateFilter === 'today' && passengerDate === '{{ now()->format('Y-m-d') }}');

                    return matchesSearch && matchesStatus && matchesDate;
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
                    let local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '').replace(/^0+/, '');
                    if (!local) return '';
                    if (dial && local.startsWith(dial)) {
                        local = local.substring(dial.length).replace(/^0+/, '');
                    }
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
                records: recordsList || [],
                countries: countriesList || [],
                localPhoneNumber: '',
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                showDropdown: false,
                selectedRecordId: '',
                nameInput: '',
                initializedRecordKey: '',

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
                    let local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '').replace(/^0+/, '');
                    if (!local) return '';
                    if (dial && local.startsWith(dial)) {
                        local = local.substring(dial.length).replace(/^0+/, '');
                    }
                    return dial + local;
                },

                get filteredRecords() {
                    const phone = this.fullPhoneNumber;
                    const local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '');
                    if (!local) return [];
                    return this.records.filter(record => {
                        const recordPhone = String(record.phone || '').replace(/[^\d]/g, '');
                        return recordPhone.includes(local) || recordPhone.includes(phone);
                    });
                },

                searchRecord() {
                    const exact = this.records.find(record => String(record.phone || '').replace(/[^\d]/g, '') === String(
                        this.fullPhoneNumber || '').replace(/[^\d]/g, ''));
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
                },

                loadInitial(record) {
                    if (!record || (!record.id && !record.phone && !record.name)) return;
                    const key = JSON.stringify(record);
                    if (this.initializedRecordKey === key) return;
                    this.initializedRecordKey = key;
                    this.selectedRecordId = record.id || '';
                    this.nameInput = record.name || '';
                    if (record.phone) {
                        this.applyPhone(record.phone);
                    }
                }
            }
        }

        async function sendPassengerBookingDirect(passengerId, btn, fallbackLink = '') {
            if (!passengerId || btn.disabled) return;

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                <span>جاري الإرسال...</span>
            `;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const url = "{{ route('whatsapp.passengerBooking', ':id') }}".replace(':id', passengerId);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                        <span class="text-emerald-600">تم الإرسال بنجاح ✅</span>
                    `;
                    showNotificationToast(data.message || 'تم إرسال تفاصيل الحجز للراكب بنجاح ✅', 'success');
                } else if (data.fallback && data.whatsapp_link) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">جاري فتح الواتساب...</span>
                    `;
                    showNotificationToast(data.message || 'خدمة الواتساب غير متصلة، جاري فتح الواتساب كرسالة...', 'warning');
                    window.open(data.whatsapp_link, '_blank');
                } else {
                    if (fallbackLink) {
                        window.open(fallbackLink, '_blank');
                    }
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">تم التحويل للواتساب</span>
                    `;
                    showNotificationToast(data.message || 'تم فتح الواتساب للإرسال اليدوي', 'warning');
                }
            } catch (error) {
                console.error('WhatsApp direct booking send error:', error);
                if (fallbackLink) {
                    window.open(fallbackLink, '_blank');
                    showNotificationToast('تعذر الاتصال بالسيرفر، تم فتح الواتساب للإرسال كرسالة.', 'warning');
                } else {
                    showNotificationToast('حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
                }
                btn.innerHTML = `
                    <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                    <span class="text-amber-500">تم التحويل للواتساب</span>
                `;
            } finally {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = originalHtml;
                }, 3000);
            }
        }

        async function sendPassengerCancelDirect(passengerId, btn, fallbackLink = '') {
            if (!passengerId || btn.disabled) return;

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                <span>جاري الإرسال...</span>
            `;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const url = "{{ route('whatsapp.passengerCancel', ':id') }}".replace(':id', passengerId);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                        <span class="text-emerald-600">تم الإرسال بنجاح ✅</span>
                    `;
                    showNotificationToast(data.message || 'تم إرسال إشعار الإلغاء للوسيط بنجاح ✅', 'success');
                } else if (data.fallback && data.whatsapp_link) {
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">جاري فتح الواتساب...</span>
                    `;
                    showNotificationToast(data.message || 'خدمة الواتساب غير متصلة، جاري فتح الواتساب كرسالة...', 'warning');
                    window.open(data.whatsapp_link, '_blank');
                } else {
                    if (fallbackLink) {
                        window.open(fallbackLink, '_blank');
                    }
                    btn.innerHTML = `
                        <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                        <span class="text-amber-500">تم التحويل للواتساب</span>
                    `;
                    showNotificationToast(data.message || 'تم فتح الواتساب للإرسال اليدوي', 'warning');
                }
            } catch (error) {
                console.error('WhatsApp direct cancel send error:', error);
                if (fallbackLink) {
                    window.open(fallbackLink, '_blank');
                    showNotificationToast('تعذر الاتصال بالسيرفر، تم فتح الواتساب للإرسال كرسالة.', 'warning');
                } else {
                    showNotificationToast('حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
                }
                btn.innerHTML = `
                    <span class="material-symbols-outlined text-[18px] text-amber-500">open_in_new</span>
                    <span class="text-amber-500">تم التحويل للواتساب</span>
                `;
            } finally {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = originalHtml;
                }, 3000);
            }
        }

        function showNotificationToast(message, type = 'success') {
            const existingToast = document.getElementById('direct-whatsapp-toast');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.id = 'direct-whatsapp-toast';
            let bgClass = 'bg-emerald-600 shadow-emerald-600/25';
            let icon = 'check_circle';

            if (type === 'error') {
                bgClass = 'bg-red-600 shadow-red-600/25';
                icon = 'error';
            } else if (type === 'warning') {
                bgClass = 'bg-amber-600 shadow-amber-600/25';
                icon = 'open_in_new';
            }

            toast.className = `fixed bottom-6 left-6 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-white font-bold text-sm transition-all duration-300 transform translate-y-0 ${bgClass}`;

            toast.innerHTML = `
                <span class="material-symbols-outlined text-[20px]">${icon}</span>
                <span>${message}</span>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 400);
            }, 3500);
        }
    </script>

    @if (session('whatsapp_redirect_url'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    window.open(@js(session('whatsapp_redirect_url')), '_blank');
                }, 300);
            });
        </script>
    @endif
@endsection
