@extends('mobile.layouts.app')

@section('title', 'تعديل الرحلة #' . $trip->id)

@section('content')

@php
    // دمج الركاب المضافين حالياً للرحلة مع الركاب قيد الانتظار
    $allPassengers = collect($trip->passengers)->merge($pendingPassengers)->unique('id')->values();
    $selectedIds = $trip->passengers->pluck('id')->toArray();
@endphp

<div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black font-body" dir="rtl">

    {{-- Header --}}
    <div class="flex items-center gap-3 px-4 mb-6">
        <a href="{{ route('trips.index') }}" class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-95">
            <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
        </a>
        <div>
            <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white flex items-center gap-2">
                تعديل الرحلة
                <span class="text-amber-500 font-mono bg-amber-500/10 px-2 py-0.5 rounded-md text-sm">#{{ $trip->id }}</span>
            </h1>
            <p class="text-xs text-gray-400 mt-0.5">تعديل بيانات السائق والركاب المنضمين للرحلة.</p>
        </div>
    </div>

    {{-- Content --}}
    <div class="px-4">
        
        <form action="{{ route('trips.update', $trip->id) }}" method="POST"
            x-data="editTripForm(@js($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])), @js($allPassengers), @js($selectedIds), '{{ $trip->driver_id }}')">
            @csrf
            @method('PUT')

            {{-- قسم السائق --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] p-5 border border-gray-100 dark:border-boxdark-2 shadow-sm mb-6">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 dark:text-white font-headline flex items-center gap-2 mb-1">
                            <span class="material-symbols-outlined text-amber-500 text-[20px]">local_taxi</span>
                            تحديد السائق المسؤول
                        </h3>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold text-slate-500 dark:text-gray-400 mb-2">السائق المسؤول <span class="text-rose-500">*</span></label>
                        <input type="hidden" name="driver_id" :value="driverId">
                        
                        {{-- Custom Select Button --}}
                        <div @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false" 
                            class="flex items-center justify-between w-full h-14 px-4 bg-slate-50 dark:bg-boxdark-2 border border-slate-200 dark:border-boxdark-2 rounded-xl cursor-pointer hover:border-amber-500 transition-colors focus:ring-2 focus:ring-amber-500/20"
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
                            class="absolute z-[60] w-full mt-2 bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-2xl shadow-2xl overflow-hidden">
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
            </div>

            {{-- قسم الركاب --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center px-1">
                    <h3 class="font-black text-slate-800 dark:text-white font-headline text-sm flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-amber-500">group</span>
                        ركاب الرحلة
                        <span class="text-amber-500 font-mono bg-amber-500/10 px-1.5 py-0.5 rounded text-[10px] mr-1" x-text="selectedPassengers.length"></span>
                    </h3>
                    
                    <button type="button" @click="selectAllFiltered()" class="text-[11px] font-bold text-amber-500 bg-amber-500/10 px-2 py-1 rounded-lg">
                        تحديد المعروض
                    </button>
                </div>

                <div class="relative px-1">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400 text-[18px]">search</span>
                    <input type="text" x-model="searchQuery" placeholder="بحث برقم الجوال..."
                        class="pr-11 pl-4 w-full h-12 text-sm bg-white dark:bg-boxdark text-slate-800 dark:text-white rounded-2xl border transition-all outline-none border-slate-200 dark:border-boxdark-2 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <template x-for="passenger in filteredPassengers()" :key="passenger.id">
                        <label class="block relative cursor-pointer group">
                            <input type="checkbox" name="passenger_ids[]" :value="passenger.id" class="hidden peer"
                                :checked="selectedPassengers.includes(passenger.id)"
                                @change="togglePassenger(passenger.id)">

                            <div class="bg-white dark:bg-boxdark p-4 rounded-3xl border transition-all border-slate-100 dark:border-boxdark-2 shadow-[0_4px_20px_rgb(0,0,0,0.01)] flex items-center justify-between gap-4 peer-checked:ring-2 peer-checked:ring-amber-500 peer-checked:border-transparent peer-checked:bg-amber-500/5 hover:border-amber-500/30">
                                
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="flex flex-col justify-center items-center w-11 h-11 rounded-2xl transition-all bg-slate-50 dark:bg-boxdark-2 text-slate-600 dark:text-gray-300 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:shadow-sm shrink-0 border border-slate-100 dark:border-boxdark-2 peer-checked:border-transparent">
                                        <span class="text-sm font-black leading-none" x-text="passenger.count"></span>
                                        <span class="text-[8px] font-bold mt-0.5">ركاب</span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <span class="block font-headline text-sm font-black text-slate-800 dark:text-white tracking-tight truncate mb-0.5" style="direction: ltr; text-align: right;" x-text="passenger.passenger_number"></span>
                                        
                                        <div class="flex gap-1 items-center">
                                            <span class="material-symbols-outlined text-[13px] text-amber-500">location_on</span>
                                            <span class="text-xs font-bold text-slate-500 dark:text-gray-400 truncate" x-text="passenger.destination"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-center items-center w-6 h-6 rounded-full border-2 transition-all border-slate-200 dark:border-boxdark-2 peer-checked:bg-amber-500 peer-checked:border-amber-500 shrink-0">
                                    <span class="material-symbols-outlined text-white text-[16px] hidden peer-checked:block">check</span>
                                </div>
                            </div>
                        </label>
                    </template>

                    <div x-show="filteredPassengers().length === 0" class="bg-white dark:bg-boxdark p-10 rounded-[2.5rem] border border-slate-100 dark:border-boxdark-2 shadow-sm flex flex-col items-center justify-center text-slate-400">
                        <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-slate-50 dark:bg-boxdark-2">
                            <span class="material-symbols-outlined text-[32px] opacity-30">person_search</span>
                        </div>
                        <p class="text-xs font-bold text-slate-500 dark:text-gray-400">لا يوجد ركاب يطابقون خيارات التصفية حالياً</p>
                    </div>
                </div>
            </div>

            {{-- شريط الحفظ السفلي --}}
            <div class="mt-8 p-5 bg-white dark:bg-boxdark rounded-[2.5rem] border border-slate-50 dark:border-boxdark-2 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] flex flex-col gap-3"
                x-data="{ isSubmitting: false }">

                <button type="submit"
                    @click="if(selectedPassengers.length > 0 && driverId && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                    :disabled="selectedPassengers.length === 0 || !driverId || isSubmitting" 
                    class="w-full h-14 px-8 bg-amber-500 text-white rounded-2xl font-black text-sm transition-all active:scale-95 flex items-center justify-center gap-3 disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-boxdark-2 dark:disabled:text-gray-600 disabled:shadow-none">

                    <template x-if="isSubmitting">
                        <div class="flex gap-2 items-center">
                            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                            <span>جاري حفظ التعديلات...</span>
                        </div>
                    </template>

                    <template x-if="!isSubmitting">
                        <div class="flex gap-3 items-center">
                            <span class="material-symbols-outlined text-[20px]">save</span>
                            <span>حفظ التعديلات</span>
                            <div class="flex justify-center items-center w-7 h-7 rounded-xl bg-white/20">
                                <span class="text-[12px]" x-text="selectedPassengers.length">0</span>
                            </div>
                        </div>
                    </template>
                </button>
                <a href="{{ route('trips.index') }}" class="w-full flex justify-center items-center h-14 rounded-2xl font-bold text-sm text-slate-600 dark:text-gray-300 bg-slate-100 dark:bg-boxdark-2 hover:bg-slate-200 dark:hover:bg-gray-800 transition-colors active:scale-95">
                    إلغاء
                </a>
            </div>

        </form>
    </div>
</div>

@endsection

@section('script')
    <script>
        function editTripForm(driversList, allPassengersList, initialSelectedIds, initialDriverId) {
            return {
                drivers: driversList || [],
                passengers: (allPassengersList || []).map(p => ({
                    id: p.id,
                    passenger_number: p.passenger_number,
                    count: p.count || 1,
                    destination: p.destination || 'غير محدد'
                })),
                
                driverId: initialDriverId,
                searchDriver: '',
                dropdownOpen: false,

                searchQuery: '',
                selectedPassengers: initialSelectedIds || [],

                get selectedDriver() {
                    return this.drivers.find(d => String(d.id) === String(this.driverId));
                },
                get filteredDrivers() {
                    if (this.searchDriver === '') return this.drivers;
                    return this.drivers.filter(d => 
                        String(d.name).includes(this.searchDriver) || 
                        String(d.phone).includes(this.searchDriver)
                    );
                },

                filteredPassengers() {
                    return this.passengers.filter(p => {
                        return this.searchQuery === '' || String(p.passenger_number).includes(this.searchQuery);
                    });
                },
                togglePassenger(id) {
                    if (this.selectedPassengers.includes(id)) {
                        this.selectedPassengers = this.selectedPassengers.filter(pId => pId !== id);
                    } else {
                        this.selectedPassengers.push(id);
                    }
                },
                selectAllFiltered() {
                    const visibleIds = this.filteredPassengers().map(p => p.id);
                    this.selectedPassengers = [...new Set([...this.selectedPassengers, ...visibleIds])];
                }
            }
        }
    </script>
@endsection
