@extends('mobile.layouts.app')

@section('title', 'تفاصيل الطرد - ' . $shipment->id)

@section('content')
    @php
        // الحسابات المالية المشتركة للموديل والواجهة
        $remainingAmount = $shipment->total_amount - $shipment->partial_amount;

        $refundAmount = 0;
        if ($shipment->payment_method === 'prepaid') {
            $refundAmount = $shipment->total_amount;
        } elseif ($shipment->payment_method === 'partial_payment') {
            $refundAmount = $shipment->partial_amount;
        }
    @endphp

    {{-- تم إضافة x-data هنا للتحكم في المودالات على مستوى الصفحة --}}
    <div class="flex relative flex-col gap-5 px-4 pt-6 pb-8 min-h-screen bg-slate-50/50" x-data="{ showPaymentModal: false, showRefundModal: false }">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex relative z-10 justify-between items-center">
            <div class="flex gap-3 items-center">
                <a href="{{ route('shipment.outgoing.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-lg font-black font-headline text-slate-800">رقم الطرد</h1>
                    <p class="text-sm font-bold tracking-wider text-primary">{{ $shipment->id }}</p>
                </div>
            </div>

            {{-- ================= حالة الطرد الذكية ================= --}}
            @php
                $statusColors = [
                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'in_transit' => 'bg-blue-50 text-blue-600 border-blue-200',
                    'received_at_branch' => 'bg-purple-50 text-purple-600 border-purple-200',
                    'out_for_delivery' => 'bg-teal-50 text-teal-600 border-teal-200',
                    'delivered' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                    'cancelled' => 'bg-slate-50 text-slate-600 border-slate-200',
                    'returned' => 'bg-rose-50 text-rose-600 border-rose-200',
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
                    $colorClass = 'bg-rose-50 text-rose-600 border-rose-200';
                    $icon = 'keyboard_return';
                    if ($shipment->status === 'returned') {
                        $name = 'مرتجع (سُلم للتاجر)';
                    } else {
                        $currentName = $statusNames[$shipment->status] ?? $shipment->status;
                        $name = "مرتجع ($currentName)";
                    }
                } else {
                    $colorClass = $statusColors[$shipment->status] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                    $icon = $statusIcons[$shipment->status] ?? 'info';
                    $name = $statusNames[$shipment->status] ?? $shipment->status;
                }
            @endphp
            <div
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-bold text-xs shadow-sm {{ $colorClass }}">
                <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
                {{ $name }}
            </div>
        </div>

        {{-- ================= أزرار الإجراءات الراقية (Premium Actions) ================= --}}
        <div class="flex gap-3 items-center mt-1 relative z-40">

            @php
                $currentStatus = $shipment->status;
                $availableStatuses = [];

                if ($shipment->is_returned && $currentStatus !== 'returned') {
                    $availableStatuses = [
                        'returned' => [
                            'label' => 'تأكيد استلام التاجر للمرتجع',
                            'icon' => 'inventory_2',
                            'bg_color' => 'bg-emerald-50',
                            'text_color' => 'text-emerald-600',
                        ],
                    ];
                } elseif (!$shipment->is_returned) {
                    if ($currentStatus === 'pending') {
                        $availableStatuses = [
                            'returned' => [
                                'label' => 'إلغاء الطرد (مرتجع)',
                                'icon' => 'cancel',
                                'bg_color' => 'bg-rose-50',
                                'text_color' => 'text-rose-600',
                            ],
                        ];
                    } elseif ($currentStatus === 'in_transit') {
                        $availableStatuses = [
                            'delivered' => [
                                'label' => 'تم التسليم بنجاح',
                                'icon' => 'check_circle',
                                'bg_color' => 'bg-emerald-50',
                                'text_color' => 'text-emerald-600',
                            ],
                            'returned' => [
                                'label' => 'فشل التسليم (مرتجع)',
                                'icon' => 'assignment_return',
                                'bg_color' => 'bg-rose-50',
                                'text_color' => 'text-rose-600',
                            ],
                        ];
                    }
                }
            @endphp

            @if (!empty($availableStatuses))
                <div class="flex-[2] relative" x-data="{ openStatusMenu: false }">
                    {{-- تمت إضافة المعرفات (id) هنا لكي نتصل بها من المودال --}}
                    <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" x-ref="statusForm"
                        id="updateStatusFormMobile">
                        @csrf
                        <input type="hidden" name="status" x-ref="statusInput" id="updateStatusInputMobile">

                        <button type="button" @click="openStatusMenu = !openStatusMenu"
                            @click.outside="openStatusMenu = false"
                            class="w-full flex items-center justify-between px-4 h-12 {{ $shipment->is_returned ? 'bg-rose-500 hover:bg-rose-600 shadow-[0_8px_20px_rgba(244,63,94,0.2)]' : 'bg-slate-800 hover:bg-slate-700 shadow-[0_8px_20px_rgba(30,41,59,0.2)]' }} text-white rounded-2xl font-bold text-xs active:scale-95 transition-all">
                            <div class="flex gap-2 items-center">
                                <span
                                    class="material-symbols-outlined text-[18px]">{{ $shipment->is_returned ? 'assignment_returned' : 'update' }}</span>
                                <span>{{ $shipment->is_returned ? 'إجراءات الطرد المرتجع' : 'تحديث حالة الطرد' }}</span>
                            </div>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-300"
                                :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <div x-show="openStatusMenu" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            class="absolute top-full right-0 mt-2 w-full bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_15px_40px_-10px_rgba(0,0,0,0.15)] border border-slate-100 p-1.5 z-50">

                            @foreach ($availableStatuses as $value => $data)
                                <button type="button"
                                    @click="
                                        openStatusMenu = false;
                                        if ('{{ $value }}' === 'delivered' && {{ !$shipment->is_returned && !in_array($shipment->payment_method, ['prepaid', 'customer_credit']) ? 'true' : 'false' }}) {
                                            showPaymentModal = true;
                                        } else if ('{{ $value }}' === 'returned' && {{ $shipment->is_returned && in_array($shipment->payment_method, ['prepaid', 'partial_payment']) ? 'true' : 'false' }}) {
                                            showRefundModal = true;
                                        } else {
                                            document.getElementById('updateStatusInputMobile').value = '{{ $value }}'; 
                                            document.getElementById('updateStatusFormMobile').submit();
                                        }
                                    "
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-all text-right group active:scale-[0.98]">

                                    <div
                                        class="w-8 h-8 rounded-lg {{ $data['bg_color'] }} {{ $data['text_color'] }} flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">{{ $data['icon'] }}</span>
                                    </div>
                                    <span class="text-xs font-black text-slate-700">{{ $data['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </form>
                </div>
            @else
                <div
                    class="flex-[2] flex items-center justify-center gap-2 h-12 bg-slate-50 text-slate-400 rounded-2xl font-bold text-[10px] border border-slate-100">
                    <span
                        class="material-symbols-outlined text-[16px]">{{ $shipment->status === 'returned' ? 'done_all' : 'lock' }}</span>
                    {{ $shipment->status === 'returned' ? 'تم إغلاق المرتجع' : 'تم الإغلاق' }}
                </div>
            @endif

            @if (!in_array($shipment->status, ['returned', 'cancelled']))
                <a href="{{ route('receipt.generate', ['type' => 'sender', 'id' => $shipment->uuid]) }}" target="_blank"
                    class="flex flex-1 gap-2 justify-center items-center h-12 text-xs font-bold bg-white rounded-2xl border shadow-sm transition-all text-slate-600 border-slate-100 hover:bg-slate-50 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                </a>
                <a href="{{ route('receipt.generate', ['type' => 'thermal', 'id' => $shipment->uuid]) }}" target="_blank"
                    class="flex flex-1 gap-2 justify-center items-center h-12 text-xs font-bold bg-white rounded-2xl border shadow-sm transition-all text-slate-600 border-slate-100 hover:bg-slate-50 active:scale-95">
                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                </a>
            @endif
        </div>

        {{-- ================= بطاقة المسار (من -> إلى) ================= --}}
        <div
            class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden mt-2">
            <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-primary/5"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-emerald-500/5">
            </div>

            <div class="flex relative z-10 justify-between items-center mb-8">
                <div class="flex gap-3 items-center">
                    <div
                        class="flex justify-center items-center w-10 h-10 bg-gradient-to-br rounded-xl shadow-inner from-primary/20 to-primary/5 text-primary">
                        <span class="material-symbols-outlined text-[20px]">route</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 font-headline">مسار الرحلة</h3>
                </div>
                <span class="px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-bold border border-slate-100">
                    {{ $shipment->created_at->format('Y-m-d h:i A') }}
                </span>
            </div>

            @php
                $senderMsg =
                    'مرحباً *' .
                    ($shipment->senderCustomer?->name ?? 'عميلنا العزيز') .
                    "*،\nتم إصدار بوليصة شحن طردك برقم: *" .
                    $shipment->id .
                    '*';
                $receiverMsg =
                    'مرحباً *' .
                    ($shipment->receiverCustomer?->name ?? 'عميلنا العزيز') .
                    "*،\nلديك طرد قادم إليك برقم بوليصة: *" .
                    $shipment->id .
                    "*\n";
                if (in_array($shipment->payment_method, ['cod', 'partial_payment'])) {
                    $receiverMsg .= 'المبلغ المطلوب عند الاستلام: *' . number_format($remainingAmount, 0) . '* ريال.';
                } else {
                    $receiverMsg .= 'الطرد مدفوع مسبقاً، لا توجد رسوم إضافية عند الاستلام.';
                }
            @endphp

            <div class="relative pr-6 pl-2 space-y-8">
                <div
                    class="absolute right-[11px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-slate-200 via-primary/30 to-primary rounded-full">
                </div>

                {{-- نقطة الانطلاق (المرسل) --}}
                <div class="relative z-10">
                    <div
                        class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-white border-4 border-slate-300 rounded-full shadow-sm">
                    </div>
                    <div
                        class="p-3.5 rounded-2xl border transition-colors bg-slate-50/50 border-slate-100/50 hover:bg-slate-50">
                        <span class="text-[10px] font-black text-slate-400 mb-1 block">المرسل •
                            {{ $shipment->senderBranch?->name ?? 'مكتب خارجي' }}</span>
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="block text-sm font-black text-slate-800">{{ $shipment->senderCustomer?->name ?? 'عميل نقدي' }}</span>
                                <span class="block mt-0.5 text-xs font-medium text-right text-slate-500 dir-ltr">
                                    <x-phone-number :value="$shipment->senderCustomer?->phone ?? '---'" class="text-[11px] font-bold text-gray-500" />
                                </span>
                            </div>
                            @if ($shipment->senderCustomer?->phone)
                                <div class="flex gap-1.5 items-center">
                                    <a href="{{ $shipment->sender_whatsapp_link }}" target="_blank"
                                        class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all group">
                                        <svg class="w-5 h-5 fill-[#25D366]" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                    </a>
                                    <a href="tel:{{ $shipment->senderCustomer?->phone }}"
                                        class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border shadow-sm transition-all border-slate-100 text-slate-600 hover:text-primary hover:border-primary/30 active:scale-95">
                                        <span class="material-symbols-outlined text-[18px]">call</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- نقطة الوصول (المستلم) --}}
                <div class="relative z-10">
                    <div
                        class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-primary ring-4 ring-primary/20 rounded-full {{ $shipment->status == 'in_transit' ? 'animate-pulse' : '' }}">
                    </div>
                    <div
                        class="bg-primary/[0.02] p-3.5 rounded-2xl border border-primary/10 hover:bg-primary/5 transition-colors">
                        <span class="text-[10px] font-black text-primary/60 mb-1 block">المستلم •
                            {{ $shipment->receiverBranch?->name ?? 'مكتب خارجي' }}</span>
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="block text-sm font-black text-primary">{{ $shipment->receiverCustomer?->name ?? 'عميل نقدي' }}</span>
                                <span class="block mt-0.5 text-xs font-medium text-right text-primary/70 dir-ltr">
                                    <x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'" class="text-[11px] font-bold text-gray-500" />
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($shipment->package)
            @php
                $driverMsg =
                    'مرحباً كابتن *' .
                    ($shipment->package->driver->name ?? 'السائق') .
                    "*،\nبخصوص الطرد رقم: *" .
                    $shipment->id .
                    "*\nالموجود ضمن الإرسالية رقم: *" .
                    $shipment->package->id .
                    '*';
            @endphp
            <div
                class="bg-gradient-to-br from-white to-blue-50/30 p-6 rounded-[2rem] border border-blue-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
                <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-blue-500/5">
                </div>
                <div class="flex relative z-10 justify-between items-center mb-5">
                    <div class="flex gap-3 items-center">
                        <div
                            class="flex justify-center items-center w-10 h-10 text-blue-600 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl shadow-inner">
                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                        </div>
                        <h3 class="text-sm font-black text-slate-800 font-headline">بيانات رحلة التوصيل</h3>
                    </div>
                </div>
                <div class="flex relative z-10 flex-col gap-3">
                    <div
                        class="flex justify-between items-center p-4 rounded-2xl border border-blue-50 shadow-sm backdrop-blur-sm bg-white/80">
                        <div class="flex gap-3 items-center">
                            <div
                                class="flex justify-center items-center w-10 h-10 rounded-full bg-slate-100 text-slate-500">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 mb-0.5 uppercase tracking-wider">السائق
                                    المسؤول</p>
                                <p class="text-xs font-black text-slate-800">
                                    {{ $shipment->package->driver->name ?? 'غير محدد' }}</p>
                                <p class="text-[10px] font-bold text-slate-500 dir-ltr text-right mt-0.5">
                                    <x-phone-number :value="$shipment->package->driver->phone ?? '---'" class="text-[11px] font-bold" />
                                </p>
                            </div>
                        </div>
                        @if ($shipment->package->driver && $shipment->package->driver->phone)
                            <div class="flex gap-1.5 items-center">
                                {{-- <a href="https://wa.me/{{ ltrim($shipment->package->driver->phone, '+') }}?text={{ urlencode($driverMsg) }}"
                                    target="_blank"
                                    class="w-10 h-10 bg-white rounded-xl shadow-sm border border-blue-100 flex items-center justify-center hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all">
                                    <svg class="w-5 h-5 fill-[#25D366]" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                </a> --}}
                                <a href="tel:{{ $shipment->package->driver->phone }}"
                                    class="flex justify-center items-center w-10 h-10 text-blue-600 bg-blue-50 rounded-xl border border-blue-100 shadow-sm transition-all hover:bg-blue-600 hover:text-white active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">call</span>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div
                        class="flex justify-between items-center p-4 bg-blue-600 rounded-2xl shadow-lg shadow-blue-600/20">
                        <div>
                            <p class="text-[9px] font-black text-blue-200 mb-0.5 uppercase tracking-wider">ضمن الإرسالية
                                المجمعة</p>
                            <p class="font-mono text-sm font-black tracking-widest text-white">
                                {{ $shipment->package->id }}</p>
                        </div>
                        <a href="{{ route('shipmentpackage.outgoing.show', $shipment->package->id) }}"
                            class="flex items-center gap-1.5 bg-white text-blue-600 px-4 py-2 rounded-xl text-[10px] font-black hover:bg-blue-50 active:scale-95 transition-all shadow-sm">
                            التفاصيل
                            <span class="material-symbols-outlined text-[14px]">arrow_back_ios_new</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= بطاقة المحتويات والتسعير التفصيلي ================= --}}
        <div class="bg-white p-5 rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.03)]">
            <div class="flex gap-2 items-center mb-5">
                <div
                    class="flex justify-center items-center w-9 h-9 bg-gradient-to-br rounded-xl shadow-inner from-blue-50 to-blue-50/50 text-blue-600">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
                <h3 class="font-black text-slate-800 font-headline">بيانات المحتويات والتسعير</h3>
            </div>

            {{-- شبكة بيانات الطرد الأساسية --}}
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block mb-1">نوع الطرد</span>
                    <span class="text-sm font-black text-slate-800">{{ $shipment->package_type }}</span>
                </div>
                @if ($shipment->weight > 0)
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block mb-1">الوزن</span>
                        <span class="text-sm font-black text-slate-800">{{ $shipment->weight }} <span
                                class="text-xs font-bold text-slate-500">كجم</span></span>
                    </div>
                @endif
            </div>

            {{-- تفاصيل التسعير والعمولات --}}
            <div class="space-y-3">
                {{-- الطرد العادي --}}
                <div class="p-4 rounded-2xl border border-blue-100/50 bg-blue-50/30">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] font-black text-blue-700 uppercase">أجرة شحن الطرد</span>
                        <span class="text-sm font-black text-blue-700">{{ number_format($shipment->package_fee, 0) }}
                            ر.ي</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-bold text-emerald-600">
                        <span>العمولة ({{ number_format($shipment->package_commission_rate, 0) }}%)</span>
                        <span>{{ number_format($shipment->package_commission_amount, 0) }} ر.ي</span>
                    </div>
                </div>

                {{-- العسل (يظهر فقط إذا كان هناك عسل) --}}
                @if ($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0 || $shipment->honey_fee > 0)
                    <div class="p-4 rounded-2xl border border-amber-100/50 bg-amber-50/30">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black text-amber-700 uppercase">شحن العسل</span>
                            <span class="text-sm font-black text-amber-700">{{ number_format($shipment->honey_fee, 0) }}
                                ر.ي</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] font-bold text-emerald-600">
                            <span>عمولة العسل ({{ number_format($shipment->honey_commission_rate, 0) }}%)</span>
                            <span>{{ number_format($shipment->honey_commission_amount, 0) }} ر.ي</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= بطاقة المالية (الخلاصة) ================= --}}
        <div class="bg-white p-5 rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.03)] mt-4">
            <div class="flex justify-between items-center mb-4">
                <div class="flex gap-2 items-center">
                    <div
                        class="flex justify-center items-center w-9 h-9 text-emerald-600 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 font-headline">الخلاصة المالية</h3>
                </div>
                <span
                    class="px-2.5 py-1 bg-slate-800 text-white text-[9px] font-black rounded-lg uppercase tracking-wider">
                    {{ $shipment->payment_method == 'partial_payment' ? 'دفع جزئي' : $shipment->payment_method }}
                </span>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center py-3 border-b border-dashed border-slate-100">
                    <span class="text-xs font-bold text-slate-500">إجمالي رسوم الشحن</span>
                    <span
                        class="text-base font-black text-slate-800">{{ number_format($shipment->total_amount, 0) }}</span>
                </div>

                {{-- إجمالي عمولة المكتب (المكان الجديد المتميز) --}}
                <div
                    class="flex justify-between items-center py-3 bg-emerald-50/50 px-3 rounded-xl border border-emerald-100">
                    <span class="text-xs font-black text-emerald-700 uppercase">إجمالي العمولة </span>
                    <span
                        class="text-base font-black text-emerald-600">{{ number_format($shipment->total_commission, 0) }}
                        ر.ي</span>
                </div>

                @if ($shipment->payment_method == 'partial_payment')
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs font-bold text-slate-400">المبلغ المدفوع</span>
                        <span
                            class="text-sm font-bold text-emerald-600">{{ number_format($shipment->partial_amount, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 bg-rose-50 px-3 rounded-xl">
                        <span class="text-xs font-black text-rose-600 uppercase">المتبقي للتحصيل</span>
                        <span class="text-lg font-black text-rose-600">{{ number_format($remainingAmount, 0) }} ر.ي</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ================= مودال تحصيل الدفع المتبقي (الجوال) ================= --}}
        @if (!$shipment->is_returned && !in_array($shipment->payment_method, ['prepaid', 'customer_credit']))
            <template x-teleport="body">
                <div x-show="showPaymentModal" x-cloak
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
                    <div x-show="showPaymentModal" x-transition.opacity duration.300ms @click="showPaymentModal = false"
                        class="absolute inset-0 backdrop-blur-md bg-slate-900/60"></div>

                    <div x-show="showPaymentModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="relative p-6 w-full max-w-sm text-center bg-white rounded-3xl border shadow-2xl border-slate-100">

                        <div
                            class="flex justify-center items-center mx-auto mb-4 w-20 h-20 text-emerald-500 bg-emerald-50 rounded-full animate-bounce">
                            <span class="material-symbols-outlined text-[40px]">payments</span>
                        </div>

                        <h3 class="mb-2 text-lg font-black text-slate-800 font-headline">تنبيه تحصيل مالي!</h3>
                        <p class="mb-6 text-xs font-bold leading-relaxed text-slate-500">
                            هذا الطرد غير مدفوع مسبقاً بالكامل. الرجاء استلام المبلغ التالي من العميل قبل تأكيد عملية
                            التسليم.
                        </p>

                        <div class="p-5 mb-6 bg-emerald-50 rounded-2xl border border-emerald-100">
                            <p class="mb-1 text-[10px] font-black text-emerald-600/70">المبلغ المطلوب تحصيله</p>
                            <p class="font-mono text-3xl font-black text-emerald-700 dir-ltr">
                                {{ number_format($remainingAmount, 0) }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="showPaymentModal = false"
                                class="flex-1 h-12 text-xs font-black rounded-xl transition-colors text-slate-600 bg-slate-100 hover:bg-slate-200">
                                تراجع
                            </button>
                            <button type="button"
                                @click="document.getElementById('updateStatusInputMobile').value = 'delivered'; document.getElementById('updateStatusFormMobile').submit();"
                                class="flex-[2] h-12 bg-emerald-500 text-white hover:bg-emerald-600 rounded-xl font-black text-xs shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">verified</span>
                                تم استلام المبلغ
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        @endif

        {{-- ================= مودال إرجاع المبلغ للعميل للمرتجعات (الجوال) ================= --}}
        @if ($shipment->is_returned && $refundAmount > 0)
            <template x-teleport="body">
                <div x-show="showRefundModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
                    <div x-show="showRefundModal" x-transition.opacity duration.300ms @click="showRefundModal = false"
                        class="absolute inset-0 backdrop-blur-md bg-slate-900/60"></div>

                    <div x-show="showRefundModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="relative p-6 w-full max-w-sm text-center bg-white rounded-3xl border shadow-2xl border-slate-100">

                        <div
                            class="flex justify-center items-center mx-auto mb-4 w-20 h-20 text-amber-500 bg-amber-50 rounded-full animate-bounce">
                            <span class="material-symbols-outlined text-[40px]">currency_exchange</span>
                        </div>

                        <h3 class="mb-2 text-lg font-black text-slate-800 font-headline">تنبيه إرجاع مالي!</h3>
                        <p class="mb-6 text-xs font-bold leading-relaxed text-slate-500">
                            هذا الطرد مرتجع للتاجر وقد تم دفع رسومه مسبقاً. الرجاء إرجاع المبلغ التالي للعميل يدوياً قبل
                            إغلاق دورة الطرد.
                        </p>

                        <div class="p-5 mb-6 bg-amber-50 rounded-2xl border border-amber-100">
                            <p class="mb-1 text-[10px] font-black text-amber-600/70">المبلغ المطلوب إرجاعه</p>
                            <p class="font-mono text-3xl font-black text-amber-700 dir-ltr">
                                {{ number_format($refundAmount, 0) }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" @click="showRefundModal = false"
                                class="flex-1 h-12 text-xs font-black rounded-xl transition-colors text-slate-600 bg-slate-100 hover:bg-slate-200">
                                تراجع
                            </button>
                            <button type="button"
                                @click="document.getElementById('updateStatusInputMobile').value = 'returned'; document.getElementById('updateStatusFormMobile').submit();"
                                class="flex-[2] h-12 bg-amber-500 text-white hover:bg-amber-600 rounded-xl font-black text-xs shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">done_all</span>
                                تم الإرجاع للعميل
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        @endif

    </div>
@endsection
