@extends('mobile.layouts.app')

@section('title', 'ملف العميل | ' . $customer->name)

@section('content')
    @php
        // الحسابات المالية الدقيقة من دفتر الأستاذ
        $credit = $customer->sum_credit ?? 0; // متحصلاته
        $debit = $customer->sum_debit ?? 0;  // ديونه (أجور الشحن)
        $balance = $credit - $debit;           // الصافي
    @endphp

    {{-- 💡 Alpine.js: تحديد التبويب النشط بناءً على الرابط (للحفاظ على التبويب عند الانتقال بين الصفحات) --}}
    <div x-data="{ activeTab: new URLSearchParams(window.location.search).has('ship_page') || new URLSearchParams(window.location.search).has('direction') ? 'shipments' : 'financials' }" 
         class="flex flex-col gap-5 px-4 pt-4 pb-24 min-h-screen bg-slate-50/50">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex justify-between items-center">
            <div class="flex gap-3 items-center">
                <a href="{{ route('customers.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <h1 class="text-xl font-black font-headline text-slate-800">ملف العميل</h1>
            </div>

            <div class="flex gap-2 items-center">
                {{-- زر مراسلة العميل واتساب --}}
                <a href="https://wa.me/{{ str_starts_with(ltrim($customer->phone, '+'), '7') ? '967' . ltrim($customer->phone, '+') : ltrim($customer->phone, '+') }}" target="_blank"
                    class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform active:scale-95">
                    <svg class="w-5 h-5 fill-emerald-500" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                </a>
                {{-- زر طباعة كشف الحساب --}}
                <a href="{{ route('receipt.generate', ['type' => 'CustomerAccountStatementReceipt', 'id' => $customer->uuid]) }}"
                    target="_blank"
                    class="flex gap-1.5 items-center px-3 h-10 text-xs font-bold rounded-xl border transition-transform border-primary/20 bg-primary/5 text-primary active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    التقرير
                </a>
            </div>
        </div>

        {{-- ================= بطاقة الهوية ================= --}}
        <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.02)] flex items-center gap-4 relative overflow-hidden">
            <div class="flex justify-center items-center w-16 h-16 text-2xl font-black rounded-2xl border shadow-inner bg-gradient-to-br from-primary/10 to-primary/5 text-primary border-primary/10 shrink-0">
                @php
                    $words = explode(' ', $customer->name);
                    echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') . (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                @endphp
            </div>
            <div>
                <h2 class="text-lg font-black text-slate-800 font-headline">{{ $customer->name }}</h2>
                <div class="flex gap-2 items-center mt-1 text-slate-500">
                    <span class="material-symbols-outlined text-[14px] text-primary/60">phone_iphone</span>
                    <p class="font-mono text-xs font-bold tracking-wider">{{ $customer->phone }}</p>
                </div>
            </div>
        </div>

        {{-- ================= الداشبورد المالي ================= --}}
        {{-- 💡 أضفنا x-data هنا للتحكم في النافذة المنبثقة للسداد --}}
       <div x-data="{ showPaymentModal: false, amountToPay: {{ abs($balance) }} }">
            <div class="grid grid-cols-2 gap-3">
                {{-- الرصيد الصافي --}}
                <div class="col-span-2 bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center justify-between relative overflow-hidden">
                    <div class="absolute right-0 top-0 bottom-0 w-1.5 {{ $balance >= 0 ? 'bg-emerald-400' : 'bg-rose-400' }}"></div>

                    <div class="pr-2">
                        <p class="text-[10px] font-bold text-slate-400 mb-1">الرصيد الصافي للعميل</p>
                        <p class="text-2xl font-black font-headline {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ number_format(abs($balance), 0) }} <span class="text-xs {{ $balance >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">ريال</span>
                        </p>
                    </div>

                    <div class="flex flex-col items-end gap-2">
                        <div class="px-3 py-1.5 rounded-lg text-[10px] font-black {{ $balance >= 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100' }}">
                            {{ $balance >= 0 ? 'رصيد لصالحه (له)' : 'مطلوب سداده (عليه)' }}
                        </div>

                        {{-- 💡 زر السداد/الصرف (يظهر إذا كان الرصيد لا يساوي صفر) --}}
                        @if ($balance != 0)
                            <button @click="showPaymentModal = true" type="button" 
                                class="flex items-center gap-1 px-3 py-1.5 text-white rounded-lg text-[10px] font-bold shadow-sm active:scale-95 transition-all 
                                {{ $balance < 0 ? 'bg-slate-800 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                <span class="material-symbols-outlined text-[14px]">
                                    {{ $balance < 0 ? 'payments' : 'request_quote' }}
                                </span>
                                {{ $balance < 0 ? 'تسديد مبلغ' : 'صرف رصيد' }}
                            </button>
                        @endif
                    </div>
                </div>

                {{-- إجمالي متحصلاته (له) --}}
                <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                    <div class="flex items-center gap-1.5 mb-1.5 text-emerald-500">
                        <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
                        <p class="text-[10px] font-bold text-slate-500">متحصلات (له)</p>
                    </div>
                    <p class="text-lg font-black text-slate-700 font-headline">{{ number_format($credit, 0) }}</p>
                </div>

                {{-- إجمالي ديونه (عليه) --}}
                <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                    <div class="flex items-center gap-1.5 mb-1.5 text-rose-500">
                        <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                        <p class="text-[10px] font-bold text-slate-500">أجور شحن (عليه)</p>
                    </div>
                    <p class="text-lg font-black text-slate-700 font-headline">{{ number_format($debit, 0) }}</p>
                </div>
            </div>

            {{-- ================= النافذة المنبثقة (Modal) للسداد أو الصرف ================= --}}
            <div x-show="showPaymentModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4">
                {{-- خلفية ضبابية --}}
                <div x-show="showPaymentModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showPaymentModal = false"></div>

                {{-- صندوق العملية --}}
                <div x-show="showPaymentModal" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                     class="bg-white w-full max-w-sm rounded-[2rem] p-6 shadow-2xl relative z-10 border border-slate-100">

                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-black text-slate-800 font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined {{ $balance < 0 ? 'text-primary' : 'text-emerald-500' }}">
                                account_balance_wallet
                            </span>
                            {{ $balance < 0 ? 'تسديد مديونية' : 'صرف رصيد للعميل' }}
                        </h3>
                        <button @click="showPaymentModal = false" class="text-slate-400 hover:text-rose-500 transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('customers.addPayment', $customer->id) }}" method="POST">
                        @csrf

                        {{-- حقل مخفي يخبر الكنترولر بنوع العملية (سداد أم صرف) --}}
                        <input type="hidden" name="transaction_action" value="{{ $balance < 0 ? 'pay_debt' : 'withdraw_balance' }}">

                        {{-- حقل المبلغ --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-500 mb-2">
                                {{ $balance < 0 ? 'المبلغ المراد سداده (ريال)' : 'المبلغ المراد صرفه للعميل (ريال)' }}
                            </label>
                            <div class="relative">
                                <input type="number" name="amount" x-model="amountToPay" step="0.01" min="1" max="{{ abs($balance) }}" required
                                    class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-lg font-black rounded-xl px-4 py-3 pl-12 focus:ring-2 {{ $balance < 0 ? 'focus:ring-primary/20' : 'focus:ring-emerald-500/20' }} outline-none transition-all">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">ريال</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1.5">
                                {{ $balance < 0 ? 'يمكنك تسديد المبلغ كاملاً أو إدخال دفعة جزئية.' : 'يمكنك صرف الرصيد كاملاً أو سحب جزء منه.' }}
                            </p>
                        </div>

                        {{-- حقل الملاحظات (اختياري) --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-500 mb-2">ملاحظات (اختياري)</label>
                            <input type="text" name="notes" placeholder="مثال: تحويل بنكي، كاش للمندوب..."
                                class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm font-bold rounded-xl px-4 py-3 focus:ring-2 {{ $balance < 0 ? 'focus:ring-primary/20' : 'focus:ring-emerald-500/20' }} outline-none transition-all">
                        </div>

                        <button type="submit" class="w-full py-3.5 text-white text-sm font-black rounded-xl shadow-[0_4px_12px_rgba(30,41,59,0.3)] transition-all active:scale-95 flex justify-center items-center gap-2 {{ $balance < 0 ? 'bg-slate-800 hover:bg-slate-900' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            <span class="material-symbols-outlined text-[18px]">done_all</span>
                            {{ $balance < 0 ? 'تأكيد السداد' : 'تأكيد الصرف' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= التبويبات الذكية (Alpine.js) ================= --}}
        <div class="flex p-1 bg-slate-200/60 rounded-[1rem]">
            <button @click="activeTab = 'financials'" 
                    :class="activeTab === 'financials' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700'" 
                    class="flex-1 flex justify-center items-center gap-1.5 py-2.5 text-xs font-bold rounded-[0.75rem] transition-all">
                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                كشف الحساب
            </button>
            <button @click="activeTab = 'shipments'" 
                    :class="activeTab === 'shipments' ? 'bg-white text-primary shadow-sm' : 'text-slate-500 hover:text-slate-700'" 
                    class="flex-1 flex justify-center items-center gap-1.5 py-2.5 text-xs font-bold rounded-[0.75rem] transition-all">
                <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                سجل الطرود
            </button>
        </div>

       {{-- ================= التبويب الأول: كشف الحساب (المالية) ================= --}}
        <div x-show="activeTab === 'financials'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">

            <h3 class="text-sm font-black text-slate-800 mb-2">الحركات المالية الأخيرة</h3>

            <div class="space-y-3">
                @forelse($transactions as $trans)
                    {{-- تهيئة رسالة الواتساب --}}
                    @php
                        $waMessage = "مرحباً، نرفق لكم تفاصيل الحركة المالية:\n\n"
                            . "نوع الحركة: " . ($trans->type == 'credit' ? 'إيداع/لصالحك' : 'خصم/عليك') . "\n"
                            . "المبلغ: " . number_format($trans->amount, 2) . " ريال\n"
                            . "البيان: " . $trans->description . "\n"
                            . "التاريخ: " . $trans->created_at->format('Y-m-d h:i A');
                        $waUrl = "https://wa.me/?text=" . urlencode($waMessage);
                    @endphp

                    {{-- تغليف الحركة بـ x-data للتحكم بالنافذة المنبثقة --}}
                    <div x-data="{ showTransactionDetails: false }">

                        {{-- البطاقة المصغرة (قابلة للضغط) --}}
                        <div @click="showTransactionDetails = true" class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center gap-3 cursor-pointer active:scale-95 transition-transform">
                            {{-- أيقونة الحركة --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $trans->type == 'credit' ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }}">
                                <span class="material-symbols-outlined text-[20px]">
                                    {{ $trans->type == 'credit' ? 'add_card' : 'credit_score' }}
                                </span>
                            </div>

                            {{-- تفاصيل الحركة (مختصرة) --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-700 truncate">{{ $trans->description }}</p>
                                <p class="text-[9px] font-bold text-slate-400 mt-0.5">{{ $trans->created_at->format('Y-m-d (h:i A)') }}</p>
                            </div>

                            {{-- المبلغ --}}
                            <div class="text-right shrink-0">
                                <p class="text-sm font-black font-mono {{ $trans->type == 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $trans->type == 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }}
                                </p>
                            </div>
                        </div>

                        {{-- ================= النافذة المنبثقة (Modal) للتفاصيل ================= --}}
                        <div x-show="showTransactionDetails" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4">
                            {{-- خلفية ضبابية --}}
                            <div x-show="showTransactionDetails" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showTransactionDetails = false"></div>

                            {{-- صندوق التفاصيل --}}
                            <div x-show="showTransactionDetails" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                                 class="bg-white w-full max-w-sm rounded-[2rem] p-6 shadow-2xl relative z-10 border border-slate-100">

                                {{-- زر الإغلاق --}}
                                <button @click="showTransactionDetails = false" class="absolute top-4 left-4 w-8 h-8 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-rose-500 rounded-full transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>

                                {{-- رأس النافذة --}}
                                <div class="text-center mb-6 mt-2">
                                    <div class="w-14 h-14 mx-auto rounded-full flex items-center justify-center mb-3 {{ $trans->type == 'credit' ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }}">
                                        <span class="material-symbols-outlined text-[28px]">
                                            {{ $trans->type == 'credit' ? 'add_card' : 'credit_score' }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-black font-headline {{ $trans->type == 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $trans->type == 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }} <span class="text-sm">ريال</span>
                                    </h3>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1">{{ $trans->created_at->format('Y-m-d - h:i A') }}</p>
                                </div>

                                {{-- البيان الكامل --}}
                                <div class="bg-slate-50 rounded-xl p-4 mb-6">
                                    <p class="text-[10px] font-bold text-slate-400 mb-1">البيان (التفاصيل الكاملة):</p>
                                    <p class="text-sm font-bold text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $trans->description }}</p>
                                </div>

                                {{-- أزرار الإجراءات --}}
                                <div class="grid grid-cols-2 gap-3">
                                    {{-- زر الواتساب --}}
                                    <a href="{{ $trans->waUrl }}" target="_blank" class="flex items-center justify-center gap-1.5 py-3 bg-[#25D366] text-white rounded-xl text-xs font-black shadow-sm hover:bg-[#20bd5a] active:scale-95 transition-all">
                                        <i class="fa-brands fa-whatsapp text-[16px]"></i>
                                     إرسال واتساب
                                    </a>

                                    {{-- زر الطباعة --}}
                                    <a href="{{ $trans->printUrl }}" target="_blank" class="flex items-center justify-center gap-1.5 py-3 bg-slate-800 text-white rounded-xl text-xs font-black shadow-sm hover:bg-slate-900 active:scale-95 transition-all">
                                        <span class="material-symbols-outlined text-[16px]">print</span>
                                     طباعة السند
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 flex flex-col items-center justify-center bg-white rounded-[2rem] border-2 border-dashed border-slate-100 text-center">
                        <span class="mb-2 text-4xl material-symbols-outlined text-slate-300">receipt_long</span>
                        <p class="text-sm font-bold text-slate-500">لا توجد حركات مالية مسجلة</p>
                    </div>
                @endforelse
            </div>

            {{-- ترقيم الحركات المالية --}}
            @if ($transactions->hasPages())
                <div class="mt-4">
                    {{ $transactions->links('vendor.pagination.mobile') }}
                </div>
            @endif
        </div>

        {{-- ================= التبويب الثاني: سجل الطرود ================= --}}
        <div x-show="activeTab === 'shipments'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

            {{-- شريط الفلترة الأفقي --}}
            <div class="flex overflow-x-auto gap-2 pb-2 mb-3 custom-scrollbar snap-x snap-mandatory">
                <a href="{{ request()->fullUrlWithQuery(['direction' => 'all', 'ship_page' => null]) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $direction == 'all' ? 'bg-slate-800 text-white border-slate-800 shadow-[0_4px_12px_rgba(30,41,59,0.2)]' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    جميع الشحنات
                </a>
                <a href="{{ request()->fullUrlWithQuery(['direction' => 'sent', 'ship_page' => null]) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $direction == 'sent' ? 'bg-primary text-white border-primary shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[14px] mr-1">arrow_upward</span> صادرة منه
                </a>
                <a href="{{ request()->fullUrlWithQuery(['direction' => 'received', 'ship_page' => null]) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $direction == 'received' ? 'bg-emerald-500 text-white border-emerald-500 shadow-sm' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[14px] mr-1">arrow_downward</span> واردة إليه
                </a>
            </div>

            {{-- قائمة الطرود --}}
            <div class="space-y-4">
                @forelse($shipments as $shipment)
                    <div class="bg-white rounded-[1.5rem] border border-slate-200/60 shadow-sm overflow-hidden relative">
                        <div class="flex justify-between items-center px-4 py-2 border-b border-slate-100 bg-slate-50/50">

                            <div class="flex items-center gap-2">
                                {{-- شارة مرسل / مستلم --}}
                                @if ($shipment->sender_customer_id == $customer->id)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-primary/10 text-primary border border-primary/20 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">arrow_upward</span>
                                        مرسل
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">arrow_downward</span>
                                        مستلم
                                    </span>
                                @endif

                                {{-- شارة حالة الطرد (ملونة ديناميكياً) --}}
                                @php
                                    $statusLabels = [
                                        'pending' => ['name' => 'قيد التجهيز', 'class' => 'bg-amber-50 text-amber-600 border-amber-200'],
                                        'in_transit' => ['name' => 'في الطريق', 'class' => 'bg-blue-50 text-blue-600 border-blue-200'],
                                        'received_at_branch' => ['name' => 'بالمستودع', 'class' => 'bg-purple-50 text-purple-600 border-purple-200'],
                                        'out_for_delivery' => ['name' => 'خرج للتوصيل', 'class' => 'bg-indigo-50 text-indigo-600 border-indigo-200'],
                                        'delivered' => ['name' => 'مكتملة', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-200'],
                                        'returned' => ['name' => 'مرتجعة', 'class' => 'bg-rose-50 text-rose-600 border-rose-200'],
                                        'cancelled' => ['name' => 'ملغاة', 'class' => 'bg-slate-50 text-slate-600 border-slate-200'],
                                    ];
                                    $currentStatus = $statusLabels[$shipment->status] ?? ['name' => 'غير محدد', 'class' => 'bg-gray-50 text-gray-600 border-gray-200'];
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black border {{ $currentStatus['class'] }}">
                                    {{ $currentStatus['name'] }}
                                </span>
                            </div>

                            <span class="text-[10px] font-bold text-slate-400">{{ $shipment->created_at->format('Y-m-d') }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4">
                            <div>
                                <h3 class="font-mono text-sm font-black tracking-tight text-slate-800">{{ $shipment->bond_number }}</h3>
                                <p class="text-[10px] font-bold text-slate-500 mt-1">
                                    @if ($shipment->payment_method == 'customer_credit')
                                        <span class="px-1.5 py-0.5 text-rose-500 bg-rose-50 rounded border border-rose-100">آجل</span>
                                    @elseif($shipment->payment_method == 'prepaid')
                                        مدفوع مقدماً
                                    @elseif($shipment->payment_method == 'cod')
                                        دفع عند الاستلام
                                    @else
                                        دفع جزئي
                                    @endif
                                    •
                                    <span class="font-black text-slate-800">{{ number_format($shipment->total_amount, 0) }} ريال</span>
                                </p>
                            </div>

                            <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                                class="flex justify-center items-center w-10 h-10 rounded-full border transition-colors bg-slate-50 text-slate-400 hover:bg-primary hover:text-white border-slate-100 active:scale-90">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-12 flex flex-col items-center justify-center bg-white rounded-[2rem] border-2 border-dashed border-slate-100 text-center">
                        <span class="mb-2 text-4xl material-symbols-outlined text-slate-300">package_2</span>
                        <p class="text-sm font-bold text-slate-500">لا توجد شحنات مطابقة</p>
                    </div>
                @endforelse
            </div>

            {{-- الترقيم للطرود --}}
            @if ($shipments->hasPages())
                <div class="mt-4">
                    {{ $shipments->links('vendor.pagination.mobile') }}
                </div>
            @endif
        </div>

    </div>
@endsection