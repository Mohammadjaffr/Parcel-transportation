@extends('layouts.app')

@section('title', 'ملف العميل | ' . $customer->name)

@section('content')
    @php
        // الحسابات المالية الدقيقة من دفتر الأستاذ
        $credit  = $customer->sum_credit ?? 0; // متحصلاته
        $debit   = $customer->sum_debit ?? 0;  // ديونه (أجور الشحن)
        $balance = $credit - $debit;           // الصافي
    @endphp

<div x-data="{ 
        activeTab: new URLSearchParams(window.location.search).has('ship_page') || new URLSearchParams(window.location.search).has('direction') ? 'shipments' : 'financials', 
        showPaymentModal: false, 
        amountToPay: {{ abs($balance) }} 
     }" 
     class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl">

    {{-- ================= النافذة المنبثقة (Modal) للسداد ================= --}}
    <div x-show="showPaymentModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4">
        {{-- خلفية ضبابية --}}
        <div x-show="showPaymentModal" x-transition.opacity class="fixed inset-0 backdrop-blur-sm bg-slate-900/40 dark:bg-black/60" @click="showPaymentModal = false"></div>

        {{-- صندوق السداد --}}
        <div x-show="showPaymentModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-90 translate-y-4"
             class="bg-white dark:bg-boxdark w-full max-w-sm rounded-[2rem] p-6 shadow-2xl relative z-10 border border-slate-100 dark:border-boxdark-2">
            
            <div class="flex justify-between items-center mb-5">
                <h3 class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white font-headline">
                    <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                    تسديد مديونية
                </h3>
                <button @click="showPaymentModal = false" class="transition-colors text-slate-400 hover:text-rose-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <form action="{{ route('customers.addPayment', $customer->id) }}" method="POST">
                @csrf
                
                {{-- حقل المبلغ --}}
                <div class="mb-4">
                    <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">المبلغ المراد سداده (ريال)</label>
                    <div class="relative">
                        <input type="number" name="amount" x-model="amountToPay" step="0.01" min="1" max="{{ abs($balance) }}" required
                            class="px-4 py-3 pl-12 w-full text-lg font-black rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20">
                        <span class="absolute left-4 top-1/2 text-sm font-bold -translate-y-1/2 text-slate-400">ريال</span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1.5">يمكنك تسديد المبلغ كاملاً أو إدخال دفعة جزئية.</p>
                </div>

                {{-- حقل الملاحظات (اختياري) --}}
                <div class="mb-6">
                    <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">ملاحظات (اختياري)</label>
                    <input type="text" name="notes" placeholder="مثال: تحويل بنكي، كاش للمندوب..."
                        class="px-4 py-3 w-full text-sm font-bold rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20">
                </div>

                <button type="submit" class="w-full py-3.5 bg-slate-800 dark:bg-primary text-white text-sm font-black rounded-xl shadow-[0_4px_12px_rgba(30,41,59,0.3)] dark:shadow-[0_4px_12px_rgba(var(--color-primary),0.3)] hover:bg-slate-900 dark:hover:bg-primary-hover transition-all active:scale-95 flex justify-center items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                    تأكيد السداد
                </button>
            </form>
        </div>
    </div>
    <div class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
        <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
            <div class="flex gap-4 items-center">
                <a href="{{ route('customers.index') }}"
                    class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
                <div>
                    <h1 class="text-xl font-black md:text-2xl font-headline text-on-surface dark:text-white">ملف العميل</h1>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-bodydark">تفاصيل الحساب وسجل الشحنات</p>
                </div>
            </div>
          
            {{-- زر مراسلة العميل واتساب (يظهر كأيقونة في الموبايل ونص في الديسكتوب) --}}
            <a href="{{ route('whatsapp.customer.account.statement', $customer->id) }}"
                target="_blank"
                class="flex gap-2 justify-center items-center px-3 h-10 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform md:px-4 dark:bg-emerald-500/10 dark:text-emerald-400 active:scale-95 dark:border-emerald-500/20 hover:shadow-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20">
                <span class="material-symbols-outlined text-[18px]">send</span>
                <span class="hidden md:inline">إرسال كشف الحساب</span>
            </a>
            <a href="{{ route('receipt.generate', ['type' => 'CustomerAccountStatementReceipt', 'id' => $customer->uuid ?? $customer->id]) }}" target="_blank"
                class="flex gap-2 justify-center items-center px-3 h-10 text-xs font-bold rounded-xl border transition-transform text-primary bg-primary-container border-primary/10 md:px-4 dark:bg-primary/10 dark:text-primary active:scale-95 hover:shadow-md hover:bg-primary/10 dark:hover:bg-primary/20">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                <span class="hidden md:inline">كشف حساب</span>
            </a>
        </div>  
    </div>

    {{-- ================= محتوى الصفحة (Grid Layout) ================= --}}
    <div x-data="{ searchQuery: '' }" class="grid grid-cols-1 gap-6 items-start p-4 mx-auto max-w-7xl md:p-6 xl:grid-cols-12">
        
        {{-- ================= الجانب الأيمن: بيانات العميل (Sidebar) ================= --}}
        <div class="xl:col-span-4 flex flex-col gap-6 xl:sticky xl:top-[5.5rem]">
            
            {{-- بطاقة الهوية --}}
            <div class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm relative overflow-hidden flex flex-col items-center text-center gap-4">
                <div class="w-24 h-24 rounded-[1.5rem] bg-primary-container dark:bg-primary/10 text-primary flex items-center justify-center text-4xl font-black shadow-inner border border-primary/20 dark:border-primary/10 shrink-0">
                    @php
                        $words = explode(' ', $customer->name);
                        echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') . (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                    @endphp
                </div>
                <div>
                    <h2 class="text-xl font-black text-on-surface dark:text-white font-headline">{{ $customer->name }}</h2>
                    <div class="flex gap-2 justify-center items-center mt-2 text-gray-500 dark:text-bodydark">
                        <span class="material-symbols-outlined text-[16px]">phone_iphone</span>
                        <p class="font-mono text-sm font-bold tracking-wider dir-ltr">{{ $customer->phone }}</p>
                    </div>
                </div>
            </div>

            {{-- الداشبورد المالي للعميل --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm p-6">
                <h3 class="flex gap-2 items-center mb-5 text-base font-black text-on-surface dark:text-white">
                    <span class="material-symbols-outlined text-primary bg-primary-container dark:bg-primary/10 p-1.5 rounded-lg text-[18px]">account_balance_wallet</span>
                    الملخص المالي
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    {{-- الرصيد الصافي --}}
                    <div class="col-span-2 bg-surface dark:bg-boxdark-2 p-5 rounded-[1.5rem] border border-gray-100 dark:border-boxdark shadow-sm flex items-center justify-between relative overflow-hidden transition-all hover:shadow-md hover:border-gray-200 dark:hover:border-gray-700">
                        <div class="absolute right-0 top-0 bottom-0 w-1.5 {{ $balance >= 0 ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>
                        
                        <div class="pr-2">
                            <p class="mb-1 text-xs font-bold text-gray-500 dark:text-bodydark">الرصيد الصافي للعميل</p>
                            <p class="text-3xl font-black font-headline {{ $balance >= 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400' }}">
                                {{ number_format(abs($balance), 0) }} <span class="text-xs {{ $balance >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">ريال</span>
                            </p>
                        </div>
                        
                        <div class="flex flex-col gap-2 items-end">
                            <div class="px-3 py-1.5 rounded-lg text-[10px] font-black {{ $balance >= 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' }}">
                                {{ $balance >= 0 ? 'رصيد لصالحه (له)' : 'مطلوب سداده (عليه)' }}
                            </div>
                            
                            {{-- 💡 زر السداد (يظهر فقط إذا كان عليه ديون $balance < 0) --}}
                            @if ($balance < 0)
                                <button @click="showPaymentModal = true" type="button" class="flex items-center gap-1 px-3 py-1.5 bg-slate-800 dark:bg-white dark:text-slate-800 text-white rounded-lg text-[10px] font-bold shadow-sm hover:bg-slate-700 dark:hover:bg-gray-100 active:scale-95 transition-all">
                                    <span class="material-symbols-outlined text-[14px]">payments</span>
                                    تسديد مبلغ
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- إجمالي متحصلاته (له) --}}
                    <div class="bg-surface dark:bg-boxdark-2 p-4 rounded-[1.5rem] border border-gray-100 dark:border-boxdark shadow-sm flex flex-col justify-center">
                        <div class="flex gap-1.5 items-center mb-1.5 text-emerald-500 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
                            <p class="text-[10px] font-bold text-gray-500 dark:text-bodydark">متحصلات (له)</p>
                        </div>
                        <p class="text-xl font-black text-gray-700 dark:text-gray-200 font-headline">{{ number_format($credit, 0) }}</p>
                    </div>

                    {{-- إجمالي ديونه (عليه) --}}
                    <div class="bg-surface dark:bg-boxdark-2 p-4 rounded-[1.5rem] border border-gray-100 dark:border-boxdark shadow-sm flex flex-col justify-center">
                        <div class="flex gap-1.5 items-center mb-1.5 text-rose-500 dark:text-rose-400">
                            <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                            <p class="text-[10px] font-bold text-gray-500 dark:text-bodydark">أجور شحن (عليه)</p>
                        </div>
                        <p class="text-xl font-black text-gray-700 dark:text-gray-200 font-headline">{{ number_format($debit, 0) }}</p>
                    </div>
                </div>
                
                @if(isset($unpaidShipmentsCount) && $unpaidShipmentsCount > 0)
                    <div class="flex gap-2.5 items-start px-4 py-3 mt-4 text-xs font-bold leading-relaxed text-rose-600 rounded-xl border border-rose-100 bg-rose-50/50 dark:bg-rose-500/5 dark:border-rose-500/20 dark:text-rose-400">
                        <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">info</span>
                        <div>يوجد لدى العميل <span class="px-1 font-black">{{ $unpaidShipmentsCount }}</span> شحنات غير مسددة أو مسددة جزئياً، يرجى المتابعة.</div>
                    </div>
                @endif
            </div>

          
        </div>

        {{-- ================= الجانب الأيسر: سجل الشحنات ================= --}}
        <div class="flex flex-col gap-6 xl:col-span-8">
            
            {{-- ================= التبويبات الذكية (Alpine.js) ================= --}}
            <div class="flex p-1 bg-gray-100 dark:bg-boxdark rounded-[1rem]">
                <button @click="activeTab = 'financials'" 
                        :class="activeTab === 'financials' ? 'bg-white dark:bg-boxdark-2 text-primary shadow-sm' : 'text-gray-500 dark:text-bodydark hover:text-gray-700 dark:hover:text-white'" 
                        class="flex-1 flex justify-center items-center gap-1.5 py-2.5 text-xs font-bold rounded-[0.75rem] transition-all">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    كشف الحساب
                </button>
                <button @click="activeTab = 'shipments'" 
                        :class="activeTab === 'shipments' ? 'bg-white dark:bg-boxdark-2 text-primary shadow-sm' : 'text-gray-500 dark:text-bodydark hover:text-gray-700 dark:hover:text-white'" 
                        class="flex-1 flex justify-center items-center gap-1.5 py-2.5 text-xs font-bold rounded-[0.75rem] transition-all">
                    <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                    سجل الطرود
                </button>
            </div>

            {{-- ================= التبويب الأول: كشف الحساب (المالية) ================= --}}
            <div x-show="activeTab === 'financials'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                
                <h3 class="flex gap-2 items-center mb-2 text-lg font-black text-on-surface dark:text-white font-headline">
                    <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                    الحركات المالية الأخيرة
                </h3>

                <div class="space-y-3">
                    @forelse($transactions as $trans)
                        <div class="bg-white dark:bg-boxdark p-4 rounded-[1.5rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex items-center gap-3 hover:border-gray-200 dark:hover:border-gray-700 transition-all">
                            {{-- أيقونة الحركة --}}
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 {{ $trans->type == 'credit' ? 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400' }}">
                                <span class="material-symbols-outlined text-[24px]">
                                    {{ $trans->type == 'credit' ? 'add_card' : 'credit_score' }}
                                </span>
                            </div>
                            
                            {{-- تفاصيل الحركة --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-700 truncate dark:text-gray-200">{{ $trans->description }}</p>
                                <p class="text-[10px] font-bold text-gray-400 dark:text-bodydark mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">schedule</span>
                                    {{ $trans->created_at->format('Y-m-d (h:i A)') }}
                                </p>
                            </div>

                            {{-- المبلغ --}}
                            <div class="text-right shrink-0">
                                <p class="text-lg font-black font-headline {{ $trans->type == 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $trans->type == 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 flex flex-col items-center justify-center bg-white dark:bg-boxdark rounded-[2rem] border-2 border-dashed border-gray-100 dark:border-boxdark-2 text-center">
                            <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">receipt_long</span>
                            <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا توجد حركات مالية مسجلة</p>
                        </div>
                    @endforelse
                </div>

                {{-- ترقيم الحركات المالية --}}
                @if ($transactions->hasPages())
                    <div class="mt-4 p-4 border-t border-gray-50 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                        {{ $transactions->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>

            {{-- ================= التبويب الثاني: سجل الطرود ================= --}}
            <div x-show="activeTab === 'shipments'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="flex flex-col gap-6">

            {{-- لوحة الفلترة والبحث --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm p-5 md:p-6">
                <div class="flex flex-col gap-4 justify-between items-start mb-5 md:flex-row md:items-center">
                    <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                        <span class="material-symbols-outlined text-primary text-[22px]">local_shipping</span>
                        سجل الشحنات
                    </h3>

                    {{-- شريط البحث --}}
                    <div class="relative w-full group md:w-64">
                        <span class="absolute right-3 top-1/2 text-gray-400 transition-colors -translate-y-1/2 material-symbols-outlined dark:text-bodydark group-focus-within:text-primary">search</span>
                        <input type="text" x-model="searchQuery" placeholder="ابحث برقم التتبع..."
                            class="pr-10 pl-4 w-full h-11 text-sm font-bold placeholder-gray-400 rounded-xl border border-gray-100 transition-all outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white dark:placeholder-bodydark">
                    </div>
                </div>

                {{-- شريط الفلترة الأفقي --}}
                <div class="flex overflow-x-auto gap-2 pb-2 custom-scrollbar">
                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'all', 'page' => null]) }}"
                        class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                        {{ $direction == 'all' ? 'bg-primary text-white border-primary shadow-md dark:bg-primary dark:border-primary dark:shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                        الكل
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'sent', 'page' => null]) }}"
                        class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                        {{ $direction == 'sent' ? 'bg-primary text-white border-primary shadow-md shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                        <span class="material-symbols-outlined text-[16px] mr-1.5">arrow_upward</span> مرسلة منه
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'received', 'page' => null]) }}"
                        class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                        {{ $direction == 'received' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/20 dark:bg-emerald-600 dark:border-emerald-600' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                        <span class="material-symbols-outlined text-[16px] mr-1.5">arrow_downward</span> واردة إليه
                    </a>
                </div>
            </div>

            {{-- ================= قائمة الشحنات (Desktop Table / Mobile Cards) ================= --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden">
                
                {{-- Desktop View (Table) --}}
                <div class="hidden overflow-x-auto p-5 md:block">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] bg-surface dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark">
                                <th class="px-6 py-4">رقم التتبع</th>
                                <th class="px-6 py-4">الاتجاه</th>
                                <th class="px-6 py-4 text-center">المبلغ المالي</th>
                                <th class="px-6 py-4 text-center">التاريخ</th>
                                <th class="px-6 py-4 text-center">التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-boxdark-2">
                            @forelse($shipments as $shipment)
                                <tr x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery)" x-transition
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
                                            <span class="text-sm font-black text-on-surface dark:text-white">{{ number_format($shipment->total_amount, 0) }} ريال</span>
                                            <span class="text-[10px] font-bold mt-1 px-1.5 py-0.5 rounded {{ $shipment->payment_method == 'customer_credit' ? 'text-rose-500 bg-rose-50 dark:bg-rose-500/10 dark:text-rose-400' : 'text-gray-500 bg-gray-50 dark:bg-boxdark-2 dark:text-gray-400' }}">
                                                @if($shipment->payment_method == 'customer_credit') آجل
                                                @elseif($shipment->payment_method == 'prepaid') مدفوع مقدماً
                                                @elseif($shipment->payment_method == 'cod') دفع عند الاستلام
                                                @else دفع جزئي @endif
                                            </span>
                                        </div>
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
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full bg-surface dark:bg-boxdark-2">
                                                <span class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">package_2</span>
                                            </div>
                                            <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا توجد شحنات مطابقة للفلتر أو البحث</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View (Cards) --}}
                <div class="flex flex-col gap-4 p-5 md:hidden">
                    @forelse($shipments as $shipment)
                        <div x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery)" x-transition
                            class="overflow-hidden relative rounded-2xl border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                            
                            {{-- تحديد بصري: مرسل أم مستقبل --}}
                            <div class="flex justify-between items-center px-4 py-2.5 bg-white border-b border-gray-100 dark:border-boxdark dark:bg-boxdark">
                                @if($shipment->sender_customer_id == $customer->id)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-primary-container dark:bg-primary/10 text-primary border border-primary/20 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">arrow_upward</span>
                                        مرسل
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">arrow_downward</span>
                                        مستلم
                                    </span>
                                @endif
                                <span class="text-[10px] font-bold text-gray-400 dark:text-bodydark">{{ $shipment->created_at->format('Y-m-d') }}</span>
                            </div>

                            <div class="flex justify-between items-center p-4">
                                <div>
                                    <h3 class="font-mono text-sm font-black tracking-tight text-on-surface dark:text-white">{{ $shipment->bond_number }}</h3>
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
                                        <span class="font-black text-on-surface dark:text-gray-200">{{ number_format($shipment->total_amount, 0) }} ريال</span>
                                    </p>
                                </div>

                                <a href="{{ route('shipment.outgoing.show', $shipment->id) }}" class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark hover:bg-primary hover:text-white dark:border-boxdark">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 flex flex-col items-center justify-center bg-white dark:bg-boxdark rounded-[2rem] border-2 border-dashed border-gray-100 dark:border-boxdark-2 text-center">
                            <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">package_2</span>
                            <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا توجد شحنات مطابقة للفلتر</p>
                        </div>
                    @endforelse
                </div>

                {{-- الترقيم --}}
                @if($shipments->hasPages())
                    <div class="p-5 md:p-6 border-t border-gray-50 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                        {{ $shipments->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>

            </div> {{-- نهاية تبويب الشحنات --}}

        </div>
    </div>
</div>
@endsection