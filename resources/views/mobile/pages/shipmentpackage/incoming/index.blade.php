@extends('mobile.layouts.app')

@section('title', 'الشحنات المستقبلة (الواردة)')

@section('content')
<div class="flex flex-col gap-6 px-4 pb-24 pt-4 min-h-screen bg-slate-50/50">

    {{-- ================= الهيدر الاحترافي ================= --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            {{-- زر الرجوع للقائمة السابقة --}}
            <a href="{{ route('mobile.shipmentpackage.index') }}"
                class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 active:scale-90 transition-all">
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

    {{-- ================= قائمة الإرساليات المستقبلة ================= --}}
    <div class="space-y-4">
        @forelse($packages as $package)
            <a href="{{ route('shipmentpackage.incoming.show', $package->id) }}" 
                class="block bg-white rounded-[1.5rem] border border-slate-200/60 shadow-sm overflow-hidden relative active:scale-[0.98] transition-transform">
                
                {{-- شريط جانبي لوني يوضح الحالة --}}
                <div class="absolute right-0 top-0 bottom-0 w-1.5 
                    {{ $package->status == 'delivered' ? 'bg-emerald-500' : 'bg-secondary' }}">
                </div>

                <div class="p-4 pr-5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-wider mb-1 block">رقم الإرسالية</span>
                            <h3 class="text-sm font-black text-slate-800 font-mono">{{ $package->tracking_number }}</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black 
                            {{ $package->status == 'delivered' ? 'bg-emerald-50 text-emerald-600' : 'bg-secondary/10 text-secondary' }}">
                            {{ $package->status == 'delivered' ? 'تم الاستلام' : 'قيد المعالجة' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mb-3 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">store</span>
                        <div class="flex-1">
                            <p class="text-[9px] font-bold text-slate-400">واردة من فرع</p>
                            <p class="text-xs font-bold text-slate-700 truncate">{{ $package->senderBranch->name ?? 'غير محدد' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                        <div class="flex items-center gap-1.5 text-slate-500">
                            <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                            <span class="text-[11px] font-bold truncate max-w-[100px]">{{ $package->driver->name ?? 'سائق غير محدد' }}</span>
                        </div>
                        
                        <div class="flex items-center gap-1 bg-slate-800 text-white px-2.5 py-1 rounded-lg">
                            <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                            <span class="text-[11px] font-black">{{ $package->shipments_count }} طرود</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="py-16 flex flex-col items-center justify-center bg-white rounded-[2rem] border-2 border-dashed border-slate-200">
                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-4xl">move_to_inbox</span>
                </div>
                <p class="text-sm font-bold text-slate-500 font-headline">لا توجد إرساليات واردة</p>
                <p class="text-[10px] font-bold text-slate-400 mt-1">لم تقم باستلام أي إرساليات بعد.</p>
            </div>
        @endforelse
    </div>

    {{-- الترقيم --}}
    @if($packages->hasPages())
        <div class="mt-4">
            {{ $packages->links('vendor.pagination.mobile') }}
        </div>
    @endif

</div>
@endsection