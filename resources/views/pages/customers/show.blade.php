@extends('layouts.app')
@section('title', 'كشف حساب: ' . $customer->name)
@section('addButton')
  <x-modals.success-modal />
    <x-modals.error-modal />
@endsection

@section('content')
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6">
            <div
                 class="relative flex  flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-brand-500">
                     <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">إجمالي قيمة الشحنات</span>
                    <h4 class="text-xl font-black dark:text-white" >{{ number_format($grandTotalCost, 2) }} <span class="text-sm font-medium text-gray-400">ر.ي</span></h4>
                </div>
            </div>

            <div 
                 class="relative flex  flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V22" /></svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">إجمالي المسدد (لك)</span>
                    <h4 class="text-xl font-black dark:text-white">{{ number_format($grandTotalPaid, 2) }} <span class="text-sm font-medium text-success-400">ر.ي</span></h4>
                </div>
            </div>

            <div
                 class="relative flex  flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">المبلغ المتبقي (عليك)</span>
                    <h4 class="text-xl font-black dark:text-white" >{{ number_format($grandTotalRemaining, 2) }} <span class="text-sm font-medium text-gray-400">ر.ي</span></h4>
                </div>
            </div>
            <div
                 class="relative flex  flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest"> عدد الشحنات الغير مسددة</span>
                    <h4 class="text-xl font-black dark:text-white" >{{ $unpaidShipmentsCount }} <span class="text-sm font-medium text-gray-400">شحنة</span></h4>
                </div>
            </div>
        </div>

    <div class="p-4 md:p-6 lg:p-8 bg-[#F8F9FC] dark:bg-gray-950 min-h-screen font-outfit" dir="rtl"
        x-data="customerRegistry()">
        
        @include('pages.customers.edit-customer-modal')
        <div class="max-w-[1400px] mx-auto space-y-6">
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-brand-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 text-2xl font-black">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">{{ $customer->name }}</h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                            <span class="text-theme-xs font-bold text-gray-500 flex items-center gap-1">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                {{ $customer->phone }}
                            </span>
                            <span class="text-[10px] px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-md font-black uppercase tracking-tighter">
                                فرع: {{ $customer->branch_code }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('customers.index') }}" class="h-11 px-5 flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm shadow-theme-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 12H5m7 7l-7-7 7-7" /></svg>
                        العودة للعملاء
                    </a>
                    <button @click="openEditModal({{ $customer->id }})" :disabled="isFetching == {{ $customer->id }}" class="h-11 px-5 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl transition-all shadow-lg shadow-brand-500/20 text-sm disabled:opacity-75">
                        تعديل البيانات
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-theme-xs overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg">
                            <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">سجل الشحنات</h3>
                            <p class="text-xs text-gray-400 mt-0.5">تفاصيل الشحنات المرسلة والمستلمة</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                        <form method="GET" action="{{ url()->current() }}" class="flex items-center">
                            <select name="direction" onchange="this.form.submit()" class="h-9 pr-8 pl-3 text-xs font-bold bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-gray-600 dark:text-gray-300 shadow-sm cursor-pointer">
                                <option value="all">كل الاتجاهات</option>
                                <option value="sent" {{ request('direction') == 'sent' ? 'selected' : '' }}>صادرة (مرسل)</option>
                                <option value="received" {{ request('direction') == 'received' ? 'selected' : '' }}>واردة (مستلم)</option>
                            </select>
                            <select name="payment_method" onchange="this.form.submit()" class="h-9 pr-8 pl-3 text-xs font-bold bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-gray-600 dark:text-gray-300 shadow-sm cursor-pointer">
                                <option value="all">كل طرق الدفع</option>
                                <option value="prepaid" {{ request('payment_method') == 'prepaid' ? 'selected' : '' }}>دفع مسبق</option>
                                <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>دفع عند الاستلام</option>
                                <option value="customer_credit" {{ request('payment_method') == 'customer_credit' ? 'selected' : '' }}>آجل (دين)</option>
                                <option value="partial_payment" {{ request('payment_method') == 'partial_payment' ? 'selected' : '' }}>دفع جزئي</option>
                            </select>
                        </form>
                        <div class="px-3 py-1 bg-white dark:bg-gray-800 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm">
                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300">
                            عدد الحركات: <span class="text-brand-600 dark:text-brand-400">{{ $shipments->total() }}</span>
                        </span>
                    </div>
                 </div>
                    
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-800">
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">رقم التتبع / التاريخ</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">اتجاه الشحنة</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">طريقة الدفع</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">مبلغ الشحنة</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">المسار (من - إلى)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800 bg-white dark:bg-gray-900">
    @forelse($shipments as $shipment)
        @php
            $isSender = $shipment->sender_customer_id == $customer->id;
            
            // 1. حساب المدفوع والمتبقي لهذه الشحنة
            $paidAmount = $shipment->payments->sum('amount');
            $remainingAmount = $shipment->total_amount - $paidAmount;
            $isUnpaid = $remainingAmount > 0;

            // تحديد تسمية طريقة الدفع
            $paymentLabel = match($shipment->payment_method) {
                'prepaid' => 'دفع مسبق',
                'cod' => 'دفع عند الاستلام',
                'customer_credit' => 'آجل (دين)',
                'partial_payment' => 'دفع جزئي',
                default => $shipment->payment_method
            };

            // تحديد لون طريقة الدفع
            $paymentClass = match($shipment->payment_method) {
                'prepaid' => 'bg-success-50 text-success-700 border-success-100',
                'cod' => 'bg-warning-50 text-warning-700 border-warning-100',
                'customer_credit' => 'bg-error-50 text-error-700 border-error-100',
                'partial_payment' => 'bg-brand-50 text-brand-700 border-brand-100',
                default => 'bg-gray-50 text-gray-600 border-gray-100'
            };
        @endphp

        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors duration-200">
            
            <td class="px-6 py-4 align-top">
                <div class="flex flex-col">
                    <span class="text-xs font-black text-gray-800 dark:text-white font-mono tracking-wide">{{ $shipment->tracking_number ?? $shipment->bond_number }}</span>
                    <span class="text-[10px] text-gray-400 mt-1 font-medium">{{ $shipment->created_at->format('Y-m-d h:i A') }}</span>
                </div>
            </td>

            <td class="px-6 py-4 align-top">
                @if($isSender)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20">
                        <svg class="w-3 h-3 rotate-45 rtl:-rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                        صادرة (مرسل)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20">
                        <svg class="w-3 h-3 rotate-45 rtl:-rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                        واردة (مستلم)
                    </span>
                @endif
            </td>

            <td class="px-6 py-4 align-top">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $paymentClass }} dark:bg-opacity-10 dark:border-opacity-20">
                    {{ $paymentLabel }}
                </span>
            </td>

            <td class="px-6 py-4 align-top">
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-black font-mono text-gray-900 dark:text-white">
                        {{ number_format($shipment->total_amount, 2) }}
                        <span class="text-[10px] text-gray-400 font-sans">ر.ي</span>
                    </span>
                    @php
            // 1. هل المستخدم الحالي هو المرسل؟
            $isSender = $shipment->sender_customer_id == $customer->id;

            // 2. الحسابات المالية
            $paidAmount = $shipment->payments->sum('amount');
            $remainingAmount = $shipment->total_amount - $paidAmount;
            
            // 3. متى يظهر زر السداد؟
            // أ) يجب أن يكون هناك مبلغ متبقي (أكبر من صفر)
            // ب) يجب أن يكون المستخدم هو المرسل (لأنك حددت في الكويري sender)
            // ج) يجب أن تكون طريقة الدفع "آجل" (customer_credit)
            $shouldShowPayButton = ($remainingAmount > 0) 
                                    && $isSender 
                                    && ($shipment->payment_method == 'customer_credit');
        @endphp

                   @if($shouldShowPayButton)
            
            <div class="flex flex-col items-start gap-2 mt-2">
                <span class="text-[10px] font-bold text-error-600 bg-error-50 dark:bg-error-500/10 px-2 py-1 rounded border border-error-100 dark:border-error-500/20">
                    مستحق عليك: {{ number_format($remainingAmount, 2) }}
                </span>
                
                <button onclick="openPaymentModal({{ $shipment->id }}, {{ $remainingAmount }})" 
        class="w-full px-3 py-1.5 bg-success-400 hover:bg-success-500 text-[11px] font-bold rounded-lg shadow-md shadow-success-500/20 transition-all flex items-center justify-center gap-1.5">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
    سداد الدين
</button>
            </div>

        @elseif($remainingAmount <= 0)
            
            <span class="text-[10px] text-success-600 font-bold flex items-center gap-1 mt-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                خالص (مدفوع)
            </span>

        @else
            
            <span class="text-[10px] text-gray-400 mt-1">
                {{ $paymentLabel }}
            </span>

        @endif
                </div>
            </td>

            <td class="px-6 py-4 align-top">
                <div class="flex items-center gap-2">
                    <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded px-2 py-1.5 border border-gray-200 dark:border-gray-700">
                        <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">{{ $shipment->senderBranch->name ?? $shipment->sender_branch_code }}</span>
                        <svg class="w-3 h-3 text-gray-400 mx-2 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">{{ $shipment->receiverBranch->name ?? $shipment->receiver_branch_code }}</span>
                    </div>
                </div>
            </td>

        </tr>
    @empty
        <tr>
            <td colspan="5" class="py-24">
                <div class="flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <h3 class="text-gray-900 dark:text-white font-bold text-base">لا توجد شحنات</h3>
                    <p class="text-xs text-gray-400 mt-1">لم يتم العثور على أي شحنات مرتبطة بهذا العميل.</p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
                    </table>
                </div>

                @if ($shipments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 flex justify-center" dir="ltr">
                        {{ $shipments->appends(request()->query())->links() }}
                    </div>
                 @endif
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        function customerRegistry() {
            return {
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

                parsePhoneNumber(fullNumber) {
                    if (!fullNumber) return {
                        country: this.countries[0],
                        local: ''
                    };
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