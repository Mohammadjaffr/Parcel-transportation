@extends('mobile.layouts.app')

@section('title', 'الركاب')

@section('content')
<div x-data="{
    searchQuery: '',
    showCreateModal: false,
    showEditModal: false,
    showDeleteModal: false,
    deletePassengerData: {
        id: null,
        passenger_number: '',
        url: ''
    },
    editPassengerData: {
        id: null,
        date: '',
        day: '',
        passenger_number: '',
        location: '',
        count: null,
        total_commission: null,
        broker: '',
        driver_id: null,
        driver_name: '',
        driver_phone: '',
        note: '',
        url: ''
    },
    openDeleteModal(id, passengerNumber) {
        this.deletePassengerData = { id: id, passenger_number: passengerNumber, url: '{{ url('passengers') }}/' + id };
        this.showDeleteModal = true;
    },
    openEditModal(id, date, day, passengerNumber, location, count, totalCommission, broker, driverId, driverName, driverPhone, note) {
        this.editPassengerData = {
            id: id, date: date, day: day, passenger_number: passengerNumber, location: location,
            count: count, total_commission: totalCommission, broker: broker || '', driver_id: driverId,
            driver_name: driverName || '', driver_phone: driverPhone || '', note: note || '', url: '{{ url('passengers') }}/' + id
        };
        this.showEditModal = true;
    },
    closeModals() {
        this.showCreateModal = false;
        this.showEditModal = false;
        this.showDeleteModal = false;
    }
}" class="flex relative flex-col gap-6 pb-24 min-h-screen">

    <!-- Header Section -->
    <div class="flex justify-between items-center px-2 mt-2">
        <div>
            <h1 class="text-2xl font-black tracking-tight font-headline text-slate-800">الركاب</h1>
            <p class="mt-0.5 text-xs font-semibold text-slate-400">
                إجمالي <span class="text-primary">{{ $passengers->total() ?? 0 }}</span> راكب مسجل
            </p>
        </div>
        <button type="button" @click="showCreateModal = true" 
            class="flex justify-center items-center w-12 h-12 text-white rounded-2xl shadow-xl transition-all bg-primary shadow-primary/20 active:scale-95">
            <span class="text-2xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">person_add</span>
        </button>
    </div>

    <!-- Search Bar Section -->
    <div class="px-2">
        <div class="relative group">
            <span class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</span>
            <input type="text" x-model="searchQuery" 
                placeholder="ابحث برقم الراكب أو المكان..."
                class="w-full h-14 pr-12 pl-12 rounded-[1.25rem] border-none bg-white shadow-sm ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 transition-all font-headline text-sm text-slate-700 outline-none">
            
            <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                class="flex absolute left-4 top-1/2 justify-center items-center w-8 h-8 rounded-xl transition-transform -translate-y-1/2 bg-slate-50 text-slate-400 active:scale-95">
                <span class="text-lg material-symbols-outlined">close</span>
            </button>
        </div>
    </div>

    <!-- List Grid -->
    <div class="px-2 space-y-4">
        @forelse($passengers as $passenger)
            <div x-show="searchQuery === '' || '{{ $passenger->passenger_number }}'.includes(searchQuery) || '{{ $passenger->location }}'.includes(searchQuery)"
                class="bg-white rounded-[1.75rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-50 relative overflow-hidden active:scale-[0.98] transition-all">
                
                <!-- Top Info -->
                <div class="flex relative z-10 gap-4 items-center mb-4">
                    <div class="flex justify-center items-center w-14 h-14 text-lg font-black bg-gradient-to-br rounded-2xl border shadow-inner from-primary/10 to-primary/5 text-primary font-headline border-primary/5 shrink-0">
                         <span class="material-symbols-outlined text-3xl">group</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="mb-1.5 text-base font-bold leading-none truncate font-headline text-slate-800">{{ $passenger->passenger_number }}</h3>
                        <div class="flex gap-2 items-center text-slate-500">
                            <span class="material-symbols-outlined text-[16px] text-primary/60">location_on</span>
                            <span class="text-xs font-bold">{{ $passenger->location }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('passengers.show', $passenger->id) }}"
                        class="flex justify-center items-center w-10 h-10 rounded-xl transition-all bg-indigo-50 text-indigo-500 hover:bg-indigo-100 active:scale-90">
                        <span class="text-xl material-symbols-outlined">visibility</span>
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4 p-3 bg-slate-50/50 rounded-2xl border border-slate-100">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-400 mb-0.5">العدد والعمولة</span>
                        <span class="text-xs font-bold text-slate-700">{{ $passenger->count }} ركاب | <span class="text-amber-500">{{ $passenger->total_commission }}</span></span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-400 mb-0.5">التاريخ واليوم</span>
                        <span class="text-xs font-bold text-slate-700">{{ $passenger->date }} - {{ $passenger->day }}</span>
                    </div>
                    <div class="flex flex-col col-span-2 pt-2 mt-1 border-t border-slate-100/80">
                        <span class="text-[10px] font-bold text-slate-400 mb-0.5">السائق</span>
                        <span class="text-xs font-bold text-primary">{{ $passenger->driver->name ?? 'غير محدد' }}</span>
                    </div>
                </div>

                <!-- Divider -->
                <div class="mb-4 h-px bg-gradient-to-r from-transparent to-transparent via-slate-100"></div>

                <!-- Actions -->
                <div class="flex relative z-10 justify-between items-center">
                    <button type="button" @click="openEditModal({{ $passenger->id }}, {{ json_encode($passenger->date) }}, {{ json_encode($passenger->day) }}, {{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->location) }}, {{ $passenger->count ?? 'null' }}, {{ $passenger->total_commission ?? 'null' }}, {{ json_encode($passenger->broker) }}, {{ json_encode($passenger->driver_id) }}, {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($passenger->note) }})"
                        class="flex gap-2 items-center px-4 py-2 text-xs font-bold rounded-xl border transition-transform bg-primary/5 text-primary font-headline active:scale-95 border-primary/5">
                        <span class="text-sm material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">edit_square</span>
                        تعديل
                    </button>
                     
                    <button type="button" @click="openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                        class="flex justify-center items-center w-10 h-10 text-red-500 bg-red-50 rounded-xl transition-all hover:bg-red-100 active:scale-90">
                        <span class="text-xl material-symbols-outlined">delete_outline</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2 shadow-sm">
                <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                    <span class="text-6xl material-symbols-outlined">group_off</span>
                </div>
                <p class="text-lg font-bold font-headline text-slate-400">لم نعثر على أي ركاب</p>
            </div>
        @endforelse

        <div x-show="searchQuery !== '' && !Array.from(document.querySelectorAll('.space-y-4 > div[x-show]')).some(el => el.style.display !== 'none')" 
             style="display: none;"
             class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2 shadow-sm">
            <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                <span class="text-6xl material-symbols-outlined">search_off</span>
            </div>
            <p class="text-lg font-bold font-headline text-slate-400">لا يوجد نتائج للبحث</p>
        </div>
    </div>

    <div class="px-2 mt-4" x-show="searchQuery === ''">
        {{ $passengers->links('vendor.pagination.mobile') }}
    </div>

    {{-- Modals Includes --}}
    @include('mobile.pages.passengers.model.create')
    @include('mobile.pages.passengers.model.index')

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('driverSelect', ({ drivers, countries, initialId = null, initialName = '', initialPhone = '' }) => ({
            drivers: drivers || [],
            countries: countries || [],
            openDropdown: false,
            searchCountry: '',
            selectedCountry: null,
            phone: '',
            fullPhone: '',
            driverName: initialName,
            selectedDriverId: initialId,
            isExistingDriver: initialId !== null,
            filteredDrivers: [],
            showDriverDropdown: false,

            init() {
                const yemen = this.countries.find(c => c.dial_code === '+967');
                this.selectedCountry = yemen || (this.countries.length > 0 ? this.countries[0] : { dial_code: '+967' });

                if (initialPhone) {
                    let matchedCountry = this.countries.find(c => initialPhone.startsWith(c.dial_code));
                    if (matchedCountry) {
                        this.selectedCountry = matchedCountry;
                        this.phone = initialPhone.substring(matchedCountry.dial_code.length);
                    } else {
                        this.phone = initialPhone;
                    }
                    this.updateFullPhone();
                    // We don't automatically trigger search on init
                }
            },

            selectCountry(country) {
                this.selectedCountry = country;
                this.openDropdown = false;
                this.updateFullPhone();
                this.searchDriver();
            },

            handlePhoneInput() {
                this.phone = this.phone.replace(/[^0-9]/g, '');
                this.updateFullPhone();
                this.searchDriver();
            },

            updateFullPhone() {
                this.fullPhone = this.phone ? (this.selectedCountry.dial_code + this.phone) : '';
            },

            searchDriver() {
                this.selectedDriverId = null;
                this.isExistingDriver = false;

                if (this.phone.trim() === '') {
                    this.filteredDrivers = [];
                    this.showDriverDropdown = false;
                    // Don't clear driverName if we're just backspacing the phone? Actually yes, clear it
                    if (!initialId) {
                        this.driverName = '';
                    }
                    return;
                }

                let query = this.fullPhone.trim();

                this.filteredDrivers = this.drivers.filter(d => {
                    return d.phone && String(d.phone).includes(query);
                });

                this.showDriverDropdown = true;
                
                // Optional: Auto-fill if exact match is found, but keep dropdown open
                const exactMatch = this.drivers.find(d => d.phone === query);
                if (exactMatch && !this.showDriverDropdown) {
                    this.selectDriver(exactMatch);
                }
            },

            selectDriver(driver) {
                this.selectedDriverId = driver.id;
                this.driverName = driver.name;
                this.isExistingDriver = true;
                this.showDriverDropdown = false;

                let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';
                let searchDialCode = this.selectedCountry.dial_code;

                if (driver.phone && driver.phone.startsWith(searchDialCode)) {
                    this.phone = driver.phone.substring(searchDialCode.length);
                } else if (driver.phone && driver.phone.startsWith(dialCode)) {
                    this.phone = driver.phone.substring(dialCode.length);
                } else {
                    this.phone = driver.phone;
                }
                this.updateFullPhone();
            },

            resetSelection() {
                this.selectedDriverId = null;
                this.phone = '';
                this.driverName = '';
                this.isExistingDriver = false;
                this.showDriverDropdown = false;
                this.updateFullPhone();
            }
        }));
    });
</script>
@endsection
