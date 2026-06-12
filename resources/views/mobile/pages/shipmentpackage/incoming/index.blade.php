@extends('mobile.layouts.app')

@section('title', 'الشحنات المستقبلة (الواردة)')

@section('content')
    <div x-data="{ searchQuery: '' }" class="flex flex-col gap-5 px-4 pt-4 pb-24 min-h-screen bg-slate-50/50">

        {{-- ================= الهيدر الاحترافي ================= --}}
        <div class="flex justify-between items-center mb-1">
            <div class="flex gap-3 items-center">
                {{-- زر الرجوع للقائمة السابقة --}}
                <a href="{{ route('mobile.shipmentpackage.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-xl font-black font-headline text-slate-800">الشحنات المستقبلة</h1>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">الإرساليات الواردة لفرعك</p>
                </div>
            </div>

            {{-- زر اختصار لاستلام إرسالية جديدة --}}
            <a href="{{ route('shipmentpackage.incoming.create') }}"
                class="w-12 h-12 bg-orange-400 text-white rounded-2xl flex items-center justify-center shadow-[0_8px_20px_rgba(251,146,60,0.4)] hover:bg-orange-500 active:scale-90 transition-all shrink-0">
                <span class="material-symbols-outlined text-[26px]">add_box</span>
            </a>
        </div>

        {{-- ================= شريط البحث السريع ================= --}}
        <div class="relative">
            <input type="text" x-model="searchQuery" placeholder="ابحث برقم الإرسالية، أو اسم السائق..."
                class="py-3 pr-4 pl-12 w-full text-sm font-bold bg-white rounded-2xl border-none shadow-sm transition-all outline-none focus:ring-2 focus:ring-primary/20 text-slate-700 placeholder-slate-400">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[20px]">search</span>
        </div>

        {{-- ================= شريط الفلترة حسب الحالة ================= --}}
        <div class="flex overflow-x-auto gap-2 pb-2 custom-scrollbar snap-x snap-mandatory">
            {{-- الكل --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}"
                class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border 
                {{ !request('status') ? 'bg-slate-800 text-white border-slate-800 shadow-md' : 'bg-white text-slate-500 border-slate-100 hover:bg-slate-50' }}">
                الكل
            </a>

            {{-- قيد التجهيز --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => null]) }}"
                class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-500/20' : 'bg-white text-amber-600 border-amber-100 hover:bg-amber-50' }}">
                قيد التجهيز
            </a>

            {{-- في الطريق --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => 'in_transit', 'page' => null]) }}"
                class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'in_transit' ? 'bg-blue-500 text-white border-blue-500 shadow-md shadow-blue-500/20' : 'bg-white text-blue-600 border-blue-100 hover:bg-blue-50' }}">
                في الطريق إلينا
            </a>

            {{-- بالمستودع --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => 'received_at_branch', 'page' => null]) }}"
                class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'received_at_branch' ? 'bg-purple-500 text-white border-purple-500 shadow-md shadow-purple-500/20' : 'bg-white text-purple-600 border-purple-100 hover:bg-purple-50' }}">
                بالمستودع
            </a>

            {{-- مكتملة --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => 'delivered', 'page' => null]) }}"
                class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'delivered' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/20' : 'bg-white text-emerald-600 border-emerald-100 hover:bg-emerald-50' }}">
                مكتملة
            </a>

            {{-- مرتجعة --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => 'returned', 'page' => null]) }}"
                class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'returned' ? 'bg-rose-500 text-white border-rose-500 shadow-md shadow-rose-500/20' : 'bg-white text-rose-600 border-rose-100 hover:bg-rose-50' }}">
                مرتجعة
            </a>

            {{-- ملغاة --}}
            <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled', 'page' => null]) }}"
                class="snap-start shrink-0 px-5 py-2.5 rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'cancelled' ? 'bg-slate-500 text-white border-slate-500 shadow-md shadow-slate-500/20' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50' }}">
                ملغاة
            </a>
        </div>

        {{-- ================= قائمة الإرساليات المستقبلة ================= --}}
        <div class="space-y-4">
            @forelse($packages as $package)
            @php
                    // إعدادات الألوان والأيقونات بناءً على حالة الإرسالية (Package Status)
                    $statusSettings = [
                        'pending'            => ['color' => 'bg-amber-50 text-amber-600 border-amber-200', 'icon' => 'schedule', 'name' => 'قيد التجهيز', 'sidebar' => 'bg-amber-400'],
                        'in_transit'         => ['color' => 'bg-blue-50 text-blue-600 border-blue-200', 'icon' => 'local_shipping', 'name' => 'في الطريق', 'sidebar' => 'bg-blue-400'],
                        'received_at_branch' => ['color' => 'bg-purple-50 text-purple-600 border-purple-200', 'icon' => 'inventory_2', 'name' => 'بالمستودع', 'sidebar' => 'bg-purple-400'],
                        'out_for_delivery'   => ['color' => 'bg-indigo-50 text-indigo-600 border-indigo-200', 'icon' => 'two_wheeler', 'name' => 'خرج للتوصيل', 'sidebar' => 'bg-indigo-400'],
                        'delivered'          => ['color' => 'bg-emerald-50 text-emerald-600 border-emerald-200', 'icon' => 'task_alt', 'name' => 'مكتملة', 'sidebar' => 'bg-emerald-400'],
                        'returned'           => ['color' => 'bg-rose-50 text-rose-600 border-rose-200', 'icon' => 'assignment_return', 'name' => 'مرتجعة', 'sidebar' => 'bg-rose-400'],
                        'cancelled'          => ['color' => 'bg-slate-50 text-slate-600 border-slate-200', 'icon' => 'block', 'name' => 'ملغاة', 'sidebar' => 'bg-slate-400'],
                    ];

                    $currentStatus = $statusSettings[$package->status] ?? $statusSettings['pending'];
                    
                    $colorClass   = $currentStatus['color'];
                    $icon         = $currentStatus['icon'];
                    $name         = $currentStatus['name'];
                    $sideBarClass = $currentStatus['sidebar'];
                @endphp
                <a x-show="searchQuery === '' || '{{ $package->id }}'.includes(searchQuery) || '{{ $package->driver->name ?? '' }}'.includes(searchQuery)"
                    href="{{ route('shipmentpackage.incoming.show', $package->id) }}"
                    class="block bg-white rounded-[1.5rem] border border-slate-200/60 shadow-sm overflow-hidden relative active:scale-[0.98] transition-transform">
                    

                    {{-- شريط جانبي لوني يوضح الحالة --}}
                    <div class="absolute right-0 top-0 bottom-0 w-1.5 {{ $sideBarClass }}"></div>

                    <div class="p-4 pr-5">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1 block">رقم الإرسالية</span>
                                <h3 class="font-mono text-sm font-black text-slate-800">{{ $package->id }}</h3>
                            </div>

                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-bold text-[10px] shadow-sm {{ $colorClass }}">
                                <span class="material-symbols-outlined text-[14px]">{{ $icon }}</span>
                                {{ $name }}
                            </div>
                        </div>

                        <div class="flex gap-2 items-center p-2.5 mb-3 rounded-xl border bg-slate-50 border-slate-100">
                            <span class="material-symbols-outlined text-[18px] text-primary">store</span>
                            <div class="flex-1">
                                <p class="text-[9px] font-bold text-slate-400 mb-0.5">واردة من</p>
                                <p class="text-xs font-bold text-slate-700 truncate max-w-[200px]">
                                    @if($package->sender_office_branch_id && $package->senderOfficeBranch)
                                        <span class="text-primary">{{ $package->senderOfficeBranch->office->name ?? 'مكتب خارجي' }}</span>
                                        -
                                    @endif
                                    {{ $package->sender_entity->name ?? 'غير محدد' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                            <div class="flex gap-1.5 items-center text-slate-500">
                                <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                                <span class="text-[11px] font-bold truncate max-w-[120px]">{{ $package->driver->name ?? 'سائق غير محدد' }}</span>
                            </div>

                            <div class="flex gap-1 items-center px-2.5 py-1 text-white rounded-lg shadow-sm bg-slate-800">
                                <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                <span class="text-[10px] font-black">{{ $package->shipments_count }} طرود</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="py-16 flex flex-col items-center justify-center bg-white rounded-[2rem] border-2 border-dashed border-slate-200">
                    <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full border shadow-sm bg-slate-50 text-slate-300 border-slate-100">
                        <span class="text-4xl material-symbols-outlined">move_to_inbox</span>
                    </div>
                    <p class="text-sm font-black text-slate-600 font-headline">لا توجد إرساليات واردة</p>
                    <p class="text-[10px] font-bold text-slate-400 mt-1">لم نعثر على إرساليات تطابق بحثك حالياً.</p>
                </div>
            @endforelse
        </div>

        {{-- الترقيم --}}
        @if(method_exists($packages, 'hasPages') && $packages->hasPages())
            <div class="mt-6">
                {{ $packages->links('vendor.pagination.mobile') }}
            </div>
        @endif

    </div>
@endsection