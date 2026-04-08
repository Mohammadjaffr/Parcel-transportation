@extends('mobile.layouts.app')

@section('title', 'الطرود المرسلة')

@section('content')
<div x-data="{ searchQuery: '' }" class="flex flex-col gap-6 px-2 pb-24 relative min-h-screen">

    <div class="flex justify-between items-end mb-2">
        <div>
            <h1 class="text-2xl font-headline font-bold text-on-surface">الطرود المرسلة</h1>
            <p class="text-on-surface-variant text-sm mt-1">إدارة ومتابعة طرودك الصادرة</p>
        </div>
        <div class="bg-primary/10 px-3 py-1.5 rounded-xl border border-primary/20">
            <span class="text-xs font-bold text-primary">إجمالي: {{ $shipments->total() ?? 0 }}</span>
        </div>
    </div>

    <a href="#" class="flex items-center justify-center gap-2 w-full py-4 bg-primary text-white rounded-[1.25rem] font-bold text-sm shadow-lg shadow-primary/30 active:scale-95 transition-transform duration-200">
        <span class="material-symbols-outlined text-[20px]">add_box</span>
        إرسال طرد جديد
    </a>

    <div class="relative group">
        <span class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</span>
        <input type="text" x-model="searchQuery" placeholder="ابحث برقم السند، أو هاتف العميل..."
            class="pr-12 w-full h-14 text-sm bg-white rounded-[1.25rem] border-none ring-1 shadow-sm transition-all outline-none ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline text-slate-700">
    </div>

    <div class="space-y-4">
        @forelse($shipments as $shipment)
            <div x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery) || '{{ $shipment->receiverCustomer?->phone }}'.includes(searchQuery)"
                 class="bg-white rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.03)] overflow-hidden transition-all duration-300">
                
                <div class="flex justify-between items-center p-4 border-b border-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[16px]">tag</span>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold mb-0.5">رقم السند</p>
                            <h3 class="text-sm font-bold font-headline text-slate-800">{{ $shipment->bond_number }}</h3>
                        </div>
                    </div>

                    @if($shipment->status == 'pending')
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 border border-amber-100">
                            <span class="material-symbols-outlined text-[14px]">hourglass_empty</span>
                            <span class="text-[10px] font-bold">قيد الانتظار</span>
                        </div>
                    @elseif($shipment->status == 'in_transit')
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 border border-blue-100">
                            <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                            <span class="text-[10px] font-bold">في الطريق</span>
                        </div>
                    @elseif($shipment->status == 'delivered')
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                            <span class="text-[10px] font-bold">تم التسليم</span>
                        </div>
                    @else
                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-50 text-slate-600 border border-slate-200">
                            <span class="text-[10px] font-bold">ملغي / مرتجع</span>
                        </div>
                    @endif
                </div>

                <div class="p-4 bg-slate-50/30">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-slate-400 font-bold mb-1">الوجهة</span>
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-primary">store</span>
                                {{ $shipment->receiverBranch?->name ?? 'فرع غير محدد' }}
                            </span>
                        </div>
                        <div class="h-8 w-px bg-slate-200"></div>
                        <div class="flex flex-col text-left">
                            <span class="text-[10px] text-slate-400 font-bold mb-1">التاريخ</span>
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-1 justify-end">
                                <span class="material-symbols-outlined text-[14px] text-slate-400">calendar_today</span>
                                {{ $shipment->created_at->format('Y-m-d') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-white p-3 rounded-2xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-primary/5 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-[16px]">person</span>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400">المستلم</p>
                                <p class="text-xs font-bold text-slate-700 truncate w-24 sm:w-32">{{ $shipment->receiverCustomer?->name ?? 'عميل نقدي' }}</p>
                            </div>
                        </div>
                        
                        <div class="text-left">
                            <p class="text-[10px] text-slate-400">المبلغ الإجمالي</p>
                            <p class="text-sm font-black text-primary font-headline">{{ number_format($shipment->total_amount, 2) }} <span class="text-[9px]">ريال</span></p>
                        </div>
                    </div>
                </div>

                <div class="flex p-2 gap-2 bg-white border-t border-slate-50">
                    <a href="#" class="flex-1 py-2.5 flex items-center justify-center gap-2 bg-slate-50 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition-colors active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        التفاصيل
                    </a>
                    <a href="#" class="flex-1 py-2.5 flex items-center justify-center gap-2 bg-slate-50 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition-colors active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        طباعة السند
                    </a>
                </div>
            </div>
        @empty
            <div class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2">
                <div class="w-24 h-24 mb-6 rounded-full bg-primary/5 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-6xl">unarchive</span>
                </div>
                <h3 class="text-lg font-headline font-bold text-slate-800 mb-1">لا توجد طرود مرسلة</h3>
                <p class="text-sm text-slate-400 text-center px-6">لم تقم بإرسال أي طرود حتى الآن، اضغط على زر الإرسال للبدء.</p>
            </div>
        @endforelse

        @if(method_exists($shipments, 'hasPages') && $shipments->hasPages())
            <div class="mt-6">
                {{ $shipments->links('vendor.pagination.mobile') }}
            </div>
        @endif
    </div>
</div>
@endsection