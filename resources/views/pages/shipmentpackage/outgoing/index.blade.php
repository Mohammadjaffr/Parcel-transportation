@extends('layouts.app')

@section('title', 'الشحنات المرسلة (المانيفست)')

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

    <div x-data="{ searchQuery: '' }" class="flex relative flex-col gap-6 font-body" dir="rtl">

        {{-- الهيدر العلوي --}}
        <div
            class="flex justify-between items-center p-2 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 lg:p-4">
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
    {{ !request('status') ? 'bg-boxdark text-primary border-primary shadow-md dark:bg-primary dark:border-primary dark:shadow-primary/20' : 'bg-white text-gray-500 border-gray-100 hover:bg-surface dark:bg-primary dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
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
                {{-- <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'out_for_delivery']) }}"
                    class="snap-start shrink-0 px-4 h-11 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
    {{ request('status') == 'out_for_delivery' ? 'bg-indigo-500 text-white border-indigo-500 shadow-md shadow-indigo-500/20 dark:bg-indigo-600 dark:border-indigo-600' : 'bg-white text-indigo-600 border-indigo-100 hover:bg-indigo-50 dark:bg-boxdark-2 dark:text-indigo-500 dark:border-boxdark dark:hover:bg-indigo-500/10' }}">
                    خرج للتوصيل
                </a> --}}

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
        <div
            class="bg-white rounded-[2rem] border shadow-sm border-slate-200/60 dark:bg-boxdark dark:border-boxdark-2 transition-all">
            <div class="overflow-x-auto min-h-[350px] custom-scrollbar rounded-[2rem]">
                <table class="w-full text-sm text-right whitespace-nowrap">

                    {{-- رأس الجدول --}}
                    <thead
                        class="text-xs font-black uppercase border-b text-slate-500 bg-slate-50/80 border-slate-200/60 dark:bg-boxdark-2/50 dark:border-boxdark dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-5">رقم الإرسالية / التاريخ</th>
                            <th class="px-6 py-5">فرع التجميع (المرسل)</th>
                            <th class="px-6 py-5">الوجهة</th>
                            <th class="px-6 py-5">بيانات الحمولة</th>
                            <th class="px-6 py-5 text-center">الحالة</th>
                            <th class="px-6 py-5 text-center">إجراءات</th>
                        </tr>
                    </thead>

                    {{-- جسم الجدول --}}
                    <tbody class="divide-y divide-slate-100 dark:divide-boxdark-2">
                        @forelse($packages as $package)
                            <tr x-show="searchQuery === '' || '{{ $package->tracking_number }}'.includes(searchQuery) || '{{ $package->driver->name ?? '' }}'.includes(searchQuery)"
                                class="transition-colors duration-200 hover:bg-slate-50/50 dark:hover:bg-boxdark-2/30 group">

                                {{-- 1. رقم الإرسالية والتاريخ --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 items-center">
                                        <div
                                            class="flex justify-center items-center w-11 h-11 rounded-xl border transition-transform bg-slate-50 border-slate-100 dark:bg-boxdark-2 dark:border-gray-700 group-hover:scale-105">
                                            <span
                                                class="material-symbols-outlined text-slate-400 dark:text-gray-500 text-[22px]">local_shipping</span>
                                        </div>
                                        <div>
                                            <div class="font-black text-slate-800 dark:text-white font-headline">
                                                {{ $package->tracking_number }}</div>
                                            <div
                                                class="flex gap-1 items-center mt-0.5 text-[11px] font-bold text-slate-400 dark:text-gray-500">
                                                <span class="material-symbols-outlined text-[12px]">schedule</span>

                                                <span
                                                    class="text-primary/80">{{ $package->created_at->translatedFormat('l') }}</span>
                                                <span class="mx-0.5 text-slate-300">|</span>
                                                <span>{{ $package->created_at->format('Y/m/d') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. فرع التجميع (بواسطة من) --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-gray-200 truncate max-w-[150px]">
                                        {{ $package->senderBranch->name ?? 'غير محدد' }}
                                    </div>
                                    <div class="text-[11px] font-bold text-slate-400 dark:text-gray-500 mt-0.5">
                                        بواسطة: {{ $package->creator->name ?? 'النظام' }}
                                    </div>
                                </td>

                                {{-- 3. الوجهة (حساب الوجهات المتعددة) --}}
                                <td class="px-6 py-4">
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

                                    <div
                                        class="inline-flex gap-1.5 items-center px-2.5 py-1 rounded-lg border bg-primary/5 border-primary/10">
                                        <span class="material-symbols-outlined text-[16px] text-primary">route</span>
                                        <span class="text-xs font-black text-primary truncate max-w-[150px]"
                                            title="{{ $destinations->join('، ') }}">
                                            {{ $destText }}
                                        </span>
                                    </div>
                                </td>

                                {{-- 4. بيانات الحمولة (السائق والعدد) --}}
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-gray-200 truncate max-w-[150px]">
                                        <span class="text-[10px] text-slate-400 dark:text-gray-500 ml-1">السائق:</span>
                                        {{ $package->driver->name ?? 'غير محدد' }}
                                    </div>

                                    <x-phone-number :value="$package->driver?->phone ?? '---'"
                                        class="text-[15px] font-bold text-gray-500 dark:text-bodydark" />
                                    <div
                                        class="flex gap-1 items-center mt-0.5 text-[11px] font-bold text-slate-500 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                        إجمالي: <span
                                            class="ml-1 font-black text-primary">{{ $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0) }}</span>

                                        طرد
                                    </div>
                                </td>

                                {{-- 5. الحالة --}}
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusMap = [
                                            'pending' => [
                                                'label' => 'قيد التجهيز',
                                                'class' =>
                                                    'bg-amber-50 text-amber-600 ring-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400',
                                                'icon' => 'schedule',
                                            ],
                                            'in_transit' => [
                                                'label' => 'في الطريق',
                                                'class' =>
                                                    'bg-blue-50 text-blue-600 ring-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400',
                                                'icon' => 'local_shipping',
                                            ],
                                            'delivered' => [
                                                'label' => 'مكتملة',
                                                'class' =>
                                                    'bg-emerald-50 text-emerald-600 ring-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'icon' => 'task_alt',
                                            ],
                                            'returned' => [
                                                'label' => 'مرتجعة',
                                                'class' =>
                                                    'bg-rose-50 text-rose-600 ring-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400',
                                                'icon' => 'assignment_return',
                                            ],
                                        ];
                                        $currStatus = $statusMap[$package->status] ?? $statusMap['pending'];
                                    @endphp

                                    <div
                                        class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl ring-1 ring-inset {{ $currStatus['class'] }}">
                                        <span
                                            class="material-symbols-outlined text-[16px]">{{ $currStatus['icon'] }}</span>
                                        <span>{{ $currStatus['label'] }}</span>
                                    </div>
                                </td>

                                {{-- 6. الإجراءات (النقاط الثلاث) --}}
                                <td class="px-6 py-4 text-center">
                                    <div x-data="{ openMenu: false }" class="inline-block relative text-right">
                                        <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                            class="flex justify-center items-center w-9 h-9 rounded-full transition-colors text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-boxdark-2 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-primary/20">
                                            <span class="material-symbols-outlined text-[22px]">more_vert</span>
                                        </button>

                                        <div x-show="openMenu" x-transition.opacity.scale.origin.top.left x-cloak
                                            class="absolute top-full left-0 mt-2 w-48 bg-white dark:bg-boxdark-2 rounded-[1.2rem] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100 dark:border-boxdark z-[99] overflow-hidden py-1.5 text-right">

                                            <a href="{{ route('shipmentpackage.outgoing.show', $package->id) }}"
                                                class="flex gap-2.5 items-center px-4 py-2.5 text-xs font-bold transition-colors text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark hover:text-primary">
                                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                عرض التفاصيل
                                            </a>

                                            @if (!in_array($package->status, ['returned', 'cancelled']))
                                                <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->uuid]) }}"
                                                    target="_blank"
                                                    class="flex gap-2.5 items-center px-4 py-2.5 text-xs font-bold transition-colors text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">print</span>
                                                    طباعة كشف الرحلة
                                                </a>

                                                @if ($package->driver && $package->driver->phone)
                                                    <div class="mx-3 my-1 h-px bg-slate-100 dark:bg-gray-700/50"></div>
                                                    <a href="{{ $package->DriverDetection }}" target="_blank"
                                                        class="flex gap-2.5 items-center px-4 py-2.5 text-xs font-bold transition-colors text-slate-600 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:text-emerald-700 dark:hover:text-emerald-400">
                                                        <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                        </svg>
                                                        إرسال للسائق
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- حالة عدم وجود بيانات (Empty State) --}}
                            <tr>
                                <td colspan="6" class="px-6 py-24 text-center">
                                    <div class="flex flex-col justify-center items-center">
                                        <div class="relative mb-5">
                                            <div class="absolute inset-0 rounded-full blur-xl bg-primary/20"></div>
                                            <div
                                                class="flex relative z-10 justify-center items-center w-20 h-20 rounded-[1.5rem] border shadow-sm bg-slate-50 border-slate-100 dark:bg-boxdark-2 dark:border-boxdark">
                                                <span
                                                    class="material-symbols-outlined text-[40px] text-slate-300 dark:text-gray-600">local_shipping</span>
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-black text-slate-700 dark:text-white font-headline">لا توجد
                                            إرساليات</h3>
                                        <p class="mt-1.5 text-sm font-bold text-slate-400 dark:text-gray-500">لم نجد أي
                                            إرساليات تطابق بحثك حالياً.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- الترقيم --}}
            @if (method_exists($packages, 'hasPages') && $packages->hasPages())
                <div class="px-6 py-5 rounded-b-[2rem] pagination-container">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
