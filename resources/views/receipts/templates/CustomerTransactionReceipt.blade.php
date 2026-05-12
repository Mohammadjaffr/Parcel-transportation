@extends('receipts.layout')

@section('title', $title . ' - ' . $transaction_id)

@section('content')
    <div
        class="max-w-2xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        <div
            class="p-6 sm:p-8 border-b border-slate-100 {{ $is_credit ? 'bg-gradient-to-l from-emerald-50/50 to-white' : 'bg-gradient-to-l from-rose-50/50 to-white' }}">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5">
                <div class="flex items-center gap-4">

                    <div
                        class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center p-2 border border-slate-100 shrink-0">
                        @if(!empty($company['logo']))
                            <img src="{{ $company['logo'] }}" alt="Logo" class="w-full h-full object-contain">
                        @else
                            <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Logo"
                                class="w-full h-full object-contain">
                        @endif
                    </div>

                    <div>
                        <h1 class="text-xl font-black text-slate-800 tracking-tight">{{ $company['name'] ?? 'الشركة' }}</h1>
                        <p class="text-slate-500 font-medium text-xs mt-1">
                            {{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}
                        </p>
                        <p class="text-slate-500 font-bold text-xs mt-1">رقم الحركة: {{ $transaction_id }}</p>
                    </div>
                </div>

                <div class="text-right">
                    <div
                        class="inline-flex items-center px-3 py-1.5 rounded-xl font-bold text-xs border {{ $is_credit ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                        {{ $title }}
                    </div>
                    <div class="text-slate-400 text-xs font-medium mt-1.5" dir="ltr">{{ $date }}</div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">

            <div class="text-center py-6 bg-slate-50 rounded-3xl border border-slate-100">
                <p class="text-sm font-bold text-slate-500 mb-2">المبلغ الكلي</p>
                <h2 class="text-4xl font-black {{ $is_credit ? 'text-emerald-600' : 'text-rose-600' }}" dir="ltr">
                    {{ $amount }} <span class="text-lg text-slate-400">ر.ي</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-blue-50/50 rounded-2xl border border-blue-100 p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold text-blue-600 uppercase tracking-wider">بيانات العميل</h3>
                    </div>
                    <div class="space-y-2">
                        <div><span class="text-xs text-slate-400">الاسم:</span> <span
                                class="text-slate-800 font-bold text-sm">{{ $customer_name }}</span></div>
                        <div><span class="text-xs text-slate-400">الهاتف:</span> <span
                                class="text-slate-800 font-bold text-sm" dir="ltr">{{ $customer_phone }}</span></div>
                        <div><span class="text-xs text-slate-400">الفرع:</span> <span
                                class="text-slate-700 font-medium text-sm">{{ $customer_branch }}</span></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">تفاصيل الحركة</h3>
                    </div>
                    <div class="space-y-2">
                        <div><span class="text-xs text-slate-400">طريقة الدفع:</span> <span
                                class="text-slate-800 font-bold text-sm">{{ $payment_method }}</span></div>
                        <div><span class="text-xs text-slate-400">المرجع:</span> <span
                                class="text-slate-800 font-bold text-sm">{{ $reference_number }}</span></div>
                        <div><span class="text-xs text-slate-400">التاريخ:</span> <span
                                class="text-slate-700 font-medium text-sm">{{ $date }}</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="text-sm font-black text-slate-800 mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-5 rounded-full {{ $is_credit ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    البيان (التفاصيل)
                </h3>
                <p class="text-slate-700 font-bold text-sm leading-relaxed">{{ $description }}</p>

                @if($notes !== 'لا توجد ملاحظات')
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-xs text-slate-400 font-bold mb-1">ملاحظات إضافية:</p>
                        <p class="text-slate-600 text-sm">{{ $notes }}</p>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-8 mt-10 mb-2">
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 mb-8">توقيع العميل المستلم/الدافع</p>
                    <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 mb-8">توقيع الموظف المختص</p>
                    <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            {{-- بيانات الفاتورة --}}
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? now()->format('Y-m-d H:i') }}
            </p>

            {{-- الخط الفاصل التسويقي لشركة تيار --}}
            <div class="mt-3 pt-3 border-t border-slate-700/50">
                <p class="text-[10px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr" class="text-slate-400 font-mono">+967 780 261 952</span>
                </p>
            </div>
        </div>
    </div>
@endsection