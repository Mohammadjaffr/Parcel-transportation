@extends('layouts.app')

@section('title', 'تفاصيل الإرسالية الواردة - ' . $package->tracking_number)

@section('content')
<div x-data="{ isSubmitting: false }" class="flex flex-col gap-6 p-4 rounded-3xl bg-slate-50/50 dark:bg-boxdark-2 lg:p-6 font-body" dir="rtl">

    {{-- ================= الهيدر العلوي الذكي ================= --}}
    <div class="flex flex-col gap-4 justify-between items-start p-5 bg-white rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] md:flex-row md:items-center dark:bg-boxdark dark:border-boxdark-2">
        <div class="flex gap-4 items-center">
            <a href="{{ route('shipmentpackage.incoming.index') }}" 
                class="flex justify-center items-center w-12 h-12 rounded-2xl border shadow-sm transition-all text-slate-400 bg-slate-50 border-slate-100 hover:bg-emerald-500 hover:text-white hover:border-emerald-500 dark:bg-boxdark-2 dark:border-boxdark-2 active:scale-95">
                <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
            </a>
            <div>
                <h1 class="text-2xl font-black md:text-3xl font-headline text-slate-800 dark:text-white">{{ $package->tracking_number }}</h1>
                <div class="flex gap-2 items-center mt-1.5">
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:border-amber-500/20',
                            'in_transit' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:border-blue-500/20',
                            'delivered' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:border-emerald-500/20',
                            'returned' => 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:border-rose-500/20',
                        ];
                        $statusIcons = [
                            'pending' => 'schedule',
                            'in_transit' => 'local_shipping',
                            'delivered' => 'inventory_2',
                            'returned' => 'assignment_return',
                        ];
                        $statusNames = [
                            'pending' => 'جاري التجهيز بالمصدر',
                            'in_transit' => 'في الطريق إلينا',
                            'delivered' => 'تم الاستلام بالمستودع',
                            'returned' => 'مرتجعة',
                        ];
                        $colorClass = $statusColors[$package->status] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                        $icon = $statusIcons[$package->status] ?? 'info';
                        $name = $statusNames[$package->status] ?? $package->status;
                    @endphp
                    <span class="flex items-center gap-1 px-3 py-1 rounded-xl text-[11px] font-black border shadow-sm {{ $colorClass }}">
                        <span class="material-symbols-outlined text-[14px]">{{ $icon }}</span>
                        {{ $name }}
                    </span>
                    <span class="px-2.5 py-1 text-[11px] font-bold text-slate-400 bg-slate-50 rounded-xl border border-slate-100 dark:bg-boxdark-2 dark:border-boxdark">
                        {{ $package->created_at->format('Y/m/d H:i') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- أزرار الإجراءات للرحلة كاملة --}}
        <div class="flex gap-3 w-full md:w-auto">
            @php
                $availableStatuses = [];
                if ($package->status === 'in_transit') {
                    $availableStatuses = [
                        'delivered' => [
                            'label' => 'تأكيد استلام الإرسالية بالمستودع',
                            'icon' => 'inventory_2',
                            'bg_color' => 'bg-emerald-50 dark:bg-emerald-500/10',
                            'text_color' => 'text-emerald-600 dark:text-emerald-400'
                        ]
                    ];
                }
            @endphp

            @if(!empty($availableStatuses))
                <div class="flex-[2] relative w-full md:w-auto" x-data="{ openStatusMenu: false }">
                    <form action="{{ route('shipmentpackage.updateStatus', $package->id) }}" method="POST" x-ref="statusForm" @submit="isSubmitting = true">
                        @csrf
                        <input type="hidden" name="status" x-ref="statusInput">

                        <button type="button" @click="openStatusMenu = !openStatusMenu" @click.outside="openStatusMenu = false"
                            class="flex justify-between items-center px-6 h-12 w-full md:w-auto text-sm font-black text-white rounded-xl shadow-lg transition-all bg-emerald-500 hover:bg-emerald-600 hover:scale-[1.02] shadow-emerald-500/20 active:scale-95">
                            <div class="flex gap-2 items-center">
                                <span x-show="!isSubmitting" class="material-symbols-outlined text-[20px]">download_done</span>
                                <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                <span x-text="isSubmitting ? 'جاري التأكيد...' : 'إجراءات الاستلام'"></span>
                            </div>
                            <span class="material-symbols-outlined text-[20px] transition-transform duration-300 mr-3"
                                :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <div x-show="openStatusMenu" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            class="absolute top-full left-0 mt-2 w-full min-w-[280px] bg-white dark:bg-boxdark-2 rounded-2xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-slate-100 dark:border-boxdark p-1.5 z-[60]">

                            <div class="px-4 py-2.5 mb-1 rounded-t-xl border-b border-slate-50 dark:border-boxdark bg-slate-50/50">
                                <span class="text-[10px] font-black text-slate-500">بمجرد التأكيد ستصبح جميع الطرود في عهدتك.</span>
                            </div>

                            @foreach($availableStatuses as $value => $data)
                                <button type="button" @click="$refs.statusInput.value = '{{ $value }}'; $refs.statusForm.submit()"
                                    :disabled="isSubmitting"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-boxdark transition-all text-right group active:scale-[0.98]">
                                    <div class="w-9 h-9 rounded-xl {{ $data['bg_color'] }} {{ $data['text_color'] }} flex items-center justify-center group-hover:scale-110 transition-transform shrink-0 shadow-sm border border-white/50">
                                        <span class="material-symbols-outlined text-[20px]">{{ $data['icon'] }}</span>
                                    </div>
                                    <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ $data['label'] }}</span>
                                </button>
                            @endforeach
                        </div>
                    </form>
                </div>
            @else
                <div class="flex gap-2 justify-center items-center px-6 w-full h-12 text-sm font-bold rounded-xl border shadow-inner bg-slate-50 text-slate-400 dark:bg-boxdark-2 dark:text-slate-500 border-slate-100 dark:border-boxdark md:w-auto">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    الرحلة مستلمة ومغلقة
                </div>
            @endif

            <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->id]) }}" target="_blank"
                class="flex gap-2 justify-center items-center px-6 w-full h-12 text-sm font-black bg-white rounded-xl border shadow-sm transition-all md:w-auto text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-primary active:scale-95 dark:bg-boxdark dark:border-boxdark dark:text-slate-300 dark:hover:text-white">
                <span class="material-symbols-outlined text-[20px]">print</span>
                طباعة الكشف
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        
        {{-- ================= العمود الأيمن: معلومات الرحلة (Route & Stats) ================= --}}
        <div class="space-y-6 lg:col-span-1">
            
            {{-- بطاقة المسار الزمني --}}
            <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:bg-boxdark dark:border-boxdark-2 relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full blur-3xl pointer-events-none bg-primary/5"></div>
                
                <h3 class="flex relative z-10 gap-2 items-center mb-8 text-sm font-black text-slate-800 dark:text-white font-headline">
                    <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-[18px]">route</span>
                    </div>
                    مسار الإرسالية الواردة
                </h3>

                <div class="relative z-10 pr-6 pl-2 space-y-8">
                    {{-- الخط العمودي --}}
                    <div class="absolute right-[11px] top-2 bottom-2 w-[2px] bg-gradient-to-b from-slate-200 via-primary/50 to-emerald-400 dark:from-boxdark-2 rounded-full"></div>

                    {{-- 1. مصدر الانطلاق --}}
                    <div class="relative z-10 group">
                        <div class="absolute -right-[31.5px] top-1.5 w-4 h-4 bg-white dark:bg-boxdark border-[4px] border-slate-300 dark:border-slate-600 rounded-full shadow-sm group-hover:border-primary transition-colors"></div>
                        <div class="p-4 rounded-2xl border transition-all bg-slate-50/50 border-slate-100/80 dark:bg-boxdark-2/50 dark:border-boxdark hover:bg-white hover:shadow-md">
                            <span class="text-[10px] font-black text-slate-400 mb-1.5 block tracking-widest">مصدر الإرسالية</span>
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-black text-slate-800 dark:text-white">
                                    @if($package->sender_office_branch_id && $package->senderOfficeBranch)
                                        <span class="text-primary">{{ $package->senderOfficeBranch->office->name ?? 'مكتب خارجي' }}</span> -
                                    @endif
                                    {{ $package->sender_entity->name ?? 'غير معروف' }}
                                </span>
                                <span class="flex gap-1 items-center text-[10px] font-bold text-slate-500 bg-slate-100/50 w-fit px-2 py-0.5 rounded-md border border-slate-100">
                                    <span class="material-symbols-outlined text-[12px]">storefront</span>
                                    {{ $package->sender_office_branch_id ? 'مكتب وكيل خارجي' : 'فرع داخلي' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. السائق --}}
                    <div class="relative z-10 group">
                        <div class="absolute -right-[31.5px] top-1.5 w-4 h-4 bg-primary ring-4 ring-primary/20 rounded-full shadow-sm {{ $package->status == 'in_transit' ? 'animate-pulse' : '' }}"></div>
                        <div class="bg-primary/[0.02] p-4 rounded-2xl border border-primary/10 transition-all hover:bg-primary/5 hover:shadow-md hover:border-primary/20">
                            <span class="text-[10px] font-black text-primary/60 mb-1.5 block tracking-widest">معلومات السائق</span>
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="block text-sm font-black text-primary">{{ $package->driver->name ?? 'غير محدد' }}</span>
                                    <span class="block mt-0.5 text-xs font-bold text-right text-primary/70 dir-ltr">{{ $package->driver->phone }}</span>
                                </div>
                                @if($package->driver && $package->driver->phone)
                                    @php
                                        $driverMsg = "مرحباً كابتن *" . ($package->driver->name ?? 'السائق') . "*،\nنحن في انتظار وصول الإرسالية رقم: *" . $package->tracking_number . "* إلى مستودعنا.\nمتى تتوقع الوصول؟";
                                    @endphp
                                    <div class="flex gap-1.5">
                                        <a href="https://wa.me/{{ ltrim($package->driver->phone, '+') }}?text={{ urlencode($driverMsg) }}"
                                            target="_blank" title="مراسلة واتساب"
                                            class="flex justify-center items-center w-9 h-9 bg-white dark:bg-boxdark rounded-xl shadow-sm border border-primary/10 hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all">
                                            <svg class="w-[18px] h-[18px] fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                            </svg>
                                        </a>
                                        <a href="tel:{{ $package->driver->phone }}" title="اتصال مباشر"
                                            class="flex justify-center items-center w-9 h-9 text-white rounded-xl shadow-sm transition-all bg-primary shadow-primary/30 hover:bg-primary/90 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">call</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 3. الوجهة --}}
                    <div class="relative z-10 group">
                        <div class="absolute -right-[31.5px] top-1.5 w-4 h-4 bg-emerald-500 ring-4 ring-emerald-500/20 rounded-full shadow-sm"></div>
                        <div class="p-4 rounded-2xl border border-emerald-100 transition-all dark:border-emerald-500/20 bg-emerald-50/50 dark:bg-emerald-500/5 hover:bg-white hover:shadow-md hover:border-emerald-200">
                            <span class="text-[10px] font-black text-emerald-600/70 mb-1.5 block tracking-widest">الوجهة (إلينا)</span>
                            <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-black text-emerald-700 bg-white rounded-xl border border-emerald-100 shadow-sm dark:bg-boxdark dark:border-emerald-500/20 dark:text-emerald-400">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                {{ auth()->user()->branch->name ?? 'مستودعنا' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($package->notes)
                <div class="relative z-10 p-4 mt-8 rounded-2xl border bg-slate-50/80 border-slate-100 dark:bg-boxdark-2 dark:border-boxdark">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">notes</span>
                        ملاحظات إضافية
                    </p>
                    <p class="text-xs font-bold leading-relaxed text-slate-600 dark:text-bodydark">
                        {{ $package->notes }}
                    </p>
                </div>
                @endif
            </div>

            {{-- إحصائيات --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-5 bg-white rounded-3xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:bg-boxdark dark:border-boxdark-2 relative overflow-hidden group">
                    <div class="absolute -bottom-6 -left-6 z-0 w-24 h-24 rounded-full transition-transform bg-slate-50 dark:bg-boxdark-2 group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="flex justify-center items-center mb-3 w-10 h-10 rounded-xl border bg-slate-50 text-slate-500 border-slate-100">
                            <span class="material-symbols-outlined text-[20px]">package_2</span>
                        </div>
                        <p class="text-3xl font-black text-slate-800 dark:text-white font-headline">{{ $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0) }}</p>
                        <p class="text-[11px] font-black text-slate-400 mt-1">طرد مضمن</p>
                    </div>
                </div>
                
                <div class="overflow-hidden relative p-5 text-white bg-gradient-to-br rounded-3xl shadow-lg from-primary to-primary/90 shadow-primary/20 group">
                    <div class="absolute -bottom-6 -left-6 z-0 w-24 h-24 rounded-full transition-transform bg-white/10 group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="flex justify-center items-center mb-3 w-10 h-10 text-white rounded-xl backdrop-blur-sm bg-white/20">
                            <span class="material-symbols-outlined text-[20px]">scale</span>
                        </div>
                        <p class="text-3xl font-black font-headline">--</p>
                        <p class="text-[11px] font-black text-white/70 mt-1">الوزن التقديري</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= العمود الأيسر: قائمة الطرود المضمنة ================= --}}
        <div class="space-y-5 lg:col-span-2">
            <div class="flex justify-between items-center px-2">
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border shadow-sm border-slate-200 text-slate-600 dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-300">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white font-headline">الطرود القادمة</h3>
                        <span class="text-xs font-bold text-slate-500">اتبع حالة كل طرد عند التفريغ</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @forelse($package->shipments as $shipment)
                    <div class="flex flex-col p-5 bg-white rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] dark:bg-boxdark dark:border-boxdark-2 relative overflow-hidden group hover:border-emerald-500/30">
                        
                        {{-- مثلث الاستلام في الزاوية --}}
                        @if($shipment->status == 'delivered')
                            <div class="flex absolute -top-6 -left-6 z-10 justify-center items-end pb-1 w-14 h-14 text-white bg-emerald-500 shadow-sm rotate-45">
                                <span class="material-symbols-outlined text-[16px]">done_all</span>
                            </div>
                        @endif

                        <div class="flex relative z-20 justify-between items-start mb-4">
                            {{-- بيانات الطرد والمستلم --}}
                            <div class="flex gap-3.5 items-start">
                                <div class="flex justify-center items-center w-12 h-12 rounded-2xl border shadow-sm transition-all bg-slate-50 dark:bg-boxdark-2 text-slate-400 shrink-0 border-slate-100 dark:border-boxdark group-hover:text-primary group-hover:bg-primary/5 group-hover:border-primary/20">
                                    <span class="material-symbols-outlined text-[24px]">{{ $shipment->package_type == 'carton' ? 'inventory_2' : 'package_2' }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <h4 class="font-mono text-base font-black text-slate-800 dark:text-white">{{ $shipment->code }}</h4>
                                    <div class="text-[11px] font-black text-slate-600 dark:text-slate-300 truncate max-w-[150px]">
                                        {{ $shipment->receiverCustomer->name ?? $shipment->receiver_name }}
                                    </div>
                                    <div class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-100 w-fit">
                                        {{ $shipment->package_type ?? 'طرد عادي' }}
                                    </div>
                                </div>
                            </div>
                            
                            {{-- أزرار الإجراءات الفردية --}}
                            <div class="flex gap-2 items-center shrink-0">
                                @if($shipment->status === 'in_transit')
                                    <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" x-data="{ isSubmittingShipment: false }" @submit="isSubmittingShipment = true">
                                        @csrf
                                        <input type="hidden" name="status" value="received_at_branch">
                                        <button type="submit" :disabled="isSubmittingShipment" title="تأكيد الوصول للمستودع"
                                            class="flex justify-center items-center w-9 h-9 text-blue-600 bg-blue-50 rounded-xl border border-blue-100 shadow-sm transition-all dark:bg-blue-500/10 dark:text-blue-400 hover:bg-blue-500 hover:text-white active:scale-95 dark:border-blue-500/20">
                                            <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                        </button>
                                    </form>
                                @elseif(in_array($shipment->status, ['received_at_branch', 'out_for_delivery', 'delivered']))
                                    <div class="flex justify-center items-center w-9 h-9 text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20" title="تم الاستلام بالمستودع">
                                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                    </div>
                                @endif

                                <div class="relative" x-data="{ menuOpen: false }">
                                    <button type="button" @click="menuOpen = !menuOpen" @click.outside="menuOpen = false"
                                        class="flex justify-center items-center w-9 h-9 bg-white rounded-xl border shadow-sm transition-all dark:bg-boxdark-2 border-slate-100 dark:border-boxdark text-slate-400 hover:text-emerald-600 hover:border-emerald-200 active:scale-90">
                                        <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                    </button>

                                    <div x-show="menuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                        class="absolute left-0 top-full mt-2 w-48 bg-white dark:bg-boxdark-2 rounded-2xl shadow-[0_20px_50px_-15px_rgba(0,0,0,0.15)] border border-slate-100 dark:border-boxdark p-1.5 z-50">
                                        
                                        <a href="{{ route('shipment.incoming.show', $shipment->id) }}"
                                            class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-boxdark text-slate-700 dark:text-gray-300 transition-colors active:scale-[0.98]">
                                            <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-boxdark text-slate-500">
                                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                            </div>
                                            <span class="text-xs font-black">{{ $shipment->is_returned ? 'تفاصيل المرتجع' : 'التفاصيل والتسليم' }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- الفوتر: الحالة المالية والمراسلة --}}
                        <div class="flex justify-between items-center p-3.5 mt-auto rounded-2xl border border-slate-50 bg-slate-50/50 dark:bg-boxdark-2/50 dark:border-boxdark">
                            <div class="flex gap-2 items-center">
                                <div class="flex justify-center items-center w-8 h-8 bg-white rounded-full border shadow-sm border-slate-100">
                                    <span class="material-symbols-outlined text-[16px] text-slate-400">payments</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">حالة الدفع</span>
                                    @php $isPaid = $shipment->payment_method === 'prepaid'; @endphp
                                    <span class="text-[11px] font-black {{ $isPaid ? 'text-emerald-500' : 'text-amber-500 font-headline' }}">
                                        {{ $isPaid ? 'خالص (مدفوع)' : (number_format($shipment->total_amount) . ' ر.ي') }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                @php
                                    $targetCustomer = $shipment->is_returned ? $shipment->senderCustomer : $shipment->receiverCustomer;
                                    if($shipment->is_returned) {
                                        $whatsappMsg = "مرحباً *" . ($targetCustomer->name ?? '') . "*،\nنفيدك بوصول طردكم (المرتجع) برقم السند: *" . $shipment->code . "* إلى فرعنا.\nيرجى التفضل باستلامه.";
                                    } else {
                                        $whatsappMsg = "مرحباً *" . ($targetCustomer->name ?? '') . "*،\nنفيدك بوصول طردك برقم السند: *" . $shipment->code . "* إلى فرعنا.\n" . ($shipment->payment_method !== 'prepaid' ? "المبلغ المطلوب عند الاستلام: *" . number_format($shipment->total_amount, 0) . "* ريال." : "الطرد خالص الدفع.");
                                    }
                                @endphp
                                <div class="flex gap-1.5">
                                    <a href="tel:{{ $shipment->receiverCustomer->phone }}" title="اتصال بالمستلم"
                                        class="flex justify-center items-center w-8 h-8 bg-white rounded-lg border shadow-sm transition-all text-slate-400 border-slate-100 dark:bg-boxdark hover:text-primary hover:border-primary/30 active:scale-95 dark:border-boxdark-2">
                                        <span class="material-symbols-outlined text-[16px]">call</span>
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $shipment->receiverCustomer->phone) }}?text={{ urlencode($whatsappMsg) }}" 
                                        target="_blank" title="مراسلة المستلم"
                                        class="flex justify-center items-center w-8 h-8 text-emerald-500 bg-white rounded-lg border border-slate-100 shadow-sm transition-all dark:bg-boxdark hover:bg-[#25D366] hover:text-white hover:border-[#25D366] active:scale-95 dark:border-boxdark-2">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col col-span-1 justify-center items-center py-20 rounded-3xl border-2 border-dashed xl:col-span-2 bg-slate-50/50 border-slate-200 dark:bg-boxdark-2/30 dark:border-boxdark">
                        <div class="flex justify-center items-center mb-4 w-20 h-20 bg-white rounded-full border shadow-sm border-slate-100">
                            <span class="material-symbols-outlined text-[40px] text-slate-300">inventory_2</span>
                        </div>
                        <p class="text-sm font-black text-slate-500 font-headline">لا توجد طرود مضمنة في هذه الإرسالية</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection