@extends('layouts.app')
@section('title', 'إدارة العملاء')
@section('Breadcrumb', 'إدارة العملاء')

@section('addButton')
    {{-- زر الإضافة أضفنا له x-data ليعمل --}}
    <button x-data @click="$dispatch('open-create-customer-modal')"
        class="inline-flex gap-2 items-center px-4 py-2 text-sm font-semibold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95">
        <span class="material-symbols-outlined text-[20px]">add</span>
        إضافة عميل جديد
    </button>
@endsection

@section('content')

    {{-- الحاوية الرئيسية: أضفنا لها مستمع لفتح مودال الإضافة --}}
    <div class="space-y-6 font-outfit" dir="rtl" 
         x-data="customerRegistry()" 
         @open-create-customer-modal.window="createModalOpen = true">
        
        {{-- Modals --}}
        @include('pages.customers.create-customer-modal')
        @include('pages.customers.edit-customer-modal')

        {{-- ===== Stats Cards ===== --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            {{-- إجمالي العملاء --}}
            <div @click="filterStatus = 'all'; updateVisibility()"
                :class="filterStatus === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-gray-800'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[22px]">group</span>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي العملاء</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $customers->total() }}</h4>
                </div>
            </div>

            {{-- المديونين --}}
            <div @click="filterStatus = 'debtor'; updateVisibility()"
                :class="filterStatus === 'debtor' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-gray-100 hover:border-red-300 dark:border-gray-800'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm border-r-4 border-r-red-500">
                <div class="flex justify-center items-center w-10 h-10 text-red-500 bg-red-50 rounded-xl dark:bg-red-500/10">
                    <span class="material-symbols-outlined text-[22px]">account_balance_wallet</span>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-red-500 uppercase text-theme-xs">المديونين</span>
                    <h4 class="text-xl font-black dark:text-white">
                        {{ $customers->getCollection()->filter(fn($c) => ($c->debit_sum ?? 0) > ($c->credit_sum ?? 0))->count() }}
                    </h4>
                </div>
            </div>

            {{-- رصيد مسدد --}}
            <div @click="filterStatus = 'cleared'; updateVisibility()"
                :class="filterStatus === 'cleared' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100 hover:border-success-300 dark:border-gray-800'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm border-r-4 border-r-success-500">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <span class="material-symbols-outlined text-[22px]">task_alt</span>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest uppercase text-theme-xs text-success-500">رصيد مسدد</span>
                    <h4 class="text-xl font-black dark:text-white">
                        {{ $customers->getCollection()->filter(fn($c) => ($c->debit_sum ?? 0) <= ($c->credit_sum ?? 0))->count() }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- ===== Search & Table Section ===== --}}
        <div class="bg-white dark:bg-boxdark rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden transition-colors">
            
            {{-- شريط البحث --}}
            <div class="p-4 w-full bg-white rounded-2xl border-b border-gray-100 dark:bg-transparent dark:border-gray-800">
                <div class="relative rounded-2xl border border-gray-200 transition-all group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:border-gray-700">
                    <input type="text" x-model="search" @input.debounce.300ms="updateVisibility()"
                        placeholder="ابحث باسم العميل أو رقم الهاتف (في هذه الصفحة)..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 rounded-2xl border-none transition-all outline-none bg-gray-50/50 dark:bg-gray-900 focus:ring-0 dark:text-white">
                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>
                </div>
            </div>

            {{-- ===== Mobile View ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                @forelse($customers as $customer)
                    @php
                        $balance = ($customer->debit_sum ?? 0) - ($customer->credit_sum ?? 0);
                        $is_debtor = $balance > 0;
                    @endphp
                    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 transition-all customer-row bg-gray-50/50 dark:bg-gray-800/50 dark:border-gray-700 hover:border-primary/30"
                        x-show="showRow('{{ $customer->name }}', '{{ $customer->phone }}', {{ $is_debtor ? 'true' : 'false' }})">
                        
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-black text-white rounded-full shadow-sm bg-primary">
                                    {{ mb_substr($customer->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $customer->name }}</span>
                                    <x-phone-number :value="$customer->phone" class="text-xs text-gray-500 dark:text-gray-400" />
                                </div>
                            </div>
                            
                            {{-- Mobile Actions --}}
                            <div class="flex gap-1">
                                <button type="button" @click="openEditModal({{ $customer->id }})" 
                                        class="p-1.5 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-primary dark:bg-gray-900 dark:border-gray-800">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span class="px-2.5 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[10px] font-black uppercase">
                                {{ $customer->branch->name ?? 'N/A' }}
                            </span>
                            
                            <div class="flex flex-col items-end">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black {{ $is_debtor ? 'bg-red-50 text-red-600 dark:bg-red-500/10' : 'bg-success-50 text-success-600 dark:bg-success-500/10' }}">
                                    {{ $is_debtor ? 'مديون' : 'مسدد' }}
                                </span>
                                @if ($is_debtor)
                                    <span class="mt-1 text-[11px] font-bold text-red-500">{{ number_format($balance, 0) }} ر.ي</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-400">لا توجد بيانات عملاء مطابقة..</div>
                @endforelse

                {{-- رسالة بحث الموبايل --}}
                <div x-show="visibleCount === 0 && {{ $customers->count() }} > 0" x-cloak class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-700">
                    <span class="text-3xl text-gray-400 material-symbols-outlined">search_off</span>
                    <h4 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">لا توجد نتائج</h4>
                </div>
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 mt-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] dark:text-bodydark2">
                            <th class="px-6 py-4">العميل</th>
                            <th class="px-6 py-4 text-center">المكتب</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            @php
                                $balance = ($customer->debit_sum ?? 0) - ($customer->credit_sum ?? 0);
                                $is_debtor = $balance > 0;
                            @endphp
                            <tr class="bg-white rounded-2xl border border-transparent shadow-sm transition-all customer-row dark:bg-gray-900/50 hover:shadow-md hover:border-primary/30 dark:hover:border-primary/30"
                                x-show="showRow('{{ $customer->name }}', '{{ $customer->phone }}', {{ $is_debtor ? 'true' : 'false' }})">
                                
                                <td class="px-6 py-5 border-r border-gray-100 border-y dark:border-gray-800 first:rounded-r-2xl">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full shadow-sm bg-primary">
                                            {{ mb_substr($customer->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-900 dark:text-white">{{ $customer->name }}</span>
                                            <x-phone-number :value="$customer->phone" class="text-xs text-gray-400" />
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-gray-100 border-y dark:border-gray-800">
                                    <span class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[10px] font-black">
                                        {{ $customer->branch->name ?? 'N/A' }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center border-gray-100 border-y dark:border-gray-800">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $is_debtor ? 'bg-red-50 text-red-600 dark:bg-red-500/10' : 'bg-success-50 text-success-600 dark:bg-success-500/10' }}">
                                            {{ $is_debtor ? 'مديون' : 'مسدد' }}
                                        </span>
                                        @if ($is_debtor)
                                            <span class="text-xs font-bold text-red-500">{{ number_format($balance, 0) }} ر.ي</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-l border-gray-100 border-y dark:border-gray-800 last:rounded-l-2xl">
                                    <div class="flex gap-2 justify-center items-center">
                                        
                                        {{-- كشف حساب --}}
                                        <a href="{{ route('customers.show', $customer->id) }}" title="كشف الحساب"
                                            class="inline-flex p-2 text-gray-400 bg-gray-50 rounded-lg transition-all dark:bg-gray-800 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                        </a>

                                        {{-- تصفية حساب --}}
                                        @if ($is_debtor)
                                            @include('pages.customers.clearamount', ['customer' => $customer])
                                        @endif

                                        {{-- تعديل --}}
                                        <button type="button" @click="openEditModal({{ $customer->id }})" title="تعديل العميل"
                                            class="inline-flex p-2 text-gray-400 bg-gray-50 rounded-lg transition-all dark:bg-gray-800 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>

                                        {{-- حذف --}}
                                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف العميل؟')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="حذف" class="inline-flex p-2 text-gray-400 bg-gray-50 rounded-lg transition-all dark:bg-gray-800 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-gray-400">لا توجد بيانات عملاء مطابقة..</td></tr>
                        @endforelse

                        {{-- رسالة بحث الديسكتوب المتزامنة --}}
                        <tr x-show="visibleCount === 0 && {{ $customers->count() }} > 0" x-cloak>
                            <td colspan="4" class="py-20 text-center">
                                <div class="flex flex-col justify-center items-center">
                                    <span class="mb-2 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">لا توجد نتائج تطابق بحثك في هذه الصفحة.</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($customers->hasPages())
                <div class="px-6 pt-4 pb-6 mt-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
    <script>
        function customerRegistry() {
            return {
                search: '',
                filterStatus: 'all',
                isFetching: null,
                visibleCount: {{ $customers->count() }}, 
                countries: @json(array_values(config('countries') ?? [])),
                customersList: @json($customers->items()),
                
                editCustomer: { id: null, name: '', phone: '', phone_country: null, phone_local: '', url: '' },
                editModalOpen: false,
                createModalOpen: false,

                init() {
                    if (!this.countries || this.countries.length === 0) {
                        this.countries = [
                            { name: 'اليمن', code: 'YE', dial_code: '967', svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 600"><rect width="900" height="600" fill="#000"/><rect width="900" height="200" fill="#ce1126"/><rect y="400" width="900" height="200" fill="#fff"/></svg>' }
                        ];
                    }
                    this.editCustomer.phone_country = this.countries.find(c => c.code === 'YE') || this.countries[0];
                },

                showRow(name, phone, isDebtor) {
                    const matchesSearch = name.toLowerCase().includes(this.search.toLowerCase()) || phone.includes(this.search);
                    const matchesStatus = this.filterStatus === 'all' || 
                                          (this.filterStatus === 'debtor' && isDebtor) || 
                                          (this.filterStatus === 'cleared' && !isDebtor);
                    return matchesSearch && matchesStatus;
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.customer-row:not([style*="display: none"])').length;
                    });
                },

                parsePhoneNumber(fullNumber) {
                    if (!fullNumber) return {
                        country: this.countries.find(c => c.code === 'YE') || this.countries[0],
                        local: ''
                    };
                    
                    let phone = String(fullNumber).replace('+', '');
                    const sortedCountries = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);

                    for (let country of sortedCountries) {
                        const regex = new RegExp(`^(00)?${country.dial_code}`);
                        if (regex.test(phone)) {
                            return {
                                country: country,
                                local: phone.replace(regex, '')
                            };
                        }
                    }
                    return {
                        country: this.countries.find(c => c.code === 'YE') || this.countries[0],
                        local: fullNumber
                    };
                },

                openEditModal(customerId) {
                    try {
                        const customer = this.customersList.find(c => c.id === customerId);
                        if (!customer) {
                            alert('تعذر العثور على بيانات العميل');
                            return;
                        }

                        let parsedPhone;
                        try {
                            parsedPhone = this.parsePhoneNumber(customer.phone);
                        } catch(e) {
                            console.error('Phone parsing error:', e);
                            parsedPhone = {
                                country: this.countries.find(c => c.code === 'YE') || this.countries[0],
                                local: customer.phone || ''
                            };
                        }

                        this.editCustomer.id = customer.id;
                        this.editCustomer.name = customer.name;
                        this.editCustomer.phone_local = parsedPhone.local;
                        this.editCustomer.phone_country = parsedPhone.country;
                        this.editCustomer.url = '/customers/' + customer.id;

                        this.editModalOpen = true; 
                    } catch (error) {
                        alert('حدث خطأ غير متوقع: ' + error.message);
                    }
                }
            }
        }
    </script>
@endsection