@extends('layouts.app')
@section('title', 'إدارة العملاء')

@section('content')

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl" x-data="customerRegistry()"
        @open-create-customer-modal.window="openCreateModal()">
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex gap-4 justify-between items-start">
                <div class="text-right">
                    <h1 class="text-2xl font-black md:text-3xl text-on-surface dark:text-white">
                        إدارة العملاء
                    </h1>
                    <p class="mt-1 text-sm font-bold text-gray-500 dark:text-bodydark">
                        إجمالي {{ $customers->total() ?? 0 }} عميل مسجل
                    </p>
                </div>

                <button type="button" @click="openCreateModal()"
                    class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                    <span>إضافة عميل جديد</span>
                </button>
            </div>
        </div>

        {{-- Modals --}}
        @include('pages.customers.create-customer-modal')
        @include('pages.customers.edit-customer-modal')

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-2 mx-auto max-w-7xl xl:grid-cols-3 md:gap-6">

            {{-- إجمالي العملاء --}}
            <div @click="filterStatus = 'all'; updateVisibility()"
                :class="filterStatus === 'all' ? 'border-primary ring-2 ring-primary/20' :
                    'border-gray-100 hover:border-primary/50 dark:border-boxdark-2'"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md">
                <div
                    class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">group</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">إجمالي
                        العملاء</span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $customers->total() }}
                    </h4>
                </div>
            </div>

            {{-- المديونين --}}
            <div @click="filterStatus = 'debtor'; updateVisibility()"
                :class="filterStatus === 'debtor' ? 'border-rose-500 ring-2 ring-rose-500/20' :
                    'border-gray-100 hover:border-rose-300 dark:border-boxdark-2'"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md border-r-rose-500 dark:border-r-rose-500">
                <div
                    class="flex justify-center items-center w-12 h-12 text-rose-500 bg-rose-50 rounded-xl dark:bg-rose-500/10">
                    <span class="material-symbols-outlined text-[24px]">account_balance_wallet</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-rose-500 uppercase">المديونين</span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $customers->getCollection()->filter(fn($c) => ($c->debit_sum ?? 0) > ($c->credit_sum ?? 0))->count() }}
                    </h4>
                </div>
            </div>

            {{-- رصيد مسدد --}}
            <div @click="filterStatus = 'cleared'; updateVisibility()"
                :class="filterStatus === 'cleared' ? 'border-emerald-500 ring-2 ring-emerald-500/20' :
                    'border-gray-100 hover:border-emerald-300 dark:border-boxdark-2'"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md border-r-emerald-500 dark:border-r-emerald-500">
                <div
                    class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">task_alt</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">رصيد مسدد</span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $customers->getCollection()->filter(fn($c) => ($c->debit_sum ?? 0) <= ($c->credit_sum ?? 0))->count() }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- ====================== Search & Table Section ====================== --}}
        <div
            class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

                    {{-- Search --}}
                    <div
                        class="relative w-full rounded-2xl border border-gray-200 transition-all md:max-w-md dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">

                        <input type="text" x-model="search" @input.debounce.300ms="updateVisibility()"
                            placeholder="ابحث بالاسم أو رقم الهاتف..."
                            class="pr-12 pl-12 w-full h-12 text-sm font-bold placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                        <div
                            class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-outlined text-[22px]">search</span>
                        </div>

                        <button type="button" x-show="search && search.length > 0" x-cloak
                            @click="search = ''; updateVisibility()"
                            class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all -translate-y-1/2 dark:bg-boxdark dark:border-boxdark-2 hover:text-rose-500 active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>

                    {{-- Filters --}}
                    <div class="flex overflow-x-auto gap-2 pb-1 md:pb-0 custom-scrollbar snap-x md:justify-end">

                        {{-- الكل --}}
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
                            class="snap-start shrink-0 px-4 h-11 inline-flex gap-2 items-center justify-center rounded-2xl text-[12px] font-black transition-all duration-200 border active:scale-95
                {{ $filter == 'all'
                    ? 'bg-primary text-white border-primary shadow-md shadow-primary/20 dark:bg-white dark:text-slate-900 dark:border-white'
                    : 'bg-white text-gray-500 border-gray-100 shadow-sm hover:bg-gray-50 hover:text-gray-700 dark:bg-boxdark dark:text-bodydark dark:border-boxdark-2 dark:hover:bg-boxdark-2' }}">

                            <span class="material-symbols-outlined text-[18px]">groups</span>
                            جميع العملاء
                        </a>

                        {{-- عليهم ديون --}}
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'debtors']) }}"
                            class="snap-start shrink-0 px-4 h-11 inline-flex gap-2 items-center justify-center rounded-2xl text-[12px] font-black transition-all duration-200 border active:scale-95
                {{ $filter == 'debtors'
                    ? 'bg-rose-500 text-white border-rose-500 shadow-md shadow-rose-500/25'
                    : 'bg-white text-gray-500 border-gray-100 shadow-sm hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 dark:bg-boxdark dark:text-bodydark dark:border-boxdark-2 dark:hover:bg-rose-500/10 dark:hover:text-rose-400 dark:hover:border-rose-500/20' }}">

                            <span class="material-symbols-outlined text-[18px]">money_off</span>
                            عليهم ديون
                        </a>

                        {{-- حسابات مصفّرة --}}
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'creditors']) }}"
                            class="snap-start shrink-0 px-4 h-11 inline-flex gap-2 items-center justify-center rounded-2xl text-[12px] font-black transition-all duration-200 border active:scale-95
                {{ $filter == 'creditors'
                    ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/25'
                    : 'bg-white text-gray-500 border-gray-100 shadow-sm hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-100 dark:bg-boxdark dark:text-bodydark dark:border-boxdark-2 dark:hover:bg-emerald-500/10 dark:hover:text-emerald-400 dark:hover:border-emerald-500/20' }}">

                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                            حسابات مصفّرة
                        </a>
                    </div>
                </div>

                {{-- Results counter --}}
                <div class="flex gap-2 items-center mt-4 text-xs font-black text-gray-500 dark:text-bodydark">
                    <span
                        class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                    </span>

                    <span>
                        النتائج المعروضة:
                        <span class="text-primary" x-text="visibleCount"></span>
                        من
                        <span>{{ $customers->count() }}</span>
                    </span>
                </div>
            </div>

            {{-- ====================== Mobile View ====================== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse($customers as $customer)
                    @php
                        $balance = ($customer->debit_sum ?? 0) - ($customer->credit_sum ?? 0);
                        $is_debtor = $balance > 0;
                    @endphp

                    <div class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all customer-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm"
                        x-show="showRow('{{ $customer->name }}', '{{ $customer->phone }}', {{ $is_debtor ? 'true' : 'false' }})">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-lg font-black text-white rounded-xl shadow-inner bg-primary">
                                    {{ mb_substr($customer->name, 0, 1) }}
                                </div>

                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-black text-on-surface dark:text-white font-headline">
                                        {{ $customer->name }}
                                    </span>
                                    <x-phone-number :value="$customer->phone"
                                        class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                </div>
                            </div>

                            <div x-data="{ menuOpen: false }" class="relative">
                                <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false"
                                    class="p-2 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="menuOpen" x-transition x-cloak
                                    class="absolute left-0 top-full z-[999] py-1.5 mt-2 w-52 rounded-2xl border border-gray-100 shadow-lg backdrop-blur-md bg-white/95 dark:bg-boxdark-2/95 dark:border-boxdark overflow-hidden">

                                    <a href="{{ route('customers.show', $customer->id) }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark dark:hover:text-blue-400">
                                        <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                        كشف الحساب
                                    </a>

                                    <button type="button" @click="menuOpen = false; openEditModal({{ $customer->id }})"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark dark:hover:text-primary">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                        تعديل البيانات
                                    </button>

                                    @if ($is_debtor)
                                        <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>
                                        <div class="px-2 py-1">
                                            @include('pages.customers.clearamount', [
                                                'customer' => $customer,
                                            ])
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-boxdark">
                            <span
                                class="px-2.5 py-1 rounded-lg bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 shadow-sm text-gray-500 dark:text-gray-300 text-[10px] font-black uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px] text-primary">store</span>
                                {{ $customer->branch->name ?? 'N/A' }}
                            </span>

                            <div class="flex flex-col items-end">
                                <span
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-black {{ $is_debtor ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' }}">
                                    {{ $is_debtor ? 'مديون' : 'مسدد' }}
                                </span>

                                @if ($is_debtor)
                                    <span class="mt-1.5 text-xs font-black text-rose-500">
                                        {{ number_format($balance, 0) }} ر.ي
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col gap-3 items-center py-16 text-center text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:text-bodydark dark:border-boxdark-2 bg-surface dark:bg-boxdark-2">
                        <span class="material-symbols-outlined text-[40px] opacity-30">group_off</span>
                        <p class="text-sm font-bold">لا توجد بيانات عملاء مطابقة..</p>
                    </div>
                @endforelse

                <div x-show="visibleCount === 0 && {{ $customers->count() }} > 0" x-cloak
                    class="py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex flex-col justify-center items-center">
                        <span
                            class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                        <h4 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            لا توجد نتائج تطابق بحثك أو تصفيتك في هذه الصفحة.
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
                            <th class="px-6 py-4">العميل</th>
                            <th class="px-6 py-4 text-center">الفرع المسجل</th>
                            <th class="px-6 py-4 text-center">الرصيد المالي</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse($customers as $customer)
                            @php
                                $balance = ($customer->debit_sum ?? 0) - ($customer->credit_sum ?? 0);
                                $is_debtor = $balance > 0;
                            @endphp

                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group customer-row"
                                x-show="showRow('{{ $customer->name }}', '{{ $customer->phone }}', {{ $is_debtor ? 'true' : 'false' }})">

                                {{-- العميل --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-4 items-center">
                                        <div
                                            class="flex justify-center items-center w-11 h-11 text-lg font-black text-white rounded-lg shadow-inner bg-primary">
                                            {{ mb_substr($customer->name, 0, 1) }}
                                        </div>

                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-black text-gray-800 dark:text-white">
                                                {{ $customer->name }}
                                            </span>
                                            <x-phone-number :value="$customer->phone"
                                                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                        </div>
                                    </div>
                                </td>

                                {{-- الفرع --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                        {{ $customer->branch->name ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- الرصيد --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span
                                            class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase {{ $is_debtor ? 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' }}">
                                            {{ $is_debtor ? 'مديون' : 'مسدد' }}
                                        </span>

                                        @if ($is_debtor)
                                            <span class="text-xs font-black text-rose-500">
                                                {{ number_format($balance, 0) }} ر.ي
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- الإجراءات - Desktop Fixed --}}
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

                                                {{-- كشف الحساب --}}
                                                <a href="{{ route('customers.show', $customer->id) }}"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark-2 dark:hover:text-blue-400">
                                                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                                    كشف الحساب
                                                </a>

                                                {{-- تعديل العميل --}}
                                                <button type="button"
                                                    @click="open = false; openEditModal({{ $customer->id }})"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark-2 dark:hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                    تعديل العميل
                                                </button>

                                                {{-- تصفية الحساب --}}
                                                @if ($is_debtor)
                                                    <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                                    <div class="px-2 py-1">
                                                        @include('pages.customers.clearamount', [
                                                            'customer' => $customer,
                                                        ])
                                                    </div>
                                                @endif

                                                {{-- حذف العميل - اختياري، فعّله إذا عندك route destroy --}}
                                                {{-- 
                                                <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                                <form action="{{ route('customers.destroy', $customer->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                        حذف العميل
                                                    </button>
                                                </form>
                                                --}}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div
                                            class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span
                                                class="material-symbols-outlined text-[28px] text-gray-400">group_off</span>
                                        </div>

                                        <div>
                                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                                لا توجد بيانات للعملاء
                                            </h3>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                لم نعثر على أي عملاء مسجلين في النظام حالياً.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $customers->count() }} > 0" x-cloak>
                            <td colspan="4" class="py-24 text-center">
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
                                            لم نعثر على أي عملاء يطابقون كلمة البحث المدخلة.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($customers->hasPages())
                <div
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
    <script>
        function customerRegistry() {
            return {
                search: '',
                filterStatus: 'all',
                isFetching: null,
                visibleCount: {{ $customers->count() }},
                countries: @json(array_values(config('countries') ?? [])),
                customersList: @json($customers->items()),

                editCustomer: {
                    id: null,
                    name: '',
                    phone: '',
                    phone_country: null,
                    phone_local: '',
                    url: ''
                },

                editModalOpen: false,
                createModalOpen: false,

                showDeleteModal: false,
                isSubmitting: false,
                errors: {},

                createCustomerData: {
                    name: '',
                    phone: ''
                },

                deleteCustomerData: {
                    id: '',
                    name: '',
                    url: ''
                },

                init() {
                    if (!this.countries || this.countries.length === 0) {
                        this.countries = [{
                            name: 'اليمن',
                            code: 'YE',
                            dial_code: '967',
                            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 600"><rect width="900" height="600" fill="#000"/><rect width="900" height="200" fill="#ce1126"/><rect y="400" width="900" height="200" fill="#fff"/></svg>'
                        }];
                    }

                    this.editCustomer.phone_country = this.countries.find(c => c.code === 'YE') || this.countries[0];
                },

                openCreateModal() {
                    this.errors = {};
                    this.createCustomerData = {
                        name: '',
                        phone: ''
                    };
                    this.createModalOpen = true;
                },

                openDeleteModal(id, name) {
                    this.deleteCustomerData = {
                        id: id,
                        name: name,
                        url: '{{ route('customers.index') }}/' + id
                    };

                    this.showDeleteModal = true;
                },

                closeModals() {
                    this.createModalOpen = false;
                    this.editModalOpen = false;
                    this.showDeleteModal = false;
                    this.errors = {};
                },

                async submitForm(url, method, data) {
                    this.isSubmitting = true;
                    this.errors = {};

                    try {
                        const response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            if (response.status === 422) {
                                this.errors = result.errors;
                            } else {
                                alert(result.message || 'حدث خطأ غير متوقع.');
                            }
                        } else {
                            this.closeModals();
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('حدث خطأ في الاتصال بالخادم.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                showRow(name, phone, isDebtor) {
                    const searchValue = this.search.toLowerCase();

                    const matchesSearch =
                        String(name).toLowerCase().includes(searchValue) ||
                        String(phone).includes(this.search);

                    const matchesStatus =
                        this.filterStatus === 'all' ||
                        (this.filterStatus === 'debtor' && isDebtor) ||
                        (this.filterStatus === 'cleared' && !isDebtor);

                    return matchesSearch && matchesStatus;
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.customer-row:not([style*="display: none"])')
                            .length;
                    });
                },

                parsePhoneNumber(fullNumber) {
                    if (!fullNumber) {
                        return {
                            country: this.countries.find(c => c.code === 'YE') || this.countries[0],
                            local: ''
                        };
                    }

                    let phone = String(fullNumber).replace('+', '');
                    const sortedCountries = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);

                    for (let country of sortedCountries) {
                        const regex = new RegExp(`^(00)?${country.dial_code}`);

                        if (regex.test(phone)) {
                            return {
                                country: country,
                                local: phone.replace(regex, '')
                            };
                        }
                    }

                    return {
                        country: this.countries.find(c => c.code === 'YE') || this.countries[0],
                        local: fullNumber
                    };
                },

                openEditModal(customerId) {
                    try {
                        this.errors = {};

                        const customer = this.customersList.find(c => c.id === customerId);

                        if (!customer) {
                            alert('تعذر العثور على بيانات العميل');
                            return;
                        }

                        let parsedPhone;

                        try {
                            parsedPhone = this.parsePhoneNumber(customer.phone);
                        } catch (e) {
                            console.error('Phone parsing error:', e);

                            parsedPhone = {
                                country: this.countries.find(c => c.code === 'YE') || this.countries[0],
                                local: customer.phone || ''
                            };
                        }

                        this.editCustomer.id = customer.id;
                        this.editCustomer.name = customer.name;
                        this.editCustomer.phone_local = parsedPhone.local;
                        this.editCustomer.phone_country = parsedPhone.country;
                        this.editCustomer.url = '{{ route('customers.index') }}/' + customer.id;

                        this.editModalOpen = true;
                    } catch (error) {
                        alert('حدث خطأ غير متوقع: ' + error.message);
                    }
                }
            }
        }
    </script>
@endsection
