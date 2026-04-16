@extends('layouts.app')
@section('title', 'إدارة الرحلات والشحنات')
@section('Breadcrumb', 'إدارة الرحلات والشحنات المرسله')
@section('addButton')
        <button @click="$dispatch('open-new-trip')"
            class="flex gap-2 justify-center items-center px-4 h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary hover:bg-primary-hover shadow-primary/20 active:scale-95">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" />
            </svg>
            تجهيز رحلة جديدة
        </button>
   
@endsection
       
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

        /* تعديل السكرول بار للوضع الداكن ليتناسب مع boxdark */
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #24303F; 
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

    <div class="space-y-6 font-body" dir="rtl" x-data="{
        search: '',
        openModal: false,
        selectedCount: 0,
        selectedBranch: '',
        showRow(tracking, driver) {
            return tracking.toLowerCase().includes(this.search.toLowerCase()) ||
                driver.toLowerCase().includes(this.search.toLowerCase());
        }
    }" @open-new-trip.window="openModal = true">

        <div class="grid grid-cols-2 gap-4 mb-6 lg:grid-cols-4 lg:gap-6">

            {{-- بطاقة: الطرود قيد الانتظار --}}
            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 dark:border-boxdark-2'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-surface dark:bg-boxdark-2 text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        طرد بانتظار الشحن
                    </span>
                    <h4 class="text-xl font-black text-on-surface dark:text-white">{{ $pendingShipments->count() }}</h4>
                </div>
            </div>

            {{-- بطاقة: عدد جميع الشحنات --}}
            <div @click="filterStatus = 'in_transit'"
                :class="filterStatus === 'in_transit' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-100 dark:border-boxdark-2'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md">
                <div
                    class="flex justify-center items-center w-10 h-10 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        إجمالي الشحنات
                    </span>
                    <h4 class="text-xl font-black text-on-surface dark:text-white">{{ $packages->count() }}</h4>
                </div>
            </div>

            <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]"></div>
            <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]"></div>
        </div>

        <div class="overflow-hidden bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark">
            <div class="p-6 w-full bg-white rounded-2xl dark:bg-boxdark">
                <div class="relative w-full rounded-2xl border ring-2 group border-primary ring-primary/20 dark:border-primary dark:ring-primary/20">
                    <input type="text" x-model="search" placeholder="ابحث برقم التتبع، اسم السائق..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 rounded-xl border-none transition-all outline-none bg-surface dark:bg-boxdark-2 focus:ring-2 focus:ring-primary/20 dark:text-white">
                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                @forelse($packages as $pkg)
                    <div x-show="showRow('{{ $pkg->tracking_number }}', '{{ $pkg->driver_name }}')" x-transition
                        class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">

                        {{-- Header --}}
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <span class="px-3 py-1.5 text-xs font-black bg-white rounded-lg border border-gray-100 shadow-inner dark:bg-boxdark text-primary dark:border-boxdark-2">
                                    #{{ $pkg->tracking_number }}
                                </span>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-on-surface dark:text-white">{{ $pkg->driver_name }}</span>
                                    <x-phone-number :value="$pkg->driver_phone" class="font-bold text-[10px] text-gray-400 dark:text-bodydark" />
                                </div>
                            </div>
                            <a href="{{ route('shipmentpackage.show', $pkg->id) }}"
                                class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-primary dark:bg-boxdark dark:border-boxdark-2"
                                title="عرض تفاصيل الشحنه">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>

                        {{-- Info Row --}}
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-boxdark">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-primary-container dark:bg-primary/10 text-primary-hover dark:text-primary">
                                {{ $pkg->shipments_count }} طرود مشحونه
                            </span>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter dark:text-bodydark">{{ $pkg->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="flex flex-col gap-2 items-center italic text-gray-400 dark:text-bodydark">
                            <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <span>لا توجد رحلات مسجلة حالياً..</span>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] dark:text-bodydark">
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
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-boxdark hover:shadow-md hover:border-gray-100 dark:hover:border-boxdark-2 group">

                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-boxdark-2">
                                    <span class="px-3 py-1.5 text-xs font-black rounded-lg border border-gray-100 shadow-inner bg-surface dark:bg-boxdark-2 text-primary dark:border-boxdark">
                                        #{{ $pkg->tracking_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-boxdark-2">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-on-surface dark:text-white">{{ $pkg->driver_name }}</span>
                                        <x-phone-number :value="$pkg->driver_phone" class="font-bold text-[10px] text-gray-400 dark:text-bodydark" />
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-boxdark-2">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg border bg-primary-container dark:bg-primary/10 text-primary-hover dark:text-primary border-primary/20 dark:border-primary/10">
                                        {{ $pkg->shipments_count }} طرود مشحونه
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-boxdark-2">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter dark:text-bodydark">{{ $pkg->created_at->format('Y-m-d') }}</span>
                                </td>

                                <td class="px-6 py-5 text-center border-l last:rounded-l-2xl border-y dark:border-boxdark-2">
                                    <div class="flex gap-2 justify-center items-center">
                                        <a href="{{ route('shipmentpackage.show', $pkg->id) }}"
                                            class="p-2 text-gray-400 rounded-xl transition-all hover:text-primary hover:bg-primary-container dark:hover:bg-primary/10"
                                            title="عرض تفاصيل الشحنه">
                                            <svg class="w-5 h-5 text-gray-400 transition-colors group-hover:text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <div class="flex flex-col gap-2 items-center italic text-gray-400 dark:text-bodydark">
                                        <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
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
                <div class="p-8 border-t border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark text-primary">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>

        @include('pages.shipmentpackage.modals.create-modal')
    </div>

@endsection