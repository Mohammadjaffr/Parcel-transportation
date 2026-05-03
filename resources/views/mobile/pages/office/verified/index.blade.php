@extends('mobile.layouts.app')

@section('title', 'المكاتب الموثوقة')

@section('content')
 

    <div x-data="{ searchQuery: '{{ addslashes(request('search', '')) }}' }" class="flex relative flex-col gap-6 pb-24 min-h-screen">

        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-2xl font-black font-headline text-slate-800">المكاتب الموثوقة</h1>
                <p class="text-xs font-semibold text-slate-400">إجمالي المكاتب الموثوقة: <span
                        class="font-bold text-primary">{{ $offices->total() }}</span></p>
            </div>
        </div>

            <form action="{{ route('app.index') }}" method="GET" class="px-2">
                <div class="relative group">
                    <button type="submit"
                        class="absolute right-4 top-1/2 z-10 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</button>
                    <input type="text" name="search" value="{{ request('search') }}" x-model="searchQuery" placeholder="ابحث باسم المكتب، رقم الهاتف أو العنوان..."
                        class="pr-12 w-full h-14 text-sm bg-white rounded-[1.25rem] border-none ring-1 shadow-sm transition-all outline-none ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline text-slate-700">
                </div>
            </form>

        <div class="px-2 space-y-4">
            @forelse($offices as $office)
                @php
                    $searchData = collect([$office->name]);
                    foreach($office->branches as $branch) {
                        $searchData->push($branch->name);
                        $searchData->push($branch->phone);
                        $searchData->push($branch->city);
                    }
                    $searchString = strtolower($searchData->filter()->implode(' '));
                @endphp
                <div x-data="{ expanded: false, isSending: false, searchString: '{{ addslashes($searchString) }}' }"
                    x-show="searchQuery === '' || searchString.includes(searchQuery.toLowerCase())"
                    class="bg-white rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden transition-all duration-300 relative">

                    <div class="relative p-5">
                        <div class="flex gap-4 items-center mt-2">

                            <div @click="expanded = !expanded" class="cursor-pointer">
                                @if($office->logo)
                                    <div
                                        class="overflow-hidden relative w-14 h-14 bg-white rounded-2xl border shadow-inner shrink-0 border-slate-100">
                                        <img src="{{ asset('storage/' . $office->logo) }}" alt="شعار {{ $office->name }}"
                                            class="object-cover w-full h-full">
                                    </div>
                                @else
                                    <div
                                        class="flex justify-center items-center w-14 h-14 rounded-2xl border shadow-inner text-primary bg-primary/10 border-primary/20 shrink-0">
                                        <span class="text-2xl material-symbols-outlined"
                                            style="font-variation-settings: 'FILL' 1;">verified</span>
                                    </div>
                                @endif
                            </div>

                            <div @click="expanded = !expanded" class="flex-1 min-w-0 cursor-pointer">
                                <div class="flex gap-2 items-center mb-1">
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
                                            class="flex gap-1.5 items-center px-4 h-10 text-xs font-bold text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/20 active:scale-95 disabled:opacity-50">
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
                        class="px-5 pt-3 pb-5 space-y-3 border-t bg-slate-50/50 border-slate-50">
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-primary"></span>
                            الفروع المتاحة للمكتب
                        </div>

                        @forelse($office->branches as $branch)
                            <div
                                class="flex justify-between items-center p-3 bg-white rounded-2xl border shadow-sm border-slate-100">
                                <div class="flex gap-3 items-center">
                                    <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary/5 text-primary">
                                        <span class="text-sm material-symbols-outlined">location_on</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-700 font-headline">{{ $branch->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $branch->city }}</div>
                                    </div>
                                </div>

                                @if($office->connection_status == 'accepted')
                                    <a href="tel:{{ $branch->phone }}"
                                        class="flex justify-center items-center w-8 h-8 text-emerald-600 bg-emerald-50 rounded-full transition-transform active:scale-90">
                                        <span class="text-sm material-symbols-outlined">call</span>
                                    </a>
                                @else
                                    <span class="text-sm material-symbols-outlined text-slate-200">lock</span>
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
                    <span class="mb-4 text-6xl material-symbols-outlined text-slate-200">verified</span>
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