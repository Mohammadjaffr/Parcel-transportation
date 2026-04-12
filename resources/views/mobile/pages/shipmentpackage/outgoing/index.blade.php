@extends('mobile.layouts.app')

@section('title', 'الشحنات المرسلة')

@section('content')
    <div class="flex flex-col gap-5 px-4 pb-28 pt-4">

        {{-- الهيدر الاحترافي --}}
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-4">
                <a href="{{ route('outgoing.create') }}"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 active:scale-90 transition-all">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-xl font-black font-headline text-slate-800">الشحنات المرسلة</h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Outgoing Shipments Log</p>
                </div>
            </div>

            {{-- أيقونة تدل على الإرسال --}}
            <div class="w-12 h-12 bg-primary/5 rounded-2xl flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1">outbox</span>
            </div>
        </div>

        {{-- شريط البحث المطور --}}
        <div class="relative group">
            <span
                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary transition-colors">search</span>
            <input type="text" placeholder="ابحث برقم التتبع أو الوجهة..."
                class="w-full h-14 pr-12 pl-4 bg-white border-none rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.02)] ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 outline-none text-sm font-bold transition-all placeholder:text-slate-300">
        </div>

        {{-- قائمة الشحنات --}}
        <div class="flex flex-col gap-5">
            @forelse($packages as $package)
                <div
                    class="bg-white p-5 rounded-[2.5rem] border border-slate-50 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] relative overflow-hidden group active:scale-[0.98] transition-all">

                    {{-- بار علوي: رقم التتبع والحالة --}}
                    <div class="flex justify-between items-start mb-5">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">رقم التتبع</span>
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-base font-black text-slate-800 font-mono tracking-tight">{{ $package->tracking_number }}</span>
                                <button class="text-slate-300 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                </button>
                            </div>
                        </div>

                        {{-- الحالة الديناميكية --}}
                        @php
                            $statusMap = [
                                'pending' => ['label' => 'قيد التجهيز', 'class' => 'bg-amber-50 text-amber-600 border-amber-100', 'icon' => 'inventory'],
                                'in_transit' => ['label' => 'في الطريق', 'class' => 'bg-blue-50 text-blue-600 border-blue-100', 'icon' => 'local_shipping'],
                                'delivered' => ['label' => 'وصلت', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'icon' => 'check_circle'],
                                'returned' => ['label' => 'مرتجعة', 'class' => 'bg-rose-50 text-rose-600 border-rose-100', 'icon' => 'assignment_return'],
                            ];
                            $currStatus = $statusMap[$package->status] ?? $statusMap['pending'];
                        @endphp
                        <div
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border {{ $currStatus['class'] }} shadow-sm">
                            <span class="material-symbols-outlined text-[14px]">{{ $currStatus['icon'] }}</span>
                            <span class="text-[10px] font-black">{{ $currStatus['label'] }}</span>
                        </div>
                    </div>

                    {{-- مسار الرحلة (Vertical Timeline) --}}
                    <div class="relative pr-6 mb-5 space-y-4">
                        {{-- الخط المتصل --}}
                        <div
                            class="absolute right-[9px] top-2 bottom-2 w-[2px] bg-gradient-to-b from-slate-100 via-primary/20 to-slate-100 rounded-full">
                        </div>

                        {{-- فرع المصدر --}}
                        <div class="relative">
                            <div
                                class="absolute -right-[21px] top-1 w-3 h-3 bg-white border-2 border-slate-300 rounded-full z-10">
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400">من فرع</span>
                                <span
                                    class="text-xs font-black text-slate-700">{{ $package->senderBranch->name ?? 'غير محدد' }}</span>
                            </div>
                        </div>

                        {{-- فرع الوجهة --}}
                        <div class="relative">
                            <div
                                class="absolute -right-[21px] top-1 w-3 h-3 bg-primary border-2 border-white rounded-full z-10 shadow-[0_0_10px_rgba(36,56,156,0.3)]">
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[9px] font-bold text-slate-400">إلى فرع</span>
                                <span
                                    class="text-xs font-black text-primary">{{ $package->receiverBranch->name ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- بيانات السائق والموظف (New Updates) --}}
                    <div
                        class="flex items-center justify-between mb-5 p-3 bg-slate-50/80 rounded-2xl border border-slate-100/50">
                        {{-- السائق --}}
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center text-slate-500 shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">person_pin</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[8px] font-bold text-slate-400">السائق المسؤول</span>
                                <span
                                    class="text-[11px] font-black text-slate-700">{{ $package->driver->name ?? 'لم يتم التعيين' }}</span>
                            </div>
                        </div>

                        {{-- الموظف المنشئ --}}
                        <div class="flex flex-col items-end">
                            <span class="text-[8px] font-bold text-slate-400 uppercase">بواسطة</span>
                            <div class="flex items-center gap-1">
                                <span
                                    class="text-[10px] font-black text-slate-600">{{ $package->creator->name ?? 'النظام' }}</span>
                                <span class="material-symbols-outlined text-[14px] text-slate-400">verified_user</span>
                            </div>
                        </div>
                    </div>

                    {{-- Bento Stats --}}
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-primary/5 p-3 rounded-2xl flex flex-col items-center border border-primary/5">
                            <span class="text-[9px] font-bold text-primary/60">الطرود</span>
                            <span
                                class="text-sm font-black text-primary">{{ $package->parcels_count ?? ($package->parcels ? $package->parcels->count() : 0) }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-2xl flex flex-col items-center border border-slate-100">
                            <span class="text-[9px] font-bold text-slate-400">الوزن</span>
                            <span class="text-sm font-black text-slate-700">{{ number_format($package->total_weight, 1) }}
                                <small class="text-[8px]">كجم</small></span>
                        </div>
                        {{-- زر الانتقال للتفاصيل --}}
                        <a href="{{ route('mobile.shipmentpackage.show', $package->id) }}"
                            class="bg-slate-900 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-slate-200 active:scale-90 transition-all">
                            <span class="material-symbols-outlined text-[20px]">arrow_back_ios_new</span>
                        </a>
                    </div>

                    {{-- تزيين خلفي --}}
                    <div
                        class="absolute -top-12 -left-12 w-24 h-24 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors">
                    </div>
                </div>
            @empty
                {{-- حالة عدم وجود بيانات --}}
                <div class="py-24 flex flex-col items-center justify-center text-slate-300">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[40px] opacity-20">local_shipping</span>
                    </div>
                    <p class="font-bold text-sm">لا توجد شحنات مرسلة حتى الآن</p>
                    <a href="{{ route('outgoing.create') }}"
                        class="mt-4 text-primary font-bold text-xs underline underline-offset-4">أنشئ أول شحنة الآن</a>
                </div>
            @endforelse
        </div>

        {{-- الترقيم المخصص --}}
        @if($packages->hasPages())
            <div class="mt-6 px-2">
                {{ $packages->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>

    {{-- زر الإنشاء العائم (Floating Action Button) --}}
    
@endsection