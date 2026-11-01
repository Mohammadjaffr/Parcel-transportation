@extends('layouts.app')

@section('title', 'كشف حساب السائق | ' . $driver->name)

@section('content')
    @php
        $credit  = $totalCredit ?? $driver->total_credit;
        $debit   = $totalDebit ?? $driver->total_debit;
        $bal     = $balance ?? $driver->balance;

        $driverInitials = collect(explode(' ', trim($driver->name)))
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_substr($word, 0, 1, 'utf-8'))
            ->implode('');
    @endphp

    <div
        x-data="{
            showNewEntryModal: false,
            showClearModal: false,
            entryType: 'debit',
            totalAmount: '',
            paidNow: 0,
            get remainingAmount() {
                let total = parseFloat(this.totalAmount) || 0;
                let paid = parseFloat(this.paidNow) || 0;
                return Math.max(0, total - paid);
            },
            clearType: '{{ $bal < 0 ? 'receive' : 'pay' }}',
            clearAmount: '{{ abs($bal) > 0 ? abs($bal) : '' }}'
        }"
        class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12"
        dir="rtl">

        {{-- ================= Modal: تسجيل قيد جديد (مع دفعة جزئية) ================= --}}
        <template x-teleport="body">
            <div x-show="showNewEntryModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4" dir="rtl">
                <div
                    x-show="showNewEntryModal"
                    x-transition.opacity
                    class="fixed inset-0 backdrop-blur-sm bg-slate-900/40 dark:bg-black/60"
                    @click="showNewEntryModal = false">
                </div>

                <div
                    x-show="showNewEntryModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                    class="relative z-10 p-6 w-full max-w-lg bg-white rounded-[2rem] border border-slate-100 shadow-2xl dark:bg-boxdark dark:border-boxdark-2 max-h-[90vh] overflow-y-auto">

                    <div class="flex justify-between items-center mb-5 border-b border-gray-100 dark:border-boxdark-2 pb-4">
                        <h3 class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white font-headline">
                            <span class="material-symbols-outlined text-primary">
                                post_add
                            </span>
                            تسجيل قيد مالي جديد للسائق
                        </h3>

                        <button type="button" @click="showNewEntryModal = false" class="transition-colors text-slate-400 hover:text-rose-500">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('drivers.storeTransaction', $driver->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="new_entry">

                        {{-- نوع القيد --}}
                        <div class="mb-4">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                نوع القيد المالي <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    :class="entryType === 'debit' ? 'border-rose-500 bg-rose-50/50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' : 'border-gray-200 bg-white text-gray-600 dark:bg-boxdark-2 dark:border-boxdark dark:text-gray-300'"
                                    class="flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all">
                                    <input type="radio" name="type" value="debit" x-model="entryType" class="hidden">
                                    <span class="material-symbols-outlined text-[20px] text-rose-500">north_east</span>
                                    <div>
                                        <div class="text-xs font-black">قيد على السائق (مدين)</div>
                                        <div class="text-[10px] text-gray-400">عهدة، أجور، غرامة، كشف</div>
                                    </div>
                                </label>

                                <label
                                    :class="entryType === 'credit' ? 'border-emerald-500 bg-emerald-50/50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'border-gray-200 bg-white text-gray-600 dark:bg-boxdark-2 dark:border-boxdark dark:text-gray-300'"
                                    class="flex items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all">
                                    <input type="radio" name="type" value="credit" x-model="entryType" class="hidden">
                                    <span class="material-symbols-outlined text-[20px] text-emerald-500">south_west</span>
                                    <div>
                                        <div class="text-xs font-black">قيد لصالح السائق (دائن)</div>
                                        <div class="text-[10px] text-gray-400">مستحقات، نسبة، أتعاب</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- المبلغ الكلي --}}
                        <div class="mb-4">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                المبلغ الكلي للقيد <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    name="total_amount"
                                    x-model="totalAmount"
                                    step="0.01"
                                    min="0.01"
                                    required
                                    placeholder="0.00"
                                    class="px-4 py-3 pl-12 w-full text-lg font-black rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20">
                                <span class="absolute left-4 top-1/2 text-sm font-bold -translate-y-1/2 text-slate-400">
                                    ر.ي
                                </span>
                            </div>
                        </div>

                        {{-- الدفعة المسددة حالياً (تظهر فقط عند قيد على السائق) --}}
                        <div x-show="entryType === 'debit'" x-transition class="mb-4 p-4 rounded-xl bg-amber-50/60 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20">
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-black text-amber-800 dark:text-amber-400">
                                    الدفعة المسددة حالياً نقدياً (إن وُجدت)
                                </label>
                                <span class="text-[10px] font-bold text-amber-600 bg-amber-100 dark:bg-amber-900/50 px-2 py-0.5 rounded">
                                    تورد للصندوق فوراً
                                </span>
                            </div>

                            <div class="relative">
                                <input
                                    type="number"
                                    name="paid_now"
                                    x-model="paidNow"
                                    step="0.01"
                                    min="0"
                                    :max="totalAmount || 0"
                                    placeholder="0.00"
                                    class="px-4 py-2.5 pl-12 w-full text-base font-black rounded-xl border transition-all outline-none bg-white dark:bg-boxdark border-amber-200 dark:border-amber-500/30 text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500/20">
                                <span class="absolute left-4 top-1/2 text-sm font-bold -translate-y-1/2 text-slate-400">
                                    ر.ي
                                </span>
                            </div>

                            {{-- شريط معاينة المتبقي الفوري --}}
                            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-amber-200/60 dark:border-amber-500/20">
                                <span class="text-amber-800 dark:text-amber-300 font-bold">المتبقي على السائق (يسجل كمديونية):</span>
                                <span class="font-black text-rose-600 dark:text-rose-400" x-text="remainingAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ر.ي'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            {{-- طريقة الدفع --}}
                            <div>
                                <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                    طريقة الدفع <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    name="payment_method"
                                    required
                                    class="px-4 py-2.5 w-full text-xs font-bold rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20">
                                    <option value="cash">نقداً (كاش)</option>
                                    <option value="bank_transfer">تحويل بنكي / حوالة</option>
                                </select>
                            </div>

                            {{-- رقم المرجع --}}
                            <div>
                                <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                    رقم المرجع / الإشعار (اختياري)
                                </label>
                                <input
                                    type="text"
                                    name="reference_number"
                                    placeholder="مثال: رقم الحوالة أو السند"
                                    class="px-4 py-2.5 w-full text-xs font-bold rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20">
                            </div>
                        </div>

                        {{-- البيان --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                البيان (التفاصيل والوصف) <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                name="description"
                                rows="3"
                                required
                                placeholder="مثال: كشف حساب رحلة رقم 12، عهدة وقود، تسليم بضائع..."
                                class="px-4 py-3 w-full text-xs font-bold rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20"></textarea>
                        </div>

                        <button
                            type="submit"
                            class="flex gap-2 justify-center items-center w-full py-3.5 text-sm font-black text-white rounded-xl shadow-lg transition-all active:scale-95 bg-primary hover:bg-primary-hover">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            حفظ وتأكيد القيد
                        </button>
                    </form>
                </div>
            </div>
        </template>

        {{-- ================= Modal: تصفية حساب (سند قبض أو صرف) ================= --}}
        <template x-teleport="body">
            <div x-show="showClearModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4" dir="rtl">
                <div
                    x-show="showClearModal"
                    x-transition.opacity
                    class="fixed inset-0 backdrop-blur-sm bg-slate-900/40 dark:bg-black/60"
                    @click="showClearModal = false">
                </div>

                <div
                    x-show="showClearModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                    class="relative z-10 p-6 w-full max-w-sm bg-white rounded-[2rem] border border-slate-100 shadow-2xl dark:bg-boxdark dark:border-boxdark-2">

                    <div class="flex justify-between items-center mb-5 border-b border-gray-100 dark:border-boxdark-2 pb-4">
                        <h3 class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white font-headline">
                            <span class="material-symbols-outlined text-primary">
                                payments
                            </span>
                            تصفية وسداد حساب السائق
                        </h3>

                        <button type="button" @click="showClearModal = false" class="transition-colors text-slate-400 hover:text-rose-500">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('drivers.storeTransaction', $driver->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="clear_amount">

                        {{-- نوع التصفية --}}
                        <div class="mb-4">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                نوع العملية <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <label
                                    :class="clearType === 'receive' ? 'border-emerald-500 bg-emerald-50/50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'border-gray-200 bg-white text-gray-600 dark:bg-boxdark-2 dark:border-boxdark dark:text-gray-300'"
                                    class="flex items-center gap-1.5 p-2.5 rounded-xl border-2 cursor-pointer transition-all">
                                    <input type="radio" name="clear_type" value="receive" x-model="clearType" class="hidden">
                                    <span class="material-symbols-outlined text-[18px] text-emerald-500">south_west</span>
                                    <div class="text-xs font-black">قبض من السائق</div>
                                </label>

                                <label
                                    :class="clearType === 'pay' ? 'border-rose-500 bg-rose-50/50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400' : 'border-gray-200 bg-white text-gray-600 dark:bg-boxdark-2 dark:border-boxdark dark:text-gray-300'"
                                    class="flex items-center gap-1.5 p-2.5 rounded-xl border-2 cursor-pointer transition-all">
                                    <input type="radio" name="clear_type" value="pay" x-model="clearType" class="hidden">
                                    <span class="material-symbols-outlined text-[18px] text-rose-500">north_east</span>
                                    <div class="text-xs font-black">صرف للسائق</div>
                                </label>
                            </div>
                        </div>

                        {{-- المبلغ --}}
                        <div class="mb-4">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                المبلغ المراد <span x-text="clearType === 'receive' ? 'قبضه وتوريده' : 'صرفه للسائق'"></span> <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="number"
                                    name="amount"
                                    x-model="clearAmount"
                                    step="0.01"
                                    min="0.01"
                                    required
                                    placeholder="0.00"
                                    class="px-4 py-3 pl-12 w-full text-lg font-black rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/20">
                                <span class="absolute left-4 top-1/2 text-sm font-bold -translate-y-1/2 text-slate-400">
                                    ر.ي
                                </span>
                            </div>
                        </div>

                        {{-- طريقة الدفع --}}
                        <div class="mb-4">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                طريقة الدفع <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="payment_method"
                                required
                                class="px-4 py-2.5 w-full text-xs font-bold rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20">
                                <option value="cash">نقداً من الصندوق</option>
                                <option value="bank_transfer">تحويل بنكي / حوالة</option>
                            </select>
                        </div>

                        {{-- رقم المرجع --}}
                        <div class="mb-4">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                رقم السند / الإشعار (اختياري)
                            </label>
                            <input
                                type="text"
                                name="reference_number"
                                placeholder="رقم إشعار الحوالة أو السند"
                                class="px-4 py-2.5 w-full text-xs font-bold rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20">
                        </div>

                        {{-- ملاحظات --}}
                        <div class="mb-6">
                            <label class="block mb-2 text-xs font-bold text-slate-500 dark:text-bodydark">
                                ملاحظات (اختياري)
                            </label>
                            <input
                                type="text"
                                name="notes"
                                placeholder="مثال: تسوية متبقي رحلة..."
                                class="px-4 py-2.5 w-full text-xs font-bold rounded-xl border transition-all outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20">
                        </div>

                        <button
                            type="submit"
                            :class="clearType === 'receive' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                            class="flex gap-2 justify-center items-center w-full py-3.5 text-sm font-black text-white rounded-xl shadow-lg transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">done_all</span>
                            <span x-text="clearType === 'receive' ? 'تأكيد القبض والتوريد' : 'تأكيد الصرف من الصندوق'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </template>

        {{-- ================= Top Bar / Header ================= --}}
        <div class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
            <div class="flex flex-wrap justify-between items-center gap-4 px-4 py-4 mx-auto max-w-7xl md:px-6">
                <div class="flex gap-4 items-center min-w-0">
                    <a href="{{ route('drivers.index') }}"
                        class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </a>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-black truncate md:text-2xl font-headline text-on-surface dark:text-white">
                                {{ $driver->name }}
                            </h1>
                            @if($driver->is_banned)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-rose-50 text-rose-600 border border-rose-200">
                                    محظور
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2 items-center mt-1">
                            <x-phone-number
                                :value="$driver->phone"
                                class="text-[11px] font-bold text-gray-500 dark:text-bodydark" />

                            <span class="w-1 h-1 bg-gray-300 rounded-full dark:bg-gray-600"></span>

                            <span class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                مركبة: {{ $driver->vehicle_type ?? 'غير محدد' }}
                            </span>

                            @if($driver->branch)
                                <span class="w-1 h-1 bg-gray-300 rounded-full dark:bg-gray-600"></span>
                                <span class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                    فرع: {{ $driver->branch->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap gap-2 items-center">
                    {{-- شحنات السائق --}}
                    <a href="{{ route('drivers.shipments', $driver->id) }}"
                        class="flex gap-2 justify-center items-center px-3 h-10 text-xs font-bold rounded-xl border transition-transform text-slate-700 bg-slate-50 border-slate-200 dark:bg-boxdark-2 dark:text-gray-300 dark:border-boxdark active:scale-95 hover:shadow-md">
                        <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                        <span class="hidden md:inline">الشحنات</span>
                    </a>

                    {{-- إرسال كشف الحساب عبر واتساب --}}
                    <a href="{{ route('whatsapp.driver.account.statement', $driver->id) }}"
                        target="_blank"
                        class="flex gap-2 justify-center items-center px-3 h-10 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform md:px-4 dark:bg-emerald-500/10 dark:text-emerald-400 active:scale-95 dark:border-emerald-500/20 hover:shadow-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span class="hidden md:inline">إرسال كشف الحساب واتساب</span>
                    </a>

                    {{-- طباعة كشف الحساب PDF --}}
                    <a href="{{ route('receipt.generate', ['type' => 'DriverAccountStatementReceipt', 'id' => $driver->uuid ?? $driver->id]) }}"
                        target="_blank"
                        class="flex gap-2 justify-center items-center px-3 h-10 text-xs font-bold rounded-xl border transition-transform text-primary bg-primary-container border-primary/10 md:px-4 dark:bg-primary/10 dark:text-primary active:scale-95 hover:shadow-md hover:bg-primary/10 dark:hover:bg-primary/20">
                        <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                        <span class="hidden md:inline">طباعة كشف الحساب PDF</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ================= Page Content ================= --}}
        <div class="flex flex-col gap-6 p-4 mx-auto max-w-7xl md:p-6">

            {{-- ================= الملخص المالي والبطاقات الإحصائية ================= --}}
            <div class="p-6 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <div class="flex flex-col gap-4 justify-between mb-5 md:flex-row md:items-center">
                    <div>
                        <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                            <span class="material-symbols-outlined text-primary bg-primary-container dark:bg-primary/10 p-1.5 rounded-lg text-[18px]">
                                account_balance_wallet
                            </span>
                            الملخص المالي للسائق
                        </h3>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            ملخص الرصيد والذمم والعهد المالية المسجلة على السائق
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2 items-center">
                        {{-- زر تسجيل قيد كشف جديد --}}
                        <button
                            @click="showNewEntryModal = true"
                            type="button"
                            class="flex gap-2 items-center px-4 h-10 text-xs font-black text-white rounded-xl shadow-sm transition-all active:scale-95 bg-primary hover:bg-primary-hover">
                            <span class="material-symbols-outlined text-[18px]">post_add</span>
                            تسجيل قيد / كشف جديد
                        </button>

                        {{-- زر تصفية حساب --}}
                        <button
                            @click="showClearModal = true"
                            type="button"
                            class="flex gap-2 items-center px-4 h-10 text-xs font-black text-white rounded-xl shadow-sm transition-all active:scale-95 {{ $bal < 0 ? 'bg-slate-800 hover:bg-slate-700 dark:bg-white dark:text-slate-800 dark:hover:bg-gray-100' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            <span class="material-symbols-outlined text-[18px]">
                                {{ $bal < 0 ? 'payments' : 'request_quote' }}
                            </span>
                            تصفية حساب / سند
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    {{-- بطاقة الرصيد الصافي --}}
                    <div class="relative overflow-hidden p-5 rounded-[1.5rem] border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="absolute right-0 top-0 bottom-0 w-1.5 {{ $bal < 0 ? 'bg-rose-500' : ($bal > 0 ? 'bg-emerald-500' : 'bg-slate-400') }}"></div>

                        <p class="mb-2 text-xs font-bold text-gray-500 dark:text-bodydark">
                            الرصيد الصافي المتبقي
                        </p>

                        <p class="text-3xl font-black font-headline {{ $bal < 0 ? 'text-rose-500 dark:text-rose-400' : ($bal > 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-300') }}">
                            {{ number_format(abs($bal), 2) }}
                            <span class="text-xs {{ $bal < 0 ? 'text-rose-400' : ($bal > 0 ? 'text-emerald-400' : 'text-slate-400') }}">ر.ي</span>
                        </p>

                        <div class="inline-flex px-3 py-1.5 mt-3 rounded-lg text-[10px] font-black {{ $bal < 0 ? 'bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' : ($bal > 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-slate-100 text-slate-600 dark:bg-boxdark dark:text-slate-400') }}">
                            {{ $bal < 0 ? 'متبقي على السائق (مدين للمكتب)' : ($bal > 0 ? 'متبقي للسائق (دائن على المكتب)' : 'حساب مصفى تماماً') }}
                        </div>
                    </div>

                    {{-- بطاقة ما على السائق (Debit) --}}
                    <div class="p-5 rounded-[1.5rem] border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                    إجمالي ما على السائق (مدين)
                                </p>
                                <p class="mt-2 text-2xl font-black text-rose-500 dark:text-rose-400 font-headline">
                                    {{ number_format($debit, 2) }}
                                    <span class="text-xs text-rose-400">ر.ي</span>
                                </p>
                                <p class="mt-1 text-[10px] text-gray-400">عهد، إرساليات، غرامات</p>
                            </div>

                            <div class="flex justify-center items-center w-11 h-11 text-rose-500 bg-rose-50 rounded-xl dark:bg-rose-500/10">
                                <span class="material-symbols-outlined text-[22px]">north_east</span>
                            </div>
                        </div>
                    </div>

                    {{-- بطاقة ما سدده / ما للسائق (Credit) --}}
                    <div class="p-5 rounded-[1.5rem] border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                    إجمالي ما سدده / له (دائن)
                                </p>
                                <p class="mt-2 text-2xl font-black text-emerald-500 dark:text-emerald-400 font-headline">
                                    {{ number_format($credit, 2) }}
                                    <span class="text-xs text-emerald-400">ر.ي</span>
                                </p>
                                <p class="mt-1 text-[10px] text-gray-400">سندات قبض، توريد كاش</p>
                            </div>

                            <div class="flex justify-center items-center w-11 h-11 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                                <span class="material-symbols-outlined text-[22px]">south_west</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= شريط الفلترة بالتاريخ ================= --}}
            <div class="p-4 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <form action="{{ route('drivers.show', $driver->id) }}" method="GET" class="flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[140px]">
                        <label class="block mb-1 text-[11px] font-bold text-gray-500 dark:text-bodydark">من تاريخ</label>
                        <input
                            type="date"
                            name="from_date"
                            value="{{ request('from_date') }}"
                            class="px-3 py-2 w-full text-xs font-bold rounded-xl border outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div class="flex-1 min-w-[140px]">
                        <label class="block mb-1 text-[11px] font-bold text-gray-500 dark:text-bodydark">إلى تاريخ</label>
                        <input
                            type="date"
                            name="to_date"
                            value="{{ request('to_date') }}"
                            class="px-3 py-2 w-full text-xs font-bold rounded-xl border outline-none bg-slate-50 dark:bg-boxdark-2 border-slate-200 dark:border-boxdark text-slate-700 dark:text-white focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="flex items-center gap-1.5 px-4 py-2 text-xs font-black text-white rounded-xl bg-primary hover:bg-primary-hover transition-colors">
                            <span class="material-symbols-outlined text-[16px]">filter_list</span>
                            تصفية
                        </button>

                        @if(request('from_date') || request('to_date'))
                            <a
                                href="{{ route('drivers.show', $driver->id) }}"
                                class="flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 dark:bg-boxdark dark:border-boxdark dark:text-gray-300">
                                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                                إعادة تعيين
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- ================= جدول الحركات المالية ================= --}}
            <div class="bg-white border border-gray-100 shadow-sm dark:bg-boxdark rounded-[2rem] dark:border-boxdark-2 relative">
                <div class="flex flex-col gap-3 justify-between p-5 border-b border-gray-100 md:flex-row md:items-center dark:border-boxdark-2">
                    <div>
                        <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                            <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                            سجل القيود والحركات المالية
                        </h3>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            كافة القيود وسندات القبض والصرف المرتبطة بالسائق
                        </p>
                    </div>
                </div>

                {{-- Desktop Table --}}
                <div class="hidden pb-4 md:block overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
                                <th class="px-5 py-4">#</th>
                                <th class="px-5 py-4">التاريخ والوقت</th>
                                <th class="px-5 py-4">نوع الحركة</th>
                                <th class="px-5 py-4">البيان</th>
                                <th class="px-5 py-4">سند الصندوق</th>
                                <th class="px-5 py-4 text-left">المبلغ</th>
                                <th class="px-5 py-4 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                            @forelse($transactions as $trans)
                                <tr class="transition-colors hover:bg-gray-50/70 dark:hover:bg-boxdark-2/50 group">
                                    <td class="px-5 py-4">
                                        <span class="text-xs font-black text-gray-400">
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-xs font-black text-gray-700 dark:text-gray-200">
                                                {{ $trans->created_at->format('Y-m-d') }}
                                            </span>
                                            <span class="text-[10px] font-bold text-gray-400">
                                                {{ $trans->created_at->format('h:i A') }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        @if($trans->type === 'credit')
                                            <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                                <span class="material-symbols-outlined text-[14px]">south_west</span>
                                                قبض / توريد
                                            </span>
                                        @else
                                            <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black rounded-xl bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                                                <span class="material-symbols-outlined text-[14px]">north_east</span>
                                                قيد / عهدة
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <p class="max-w-[320px] text-xs font-bold leading-6 text-gray-700 dark:text-gray-200" title="{{ $trans->description }}">
                                            {{ $trans->description }}
                                        </p>
                                        @if($trans->creator)
                                            <span class="text-[10px] text-gray-400">بواسطة: {{ $trans->creator->name }}</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        @if($trans->cashTransaction)
                                            <span class="text-xs font-black text-slate-700 dark:text-gray-300">
                                                #{{ $trans->cashTransaction->receipt_number }}
                                            </span>
                                            <div class="text-[10px] text-gray-400">
                                                {{ $trans->cashTransaction->payment_method === 'cash' ? 'كاش' : 'تحويل' }}
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">قيد دفتري</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-left">
                                        <span class="text-sm font-black font-headline {{ $trans->type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}" dir="ltr">
                                            {{ $trans->type === 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- زر إرسال واتساب --}}
                                            <a
                                                href="{{ route('whatsapp.driver.transaction', $trans->id) }}"
                                                target="_blank"
                                                title="إرسال سند الحركة واتساب"
                                                class="flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 transition-colors">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                                </svg>
                                            </a>

                                            {{-- زر طباعة السند PDF --}}
                                            <a
                                                href="{{ route('receipt.generate', ['type' => 'driver_transaction', 'id' => $trans->id]) }}"
                                                target="_blank"
                                                title="طباعة السند PDF"
                                                class="flex items-center justify-center w-8 h-8 rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-boxdark-2 dark:text-white transition-colors">
                                                <span class="material-symbols-outlined text-[16px]">print</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-16 text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="flex justify-center items-center mb-3 w-14 h-14 bg-gray-50 rounded-2xl dark:bg-boxdark-2">
                                                <span class="material-symbols-outlined text-[28px] text-gray-300 dark:text-gray-600">receipt_long</span>
                                            </div>
                                            <p class="text-xs font-bold text-gray-500 dark:text-bodydark">
                                                لا توجد حركات مالية مسجلة لهذا السائق
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
                        <div class="p-4 rounded-2xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                            <div class="flex gap-3 justify-between items-start">
                                <div class="flex gap-3 items-start min-w-0">
                                    <div class="flex justify-center items-center w-10 h-10 rounded-xl shrink-0 {{ $trans->type === 'credit' ? 'bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400' }}">
                                        <span class="material-symbols-outlined text-[20px]">
                                            {{ $trans->type === 'credit' ? 'south_west' : 'north_east' }}
                                        </span>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-gray-800 dark:text-white">
                                            {{ $trans->type === 'credit' ? 'قبض / توريد' : 'قيد / عهدة' }}
                                        </p>
                                        <p class="mt-1 text-[11px] font-bold text-gray-600 dark:text-gray-300">
                                            {{ $trans->description }}
                                        </p>
                                        <p class="mt-1 text-[10px] font-bold text-gray-400">
                                            {{ $trans->created_at->format('Y-m-d h:i A') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-left shrink-0">
                                    <span class="text-sm font-black {{ $trans->type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}" dir="ltr">
                                        {{ $trans->type === 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-boxdark">
                                <a
                                    href="{{ route('whatsapp.driver.transaction', $trans->id) }}"
                                    target="_blank"
                                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-lg dark:bg-emerald-500/10">
                                    <span>واتساب</span>
                                </a>

                                <a
                                    href="{{ route('receipt.generate', ['type' => 'driver_transaction', 'id' => $trans->id]) }}"
                                    target="_blank"
                                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-100 rounded-lg dark:bg-boxdark dark:text-white">
                                    <span>طباعة السند</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-xs font-bold text-gray-400">
                            لا توجد حركات مالية مسجلة
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Links --}}
                @if($transactions->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-boxdark-2">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection

