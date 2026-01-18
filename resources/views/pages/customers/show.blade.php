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
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">إجمالي
                            الشحنات</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">
                                {{ number_format($grandTotalCost, 2) }}</h4>
                            <span class="text-xs font-medium text-gray-400">ر.ي</span>
                        </div>
                    </div>
                </div>

                <div @click="setFilter('paid')"
                    :class="activeFilter === 'paid' ? 'border-success-500 ring-2 ring-success-500/20 shadow-md' :
                        'border-gray-100 hover:border-success-200'"
                    class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V22" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">إجمالي
                            المسدد (لك)</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">
                                {{ number_format($grandTotalPaid, 2) }}</h4>
                            <span class="text-xs font-medium text-success-500">ر.ي</span>
                        </div>
                    </div>
                </div>

                <div @click="setFilter('unpaid')"
                    :class="activeFilter === 'unpaid' ? 'border-error-500 ring-2 ring-error-500/20 shadow-md' :
                        'border-gray-100 hover:border-error-200'"
                    class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">المبلغ
                            المتبقي (عليك)</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">
                                {{ number_format($grandTotalRemaining, 2) }}</h4>
                            <span class="text-xs font-medium text-error-500">ر.ي</span>
                        </div>
                    </div>
                </div>

                <div @click="setFilter('unpaidShipments')"
                    :class="activeFilter === 'unpaidShipments' ? 'border-warning-500 ring-2 ring-warning-500/20 shadow-md' :
                        'border-gray-100 hover:border-warning-200'"
                    class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">شحنات غير
                            مسددة</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <h4 class="text-2xl font-black text-gray-900 dark:text-white">{{ $unpaidShipmentsCount }}</h4>
                            <span class="text-xs font-medium text-gray-400">شحنة</span>
                        </div>
                    </div>
                </div>

            </div>

            @include('pages.customers.edit-customer-modal')

            {{-- Payment Modal --}}
            <div x-show="paymentModalOpen" x-cloak
                class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                {{-- Overlay --}}
                <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                    @click="closePaymentModal()"></div>

                {{-- Modal Content --}}
                <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 dark:border-gray-800"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95">

                    {{-- Header --}}
                    <div
                        class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-brand-500 to-brand-600">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 rounded-xl">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-white">سداد الدين</h3>
                            </div>
                            <button @click="closePaymentModal()" class="p-2 hover:bg-white/20 rounded-xl transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <form :action="`{{ route('shipments.index') }}/${paymentData.shipmentId}/payment`" method="POST"
                        class="p-6 space-y-5">
                        @csrf

                        {{-- Amount Field --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المبلغ
                                المستحق</label>
                            <div class="relative">
                                <input type="number" name="amount" x-model="paymentData.amount" step="0.01"
                                    min="0" :max="paymentData.maxAmount"
                                    class="w-full h-12 pr-4 pl-12 text-lg font-bold bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                                    placeholder="أدخل المبلغ">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">ر.ي</span>
                            </div>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                المبلغ المتبقي: <span class="font-bold text-error-500"
                                    x-text="paymentData.maxAmount.toLocaleString()"></span> ر.ي
                            </p>
                        </div>

                        {{-- Payment Type --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">طريقة
                                الدفع</label>
                            <div class="grid grid-cols-2 gap-3">
                                {{-- Cash Option --}}
                                <label
                                    :class="paymentData.paymentType === 'cash' ?
                                        'border-brand-500 bg-brand-50 dark:bg-brand-500/10 ring-2 ring-brand-500/20' :
                                        'border-gray-200 dark:border-gray-700 hover:border-brand-300'"
                                    class="relative flex flex-col items-center gap-2 p-4 border rounded-xl cursor-pointer transition-all">
                                    <input type="radio" name="payment_method" value="cash"
                                        x-model="paymentData.paymentType" class="sr-only">
                                    <div :class="paymentData.paymentType === 'cash' ? 'bg-brand-500 text-white' :
                                        'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                                        class="p-2.5 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <span
                                        :class="paymentData.paymentType === 'cash' ? 'text-brand-600 dark:text-brand-400' :
                                            'text-gray-600 dark:text-gray-400'"
                                        class="text-sm font-bold transition-colors">كاش</span>
                                </label>

                                {{-- Bank Transfer Option --}}
                                <label
                                    :class="paymentData.paymentType === 'bank_transfer' ?
                                        'border-brand-500 bg-brand-50 dark:bg-brand-500/10 ring-2 ring-brand-500/20' :
                                        'border-gray-200 dark:border-gray-700 hover:border-brand-300'"
                                    class="relative flex flex-col items-center gap-2 p-4 border rounded-xl cursor-pointer transition-all">
                                    <input type="radio" name="payment_method" value="bank_transfer"
                                        x-model="paymentData.paymentType" class="sr-only">
                                    <div :class="paymentData.paymentType === 'bank_transfer' ? 'bg-brand-500 text-white' :
                                        'bg-gray-100 dark:bg-gray-800 text-gray-500'"
                                        class="p-2.5 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </div>
                                    <span
                                        :class="paymentData.paymentType === 'bank_transfer' ?
                                            'text-brand-600 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400'"
                                        class="text-sm font-bold transition-colors">تحويل بنكي</span>
                                </label>
                            </div>
                        </div>

                        {{-- Reference Number (for bank transfer) --}}
                        <div x-show="paymentData.paymentType === 'bank_transfer'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">رقم الإيداع /
                                المرجع</label>
                            <div class="relative">
                                <input type="text" name="reference_number" x-model="paymentData.referenceNumber"
                                    class="w-full h-12 pr-12 pl-4 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all"
                                    placeholder="أدخل رقم الإيداع أو المرجع">
                                <div class="absolute px-2 right-4 top-1/2 -translate-y-1/2">
                                    {{-- <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg> --}}
                                </div>
                            </div>
                        </div>

                        {{-- Notes Field --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات
                                (اختياري)</label>
                            <textarea name="notes" x-model="paymentData.notes" rows="2"
                                class="w-full px-4 py-3 text-sm font-medium bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-900 dark:text-white transition-all resize-none"
                                placeholder="أضف ملاحظات إن وجدت..."></textarea>
                        </div>

                        {{-- Hidden Fields --}}
                        <input type="hidden" name="shipment_id" :value="paymentData.shipmentId">
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                        {{-- Actions --}}
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="closePaymentModal()"
                                class="flex-1 h-12 px-4 flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                إلغاء
                            </button>
                            <button type="submit" :disabled="!paymentData.amount || paymentData.amount <= 0"
                                class="flex-1 h-12 px-4 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl shadow-lg shadow-brand-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                تأكيد الدفع
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
                <div class="flex items-center gap-5">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 text-3xl font-black transform rotate-3">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight tracking-tight">
                            {{ $customer->name }}</h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-2xl  bg-gray-50 dark:bg-gray-800 text-theme-xs font-bold text-gray-500 dark:text-gray-300 border border-gray-100 dark:border-gray-700">
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

                <div class="flex items-center gap-3 w-full lg:w-auto">
                    {{-- <a href="{{ route('customers.index') }}"
                        class="flex-1 lg:flex-none h-11 px-5 flex w-full items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-300 font-bold rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        العودة للعملاء
                    </a> --}}
                    <button @click="openEditModal({{ $customer->id }})" :disabled="isFetching == {{ $customer->id }}"
                        class="flex-1 lg:flex-none h-11 px-6 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40 text-sm disabled:opacity-75 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        تعديل البيانات
                    </button>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-xs overflow-hidden">

                <div
                    class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div
                            class="p-2 bg-white dark:bg-gray-800 rounded-2xl  shadow-sm border border-gray-100 dark:border-gray-700">
                            <svg class="w-5 h-5 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">سجل الشحنات المالي</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                <span
                                    x-text="activeFilter === 'all' ? 'عرض جميع الشحنات' : (activeFilter === 'paid' ? 'عرض الشحنات المسددة فقط' : activeFilter === 'unpaid' ? 'عرض الشحنات الغير مسددة' : activeFilter === 'unpaidShipments' ? 'عرض الشحنات الغير مسددة')"></span>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
                        <form method="GET" action="{{ url()->current() }}"
                            class="flex items-center gap-2 flex-1 md:flex-none">
                            <select name="direction" onchange="this.form.submit()"
                                class="h-10 pr-8 pl-3 text-xs font-bold bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-500 dark:text-gray-300 shadow-sm cursor-pointer transition-all hover:border-brand-300">
                                <option value="all">كل الاتجاهات</option>
                                <option value="sent" {{ request('direction') == 'sent' ? 'selected' : '' }}>صادرة (مرسل)
                                </option>
                                <option value="received" {{ request('direction') == 'received' ? 'selected' : '' }}>واردة
                                    (مستلم)</option>
                            </select>
                            <select name="payment_method" onchange="this.form.submit()"
                                class="h-10 pr-8 pl-3 text-xs font-bold bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-2xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 text-gray-500 dark:text-gray-300 shadow-sm cursor-pointer transition-all hover:border-brand-300">
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
                            class="px-4 py-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">
                                عدد النتائج: <span
                                    class="text-brand-500 dark:text-brand-400 text-sm font-black">{{ $shipments->total() }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-800">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800 bg-white dark:bg-gray-900">
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
                                    class="group hover:bg-brand-50/30 dark:hover:bg-gray-800/60 transition-colors duration-200">

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-8 h-8 rounded-2xl  bg-brand-500 dark:bg-gray-800 flex items-center justify-center text-white">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <span
                                                        class="block text-sm font-black text-gray-800 dark:text-white font-mono tracking-wide">
                                                        {{ $shipment->tracking_number ?? $shipment->bond_number }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span
                                                class="text-[10px] text-gray-400 mt-1 font-bold mr-10">{{ $shipment->created_at->format('Y-m-d h:i A') }}</span>
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
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-300">
                                                {{ $paymentLabel }}
                                            </span>
                                            @if (!$isUnpaid)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-2xl text-[10px] font-bold bg-success-50 text-success-500 border border-success-100">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    خالص
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
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex flex-col gap-2">
                                            <span
                                                class="text-sm font-black bg-brand-50 dark:bg-gray-800 px-2 py-1 rounded-2xl font-mono text-gray-900 dark:text-white">
                                                {{ number_format($shipment->total_amount, 2) }}
                                                <span class="text-[10px] text-gray-400 font-sans">ر.ي</span>
                                            </span>

                                            @if ($isUnpaid && $isSender && $shipment->payment_method == 'customer_credit')
                                                <div class="flex flex-col gap-1.5">
                                                    <span
                                                        class="text-[10px] font-bold text-error-500 bg-error-50 px-2 py-1 rounded-2xl border border-error-100 dark:border-error-500/20">
                                                        متبقي: {{ number_format($remainingAmount, 2) }}
                                                    </span>
                                                    <button
                                                        @click="openPaymentModal({{ $shipment->id }}, {{ $remainingAmount }})"
                                                        class="w-full px-3 py-1.5 bg-brand-500 hover:bg-brand-500 text-white text-[11px] font-bold rounded-2xl shadow-md shadow-brand-500/20 transition-all flex items-center justify-center gap-1.5 hover:scale-[1.02] active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                        سداد الدين
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 align-top">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="flex items-center bg-brand-50 dark:bg-gray-800 rounded-2xl  px-3 py-2 border border-gray-100 dark:border-gray-700">
                                                <span
                                                    class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $shipment->senderBranch->name ?? $shipment->sender_branch_code }}</span>
                                                <svg class="w-3 h-3 text-gray-400 mx-2 rotate-180 rtl:rotate-0"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                </svg> <span
                                                    class="text-[10px] font-bold text-gray-700 dark:text-gray-300">{{ $shipment->receiverBranch->name ?? $shipment->receiver_branch_code }}</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-24">
                                        <div class="flex flex-col items-center justify-center text-center">
                                            <div
                                                class="w-24 h-24 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 border border-gray-100 dark:border-gray-700">
                                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-gray-900 dark:text-white font-bold text-lg">لا توجد شحنات</h3>
                                            <p class="text-sm text-gray-400 mt-2 max-w-xs">لم يتم العثور على أي عمليات شحن
                                                مرتبطة بهذا العميل حالياً.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($shipments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 flex justify-center"
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
