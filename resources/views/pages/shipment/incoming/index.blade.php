@extends('layouts.app')

@section('title', 'الطرود الواردة')


@section('content')

    <div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl" x-data="incomingShipmentsRegistry()">

        <div class="mx-auto w-full max-w-7xl">
            <div class="flex gap-4 justify-between items-start">
                <div class="text-right">
                    <h1 class="text-2xl font-black md:text-3xl text-on-surface dark:text-white">
                        الطرود الواردة
                    </h1>
                    <p class="mt-1 text-sm font-bold text-gray-500 dark:text-bodydark">
                        إجمالي {{ $shipments->total() ?? 0 }} طرد وارد
                    </p>
                </div>
            </div>
        </div>
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
        @endphp

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

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="flex flex-col gap-4 justify-between items-stretch md:flex-row md:items-center">

                    {{-- مربع البحث --}}
                    <div
                        class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-[420px] dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">

                        <input type="text" x-model="searchQuery" @input.debounce.300ms="updateVisibility()"
                            placeholder="ابحث برقم السند، اسم العميل، أو رقم الهاتف..."
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
                            <span>{{ $shipments->count() }}</span>
                        </span>
                    </div>
                </div>
            </div>

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

                    <div class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all incoming-shipment-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm"
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

                                    <a href="{{ route('shipment.outgoing.edit', $shipment->id) }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-slate-50 hover:text-primary dark:hover:bg-boxdark-2">
                                        <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                        تعديل
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
                                            class="flex gap-3 items-center px-4 py-2 text-xs font-bold text-slate-400 cursor-not-allowed">
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
                                                class="flex gap-3 items-center px-4 py-2.5 w-full text-right text-xs font-bold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10">
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
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-right text-xs font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
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
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-right text-xs font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">task_alt</span>
                                                    <span>تأكيد التسليم للعميل</span>
                                                </button>

                                                {{-- رفض الاستلام / إرجاع --}}
                                                <button type="button" :disabled="isSubmitting"
                                                    @click="$refs.statusInput.value = 'returned'; $refs.statusForm.submit(); menuOpen = false"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-right text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">
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

            {{-- ====================== Desktop View ====================== --}}
            <div class="hidden overflow-visible w-full lg:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr
                            class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
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

                                                <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group incoming-shipment-row"
                                                    x-show="showRow(
                                                                        @js($shipment->code ?? $shipment->bond_number),
                                                                        @js($shipment->senderCustomer?->name),
                                                                        @js($shipment->senderCustomer?->phone),
                                                                        @js($shipment->receiverCustomer?->name),
                                                                        @js($shipment->receiverCustomer?->phone),
                                                                        @js($source)
                                                                    )">

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

                                            {{-- رابط تعديل الشحنة --}}
                                            <a href="{{ route('shipment.outgoing.edit', $shipment->id) }}"
                                                class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-slate-50 hover:text-primary dark:hover:bg-boxdark-2">
                                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                                تعديل
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
                                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-right text-xs font-bold text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10">
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
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-right text-xs font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
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
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-right text-xs font-bold text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                                                            <span>تأكيد التسليم للعميل</span>
                                                        </button>

                                                        <button type="button" :disabled="isSubmitting"
                                                            @click="$refs.statusInput.value = 'returned'; $refs.statusForm.submit(); open = false"
                                                            class="flex gap-3 items-center px-4 py-2.5 w-full text-right text-xs font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10">
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
                                    <td colspan="7" class="py-24 text-center">
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
                                <td colspan="7" class="py-24 text-center">
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
                @if(method_exists($shipments, 'hasPages') && $shipments->hasPages())
                    <div
                        class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                        {{ $shipments->links() }}
                    </div>
                @endif
            </div>
        </div>

@endsection

@section('script')
    <script>
        function incomingShipmentsRegistry() {
            return {
                searchQuery: '',
                visibleCount: {{ $shipments->count() }},

                init() {
                    this.updateVisibility();
                },

                showRow(code, senderName, senderPhone, receiverName, receiverPhone, source) {
                    const query = this.searchQuery.toLowerCase().trim();

                    if (!query) {
                        return true;
                    }

                    return String(code || '').toLowerCase().includes(query)
                        || String(senderName || '').toLowerCase().includes(query)
                        || String(senderPhone || '').toLowerCase().includes(query)
                        || String(receiverName || '').toLowerCase().includes(query)
                        || String(receiverPhone || '').toLowerCase().includes(query)
                        || String(source || '').toLowerCase().includes(query);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.incoming-shipment-row:not([style*="display: none"])').length;
                    });
                }
            }
        }
    </script>
@endsection