@extends('layouts.app')
@section('title', 'تفاصيل الطرد #' . $shipment->bond_number)

@section('content')

    {{-- Payment Modals --}}
    @include('components.modals.payment-modal')
    @include('components.modals.payments-list-modal')

    <div x-data="{
        isgreenModalOpen: false,
        greenTitle: '',
        greenMessage: '',
    }"
        @open-green-modal.window="
            greenTitle = $event.detail.title;
            greenMessage = $event.detail.message;
            isgreenModalOpen = true;
        ">
    </div>

    @php
        $paidAmount = $shipment->payments->sum('amount');
        $remainingAmount = $shipment->total_amount - $paidAmount;
        $isUnpaid = $remainingAmount > 0;

        // حساب مبلغ الإرجاع للمرتجعات
        $refundAmount = 0;
        if ($shipment->payment_method === 'prepaid') {
            $refundAmount = $shipment->total_amount;
        } elseif ($shipment->payment_method === 'partial_payment') {
            $refundAmount = $shipment->partial_amount;
        }
    @endphp

    <div class="pb-10 space-y-6 font-outfit" dir="rtl" x-data="{
        showPaymentModal: false,
        showRefundModal: false,
        paymentModalOpen: false,
        paymentData: { shipmentId: {{ $shipment->id }}, amount: {{ $remainingAmount }}, maxAmount: {{ $remainingAmount }}, paymentType: 'cash', referenceNumber: '', notes: '' },
        paymentsListModalOpen: false,
        paymentsList: {{ Js::from($shipment->payments) }},
        returnModalOpen: false,
        returnReason: '',
        returnLoading: false,
        cancelModalOpen: false,
        cancelReason: '',
        cancelLoading: false,
        unlinkModalOpen: false,
        selectedShipmentId: null,
        selectedBondNumber: '',
        unlinkLoading: false,
        openUnlinkModal(shipmentId, bondNumber) {
            this.selectedShipmentId = shipmentId;
            this.selectedBondNumber = bondNumber;
            this.unlinkModalOpen = true;
        }
    }">

        {{-- ================= كرت رأس الصفحة (معلومات الطرد الأساسية) ================= --}}
        <div
            class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark-2 dark:border-boxdark relative z-20">
            <div class="flex flex-col gap-6 justify-between xl:flex-row xl:items-center">

                <div class="flex gap-4 items-center">
                    <div
                        class="flex justify-center items-center w-16 h-16 rounded-[1.2rem] bg-primary/10 text-primary shrink-0 dark:bg-primary/20">
                        <span class="material-symbols-outlined text-[32px]">inventory_2</span>
                    </div>
                    <div>
                        <div class="flex flex-wrap gap-3 items-center mb-1.5">
                            <h1 class="text-2xl font-black text-gray-900 dark:text-white font-headline">
                                الطرد #{{ $shipment->bond_number }}
                            </h1>

                            {{-- ================= حالة الطرد الذكية ================= --}}
                            @php
                                $statusColors = [
                                    'pending' =>
                                        'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                    'in_transit' =>
                                        'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
                                    'received_at_branch' =>
                                        'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
                                    'out_for_delivery' =>
                                        'bg-teal-50 text-teal-600 border-teal-200 dark:bg-teal-500/10 dark:text-teal-400 dark:border-teal-500/20',
                                    'delivered' =>
                                        'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                    'cancelled' =>
                                        'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/20',
                                    'returned' =>
                                        'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
                                ];

                                $statusIcons = [
                                    'pending' => 'schedule',
                                    'in_transit' => 'local_shipping',
                                    'received_at_branch' => 'storefront',
                                    'out_for_delivery' => 'two_wheeler',
                                    'delivered' => 'check_circle',
                                    'cancelled' => 'cancel',
                                    'returned' => 'assignment_return',
                                ];

                                $statusNames = [
                                    'pending' => 'قيد الانتظار',
                                    'in_transit' => 'في الطريق',
                                    'received_at_branch' => 'بالمستودع',
                                    'out_for_delivery' => 'خرج للتوصيل',
                                    'delivered' => 'تم التسليم',
                                    'cancelled' => 'ملغي',
                                    'returned' => 'مرتجع',
                                ];

                                if ($shipment->is_returned) {
                                    $badgeColorClass =
                                        'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
                                    $badgeIcon = 'keyboard_return';
                                    if ($shipment->status === 'returned') {
                                        $badgeName = 'مرتجع (سُلم للتاجر)';
                                    } else {
                                        $currentName = $statusNames[$shipment->status] ?? $shipment->status;
                                        $badgeName = "مرتجع ($currentName)";
                                    }
                                } else {
                                    $badgeColorClass =
                                        $statusColors[$shipment->status] ??
                                        'bg-slate-50 text-slate-600 border-slate-200 dark:bg-boxdark dark:text-gray-400 dark:border-gray-700';
                                    $badgeIcon = $statusIcons[$shipment->status] ?? 'info';
                                    $badgeName = $statusNames[$shipment->status] ?? $shipment->status;
                                }
                            @endphp

                            <div
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-bold text-xs shadow-sm {{ $badgeColorClass }}">
                                <span class="material-symbols-outlined text-[16px]">{{ $badgeIcon }}</span>
                                {{ $badgeName }}
                            </div>
                        </div>
                        <div class="flex gap-2 items-center text-sm font-bold text-gray-500 dark:text-gray-400">
                            <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                            {{ $shipment->created_at->format('Y/m/d h:i A') }}
                        </div>
                    </div>
                </div>

                {{-- ================= أزرار الإجراءات ================= --}}
                <div class="flex flex-wrap gap-3 items-center">

                    @php
                        $currentStatus = $shipment->status;
                        $availableStatuses = [];

                        // 💡 اللوجيك الذكي: فصل إجراءات المرتجع عن الطرد العادي
                        if ($shipment->is_returned && $currentStatus !== 'returned') {
                            $availableStatuses = [
                                'returned' => [
                                    'label' => 'تأكيد استلام التاجر للمرتجع',
                                    'icon' => 'inventory_2',
                                    'bg_color' => 'bg-emerald-50 dark:bg-emerald-500/10',
                                    'text_color' => 'text-emerald-600 dark:text-emerald-400',
                                ],
                            ];
                        } elseif (!$shipment->is_returned) {
                            if ($currentStatus === 'pending') {
                                $availableStatuses = [
                                    'returned' => [
                                        'label' => 'إلغاء الطرد (مرتجع)',
                                        'icon' => 'cancel',
                                        'bg_color' => 'bg-rose-50 dark:bg-rose-500/10',
                                        'text_color' => 'text-rose-600 dark:text-rose-400',
                                    ],
                                ];
                            } elseif ($currentStatus === 'in_transit') {
                                $availableStatuses = [
                                    'delivered' => [
                                        'label' => 'تم التسليم بنجاح',
                                        'icon' => 'check_circle',
                                        'bg_color' => 'bg-emerald-50 dark:bg-emerald-500/10',
                                        'text_color' => 'text-emerald-600 dark:text-emerald-400',
                                    ],
                                    'returned' => [
                                        'label' => 'فشل التسليم (مرتجع)',
                                        'icon' => 'assignment_return',
                                        'bg_color' => 'bg-rose-50 dark:bg-rose-500/10',
                                        'text_color' => 'text-rose-600 dark:text-rose-400',
                                    ],
                                ];
                            }
                        }
                    @endphp

                    @if (!empty($availableStatuses))
                        {{-- 💡تم رفع z-index هنا إلى z-[60] لكي لا تظهر القائمة خلف الكروت --}}
                        <div class="relative z-[60]" x-data="{ openStatusMenu: false }">
                            <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST"
                                x-ref="statusForm" id="updateStatusForm">
                                @csrf
                                <input type="hidden" name="status" x-ref="statusInput" id="updateStatusInput">

                                {{-- الزر الرئيسي --}}
                                <button type="button" @click="openStatusMenu = !openStatusMenu"
                                    @click.outside="openStatusMenu = false"
                                    class="flex gap-2 items-center px-5 h-12 text-sm font-bold text-white rounded-xl shadow-sm transition-all active:scale-95 {{ $shipment->is_returned ? 'bg-rose-500 hover:bg-rose-600 shadow-[0_8px_20px_rgba(244,63,94,0.2)]' : 'bg-slate-800 hover:bg-slate-700 dark:bg-boxdark dark:hover:bg-boxdark-2' }}">

                                    <span class="material-symbols-outlined text-[20px]">
                                        {{ $shipment->is_returned ? 'assignment_returned' : 'update' }}
                                    </span>
                                    <span>
                                        {{ $shipment->is_returned ? 'إجراءات الطرد المرتجع' : 'تحديث الحالة' }}
                                    </span>
                                    <span class="material-symbols-outlined text-[20px] transition-transform duration-300"
                                        :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                                </button>

                                {{-- القائمة المنسدلة --}}
                                <div x-show="openStatusMenu" x-cloak x-transition
                                    class="absolute top-full right-0 mt-2 w-full min-w-[220px] bg-white dark:bg-boxdark-2 rounded-2xl shadow-[0_15px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 dark:border-boxdark p-2 z-[60]">
                                    @foreach ($availableStatuses as $value => $data)
                                        <button type="button"
                                            @click="
                                                openStatusMenu = false;
                                                if ('{{ $value }}' === 'delivered' && {{ !$shipment->is_returned && !in_array($shipment->payment_method, ['prepaid', 'customer_credit']) ? 'true' : 'false' }}) {
                                                    showPaymentModal = true;
                                                } else if ('{{ $value }}' === 'returned' && {{ $shipment->is_returned && in_array($shipment->payment_method, ['prepaid', 'partial_payment']) ? 'true' : 'false' }}) {
                                                    showRefundModal = true;
                                                } else {
                                                    $refs.statusInput.value = '{{ $value }}'; 
                                                    $refs.statusForm.submit();
                                                }
                                            "
                                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-boxdark transition-all text-right group active:scale-[0.98]">
                                            <div
                                                class="flex justify-center items-center w-8 h-8 rounded-lg shrink-0 transition-transform group-hover:scale-110 {{ $data['bg_color'] }} {{ $data['text_color'] }}">
                                                <span
                                                    class="material-symbols-outlined text-[18px]">{{ $data['icon'] }}</span>
                                            </div>
                                            <span
                                                class="text-xs font-black text-gray-700 dark:text-gray-200">{{ $data['label'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    @else
                        {{-- الحالة النهائية (مغلق) --}}
                        <div
                            class="flex gap-2 items-center px-5 h-12 text-xs font-bold text-gray-400 bg-gray-50 rounded-xl border border-gray-100 dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500">
                            <span class="material-symbols-outlined text-[18px]">
                                {{ $shipment->status === 'returned' ? 'done_all' : 'lock' }}
                            </span>
                            {{ $shipment->status === 'returned' ? 'تم تسليم المرتجع وإغلاق الطرد' : 'تم إغلاق دورة حياة الطرد' }}
                        </div>
                    @endif

                    @if (!in_array($shipment->status, ['returned', 'cancelled']))
                        <div class="hidden mx-1 w-px h-8 bg-gray-200 dark:bg-gray-700 sm:block"></div>

                        <div class="flex gap-2 items-center">
                            <a href="{{ route('receipt.generate', ['type' => 'sender', 'id' => $shipment->uuid]) }}"
                                target="_blank" title="طباعة الفاتورة"
                                class="flex justify-center items-center w-12 h-12 text-gray-600 bg-white rounded-xl border border-gray-200 transition-all hover:bg-gray-50 hover:text-primary dark:bg-boxdark-2 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-boxdark hover:shadow-sm active:scale-95">
                                <span class="material-symbols-outlined text-[22px]">print</span>
                            </a>
                            <a href="{{ route('receipt.generate', ['type' => 'thermal', 'id' => $shipment->uuid]) }}"
                                target="_blank" title="طباعة سند حرارية"
                                class="flex justify-center items-center w-12 h-12 text-gray-600 bg-white rounded-xl border border-gray-200 transition-all hover:bg-gray-50 hover:text-primary dark:bg-boxdark-2 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-boxdark hover:shadow-sm active:scale-95">
                                <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                            </a>
                            @if ($shipment->status === 'in_transit')
                                <button @click="openUnlinkModal({{ $shipment->id }}, '{{ $shipment->bond_number }}')"
                                    title="فك ربط الطرد من الرحلة"
                                    class="flex justify-center items-center w-12 h-12 bg-white rounded-xl border border-gray-200 transition-all text-warning-500 hover:bg-warning-50 hover:border-warning-200 dark:bg-boxdark-2 dark:border-gray-700 dark:hover:bg-warning-500/10 hover:shadow-sm active:scale-95">
                                    <span class="material-symbols-outlined text-[22px]">link_off</span>
                                </button>
                            @endif
                        </div>
                    @endif

                    @if (!in_array($shipment->status, ['delivered', 'returned', 'cancelled']))
                        <a href="{{ route('shipment.outgoing.edit', $shipment->id) }}"
                            class="flex gap-2 items-center px-6 h-12 text-sm font-bold text-white rounded-xl shadow-sm transition-all bg-primary hover:bg-primary-hover hover:shadow-md hover:shadow-primary/20 active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">edit_square</span>
                            تعديل
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= شبكة المربعات السفلية (Grid) ================= --}}
        <div class="grid relative z-10 grid-cols-1 gap-6 lg:grid-cols-3">

            <div class="space-y-6 lg:col-span-2">
                <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

                    {{-- مربع المرسل (Blue Theme) --}}
                    <div
                        class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark-2 dark:border-boxdark transition-all hover:shadow-md hover:border-blue-200 dark:hover:border-blue-500/30 group">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex gap-2 items-center text-sm font-black tracking-widest text-blue-500 uppercase">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                بيانات المرسل
                            </div>
                            <div class="flex gap-2 items-center">
                                @if ($shipment->senderCustomer)
                                    <a href="{{ route('customers.show', $shipment->senderCustomer->id) }}"
                                        title="ملف العميل"
                                        class="flex justify-center items-center w-9 h-9 text-blue-500 bg-blue-50 rounded-xl transition-colors hover:bg-blue-100 dark:bg-blue-500/10 dark:hover:bg-blue-500/20">
                                        <span class="material-symbols-outlined text-[20px]">person</span>
                                    </a>
                                @endif
                                <a href="{{ $shipment->sender_whatsapp_link }}" target="_blank" title="مراسلة واتساب"
                                    class="flex justify-center items-center w-9 h-9 text-green-500 bg-green-50 rounded-xl transition-colors hover:bg-green-100 dark:bg-green-500/10 dark:hover:bg-green-500/20">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="flex gap-4 items-center mb-6">
                            <div
                                class="flex justify-center items-center w-14 h-14 text-xl font-black text-blue-600 bg-blue-50 rounded-2xl transition-transform duration-300 dark:bg-blue-500/10 dark:text-blue-400 group-hover:scale-105">
                                {{ mb_substr($shipment->senderCustomer->name ?? ($shipment->sender_name ?? '?'), 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white font-headline">
                                    {{ $shipment->senderCustomer->name ?? 'غير محدد' }}
                                </h4>
                                <div class="mt-1 text-sm font-bold text-gray-500 dir-ltr dark:text-gray-400">
                                    <x-phone-number :value="$shipment->senderCustomer?->phone ?? '---'" />
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center pt-4 text-sm border-t border-gray-100 dark:border-gray-800">
                            <span class="font-bold text-gray-400 dark:text-gray-500">فرع الإرسال:</span>
                            @if ($shipment->senderOfficeBranch)
                                <span
                                    class="px-3 py-1.5 font-bold rounded-full text-primary bg-pri-200 dark:text-white dark:bg-boxdark">
                                    {{ $shipment->senderOfficeBranch->name }}
                                    ({{ $shipment->senderOfficeBranch->office->name }})
                                </span>
                            @else
                                <span
                                    class="px-3 py-1.5 font-bold rounded-full text-primary bg-pri-200 dark:text-white dark:bg-boxdark">
                                    {{ $shipment->senderBranch->name ?? 'غير محدد' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- مربع المستلم (Emerald Theme) --}}
                    <div
                        class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark-2 dark:border-boxdark transition-all hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-500/30 group">
                        <div class="flex justify-between items-start mb-6">
                            <div
                                class="flex gap-2 items-center text-sm font-black tracking-widest text-emerald-500 uppercase">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                بيانات المستلم
                            </div>
                            <div class="flex gap-2 items-center">
                                @if ($shipment->receiverCustomer)
                                    <a href="{{ route('customers.show', $shipment->receiverCustomer->id) }}"
                                        title="ملف العميل"
                                        class="flex justify-center items-center w-9 h-9 text-emerald-500 bg-emerald-50 rounded-xl transition-colors hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20">
                                        <span class="material-symbols-outlined text-[20px]">person</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-4 items-center mb-6">
                            <div
                                class="flex justify-center items-center w-14 h-14 text-xl font-black text-emerald-600 bg-emerald-50 rounded-2xl transition-transform duration-300 dark:bg-emerald-500/10 dark:text-emerald-400 group-hover:scale-105">
                                {{ mb_substr($shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '?'), 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white font-headline">
                                    {{ $shipment->receiverCustomer->name ?? 'غير محدد' }}
                                </h4>
                                <div class="mt-1 text-sm font-bold text-gray-500 dir-ltr dark:text-gray-400">
                                    <x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'" />
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center pt-4 text-sm border-t border-gray-100 dark:border-gray-800">
                            <span class="font-bold text-gray-400 dark:text-gray-500">فرع الاستلام:</span>
                            @if ($shipment->receiverOfficeBranch)
                                <span
                                    class="px-3 py-1.5 font-black text-gray-900 bg-gray-50 rounded-lg dark:text-white dark:bg-boxdark">
                                    {{ $shipment->receiverOfficeBranch->name }}
                                    ({{ $shipment->receiverOfficeBranch->office->name }})
                                </span>
                            @else
                                <span
                                    class="px-3 py-1.5 font-black text-gray-900 bg-gray-50 rounded-lg dark:text-white dark:bg-boxdark">
                                    {{ $shipment->receiverBranch->name ?? 'غير محدد' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- مربع محتوى الطرد (Purple Theme) --}}
                <div
                    class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark-2 dark:border-boxdark transition-all hover:shadow-md hover:border-purple-200 dark:hover:border-purple-500/30">
                    <div class="flex gap-2 items-center mb-6 text-sm font-black tracking-widest text-purple-500 uppercase">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        بيانات محتوى الطرد
                    </div>

                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div
                            class="flex flex-col justify-center p-5 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark dark:border-gray-800">
                            <div class="mb-2 text-xs font-bold text-gray-500 dark:text-gray-400">نوع الشحنة</div>
                            <div class="text-lg font-black text-gray-900 dark:text-white font-headline">
                                {{ $shipment->package_type ?? '-' }}</div>
                        </div>
                        <div
                            class="flex flex-col justify-center p-5 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark dark:border-gray-800">
                            <div class="mb-2 text-xs font-bold text-gray-500 dark:text-gray-400">عدد الجوالين</div>
                            <div class="text-lg font-black text-gray-900 dark:text-white font-headline">
                                {{ $shipment->no_gallons_honey ?? 0 }}</div>
                        </div>
                        <div
                            class="flex flex-col justify-center p-5 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark dark:border-gray-800">
                            <div class="mb-2 text-xs font-bold text-gray-500 dark:text-gray-400">عدد القروف</div>
                            <div class="text-lg font-black text-gray-900 dark:text-white font-headline">
                                {{ $shipment->no_honey_jars ?? 0 }}</div>
                        </div>
                    </div>

                    @if ($shipment->notes)
                        <div class="pt-6 mt-6 border-t border-gray-100 dark:border-gray-800">
                            <h4 class="flex gap-2 items-center mb-3 text-sm font-bold text-gray-500 dark:text-gray-400">
                                <span class="material-symbols-outlined text-[18px]">info</span>
                                ملاحظات هامة:
                            </h4>
                            <div
                                class="p-5 text-sm font-bold leading-relaxed rounded-2xl border border-warning-200 bg-warning-50 text-warning-800 dark:bg-warning-500/10 dark:text-warning-400 dark:border-warning-500/20">
                                {{ $shipment->notes }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <div class="space-y-6 lg:col-span-1">
                {{-- مربع المالية (Amber Theme) --}}
                <div
                    class="p-6 bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark-2 dark:border-boxdark transition-all hover:shadow-md hover:border-amber-200 dark:hover:border-amber-500/30">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex gap-2 items-center text-sm font-black tracking-widest text-amber-500 uppercase">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            المالية
                        </div>
                        <div
                            class="flex justify-center items-center w-10 h-10 text-amber-600 bg-amber-50 rounded-xl dark:bg-amber-500/10 dark:text-amber-400">
                            <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-800">
                            <div class="text-sm font-bold text-gray-500 dark:text-gray-400">إجمالي المبلغ</div>
                            <div class="font-mono text-3xl font-black tracking-tight text-gray-900 dark:text-white">
                                {{ number_format($shipment->total_amount, 0) }}
                                <span class="font-sans text-xs font-bold text-gray-400">ر.ي</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-4 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400">طريقة الدفع</span>
                            @php
                                $paymentMethodText = match ($shipment->payment_method) {
                                    'prepaid' => 'دفع مسبق',
                                    'cod' => 'دفع عند الاستلام',
                                    'customer_credit' => 'آجل',
                                    'partial_payment' => 'دفع جزئي',
                                    default => $shipment->payment_method,
                                };
                            @endphp
                            <span
                                class="px-3 py-1.5 text-xs font-black text-gray-700 bg-gray-100 rounded-lg dark:bg-boxdark dark:text-gray-300">
                                {{ $paymentMethodText }}
                            </span>
                        </div>

                        @if ($shipment->payment_method === 'partial_payment' && $paidAmount > 0)
                            <div class="flex justify-between items-center py-4">
                                <span class="text-sm font-bold text-gray-500 dark:text-gray-400">المبلغ المدفوع</span>
                                <span class="font-mono text-xl font-black text-green-500">
                                    {{ number_format($paidAmount, 0) }} <span class="font-sans text-xs">ر.ي</span>
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= مودال تحصيل الدفع المتبقي ================= --}}
        @if (!$shipment->is_returned && !in_array($shipment->payment_method, ['prepaid', 'customer_credit']))
            <template x-teleport="body">
                <div x-show="showPaymentModal" x-cloak
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
                    <div x-show="showPaymentModal" x-transition.opacity duration.300ms @click="showPaymentModal = false"
                        class="absolute inset-0 backdrop-blur-sm bg-slate-900/40"></div>

                    <div x-show="showPaymentModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="relative p-6 w-full max-w-sm text-center bg-white rounded-3xl border shadow-2xl border-slate-100 dark:bg-boxdark dark:border-boxdark-2">

                        <div
                            class="flex justify-center items-center mx-auto mb-4 w-20 h-20 text-emerald-500 bg-emerald-50 rounded-full animate-bounce dark:bg-emerald-500/10 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-[40px]">payments</span>
                        </div>

                        <h3 class="mb-2 text-lg font-black text-slate-800 dark:text-white font-headline">تنبيه تحصيل مالي!
                        </h3>
                        <p class="mb-6 text-xs font-bold leading-relaxed text-slate-500 dark:text-slate-400">
                            هذا الطرد غير مدفوع مسبقاً بالكامل. الرجاء استلام المبلغ التالي من العميل قبل تأكيد عملية
                            التسليم.
                        </p>

                        <div
                            class="p-5 mb-6 bg-emerald-50 rounded-2xl border border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20">
                            <p class="mb-1 text-[10px] font-black text-emerald-600/70 dark:text-emerald-400">المبلغ المطلوب
                                تحصيله</p>
                            <p class="font-mono text-3xl font-black text-emerald-700 dir-ltr dark:text-emerald-300">
                                {{ number_format($remainingAmount, 0) }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="showPaymentModal = false"
                                class="flex-1 h-12 text-xs font-black rounded-xl transition-colors text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-boxdark-2 dark:text-slate-300 dark:hover:bg-gray-600">
                                تراجع
                            </button>
                            <button type="button"
                                @click="document.getElementById('updateStatusInput').value = 'delivered'; document.getElementById('updateStatusForm').submit();"
                                class="flex-[2] h-12 bg-emerald-500 text-white hover:bg-emerald-600 rounded-xl font-black text-xs shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">verified</span>
                                تم استلام المبلغ
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        @endif

        {{-- ================= مودال إرجاع المبلغ للعميل (للمرتجعات المدفوعة) ================= --}}
        @if ($shipment->is_returned && $refundAmount > 0)
            <template x-teleport="body">
                <div x-show="showRefundModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
                    <div x-show="showRefundModal" x-transition.opacity duration.300ms @click="showRefundModal = false"
                        class="absolute inset-0 backdrop-blur-sm bg-slate-900/40"></div>

                    <div x-show="showRefundModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="relative p-6 w-full max-w-sm text-center bg-white rounded-3xl border shadow-2xl border-slate-100 dark:bg-boxdark dark:border-boxdark-2">

                        <div
                            class="flex justify-center items-center mx-auto mb-4 w-20 h-20 text-amber-500 bg-amber-50 rounded-full animate-bounce dark:bg-amber-500/10 dark:text-amber-400">
                            <span class="material-symbols-outlined text-[40px]">currency_exchange</span>
                        </div>

                        <h3 class="mb-2 text-lg font-black text-slate-800 dark:text-white font-headline">تنبيه إرجاع مالي!
                        </h3>
                        <p class="mb-6 text-xs font-bold leading-relaxed text-slate-500 dark:text-slate-400">
                            هذا الطرد مرتجع للتاجر وقد تم دفع رسومه مسبقاً. الرجاء إرجاع المبلغ التالي للعميل يدوياً قبل
                            إغلاق دورة الطرد.
                        </p>

                        <div
                            class="p-5 mb-6 bg-amber-50 rounded-2xl border border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20">
                            <p class="mb-1 text-[10px] font-black text-amber-600/70 dark:text-amber-400">المبلغ المطلوب
                                إرجاعه</p>
                            <p class="font-mono text-3xl font-black text-amber-700 dir-ltr dark:text-amber-300">
                                {{ number_format($refundAmount, 0) }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="showRefundModal = false"
                                class="flex-1 h-12 text-xs font-black rounded-xl transition-colors text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-boxdark-2 dark:text-slate-300 dark:hover:bg-gray-600">
                                تراجع
                            </button>
                            <button type="button"
                                @click="document.getElementById('updateStatusInput').value = 'returned'; document.getElementById('updateStatusForm').submit();"
                                class="flex-[2] h-12 bg-amber-500 text-white hover:bg-amber-600 rounded-xl font-black text-xs shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">done_all</span>
                                تم الإرجاع للعميل
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        @endif

        {{-- Modals Includes --}}
        @include('pages.shipment.modals.cancel-shipment-modal', ['shipment' => $shipment])
        @include('pages.shipmentpackage.modals.unlink-modal')
    </div>
@endsection

@section('script')
    {{-- كود الـ Script كما هو ولم يتغير --}}
@endsection
