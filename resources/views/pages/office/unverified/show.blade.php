@extends('layouts.app')

@section('title', 'تفاصيل المكتب: ' . $office->name)

@section('content')

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl" x-data="officeShowRegistry()">

        {{-- ====================== Header ====================== --}}
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex gap-4 justify-between items-start">
                <div class="flex gap-4 items-center min-w-0 text-right">
                    <a href="{{ route('offices.unverified.index') }}"
                        class="inline-flex justify-center items-center w-11 h-11 text-gray-400 bg-white rounded-2xl border border-gray-100 shadow-sm transition-all hover:bg-gray-100 hover:text-primary hover:border-primary/20 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2 active:scale-95 shrink-0">
                        <span class="material-symbols-outlined text-[21px]">arrow_forward</span>
                    </a>

                    <div class="min-w-0">
                        <h1 class="text-2xl font-black truncate md:text-3xl text-on-surface dark:text-white">
                            {{ $office->name }}
                        </h1>
                        <p class="mt-1 text-sm font-bold text-gray-500 dark:text-bodydark">
                            مكتب خارجي غير موثوق · تفاصيل الفروع وسجل الطرود
                        </p>
                    </div>
                </div>

                <a href="{{ route('offices.edit', $office->id) }}"
                    class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-[20px]">edit_square</span>
                    <span class="hidden sm:inline">تعديل المكتب</span>
                </a>
            </div>
        </div>

        @php
            $branchesCount = $office->branches->count();
            $pageShipments = $shipments->getCollection();

            $pendingCount = $pageShipments->where('status', 'pending')->count();
            $inTransitCount = $pageShipments->where('status', 'in_transit')->count();
            $deliveredCount = $pageShipments->where('status', 'delivered')->count();
            $returnedCount = $pageShipments->where('status', 'returned')->count();

            $totalAmount = $pageShipments->sum('total_amount');
            $cities = $office->branches->pluck('city')->unique()->filter()->join('، ');
        @endphp

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-2 mx-auto max-w-7xl xl:grid-cols-4 md:gap-6">

            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-gray-100 shadow-sm transition-all cursor-default dark:bg-boxdark hover:shadow-md hover:border-primary/50 dark:border-boxdark-2">
                <div
                    class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        إجمالي الطرود
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $shipments->total() ?? 0 }}
                    </h4>
                </div>
            </div>

            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all cursor-default dark:bg-boxdark hover:shadow-md border-r-indigo-500 dark:border-r-indigo-500 hover:border-indigo-300 dark:border-boxdark-2">
                <div
                    class="flex justify-center items-center w-12 h-12 text-indigo-500 bg-indigo-50 rounded-xl dark:bg-indigo-500/10">
                    <span class="material-symbols-outlined text-[24px]">account_tree</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-indigo-500 uppercase">
                        الفروع المسجلة
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $branchesCount }}
                    </h4>
                </div>
            </div>

            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all cursor-default dark:bg-boxdark hover:shadow-md border-r-amber-500 dark:border-r-amber-500 hover:border-amber-300 dark:border-boxdark-2">
                <div
                    class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10">
                    <span class="material-symbols-outlined text-[24px]">hourglass_top</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-amber-500 uppercase">
                        قيد التجهيز
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $pendingCount }}
                    </h4>
                </div>
            </div>

            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all cursor-default dark:bg-boxdark hover:shadow-md border-r-emerald-500 dark:border-r-emerald-500 hover:border-emerald-300 dark:border-boxdark-2">
                <div
                    class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">
                        إجمالي مبالغ الصفحة
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($totalAmount, 0) }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- ====================== Branches Section ====================== --}}
        <div
            class="mx-auto max-w-7xl bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden">

            <div class="flex justify-between items-center p-5 border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div>
                    <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                        <span
                            class="flex justify-center items-center w-9 h-9 rounded-lg bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[20px]">account_tree</span>
                        </span>
                        الفروع التابعة للمكتب
                    </h3>

                    <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                        {{ $cities ?: 'لا توجد مدن محددة' }}
                    </p>
                </div>

                <span
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-black text-[11px]">
                    <span class="material-symbols-outlined text-[14px]">account_tree</span>
                    {{ $branchesCount }} فرع
                </span>
            </div>

            <div class="grid grid-cols-1 gap-3 p-5 md:grid-cols-2 xl:grid-cols-3">
                @forelse($office->branches as $branch)
                    <div
                        class="flex gap-3 justify-between items-start p-4 rounded-2xl border border-gray-100 transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm">

                        <div class="flex gap-3 min-w-0">
                            <div
                                class="flex justify-center items-center w-11 h-11 rounded-xl text-primary bg-primary-container dark:bg-primary/10 shrink-0">
                                <span class="material-symbols-outlined text-[22px]">location_city</span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-black truncate text-on-surface dark:text-white">
                                    {{ $branch->name }}
                                </p>

                                <p class="mt-1 text-[11px] font-bold text-gray-500 dark:text-bodydark truncate">
                                    {{ $branch->city ?: 'مدينة غير محددة' }}
                                </p>

                                @if ($branch->address)
                                    <p
                                        class="flex items-center gap-1 mt-1 text-[10px] font-bold text-gray-400 dark:text-gray-500 truncate">
                                        <span class="material-symbols-outlined text-[12px]">location_on</span>
                                        {{ $branch->address }}
                                    </p>
                                @endif

                                @if ($branch->phone)
                                    <p class="flex items-center gap-1 mt-1 text-[10px] font-bold text-gray-400 dark:text-gray-500"
                                        dir="ltr">
                                        {{-- <span class="material-symbols-outlined text-[12px]">call</span> --}}
                                        <x-phone-number :value="$branch->phone" />
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if ($branch->phone)
                            <a href="tel:{{ $branch->phone }}"
                                class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl transition-all dark:bg-emerald-500/10 dark:text-emerald-400 active:scale-95 shrink-0">
                                <span class="material-symbols-outlined text-[18px]">call</span>
                            </a>
                            {{-- <a href="{{ $branch->DriverDetection }}"
                                class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                <span class="material-symbols-outlined text-[18px]">send</span>
                                إرسال للفرع
                            </a> --}}
                        @endif
                    </div>
                @empty
                    <div
                        class="col-span-full py-12 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <span class="text-[42px] material-symbols-outlined text-gray-300 dark:text-gray-600">
                            account_tree
                        </span>
                        <p class="mt-2 text-sm font-bold text-gray-500 dark:text-bodydark">
                            لا توجد فروع مسجلة لهذا المكتب
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ====================== Shipments Table Section ====================== --}}
        <div
            class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div
                    class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-96 dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">

                    <input type="text" x-model="searchQuery" @input.debounce.300ms="updateVisibility()"
                        placeholder="ابحث برقم السند، اسم المستلم، الفرع أو رقم الهاتف..."
                        class="pr-12 pl-12 w-full h-12 text-sm font-medium placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                    <div
                        class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>

                    <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; updateVisibility()"
                        x-cloak
                        class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-gray-100 rounded-xl transition-all -translate-y-1/2 dark:bg-boxdark hover:text-error active:scale-95">
                        <span class="text-[18px] material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            {{-- ====================== Mobile View ====================== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse($shipments as $shipment)
                    @php
                        $packageTypeLabel = match ($shipment->package_type) {
                            'carton' => 'كرتون',
                            'bag' => 'كيس',
                            'envelope' => 'مغلف',
                            default => 'أخرى',
                        };

                        $paymentLabel = match ($shipment->payment_method) {
                            'prepaid' => 'مدفوع مقدماً',
                            'cod' => 'الدفع عند الاستلام',
                            'partial_payment' => 'دفع جزئي',
                            'customer_credit' => 'آجل',
                            default => $shipment->payment_method,
                        };

                        $remainingAmount =
                            (float) ($shipment->total_amount ?? 0) - (float) ($shipment->partial_amount ?? 0);

                        $destination = $shipment->receiverOfficeBranch?->name ?? '---';

                        $searchText =
                            $shipment->id .
                            ' ' .
                            ($shipment->receiverCustomer?->name ?? '') .
                            ' ' .
                            ($shipment->receiverCustomer?->phone ?? '') .
                            ' ' .
                            $destination .
                            ' ' .
                            $packageTypeLabel;
                    @endphp

                    <div class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all shipment-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm"
                        x-show="showRow(@js($searchText))">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center min-w-0">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-white rounded-xl shadow-inner bg-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">package_2</span>
                                </div>

                                <div class="flex flex-col gap-1 min-w-0">
                                    <span class="text-sm font-black truncate text-on-surface dark:text-white font-headline">
                                        {{ $shipment->id }}
                                    </span>

                                    <div
                                        class="flex gap-1.5 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                                        <span>{{ optional($shipment->created_at)->format('Y/m/d - H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            @if (View::exists('components.shipment-status'))
                                <x-shipment-status :status="$shipment->status" />
                            @else
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
                                        'in_transit' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
                                        'delivered' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
                                        'returned' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
                                        'cancelled' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
                                    ];

                                    $statusLabels = [
                                        'pending' => 'قيد الانتظار',
                                        'in_transit' => 'في الطريق',
                                        'delivered' => 'تم التسليم',
                                        'returned' => 'مرتجعة',
                                        'cancelled' => 'ملغية',
                                    ];
                                @endphp

                                <span
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-black {{ $statusClasses[$shipment->status] ?? 'bg-gray-50 text-gray-500 dark:bg-boxdark dark:text-gray-400' }}">
                                    {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المستلم</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $shipment->receiverCustomer?->name ?? 'غير محدد' }}
                                </span>

                                @if (View::exists('components.phone-number'))
                                    <x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'"
                                        class="text-[10px] font-bold text-gray-500 dark:text-bodydark" />
                                @else
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-bodydark" dir="ltr">
                                        {{ $shipment->receiverCustomer?->phone ?? '---' }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">فرع الوجهة</span>
                                <span class="text-xs font-bold text-primary">
                                    {{ $destination }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المحتوى</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $packageTypeLabel }}
                                    @if ($shipment->weight > 0)
                                        - {{ $shipment->weight }} كجم
                                    @endif
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المبلغ</span>
                                <span
                                    class="text-xs font-black {{ $shipment->payment_method == 'prepaid' ? 'text-emerald-500' : 'text-amber-500' }}">
                                    {{ number_format($shipment->total_amount, 0) }} ر.ي
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold text-gray-500 dark:text-bodydark">
                                    {{ $paymentLabel }}
                                </span>

                                @if ($shipment->payment_method === 'partial_payment')
                                    <span class="text-[10px] font-bold text-rose-500">
                                        المتبقي: {{ number_format($remainingAmount, 0) }}
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                                class="inline-flex gap-1.5 items-center px-3 h-9 text-[10px] font-black text-white rounded-xl transition-all bg-primary hover:bg-primary-hover active:scale-95">
                                التفاصيل
                                <span class="material-symbols-outlined text-[14px]">arrow_back_ios</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col gap-3 items-center py-16 text-center text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:text-bodydark dark:border-boxdark-2 bg-surface dark:bg-boxdark-2">
                        <span class="material-symbols-outlined text-[40px] opacity-30">inventory_2</span>
                        <p class="text-sm font-bold">لا توجد طرود مسجلة لهذا المكتب حالياً.</p>
                    </div>
                @endforelse

                <div x-show="visibleCount === 0 && {{ $shipments->count() }} > 0" x-cloak
                    class="py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex flex-col justify-center items-center">
                        <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">
                            search_off
                        </span>
                        <h4 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            لا توجد طرود تطابق بحثك في هذه الصفحة.
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
                            <th class="px-6 py-4">رقم السند</th>
                            <th class="px-6 py-4">المستلم</th>
                            <th class="px-6 py-4">فرع الوجهة</th>
                            <th class="px-6 py-4 text-center">المحتوى</th>
                            <th class="px-6 py-4 text-center">المبلغ</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse($shipments as $shipment)
                            @php
                                $packageTypeLabel = match ($shipment->package_type) {
                                    'carton' => 'كرتون',
                                    'bag' => 'كيس',
                                    'envelope' => 'مغلف',
                                    default => 'أخرى',
                                };

                                $paymentLabel = match ($shipment->payment_method) {
                                    'prepaid' => 'مدفوع مقدماً',
                                    'cod' => 'الدفع عند الاستلام',
                                    'partial_payment' => 'دفع جزئي',
                                    'customer_credit' => 'آجل',
                                    default => $shipment->payment_method,
                                };

                                $remainingAmount =
                                    (float) ($shipment->total_amount ?? 0) - (float) ($shipment->partial_amount ?? 0);

                                $destination = $shipment->receiverOfficeBranch?->name ?? '---';

                                $searchText =
                                    $shipment->bond_number .
                                    ' ' .
                                    ($shipment->receiverCustomer?->name ?? '') .
                                    ' ' .
                                    ($shipment->receiverCustomer?->phone ?? '') .
                                    ' ' .
                                    $destination .
                                    ' ' .
                                    $packageTypeLabel;
                            @endphp

                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group shipment-row"
                                x-show="showRow(@js($searchText))">

                                <td class="px-6 py-4">
                                    <div class="flex gap-4 items-center">
                                        <div
                                            class="flex justify-center items-center w-11 h-11 text-white rounded-lg shadow-inner bg-primary">
                                            <span class="material-symbols-outlined text-[20px]">package_2</span>
                                        </div>

                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-black text-gray-800 dark:text-white">
                                                {{ $shipment->bond_number }}
                                            </span>

                                            <span
                                                class="flex gap-1 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                                <span class="material-symbols-outlined text-[13px]">schedule</span>
                                                {{ optional($shipment->created_at)->format('Y/m/d - H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-black text-gray-800 dark:text-white truncate max-w-[180px]">
                                            {{ $shipment->receiverCustomer?->name ?? 'غير محدد' }}
                                        </span>

                                        @if (View::exists('components.phone-number'))
                                            <x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'"
                                                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                        @else
                                            <span class="text-[11px] font-bold text-gray-500 dark:text-bodydark" dir="ltr">
                                                {{ $shipment->receiverCustomer?->phone ?? '---' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-black text-gray-800 dark:text-white truncate max-w-[180px]">
                                            {{ $destination }}
                                        </span>

                                        <span class="text-[11px] font-bold text-primary truncate max-w-[220px]">
                                            {{ $office->name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1.5 items-center">
                                        <span
                                            class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                            {{ $packageTypeLabel }}

                                            @if ($shipment->weight > 0)
                                                <span class="text-gray-400">({{ $shipment->weight }} كجم)</span>
                                            @endif
                                        </span>

                                        @if ($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0)
                                            <span
                                                class="flex items-center gap-1 text-[10px] font-black text-amber-600 dark:text-amber-400">
                                                <span class="material-symbols-outlined text-[14px]">local_drink</span>

                                                @if ($shipment->no_gallons_honey > 0)
                                                    {{ $shipment->no_gallons_honey }} دباب
                                                @endif

                                                @if ($shipment->no_gallons_honey > 0 && $shipment->no_honey_jars > 0)
                                                    +
                                                @endif

                                                @if ($shipment->no_honey_jars > 0)
                                                    {{ $shipment->no_honey_jars }} قوارير
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span
                                            class="text-sm font-black {{ $shipment->payment_method == 'prepaid' ? 'text-emerald-500' : 'text-amber-500' }}">
                                            {{ number_format($shipment->total_amount, 0) }} ر.ي
                                        </span>

                                        <span
                                            class="px-2.5 py-1 text-[10px] font-black rounded-lg {{ $shipment->payment_method == 'prepaid' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' }}">
                                            {{ $paymentLabel }}
                                        </span>

                                        @if ($shipment->payment_method === 'partial_payment')
                                            <span class="text-[10px] font-bold text-rose-500">
                                                المتبقي: {{ number_format($remainingAmount, 0) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if (View::exists('components.shipment-status'))
                                        <x-shipment-status :status="$shipment->status" />
                                    @else
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
                                                'in_transit' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
                                                'delivered' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'returned' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
                                                'cancelled' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
                                            ];

                                            $statusLabels = [
                                                'pending' => 'قيد الانتظار',
                                                'in_transit' => 'في الطريق',
                                                'delivered' => 'تم التسليم',
                                                'returned' => 'مرتجعة',
                                                'cancelled' => 'ملغية',
                                            ];
                                        @endphp

                                        <span
                                            class="px-3 py-1.5 rounded-lg text-xs font-black {{ $statusClasses[$shipment->status] ?? 'bg-gray-50 text-gray-500 dark:bg-boxdark-2 dark:text-gray-400' }}">
                                            {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                                        </span>
                                    @endif
                                </td>

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
                                            class="absolute left-0 top-full mt-2 z-[999] w-56 bg-white/95 backdrop-blur-md rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark/95 dark:border-boxdark-2 focus:outline-none origin-top-left overflow-hidden"
                                            style="display: none;">

                                            <div class="py-1" role="menu">
                                                <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark-2 dark:hover:text-blue-400">
                                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                    التفاصيل
                                                </a>

                                                @if (auth()->user()->type === 'admin' || $shipment->status === 'pending')
                                                    <a href="{{ route('shipment.outgoing.edit', $shipment->id) }}"
                                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark-2 dark:hover:text-primary">
                                                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                                        تعديل البيانات
                                                    </a>
                                                @endif

                                                @if (!in_array($shipment->status, ['returned', 'cancelled']))
                                                    <a href="{{ route('receipt.generate', ['type' => 'sender', 'id' => $shipment->uuid]) }}"
                                                        target="_blank"
                                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-slate-50 hover:text-primary dark:hover:bg-boxdark-2">
                                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                                        طباعة السند
                                                    </a>

                                                    <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                                    @if ($shipment->receiverCustomer && $shipment->receiverCustomer->phone && $shipment->receiver_whatsapp_link)
                                                        <a href="{{ $shipment->receiver_whatsapp_link }}" target="_blank"
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                            <span class="material-symbols-outlined text-[18px]">send</span>
                                                            إرسال للمستلم
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div
                                            class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span class="material-symbols-outlined text-[28px] text-gray-400">
                                                inventory_2
                                            </span>
                                        </div>

                                        <div>
                                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                                لا توجد طرود مسجلة
                                            </h3>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                لم نعثر على أي طرود موجهة لهذا المكتب حالياً.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $shipments->count() }} > 0" x-cloak>
                            <td colspan="7" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div
                                        class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[28px] text-gray-400">
                                            search_off
                                        </span>
                                    </div>

                                    <div>
                                        <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                            لا توجد نتائج مطابقة
                                        </h3>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            لم نعثر على طرود تطابق كلمة البحث المدخلة.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($shipments->hasPages())
                <div
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $shipments->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
    <script>
        function officeShowRegistry() {
            return {
                searchQuery: '',
                visibleCount: {{ $shipments->count() }},

                showRow(searchText) {
                    const query = this.searchQuery.toLowerCase().trim();

                    if (!query) {
                        return true;
                    }

                    return String(searchText || '').toLowerCase().includes(query);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.shipment-row:not([style*="display: none"])')
                            .length;
                    });
                }
            }
        }
    </script>
@endsection