@extends('layouts.app')
@section('title', 'كشف حساب: ' . $customer->name)

@section('content')
    <div class="p-4 md:p-6 lg:p-8 bg-[#F8F9FC] dark:bg-gray-950 min-h-screen font-outfit" dir="rtl"
        x-data="customerRegistry()">
        @include('pages.customers.edit-customer-modal')
        <x-modals.success-modal />
        <x-modals.error-modal />

        <div class="max-w-[1400px] mx-auto space-y-4">
            <div class="lg:col-span-8 grid grid-cols-1 xl:grid-cols-3 gap-4 my-4">
                <div
                    class="bg-white dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center text-brand-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">إجمالي ما عليه</p>
                        <h4 class="text-xl font-black dark:text-white"> {{ number_format($debit, 2) }}
                        </h4>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm flex items-center gap-4 border-r-4 border-r-error-500">
                    <div
                        class="w-12 h-12 rounded-xl bg-error-50 dark:bg-error-500/10 flex items-center justify-center text-error-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-error-500 uppercase tracking-widest">المديونين</p>
                        <h4 class="text-xl font-black dark:text-white"> {{ number_format($credit, 2) }}
                        </h4>
                    </div>
                </div>


            </div>
            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-white/[0.03] p-6 rounded-2xl border my-4 border-gray-100 dark:border-gray-800 shadow-theme-sm">
                <div class="flex items-center gap-5">
                    <div
                        class="w-16 h-16 bg-brand-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 text-2xl font-black">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">{{ $customer->name }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                            <span class="text-theme-xs font-bold text-gray-500 flex items-center gap-1">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $customer->phone }}
                            </span>
                            @if ($customer->whatsapp_number)
                                <span class="text-theme-xs font-bold text-success-500 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-success-500 animate-pulse"></span>
                                    واتساب: {{ $customer->whatsapp_number }}
                                </span>
                            @endif
                            <span
                                class="text-[10px] px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-md font-black uppercase tracking-tighter">
                                فرع: {{ $customer->branch_code }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('customers.index') }}"
                        class="h-11 px-5 flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm shadow-theme-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 12H5m7 7l-7-7 7-7" />
                        </svg>
                        العودة للعملاء
                    </a>
                    <button @click="openEditModal({{ $customer->id }})" :disabled="isFetching == {{ $customer->id }}"
                        class="h-11 px-5 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl transition-all shadow-lg shadow-brand-500/20 text-sm disabled:opacity-75">
                        <template x-if="isFetching == {{ $customer->id }}">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </template>
                        <template x-if="isFetching != {{ $customer->id }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </template>
                        تعديل البيانات
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
    <div class="relative overflow-hidden rounded-3xl bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 shadow-theme-sm transition-all duration-300 group hover:shadow-theme-md">
        
        <div class="absolute top-0 left-0 w-full h-full opacity-30 pointer-events-none">
             <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full blur-3xl {{ $balance > 0 ? 'bg-error-500/20' : 'bg-success-500/20' }}"></div>
             <div class="absolute -left-10 -bottom-10 w-32 h-32 rounded-full blur-3xl {{ $balance > 0 ? 'bg-error-500/10' : 'bg-success-500/10' }}"></div>
        </div>

        <div class="relative p-6 flex items-center justify-between z-10">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full {{ $balance > 0 ? 'bg-error-500 animate-pulse' : 'bg-success-500' }}"></div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                        صافي الرصيد الحالي
                    </p>
                </div>
                
                <div class="flex items-baseline gap-1">
                    <h3 class="text-4xl font-black {{ $balance > 0 ? 'text-error-600 dark:text-error-400' : 'text-success-600 dark:text-success-400' }} tracking-tight tabular-nums">
                        {{ number_format(abs($balance), 2) }}
                    </h3>
                    <span class="text-sm font-bold text-gray-400">ر.ي</span>
                </div>
                
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold border {{ $balance > 0 ? 'bg-error-50 text-error-600 border-error-100 dark:bg-error-500/10 dark:text-error-400 dark:border-error-500/20' : 'bg-success-50 text-success-600 border-success-100 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/20' }}">
                    {{ $balance > 0 ? 'رصيد مدين (عليك)' : 'رصيد دائن (لك)' }}
                </span>
            </div>

            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm {{ $balance > 0 ? 'bg-gradient-to-br from-error-50 to-error-100 text-error-600 dark:from-error-900/50 dark:to-error-800/50 dark:text-error-400' : 'bg-gradient-to-br from-success-50 to-success-100 text-success-600 dark:from-success-900/50 dark:to-success-800/50 dark:text-success-400' }}">
                @if ($balance > 0)
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>
                @else
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 shadow-theme-xs overflow-hidden">

    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50 dark:bg-gray-900/50">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-brand-50 dark:bg-brand-500/10 rounded-lg">
                <svg class="w-5 h-5 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">سجل الحركات المالية</h3>
                <p class="text-xs text-gray-400 mt-0.5">تفاصيل العمليات الصادرة والواردة</p>
            </div>
        </div>
        <div class="px-3 py-1 bg-white dark:bg-gray-800 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">
                إجمالي: <span class="text-brand-600 dark:text-brand-400">{{ $transactions->total() }}</span>
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-800">
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">التاريخ والوقت</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">نوع العملية</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">المبلغ</th>
                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">التفاصيل</th>
                    <th class="px-6 py-4 text-center text-[11px] font-bold text-gray-400 uppercase tracking-wider">رقم السند</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800 bg-white dark:bg-gray-900">
                @forelse($transactions as $t)
                    @php
                        $isShipment = $t instanceof \App\Models\Shipment;
                        if ($isShipment) {
                             $isSender = $t->sender_customer_id == $customer->id;
                             $pm = $t->payment_method;
                             
                             $pmLabel = match ($pm) {
                                 'prepaid' => 'دفع مسبق',
                                 'cod' => 'عند الاستلام',
                                 'customer_credit' => 'آجل',
                                 'partial_payment' => 'دفع جزئي',
                                 default => $pm,
                             };

                             // تصميم الشارات (Badges)
                             $pmClass = match ($pm) {
                                 'prepaid' => 'bg-success-50 text-success-700 border-success-100 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/20',
                                 'cod' => 'bg-warning-50 text-warning-700 border-warning-100 dark:bg-warning-500/10 dark:text-warning-400 dark:border-warning-500/20',
                                 'customer_credit' => 'bg-brand-50 text-brand-700 border-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20',
                                 default => 'bg-gray-50 text-gray-600 border-gray-100 dark:bg-gray-800 dark:text-gray-400',
                             };
                        }
                    @endphp
                    
                    <tr class="group hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors duration-200">
                        
                        <td class="px-6 py-4 align-top w-[140px]">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-200 font-mono">{{ $t->created_at->format('Y-m-d') }}</span>
                                <span class="text-[10px] text-gray-400 mt-1">{{ $t->created_at->format('h:i A') }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-4 align-top w-[180px]">
                            @if ($isShipment)
                                <div class="flex flex-col items-start gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $isSender ? 'bg-success-50 text-success-700 border-success-100 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/20' : 'bg-brand-50 text-brand-700 border-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isSender ? 'bg-success-500' : 'bg-brand-500' }}"></span>
                                        {{ $isSender ? 'شحنة صادرة' : 'شحنة واردة' }}
                                    </span>
                                    
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border {{ $pmClass }} text-[10px] font-bold">
                                        @if($pm === 'customer_credit')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        @elseif($pm === 'cod')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        @endif
                                        {{ $pmLabel }}
                                    </span>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $t->type === 'debit' ? 'bg-error-50 text-error-700 border-error-100 dark:bg-error-500/10 dark:text-error-400 dark:border-error-500/20' : 'bg-success-50 text-success-700 border-success-100 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/20' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $t->type === 'debit' ? 'bg-error-500' : 'bg-success-500' }}"></span>
                                    {{ $t->type === 'debit' ? 'سحب رصيد' : 'إيداع رصيد' }}
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 align-top">
                             <div class="flex flex-col">
                                <span class="text-sm font-black font-mono {{ $isShipment ? 'text-gray-900 dark:text-white' : ($t->type === 'debit' ? 'text-error-600 dark:text-error-400' : 'text-success-600 dark:text-success-400') }}">
                                    {{ $isShipment ? '' : ($t->type === 'debit' ? '-' : '+') }}
                                    {{ number_format($isShipment ? $t->total_amount : $t->amount, 2) }}
                                    <span class="text-[10px] text-gray-400 font-sans font-bold">ر.ي</span>
                                </span>
                                @if ($isShipment)
                                    @if ($t->payment_method === 'customer_credit')
                                        <span class="text-[9px] text-error-500 mt-1 font-bold">مستحقة الدفع</span>
                                    @elseif($t->payment_method === 'cod' && !$isSender)
                                        <span class="text-[9px] text-warning-600 mt-1 font-bold">مطلوب السداد</span>
                                    @endif
                                @endif
                             </div>
                        </td>

                        <td class="px-6 py-4 align-top">
                             @if ($isShipment)
                                <div class="max-w-[280px]">
                                    <p class="text-xs font-bold text-gray-700 dark:text-gray-300 truncate" title="{{ $t->notes }}">{{ $t->notes ?? '-' }}</p>
                                    
                                    <div class="flex items-center gap-1 mt-2">
                                        <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded px-2 py-1">
                                            <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">{{ $t->senderBranch->name ?? $t->sender_branch_code }}</span>
                                            <svg class="w-3 h-3 text-gray-400 mx-1 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                                            <span class="text-[10px] font-medium text-gray-600 dark:text-gray-400">{{ $t->receiverBranch->name ?? $t->receiver_branch_code }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 max-w-[280px] leading-relaxed" title="{{ $t->description }}">
                                    {{ $t->description ?? 'لا يوجد وصف متاح' }}
                                </p>
                            @endif
                        </td>

                        <td class="px-6 py-4 align-top text-center">
                            <span class="inline-block px-2 py-1 bg-gray-50 dark:bg-gray-800 rounded-md text-[10px] font-mono font-bold text-gray-500 border border-gray-200 dark:border-gray-700 select-all hover:border-brand-300 dark:hover:border-brand-700 transition-colors cursor-pointer" title="انقر للنسخ">
                                {{ $isShipment ? $t->bond_number : ($t->reference_id ? '#' . $t->reference_id : '---') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-32">
                            <div class="flex flex-col items-center justify-center text-center">
                                <div class="relative w-20 h-20 mb-4">
                                    <div class="absolute inset-0 bg-brand-100 dark:bg-brand-900/20 rounded-full animate-ping opacity-20"></div>
                                    <div class="relative w-20 h-20 bg-brand-50 dark:bg-gray-800 rounded-full flex items-center justify-center border border-brand-100 dark:border-gray-700">
                                        <svg class="w-8 h-8 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                </div>
                                <h3 class="text-gray-900 dark:text-white font-bold text-base">لا توجد حركات مالية</h3>
                                <p class="text-xs text-gray-400 mt-1 max-w-xs mx-auto">لم يتم تسجيل أي عمليات شحن أو حركات مالية لهذا الحساب حتى الآن.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($transactions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 flex justify-center">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
        </div>
    </div>

    <style>
        /* تحسينات بصرية إضافية لمخطط الحسابات */
        .shadow-theme-sm {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
        }

        .shadow-inner {
            box-shadow: inset 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        h2,
        h3,
        h4 {
            letter-spacing: -0.02em;
        }

        .font-black {
            font-weight: 900;
        }
    </style>
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
