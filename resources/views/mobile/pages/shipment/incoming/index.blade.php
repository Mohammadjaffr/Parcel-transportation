@extends('mobile.layouts.app')

@section('title', 'الطرود الواردة')

@section('content')
@php
    // استخراج الفروع المصدر للفلترة السريعة
    $uniqueBranches = $shipments->map(function($shipment) {
        return $shipment->senderBranch?->name ?? $shipment->senderOfficeBranch?->name ?? 'فرع غير محدد';
    })->filter()->unique()->values();

    // تجهيز بيانات الطرود التي في الطريق لـ Alpine.js للتعرف السريع عليها
    $alpineParcels = collect();
    if (isset($inTransitParcels) && $inTransitParcels->isNotEmpty()) {
        $alpineParcels = $inTransitParcels->map(function($p) {
            return [
                'id' => $p->id,
                'uuid' => $p->uuid ?? '',
                'bond_number' => $p->bond_number ?? '',
                'code' => $p->code ?? '',
                'status' => $p->status ?? 'in_transit',
                'sender_branch' => $p->senderBranch?->name ?? $p->senderOfficeBranch?->name ?? 'غير محدد',
                'receiver_name' => $p->receiverCustomer?->name ?? '',
                'sender_name' => $p->senderCustomer?->name ?? '',
                'amount' => (float)$p->total_amount,
            ];
        });
    }

    $currentBranchCode = auth()->user()->branch?->code ?? '';
@endphp

<div x-data="incomingMobileShipmentsRegistry({{ $alpineParcels->toJson() }}, '{{ $currentBranchCode }}')" class="flex relative flex-col gap-5 px-4 pb-32 min-h-screen bg-slate-50/50">

    {{-- ================= نافذة الكاميرا للموبايل (Camera Scanner Modal) ================= --}}
    <div x-show="isScannerOpen" x-cloak x-transition.opacity
        class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-3">
        
        <div @click.away="stopCameraScanner()"
            class="w-full max-w-md bg-white rounded-[2.5rem] p-5 shadow-2xl border border-slate-100 flex flex-col gap-4">
            
            {{-- هيدر نافذة الكاميرا --}}
            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600">
                        <span class="material-symbols-outlined text-[22px]">qr_code_scanner</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 font-headline">مسح باركود استلام الطرد</h3>
                        <p class="text-[10px] font-bold text-slate-400">وجه الكاميرا نحو باركود السند لاستلامه فورياً</p>
                    </div>
                </div>
                <button type="button" @click="stopCameraScanner()"
                    class="flex justify-center items-center w-9 h-9 rounded-full bg-slate-100 text-slate-500 active:scale-90">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            {{-- شاشة الكاميرا --}}
            <div class="overflow-hidden relative rounded-3xl border-2 shadow-inner bg-slate-950 aspect-square border-slate-100">
                <div id="mobile-incoming-qr-reader" class="w-full h-full"></div>
            </div>

            {{-- إشعار فوري داخل نافذة الكاميرا عند المسح --}}
            <template x-if="scanFeedback.message">
                <div x-transition class="flex gap-2.5 items-center p-3.5 text-xs font-bold rounded-2xl"
                    :class="{
                        'bg-emerald-50 text-emerald-700 border border-emerald-200': scanFeedback.type === 'success',
                        'bg-amber-50 text-amber-700 border border-amber-200': scanFeedback.type === 'info',
                        'bg-rose-50 text-rose-700 border border-rose-200': scanFeedback.type === 'error'
                    }">
                    <span class="material-symbols-outlined text-[20px] shrink-0"
                        x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                    <span class="leading-relaxed" x-text="scanFeedback.message"></span>
                </div>
            </template>

            {{-- شريط سفلي داخل المودال يوضح العدد وزر الإغلاق --}}
            <div class="flex justify-between items-center pt-2">
                <div class="flex gap-2 items-center">
                    <span class="text-xs font-bold text-slate-500">تم تحديد:</span>
                    <span class="px-3 py-1 text-xs font-black rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200"
                        x-text="selectedParcels.length + ' طرد'"></span>
                </div>
                <button type="button" @click="stopCameraScanner()"
                    class="px-5 py-2.5 text-xs font-black text-white rounded-2xl transition-transform bg-slate-900 active:scale-95">
                    إتمام المسح
                </button>
            </div>
        </div>
    </div>

    {{-- نموذج الإرسال الخفي لاستلام الطرود دفعة واحدة --}}
    <form id="receiveParcelsFormMobile" action="{{ route('shipment.incoming.batchReceive') }}" method="POST" class="hidden">
        @csrf
        <template x-for="parcelId in selectedParcels" :key="parcelId">
            <input type="hidden" name="parcel_ids[]" :value="parcelId">
        </template>
    </form>

    {{-- ================= الرأس (Header) ================= --}}
    <div class="flex justify-between items-center mt-2">
        <div class="flex gap-3 items-center">
            {{-- زر الرجوع --}}
            <a href="javascript:history.back()"
                class="flex justify-center items-center w-11 h-11 bg-white rounded-2xl border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-emerald-600 active:scale-90">
                <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
            </a>
            <div class="flex flex-col">
                <h1 class="text-2xl font-black font-headline text-slate-800">الطرود الواردة</h1>
                <p class="mt-0.5 text-xs font-bold text-slate-500 flex items-center gap-1.5">
                    <span>إجمالي <span class="font-black text-emerald-600">{{ $shipments->total() ?? 0 }}</span> طرد</span>
                    <template x-if="selectedParcels.length > 0">
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            محدد للاستلام: <span x-text="selectedParcels.length"></span>
                        </span>
                    </template>
                </p>
            </div>
        </div>

        {{-- زر استلام الطرود بالأعلى عند تحديد أي طرد --}}
        <template x-if="selectedParcels.length > 0">
            <button type="button" @click="submitBatchReceive()" :disabled="isSubmitting"
                class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white font-black text-xs rounded-2xl shadow-lg shadow-emerald-600/30 active:scale-95 transition-all">
                <template x-if="!isSubmitting">
                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                </template>
                <template x-if="isSubmitting">
                    <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </template>
                <span>استلام (<span x-text="selectedParcels.length"></span>)</span>
            </button>
        </template>

        <template x-if="selectedParcels.length === 0">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-[1rem] flex items-center justify-center border border-emerald-100 shrink-0">
                <span class="material-symbols-outlined text-[26px]">inventory_2</span>
            </div>
        </template>
    </div>

    {{-- ================= شريط المسح الضوئي والبحث الذكي ================= --}}
    <div class="bg-white rounded-[24px] p-4 border border-slate-200/70 shadow-sm space-y-3">
        {{-- حقل إدخال الباركود وزر الكاميرا --}}
        <div class="flex gap-2 items-center">
            <div class="relative flex-1">
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[20px]">barcode_scanner</span>
                <input type="text" x-model="barcodeInput" @keydown.enter.prevent="handleBarcodeScan(barcodeInput)"
                    placeholder="امسح باركود السند أو اكتب رقمه واضغط Enter..."
                    class="w-full pr-10 pl-3 h-12 text-xs font-bold bg-slate-50 border border-slate-200/80 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-slate-800 placeholder-slate-400 transition-all">
            </div>

            <button type="button" @click="startCameraScanner()"
                class="flex justify-center items-center w-12 h-12 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-2xl hover:bg-emerald-100 active:scale-90 transition-all shadow-sm"
                title="فتح الكاميرا للمسح">
                <span class="material-symbols-outlined text-[22px]">photo_camera</span>
            </button>
        </div>

        {{-- تنبيه نتائج المسح الفوري --}}
        <template x-if="scanFeedback.message">
            <div x-transition class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold"
                :class="{
                    'bg-emerald-50 text-emerald-700 border border-emerald-200': scanFeedback.type === 'success',
                    'bg-amber-50 text-amber-700 border border-amber-200': scanFeedback.type === 'info',
                    'bg-rose-50 text-rose-700 border border-rose-200': scanFeedback.type === 'error'
                }">
                <span class="material-symbols-outlined text-[18px] shrink-0"
                    x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                <span class="leading-relaxed" x-text="scanFeedback.message"></span>
            </div>
        </template>

        {{-- أدوات البحث والفلترة السريعة --}}
        <div class="grid grid-cols-1 gap-2 pt-1">
            <div class="relative">
                <input type="text" x-model="searchQuery" placeholder="بحث برقم السند، أو هاتف المستلم..."
                    class="pr-10 pl-4 w-full h-11 text-xs font-bold bg-slate-50 border border-slate-200/70 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-700 placeholder-slate-400">
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[18px]">search</span>
            </div>

            <div class="flex gap-2">
                @if($uniqueBranches->isNotEmpty())
                    <select x-model="selectedBranch"
                        class="flex-1 h-11 px-3 text-xs font-bold bg-slate-50 border border-slate-200/70 rounded-2xl outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-700">
                        <option value="">جميع فروع المصدر</option>
                        @foreach($uniqueBranches as $bName)
                            <option value="{{ $bName }}">{{ $bName }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="button" @click="selectAllFiltered()"
                    class="px-3 h-11 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl active:scale-95 transition-all flex items-center gap-1 shrink-0">
                    <span class="material-symbols-outlined text-[16px]">select_all</span>
                    <span>تحديد المعروض</span>
                </button>

                <template x-if="selectedParcels.length > 0">
                    <button type="button" @click="clearSelection()"
                        class="px-3 h-11 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs rounded-2xl active:scale-95 transition-all flex items-center gap-1 shrink-0">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>إلغاء</span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- ================= شريط الفلترة حسب الحالة ================= --}}
    <div class="flex overflow-x-auto gap-2 pb-1 custom-scrollbar snap-x snap-mandatory">
        {{-- الكل --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}"
            class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ !request('status') ? 'bg-slate-800 text-white border-slate-800 shadow-[0_4px_12px_rgba(30,41,59,0.2)]' : 'bg-white text-slate-500 border-slate-100 hover:bg-slate-50' }}">
            الكل
        </a>

        {{-- 1. قيد التجهيز (بالمصدر) --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => null]) }}"
            class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-[0_4px_12px_rgba(245,158,11,0.2)]' : 'bg-white text-amber-600 border-amber-100 hover:bg-amber-50' }}">
            قيد التجهيز بالمصدر
        </a>

        {{-- 2. في الطريق إلينا --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => 'in_transit', 'page' => null]) }}"
            class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'in_transit' ? 'bg-blue-500 text-white border-blue-500 shadow-[0_4px_12px_rgba(59,130,246,0.2)]' : 'bg-white text-blue-600 border-blue-100 hover:bg-blue-50' }}">
            في الطريق إلينا
        </a>

        {{-- 3. بالمستودع (وصل فرعنا) --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => 'received_at_branch', 'page' => null]) }}"
            class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'received_at_branch' ? 'bg-purple-500 text-white border-purple-500 shadow-[0_4px_12px_rgba(168,85,247,0.2)]' : 'bg-white text-purple-600 border-purple-100 hover:bg-purple-50' }}">
            بالمستودع (جاهز للتسليم)
        </a>

        {{-- 5. تم التسليم --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => 'delivered', 'page' => null]) }}"
            class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'delivered' ? 'bg-emerald-500 text-white border-emerald-500 shadow-[0_4px_12px_rgba(16,185,129,0.2)]' : 'bg-white text-emerald-600 border-emerald-100 hover:bg-emerald-50' }}">
            تم تسليمه للعميل
        </a>

        {{-- 6. مرتجع --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => 'returned', 'page' => null]) }}"
            class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'returned' ? 'bg-rose-500 text-white border-rose-500 shadow-[0_4px_12px_rgba(244,63,94,0.2)]' : 'bg-white text-rose-600 border-rose-100 hover:bg-rose-50' }}">
            مرتجع
        </a>

        {{-- 7. ملغي --}}
        <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled', 'page' => null]) }}"
            class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'cancelled' ? 'bg-slate-500 text-white border-slate-500 shadow-[0_4px_12px_rgba(100,116,139,0.2)]' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50' }}">
            ملغي
        </a>
    </div>

    {{-- ================= قائمة الطرود ================= --}}
    <div class="space-y-4">
        @forelse($shipments as $shipment)
            @php
                $isInTransit = ($shipment->status === 'in_transit');
                $sourceBranchName = $shipment->senderBranch?->name ?? $shipment->senderOfficeBranch?->name ?? '';
            @endphp
            <div x-show="(searchQuery === '' || '{{ $shipment->code }}'.includes(searchQuery) || '{{ $shipment->bond_number }}'.includes(searchQuery) || '{{ $shipment->receiverCustomer?->phone }}'.includes(searchQuery) || '{{ $shipment->receiverCustomer?->name }}'.includes(searchQuery)) && (selectedBranch === '' || '{{ $sourceBranchName }}' === selectedBranch)"
                @if($isInTransit)
                    @click="if (!$event.target.closest('a, button, select, input, .dropdown-action')) { toggleParcel({{ $shipment->id }}) }"
                    :class="isSelected({{ $shipment->id }}) ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/20' : 'bg-white border-slate-200/60'"
                @else
                    class="bg-white border-slate-200/60"
                @endif
                class="rounded-[24px] border shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] overflow-visible transition-all duration-300 relative group">

                {{-- شريط لوني علوي خفيف يعطي طابعاً مميزاً للوارد --}}
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-400/80 to-blue-400/80 rounded-t-[24px] opacity-70"></div>

                {{-- ================= 1. الرأس (Header) ================= --}}
                <div class="flex justify-between items-start p-4 pb-3">
                    <div class="flex gap-3 items-center">
                        {{-- خانة الاختيار إن كان في الطريق --}}
                        @if($isInTransit)
                            <div class="flex items-center justify-center shrink-0" @click.stop>
                                <input type="checkbox" :checked="isSelected({{ $shipment->id }})" @change="toggleParcel({{ $shipment->id }})"
                                    class="w-5 h-5 rounded-lg text-emerald-600 focus:ring-emerald-500/30 border-slate-300 cursor-pointer">
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-[14px] bg-slate-50 flex items-center justify-center border border-slate-100/80 shrink-0">
                                <span class="material-symbols-outlined text-slate-500 text-[20px]">inventory_2</span>
                            </div>
                        @endif

                        <div class="flex flex-col">
                            <h3 class="text-sm font-black tracking-tight text-slate-900 font-headline flex items-center gap-1.5">
                                <span>{{ $shipment->code }}</span>
                                @if($shipment->bond_number)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono">
                                        #{{ $shipment->bond_number }}
                                    </span>
                                @endif
                            </h3>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">schedule</span>
                                {{ $shipment->created_at->format('Y/m/d - H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-1.5 items-center">
                        {{-- ================= شارة الحالة الشاملة الذكية ================= --}}
                        @if ($shipment->is_returned && !in_array($shipment->status, ['delivered', 'cancelled']))
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-600 ring-1 ring-rose-500/30 ring-inset flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px] animate-pulse">keyboard_return</span>
                                مرتجع
                            </span>
                        @elseif($shipment->status == 'pending')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-600 ring-1 ring-amber-500/20 ring-inset">قيد التجهيز</span>
                        @elseif($shipment->status == 'in_transit')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 ring-1 ring-blue-500/20 ring-inset flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">local_shipping</span>
                                في الطريق
                            </span>
                        @elseif($shipment->status == 'received_at_branch')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20 ring-inset flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                بالمستودع
                            </span>
                        @elseif($shipment->status == 'out_for_delivery')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-50 text-purple-600 ring-1 ring-purple-500/20 ring-inset">خرج للتوصيل</span>
                        @elseif($shipment->status == 'delivered')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20 ring-inset flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">done_all</span>
                                تم التسليم
                            </span>
                        @elseif($shipment->status == 'returned')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-600 ring-1 ring-rose-500/20 ring-inset">مرتجع</span>
                        @elseif($shipment->status == 'cancelled')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-50 text-slate-600 ring-1 ring-slate-500/20 ring-inset">ملغي</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-gray-50 text-gray-500 ring-1 ring-gray-500/20 ring-inset">{{ $shipment->status }}</span>
                        @endif

                        {{-- ================= قائمة الثلاث نقاط ================= --}}
                        <div x-data="{ openMenu: false }" class="relative dropdown-action" @click.stop>
                            <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                class="flex justify-center items-center w-8 h-8 rounded-full transition-colors text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                <span class="material-symbols-outlined text-[18px]">more_vert</span>
                            </button>

                            <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                class="absolute top-full left-0 mt-1.5 w-44 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden py-1.5">
                                <a href="{{ route('shipment.incoming.show', $shipment->id) }}"
                                    class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-emerald-600">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    {{ $shipment->is_returned ? 'تفاصيل المرتجع' : 'التفاصيل والتسليم' }}
                                </a>

                                <a href="{{ route('receipt.generate', ['type' => 'Shipment', 'id' => $shipment->id]) }}"
                                    target="_blank"
                                    class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-emerald-600">
                                    <span class="material-symbols-outlined text-[18px]">print</span>
                                    طباعة السند
                                </a>

                                @php
                                    $targetCustomer = $shipment->is_returned ? $shipment->senderCustomer : $shipment->receiverCustomer;
                                @endphp
                                @if ($targetCustomer && $targetCustomer->phone && !empty($shipment->whatsappUrl))
                                    <div class="mx-3 my-1 h-px bg-slate-100"></div>
                                    <a href="{{ $shipment->whatsappUrl }}" target="_blank"
                                        class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold text-slate-600 hover:bg-emerald-50 hover:text-emerald-700">
                                        <svg class="w-[15px] h-[15px] fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                        {{ $shipment->is_returned ? 'إشعار التاجر' : 'إشعار المستلم' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= 2. الفاصل المقطّع ================= --}}
                <div class="flex overflow-hidden relative items-center h-3">
                    <div class="absolute -right-2 w-3.5 h-3.5 rounded-full border-l shadow-inner bg-slate-50/50 border-slate-200/60"></div>
                    <div class="w-full border-t-[1.5px] border-dashed border-slate-200/70"></div>
                    <div class="absolute -left-2 w-3.5 h-3.5 rounded-full border-r shadow-inner bg-slate-50/50 border-slate-200/60"></div>
                </div>

                {{-- ================= 3. جسد البطاقة (The Journey & Data) ================= --}}
                <div class="p-4 pt-3 space-y-4">
                    <div class="flex gap-3 justify-between items-start">
                        {{-- المصدر --}}
                        <div class="flex-1">
                            <p class="text-[9px] font-black text-slate-400 mb-0.5">وارد من</p>
                            <p class="text-xs font-black text-slate-800 truncate">
                                {{ $shipment->senderCustomer?->name ?? 'مصدر غير محدد' }}
                            </p>
                            <x-phone-number :value="$shipment->senderCustomer?->phone ?? '---'" class="text-[12px] font-bold text-slate-500" />
                            <div class="mt-1">
                                <span class="text-[10px] font-black text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                                    {{ $shipment->senderBranch->name ?? '---' }}
                                </span>
                            </div>
                        </div>

                        {{-- سهم الانتقال --}}
                        <div class="flex flex-col items-center justify-center pt-2 text-slate-300">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        </div>

                        {{-- المستلم --}}
                        <div class="flex-1 text-left">
                            <p class="text-[9px] font-black text-slate-400 mb-0.5">المستلم</p>
                            <p class="text-xs font-black text-slate-800 truncate">
                                {{ $shipment->receiverCustomer?->name ?? 'غير مسجل' }}
                            </p>
                            <x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'" class="text-[12px] font-bold text-slate-500 justify-end" />
                            <div class="mt-1">
                                <span class="text-[10px] font-bold text-slate-400">
                                    {{ $shipment->items_count ?? 1 }} قطعة
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- ================= 4. كبسولة المالية ================= --}}
                    <div class="bg-slate-800 rounded-[16px] p-3 flex justify-between items-center shadow-sm">
                        <div class="flex gap-2 items-center">
                            <div class="flex justify-center items-center w-8 h-8 rounded-xl bg-slate-700 text-slate-300">
                                <span class="material-symbols-outlined text-[16px]">wallet</span>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400">طريقة الدفع</p>
                                <p class="text-[11px] font-bold {{ $shipment->payment_method == 'prepaid' ? 'text-emerald-400' : 'text-white' }}">
                                    @if ($shipment->payment_method == 'prepaid')
                                        خالص (مدفوع)
                                    @elseif($shipment->payment_method == 'cod')
                                        الدفع عند الاستلام
                                    @elseif($shipment->payment_method == 'partial_payment')
                                        دفع جزئي
                                    @else
                                        آجل
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="text-left">
                            <p class="text-[9px] font-bold text-slate-400">المطلوب</p>
                            <p class="text-base font-black tracking-tight leading-none {{ $shipment->payment_method == 'prepaid' ? 'text-emerald-400' : 'text-amber-400' }} font-headline">
                                {{ $shipment->payment_method == 'prepaid' ? '0' : number_format($shipment->total_amount, 0) }}
                                <span class="text-[9px] font-bold text-slate-400">ريال</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 bg-white rounded-[24px] border-2 border-dashed border-slate-200 mt-4 shadow-sm">
                <div class="relative mb-3">
                    <div class="absolute inset-0 rounded-full blur-xl bg-emerald-500/20"></div>
                    <div class="w-14 h-14 bg-gradient-to-br from-slate-50 to-slate-100 rounded-[18px] flex items-center justify-center border border-white shadow-sm relative z-10">
                        <span class="material-symbols-outlined text-[28px] text-slate-300">inbox</span>
                    </div>
                </div>
                <h3 class="text-sm font-black text-slate-700 font-headline">لا توجد طرود واردة</h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">لم نعثر على أي طرود تطابق معايير البحث حالياً.</p>
            </div>
        @endforelse

        {{-- الترقيم --}}
        @if (method_exists($shipments, 'hasPages') && $shipments->hasPages())
            <div class="mt-6">
                {{ $shipments->links('vendor.pagination.mobile') }}
            </div>
        @endif
    </div>

    {{-- ================= الشريط العائم السفلي للاستلام ================= --}}
    <div x-show="selectedParcels.length > 0" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-12" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-12"
        class="fixed bottom-20 inset-x-4 z-40 bg-slate-900/95 backdrop-blur-md rounded-2xl p-3.5 shadow-2xl border border-slate-800 flex items-center justify-between text-white">
        
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black">
                <span x-text="selectedParcels.length"></span>
            </div>
            <div>
                <p class="text-xs font-black">طرود محددة للاستلام</p>
                <p class="text-[10px] text-slate-400 font-bold">تغيير الحالة إلى "بالمستودع"</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="clearSelection()"
                class="px-3 py-2 text-xs font-bold text-slate-400 hover:text-white transition-colors">
                إلغاء
            </button>
            <button type="button" @click="submitBatchReceive()" :disabled="isSubmitting"
                class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs rounded-xl shadow-lg shadow-emerald-500/30 active:scale-95 transition-all flex items-center gap-1.5">
                <template x-if="!isSubmitting">
                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                </template>
                <template x-if="isSubmitting">
                    <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </template>
                <span>استلام الآن</span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('incomingMobileShipmentsRegistry', (inTransitParcels, branchCode) => ({
            selectedParcels: [],
            searchQuery: '',
            selectedBranch: '',
            barcodeInput: '',
            isScannerOpen: false,
            html5QrCode: null,
            lastScannedCode: '',
            lastScannedTime: 0,
            scanFeedback: { message: '', type: '' },
            feedbackTimeout: null,
            isSubmitting: false,
            parcels: inTransitParcels || [],
            currentBranchCode: branchCode || '',

            init() {
                // تصفية أوتوماتيكية للطرود في الطريق
                if (new URLSearchParams(window.location.search).get('status') === 'in_transit') {
                    // بالفعل تم الفلترة
                }
            },

            isSelected(id) {
                return this.selectedParcels.includes(id);
            },

            toggleParcel(id) {
                const index = this.selectedParcels.indexOf(id);
                if (index > -1) {
                    this.selectedParcels.splice(index, 1);
                } else {
                    this.selectedParcels.push(id);
                }
            },

            clearSelection() {
                this.selectedParcels = [];
                this.showFeedback('تم إلغاء التحديد', 'info');
            },

            selectAllFiltered() {
                let addedCount = 0;
                this.parcels.forEach(p => {
                    const matchesSearch = this.searchQuery === '' || 
                        (String(p.id) + ' ' + String(p.bond_number) + ' ' + String(p.code) + ' ' + String(p.receiver_name) + ' ' + String(p.sender_name)).toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesBranch = this.selectedBranch === '' || p.sender_branch === this.selectedBranch;

                    if (matchesSearch && matchesBranch && p.status === 'in_transit') {
                        if (!this.selectedParcels.includes(p.id)) {
                            this.selectedParcels.push(p.id);
                            addedCount++;
                        }
                    }
                });

                if (addedCount > 0) {
                    this.playBeep(true);
                    this.showFeedback(`تم تحديد ${this.selectedParcels.length} طرد للاستلام`, 'success');
                } else {
                    this.showFeedback('لا توجد طرود جديدة مؤهلة للتحديد في هذا الفلتر', 'info');
                }
            },

            playBeep(isSuccess = true) {
                try {
                    if (navigator.vibrate) {
                        navigator.vibrate(isSuccess ? 80 : [100, 50, 100]);
                    }
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

            handleBarcodeScan(rawValue) {
                if (!rawValue || !rawValue.trim()) return;

                let scanned = rawValue.trim();

                const now = Date.now();
                if (scanned === this.lastScannedCode && (now - this.lastScannedTime) < 1800) {
                    return;
                }
                this.lastScannedCode = scanned;
                this.lastScannedTime = now;

                if (scanned.includes('/')) {
                    const parts = scanned.replace(/\/$/, '').split('/');
                    scanned = parts[parts.length - 1].split('?')[0];
                }

                const cleanScanned = scanned.replace(/^#/, '');
                const numericScanned = cleanScanned.replace(/^0+/, '');
                const paddedScanned = numericScanned.padStart(3, '0');

                const matchedParcel = this.parcels.find(p => {
                    const pId = String(p.id);
                    const pBond = String(p.bond_number || '').replace(/^#/, '');
                    const pBondNoZeros = pBond.replace(/^0+/, '');
                    const pUuid = String(p.uuid || '');
                    const pCode = String(p.code || '');

                    const matchesDirect = (
                        pId === cleanScanned ||
                        pId === numericScanned ||
                        (pBond !== '' && (pBond === cleanScanned || pBondNoZeros === numericScanned)) ||
                        (pUuid !== '' && pUuid === cleanScanned) ||
                        (pCode !== '' && pCode === cleanScanned)
                    );

                    const matchesSuffix = (
                        (pBond !== '' && pBond.endsWith('-' + cleanScanned)) ||
                        (pBond !== '' && pBond.endsWith('-' + paddedScanned)) ||
                        (pBond !== '' && pBond.endsWith('-' + numericScanned))
                    );

                    return matchesDirect || matchesSuffix;
                });

                if (matchedParcel) {
                    if (matchedParcel.status !== 'in_transit') {
                        this.playBeep(false);
                        const statusArabic = matchedParcel.status === 'received_at_branch' ? 'مستلم بالمستودع مسبقاً' : matchedParcel.status;
                        this.showFeedback(`الطرد #${matchedParcel.bond_number || matchedParcel.code} حالته (${statusArabic}) وليست (في الطريق)`, 'error');
                        this.barcodeInput = '';
                        return;
                    }

                    const alreadySelected = this.selectedParcels.includes(matchedParcel.id);
                    const displayNum = matchedParcel.bond_number || matchedParcel.code || matchedParcel.id;

                    if (!alreadySelected) {
                        this.selectedParcels.push(matchedParcel.id);
                        this.playBeep(true);
                        this.showFeedback(`تم تحديد السند #${displayNum} (${matchedParcel.receiver_name || 'عميل'}) للاستلام`, 'success');
                    } else {
                        this.playBeep(true);
                        this.showFeedback(`السند #${displayNum} محدد مسبقاً في قائمة الاستلام`, 'info');
                    }
                } else {
                    this.playBeep(false);
                    this.showFeedback(`لم يتم العثور على طرد (في الطريق) يطابق الكود: (${cleanScanned})`, 'error');
                }

                this.barcodeInput = '';
            },

            startCameraScanner() {
                this.isScannerOpen = true;
                this.$nextTick(() => {
                    if (!this.html5QrCode) {
                        this.html5QrCode = new Html5Qrcode("mobile-incoming-qr-reader");
                    }
                    this.html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: { width: 220, height: 220 } },
                        (decodedText) => {
                            this.handleBarcodeScan(decodedText);
                        },
                        (errorMessage) => {}
                    ).catch(() => {
                        this.showFeedback('يرجى السماح للمتصفح بالوصول إلى الكاميرا.', 'error');
                    });
                });
            },

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

            submitBatchReceive() {
                if (this.selectedParcels.length === 0) {
                    this.showFeedback('يرجى تحديد طرد واحد على الأقل للاستلام.', 'error');
                    return;
                }

                this.isSubmitting = true;
                const form = document.getElementById('receiveParcelsFormMobile');
                if (form) {
                    form.submit();
                }
            }
        }));
    });
</script>
@endsection
