@extends('mobile.layouts.app')

@section('title', 'تفاصيل المكتب: ' . $office->name)

@section('content')
    <div class="flex flex-col gap-5 px-4 pb-24 pt-4 min-h-screen bg-slate-50/50">

        {{-- ================= 1. الهيدر ================= --}}
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('offices.index') }}"
                class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 hover:text-primary active:scale-90 transition-all">
                <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
            </a>
            <div>
                <h1 class="text-xl font-black font-headline text-slate-800">{{ $office->name }}</h1>
                <p class="text-xs font-bold text-slate-400 mt-0.5">مكتب خارجي (غير موثوق)</p>
            </div>
        </div>

        {{-- ================= 2. بطاقة إحصائيات سريعة ================= --}}
        <div class="grid grid-cols-2 gap-3">
            <div
                class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <p class="text-[10px] font-bold text-slate-400">إجمالي الطرود المرسلة</p>
                <p class="text-lg font-black text-slate-800">{{ $shipments->total() }} <span
                        class="text-[10px] text-slate-400">طرد</span></p>
            </div>

            <div
                class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mb-2">
                    <span class="material-symbols-outlined">account_tree</span>
                </div>
                <p class="text-[10px] font-bold text-slate-400">الفروع المسجلة</p>
                <p class="text-lg font-black text-slate-800">{{ $office->branches->count() }} <span
                        class="text-[10px] text-slate-400">فرع</span></p>
            </div>
        </div>

        {{-- ================= 3. قائمة الطرود المرسلة إليه ================= --}}
        <div>
            <h3 class="font-black text-sm text-slate-800 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[18px]">history</span>
                سجل الطرود الموجهة للمكتب
            </h3>

            <div class="space-y-5">
                @forelse($shipments as $shipment)
                    <div
                        class="bg-white rounded-[24px] border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] overflow-visible transition-all duration-300 relative group">

                        {{-- شريط لوني علوي يوضح الحالة --}}
                        <div class="absolute top-0 inset-x-0 h-1 rounded-t-[24px] opacity-70
                        @if($shipment->status == 'delivered') bg-emerald-500 
                        @elseif($shipment->status == 'in_transit') bg-blue-500 
                        @elseif($shipment->status == 'returned') bg-rose-500 
                        @else bg-amber-500 @endif">
                        </div>

                        {{-- ================= 1. الرأس (Header) ================= --}}
                        <div class="p-5 flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div
                                    class="w-11 h-11 rounded-[14px] bg-slate-50 flex items-center justify-center border border-slate-100/80 group-hover:scale-105 transition-transform duration-300">
                                    <span class="material-symbols-outlined text-slate-500 text-[22px]">package_2</span>
                                </div>
                                <div class="flex flex-col">
                                    <h3 class="text-sm font-black text-slate-900 font-headline tracking-tight">
                                        {{ $shipment->bond_number }}</h3>
                                    <p class="text-[10px] font-bold text-slate-400 mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                                        {{ $shipment->created_at->format('Y/m/d - H:i') }}
                                    </p>
                                </div>
                            </div>

                            {{-- شارة الحالة الذكية --}}
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-50 text-amber-600 ring-amber-500/20',
                                    'in_transit' => 'bg-blue-50 text-blue-600 ring-blue-500/20',
                                    'delivered' => 'bg-emerald-50 text-emerald-600 ring-emerald-500/20',
                                    'returned' => 'bg-rose-50 text-rose-600 ring-rose-500/20',
                                ];
                                $statusLabels = ['pending' => 'قيد الانتظار', 'in_transit' => 'في الطريق', 'delivered' => 'تم التسليم', 'returned' => 'مرتجعة'];
                            @endphp
                            <span
                                class="px-2.5 py-1 rounded-full text-[9px] font-black ring-1 ring-inset {{ $statusClasses[$shipment->status] ?? 'bg-slate-50 text-slate-600' }}">
                                {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                            </span>
                        </div>

                        {{-- ================= 2. الفاصل المقطّع (Ticket Divider) ================= --}}
                        <div class="relative flex items-center h-4 overflow-hidden">
                            <div
                                class="absolute -right-2 w-4 h-4 bg-slate-50 rounded-full border-l border-slate-200/60 shadow-inner">
                            </div>
                            <div class="w-full border-t-[1.5px] border-dashed border-slate-200/70"></div>
                            <div
                                class="absolute -left-2 w-4 h-4 bg-slate-50 rounded-full border-r border-slate-200/60 shadow-inner">
                            </div>
                        </div>

                        {{-- ================= 3. جسد البطاقة (البيانات) ================= --}}
                        <div class="p-5 pt-4 space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                {{-- الجهة اليمين: خط السير المختصر --}}
                                <div class="flex items-stretch gap-3 w-1/2">
                                    <div class="flex flex-col items-center mt-1">
                                        <div class="w-2 h-2 rounded-full border-2 border-slate-300 bg-white"></div>
                                        <div class="w-[1px] h-8 bg-slate-200 my-0.5"></div>
                                        <div class="w-2 h-2 rounded-full border-2 border-primary bg-white"></div>
                                    </div>
                                    <div class="flex flex-col justify-between">
                                        <div>
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">المستلم
                                            </p>
                                            <p class="text-[11px] font-bold text-slate-800 truncate">
                                                {{ $shipment->receiverCustomer->name ?? 'غير محدد' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">الوجهة
                                            </p>
                                            <p class="text-[11px] font-bold text-primary truncate">
                                                {{ $shipment->receiverOfficeBranch->name ?? '---' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- الجهة اليسار: كبسولة تفاصيل المحتوى --}}
                                <div
                                    class="w-1/2 bg-slate-50/70 rounded-2xl p-3 border border-slate-100 flex flex-col justify-center gap-2">
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="font-bold text-slate-400 text-[9px]">الوزن:</span>
                                        <span class="font-black text-slate-700">{{ $shipment->weight ?? '0' }} كجم</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px]">
                                        <span class="font-bold text-slate-400 text-[9px]">النوع:</span>
                                        <span class="font-black text-slate-700">@if($shipment->package_type == 'carton') كرتون
                                        @elseif($shipment->package_type == 'bag') كيس @else أخرى @endif</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ================= 4. كبسولة الفوتر المظلمة (الأكشن المالي) ================= --}}
                            <div
                                class="bg-slate-800 rounded-[18px] p-3 flex justify-between items-center shadow-lg shadow-slate-900/10">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-slate-700 flex items-center justify-center text-amber-400">
                                        <span class="material-symbols-outlined text-[16px]">payments</span>
                                    </div>
                                    <div>
                                        <p class="text-[8px] font-black text-slate-400">الإجمالي</p>
                                        <p class="text-[11px] font-black text-white">
                                            {{ number_format($shipment->total_amount, 0) }} ريال</p>
                                    </div>
                                </div>

                                <a href="{{ route('shipment.show', $shipment->id) }}"
                                    class="h-8 px-4 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] font-black flex items-center gap-1.5 transition-all active:scale-95 border border-white/5">
                                    التفاصيل
                                    <span class="material-symbols-outlined text-[14px]">arrow_back_ios</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2">
                        <div class="w-20 h-20 rounded-full bg-slate-50 flex items-center justify-center text-slate-200 mb-4">
                            <span class="text-5xl material-symbols-outlined">inventory_2</span>
                        </div>
                        <p class="text-lg font-bold font-headline text-slate-400">لا توجد طرود مسجلة</p>
                        <p class="text-xs text-slate-300">لم يتم إرسال أي طرود لهذا المكتب بعد</p>
                    </div>
                @endforelse

                {{-- الترقيم المخصص --}}
                @if($shipments->hasPages())
                    <div class="mt-6 px-2 pb-6">
                        {{ $shipments->links('vendor.pagination.mobile') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection