@extends('layouts.app')

@section('title', 'تفاصيل الرحلة #' . $trip->id)
@section('Breadcrumb', 'تفاصيل الرحلة')

@section('content')

<div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl"
    x-data="tripDetailsForm(@js($trip->passengers->map(function($p) {
        return [
            'id' => $p->id,
            'passenger_number' => $p->passenger_number,
            'count' => $p->count ?? 1,
            'destination' => $p->destination  ?? ' '
        ];
    }) ?? []))">

    {{-- Header --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('trips.index') }}" class="flex justify-center items-center w-12 h-12 bg-white dark:bg-boxdark rounded-2xl border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary hover:border-primary/30 active:scale-95">
                    <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
                </a>
                <div>
                    <h1 class="text-2xl font-black font-headline text-slate-800 dark:text-white flex items-center gap-2">
                        تفاصيل الرحلة
                        <span class="text-primary font-mono bg-primary/10 px-2 py-0.5 rounded-lg text-lg">#{{ $trip->id }}</span>
                    </h1>
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mt-1">تاريخ الإنشاء: {{ $trip->created_at->format('Y-m-d h:i A') }}</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('receipt.generate', ['type' => 'trip', 'id' => $trip->uuid]) }}" target="_blank" class="flex items-center gap-2 px-5 h-12 bg-white dark:bg-boxdark text-emerald-600 dark:text-emerald-400 rounded-2xl border border-emerald-100 dark:border-emerald-500/20 shadow-sm transition-all hover:bg-emerald-50 dark:hover:bg-emerald-500/10 active:scale-95 font-bold text-sm">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                    طباعة الكشف
                </a>
                <a href="{{ route('trips.edit', $trip->id) }}" class="flex items-center gap-2 px-5 h-12 bg-white dark:bg-boxdark text-amber-600 dark:text-amber-400 rounded-2xl border border-amber-100 dark:border-amber-500/20 shadow-sm transition-all hover:bg-amber-50 dark:hover:bg-amber-500/10 active:scale-95 font-bold text-sm">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                    تعديل الرحلة
                </a>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Right Column (Driver & Stats) --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- Driver Card --}}
                <div class="bg-white dark:bg-boxdark rounded-[2rem] p-6 border border-gray-100 dark:border-boxdark-2 shadow-sm">
                    <h3 class="text-sm font-black text-gray-500 dark:text-gray-400 mb-4 flex items-center gap-2 font-headline">
                        <span class="material-symbols-outlined text-[20px]">badge</span>
                        السائق المسؤول
                    </h3>
                    
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center border border-emerald-100 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400 shrink-0">
                            <span class="material-symbols-outlined text-[32px]">local_taxi</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-black text-slate-800 dark:text-white truncate">
                                {{ $trip->driver->name ?? 'سائق غير معين' }}
                            </h3>
                            @if($trip->driver?->phone)
                                <a href="tel:{{ $trip->driver->phone }}" class="inline-block mt-1.5" style="direction: ltr;">
                                    <div class="bg-primary/5 px-2.5 py-1 rounded-xl border border-primary/10 transition-colors hover:bg-primary/10">
                                        <x-phone-number :value="$trip->driver->phone" class="text-sm text-primary !justify-start" />
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex flex-col justify-center items-center gap-2 text-center">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[24px]">group_work</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-bold block mb-0.5">مجموعات الركاب</span>
                            <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $trip->passengers->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="p-5 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex flex-col justify-center items-center gap-2 text-center">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[24px]">person</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-bold block mb-0.5">إجمالي الأفراد</span>
                            <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $trip->passengers->sum('count') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Left Column (Passengers List) --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-boxdark rounded-[2rem] p-6 border border-gray-100 dark:border-boxdark-2 shadow-sm h-full flex flex-col">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="font-black text-slate-800 dark:text-white font-headline text-lg flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">group</span>
                                ركاب الرحلة المقيدين
                            </h3>
                            <p class="text-xs text-gray-500 mt-1 font-bold">قائمة الركاب الذين تم تسييرهم في هذه الرحلة.</p>
                        </div>

                        <div class="relative w-full md:w-64 shrink-0">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[20px]">search</span>
                            <input type="text" x-model="searchQuery" placeholder="بحث عن راكب..."
                                class="pr-11 pl-4 w-full h-12 text-sm bg-slate-50 dark:bg-boxdark-2 text-slate-800 dark:text-white rounded-xl border outline-none border-slate-200 dark:border-boxdark-2 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1 content-start auto-rows-max overflow-y-auto custom-scrollbar pr-2" style="max-height: 500px;">
                        @forelse($trip->passengers as $passenger)
                            <div class="bg-slate-50/50 dark:bg-boxdark-2/50 p-4 rounded-2xl border border-slate-100 dark:border-boxdark-2 flex items-center justify-between gap-4 hover:border-primary/30 transition-colors"
                                x-show="searchQuery === '' || String(@js($passenger->passenger_number)).includes(searchQuery)">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <div class="flex flex-col justify-center items-center w-12 h-12 rounded-xl bg-white dark:bg-boxdark text-slate-600 dark:text-gray-300 shrink-0 border border-slate-200 dark:border-boxdark-2 shadow-sm">
                                        <span class="text-lg font-black leading-none">{{ $passenger->count ?? 1 }}</span>
                                        <span class="text-[9px] font-bold mt-0.5">ركاب</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="block font-headline text-base font-black text-slate-800 dark:text-white tracking-tight truncate mb-1" style="direction: ltr; text-align: right;">
                                            <x-phone-number :value="$passenger->passenger_number" class="text-base text-slate-800 dark:text-white !justify-start" />
                                        </span>
                                        <div class="flex gap-1.5 items-center">
                                            <span class="material-symbols-outlined text-[14px] text-primary">flag</span>
                                            <span class="text-xs font-bold text-slate-500 dark:text-gray-400 truncate">{{ $passenger->destination ?? ' ' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0 border border-primary/20">
                                    <span class="material-symbols-outlined text-[18px] font-bold">done_all</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-1 md:col-span-2 py-12 text-center flex flex-col items-center justify-center text-slate-400 bg-slate-50 dark:bg-boxdark-2/30 rounded-2xl border border-dashed border-slate-200 dark:border-boxdark-2">
                                <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-white dark:bg-boxdark shadow-sm">
                                    <span class="material-symbols-outlined text-[32px] opacity-30">group_off</span>
                                </div>
                                <p class="text-sm font-bold text-slate-500 dark:text-gray-400">لا يوجد ركاب مضافين لهذه الرحلة حتى الآن</p>
                            </div>
                        @endforelse

                        @if($trip->passengers->isNotEmpty())
                            <div x-show="countVisible === 0" x-cloak class="col-span-1 md:col-span-2 py-12 text-center flex flex-col items-center justify-center text-slate-400 bg-slate-50 dark:bg-boxdark-2/30 rounded-2xl border border-dashed border-slate-200 dark:border-boxdark-2">
                                <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-white dark:bg-boxdark shadow-sm">
                                    <span class="material-symbols-outlined text-[32px] opacity-30">person_search</span>
                                </div>
                                <p class="text-sm font-bold text-slate-500 dark:text-gray-400">لم يتم العثور على ركاب يطابقون بحثك</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function tripDetailsForm() {
        return {
            searchQuery: '',
            get countVisible() {
                if (this.searchQuery === '') return {{ $trip->passengers->count() }};
                let count = 0;
                @foreach($trip->passengers as $passenger)
                    if (String(@js($passenger->passenger_number)).includes(this.searchQuery)) count++;
                @endforeach
                return count;
            }
        }
    }
</script>
@endsection
