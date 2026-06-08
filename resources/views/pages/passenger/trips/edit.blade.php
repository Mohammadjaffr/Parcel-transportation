@extends('layouts.app')

@section('title', 'تعديل الرحلة #' . $trip->id)
@section('Breadcrumb', 'تعديل الرحلة')

@section('content')

<div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl">

    {{-- Header --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('trips.index') }}" class="flex justify-center items-center w-12 h-12 bg-white dark:bg-boxdark rounded-2xl border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary hover:border-primary/30 active:scale-95">
                <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
            </a>
            <div>
                <h1 class="text-2xl font-black font-headline text-slate-800 dark:text-white flex items-center gap-2">
                    تعديل الرحلة
                    <span class="text-amber-500 font-mono bg-amber-500/10 px-2 py-0.5 rounded-lg text-lg">#{{ $trip->id }}</span>
                </h1>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mt-1">تغيير السائق المسؤول عن هذه الرحلة.</p>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="mx-auto w-full max-w-3xl">
        <div class="bg-white dark:bg-boxdark rounded-[2rem] p-6 md:p-10 border border-gray-100 dark:border-boxdark-2 shadow-sm">
            
            <form action="{{ route('trips.update', $trip->id) }}" method="POST"
                x-data="{
                    driverId: '{{ $trip->driver_id }}',
                    searchDriver: '',
                    dropdownOpen: false,
                    drivers: @js($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])),
                    get selectedDriver() {
                        return this.drivers.find(d => String(d.id) === String(this.driverId));
                    },
                    get filteredDrivers() {
                        if (this.searchDriver === '') return this.drivers;
                        return this.drivers.filter(d => 
                            String(d.name).includes(this.searchDriver) || 
                            String(d.phone).includes(this.searchDriver)
                        );
                    }
                }">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white font-headline flex items-center gap-2 mb-2">
                            <span class="material-symbols-outlined text-amber-500">local_taxi</span>
                            تحديد السائق الجديد
                        </h3>
                        <p class="text-sm font-bold text-gray-500 dark:text-gray-400">اختر السائق الذي سيتولى هذه الرحلة من القائمة أدناه.</p>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-500 dark:text-gray-400 mb-2">السائق المسؤول <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="driver_id" :value="driverId">
                        
                        {{-- Custom Select Button --}}
                        <div @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" 
                            class="flex items-center justify-between w-full h-16 px-4 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-boxdark-2 rounded-xl cursor-pointer hover:border-amber-500 transition-colors focus:ring-2 focus:ring-amber-500/20"
                            :class="dropdownOpen ? 'border-amber-500 ring-2 ring-amber-500/20' : ''">
                            
                            <div class="flex items-center gap-3">
                                <template x-if="selectedDriver">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-800 dark:text-white" x-text="selectedDriver.name"></span>
                                        <span class="text-xs text-gray-500 font-mono text-right dir-ltr block mt-0.5" style="direction: ltr;" x-text="selectedDriver.phone"></span>
                                    </div>
                                </template>
                                <template x-if="!selectedDriver">
                                    <span class="text-sm text-gray-400 font-bold">-- اختر سائقاً --</span>
                                </template>
                            </div>
                            <span class="material-symbols-outlined text-gray-400 transition-transform" :class="dropdownOpen ? 'rotate-180 text-amber-500' : ''">expand_more</span>
                        </div>

                        {{-- Dropdown Menu --}}
                        <div x-show="dropdownOpen" x-cloak x-transition
                            class="absolute z-50 w-full mt-2 bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-2xl shadow-xl overflow-hidden">
                            <div class="p-3 border-b border-slate-50 dark:border-boxdark-2 bg-slate-50/50 dark:bg-boxdark-2/50">
                                <div class="relative">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400 text-[18px]">search</span>
                                    <input type="text" x-model="searchDriver" placeholder="ابحث باسم السائق أو رقمه..."
                                        class="w-full pl-3 pr-10 py-2.5 bg-white dark:bg-boxdark border border-slate-200 dark:border-boxdark-2 rounded-xl text-sm outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                                </div>
                            </div>
                            <div class="max-h-60 overflow-y-auto custom-scrollbar py-2">
                                <template x-for="driver in filteredDrivers" :key="driver.id">
                                    <div @click="driverId = driver.id; dropdownOpen = false; searchDriver = ''"
                                        class="flex flex-col px-4 py-2.5 cursor-pointer transition-colors hover:bg-amber-50 dark:hover:bg-amber-500/10 group"
                                        :class="driverId === String(driver.id) ? 'bg-amber-50 dark:bg-amber-500/10' : ''">
                                        <span class="text-sm font-black group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors"
                                            :class="driverId === String(driver.id) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-gray-200'" x-text="driver.name"></span>
                                        <span class="text-xs text-gray-500 font-mono text-right dir-ltr block mt-0.5" style="direction: ltr;" x-text="driver.phone"></span>
                                    </div>
                                </template>
                                <div x-show="filteredDrivers.length === 0" class="px-4 py-6 text-center text-gray-400 text-sm font-bold">
                                    لم يتم العثور على سائق يطابق بحثك.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-slate-100 dark:border-boxdark-2 flex justify-end gap-3">
                    <a href="{{ route('trips.index') }}" class="px-6 py-3 rounded-xl font-bold text-sm text-slate-600 dark:text-gray-300 bg-slate-100 dark:bg-boxdark-2 hover:bg-slate-200 dark:hover:bg-gray-800 transition-colors active:scale-95">
                        إلغاء
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-xl font-black text-sm text-white bg-amber-500 hover:bg-amber-600 shadow-sm shadow-amber-500/30 transition-all active:scale-95 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
