@extends('layouts.app')
@section('title', 'إدارة الرحلات والشحنات')

@section('style')
    <style>
        /* تنسيق السكرول بار داخل قائمة الطرود */
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
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
                search: '',
                openModal: false,
                selectedCount: 0,
                selectedBranch: '',
                showRow(tracking, driver) {
                    return tracking.toLowerCase().includes(this.search.toLowerCase()) ||
                        driver.toLowerCase().includes(this.search.toLowerCase());
                }
            }">

        <div
            class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="flex gap-4 items-center xl:col-span-4">
                <div
                    class="flex justify-center items-center w-12 h-12 text-white rounded-2xl shadow-lg bg-brand-500 shadow-brand-500/30 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black leading-tight text-gray-900 dark:text-white">شحنات الرحلات</h2>
                    <p class="italic font-bold tracking-widest text-gray-500 uppercase text-theme-xs">تجميع وإدارة الشحن
                        الجماعي</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 justify-end xl:col-span-8 md:flex-row">
                <div
                    class="flex gap-4 items-center px-6 py-3 rounded-2xl border bg-brand-50 dark:bg-brand-500/10 border-brand-100 dark:border-brand-500/20">
                    <span
                        class="text-2xl font-black text-brand-600 dark:text-brand-400">{{ $pendingShipments->count() }}</span>
                    <span class="text-xs font-bold leading-tight text-gray-500 uppercase">طرد بانتظار <br>الشحن</span>
                </div>

                @if ($pendingShipments->count() > 0)
                    <button @click="openModal = true"
                        class="flex gap-2 justify-center items-center px-8 h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/20 active:scale-95">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" />
                        </svg>
                        تجهيز رحلة جديدة
                    </button>
                @endif
            </div>
        </div>

        <div
            class="w-full bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="relative w-full group">
                <input type="text" x-model="search" placeholder="ابحث برقم التتبع، اسم السائق..."
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

        <div
            class="overflow-hidden bg-white rounded-2xl border border-gray-100 dark:bg-gray-800 dark:border-gray-800 shadow-theme-sm">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">رقم التتبع</th>
                            <th class="px-6 py-4">السائق المسؤول</th>
                            <th class="px-6 py-4 text-center">حمولة الطرود</th>
                            <th class="px-6 py-4 text-center">التاريخ</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse($packages as $pkg)
                            <tr x-show="showRow('{{ $pkg->tracking_number }}', '{{ $pkg->driver_name }}')" x-transition
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1.5 text-xs font-black bg-gray-50 rounded-lg border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                                        #{{ $pkg->tracking_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-900 dark:text-white">{{ $pkg->driver_name }}</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 font-mono">{{ $pkg->driver_phone }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1 text-xs font-black rounded-lg border bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-100/50">
                                        {{ $pkg->shipments_count }} طرود مشحونه
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $pkg->created_at->format('Y-m-d') }}</span>
                                </td>

                                <td class="px-6 py-5 text-center border-l last:rounded-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-2 justify-center items-center">
                                        <a href="{{ route('shipmentpackage.show', $pkg->id) }}"
                                            class="p-2 text-gray-400 rounded-xl transition-all hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10"
                                            title="عرض تفاصيل الشحنه">
                                            <svg class="w-5 h-5 text-gray-400 transition-colors group-hover:text-brand-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <div class="flex flex-col gap-2 items-center italic text-gray-400">
                                        <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <span>لا توجد رحلات مسجلة حالياً..</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($packages->hasPages())
                <div class="p-8 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>

        @include('pages.shipmentpackage.modals.create-modal')
    </div>


@endsection