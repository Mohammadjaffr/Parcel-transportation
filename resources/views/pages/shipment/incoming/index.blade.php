@extends('layouts.app')

@section('title', 'الطرود الواردة')


@section('content')
    @php
        $currentStatus = request('status');
        $pageShipments = $shipments->getCollection();

        $receivedAtBranchCount = $pageShipments->where('status', 'received_at_branch')->count();
        $inTransitCount = $pageShipments->where('status', 'in_transit')->count();
        $deliveredCount = $pageShipments->where('status', 'delivered')->count();
        $returnedCount = $pageShipments->where('is_returned', true)->count();

        $totalCollectable = $pageShipments
            ->filter(fn($shipment) => $shipment->payment_method !== 'prepaid')
            ->sum('total_amount');

        $statusUrl = fn($status = null) => request()->fullUrlWithQuery([
            'status' => $status,
            'page' => null,
        ]);

        // 1. استخراج الفروع المصدرة للفلترة
        $uniqueSourceBranches = $pageShipments
            ->map(function ($shipment) {
                return $shipment->senderBranch?->name ?? ($shipment->senderOfficeBranch?->name ?? 'مصدر غير محدد');
            })
            ->filter()
            ->unique()
            ->values();

        // 2. تجميع بيانات الطرود لـ Alpine.js (شاملة المعروضة + كل طرود في الطريق للفرع)
        $allParcelsMap = collect();
        foreach ($pageShipments as $s) {
            $allParcelsMap->put($s->id, $s);
        }
        if (isset($inTransitParcels)) {
            foreach ($inTransitParcels as $itp) {
                if (!$allParcelsMap->has($itp->id)) {
                    $allParcelsMap->put($itp->id, $itp);
                }
            }
        }

        $alpineParcels = $allParcelsMap->values()->map(function ($p) {
            $branchName = $p->senderBranch?->name ?? ($p->senderOfficeBranch?->name ?? 'مصدر غير محدد');
            $customerName = $p->receiverCustomer?->name ?? ($p->senderCustomer?->name ?? '');
            return [
                'id' => $p->id,
                'uuid' => $p->uuid ?? '',
                'bond_number' => $p->bond_number ?? '',
                'code' => $p->code ?? '',
                'status' => $p->status,
                'status_label' => match ($p->status) {
                    'pending' => 'قيد التجهيز',
                    'in_transit' => 'في الطريق',
                    'received_at_branch' => 'بالمستودع',
                    'out_for_delivery' => 'خرج للتوصيل',
                    'delivered' => 'تم التسليم',
                    'returned' => 'مرتجع',
                    'cancelled' => 'ملغي',
                    default => $p->status
                },
                'customer_name' => $customerName,
                'branch_name' => $branchName,
            ];
        });

        $currentBranchCode = auth()->user()->branch?->code ?? 'B' . auth()->user()->branch_id;
    @endphp

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl"
        x-data="incomingShipmentsRegistry({{ $alpineParcels->toJson() }}, '{{ $currentBranchCode }}')">

        {{-- ================= نافذة مسح الباركود بالكاميرا (Camera Scanner Modal) ================= --}}
        <div x-show="isScannerOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
            <div @click.away="stopCameraScanner()"
                class="relative w-full max-w-md overflow-hidden bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6">

                <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-100 dark:border-boxdark-2">
                    <div class="flex gap-2.5 items-center">
                        <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-on-surface dark:text-white">مسح باركود السند للاستلام</h3>
                            <p class="text-[11px] font-bold text-gray-400">وجه الكاميرا نحو رمز QR أو الباركود في السند</p>
                        </div>
                    </div>
                    <button type="button" @click="stopCameraScanner()"
                        class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-full transition-colors hover:bg-gray-100 dark:hover:bg-boxdark-2 hover:text-error">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                {{-- منطقة الكاميرا --}}
                <div class="overflow-hidden relative bg-black rounded-2xl aspect-square">
                    <div id="qr-reader" class="w-full h-full"></div>
                </div>

                {{-- رسالة الحالة داخل المودال --}}
                <template x-if="scanFeedback.message">
                    <div x-transition class="flex gap-2 items-center p-3 mt-4 text-xs font-bold rounded-xl"
                        :class="{
                            'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400': scanFeedback.type === 'success',
                            'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400': scanFeedback.type === 'info',
                            'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400': scanFeedback.type === 'error'
                        }">
                        <span class="material-symbols-outlined text-[18px]"
                            x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                        <span x-text="scanFeedback.message"></span>
                    </div>
                </template>

                <div class="flex justify-between items-center pt-3 mt-5 border-t border-gray-100 dark:border-boxdark-2">
                    <span class="text-xs font-bold text-gray-500">الطرود المحددة: <strong class="text-primary"
                            x-text="selectedParcels.length"></strong></span>
                    <button type="button" @click="stopCameraScanner()"
                        class="px-5 py-2 text-xs font-black text-white rounded-xl bg-primary hover:bg-primary-hover">
                        إغلاق الكاميرا
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= نموذج استلام الطرود الجماعي (Hidden Form) ================= --}}
        <form id="receiveParcelsForm" action="{{ route('shipment.incoming.batchReceive') }}" method="POST"
            @submit="if(!isSubmitting) { isSubmitting = true; return true; } else { $event.preventDefault(); return false; }">
            @csrf
            <template x-for="id in selectedParcels" :key="id">
                <input type="hidden" name="parcel_ids[]" :value="id">
            </template>
        </form>

        <div class="mx-auto w-full max-w-7xl">
            <div class="flex flex-col gap-4 justify-between items-start md:flex-row md:items-center">
                <div class="text-right">
                    <h1 class="text-2xl font-black md:text-3xl text-on-surface dark:text-white">
                        الطرود الواردة
                    </h1>
                    <p class="mt-1 text-sm font-bold text-gray-500 dark:text-bodydark">
                        إجمالي {{ $shipments->total() ?? 0 }} طرد وارد مسجل بالنظام
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 items-center w-full md:w-auto">
                    {{-- مؤشر الطرود المحددة للاستلام --}}
                    <div class="hidden flex-col items-end pl-4 border-l border-gray-200 dark:border-boxdark-2 sm:flex"
                        x-show="selectedParcels.length > 0" x-cloak>
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">الطرود المحددة</span>
                        <span class="text-sm font-black text-primary" x-text="selectedParcels.length + ' طرد'"></span>
                    </div>

                    {{-- زر استلام الطرود المحددة --}}
                    <button type="button" @click="submitBatchReceive()"
                        :disabled="selectedParcels.length === 0 || isSubmitting"
                        class="flex gap-2 items-center px-6 h-12 text-sm font-black text-white rounded-2xl shadow-md transition-all bg-primary hover:bg-primary-hover shadow-primary/20 disabled:bg-surface disabled:dark:bg-boxdark-2 disabled:text-gray-400 disabled:shadow-none active:scale-95 shrink-0"
                        :class="selectedParcels.length > 0 ? 'ring-2 ring-primary/40' : ''">
                        <template x-if="isSubmitting">
                            <div class="flex gap-2 items-center">
                                <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                <span>جاري الاستلام...</span>
                            </div>
                        </template>
                        <template x-if="!isSubmitting">
                            <div class="flex gap-2 items-center">
                                <span class="material-symbols-outlined text-[20px]">warehouse</span>
                                <span>استلام الطرود</span>
                                <span x-show="selectedParcels.length > 0" x-text="'(' + selectedParcels.length + ')'"
                                    class="px-2 py-0.5 text-xs bg-white/20 rounded-lg"></span>
                            </div>
                        </template>
                    </button>

                    <a href="{{ route('shipment.incoming.scan') }}"
                        class="hidden sm:inline-flex gap-2 items-center px-4 h-12 text-sm font-black text-white rounded-2xl transition-all bg-[#fb6514] hover:bg-[#e0550c] shadow-sm hover:shadow-lg hover:shadow-[#fb6514]/20 active:scale-95 shrink-0"
                        title="الانتقال لشاشة الاستلام السريع بالباركود المنفصلة">
                        <span class="material-symbols-outlined text-[22px]">barcode_scanner</span>
                        <span>شاشة الاستلام المنفصلة</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-2 mx-auto max-w-7xl xl:grid-cols-4 md:gap-6">

            {{-- إجمالي الطرود --}}
            <a href="{{ $statusUrl(null) }}"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md {{ !$currentStatus ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-boxdark-2' }}">

                <div
                    class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        إجمالي الوارد
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $shipments->total() ?? 0 }}
                    </h4>
                </div>
            </a>

            {{-- في الطريق إلينا --}}
            <a href="{{ $statusUrl('in_transit') }}"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md border-r-blue-500 dark:border-r-blue-500 {{ $currentStatus === 'in_transit' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-100 hover:border-blue-300 dark:border-boxdark-2' }}">

                <div
                    class="flex justify-center items-center w-12 h-12 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                    <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-blue-500 uppercase">
                        في الطريق إلينا
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $inTransitCount }}
                    </h4>
                </div>
            </a>

            {{-- بالمستودع --}}
            <a href="{{ $statusUrl('received_at_branch') }}"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md border-r-purple-500 dark:border-r-purple-500 {{ $currentStatus === 'received_at_branch' ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-gray-100 hover:border-purple-300 dark:border-boxdark-2' }}">

                <div
                    class="flex justify-center items-center w-12 h-12 text-purple-500 bg-purple-50 rounded-xl dark:bg-purple-500/10">
                    <span class="material-symbols-outlined text-[24px]">warehouse</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-purple-500 uppercase">
                        بالمستودع
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $receivedAtBranchCount }}
                    </h4>
                </div>
            </a>

            {{-- المبالغ المطلوب تحصيلها --}}
            <div
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all dark:bg-boxdark hover:shadow-md border-r-emerald-500 dark:border-r-emerald-500 hover:border-emerald-300 dark:border-boxdark-2">

                <div
                    class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">
                        مطلوب تحصيله بالصفحة
                    </span>
                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($totalCollectable, 0) }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- ====================== Search & Table Section ====================== --}}
        <div
            class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- ================= شريط مسح الباركود والبحث والفلترة ================= --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="flex flex-wrap gap-3 justify-between items-center mb-4">
                    <h3 class="flex gap-2 items-center text-sm font-black font-headline text-on-surface dark:text-white">
                        <div class="flex justify-center items-center w-8 h-8 text-blue-500 bg-blue-50 rounded-lg dark:bg-blue-500/10">
                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                        </div>
                        استلام الطرود الواردة (في الطريق إلينا)
                    </h3>

                    <div class="flex gap-2">
                        <button type="button" @click="selectAllFiltered()"
                            class="flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-colors text-primary bg-primary-container dark:bg-primary/10 hover:bg-primary/20 active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">done_all</span>
                            <span class="hidden sm:inline">تحديد المعروض (في الطريق)</span>
                            <span class="sm:hidden">الكل</span>
                        </button>
                        <button type="button" @click="selectedParcels = [];" x-show="selectedParcels.length > 0" x-cloak
                            class="flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-colors text-error bg-error/10 hover:bg-error/20 active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">deselect</span>
                            إلغاء (<span x-text="selectedParcels.length"></span>)
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    {{-- 1. حقل مسح الباركود السريع + زر الكاميرا --}}
                    <div class="flex gap-2 w-full lg:w-2/5">
                        <div class="relative flex-1">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-primary material-symbols-outlined">qr_code_scanner</span>
                            <input type="text" x-model="barcodeInput" x-ref="barcodeField"
                                @keydown.enter.prevent="handleBarcodeScan(barcodeInput)"
                                placeholder="امسح باركود السند للاستلام (أو اكتب الرقم + Enter)..."
                                class="pr-10 pl-4 w-full h-11 text-sm font-bold rounded-xl border-2 transition-all outline-none border-primary/30 bg-primary/5 dark:bg-primary/10 dark:border-primary/40 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary focus:bg-white">
                        </div>

                        <button type="button" @click="startCameraScanner()"
                            class="flex gap-1.5 justify-center items-center px-4 h-11 text-xs font-black text-white rounded-xl shadow-sm transition-all bg-primary hover:bg-primary-hover shrink-0 active:scale-95"
                            title="مسح باستخدام الكاميرا">
                            <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                            <span class="hidden sm:inline">مسح بالكاميرا</span>
                        </button>
                    </div>

                    {{-- 2. حقل البحث النصي العادي --}}
                    <div class="relative flex-1">
                        <span class="absolute right-3 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined">search</span>
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="updateVisibility()"
                            placeholder="ابحث برقم السند، اسم العميل، أو رقم الهاتف..."
                            class="pr-10 pl-10 w-full h-11 text-sm rounded-xl border border-gray-200 transition-all outline-none bg-surface dark:bg-boxdark dark:border-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white">
                        <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; updateVisibility()" x-cloak
                            class="flex absolute left-2 top-1/2 justify-center items-center w-7 h-7 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-all -translate-y-1/2 dark:bg-boxdark dark:border-boxdark-2 hover:text-error active:scale-95">
                            <span class="text-[16px] material-symbols-outlined">close</span>
                        </button>
                    </div>

                    {{-- 3. فلتر الفرع المصدر --}}
                    <div class="relative w-full lg:w-56 shrink-0">
                        <span class="absolute right-3 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined">store</span>
                        <select x-model="selectedBranch" @change="updateVisibility()"
                            class="pr-10 pl-4 w-full h-11 text-sm rounded-xl border border-gray-200 transition-all appearance-none cursor-pointer outline-none bg-surface dark:bg-boxdark dark:border-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/20 focus:border-primary focus:bg-white">
                            <option value="">كل الفروع المصدرة</option>
                            @foreach ($uniqueSourceBranches as $branch)
                                <option value="{{ $branch }}">{{ $branch }}</option>
                            @endforeach
                        </select>
                        <span class="absolute left-3 top-1/2 text-gray-400 -translate-y-1/2 pointer-events-none material-symbols-outlined">expand_more</span>
                    </div>
                </div>

                {{-- معلومات النتائج --}}
                <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-50 dark:border-boxdark">
                    <div class="flex gap-2 items-center text-xs font-black text-gray-500 dark:text-bodydark">
                        <span class="inline-flex justify-center items-center w-7 h-7 rounded-lg bg-primary-container dark:bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                        </span>
                        <span>
                            النتائج المعروضة:
                            <span class="text-primary font-mono" x-text="visibleCount"></span>
                            من
                            <span class="font-mono">{{ $shipments->count() }}</span>
                        </span>
                    </div>

                    <div class="flex items-center gap-2" x-show="selectedParcels.length > 0" x-cloak>
                        <span class="text-xs font-bold text-gray-500">تم تحديد:</span>
                        <span class="px-2.5 py-0.5 text-xs font-black text-primary bg-primary/10 rounded-full font-mono" x-text="selectedParcels.length + ' طرد'"></span>
                    </div>
                </div>
            </div>

            {{-- ================= تنبيه نتيجة المسح الضوئي (Feedback Alert) ================= --}}
            <template x-if="scanFeedback.message && !isScannerOpen">
                <div class="p-4 md:px-6">
                    <div x-transition
                        class="flex justify-between items-center p-3 text-xs font-bold rounded-xl border"
                        :class="{
                            'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400': scanFeedback.type === 'success',
                            'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400': scanFeedback.type === 'info',
                            'bg-red-50 border-red-200 text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-400': scanFeedback.type === 'error'
                        }">
                        <div class="flex gap-2 items-center">
                            <span class="material-symbols-outlined text-[18px]"
                                x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                            <span x-text="scanFeedback.message"></span>
                        </div>
                        <button type="button" @click="scanFeedback.message = ''" class="opacity-60 hover:opacity-100">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    </div>
                </div>
            </template>

            {{-- ====================== Mobile View ====================== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse($shipments as $shipment)
                    @php
                        $source = $shipment->senderBranch?->name ?? $shipment->senderOfficeBranch?->name ?? 'مصدر غير محدد';

                        $paymentLabel = match ($shipment->payment_method) {
                            'prepaid' => 'خالص الدفع',
                            'cod' => 'الدفع عند الاستلام',
                            'partial_payment' => 'دفع جزئي',
                            'customer_credit' => 'آجل',
                            default => $shipment->payment_method,
                        };

                        $collectAmount = $shipment->payment_method === 'prepaid'
                            ? 0
                            : (float) ($shipment->total_amount ?? 0);

                        $remainingAmount = (float) ($shipment->total_amount ?? 0) - (float) ($shipment->partial_amount ?? 0);

                        $targetCustomer = $shipment->is_returned ? $shipment->senderCustomer : $shipment->receiverCustomer;

                        if ($targetCustomer && $targetCustomer->phone) {
                            if ($shipment->is_returned) {
                                $whatsappMsg = "مرحباً *" . $targetCustomer->name . "*،\nنفيدك بوصول طردكم (المرتجع) برقم السند: *" . ($shipment->code ?? $shipment->bond_number) . "* إلى فرعنا.\nيرجى التفضل باستلامه.";
                            } else {
                                $whatsappMsg = "مرحباً *" . $targetCustomer->name . "*،\nنفيدك بوصول طردك برقم السند: *" . ($shipment->code ?? $shipment->bond_number) . "* إلى فرعنا.\n" . ($shipment->payment_method !== 'prepaid' ? "المبلغ المطلوب عند الاستلام: *" . number_format($shipment->total_amount, 0) . "* ريال." : "الطرد خالص الدفع.");
                            }

                            $targetPhone = ltrim($targetCustomer->phone, '+');

                            if (str_starts_with($targetPhone, '00')) {
                                $targetPhone = substr($targetPhone, 2);
                            }

                            if (preg_match('/^0(7\d{8})$/', $targetPhone, $matches)) {
                                $targetPhone = '967' . $matches[1];
                            } elseif (preg_match('/^(7\d{8})$/', $targetPhone, $matches)) {
                                $targetPhone = '967' . $matches[1];
                            }

                            $whatsappLink = "https://wa.me/{$targetPhone}?text=" . urlencode($whatsappMsg);
                        } else {
                            $whatsappLink = null;
                        }
                    @endphp

                    <div id="shipment-card-{{ $shipment->id }}"
                        class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all incoming-shipment-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm {{ $shipment->status === 'in_transit' ? 'cursor-pointer' : '' }}"
                        :class="selectedParcels.includes({{ $shipment->id }}) ? 'ring-2 ring-primary border-primary bg-primary-container/20 dark:bg-primary/10' : ''"
                        @click="{{ $shipment->status === 'in_transit' ? 'toggleParcel(' . $shipment->id . ')' : '' }}"
                        x-show="showRow(
                                            @js($shipment->code ?? $shipment->bond_number),
                                            @js($shipment->senderCustomer?->name),
                                            @js($shipment->senderCustomer?->phone),
                                            @js($shipment->receiverCustomer?->name),
                                            @js($shipment->receiverCustomer?->phone),
                                            @js($source)
                                        )">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center min-w-0">
                                @if($shipment->status === 'in_transit')
                                    <label class="flex justify-center items-center p-1.5 cursor-pointer shrink-0" @click.stop>
                                        <input type="checkbox" class="sr-only peer"
                                            :checked="selectedParcels.includes({{ $shipment->id }})"
                                            @change.stop="toggleParcel({{ $shipment->id }})">
                                        <div
                                            class="flex justify-center items-center w-6 h-6 bg-white rounded-lg border-2 border-gray-300 shadow-sm transition-all dark:border-gray-600 dark:bg-boxdark peer-checked:bg-primary peer-checked:border-primary">
                                            <span
                                                class="material-symbols-outlined text-[16px] text-white opacity-0 peer-checked:opacity-100 transition-opacity">check</span>
                                        </div>
                                    </label>
                                @endif
                                <div
                                    class="flex justify-center items-center w-12 h-12 text-white rounded-xl shadow-inner bg-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                                </div>

                                <div class="flex flex-col gap-1 min-w-0">
                                    <span class="text-sm font-black truncate text-on-surface dark:text-white font-headline">
                                        {{ $shipment->code ?? $shipment->bond_number }}
                                    </span>

                                    <div
                                        class="flex gap-1.5 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                                        <span>{{ optional($shipment->created_at)->format('Y/m/d - H:i') }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile Actions --}}
                            <div x-data="{ menuOpen: false, showPaymentModal: false, isSubmitting: false }"
                                class="relative shrink-0">

                                {{-- زر فتح القائمة --}}
                                <button @click="menuOpen = !menuOpen" @click.outside="menuOpen = false"
                                    class="p-2 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                {{-- نافذة القائمة المنسدلة --}}
                                <div x-show="menuOpen" x-transition x-cloak
                                    class="absolute left-0 top-full {{ strip_tags($shipment->status) === 'in_transit' || (!in_array($shipment->status, ['delivered', 'returned', 'cancelled', 'pending', 'in_transit'])) ? 'z-[100]' : 'z-[999]' }} py-1.5 mt-2 w-60 rounded-2xl border border-gray-100 shadow-lg backdrop-blur-md bg-white/95 dark:bg-boxdark-2/95 dark:border-boxdark overflow-hidden">

                                    {{-- 1. الروابط الأساسية الثابتة --}}
                                    <a href="{{ route('shipment.incoming.show', $shipment->id) }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark dark:hover:text-blue-400">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        {{ $shipment->is_returned ? 'تفاصيل المرتجع' : 'التفاصيل والتسليم' }}
                                    </a>

                                    <a href="{{ route('receipt.generate', ['type' => 'receiver', 'id' => $shipment->uuid]) }}"
                                        target="_blank"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-slate-50 hover:text-primary dark:hover:bg-boxdark">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                        طباعة السند
                                    </a>

                                    @if($whatsappLink)
                                        <a href="{{ $whatsappLink }}" target="_blank"
                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                            <span class="material-symbols-outlined text-[18px]">send</span>
                                            {{ $shipment->is_returned ? 'إشعار التاجر بالمرتجع' : 'إشعار المستلم' }}
                                        </a>
                                    @endif

                                    <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                    {{-- 2. قسم الإجراءات السريعة بناءً على حالة الطرد --}}
                                    @if(in_array($shipment->status, ['delivered', 'returned', 'cancelled']))
                                        {{-- حالة الإغلاق --}}
                                        <div
                                            class="flex gap-3 items-center px-4 py-2 text-xs font-bold cursor-not-allowed text-slate-400">
                                            <span class="material-symbols-outlined text-[18px]">lock</span>
                                            تم إغلاق هذا الطرد
                                        </div>

                                    @elseif($shipment->status === 'pending')
                                        {{-- حالة قيد التجهيز --}}
                                        <div class="flex gap-3 items-center px-4 py-2 text-xs font-bold text-amber-500">
                                            <span class="material-symbols-outlined text-[18px]">schedule</span>
                                            الطرد لا يزال قيد التجهيز
                                        </div>

                                    @elseif($shipment->status === 'in_transit')
                                        {{-- إجراء استلام للمستودع --}}
                                        <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST"
                                            @submit="isSubmitting = true">
                                            @csrf
                                            <input type="hidden" name="status" value="received_at_branch">
                                            <button type="submit" :disabled="isSubmitting"
                                                class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10">
                                                <span class="material-symbols-outlined text-[18px]"
                                                    x-show="!isSubmitting">inventory_2</span>
                                                <span class="material-symbols-outlined animate-spin text-[18px]"
                                                    x-show="isSubmitting">progress_activity</span>
                                                <span x-text="isSubmitting ? 'جاري التأكيد...' : 'تأكيد الوصول للمستودع'"></span>
                                            </button>
                                        </form>

                                    @else
                                        {{-- إجراءات التسليم والرفض المتطورة --}}
                                        <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST"
                                            x-ref="statusForm" @submit="isSubmitting = true">
                                            @csrf
                                            <input type="hidden" name="status" x-ref="statusInput">

                                            @if($shipment->is_returned)
                                                {{-- تسليم المرتجع للتاجر --}}
                                                <button type="button" :disabled="isSubmitting"
                                                    @click="$refs.statusInput.value = 'delivered'; $refs.statusForm.submit(); menuOpen = false"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                                    <span>تأكيد تسليم المرتجع للتاجر</span>
                                                </button>
                                            @else
                                                {{-- تسليم الطرد للعميل --}}
                                                <button type="button" :disabled="isSubmitting" @click="
                                                                    @if($shipment->payment_method !== 'prepaid')
                                                                        showPaymentModal = true; 
                                                                        menuOpen = false;
                                                                    @else
                                                                        $refs.statusInput.value = 'delivered'; 
                                                                        $refs.statusForm.submit();
                                                                        menuOpen = false;
                                                                    @endif
                                                                "
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">task_alt</span>
                                                    <span>تأكيد التسليم للعميل</span>
                                                </button>

                                                {{-- رفض الاستلام / إرجاع --}}
                                                <button type="button" :disabled="isSubmitting"
                                                    @click="$refs.statusInput.value = 'returned'; $refs.statusForm.submit(); menuOpen = false"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                                                    <span>رفض الاستلام (إرجاع)</span>
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </div>

                                {{-- 3. نافذة التحصيل المالي (Modal) - خارج قائمة المنيو لتعمل بوضعية التثبيت الشامل --}}
                                @if(!$shipment->is_returned && $shipment->payment_method !== 'prepaid')
                                    <div x-show="showPaymentModal" x-cloak
                                        class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
                                        <div x-show="showPaymentModal" x-transition.opacity duration.300ms
                                            @click="showPaymentModal = false"
                                            class="absolute inset-0 backdrop-blur-sm bg-slate-900/40"></div>

                                        <div x-show="showPaymentModal" x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                            class="relative p-6 w-full max-w-sm text-center bg-white rounded-3xl border shadow-2xl border-slate-100 dark:bg-boxdark dark:border-boxdark-2 z-[10000]">

                                            <div
                                                class="flex justify-center items-center mx-auto mb-4 w-20 h-20 text-rose-500 bg-rose-50 rounded-full animate-bounce dark:bg-rose-500/10 dark:text-rose-400">
                                                <span class="material-symbols-outlined text-[40px]">payments</span>
                                            </div>

                                            <h3 class="mb-2 text-lg font-black text-slate-800 dark:text-white font-headline">تنبيه
                                                تحصيل مالي!</h3>
                                            <p class="mb-6 text-xs font-bold leading-relaxed text-slate-500 dark:text-slate-400">
                                                هذا الطرد غير مدفوع مسبقاً. الرجاء استلام المبلغ التالي من العميل قبل تأكيد عملية
                                                التسليم.
                                            </p>

                                            <div
                                                class="p-5 mb-6 bg-rose-50 rounded-2xl border border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20">
                                                <p class="mb-1 text-[10px] font-black text-rose-600/70 dark:text-rose-400">المبلغ
                                                    المطلوب تحصيله</p>
                                                <p class="font-mono text-3xl font-black text-rose-700 dir-ltr dark:text-rose-300">
                                                    {{ number_format($shipment->total_amount, 0) }}
                                                </p>
                                            </div>

                                            <div class="flex gap-2">
                                                <button type="button" @click="showPaymentModal = false"
                                                    class="flex-1 h-12 text-xs font-black rounded-xl transition-colors text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-boxdark-2 dark:text-slate-300 dark:hover:bg-gray-600">
                                                    تراجع
                                                </button>

                                                {{-- زر التأكيد النهائي من داخل المودال مدمج لمخاطبة نموذج الـ Form عبر الـ x-ref
                                                --}}
                                                <button type="button"
                                                    @click="$refs.statusInput.value = 'delivered'; $refs.statusForm.submit(); showPaymentModal = false"
                                                    class="flex-[2] h-12 bg-emerald-500 text-white hover:bg-emerald-600 rounded-xl font-black text-xs shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                                                    <span class="material-symbols-outlined text-[18px]">verified</span>
                                                    تم استلام المبلغ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المصدر</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $shipment->senderCustomer?->name ?? 'مصدر غير محدد' }}
                                </span>
                                <x-phone-number :value="$shipment->senderCustomer?->phone ?? '---'"
                                    class="text-[10px] font-bold text-gray-500 dark:text-bodydark" />
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المستلم</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $shipment->receiverCustomer?->name ?? 'غير مسجل' }}
                                </span>
                                <x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'"
                                    class="text-[10px] font-bold text-gray-500 dark:text-bodydark" />
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">فرع المصدر</span>
                                <span class="text-xs font-bold text-primary">
                                    {{ $source }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المحتوى</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $shipment->package_type }}
                                    @if($shipment->weight > 0)
                                        - {{ $shipment->weight }} كجم
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المطلوب تحصيله</span>
                                <span
                                    class="text-sm font-black {{ $collectAmount > 0 ? 'text-amber-500' : 'text-emerald-500' }}">
                                    {{ number_format($collectAmount, 0) }} ر.ي
                                </span>

                                @if($shipment->payment_method === 'partial_payment')
                                    <span class="text-[10px] font-bold text-rose-500">
                                        المتبقي: {{ number_format($remainingAmount, 0) }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-bodydark">
                                        {{ $paymentLabel }}
                                    </span>
                                @endif
                            </div>

                            @if($shipment->is_returned && !in_array($shipment->status, ['delivered', 'cancelled']))
                                <span
                                    class="inline-flex gap-1 items-center px-3 py-1.5 text-xs font-black text-rose-600 bg-rose-50 rounded-lg ring-1 ring-inset ring-rose-500/30">
                                    <span class="material-symbols-outlined text-[14px] animate-pulse">keyboard_return</span>
                                    مرتجع
                                </span>
                            @else
                                <x-shipment-status :status="$shipment->status" />
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col gap-3 items-center py-16 text-center text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:text-bodydark dark:border-boxdark-2 bg-surface dark:bg-boxdark-2">
                        <span class="material-symbols-outlined text-[40px] opacity-30">inventory_2</span>
                        <p class="text-sm font-bold">لا توجد طرود واردة حالياً.</p>
                    </div>
                @endforelse

                <div x-show="visibleCount === 0 && {{ $shipments->count() }} > 0" x-cloak
                    class="py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex flex-col justify-center items-center">
                        <span
                            class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                        <h4 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            لا توجد طرود واردة تطابق بحثك في هذه الصفحة.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ================= شريط الإجراءات العائم للموبايل ================= --}}
            <div x-show="selectedParcels.length > 0" x-cloak x-transition
                class="fixed bottom-4 inset-x-4 z-50 p-4 bg-white/95 backdrop-blur-md dark:bg-boxdark/95 rounded-2xl shadow-2xl border border-gray-100 dark:border-boxdark-2 flex items-center justify-between lg:hidden">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-black text-sm shadow-sm" x-text="selectedParcels.length"></div>
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-on-surface dark:text-white">طرود محددة</span>
                        <span class="text-[10px] text-gray-500 dark:text-bodydark">جاهزة للتأكيد والاستلام</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="selectedParcels = []"
                        class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-error transition-colors">
                        إلغاء
                    </button>
                    <button type="button" @click="submitBatchReceive()" :disabled="isSubmitting"
                        class="px-4 py-2.5 text-xs font-black text-white bg-primary rounded-xl shadow-md hover:bg-primary-hover active:scale-95 flex items-center gap-1.5 shadow-primary/20">
                        <span class="material-symbols-outlined text-[16px]">warehouse</span>
                        <span>استلام الآن</span>
                    </button>
                </div>
            </div>

            {{-- ====================== Desktop View ====================== --}}
            <div class="hidden overflow-visible w-full lg:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr
                            class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
                            <th class="px-4 py-4 w-12 text-center">تحديد</th>
                            <th class="px-6 py-4">رقم السند</th>
                            <th class="px-6 py-4">المصدر</th>
                            <th class="px-6 py-4">المستلم</th>
                            <th class="px-6 py-4 text-center">المحتوى</th>
                            <th class="px-6 py-4 text-center">المطلوب تحصيله</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse($shipments as $shipment)
                            @php
                                $source = $shipment->senderBranch?->name ?? $shipment->senderOfficeBranch?->name ?? 'مصدر غير محدد';
                                $paymentLabel = match ($shipment->payment_method) {
                                    'prepaid' => 'خالص الدفع',
                                    'cod' => 'الدفع عند الاستلام',
                                    'partial_payment' => 'دفع جزئي',
                                    'customer_credit' => 'آجل',
                                    default => $shipment->payment_method,
                                };

                                $collectAmount = $shipment->payment_method === 'prepaid'
                                    ? 0
                                    : (float) ($shipment->total_amount ?? 0);

                                $remainingAmount = (float) ($shipment->total_amount ?? 0) - (float) ($shipment->partial_amount ?? 0);

                                $targetCustomer = $shipment->is_returned ? $shipment->senderCustomer : $shipment->receiverCustomer;

                                if ($targetCustomer && $targetCustomer->phone) {
                                    if ($shipment->is_returned) {
                                        $whatsappMsg = "مرحباً *" . $targetCustomer->name . "*،\nنفيدك بوصول طردكم (المرتجع) برقم السند: *" . ($shipment->code ?? $shipment->bond_number) . "* إلى فرعنا.\nيرجى التفضل باستلامه.";
                                    } else {
                                        $whatsappMsg = "مرحباً *" . $targetCustomer->name . "*،\nنفيدك بوصول طردك برقم السند: *" . ($shipment->code ?? $shipment->bond_number) . "* إلى فرعنا.\n" . ($shipment->payment_method !== 'prepaid' ? "المبلغ المطلوب عند الاستلام: *" . number_format($shipment->total_amount, 0) . "* ريال." : "الطرد خالص الدفع.");
                                    }

                                    $targetPhone = ltrim($targetCustomer->phone, '+');

                                    if (str_starts_with($targetPhone, '00')) {
                                        $targetPhone = substr($targetPhone, 2);
                                    }

                                    if (preg_match('/^0(7\d{8})$/', $targetPhone, $matches)) {
                                        $targetPhone = '967' . $matches[1];
                                    } elseif (preg_match('/^(7\d{8})$/', $targetPhone, $matches)) {
                                        $targetPhone = '967' . $matches[1];
                                    }

                                    $whatsappLink = "https://wa.me/{$targetPhone}?text=" . urlencode($whatsappMsg);
                                } else {
                                    $whatsappLink = null;
                                }
                            @endphp

                            <tr id="shipment-row-{{ $shipment->id }}"
                                class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group incoming-shipment-row {{ $shipment->status === 'in_transit' ? 'cursor-pointer' : '' }}"
                                :class="selectedParcels.includes({{ $shipment->id }}) ? 'bg-primary/5 dark:bg-primary/10' : ''"
                                @click="{{ $shipment->status === 'in_transit' ? 'toggleParcel(' . $shipment->id . ')' : '' }}"
                                x-show="showRow(
                                    @js($shipment->code ?? $shipment->bond_number),
                                    @js($shipment->senderCustomer?->name),
                                    @js($shipment->senderCustomer?->phone),
                                    @js($shipment->receiverCustomer?->name),
                                    @js($shipment->receiverCustomer?->phone),
                                    @js($source)
                                )">

                                {{-- عمود التحديد --}}
                                <td class="px-4 py-4 text-center" @click.stop>
                                    @if($shipment->status === 'in_transit')
                                        <label class="flex relative justify-center items-center p-2 w-full h-full cursor-pointer">
                                            <input type="checkbox" class="sr-only peer"
                                                :checked="selectedParcels.includes({{ $shipment->id }})"
                                                @change.stop="toggleParcel({{ $shipment->id }})">
                                            <div
                                                class="flex justify-center items-center w-5 h-5 bg-white rounded border-2 border-gray-300 shadow-sm transition-all dark:border-gray-600 dark:bg-boxdark peer-checked:bg-primary peer-checked:border-primary">
                                                <span
                                                    class="material-symbols-outlined text-[14px] text-white opacity-0 peer-checked:opacity-100 transition-opacity">check</span>
                                            </div>
                                        </label>
                                    @elseif($shipment->status === 'received_at_branch')
                                        <div class="flex justify-center items-center w-full h-full text-purple-500" title="بالمستودع (تم الاستلام)">
                                            <span class="material-symbols-outlined text-[18px]">warehouse</span>
                                        </div>
                                    @else
                                        <div class="flex justify-center items-center w-full h-full text-gray-300 dark:text-gray-600" title="تم إنهاء هذا الطرد">
                                            <span class="material-symbols-outlined text-[18px]">done</span>
                                        </div>
                                    @endif
                                </td>

                                                    {{-- رقم السند --}}
                                                    <td class="px-6 py-4">
                                                        <div class="flex gap-4 items-center">
                                                            <div
                                                                class="flex justify-center items-center w-11 h-11 text-white rounded-lg shadow-inner bg-primary">
                                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                                            </div>

                                                            <div class="flex flex-col gap-1">
                                                                <span class="text-sm font-black text-gray-800 dark:text-white">
                                                                    {{ $shipment->code ?? $shipment->bond_number }}
                                                                </span>
                                                                <span
                                                                    class="flex gap-1 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                                                    <span class="material-symbols-outlined text-[13px]">schedule</span>
                                                                    {{ optional($shipment->created_at)->format('Y/m/d - H:i') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    {{-- المصدر --}}
                                                    <td class="px-6 py-4">
                                                        <div class="flex flex-col gap-1">
                                                            <span class="text-sm font-black text-gray-800 dark:text-white truncate max-w-[170px]">
                                                                {{ $shipment->senderCustomer?->name ?? 'مصدر غير محدد' }}
                                                            </span>

                                                            <span class="text-[11px] font-bold text-primary truncate max-w-[180px]">
                                                                {{ $source }}
                                                            </span>

                                                            <x-phone-number :value="$shipment->senderCustomer?->phone ?? '---'"
                                                                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                                        </div>
                                                    </td>

                                                    {{-- المستلم --}}
                                                    <td class="px-6 py-4">
                                                        <div class="flex flex-col gap-1">
                                                            <span class="text-sm font-black text-gray-800 dark:text-white truncate max-w-[180px]">
                                                                {{ $shipment->receiverCustomer?->name ?? 'غير مسجل' }}
                                                            </span>

                                                            <x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'"
                                                                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                                        </div>
                                                    </td>

                                                    {{-- المحتوى --}}
                                                    <td class="px-6 py-4 text-center">
                                                        <div class="flex flex-col gap-1.5 items-center">
                                                            <span
                                                                class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                                                {{ $shipment->package_type }}
                                                                @if($shipment->weight > 0)
                                                                    <span class="text-gray-400">({{ $shipment->weight }} كجم)</span>
                                                                @endif
                                                            </span>

                                                            @if($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0)
                                                                <span
                                                                    class="flex items-center gap-1 text-[10px] font-black text-amber-600 dark:text-amber-400">
                                                                    <span class="material-symbols-outlined text-[14px]">local_drink</span>

                                                                    @if($shipment->no_gallons_honey > 0)
                                                                        {{ $shipment->no_gallons_honey }} جوالين
                                                                    @endif

                                                                    @if($shipment->no_gallons_honey > 0 && $shipment->no_honey_jars > 0)
                                                                        +
                                                                    @endif

                                                                    @if($shipment->no_honey_jars > 0)
                                                                        {{ $shipment->no_honey_jars }} قروف
                                                                    @endif
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>

                                                    {{-- المطلوب --}}
                                                    <td class="px-6 py-4 text-center">
                                                        <div class="flex flex-col gap-1 items-center">
                                                            <span
                                                                class="text-sm font-black {{ $collectAmount > 0 ? 'text-amber-500' : 'text-emerald-500' }}">
                                                                {{ number_format($collectAmount, 0) }} ر.ي
                                                            </span>

                                                            <span
                                                                class="px-2.5 py-1 text-[10px] font-black rounded-lg {{ $collectAmount > 0 ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' }}">
                                                                {{ $paymentLabel }}
                                                            </span>

                                                            @if($shipment->payment_method === 'partial_payment')
                                                                <span class="text-[10px] font-bold text-rose-500">
                                                                    المتبقي: {{ number_format($remainingAmount, 0) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>

                                                    {{-- الحالة --}}
                                                    <td class="px-6 py-4 text-center">
                                                        @if($shipment->is_returned && !in_array($shipment->status, ['delivered', 'cancelled']))
                                                            <span
                                                                class="inline-flex gap-1 items-center px-3 py-1.5 text-xs font-black text-rose-600 bg-rose-50 rounded-lg ring-1 ring-inset ring-rose-500/30">
                                                                <span class="material-symbols-outlined text-[14px] animate-pulse">keyboard_return</span>
                                                                مرتجع
                                                                @if($shipment->status == 'pending') (بالمستودع)
                                                                @elseif($shipment->status == 'in_transit') (في الطريق)
                                                                @elseif($shipment->status == 'received_at_branch') (وصل المصدر)
                                                                @endif
                                                            </span>
                                                        @else
                                                            <x-shipment-status :status="$shipment->status" />
                                                        @endif
                                                    </td>

                                                    {{-- الإجراءات --}}
                                                    <td class="relative px-6 py-4 text-center">
                                                        {{-- تهيئة متغيرات الحالات المستقلة لكل سطر في الجدول --}}
                                <div x-data="{ open: false, showPaymentModal: false, isSubmitting: false }" class="inline-block relative text-right"
                                    @click.away="open = false">

                                    {{-- زر خيارات (more_vert) --}}
                                    <button @click="open = !open" type="button" title="خيارات"
                                        class="inline-flex justify-center items-center w-9 h-9 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-all hover:bg-gray-100 hover:text-gray-600 hover:border-gray-200 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2 dark:hover:text-gray-300 active:scale-95">
                                        <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                    </button>

                                    {{-- القائمة المنسدلة للخيارات --}}
                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute left-0 top-full mt-2 z-[999] w-56 bg-white/95 backdrop-blur-md rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark/95 dark:border-boxdark-2 focus:outline-none origin-top-left overflow-hidden"
                                        style="display: none;">

                                        <div class="py-1" role="menu">
                                            {{-- رابط تفاصيل الشحنة --}}
                                            <a href="{{ route('shipment.incoming.show', $shipment->id) }}"
                                                class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark-2 dark:hover:text-blue-400">
                                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                {{ $shipment->is_returned ? 'تفاصيل المرتجع' : 'التفاصيل والتسليم' }}
                                            </a>

                                            {{-- رابط طباعة السند --}}
                                            <a href="{{ route('receipt.generate', ['type' => 'receiver', 'id' => $shipment->uuid]) }}"
                                                target="_blank"
                                                class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-slate-50 hover:text-primary dark:hover:bg-boxdark-2">
                                                <span class="material-symbols-outlined text-[18px]">print</span>
                                                طباعة السند
                                            </a>

                                            {{-- رابط إشعار واتساب --}}
                                            @if($whatsappLink)
                                                <a href="{{ $shipment->whatsappUrl }}" target="_blank"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">send</span>
                                                    {{ $shipment->is_returned ? 'إشعار التاجر بالمرتجع' : 'إشعار المستلم' }}
                                                </a>
                                            @endif

                                            {{-- خط فاصل قبل الإجراءات السريعة --}}
                                            <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                            {{-- ================= الإجراءات السريعة المدمجة ================= --}}
                                            @if(in_array($shipment->status, ['delivered', 'returned', 'cancelled']))
                                                <div class="flex gap-3 items-center px-4 py-2 text-[11px] font-bold text-slate-400 cursor-not-allowed">
                                                    <span class="material-symbols-outlined text-[16px]">lock</span>
                                                    تم إغلاق هذا الطرد
                                                </div>

                                            @elseif($shipment->status === 'pending')
                                                <div class="flex gap-3 items-center px-4 py-2 text-[11px] font-bold text-amber-500">
                                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                                    الطرد قيد التجهيز
                                                </div>

                                            @elseif($shipment->status === 'in_transit')
                                                {{-- نموذج تأكيد الوصول السريع --}}
                                                <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" @submit="isSubmitting = true">
                                                    @csrf
                                                    <input type="hidden" name="status" value="received_at_branch">
                                                    <button type="submit" :disabled="isSubmitting"
                                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10">
                                                        <span class="material-symbols-outlined text-[18px]" x-show="!isSubmitting">inventory_2</span>
                                                        <span class="material-symbols-outlined animate-spin text-[18px]" x-show="isSubmitting">progress_activity</span>
                                                        <span x-text="isSubmitting ? 'جاري التأكيد...' : 'تأكيد وصول المستودع'"></span>
                                                    </button>
                                                </form>

                                            @else
                                                {{-- نموذج إجراءات التسليم أو الإرجاع الفوري --}}
                                                <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" x-ref="statusForm" @submit="isSubmitting = true">
                                                    @csrf
                                                    <input type="hidden" name="status" x-ref="statusInput">

                                                    @if($shipment->is_returned)
                                                        <button type="button" :disabled="isSubmitting"
                                                            @click="$refs.statusInput.value = 'delivered'; $refs.statusForm.submit(); open = false"
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                                            <span>تأكيد تسليم المرتجع للتاجر</span>
                                                        </button>
                                                    @else
                                                        <button type="button" :disabled="isSubmitting"
                                                            @click="
                                                                @if($shipment->payment_method !== 'prepaid')
                                                                    showPaymentModal = true; 
                                                                    open = false;
                                                                @else
                                                                    $refs.statusInput.value = 'delivered'; 
                                                                    $refs.statusForm.submit();
                                                                    open = false;
                                                                @endif
                                                            "
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                                                            <span>تأكيد التسليم للعميل</span>
                                                        </button>

                                                        <button type="button" :disabled="isSubmitting"
                                                            @click="$refs.statusInput.value = 'returned'; $refs.statusForm.submit(); open = false"
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                            <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                                                            <span>رفض الاستلام (إرجاع)</span>
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- ================= نافذة التحصيل المالي (Modal) الشاملة للجدول ================= --}}
                                    @if(!$shipment->is_returned && $shipment->payment_method !== 'prepaid')
                                        <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4 text-center">
                                            {{-- الخلفية المضببة --}}
                                            <div x-show="showPaymentModal" x-transition.opacity duration.300ms @click="showPaymentModal = false" class="absolute inset-0 backdrop-blur-sm bg-slate-900/40"></div>

                                            {{-- هيكل النافذة المربوط بالنموذج الداخلي --}}
                                            <div x-show="showPaymentModal" x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                class="relative p-6 w-full max-w-sm text-center bg-white rounded-3xl border shadow-2xl border-slate-100 dark:bg-boxdark dark:border-boxdark-2 z-[10000]">

                                                <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 text-rose-500 bg-rose-50 rounded-full animate-bounce dark:bg-rose-500/10 dark:text-rose-400">
                                                    <span class="material-symbols-outlined text-[40px]">payments</span>
                                                </div>

                                                <h3 class="mb-2 text-lg font-black text-slate-800 dark:text-white font-headline">تنبيه تحصيل مالي!</h3>
                                                <p class="mb-6 text-xs font-bold leading-relaxed text-slate-500 dark:text-slate-400">
                                                    هذا الطرد غير مدفوع مسبقاً. الرجاء استلام المبلغ التالي من العميل قبل تأكيد عملية التسليم السريع.
                                                </p>

                                                <div class="p-5 mb-6 bg-rose-50 rounded-2xl border border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20">
                                                    <p class="mb-1 text-[10px] font-black text-rose-600/70 dark:text-rose-400">المبلغ المطلوب تحصيله</p>
                                                    <p class="font-mono text-3xl font-black text-rose-700 dir-ltr dark:text-rose-300">
                                                        {{ number_format($shipment->total_amount, 0) }}
                                                    </p>
                                                </div>

                                                <div class="flex gap-2">
                                                    <button type="button" @click="showPaymentModal = false"
                                                        class="flex-1 h-12 text-xs font-black rounded-xl transition-colors text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-boxdark-2 dark:text-slate-300 dark:hover:bg-gray-600">
                                                        تراجع
                                                    </button>
                                                    <button type="button" @click="$refs.statusInput.value = 'delivered'; $refs.statusForm.submit(); showPaymentModal = false"
                                                        class="flex-[2] h-12 bg-emerald-500 text-white hover:bg-emerald-600 rounded-xl font-black text-xs shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                                                        <span class="material-symbols-outlined text-[18px]">verified</span>
                                                        تم استلام المبلغ والتسليم
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </td>
                                                        </tr>
                        @empty
                                <tr>
                                    <td colspan="8" class="py-24 text-center">
                                        <div class="flex flex-col gap-4 justify-center items-center">
                                            <div
                                                class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                                <span class="material-symbols-outlined text-[28px] text-gray-400">inventory_2</span>
                                            </div>

                                            <div>
                                                <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                                    لا توجد طرود واردة
                                                </h3>
                                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                    لم نعثر على أي طرود واردة في النظام حالياً.
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <tr x-show="visibleCount === 0 && {{ $shipments->count() }} > 0" x-cloak>
                                <td colspan="8" class="py-24 text-center">
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
                                                لم نعثر على طرود واردة تطابق كلمة البحث المدخلة.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                {{ $shipments->links('vendor.pagination.custom-minimal') }}

            </div>
        </div>

@endsection

@section('script')
    {{-- مكتبة html5-qrcode لمسح الباركود و QR عبر الكاميرا --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        function incomingShipmentsRegistry(parcels = [], branchCode = '') {
            return {
                searchQuery: '',
                selectedBranch: '',
                barcodeInput: '',
                selectedParcels: [],
                isScannerOpen: false,
                html5QrCode: null,
                scanFeedback: { message: '', type: '' },
                feedbackTimeout: null,
                parcels: parcels || [],
                branchCode: branchCode || '',
                isSubmitting: false,
                visibleCount: {{ $shipments->count() }},

                init() {
                    this.updateVisibility();
                },

                playBeep(isSuccess = true) {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.type = isSuccess ? 'sine' : 'sawtooth';
                        osc.frequency.setValueAtTime(isSuccess ? 880 : 240, ctx.currentTime);
                        gain.gain.setValueAtTime(0.15, ctx.currentTime);
                        osc.start();
                        osc.stop(ctx.currentTime + (isSuccess ? 0.12 : 0.25));
                    } catch (e) {}
                },

                showFeedback(message, type = 'success') {
                    this.scanFeedback = { message, type };
                    if (this.feedbackTimeout) clearTimeout(this.feedbackTimeout);
                    this.feedbackTimeout = setTimeout(() => {
                        this.scanFeedback = { message: '', type: '' };
                    }, 4000);
                },

                // اللوجيك الذكي لمسح الباركود
                handleBarcodeScan(rawValue) {
                    if (!rawValue || !rawValue.trim()) return;

                    let scanned = rawValue.trim();

                    // معالجة الروابط في حال تم مسح رابط كامل
                    if (scanned.includes('/')) {
                        const parts = scanned.replace(/\/$/, '').split('/');
                        scanned = parts[parts.length - 1].split('?')[0];
                    }

                    // تنظيف علامة #
                    const cleanScanned = scanned.replace(/^#/, '');

                    // إضافة أصفار لتطابق 001 أو 01
                    let paddedNumber = cleanScanned.padStart(3, '0');
                    const numericScanned = cleanScanned.replace(/^0+/, '');

                    let expectedBondNumber = cleanScanned;
                    if (!cleanScanned.includes('-') && this.branchCode) {
                        expectedBondNumber = this.branchCode + '-' + paddedNumber;
                    }

                    // البحث عن الطرد المطابق
                    const matchedParcel = this.parcels.find(p => {
                        const pId = String(p.id);
                        const pBond = String(p.bond_number || '').replace(/^#/, '');
                        const pUuid = String(p.uuid || '');
                        const pCode = String(p.code || '');

                        return (
                            pId === cleanScanned ||
                            pBond === cleanScanned ||
                            pBond === expectedBondNumber ||
                            pBond.endsWith('-' + cleanScanned) ||
                            pBond.endsWith('-' + paddedNumber) ||
                            (numericScanned && pBond.endsWith('-' + numericScanned)) ||
                            pUuid === cleanScanned ||
                            pCode === cleanScanned
                        );
                    });

                    if (matchedParcel) {
                        let displayNum = matchedParcel.bond_number || matchedParcel.id;
                        if (displayNum.includes('-')) {
                            displayNum = displayNum.split('-').pop();
                        }

                        // التحقق من حالة الطرد
                        if (matchedParcel.status !== 'in_transit') {
                            this.playBeep(false);
                            if (matchedParcel.status === 'received_at_branch') {
                                this.showFeedback(`الطرد رقم #${displayNum} مستلم في المستودع مسبقاً`, 'info');
                            } else if (matchedParcel.status === 'delivered') {
                                this.showFeedback(`الطرد رقم #${displayNum} تم تسليمه للعميل مسبقاً`, 'info');
                            } else {
                                this.showFeedback(`الطرد رقم #${displayNum} غير مؤهل للاستلام (حالته: ${matchedParcel.status_label || matchedParcel.status})`, 'error');
                            }
                            this.barcodeInput = '';
                            return;
                        }

                        const alreadySelected = this.selectedParcels.includes(matchedParcel.id);

                        if (!alreadySelected) {
                            this.selectedParcels.push(matchedParcel.id);
                            this.playBeep(true);
                            this.showFeedback(`تم تحديد الطرد رقم #${displayNum} (${matchedParcel.customer_name || 'بدون اسم'}) للاستلام بنجاح`, 'success');

                            // التمرير إلى السطر المحدد إن كان معروضاً في الصفحة
                            this.$nextTick(() => {
                                const rowElem = document.getElementById('shipment-row-' + matchedParcel.id) || document.getElementById('shipment-card-' + matchedParcel.id);
                                if (rowElem) {
                                    rowElem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            });
                        } else {
                            this.playBeep(true);
                            this.showFeedback(`الطرد رقم #${displayNum} محدد بالفعل في القائمة`, 'info');
                        }
                    } else {
                        this.playBeep(false);
                        this.showFeedback(`لم يتم العثور على طرد يطابق الكود: (${cleanScanned})`, 'error');
                    }

                    this.barcodeInput = '';
                },

                // تشغيل كاميرا المسح
                startCameraScanner() {
                    this.isScannerOpen = true;
                    this.$nextTick(() => {
                        if (!this.html5QrCode) {
                            this.html5QrCode = new Html5Qrcode("qr-reader");
                        }
                        this.html5QrCode.start(
                            { facingMode: "environment" },
                            {
                                fps: 10,
                                qrbox: { width: 230, height: 230 }
                            },
                            (decodedText) => {
                                this.handleBarcodeScan(decodedText);
                            },
                            (errorMessage) => {}
                        ).catch((err) => {
                            this.showFeedback('تعذر الوصول إلى الكاميرا، تأكد من منح الصلاحية للمتصفح.', 'error');
                        });
                    });
                },

                // إيقاف الكاميرا
                stopCameraScanner() {
                    if (this.html5QrCode && this.html5QrCode.isScanning) {
                        this.html5QrCode.stop().then(() => {
                            this.isScannerOpen = false;
                        }).catch(() => {
                            this.isScannerOpen = false;
                        });
                    } else {
                        this.isScannerOpen = false;
                    }
                },

                toggleParcel(id) {
                    const index = this.selectedParcels.indexOf(id);
                    if (index > -1) {
                        this.selectedParcels.splice(index, 1);
                    } else {
                        this.selectedParcels.push(id);
                    }
                },

                selectAllFiltered() {
                    let addedCount = 0;
                    this.parcels.forEach(p => {
                        if (p.status !== 'in_transit') return;

                        const matchesSearch = this.searchQuery === '' ||
                            (p.id + ' ' + p.bond_number + ' ' + p.code + ' ' + p.customer_name).toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesBranch = this.selectedBranch === '' ||
                            p.branch_name === this.selectedBranch;

                        if (matchesSearch && matchesBranch) {
                            if (!this.selectedParcels.includes(p.id)) {
                                this.selectedParcels.push(p.id);
                                addedCount++;
                            }
                        }
                    });

                    if (addedCount > 0) {
                        this.playBeep(true);
                        this.showFeedback(`تم تحديد ${addedCount} طرد للاستلام بنجاح`, 'success');
                    } else if (this.selectedParcels.length > 0) {
                        this.showFeedback(`جميع الطرود المعروضة محددة بالفعل`, 'info');
                    } else {
                        this.playBeep(false);
                        this.showFeedback(`لا توجد طرود في الطريق متاحة للاستلام في هذا العرض`, 'info');
                    }
                },

                submitBatchReceive() {
                    if (this.selectedParcels.length === 0 || this.isSubmitting) return;
                    const form = document.getElementById('receiveParcelsForm');
                    if (form) {
                        this.isSubmitting = true;
                        form.submit();
                    }
                },

                showRow(code, senderName, senderPhone, receiverName, receiverPhone, source) {
                    const query = this.searchQuery.toLowerCase().trim();
                    const branchMatch = this.selectedBranch === '' || String(source || '') === this.selectedBranch;

                    if (!query) {
                        return branchMatch;
                    }

                    const searchMatch = String(code || '').toLowerCase().includes(query)
                        || String(senderName || '').toLowerCase().includes(query)
                        || String(senderPhone || '').toLowerCase().includes(query)
                        || String(receiverName || '').toLowerCase().includes(query)
                        || String(receiverPhone || '').toLowerCase().includes(query)
                        || String(source || '').toLowerCase().includes(query);

                    return branchMatch && searchMatch;
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.incoming-shipment-row:not([style*="display: none"])').length;
                    });
                }
            };
        }
    </script>
@endsection