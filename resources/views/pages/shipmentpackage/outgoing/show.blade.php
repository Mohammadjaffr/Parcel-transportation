@extends('layouts.app')

@section('title', 'تفاصيل الإرسالية - ' . $package->id)
@section('Breadcrumb', 'إدارة الشحنات / تفاصيل الإرسالية')

@section('content')
    {{-- إضافة x-data للتحكم بالمودال والبحث --}}
    <div class="flex relative flex-col gap-6 p-4 rounded-3xl bg-surface dark:bg-boxdark-2 lg:p-6 font-body" dir="rtl"
        x-data="{
            isAddModalOpen: false,
            searchShipment: '',
            showDeleteModal: false,
            isSubmitting: false,
            deleteShipmentData: { bond_number: '', url: '' }
        }">

        {{-- الهيدر العلوي --}}
        <div
            class="flex flex-col gap-4 justify-between items-start p-4 mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm md:flex-row md:items-center dark:bg-boxdark dark:border-boxdark-2 lg:p-6">
            <div class="flex gap-4 items-center">
                <a href="{{ route('shipmentpackage.outgoing.index') }}"
                    class="flex justify-center items-center w-12 h-12 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 hover:text-primary dark:border-boxdark active:scale-90">
                    <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
                </a>
                <div>
                    <h1 class="text-2xl font-black md:text-3xl font-headline text-on-surface dark:text-white">إرسالية
                        #{{ $package->id }}</h1>
                    <p class="mt-1 text-sm font-medium text-gray-500 dark:text-bodydark">
                        {{ $package->created_at->format('Y-m-d h:i A') }}</p>
                </div>
            </div>

            @if (!in_array($package->status, ['returned', 'cancelled']))
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
                            'delivered' => 'check_circle',
                            'returned' => 'assignment_return',
                        ];
                        $statusNames = [
                            'pending' => 'قيد التجهيز',
                            'in_transit' => 'في الطريق',
                            'delivered' => 'مكتملة',
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

                    <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->uuid]) }}"
                        target="_blank"
                        class="flex gap-2 items-center px-5 h-11 text-xs font-black text-gray-700 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark-2 dark:text-white hover:bg-gray-50 dark:hover:bg-boxdark dark:border-boxdark">
                        <span class="material-symbols-outlined text-[18px]">print</span>
                        طباعة
                    </a>

                    {{-- تحديث الحالة --}}
                    @php
                        $currentStatus = $package->status;
                        $availableStatuses = [];

                        if ($currentStatus === 'pending') {
                            $availableStatuses = [
                                'in_transit' => ['label' => 'انطلاق الرحلة', 'icon' => 'local_shipping'],
                                'returned' => ['label' => 'إلغاء الرحلة', 'icon' => 'cancel'],
                            ];
                        } elseif ($currentStatus === 'in_transit') {
                            $availableStatuses = [
                                'delivered' => ['label' => 'إنهاء الرحلة للتسليم', 'icon' => 'check_circle'],
                                'returned' => ['label' => 'إلغاء الرحلة وارجاعها', 'icon' => 'assignment_return'],
                            ];
                        }
                    @endphp
                    @if (!empty($availableStatuses))
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                class="flex gap-2 items-center px-5 h-11 text-xs font-black text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover shadow-primary/20">
                                <span class="material-symbols-outlined text-[18px]">update</span>
                                تحديث الحالة
                                <span class="material-symbols-outlined text-[18px] transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''">expand_more</span>
                            </button>
                            <div x-show="open" x-cloak x-transition.origin.top.right
                                class="overflow-hidden absolute left-0 top-full z-50 mt-2 w-56 bg-white rounded-2xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-boxdark-2">
                                <form action="{{ route('shipmentpackage.updateStatus', $package->id) }}" method="POST">
                                    @csrf
                                    @foreach ($availableStatuses as $value => $data)
                                        <button type="submit" name="status" value="{{ $value }}"
                                            class="flex gap-3 items-center px-5 py-4 w-full text-sm font-bold text-right text-gray-700 border-b border-gray-50 transition-colors hover:bg-gray-50 dark:hover:bg-boxdark-2 last:border-0 dark:border-boxdark-2 dark:text-white">
                                            <span
                                                class="material-symbols-outlined text-[20px] {{ $value === 'returned' ? 'text-rose-500' : 'text-primary' }}">{{ $data['icon'] }}</span>
                                            {{ $data['label'] }}
                                        </button>
                                    @endforeach
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- الجانب الأيمن: بيانات السائق وخط السير --}}
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
                                    "*،\nتم تكليفك برحلة شحن جديدة رقم: *" .
                                    $package->id .
                                    "*\nعدد الطرود: *" .
                                    ($package->shipments_count ??
                                        ($package->shipments ? $package->shipments->count() : 0)) .
                                    '* طرد.';
                            @endphp
                            <a href="{{ $package->DriverDetection }}" target="_blank"
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

                {{-- بطاقة خط السير --}}
                @php
                    $destinations = collect();
                    if ($package->shipments) {
                        foreach ($package->shipments as $ship) {
                            if ($ship->receiverOfficeBranch) {
                                $destinations->push([
                                    'name' =>
                                        $ship->receiverOfficeBranch->office->name .
                                        ' - ' .
                                        $ship->receiverOfficeBranch->name,
                                    'type' => 'office',
                                    'phone' =>
                                        $ship->receiverOfficeBranch->phone ??
                                        ($ship->receiverOfficeBranch->office->phone ?? null),
                                    'branch' => $ship->receiverOfficeBranch,
                                ]);
                            } elseif ($ship->receiverBranch) {
                                $destinations->push([
                                    'name' => $ship->receiverBranch->name,
                                    'type' => 'internal',
                                    'phone' => null,
                                    'branch' => $ship->receiverBranch,
                                ]);
                            }
                        }
                    }
                    $uniqueDestinations = $destinations->unique('name')->values();
                @endphp
                <div
                    class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm relative overflow-hidden">
                    <div
                        class="absolute -top-10 -right-10 w-40 h-40 rounded-full blur-3xl pointer-events-none bg-primary/5">
                    </div>

                    <h3
                        class="flex relative z-10 gap-2 items-center mb-8 text-lg font-black font-headline text-on-surface dark:text-white">
                        <div class="flex justify-center items-center w-9 h-9 text-emerald-500 rounded-xl bg-emerald-500/10">
                            <span class="material-symbols-outlined text-[22px]">explore</span>
                        </div>
                        خطة خط السير
                    </h3>

                    <div class="relative z-10 pr-8 pl-2 space-y-10">
                        {{-- خط الربط --}}
                        <div
                            class="absolute right-[15px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-gray-200 via-primary/30 to-emerald-400 dark:from-boxdark-2 dark:via-primary/20 dark:to-emerald-500/50 rounded-full">
                        </div>

                        {{-- نقطة الانطلاق --}}
                        <div class="relative z-10">
                            <div
                                class="absolute -right-[43px] top-2 w-5 h-5 bg-white dark:bg-boxdark-2 border-4 border-gray-300 dark:border-gray-600 rounded-full shadow-sm">
                            </div>
                            <div
                                class="p-5 rounded-2xl border border-gray-100 transition-all bg-gray-50/80 dark:bg-boxdark-2/40 dark:border-boxdark hover:bg-gray-50">
                                <div
                                    class="text-[10px] font-black tracking-wider text-gray-400 dark:text-gray-500 mb-1.5 uppercase">
                                    نقطة الانطلاق</div>
                                <div class="text-sm font-black leading-tight text-on-surface dark:text-white">
                                    {{ $package->senderBranch->name ?? 'مستودع الانطلاق' }}</div>
                                <div
                                    class="text-[11px] text-gray-500 dark:text-gray-400 mt-3 font-bold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[14px]">person_outline</span>
                                    مُنشئ الرحلة: {{ $package->creator->name ?? 'النظام' }}
                                </div>
                            </div>
                        </div>

                        {{-- الوجهات --}}
                        <div class="relative z-10">
                            <div
                                class="absolute -right-[43px] top-2 w-5 h-5 bg-emerald-500 ring-4 ring-emerald-500/20 rounded-full shadow-lg shadow-emerald-500/20">
                            </div>
                            <div
                                class="p-5 rounded-2xl border border-emerald-100 transition-all bg-emerald-50/50 dark:bg-emerald-500/5 dark:border-emerald-500/10 hover:bg-emerald-50">
                                <div
                                    class="text-[10px] font-black tracking-wider text-emerald-600/70 dark:text-emerald-400/70 mb-4 uppercase">
                                    الوُجهات المقصودة</div>
                                <div class="flex flex-col gap-2.5">
                                    @forelse($uniqueDestinations as $dest)
                                        <div
                                            class="bg-white dark:bg-boxdark px-4 py-3 rounded-xl text-[13px] font-black text-on-surface dark:text-white flex items-center justify-between border border-emerald-100 dark:border-emerald-500/20 shadow-sm transition-all hover:scale-[1.02]">
                                            <div class="flex items-center gap-2.5">
                                                <span
                                                    class="material-symbols-outlined text-[18px] text-emerald-500">location_on</span>
                                                {{ $dest['name'] }}
                                            </div>
                                            @if ($dest['type'] === 'office' && $dest['phone'])
                                                @php
                                                    $package->external_office_branch = $dest['branch'];
                                                    $whatsappUrl = \App\Services\WhatsApp\WhatsAppLinkService::generate(
                                                        $package,
                                                        'ExternalOfficeDetection',
                                                    );
                                                @endphp
                                                @if ($whatsappUrl)
                                                    <a href="{{ $whatsappUrl }}" target="_blank"
                                                        class="flex shrink-0 items-center gap-1.5 px-3 py-1.5 text-[11px] font-black text-emerald-600 rounded-lg bg-emerald-50 border border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20 hover:bg-emerald-100 dark:hover:bg-emerald-500/30 transition-all shadow-sm"
                                                        title="إرسال الكشف للمكتب عبر الواتساب">
                                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                            <path
                                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                        </svg>
                                                        مراسلة
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-xs italic font-bold text-gray-500 dark:text-gray-400">لا توجد
                                            وجهات مسجلة</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الجانب الأيسر: قائمة الطرود --}}
            <div class="lg:col-span-2">
                <div
                    class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm min-h-full flex flex-col">
                    <div
                        class="flex justify-between items-center p-4 mb-8 rounded-2xl border border-gray-100 bg-gray-50/50 dark:bg-boxdark-2/50 dark:border-boxdark-2">
                        <div class="flex gap-3 items-center">
                            <div
                                class="flex justify-center items-center w-10 h-10 text-gray-500 bg-white rounded-xl border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-400 dark:border-boxdark">
                                <span class="material-symbols-outlined text-[22px]">package_2</span>
                            </div>
                            <h3 class="text-lg font-black font-headline text-on-surface dark:text-white">الطرود المضمنة</h3>
                        </div>
                        <div
                            class="px-4 py-2 text-xs font-black text-white rounded-xl shadow-lg bg-primary shadow-primary/20">
                            {{ $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0) }} طرد
                        </div>

                    </div>

                    <div class="flex flex-col gap-4 flex-1">
                        @forelse($package->shipments as $shipment)
                            <div
                                class="flex items-center gap-5 bg-white dark:bg-boxdark-2 p-5 rounded-[2rem] border border-gray-100 dark:border-boxdark shadow-sm hover:shadow-lg hover:border-primary/20 transition-all duration-300 group">

                                <div
                                    class="flex justify-center items-center w-16 h-16 text-gray-400 bg-gray-50 rounded-2xl border border-gray-100 shadow-inner transition-all duration-300 dark:bg-boxdark shrink-0 dark:border-boxdark group-hover:bg-primary group-hover:text-white">
                                    <span class="material-symbols-outlined text-[30px]">inventory_2</span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap gap-3 items-center mb-2">
                                        <span
                                            class="font-mono text-lg font-black leading-none text-on-surface dark:text-white">{{ $shipment->id }}</span>
                                        <span
                                            class="text-[10px] font-black text-primary bg-primary/5 border border-primary/10 px-2.5 py-1 rounded-lg flex items-center gap-1.5 dark:border-primary/20">
                                            <span class="material-symbols-outlined text-[14px]">account_balance</span>
                                            {{ $shipment->receiverBranch->name ?? 'مستودع' }}
                                        </span>
                                        <span
                                            class="flex items-center gap-2 px-4 py-2.5 rounded-xl {{ $colorClass }} font-black text-xs ring-1 ring-inset shadow-sm">
                                            <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                                            {{ $name }}
                                        </span>
                                    </div>
                                    <div
                                        class="flex flex-wrap gap-y-2 gap-x-5 items-center text-xs font-bold text-gray-500 dark:text-gray-400">
                                        <div class="flex gap-1.5 items-center">
                                            <span
                                                class="material-symbols-outlined text-[16px] text-gray-400">person_outline</span>
                                            <span class="truncate max-w-[150px]">لـ:
                                                {{ $shipment->receiverCustomer->name ?? 'غير مسجل' }}</span>
                                        </div>
                                        <div class="flex gap-1.5 items-center">
                                            <span
                                                class="material-symbols-outlined text-[16px] text-gray-400">qr_code_2</span>
                                            <span
                                                class="font-mono tracking-widest">{{ $shipment->id }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex gap-2 items-center pr-4 border-r border-gray-100 dark:border-boxdark shrink-0">
                                    {{-- عرض التفاصيل --}}
                                    <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                                        class="flex justify-center items-center w-11 h-11 text-gray-400 bg-gray-50 rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark hover:text-primary hover:bg-primary/5 dark:border-boxdark"
                                        title="عرض التفاصيل">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>


                                    {{-- فك الارتباط --}}
                                    @if (!in_array($package->status, ['delivered']))
                                        <form
                                            action="{{ route('shipmentpackage.removeShipment', ['package' => $package->id, 'shipment' => $shipment->id]) }}"
                                            method="POST"
                                            onsubmit="return confirm('تأكيد فك الارتباط لإرجاع الطرد للمستودع؟');">
                                            @csrf
                                            <button type="button"
                                                @click="
        deleteShipmentData = { 
            bond_number: '{{ $shipment->bond_number }}', 
            url: '{{ route('shipmentpackage.removeShipment', ['package' => $package->id, 'shipment' => $shipment->id]) }}' 
        }; 
        showDeleteModal = true;
    "
                                                class="flex justify-center items-center w-11 h-11 text-gray-400 bg-gray-50 rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark hover:text-rose-500 hover:bg-rose-50 dark:border-boxdark dark:hover:bg-rose-500/10 dark:hover:border-rose-500/20"
                                                title="فك ارتباط">
                                                <span class="material-symbols-outlined text-[20px]">link_off</span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex justify-center items-center w-11 h-11 text-gray-200 bg-gray-50 rounded-xl border border-gray-100 cursor-not-allowed dark:bg-boxdark dark:text-gray-700 dark:border-boxdark"
                                            title="لا يمكن فك ارتباط الطرد من رحلة مغلقة">
                                            <span class="material-symbols-outlined text-[20px]">lock_person</span>
                                        </div>
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
                                <p class="mt-1 text-xs font-bold text-gray-400">لم يتم إضافة أي طرود لهذه الإرسالية بعد.
                                </p>
                            </div>
                        @endforelse
                        {{-- ================= نافذة تأكيد فك الارتباط (Delete Modal) ================= --}}
                        <template x-teleport="body">
                            <div x-show="showDeleteModal" x-cloak
                                class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body"
                                dir="rtl">

                                {{-- الخلفية المظللة --}}
                                <div x-show="showDeleteModal" x-transition.opacity.duration.300ms
                                    class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                                    @click="showDeleteModal = false"></div>

                                {{-- المودال نفسه --}}
                                <div x-show="showDeleteModal"
                                    x-transition:enter="transform transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transform transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                                    class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-[2rem] shadow-2xl p-8 text-center border border-gray-100 dark:border-boxdark-2">

                                    {{-- الأيقونة --}}
                                    <div
                                        class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-rose-500 rounded-[1.5rem] border border-rose-100 dark:border-rose-500/20">
                                        <span class="text-4xl material-symbols-outlined">link_off</span>
                                    </div>

                                    <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">
                                        تأكيد فك الارتباط</h3>

                                    <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                                        هل أنت متأكد من فك ارتباط الطرد رقم <br>
                                        <span class="text-base font-black text-on-surface dark:text-white font-headline"
                                            x-text="deleteShipmentData.bond_number"></span>؟<br>
                                        <span class="text-rose-500/80">سيتم إرجاع هذا الطرد إلى المستودع.</span>
                                    </p>

                                    {{-- فورم الإرسال --}}
                                    <form :action="deleteShipmentData.url" method="POST" @submit="isSubmitting = true"
                                        class="flex gap-3">
                                        @csrf

                                        <button type="button" @click="showDeleteModal = false"
                                            class="flex-1 py-3.5 text-sm font-black rounded-xl transition-all text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                                            تراجع
                                        </button>

                                        <button type="submit" :disabled="isSubmitting"
                                            class="flex flex-1 gap-2 justify-center items-center py-3.5 text-sm font-black text-white bg-rose-500 rounded-xl shadow-lg transition-all hover:bg-rose-600 shadow-rose-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                                            <span x-show="!isSubmitting">نعم، فك الارتباط</span>
                                            <span x-show="isSubmitting"
                                                class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </template>
                        {{-- ================= زر إضافة طرد جديد (Desktop) ================= --}}
                        @if (!in_array($package->status, ['delivered', 'returned', 'received_at_branch']) && isset($availableShipments))
                            <button type="button" @click="isAddModalOpen = true"
                                class="flex gap-2 justify-center items-center mt-4 w-full h-14 text-sm font-black rounded-2xl border-2 border-dashed transition-all border-gray-200 dark:border-boxdark text-gray-400 dark:text-gray-500 hover:border-primary hover:text-primary hover:bg-primary/5 dark:hover:bg-primary/10 active:scale-95">
                                <span class="material-symbols-outlined text-[24px]">add_box</span>
                                إضافة طرد جديد للإرسالية
                            </button>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= نافذة إضافة طرد منبثقة (Desktop Modal) ================= --}}
        {{-- ================= نافذة إضافة طرد منبثقة (Desktop Modal) ================= --}}
        @if (!in_array($package->status, ['delivered', 'returned', 'received_at_branch']) && isset($availableShipments))
            {{-- استخدام x-teleport لنقل المودال خارج القوائم الجانبية ليكون فوق كل شيء --}}
            <template x-teleport="body">
                <div x-show="isAddModalOpen" x-cloak
                    class="fixed inset-0 z-[99999] flex justify-center items-center p-4 sm:p-0 font-body" dir="rtl">

                    {{-- الخلفية المظللة --}}
                    <div x-show="isAddModalOpen" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                        @click="isAddModalOpen = false"></div>

                    {{-- المودال نفسه --}}
                    <div x-show="isAddModalOpen" x-transition:enter="transform transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transform transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="relative bg-white dark:bg-boxdark w-full max-w-3xl max-h-[85vh] rounded-[2rem] shadow-2xl flex flex-col overflow-hidden border border-gray-100 dark:border-boxdark-2">

                        {{-- الهيدر وزر الإغلاق --}}
                        <div
                            class="flex z-10 justify-between items-center p-6 border-b border-gray-100 dark:border-boxdark-2 bg-white dark:bg-boxdark shrink-0">
                            <div>
                                <h2
                                    class="text-xl font-black text-on-surface dark:text-white font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">add_circle</span>
                                    إضافة طرد للإرسالية
                                </h2>
                                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-1">ابحث عن الطرد المطلوب
                                    واضغط إضافة لربطه بالرحلة</p>
                            </div>
                            <button type="button" @click="isAddModalOpen = false"
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-boxdark-2 text-gray-500 hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-500/10 dark:hover:text-rose-400 transition-colors active:scale-90">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        {{-- حقل البحث --}}
                        <div
                            class="p-6 border-b bg-gray-50/50 dark:bg-boxdark-2/50 shrink-0 border-gray-100 dark:border-boxdark-2">
                            <div class="relative group">
                                <input type="text" x-model="searchShipment"
                                    placeholder="ابحث برقم السند أو اسم المستلم..."
                                    class="pr-12 pl-4 w-full h-14 text-sm font-bold bg-white dark:bg-boxdark rounded-xl border transition-all outline-none border-gray-200 dark:border-boxdark focus:border-primary focus:ring-2 focus:ring-primary/20 text-gray-700 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 shadow-sm">
                                <div
                                    class="flex absolute inset-y-0 right-0 items-center pr-4 transition-colors text-gray-400 group-focus-within:text-primary">
                                    <span class="material-symbols-outlined text-[24px]">search</span>
                                </div>
                            </div>
                        </div>

                        {{-- قائمة الطرود المتاحة للإضافة (عرض شبكي Grid) --}}
                        <div class="overflow-y-auto flex-1 p-6 custom-scrollbar bg-gray-50/30 dark:bg-boxdark/30">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @forelse($availableShipments as $availShipment)
                                    <div class="flex flex-col justify-between p-5 bg-white dark:bg-boxdark-2 rounded-2xl border shadow-sm transition-colors border-gray-100 dark:border-boxdark hover:border-primary/30 group"
                                        x-show="'{{ $availShipment->bond_number }}'.includes(searchShipment) || '{{ $availShipment->receiverCustomer->name ?? '' }}'.includes(searchShipment)">

                                        <div class="mb-5">
                                            <div class="flex justify-between items-start mb-2">
                                                <span
                                                    class="font-mono text-base font-black text-on-surface dark:text-white">{{ $availShipment->bond_number }}</span>
                                                <span
                                                    class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-boxdark text-gray-600 dark:text-gray-300 flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px]">store</span>
                                                    {{ $availShipment->receiverBranch->name ?? 'مستودع' }}
                                                </span>
                                            </div>
                                            <p
                                                class="text-[11px] font-bold text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                                <span class="material-symbols-outlined text-[16px]">person</span>
                                                {{ $availShipment->receiverCustomer->name ?? 'غير مسجل' }}
                                            </p>
                                        </div>

                                        <form action="{{ route('shipmentpackage.addShipment', $package->id) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="shipment_id" value="{{ $availShipment->id }}">
                                            <button type="submit"
                                                class="w-full h-11 bg-primary/10 text-primary hover:bg-primary hover:text-white dark:bg-primary/20 dark:hover:bg-primary rounded-xl text-xs font-black transition-all active:scale-95 flex justify-center items-center gap-2">
                                                <span class="material-symbols-outlined text-[18px]">add</span>
                                                إضافة للإرسالية
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="col-span-full py-12 text-center">
                                        <div
                                            class="flex justify-center items-center mx-auto mb-4 w-16 h-16 bg-gray-100 rounded-full dark:bg-boxdark">
                                            <span
                                                class="material-symbols-outlined text-[32px] text-gray-400 dark:text-gray-600">inventory_2</span>
                                        </div>
                                        <p class="text-sm font-black text-gray-600 dark:text-gray-400">لا توجد طرود متاحة
                                            في المستودع</p>
                                        <p class="text-[11px] font-bold text-gray-400 dark:text-gray-500 mt-1">جميع الطرود
                                            تم شحنها أو تسليمها.</p>
                                    </div>
                                @endforelse

                                {{-- رسالة تظهر عندما لا يتطابق البحث مع أي نتيجة --}}
                                @if ($availableShipments->isNotEmpty())
                                    <div class="col-span-full py-12 text-center"
                                        x-show="searchShipment !== '' && $el.previousElementSibling.querySelectorAll('div[x-show]').length && Array.from($el.previousElementSibling.querySelectorAll('div[x-show]')).every(el => el.style.display === 'none')"
                                        x-cloak>
                                        <p class="text-sm font-black text-gray-500 dark:text-gray-400">لا توجد نتائج مطابقة
                                            للبحث.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        @endif

    </div>
@endsection
