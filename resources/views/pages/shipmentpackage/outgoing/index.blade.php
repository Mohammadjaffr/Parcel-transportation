@extends('layouts.app')

@section('title', 'الشحنات المرسلة')

@section('content')

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl" x-data="outgoingPackagesRegistry()">
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex gap-4 justify-between items-start">
                <div class="text-right">
                    <h1 class="text-2xl font-black md:text-3xl text-on-surface dark:text-white">
                        الإرساليات المرسلة
                    </h1>
                    <p class="mt-1 text-sm font-bold text-gray-500 dark:text-bodydark">
                        إجمالي {{ $packages->total() ?? 0 }} إرسالية مسجلة
                    </p>
                </div>

                <a href="{{ route('shipmentpackage.outgoing.create') }}"
                    class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95 shrink-0">
                    <span class="material-symbols-outlined text-[20px]">add_box</span>
                    <span>إضافة إرسالية جديدة</span>
                </a>
            </div>
        </div>
        @php
            $currentStatus = request('status');
            $pagePackages = $packages->getCollection();

            $pendingCount = $pagePackages->where('status', 'pending')->count();
            $inTransitCount = $pagePackages->where('status', 'in_transit')->count();
            $deliveredCount = $pagePackages->where('status', 'delivered')->count();

            $totalShipmentsCount = $pagePackages->sum(function ($package) {
                return $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0);
            });

            $statusUrl = fn($status = null) => request()->fullUrlWithQuery([
                'status' => $status,
                'page' => null,
            ]);

            $statusData = function ($status) {
                return match ($status) {
                    'pending' => [
                        'label' => 'قيد التجهيز',
                        'class' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400',
                        'dot' => 'bg-amber-500',
                        'icon' => 'schedule',
                    ],
                    'in_transit' => [
                        'label' => 'في الطريق',
                        'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
                        'dot' => 'bg-blue-500',
                        'icon' => 'local_shipping',
                    ],
                    'received_at_branch' => [
                        'label' => 'بالمستودع',
                        'class' => 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400',
                        'dot' => 'bg-purple-500',
                        'icon' => 'warehouse',
                    ],
                    'delivered' => [
                        'label' => 'مكتملة',
                        'class' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
                        'dot' => 'bg-emerald-500',
                        'icon' => 'task_alt',
                    ],
                    'returned' => [
                        'label' => 'مرتجعة',
                        'class' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400',
                        'dot' => 'bg-rose-500',
                        'icon' => 'assignment_return',
                    ],
                    'cancelled' => [
                        'label' => 'ملغاة',
                        'class' => 'bg-gray-50 text-gray-600 dark:bg-gray-500/10 dark:text-gray-300',
                        'dot' => 'bg-gray-500',
                        'icon' => 'block',
                    ],
                    default => [
                        'label' => $status ?: 'غير محدد',
                        'class' => 'bg-gray-50 text-gray-600 dark:bg-gray-500/10 dark:text-gray-300',
                        'dot' => 'bg-gray-500',
                        'icon' => 'help',
                    ],
                };
            };
        @endphp

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-2 mx-auto max-w-7xl xl:grid-cols-4 md:gap-6">

            {{-- إجمالي الإرساليات --}}
            <a href="{{ $statusUrl(null) }}"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md {{ !$currentStatus ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-boxdark-2' }}">

                <div
                    class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        إجمالي الإرساليات
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $packages->total() ?? 0 }}
                    </h4>
                </div>
            </a>

            {{-- قيد التجهيز --}}
            <a href="{{ $statusUrl('pending') }}"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md border-r-amber-500 dark:border-r-amber-500 {{ $currentStatus === 'pending' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-gray-100 hover:border-amber-300 dark:border-boxdark-2' }}">

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
            </a>

            {{-- في الطريق --}}
            <a href="{{ $statusUrl('in_transit') }}"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md border-r-blue-500 dark:border-r-blue-500 {{ $currentStatus === 'in_transit' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-100 hover:border-blue-300 dark:border-boxdark-2' }}">

                <div
                    class="flex justify-center items-center w-12 h-12 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                    <span class="material-symbols-outlined text-[24px]">route</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-blue-500 uppercase">
                        في الطريق
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $inTransitCount }}
                    </h4>
                </div>
            </a>

            {{-- إجمالي الطرود داخل الإرساليات --}}
            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all dark:bg-boxdark hover:shadow-md border-r-emerald-500 dark:border-r-emerald-500 hover:border-emerald-300 dark:border-boxdark-2">

                <div
                    class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">
                        طرود الصفحة
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($totalShipmentsCount, 0) }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- ====================== Search & Table Section ====================== --}}
        <div
            class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="flex flex-col gap-4 justify-between items-stretch md:flex-row md:items-center">

                    {{-- مربع البحث --}}
                    <div
                        class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-[420px] dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">

                        <input type="text" x-model="searchQuery" @input.debounce.300ms="updateVisibility()"
                            placeholder="ابحث برقم الإرسالية، السائق، الوجهة، أو الفرع..."
                            class="pr-12 pl-12 w-full h-12 text-sm font-bold placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                        <div
                            class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-outlined text-[22px]">search</span>
                        </div>

                        <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; updateVisibility()"
                            x-cloak
                            class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all -translate-y-1/2 dark:bg-boxdark dark:border-boxdark-2 hover:text-error active:scale-95">
                            <span class="text-[18px] material-symbols-outlined">close</span>
                        </button>
                    </div>

                    {{-- معلومات النتائج --}}
                    <div class="flex gap-2 items-center text-xs font-black text-gray-500 dark:text-bodydark">
                        <span
                            class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        </span>

                        <span>
                            النتائج المعروضة:
                            <span class="text-primary" x-text="visibleCount"></span>
                            من
                            <span>{{ $packages->count() }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- ====================== Mobile View ====================== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse($packages as $package)
                    @php
                        $destinations = $package->shipments
                            ->map(function ($shipment) {
                                return $shipment->receiverBranch->name ??
                                    ($shipment->receiverOfficeBranch->name ?? null);
                            })
                            ->filter()
                            ->unique()
                            ->values();

                        $destText =
                            $destinations->count() > 1
                                ? 'وجهات متعددة (' . $destinations->count() . ')'
                                : $destinations->first() ?? 'غير محدد';

                        $shipmentsCount =
                            $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0);
                        $currentStatusData = $statusData($package->status);
                    @endphp

                    <div class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all package-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm"
                        x-show="showRow(
                            @js($package->tracking_number),
                            @js($package->senderBranch->name ?? ''),
                            @js($package->driver->name ?? ''),
                            @js($package->driver->phone ?? ''),
                            @js($destText)
                        )">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center min-w-0">
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-white rounded-xl shadow-inner bg-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                                </div>

                                <div class="flex flex-col gap-1 min-w-0">
                                    <span class="text-sm font-black truncate text-on-surface dark:text-white font-headline">
                                        {{ $package->id }}
                                    </span>

                                    <div
                                        class="flex gap-1.5 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                                        <span>{{ $package->created_at->translatedFormat('l') }}</span>
                                        <span class="text-gray-300">|</span>
                                        <span>{{ $package->created_at->format('Y/m/d') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile Actions --}}
                            <div x-data="{ menuOpen: false }" class="relative shrink-0">
                                <button @click="menuOpen = !menuOpen" @click.away="menuOpen = false"
                                    class="p-2 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="menuOpen" x-transition x-cloak
                                    class="absolute left-0 top-full z-[999] py-1.5 mt-2 w-56 rounded-2xl border border-gray-100 shadow-lg backdrop-blur-md bg-white/95 dark:bg-boxdark-2/95 dark:border-boxdark overflow-hidden">

                                    <a href="{{ route('shipmentpackage.outgoing.show', $package->id) }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark dark:hover:text-blue-400">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        عرض التفاصيل
                                    </a>

                                    @if (!in_array($package->status, ['returned', 'cancelled']))
                                        <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->uuid]) }}"
                                            target="_blank"
                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-slate-50 hover:text-primary dark:hover:bg-boxdark">
                                            <span class="material-symbols-outlined text-[18px]">print</span>
                                            طباعة كشف الرحلة
                                        </a>

                                        @if ($package->driver && $package->driver->phone && $package->DriverDetection)
                                            <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                            <a href="{{ $package->DriverDetection }}" target="_blank"
                                                class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                <span class="material-symbols-outlined text-[18px]">send</span>
                                                إرسال للسائق
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">فرع التجميع</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $package->senderBranch->name ?? 'غير محدد' }}
                                </span>
                                <span class="text-[10px] font-bold text-gray-500 dark:text-bodydark">
                                    بواسطة: {{ $package->creator->name ?? 'النظام' }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">الوجهة</span>
                                <span class="text-xs font-bold text-primary">
                                    {{ $destText }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">السائق</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $package->driver->name ?? 'غير محدد' }}
                                </span>
                                <x-phone-number :value="$package->driver?->phone ?? '---'"
                                    class="text-[10px] font-bold text-gray-500 dark:text-bodydark" />
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">عدد الطرود</span>
                                <span class="text-xs font-black text-primary">
                                    {{ $shipmentsCount }} طرد
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-boxdark">
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black {{ $currentStatusData['class'] }}">
                                <span
                                    class="material-symbols-outlined text-[14px]">{{ $currentStatusData['icon'] }}</span>
                                {{ $currentStatusData['label'] }}
                            </span>

                            <span
                                class="px-2.5 py-1 rounded-lg bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 shadow-sm text-gray-500 dark:text-gray-300 text-[10px] font-black">
                                {{ $destinations->count() > 1 ? $destinations->join('، ') : $destText }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col gap-3 items-center py-16 text-center text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:text-bodydark dark:border-boxdark-2 bg-surface dark:bg-boxdark-2">
                        <span class="material-symbols-outlined text-[40px] opacity-30">local_shipping</span>
                        <p class="text-sm font-bold">لا توجد إرساليات مرسلة حالياً.</p>
                    </div>
                @endforelse

                <div x-show="visibleCount === 0 && {{ $packages->count() }} > 0" x-cloak
                    class="py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex flex-col justify-center items-center">
                        <span
                            class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                        <h4 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            لا توجد إرساليات تطابق بحثك في هذه الصفحة.
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
                            <th class="px-6 py-4">رقم الإرسالية</th>
                            <th class="px-6 py-4">فرع التجميع</th>
                            <th class="px-6 py-4 text-center">الوجهة</th>
                            <th class="px-6 py-4">بيانات الحمولة</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse($packages as $package)
                            @php
                                $destinations = $package->shipments
                                    ->map(function ($shipment) {
                                        return $shipment->receiverBranch->name ??
                                            ($shipment->receiverOfficeBranch->name ?? null);
                                    })
                                    ->filter()
                                    ->unique()
                                    ->values();

                                $destText =
                                    $destinations->count() > 1
                                        ? 'وجهات متعددة (' . $destinations->count() . ')'
                                        : $destinations->first() ?? 'غير محدد';

                                $shipmentsCount =
                                    $package->shipments_count ??
                                    ($package->shipments ? $package->shipments->count() : 0);
                                $currentStatusData = $statusData($package->status);
                            @endphp

                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group package-row"
                                x-show="showRow(
                                    @js($package->tracking_number),
                                    @js($package->senderBranch->name ?? ''),
                                    @js($package->driver->name ?? ''),
                                    @js($package->driver->phone ?? ''),
                                    @js($destText)
                                )">

                                {{-- رقم الإرسالية --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-4 items-center">
                                        <div
                                            class="flex justify-center items-center w-11 h-11 text-white rounded-lg shadow-inner bg-primary">
                                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                        </div>

                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-black text-gray-800 dark:text-white">
                                                {{ $package->id }}
                                            </span>
                                            <span
                                                class="flex gap-1 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                                <span class="material-symbols-outlined text-[13px]">schedule</span>
                                                <span
                                                    class="text-primary">{{ $package->created_at->translatedFormat('l') }}</span>
                                                <span class="text-gray-300">|</span>
                                                {{ $package->created_at->format('Y/m/d') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- فرع التجميع --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-sm font-black text-gray-800 dark:text-white truncate max-w-[170px]">
                                            {{ $package->senderBranch->name ?? 'غير محدد' }}
                                        </span>

                                        <span class="text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                            بواسطة: {{ $package->creator->name ?? 'النظام' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- الوجهة --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold text-primary bg-primary/5 rounded-lg border border-primary/10 max-w-[220px]">
                                        <span class="material-symbols-outlined text-[16px]">route</span>
                                        <span class="truncate" title="{{ $destinations->join('، ') }}">
                                            {{ $destText }}
                                        </span>
                                    </span>
                                </td>

                                {{-- بيانات الحمولة --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-sm font-black text-gray-800 dark:text-white truncate max-w-[170px]">
                                            {{ $package->driver->name ?? 'غير محدد' }}
                                        </span>

                                        <x-phone-number :value="$package->driver?->phone ?? '---'"
                                            class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />

                                        <span
                                            class="flex gap-1 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                            إجمالي:
                                            <span class="font-black text-primary">{{ $shipmentsCount }}</span>
                                            طرد
                                        </span>
                                    </div>
                                </td>

                                {{-- الحالة --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black {{ $currentStatusData['class'] }}">
                                        <span
                                            class="material-symbols-outlined text-[14px]">{{ $currentStatusData['icon'] }}</span>
                                        {{ $currentStatusData['label'] }}
                                    </span>
                                </td>

                                {{-- الإجراءات --}}
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
                                                <a href="{{ route('shipmentpackage.outgoing.show', $package->id) }}"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark-2 dark:hover:text-blue-400">
                                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                    عرض التفاصيل
                                                </a>

                                                @if (!in_array($package->status, ['returned', 'cancelled']))
                                                    <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->uuid]) }}"
                                                        target="_blank"
                                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-slate-50 hover:text-primary dark:hover:bg-boxdark-2">
                                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                                        طباعة كشف الرحلة
                                                    </a>

                                                    @if ($package->driver && $package->driver->phone && $package->DriverDetection)
                                                        <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                                        <a href="{{ $package->DriverDetection }}" target="_blank"
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                            <span class="material-symbols-outlined text-[18px]">send</span>
                                                            إرسال للسائق
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
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div
                                            class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span
                                                class="material-symbols-outlined text-[28px] text-gray-400">local_shipping</span>
                                        </div>

                                        <div>
                                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                                لا توجد إرساليات
                                            </h3>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                لم نعثر على أي إرساليات مرسلة في النظام حالياً.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $packages->count() }} > 0" x-cloak>
                            <td colspan="6" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div
                                        class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[28px] text-gray-400">search_off</span>
                                    </div>

                                    <div>
                                        <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                            لا توجد نتائج مطابقة
                                        </h3>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            لم نعثر على إرساليات تطابق كلمة البحث المدخلة.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if (method_exists($packages, 'hasPages') && $packages->hasPages())
                <div
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
    <script>
        function outgoingPackagesRegistry() {
            return {
                searchQuery: '',
                visibleCount: {{ $packages->count() }},

                init() {
                    this.updateVisibility();
                },

                showRow(trackingNumber, senderBranch, driverName, driverPhone, destination) {
                    const query = this.searchQuery.toLowerCase().trim();

                    if (!query) {
                        return true;
                    }

                    return String(trackingNumber || '').toLowerCase().includes(query) ||
                        String(senderBranch || '').toLowerCase().includes(query) ||
                        String(driverName || '').toLowerCase().includes(query) ||
                        String(driverPhone || '').toLowerCase().includes(query) ||
                        String(destination || '').toLowerCase().includes(query);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.package-row:not([style*="display: none"])')
                            .length;
                    });
                }
            }
        }
    </script>
@endsection
