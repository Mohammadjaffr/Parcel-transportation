@extends('layouts.app')

@section('title', 'ملف العميل | ' . $customer->name)

@section('content')
    @php
        $credit  = $customer->sum_credit ?? 0;
        $debit   = $customer->sum_debit ?? 0;
        $balance = $credit - $debit;

        $customerInitials = collect(explode(' ', trim($customer->name)))
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_substr($word, 0, 1, 'utf-8'))
            ->implode('');
    @endphp

    <div
        x-data="{
            activeTab: new URLSearchParams(window.location.search).has('ship_page') || new URLSearchParams(window.location.search).has('direction') ? 'shipments' : 'financials',
            showPaymentModal: false,
            amountToPay: {{ abs($balance) }},
            searchQuery: ''
        }"
        class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12"
        dir="rtl">

        {{-- ================= Modal: تسديد مديونية / صرف رصيد ================= --}}
        <template x-teleport="body">
            <div x-show="showPaymentModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4" dir="rtl">
                <div
                    x-show="showPaymentModal"
                    x-transition.opacity
                    class="fixed inset-0 backdrop-blur-sm bg-slate-900/40 dark:bg-black/60"
                    @click="showPaymentModal = false">
                </div>

                <div
                    x-show="showPaymentModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                    class="relative z-10 p-6 w-full max-w-sm bg-white rounded-[2rem] border border-slate-100 shadow-2xl dark:bg-boxdark dark:border-boxdark-2">

                    <div class="flex justify-between items-center mb-5">
                        <h3 class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white font-headline">
                            <span class="material-symbols-outlined {{ $balance < 0 ? 'text-primary' : 'text-emerald-500' }}">
                                account_balance_wallet
                            </span>
                            {{ $balance < 0 ? 'تسديد مديونية' : 'صرف رصيد للعميل' }}
                        </h3>

                        <button type="button" @click="showPaymentModal = false" class="transition-colors text-slate-400 hover:text-rose-500">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('customers.addPayment', $customer->id) }}" method="POST">
                        @csrf
                        
                        {{-- حقل مخفي يخبر الكنترولر بنوع العملية (سداد أم صرف) --}}
                        <input type="hidden" name="transaction_action" value="{{ $balance < 0 ? 'pay_debt' : 'withdraw_balance' }}">

                        <div class="mb-4">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                {{ $balance < 0 ? 'المبلغ المراد سداده' : 'المبلغ المراد صرفه للعميل' }}
                            </label>

                            <div class="relative">
                                <input
                                    type="number"
                                    name="amount"
                                    x-model="amountToPay"
                                    step="0.01"
                                    min="1"
                                    max="{{ abs($balance) }}"
                                    required
                                    class="px-4 py-3 pl-12 w-full text-lg font-black rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-800 dark:text-white focus:ring-2 {{ $balance < 0 ? 'focus:ring-primary/20' : 'focus:ring-emerald-500/20' }}">

                                <span class="absolute left-4 top-1/2 text-sm font-bold -translate-y-1/2 text-slate-400">
                                    ريال
                                </span>
                            </div>

                            <p class="mt-1.5 text-[10px] text-slate-400">
                                {{ $balance < 0 ? 'يمكنك تسديد المبلغ كاملاً أو إدخال دفعة جزئية.' : 'يمكنك صرف الرصيد كاملاً أو سحب جزء منه.' }}
                            </p>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                ملاحظات (اختياري)
                            </label>

                            <input
                                type="text"
                                name="notes"
                                placeholder="مثال: تحويل بنكي، كاش للمندوب..."
                                class="px-4 py-3 w-full text-sm font-bold rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 {{ $balance < 0 ? 'focus:ring-primary/20' : 'focus:ring-emerald-500/20' }}">
                        </div>

                        <button
                            type="submit"
                            class="flex gap-2 justify-center items-center w-full py-3.5 text-sm font-black text-white rounded-xl shadow-[0_4px_12px_rgba(30,41,59,0.3)] transition-all active:scale-95 {{ $balance < 0 ? 'bg-slate-800 dark:bg-primary dark:shadow-primary/30 hover:bg-slate-900 dark:hover:bg-primary-hover' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            <span class="material-symbols-outlined text-[18px]">done_all</span>
                            {{ $balance < 0 ? 'تأكيد السداد' : 'تأكيد الصرف' }}
                        </button>
                    </form>
                </div>
            </div>
        </template>

        {{-- ================= Header ================= --}}
        <div class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
            <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
                <div class="flex gap-4 items-center min-w-0">
                    <a href="{{ route('customers.index') }}"
                        class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>

                    <div class="min-w-0">
                        <h1 class="text-xl font-black truncate md:text-2xl font-headline text-on-surface dark:text-white">
                            {{ $customer->name }}
                        </h1>

                        <div class="flex gap-2 items-center mt-1">
                            <x-phone-number
                                :value="$customer->phone"
                                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />

                            <span class="hidden w-1 h-1 bg-gray-300 rounded-full md:inline-block dark:bg-gray-600"></span>

                            <span class="hidden text-xs font-bold text-gray-500 md:inline-block dark:text-bodydark">
                                تفاصيل الحساب وسجل الشحنات
                            </span>
                        </div>
                    </div>
                </div>

                
                <div class="flex gap-2 items-center">
                    {{-- زر مراسلة العميل واتساب --}}
                    <a href="{{ $customer->waUrl }}" target="_blank"
                        class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform active:scale-95 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 hover:shadow-md">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>
                    </a>

                    <a href="{{ route('whatsapp.customer.account.statement', $customer->id) }}"
                        target="_blank"
                        class="flex gap-2 justify-center items-center px-3 h-10 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform md:px-4 dark:bg-emerald-500/10 dark:text-emerald-400 active:scale-95 dark:border-emerald-500/20 hover:shadow-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span class="hidden md:inline">إرسال كشف الحساب</span>
                    </a>

                    <a href="{{ route('receipt.generate', ['type' => 'CustomerAccountStatementReceipt', 'id' => $customer->uuid ?? $customer->id]) }}"
                        target="_blank"
                        class="flex gap-2 justify-center items-center px-3 h-10 text-xs font-bold rounded-xl border transition-transform text-primary bg-primary-container border-primary/10 md:px-4 dark:bg-primary/10 dark:text-primary active:scale-95 hover:shadow-md hover:bg-primary/10 dark:hover:bg-primary/20">
                        <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                        <span class="hidden md:inline">التقرير المالي</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ================= Page Content ================= --}}
        <div class="flex flex-col gap-6 p-4 mx-auto max-w-7xl md:p-6">

            {{-- ================= الملخص المالي ================= --}}
            <div class="p-6 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex flex-col gap-4 justify-between mb-5 md:flex-row md:items-center">
                    <div>
                        <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                            <span class="material-symbols-outlined text-primary bg-primary-container dark:bg-primary/10 p-1.5 rounded-lg text-[18px]">
                                account_balance_wallet
                            </span>
                            الملخص المالي
                        </h3>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            ملخص الرصيد والمتحصلات وأجور الشحن الخاصة بالعميل
                        </p>
                    </div>

                    @if ($balance != 0)
                        <button
                            @click="showPaymentModal = true"
                            type="button"
                            class="flex gap-2 items-center px-4 h-10 text-xs font-black text-white rounded-xl shadow-sm transition-all active:scale-95 {{ $balance < 0 ? 'bg-slate-800 hover:bg-slate-700 dark:bg-white dark:text-slate-800 dark:hover:bg-gray-100' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            <span class="material-symbols-outlined text-[16px]">
                                {{ $balance < 0 ? 'payments' : 'request_quote' }}
                            </span>
                            {{ $balance < 0 ? 'تسديد مبلغ' : 'صرف رصيد' }}
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    <div class="relative overflow-hidden p-5 rounded-[1.5rem] border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="absolute right-0 top-0 bottom-0 w-1.5 {{ $balance >= 0 ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>

                        <p class="mb-2 text-xs font-bold text-gray-500 dark:text-bodydark">
                            الرصيد الصافي للعميل
                        </p>

                        <p class="text-3xl font-black font-headline {{ $balance >= 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400' }}">
                            {{ number_format(abs($balance), 0) }}
                            <span class="text-xs {{ $balance >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">ريال</span>
                        </p>

                        <div class="inline-flex px-3 py-1.5 mt-3 rounded-lg text-[10px] font-black {{ $balance >= 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' }}">
                            {{ $balance >= 0 ? 'رصيد لصالحه (له)' : 'مطلوب سداده (عليه)' }}
                        </div>
                    </div>

                    <div class="p-5 rounded-[1.5rem] border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                    متحصلات العميل (له)
                                </p>
                                <p class="mt-2 text-2xl font-black text-emerald-500 dark:text-emerald-400 font-headline">
                                    {{ number_format($credit, 0) }}
                                </p>
                            </div>

                            <div class="flex justify-center items-center w-11 h-11 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                                <span class="material-symbols-outlined text-[22px]">south_west</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 rounded-[1.5rem] border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                    أجور شحن (عليه)
                                </p>
                                <p class="mt-2 text-2xl font-black text-rose-500 dark:text-rose-400 font-headline">
                                    {{ number_format($debit, 0) }}
                                </p>
                            </div>

                            <div class="flex justify-center items-center w-11 h-11 text-rose-500 bg-rose-50 rounded-xl dark:bg-rose-500/10">
                                <span class="material-symbols-outlined text-[22px]">north_east</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($unpaidShipmentsCount) && $unpaidShipmentsCount > 0)
                    <div class="flex gap-2.5 items-start px-4 py-3 mt-4 text-xs font-bold leading-relaxed text-rose-600 rounded-xl border border-rose-100 bg-rose-50/50 dark:bg-rose-500/5 dark:border-rose-500/20 dark:text-rose-400">
                        <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">info</span>
                        <div>
                            يوجد لدى العميل
                            <span class="px-1 font-black">{{ $unpaidShipmentsCount }}</span>
                            شحنات غير مسددة أو مسددة جزئياً، يرجى المتابعة.
                        </div>
                    </div>
                @endif
            </div>

            {{-- ================= التبويبات ================= --}}
            <div class="flex p-1 bg-gray-100 dark:bg-boxdark rounded-[1rem]">
                <button
                    @click="activeTab = 'financials'"
                    :class="activeTab === 'financials' ? 'bg-white dark:bg-boxdark-2 text-primary shadow-sm' : 'text-gray-500 dark:text-bodydark hover:text-gray-700 dark:hover:text-white'"
                    class="flex flex-1 gap-1.5 justify-center items-center py-2.5 text-xs font-bold rounded-[0.75rem] transition-all">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    كشف الحساب
                </button>

                <button
                    @click="activeTab = 'shipments'"
                    :class="activeTab === 'shipments' ? 'bg-white dark:bg-boxdark-2 text-primary shadow-sm' : 'text-gray-500 dark:text-bodydark hover:text-gray-700 dark:hover:text-white'"
                    class="flex flex-1 gap-1.5 justify-center items-center py-2.5 text-xs font-bold rounded-[0.75rem] transition-all">
                    <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                    سجل الطرود
                </button>
            </div>

            {{-- ================= تبويب الحركات المالية ================= --}}
            <div
                x-show="activeTab === 'financials'"
                style="display: none;"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0">

                {{-- تمت إزالة overflow-hidden للسماح للقائمة بالظهور بحرية --}}
                <div class="bg-white border border-gray-100 shadow-sm dark:bg-boxdark rounded-[2rem] dark:border-boxdark-2 relative">

                    <div class="flex flex-col gap-3 justify-between p-5 border-b border-gray-100 md:flex-row md:items-center dark:border-boxdark-2">
                        <div>
                            <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                                <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                                الحركات المالية الأخيرة
                            </h3>
                            <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                                كشف مختصر لآخر الحركات المالية على حساب العميل.
                            </p>
                        </div>
                    </div>

                    {{-- Desktop Table: تمت إزالة overflow-x-auto لكي لا تقص القوائم --}}
                    <div class="hidden pb-4 md:block">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
                                    <th class="px-5 py-4">#</th>
                                    <th class="px-5 py-4">التاريخ</th>
                                    <th class="px-5 py-4">نوع الحركة</th>
                                    <th class="px-5 py-4">البيان</th>
                                    <th class="px-5 py-4 text-left">المبلغ</th>
                                    <th class="px-5 py-4 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                                @forelse($transactions as $trans)
                                    @php
                                        $waMessage = "مرحباً، نرفق لكم تفاصيل الحركة المالية:\n\n"
                                            . "نوع الحركة: " . ($trans->type == 'credit' ? 'إيداع/لصالحك' : 'خصم/عليك') . "\n"
                                            . "المبلغ: " . number_format($trans->amount, 2) . " ريال\n"
                                            . "البيان: " . $trans->description . "\n"
                                            . "التاريخ: " . $trans->created_at->format('Y-m-d h:i A');
                                        $waUrl = "https://wa.me/?text=" . urlencode($waMessage);
                                    @endphp
                                    <tr x-data="{ showTransactionDetails: false }" class="transition-colors hover:bg-gray-50/70 dark:hover:bg-boxdark-2/50 group">
                                        
                                        <td class="px-5 py-4">
                                            <span class="text-xs font-black text-gray-400">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-5 py-4">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-xs font-black text-gray-700 dark:text-gray-200">
                                                    {{ $trans->created_at->format('Y-m-d') }}
                                                </span>
                                                <span class="text-[10px] font-bold text-gray-400">
                                                    {{ $trans->created_at->format('h:i A') }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="px-5 py-4">
                                            @if($trans->type == 'credit')
                                                <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                                    <span class="material-symbols-outlined text-[14px]">south_west</span>
                                                    تحصيل / سداد
                                                </span>
                                            @else
                                                <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black rounded-xl bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                                                    <span class="material-symbols-outlined text-[14px]">north_east</span>
                                                    مديونية / رسوم
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-5 py-4">
                                            <p class="max-w-[360px] text-xs font-bold leading-6 text-gray-600 dark:text-gray-300 truncate" title="{{ $trans->description }}">
                                                {{ $trans->description }}
                                            </p>
                                        </td>
                                        
                                        <td class="px-5 py-4 text-left">
                                            <span class="text-sm font-black font-headline {{ $trans->type == 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                {{ $trans->type == 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-5 py-4 text-center">
                                            {{-- Dropdown الإجراءات --}}
                                            <div x-data="{ open: false }" @click.outside="open = false" class="inline-block relative text-right">
                                                <button @click="open = !open" type="button" class="p-2 text-gray-400 rounded-lg transition-colors hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-boxdark-2 dark:hover:text-white">
                                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                                </button>
                                                
                                                {{-- تم توحيد الاتجاه للأسفل (top-full mt-1) دائماً، لأننا أزلنا القيود (overflow-hidden) --}}
                                                <div x-show="open" 
                                                     x-transition.opacity.duration.200ms
                                                     style="display: none;"
                                                     class="absolute left-0 top-full mt-1 w-44 bg-white dark:bg-boxdark-2 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.12)] border border-gray-100 dark:border-boxdark z-[99] py-1.5">
                                                     
                                                    <button @click="showTransactionDetails = true; open = false" class="flex gap-2 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-gray-700 transition-colors dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-boxdark hover:text-primary">
                                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                                        عرض التفاصيل
                                                    </button>
                                                    
                                                    <a href="{{ $trans->waUrl ?? $waUrl }}" target="_blank" class="w-full text-right flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-[#25D366]/10 hover:text-[#25D366] transition-colors">
                                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                        </svg>                                                          إرسال واتساب
                                                    </a>
                                                    
                                                    <a href="{{ $trans->printUrl ?? route('receipt.generate', ['type' => 'TransactionReceipt', 'id' => $trans->id]) }}" target="_blank" class="flex gap-2 items-center px-4 py-2.5 w-full text-xs font-bold text-right text-gray-700 transition-colors dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-boxdark hover:text-slate-900 dark:hover:text-white">
                                                        <span class="material-symbols-outlined text-[16px]">print</span>
                                                        طباعة السند
                                                    </a>
                                                </div>
                                            </div>

                                            {{-- Modal التفاصيل مع x-teleport لضمان ظهوره بشكل سليم فوق كامل الصفحة --}}
                                            <template x-teleport="body">
                                                <div x-show="showTransactionDetails" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4" dir="rtl" @click.stop>
                                                    <div x-show="showTransactionDetails" x-transition.opacity class="fixed inset-0 backdrop-blur-sm bg-slate-900/40 dark:bg-black/60" @click="showTransactionDetails = false"></div>
                                                    
                                                    <div x-show="showTransactionDetails" 
                                                         x-transition:enter="transition ease-out duration-300"
                                                         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                                                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                         x-transition:leave="transition ease-in duration-200"
                                                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                         x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                                                         class="bg-white dark:bg-boxdark w-full max-w-sm rounded-[2rem] p-6 shadow-2xl relative z-[101] border border-slate-100 dark:border-boxdark-2 text-right">
                                                        
                                                        <button @click="showTransactionDetails = false" class="flex absolute top-4 left-4 justify-center items-center w-8 h-8 rounded-full transition-colors bg-slate-50 dark:bg-boxdark-2 text-slate-400 hover:text-rose-500">
                                                            <span class="material-symbols-outlined text-[18px]">close</span>
                                                        </button>
                                                        
                                                        <div class="mt-2 mb-6 text-center">
                                                            <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-3 {{ $trans->type == 'credit' ? 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400' }}">
                                                                <span class="material-symbols-outlined text-[28px]">
                                                                    {{ $trans->type == 'credit' ? 'add_card' : 'credit_score' }}
                                                                </span>
                                                            </div>
                                                            <h3 class="text-lg font-black font-headline {{ $trans->type == 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                                {{ $trans->type == 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }} <span class="text-sm">ريال</span>
                                                            </h3>
                                                            <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $trans->created_at->format('Y-m-d - h:i A') }}</p>
                                                        </div>
                                                        
                                                        <div class="p-4 mb-6 text-right rounded-xl bg-slate-50 dark:bg-boxdark-2">
                                                            <p class="text-[10px] font-bold text-slate-400 mb-1">البيان (التفاصيل الكاملة):</p>
                                                            <p class="text-sm font-bold leading-relaxed whitespace-pre-wrap text-slate-700 dark:text-gray-300">{{ $trans->description }}</p>
                                                        </div>
                                                        
                                                        <div class="grid grid-cols-2 gap-3">
                                                            <a href="{{ $trans->waUrl ?? $waUrl }}" target="_blank" class="flex items-center justify-center gap-1.5 py-3 bg-[#25D366] text-white rounded-xl text-xs font-black shadow-sm hover:bg-[#20bd5a] active:scale-95 transition-all">
                                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                                                إرسال واتساب
                                                            </a>
                                                            <a href="{{ $trans->printUrl ?? route('receipt.generate', ['type' => 'TransactionReceipt', 'id' => $trans->id]) }}" target="_blank" class="flex gap-1.5 justify-center items-center py-3 text-xs font-black text-white rounded-xl shadow-sm transition-all bg-slate-800 dark:bg-primary hover:bg-slate-900 dark:hover:bg-primary-hover active:scale-95">
                                                                <span class="material-symbols-outlined text-[16px]">print</span>
                                                                طباعة السند
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-20 text-center">
                                            <div class="flex flex-col justify-center items-center">
                                                <div class="flex justify-center items-center mb-4 w-16 h-16 bg-gray-50 rounded-2xl dark:bg-boxdark-2">
                                                    <span class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">receipt_long</span>
                                                </div>
                                                <p class="text-sm font-bold text-gray-500 dark:text-bodydark">
                                                    لا توجد حركات مالية مسجلة
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="flex flex-col gap-3 p-4 md:hidden">
                        @forelse($transactions as $trans)
                            @php
                                $waMessage = "مرحباً، نرفق لكم تفاصيل الحركة المالية:\n\n"
                                    . "نوع الحركة: " . ($trans->type == 'credit' ? 'إيداع/لصالحك' : 'خصم/عليك') . "\n"
                                    . "المبلغ: " . number_format($trans->amount, 2) . " ريال\n"
                                    . "البيان: " . $trans->description . "\n"
                                    . "التاريخ: " . $trans->created_at->format('Y-m-d h:i A');
                                $waUrl = "https://wa.me/?text=" . urlencode($waMessage);
                            @endphp
                            
                            <div x-data="{ showTransactionDetails: false }">
                                <div @click="showTransactionDetails = true" class="p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark cursor-pointer active:scale-[0.98] transition-transform">
                                    <div class="flex gap-3 justify-between items-start">
                                        <div class="flex gap-3 items-start min-w-0">
                                            <div class="flex justify-center items-center w-11 h-11 rounded-xl shrink-0 {{ $trans->type == 'credit' ? 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400' }}">
                                                <span class="material-symbols-outlined text-[22px]">
                                                    {{ $trans->type == 'credit' ? 'south_west' : 'north_east' }}
                                                </span>
                                            </div>

                                            <div class="min-w-0">
                                                <p class="text-xs font-black text-gray-800 dark:text-white">
                                                    {{ $trans->type == 'credit' ? 'تحصيل / سداد' : 'مديونية / رسوم' }}
                                                </p>
                                                <p class="mt-1 text-[11px] font-bold leading-5 text-gray-500 dark:text-bodydark truncate">
                                                    {{ $trans->description }}
                                                </p>
                                                <p class="mt-2 text-[10px] font-bold text-gray-400">
                                                    {{ $trans->created_at->format('Y-m-d h:i A') }}
                                                </p>
                                            </div>
                                        </div>

                                        <span class="text-sm font-black shrink-0 {{ $trans->type == 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            {{ $trans->type == 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Modal التفاصيل للموبايل --}}
                                <template x-teleport="body">
                                    <div x-show="showTransactionDetails" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4" dir="rtl" @click.stop>
                                        <div x-show="showTransactionDetails" x-transition.opacity class="fixed inset-0 backdrop-blur-sm bg-slate-900/40 dark:bg-black/60" @click="showTransactionDetails = false"></div>
                                        
                                        <div x-show="showTransactionDetails" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                             x-transition:leave="transition ease-in duration-200"
                                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                             x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                                             class="bg-white dark:bg-boxdark w-full max-w-sm rounded-[2rem] p-6 shadow-2xl relative z-10 border border-slate-100 dark:border-boxdark-2 text-right">
                                            
                                            <button @click="showTransactionDetails = false" class="flex absolute top-4 left-4 justify-center items-center w-8 h-8 rounded-full transition-colors bg-slate-50 dark:bg-boxdark-2 text-slate-400 hover:text-rose-500">
                                                <span class="material-symbols-outlined text-[18px]">close</span>
                                            </button>
                                            
                                            <div class="mt-2 mb-6 text-center">
                                                <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-3 {{ $trans->type == 'credit' ? 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400' }}">
                                                    <span class="material-symbols-outlined text-[28px]">
                                                        {{ $trans->type == 'credit' ? 'add_card' : 'credit_score' }}
                                                    </span>
                                                </div>
                                                <h3 class="text-lg font-black font-headline {{ $trans->type == 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                    {{ $trans->type == 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }} <span class="text-sm">ريال</span>
                                                </h3>
                                                <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $trans->created_at->format('Y-m-d - h:i A') }}</p>
                                            </div>
                                            
                                            <div class="p-4 mb-6 rounded-xl bg-slate-50 dark:bg-boxdark-2">
                                                <p class="text-[10px] font-bold text-slate-400 mb-1">البيان (التفاصيل الكاملة):</p>
                                                <p class="text-sm font-bold leading-relaxed whitespace-pre-wrap text-slate-700 dark:text-gray-300">{{ $trans->description }}</p>
                                            </div>
                                            
                                            <div class="grid grid-cols-2 gap-3">
                                                <a href="{{ $trans->waUrl ?? $waUrl }}" target="_blank" class="flex items-center justify-center gap-1.5 py-3 bg-[#25D366] text-white rounded-xl text-xs font-black shadow-sm hover:bg-[#20bd5a] active:scale-95 transition-all">
                                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                                    إرسال واتساب
                                                </a>
                                                <a href="{{ $trans->printUrl ?? route('receipt.generate', ['type' => 'TransactionReceipt', 'id' => $trans->id]) }}" target="_blank" class="flex gap-1.5 justify-center items-center py-3 text-xs font-black text-white rounded-xl shadow-sm transition-all bg-slate-800 dark:bg-primary hover:bg-slate-900 dark:hover:bg-primary-hover active:scale-95">
                                                    <span class="material-symbols-outlined text-[16px]">print</span>
                                                    طباعة السند
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @empty
                            <div class="flex flex-col justify-center items-center py-12 text-center bg-white rounded-[2rem] border-2 border-gray-100 border-dashed dark:bg-boxdark dark:border-boxdark-2">
                                <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">receipt_long</span>
                                <p class="text-sm font-bold text-gray-500 dark:text-bodydark">
                                    لا توجد حركات مالية مسجلة
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if ($transactions->hasPages())
                        <div class="p-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                            {{ $transactions->links('vendor.pagination.tailwind') }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ================= تبويب سجل الطرود ================= --}}
            <div
                x-show="activeTab === 'shipments'"
                style="display: none;"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="flex flex-col gap-6">

                {{-- Filters --}}
                <div class="p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 md:p-6">
                    <div class="flex flex-col gap-4 justify-between items-start mb-5 md:flex-row md:items-center">
                        <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                            <span class="material-symbols-outlined text-primary text-[22px]">local_shipping</span>
                            سجل الشحنات
                        </h3>

                        <div class="relative w-full group md:w-64">
                            <span class="absolute right-3 top-1/2 text-gray-400 transition-colors -translate-y-1/2 material-symbols-outlined dark:text-bodydark group-focus-within:text-primary">
                                search
                            </span>

                            <input
                                type="text"
                                x-model="searchQuery"
                                placeholder="ابحث برقم التتبع..."
                                class="pr-10 pl-4 w-full h-11 text-sm font-bold placeholder-gray-400 rounded-xl border border-gray-100 transition-all outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white dark:placeholder-bodydark">
                        </div>
                    </div>

                    <div class="flex overflow-x-auto gap-2 pb-2 custom-scrollbar">
                        <a href="{{ request()->fullUrlWithQuery(['direction' => 'all', 'ship_page' => null]) }}"
                            class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border
                            {{ $direction == 'all' ? 'bg-primary text-white border-primary shadow-md dark:bg-primary dark:border-primary dark:shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                            الكل
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['direction' => 'sent', 'ship_page' => null]) }}"
                            class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border
                            {{ $direction == 'sent' ? 'bg-primary text-white border-primary shadow-md shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                            <span class="material-symbols-outlined text-[16px] mr-1.5">arrow_upward</span>
                            مرسلة منه
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['direction' => 'received', 'ship_page' => null]) }}"
                            class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border
                            {{ $direction == 'received' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/20 dark:bg-emerald-600 dark:border-emerald-600' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                            <span class="material-symbols-outlined text-[16px] mr-1.5">arrow_downward</span>
                            واردة إليه
                        </a>
                    </div>
                </div>

                {{-- Shipments List --}}
                <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">

                    <div class="hidden p-5 md:block">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] bg-surface dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark">
                                    <th class="px-6 py-4">رقم التتبع</th>
                                    <th class="px-6 py-4">الاتجاه</th>
                                    <th class="px-6 py-4 text-center">المبلغ المالي</th>
                                    <th class="px-6 py-4 text-center">الحالة</th>
                                    <th class="px-6 py-4 text-center">التاريخ</th>
                                    <th class="px-6 py-4 text-center">التفاصيل</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-50 dark:divide-boxdark-2">
                                @forelse($shipments as $shipment)
                                    <tr
                                        x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery)"
                                        x-transition
                                        class="transition-all hover:bg-surface/50 dark:hover:bg-boxdark-2/50 group">

                                        <td class="px-6 py-4">
                                            <div class="flex gap-3 items-center">
                                                <div class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark dark:border-boxdark-2 dark:text-bodydark group-hover:border-primary/30">
                                                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                                </div>

                                                <span class="font-mono text-sm font-black text-on-surface dark:text-white">
                                                    {{ $shipment->bond_number }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            @if($shipment->sender_customer_id == $customer->id)
                                                <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-black bg-primary/10 text-primary border border-primary/20 items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
                                                    مرسل
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 items-center gap-1.5">
                                                    <span class="material-symbols-outlined text-[14px]">arrow_downward</span>
                                                    مستلم
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <div class="flex flex-col items-center">
                                                <span class="text-sm font-black text-on-surface dark:text-white">
                                                    {{ number_format($shipment->total_amount, 0) }} ريال
                                                </span>
                                                <span class="text-[10px] font-bold mt-1 px-1.5 py-0.5 rounded {{ $shipment->payment_method == 'customer_credit' ? 'text-rose-500 bg-rose-50 dark:bg-rose-500/10 dark:text-rose-400' : 'text-gray-500 bg-gray-50 dark:bg-boxdark-2 dark:text-gray-400' }}">
                                                    @if($shipment->payment_method == 'customer_credit')
                                                        آجل
                                                    @elseif($shipment->payment_method == 'prepaid')
                                                        مدفوع مقدماً
                                                    @elseif($shipment->payment_method == 'cod')
                                                        دفع عند الاستلام
                                                    @else
                                                        دفع جزئي
                                                    @endif
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusLabels = [
                                                    'pending' => ['name' => 'قيد التجهيز', 'class' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20'],
                                                    'in_transit' => ['name' => 'في الطريق', 'class' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20'],
                                                    'received_at_branch' => ['name' => 'بالمستودع', 'class' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20'],
                                                    'out_for_delivery' => ['name' => 'خرج للتوصيل', 'class' => 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20'],
                                                    'delivered' => ['name' => 'مكتملة', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20'],
                                                    'returned' => ['name' => 'مرتجعة', 'class' => 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20'],
                                                    'cancelled' => ['name' => 'ملغاة', 'class' => 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400 dark:border-slate-500/20'],
                                                ];
                                                $currentStatus = $statusLabels[$shipment->status] ?? ['name' => 'غير محدد', 'class' => 'bg-gray-50 text-gray-600 border-gray-200'];
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-black border {{ $currentStatus['class'] }}">
                                                {{ $currentStatus['name'] }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <span class="flex gap-1.5 justify-center items-center text-xs font-bold text-gray-500 dark:text-bodydark">
                                                <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                                {{ $shipment->created_at->format('Y-m-d') }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                                                class="inline-flex p-2 text-gray-400 rounded-xl border border-gray-100 shadow-sm transition-all bg-surface hover:text-primary hover:bg-primary-container hover:border-primary/20 dark:bg-boxdark-2 dark:border-boxdark dark:hover:bg-primary/10 dark:hover:border-primary/30"
                                                title="عرض التفاصيل">
                                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-20 text-center">
                                            <div class="flex flex-col justify-center items-center">
                                                <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full bg-surface dark:bg-boxdark-2">
                                                    <span class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">package_2</span>
                                                </div>
                                                <p class="text-sm font-bold text-gray-500 dark:text-bodydark">
                                                    لا توجد شحنات مطابقة للبحث أو الفلتر
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-col gap-4 p-5 md:hidden">
                        @forelse($shipments as $shipment)
                            <div
                                x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery)"
                                x-transition
                                class="overflow-hidden relative rounded-2xl border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">

                                <div class="flex justify-between items-center px-4 py-2.5 bg-white border-b border-gray-100 dark:border-boxdark dark:bg-boxdark">
                                    @if($shipment->sender_customer_id == $customer->id)
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-primary-container dark:bg-primary/10 text-primary border border-primary/20 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">arrow_upward</span> مرسل
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">arrow_downward</span> مستلم
                                        </span>
                                    @endif

                                    <span class="text-[10px] font-bold text-gray-400 dark:text-bodydark">
                                        {{ $shipment->created_at->format('Y-m-d') }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center p-4">
                                    <div>
                                        <h3 class="font-mono text-sm font-black tracking-tight text-on-surface dark:text-white">
                                            {{ $shipment->bond_number }}
                                        </h3>
                                        <p class="text-[10px] font-bold text-gray-500 dark:text-bodydark mt-1 flex items-center gap-1.5">
                                            @if($shipment->payment_method == 'customer_credit')
                                                <span class="px-1.5 py-0.5 text-rose-500 bg-rose-50 rounded dark:bg-rose-500/10 dark:text-rose-400">آجل</span>
                                            @elseif($shipment->payment_method == 'prepaid')
                                                مدفوع مقدماً
                                            @elseif($shipment->payment_method == 'cod')
                                                دفع عند الاستلام
                                            @else
                                                دفع جزئي
                                            @endif
                                            <span class="text-gray-300 dark:text-gray-600">•</span>
                                            <span class="font-black text-on-surface dark:text-gray-200">
                                                {{ number_format($shipment->total_amount, 0) }} ريال
                                            </span>
                                        </p>
                                    </div>
                                    <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                                        class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark hover:bg-primary hover:text-white dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col justify-center items-center py-12 text-center bg-white rounded-[2rem] border-2 border-gray-100 border-dashed dark:bg-boxdark dark:border-boxdark-2">
                                <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">package_2</span>
                                <p class="text-sm font-bold text-gray-500 dark:text-bodydark">
                                    لا توجد شحنات مطابقة
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if($shipments->hasPages())
                        <div class="p-5 border-t border-gray-50 md:p-6 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                            {{ $shipments->links('vendor.pagination.tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection