@extends('mobile.layouts.app')

@section('title', 'ملف السائق | ' . $driver->name)

@section('content')
    @php
        $credit = $totalCredit ?? $driver->total_credit;
        $debit  = $totalDebit ?? $driver->total_debit;
        $bal    = $balance ?? $driver->balance;

        $words = explode(' ', trim($driver->name));
        $initials = mb_substr($words[0] ?? '', 0, 1, 'utf-8') . (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
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
        class="flex flex-col gap-4 px-4 pt-4 pb-24 min-h-screen bg-slate-50/50"
        dir="rtl">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex justify-between items-center">
            <div class="flex gap-3 items-center">
                <a href="{{ route('drivers.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <h1 class="text-xl font-black font-headline text-slate-800">كشف حساب السائق</h1>
            </div>

            <div class="flex gap-2 items-center">
                {{-- زر إرسال كشف الحساب واتساب --}}
                <a href="{{ route('whatsapp.driver.account.statement', $driver->id) }}" target="_blank"
                    class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform active:scale-95">
                    <svg class="w-5 h-5 fill-emerald-500" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>
                </a>

                {{-- زر طباعة كشف الحساب --}}
                <a href="{{ route('receipt.generate', ['type' => 'DriverAccountStatementReceipt', 'id' => $driver->uuid ?? $driver->id]) }}"
                    target="_blank"
                    class="flex gap-1.5 items-center px-3 h-10 text-xs font-bold rounded-xl border transition-transform border-primary/20 bg-primary/5 text-primary active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    PDF
                </a>
            </div>
        </div>

        {{-- ================= بطاقة تعريف السائق ================= --}}
        <div class="bg-white p-4 rounded-[2rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
                <div class="flex justify-center items-center w-14 h-14 text-xl font-black rounded-2xl border shadow-inner bg-gradient-to-br from-amber-500/10 to-amber-500/5 text-amber-600 border-amber-200/50 shrink-0">
                    {{ $initials }}
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h2 class="text-base font-black text-slate-800 font-headline truncate">{{ $driver->name }}</h2>
                        @if($driver->is_banned)
                            <span class="px-1.5 py-0.5 text-[9px] font-bold rounded bg-rose-50 text-rose-600 border border-rose-200 shrink-0">محظور</span>
                        @endif
                    </div>
                    <div class="flex gap-2 items-center mt-0.5 text-slate-500 text-xs">
                        <span class="material-symbols-outlined text-[14px] text-slate-400">phone</span>
                        <p class="font-mono text-xs font-bold tracking-wider"><x-phone-number :value="$driver->phone" /></p>
                    </div>
                    @if($driver->vehicle_type)
                        <p class="text-[10px] text-slate-400 mt-0.5">مركبة: {{ $driver->vehicle_type }}</p>
                    @endif
                </div>
            </div>

            <a href="{{ route('drivers.shipments', $driver->id) }}"
                class="flex items-center justify-center p-2 rounded-xl text-slate-600 bg-slate-50 border border-slate-200 shrink-0">
                <span class="material-symbols-outlined text-[20px]">local_shipping</span>
            </a>
        </div>

        {{-- ================= الداشبورد المالي ================= --}}
        <div class="grid grid-cols-2 gap-3">
            {{-- الرصيد الصافي --}}
            <div class="col-span-2 bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center justify-between relative overflow-hidden">
                <div class="absolute right-0 top-0 bottom-0 w-1.5 {{ $bal < 0 ? 'bg-rose-500' : ($bal > 0 ? 'bg-emerald-500' : 'bg-slate-400') }}"></div>

                <div class="pr-2">
                    <p class="text-[10px] font-bold text-slate-400 mb-1">الرصيد الصافي للسائق</p>
                    <p class="text-2xl font-black font-headline {{ $bal < 0 ? 'text-rose-600' : ($bal > 0 ? 'text-emerald-600' : 'text-slate-700') }}">
                        {{ number_format(abs($bal), 2) }} <span class="text-xs {{ $bal < 0 ? 'text-rose-400' : ($bal > 0 ? 'text-emerald-400' : 'text-slate-400') }}">ر.ي</span>
                    </p>
                </div>

                <div class="flex flex-col items-end gap-1.5">
                    <div class="px-2.5 py-1 rounded-lg text-[10px] font-black {{ $bal < 0 ? 'bg-rose-50 text-rose-600 border border-rose-100' : ($bal > 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-600') }}">
                        {{ $bal < 0 ? 'متبقي عليه (مدين)' : ($bal > 0 ? 'متبقي له (دائن)' : 'حساب مصفى') }}
                    </div>
                </div>
            </div>

            {{-- ما على السائق (مدين) --}}
            <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                <div class="flex items-center gap-1.5 mb-1.5 text-rose-500">
                    <span class="material-symbols-outlined text-[16px]">north_east</span>
                    <p class="text-[10px] font-bold text-slate-500">ما عليه (مدين)</p>
                </div>
                <p class="text-base font-black text-rose-600 font-headline">{{ number_format($debit, 2) }}</p>
            </div>

            {{-- ما سدده / له (دائن) --}}
            <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                <div class="flex items-center gap-1.5 mb-1.5 text-emerald-500">
                    <span class="material-symbols-outlined text-[16px]">south_west</span>
                    <p class="text-[10px] font-bold text-slate-500">ما سدده (دائن)</p>
                </div>
                <p class="text-base font-black text-emerald-600 font-headline">{{ number_format($credit, 2) }}</p>
            </div>
        </div>

        {{-- أزرار الإجراءات السريعة --}}
        <div class="grid grid-cols-2 gap-3">
            <button
                @click="showNewEntryModal = true"
                type="button"
                class="flex items-center justify-center gap-1.5 py-3 px-3 text-white rounded-xl text-xs font-black shadow-sm bg-primary active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">post_add</span>
                تسجيل قيد جديد
            </button>

            <button
                @click="showClearModal = true"
                type="button"
                class="flex items-center justify-center gap-1.5 py-3 px-3 text-white rounded-xl text-xs font-black shadow-sm {{ $bal < 0 ? 'bg-slate-800' : 'bg-emerald-600' }} active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                تصفية وسداد
            </button>
        </div>

        {{-- ================= Modal: تسجيل قيد جديد ================= --}}
        <template x-teleport="body">
            <div x-show="showNewEntryModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4" dir="rtl">
                <div x-show="showNewEntryModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showNewEntryModal = false"></div>

                <div x-show="showNewEntryModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                     class="bg-white w-full max-w-sm rounded-[2rem] p-5 shadow-2xl relative z-10 border border-slate-100 max-h-[85vh] overflow-y-auto">

                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                        <h3 class="text-base font-black text-slate-800 font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">post_add</span>
                            تسجيل قيد مالي جديد
                        </h3>
                        <button @click="showNewEntryModal = false" class="text-slate-400 hover:text-rose-500">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('drivers.storeTransaction', $driver->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="new_entry">

                        <div class="mb-3">
                            <label class="block mb-1.5 text-xs font-bold text-slate-500">نوع القيد</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label
                                    :class="entryType === 'debit' ? 'border-rose-500 bg-rose-50 text-rose-700' : 'border-slate-200 bg-white text-slate-600'"
                                    class="flex items-center gap-1.5 p-2 rounded-xl border-2 cursor-pointer text-xs font-black">
                                    <input type="radio" name="type" value="debit" x-model="entryType" class="hidden">
                                    <span class="material-symbols-outlined text-[16px] text-rose-500">north_east</span>
                                    <span>قيد عليه (مدين)</span>
                                </label>

                                <label
                                    :class="entryType === 'credit' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-600'"
                                    class="flex items-center gap-1.5 p-2 rounded-xl border-2 cursor-pointer text-xs font-black">
                                    <input type="radio" name="type" value="credit" x-model="entryType" class="hidden">
                                    <span class="material-symbols-outlined text-[16px] text-emerald-500">south_west</span>
                                    <span>قيد له (دائن)</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 text-xs font-bold text-slate-500">المبلغ الكلي</label>
                            <input
                                type="number"
                                name="total_amount"
                                x-model="totalAmount"
                                step="0.01"
                                min="0.01"
                                required
                                placeholder="0.00"
                                class="px-3 py-2.5 w-full text-base font-black rounded-xl border border-slate-200 bg-slate-50 outline-none focus:ring-2 focus:ring-primary/20">
                        </div>

                        <div x-show="entryType === 'debit'" class="mb-3 p-3 rounded-xl bg-amber-50 border border-amber-200">
                            <label class="block mb-1 text-xs font-black text-amber-800">الدفعة المسددة حالياً (كاش)</label>
                            <input
                                type="number"
                                name="paid_now"
                                x-model="paidNow"
                                step="0.01"
                                min="0"
                                :max="totalAmount || 0"
                                placeholder="0.00"
                                class="px-3 py-2 w-full text-sm font-black rounded-lg border border-amber-300 bg-white outline-none">
                            <div class="mt-2 flex justify-between text-xs font-bold text-amber-900">
                                <span>المتبقي عليه:</span>
                                <span class="font-black text-rose-600" x-text="remainingAmount.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' ر.ي'"></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 text-xs font-bold text-slate-500">طريقة الدفع</label>
                            <select name="payment_method" class="px-3 py-2 w-full text-xs font-bold rounded-xl border border-slate-200 bg-slate-50 outline-none">
                                <option value="cash">نقداً (كاش)</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 text-xs font-bold text-slate-500">البيان</label>
                            <textarea
                                name="description"
                                rows="2"
                                required
                                placeholder="تفاصيل القيد أو الكشف..."
                                class="px-3 py-2 w-full text-xs font-bold rounded-xl border border-slate-200 bg-slate-50 outline-none"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 text-xs font-black text-white rounded-xl bg-primary hover:bg-primary-hover shadow-sm">
                            حفظ وتأكيد
                        </button>
                    </form>
                </div>
            </div>
        </template>

        {{-- ================= Modal: تصفية وسداد ================= --}}
        <template x-teleport="body">
            <div x-show="showClearModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center px-4" dir="rtl">
                <div x-show="showClearModal" x-transition.opacity class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showClearModal = false"></div>

                <div x-show="showClearModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                     class="bg-white w-full max-w-sm rounded-[2rem] p-5 shadow-2xl relative z-10 border border-slate-100">

                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                        <h3 class="text-base font-black text-slate-800 font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">payments</span>
                            تصفية وسداد حساب
                        </h3>
                        <button @click="showClearModal = false" class="text-slate-400 hover:text-rose-500">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('drivers.storeTransaction', $driver->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="clear_amount">

                        <div class="mb-3">
                            <label class="block mb-1.5 text-xs font-bold text-slate-500">نوع التصفية</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label
                                    :class="clearType === 'receive' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-600'"
                                    class="flex items-center gap-1.5 p-2 rounded-xl border-2 cursor-pointer text-xs font-black">
                                    <input type="radio" name="clear_type" value="receive" x-model="clearType" class="hidden">
                                    <span class="material-symbols-outlined text-[16px] text-emerald-500">south_west</span>
                                    <span>قبض وتوريد</span>
                                </label>

                                <label
                                    :class="clearType === 'pay' ? 'border-rose-500 bg-rose-50 text-rose-700' : 'border-slate-200 bg-white text-slate-600'"
                                    class="flex items-center gap-1.5 p-2 rounded-xl border-2 cursor-pointer text-xs font-black">
                                    <input type="radio" name="clear_type" value="pay" x-model="clearType" class="hidden">
                                    <span class="material-symbols-outlined text-[16px] text-rose-500">north_east</span>
                                    <span>صرف للسائق</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 text-xs font-bold text-slate-500">المبلغ</label>
                            <input
                                type="number"
                                name="amount"
                                x-model="clearAmount"
                                step="0.01"
                                min="0.01"
                                required
                                placeholder="0.00"
                                class="px-3 py-2.5 w-full text-base font-black rounded-xl border border-slate-200 bg-slate-50 outline-none">
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 text-xs font-bold text-slate-500">طريقة الدفع</label>
                            <select name="payment_method" class="px-3 py-2 w-full text-xs font-bold rounded-xl border border-slate-200 bg-slate-50 outline-none">
                                <option value="cash">نقداً من الصندوق</option>
                                <option value="bank_transfer">تحويل بنكي</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-1 text-xs font-bold text-slate-500">ملاحظات (اختياري)</label>
                            <input
                                type="text"
                                name="notes"
                                placeholder="سند سداد / تسوية..."
                                class="px-3 py-2 w-full text-xs font-bold rounded-xl border border-slate-200 bg-slate-50 outline-none">
                        </div>

                        <button
                            type="submit"
                            :class="clearType === 'receive' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                            class="w-full py-3 text-xs font-black text-white rounded-xl shadow-sm">
                            <span x-text="clearType === 'receive' ? 'تأكيد القبض والتوريد' : 'تأكيد الصرف من الصندوق'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </template>

        {{-- ================= قائمة الحركات المالية ================= --}}
        <div class="flex flex-col gap-3">
            <h3 class="text-sm font-black text-slate-800 font-headline flex items-center gap-1.5 px-1">
                <span class="material-symbols-outlined text-[18px] text-primary">history</span>
                سجل القيود المالية
            </h3>

            @forelse($transactions as $trans)
                <div class="p-4 bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-3">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <div class="flex justify-center items-center w-9 h-9 rounded-xl shrink-0 {{ $trans->type === 'credit' ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }}">
                                <span class="material-symbols-outlined text-[18px]">
                                    {{ $trans->type === 'credit' ? 'south_west' : 'north_east' }}
                                </span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-xs font-black text-slate-800">
                                    {{ $trans->type === 'credit' ? 'قبض / توريد' : 'قيد / عهدة' }}
                                </p>
                                <p class="text-xs font-bold text-slate-600 mt-0.5 line-clamp-2">
                                    {{ $trans->description }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-1">
                                    {{ $trans->created_at->format('Y-m-d h:i A') }}
                                </p>
                            </div>
                        </div>

                        <div class="text-left shrink-0">
                            <span class="text-sm font-black {{ $trans->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}" dir="ltr">
                                {{ $trans->type === 'credit' ? '+' : '-' }}{{ number_format($trans->amount, 2) }}
                            </span>
                            <div class="text-[10px] text-slate-400">ر.ي</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2.5 border-t border-slate-50 text-xs">
                        <span class="text-[10px] font-bold text-slate-400">
                            {{ $trans->cashTransaction ? 'سند #' . $trans->cashTransaction->receipt_number : 'قيد دفتري' }}
                        </span>

                        <div class="flex gap-2">
                            <a
                                href="{{ route('whatsapp.driver.transaction', $trans->id) }}"
                                target="_blank"
                                class="flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 rounded-lg">
                                <span>واتساب</span>
                            </a>

                            <a
                                href="{{ route('receipt.generate', ['type' => 'driver_transaction', 'id' => $trans->id]) }}"
                                target="_blank"
                                class="flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-slate-700 bg-slate-100 rounded-lg">
                                <span>السند</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-xs font-bold text-slate-400 bg-white rounded-2xl border border-slate-100">
                    لا توجد حركات مالية مسجلة لهذا السائق
                </div>
            @endforelse

            @if($transactions->hasPages())
                <div class="pt-2">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

