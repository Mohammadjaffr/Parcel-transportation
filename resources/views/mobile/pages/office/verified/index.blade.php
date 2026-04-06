@extends('mobile.layouts.app')

@section('title', 'المكاتب الموثوقة')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div x-data="{ searchQuery: '' }" class="flex flex-col gap-6 relative min-h-screen pb-24">

        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-2xl font-black font-headline text-slate-800">المكاتب الموثوقة</h1>
                <p class="text-xs font-semibold text-slate-400">إجمالي المكاتب الموثوقة: <span
                        class="font-bold text-primary">{{ $offices->total() }}</span></p>
            </div>
        </div>

        <div class="px-2">
            <div class="relative group">
                <span
                    class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</span>
                <input type="text" x-model="searchQuery" placeholder="ابحث باسم المكتب، رقم الهاتف أو العنوان..."
                    class="pr-12 w-full h-14 text-sm bg-white rounded-[1.25rem] border-none ring-1 shadow-sm transition-all outline-none ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline text-slate-700">
            </div>
        </div>

        <div class="px-2 space-y-4">
            @forelse($offices as $office)
                <div x-data="{ expanded: false, isSending: false }"
                    class="bg-white rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden transition-all duration-300 relative">

                    <div class="p-5 relative">
                        <div class="flex gap-4 items-center mt-2">

                            <div @click="expanded = !expanded" class="cursor-pointer">
                                @if($office->logo)
                                    <div
                                        class="relative w-14 h-14 rounded-2xl overflow-hidden shrink-0 border shadow-inner border-slate-100 bg-white">
                                        <img src="{{ asset('storage/' . $office->logo) }}" alt="شعار {{ $office->name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div
                                        class="flex justify-center items-center w-14 h-14 text-primary bg-primary/10 rounded-2xl border shadow-inner border-primary/20 shrink-0">
                                        <span class="text-2xl material-symbols-outlined"
                                            style="font-variation-settings: 'FILL' 1;">verified</span>
                                    </div>
                                @endif
                            </div>

                            <div @click="expanded = !expanded" class="flex-1 min-w-0 cursor-pointer">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-base font-bold truncate text-slate-800 font-headline">{{ $office->name }}
                                    </h3>
                                </div>
                                <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">store</span>
                                    {{ $office->branches->count() }} فرع متاح
                                </p>
                            </div>

                            <div class="shrink-0">
                                @if($office->connection_status == 'none')
                                    <form action="{{ route('offices.connect', $office->id) }}" method="POST"
                                        @submit="isSending = true">
                                        @csrf
                                        <button type="submit" :disabled="isSending"
                                            class="flex items-center gap-1.5 px-4 h-10 rounded-xl bg-primary text-white text-xs font-bold shadow-lg shadow-primary/20 active:scale-95 transition-all disabled:opacity-50">
                                            <span x-show="!isSending"
                                                class="material-symbols-outlined text-[18px]">person_add</span>
                                            <span x-show="isSending"
                                                class="animate-spin material-symbols-outlined text-[18px]">progress_activity</span>
                                            <span x-text="isSending ? 'جاري..' : 'ربط'"></span>
                                        </button>
                                    </form>

                                @elseif($office->connection_status == 'pending')
                                    <div
                                        class="flex items-center gap-1.5 px-3 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 text-[11px] font-bold">
                                        <span class="material-symbols-outlined text-[18px] animate-pulse">hourglass_empty</span>
                                        قيد الانتظار
                                    </div>

                                @elseif($office->connection_status == 'accepted')
                                    <div
                                        class="flex items-center gap-1.5 px-3 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 text-[11px] font-bold">
                                        <span class="material-symbols-outlined text-[18px]"
                                            style="font-variation-settings: 'FILL' 1;">verified</span>
                                        متصل
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div x-show="expanded" x-collapse x-cloak
                        class="bg-slate-50/50 border-t border-slate-50 px-5 pb-5 pt-3 space-y-3">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-primary"></span>
                            الفروع المتاحة للمكتب
                        </div>

                        @forelse($office->branches as $branch)
                            <div
                                class="flex items-center justify-between p-3 bg-white rounded-2xl border border-slate-100 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary/5 text-primary flex items-center justify-center">
                                        <span class="text-sm material-symbols-outlined">location_on</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-700 font-headline">{{ $branch->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $branch->city }}</div>
                                    </div>
                                </div>

                                @if($office->connection_status == 'accepted')
                                    <a href="tel:{{ $branch->phone }}"
                                        class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center active:scale-90 transition-transform">
                                        <span class="text-sm material-symbols-outlined">call</span>
                                    </a>
                                @else
                                    <span class="material-symbols-outlined text-slate-200 text-sm">lock</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-center text-[10px] text-slate-400 italic py-2">لا توجد فروع مسجلة</p>
                        @endforelse
                    </div>
                </div>
            @empty
                <div
                    class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100">
                    <span class="text-6xl material-symbols-outlined text-slate-200 mb-4">verified</span>
                    <p class="text-lg font-bold text-slate-400">لا توجد مكاتب موثوقة حالياً</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination info --}}
        @if ($offices->hasPages())
            <div class="px-2 pb-6">
                {{ $offices->links('vendor.pagination.mobile') }}
            </div>
        @endif

    </div>
@endsection