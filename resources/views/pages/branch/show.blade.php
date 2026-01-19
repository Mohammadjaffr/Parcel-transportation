@extends('layouts.app')
@section('title', 'تفاصيل الفرع: ' . $branch->name)

@section('content')
    <div class="min-h-screen bg-[#F8F9FC] dark:bg-gray-950 font-outfit" dir="rtl">

        <div class="max-w-[1400px] mx-auto p-4 md:p-6 lg:p-8 space-y-6">

            {{-- Statistics Cards --}}
            <div class="flex gap-6">

                <div
                    class="flex-1 relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 hover:border-brand-200 transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex justify-center items-center w-12 h-12 rounded-xl transition-transform duration-300 bg-brand-50 text-brand-500 dark:bg-brand-500/10 group-hover:scale-110">
                        <svg class="w-6 h-6 rotate-45 rtl:-rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400">الشحنات
                            المرسلة</span>
                        <div class="flex gap-1 items-baseline mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">
                                {{ $totalSentShipments }}
                            </h4>
                            <span class="text-xs font-medium text-gray-400">شحنة</span>
                        </div>
                    </div>
                </div>

                <div
                    class="flex-1 relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 hover:border-success-200 transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex justify-center items-center w-12 h-12 rounded-xl transition-transform duration-300 bg-success-50 dark:bg-success-500/10 text-success-500 group-hover:scale-110">
                        <svg class="w-6 h-6 rotate-45 rtl:-rotate-45" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400">الشحنات
                            المستقبلة</span>
                        <div class="flex gap-1 items-baseline mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">
                                {{ $totalReceivedShipments }}
                            </h4>
                            <span class="text-xs font-medium text-gray-400">شحنة</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Branch Information Card --}}
            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
                <div class="flex gap-5 items-center">
                    <div class="flex gap-3 items-center">
                        <div
                            class="flex justify-center items-center w-10 h-10 text-sm font-black bg-gray-50 rounded-xl border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                            {{ mb_substr($branch->name, 0, 1) }}
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black tracking-tight leading-tight text-gray-900 dark:text-white">
                            {{ $branch->name }}
                        </h2>
                        <div class="flex flex-wrap gap-y-2 gap-x-4 items-center mt-2">
                            <span
                                class="inline-flex gap-1.5 items-center px-3 py-1 font-bold text-gray-500 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800 text-theme-xs dark:text-gray-300 dark:border-gray-700">
                                <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span dir="ltr">{{ $branch->phone }}</span>
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-2xl  bg-gray-50 dark:bg-gray-800 text-[10px] font-black uppercase text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                                <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $branch->city }} - {{ $branch->address }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-2xl  bg-brand-50 dark:bg-brand-500/10 text-[10px] font-black uppercase text-brand-500 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                {{ $branch->code }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 items-center w-full lg:w-auto">
                    <a href="{{ route('branch.index') }}"
                        class="flex flex-1 gap-2 justify-center items-center px-5 w-full h-11 text-sm font-bold text-gray-500 rounded-2xl border border-gray-200 shadow-sm transition-all lg:flex-none dark:border-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        العودة للفروع
                    </a>
                </div>
            </div>

            {{-- Shipments Table --}}
            <div
                class="overflow-hidden bg-white rounded-2xl border border-gray-100 dark:bg-gray-900 dark:border-gray-800 shadow-theme-xs">

                <div
                    class="flex flex-col gap-4 justify-between items-center px-6 py-5 border-b border-gray-100 dark:border-gray-800 md:flex-row bg-gray-50/50 dark:bg-gray-900/50">
                    <div class="flex gap-3 items-center w-full md:w-auto">
                        <div
                            class="p-2 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                            <svg class="w-5 h-5 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">جدول الشحنات</h3>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                الشحنات بين فرعك وهذا الفرع
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-end items-center w-full md:w-auto">
                        <form method="GET" action="{{ url()->current() }}"
                            class="flex flex-1 gap-2 items-center md:flex-none">
                            <select name="direction" onchange="this.form.submit()"
                                class="pr-8 pl-3 h-10 text-xs font-bold text-gray-500 bg-white rounded-2xl border-gray-200 shadow-sm transition-all cursor-pointer dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-gray-300 hover:border-brand-300">
                                <option value="all">كل الاتجاهات</option>
                                <option value="sent" {{ request('direction') == 'sent' ? 'selected' : '' }}>صادرة (مرسلة)
                                </option>
                                <option value="received" {{ request('direction') == 'received' ? 'selected' : '' }}>واردة
                                    (مستقبلة)</option>
                            </select>
                        </form>

                        <div
                            class="px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                عدد النتائج: <span
                                    class="text-sm font-black text-brand-500 dark:text-brand-400">{{ $shipments->total() }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/80 dark:bg-gray-800/80 dark:border-gray-800">
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">الشحنة /
                                    التاريخ</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">الاتجاه
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">المبلغ
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">المرسل
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">المستلم
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">التفاصيل
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50 dark:divide-gray-800 dark:bg-gray-900">
                            @forelse($shipments as $shipment)
                                @php
                                    $userBranchCode = auth()->user()->branch_code;
                                    $isSent = $shipment->sender_branch_code == $userBranchCode;
                                @endphp

                                <tr
                                    class="transition-colors duration-200 group hover:bg-brand-50/30 dark:hover:bg-gray-800/60">

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col">
                                            <div class="flex gap-2 items-center">
                                                <div
                                                    class="flex justify-center items-center w-8 h-8 text-white rounded-2xl bg-brand-500 dark:bg-gray-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <span
                                                        class="block font-mono text-sm font-black tracking-wide text-gray-800 dark:text-white">
                                                        {{ $shipment->bond_number }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span
                                                class="text-[10px] text-gray-400 mt-1 font-bold mr-10">{{ $shipment->created_at->translatedFormat('d F Y') }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        @if ($isSent)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-2xl text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20">
                                                <svg class="w-3 h-3 rotate-45 rtl:-rotate-45" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                                صادرة (مرسلة)
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-2xl text-[10px] font-bold bg-success-50 text-success-700 border border-success-100 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/20">
                                                <svg class="w-3 h-3 rotate-45 rtl:-rotate-45" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                                واردة (مستقبلة)
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <span
                                            class="px-2 py-1 font-mono text-sm font-black text-gray-900 rounded-2xl bg-brand-50 dark:bg-gray-800 dark:text-white">
                                            {{ number_format($shipment->total_amount, 2) }}
                                            <span class="text-[10px] text-gray-400 font-sans">ر.ي</span>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                {{ $shipment->senderCustomer->name ?? 'غير محدد' }}
                                            </span>
                                            <span class="text-[10px] text-gray-400">
                                                {{ $shipment->senderBranch->name ?? $shipment->sender_branch_code }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                {{ $shipment->receiverCustomer->name ?? 'غير محدد' }}
                                            </span>
                                            <span class="text-[10px] text-gray-400">
                                                {{ $shipment->receiverBranch->name ?? $shipment->receiver_branch_code }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <a href="{{ route('shipments.show', $shipment->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-brand-500 hover:bg-brand-500 text-white text-[10px] font-bold rounded-xl shadow-sm transition-all hover:scale-[1.02] active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-24">
                                        <div class="flex flex-col justify-center items-center text-center">
                                            <div
                                                class="flex justify-center items-center mb-4 w-24 h-24 bg-gray-50 rounded-full border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">لا توجد شحنات</h3>
                                            <p class="mt-2 max-w-xs text-sm text-gray-400">لم يتم العثور على أي شحنات بين
                                                فرعك وهذا الفرع.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($shipments->hasPages())
                    <div class="flex justify-center px-6 py-4 bg-gray-50 border-t border-gray-100 dark:border-gray-800 dark:bg-gray-900"
                        dir="ltr">
                        {{ $shipments->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
