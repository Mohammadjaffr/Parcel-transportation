@extends('layouts.app')

@section('title', 'تفاصيل الإرسالية الواردة - ' . $package->tracking_number)
@section('Breadcrumb', 'إدارة الشحنات / تفاصيل الإرسالية الواردة')

@section('content')
    <div x-data="{ isSubmitting: false }" class="flex relative flex-col gap-6 p-4 rounded-3xl bg-surface dark:bg-boxdark-2 lg:p-6 font-body"
        dir="rtl">

        {{-- ================= الهيدر العلوي الذكي ================= --}}
        <div
            class="flex flex-col gap-4 justify-between items-start p-4 mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm md:flex-row md:items-center dark:bg-boxdark dark:border-boxdark-2 lg:p-6">
            <div class="flex gap-4 items-center">
                <a href="{{ route('shipmentpackage.incoming.index') }}"
                    class="flex justify-center items-center w-12 h-12 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 hover:text-primary dark:border-boxdark active:scale-90">
                    <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
                </a>
                <div>
                    <h1 class="text-2xl font-black md:text-3xl font-headline text-on-surface dark:text-white">
                        إرسالية واردة #{{ $package->tracking_number }}</h1>
                    <p class="mt-1 text-sm font-medium text-gray-500 dark:text-bodydark">
                        {{ $package->created_at->format('Y-m-d h:i A') }}</p>
                </div>
            </div>

            <div class="flex gap-3 items-center w-full md:w-auto">
                @php
                    $statusColors = [
                        'pending' =>
                            'bg-amber-50 text-amber-600 ring-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400',
                        'in_transit' =>
                            'bg-blue-50 text-blue-600 ring-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400',
                        'delivered' =>
                            'bg-emerald-50 text-emerald-600 ring-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                        'returned' =>
                            'bg-rose-50 text-rose-600 ring-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400',
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
                    $colorClass = $statusColors[$package->status] ?? 'bg-gray-50 text-gray-600';
                    $icon = $statusIcons[$package->status] ?? 'info';
                    $name = $statusNames[$package->status] ?? $package->status;
                @endphp
                <div
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl {{ $colorClass }} font-black text-xs ring-1 ring-inset shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                    {{ $name }}
                </div>

                <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->id]) }}"
                    target="_blank"
                    class="flex gap-2 items-center px-5 h-11 text-xs font-black text-gray-700 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark-2 dark:text-white hover:bg-gray-50 dark:hover:bg-boxdark dark:border-boxdark">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    طباعة الكشف
                </a>

                {{-- أزرار الإجراءات للرحلة كاملة --}}
                @php
                    $availableStatuses = [];
                    if ($package->status === 'in_transit') {
                        $availableStatuses = [
                            'delivered' => [
                                'label' => 'تأكيد استلام الإرسالية بالمستودع',
                                'icon' => 'inventory_2',
                            ],
                        ];
                    }
                @endphp

                @if (!empty($availableStatuses))
                    <div x-data="{ openStatusMenu: false }" class="relative">
                        <form action="{{ route('shipmentpackage.updateStatus', $package->id) }}" method="POST"
                            x-ref="statusForm" @submit="isSubmitting = true">
                            @csrf
                            <input type="hidden" name="status" x-ref="statusInput">

                            <button type="button" @click="openStatusMenu = !openStatusMenu"
                                @click.away="openStatusMenu = false"
                                class="flex gap-2 items-center px-5 h-11 text-xs font-black text-white bg-emerald-500 rounded-xl shadow-lg transition-all hover:bg-emerald-600 shadow-emerald-500/20 active:scale-95">
                                <span x-show="!isSubmitting"
                                    class="material-symbols-outlined text-[18px]">download_done</span>
                                <span x-show="isSubmitting"
                                    class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                <span x-text="isSubmitting ? 'جاري التأكيد...' : 'إجراءات الاستلام'"></span>
                                <span class="material-symbols-outlined text-[18px] transition-transform duration-300"
                                    :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                            </button>

                            <div x-show="openStatusMenu" x-cloak x-transition.origin.top.right
                                class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-2xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 dark:bg-boxdark dark:border-boxdark">
                                    <span class="text-xs font-bold text-gray-500">بمجرد التأكيد ستصبح جميع الطرود في عهدتك.</span>
                                </div>
                                @foreach ($availableStatuses as $value => $data)
                                    <button type="button"
                                        @click="$refs.statusInput.value = '{{ $value }}'; $refs.statusForm.submit()"
                                        :disabled="isSubmitting"
                                        class="flex gap-3 items-center px-5 py-4 w-full text-sm font-bold text-right text-gray-700 transition-colors hover:bg-gray-50 dark:hover:bg-boxdark-2 dark:text-white">
                                        <span class="material-symbols-outlined text-[20px] text-emerald-500">{{ $data['icon'] }}</span>
                                        {{ $data['label'] }}
                                    </button>
                                @endforeach
                            </div>
                        </form>
                    </div>
                @else
                    <div
                        class="flex gap-2 justify-center items-center px-5 h-11 text-xs font-bold text-gray-500 bg-gray-50 rounded-xl dark:bg-boxdark-2 dark:text-gray-400 md:w-auto">
                        <span class="material-symbols-outlined text-[16px]">lock</span>
                        الرحلة مستلمة ومغلقة
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ================= العمود الأيمن: معلومات الرحلة (Route & Stats) ================= --}}
            <div class="space-y-6 lg:col-span-1">

                {{-- بطاقة السائق --}}
                <div
                    class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm">
                    <h3
                        class="flex gap-2 items-center mb-6 text-lg font-black font-headline text-on-surface dark:text-white">
                        <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[22px]">sports_motorsports</span>
                        </div>
                        بيانات السائق
                    </h3>

                    <div
                        class="flex gap-4 items-start p-4 mb-2 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                        <div
                            class="flex justify-center items-center w-16 h-16 text-gray-400 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark shrink-0">
                            <span class="material-symbols-outlined text-[32px]">person</span>
                        </div>
                        <div class="flex-1 pt-1">
                            <div class="text-lg font-black truncate text-on-surface dark:text-white">
                                {{ $package->driver->name ?? 'غير محدد' }}</div>
                            <div
                                class="text-[13px] font-bold text-gray-500 dark:text-gray-400 mt-1 dir-ltr text-right truncate">
                                <x-phone-number :value="$package->driver->phone ?? '---'" />
                            </div>
                        </div>
                    </div>

                    @if ($package->driver && $package->driver->phone)
                        <div class="flex gap-3 pt-6 mt-6 border-t border-gray-100 dark:border-boxdark">
                            <a href="tel:{{ $package->driver->phone }}"
                                class="flex flex-1 gap-2 justify-center items-center h-12 text-xs font-black text-gray-700 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark-2 dark:text-gray-300 hover:bg-gray-50 dark:border-boxdark">
                                <span class="material-symbols-outlined text-[18px]">call</span>
                                اتصال
                            </a>
                            @php
                                $driverMsg =
                                    'مرحباً كابتن *' .
                                    ($package->driver->name ?? 'السائق') .
                                    "*،\nنحن في انتظار وصول الإرسالية رقم: *" .
                                    $package->tracking_number .
                                    "* إلى مستودعنا.\nمتى تتوقع الوصول؟";
                            @endphp
                            <a href="https://wa.me/{{ ltrim($package->driver->phone, '+') }}?text={{ urlencode($driverMsg) }}"
                                target="_blank"
                                class="flex flex-1 gap-2 justify-center items-center h-12 text-xs font-black text-emerald-600 rounded-xl border transition-all bg-emerald-500/10 hover:bg-emerald-500/20 border-emerald-500/20">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                                واتساب
                            </a>
                        </div>
                    @endif
                </div>

                {{-- بطاقة المسار الزمني --}}
                <div
                    class="p-6 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 relative overflow-hidden">
                    <div
                        class="absolute -top-10 -right-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-primary/5">
                    </div>

                    <h3
                        class="flex relative z-10 gap-2 items-center mb-8 text-lg font-black text-on-surface dark:text-white font-headline">
                        <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[22px]">route</span>
                        </div>
                        مسار الإرسالية الواردة
                    </h3>

                    <div class="relative z-10 pr-8 pl-2 space-y-10">
                        {{-- الخط العمودي --}}
                        <div
                            class="absolute right-[15px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-gray-200 via-primary/30 to-emerald-400 dark:from-boxdark-2 dark:via-primary/20 dark:to-emerald-500/50 rounded-full">
                        </div>

                        {{-- 1. مصدر الانطلاق --}}
                        <div class="relative z-10">
                            <div
                                class="absolute -right-[43px] top-2 w-5 h-5 bg-white dark:bg-boxdark-2 border-4 border-gray-300 dark:border-gray-600 rounded-full shadow-sm">
                            </div>
                            <div
                                class="p-5 rounded-2xl border border-gray-100 transition-all bg-gray-50/80 dark:bg-boxdark-2/40 dark:border-boxdark hover:bg-gray-50">
                                <span class="text-[10px] font-black tracking-wider text-gray-400 dark:text-gray-500 mb-1.5 uppercase block">
                                    مصدر الإرسالية</span>
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-black text-on-surface dark:text-white">
                                        @if ($package->sender_office_branch_id && $package->senderOfficeBranch)
                                            <span
                                                class="text-primary">{{ $package->senderOfficeBranch->office->name ?? 'مكتب خارجي' }}</span>
                                            -
                                        @endif
                                        {{ $package->sender_entity->name ?? 'غير معروف' }}
                                    </span>
                                    <span
                                        class="flex gap-1.5 items-center mt-2 text-xs font-bold text-gray-500 w-fit">
                                        <span class="material-symbols-outlined text-[14px]">storefront</span>
                                        {{ $package->sender_office_branch_id ? 'مكتب وكيل خارجي' : 'فرع داخلي' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- 2. الوجهة --}}
                        <div class="relative z-10">
                            <div
                                class="absolute -right-[43px] top-2 w-5 h-5 bg-emerald-500 ring-4 ring-emerald-500/20 rounded-full shadow-lg shadow-emerald-500/20">
                            </div>
                            <div
                                class="p-5 rounded-2xl border border-emerald-100 transition-all bg-emerald-50/50 dark:bg-emerald-500/5 dark:border-emerald-500/10 hover:bg-emerald-50">
                                <span class="text-[10px] font-black tracking-wider text-emerald-600/70 dark:text-emerald-400/70 mb-4 uppercase block">
                                    الوجهة (إلينا)</span>
                                <span
                                    class="inline-flex gap-2.5 items-center px-4 py-3 text-[13px] font-black text-emerald-700 bg-white rounded-xl border border-emerald-100 shadow-sm dark:bg-boxdark dark:border-emerald-500/20 dark:text-emerald-400 transition-all hover:scale-[1.02]">
                                    <span class="material-symbols-outlined text-[18px]">location_on</span>
                                    {{ auth()->user()->branch->name ?? 'مستودعنا' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($package->notes)
                        <div
                            class="relative z-10 p-4 mt-8 rounded-2xl border bg-amber-50/50 border-amber-100/50 dark:bg-amber-500/5 dark:border-amber-500/10">
                            <p
                                class="text-[11px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">notes</span>
                                ملاحظات الإرسالية
                            </p>
                            <p class="text-sm font-bold leading-relaxed text-gray-700 dark:text-gray-300">
                                {{ $package->notes }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ================= العمود الأيسر: قائمة الطرود المضمنة ================= --}}
            <div class="lg:col-span-2">
                <div
                    class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm min-h-full">
                    
                    <div
                        class="flex justify-between items-center p-4 mb-8 rounded-2xl border border-gray-100 bg-gray-50/50 dark:bg-boxdark-2/50 dark:border-boxdark-2">
                        <div class="flex gap-3 items-center">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-gray-500 bg-white rounded-xl border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-400 dark:border-boxdark">
                                <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black font-headline text-on-surface dark:text-white">الطرود القادمة</h3>
                                <p class="text-xs font-bold text-gray-500">اتبع حالة كل طرد عند التفريغ</p>
                            </div>
                        </div>
                        <div
                            class="px-4 py-2 text-xs font-black text-white rounded-xl shadow-lg bg-primary shadow-primary/20">
                            {{ $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0) }} طرد
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        @forelse($package->shipments as $shipment)
                            <div
                                class="flex items-center gap-5 bg-white dark:bg-boxdark-2 p-5 rounded-[2rem] border border-gray-100 dark:border-boxdark shadow-sm hover:shadow-lg hover:border-emerald-500/30 transition-all duration-300 group relative overflow-hidden">

                                {{-- مثلث الاستلام في الزاوية --}}
                                @if (in_array($shipment->status, ['received_at_branch', 'out_for_delivery', 'delivered']))
                                    <div
                                        class="flex absolute -top-6 -left-6 z-10 justify-center items-end pb-1 w-14 h-14 text-white bg-emerald-500 shadow-sm rotate-45">
                                        <span class="material-symbols-outlined text-[16px]">done_all</span>
                                    </div>
                                @endif

                                <div
                                    class="flex justify-center items-center w-16 h-16 text-gray-400 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner transition-all duration-300 dark:bg-boxdark shrink-0 dark:border-boxdark group-hover:bg-primary/5 group-hover:text-primary group-hover:border-primary/20">
                                    <span
                                        class="material-symbols-outlined text-[30px]">{{ $shipment->package_type == 'carton' ? 'inventory_2' : 'package_2' }}</span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap gap-3 items-center mb-2">
                                        <span class="font-mono text-lg font-black leading-none text-on-surface dark:text-white">{{ $shipment->code }}</span>
                                        <span class="text-[10px] font-black text-gray-500 bg-gray-100/80 border border-gray-200/50 px-2.5 py-1 rounded-lg dark:bg-boxdark dark:border-boxdark dark:text-gray-400">
                                            {{ $shipment->package_type ?? 'طرد عادي' }}
                                        </span>
                                        @php $isPaid = $shipment->payment_method === 'prepaid'; @endphp
                                        <span class="text-[10px] font-black px-2.5 py-1 rounded-lg border {{ $isPaid ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400' : 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400' }}">
                                            {{ $isPaid ? 'خالص (مدفوع)' : number_format($shipment->total_amount) . ' ر.ي' }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-y-2 gap-x-5 items-center text-xs font-bold text-gray-500 dark:text-gray-400">
                                        <div class="flex gap-1.5 items-center">
                                            <span class="material-symbols-outlined text-[16px] text-gray-400">person_outline</span>
                                            <span class="truncate max-w-[150px]">المستلم: {{ $shipment->receiverCustomer->name ?? $shipment->receiver_name ?? 'غير مسجل' }}</span>
                                        </div>
                                        @if ($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                            <div class="flex gap-1.5 items-center dir-ltr">
                                                <span class="material-symbols-outlined text-[16px] text-gray-400">call</span>
                                                <span class="truncate"> <x-phone-number :value="$shipment->receiverCustomer->phone ?? '---'" /></span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex z-20 gap-2 items-center pr-4 border-r border-gray-100 dark:border-boxdark shrink-0">
                                    {{-- تأكيد الوصول الفردي --}}
                                    @if ($shipment->status === 'in_transit')
                                        <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST"
                                            x-data="{ isSubmittingShipment: false }" @submit="isSubmittingShipment = true">
                                            @csrf
                                            <input type="hidden" name="status" value="received_at_branch">
                                            <button type="submit" :disabled="isSubmittingShipment"
                                                title="تأكيد الوصول للمستودع"
                                                class="flex justify-center items-center w-11 h-11 text-blue-600 bg-blue-50 rounded-xl border border-blue-100 shadow-sm transition-all dark:bg-blue-500/10 dark:text-blue-400 hover:bg-blue-500 hover:text-white active:scale-95 dark:border-blue-500/20">
                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- عرض التفاصيل --}}
                                    <a href="{{ route('shipment.incoming.show', $shipment->id) }}"
                                        class="flex justify-center items-center w-11 h-11 text-gray-400 bg-gray-50 rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-100 dark:border-boxdark"
                                        title="{{ $shipment->is_returned ? 'تفاصيل المرتجع' : 'التفاصيل والتسليم' }}">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>

                                    {{-- مراسلة المستلم --}}
                                    @if ($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                        @php
                                            $targetCustomer = $shipment->is_returned ? $shipment->senderCustomer : $shipment->receiverCustomer;
                                            if ($shipment->is_returned) {
                                                $whatsappMsg = "مرحباً *" . ($targetCustomer->name ?? '') . "*،\nنفيدك بوصول طردكم (المرتجع) برقم السند: *" . $shipment->code . "* إلى فرعنا.\nيرجى التفضل باستلامه.";
                                            } else {
                                                $whatsappMsg = "مرحباً *" . ($targetCustomer->name ?? '') . "*،\nنفيدك بوصول طردك برقم السند: *" . $shipment->code . "* إلى فرعنا.\n" . ($shipment->payment_method !== 'prepaid' ? "المبلغ المطلوب عند الاستلام: *" . number_format($shipment->total_amount, 0) . "* ريال." : "الطرد خالص الدفع.");
                                            }
                                        @endphp
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $shipment->receiverCustomer->phone) }}?text={{ urlencode($whatsappMsg) }}"
                                            target="_blank" title="مراسلة المستلم"
                                            class="flex justify-center items-center w-11 h-11 text-emerald-500 bg-emerald-50 rounded-xl border border-emerald-100 shadow-sm transition-all dark:bg-emerald-500/10 hover:bg-[#25D366] hover:text-white hover:border-[#25D366] active:scale-95 dark:border-emerald-500/20">
                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                                <path
                                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div
                                class="text-center py-20 bg-gray-50/50 dark:bg-boxdark-2/30 rounded-[2.5rem] border-2 border-dashed border-gray-200 dark:border-boxdark">
                                <div
                                    class="flex justify-center items-center mx-auto mb-4 w-20 h-20 bg-white rounded-full border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark">
                                    <span
                                        class="material-symbols-outlined text-[40px] text-gray-300 dark:text-gray-700">inbox_customize</span>
                                </div>
                                <p class="text-base font-black text-gray-500 dark:text-gray-500">لا توجد طرود مضمنة</p>
                                <p class="mt-1 text-xs font-bold text-gray-400">لم يتم إضافة أي طرود لهذه الإرسالية بعد.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

