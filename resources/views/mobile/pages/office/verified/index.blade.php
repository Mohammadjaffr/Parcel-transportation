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
                <div x-data="{ expanded: false }"
                    x-show="searchQuery === '' || '{{ $office->name }}'.includes(searchQuery) || '{{ $office->address }}'.includes(searchQuery) || '{{ $office->phone }}'.includes(searchQuery)"
                    class="bg-white rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden transition-all duration-300 relative">

                    <div @click="expanded = !expanded" class="p-5 relative cursor-pointer active:bg-slate-50 transition-colors">
                        <div class="flex gap-4 items-center mt-2">
                            
                            @if($office->logo)
                                <div class="relative w-14 h-14 rounded-2xl overflow-hidden shrink-0 border shadow-inner border-slate-100 bg-white">
                                    <img src="{{ asset('storage/' . $office->logo) }}" alt="شعار {{ $office->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="flex justify-center items-center w-14 h-14 text-primary bg-primary/10 rounded-2xl border shadow-inner border-primary/20 shrink-0">
                                    <span class="text-2xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified</span>
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-base font-bold truncate text-slate-800 font-headline">{{ $office->name }}</h3>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary transition-transform"
                                        :class="expanded ? 'scale-110' : ''">
                                        {{ $office->branches->count() }} فرع
                                    </span>
                                </div>

                                <p class="flex gap-1 items-center mt-1 text-xs text-slate-500">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">location_on</span>
                                    <span class="truncate">{{ $office->address ?: 'لم يتم تحديد العنوان' }}</span>
                                </p>
                            </div>

                            <div class="text-slate-300 transition-transform duration-300"
                                :class="expanded ? 'rotate-180 text-primary' : ''">
                                <span class="material-symbols-outlined">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <div x-show="expanded" x-collapse x-cloak
                        class="bg-slate-50/50 border-t border-slate-50 px-5 pb-5 pt-2 space-y-3">

                        <div
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-primary"></span>
                            قائمة الفروع المتاحة
                        </div>

                        @forelse($office->branches as $branch)
                            <div
                                class="flex items-center justify-between p-3 bg-white rounded-2xl border border-slate-100 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-primary/5 text-primary flex items-center justify-center">
                                        <span class="text-sm material-symbols-outlined">location_city</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-700 font-headline">{{ $branch->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $branch->city }}</div>
                                    </div>
                                </div>
                                <a href="tel:{{ $branch->phone }}"
                                    class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center active:scale-90 transition-transform">
                                    <span class="text-sm material-symbols-outlined">call</span>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-4 text-xs text-slate-400 font-medium italic">
                                لا توجد فروع مسجلة لهذا المكتب
                            </div>
                        @endforelse

                        <div class="mt-4 pt-2 border-t border-slate-100">
                            <a href="#"
                                class="w-full flex justify-center items-center gap-2 h-12 text-sm font-bold rounded-xl bg-primary/5 text-primary active:scale-95 transition-all">
                                <span class="text-lg material-symbols-outlined">visibility</span>
                                عرض تفاصيل المكتب
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 shadow-sm mx-2">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-primary/5 text-primary">
                        <span class="text-6xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لا توجد مكاتب موثوقة حالياً</p>
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