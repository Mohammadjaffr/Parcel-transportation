@extends('mobile.layouts.app')

@section('title', 'ملف الموظف | ' . $user->name)

@section('content')
    <div class="flex flex-col gap-6 px-4 pb-24 pt-4 min-h-screen bg-slate-50/50">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('users.index') }}"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 hover:text-primary active:scale-90 transition-all">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-xl font-black font-headline text-slate-800">ملف الموظف</h1>
                </div>
            </div>
            
            <div class="px-3 py-1.5 rounded-xl border text-[10px] font-black shadow-sm 
                {{ $user->is_banned ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200' }}">
                {{ $user->is_banned ? 'محظور' : 'حساب نشط' }}
            </div>
        </div>

        {{-- ================= بطاقة بيانات الموظف ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full -z-0"></div>
            
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-2xl font-black shadow-inner border border-primary/20 shrink-0">
                    @php
                        $words = explode(' ', $user->name);
                        echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') . (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                    @endphp
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-800 font-headline">{{ $user->name }}</h2>
                    <p class="text-xs text-slate-500 font-mono font-bold mt-1 dir-ltr text-right">{{ $user->phone }}</p>
                    
                    {{-- 💡 الصلاحية والفرع --}}
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-md {{ $user->type == 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $user->type == 'admin' ? 'مدير نظام' : 'موظف فرع' }}
                        </span>
                        
                        <span class="px-2 py-1 text-[10px] font-bold rounded-md bg-slate-100 text-slate-600 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">store</span>
                            {{ $user->branch->name ?? 'غير محدد' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= إحصائيات الإنتاجية ================= --}}
        <div class="flex items-end justify-between mt-4 mb-3">
            <h3 class="font-black text-sm text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">monitoring</span>
                إنتاجية الموظف
            </h3>
        </div>
        <div class="flex overflow-x-auto gap-2 pb-2 mb-1 custom-scrollbar snap-x snap-mandatory">
            <a href="{{ request()->fullUrlWithQuery(['period' => 'all']) }}"
                class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                {{ $period == 'all' ? 'bg-slate-800 text-white border-slate-800 shadow-[0_4px_12px_rgba(30,41,59,0.2)]' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                السجل الشامل
            </a>
            
            <a href="{{ request()->fullUrlWithQuery(['period' => 'today']) }}"
                class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                {{ $period == 'today' ? 'bg-primary text-white border-primary shadow-[0_4px_12px_rgba(var(--color-primary-rgb),0.2)]' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                اليوم
            </a>

            <a href="{{ request()->fullUrlWithQuery(['period' => 'week']) }}"
                class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                {{ $period == 'week' ? 'bg-primary text-white border-primary shadow-[0_4px_12px_rgba(var(--color-primary-rgb),0.2)]' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                هذا الأسبوع
            </a>

            <a href="{{ request()->fullUrlWithQuery(['period' => 'month']) }}"
                class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                {{ $period == 'month' ? 'bg-primary text-white border-primary shadow-[0_4px_12px_rgba(var(--color-primary-rgb),0.2)]' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                هذا الشهر
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- إحصائية الطرود --}}
            <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute -left-4 -bottom-4 text-slate-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-[80px]">package_2</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center mb-3 relative z-10">
                    <span class="material-symbols-outlined">inventory_2</span>
                </div>
                <p class="text-3xl font-black text-slate-800 font-headline relative z-10">{{ $shipmentsCount }}</p>
                <p class="text-[10px] font-bold text-slate-400 relative z-10">طرد تم إصداره</p>
            </div>

            {{-- إحصائية الإرساليات --}}
            <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute -left-4 -bottom-4 text-slate-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-[80px]">local_shipping</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center mb-3 relative z-10">
                    <span class="material-symbols-outlined">all_inbox</span>
                </div>
                <p class="text-3xl font-black text-slate-800 font-headline relative z-10">{{ $manifestsCount }}</p>
                <p class="text-[10px] font-bold text-slate-400 relative z-10">إرسالية مجمعة</p>
            </div>

            {{-- إحصائية العملاء (تمتد على العمودين) --}}
            <div class="col-span-2 bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center justify-between relative overflow-hidden group">
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">group_add</span>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800 font-headline leading-none">{{ $customersCount ?? 0 }}</p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1">عميل جديد تم تسجيله</p>
                    </div>
                </div>
                <div class="absolute -right-4 -top-4 text-slate-50 opacity-50 group-hover:scale-110 transition-transform duration-500">
                    <span class="material-symbols-outlined text-[100px]">group</span>
                </div>
            </div>
        </div>

        {{-- ================= أحدث النشاطات ================= --}}
        @if($recentManifests->isNotEmpty())
            <div class="mt-4">
                <h3 class="font-black text-sm text-slate-800 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[18px]">history</span>
                    آخر الإرساليات التي أنشأها
                </h3>

                <div class="space-y-3">
                    @foreach($recentManifests as $manifest)
                        <a href="{{ route('shipmentpackage.outgoing.show', $manifest->id) }}" class="flex items-center justify-between bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm hover:border-primary/30 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800 font-mono">{{ $manifest->tracking_number }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                                        {{ $manifest->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-left">
                                <span class="px-2 py-1 rounded-md text-[9px] font-black {{ $manifest->status == 'delivered' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-500' }}">
                                    {{ $manifest->status == 'delivered' ? 'مكتملة' : 'تحديث' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection