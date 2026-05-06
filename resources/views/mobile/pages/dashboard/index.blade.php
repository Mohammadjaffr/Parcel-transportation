@extends('mobile.layouts.app')

@section('title', 'الرئيسية - الداشبورد')

@section('content')
    <div class="flex flex-col gap-6 px-4 pt-4 pb-24 min-h-screen bg-slate-50/50">

        {{-- ================= 1. الترحيب ومعلومات الفرع ================= --}}
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[2rem] p-6 shadow-lg relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-bold mb-1">  {{ now()->translatedFormat('l، d F Y') }}</p>
                    <h1 class="text-2xl font-headline font-black text-white mb-1">أهلاً، {{ auth()->user()->name ?? 'محمد' }}</h1>
                    <div class="flex items-center gap-1.5 text-slate-300 text-[10px] font-bold bg-white/10 w-max px-2 py-1 rounded-lg backdrop-blur-sm mt-2">
                        <span class="material-symbols-outlined text-[14px]">storefront</span>
                        {{ auth()->user()->branch->name ?? 'الفرع الرئيسي' }}
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center backdrop-blur-md shadow-inner">
                    <span class="material-symbols-outlined text-white text-[24px]">person</span>
                </div>
            </div>
        </div>

        {{-- ================= 2. إحصائيات العمليات مع فلتر الوقت ================= --}}
        <div>
            <div class="flex justify-between items-center mb-3 px-1">
                <h2 class="text-sm font-black text-slate-800 flex items-center gap-1">
                    مؤشرات الأداء
                    <span class="text-[10px] text-slate-400 font-normal">({{ $periodName }})</span>
                </h2>
            </div>

            {{-- شريط الفلترة الزمني (أفقي قابل للتمرير) --}}
            <div class="flex overflow-x-auto gap-2 pb-2 mb-2 custom-scrollbar snap-x">
                <a href="{{ request()->fullUrlWithQuery(['period' => 'today']) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $period == 'today' ? 'bg-slate-800 text-white border-slate-800 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    اليوم
                </a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'this_week']) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $period == 'this_week' ? 'bg-slate-800 text-white border-slate-800 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    هذا الأسبوع
                </a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'this_month']) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $period == 'this_month' ? 'bg-slate-800 text-white border-slate-800 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    هذا الشهر
                </a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'last_month']) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $period == 'last_month' ? 'bg-slate-800 text-white border-slate-800 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    الشهر الماضي
                </a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'all']) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $period == 'all' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    الكل
                </a>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-2">
                {{-- بالمستودع --}}
                <div class="bg-white rounded-[1.5rem] p-5 border border-slate-100 shadow-sm flex flex-col gap-1 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-amber-50 rounded-full transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-1.5 text-amber-500 mb-3">
                            <span class="material-symbols-outlined text-[18px]">storefront</span>
                            <span class="text-xs font-bold text-slate-500">بالمستودع للتوزيع</span>
                        </div>
                        <span class="text-3xl font-headline font-black text-slate-800">{{ number_format($stats['pending']) }}</span>
                    </div>
                </div>
                
                {{-- مع المندوب --}}
                <div class="bg-white rounded-[1.5rem] p-5 border border-slate-100 shadow-sm flex flex-col gap-1 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-blue-50 rounded-full transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-1.5 text-blue-500 mb-3">
                            <span class="material-symbols-outlined text-[18px]">two_wheeler</span>
                            <span class="text-xs font-bold text-slate-500">في الطريق </span>
                        </div>
                        <span class="text-3xl font-headline font-black text-slate-800">{{ number_format($stats['with_driver']) }}</span>
                    </div>
                </div>
                
                {{-- تم التسليم --}}
                <div class="bg-white rounded-[1.5rem] p-5 border border-slate-100 shadow-sm flex flex-col gap-1 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-emerald-50 rounded-full transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-1.5 text-emerald-500 mb-3">
                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                            <span class="text-xs font-bold text-slate-500">تم التسليم بنجاح</span>
                        </div>
                        <span class="text-3xl font-headline font-black text-slate-800">{{ number_format($stats['delivered']) }}</span>
                    </div>
                </div>
                
                {{-- المرتجعات --}}
                <div class="bg-rose-50 rounded-[1.5rem] p-5 border border-rose-100 flex flex-col gap-1 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-rose-100/50 rounded-full transition-transform group-hover:scale-150"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-1.5 text-rose-500 mb-3">
                            <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                            <span class="text-xs font-bold text-rose-600">مرتجعات معلقة</span>
                        </div>
                        <span class="text-3xl font-headline font-black text-rose-600">{{ number_format($stats['returned']) }}</span>
                    </div>
                </div>

                {{-- ... (البطاقات الأربع السابقة: بالمستودع، المندوب، التسليم، المرتجعات) ... --}}

                {{-- ================= تنبيه المديونيات (يأخذ عرض عمودين) ================= --}}
                <a href="{{ route('customers.index') }}" class="col-span-2 bg-rose-500 rounded-[1.5rem] p-5 shadow-[0_8px_20px_rgba(244,63,94,0.2)] flex items-center justify-between relative overflow-hidden group transition-transform active:scale-95 block mt-1">
                    {{-- تأثيرات الخلفية --}}
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full transition-transform group-hover:scale-150"></div>
                    <div class="absolute right-12 -top-6 w-16 h-16 bg-white/10 rounded-full"></div>
                    
                    <div class="relative z-10 flex items-center gap-4 text-white">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm shrink-0 border border-white/20">
                            <span class="material-symbols-outlined text-[26px]">money_off</span>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-rose-100 mb-0.5">عملاء لديهم مديونية (أجور شحن)</p>
                            <div class="flex items-end gap-2">
                                <span class="text-3xl font-headline font-black">{{ $customersWithDebtCount }}</span>
                                <span class="text-xs font-bold text-rose-200 mb-1.5">عميل يجب مطالبته</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative z-10 text-white/50 group-hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px] rtl:rotate-180">arrow_forward_ios</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- ================= 3. أحدث الطرود (Active Shipments) ================= --}}
        <div>
            <div class="flex justify-between items-center mb-3 px-1">
                <h2 class="text-sm font-black text-slate-800">آخر التحديثات في الفرع</h2>
                <a href="{{ route('shipment.outgoing.index') }}" class="text-[10px] text-primary hover:underline">عرض الكل</a>
            </div>
            
            <div class="space-y-3">
                @forelse($latestShipments as $shipment)
                    @php
                        // تحديد لون وأيقونة البطاقة بناءً على الحالة
                        $statusStyle = match($shipment->status) {
                            'pending', 'received_at_branch' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-500', 'border' => 'border-amber-200', 'icon' => 'inventory_2', 'label' => 'بالمستودع'],
                            'in_transit', 'out_for_delivery' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-500', 'border' => 'border-blue-200', 'icon' => 'local_shipping', 'label' => 'في الطريق'],
                            'delivered' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500', 'border' => 'border-emerald-200', 'icon' => 'done_all', 'label' => 'مكتمل'],
                            'returned' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-500', 'border' => 'border-rose-200', 'icon' => 'assignment_return', 'label' => 'مرتجع'],
                            'cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'border' => 'border-slate-200', 'icon' => 'cancel', 'label' => 'ملغي'],
                            default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-500', 'border' => 'border-gray-200', 'icon' => 'info', 'label' => 'غير محدد']
                        };
                    @endphp

                    <a href="{{ route('shipment.outgoing.show', $shipment->id) }}" class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center justify-between transition-transform active:scale-95 block">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 {{ $statusStyle['bg'] }} rounded-[1rem] flex items-center justify-center {{ $statusStyle['text'] }} shrink-0">
                                <span class="material-symbols-outlined text-[24px]">{{ $statusStyle['icon'] }}</span>
                            </div>
                            <div>
                                <p class="font-mono text-sm font-black text-slate-800">{{ $shipment->bond_number }}</p>
                                <p class="text-[10px] font-bold text-slate-400 mt-1">
                                    {{ $shipment->senderCustomer->name ?? 'عميل' }} 
                                    <span class="mx-1 text-slate-300">»</span> 
                                    {{ $shipment->receiverCustomer->name ?? 'مستلم' }}
                                </p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 {{ $statusStyle['bg'] }} {{ $statusStyle['text'] }} {{ $statusStyle['border'] }} border rounded-lg text-[9px] font-black shrink-0">
                            {{ $statusStyle['label'] }}
                        </span>
                    </a>
                @empty
                    <div class="py-8 flex flex-col items-center justify-center bg-white rounded-[1.5rem] border border-dashed border-slate-200 text-center">
                        <span class="mb-2 text-3xl material-symbols-outlined text-slate-300">inbox</span>
                        <p class="text-xs font-bold text-slate-400">لا توجد طرود حديثة في هذا الفرع.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection