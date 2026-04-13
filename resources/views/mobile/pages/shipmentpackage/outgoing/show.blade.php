@extends('mobile.layouts.app')

@section('title', 'تفاصيل الإرسالية - ' . $package->tracking_number)

@section('content')
    <div class="flex flex-col gap-5 px-4 pb-8 relative min-h-screen bg-slate-50/50 pt-6">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('shipmentpackage.outgoing.index') }}"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 hover:text-primary active:scale-90 transition-all">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-lg font-black font-headline text-slate-800">رقم الإرسالية</h1>
                    <p class="text-sm font-bold text-primary tracking-wider">{{ $package->tracking_number }}</p>
                </div>
            </div>

            {{-- حالة الإرسالية --}}
            @php
                $statusColors = [
                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'in_transit' => 'bg-blue-50 text-blue-600 border-blue-200',
                    'delivered' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                    'returned' => 'bg-rose-50 text-rose-600 border-rose-200',
                ];
                $statusIcons = [
                    'pending' => 'schedule',
                    'in_transit' => 'local_shipping',
                    'delivered' => 'check_circle',
                    'returned' => 'assignment_return',
                ];
                $statusNames = [
                    'pending' => 'قيد التجهيز',
                    'in_transit' => 'في الطريق',
                    'delivered' => 'مكتملة',
                    'returned' => 'مرتجعة',
                ];
                $colorClass = $statusColors[$package->status] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                $icon = $statusIcons[$package->status] ?? 'info';
                $name = $statusNames[$package->status] ?? $package->status;
            @endphp
            <div
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-bold text-xs shadow-sm {{ $colorClass }}">
                <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
                {{ $name }}
            </div>
        </div>

        {{-- ================= أزرار الإجراءات الراقية (Premium Actions) ================= --}}
        <div class="flex items-center gap-3 mt-1">

            @php
                $currentStatus = $package->status;
                $availableStatuses = [];

                if ($currentStatus === 'pending') {
                    $availableStatuses = [
                        'in_transit' => [
                            'label' => 'انطلاق الرحلة (في الطريق)',
                            'icon' => 'local_shipping',
                            'bg_color' => 'bg-blue-50',
                            'text_color' => 'text-blue-600'
                        ],
                        'returned' => [
                            'label' => 'إلغاء الرحلة',
                            'icon' => 'cancel',
                            'bg_color' => 'bg-rose-50',
                            'text_color' => 'text-rose-600'
                        ],
                    ];
                } elseif ($currentStatus === 'in_transit') {
                    $availableStatuses = [
                        'delivered' => [
                            'label' => 'إنهاء الرحلة (تم التسليم)',
                            'icon' => 'check_circle',
                            'bg_color' => 'bg-emerald-50',
                            'text_color' => 'text-emerald-600'
                        ],
                        'returned' => [
                            'label' => 'فشل الرحلة (مرتجع)',
                            'icon' => 'assignment_return',
                            'bg_color' => 'bg-rose-50',
                            'text_color' => 'text-rose-600'
                        ],
                    ];
                }
            @endphp

            @if(!empty($availableStatuses))
                <div class="flex-[2] relative" x-data="{ openStatusMenu: false }">
                    <form action="{{ route('shipmentpackage.updateStatus', $package->id) }}" method="POST" x-ref="statusForm">
                        @csrf
                        <input type="hidden" name="status" x-ref="statusInput">

                        {{-- الزر الرئيسي --}}
                        <button type="button" @click="openStatusMenu = !openStatusMenu" @click.outside="openStatusMenu = false"
                            class="w-full flex items-center justify-between px-4 h-12 bg-slate-800 text-white rounded-2xl font-bold text-xs shadow-[0_8px_20px_rgba(30,41,59,0.2)] hover:bg-slate-700 active:scale-95 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">update</span>
                                <span>تحديث حالة الرحلة</span>
                            </div>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-300"
                                :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        {{-- القائمة المنسدلة الذكية --}}
                        <div x-show="openStatusMenu" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            class="absolute top-full right-0 mt-2 w-full bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_15px_40px_-10px_rgba(0,0,0,0.15)] border border-slate-100 p-1.5 z-[60]">

                            <div class="px-3 py-2 border-b border-slate-50 mb-1">
                                <span class="text-[9px] font-bold text-slate-400">تنبيه: سيتم تحديث حالة جميع الطرود بداخلها
                                    تلقائياً.</span>
                            </div>

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
                <div
                    class="flex-[2] flex items-center justify-center gap-2 h-12 bg-slate-50 text-slate-400 rounded-2xl font-bold text-[10px] border border-slate-100">
                    <span class="material-symbols-outlined text-[16px]">lock</span>
                    الرحلة مغلقة
                </div>
            @endif

            {{-- زر الطباعة --}}
            <a href="#"
                class="flex-1 flex items-center justify-center gap-2 h-12 bg-white text-slate-600 rounded-2xl font-bold text-xs shadow-sm border border-slate-100 hover:bg-slate-50 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span>
                طباعة
            </a>
        </div>

        {{-- ================= بطاقة المسار (من الفرع -> السائق) ================= --}}
        <div
            class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="flex items-center justify-between mb-8 relative z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center text-primary shadow-inner">
                        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                    </div>
                    <h3 class="font-black text-slate-800 text-sm font-headline">تفاصيل التكليف</h3>
                </div>
                <span class="px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-bold border border-slate-100">
                    {{ $package->created_at->format('Y-m-d h:i A') }}
                </span>
            </div>

            @php
                // رسالة واتساب للسائق
                $driverMsg = "مرحباً كابتن *" . ($package->driver->name ?? 'السائق') . "*،\nتم تكليفك برحلة شحن جديدة رقم: *" . $package->tracking_number . "*\nعدد الطرود: *" . ($package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0)) . "* طرد.";
            @endphp

            {{-- التايم لاين الحديث للإرسالية --}}
            <div class="relative pl-2 pr-6 space-y-8 z-10">
                {{-- الخط المتصل المتدرج --}}
                <div
                    class="absolute right-[11px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-slate-200 via-primary/30 to-primary rounded-full">
                </div>

                {{-- نقطة الانطلاق (فرع التجميع) --}}
                <div class="relative z-10">
                    <div
                        class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-white border-4 border-slate-300 rounded-full shadow-sm">
                    </div>
                    <div
                        class="bg-slate-50/50 p-3.5 rounded-2xl border border-slate-100/50 hover:bg-slate-50 transition-colors">
                        <span class="text-[10px] font-black text-slate-400 mb-1 block">نقطة الانطلاق (فرع التجميع)</span>
                        <div class="flex items-center justify-between">
                            <div>
                                <span
                                    class="font-black text-sm text-slate-800 block">{{ $package->senderBranch->name ?? 'مستودع غير محدد' }}</span>
                                <span class="text-xs text-slate-500 mt-0.5 block font-medium flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">person</span>
                                    بواسطة: {{ $package->creator->name ?? 'النظام' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- نقطة التكليف (السائق) --}}
                <div class="relative z-10">
                    <div
                        class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-primary ring-4 ring-primary/20 rounded-full {{ $package->status == 'in_transit' ? 'animate-pulse' : '' }}">
                    </div>
                    <div
                        class="bg-primary/[0.02] p-3.5 rounded-2xl border border-primary/10 hover:bg-primary/5 transition-colors">
                        <span class="text-[10px] font-black text-primary/60 mb-1 block">السائق المسؤول</span>
                        <div class="flex items-center justify-between">
                            <div>
                                <span
                                    class="font-black text-sm text-primary block">{{ $package->driver->name ?? 'غير محدد' }}</span>
                                <span
                                    class="text-xs text-primary/70 dir-ltr text-right mt-0.5 block font-medium">{{ $package->driver->phone }}</span>
                            </div>
                            @if($package->driver && $package->driver->phone)
                                <div class="flex items-center gap-1.5">
                                    {{-- زر الواتساب للسائق --}}
                                    <a href="https://wa.me/{{ ltrim($package->driver->phone, '+') }}?text={{ urlencode($driverMsg) }}"
                                        target="_blank"
                                        class="w-10 h-10 bg-white rounded-xl shadow-sm border border-primary/10 flex items-center justify-center hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all">
                                        <svg class="w-5 h-5 fill-[#25D366]" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                    </a>
                                    {{-- زر الاتصال للسائق --}}
                                    <a href="tel:{{ $package->driver->phone }}"
                                        class="w-10 h-10 bg-primary text-white rounded-xl shadow-sm shadow-primary/30 flex items-center justify-center hover:bg-primary/90 active:scale-95 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">call</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= بطاقة الطرود المضمنة ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center text-slate-600 shadow-inner">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                    </div>
                    <h3 class="font-black text-slate-800 text-sm font-headline">الطرود المضمنة</h3>
                </div>
                <div class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-[10px] font-black shadow-sm">
                    إجمالي: {{ $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0) }} طرد
                </div>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($package->shipments as $shipment)
                    <div
                        class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100 flex items-center gap-4 group hover:bg-white hover:shadow-md hover:border-primary/20 transition-all duration-300">

                        <div
                            class="w-12 h-12 bg-white rounded-[14px] flex items-center justify-center text-slate-400 shrink-0 border border-slate-100 shadow-sm group-hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[22px]">package_2</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <span
                                    class="text-sm font-black text-slate-800 font-mono tracking-tight">{{ $shipment->bond_number }}</span>
                                <span
                                    class="text-[9px] font-black text-primary bg-primary/5 px-2 py-0.5 rounded-md flex items-center gap-1 border border-primary/10">
                                    <span class="material-symbols-outlined text-[10px]">store</span>
                                    {{ $shipment->receiverBranch->name ?? 'مستودع' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 mt-1">
                                <span class="material-symbols-outlined text-[12px] text-slate-400">person</span>
                                <span class="text-[10px] font-bold text-slate-500 truncate">المستلم:
                                    {{ $shipment->receiverCustomer->name ?? 'غير مسجل' }}</span>
                            </div>
                        </div>

                        {{-- أزرار التحكم --}}
                        {{-- أزرار التحكم --}}
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- زر الانتقال لتفاصيل الطرد --}}
                            <a href="{{ route('shipment.show', $shipment->id) }}"
                                class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-slate-300 hover:text-primary hover:bg-primary/5 border border-slate-100 shadow-sm transition-all active:scale-90"
                                title="عرض التفاصيل">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                            </a>

                            {{-- 💡 اللوجيك: إخفاء زر الفك إذا كانت الإرسالية في حالة نهائية (مكتملة أو مرتجعة) --}}
                            @if(!in_array($package->status, ['delivered']))
                                {{-- زر فك الارتباط --}}
                                <form
                                    action="{{ route('shipmentpackage.removeShipment', ['package' => $package->id, 'shipment' => $shipment->id]) }}"
                                    method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من فك ارتباط هذا الطرد وإعادته للمستودع؟');">
                                    @csrf
                                    <button type="submit"
                                        class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-rose-300 hover:text-rose-500 hover:bg-rose-50 border border-slate-100 shadow-sm transition-all active:scale-90"
                                        title="فك ارتباط الطرد">
                                        <span class="material-symbols-outlined text-[16px]">link_off</span>
                                    </button>
                                </form>
                            @else
                                {{-- زر مقفل للتوضيح للموظف أنه لا يمكن التعديل --}}
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 border border-slate-100 cursor-not-allowed"
                                    title="لا يمكن فك ارتباط الطرد من رحلة مغلقة">
                                    <span class="material-symbols-outlined text-[16px]">lock</span>
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="text-center py-8">
                        <span class="material-symbols-outlined text-[40px] text-slate-200 mb-2">inbox</span>
                        <p class="text-xs font-bold text-slate-400">لا توجد طرود مضمنة في هذه الإرسالية.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection