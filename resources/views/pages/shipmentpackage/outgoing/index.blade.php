@extends('layouts.app')

@section('title', 'الشحنات المرسلة (المانيفست)')
@section('Breadcrumb', 'إدارة الشحنات / الشحنات المرسلة')

@section('content')
    <style type="text/tailwindcss">
        @layer components {

            /* تلوين الصفحة النشطة */
            .pagination-container span[aria-current="page"]>span {
                @apply bg-primary border-primary text-white font-black !important;
            }

            /* تلوين أرقام الصفحات العادية والأسهم */
            .pagination-container a,
            .pagination-container span[aria-disabled="true"]>span {
                @apply text-primary border-primary/30 dark:border-primary/20 !important;
            }

            /* تأثير عند تمرير الماوس */
            .pagination-container a:hover {
                @apply bg-primary-container text-primary-hover dark:bg-primary/10 dark:text-primary !important;
            }

            /* تدوير الحواف وإزالة الظل */
            .pagination-container .isolate>* {
                @apply rounded-lg mx-0.5 !important;
            }

            .pagination-container .isolate {
                @apply shadow-none !important;
            }
        }
    </style>

    <div x-data="{ searchQuery: '' }"
        class="flex relative flex-col gap-6 p-4 rounded-3xl bg-surface dark:bg-boxdark-2 lg:p-6 font-body" dir="rtl">

        {{-- الهيدر العلوي --}}
        <div
            class="flex justify-between items-center p-2 mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 lg:p-4">
            <div class="flex flex-col">
                <h1 class="text-2xl font-black md:text-3xl font-headline text-on-surface dark:text-white">الشحنات المرسلة
                </h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-bodydark">
                    إجمالي <span class="font-bold text-primary">{{ $packages->total() ?? 0 }}</span> إرسالية مسجلة
                </p>
            </div>

            <a href="{{ route('shipmentpackage.outgoing.create') }}"
                class="flex justify-center items-center w-32 h-12 text-white rounded-2xl shadow-lg transition-transform text-md shrink-0 bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-90"
                title="إضافة إرسالية جديدة">
                <span class="text-[26px] material-symbols-outlined">add_box</span>
                إضافة شحنة
            </a>
        </div>

        {{-- شريط البحث والفلترة --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            {{-- البحث --}}
            <div class="relative flex-1 max-w-xl">
                <input type="text" x-model="searchQuery" placeholder="ابحث برقم التتبع أو الوجهة..."
                    class="pr-12 pl-4 w-full h-14 text-sm font-bold placeholder-gray-400 bg-white rounded-2xl border border-gray-100 shadow-sm transition-all outline-none dark:bg-boxdark dark:border-boxdark-2 focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white">
                <span
                    class="absolute right-4 top-1/2 text-gray-400 transition-colors -translate-y-1/2 material-symbols-outlined dark:text-bodydark">search</span>
            </div>

            {{-- شريط الفلترة حسب الحالة --}}
            <div class="flex overflow-x-auto gap-2 pb-2 custom-scrollbar snap-x snap-mandatory lg:pb-0">
                {{-- الكل --}}
                <a href="{{ route('shipmentpackage.outgoing.index') }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ !request('status') ? 'bg-boxdark text-white border-boxdark shadow-md dark:bg-primary dark:border-primary dark:shadow-primary/20' : 'bg-white text-gray-500 border-gray-100 hover:bg-surface dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                    الكل
                </a>

                {{-- قيد التجهيز --}}
                <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'pending']) }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ request('status') == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-500/20 dark:bg-amber-600 dark:border-amber-600' : 'bg-white text-amber-600 border-amber-100 hover:bg-amber-50 dark:bg-boxdark-2 dark:text-amber-500 dark:border-boxdark dark:hover:bg-amber-500/10' }}">
                    قيد التجهيز
                </a>

                {{-- في الطريق --}}
                <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'in_transit']) }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ request('status') == 'in_transit' ? 'bg-blue-500 text-white border-blue-500 shadow-md shadow-blue-500/20 dark:bg-blue-600 dark:border-blue-600' : 'bg-white text-blue-600 border-blue-100 hover:bg-blue-50 dark:bg-boxdark-2 dark:text-blue-500 dark:border-boxdark dark:hover:bg-blue-500/10' }}">
                    في الطريق
                </a>

                {{-- بالمستودع --}}
                <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'received_at_branch']) }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ request('status') == 'received_at_branch' ? 'bg-purple-500 text-white border-purple-500 shadow-md shadow-purple-500/20 dark:bg-purple-600 dark:border-purple-600' : 'bg-white text-purple-600 border-purple-100 hover:bg-purple-50 dark:bg-boxdark-2 dark:text-purple-500 dark:border-boxdark dark:hover:bg-purple-500/10' }}">
                    بالمستودع
                </a>

                {{-- خرج للتوصيل --}}
                <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'out_for_delivery']) }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ request('status') == 'out_for_delivery' ? 'bg-indigo-500 text-white border-indigo-500 shadow-md shadow-indigo-500/20 dark:bg-indigo-600 dark:border-indigo-600' : 'bg-white text-indigo-600 border-indigo-100 hover:bg-indigo-50 dark:bg-boxdark-2 dark:text-indigo-500 dark:border-boxdark dark:hover:bg-indigo-500/10' }}">
                    خرج للتوصيل
                </a>

                {{-- مكتملة (تم التسليم) --}}
                <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'delivered']) }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ request('status') == 'delivered' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/20 dark:bg-emerald-600 dark:border-emerald-600' : 'bg-white text-emerald-600 border-emerald-100 hover:bg-emerald-50 dark:bg-boxdark-2 dark:text-emerald-500 dark:border-boxdark dark:hover:bg-emerald-500/10' }}">
                    مكتملة
                </a>

                {{-- مرتجعة --}}
                <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'returned']) }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ request('status') == 'returned' ? 'bg-rose-500 text-white border-rose-500 shadow-md shadow-rose-500/20 dark:bg-rose-600 dark:border-rose-600' : 'bg-white text-rose-600 border-rose-100 hover:bg-rose-50 dark:bg-boxdark-2 dark:text-rose-500 dark:border-boxdark dark:hover:bg-rose-500/10' }}">
                    مرتجعة
                </a>

                {{-- ملغاة --}}
                <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'cancelled']) }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ request('status') == 'cancelled' ? 'bg-slate-500 text-white border-slate-500 shadow-md shadow-slate-500/20 dark:bg-slate-600 dark:border-slate-600' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50 dark:bg-boxdark-2 dark:text-slate-500 dark:border-boxdark dark:hover:bg-slate-500/10' }}">
                    ملغاة
                </a>
            </div>
        </div>

        {{-- شبكة الإرساليات --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 2xl:grid-cols-3">
            @forelse($packages as $package)
                <div x-show="searchQuery === '' || '{{ $package->tracking_number }}'.includes(searchQuery) || '{{ $package->driver->name ?? '' }}'.includes(searchQuery)"
                    x-data="{ openMenu: false }"
                    class="bg-white dark:bg-boxdark rounded-[24px] border border-gray-100 dark:border-boxdark-2 shadow-sm hover:shadow-md hover:border-primary/30 dark:hover:border-primary/50 overflow-visible transition-all duration-300 relative group">

                    {{-- شريط لوني علوي خفيف يعطي طابعاً مميزاً --}}
                    <div
                        class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-primary to-primary-hover rounded-t-[24px] opacity-80">
                    </div>

                    {{-- ================= 1. الرأس (Header) ================= --}}
                    <div class="flex justify-between items-start p-5">
                        <div class="flex gap-3 items-center">
                            {{-- أيقونة الإرسالية --}}
                            <div
                                class="w-11 h-11 rounded-[14px] bg-surface dark:bg-boxdark-2 flex items-center justify-center border border-gray-100 dark:border-boxdark group-hover:scale-105 transition-transform duration-300">
                                <span
                                    class="material-symbols-outlined text-gray-500 dark:text-bodydark text-[22px]">local_shipping</span>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-sm font-black tracking-tight text-on-surface dark:text-white font-headline">
                                    {{ $package->tracking_number }}
                                </h3>
                                <p
                                    class="text-[10px] font-bold text-gray-400 dark:text-gray-500 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">schedule</span>
                                    {{ $package->created_at->format('Y/m/d - H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2 items-center">
                            {{-- شارة الحالة --}}
                            @php
                                $statusMap = [
                                    'pending' => [
                                        'label' => 'قيد التجهيز',
                                        'class' =>
                                            'bg-amber-50 text-amber-600 ring-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400',
                                    ],
                                    'in_transit' => [
                                        'label' => 'في الطريق',
                                        'class' =>
                                            'bg-blue-50 text-blue-600 ring-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400',
                                    ],
                                    'delivered' => [
                                        'label' => 'مكتملة',
                                        'class' =>
                                            'bg-emerald-50 text-emerald-600 ring-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                    ],
                                    'returned' => [
                                        'label' => 'مرتجعة',
                                        'class' =>
                                            'bg-rose-50 text-rose-600 ring-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400',
                                    ],
                                ];
                                $currStatus = $statusMap[$package->status] ?? $statusMap['pending'];
                            @endphp
                            <span
                                class="px-2.5 py-1 rounded-md text-[9px] font-black ring-1 ring-inset {{ $currStatus['class'] }}">
                                {{ $currStatus['label'] }}
                            </span>

                            {{-- قائمة الخيارات --}}
                            <div class="relative">
                                <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                    class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-full transition-colors hover:bg-surface hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 dark:hover:bg-boxdark-2 dark:hover:text-white">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                    class="overflow-hidden absolute left-0 top-full z-50 py-1.5 mt-1.5 w-48 rounded-2xl border border-gray-100 shadow-lg backdrop-blur-md bg-white/95 dark:bg-boxdark-2/95 dark:border-boxdark">

                                    <a href="{{ route('shipmentpackage.outgoing.show', $package->id) }}"
                                        class="flex gap-2.5 items-center px-4 py-2.5 text-xs font-bold text-gray-600 transition-colors hover:bg-surface hover:text-primary dark:text-gray-300 dark:hover:bg-boxdark">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        عرض التفاصيل
                                    </a>
                                    @if (!in_array($package->status, ['returned', 'cancelled']))
                                        <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->uuid]) }}"
                                            target="_blank"
                                            class="flex gap-2.5 items-center px-4 py-2.5 text-xs font-bold text-gray-600 transition-colors hover:bg-surface hover:text-primary dark:text-gray-300 dark:hover:bg-boxdark">
                                            <span class="material-symbols-outlined text-[18px]">print</span>
                                            طباعة كشف الرحلة
                                        </a>

                                        @if ($package->driver && $package->driver->phone)
                                            <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>
                                            <a href="{{ $package->DriverDetection }}" target="_blank"
                                                class="flex gap-2.5 items-center px-4 py-2.5 text-xs font-bold text-gray-600 transition-colors hover:bg-surface hover:text-on-surface dark:text-gray-300 dark:hover:bg-boxdark dark:hover:text-white">
                                                <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                </svg>
                                                إرسال لسائق
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= 2. الفاصل المقطّع (Ticket Divider) ================= --}}
                    <div class="flex overflow-hidden relative items-center h-4">
                        <div
                            class="absolute -right-2 w-4 h-4 rounded-full border-l shadow-inner bg-surface border-gray-200/60 dark:bg-boxdark-2 dark:border-boxdark">
                        </div>
                        <div class="w-full border-t-[1.5px] border-dashed border-gray-200/70 dark:border-boxdark-2"></div>
                        <div
                            class="absolute -left-2 w-4 h-4 rounded-full border-r shadow-inner bg-surface border-gray-200/60 dark:bg-boxdark-2 dark:border-boxdark">
                        </div>
                    </div>

                    {{-- ================= 3. جسد البطاقة ================= --}}
                    <div class="p-5 pt-4 space-y-5">
                        <div class="flex gap-4 justify-between items-start">

                            {{-- العمود الأيمن: خط السير --}}
                            <div class="flex gap-3 items-stretch w-1/2">
                                <div class="flex flex-col items-center mt-1">
                                    <div
                                        class="w-2.5 h-2.5 rounded-full border-[2.5px] border-gray-300 bg-white z-10 dark:border-gray-500 dark:bg-boxdark">
                                    </div>
                                    <div class="w-[1.5px] h-10 bg-gray-200 my-0.5 dark:bg-boxdark-2"></div>
                                    <div
                                        class="w-2.5 h-2.5 rounded-full border-[2.5px] border-primary bg-white z-10 shadow-[0_0_8px_rgba(247,144,9,0.4)] dark:bg-boxdark">
                                    </div>
                                </div>

                                <div class="flex flex-col flex-1 justify-between space-y-4">
                                    <div>
                                        <p
                                            class="text-[9px] font-black text-gray-400 mb-0.5 tracking-wide dark:text-gray-500">
                                            فرع التجميع</p>
                                        <p
                                            class="text-xs font-bold text-on-surface truncate max-w-[100px] dark:text-gray-200">
                                            {{ $package->senderBranch->name ?? 'غير محدد' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-[9px] font-black text-gray-400 mb-0.5 tracking-wide flex items-center gap-1 dark:text-gray-500">
                                            الوجهة:
                                        </p>

                                        @php
                                            $destinations = $package->shipments
                                                ->map(function ($shipment) {
                                                    return $shipment->receiverBranch->name ??
                                                        ($shipment->receiverOfficeBranch->name ?? null);
                                                })
                                                ->filter()
                                                ->unique()
                                                ->values();

                                            $destText =
                                                $destinations->count() > 1
                                                    ? 'وجهات متعددة (' . $destinations->count() . ')'
                                                    : $destinations->first() ?? 'غير محدد';
                                        @endphp

                                        <p class="text-xs font-bold text-primary truncate max-w-[100px]"
                                            title="{{ $destinations->join('، ') }}">
                                            {{ $destText }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- العمود الأيسر: تفاصيل إضافية --}}
                            <div
                                class="flex flex-col gap-2.5 justify-center p-3 w-1/2 rounded-xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <div class="flex justify-between items-center"
                                    title="{{ $package->driver->phone ?? '---' }}">
                                    <span class="text-[10px] text-gray-400 font-bold dark:text-gray-500">السائق:</span>
                                    <span
                                        class="text-[10px] font-black text-on-surface bg-white px-2 py-0.5 rounded-md border border-gray-100 shadow-sm truncate max-w-[70px] dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-200">
                                        {{ $package->driver->name ?? 'غير محدد' }}
                                    </span>
                                </div>
                                <div
                                    class="flex justify-between items-center pt-2 mt-1 border-t border-gray-200/50 dark:border-boxdark">
                                    <span class="text-[10px] text-gray-400 font-bold dark:text-gray-500">بواسطة:</span>
                                    <span
                                        class="text-[10px] font-black text-gray-600 truncate max-w-[70px] dark:text-gray-300">
                                        {{ $package->creator->name ?? 'النظام' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ================= 4. كبسولة الفوتر (الداكنة) ================= --}}
                        <div
                            class="bg-boxdark rounded-[18px] p-3.5 flex justify-between items-center shadow-lg shadow-gray-900/10 dark:bg-black/30 dark:border dark:border-boxdark-2">
                            {{-- الحالة --}}
                            <div class="flex gap-2.5 items-center">
                                <div
                                    class="flex justify-center items-center w-9 h-9 text-gray-300 rounded-xl bg-boxdark-2 dark:bg-boxdark dark:text-gray-400">
                                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 mb-0.5">حالة الرحلة</p>
                                    <p class="text-[11px] font-bold text-white tracking-wide">
                                        {{ $currStatus['label'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- إجمالي الطرود --}}
                            <div class="pl-2 text-left">
                                <p class="text-[9px] font-bold text-gray-400 mb-0.5">إجمالي الطرود</p>
                                <p class="text-lg font-black tracking-tight leading-none text-primary font-headline">
                                    {{ $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0) }}
                                    <span class="text-[10px] font-bold text-gray-300">طرد</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div
                    class="flex flex-col col-span-full items-center justify-center py-20 bg-white rounded-[24px] border-2 border-dashed border-gray-200 mt-4 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                    <div class="relative mb-4">
                        <div class="absolute inset-0 rounded-full blur-xl bg-primary/20"></div>
                        <div
                            class="w-16 h-16 bg-surface dark:bg-boxdark-2 rounded-[18px] flex items-center justify-center border border-white dark:border-boxdark shadow-sm relative z-10">
                            <span
                                class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">search_off</span>
                        </div>
                    </div>
                    <h3 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد إرساليات</h3>
                    <p class="text-[11px] font-bold text-gray-400 dark:text-bodydark mt-1">لم نجد أي إرساليات مجمعة حتى
                        الآن.</p>
                </div>
            @endforelse
        </div>

        {{-- الترقيم --}}
        @if (method_exists($packages, 'hasPages') && $packages->hasPages())
            <div class="flex justify-center items-center pt-6 mt-4 w-full">
                <div
                    class="w-full p-4 transition-all bg-white border shadow-sm pagination-container rounded-[2rem] border-primary/50 dark:bg-boxdark dark:border-primary/30 hover:shadow-md lg:w-fit lg:min-w-[50%]">
                    <div class="flex overflow-x-auto justify-center w-full custom-scrollbar text-primary">
                        {{ $packages->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
