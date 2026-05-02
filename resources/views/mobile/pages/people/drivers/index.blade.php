@extends('mobile.layouts.app')

@section('title', 'إدارة السائقين')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false, // مودل الحذف الجديد
        searchQuery: '', 
        
        editDriverData: { id: '', name: '', phone: '', url: '' },
        deleteDriverData: { id: '', name: '', url: '' }, // بيانات السائق المراد حذفه

        openEditModal(id, name, phone) {
            this.editDriverData = { 
                id: id, 
                name: name, 
                phone: phone, 
                url: '{{ route('drivers.index') }}/' + id
            };
            this.showEditModal = true;
        },

        openDeleteModal(id, name) {
            this.deleteDriverData = {
                id: id,
                name: name,
                url: '{{ route('drivers.index') }}/' + id
            };
            this.showDeleteModal = true;
        },

        closeModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showDeleteModal = false; // إغلاق مودل الحذف
        }
    }" 
    class="flex relative flex-col gap-6 pb-24 min-h-screen">

        <!-- Header Section -->
        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-2xl font-black tracking-tight font-headline text-slate-800">السائقين</h1>
                <p class="mt-0.5 text-xs font-semibold text-slate-400">
                    إجمالي <span class="text-primary">{{ $drivers->total() }}</span> سائق مسجل
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
                    placeholder="ابحث باسم السائق أو رقم الهاتف..."
                    class="w-full h-14 pr-12 pl-12 rounded-[1.25rem] border-none bg-white shadow-sm ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 transition-all font-headline text-sm text-slate-700 outline-none">
                
                <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                    class="flex absolute left-4 top-1/2 justify-center items-center w-8 h-8 rounded-xl transition-transform -translate-y-1/2 bg-slate-50 text-slate-400 active:scale-95">
                    <span class="text-lg material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <!-- Driver List Grid -->
        <div class="px-2 space-y-4">
            @forelse ($drivers as $driver)
                <div x-show="searchQuery === '' || '{{ $driver->name }}'.includes(searchQuery) || '{{ $driver->phone }}'.includes(searchQuery)"
                     class="bg-white rounded-[1.75rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-50 relative overflow-hidden active:scale-[0.98] transition-all">
                    
                    <!-- Top Info -->
                    <div class="flex relative z-10 gap-4 items-center mb-4">
                        <div class="flex justify-center items-center w-14 h-14 text-lg font-black bg-gradient-to-br rounded-2xl border shadow-inner from-primary/10 to-primary/5 text-primary font-headline border-primary/5 shrink-0">
                             @php
                                $words = explode(' ', $driver->name);
                                $first = mb_substr($words[0] ?? '', 0, 1, 'utf-8');
                                $second = isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '';
                                echo $first . $second;
                            @endphp
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="mb-1.5 text-base font-bold leading-none truncate font-headline text-slate-800">{{ $driver->name }}</h3>
                            <div class="flex gap-2 items-center text-slate-500">
                                <span class="material-symbols-outlined text-[16px] text-primary/60">phone_iphone</span>
                                <span class="font-mono text-xs font-bold tracking-wider">{{ $driver->phone }}</span>
                            </div>
                        </div>
                        <button type="button" @click="openEditModal({{ $driver->id }}, {{ json_encode($driver->name) }}, {{ json_encode($driver->phone) }})"
                            class="flex justify-center items-center w-10 h-10 rounded-xl transition-all bg-slate-50 text-slate-400 hover:bg-primary/5 hover:text-primary active:scale-90">
                            <span class="text-xl material-symbols-outlined">edit_square</span>
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="mb-4 h-px bg-gradient-to-r from-transparent to-transparent via-slate-100"></div>

                    <!-- Actions -->
                    <div class="flex relative z-10 justify-between items-center">
                         <div class="flex gap-2.5">
                             <a href="tel:{{ $driver->phone }}" 
                                class="flex gap-2 items-center px-4 py-2 text-xs font-bold rounded-xl border transition-transform bg-primary/5 text-primary font-headline active:scale-95 border-primary/5">
                                <span class="text-sm material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">call</span>
                                اتصال
                             </a>
                             <a href="https://wa.me/{{ $driver->phone }}" 
                                class="flex gap-2 items-center px-4 py-2 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border transition-transform font-headline active:scale-95 border-emerald-100/50">
                                <span class="text-sm material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">chat</span>
                                واتساب
                             </a>
                         </div>
                         
                         <!-- زر الحذف الجديد الذي يفتح المودل -->
                         <button type="button" @click="openDeleteModal({{ $driver->id }}, {{ json_encode($driver->name) }})"
                            class="flex justify-center items-center w-10 h-10 text-red-500 bg-red-50 rounded-xl transition-all hover:bg-red-100 active:scale-90">
                            <span class="text-xl material-symbols-outlined">delete_outline</span>
                         </button>
                    </div>
                </div>
            @empty
                <div class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2 shadow-sm">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                        <span class="text-6xl material-symbols-outlined">no_accounts</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لم نعثر على أي سائقين</p>
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
            {{ $drivers->links('vendor.pagination.mobile') }}
        </div>

        <!-- ======================== Bottom Sheet Modals ======================== -->

        <!-- Create Driver Bottom Sheet -->
        <div x-show="showCreateModal" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full"
    class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">
    
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

    <div class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto">
        <div @click="closeModals()" class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90"></div>

        <div class="flex justify-between items-center px-2 mb-8">
            <h3 class="text-xl font-black font-headline text-slate-800">إضافة سائق جديد</h3>
            <button type="button" @click="closeModals()" class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors bg-slate-50 text-slate-400 hover:bg-slate-100">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('drivers.store') }}" method="POST" class="px-2 space-y-6">
            @csrf
            <div>
                <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الاسم الكامل</label>
                <div class="relative">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">person</span>
                    <input type="text" name="name" required placeholder="مثلاً: أحمد السعيدي"
                        class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                </div>
            </div>

            <div x-data="{
                open: false,
                search: '',
                countries: @js(array_values(config('countries', []))),
                selectedCountry: null,
                localPhoneNumber: '',
                init() {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                },
                get filteredCountries() {
                    if (this.search === '') return this.countries;
                    return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                }
            }">
                <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف <span class="text-rose-500">*</span></label>
                
                <div class="relative">
                    <input type="hidden" name="phone" :value="(selectedCountry?.dial_code.replace('+', '') || '') + localPhoneNumber">
                    
                    <div class="flex overflow-hidden relative items-center rounded-2xl ring-1 transition-all group bg-slate-50 focus-within:bg-white ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20">
                        
                        {{-- Phone Input --}}
                        <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX" required inputmode="numeric"
                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                            class="flex-1 pr-12 pl-4 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline dir-ltr">
                        
                        {{-- Phone Icon --}}
                        <div class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-primary">
                            <span class="material-symbols-outlined">smartphone</span>
                        </div>

                        {{-- Country Selector Button --}}
                        <button type="button" @click="open = !open"
                            class="flex gap-2 items-center px-3 h-14 border-r transition-colors bg-slate-100 border-slate-200 shrink-0 hover:bg-slate-200">
                            <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                            <span class="text-sm font-bold text-slate-600 dir-ltr" x-text="selectedCountry?.dial_code"></span>
                            <template x-if="selectedCountry?.svg">
                                <svg class="w-6 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                            </template>
                        </button>
                    </div>

                    {{-- Dropdown panel --}}
                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                        class="absolute top-[calc(100%+6px)] left-0 z-50 w-full sm:w-[320px] max-h-60 bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden">
                        <div class="p-2 border-b border-slate-50">
                            <input type="text" x-model="search" placeholder="ابحث عن الدولة أو الرمز..."
                                class="px-4 py-2 w-full text-sm rounded-xl transition-colors outline-none bg-slate-50 focus:bg-slate-100 hover:bg-slate-100 font-headline">
                        </div>
                        <div class="overflow-y-auto max-h-40 custom-scrollbar">
                            <template x-for="country in filteredCountries" :key="country.code">
                                <div @click="selectedCountry = country; open = false; search = ''"
                                    class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                    <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                    <span class="flex-grow text-sm font-medium truncate text-slate-700 font-headline" x-text="country.name"></span>
                                    <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr" x-text="country.dial_code"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" 
                class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95">
                <span class="material-symbols-outlined">save</span>
                حفظ البيانات
            </button>
        </form>
    </div>
</div>

        <!-- Edit Driver Bottom Sheet -->
        <div x-show="showEditModal" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-full"
             class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">
            
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

            <div class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto">
                <div @click="closeModals()" class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90"></div>

                <div class="flex justify-between items-center px-2 mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات السائق</h3>
                    <button type="button" @click="closeModals()" class="flex justify-center items-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="editDriverData.url" method="POST" class="px-2 space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الاسم الكامل</label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">person</span>
                            <input type="text" name="name" x-model="editDriverData.name" required
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                        </div>
                    </div>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries'))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                            const countryCodes = this.countries.map(c => c.dial_code.replace('+', '')).sort((a,b) => b.length - a.length);

                            this.$watch('editDriverData.phone', newValue => {
                                if (!newValue) {
                                    this.localPhoneNumber = '';
                                    return;
                                }
                                const currentConstructed = (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhoneNumber;
                                if (newValue !== currentConstructed) {
                                    let matched = false;
                                    for(let code of countryCodes) {
                                        if (newValue.startsWith(code)) {
                                            this.selectedCountry = this.countries.find(c => c.dial_code.replace('+','') === code);
                                            this.localPhoneNumber = newValue.substring(code.length);
                                            matched = true;
                                            break;
                                        }
                                    }
                                    if (!matched) {
                                        this.localPhoneNumber = newValue;
                                    }
                                }
                            });
                            
                            this.$watch('localPhoneNumber', value => {
                                editDriverData.phone = (this.selectedCountry?.dial_code.replace('+', '') || '') + value;
                            });
                            this.$watch('selectedCountry', value => {
                                editDriverData.phone = (value?.dial_code.replace('+', '') || '') + this.localPhoneNumber;
                            });
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }">
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="hidden" name="phone" :value="editDriverData.phone"
                            
                            >
                            
                            <div class="flex relative rounded-2xl ring-1 transition-all bg-slate-50 focus-within:bg-white ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20">
                                
                                {{-- Country Selector Button --}}
                                <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-4 bg-transparent rounded-r-2xl border-l transition-colors border-slate-200 shrink-0 hover:bg-slate-100">
                                    <template x-if="selectedCountry">
                                        <svg class="w-5 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                    </template>
                                    <span class="text-xs font-bold text-slate-600" dir="ltr" x-text="selectedCountry?.dial_code"></span>
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                </button>

                                {{-- Phone Input --}}
                                <input type="tel" x-model="localPhoneNumber" placeholder="7xx xxx xxx" required
                                    class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent rounded-l-2xl border-none outline-none font-headline" dir="ltr">
                                
                            </div>

                            {{-- Dropdown panel --}}
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute top-[calc(100%+4px)] right-0 z-20 w-full max-h-60 bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden"
                                style="display: none;">
                                <div class="p-2 border-b border-slate-50">
                                    <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                        class="px-4 py-2 w-full text-sm rounded-xl outline-none bg-slate-50 font-headline">
                                </div>
                                <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                    <template x-for="country in filteredCountries" :key="country.code">
                                        <div @click="selectedCountry = country; open = false; search = ''"
                                            class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                            <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                            <span class="flex-grow text-sm font-medium truncate text-slate-700 font-headline" x-text="country.name"></span>
                                            <span class="font-mono text-xs font-bold text-slate-500 shrink-0" dir="ltr" x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                        class="flex gap-2 justify-center items-center mt-4 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95">
                        <span class="material-symbols-outlined">update</span>
                        حفظ التعديلات
                    </button>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Bottom Sheet -->
        <div x-show="showDeleteModal" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-full"
             class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">
            
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

            <div class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto text-center">
                <!-- Handle Bar -->
                <div @click="closeModals()" class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90"></div>

                <!-- أيقونة التحذير -->
                <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-red-50 text-red-500 rounded-[1.5rem]">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد الحذف</h3>
                
                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من أنك تريد حذف السائق <br>
                    <span class="text-base font-bold text-slate-800 font-headline" x-text="deleteDriverData.name"></span>؟<br>
                    <span class="text-red-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>

                <form :action="deleteDriverData.url" method="POST" class="flex gap-3 px-2">
                    @csrf
                    @method('DELETE')
                    
                    <button type="button" @click="closeModals()"
                        class="flex-1 py-4 text-sm font-bold rounded-2xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 active:scale-95 font-headline">
                        تراجع
                    </button>
                    
                    <button type="submit" 
                        class="flex-1 py-4 text-sm font-bold text-white bg-red-500 rounded-2xl shadow-lg transition-all hover:bg-red-600 shadow-red-500/30 active:scale-95 font-headline">
                        نعم، احذف
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection