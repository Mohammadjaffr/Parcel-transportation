@extends('receipts.layout')

@section('title', $title . ' - #' . $transaction_id)

@section('content')
    <div
        class="max-w-2xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        <div class="p-4 bg-white border-b sm:p-6 border-slate-100">
            @include('receipts.partials.receipt_header')
        </div>

        <div class="p-6 space-y-6 sm:p-8">

            {{-- بطاقة المبلغ ونوع الحركة --}}
            <div class="py-6 text-center rounded-3xl border bg-slate-50 border-slate-100">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 mb-2 text-xs font-black rounded-full {{ $is_credit ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ $is_credit ? 'سند قبض وتوريد' : 'سند قيد مديونية / عهدة' }}
                </span>
                <p class="mb-2 text-sm font-bold text-slate-500">مبلغ السند</p>
                <h2 class="text-4xl font-black {{ $is_credit ? 'text-emerald-600' : 'text-rose-600' }}" dir="ltr">
                    {{ $amount }} <span class="text-lg text-slate-400">ر.ي</span>
                </h2>
            </div>

            {{-- بيانات السائق وتفاصيل الحركة --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="p-5 rounded-2xl border border-amber-100 bg-amber-50/40">
                    <div class="flex gap-2 items-center mb-3">
                        <div class="flex justify-center items-center w-8 h-8 bg-amber-100 rounded-lg">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold tracking-wider text-amber-700 uppercase">بيانات السائق</h3>
                    </div>
                    <div class="space-y-2">
                        <div><span class="text-xs text-slate-400">الاسم:</span> <span
                                class="text-sm font-bold text-slate-800">{{ $driver_name }}</span></div>
                        <div><span class="text-xs text-slate-400">الهاتف:</span> <span
                                class="text-sm font-bold text-slate-800" dir="ltr">{{ $driver_phone }}</span></div>
                        <div><span class="text-xs text-slate-400">الفرع:</span> <span
                                class="text-sm font-medium text-slate-700">{{ $driver_branch }}</span></div>
                    </div>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200">
                    <div class="flex gap-2 items-center mb-3">
                        <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-slate-100">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold tracking-wider uppercase text-slate-600">تفاصيل السند</h3>
                    </div>
                    <div class="space-y-2">
                        <div><span class="text-xs text-slate-400">طريقة الدفع:</span> <span
                                class="text-sm font-bold text-slate-800">{{ $payment_method ?? 'نقداً' }}</span></div>
                        <div><span class="text-xs text-slate-400">سند الصندوق:</span> <span
                                class="text-sm font-bold text-slate-800">{{ $cash_receipt_number ? '#' . $cash_receipt_number : 'قيد دفتري' }}</span>
                        </div>
                        <div><span class="text-xs text-slate-400">التاريخ:</span> <span
                                class="text-sm font-medium text-slate-700">{{ $date }}</span></div>
                    </div>
                </div>
            </div>

            {{-- البيان والملاحظات --}}
            <div class="p-5 bg-white rounded-2xl border border-slate-200">
                <h3 class="flex gap-2 items-center mb-3 text-sm font-black text-slate-800">
                    <span class="w-1.5 h-5 rounded-full {{ $is_credit ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    البيان (التفاصيل)
                </h3>
                <p class="text-sm font-bold leading-relaxed text-slate-700">{{ $description }}</p>

                @if (!empty($notes) && $notes !== '---')
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <p class="mb-1 text-xs font-bold text-slate-400">ملاحظات إضافية:</p>
                        <p class="text-sm text-slate-600">{{ $notes }}</p>
                    </div>
                @endif
            </div>

            {{-- ملخص الرصيد السابق والحالي --}}
            <div class="grid grid-cols-2 gap-4 p-4 text-center rounded-2xl border bg-slate-50 border-slate-100">
                <div class="p-3 bg-white rounded-xl border border-slate-100">
                    <p class="mb-1 text-xs font-bold text-slate-400">الرصيد السابق</p>
                    <p class="text-base font-black text-slate-700" dir="ltr">
                        {{ number_format(abs($previous_balance), 2) }}
                        <span class="text-xs text-slate-400">ر.ي</span>
                    </p>
                    <span
                        class="text-[10px] font-bold {{ $previous_balance < 0 ? 'text-rose-500' : ($previous_balance > 0 ? 'text-emerald-500' : 'text-slate-400') }}">
                        {{ $previous_balance < 0 ? 'عليه' : ($previous_balance > 0 ? 'له' : 'مصفى') }}
                    </span>
                </div>

                <div class="p-3 bg-white rounded-xl border border-slate-100">
                    <p class="mb-1 text-xs font-bold text-slate-400">الرصيد المتبقي الحالي</p>
                    <p class="text-base font-black {{ $current_balance < 0 ? 'text-rose-600' : ($current_balance > 0 ? 'text-emerald-600' : 'text-slate-700') }}"
                        dir="ltr">
                        {{ number_format(abs($current_balance), 2) }}
                        <span class="text-xs text-slate-400">ر.ي</span>
                    </p>
                    <span
                        class="text-[10px] font-bold {{ $current_balance < 0 ? 'text-rose-500' : ($current_balance > 0 ? 'text-emerald-500' : 'text-slate-400') }}">
                        {{ $current_balance < 0 ? 'متبقي على السائق' : ($current_balance > 0 ? 'متبقي للسائق' : 'حساب مصفى') }}
                    </span>
                </div>
            </div>

        </div>

        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة:
                {{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}
            </p>

            <div class="pt-3 mt-3 border-t border-slate-700/50">
                <p class="text-[10px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr"
                        class="font-mono text-slate-400">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection
