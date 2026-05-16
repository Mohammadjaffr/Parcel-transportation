@extends('mobile.layouts.app')

@section('title', 'إدارة العملاء')

@section('content')


    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        searchQuery: '',
        isSubmitting: false,
        errors: {},
    
        editCustomerData: { id: '', name: '', phone: '', url: '' },
        createCustomerData: { name: '', phone: '' },
        deleteCustomerData: { id: '', name: '', url: '' },
    
        openEditModal(id, name, phone) {
            this.errors = {};
            this.editCustomerData = {
                id: id,
                name: name,
                phone: phone,
                url: '{{ route('customers.index') }}/' + id
            };
            this.$dispatch('set-edit-phone', { phone: phone });
            this.showEditModal = true;
        },
    
        openCreateModal() {
            this.errors = {};
            this.createCustomerData = { name: '', phone: '' };
            this.showCreateModal = true;
        },
    
        openDeleteModal(id, name) {
            this.deleteCustomerData = {
                id: id,
                name: name,
                url: '{{ route('customers.index') }}/' + id
            };
            this.showDeleteModal = true;
        },
    
        closeModals() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showDeleteModal = false;
            this.errors = {};
        },
    
        async submitForm(url, method, data) {
            this.isSubmitting = true;
            this.errors = {};
    
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = result.errors;
                    } else {
                        alert(result.message || 'حدث خطأ غير متوقع.');
                    }
                } else {
                    this.closeModals();
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ في الاتصال بالخادم.');
            } finally {
                this.isSubmitting = false;
            }
        }
    }" class="flex relative flex-col gap-6 pb-24 min-h-screen">

        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-2xl font-black tracking-tight font-headline text-slate-800">العملاء</h1>
                <p class="mt-0.5 text-xs font-semibold text-slate-400">
                    إجمالي <span class="text-primary">{{ $customers->total() }}</span> عميل مسجل
                </p>
            </div>
            <button type="button" @click="openCreateModal()"
                class="flex justify-center items-center w-12 h-12 text-white rounded-2xl shadow-xl transition-all bg-primary shadow-primary/20 active:scale-95">
                <span class="text-2xl material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">person_add</span>
            </button>
        </div>

        <div class="px-2">
            <div class="relative group">
                <span
                    class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</span>
                <input type="text" x-model="searchQuery" placeholder="ابحث باسم العميل أو رقم الهاتف..."
                    class="w-full h-14 pr-12 pl-12 rounded-[1.25rem] border-none bg-white shadow-sm ring-1 ring-slate-100 focus:ring-2 focus:ring-primary/20 transition-all font-headline text-sm text-slate-700 outline-none">

                <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                    class="flex absolute left-4 top-1/2 justify-center items-center w-8 h-8 rounded-xl transition-transform -translate-y-1/2 bg-slate-50 text-slate-400 active:scale-95">
                    <span class="text-lg material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        {{-- ================= شريط فلترة المديونية ================= --}}
        <div class="flex overflow-x-auto gap-3 px-2 py-1 custom-scrollbar snap-x">

            {{-- زر الكل --}}
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'all']) }}"
                class="snap-start shrink-0 px-5 h-11 flex items-center justify-center rounded-[1rem] text-[13px] font-black transition-all duration-200 border active:scale-95
                {{ $filter == 'all' ? 'bg-slate-800 text-white border-slate-800 shadow-md shadow-slate-800/20' : 'bg-white text-slate-500 border-slate-200 shadow-sm hover:bg-slate-50 hover:text-slate-700' }}">
                جميع العملاء
            </a>

            {{-- زر عليهم ديون (أحمر) --}}
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'debtors']) }}"
                class="snap-start shrink-0 px-5 h-11 flex items-center justify-center rounded-[1rem] text-[13px] font-black transition-all duration-200 border active:scale-95
                {{ $filter == 'debtors' ? 'bg-rose-500 text-white border-rose-500 shadow-md shadow-rose-500/30' : 'bg-white text-slate-500 border-slate-200 shadow-sm hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200' }}">
                <span class="material-symbols-outlined text-[18px] ml-1.5">money_off</span>
                عليهم ديون
            </a>

            {{-- زر مصفرين / لهم رصيد (أخضر) --}}
            <a href="{{ request()->fullUrlWithQuery(['filter' => 'creditors']) }}"
                class="snap-start shrink-0 px-5 h-11 flex items-center justify-center rounded-[1rem] text-[13px] font-black transition-all duration-200 border active:scale-95
                {{ $filter == 'creditors' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/30' : 'bg-white text-slate-500 border-slate-200 shadow-sm hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200' }}">
                <span class="material-symbols-outlined text-[18px] ml-1.5">task_alt</span>
                حسابات مصفّرة
            </a>

        </div>

        <div class="px-2 space-y-4">
            @forelse ($customers as $customer)
                @php
                    $balance = ($customer->sum_debit ?? 0) - ($customer->sum_credit ?? 0);
                @endphp
                {{-- 💡 أضفنا x-data="openMenu: false" هنا، وحذفنا overflow-hidden --}}
                <div x-data="{ openMenu: false }"
                    x-show="searchQuery === '' || '{{ $customer->name }}'.includes(searchQuery) || '{{ $customer->phone }}'.includes(searchQuery)"
                    class="bg-white rounded-[1.75rem] p-5 shadow-[0_8px_30px_rgb(0,0,0,0.02)] border border-slate-50 relative overflow-visible active:scale-[0.98] transition-all">

                    <div class="flex relative z-10 gap-3 items-start mb-5">

                        {{-- الصورة الرمزية (Avatar) --}}
                        <div
                            class="flex justify-center items-center w-12 h-12 text-lg font-black bg-gradient-to-br rounded-2xl border shadow-inner from-primary/10 to-primary/5 text-primary font-headline border-primary/5 shrink-0">
                            @php
                                $words = explode(' ', $customer->name);
                                $first = mb_substr($words[0] ?? '', 0, 1, 'utf-8');
                                $second = isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '';
                                echo $first . $second;
                            @endphp
                        </div>

                        {{-- بيانات العميل --}}
                        <div class="flex-1 pt-0.5 min-w-0">
                            <h3 class="mb-1 text-sm font-black leading-none truncate font-headline text-slate-800">
                                {{ $customer->name }}
                            </h3>
                            <div class="flex gap-1.5 items-center mt-1.5 text-slate-500">
                                <span class="material-symbols-outlined text-[14px] text-primary/60">phone_iphone</span>
                                <x-phone-number :value="$customer->phone" class="font-mono text-[11px] font-bold tracking-wider" />
                            </div>
                            <div class="flex gap-1.5 items-center mt-1.5 text-slate-500">
                                <span class="material-symbols-outlined text-[14px] text-primary/60">store</span>
                                <span class="text-xs text-gray-500">{{ $customer->branch->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">{{ $customer->created_at->diffForHumans() }}</span>
                        </div>


                        {{-- 💡 القائمة المنسدلة للإجراءات (Kebab Menu) --}}
                        <div class="relative z-50 shrink-0">
                            <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                class="flex justify-center items-center -mr-2 w-8 h-8 rounded-full transition-colors text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none">
                                <span class="material-symbols-outlined text-[22px]">more_vert</span>
                            </button>

                            <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                class="absolute top-full left-0 mt-1 w-44 bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100/50 z-[60] overflow-hidden py-1.5">

                                {{-- ملف العميل (التفاصيل) --}}
                                <a href="{{ route('customers.show', $customer->id) }}"
                                    class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-primary/5 hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    ملف العميل
                                </a>

                                {{-- مراسلة واتساب --}}
                                <a href="{{ route('whatsapp.customer.account.statement', $customer->id) }}" target="_blank"
                                    class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-emerald-50 hover:text-emerald-600">

                                    {{-- أيقونة واتساب الرسمية باللون الأخضر --}}
                                    <svg class="w-[18px] h-[18px] fill-[#25D366]" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                    </svg>

                                    مراسلة (واتساب)
                                </a>

                                <div class="mx-3 my-1 h-px bg-slate-100/80"></div>

                                {{-- تعديل البيانات --}}
                                <button type="button"
                                    @click="openMenu = false; openEditModal({{ $customer->id }}, {{ json_encode($customer->name) }}, {{ json_encode($customer->phone) }})"
                                    class="flex gap-2.5 items-center px-4 py-2 w-full text-xs font-bold text-right transition-colors text-slate-600 hover:bg-amber-50 hover:text-amber-600">
                                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                                    تعديل البيانات
                                </button>

                                {{-- حذف العميل --}}
                                {{-- <button type="button"
                                    @click="openMenu = false; openDeleteModal({{ $customer->id }}, {{ json_encode($customer->name) }})"
                                    class="flex gap-2.5 items-center px-4 py-2 w-full text-xs font-bold text-right text-rose-500 transition-colors hover:bg-rose-50">
                                    <span class="material-symbols-outlined text-[18px]">delete_outline</span>
                                    حذف العميل
                                </button> --}}
                            </div>
                        </div>
                    </div>

                    @php
                        // حساب إجمالي الدائن والمدين
                        $totalCredit = $customer->sum_credit ?? 0;
                        $totalDebit = $customer->sum_debit ?? 0;

                        // استخراج الرصيد الصافي (الفرق بينهما)
                        $netBalance = $totalCredit - $totalDebit;

                        // تحديد من له ومن عليه بناءً على الصافي
                        $displayCredit = $netBalance > 0 ? $netBalance : 0;
                        $displayDebit = $netBalance < 0 ? abs($netBalance) : 0;
                    @endphp

                    <div class="flex gap-2 p-3 rounded-2xl border bg-slate-50 border-slate-100/50">
                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 mb-1">الشحنات</span>
                            <span
                                class="block text-sm font-black text-slate-700">{{ $customer->sent_shipments_count ?? 0 }}</span>
                        </div>

                        <div class="w-px bg-slate-200/60"></div>

                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 mb-1">رصيد له</span>
                            <span
                                class="block text-sm font-black {{ $displayCredit > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ number_format($displayCredit, 2) }}
                            </span>
                        </div>

                        <div class="w-px bg-slate-200/60"></div>

                        <div class="flex-1 text-center">
                            <span class="block text-[10px] font-bold text-slate-400 mb-1">رصيد عليه</span>
                            <span
                                class="block text-sm font-black {{ $displayDebit > 0 ? 'text-rose-500' : 'text-slate-400' }}">
                                {{ number_format($displayDebit, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 mx-2 shadow-sm">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                        <span class="text-6xl material-symbols-outlined">group_off</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لم نعثر على أي عملاء</p>
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
            {{ $customers->links('vendor.pagination.mobile') }}
        </div>

        <div x-show="showCreateModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()">
            </div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto">
                <div @click="closeModals()"
                    class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div class="flex justify-between items-center px-2 mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">إضافة عميل جديد</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm('{{ route('customers.store') }}', 'POST', createCustomerData)"
                    class="px-2 space-y-5">
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الاسم الكامل <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined"
                                :class="errors.name ? 'text-red-400' : 'text-slate-400'">person</span>
                            <input type="text" x-model="createCustomerData.name" placeholder="مثلاً: محمد عبدالله"
                                required
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline"
                                :class="errors.name ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.name">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries', []))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        fullPhone: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                            this.$watch('localPhoneNumber', () => this.updateFullPhone());
                            this.$watch('selectedCountry', () => this.updateFullPhone());
                        },
                        updateFullPhone() {
                            this.fullPhone = this.localPhoneNumber ? (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhoneNumber : '';
                            createCustomerData.phone = this.fullPhone;
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }">
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف <span
                                class="text-rose-500">*</span></label>

                        <div class="relative">
                            <div class="flex overflow-hidden relative items-center rounded-2xl ring-1 transition-all group bg-slate-50 focus-within:bg-white ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20"
                                :class="errors.phone ? 'ring-red-300 focus-within:ring-red-400' : ''">

                                <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX" required
                                    inputmode="numeric" :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 pr-12 pl-4 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline dir-ltr">

                                <div
                                    class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-primary">
                                    <span class="material-symbols-outlined">call</span>
                                </div>

                                <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-3 h-14 border-r transition-colors bg-slate-100 border-slate-200 shrink-0 hover:bg-slate-200">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                    <span class="text-sm font-bold text-slate-600 dir-ltr"
                                        x-text="selectedCountry?.dial_code"></span>
                                    <template x-if="selectedCountry?.svg">
                                        <svg class="w-6 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                    </template>
                                </button>
                            </div>

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
                                            <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg"
                                                x-html="country.svg"></svg>
                                            <span
                                                class="flex-grow text-sm font-medium truncate text-slate-700 font-headline"
                                                x-text="country.name"></span>
                                            <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr"
                                                x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <template x-if="errors.phone">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.phone[0]"></p>
                        </template>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95 disabled:opacity-70">
                        <span x-show="!isSubmitting" class="material-symbols-outlined">save</span>
                        <span x-show="isSubmitting"
                            class="animate-spin material-symbols-outlined">progress_activity</span>
                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ بيانات العميل'"></span>
                    </button>
                </form>
            </div>
        </div>

        <div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()">
            </div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto">
                <div @click="closeModals()"
                    class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div class="flex justify-between items-center px-2 mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات العميل</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-100">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm(editCustomerData.url, 'POST', { ...editCustomerData, _method: 'PUT' })"
                    class="px-2 space-y-5">
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الاسم الكامل <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined"
                                :class="errors.name ? 'text-red-400' : 'text-slate-400'">person</span>
                            <input type="text" x-model="editCustomerData.name" required
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white font-headline"
                                :class="errors.name ? 'ring-red-300 focus:ring-red-400' :
                                    'ring-slate-100 focus:ring-2 focus:ring-primary/20'">
                        </div>
                        <template x-if="errors.name">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries', []))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        fullPhone: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                            this.$watch('localPhoneNumber', () => this.updateFullPhone());
                            this.$watch('selectedCountry', () => this.updateFullPhone());
                        },
                        updateFullPhone() {
                            this.fullPhone = this.localPhoneNumber ? (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhoneNumber : '';
                            editCustomerData.phone = this.fullPhone;
                        },
                        handleSetPhone(phoneString) {
                            if (!phoneString) {
                                this.localPhoneNumber = '';
                                return;
                            }
                            let matched = this.countries.find(c => phoneString.startsWith(c.dial_code.replace('+', '')));
                            if (matched) {
                                this.selectedCountry = matched;
                                this.localPhoneNumber = phoneString.substring(matched.dial_code.replace('+', '').length);
                            } else {
                                this.localPhoneNumber = phoneString;
                            }
                            this.updateFullPhone();
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }" @set-edit-phone.window="handleSetPhone($event.detail.phone)">
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الهاتف <span
                                class="text-rose-500">*</span></label>

                        <div class="relative">
                            <div class="flex overflow-hidden relative items-center rounded-2xl ring-1 transition-all group bg-slate-50 focus-within:bg-white ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20"
                                :class="errors.phone ? 'ring-red-300 focus-within:ring-red-400' : ''">

                                <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX" required
                                    inputmode="numeric" :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                    class="flex-1 pr-12 pl-4 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline dir-ltr">

                                <div
                                    class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-primary">
                                    <span class="material-symbols-outlined">call</span>
                                </div>

                                <button type="button" @click="open = !open"
                                    class="flex gap-2 items-center px-3 h-14 border-r transition-colors bg-slate-100 border-slate-200 shrink-0 hover:bg-slate-200">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                    <span class="text-sm font-bold text-slate-600 dir-ltr"
                                        x-text="selectedCountry?.dial_code"></span>
                                    <template x-if="selectedCountry?.svg">
                                        <svg class="w-6 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                    </template>
                                </button>
                            </div>

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
                                            <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg"
                                                x-html="country.svg"></svg>
                                            <span
                                                class="flex-grow text-sm font-medium truncate text-slate-700 font-headline"
                                                x-text="country.name"></span>
                                            <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr"
                                                x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <template x-if="errors.phone">
                            <p class="mt-2 text-xs font-bold text-red-500" x-text="errors.phone[0]"></p>
                        </template>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95 disabled:opacity-70">
                        <span x-show="!isSubmitting" class="material-symbols-outlined">update</span>
                        <span x-show="isSubmitting"
                            class="animate-spin material-symbols-outlined">progress_activity</span>
                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
                    </button>
                </form>
            </div>
        </div>

        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()">
            </div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto text-center">
                <div @click="closeModals()"
                    class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-red-50 text-red-500 rounded-[1.5rem]">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد الحذف</h3>

                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من أنك تريد حذف العميل <br>
                    <span class="text-base font-bold text-slate-800 font-headline"
                        x-text="deleteCustomerData.name"></span>؟<br>
                    {{-- <span class="text-red-500/80">لا يمكن حذف عميل لديه حركات مالية.</span> --}}
                </p>

                <form @submit.prevent="submitForm(deleteCustomerData.url, 'POST', { _method: 'DELETE' })"
                    class="flex gap-3 px-2">
                    <button type="button" @click="closeModals()"
                        class="flex-1 py-4 text-sm font-bold rounded-2xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 active:scale-95 font-headline">
                        تراجع
                    </button>

                    <button type="submit" :disabled="isSubmitting"
                        class="flex-1 py-4 text-sm font-bold text-white bg-red-500 rounded-2xl shadow-lg transition-all hover:bg-red-600 shadow-red-500/30 active:scale-95 font-headline">
                        <span x-show="!isSubmitting">نعم، احذف</span>
                        <span x-show="isSubmitting"
                            class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
