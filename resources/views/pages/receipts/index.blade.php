@extends('layouts.app')
@section('title', 'الشحنات المستلمه')
@section('Breadcrumb', 'إدارة الرحلات والشحنات المستلمة')
@section('addButton')
    <a href="{{ route('receipts.create') }}"
        class="flex gap-2 justify-center items-center px-4 h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/20 active:scale-95">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" />
        </svg>
        إضافة بيان استلام
    </a>
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection

@section('style')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }
    </style>
@endsection

@section('content')

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
                                    search: '',
                                    searchType: 'all',
                                    filterStatus: 'all',
                                    showRow(number, driver, branch, itemNumbers, deliveryStatus) {
                                        if (this.filterStatus !== 'all' && deliveryStatus !== this.filterStatus) return false;
                                        const s = this.search.toLowerCase();
                                        if (!s) return true;
                                        if (this.searchType === 'receipt') {
                                            return number.toLowerCase().includes(s);
                                        } else if (this.searchType === 'item') {
                                            return itemNumbers.toLowerCase().includes(s);
                                        } else {
                                            return number.toLowerCase().includes(s) ||
                                                driver.toLowerCase().includes(s) ||
                                                branch.toLowerCase().includes(s) ||
                                                itemNumbers.toLowerCase().includes(s);
                                        }
                                    }
                                }">

        {{-- بطاقات إحصائية --}}
        <div class="flex gap-6 mb-6">
            {{-- إجمالي البيانات --}}
            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-xl dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        إجمالي بيانات الاستلام</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $totalReceipts }}</h4>
                </div>
            </div>

            {{-- إجمالي الطرود --}}
            <div
                class="flex-1 relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        إجمالي الطرود المستلمه</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $totalItems }}</h4>
                </div>
            </div>

            {{-- شحنات مكتملة التسليم --}}
            <div @click="filterStatus = 'all_delivered'"
                :class="filterStatus === 'all_delivered' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        مكتملة التسليم</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $fullyDelivered }}</h4>
                </div>
            </div>

            {{-- شحنات بها طرود غير مسلمة --}}
            <div @click="filterStatus = 'has_pending'"
                :class="filterStatus === 'has_pending' ? 'border-warning-500 ring-2 ring-warning-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        غير مكتملة</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $hasPending }}</h4>
                </div>
            </div>
        </div>

        {{-- الجدول --}}
        <div
            class="overflow-hidden bg-white rounded-2xl border border-gray-100 dark:bg-gray-800 dark:border-gray-800 shadow-theme-sm">
            <div class="w-full bg-white dark:bg-white/[0.03] p-6 rounded-2xl">
                <div style="display: flex; gap: 10px;">
                    <select x-model="searchType"
                        class="h-12 px-4 text-sm font-bold text-gray-700 bg-gray-50 rounded-xl border border-gray-200 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
                        style="flex: 0 0 auto; min-width: 160px;">
                        <option value="all">بحث عام</option>
                        <option value="receipt">رقم سند الشحنة</option>
                        <option value="item">رقم سند الطرد</option>
                    </select>
                    <div class="relative group border border-brand-500 ring-2 ring-brand-500/20 rounded-2xl"
                        style="flex: 1;">
                        <input type="text" x-model="search"
                            :placeholder="searchType === 'receipt' ? 'ابحث برقم سند الشحنة...' : (searchType === 'item' ? 'ابحث برقم سند الطرد...' : 'ابحث برقم السند، اسم السائق، المكتب المرسل...')"
                            class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                        <div
                            class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 group-focus-within:text-brand-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">رقم السند</th>
                            <th class="px-6 py-4">المكتب المرسل</th>
                            <th class="px-6 py-4">السائق</th>
                            <th class="px-6 py-4 text-center">عدد الطرود</th>
                            <th class="px-6 py-4 text-center">الملاحظات</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse($receipts as $receipt)
                            <tr x-show="showRow('{{ $receipt->number }}', '{{ $receipt->driver->name ?? '' }}', '{{ $receipt->sourceBranch->name ?? '' }}', '{{ $receipt->items->pluck('number')->implode(',') }}', '{{ $receipt->items->count() > 0 && $receipt->items->every(fn($i) => $i->is_delivered) ? 'all_delivered' : 'has_pending' }}')"
                                x-transition
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">

                                {{-- رقم السند --}}
                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1.5 text-xs font-black bg-gray-50 rounded-lg border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                                        #{{ $receipt->number }}
                                    </span>
                                </td>

                                {{-- المكتب المرسل --}}
                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <span
                                        class="text-sm font-black text-gray-900 dark:text-white">{{ $receipt->sourceBranch->name ?? '—' }}</span>
                                </td>

                                {{-- السائق --}}
                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-900 dark:text-white">{{ $receipt->driver->name ?? '—' }}</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 font-mono">{{ $receipt->driver->phone ?? '' }}</span>
                                    </div>
                                </td>

                                {{-- عدد الطرود --}}
                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1 text-xs font-black rounded-lg border bg-success-50 dark:bg-success-500/10 text-success-600 dark:text-success-400 border-success-100/50">
                                        {{ $receipt->items->count() }} طرود
                                    </span>
                                </td>

                                {{-- الملاحظات --}}
                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $receipt->notes ?? '-' }}</span>
                                </td>

                                {{-- الإجراءات --}}
                                <td class="px-6 py-5 text-center border-l last:rounded-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-2 justify-center items-center">
                                        <a href="{{ route('receipts.show', $receipt->id) }}"
                                            class="p-2 text-gray-400 rounded-xl transition-all hover:text-success-500 hover:bg-success-50 dark:hover:bg-success-500/10"
                                            title="عرض التفاصيل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('receipts.edit', $receipt->id) }}"
                                            class="p-2 text-gray-400 rounded-xl transition-all hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10"
                                            title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col gap-2 items-center italic text-gray-400">
                                        <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <span>لا توجد بيانات استلام مسجلة حالياً..</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($receipts->hasPages())
                <div class="p-8 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
                    {{ $receipts->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection