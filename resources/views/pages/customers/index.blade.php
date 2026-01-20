@extends('layouts.app')
@section('title', 'إدارة العملاء')
@section('addButton')
    @include('pages.customers.create-customer-modal')
@endsection


@section('content')
    <div class="space-y-6 font-outfit" dir="rtl" x-data="customerRegistry()">
        @include('pages.customers.edit-customer-modal')
        <x-modals.success-modal />
        <x-modals.error-modal />

        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-xl dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        العملاء</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $customers->total() }}</h4>
                </div>
            </div>

            <div @click="filterStatus = 'debtor'"
                :class="filterStatus === 'debtor' ? 'border-error-500 ring-2 ring-error-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm border-r-4 border-r-error-500">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest uppercase text-theme-xs text-error-500">المديونين</span>
                    <h4 class="text-xl font-black dark:text-white">
                        {{ $customers->getCollection()->filter(fn($c) => ($c->debit_sum ?? 0) > ($c->credit_sum ?? 0))->count() }}
                    </h4>
                </div>
            </div>

            <div @click="filterStatus = 'cleared'"
                :class="filterStatus === 'cleared' ? 'border-success-500 ring-2 ring-success-500/20' :
                    'border-gray-100 dark:border-gray-800'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm border-r-4 border-r-success-500">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest uppercase text-theme-xs text-success-500">رصيد خالص</span>
                    <h4 class="text-xl font-black dark:text-white">
                        {{ $customers->getCollection()->filter(fn($c) => ($c->debit_sum ?? 0) <= ($c->credit_sum ?? 0))->count() }}
                    </h4>
                </div>
            </div>
        </div>

        <div
            class="grid grid-cols-1 md:grid-cols-2 items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm gap-6">
            <div class="relative w-full group">
                <input type="text" x-model="search" placeholder="ابحث باسم العميل أو رقم الهاتف..."
                    class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-gray-50 rounded-xl border-none shadow-inner transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                <div
                    class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="flex md:justify-end">
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">العميل</th>
                            <th class="px-6 py-4">بيانات التواصل</th>
                            <th class="px-6 py-4 text-center">الفرع</th>
                            <th class="px-6 py-4 text-center">عدد الشحنات</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse($customers as $customer)
                            @php
                                $balance = ($customer->debit_sum ?? 0) - ($customer->credit_sum ?? 0);
                                $is_debtor = $balance > 0;
                            @endphp
                            <tr x-show="showRow('{{ $customer->name }}', '{{ $customer->phone }}', {{ $is_debtor ? 'true' : 'false' }})"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md group hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-3 items-center">
                                        <div
                                            class="flex justify-center items-center w-10 h-10 text-sm font-black bg-gray-50 rounded-xl border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                                            {{ mb_substr($customer->name, 0, 1) }}
                                        </div>
                                        <span class="font-black text-gray-900 dark:text-white">{{ $customer->name }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-sm font-bold text-gray-600 dark:text-gray-400">{{ $customer->phone }}</span>
                                        @if ($customer->whatsapp_number)
                                            <span
                                                class="text-[9px] text-success-500 font-black uppercase flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full animate-pulse bg-success-500"></span>
                                                متصل واتساب
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-500 text-[10px] font-black uppercase border border-gray-100 dark:border-gray-700">
                                        {{ $customer->branch->name ?? $customer->branch_code }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <div class="text-sm font-black text-brand-600 dark:text-brand-400">
                                        {{ $customer->shipments_count ?? 0 }}
                                        <small class="text-[10px] mr-0.5 uppercase">شحنة</small>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <div class="flex flex-col gap-1.5 items-center">
                                        <span
                                            class="px-3 py-1 rounded-lg text-[10px] font-black uppercase shadow-lg {{ $is_debtor ? 'bg-error-500 text-white shadow-error-500/20' : 'bg-success-500 text-white shadow-success-500/20' }}">
                                            {{ $is_debtor ? 'مديون' : 'خالص' }}
                                        </span>
                                        @if ($is_debtor)
                                            <span
                                                class="px-2 py-1 text-xs font-bold rounded-lg text-error-500 bg-error-50 dark:bg-error-500/10">
                                                {{ number_format($balance, 0) }} ر.ي
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td
                                    class="px-6 py-5 text-center border-l last:rounded-l-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-2 justify-center items-center">
                                        <a href="{{ route('customers.show', $customer->id) }}"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400"
                                            title="كشف الحساب">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('customers.comprehensive-report', $customer->id) }}"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-purple-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-purple-400"
                                            title="تقرير شامل" target="_blank">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>
                                        <button @click="openEditModal({{ $customer->id }})"
                                            :disabled="isFetching == {{ $customer->id }}"
                                            class="p-2 text-gray-400 rounded-xl transition-all hover:text-warning-500 hover:bg-warning-50"
                                            title="تعديل">
                                            <template x-if="isFetching == {{ $customer->id }}">
                                                <svg class="w-5 h-5 animate-spin text-warning-500"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </template>
                                            <template x-if="isFetching != {{ $customer->id }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </template>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="italic font-bold text-gray-400">لا توجد بيانات عملاء مطابقة للبحث..</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-8 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
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
                editModalOpen: false,
                isUpdating: false,
                isFetching: null,
                countries: [{
                    name: 'Yemen',
                    code: 'YE',
                    dial_code: '967'
                }],
                editCustomer: {
                    id: null,
                    name: '',
                    phone: '',
                    whatsapp_number: '',
                    phone_local: '',
                    phone_country: null,
                    whatsapp_local: '',
                    whatsapp_country: null
                },

                init() {
                    this.editCustomer.phone_country = this.countries[0];
                    this.editCustomer.whatsapp_country = this.countries[0];
                },

                showRow(name, phone, isDebtor) {
                    const matchesSearch = name.toLowerCase().includes(this.search.toLowerCase()) || phone.includes(this
                        .search);
                    const matchesStatus = this.filterStatus === 'all' ||
                        (this.filterStatus === 'debtor' && isDebtor) ||
                        (this.filterStatus === 'cleared' && !isDebtor);
                    return matchesSearch && matchesStatus;
                },

                parsePhoneNumber(fullNumber) {
                    if (!fullNumber) return {
                        country: this.countries[0],
                        local: ''
                    };

                    // Try to match dial code
                    for (let country of this.countries) {
                        if (fullNumber.startsWith(country.dial_code)) {
                            return {
                                country: country,
                                local: fullNumber.substring(country.dial_code.length)
                            };
                        }
                    }
                    return {
                        country: this.countries[0],
                        local: fullNumber
                    };
                },

                async openEditModal(customerId) {
                    this.isFetching = customerId;
                    try {
                        const response = await fetch(`/customers/${customerId}/edit`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();

                        // Parse numbers
                        const parsedPhone = this.parsePhoneNumber(data.phone);
                        const parsedWhatsapp = this.parsePhoneNumber(data.whatsapp_number);

                        this.editCustomer = {
                            ...data,
                            phone_local: parsedPhone.local,
                            phone_country: parsedPhone.country,
                            whatsapp_local: parsedWhatsapp.local,
                            whatsapp_country: parsedWhatsapp.country
                        };

                        this.editModalOpen = true;
                    } catch (error) {
                        console.error("Error fetching customer data:", error);
                        alert("حدث خطأ أثناء جلب بيانات العميل");
                    } finally {
                        this.isFetching = null;
                    }
                }
            }
        }
    </script>
@endsection
