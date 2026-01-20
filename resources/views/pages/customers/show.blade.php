@extends('layouts.app')
@section('title', 'كشف حساب: ' . $customer->name)

@section('addButton')
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection

@section('content')
    <div class="min-h-screen bg-[#F8F9FC] dark:bg-gray-950 font-outfit" dir="rtl" x-data="pageController()">

        <div class="max-w-[1400px] mx-auto p-4 md:p-6 lg:p-8 space-y-6">

            <div class="flex gap-6">

                <div @click="setFilter('all')"
                    :class="activeFilter === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20 shadow-md' :
                        'border-gray-100 hover:border-brand-200'"
                    class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex justify-center items-center w-12 h-12 rounded-xl transition-transform duration-300 bg-brand-50 dark:bg-brand-500/10 text-brand-500 group-hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400">إجمالي
                            الشحنات</span>
                        <div class="flex gap-1 items-baseline mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">
                                {{ number_format($grandTotalCost, 2) }}
                            </h4>
                            <span class="text-xs font-medium text-gray-400">ر.ي</span>
                        </div>
                    </div>
                </div>

                <div @click="setFilter('paid')"
                    :class="activeFilter === 'paid' ? 'border-success-500 ring-2 ring-success-500/20 shadow-md' :
                        'border-gray-100 hover:border-success-200'"
                    class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex justify-center items-center w-12 h-12 rounded-xl transition-transform duration-300 bg-success-50 dark:bg-success-500/10 text-success-500 group-hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V22" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400">إجمالي
                            المسدد (لك)</span>
                        <div class="flex gap-1 items-baseline mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">
                                {{ number_format($grandTotalPaid, 2) }}
                            </h4>
                            <span class="text-xs font-medium text-success-500">ر.ي</span>
                        </div>
                    </div>
                </div>

                <div @click="setFilter('unpaid')"
                    :class="activeFilter === 'unpaid' ? 'border-error-500 ring-2 ring-error-500/20 shadow-md' :
                        'border-gray-100 hover:border-error-200'"
                    class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex justify-center items-center w-12 h-12 rounded-xl transition-transform duration-300 bg-error-50 dark:bg-error-500/10 text-error-500 group-hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400">المبلغ
                            المتبقي (عليك)</span>
                        <div class="flex gap-1 items-baseline mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">
                                {{ number_format($grandTotalRemaining, 2) }}
                            </h4>
                            <span class="text-xs font-medium text-error-500">ر.ي</span>
                        </div>
                    </div>
                </div>

                <div @click="setFilter('unpaidShipments')"
                    :class="activeFilter === 'unpaidShipments' ? 'border-warning-500 ring-2 ring-warning-500/20 shadow-md' :
                        'border-gray-100 hover:border-warning-200'"
                    class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex justify-center items-center w-12 h-12 rounded-xl transition-transform duration-300 bg-warning-50 dark:bg-warning-500/10 text-warning-500 group-hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400">شحنات غير
                            مسددة</span>
                        <div class="flex gap-1 items-baseline mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">{{ $unpaidShipmentsCount }}</h4>
                            <span class="text-xs font-medium text-gray-400">شحنة</span>
                        </div>
                    </div>
                </div>

            </div>

            @include('pages.customers.edit-customer-modal')

            {{-- Payment Modals --}}
            <x-modals.payment-modal />
            <x-modals.payments-list-modal />

            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
                <div class="flex gap-5 items-center">
                    <div class="flex gap-3 items-center">
                        <div
                            class="flex justify-center items-center w-10 h-10 text-sm font-black bg-gray-50 rounded-xl border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                            {{ mb_substr($customer->name, 0, 1) }}
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black tracking-tight leading-tight text-gray-900 dark:text-white">
                            {{ $customer->name }}
                        </h2>
                        <div class="flex flex-wrap gap-y-2 gap-x-4 items-center mt-2">
                            <span
                                class="inline-flex gap-1.5 items-center px-3 py-1 font-bold text-gray-500 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800 text-theme-xs dark:text-gray-300 dark:border-gray-700">
                                <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span dir="ltr">{{ $customer->phone }}</span>
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-2xl  bg-gray-50 dark:bg-gray-800 text-[10px] font-black uppercase text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
                                <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                فرع: {{ $customer->name }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 items-center w-full lg:w-auto">
                    {{-- <a href="{{ route('customers.index') }}"
                        class="flex flex-1 gap-2 justify-center items-center px-5 w-full h-11 text-sm font-bold text-gray-500 rounded-2xl border border-gray-200 shadow-sm transition-all lg:flex-none dark:border-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        العودة للعملاء
                    </a> --}}
                    <button @click="openEditModal({{ $customer->id }})" :disabled="isFetching == {{ $customer->id }}"
                        class="flex flex-1 gap-2 justify-center items-center px-6 h-11 text-sm font-bold text-white rounded-xl shadow-lg transition-all lg:flex-none bg-brand-500 hover:bg-brand-500 shadow-brand-500/20 hover:shadow-brand-500/40 disabled:opacity-75 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        تعديل البيانات
                    </button>
                </div>
            </div>

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
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">سجل الشحنات المالي</h3>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                <span
                                    x-text="activeFilter === 'all' ? 'عرض جميع الشحنات' : (activeFilter === 'paid' ? 'عرض الشحنات المسددة فقط' : activeFilter === 'unpaid' ? 'عرض الشحنات الغير مسددة' : activeFilter === 'unpaidShipments' ? 'عرض الشحنات الغير مسددة')"></span>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-end items-center w-full md:w-auto">
                        <form method="GET" action="{{ url()->current() }}"
                            class="flex flex-1 gap-2 items-center md:flex-none">
                            <select name="direction" onchange="this.form.submit()"
                                class="pr-8 pl-3 h-10 text-xs font-bold text-gray-500 bg-white rounded-2xl border-gray-200 shadow-sm transition-all cursor-pointer dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-gray-300 hover:border-brand-300">
                                <option value="all">كل الاتجاهات</option>
                                <option value="sent" {{ request('direction') == 'sent' ? 'selected' : '' }}>صادرة (مرسل)
                                </option>
                                <option value="received" {{ request('direction') == 'received' ? 'selected' : '' }}>واردة
                                    (مستلم)</option>
                            </select>
                            <select name="payment_method" onchange="this.form.submit()"
                                class="pr-8 pl-3 h-10 text-xs font-bold text-gray-500 bg-white rounded-2xl border-gray-200 shadow-sm transition-all cursor-pointer dark:bg-gray-800 dark:border-gray-700 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:text-gray-300 hover:border-brand-300">
                                <option value="all">كل طرق الدفع</option>
                                <option value="prepaid" {{ request('payment_method') == 'prepaid' ? 'selected' : '' }}>دفع
                                    مسبق</option>
                                <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>دفع عند
                                    الاستلام</option>
                                <option value="customer_credit"
                                    {{ request('payment_method') == 'customer_credit' ? 'selected' : '' }}>آجل (دين)
                                </option>
                                <option value="partial_payment"
                                    {{ request('payment_method') == 'partial_payment' ? 'selected' : '' }}>دفع جزئي
                                </option>
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
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">الحالة
                                    المالية</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">المبلغ
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">المسار
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">التفاصيل
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50 dark:divide-gray-800 dark:bg-gray-900">
                            @forelse($shipments as $shipment)
                                @php
                                    $isSender = $shipment->sender_customer_id == $customer->id;
                                    $paidAmount = $shipment->payments->sum('amount');
                                    $remainingAmount = $shipment->total_amount - $paidAmount;
                                    $isUnpaid = $remainingAmount > 0;
                                    $status = $isUnpaid ? 'unpaid' : 'paid';

                                    $paymentLabel = match ($shipment->payment_method) {
                                        'prepaid' => 'دفع مسبق',
                                        'cod' => 'دفع عند الاستلام',
                                        'customer_credit' => 'آجل (دين)',
                                        'partial_payment' => 'دفع جزئي',
                                        default => $shipment->payment_method,
                                    };
                                @endphp

                                <tr x-show="isVisible('{{ $status }}')"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
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
                                                        {{ $shipment->tracking_number ?? $shipment->bond_number }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span
                                                class="text-[10px] text-gray-400 mt-1 font-bold mr-10">{{ $shipment->created_at->translatedFormat('d F Y l') }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        @if ($isSender)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-2xl text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20">
                                                <svg class="w-3 h-3 rotate-45 rtl:-rotate-45" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                                صادرة (مرسل)
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-2xl text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20">
                                                <svg class="w-3 h-3 rotate-45 rtl:-rotate-45" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                                واردة (مستلم)
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col gap-1 items-start">
                                            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-300">
                                                {{ $paymentLabel }}
                                            </span>
                                            @if ($shipment->payment_method == 'customer_credit')
                                                @if (!$isUnpaid)
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-2xl text-[10px] font-bold bg-success-50 text-success-500 border border-success-100">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        مسدد
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-2xl text-[10px] font-bold bg-error-50 text-error-500 border border-error-100">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        غير مسدد
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col gap-2">
                                            <span
                                                class="px-2 py-1 font-mono text-sm font-black text-gray-900 rounded-2xl bg-brand-50 dark:bg-gray-800 dark:text-white">
                                                {{ number_format($shipment->total_amount, 2) }}
                                                <span class="text-[10px] text-gray-400 font-sans">ر.ي</span>
                                            </span>

                                            @if ($isSender && $shipment->payment_method == 'customer_credit')
                                                <div class="flex flex-col gap-1.5">
                                                    @if ($paidAmount > 0)
                                                        <span
                                                            class="text-[10px] font-bold text-success-500 bg-success-50 px-2 py-1 rounded-xl border border-success-100 dark:border-success-500/20">
                                                            مدفوع: {{ number_format($paidAmount, 2) }} ر.ي
                                                        </span>
                                                    @endif
                                                    @if ($isUnpaid)
                                                        <span
                                                            class="text-[10px] font-bold text-error-500 bg-error-50 px-2 py-1 rounded-xl border border-error-100 dark:border-error-500/20">
                                                            متبقي: {{ number_format($remainingAmount, 2) }} ر.ي
                                                        </span>
                                                    @endif
                                                    <div
                                                        class="grid {{ $isUnpaid ? 'grid-cols-2' : 'grid-cols-1' }} gap-1.5">
                                                        @if ($isUnpaid)
                                                            <button
                                                                @click="openPaymentModal({{ $shipment->id }}, {{ $remainingAmount }})"
                                                                class="px-2 py-1.5 bg-brand-500 hover:bg-brand-600 text-white text-[10px] font-bold rounded-xl shadow-md shadow-brand-500/20 transition-all flex items-center justify-center gap-1 hover:scale-[1.02] active:scale-95">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                </svg>
                                                                سداد
                                                            </button>
                                                        @endif
                                                        <button
                                                            @click="openPaymentsListModal({{ $shipment->id }}, {{ json_encode($shipment->payments) }})"
                                                            class="px-2 py-1.5 bg-brand-500 hover:bg-brand-600 text-white text-[10px] font-bold rounded-xl shadow-md shadow-brand-500/20 transition-all flex items-center justify-center gap-1 hover:scale-[1.02] active:scale-95">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            الدفعات
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif ($isSender && $shipment->payment_method == 'partial_payment' && $paidAmount > 0)
                                                <span
                                                    class="text-[10px] font-bold text-success-500 bg-success-50 px-2 py-1 rounded-xl border border-success-100 dark:border-success-500/20">
                                                    مدفوع: {{ number_format($paidAmount, 2) }} ر.ي
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex gap-2 items-center">
                                            <div
                                                class="flex items-center px-3 py-2 rounded-2xl border border-gray-100 bg-brand-50 dark:bg-gray-800 dark:border-gray-700">
                                                <span
                                                    class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $shipment->senderBranch->name ?? $shipment->sender_branch_code }}</span>
                                                <svg class="mx-2 w-3 h-3 text-gray-400 rotate-180 rtl:rotate-0"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg> <span
                                                    class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $shipment->receiverBranch->name ?? $shipment->receiver_branch_code }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <a href="{{ route('shipments.show', $shipment->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-brand-500 hover:bg-brand-600 text-white text-[10px] font-bold rounded-xl shadow-sm transition-all hover:scale-[1.02] active:scale-95">
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
                                            <p class="mt-2 max-w-xs text-sm text-gray-400">لم يتم العثور على أي عمليات شحن
                                                مرتبطة بهذا العميل حالياً.</p>
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

@section('script')
    <script>
        function pageController() {
            return {
                activeFilter: 'all', // all, paid, unpaid

                // متغيرات تعديل العميل
                editModalOpen: false,
                isFetching: null,
                countries: [{
                    name: 'Yemen',
                    code: 'YE',
                    dial_code: '967'
                }],
                editCustomer: {
                    id: null,
                    name: '',
                    phone: '',
                    whatsapp_number: '',
                    phone_local: '',
                    phone_country: null,
                    whatsapp_local: '',
                    whatsapp_country: null
                },

                // متغيرات موديل الدفع
                paymentModalOpen: false,
                paymentData: {
                    shipmentId: null,
                    amount: 0,
                    maxAmount: 0,
                    paymentType: 'cash',
                    referenceNumber: '',
                    notes: ''
                },

                // متغيرات موديل عرض الدفعات
                paymentsListModalOpen: false,
                paymentsList: [],

                // فتح موديل الدفع
                openPaymentModal(shipmentId, remainingAmount) {
                    this.paymentData = {
                        shipmentId: shipmentId,
                        amount: remainingAmount,
                        maxAmount: remainingAmount,
                        paymentType: 'cash',
                        referenceNumber: '',
                        notes: ''
                    };
                    this.paymentModalOpen = true;
                },

                // إغلاق موديل الدفع
                closePaymentModal() {
                    this.paymentModalOpen = false;
                    this.paymentData = {
                        shipmentId: null,
                        amount: 0,
                        maxAmount: 0,
                        paymentType: 'cash',
                        referenceNumber: '',
                        notes: ''
                    };
                },

                // فتح موديل عرض الدفعات
                openPaymentsListModal(shipmentId, payments) {
                    this.paymentsList = payments || [];
                    this.paymentsListModalOpen = true;
                },

                // إغلاق موديل عرض الدفعات
                closePaymentsListModal() {
                    this.paymentsListModalOpen = false;
                    this.paymentsList = [];
                },

                // دالة تغيير الفلتر
                setFilter(status) {
                    this.activeFilter = status;
                },

                // دالة التحقق من ظهور الصف في الجدول
                isVisible(rowStatus) {
                    if (this.activeFilter === 'all') return true;
                    // عند اختيار فلتر "شحنات غير مسددة" نعرض الشحنات غير المسددة
                    if (this.activeFilter === 'unpaidShipments') {
                        return rowStatus === 'unpaid';
                    }
                    return this.activeFilter === rowStatus;
                },

                init() {
                    this.editCustomer.phone_country = this.countries[0];
                    this.editCustomer.whatsapp_country = this.countries[0];
                },

                parsePhoneNumber(fullNumber) {
                    if (!fullNumber) return {
                        country: this.countries[0],
                        local: ''
                    };
                    for (let country of this.countries) {
                        if (fullNumber.startsWith(country.dial_code)) {
                            return {
                                country: country,
                                local: fullNumber.substring(country.dial_code.length)
                            };
                        }
                    }
                    return {
                        country: this.countries[0],
                        local: fullNumber
                    };
                },

                async openEditModal(customerId) {
                    this.isFetching = customerId;
                    try {
                        const response = await fetch(`/customers/${customerId}/edit`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();
                        const parsedPhone = this.parsePhoneNumber(data.phone);
                        const parsedWhatsapp = this.parsePhoneNumber(data.whatsapp_number);

                        this.editCustomer = {
                            ...data,
                            phone_local: parsedPhone.local,
                            phone_country: parsedPhone.country,
                            whatsapp_local: parsedWhatsapp.local,
                            whatsapp_country: parsedWhatsapp.country
                        };
                        this.editModalOpen = true;
                    } catch (error) {
                        console.error("Error:", error);
                        alert("حدث خطأ أثناء جلب البيانات");
                    } finally {
                        this.isFetching = null;
                    }
                }
            }
        }
    </script>
@endsection
