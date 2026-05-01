@extends('layouts.app')

@section('title', 'الشحنات المستقبلة (الواردة)')

@section('content')
    <div class="pb-24 min-h-screen bg-slate-50/50 dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl" x-data="{ searchQuery: '' }">

        {{-- ================= الشريط العلوي ================= --}}
        <div class="sticky top-0 z-40 border-b shadow-sm backdrop-blur-md border-slate-100 bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
            <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
                <div class="flex gap-4 items-center">
                    <a href="{{ route('shipmentpackage.index') }}"
                        class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border shadow-sm transition-colors text-slate-500 border-slate-100 dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>
                    <div>
                        <h1 class="text-xl font-black md:text-2xl font-headline text-slate-800 dark:text-white">الإرساليات الواردة</h1>
                        <p class="text-[11px] md:text-xs font-bold text-slate-500 dark:text-bodydark mt-0.5">سجل الإرساليات المستقبلة لفرعك</p>
                    </div>
                </div>

                <a href="{{ route('shipmentpackage.incoming.create') }}"
                    class="flex gap-2 justify-center items-center px-5 w-full h-11 text-sm font-bold text-white bg-emerald-500 rounded-xl shadow-lg transition-all hover:bg-emerald-600 shadow-emerald-500/20 active:scale-95 md:w-auto">
                    <span class="text-[22px] material-symbols-outlined">add_box</span>
                    <span class="hidden md:inline">استلام إرسالية جديدة</span>
                </a>
            </div>
        </div>

        {{-- ================= محتوى الصفحة ================= --}}
        <div class="p-4 mx-auto mt-4 max-w-7xl md:p-6">

            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-slate-100 dark:border-boxdark-2 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden transition-colors">

                <div class="flex flex-col gap-4 justify-between items-start p-5 bg-white border-b border-slate-100 dark:border-boxdark-2 dark:bg-boxdark md:flex-row md:items-center">
                    <h3 class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white font-headline">
                        <div class="flex justify-center items-center w-10 h-10 rounded-xl shadow-inner text-primary bg-primary/10 shrink-0">
                            <span class="material-symbols-outlined text-[22px]">move_to_inbox</span>
                        </div>
                        قائمة الإرساليات الواردة
                    </h3>
                    
                    {{-- شريط البحث --}}
                    <div class="relative w-full md:w-80">
                        <input type="text" x-model="searchQuery" placeholder="ابحث برقم الإرسالية، أو اسم السائق..."
                            class="py-2.5 pr-4 pl-10 w-full text-sm font-bold rounded-xl border shadow-sm transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-700 dark:text-white placeholder-slate-400">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[20px]">search</span>
                    </div>
                </div>

                {{-- ================= شريط الفلترة ================= --}}
                <div class="flex overflow-x-auto gap-2 p-3 border-b bg-slate-50/50 dark:bg-boxdark border-slate-100 dark:border-boxdark-2 custom-scrollbar snap-x snap-mandatory">
                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}"
                        class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                        {{ !request('status') ? 'bg-slate-800 text-white border-slate-800 shadow-md dark:bg-primary dark:border-primary dark:shadow-primary/20' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                        الكل
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => null]) }}"
                        class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                        {{ request('status') == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-500/20 dark:bg-amber-600 dark:border-amber-600' : 'bg-transparent text-amber-600 border-transparent hover:bg-amber-50 hover:border-amber-100 dark:text-amber-500 dark:hover:bg-amber-500/10' }}">
                        قيد التجهيز
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'in_transit', 'page' => null]) }}"
                        class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                        {{ request('status') == 'in_transit' ? 'bg-blue-500 text-white border-blue-500 shadow-md shadow-blue-500/20 dark:bg-blue-600 dark:border-blue-600' : 'bg-transparent text-blue-600 border-transparent hover:bg-blue-50 hover:border-blue-100 dark:text-blue-500 dark:hover:bg-blue-500/10' }}">
                        في الطريق إلينا
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'received_at_branch', 'page' => null]) }}"
                        class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                        {{ request('status') == 'received_at_branch' ? 'bg-purple-500 text-white border-purple-500 shadow-md shadow-purple-500/20 dark:bg-purple-600 dark:border-purple-600' : 'bg-transparent text-purple-600 border-transparent hover:bg-purple-50 hover:border-purple-100 dark:text-purple-500 dark:hover:bg-purple-500/10' }}">
                        بالمستودع
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'delivered', 'page' => null]) }}"
                        class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                        {{ request('status') == 'delivered' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/20 dark:bg-emerald-600 dark:border-emerald-600' : 'bg-transparent text-emerald-600 border-transparent hover:bg-emerald-50 hover:border-emerald-100 dark:text-emerald-500 dark:hover:bg-emerald-500/10' }}">
                        مكتملة
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'returned', 'page' => null]) }}"
                        class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                        {{ request('status') == 'returned' ? 'bg-rose-500 text-white border-rose-500 shadow-md shadow-rose-500/20 dark:bg-rose-600 dark:border-rose-600' : 'bg-transparent text-rose-600 border-transparent hover:bg-rose-50 hover:border-rose-100 dark:text-rose-500 dark:hover:bg-rose-500/10' }}">
                        مرتجعة
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled', 'page' => null]) }}"
                        class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-sm font-bold transition-all border 
                        {{ request('status') == 'cancelled' ? 'bg-slate-500 text-white border-slate-500 shadow-md shadow-slate-500/20 dark:bg-slate-600 dark:border-slate-600' : 'bg-transparent text-slate-600 border-transparent hover:bg-slate-50 hover:border-slate-100 dark:text-slate-500 dark:hover:bg-slate-500/10' }}">
                        ملغاة
                    </a>
                </div>

                {{-- ================= عرض الديسكتوب (Data Table) ================= --}}
                <div class="hidden overflow-x-auto p-5 md:block min-h-[350px]">
                    <table class="w-full text-right whitespace-nowrap border-collapse">
                        <thead>
                            <tr class="text-[11px] font-black text-slate-500 uppercase tracking-widest bg-slate-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-slate-200 dark:border-boxdark">
                                <th class="px-6 py-4">رقم الإرسالية</th>
                                <th class="px-6 py-4">واردة من فرع</th>
                                <th class="px-6 py-4">السائق</th>
                                <th class="px-6 py-4 text-center">عدد الطرود</th>
                                <th class="px-6 py-4 text-center">الحالة</th>
                                <th class="px-6 py-4 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-boxdark-2">
                            @forelse($packages as $package)
                                <tr x-show="searchQuery === '' || '{{ $package->tracking_number }}'.includes(searchQuery) || '{{ $package->driver->name ?? '' }}'.includes(searchQuery)"
                                    class="relative transition-all hover:bg-slate-50/50 dark:hover:bg-boxdark-2/50 group">

                                    {{-- 1. رقم الإرسالية --}}
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3 items-center">
                                            <div class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border shadow-sm transition-colors text-slate-400 border-slate-100 dark:bg-boxdark-2 dark:text-bodydark dark:border-boxdark group-hover:text-primary">
                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-mono text-sm font-black tracking-tight text-slate-800 dark:text-white">{{ $package->tracking_number }}</span>
                                                <span class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $package->created_at->format('Y/m/d H:i') }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 2. الفرع المرسل --}}
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 items-center text-slate-800 dark:text-slate-200">
                                            <span class="material-symbols-outlined text-[16px] text-slate-400">store</span>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold truncate max-w-[150px]" title="{{ $package->senderBranch->name ?? 'غير محدد' }}">
                                                    {{ $package->senderBranch->name ?? 'غير محدد' }}
                                                </span>
                                                @if($package->sender_office_branch_id)
                                                    <span class="text-[9px] font-black text-primary mt-0.5">مكتب وكيل خارجي</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 3. السائق --}}
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 items-center text-slate-800 dark:text-slate-200">
                                            <span class="material-symbols-outlined text-[16px] text-slate-400">local_shipping</span>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold truncate max-w-[150px]" title="{{ $package->driver->name ?? 'سائق غير محدد' }}">
                                                    {{ $package->driver->name ?? 'سائق غير محدد' }}
                                                </span>
                                                <span class="text-[10px] font-bold text-slate-400 mt-0.5 dir-ltr text-right">{{ $package->driver->phone ?? '' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 4. عدد الطرود --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-black rounded-lg border bg-primary/10 text-primary border-primary/20">
                                            <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                                            {{ $package->shipments_count }} طرود
                                        </span>
                                    </td>

                                    {{-- 5. الحالة --}}
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400',
                                                'in_transit' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400',
                                                'received_at_branch' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400',
                                                'out_for_delivery' => 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400',
                                                'delivered' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400',
                                                'cancelled' => 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400',
                                                'returned' => 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400',
                                            ];
                                            $statusIcons = [
                                                'pending' => 'schedule',
                                                'in_transit' => 'local_shipping',
                                                'received_at_branch' => 'inventory_2',
                                                'out_for_delivery' => 'two_wheeler',
                                                'delivered' => 'task_alt',
                                                'cancelled' => 'block',
                                                'returned' => 'assignment_return',
                                            ];
                                            $statusNames = [
                                                'pending' => 'قيد التجهيز',
                                                'in_transit' => 'في الطريق',
                                                'received_at_branch' => 'بالمستودع',
                                                'out_for_delivery' => 'خرج للتوصيل',
                                                'delivered' => 'تم الاستلام',
                                                'cancelled' => 'ملغي',
                                                'returned' => 'مرتجع',
                                            ];
                                            
                                            $colorClass = $statusColors[$package->status] ?? 'bg-slate-50 text-slate-500 border-slate-200 dark:bg-boxdark-2 dark:text-slate-400 dark:border-boxdark';
                                            $icon = $statusIcons[$package->status] ?? 'info';
                                            $name = $statusNames[$package->status] ?? $package->status;
                                        @endphp
                                        <span class="inline-flex gap-1.5 items-center px-3 py-1.5 rounded-xl text-xs font-black border shadow-sm {{ $colorClass }}">
                                            <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
                                            {{ $name }}
                                        </span>
                                    </td>

                                    {{-- 6. الإجراءات --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex gap-2 justify-center items-center">
                                            <a href="{{ route('shipmentpackage.incoming.show', $package->id) }}"
                                                title="التفاصيل"
                                                class="inline-flex justify-center items-center w-9 h-9 bg-white rounded-xl border shadow-sm transition-all text-slate-400 border-slate-200 dark:bg-boxdark-2 dark:text-gray-500 hover:bg-primary/10 hover:text-primary hover:border-primary/30 dark:hover:bg-primary/20 dark:hover:text-primary active:scale-95 dark:border-boxdark">
                                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                                            </a>
                                            <a href="{{ route('receipt.generate', ['type' => 'ShipmentDetection', 'id' => $package->id]) }}" target="_blank"
                                                title="طباعة الكشف"
                                                class="inline-flex justify-center items-center w-9 h-9 bg-white rounded-xl border shadow-sm transition-all text-slate-400 border-slate-200 dark:bg-boxdark-2 dark:text-gray-500 hover:bg-slate-100 hover:text-slate-700 active:scale-95 dark:border-boxdark">
                                                <span class="material-symbols-outlined text-[20px]">print</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-20 text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-2xl border shadow-sm border-slate-100 bg-slate-50 dark:bg-boxdark-2 dark:border-boxdark">
                                                <span class="material-symbols-outlined text-[32px] text-slate-300 dark:text-slate-600">move_to_inbox</span>
                                            </div>
                                            <p class="text-sm font-black text-slate-600 dark:text-bodydark font-headline">لا توجد إرساليات واردة</p>
                                            <p class="mt-1 text-xs font-bold text-slate-400">لم نعثر على إرساليات تطابق بحثك أو الفلتر المختار.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ================= عرض الموبايل (بطاقات Cards) ================= --}}
                <div class="flex flex-col gap-4 p-4 md:hidden">
                    @forelse($packages as $package)
                        <a x-show="searchQuery === '' || '{{ $package->tracking_number }}'.includes(searchQuery) || '{{ $package->driver->name ?? '' }}'.includes(searchQuery)"
                            href="{{ route('shipmentpackage.incoming.show', $package->id) }}"
                            class="block bg-white dark:bg-boxdark-2 rounded-2xl border border-slate-100 dark:border-boxdark shadow-sm overflow-hidden relative active:scale-[0.98] transition-transform hover:border-primary/30">

                            {{-- شريط جانبي لوني يوضح الحالة --}}
                            @php
                                $borderColor = 'bg-slate-300';
                                if($package->status == 'pending') $borderColor = 'bg-amber-500';
                                elseif($package->status == 'in_transit') $borderColor = 'bg-blue-500';
                                elseif(in_array($package->status, ['received_at_branch', 'out_for_delivery'])) $borderColor = 'bg-purple-500';
                                elseif($package->status == 'delivered') $borderColor = 'bg-emerald-500';
                                elseif($package->status == 'returned') $borderColor = 'bg-rose-500';
                            @endphp
                            <div class="absolute right-0 top-0 bottom-0 w-1.5 {{ $borderColor }}"></div>

                            <div class="p-4 pr-5">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">رقم الإرسالية</span>
                                        <h3 class="font-mono text-sm font-black tracking-tight text-slate-800 dark:text-white">
                                            {{ $package->tracking_number }}
                                        </h3>
                                    </div>
                                    
                                    @php
                                        $colorClassMobile = $statusColors[$package->status] ?? 'bg-slate-50 text-slate-500 border-slate-200 dark:bg-boxdark-2 dark:text-slate-400 dark:border-boxdark';
                                        $nameMobile = $statusNames[$package->status] ?? $package->status;
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black border shadow-sm {{ $colorClassMobile }}">
                                        {{ $nameMobile }}
                                    </span>
                                </div>

                                <div class="flex gap-2 items-center p-3 mb-4 rounded-xl border shadow-sm bg-slate-50/50 border-slate-100 dark:bg-boxdark dark:border-boxdark-2">
                                    <span class="material-symbols-outlined text-[18px] text-primary">store</span>
                                    <div class="flex flex-col flex-1">
                                        <p class="text-[9px] font-bold text-slate-400 dark:text-gray-500 mb-0.5">واردة من فرع</p>
                                        <p class="text-xs font-black text-slate-800 dark:text-white truncate max-w-[200px]">
                                            {{ $package->senderBranch->name ?? 'غير محدد' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-boxdark">
                                    <div class="flex gap-1.5 items-center text-slate-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[16px] text-slate-400">local_shipping</span>
                                        <span class="text-[11px] font-bold truncate max-w-[120px]">{{ $package->driver->name ?? 'سائق غير محدد' }}</span>
                                    </div>

                                    <div class="flex gap-1.5 items-center px-3 py-1.5 text-white rounded-lg border shadow-sm bg-slate-800 dark:bg-black/30 dark:border-boxdark-2">
                                        <span class="material-symbols-outlined text-[14px] text-slate-300">inventory_2</span>
                                        <span class="text-[11px] font-black">{{ $package->shipments_count }} طرود</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col justify-center items-center py-16 text-center bg-white rounded-2xl border-2 border-dashed border-slate-100 dark:bg-boxdark-2 dark:border-boxdark">
                            <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full border shadow-sm text-slate-300 bg-slate-50 border-slate-100 dark:bg-boxdark dark:text-gray-600 dark:border-boxdark-2">
                                <span class="material-symbols-outlined text-[32px]">move_to_inbox</span>
                            </div>
                            <p class="text-sm font-black text-slate-600 dark:text-bodydark font-headline">لا توجد إرساليات واردة</p>
                            <p class="text-[10px] font-bold text-slate-400 mt-1">لم تقم باستلام أي إرساليات بعد.</p>
                        </div>
                    @endforelse
                </div>

                {{-- ================= الترقيم ================= --}}
                @if (method_exists($packages, 'hasPages') && $packages->hasPages())
                    <div class="p-5 border-t border-slate-100 dark:border-boxdark-2 bg-slate-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                        <div class="hidden md:block">
                            {{ $packages->links('vendor.pagination.tailwind') }}
                        </div>
                        <div class="md:hidden">
                            {{ $packages->links('vendor.pagination.mobile') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection