@extends('mobile.layouts.app')

@section('title', 'الشحنات المرسلة')

@section('content')
    <div class="flex flex-col gap-5 px-4 pb-28 pt-4">

        {{-- الهيدر الاحترافي --}}
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-4">
                {{-- زر الرجوع للقائمة السابقة --}}
                <a href="{{ route('mobile.shipmentpackage.index') }}"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 active:scale-90 transition-all">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-xl font-black font-headline text-slate-800">الشحنات المرسلة</h1>
                </div>
            </div>

            {{-- زر اختصار لإضافة شحنة (إرسالية) جديدة --}}
            <a href="{{ route('shipmentpackage.outgoing.create') }}"
                class="w-12 h-12 bg-orange-400 text-white rounded-2xl flex items-center justify-center shadow-[0_8px_20px_rgba(251,146,60,0.4)] hover:bg-orange-500 active:scale-90 transition-all shrink-0">
                <span class="material-symbols-outlined text-[26px]">add_box</span>
            </a>
        </div>

        {{-- شريط البحث المطور --}}
        <div class="relative group">
            <span
                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary transition-colors">search</span>
            <input type="text" placeholder="ابحث برقم التتبع أو الوجهة..."
                class="w-full h-14 pr-12 pl-4 bg-white border-none rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-sm font-bold transition-all placeholder:text-slate-300">
        </div>
        {{-- شريط الفلترة حسب الحالة (Status Filters) --}}
        <div class="flex overflow-x-auto gap-2 pb-2 mt-2 custom-scrollbar snap-x snap-mandatory">
            <a href="{{ route('shipmentpackage.outgoing.index') }}"
                class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                {{ !request('status') ? 'bg-slate-800 text-white border-slate-800 shadow-[0_4px_12px_rgba(30,41,59,0.2)]' : 'bg-white text-slate-500 border-slate-100 hover:bg-slate-50' }}">
                الكل
            </a>
            
            <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'pending']) }}"
                class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-[0_4px_12px_rgba(245,158,11,0.2)]' : 'bg-white text-amber-600 border-amber-100 hover:bg-amber-50' }}">
                قيد التجهيز
            </a>

            <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'in_transit']) }}"
                class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'in_transit' ? 'bg-blue-500 text-white border-blue-500 shadow-[0_4px_12px_rgba(59,130,246,0.2)]' : 'bg-white text-blue-600 border-blue-100 hover:bg-blue-50' }}">
                في الطريق
            </a>

            <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'delivered']) }}"
                class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'delivered' ? 'bg-emerald-500 text-white border-emerald-500 shadow-[0_4px_12px_rgba(16,185,129,0.2)]' : 'bg-white text-emerald-600 border-emerald-100 hover:bg-emerald-50' }}">
                مكتملة
            </a>

            <a href="{{ route('shipmentpackage.outgoing.index', ['status' => 'returned']) }}"
                class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                {{ request('status') == 'returned' ? 'bg-rose-500 text-white border-rose-500 shadow-[0_4px_12px_rgba(244,63,94,0.2)]' : 'bg-white text-rose-600 border-rose-100 hover:bg-rose-50' }}">
                مرتجعة
            </a>
        </div>

        {{-- قائمة الإرساليات (Manifests) بالتصميم الجديد --}}
        <div class="flex flex-col gap-5">
            @forelse($packages as $package)
                <div x-data="{ openMenu: false }"
                    class="bg-white rounded-[24px] border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] overflow-visible transition-all duration-300 relative group">

                    {{-- شريط لوني علوي خفيف يعطي طابعاً مميزاً --}}
                    <div
                        class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-primary/80 to-amber-400/80 rounded-t-[24px] opacity-70">
                    </div>

                    {{-- ================= 1. الرأس (Header) ================= --}}
                    <div class="p-5 flex justify-between items-start">
                        <div class="flex gap-3 items-center">
                            {{-- أيقونة الإرسالية بشكل عصري --}}
                            <div
                                class="w-11 h-11 rounded-[14px] bg-slate-50 flex items-center justify-center border border-slate-100/80 group-hover:scale-105 transition-transform duration-300">
                                <span class="material-symbols-outlined text-slate-500 text-[22px]">local_shipping</span>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-sm font-black text-slate-900 font-headline tracking-tight">
                                    {{ $package->tracking_number }}
                                </h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">schedule</span>
                                    {{ $package->created_at->format('Y/m/d - H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            {{-- شارة الحالة (Pill Badge) بتصميم FinTech --}}
                            @php
                                $statusMap = [
                                    'pending' => ['label' => 'قيد التجهيز', 'class' => 'bg-amber-50 text-amber-600 ring-amber-500/20'],
                                    'in_transit' => ['label' => 'في الطريق', 'class' => 'bg-blue-50 text-blue-600 ring-blue-500/20'],
                                    'delivered' => ['label' => 'مكتملة', 'class' => 'bg-emerald-50 text-emerald-600 ring-emerald-500/20'],
                                    'returned' => ['label' => 'مرتجعة', 'class' => 'bg-rose-50 text-rose-600 ring-rose-500/20'],
                                ];
                                $currStatus = $statusMap[$package->status] ?? $statusMap['pending'];
                            @endphp
                            <span
                                class="px-2.5 py-1 rounded-full text-[10px] font-black ring-1 ring-inset {{ $currStatus['class'] }}">
                                {{ $currStatus['label'] }}
                            </span>

                            {{-- قائمة الثلاث نقاط (Kebab Menu) الاحترافية --}}
                            <div class="relative">
                                <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                    class="absolute top-full left-0 mt-1.5 w-48 bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100/50 z-50 overflow-hidden py-1.5">

                                    {{-- رابط عرض التفاصيل --}}
                                    <a href="{{ route('shipmentpackage.outgoing.show', $package->id) }}"
                                        class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        عرض التفاصيل
                                    </a>

                                    <a href="#"
                                        class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                        طباعة كشف الرحلة
                                    </a>

                                    @if($package->driver && $package->driver->phone)
                                        <div class="h-px bg-slate-100/80 my-1 mx-3"></div>
                                        {{-- التواصل مع السائق (واتساب) --}}
                                        <a href="https://wa.me/{{ ltrim($package->driver->phone, '+') }}" target="_blank"
                                            class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                            {{-- 💡 أيقونة واتساب باللون الأخضر الرسمي --}}
                                            <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                            </svg>
                                            إرسال لسائق
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= 2. الفاصل المقطّع (Ticket Divider) ================= --}}
                    <div class="relative flex items-center h-4 overflow-hidden">
                        <div
                            class="absolute -right-2 w-4 h-4 bg-slate-50/50 rounded-full border-l border-slate-200/60 shadow-inner">
                        </div>
                        <div class="w-full border-t-[1.5px] border-dashed border-slate-200/70"></div>
                        <div
                            class="absolute -left-2 w-4 h-4 bg-slate-50/50 rounded-full border-r border-slate-200/60 shadow-inner">
                        </div>
                    </div>

                    {{-- ================= 3. جسد البطاقة ================= --}}
                    <div class="p-5 pt-4 space-y-5">
                        <div class="flex items-start justify-between gap-4">

                            {{-- العمود الأيمن: خط السير (من المستودع للسائق) --}}
                            <div class="flex items-stretch gap-3 w-1/2">
                                <div class="flex flex-col items-center mt-1">
                                    <div class="w-2.5 h-2.5 rounded-full border-[2.5px] border-slate-300 bg-white z-10"></div>
                                    <div class="w-[1.5px] h-10 bg-slate-200 my-0.5"></div>
                                    <div
                                        class="w-2.5 h-2.5 rounded-full border-[2.5px] border-primary bg-white z-10 shadow-[0_0_8px_rgba(36,56,156,0.4)]">
                                    </div>
                                </div>

                                <div class="flex-1 flex flex-col justify-between space-y-4">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">فرع التجميع</p>
                                        <p class="text-xs font-bold text-slate-800 truncate max-w-[100px]">
                                            {{ $package->senderBranch->name ?? 'غير محدد' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p
                                            class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide flex items-center gap-1">
                                            مع السائق:</p>
                                        <p class="text-xs font-bold text-primary truncate max-w-[100px]">
                                            {{ $package->driver->name ?? 'غير محدد' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- العمود الأيسر: تفاصيل إضافية للإرسالية --}}
                            <div
                                class="w-1/2 bg-slate-50/70 rounded-xl p-3 border border-slate-100/80 flex flex-col gap-2.5 justify-center">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400 font-bold">بواسطة:</span>
                                    <span
                                        class="text-[10px] font-black text-slate-700 bg-white px-2 py-0.5 rounded-md border border-slate-100 shadow-sm truncate max-w-[70px]">
                                        {{ $package->creator->name ?? 'النظام' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mt-1 pt-2 border-t border-slate-200/50">
                                    <span class="text-[10px] text-slate-400 font-bold">رقم السائق:</span>
                                    <span class="text-[11px] font-black text-slate-600 dir-ltr text-right">
                                        {{ $package->driver->phone ?? '---' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ================= 4. كبسولة الفوتر (إحصائيات الإرسالية) ================= --}}
                        <div
                            class="bg-slate-800 rounded-[18px] p-3.5 flex justify-between items-center shadow-lg shadow-slate-900/10">

                            {{-- الحالة --}}
                            <div class="flex gap-2.5 items-center">
                                <div class="w-9 h-9 rounded-xl bg-slate-700 flex items-center justify-center text-slate-300">
                                    <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-300 mb-0.5">حالة الرحلة</p>
                                    <p class="text-[11px] font-bold text-white tracking-wide">
                                        {{ $currStatus['label'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- إجمالي الطرود (مساحة بارزة جداً) --}}
                            <div class="text-left pl-2">
                                <p class="text-[9px] font-bold text-slate-400 mb-0.5">إجمالي الطرود</p>
                                <p class="text-lg font-black text-amber-400 font-headline tracking-tight leading-none">
                                    {{ $package->shipments_count ?? ($package->shipments ? $package->shipments->count() : 0) }}
                                    <span class="text-[10px] font-bold text-slate-300">طرد</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State بتصميم أنيق ومريح --}}
                <div
                    class="flex flex-col items-center justify-center py-20 bg-white rounded-[24px] border-2 border-dashed border-slate-200/70 mt-4 shadow-sm">
                    <div class="relative mb-4">
                        <div class="absolute inset-0 bg-primary/20 blur-xl rounded-full"></div>
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-slate-50 to-slate-100 rounded-[18px] flex items-center justify-center border border-white shadow-sm relative z-10">
                            <span class="material-symbols-outlined text-[32px] text-slate-300">search_off</span>
                        </div>
                    </div>
                    <h3 class="text-sm font-black text-slate-700 font-headline">لا توجد إرساليات</h3>
                    <p class="text-[11px] font-bold text-slate-400 mt-1">لم نجد أي إرساليات مجمعة حتى الآن.</p>
                </div>
            @endforelse

            @if(method_exists($packages, 'hasPages') && $packages->hasPages())
                <div class="mt-8">
                    {{ $packages->links('vendor.pagination.mobile') }}
                </div>
            @endif
        </div>

        {{-- الترقيم المخصص --}}
        @if($packages->hasPages())
            <div class="mt-6 px-2">
                {{ $packages->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
@endsection