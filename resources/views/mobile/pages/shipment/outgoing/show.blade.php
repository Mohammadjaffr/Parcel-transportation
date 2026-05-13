@extends('mobile.layouts.app')

@section('title', 'تفاصيل الطرد - ' . $shipment->bond_number)

@section('content')
    {{-- تم تقليل pb-24 إلى pb-8 لأننا أزلنا الزر العائم --}}
    <div class="flex relative flex-col gap-5 px-4 pt-6 pb-8 min-h-screen bg-slate-50/50">

        {{-- ================= الهيدر السريع ================= --}}
       <div class="flex justify-between items-center">
    <div class="flex gap-3 items-center">
        <a href="{{ route('shipment.outgoing.index') }}"
            class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90">
            <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
        </a>
        <div>
            <h1 class="text-lg font-black font-headline text-slate-800">رقم الطرد</h1>
            <p class="text-sm font-bold tracking-wider text-primary">{{ $shipment->bond_number }}</p>
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

        // 💡 اللوجيك الذكي لشارة المرتجع
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
    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-bold text-xs shadow-sm {{ $colorClass }}">
        <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
        {{ $name }}
    </div>
</div>

{{-- ================= أزرار الإجراءات الراقية (Premium Actions) ================= --}}
<div class="flex gap-3 items-center mt-1">

    @php
        $currentStatus = $shipment->status;
        $availableStatuses = [];

        // 💡 التعديل هنا: فصل إجراءات المرتجع عن الطرد العادي
        if ($shipment->is_returned && $currentStatus !== 'returned') {
            // خيار واحد فقط للمُرسل: تسليم المرتجع للتاجر لإنهاء الدورة
            $availableStatuses = [
                'returned' => [
                    'label' => 'تأكيد استلام التاجر للمرتجع',
                    'icon' => 'inventory_2',
                    'bg_color' => 'bg-emerald-50',
                    'text_color' => 'text-emerald-600'
                ],
            ];
        } elseif (!$shipment->is_returned) {
            if ($currentStatus === 'pending') {
                $availableStatuses = [
                    'returned' => [
                        'label' => 'إلغاء الطرد (مرتجع)',
                        'icon' => 'cancel',
                        'bg_color' => 'bg-rose-50',
                        'text_color' => 'text-rose-600'
                    ],
                ];
            } elseif ($currentStatus === 'in_transit') {
                $availableStatuses = [
                    'delivered' => [
                        'label' => 'تم التسليم بنجاح',
                        'icon' => 'check_circle',
                        'bg_color' => 'bg-emerald-50',
                        'text_color' => 'text-emerald-600'
                    ],
                    'returned' => [
                        'label' => 'فشل التسليم (مرتجع)',
                        'icon' => 'assignment_return',
                        'bg_color' => 'bg-rose-50',
                        'text_color' => 'text-rose-600'
                    ],
                ];
            }
        }
    @endphp

    @if(!empty($availableStatuses))
        <div class="flex-[2] relative" x-data="{ openStatusMenu: false }">
            <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" x-ref="statusForm">
                @csrf
                <input type="hidden" name="status" x-ref="statusInput">

                {{-- الزر الرئيسي (يتغير لونه للأحمر إذا كان مرتجعاً) --}}
                <button type="button" @click="openStatusMenu = !openStatusMenu" @click.outside="openStatusMenu = false"
                    class="w-full flex items-center justify-between px-4 h-12 {{ $shipment->is_returned ? 'bg-rose-500 hover:bg-rose-600 shadow-[0_8px_20px_rgba(244,63,94,0.2)]' : 'bg-slate-800 hover:bg-slate-700 shadow-[0_8px_20px_rgba(30,41,59,0.2)]' }} text-white rounded-2xl font-bold text-xs active:scale-95 transition-all">
                    <div class="flex gap-2 items-center">
                        <span class="material-symbols-outlined text-[18px]">{{ $shipment->is_returned ? 'assignment_returned' : 'update' }}</span>
                        <span>{{ $shipment->is_returned ? 'إجراءات الطرد المرتجع' : 'تحديث حالة الطرد' }}</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300"
                        :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                </button>

                {{-- القائمة المنسدلة الذكية --}}
                <div x-show="openStatusMenu" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="absolute top-full right-0 mt-2 w-full bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_15px_40px_-10px_rgba(0,0,0,0.15)] border border-slate-100 p-1.5 z-[60]">

                    @foreach($availableStatuses as $value => $data)
                        <button type="button" @click="$refs.statusInput.value = '{{ $value }}'; $refs.statusForm.submit()"
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
        {{-- الحالة النهائية (مغلق) --}}
        <div class="flex-[2] flex items-center justify-center gap-2 h-12 bg-slate-50 text-slate-400 rounded-2xl font-bold text-[10px] border border-slate-100">
            <span class="material-symbols-outlined text-[16px]">{{ $shipment->status === 'returned' ? 'done_all' : 'lock' }}</span>
            {{ $shipment->status === 'returned' ? 'تم تسليم المرتجع وإغلاق الطرد' : 'تم إغلاق دورة حياة الطرد' }}
        </div>
    @endif

    {{-- زر الطباعة --}}
    <a href="{{ route('receipt.generate', ['type' => 'sender', 'id' => $shipment->uuid]) }}" target="_blank"
        class="flex flex-1 gap-2 justify-center items-center h-12 text-xs font-bold bg-white rounded-2xl border shadow-sm transition-all text-slate-600 border-slate-100 hover:bg-slate-50 active:scale-95">
        <span class="material-symbols-outlined text-[18px]">print</span>
        طباعة
    </a>
        <a href="{{ route('receipt.generate', ['type' => 'thermal', 'id' => $shipment->uuid]) }}" target="_blank" title="طباعة سند حرارية"
        class="flex flex-1 gap-2 justify-center items-center h-12 text-xs font-bold bg-white rounded-2xl border shadow-sm transition-all text-slate-600 border-slate-100 hover:bg-slate-50 active:scale-95">
                                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>

        سند 
    </a>
</div>
        {{-- ================= بطاقة المسار (من -> إلى) ================= --}}
        <div
            class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
            {{-- تأثيرات الإضاءة الخلفية (Glassmorphism Glow) --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-primary/5"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-emerald-500/5">
            </div>

            <div class="flex justify-between items-center mb-8">
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
                // تجهيز رسائل الواتساب الديناميكية
                $senderMsg = "مرحباً *" . ($shipment->senderCustomer?->name ?? 'عميلنا العزيز') . "*،\nتم إصدار بوليصة شحن طردك برقم: *" . $shipment->bond_number . "*";

                // حساب المبلغ المتبقي للمستلم
                $remainingAmount = $shipment->total_amount - $shipment->partial_amount;
                $receiverMsg = "مرحباً *" . ($shipment->receiverCustomer?->name ?? 'عميلنا العزيز') . "*،\nلديك طرد قادم إليك برقم بوليصة: *" . $shipment->bond_number . "*\n";
                if (in_array($shipment->payment_method, ['cod', 'partial_payment'])) {
                    $receiverMsg .= "المبلغ المطلوب عند الاستلام: *" . number_format($remainingAmount, 0) . "* ريال.";
                } else {
                    $receiverMsg .= "الطرد مدفوع مسبقاً، لا توجد رسوم إضافية عند الاستلام.";
                }
            @endphp

            {{-- التايم لاين (Timeline) الحديث --}}
            <div class="relative pr-6 pl-2 space-y-8">
                {{-- الخط المتصل المتدرج --}}
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
                                <span
                                    class="block mt-0.5 text-xs font-medium text-right text-slate-500 dir-ltr"><x-phone-number :value="$shipment->senderCustomer?->phone ?? '---'" class="text-[11px] font-bold text-gray-500 dark:text-bodydark" /></span>
                            </div>
                            @if($shipment->senderCustomer?->phone)
                                <div class="flex gap-1.5 items-center">
                                    {{-- زر الواتساب للمرسل --}}
                                    <a href="{{ $shipment->sender_whatsapp_link }}"
                                        target="_blank"
                                        class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all group">
                                        <svg class="w-5 h-5 fill-[#25D366]" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                    </a>
                                    {{-- زر الاتصال للمرسل --}}
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
                                <span
                                    class="block mt-0.5 text-xs font-medium text-right text-primary/70 dir-ltr"><x-phone-number :value="$shipment->receiverCustomer?->phone ?? '---'" class="text-[11px] font-bold text-gray-500 dark:text-bodydark" /></span>
                            </div>
                            @if($shipment->receiverCustomer?->phone)
                                <div class="flex gap-1.5 items-center">
                                    {{-- زر الواتساب للمستلم --}}
                                    <a href="{{ $shipment->receiver_whatsapp_link }}"
                                        target="_blank"
                                        class="w-10 h-10 bg-white rounded-xl shadow-sm border border-primary/10 flex items-center justify-center hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all">
                                        <svg class="w-5 h-5 fill-[#25D366]" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                    </a>
                                    {{-- زر الاتصال للمستلم --}}
                                    <a href="tel:{{ $shipment->receiverCustomer?->phone }}"
                                        class="flex justify-center items-center w-10 h-10 text-white rounded-xl shadow-sm transition-all bg-primary shadow-primary/30 hover:bg-primary/90 active:scale-95">
                                        <span class="material-symbols-outlined text-[18px]">call</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= بطاقة الرحلة / الإرسالية (تظهر فقط إذا كان الطرد مرتبطاً بإرسالية) ================= --}}
        @if($shipment->package)
            @php
                // تجهيز رسالة الواتساب للسائق
                $driverMsg = "مرحباً كابتن *" . ($shipment->package->driver->name ?? 'السائق') . "*،\nبخصوص الطرد رقم: *" . $shipment->bond_number . "*\nالموجود ضمن الإرسالية رقم: *" . $shipment->package->tracking_number . "*";
            @endphp

            <div
                class="bg-gradient-to-br from-white to-blue-50/30 p-6 rounded-[2rem] border border-blue-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
                {{-- تأثيرات الإضاءة --}}
                <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-blue-500/5"></div>

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
                    {{-- تفاصيل السائق --}}
                    <div
                        class="flex justify-between items-center p-4 rounded-2xl border border-blue-50 shadow-sm backdrop-blur-sm bg-white/80">
                        <div class="flex gap-3 items-center">
                            <div class="flex justify-center items-center w-10 h-10 rounded-full bg-slate-100 text-slate-500">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-400 mb-0.5 uppercase tracking-wider">السائق المسؤول
                                </p>
                                <p class="text-xs font-black text-slate-800">
                                    {{ $shipment->package->driver->name ?? 'غير محدد' }}
                                </p>
                                <p class="text-[10px] font-bold text-slate-500 dir-ltr text-right mt-0.5">
                                    <x-phone-number :value="$shipment->package->driver->phone ?? '---'" class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />
                                </p>
                            </div>
                        </div>

                        {{-- أزرار التواصل (واتساب + اتصال) --}}
                        @if($shipment->package->driver && $shipment->package->driver->phone)
                            <div class="flex gap-1.5 items-center">
                                {{-- زر الواتساب --}}
                                <a href="https://wa.me/{{ ltrim($shipment->package->driver->phone, '+') }}?text={{ urlencode($driverMsg) }}"
                                    target="_blank"
                                    class="w-10 h-10 bg-white rounded-xl shadow-sm border border-blue-100 flex items-center justify-center hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all">
                                    <svg class="w-5 h-5 fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>
                                </a>

                                {{-- زر الاتصال --}}
                                <a href="tel:{{ $shipment->package->driver->phone }}"
                                    class="flex justify-center items-center w-10 h-10 text-blue-600 bg-blue-50 rounded-xl border border-blue-100 shadow-sm transition-all hover:bg-blue-600 hover:text-white active:scale-95">
                                    <span class="material-symbols-outlined text-[18px]">call</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- تفاصيل الإرسالية المجمعة وزر الانتقال --}}
                    <div class="flex justify-between items-center p-4 bg-blue-600 rounded-2xl shadow-lg shadow-blue-600/20">
                        <div>
                            <p class="text-[9px] font-black text-blue-200 mb-0.5 uppercase tracking-wider">ضمن الإرسالية المجمعة
                            </p>
                            <p class="font-mono text-sm font-black tracking-widest text-white">
                                {{ $shipment->package->tracking_number }}
                            </p>
                        </div>

                        {{-- زر الانتقال لصفحة تفاصيل الإرسالية --}}
                        <a href="{{ route('shipmentpackage.outgoing.show', $shipment->package->id) }}"
                            class="flex items-center gap-1.5 bg-white text-blue-600 px-4 py-2 rounded-xl text-[10px] font-black hover:bg-blue-50 active:scale-95 transition-all shadow-sm">
                            التفاصيل
                            <span class="material-symbols-outlined text-[14px]">arrow_back_ios_new</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= بطاقة المحتويات (Bento Style) ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex gap-3 items-center mb-5">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gradient-to-br rounded-xl shadow-inner from-slate-100 to-slate-50 text-slate-600">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
                <h3 class="text-sm font-black text-slate-800 font-headline">محتويات الطرد</h3>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1.5 p-4 rounded-2xl border bg-slate-50/80 border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">نوع الطرد</span>
                    <span class="flex gap-1 items-center text-sm font-black text-slate-800">
                        {{ $shipment->package_type }}
                    </span>
                </div>
                @if($shipment->weight > 0)
                    <div class="flex flex-col gap-1.5 p-4 rounded-2xl border bg-slate-50/80 border-slate-100">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">الوزن الفعلي</span>
                        <span class="text-sm font-black text-slate-800">{{ $shipment->weight }} <span
                                class="text-xs font-bold text-slate-500">كجم
                            </span> ⚖️
                        </span>
                    </div>
                @endif

                @if($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0)
                    <div
                        class="flex overflow-hidden relative col-span-2 justify-around items-center p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border border-amber-100/50">
                        {{-- زخرفة للخلفية --}}
                        <span
                            class="material-symbols-outlined absolute -left-4 -bottom-4 text-[80px] text-amber-500/5 rotate-12">hive</span>

                        @if($shipment->no_gallons_honey > 0)
                            <div class="flex relative z-10 flex-col gap-1 items-center">
                                <span class="text-[10px] font-black text-amber-600/70">دباب عسل</span>
                                <span
                                    class="flex gap-1 items-center text-xl font-black text-amber-700">{{ $shipment->no_gallons_honey }}
                                    <span class="text-sm">🍯</span></span>
                            </div>
                        @endif

                        @if($shipment->no_gallons_honey > 0 && $shipment->no_honey_jars > 0)
                            <div class="relative z-10 w-px h-10 rounded-full bg-amber-200/50"></div>
                        @endif

                        @if($shipment->no_honey_jars > 0)
                            <div class="flex relative z-10 flex-col gap-1 items-center">
                                <span class="text-[10px] font-black text-amber-600/70">قوارير عسل</span>
                                <span
                                    class="flex gap-1 items-center text-xl font-black text-amber-700">{{ $shipment->no_honey_jars }}
                                    <span class="text-sm">🏺</span></span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if($shipment->notes)
                <div class="flex gap-3 items-start p-4 mt-3 rounded-2xl border border-rose-100 bg-rose-50/50">
                    <div class="p-1 mt-0.5 text-rose-500 bg-rose-100 rounded-full shrink-0">
                        <span class="material-symbols-outlined text-[14px]">priority_high</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-rose-500 block mb-0.5 uppercase tracking-wider">ملاحظات
                            هامة</span>
                        <p class="text-xs font-bold leading-relaxed text-rose-800">{{ $shipment->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ================= بطاقة المالية (Digital Wallet Style) ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex justify-between items-center mb-6">
                <div class="flex gap-3 items-center">
                    <div
                        class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl shadow-inner">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 font-headline">الخلاصة المالية</h3>
                </div>
                <div
                    class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-[10px] font-black flex items-center gap-1.5 shadow-sm">
                    @if($shipment->payment_method == 'prepaid') مدفوع مقدماً 💵
                    @elseif($shipment->payment_method == 'cod') دفع عند الاستلام 🚚
                    @elseif($shipment->payment_method == 'partial_payment') دفع جزئي 💳
                    @elseif($shipment->payment_method == 'customer_credit') آجل (حساب) 📝
                    @endif
                </div>
            </div>

            <div class="p-4 space-y-4 rounded-2xl border bg-slate-50/50 border-slate-100">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-black text-slate-500">إجمالي رسوم الشحن</span>
                    <span class="text-sm font-black text-slate-800">{{ number_format($shipment->total_amount, 0) }} <span
                            class="text-[10px] text-slate-400">ريال</span></span>
                </div>

                @if($shipment->payment_method == 'partial_payment')
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-black text-emerald-600">المبلغ المدفوع</span>
                        <span class="text-sm font-black text-emerald-600">{{ number_format($shipment->partial_amount, 0) }}
                            <span class="text-[10px]">ريال</span></span>
                    </div>

                    {{-- فاصل --}}
                    <div class="my-2 border-t border-dashed border-slate-200"></div>

                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-rose-500 uppercase tracking-wider mb-1">المبلغ المتبقي
                                للتحصيل</span>
                            <span class="text-xs font-bold text-slate-400">يدفع عند التسليم</span>
                        </div>
                        <span
                            class="text-2xl font-black text-rose-600">{{ number_format($shipment->total_amount - $shipment->partial_amount, 0) }}
                            <span class="text-xs font-bold text-rose-400">ريال</span></span>
                    </div>
                @elseif($shipment->payment_method == 'cod')
                    {{-- فاصل --}}
                    <div class="my-2 border-t border-dashed border-slate-200"></div>

                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-rose-500 uppercase tracking-wider mb-1">المطلوب
                                تحصيله</span>
                            <span class="text-xs font-bold text-slate-400">يدفع عند التسليم</span>
                        </div>
                        <span class="text-2xl font-black text-rose-600">{{ number_format($shipment->total_amount, 0) }} <span
                                class="text-xs font-bold text-rose-400">ريال</span></span>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection