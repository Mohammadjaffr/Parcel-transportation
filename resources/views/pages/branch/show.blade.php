@extends('layouts.app')
@section('title', 'تفاصيل الفرع: ' . $branch->name)

@section('content')
    <div class="min-h-screen bg-[#F8F9FC] dark:bg-gray-950 font-outfit" dir="rtl" x-data="{
        paymentModalOpen: false,
        isSubmitting: false,
        selectedPackage: {
            pivot_id: null,
            tracking_number: '',
            total_amount: 0,
            paid_amount: 0,
            remaining_amount: 0
        },
        openPaymentModal(pivotId, trackingNumber, totalAmount, paidAmount, remainingAmount) {
            this.selectedPackage = {
                pivot_id: pivotId,
                tracking_number: trackingNumber,
                total_amount: totalAmount,
                paid_amount: paidAmount,
                remaining_amount: remainingAmount
            };
            this.paymentModalOpen = true;
        },
        formatNumber(num) {
            return new Intl.NumberFormat('ar-EG').format(num);
        },
        async submitPayment(event) {
            this.isSubmitting = true;
            const form = event.target;
            const formData = new FormData(form);
    
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
    
                const data = await response.json();
    
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'حدث خطأ');
                }
            } catch (error) {
                console.error(error);
                alert('حدث خطأ في الاتصال');
            } finally {
                this.isSubmitting = false;
            }
        }
    }">

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
            

            {{-- Package Statistics Cards --}}
            <div
                class="bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
                <div class="flex gap-3 items-center mb-5">
                    <div
                        class="p-2 rounded-2xl border shadow-sm bg-brand-50 border-brand-100 dark:bg-brand-500/10 dark:border-brand-500/20">
                        <svg class="w-5 h-5 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">معلومات الباصات المرسلة</h3>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            الباصات والشحنات المتجهة لهذا الفرع
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
                    {{-- Package Count --}}
                    <div
                        class="flex flex-col justify-between items-start p-4 rounded-2xl border bg-success-to-br border-brand-100 from-brand-50 to-brand-100/50 dark:from-brand-500/10 dark:to-brand-500/5 dark:border-brand-500/20">
                        <div class="flex gap-2 items-center mb-2">
                            <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="text-xs font-bold text-brand-500 dark:text-brand-400">عدد الباصات</span>
                        </div>
                        <h4 class="text-2xl font-black text-brand-700 dark:text-brand-300">
                            {{ $packageCount }}
                        </h4>
                    </div>

                    {{-- Shipment Count --}}
                    <div
                        class="flex flex-col justify-between items-start p-4 rounded-2xl border bg-success-to-br from-blue-light-50 border-blue-light-100 to-blue-light-100/50 dark:from-blue-light-500/10 dark:to-blue-light-500/5 dark:border-blue-light-500/20">
                        <div class="flex gap-2 items-center mb-2">
                            <svg class="w-5 h-5 text-blue-light-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-xs font-bold text-blue-light-500 dark:text-blue-light-400">عدد الشحنات</span>
                        </div>
                        <h4 class="text-2xl font-black text-blue-light-500 dark:text-blue-light-300">
                            {{ $shipmentCount }}
                        </h4>
                    </div>

                    {{-- Total Amount --}}
                    <div
                        class="flex flex-col justify-between items-start p-4 rounded-2xl border from-success-50 border-success-100 bg-success-to-br to-success-100/50 dark:from-success-500/10 dark:to-success-500/5 dark:border-success-500/20">
                        <div class="flex gap-2 items-center mb-2">
                            <svg class="w-5 h-5 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-bold text-warning-500 dark:text-warning-400">المبلغ الإجمالي</span>
                        </div>
                        <h4 class="text-xl font-black text-warning-700 dark:text-warning-300">
                            {{ number_format($totalAmount, 0) }} <span class="font-sans text-xs">ر.ي</span>
                        </h4>
                    </div>

                    {{-- Payment Status --}}
                    <div
                        class="flex flex-col items-start justify-between p-4 rounded-2xl {{ $isPaid ? 'bg-success-to-br from-success-50 to-success-100/50 border-success-100 dark:from-success-500/10 dark:to-success-500/5 dark:border-success-500/20' : 'bg-success-to-br from-error-50 to-error-100/50 border-error-100 dark:from-error-500/10 dark:to-error-500/5 dark:border-error-500/20' }} border">
                        <div class="flex gap-2 items-center mb-2">
                            <svg class="w-5 h-5 {{ $isPaid ? 'text-success-500' : 'text-error-500' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                @if ($isPaid)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                @endif
                            </svg>
                            <span
                                class="text-xs font-bold {{ $isPaid ? 'text-success-500 dark:text-success-400' : 'text-error-500 dark:text-error-400' }}">حالة
                                الدفع</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <h4
                                class="text-xl font-black {{ $isPaid ? 'text-success-700 dark:text-success-300' : 'text-error-700 dark:text-error-300' }}">
                                {{ $isPaid ? 'مسدد' : 'غير مسدد' }}
                            </h4>
                            @if (!$isPaid && $remainingAmount > 0)
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                    المتبقي: {{ number_format($remainingAmount, 0) }} ر.ي
                                </span>
                            @endif
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
                                class="inline-flex gap-2 items-center px-3 py-1 mx-2 font-bold text-gray-500 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800 text-theme-xs dark:text-gray-300 dark:border-gray-700">
                                <svg class="w-3.5 h-3.5 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span dir="ltr">{{ $branch->phone }}</span>
                            </span>
                            <span
                                class="inline-flex items-center gap-2 mx-2  px-3 py-1 rounded-2xl  bg-gray-50 dark:bg-gray-800 text-[10px] font-black uppercase text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700">
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
                                class="inline-flex items-center gap-2 mx-2  px-3 py-1 rounded-2xl  bg-brand-50 dark:bg-brand-500/10 text-[10px] font-black uppercase text-brand-500 dark:text-brand-400 border border-brand-100 dark:border-brand-500/20">
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

            {{-- Packages Table --}}
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
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">جدول الباصات</h3>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                الباصات المتجهة لهذا الفرع
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 justify-end items-center w-full md:w-auto">
                        <div
                            class="px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                عدد الباصات: <span
                                    class="text-sm font-black text-brand-500 dark:text-brand-400">{{ $packages->total() }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/80 dark:bg-gray-800/80 dark:border-gray-800">
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">رقم
                                    الرحلة</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">السائق
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">عدد
                                    الشحنات</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">المبلغ
                                </th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">حالة
                                    الدفع</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">التفاصيل
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50 dark:divide-gray-800 dark:bg-gray-900">
                            @forelse($packages as $package)
                                <tr
                                    class="transition-colors duration-200 group hover:bg-brand-50/30 dark:hover:bg-gray-800/60">

                                    {{-- Package Number --}}
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex gap-2 items-center">
                                            <div
                                                class="flex justify-center items-center w-8 h-8 text-white rounded-2xl bg-brand-500 dark:bg-gray-800">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <span
                                                class="font-mono text-sm font-black tracking-wide text-gray-800 dark:text-white">
                                                {{ $package->tracking_number }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Driver --}}
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                                {{ $package->driver_name }}
                                            </span>
                                            <span class="text-[10px] text-gray-400" dir="ltr">
                                                {{ $package->driver_phone }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Shipment Count --}}
                                    <td class="px-6 py-4 align-top">
                                        <span
                                            class="inline-flex gap-1.5 items-center px-3 py-1.5 rounded-2xl border text-blue-light-700 bg-blue-light-50 border-blue-light-100 dark:bg-blue-light-500/10 dark:text-blue-light-400 dark:border-blue-light-500/20">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-xs font-bold">{{ $package->branch_shipment_count }}</span>
                                        </span>
                                    </td>

                                    {{-- Amount --}}
                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="px-2 py-1 font-mono text-sm font-black text-gray-900 rounded-2xl bg-brand-50 dark:bg-gray-800 dark:text-white">
                                                {{ number_format($package->branch_total_amount, 0) }}
                                                <span class="text-[10px] text-gray-400 font-sans">ر.ي</span>
                                            </span>
                                            @if ($package->branch_paid_amount > 0)
                                                <span class="text-[10px] text-gray-400">
                                                    مدفوع: {{ number_format($package->branch_paid_amount, 0) }} ر.ي
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Payment Actions --}}
                                    <td class="px-6 py-4 align-top">
                                        @php
                                            $pivotId = DB::table('branch_shipment_package')
                                                ->where('shipment_package_id', $package->id)
                                                ->where('branch_code', $branch->code)
                                                ->value('id');
                                        @endphp

                                        @if ($package->branch_is_paid)
                                            <span
                                                class="inline-flex gap-1.5 items-center px-3 py-1.5 rounded-2xl border bg-success-50 text-success-700 border-success-100 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/20">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-xs font-bold">مسدد</span>
                                            </span>
                                        @else
                                            <button type="button"
                                                @click="openPaymentModal({{ $pivotId }}, '{{ $package->tracking_number }}', {{ $package->branch_total_amount }}, {{ $package->branch_paid_amount }}, {{ $package->branch_remaining_amount }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-brand-500 hover:bg-brand-500 text-white text-xs font-bold shadow-sm transition-all hover:scale-[1.02] active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                إضافة دفعة
                                            </button>
                                        @endif
                                    </td>

                                    {{-- Details Link --}}
                                    <td class="px-6 py-4 align-top">
                 
                                           <a href="{{ route('shipmentpackage.show', $package->id) }}"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400"
                                            title="عرض الشحنات">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
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
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">لا توجد حزم</h3>
                                            <p class="mt-2 max-w-xs text-sm text-gray-400">لم يتم العثور على أي حزم متجهة
                                                لهذا
                                                الفرع.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($packages->hasPages())
                    <div class="flex justify-center px-6 py-4 bg-gray-50 border-t border-gray-100 dark:border-gray-800 dark:bg-gray-900"
                        dir="ltr">
                        {{ $packages->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Modal --}}
        @include('pages.branch.partials.payment-modal')
    </div>
@endsection
