@extends('layouts.app')
@section('title', 'التقارير المالية للفروع')
@section('Breadcrumb', 'التقارير المالية للفروع')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="p-8 space-y-10 max-w-full mx-auto.font-outfit" x-data="{
        search: '',
        normalize(text) {
            if (!text) return '';
            return text.toString().toLowerCase();
        },
        matchesSearch(...fields) {
            if (this.search.trim() === '') {
                return true;
            }
            const term = this.normalize(this.search);
            return fields.some(field => this.normalize(field).includes(term));
        }
    }">

        {{-- هيدر الصفحة --}}
        <div class="relative p-10 rounded-2xl my-4 overflow-hidden shadow-theme-lg border border-brand-500/20 bg-brand-500">
            <div class="absolute top-0 right-0 w-80 h-80 bg-white/10 rounded-full -mr-32 -mt-32 blur-[80px]"></div>

            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div class="space-y-4">
                    <h2 class="text-title-md font-bold text-white tracking-tight my-2">
                        التقارير المالية للفروع
                    </h2>
                    <div class="flex items-center gap-3 text-white/90">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                        </span>
                        <p class="text-sm font-medium leading-tight">
                            نظرة شاملة على أرصدة الفروع، الشحنات الآجلة (شاملة الجزئية)، والتسويات المالية البينية
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- البحث --}}
        <div
            class="w-full bg-white dark:bg.white/[0.03] p-6 rounded-2xl my-4 border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="relative group w-full">
                <input type="text" x-model="search" .placeholder="ابحث باسم الفرع، كود الفرع، أو قيمة الرصيد..."
                    class="w-full h-12 pr-11 pl-4 rounded-xl border-none bg-gray-50.dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all text-sm font-medium dark:text-white placeholder-gray-400">
                <div
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 group-focus-within:text-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- جدول ملخص الفروع --}}
        <div
            class="bg-white dark:bg-gray-dark rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden transition-all duration-300">
            <div class="px-8 py-6 border-b border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/40">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white flex.items-center gap-2">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    ملخص الوضع المالي لكل الفروع
                </h3>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                @foreach ($branchesSummary as $row)
                    @php
                        $branch = $row['branch'];
                        $net = $row['net_balance'];
                    @endphp
                    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800"
                        x-show="matchesSearch(@js($branch->name), @js($branch->code), @js(number_format($net, 2)))"
                        x-transition>

                        {{-- Header --}}
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div
                                    class="w-10 h-10 rounded-xl bg-brand-500/10 dark:bg-brand-500/5 flex items-center justify-center text-brand-500 font-bold text-lg border border-brand-500/10">
                                    {{ mb_substr($branch->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $branch->name }}</span>
                                    <span class="text-[11px] font-semibold text-gray-400">كود: {{ $branch->code }}</span>
                                </div>
                            </div>
                            <a href="{{ route('finance.branches.show', $branch->code) }}"
                                class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-brand-500 dark:bg-gray-900 dark:border-gray-800"
                                title="التفاصيل">
                                <svg class="w-5 h-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </a>
                        </div>

                        {{-- Financial info --}}
                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[10px] font-medium text-gray-400">إجمالي الآجل</span>
                                <span
                                    class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ number_format($row['total_cod'], 2) }}
                                    <span class="text-[10px] opacity-50">ر.ي</span></span>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[10px] font-medium text-success-600">تسويات مستلمة</span>
                                <span
                                    class="text-xs font-bold text-success-600">{{ number_format($row['total_settle_in'], 2) }}</span>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[10px] font-medium text-warning-600">تسويات مدفوعة</span>
                                <span
                                    class="text-xs font-bold text-warning-600">{{ number_format($row['total_settle_out'], 2) }}</span>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-[10px] font-medium text-gray-400">الرصيد النهائي</span>
                                @if ($net > 0)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black bg-success-500 text-white w-fit">
                                        لنا {{ number_format($net, 2) }}
                                    </span>
                                @elseif ($net < 0)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black bg-error-500 text-white w-fit">
                                        علينا {{ number_format(abs($net), 2) }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black bg-gray-100 dark:bg-gray-800 text-gray-500 w-fit">
                                        مُسوى
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto custom-scrollbar lg:block">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                            <th
                                class="px-8 py-5 text-right text-theme-xs font-bold text-gray-400 uppercase tracking-[0.15em]">
                                اسم الفرع
                            </th>
                            <th
                                class="px-8 py-5 text-right text-theme-xs font-bold text-gray-400 uppercase tracking-[0.15em]">
                                إجمالي الآجل
                            </th>
                            <th
                                class="px-8 py-5 text-right text-theme-xs font-bold text-gray-400 uppercase tracking-[0.15em]">
                                تسويات مستلمة
                            </th>
                            <th
                                class="px-8 py-5 text-right text-theme-xs font-bold text-gray-400 uppercase tracking-[0.15em]">
                                تسويات مدفوعة
                            </th>
                            <th
                                class="px-8 py-5 text-right text-theme-xs font-bold text-gray-400 uppercase tracking-[0.15em]">
                                الرصيد النهائي
                            </th>
                            <th
                                class="px-8 py-5 text-center text-theme-xs font-bold text-gray-400 uppercase tracking-[0.15em]">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800 bg-white dark:bg-transparent">
                        @foreach ($branchesSummary as $row)
                            @php
                                $branch = $row['branch'];
                                $net = $row['net_balance'];
                            @endphp
                            <tr x-show="matchesSearch(
                                    @js($branch->name),
                                    @js($branch->code),
                                    @js(number_format($row['total_cod'], 2)),
                                    @js(number_format($row['total_settle_in'], 2)),
                                    @js(number_format($row['total_settle_out'], 2)),
                                    @js(number_format($net, 2))
                                )"
                                class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors group">

                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-brand-500/10 dark:bg-brand-500/5 flex items-center justify-center text-brand-500 font-bold text-xl border border-brand-500/10">
                                            {{ mb_substr($branch->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-gray-800 dark:text-gray-200 group-hover:text-brand-500 transition-colors">
                                                {{ $branch->name }}
                                            </span>
                                            <span class="text-[11px] font-semibold text-gray-400">
                                                كود الفرع: {{ $branch->code }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-6 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ number_format($row['total_cod'], 2) }}
                                    <span class="text-[10px] opacity-50">ر.ي</span>
                                </td>

                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-success-600 dark:text-success-400 font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                        </svg>
                                        {{ number_format($row['total_settle_in'], 2) }}
                                    </div>
                                </td>

                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-warning-600 dark:text-warning-400 font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                        </svg>
                                        {{ number_format($row['total_settle_out'], 2) }}
                                    </div>
                                </td>

                                <td class="px-8 py-6 whitespace-nowrap">
                                    @if ($net > 0)
                                        <span
                                            class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black bg-success-500 text-white shadow-lg shadow-success-500/20 uppercase tracking-tighter">
                                            لنا {{ number_format($net, 2) }}
                                        </span>
                                    @elseif ($net < 0)
                                        <span
                                            class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black bg-error-500 text-white shadow-lg shadow-error-500/20 uppercase tracking-tighter">
                                            علينا {{ number_format(abs($net), 2) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-black bg-gray-100 dark:bg-gray-800 text-gray-500 uppercase tracking-tighter">
                                            مُسوى بالكامل
                                        </span>
                                    @endif
                                </td>

                                <td class="px-8 py-6 text-center whitespace-nowrap">
                                    <a href="{{ route('finance.branches.show', $branch->code) }}"
                                        class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-gray-50 dark:bg-gray-900 text-gray-400 hover:bg-brand-500 hover:text-white hover:shadow-lg hover:shadow-brand-500/30 transition-all duration-300 hover:rotate-6">
                                        <svg class="w-5 h-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
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
