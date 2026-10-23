@extends('mobile.layouts.app')

@section('title', 'إنشاء رحلة جديدة')

@section('content')

    <div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black" dir="rtl">

        {{-- ================= الهيدر وزر الرجوع ================= --}}
        <div class="flex items-center gap-3 px-4 mb-6">
            <a href="{{ route('trips.index') }}" class="flex justify-center items-center w-10 h-10 bg-white dark:bg-boxdark rounded-full border shadow-sm transition-all border-slate-100 dark:border-boxdark-2 text-slate-500 dark:text-gray-400 hover:text-primary active:scale-90">
                <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
            </a>
            <div>
                <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white">إنشاء رحلة جديدة</h1>
                <p class="text-xs text-gray-400 mt-0.5">تجميع الركاب وتسيير الرحلة فوراً</p>
            </div>
        </div>

        {{-- ================= حاوية الفورم الأساسية ================= --}}
        <div class="px-4"
            x-data="createTripForm(@js($drivers ?? []), @js(array_values(config('countries', []))), @js($pendingPassengers ?? []), @js($brokers ?? []))">
            <div class="w-full bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-5 border border-gray-100 dark:border-boxdark-2">

                <form action="{{ route('trips.store') }}" method="POST" @submit="submitTrip($event)">
                    @csrf

                    {{-- إرسال الركاب المحددين كحقول مخفية لضمان وصولهم حتى لو تم البحث والتصفية --}}
                    <template x-for="pId in selectedPassengers" :key="'submitted-p-' + pId">
                        <input type="hidden" name="passenger_ids[]" :value="pId">
                    </template>

                    {{-- ================= مكون السائق المسؤول ================= --}}
                    <div class="p-4 rounded-2xl border bg-slate-50/50 border-slate-100 dark:bg-boxdark-2/40 dark:border-boxdark-2 relative">
                        <span class="absolute -top-2.5 right-4 bg-white dark:bg-boxdark px-2 text-[10px] font-black text-slate-500 dark:text-gray-400 rounded-md border border-slate-100 dark:border-boxdark-2">
                            السائق المسؤول <span class="text-red-500">*</span>
                        </span>

                        <div class="grid relative grid-cols-1 gap-3 mt-2">
                            <input type="hidden" name="driver_id" x-model="selectedDriverId">
                            <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                            <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 transition-all group ring-slate-200 dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/50"
                                :class="selectedDriverId ? 'bg-primary/5 ring-primary/30 dark:bg-primary/5' : ''" style="direction: ltr;">

                                <div class="relative h-full shrink-0" @click.away="openCountryDropdown = false">
                                    <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex gap-2 items-center px-3 h-12 rounded-l-xl border-r transition-colors bg-slate-50 border-slate-200 dark:bg-boxdark-2 dark:border-boxdark text-slate-600 dark:text-gray-300 min-w-max">
                                        <div class="w-6 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden shrink-0" x-html="selectedCountry?.svg"></div>
                                        <span class="text-xs font-bold font-mono dir-ltr shrink-0" x-text="selectedCountry?.dial_code" style="direction: ltr;"></span>
                                    </button>

                                    <div x-show="openCountryDropdown" x-cloak x-transition
                                        class="absolute top-full left-0 mt-1 w-[260px] max-w-[90vw] bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-xl shadow-xl z-[60] overflow-hidden">
                                        <div class="p-2 border-b border-slate-50 dark:border-boxdark-2">
                                            <input type="text" x-model="searchCountryQuery" placeholder="بحث عن دولة..."
                                                class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-slate-50 dark:bg-boxdark-2 text-slate-800 dark:text-white border border-slate-100 dark:border-boxdark-2 focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all" dir="rtl">
                                        </div>
                                        <div class="overflow-y-auto max-h-56 custom-scrollbar" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button" @click="selectedCountry = country; openCountryDropdown = false; searchDriver()"
                                                    class="flex gap-3 items-center px-3 py-2.5 w-full text-left transition-colors hover:bg-slate-50 dark:hover:bg-boxdark-2 text-slate-700 dark:text-gray-200 border-b border-slate-50 dark:border-boxdark-2 last:border-0">
                                                    <div class="w-6 h-auto flex items-center justify-center rounded-[2px] overflow-hidden shrink-0 shadow-sm" x-html="country.svg"></div>
                                                    <span class="flex-1 text-xs font-bold truncate" x-text="country.name"></span>
                                                    <span class="text-xs text-slate-400 font-mono shrink-0" x-text="country.dial_code" style="direction: ltr;"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <input type="tel" x-model="localPhoneNumber" @input="searchDriver"
                                    @focus="showDriverDropdown = true" @click.away="showDriverDropdown = false"
                                    placeholder="7XXXXXXXX" :required="!selectedDriverId" inputmode="numeric" autocomplete="off"
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 px-4 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-slate-800 dark:text-white"
                                    :class="selectedDriverId ? 'font-bold text-primary dark:text-primary-hover' : ''">

                                <button type="button" x-show="selectedDriverId" @click="resetSelection"
                                    class="absolute right-3 z-10 p-0.5 bg-white dark:bg-boxdark rounded-full text-slate-400 hover:text-red-500">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            {{-- نتائج البحث المنسدلة للسائقين --}}
                            <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId" x-transition x-cloak
                                class="absolute top-[3.25rem] right-0 w-full bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-xl shadow-lg overflow-hidden max-h-48 overflow-y-auto z-50">
                                <template x-for="driver in filteredDrivers" :key="driver.id">
                                    <button type="button" @click="selectDriver(driver)"
                                        class="flex justify-between items-center px-4 py-3 w-full text-right border-b transition-colors hover:bg-slate-50 dark:hover:bg-boxdark-2 border-slate-50 dark:border-boxdark-2">
                                        <div class="flex flex-col gap-0.5">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-bold text-slate-800 dark:text-white" x-text="driver.name"></span>
                                                <span x-show="driver.vehicle_type" class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800" x-text="driver.vehicle_type"></span>
                                            </div>
                                            <span class="text-xs text-slate-500 font-mono dir-ltr text-right" x-text="driver.phone"></span>
                                        </div>
                                        <span class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                                    </button>
                                </template>
                                <div x-show="filteredDrivers.length === 0" class="px-4 py-3 text-center bg-slate-50/50 dark:bg-boxdark-2/30">
                                    <span class="text-xs font-bold text-slate-500 dark:text-gray-400">سائق جديد، سيتم حفظه تلقائياً.</span>
                                </div>
                            </div>

                            <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedDriverId !== null"
                                placeholder="اسم السائق..." :required="!selectedDriverId"
                                class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none font-bold"
                                :class="selectedDriverId ? 'bg-slate-50 border-transparent text-emerald-600 dark:text-emerald-400 dark:bg-boxdark-2/60 cursor-not-allowed' : 'bg-white border-slate-200 dark:bg-boxdark dark:border-boxdark-2 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-700 dark:text-white'">

                            <input type="text" name="driver_vehicle_type" list="mobile_trip_driver_vehicle_types" x-model="vehicleTypeInput"
                                placeholder="نوع المركبة (اختياري)..."
                                class="px-4 w-full h-12 text-sm rounded-xl border transition-colors outline-none font-bold bg-white border-slate-200 dark:bg-boxdark dark:border-boxdark-2 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-700 dark:text-white">
                            <datalist id="mobile_trip_driver_vehicle_types">
                                <option value="صالون"></option>
                                <option value="باص"></option>
                                <option value="دينا"></option>
                                <option value="شاص"></option>
                                <option value="هايلوكس"></option>
                                <option value="حبة وربع"></option>
                                <option value="نوها"></option>
                                <option value="خصوصي"></option>
                            </datalist>
                        </div>
                    </div>

                    {{-- ================= قسم الركاب المتاحين (قيد الانتظار) ================= --}}
                    <div class="space-y-4 mt-6">
                        
                        {{-- هيدر المكون مع أزرار التحكم السريع --}}
                        <div class="flex justify-between items-center px-2">
                            <h3 class="flex gap-2 items-center font-black text-slate-800 dark:text-white font-headline text-sm">
                                الركاب 
                                <span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full" x-text="selectedPassengers.length">0</span>
                            </h3>
                            <div class="flex gap-2">
                                <button type="button" @click="selectAllFiltered()"
                                    class="text-[10px] font-bold text-primary bg-primary/10 px-2.5 py-1 rounded-lg active:scale-95 transition-all">تحديد الكل</button>
                                <button type="button" @click="selectedPassengers = []"
                                    class="text-[10px] font-bold text-rose-500 bg-rose-50 dark:bg-rose-500/10 px-2.5 py-1 rounded-lg active:scale-95 transition-all">إلغاء</button>
                            </div>
                        </div>


                        {{-- قائمة عرض كروت الركاب المفصلة --}}
                        <div class="grid grid-cols-1 gap-3">
                            <template x-for="passenger in filteredPassengers()" :key="passenger.id">
                                <label class="block relative cursor-pointer group">
                                    <input type="checkbox" class="hidden peer"
                                        :checked="selectedPassengers.includes(passenger.id)"
                                        @change="togglePassenger(passenger.id)">

                                    <div class="bg-white dark:bg-boxdark p-4 rounded-3xl border border-slate-100 dark:border-boxdark-2 shadow-[0_4px_20px_rgb(0,0,0,0.02)] peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent peer-checked:bg-primary/[0.02] dark:peer-checked:bg-primary/5 transition-all flex items-center justify-between gap-4 active:scale-[0.98]">
                                        
                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                            {{-- العداد الجانبي لعدد الأفراد --}}
                                            <div class="flex flex-col justify-center items-center w-12 h-12 rounded-2xl shadow-sm transition-all bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 peer-checked:bg-primary peer-checked:text-white shrink-0">
                                                <span class="text-base font-black leading-none" x-text="passenger.count"></span>
                                                <span class="text-[9px] font-bold mt-0.5">ركاب</span>
                                            </div>

                                            {{-- تفاصيل الراكب (الرقم والوجهة فقط لمنع التداخل) --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-1 text-sm font-black tracking-tight font-headline text-slate-800 dark:text-white" style="direction: ltr; justify-content: flex-end;">
                                                    <span x-text="getPassengerPhoneDetails(passenger.passenger_number).localNumber"></span>
                                                    <div class="w-5 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden shrink-0" 
                                                         x-html="getPassengerPhoneDetails(passenger.passenger_number).flag"></div>
                                                </div>
                                                
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <template x-if="passenger.vehicle_type">
                                                        <div class="flex gap-0.5 items-center px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-900/30 text-[10px] font-bold text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/40">
                                                            <span class="material-symbols-outlined text-[11px]">directions_car</span>
                                                            <span class="truncate max-w-[90px]" x-text="passenger.vehicle_type"></span>
                                                        </div>
                                                    </template>
                                                    <div class="flex gap-1 items-center">
                                                        <span class="material-symbols-outlined text-[14px] text-primary">flag</span>
                                                        <span class="text-xs font-bold text-slate-600 dark:text-gray-300 truncate" x-text="passenger.destination || 'غير محدد'"></span>
                                                    </div>
                                                    <template x-if="passenger.pickup_location">
                                                        <div class="flex gap-0.5 items-center px-1.5 py-0.5 rounded-md bg-slate-100/80 dark:bg-boxdark-2 text-[10px] font-bold text-slate-500 dark:text-gray-400">
                                                            <span class="material-symbols-outlined text-[11px] text-slate-400">pin_drop</span>
                                                            <span class="truncate max-w-[90px]" x-text="passenger.pickup_location"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- أزرار التحكم: زر الإلغاء والتعديل والتشيك --}}
                                        <div class="flex items-center gap-2 shrink-0">
                                            <button type="button" @click.stop.prevent="openStatusModal(passenger)"
                                                title="إلغاء حجز الراكب"
                                                class="flex justify-center items-center w-8 h-8 rounded-full border border-slate-200 dark:border-boxdark-2 text-slate-400 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/20 active:scale-90 transition-all shadow-sm">
                                                <span class="material-symbols-outlined text-[17px]">block</span>
                                            </button>

                                            <button type="button" @click.stop.prevent="openEditPassenger(passenger)"
                                                title="تعديل الراكب"
                                                class="flex justify-center items-center w-8 h-8 rounded-full border border-slate-200 dark:border-boxdark-2 text-slate-400 hover:text-primary hover:border-primary/40 active:scale-90 transition-all shadow-sm">
                                                <span class="material-symbols-outlined text-[17px]">edit</span>
                                            </button>

                                            <div class="flex justify-center items-center w-6 h-6 rounded-full border-2 transition-all border-slate-200 dark:border-boxdark-2 peer-checked:bg-primary peer-checked:border-primary shrink-0">
                                                <span class="material-symbols-outlined text-white text-[16px] hidden peer-checked:block">check</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </template>

                            {{-- رسالة فارغة --}}
                            <div x-show="filteredPassengers().length === 0" class="bg-white dark:bg-boxdark p-10 rounded-[2.5rem] border border-slate-100 dark:border-boxdark-2 shadow-sm flex flex-col items-center justify-center text-slate-400">
                                <div class="flex justify-center items-center mb-3 w-16 h-16 rounded-full bg-slate-50 dark:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[32px] opacity-30">person_search</span>
                                </div>
                                <p class="text-xs font-bold text-slate-500 dark:text-gray-400">لا يوجد ركاب يطابقون خيارات التصفية حالياً</p>
                            </div>
                        </div>
                    </div>

                    {{-- ================= شريط الإنشاء السفلي ================= --}}
                    <div class="mt-8 mb-4 p-6 bg-white dark:bg-boxdark rounded-[2.5rem] border border-slate-50 dark:border-boxdark-2 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] flex flex-col sm:flex-row items-center justify-between gap-6">

                        <button type="submit"
                            :disabled="selectedPassengers.length === 0 || isSubmitting" 
                            class="w-full h-14 px-8 bg-primary text-white rounded-2xl font-black text-sm transition-all active:scale-95 flex items-center justify-center gap-3 disabled:bg-slate-100 disabled:text-slate-400 dark:disabled:bg-boxdark-2 dark:disabled:text-gray-600 disabled:shadow-none">

                            <template x-if="isSubmitting">
                                <div class="flex gap-2 items-center">
                                    <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                    <span>جاري إنشاء وتسيير الرحلة...</span>
                                </div>
                            </template>

                            <template x-if="!isSubmitting">
                                <div class="flex gap-3 items-center">
                                    <span>إنشاء الرحلة وانطلاقها</span>
                                    <div class="flex justify-center items-center w-7 h-7 rounded-xl bg-white/20">
                                        <span class="text-[12px]" x-text="selectedPassengers.length">0</span>
                                    </div>
                                </div>
                            </template>
                        </button>
                    </div>

                </form>

                {{-- ================= مودال تعديل بيانات الراكب للموبايل ================= --}}
                <div x-show="showEditPassengerModal" x-cloak
                    class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-900/60 backdrop-blur-sm"
                    @keydown.escape.window="showEditPassengerModal = false">
                    
                    <div class="bg-white dark:bg-boxdark rounded-t-[2rem] sm:rounded-[2rem] w-full max-w-lg shadow-2xl border border-slate-100 dark:border-boxdark-2 max-h-[88vh] overflow-hidden flex flex-col"
                        @click.away="showEditPassengerModal = false">
                        
                        {{-- Header --}}
                        <div class="flex justify-between items-center px-5 py-4 border-b border-slate-100 dark:border-boxdark-2 bg-slate-50/50 dark:bg-boxdark-2/50 shrink-0">
                            <div class="flex items-center gap-2.5">
                                <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-primary/10 text-primary">
                                    <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-800 dark:text-white font-headline">تعديل بيانات الراكب</h3>
                                    <p class="text-[11px] text-gray-400 font-bold">تعديل فوري للراكب</p>
                                </div>
                            </div>
                            <button type="button" @click="showEditPassengerModal = false"
                                class="flex justify-center items-center w-8 h-8 rounded-full text-slate-400 hover:text-rose-500 bg-slate-100 dark:bg-boxdark-2 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>

                        {{-- Form Body --}}
                        <div class="p-4 overflow-y-auto space-y-3.5 custom-scrollbar text-right">
                            {{-- Error banner --}}
                            <div x-show="editPassengerError" x-cloak
                                class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-2">
                                <span class="material-symbols-outlined text-[17px]">error</span>
                                <span x-html="editPassengerError"></span>
                            </div>

                            {{-- 1. بطاقة: التاريخ، مكان الصعود والوجهة --}}
                            <div class="p-3 space-y-2.5 rounded-xl border border-slate-100 bg-slate-50/50 dark:bg-boxdark-2/50 dark:border-boxdark-2">
                                <div>
                                    <label class="block mb-1 text-xs font-bold text-slate-600 dark:text-gray-300">تاريخ الحجز <span class="text-rose-500">*</span></label>
                                    <input type="date" x-model="editPassengerData.date" required
                                        class="px-2.5 w-full h-11 text-xs rounded-xl border border-slate-200 dark:border-boxdark-2 bg-white dark:bg-boxdark dark:text-white outline-none focus:border-primary">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-slate-600 dark:text-gray-300">مكان الصعود <span class="text-rose-500">*</span></label>
                                        <input type="text" x-model="editPassengerData.pickup_location" required placeholder="مكان الصعود"
                                            class="px-2.5 w-full h-11 text-xs rounded-xl border border-slate-200 dark:border-boxdark-2 bg-white dark:bg-boxdark dark:text-white outline-none focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block mb-1 text-xs font-bold text-slate-600 dark:text-gray-300">الوجهة</label>
                                        <input type="text" x-model="editPassengerData.destination" placeholder="وجهة الراكب"
                                            class="px-2.5 w-full h-11 text-xs rounded-xl border border-slate-200 dark:border-boxdark-2 bg-white dark:bg-boxdark dark:text-white outline-none focus:border-primary">
                                    </div>
                                </div>
                            </div>

                            {{-- 2. رقم جوال الراكب --}}
                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-slate-600 dark:text-gray-300">رقم جوال الراكب <span class="text-rose-500">*</span></label>
                                <div class="flex overflow-visible relative items-center h-12 bg-white rounded-xl ring-1 ring-slate-200 dark:bg-boxdark-2 dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/50" style="direction: ltr;">
                                    <div class="relative h-full shrink-0" @click.away="editPassengerData.openCountryDropdown = false">
                                        <button type="button" @click="editPassengerData.openCountryDropdown = !editPassengerData.openCountryDropdown"
                                            class="flex gap-2 items-center px-3 h-full rounded-l-xl border-r transition-colors bg-slate-50 border-slate-200 dark:bg-boxdark dark:border-boxdark-2 text-slate-600 dark:text-gray-300">
                                            <div class="w-5 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden" x-html="editPassengerData.selectedCountry?.svg"></div>
                                            <span class="text-xs font-bold" x-text="editPassengerData.selectedCountry?.dial_code"></span>
                                        </button>

                                        <div x-show="editPassengerData.openCountryDropdown" x-cloak x-transition
                                            class="absolute top-full left-0 mt-1 w-60 max-w-[85vw] bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-xl shadow-xl z-[70] overflow-hidden">
                                            <div class="p-2 border-b border-slate-50 dark:border-boxdark-2">
                                                <input type="text" x-model="editPassengerData.searchCountryQuery" placeholder="بحث..."
                                                    class="px-2.5 w-full h-8 text-xs rounded-lg outline-none bg-slate-50 dark:bg-boxdark-2 text-slate-800 dark:text-white" dir="rtl">
                                            </div>
                                            <div class="overflow-y-auto max-h-44 custom-scrollbar" dir="ltr">
                                                <template x-for="country in editPassengerFilteredCountries" :key="country.code">
                                                    <button type="button" @click="editPassengerData.selectedCountry = country; editPassengerData.openCountryDropdown = false"
                                                        class="flex gap-2.5 items-center px-3 py-2 w-full text-left border-b transition-colors hover:bg-slate-50 dark:hover:bg-boxdark-2 text-slate-700 dark:text-gray-200 border-slate-50 dark:border-boxdark-2 last:border-0 text-xs font-bold">
                                                        <div class="w-5 h-auto flex items-center justify-center rounded-[2px] overflow-hidden shadow-sm" x-html="country.svg"></div>
                                                        <span class="flex-1 truncate" x-text="country.name"></span>
                                                        <span class="font-mono text-[10px] text-slate-400" x-text="country.dial_code"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="tel" x-model="editPassengerData.localPhone" placeholder="7XXXXXXXX" inputmode="numeric" autocomplete="off"
                                        :maxlength="editPassengerData.selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-3 w-full h-full text-sm text-left bg-transparent border-0 outline-none focus:ring-0 text-slate-800 dark:text-white">
                                </div>
                            </div>

                            {{-- 3. بطاقة بيانات الوسيط: اسم الوسيط ورقم هاتف الوسيط (واتساب) --}}
                            <div class="p-3 space-y-2.5 rounded-xl border border-slate-100 bg-slate-50/50 dark:bg-boxdark-2/50 dark:border-boxdark-2">
                                <div class="relative">
                                    <label class="block mb-1 text-xs font-bold text-slate-600 dark:text-gray-300">اسم الوسيط أو المكتب <span class="text-rose-500">*</span></label>
                                    <div class="flex relative items-center bg-white rounded-xl ring-1 ring-slate-200 transition-all dark:bg-boxdark focus-within:ring-2 focus-within:ring-primary/40"
                                        :class="editPassengerData.broker_id ? 'bg-emerald-50/30 dark:bg-emerald-500/10 ring-emerald-400 dark:ring-emerald-500/50' : ''">
                                        <span class="absolute right-2.5 material-symbols-outlined text-[18px]"
                                            :class="editPassengerData.broker_id ? 'text-emerald-500' : 'text-slate-400'">handshake</span>
                                        <input type="text" x-model="editPassengerData.brokerSearchQuery"
                                            @input="onBrokerInput()"
                                            @focus="editPassengerData.showBrokerDropdown = true"
                                            @click.away="editPassengerData.showBrokerDropdown = false"
                                            placeholder="اكتب اسم الوسيط..."
                                            autocomplete="off"
                                            class="px-3 pr-9 w-full h-11 text-xs font-bold bg-transparent border-none outline-none focus:ring-0 text-slate-800 dark:text-white"
                                            :class="editPassengerData.broker_id ? 'text-emerald-600 dark:text-emerald-400 font-black' : ''">
                                        <button type="button" x-show="editPassengerData.brokerSearchQuery.length > 0" @click="resetEditBroker()"
                                            class="absolute left-2 p-1 text-slate-400 rounded-full hover:text-rose-500">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                        </button>
                                    </div>
                                    <div x-show="editPassengerData.showBrokerDropdown && editPassengerFilteredBrokers.length > 0" x-cloak x-transition
                                        class="absolute top-full right-0 mt-1 w-full bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-xl shadow-xl z-[70] overflow-hidden max-h-40 overflow-y-auto custom-scrollbar">
                                        <template x-for="b in editPassengerFilteredBrokers" :key="b.id">
                                            <button type="button" @click="selectEditBroker(b)"
                                                class="flex justify-between items-center px-3 py-2 w-full text-right border-b border-slate-50 dark:border-boxdark-2 text-xs font-bold hover:bg-slate-50 dark:hover:bg-boxdark-2 text-slate-700 dark:text-gray-200">
                                                <span x-text="b.name"></span>
                                                <span class="material-symbols-outlined text-[14px] text-emerald-500">done</span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="block mb-1 text-xs font-bold text-slate-600 dark:text-gray-300">رقم هاتف الوسيط (واتساب) <span class="text-[10px] text-slate-400 font-normal">(اختياري)</span></label>
                                    <div class="flex relative items-center bg-white rounded-xl ring-1 ring-slate-200 transition-all dark:bg-boxdark focus-within:ring-2 focus-within:ring-primary/40">
                                        <span class="absolute right-2.5 text-slate-400 material-symbols-outlined text-[18px]">call</span>
                                        <input type="text" x-model="editPassengerData.broker_phone"
                                            placeholder="77xxxxxxxx"
                                            dir="ltr"
                                            autocomplete="off"
                                            class="px-3 pr-9 w-full h-11 text-xs font-bold bg-transparent border-none outline-none focus:ring-0 text-slate-800 dark:text-white text-right">
                                    </div>
                                </div>
                            </div>

                            {{-- 4. نوع المركبة، عدد الركاب والعمولات --}}
                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-slate-600 dark:text-gray-300">نوع المركبة</label>
                                <input type="text" list="vehicle_types_list" x-model="editPassengerData.vehicle_type" placeholder="مثلاً: صالون، باص"
                                    class="px-3 w-full h-11 text-xs rounded-xl border border-slate-200 dark:border-boxdark-2 bg-white dark:bg-boxdark-2 text-slate-800 dark:text-white outline-none focus:border-primary">
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-slate-600 dark:text-gray-300">العدد <span class="text-rose-500">*</span></label>
                                    <input type="number" min="1" x-model="editPassengerData.count" required
                                        class="px-2.5 w-full h-11 text-xs font-black text-center rounded-xl border border-slate-200 dark:border-boxdark-2 bg-white dark:bg-boxdark-2 text-slate-800 dark:text-white outline-none focus:border-primary">
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-slate-600 dark:text-gray-300 truncate">عمولة المكتب <span class="text-rose-500">*</span></label>
                                    <input type="number" min="0" step="0.01" x-model="editPassengerData.office_commission" placeholder="0"
                                        class="px-2 w-full h-11 text-xs font-bold text-emerald-600 text-center rounded-xl border border-slate-200 dark:border-boxdark-2 bg-white dark:bg-boxdark-2 outline-none focus:border-primary">
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-slate-600 dark:text-gray-300 truncate">عمولة أخرى <span class="text-rose-500">*</span></label>
                                    <input type="number" min="0" step="0.01" x-model="editPassengerData.other_office_commission" placeholder="0"
                                        class="px-2 w-full h-11 text-xs font-bold text-amber-600 text-center rounded-xl border border-slate-200 dark:border-boxdark-2 bg-white dark:bg-boxdark-2 outline-none focus:border-primary">
                                </div>
                            </div>

                            {{-- 5. ملاحظات إضافية --}}
                            <div>
                                <label class="block mb-1.5 text-xs font-bold text-slate-600 dark:text-gray-300">ملاحظات إضافية</label>
                                <textarea x-model="editPassengerData.note" rows="2" placeholder="أي ملاحظات..."
                                    class="p-2.5 w-full text-xs rounded-xl border border-slate-200 dark:border-boxdark-2 bg-white dark:bg-boxdark-2 text-slate-800 dark:text-white outline-none focus:border-primary"></textarea>
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="p-4 border-t border-slate-100 dark:border-boxdark-2 bg-slate-50/50 dark:bg-boxdark-2/50 flex flex-col gap-2 shrink-0">
                            <div class="flex gap-2.5">
                                <button type="button" @click="showEditPassengerModal = false"
                                    class="flex-1 h-12 text-xs font-bold text-slate-600 dark:text-gray-300 bg-white dark:bg-boxdark rounded-xl border border-slate-200 dark:border-boxdark-2 active:scale-95 transition-all">
                                    إغلاق
                                </button>
                                <button type="button" @click="savePassengerEdit()" :disabled="isUpdatingPassenger"
                                    class="flex-[2] flex justify-center items-center gap-2 h-12 text-xs font-black text-white bg-primary active:scale-95 rounded-xl shadow-md transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                                    <template x-if="isUpdatingPassenger">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                            <span>جاري الحفظ...</span>
                                        </span>
                                    </template>
                                    <template x-if="!isUpdatingPassenger">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[18px]">save</span>
                                            <span>حفظ التعديلات</span>
                                        </span>
                                    </template>
                                </button>
                            </div>
                            <button type="button" @click="openCancelModalFromEdit()"
                                class="w-full h-11 flex items-center justify-center gap-2 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 rounded-xl transition-all border border-amber-200 dark:border-amber-500/20 active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">block</span>
                                <span>إلغاء حجز الراكب وإزالته</span>
                            </button>
                        </div>

                    </div>
                </div>

                {{-- ====================== Status Modal (Cancel Only) للموبايل ====================== --}}
                <template x-teleport="body">
                    <div x-show="showStatusModal" x-cloak
                        class="fixed inset-0 z-[99999] flex items-end sm:items-center justify-center p-0 sm:p-4 font-body"
                        dir="rtl">

                        {{-- الخلفية المظللة --}}
                        <div x-show="showStatusModal" x-transition.opacity.duration.300ms
                            class="absolute inset-0 backdrop-blur-sm bg-gray-900/60 dark:bg-black/70"
                            @click="showStatusModal = false"></div>

                        {{-- المودال نفسه --}}
                        <div x-show="showStatusModal"
                            x-transition:enter="transform transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-full sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="transform transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-full sm:scale-95"
                            class="relative bg-white dark:bg-boxdark w-full max-w-md rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl p-6 sm:p-8 text-center border border-gray-100 dark:border-boxdark-2 max-h-[90vh] overflow-y-auto">

                            {{-- الأيقونة --}}
                            <div
                                class="flex justify-center items-center mx-auto mb-4 w-16 h-16 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-2xl border border-amber-100 dark:border-amber-500/20">
                                <span class="text-3xl material-symbols-outlined">block</span>
                            </div>

                            <h3 class="mb-2 text-xl font-black font-headline text-on-surface dark:text-white">
                                تأكيد إلغاء الراكب</h3>

                            <p class="mb-4 text-xs font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                                هل أنت متأكد من إلغاء الراكب رقم <br>
                                <span class="text-sm font-black text-on-surface dark:text-white font-headline"
                                    x-text="statusPassengerData.passenger_number"></span>؟<br>
                                <span class="text-amber-500/80">سيتم تغيير حالة الراكب إلى ملغي وإزالته من القائمة.</span>
                            </p>

                            {{-- شارة الوسيط --}}
                            <template x-if="statusPassengerData.broker_name">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 mb-4 text-xs font-bold text-emerald-700 bg-emerald-50 dark:bg-emerald-500/10 dark:text-emerald-400 rounded-full border border-emerald-200 dark:border-emerald-500/20">
                                    <span class="material-symbols-outlined text-[15px]">handshake</span>
                                    <span>الوسيط:</span>
                                    <span x-text="statusPassengerData.broker_name"></span>
                                </div>
                            </template>

                            {{-- فورم الإرسال --}}
                            <form @submit.prevent="submitCancelPassenger()" class="space-y-3.5">
                                {{-- حقل رقم هاتف الوسيط (واتساب) --}}
                                <div class="text-right">
                                    <label class="block mb-1 text-xs font-bold text-gray-700 dark:text-gray-300">
                                        <span>رقم هاتف الوسيط (واتساب)</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute right-3.5 top-3.5 material-symbols-outlined text-[18px] text-gray-400">call</span>
                                        <input type="text" name="broker_phone" x-model="statusPassengerData.broker_phone"
                                            placeholder="أدخل رقم الوسيط (مثال: 771234567)"
                                            dir="ltr"
                                            class="p-3 pr-10 w-full text-xs font-bold text-gray-700 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-body placeholder:text-gray-400 text-right">
                                    </div>
                                </div>

                                {{-- حقل سبب الإلغاء --}}
                                <div class="text-right">
                                    <label class="block mb-1 text-xs font-bold text-gray-700 dark:text-gray-300">
                                        <span>سبب الإلغاء</span>
                                        <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                                    </label>
                                    <textarea name="cancel_reason" x-model="statusPassengerData.cancel_reason" rows="2"
                                        placeholder="اكتب سبب إلغاء الحجز..."
                                        class="p-3 w-full text-xs font-bold text-gray-700 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 font-body placeholder:text-gray-400"></textarea>
                                </div>

                                {{-- خيار إرسال واتساب للوسيط --}}
                                <div class="flex items-center gap-2 p-2.5 text-right bg-emerald-50/70 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                                    <input type="checkbox" id="send_whatsapp_cancel_mobile" name="send_whatsapp" value="1" 
                                        x-model="statusPassengerData.send_whatsapp"
                                        class="w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 cursor-pointer">
                                    <label for="send_whatsapp_cancel_mobile" class="text-xs font-bold text-gray-700 dark:text-gray-200 cursor-pointer select-none flex-1">
                                        إرسال إشعار الإلغاء للوسيط عبر واتساب 💬
                                    </label>
                                </div>

                                <div class="flex gap-2.5 pt-2">
                                    <button type="button" @click="showStatusModal = false"
                                        class="flex-1 py-3 text-xs font-black text-gray-600 bg-gray-100 rounded-xl transition-all hover:bg-gray-200 dark:bg-boxdark-2 dark:text-gray-300 dark:hover:bg-gray-700 active:scale-95 font-headline">
                                        تراجع
                                    </button>

                                    <button type="button" @click="submitCancelPassenger()" :disabled="isCancellingPassenger"
                                        class="flex flex-1 gap-1.5 justify-center items-center py-3 text-xs font-black text-white bg-amber-500 rounded-xl shadow-lg transition-all hover:bg-amber-600 shadow-amber-500/30 active:scale-95 font-headline disabled:opacity-70 disabled:cursor-not-allowed">
                                        <span x-show="!isCancellingPassenger">نعم، إلغاء الراكب</span>
                                        <span x-show="isCancellingPassenger"
                                            class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- Toast notification --}}
                <div x-show="editSuccessToast" x-cloak x-transition
                    class="fixed bottom-20 left-4 right-4 z-[10000] bg-emerald-600 text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2 text-xs font-bold">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span x-text="editSuccessMessage"></span>
                </div>
            </div>
        </div>
    </div>

    <datalist id="vehicle_types_list">
        <option value="صالون">
        <option value="باص">
        <option value="نوها">
        <option value="هايلوكس">
        <option value="فوكسي">
        <option value="خصوصي">
    </datalist>

@endsection

@section('script')
    <script>
        function createTripForm(driversList, countriesList, initialPassengers, brokersList) {
            return {
                // بيانات السائق
                drivers: driversList || [],
                countries: countriesList || [],
                brokers: brokersList || [],
                localPhoneNumber: '',
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                showDriverDropdown: false,
                selectedDriverId: null,
                nameInput: '',
                vehicleTypeInput: '',
                isSubmitting: false,

                // بيانات الركاب المتاحة
                passengers: (initialPassengers || []).map(p => ({
                    id: p.id,
                    vehicle_type: p.vehicle_type || '',
                    date: p.date ? (typeof p.date === 'string' ? p.date.substring(0, 10) : p.date) : new Date().toISOString().slice(0, 10),
                    passenger_number: p.passenger_number || '',
                    count: p.count || 1,
                    destination: p.destination || '',
                    pickup_location: p.pickup_location || '',
                    broker_id: p.broker_id || '',
                    broker_name: p.broker ? p.broker.name : (p.broker_name || ''),
                    broker_phone: p.broker ? (p.broker.phone || '') : (p.broker_phone || ''),
                    office_commission: p.office_commission || 0,
                    other_office_commission: p.other_office_commission || 0,
                    note: p.note || ''
                })),
                searchQuery: '',
                selectedBranch: '',
                selectedPassengers: [],

                // مودال تعديل بيانات الراكب
                showEditPassengerModal: false,
                isUpdatingPassenger: false,
                editPassengerError: '',
                editSuccessToast: false,
                editSuccessMessage: '',

                // مودال إلغاء الراكب
                showStatusModal: false,
                isCancellingPassenger: false,
                statusPassengerData: {
                    id: null,
                    passenger_number: '',
                    broker_name: '',
                    broker_phone: '',
                    cancel_reason: '',
                    send_whatsapp: true
                },

                editPassengerData: {
                    id: null,
                    vehicle_type: '',
                    date: '',
                    localPhone: '',
                    selectedCountry: null,
                    openCountryDropdown: false,
                    searchCountryQuery: '',
                    pickup_location: '',
                    destination: '',
                    count: 1,
                    office_commission: 0,
                    other_office_commission: 0,
                    broker_id: '',
                    broker_name: '',
                    brokerSearchQuery: '',
                    showBrokerDropdown: false,
                    note: ''
                },

                init() {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0] || null;
                },

                get filteredCountries() {
                    const query = this.searchCountryQuery.toLowerCase().trim();
                    if (!query) return this.countries;
                    return this.countries.filter(c => String(c.name || '').toLowerCase().includes(query) || String(c.dial_code || '').includes(query));
                },
                get fullPhoneNumber() {
                    const dial = String(this.selectedCountry?.dial_code || '').replace('+', '');
                    const local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '').replace(/^0+/, '');
                    return local ? dial + local : '';
                },
                get filteredDrivers() {
                    const local = String(this.localPhoneNumber || '').replace(/[^\d]/g, '');
                    if (!local) return [];
                    return this.drivers.filter(d => String(d.phone || '').includes(local));
                },
                searchDriver() {
                    const exact = this.drivers.find(d => String(d.phone || '').replace(/[^\d]/g, '') === String(this.fullPhoneNumber).replace(/[^\d]/g, ''));
                    if (exact) {
                        this.selectDriver(exact);
                    } else {
                        this.selectedDriverId = null;
                        this.showDriverDropdown = true;
                    }
                },
                selectDriver(driver) {
                    this.selectedDriverId = driver.id;
                    this.nameInput = driver.name;
                    this.vehicleTypeInput = driver.vehicle_type || '';
                    this.showDriverDropdown = false;
                    
                    const clean = String(driver.phone || '').replace(/[^\d]/g, '');
                    const sorted = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);
                    const country = sorted.find(c => clean.startsWith(c.dial_code.replace('+', '')));
                    if (country) {
                        this.selectedCountry = country;
                        this.localPhoneNumber = clean.substring(country.dial_code.replace('+', '').length);
                    } else {
                        this.localPhoneNumber = clean;
                    }
                },
                resetSelection() {
                    this.selectedDriverId = null;
                    this.nameInput = '';
                    this.vehicleTypeInput = '';
                    this.localPhoneNumber = '';
                    this.showDriverDropdown = false;
                },
                submitTrip(event) {
                    if (this.selectedPassengers.length === 0) {
                        event.preventDefault();
                        alert('يرجى اختيار راكب واحد على الأقل للرحلة.');
                        return;
                    }

                    if (!this.selectedDriverId && (!this.nameInput.trim() || !this.localPhoneNumber.trim())) {
                        event.preventDefault();
                        alert('يرجى تحديد السائق أو إدخال اسمه ورقم هاتفه.');
                        return;
                    }

                    this.isSubmitting = true;
                },

                // فلاتر وتحديد الركاب
                filteredPassengers() {
                    const query = (this.searchQuery || '').toLowerCase().trim();
                    return this.passengers.filter(p => {
                        const matchSearch = query === '' || 
                            String(p.passenger_number || '').includes(query) ||
                            String(p.destination || '').toLowerCase().includes(query) ||
                            String(p.pickup_location || '').toLowerCase().includes(query);
                        
                        const matchBranch = this.selectedBranch === '' || 
                            p.branch_name === this.selectedBranch;

                        return matchSearch && matchBranch;
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
                },
                getPassengerPhoneDetails(number) {
                    if (!number) return { flag: '', localNumber: '', country: null };
                    const cleanNumber = String(number).replace(/[^\d]/g, '');
                    const sorted = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);
                    for (const country of sorted) {
                        const dial = country.dial_code.replace('+', '');
                        if (cleanNumber.startsWith(dial)) {
                            const local = cleanNumber.substring(dial.length);
                            return {
                                flag: country.svg,
                                localNumber: local,
                                country: country
                            };
                        }
                    }
                    const defaultCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                    return {
                        flag: defaultCountry ? defaultCountry.svg : '',
                        localNumber: number,
                        country: defaultCountry
                    };
                },

                // دوال تعديل بيانات الراكب
                get editPassengerFilteredCountries() {
                    const query = (this.editPassengerData.searchCountryQuery || '').toLowerCase().trim();
                    if (!query) return this.countries;
                    return this.countries.filter(c => String(c.name || '').toLowerCase().includes(query) || String(c.dial_code || '').includes(query));
                },
                get editPassengerFullPhone() {
                    const dial = String(this.editPassengerData.selectedCountry?.dial_code || '').replace('+', '');
                    const local = String(this.editPassengerData.localPhone || '').replace(/[^\d]/g, '').replace(/^0+/, '');
                    return local ? dial + local : '';
                },
                get editPassengerFilteredBrokers() {
                    const query = (this.editPassengerData.brokerSearchQuery || '').toLowerCase().trim();
                    if (!query) return [];
                    return this.brokers.filter(b => String(b.name || '').toLowerCase().includes(query));
                },
                openEditPassenger(passenger) {
                    const phoneDetails = this.getPassengerPhoneDetails(passenger.passenger_number);
                    const brokerObj = this.brokers.find(b => b.id == passenger.broker_id);
                    this.editPassengerData = {
                        id: passenger.id,
                        vehicle_type: passenger.vehicle_type || '',
                        date: passenger.date ? (typeof passenger.date === 'string' ? passenger.date.substring(0, 10) : passenger.date) : new Date().toISOString().slice(0, 10),
                        localPhone: phoneDetails.localNumber || '',
                        selectedCountry: phoneDetails.country || this.countries.find(c => c.code === 'YE') || this.countries[0],
                        openCountryDropdown: false,
                        searchCountryQuery: '',
                        pickup_location: passenger.pickup_location || '',
                        destination: passenger.destination || '',
                        count: passenger.count || 1,
                        office_commission: passenger.office_commission || 0,
                        other_office_commission: passenger.other_office_commission || 0,
                        broker_id: passenger.broker_id || '',
                        broker_name: passenger.broker_name || (brokerObj ? brokerObj.name : ''),
                        broker_phone: passenger.broker_phone || (brokerObj ? brokerObj.phone : '') || '',
                        brokerSearchQuery: passenger.broker_name || (brokerObj ? brokerObj.name : ''),
                        showBrokerDropdown: false,
                        note: passenger.note || ''
                    };
                    this.editPassengerError = '';
                    this.showEditPassengerModal = true;
                },
                openStatusModal(passenger) {
                    this.statusPassengerData = {
                        id: passenger.id,
                        passenger_number: passenger.passenger_number || '',
                        broker_name: passenger.broker_name || (passenger.broker ? passenger.broker.name : '') || '',
                        broker_phone: passenger.broker_phone || (passenger.broker ? passenger.broker.phone : '') || '',
                        cancel_reason: '',
                        send_whatsapp: true
                    };
                    this.showStatusModal = true;
                },
                openCancelModalFromEdit() {
                    const brokerObj = this.brokers.find(b => b.id == this.editPassengerData.broker_id);
                    this.statusPassengerData = {
                        id: this.editPassengerData.id,
                        passenger_number: this.editPassengerFullPhone || this.editPassengerData.localPhone,
                        broker_name: this.editPassengerData.broker_name || (brokerObj ? brokerObj.name : '') || '',
                        broker_phone: this.editPassengerData.broker_phone || (brokerObj ? brokerObj.phone : '') || '',
                        cancel_reason: '',
                        send_whatsapp: true
                    };
                    this.showEditPassengerModal = false;
                    this.showStatusModal = true;
                },
                async submitCancelPassenger() {
                    if (!this.statusPassengerData.id) return;

                    this.isCancellingPassenger = true;
                    try {
                        const payload = {
                            status: 'cancel',
                            broker_phone: this.statusPassengerData.broker_phone || '',
                            cancel_reason: this.statusPassengerData.cancel_reason || '',
                            send_whatsapp: this.statusPassengerData.send_whatsapp ? 1 : 0,
                            allow_completed_cancel: true
                        };

                        const response = await fetch(`/passengers/${this.statusPassengerData.id}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (response.ok && (data.success !== false)) {
                            const removedId = this.statusPassengerData.id;
                            // إزالة الراكب من القائمة فوراً
                            this.passengers = this.passengers.filter(p => p.id != removedId);
                            this.selectedPassengers = this.selectedPassengers.filter(id => id != removedId);

                            this.showStatusModal = false;
                            this.showToast(data.message || 'تم إلغاء حجز الراكب وإزالته من الرحلة بنجاح');

                            if (data.whatsapp_url && !data.sent_directly) {
                                setTimeout(() => {
                                    window.open(data.whatsapp_url, '_blank');
                                }, 500);
                            }
                        } else {
                            alert(data.message || 'حدث خطأ أثناء إلغاء الراكب.');
                        }
                    } catch (e) {
                        console.error(e);
                        alert('تعذر الاتصال بالخادم، يرجى المحاولة لاحقاً.');
                    } finally {
                        this.isCancellingPassenger = false;
                    }
                },
                selectEditBroker(broker) {
                    this.editPassengerData.broker_id = broker.id;
                    this.editPassengerData.broker_name = broker.name;
                    this.editPassengerData.brokerSearchQuery = broker.name;
                    this.editPassengerData.broker_phone = broker.phone || '';
                    this.editPassengerData.showBrokerDropdown = false;
                },
                onBrokerInput() {
                    this.editPassengerData.broker_id = '';
                    this.editPassengerData.broker_name = this.editPassengerData.brokerSearchQuery;
                    this.editPassengerData.showBrokerDropdown = true;
                },
                resetEditBroker() {
                    this.editPassengerData.broker_id = '';
                    this.editPassengerData.broker_name = '';
                    this.editPassengerData.broker_phone = '';
                    this.editPassengerData.brokerSearchQuery = '';
                    this.editPassengerData.showBrokerDropdown = false;
                },
                async savePassengerEdit() {
                    const fullPhone = this.editPassengerFullPhone;
                    if (!fullPhone) {
                        this.editPassengerError = 'يرجى إدخال رقم جوال الراكب.';
                        return;
                    }
                    if (!this.editPassengerData.pickup_location) {
                        this.editPassengerError = 'يرجى إدخال مكان الصعود.';
                        return;
                    }
                    if (!this.editPassengerData.count || this.editPassengerData.count < 1) {
                        this.editPassengerError = 'يرجى تحديد عدد الركاب (1 على الأقل).';
                        return;
                    }

                    this.isUpdatingPassenger = true;
                    this.editPassengerError = '';

                    try {
                        const payload = {
                            vehicle_type: this.editPassengerData.vehicle_type || null,
                            date: this.editPassengerData.date,
                            passenger_number: fullPhone,
                            pickup_location: this.editPassengerData.pickup_location,
                            destination: this.editPassengerData.destination,
                            count: parseInt(this.editPassengerData.count) || 1,
                            broker_id: this.editPassengerData.broker_id || null,
                            broker_name: this.editPassengerData.broker_name || 'مباشر',
                            broker_phone: this.editPassengerData.broker_phone || null,
                            office_commission: parseFloat(this.editPassengerData.office_commission) || 0,
                            other_office_commission: parseFloat(this.editPassengerData.other_office_commission) || 0,
                            note: this.editPassengerData.note || '',
                            allow_completed_edit: true
                        };

                        const response = await fetch(`/passengers/${this.editPassengerData.id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (response.ok && (data.success !== false)) {
                            // تحديث بيانات الراكب في المصفوفة فوراً
                            const index = this.passengers.findIndex(p => p.id == this.editPassengerData.id);
                            if (index !== -1) {
                                this.passengers[index].vehicle_type = this.editPassengerData.vehicle_type || '';
                                this.passengers[index].passenger_number = fullPhone;
                                this.passengers[index].date = this.editPassengerData.date;
                                this.passengers[index].pickup_location = this.editPassengerData.pickup_location;
                                this.passengers[index].destination = this.editPassengerData.destination || '';
                                this.passengers[index].count = parseInt(this.editPassengerData.count) || 1;
                                this.passengers[index].broker_id = this.editPassengerData.broker_id;
                                this.passengers[index].broker_name = this.editPassengerData.broker_name;
                                this.passengers[index].broker_phone = this.editPassengerData.broker_phone;
                                this.passengers[index].office_commission = this.editPassengerData.office_commission;
                                this.passengers[index].other_office_commission = this.editPassengerData.other_office_commission;
                                this.passengers[index].note = this.editPassengerData.note;
                            }

                            this.showEditPassengerModal = false;
                            this.showToast('تم تعديل بيانات الراكب بنجاح');
                        } else {
                            this.editPassengerError = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'حدث خطأ أثناء حفظ التعديلات.');
                        }
                    } catch (e) {
                        console.error(e);
                        this.editPassengerError = 'تعذر الاتصال بالخادم، يرجى المحاولة لاحقاً.';
                    } finally {
                        this.isUpdatingPassenger = false;
                    }
                },
                showToast(msg) {
                    this.editSuccessMessage = msg;
                    this.editSuccessToast = true;
                    setTimeout(() => {
                        this.editSuccessToast = false;
                    }, 4000);
                }
            }
        }
    </script>
@endsection