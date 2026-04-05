@extends('layouts.app')
@section('title', 'إدارة العملاء')
@section('Breadcrumb', 'إدارة العملاء')

@section('content')


    <div class="space-y-6 font-outfit" dir="rtl" x-data="customerRegistry()">
        {{-- Modals --}}
        @include('pages.customers.edit-customer-modal')
        @include('pages.customers.create-customer-modal')

        {{-- ===== Stats Cards ===== --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            {{-- Total Customers --}}
            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100 dark:border-gray-800'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-xl dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي العملاء</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $customers->total() }}</h4>
                </div>
            </div>

            {{-- Debtors --}}
            <div @click="filterStatus = 'debtor'"
                :class="filterStatus === 'debtor' ? 'border-error-500 ring-2 ring-error-500/20' : 'border-gray-100 dark:border-gray-800'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm border-r-4 border-r-error-500">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest uppercase text-theme-xs text-error-500">المديونين</span>
                    <h4 class="text-xl font-black dark:text-white">
                        {{ $customers->getCollection()->filter(fn($c) => ($c->debit_sum ?? 0) > ($c->credit_sum ?? 0))->count() }}
                    </h4>
                </div>
            </div>

            {{-- Cleared --}}
            <div @click="filterStatus = 'cleared'"
                :class="filterStatus === 'cleared' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100 dark:border-gray-800'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm border-r-4 border-r-success-500">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
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
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="p-4 bg-white dark:bg-white/[0.03]">
                <div class="relative w-full rounded-2xl border ring-2 group border-brand-500 ring-brand-500/20">
                    <input type="text" x-model="search" placeholder="ابحث باسم العميل أو رقم الهاتف..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-gray-50 rounded-xl border-none shadow-inner transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-brand-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
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
                    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800"
                        x-show="showRow('{{ $customer->name }}', '{{ $customer->phone }}', {{ $is_debtor ? 'true' : 'false' }})"
                        x-transition>
                        {{-- Mobile Header --}}
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-black bg-gray-50 rounded-xl border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500">
                                    {{ mb_substr($customer->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $customer->name }}</span>
                                    <x-phone-number :value="$customer->phone" class="text-xs text-gray-500 dark:text-gray-400" />
                                </div>
                            </div>
                        </div>
                        {{-- Mobile Info --}}
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span class="px-2.5 py-1 rounded-full bg-gray-50 dark:bg-gray-800 text-gray-500 text-[10px] font-black uppercase">
                                {{ $customer->branch->name ?? 'N/A' }}
                            </span>
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black {{ $is_debtor ? 'bg-error-500 text-white' : 'bg-success-500 text-white' }}">
                                {{ $is_debtor ? 'مديون' : 'مسدد' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">لا توجد بيانات</div>
                @endforelse
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
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
                            <tr x-show="showRow('{{ $customer->name }}', '{{ $customer->phone }}', {{ $is_debtor ? 'true' : 'false' }})"
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md">
                                
                                <td class="px-6 py-5 first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-sm font-black bg-gray-50 rounded-xl text-brand-500">
                                            {{ mb_substr($customer->name, 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-900 dark:text-white">{{ $customer->name }}</span>
                                            <x-phone-number :value="$customer->phone" class="text-xs text-gray-400" />
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span class="px-3 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-500 text-[10px] font-black">
                                        {{ $customer->branch->name ?? 'N/A' }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="px-3 py-1 rounded-lg text-[10px] font-black {{ $is_debtor ? 'bg-error-500 text-white' : 'bg-success-500 text-white' }}">
                                            {{ $is_debtor ? 'مديون' : 'مسدد' }}
                                        </span>
                                        @if ($is_debtor)
                                            <span class="text-xs font-bold text-error-500">{{ number_format($balance, 0) }} ر.ي</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center last:rounded-l-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-2 justify-center items-center">
                                        {{-- كشف حساب --}}
                                        <a href="{{ route('customers.show', $customer->id) }}" class="p-2 text-gray-400 transition-colors hover:text-brand-600" title="كشف الحساب">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </a>

                                        {{-- تعديل --}}
                                        <button @click="openEditModal({{ $customer->id }})" class="p-2 text-gray-400 transition-colors hover:text-warning-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>

                                        {{-- حذف --}}
                                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف العميل؟')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 transition-colors hover:text-red-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>

                                        {{-- تصفية حساب --}}
                                        @if ($is_debtor)
                                            @include('pages.customers.clearamount', ['customer' => $customer])
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-12 text-center text-gray-400">لا توجد بيانات عملاء مطابقة..</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-6 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50">
                {{ $customers->links() }}
            </div>
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
                countries: [{ name: 'Yemen', code: 'YE', dial_code: '967' }],
                editCustomer: { id: null, name: '', phone: '' },

                init() {
                    this.editCustomer.phone_country = this.countries[0];
                },

                showRow(name, phone, isDebtor) {
                    const matchesSearch = name.toLowerCase().includes(this.search.toLowerCase()) || phone.includes(this.search);
                    const matchesStatus = this.filterStatus === 'all' || 
                                          (this.filterStatus === 'debtor' && isDebtor) || 
                                          (this.filterStatus === 'cleared' && !isDebtor);
                    return matchesSearch && matchesStatus;
                },

                async openEditModal(customerId) {
                    this.isFetching = customerId;
                    try {
                        const response = await fetch(`/customers/${customerId}/edit`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        this.editCustomer = data;
                        this.editModalOpen = true; 
                    } catch (error) {
                        alert("خطأ في جلب البيانات");
                    } finally {
                        this.isFetching = null;
                    }
                }
            }
        }
    </script>
@endsection