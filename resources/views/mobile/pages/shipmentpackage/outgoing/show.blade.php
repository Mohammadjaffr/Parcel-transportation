@extends('mobile.layouts.app')

@section('title', 'تفاصيل الشحنة - ' . $package->id)

@section('content')
@php
    $alpineAvailableShipments = isset($availableShipments) ? $availableShipments->map(function ($s) {
        return [
            'id' => $s->id,
            'uuid' => $s->uuid ?? '',
            'bond_number' => $s->bond_number ?? '',
            'code' => $s->code ?? '',
            'receiver_name' => $s->receiverCustomer->name ?? 'غير مسجل',
            'branch_name' => $s->receiverBranch->name ?? 'مستودع',
        ];
    })->values() : collect();

    $currentBranchCode = auth()->user()->branch->code ?? 'B' . (auth()->user()->branch_id ?? '1');
@endphp

    {{-- إضافة x-data رئيسي للتحكم بالمودال في مستوى الصفحة بالكامل ومسح الباركود --}}
    <div class="flex relative flex-col gap-5 px-4 pt-6 pb-8 min-h-screen bg-slate-50/50" x-data="outgoingPackageShowMobile({{ $alpineAvailableShipments->toJson() }}, '{{ $currentBranchCode }}')">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex justify-between items-center">
            <div class="flex gap-3 items-center">
                <a href="{{ route('shipmentpackage.outgoing.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-lg font-black font-headline text-slate-800">رقم الشحنة</h1>
                    <p class="text-sm font-bold tracking-wider text-primary">{{ $package->id }}</p>
                </div>
            </div>

            {{-- حالة الشحنة --}}
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
        <div class="flex gap-3 items-center mt-1">

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
                        'pending' => [
                            'label' => 'إرجاع للمكتب',
                            'icon' => 'undo',
                            'bg_color' => 'bg-amber-50',
                            'text_color' => 'text-amber-600'
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
                            <div class="flex gap-2 items-center">
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

                            <div class="px-3 py-2 mb-1 border-b border-slate-50">
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
            <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->uuid]) }}" target="_blank"
                class="flex flex-1 gap-2 justify-center items-center h-12 text-xs font-bold bg-white rounded-2xl border shadow-sm transition-all text-slate-600 border-slate-100 hover:bg-slate-50 active:scale-95">
                <span class="material-symbols-outlined text-[18px]">print</span>
                طباعة
            </a>
        </div>

        {{-- ================= بطاقة المسار (من الفرع -> السائق -> الوجهات) ================= --}}
        <div
            class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-primary/5"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-emerald-500/5">
            </div>

            <div class="flex relative z-10 justify-between items-center mb-8">
                <div class="flex gap-3 items-center">
                    <div
                        class="flex justify-center items-center w-10 h-10 bg-gradient-to-br rounded-xl shadow-inner from-primary/20 to-primary/5 text-primary">
                        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 font-headline">تفاصيل التكليف وخط السير</h3>
                </div>
                <span class="px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-bold border border-slate-100">
                    {{ $package->created_at->format('Y-m-d h:i A') }}
                </span>
            </div>

            @php
                // رسالة واتساب للسائق
                $driverMsg = "مرحباً كابتن *" . ($package->driver->name ?? 'السائق') . "*،\nتم تكليفك برحلة شحن جديدة رقم: *" . $package->id . "*\nعدد الطرود: *" . ($package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0)) . "* طرد.";

                // 💡 اللوجيك: استخراج الوجهات وتصنيفها (فروع داخلية ومكاتب خارجية)
                $internalBranches = collect();
                $externalOffices = collect();

                if ($package->shipments) {
                    foreach ($package->shipments as $ship) {
                        if ($ship->receiverOfficeBranch) {
                            $externalOffices->put($ship->receiverOfficeBranch->id, [
                                'name' => $ship->receiverOfficeBranch->office->name . ' - ' . $ship->receiverOfficeBranch->name,
                                'branch' => $ship->receiverOfficeBranch
                            ]);
                        } elseif ($ship->receiverBranch) {
                            $internalBranches->put($ship->receiverBranch->id, $ship->receiverBranch->name);
                        }
                    }
                }
            @endphp

            {{-- التايم لاين الحديث للإرسالية --}}
            <div class="relative z-10 pr-6 pl-2 space-y-8">
                {{-- الخط المتصل المتدرج --}}
                <div
                    class="absolute right-[11px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-slate-200 via-primary/50 to-emerald-400 rounded-full">
                </div>

                {{-- 1. نقطة الانطلاق (فرع التجميع) --}}
                <div class="relative z-10">
                    <div
                        class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-white border-4 border-slate-300 rounded-full shadow-sm">
                    </div>
                    <div
                        class="p-3.5 rounded-2xl border transition-colors bg-slate-50/50 border-slate-100/50 hover:bg-slate-50">
                        <span class="text-[10px] font-black text-slate-400 mb-1 block">نقطة الانطلاق (فرع التجميع)</span>
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="block text-sm font-black text-slate-800">{{ $package->senderBranch->name ?? 'مستودع غير محدد' }}</span>
                                <span class="block flex gap-1 items-center mt-0.5 text-xs font-medium text-slate-500">
                                    <span class="material-symbols-outlined text-[14px]">person</span>
                                    بواسطة: {{ $package->creator->name ?? 'النظام' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. نقطة التكليف (السائق) --}}
                <div class="relative z-10">
                    <div
                        class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-primary ring-4 ring-primary/20 rounded-full {{ $package->status == 'in_transit' ? 'animate-pulse' : '' }}">
                    </div>
                    <div
                        class="bg-primary/[0.02] p-3.5 rounded-2xl border border-primary/10 hover:bg-primary/5 transition-colors">
                        <span class="text-[10px] font-black text-primary/60 mb-1 block">السائق المسؤول</span>
                        <div class="flex justify-between items-center">
                            <div>
                                <span
                                    class="block text-sm font-black text-primary">{{ $package->driver->name ?? 'غير محدد' }}</span>
                                <span
                                    class="block mt-0.5 text-xs font-medium text-right text-primary/70 dir-ltr">{{ $package->driver->phone }}</span>
                            </div>
                            @if($package->driver && $package->driver->phone)
                                <div class="flex gap-1.5 items-center">
                                    {{-- زر الواتساب للسائق --}}
                                    <a href="{{ $package->DriverDetection }}"
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
                                        class="flex justify-center items-center w-10 h-10 text-white rounded-xl shadow-sm transition-all bg-primary shadow-primary/30 hover:bg-primary/90 active:scale-95">
                                        <span class="material-symbols-outlined text-[18px]">call</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 3. الوجهات المقصودة (الفروع والمكاتب) --}}
                @if($internalBranches->isNotEmpty() || $externalOffices->isNotEmpty())
                    <div class="relative z-10">
                        <div
                            class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-emerald-500 ring-4 ring-emerald-500/20 rounded-full">
                        </div>
                        <div class="p-3.5 rounded-2xl border border-emerald-100 transition-colors bg-emerald-50/50 hover:bg-emerald-50">
                            
                            {{-- الفروع الداخلية --}}
                            @if($internalBranches->isNotEmpty())
                                <div class="mb-3">
                                    <span class="text-[10px] font-black text-slate-400 mb-2 block uppercase tracking-wider">الفروع الداخلية</span>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($internalBranches as $name)
                                            <span class="px-2.5 py-1.5 bg-white border border-slate-100 text-slate-700 rounded-lg text-[10px] font-bold shadow-sm flex items-center gap-1.5">
                                                <span class="material-symbols-outlined text-[14px] text-slate-400">store</span>
                                                {{ $name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- المكاتب الخارجية --}}
                            @if($externalOffices->isNotEmpty())
                                <div>
                                    <span class="text-[10px] font-black text-emerald-600/70 mb-2 block uppercase tracking-wider">المكاتب الخارجية</span>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($externalOffices as $officeId => $officeData)
                                            <div class="flex items-center bg-white border border-emerald-100 rounded-lg shadow-sm pr-2.5 pl-1 py-1 group">
                                                <div class="flex items-center gap-1.5 text-emerald-700 text-[10px] font-bold">
                                                    <span class="material-symbols-outlined text-[14px]">domain</span>
                                                    {{ $officeData['name'] }}
                                                </div>
                                                <div class="w-[1px] h-3 bg-emerald-100 mx-2"></div>
                                                {{-- زر الواتساب للمكتب --}}
                                                @php
                                                    $package->external_office_branch = $officeData['branch'];
                                                    $whatsappLink = \App\Services\WhatsApp\WhatsAppLinkService::generate($package, 'ExternalOfficeDetection');
                                                @endphp
                                                @if($whatsappLink)
                                                    <a href="{{ $whatsappLink }}" target="_blank" title="مراسلة المكتب عبر الواتساب"
                                                       class="w-6 h-6 flex items-center justify-center rounded-md bg-emerald-50 text-emerald-600 hover:bg-[#25D366] hover:text-white transition-colors active:scale-95">
                                                       <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                           <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                                       </svg>
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- ================= بطاقة الطرود المضمنة ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex justify-between items-center mb-5">
                <div class="flex gap-3 items-center">
                    <div
                        class="flex justify-center items-center w-10 h-10 bg-gradient-to-br rounded-xl shadow-inner from-slate-100 to-slate-50 text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-800 font-headline">الطرود المضمنة</h3>
                </div>
                <div class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-[10px] font-black shadow-sm">
                    إجمالي: {{ $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0) }} طرد
                </div>
            </div>

            <div class="flex flex-col gap-3">
                @forelse($package->shipments as $shipment)
                    <div
                        class="flex gap-4 items-center p-4 rounded-2xl border transition-all duration-300 bg-slate-50/50 border-slate-100 group hover:bg-white hover:shadow-md hover:border-primary/20">
                        <div
                            class="w-12 h-12 bg-white rounded-[14px] flex items-center justify-center text-slate-400 shrink-0 border border-slate-100 shadow-sm group-hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[22px]">package_2</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <span
                                    class="font-mono text-sm font-black tracking-tight text-slate-800">{{ $shipment->bond_number }}</span>
                                <span
                                    class="text-[9px] font-black text-primary bg-primary/5 px-2 py-0.5 rounded-md flex items-center gap-1 border border-primary/10">
                                    <span class="material-symbols-outlined text-[10px]">store</span>
                                    {{ $shipment->receiverBranch->name ?? 'مستودع' }}
                                </span>
                            </div>
                            <div class="flex gap-1 items-center mt-1">
                                <span class="material-symbols-outlined text-[12px] text-slate-400">person</span>
                                <span class="text-[10px] font-bold text-slate-500 truncate">المستلم:
                                    {{ $shipment->receiverCustomer->name ?? 'غير مسجل' }}</span>
                            </div>
                        </div>

                        {{-- أزرار التحكم (قائمة منسدلة ثلاث نقاط) --}}
                        <div class="relative shrink-0" x-data="{ menuOpen: false }">

                            {{-- زر الثلاث نقاط --}}
                            <button type="button" @click="menuOpen = !menuOpen" @click.outside="menuOpen = false"
                                class="flex justify-center items-center w-8 h-8 bg-white rounded-full border shadow-sm transition-all text-slate-400 hover:text-primary hover:bg-primary/5 border-slate-100 active:scale-90">
                                <span class="material-symbols-outlined text-[18px]">more_vert</span>
                            </button>

                            {{-- القائمة المنسدلة الذكية --}}
                            <div x-show="menuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                class="absolute left-0 top-full mt-2 w-48 bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_15px_40px_-10px_rgba(0,0,0,0.15)] border border-slate-100 p-1.5 z-[50]">

                                {{-- خيار عرض التفاصيل الذكي --}}
                                <a href="{{ $shipment->receiver_branch_id == auth()->user()->branch_id ? route('shipment.incoming.show', $shipment->id) : route('shipment.outgoing.show', $shipment->id) }}"
                                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 text-slate-700 transition-colors mb-1 active:scale-[0.98]">
                                    <div
                                        class="flex justify-center items-center w-7 h-7 rounded-lg bg-slate-100 text-slate-500">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    </div>
                                    <span class="text-xs font-black">عرض التفاصيل</span>
                                </a>

                                {{-- خيار فك الارتباط (يظهر فقط إذا لم تكن الرحلة منتهية) --}}
                                @if(!in_array($package->status, ['delivered', 'returned']))
                                    <button type="button" @click="
                                                                                deleteShipmentData = { 
                                                                                    bond_number: '{{ $shipment->bond_number }}', 
                                                                                    url: '{{ route('shipmentpackage.removeShipment', ['package' => $package->id, 'shipment' => $shipment->id]) }}' 
                                                                                }; 
                                                                                showDeleteModal = true; 
                                                                                menuOpen = false;
                                                                            "
                                        class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-rose-50 text-rose-600 transition-colors text-right active:scale-[0.98]">
                                        <div
                                            class="flex justify-center items-center w-7 h-7 text-rose-500 rounded-lg bg-rose-100/50">
                                            <span class="material-symbols-outlined text-[16px]">link_off</span>
                                        </div>
                                        <span class="text-xs font-black">فك ارتباط الطرد</span>
                                    </button>
                                @else
                                    {{-- خيار مقفل يوضح أن الرحلة مغلقة --}}
                                    <div class="flex gap-2.5 items-center px-3 py-2.5 rounded-xl cursor-not-allowed bg-slate-50 text-slate-400"
                                        title="لا يمكن فك ارتباط الطرد من رحلة مغلقة">
                                        <div
                                            class="flex justify-center items-center w-7 h-7 rounded-lg bg-slate-200/50 text-slate-400">
                                            <span class="material-symbols-outlined text-[16px]">lock</span>
                                        </div>
                                        <span class="text-xs font-black">مغلق (تم الاستلام)</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <span class="material-symbols-outlined text-[40px] text-slate-200 mb-2">inbox</span>
                        <p class="text-xs font-bold text-slate-400">لا توجد طرود مضمنة في هذه الشحنة.</p>
                    </div>
                @endforelse

                {{-- ================= زر إضافة طرد جديد يظهر فقط إذا الإرسالية غير مغلقة ================= --}}
                @if(!in_array($package->status, ['delivered','returned','received_at_branch']))
                    <button type="button" @click="isAddModalOpen = true"
                        class="flex gap-2 justify-center items-center mt-3 w-full h-12 text-xs font-bold rounded-2xl border-2 border-dashed transition-all border-slate-200 text-slate-400 hover:border-primary hover:text-primary hover:bg-primary/5 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">add_box</span>
                        إضافة طرد جديد للإشحنة
                    </button>
                @endif
            </div>
        </div>

        {{-- ================= نافذة الكاميرا للموبايل ================= --}}
        <div x-show="isScannerOpen" x-cloak x-transition.opacity
            class="fixed inset-0 z-[100000] flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-3">
            <div @click.away="stopCameraScanner()"
                class="w-full max-w-md bg-white rounded-[2.5rem] p-5 shadow-2xl border border-slate-100 flex flex-col gap-4">
                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-10 h-10 rounded-2xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[22px]">qr_code_scanner</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 font-headline">مسح باركود السند</h3>
                            <p class="text-[10px] font-bold text-slate-400">وجه الكاميرا نحو رمز الـ QR أعلى السند</p>
                        </div>
                    </div>
                    <button type="button" @click="stopCameraScanner()"
                        class="flex justify-center items-center w-9 h-9 rounded-full bg-slate-100 text-slate-500 active:scale-90">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <div class="overflow-hidden relative bg-black rounded-2xl aspect-square">
                    <div id="mobile-show-qr-reader" class="w-full h-full"></div>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs font-bold text-slate-500">الطرود المحددة: <strong class="text-primary" x-text="selectedShipmentIds.length"></strong></span>
                    <button type="button" @click="stopCameraScanner()"
                        class="px-4 py-2 text-xs font-black text-white rounded-xl bg-primary active:scale-95">
                        تم الانتهاء
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= نافذة إضافة طرد منبثقة (Bottom Sheet Modal مع مسح الباركود) ================= --}}
        @if(!in_array($package->status, ['delivered', 'returned', 'received_at_branch']) && isset($availableShipments))
            <div x-show="isAddModalOpen" x-cloak class="fixed inset-0 z-[100] flex justify-center items-end sm:items-center">

                {{-- الخلفية المظللة --}}
                <div x-show="isAddModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="absolute inset-0 backdrop-blur-sm bg-slate-900/60" @click="isAddModalOpen = false"></div>

                {{-- المودال نفسه --}}
                <div x-show="isAddModalOpen" x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                    x-transition:leave="transform transition ease-in duration-200" x-transition:leave-start="translate-y-0"
                    x-transition:leave-end="translate-y-full"
                    class="relative bg-white w-full sm:max-w-lg h-[85vh] sm:h-auto sm:max-h-[88vh] rounded-t-[2.5rem] sm:rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden">

                    {{-- شريط السحب والإغلاق --}}
                    <div class="flex z-10 justify-between items-center px-6 pt-5 pb-4 bg-white border-b border-slate-50 shrink-0">
                        <div>
                            <h2 class="text-base font-black text-slate-800 font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">add_circle</span>
                                إضافة طرد للإرسالية
                            </h2>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5">امسح باركود السند أو اختر من القائمة</p>
                        </div>
                        <button type="button" @click="isAddModalOpen = false"
                            class="flex justify-center items-center w-8 h-8 rounded-full bg-slate-100 text-slate-500 active:scale-90">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>

                    {{-- شريط أدوات مسح الباركود والبحث --}}
                    <div class="px-5 py-3.5 border-b bg-slate-50/70 shrink-0 border-slate-100 flex flex-col gap-2.5">
                        {{-- حقل الباركود وزر الكاميرا --}}
                        <div class="flex gap-2 items-center">
                            <div class="relative flex-1 group">
                                <input type="text" x-model="barcodeInput" x-ref="barcodeField"
                                    @keydown.enter.prevent="handleBarcodeScan(barcodeInput)"
                                    placeholder="امسح باركود السند (أو اكتب واضغط Enter)..."
                                    class="pr-10 pl-8 w-full h-11 text-xs font-bold bg-white rounded-xl border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 text-slate-700 outline-none">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-primary material-symbols-outlined text-[20px]">
                                    qr_code_scanner
                                </span>
                                <button type="button" x-show="barcodeInput" @click="barcodeInput = ''; $refs.barcodeField?.focus()"
                                    class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                            <button type="button" @click="startCameraScanner()"
                                class="flex gap-1 items-center justify-center px-3.5 h-11 text-xs font-black text-white rounded-xl bg-primary shadow-sm active:scale-95 shrink-0">
                                <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                            </button>
                        </div>

                        {{-- البحث النصي وأزرار التحديد السريع --}}
                        <div class="flex items-center justify-between gap-2">
                            <div class="relative flex-1">
                                <input type="text" x-model="searchShipment" placeholder="تصفية بالرقم أو المستلم..."
                                    class="pr-9 pl-3 w-full h-9 text-xs font-bold bg-white rounded-xl border border-slate-200 outline-none focus:border-primary text-slate-700">
                                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 material-symbols-outlined text-[16px]">search</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button type="button" @click="selectAllFiltered()"
                                    class="px-2.5 h-9 text-[11px] font-black rounded-lg text-primary bg-primary/10 hover:bg-primary/20 active:scale-95">
                                    تحديد الكل
                                </button>
                                <button type="button" x-show="selectedShipmentIds.length > 0" @click="clearSelection()"
                                    class="px-2.5 h-9 text-[11px] font-black rounded-lg text-rose-600 bg-rose-50 active:scale-95">
                                    إلغاء (<span x-text="selectedShipmentIds.length"></span>)
                                </button>
                            </div>
                        </div>

                        {{-- رسالة التغذية الراجعة عند المسح --}}
                        <template x-if="scanFeedback.message">
                            <div class="p-2 rounded-xl text-xs font-bold flex items-center gap-1.5"
                                :class="{
                                    'bg-emerald-50 text-emerald-700 border border-emerald-200': scanFeedback.type === 'success',
                                    'bg-amber-50 text-amber-700 border border-amber-200': scanFeedback.type === 'info',
                                    'bg-rose-50 text-rose-700 border border-rose-200': scanFeedback.type === 'error'
                                }">
                                <span class="material-symbols-outlined text-[16px]"
                                    x-text="scanFeedback.type === 'success' ? 'check_circle' : (scanFeedback.type === 'info' ? 'info' : 'error')"></span>
                                <span x-text="scanFeedback.message"></span>
                            </div>
                        </template>
                    </div>

                    {{-- قائمة الطرود المتاحة للإضافة --}}
                    <div class="overflow-y-auto flex-1 p-4 custom-scrollbar bg-slate-50/30">
                        <div class="space-y-2.5">
                            @forelse($availableShipments as $availShipment)
                                <div id="mobile-shipment-card-{{ $availShipment->id }}"
                                    class="flex justify-between items-center p-3.5 bg-white rounded-2xl border transition-all cursor-pointer"
                                    :class="isShipmentSelected({{ $availShipment->id }}) ? 'border-primary bg-primary/5 ring-1 ring-primary/30 shadow-sm' : 'border-slate-100 hover:border-slate-200'"
                                    @click="toggleShipment({{ $availShipment->id }})"
                                    x-show="searchShipment === '' || '{{ $availShipment->bond_number }}'.includes(searchShipment) || '{{ $availShipment->receiverCustomer->name ?? '' }}'.includes(searchShipment) || '{{ $availShipment->id }}'.includes(searchShipment)">

                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded-lg border flex items-center justify-center transition-colors"
                                            :class="isShipmentSelected({{ $availShipment->id }}) ? 'bg-primary border-primary text-white' : 'border-slate-300 bg-white'">
                                            <span class="material-symbols-outlined text-[14px]" x-show="isShipmentSelected({{ $availShipment->id }})">check</span>
                                        </div>
                                        <div>
                                            <div class="flex gap-2 items-center mb-0.5">
                                                <span class="font-mono text-xs font-black text-slate-800">{{ $availShipment->bond_number }}</span>
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-500">{{ $availShipment->receiverBranch->name ?? 'مستودع' }}</span>
                                            </div>
                                            <p class="text-[10px] font-bold text-slate-500 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">person</span>
                                                {{ $availShipment->receiverCustomer->name ?? 'غير مسجل' }}
                                            </p>
                                        </div>
                                    </div>

                                    <span class="text-[10px] font-bold px-2 py-1 rounded-lg"
                                        :class="isShipmentSelected({{ $availShipment->id }}) ? 'bg-primary text-white font-black' : 'text-slate-400 bg-slate-50'">
                                        <span x-text="isShipmentSelected({{ $availShipment->id }}) ? 'محدد' : 'تحديد'"></span>
                                    </span>
                                </div>
                            @empty
                                <div class="py-10 text-center">
                                    <span class="material-symbols-outlined text-[40px] text-slate-200 mb-2">inventory_2</span>
                                    <p class="text-xs font-bold text-slate-500">لا توجد طرود متاحة في المستودع</p>
                                    <p class="text-[10px] text-slate-400 mt-1">جميع الطرود تم شحنها أو تسليمها.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- الفوتر السفلي لتأكيد الإضافة --}}
                    <div class="p-4 bg-white border-t border-slate-100 flex items-center justify-between gap-3 shrink-0">
                        <div class="text-[11px] font-bold text-slate-500">
                            المحدد: <strong class="text-primary font-black" x-text="selectedShipmentIds.length"></strong> من <span>{{ $availableShipments->count() }}</span>
                        </div>
                        <form action="{{ route('shipmentpackage.addShipment', $package->id) }}" method="POST"
                            @submit="isSubmitting = true">
                            @csrf
                            <template x-for="sId in selectedShipmentIds" :key="'mobile-submit-s-' + sId">
                                <input type="hidden" name="shipment_ids[]" :value="sId">
                            </template>
                            <button type="submit" :disabled="selectedShipmentIds.length === 0 || isSubmitting"
                                class="flex gap-1.5 items-center px-5 h-11 text-xs font-black text-white rounded-xl shadow-md transition-all bg-primary hover:bg-primary-hover shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed active:scale-95">
                                <span class="material-symbols-outlined text-[18px]" x-show="!isSubmitting">add_box</span>
                                <span class="material-symbols-outlined text-[18px] animate-spin" x-show="isSubmitting" x-cloak>progress_activity</span>
                                <span>إضافة الطرود (<span x-text="selectedShipmentIds.length"></span>)</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            {{-- الخلفية المظللة --}}
            <div x-show="showDeleteModal" x-transition.opacity.duration.300ms
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto"
                @click="showDeleteModal = false">
            </div>

            {{-- بطاقة المودال --}}
            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto text-center">

                {{-- شريط الإغلاق العلوي --}}
                <div @click="showDeleteModal = false"
                    class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                {{-- الأيقونة --}}
                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 text-rose-500 rounded-[1.5rem]">
                    <span class="text-4xl material-symbols-outlined">link_off</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد فك الارتباط</h3>

                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من فك ارتباط الطرد رقم <br>
                    <span class="text-base font-bold text-slate-800 font-headline"
                        x-text="deleteShipmentData.bond_number"></span>؟<br>
                    <span class="text-rose-500/80">سيتم إرجاع هذا الطرد إلى المستودع.</span>
                </p>

                {{-- فورم الإرسال --}}
                <form :action="deleteShipmentData.url" method="POST" @submit="isSubmitting = true" class="flex gap-3 px-2">
                    @csrf

                    <button type="button" @click="showDeleteModal = false"
                        class="flex-1 py-4 text-sm font-bold rounded-2xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 active:scale-95 font-headline">
                        تراجع
                    </button>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex flex-1 gap-2 justify-center items-center py-4 text-sm font-bold text-white bg-rose-500 rounded-2xl shadow-lg transition-all hover:bg-rose-600 shadow-rose-500/30 active:scale-95 font-headline">
                        <span x-show="!isSubmitting">نعم، فك الارتباط</span>
                        <span x-show="isSubmitting"
                            class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- مكتبة html5-qrcode لمسح الباركود و QR عبر الكاميرا --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script>
        function registerOutgoingPackageShowMobile() {
            Alpine.data('outgoingPackageShowMobile', (availableShipments, branchCode) => ({
                isAddModalOpen: false,
                searchShipment: '',
                barcodeInput: '',
                selectedShipmentIds: [],
                showDeleteModal: false,
                isSubmitting: false,
                deleteShipmentData: { bond_number: '', url: '' },
                isScannerOpen: false,
                html5QrCode: null,
                scanFeedback: { message: '', type: '' },
                feedbackTimeout: null,
                availableShipments: availableShipments || [],
                branchCode: branchCode || '',

                init() {
                    this.$watch('isAddModalOpen', (val) => {
                        if (val) {
                            this.barcodeInput = '';
                            this.searchShipment = '';
                            setTimeout(() => {
                                this.$refs.barcodeField?.focus();
                            }, 150);
                        } else {
                            this.stopCameraScanner();
                        }
                    });
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

                handleBarcodeScan(rawValue) {
                    if (!rawValue || !rawValue.trim()) return;

                    let scanned = rawValue.trim();

                    // تنظيف الرابط في حال كان الباركود QR مشفراً كرابط
                    if (scanned.includes('/')) {
                        const parts = scanned.replace(/\/$/, '').split('/');
                        scanned = parts[parts.length - 1].split('?')[0];
                    }

                    const cleanScanned = scanned.replace(/^#/, '');

                    // معالجة الرقم الذكي (مثل تحويل 14 إلى ADN-014 أو ADN-14 إذا لم يحتوي على شرطة)
                    let expectedBondNumber = cleanScanned;
                    if (!cleanScanned.includes('-')) {
                        let paddedNumber = cleanScanned.padStart(3, '0');
                        expectedBondNumber = this.branchCode + '-' + paddedNumber;
                    }

                    const matchedShipment = this.availableShipments.find(s => {
                        const sId = String(s.id);
                        const sBond = String(s.bond_number || '').replace(/^#/, '');
                        const sUuid = String(s.uuid || '');
                        const sCode = String(s.code || '');

                        return (
                            sId === cleanScanned ||
                            sBond === cleanScanned ||
                            sBond === expectedBondNumber ||
                            sBond.endsWith('-' + cleanScanned) ||
                            sUuid === cleanScanned ||
                            sCode === cleanScanned
                        );
                    });

                    if (matchedShipment) {
                        let displayNum = matchedShipment.bond_number || matchedShipment.id;
                        const isAlreadySelected = this.selectedShipmentIds.includes(matchedShipment.id);

                        if (!isAlreadySelected) {
                            this.selectedShipmentIds.push(matchedShipment.id);
                            this.playBeep(true);
                            this.showFeedback(`تم اختيار الطرد رقم #${displayNum} بنجاح`, 'success');

                            // التمرير التلقائي للطرد المحدد
                            this.$nextTick(() => {
                                const cardEl = document.getElementById('mobile-shipment-card-' + matchedShipment.id);
                                if (cardEl) {
                                    cardEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                }
                            });
                        } else {
                            this.playBeep(true);
                            this.showFeedback(`الطرد رقم #${displayNum} محدد بالفعل في القائمة`, 'info');
                        }
                    } else {
                        this.playBeep(false);
                        this.showFeedback(`لم يتم العثور على طرد متاح يطابق الكود: (${cleanScanned})`, 'error');
                    }

                    this.barcodeInput = '';
                    this.$nextTick(() => {
                        this.$refs.barcodeField?.focus();
                    });
                },

                toggleShipment(id) {
                    const index = this.selectedShipmentIds.indexOf(id);
                    if (index > -1) {
                        this.selectedShipmentIds.splice(index, 1);
                    } else {
                        this.selectedShipmentIds.push(id);
                    }
                },

                isShipmentSelected(id) {
                    return this.selectedShipmentIds.includes(id);
                },

                selectAllFiltered() {
                    this.availableShipments.forEach(s => {
                        const matchesSearch = this.searchShipment === '' ||
                            (s.bond_number + ' ' + s.receiver_name + ' ' + s.id).includes(this.searchShipment);
                        if (matchesSearch && !this.selectedShipmentIds.includes(s.id)) {
                            this.selectedShipmentIds.push(s.id);
                        }
                    });
                },

                clearSelection() {
                    this.selectedShipmentIds = [];
                },

                startCameraScanner() {
                    this.isScannerOpen = true;
                    this.$nextTick(() => {
                        if (!this.html5QrCode) {
                            this.html5QrCode = new Html5Qrcode("mobile-show-qr-reader");
                        }
                        this.html5QrCode.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: { width: 230, height: 230 } },
                            (decodedText) => {
                                this.handleBarcodeScan(decodedText);
                            },
                            (errorMessage) => {}
                        ).catch((err) => {
                            this.showFeedback('تعذر الوصول إلى الكاميرا، تأكد من منح الصلاحية للمتصفح.', 'error');
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
                }
            }));
        }

        if (window.Alpine) {
            registerOutgoingPackageShowMobile();
        } else {
            document.addEventListener('alpine:init', registerOutgoingPackageShowMobile);
        }
    </script>
@endsection