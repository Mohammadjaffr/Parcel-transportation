@extends('layouts.app')

@section('title', 'إنشاء إرسالية جديدة')
@section('Breadcrumb', 'إدارة الشحنات / إنشاء إرسالية')

@section('content')
<div x-data="{ 
        selectedParcels: [], 
        totalWeight: 0,
        updateStats() {
            let weight = 0;
            this.selectedParcels.forEach(p => {
                weight += parseFloat(p.weight || 0);
            });
            this.totalWeight = weight.toFixed(2);
        },
        toggleParcel(id, weight) {
            const index = this.selectedParcels.findIndex(p => p.id === id);
            if (index > -1) {
                this.selectedParcels.splice(index, 1);
            } else {
                this.selectedParcels.push({id: id, weight: weight});
            }
            this.updateStats();
        }
    }" class="flex relative flex-col gap-6 p-4 rounded-3xl bg-surface dark:bg-boxdark-2 lg:p-6 font-body" dir="rtl">

    {{-- الهيدر العلوي --}}
    <div class="flex justify-between items-center mt-6 p-4 bg-white rounded-2xl border border-gray-100 dark:bg-boxdark dark:border-boxdark-2 shadow-sm lg:p-6">
        <div class="flex gap-4 items-center">
            <a href="{{ route('shipmentpackage.outgoing.index') }}"
                class="flex justify-center items-center w-12 h-12 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 hover:text-primary dark:border-boxdark active:scale-90">
                <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
            </a>
            <div>
                <h1 class="text-2xl font-black md:text-3xl font-headline text-on-surface dark:text-white">إنشاء إرسالية شحنات</h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-bodydark">اختر السائق والطرود المتجهة معه لإنشاء خط سير الرحلة</p>
            </div>
        </div>
    </div>

    <form action="{{ route('shipmentpackage.outgoing.store') }}" method="POST" id="packageForm" class="flex relative flex-col gap-6">
        @csrf
        <template x-for="parcel in selectedParcels" :key="parcel.id">
            <input type="hidden" name="parcel_ids[]" :value="parcel.id">
        </template>

        {{-- تخطيط شبكي --}}
        <div class="grid grid-cols-1 gap-6 items-start lg:grid-cols-12">
            
            {{-- ================= الجانب الأيمن: بيانات السائق (Col span 4) ================= --}}
            <div class="lg:col-span-4 border border-gray-100 dark:border-boxdark-2 bg-white dark:bg-boxdark p-6 rounded-[2rem] shadow-theme-sm lg:sticky lg:top-24 z-30">
                <h3 class="flex gap-2 items-center mb-6 text-lg font-black font-headline text-on-surface dark:text-white">
                    <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-primary/10 text-primary">
                        <span class="material-symbols-outlined text-[22px]">person</span>
                    </div>
                    معلومات السائق
                </h3>

                <div x-data="driverSelect({{ json_encode($drivers ?? []) }}, {{ json_encode(array_values(config('countries', []))) }})" class="relative">
                    <input type="hidden" name="driver_id" x-model="selectedDriverId">
                    <input type="hidden" name="driver_phone" :value="fullPhoneNumber">
                    
                    <div class="space-y-6">
                        {{-- رقم الهاتف --}}
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم هاتف السائق <span class="text-error">*</span></label>
                            
                            <div class="flex relative items-center h-14 rounded-xl ring-1 ring-gray-200 transition-all dark:ring-boxdark-2 bg-gray-50 dark:bg-boxdark-2 focus-within:ring-2 focus-within:ring-primary/50" 
                                 :class="selectedDriverId ? 'bg-primary-container dark:bg-primary/10 ring-primary/30' : ''" style="direction: ltr;">
                                
                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button" @click="openCountryDropdown = !openCountryDropdown" class="flex gap-2 items-center px-3 h-full bg-white rounded-l-xl border-r border-gray-100 transition-colors dark:bg-boxdark dark:border-boxdark-2 shrink-0 hover:bg-gray-50 dark:hover:bg-boxdark-2 shadow-sm">
                                        <div class="w-5 flex justify-center shadow-sm rounded-[2px] overflow-hidden" x-html="selectedCountry?.svg"></div>
                                        <span class="text-sm font-bold text-gray-600 dark:text-gray-300" x-text="selectedCountry?.dial_code"></span>
                                    </button>
                                    
                                    <div x-show="openCountryDropdown" x-cloak x-transition class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-2xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                        <div class="p-3 border-b border-gray-100 dark:border-boxdark">
                                            <input type="text" x-model="searchCountryQuery" placeholder="بحث..." class="px-3 py-2.5 w-full text-sm rounded-xl border border-gray-200 outline-none bg-gray-50 dark:bg-boxdark dark:border-boxdark-2 dark:text-white focus:ring-1 focus:ring-primary/30" dir="rtl">
                                        </div>
                                        <div class="overflow-y-auto max-h-56 custom-scrollbar" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button" @click="selectedCountry = country; openCountryDropdown = false; searchDriver()" class="flex gap-3 items-center px-4 py-3 w-full transition-colors hover:bg-gray-50 dark:hover:bg-boxdark">
                                                    <div class="w-6 shadow-sm rounded-[2px] overflow-hidden" x-html="country.svg"></div>
                                                    <span class="flex-1 text-sm font-semibold text-left dark:text-white" x-text="country.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="tel" x-model="localPhoneNumber" @input="searchDriver" @focus="showDriverDropdown = true" @click.away="showDriverDropdown = false" placeholder="7XXXXXXXX" required autocomplete="off" :maxlength="selectedCountry?.code === 'YE' ? 9 : 15" 
                                       class="flex-1 px-4 w-full text-sm bg-transparent border-0 ring-0 outline-none font-bold text-on-surface dark:text-white dark:placeholder-gray-500"
                                       :class="selectedDriverId ? 'text-primary' : ''">
                                
                                <button type="button" x-show="selectedDriverId" @click="resetSelection" class="absolute right-3 z-10 p-1.5 text-gray-400 rounded-full transition-colors hover:text-error bg-white/80 dark:bg-boxdark/80 shadow-sm border border-gray-100 dark:border-boxdark">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>
                            
                            {{-- Dropdown للسائقين --}}
                            <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId" x-cloak x-transition class="absolute top-[5.5rem] right-0 w-full bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-2xl shadow-xl z-50 max-h-56 overflow-hidden">
                                <div class="overflow-y-auto max-h-56 custom-scrollbar">
                                    <template x-for="driver in filteredDrivers" :key="driver.id">
                                        <button type="button" @click="selectDriver(driver)" class="flex justify-between items-center px-5 py-4 w-full text-right border-b last:border-0 border-gray-50 transition-colors hover:bg-gray-50 dark:hover:bg-boxdark-2 dark:border-boxdark">
                                            <div class="flex flex-col gap-0.5">
                                                <div class="text-sm font-black text-gray-800 dark:text-white" x-text="driver.name"></div>
                                                <div class="text-[11px] font-bold text-gray-400" x-text="driver.phone"></div>
                                            </div>
                                            <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[20px]">chevron_left</span>
                                        </button>
                                    </template>
                                    <div x-show="filteredDrivers.length === 0" class="px-5 py-6 text-sm font-bold text-center text-gray-400 dark:text-bodydark bg-gray-50/50 dark:bg-boxdark-2">
                                        سائق جديد، سيتم حفظه تلقائياً.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- اسم السائق --}}
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اسم السائق <span class="text-error">*</span></label>
                            <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedDriverId !== null" placeholder="ادخل اسم السائق الكامل..." required 
                                   class="px-4 w-full h-14 text-sm font-bold rounded-xl border border-gray-200 transition-all outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white" 
                                   :class="selectedDriverId ? 'bg-gray-50 dark:bg-boxdark text-gray-500 dark:text-gray-400 cursor-not-allowed border-transparent opacity-80' : 'bg-white focus:border-primary focus:ring-2 focus:ring-primary/20'">
                        </div>

                        {{-- إحصائيات سريعة --}}
                        <div class="pt-6 mt-6 border-t border-gray-100 dark:border-boxdark flex flex-col gap-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-400">عدد الطرود:</span>
                                <span class="text-sm font-black text-primary" x-text="selectedParcels.length">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-400">إجمالي الوزن (تقديري):</span>
                                <span class="text-sm font-black text-on-surface dark:text-white"><span x-text="totalWeight">0</span> كجم</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= الجانب الأيسر: الطرود (Col span 8) ================= --}}
            <div class="lg:col-span-8 border border-gray-100 dark:border-boxdark-2 bg-white dark:bg-boxdark p-6 rounded-[2rem] shadow-theme-sm">
                <div class="flex justify-between items-center mb-8 bg-gray-50/50 dark:bg-boxdark-2/50 p-4 rounded-2xl border border-gray-100 dark:border-boxdark">
                    <h3 class="flex gap-2 items-center text-lg font-black font-headline text-on-surface dark:text-white">
                        <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                        </div>
                        الطرود المتاحة للشحن
                        <span class="px-2.5 py-1 ml-1 text-xs font-black rounded-lg bg-primary text-white shadow-sm shadow-primary/20" x-text="selectedParcels.length">0</span>
                    </h3>
                    <button type="button" @click="selectedParcels = []; $el.closest('form').querySelectorAll('input[type=checkbox]').forEach(el => el.checked = false); updateStats()" 
                            class="flex gap-1.5 items-center px-5 py-2.5 text-xs font-black rounded-xl transition-all text-error bg-error/10 hover:bg-error/20 active:scale-95 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">deselect</span>
                        إلغاء الكل
                    </button>
                </div>

                {{-- شبكة الطرود --}}
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @forelse($pendingParcels as $parcel)
                        <label class="block relative h-full cursor-pointer group">
                            <input type="checkbox" class="hidden peer" @change="toggleParcel({{ $parcel->id }}, {{ $parcel->weight ?? 0 }})">
                            <div class="flex overflow-hidden relative flex-col gap-5 p-6 h-full rounded-[2rem] border border-gray-100 shadow-sm transition-all bg-white dark:bg-boxdark-2 dark:border-boxdark peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent peer-checked:bg-primary/5 hover:shadow-lg hover:border-primary/30">
                                
                                {{-- الزخرفة --}}
                                <div class="absolute inset-0 bg-gradient-to-br to-transparent opacity-0 transition-opacity pointer-events-none from-primary/5 peer-checked:opacity-100"></div>
                                
                                <div class="flex relative z-10 justify-between items-start">
                                    <div class="flex gap-4 items-center">
                                        <div class="flex justify-center items-center w-14 h-14 text-gray-400 bg-gray-50 rounded-2xl border border-gray-100 transition-all dark:bg-boxdark dark:border-boxdark peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary shadow-sm group-hover:scale-105">
                                            <span class="material-symbols-outlined text-[28px]">package_2</span>
                                        </div>
                                        <div>
                                            <div class="font-mono text-base font-black tracking-tight text-on-surface dark:text-white leading-none">{{ $parcel->bond_number }}</div>
                                            <div class="flex gap-1 items-center px-2 py-1 mt-2 w-max text-[10px] font-black text-primary bg-primary/5 rounded-lg border border-primary/10">
                                                <span class="material-symbols-outlined text-[14px]">store</span>
                                                {{ $parcel->receiverBranch->name ?? 'غير محدد' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-center items-center w-7 h-7 bg-gray-50 rounded-full border-2 border-gray-100 transition-all dark:border-boxdark peer-checked:bg-primary peer-checked:border-primary shrink-0 dark:bg-boxdark shadow-inner">
                                        <span class="material-symbols-outlined text-white text-[18px] hidden peer-checked:block">check</span>
                                    </div>
                                </div>

                                <div class="relative z-10 pt-5 mt-auto border-t border-gray-100 dark:border-boxdark">
                                    <div class="space-y-3">
                                        <div class="flex gap-2.5 items-center">
                                            <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-boxdark flex items-center justify-center text-gray-400 shrink-0">
                                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                            </div>
                                            <span class="text-xs font-bold text-gray-500 dark:text-bodydark truncate leading-none">
                                                {{ $parcel->receiverBranch->address ?? 'العنوان غير مسجل' }}
                                            </span>
                                        </div>
                                        <div class="flex gap-2.5 items-center">
                                            <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-boxdark flex items-center justify-center text-gray-400 shrink-0">
                                                <span class="material-symbols-outlined text-[16px]">person</span>
                                            </div>
                                            <span class="text-xs font-bold text-gray-500 dark:text-bodydark truncate leading-none">
                                                لـ: {{ $parcel->receiverCustomer->name ?? 'غير محدد' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="flex flex-col col-span-full justify-center items-center py-24 text-gray-400 rounded-[2.5rem] border-2 border-gray-100 border-dashed bg-gray-50/30 dark:bg-boxdark-2/30 dark:border-boxdark">
                            <div class="flex justify-center items-center mb-6 w-24 h-24 bg-white rounded-full shadow-theme-sm dark:bg-boxdark border border-gray-100 dark:border-boxdark">
                                <span class="material-symbols-outlined text-[48px] text-gray-300 dark:text-gray-700">inbox_customize</span>
                            </div>
                            <p class="text-lg font-black text-gray-500 dark:text-gray-500">لا توجد طرود حالياً</p>
                            <p class="text-sm font-bold text-gray-400 mt-1">جميع الطرود تم شحنها أو لا تتوفر شحنات جديدة.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ================= شريط الإجراءات العائم ================= --}}
        <div class="fixed bottom-8 left-1/2 -translate-x-1/2 w-[90%] lg:w-[calc(66.666%-4rem)] lg:left-[calc(33.333%+2rem)] lg:translate-x-0 p-5 bg-white/95 dark:bg-boxdark/95 backdrop-blur-md border border-gray-100 dark:border-boxdark-2 rounded-[2.5rem] shadow-theme-xl flex justify-between items-center z-40 transition-all duration-300" 
             x-data="{ isSubmitting: false }"
             :class="selectedParcels.length > 0 ? 'translate-y-0 opacity-100' : 'translate-y-2 opacity-95'">
            
            <div class="flex gap-5 items-center px-2">
                <div class="hidden md:flex justify-center items-center w-12 h-12 rounded-2xl bg-primary text-white shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[24px]">summarize</span>
                </div>
                <div>
                    <div class="text-[10px] font-black tracking-widest text-gray-400 uppercase dark:text-bodydark mb-0.5">الملخص</div>
                    <div class="text-lg font-black text-on-surface dark:text-white leading-none">
                        <span x-text="selectedParcels.length">0</span> <span class="text-xs text-gray-400">طرد محدد</span>
                    </div>
                </div>
            </div>
            
            <button type="submit" 
                @click="if(selectedParcels.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                :disabled="selectedParcels.length === 0 || isSubmitting" 
                class="flex gap-3 justify-center items-center h-14 px-8 min-w-[200px] text-sm font-black text-white rounded-2xl shadow-xl transition-all bg-primary hover:bg-primary-hover hover:scale-[1.02] shadow-primary/30 disabled:bg-gray-100 disabled:dark:bg-boxdark-2 disabled:text-gray-400 disabled:shadow-none active:scale-95 disabled:cursor-not-allowed">
                
                <template x-if="isSubmitting">
                    <div class="flex gap-2 items-center">
                        <span class="material-symbols-outlined animate-spin text-[22px]">progress_activity</span>
                        <span>جاري الحفظ...</span>
                    </div>
                </template>
                <template x-if="!isSubmitting">
                    <div class="flex gap-2 items-center">
                        <span class="material-symbols-outlined text-[22px]">rocket_launch</span>
                        <span>تأكيد الإرسال</span>
                    </div>
                </template>
            </button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('driverSelect', (initialDrivers, countries) => ({
            drivers: initialDrivers || [],
            countries: countries || [],
            filteredDrivers: [],
            selectedCountry: null,
            openCountryDropdown: false,
            searchCountryQuery: '',
            localPhoneNumber: '',
            nameInput: '',
            selectedDriverId: null,
            showDriverDropdown: false,
 
            init() {
                this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
            },
 
            get filteredCountries() {
                if (this.searchCountryQuery === '') return this.countries;
                return this.countries.filter(c => c.name.toLowerCase().includes(this.searchCountryQuery.toLowerCase()) || c.dial_code.includes(this.searchCountryQuery));
            },
 
            get fullPhoneNumber() {
                if (!this.selectedCountry || !this.localPhoneNumber) return '';
                let cleanLocal = this.localPhoneNumber.replace(/^0+/, '');
                return this.selectedCountry.dial_code + cleanLocal;
            },
 
            searchDriver() {
                this.selectedDriverId = null;
                this.nameInput = '';
                if (this.localPhoneNumber.length < 3) {
                    this.filteredDrivers = [];
                    return;
                }
                let currentFullPhone = this.fullPhoneNumber;
                this.filteredDrivers = this.drivers.filter(driver => {
                    return driver.phone.includes(currentFullPhone) || driver.phone.includes(this.localPhoneNumber);
                });
            },
 
            selectDriver(driver) {
                this.selectedDriverId = driver.id;
                this.nameInput = driver.name;
                let phone = driver.phone;
                let matchedCountry = this.countries.find(c => phone.startsWith(c.dial_code));
                if (matchedCountry) {
                    this.selectedCountry = matchedCountry;
                    this.localPhoneNumber = phone.substring(matchedCountry.dial_code.length);
                } else {
                    this.localPhoneNumber = phone;
                }
                this.showDriverDropdown = false;
            },
 
            resetSelection() {
                this.selectedDriverId = null;
                this.nameInput = '';
                this.localPhoneNumber = '';
                this.filteredDrivers = [];
                setTimeout(() => this.$el.querySelector('input[type="tel"]').focus(), 50);
            }
        }));
    });
</script>
@endsection