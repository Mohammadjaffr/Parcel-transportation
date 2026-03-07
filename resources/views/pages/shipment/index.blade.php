@extends('layouts.app')
@section('title', 'قائمة الطرود')
@section('Breadcrumb', 'إدارة الطرود')
@section('addButton')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="flex w-full md:justify-end" x-data="{ isLoading: false }" @pageshow.window="isLoading = false">

        <a href="{{ route('shipment.create') }}" @click="isLoading = true"
            :class="isLoading ? 'opacity-75 cursor-not-allowed pointer-events-none' : ''"
            class="flex gap-2 justify-center items-center px-3 w-full h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/20 active:scale-95 md:w-auto">

            {{-- الأيقونة العادية --}}
            <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>

            {{-- أيقونة التحميل --}}
            <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" style="display: none;">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>

            <span x-text="isLoading ? 'جاري التحويل...' : 'تسجيل طرد جديد'"></span>
        </a>
    </div>
@endsection
@section('content')
    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
        isLoading: false,
        search: '',
        filterStatus: 'all',
        showRow(status, bond, sender, receiver) {
            const matchesSearch = bond.includes(this.search) || sender.includes(this.search) || receiver.includes(this.search);
            const matchesStatus = this.filterStatus === 'all' || status === this.filterStatus;
            return matchesSearch && matchesStatus;
        }
    }" @pageshow.window="isLoading = false">

        {{-- Tabs --}}
        {{-- <div class="flex p-1 mb-6 bg-gray-100 rounded-xl dark:bg-gray-800 w-fit">
            <a href="{{ route('shipment.index', ['type' => 'outgoing']) }}"
                class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all {{ request('type', 'outgoing') == 'outgoing' ? 'bg-white dark:bg-gray-700 text-brand-500 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600' : 'text-gray-500 hover:text-gray-700' }}">
                الطرود الصادرة (من فرعنا)
            </a>
            <a href="{{ route('shipment.index', ['type' => 'incoming']) }}"
                class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all {{ request('type') == 'incoming' ? 'bg-white dark:bg-gray-700 text-brand-500 shadow-sm ring-1 ring-gray-200 dark:ring-gray-600' : 'text-gray-500 hover:text-gray-700' }}">
                الطرود الواردة (إلى فرعنا)
            </a>
        </div> --}}

        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6 lg:gap-6">

            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-xl dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        الطرود</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->count() }}</h4>
                </div>

            </div>



            <div @click="filterStatus = 'pending'"
                :class="filterStatus === 'pending' ? 'border-warning-500 ring-2 ring-warning-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">قيد
                        الانتظار</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->where('status', 'pending')->count() }}</h4>
                </div>
            </div>

            <div @click="filterStatus = 'in_transit'"
                :class="filterStatus === 'in_transit' ? 'border-blue-light-500 ring-2 ring-blue-light-500/20' :
                    'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-blue-light-50 dark:bg-blue-light-500/10 text-blue-light-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">في
                        الطريق</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->where('status', 'in_transit')->count() }}
                    </h4>
                </div>
            </div>

            <div @click="filterStatus = 'delivered'"
                :class="filterStatus === 'delivered' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">تم
                        التسليم</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->where('status', 'delivered')->count() }}
                    </h4>
                </div>
            </div>

            <div @click="filterStatus = 'cancelled'"
                :class="filterStatus === 'cancelled' ? 'border-error-500 ring-2 ring-error-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">تم
                        الإلغاء</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->where('status', 'cancelled')->count() }}
                    </h4>
                </div>
            </div>

            <div @click="filterStatus = 'returned'"
                :class="filterStatus === 'returned' ? 'border-gray-500 ring-2 ring-gray-500/20' : 'border-gray-100'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 text-gray-500 bg-gray-50 rounded-xl dark:bg-gray-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">تم
                        الإرجاع</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $requests->where('status', 'returned')->count() }}
                    </h4>
                </div>
            </div>
        </div>


        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">

            {{-- Search Bar --}}
            <div class="grid grid-cols-1 md:grid-cols-2 items-center bg-white dark:bg-white/[0.03] py-4 px-6 rounded-2xl">
                <div class="relative w-full rounded-2xl border ring-2 group border-brand-500 ring-brand-500/20">
                    <input type="text" x-model="search" placeholder="ابحث برقم السند، المرسل أو المستلم..."
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

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                @forelse ($requests as $request)
                    @php
                        $statusConfig = [
                            'pending' => [
                                'bg' => 'bg-warning-100',
                                'text' => 'text-warning-700',
                                'label' => 'قيد الانتظار',
                                'dot' => 'bg-warning-500',
                            ],
                            'in_transit' => [
                                'bg' => 'bg-blue-light-100',
                                'text' => 'text-blue-light-700',
                                'label' => 'في الطريق',
                                'dot' => 'bg-blue-light-500',
                            ],
                            'delivered' => [
                                'bg' => 'bg-success-100',
                                'text' => 'text-success-700',
                                'label' => 'تم التسليم',
                                'dot' => 'bg-success-500',
                            ],
                            'returned' => [
                                'bg' => 'bg-error-100',
                                'text' => 'text-error-700',
                                'label' => 'تم الإرجاع',
                                'dot' => 'bg-error-500',
                            ],
                            'cancelled' => [
                                'bg' => 'bg-error-100',
                                'text' => 'text-error-700',
                                'label' => 'تم الإلغاء',
                                'dot' => 'bg-error-500',
                            ],
                        ];
                        $currentStatus = $statusConfig[$request->status] ?? $statusConfig['cancelled'];

                        $paymentLabel = match ($request->payment_method) {
                            'prepaid' => 'دفع مسبق',
                            'cod' => 'دفع عند الاستلام',
                            'customer_credit' => 'آجل (دين)',
                            'partial_payment' => 'دفع جزئي',
                            default => $request->payment_method,
                        };
                        $paymentColor = match ($request->payment_method) {
                            'prepaid' => 'bg-success-50 text-success-600 border-success-100',
                            'cod' => 'bg-blue-light-50 text-blue-light-600 border-blue-light-100',
                            'customer_credit' => 'bg-error-50 text-error-600 border-error-100',
                            'partial_payment' => 'bg-warning-50 text-warning-600 border-warning-100',
                            default => 'bg-gray-50 text-gray-600 border-gray-100',
                        };
                    @endphp
                    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 transition-opacity bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800"
                        x-show="showRow('{{ $request->status }}', '{{ $request->bond_number }}', '{{ $request->senderCustomer->name ?? '' }}', '{{ $request->receiverCustomer->name ?? '' }}')"
                        x-transition>

                        {{-- Header: Bond Number + Status + Actions --}}
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col gap-2">
                                <span
                                    class="inline-block px-2.5 py-1 font-mono text-xs font-bold text-gray-600 bg-white rounded-md border border-gray-200 shadow-sm w-fit dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                    #{{ $request->bond_number }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 w-fit rounded-full text-[10px] font-medium {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} dark:bg-opacity-10">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus['dot'] }}"></span>
                                    {{ $currentStatus['label'] }}
                                </span>
                            </div>
                            @if ($request->status !== 'cancelled')
                                <div class="flex gap-1 items-center">
                                    <a href="{{ route('shipment.show', $request->id) }}"
                                        class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-brand-500 hover:border-brand-200 dark:bg-gray-900 dark:border-gray-800 dark:hover:text-brand-400 dark:hover:border-brand-800"
                                        title="عرض الشحنة">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('shipment.invoice', $request->id) }}" target="_blank"
                                        class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-brand-500 hover:border-brand-200 dark:bg-gray-900 dark:border-gray-800 dark:hover:text-brand-400 dark:hover:border-brand-800"
                                        title="طباعة الفاتورة">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Sender / Receiver --}}
                        <div class="flex gap-3 items-center mt-2">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full bg-brand-500 dark:text-brand-300">
                                {{ mb_substr($request->receiverCustomer->name ?? ($request->receiver_name ?? '?'), 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ Str::limit($request->receiverCustomer->name ?? ($request->receiver_name ?? '-'), 30) }}
                                </span>
                                <span class="flex gap-1 items-center mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    المرسل: {{ Str::limit($request->senderCustomer->name ?? '-', 20) }}
                                </span>
                            </div>
                        </div>

                        {{-- Route + Payment + Amount --}}
                        <div class="flex flex-col gap-3 pt-3 mt-1 border-t border-gray-100 dark:border-gray-800">
                            {{-- Route --}}
                            <div class="flex flex-col gap-1.5">
                                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">المسار</span>
                                <div class="flex gap-1.5 items-center text-xs">
                                    <div
                                        class="flex gap-1.5 items-center px-2 py-1 text-gray-600 bg-white rounded-md border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                                        <div class="w-1.5 h-1.5 rounded-full bg-brand-500"></div>
                                        {{ $request->senderBranch->name ?? '-' }}
                                    </div>
                                    <svg class="w-3 h-3 text-gray-400 rotate-180 rtl:rotate-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                    <div
                                        class="flex gap-1.5 items-center px-2 py-1 rounded-md border shadow-sm text-brand-700 bg-brand-50 border-brand-100 dark:bg-brand-900/20 dark:border-brand-800 dark:text-brand-300">
                                        <div class="w-1.5 h-1.5 rounded-full bg-brand-500"></div>
                                        {{ $request->receiverBranch->name ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Payment + Amount row --}}
                            <div class="flex justify-between items-end">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">النوع /
                                        الدفع</span>
                                    <div class="flex gap-1.5 items-center">
                                        <span
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $request->package_type }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase border {{ $paymentColor }}">
                                            {{ $paymentLabel }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1 items-end">
                                    <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500">المبلغ</span>
                                    <div class="font-mono text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($request->total_amount, 2) }}
                                        <span class="font-sans text-[10px] font-normal text-gray-400">ر.ي</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                        <div class="flex flex-col justify-center items-center">
                            <div class="p-3 mb-3 bg-white rounded-full shadow-sm dark:bg-gray-800">
                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">لا توجد بيانات</h4>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $type === 'incoming' ? 'لا توجد طرود واردة حالياً..' : 'لا توجد طرود صادرة حالياً..' }}
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">رقم السند</th>
                            <th class="px-6 py-4">الأطراف (مرسل/مستلم)</th>
                            <th class="px-6 py-4 text-center">خط السير</th>
                            <th class="px-6 py-4 text-center">النوع / الدفع</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-left">التكلفة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse ($requests as $request)
                            <tr x-show="showRow('{{ $request->status }}', '{{ $request->bond_number }}', '{{ $request->senderCustomer->name ?? '' }}', '{{ $request->receiverCustomer->name ?? '' }}')"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1.5 text-xs font-black bg-gray-50 rounded-lg border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                                        {{ $request->bond_number }}
                                    </span>
                                </td>

                                <td
                                    class="py-5 px-6 border-y dark:border-gray-800/50 text-center text-[10px] font-black uppercase text-gray-500">
                                    {{ $request->senderCustomer->name ?? '-' }} ⇠
                                    {{ $request->receiverCustomer->name ?? '-' }}
                                </td>
                                <td
                                    class="py-5 px-6 border-y dark:border-gray-800/50 text-center text-[10px] font-black uppercase text-gray-500">
                                    {{ $request->senderBranch->name ?? '-' }} ⇠
                                    {{ $request->receiverBranch->name ?? '-' }}
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    @php
                                        $paymentLabel = match ($request->payment_method) {
                                            'prepaid' => 'دفع مسبق',
                                            'cod' => 'دفع عند الاستلام',
                                            'customer_credit' => 'آجل (دين)',
                                            'partial_payment' => 'دفع جزئي',
                                            default => $request->payment_method,
                                        };
                                        $paymentColor = match ($request->payment_method) {
                                            'prepaid' => 'bg-success-50 text-success-600 border-success-100',
                                            'cod' => 'bg-blue-light-50 text-blue-light-600 border-blue-light-100',
                                            'customer_credit' => 'bg-error-50 text-error-600 border-error-100',
                                            'partial_payment' => 'bg-warning-50 text-warning-600 border-warning-100',
                                            default => 'bg-gray-50 text-gray-600 border-gray-100',
                                        };
                                    @endphp
                                    <div class="flex flex-col gap-1 items-center">
                                        <span
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $request->package_type }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase border {{ $paymentColor }}">
                                            {{ $paymentLabel }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    @php
                                        $colors = [
                                            'pending' => 'bg-warning-500 shadow-warning-500/20',
                                            'in_transit' => 'bg-blue-light-500 shadow-blue-500/20',
                                            'delivered' => 'bg-success-500 shadow-success-500/20',
                                            'cancelled' => 'bg-error-500 shadow-error-500/20',
                                            'returned' => 'bg-gray-500 shadow-gray-500/20',
                                        ];
                                        $labels = [
                                            'pending' => 'قيد الانتظار',
                                            'in_transit' => 'في الطريق',
                                            'delivered' => 'تم التسليم',
                                            'cancelled' => 'تم الإلغاء',
                                            'returned' => 'تم الإرجاع',
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-lg text-[10px] font-black text-white uppercase shadow-lg {{ $colors[$request->status] ?? 'bg-gray-500' }}">
                                        {{ $labels[$request->status] ?? $request->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-left border-y dark:border-gray-800/50">
                                    <span class="text-base font-black text-gray-900 dark:text-white">
                                        {{ number_format($request->total_amount, 2) }}
                                        <small class="text-[10px] font-bold text-gray-400 mr-0.5">ر.ي</small>
                                    </span>
                                </td>

                                <td
                                    class="px-6 py-5 text-center border-l last:rounded-l-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-1 justify-center items-center">
                                        @if ($request->status !== 'cancelled')
                                            <a href="{{ route('shipment.show', $request->id) }} " title="عرض الشحنة"
                                                class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('shipment.invoice', $request->id) }}" target="_blank"
                                                class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path
                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-20 italic text-center text-gray-400">
                                    {{ $type === 'incoming' ? 'لا توجد طرود واردة حالياً..' : 'لا توجد طرود صادرة حالياً..' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
                <div class="p-8 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection
