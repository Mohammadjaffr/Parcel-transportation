@extends('mobile.layouts.app')

@section('title', 'إنشاء شحنة جديدة')

@section('content')
    <div class="flex flex-col gap-6 px-4 pb-32 pt-4" x-data="{ 
                                selectedParcels: [], 
                                totalWeight: 0,
                                updateStats() {
                                    let weight = 0;
                                    this.selectedParcels.forEach(p => {
                                        weight += parseFloat(p.weight);
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
                             }">

        {{-- الهيدر --}}
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-4">
                <a href="{{  route('shipmentpackage.outgoing.index')}}"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 active:scale-90 transition-all">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-xl font-black font-headline text-slate-800">تجهيز شحنة</h1>
                </div>
            </div>
        </div>

        <form action="{{ route('shipmentpackage.outgoing.store') }}" method="POST" id="packageForm">
            @csrf

            {{-- حقول الطرود المختارة المخفية --}}
            <template x-for="parcel in selectedParcels" :key="parcel.id">
                <input type="hidden" name="parcel_ids[]" :value="parcel.id">
            </template>

            <div class="space-y-6">

                {{-- بطاقة البيانات الأساسية --}}
                <div
                    class="bg-white p-6 rounded-[2.5rem] border border-slate-50 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] space-y-6 relative overflow-visible">

                    {{-- تزيين خلفي --}}
                    <div
                        class="absolute -top-12 -right-12 w-24 h-24 bg-primary/5 rounded-full blur-3xl pointer-events-none">
                    </div>

                    {{-- ================= السائق المسؤول ================= --}}
                    <div x-data="driverSelect({{ $drivers ?? '[]' }}, @js(array_values(config('countries', []))))"
                        class="p-4 bg-slate-50 rounded-2xl border border-slate-100 z-50">
                        <span class=" -top-2.5 right-4 bg-slate-50 px-2 text-[10px] font-black text-slate-500">السائق
                            المسؤول <span class="text-red-500">*</span></span>

                        <div class="grid grid-cols-1 gap-3 mt-2 relative">
                            <input type="hidden" name="driver_id" x-model="selectedDriverId">
                            <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                            <div class="relative group flex items-center rounded-xl ring-1 transition-all bg-white ring-slate-200 focus-within:ring-2 focus-within:ring-primary/50 focus-within:border-primary overflow-visible"
                                :class="selectedDriverId ? 'bg-primary/5 ring-primary/30' : ''" style="direction: ltr;">

                                <div class="relative h-full" @click.away="openCountryDropdown = false">
                                    <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                        class="flex items-center gap-2 px-3 h-12 bg-slate-50 border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors rounded-l-xl">

                                        {{-- 💡 الحل هنا: استخدام div بدلاً من svg لمنع تداخل الأكواد --}}
                                        <div class="w-5 h-auto flex items-center justify-center rounded-[2px] shadow-sm overflow-hidden"
                                            x-html="selectedCountry?.svg"></div>

                                        <span class="text-xs font-bold text-slate-600"
                                            x-text="selectedCountry?.dial_code"></span>
                                    </button>

                                    <div x-show="openCountryDropdown" x-cloak x-transition
                                        class="absolute top-full left-0 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-100 z-[60] overflow-hidden">
                                        <div class="p-2 border-b border-slate-50">
                                            <input type="text" x-model="searchCountryQuery" placeholder="Search country..."
                                                class="w-full h-8 px-3 text-xs bg-slate-50 rounded-lg outline-none focus:ring-1 ring-primary/30"
                                                dir="ltr">
                                        </div>
                                        <div class="max-h-48 overflow-y-auto" dir="ltr">
                                            <template x-for="country in filteredCountries" :key="country.code">
                                                <button type="button"
                                                    @click="selectedCountry = country; openCountryDropdown = false; searchDriver()"
                                                    class="w-full flex items-center gap-3 px-3 py-2 text-left hover:bg-slate-50 transition-colors">
                                                    <div class="w-5 h-auto flex items-center justify-center rounded-[2px] overflow-hidden"
                                                        x-html="country.svg"></div>
                                                    <span class="text-xs font-bold text-slate-700 flex-1"
                                                        x-text="country.name"></span>
                                                    <span class="text-xs text-slate-400" x-text="country.dial_code"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                {{-- إدخال رقم الهاتف مع تقييد عدد الخانات ديناميكياً --}}
                                <input type="tel" x-model="localPhoneNumber" @input="searchDriver"
                                    @focus="showDriverDropdown = true" @click.away="showDriverDropdown = false"
                                    placeholder="7XXXXXXXX" required inputmode="numeric" autocomplete="off" {{-- 💡 التقييد
                                    الذكي: إذا كان مفتاح اليمن، أقصى طول 9، وإلا 15 كحد أقصى عام --}}
                                    :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 px-4 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-slate-800"
                                    :class="selectedDriverId ? 'font-bold text-primary' : ''">

                                <button type="button" x-show="selectedDriverId" @click="resetSelection"
                                    class="absolute right-3 text-slate-400 hover:text-red-500 z-10 bg-white p-0.5 rounded-full">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>

                            <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId"
                                x-transition x-cloak
                                class="absolute top-[3.25rem] right-0 w-full bg-white border border-slate-100 rounded-xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden max-h-48 overflow-y-auto z-50">
                                <template x-for="driver in filteredDrivers" :key="driver.id">
                                    <button type="button" @click="selectDriver(driver)"
                                        class="w-full text-right px-4 py-3 hover:bg-slate-50 border-b border-slate-50 flex items-center justify-between transition-colors">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-bold text-sm text-slate-800" x-text="driver.name"></span>
                                            <span class="text-xs text-slate-500 dir-ltr text-right"
                                                x-text="driver.phone"></span>
                                        </div>
                                        <span
                                            class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                                    </button>
                                </template>
                                <div x-show="filteredDrivers.length === 0" class="px-4 py-3 bg-slate-50/50 text-center">
                                    <span class="text-xs font-bold text-slate-500">سائق جديد، سيتم حفظه.</span>
                                </div>
                            </div>

                            <input type="text" name="driver_name" x-model="nameInput" :readonly="selectedDriverId !== null"
                                placeholder="اسم السائق..." required
                                class="w-full h-12 px-4 text-sm rounded-xl border outline-none transition-colors"
                                :class="selectedDriverId ? 'bg-slate-50 border-transparent text-slate-500 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/10 text-slate-700'">
                        </div>
                    </div>
                </div>

                {{-- قسم اختيار الطرود --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h3 class="font-black text-slate-800 font-headline flex items-center gap-2">
                            الطرود المتاحة
                            <span class="bg-primary/10 text-primary text-[10px] px-2 py-0.5 rounded-full"
                                x-text="selectedParcels.length">0</span>
                        </h3>
                        <button type="button"
                            @click="selectedParcels = []; $el.closest('form').querySelectorAll('input[type=checkbox]').forEach(el => el.checked = false); updateStats()"
                            class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2.5 py-1 rounded-lg active:scale-95 transition-all">إلغاء
                            التحديد</button>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        @forelse($pendingParcels as $parcel)
                            <label class="relative block group cursor-pointer">
                                {{-- 💡 لاحظ أننا ما زلنا نمرر الوزن (إن وجد) لدالة toggleParcel لكي يعمل إجمالي الوزن في
                                الأسفل، أو يمكنك مسحه إذا لم تعد بحاجة للإجمالي --}}
                                <input type="checkbox" class="hidden peer"
                                    @change="toggleParcel({{ $parcel->id }}, {{ $parcel->weight ?? 0 }})">

                                <div
                                    class="bg-white p-4 rounded-3xl border border-slate-100 shadow-[0_4px_20px_rgb(0,0,0,0.02)] peer-checked:ring-2 peer-checked:ring-primary peer-checked:border-transparent peer-checked:bg-primary/[0.02] transition-all flex items-center gap-4 active:scale-[0.98]">

                                    {{-- أيقونة الطرد --}}
                                    <div
                                        class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 peer-checked:bg-primary peer-checked:text-white shrink-0 transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        {{-- السطر الأول: رقم السند + اسم الفرع الوجهة بدلاً من الوزن --}}
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span
                                                class="text-sm font-black text-slate-800 font-mono tracking-tight block truncate">{{ $parcel->bond_number }}</span>

                                            {{-- 💡 شارة الفرع المستلم --}}
                                            <span
                                                class="text-[10px] font-black text-primary bg-primary/10 px-2 py-0.5 rounded-md flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">store</span>
                                                {{ $parcel->receiverBranch->name ?? 'فرع غير محدد' }}
                                            </span>
                                        </div>

                                        {{-- السطر الثاني: العنوان + العميل المستلم --}}
                                        <div class="flex flex-col gap-1">
                                            {{-- العنوان --}}
                                            <div class="flex items-center gap-1">
                                                <span
                                                    class="material-symbols-outlined text-[12px] text-slate-400">location_on</span>
                                                <span
                                                    class="text-[10px] font-bold text-slate-500 block truncate">{{ $parcel->receiverBranch->address ?? 'العنوان غير مسجل' }}</span>
                                            </div>
                                            {{-- اسم العميل --}}
                                            <div class="flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px] text-slate-400">person</span>
                                                <span class="text-[10px] font-bold text-slate-500 block truncate">لـ:
                                                    {{ $parcel->receiverCustomer->name ?? 'عميل غير محدد' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="w-6 h-6 rounded-full border-2 border-slate-200 flex items-center justify-center transition-all peer-checked:bg-primary peer-checked:border-primary shrink-0">
                                        <span
                                            class="material-symbols-outlined text-white text-[16px] hidden peer-checked:block">check</span>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div
                                class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center text-slate-400">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-[32px] opacity-30">inbox_customize</span>
                                </div>
                                <p class="text-xs font-bold text-slate-500">لا توجد طرود بانتظار الشحن حالياً</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- قسم الإرسال العادي في أسفل الصفحة --}}
            <div class="mt-8 mb-4 p-6 bg-white rounded-[2.5rem] border border-slate-50 shadow-[0_15px_50px_-15px_rgba(0,0,0,0.05)] flex flex-col sm:flex-row items-center justify-between gap-6"
                x-data="{ isSubmitting: false }">

                {{-- زر الإرسال مع حالة التحميل --}}
                <button type="submit" {{-- 💡 الحل هنا: استخدام setTimeout لتأخير التعطيل 50 جزء من الثانية --}}
                    @click="if(selectedParcels.length > 0 && $el.closest('form').checkValidity()) { setTimeout(() => isSubmitting = true, 50); }"
                    :disabled="selectedParcels.length === 0 || isSubmitting" class="w-full sm:w-auto h-14 px-8 bg-slate-900 text-white rounded-2xl font-black text-sm
                shadow-[0_10px_25px_rgba(15,23,42,0.3)] disabled:bg-slate-100 disabled:text-slate-400
                disabled:shadow-none transition-all active:scale-95 flex items-center justify-center gap-3">

                    {{-- حالة التحميل (تظهر فقط عند الإرسال) --}}
                    <template x-if="isSubmitting">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                            <span>جاري إنشاء الإرسالية...</span>
                        </div>
                    </template>

                    {{-- الحالة الطبيعية (تختفي عند الإرسال) --}}
                    <template x-if="!isSubmitting">
                        <div class="flex items-center gap-3">
                            <span>إنشاء الإرسالية</span>
                            <div class="w-7 h-7 bg-white/20 rounded-xl flex items-center justify-center">
                                <span class="text-[12px]" x-text="selectedParcels.length">0</span>
                            </div>
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
                    // هنا نحدد اليمن كدولة افتراضية ليظهر علمها ومفتاحها فور فتح الصفحة
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