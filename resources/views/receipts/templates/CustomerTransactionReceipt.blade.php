@extends('receipts.layout')

@section('title', $title . ' - ' . $transaction_id)

@section('content')
    <div
        class="max-w-2xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        <div
            class="p-6 sm:p-8 border-b border-slate-100 {{ $is_credit ? 'bg-gradient-to-l from-emerald-50/50 to-white' : 'bg-gradient-to-l from-rose-50/50 to-white' }}">
            <div class="flex flex-col gap-5 justify-between items-start sm:flex-row sm:items-center">
                <div class="flex gap-4 items-center">

                    <div
                        class="flex justify-center items-center p-2 w-14 h-14 bg-white rounded-2xl border shadow-sm border-slate-100 shrink-0">
                        @if(!empty($company['logo']))
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-full h-full">
                        @else
                            <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Logo"
                                class="object-contain w-full h-full">
                        @endif
                    </div>

                    <div>
                        <h1 class="text-xl font-black tracking-tight text-slate-800">{{ $company['name'] ?? 'الشركة' }}</h1>
                        <p class="mt-1 text-xs font-medium text-slate-500">
                            {{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}
                        </p>
                        <p class="mt-1 text-xs font-bold text-slate-500">رقم الحركة: {{ $transaction_id }}</p>
                    </div>
                </div>

                <div class="text-right">
                    <div
                        class="inline-flex items-center px-3 py-1.5 rounded-xl font-bold text-xs border {{ $is_credit ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }}">
                        {{ $title }}
                    </div>
                    <div class="mt-1.5 text-xs font-medium text-slate-400" dir="ltr">{{ $date }}</div>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6 sm:p-8">

            <div class="py-6 text-center rounded-3xl border bg-slate-50 border-slate-100">
                <p class="mb-2 text-sm font-bold text-slate-500">المبلغ الكلي</p>
                <h2 class="text-4xl font-black {{ $is_credit ? 'text-emerald-600' : 'text-rose-600' }}" dir="ltr">
                    {{ $amount }} <span class="text-lg text-slate-400">ر.ي</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="p-5 rounded-2xl border border-blue-100 bg-blue-50/50">
                    <div class="flex gap-2 items-center mb-3">
                        <div class="flex justify-center items-center w-8 h-8 bg-blue-100 rounded-lg">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-bold tracking-wider text-blue-600 uppercase">بيانات العميل</h3>
                    </div>
                    <div class="space-y-2">
                        <div><span class="text-xs text-slate-400">الاسم:</span> <span
                                class="text-sm font-bold text-slate-800">{{ $customer_name }}</span></div>
                        <div><span class="text-xs text-slate-400">الهاتف:</span> <span
                                class="text-sm font-bold text-slate-800" dir="ltr">{{ $customer_phone }}</span></div>
                        <div><span class="text-xs text-slate-400">الفرع:</span> <span
                                class="text-sm font-medium text-slate-700">{{ $customer_branch }}</span></div>
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
                        <h3 class="text-xs font-bold tracking-wider uppercase text-slate-600">تفاصيل الحركة</h3>
                    </div>
                    <div class="space-y-2">
                        <div><span class="text-xs text-slate-400">طريقة الدفع:</span> <span
                                class="text-sm font-bold text-slate-800">---</span></div>
                        <div><span class="text-xs text-slate-400">المرجع:</span> <span
                                class="text-sm font-bold text-slate-800">{{ $reference_number }}</span></div>
                        <div><span class="text-xs text-slate-400">التاريخ:</span> <span
                                class="text-sm font-medium text-slate-700">{{ $date }}</span></div>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200">
                <h3 class="flex gap-2 items-center mb-3 text-sm font-black text-slate-800">
                    <span class="w-1.5 h-5 rounded-full {{ $is_credit ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    البيان (التفاصيل)
                </h3>
                <p class="text-sm font-bold leading-relaxed text-slate-700">{{ $description }}</p>

                @if($notes !== 'لا توجد ملاحظات')
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <p class="mb-1 text-xs font-bold text-slate-400">ملاحظات إضافية:</p>
                        <p class="text-sm text-slate-600">{{ $notes }}</p>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-8 mt-10 mb-2">
                <div class="text-center">
                    <p class="mb-8 text-sm font-bold text-slate-500">توقيع العميل المستلم/الدافع</p>
                    <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
                </div>
                <div class="text-center">
                    <p class="mb-8 text-sm font-bold text-slate-500">توقيع الموظف المختص</p>
                    <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            {{-- بيانات الفاتورة --}}
            <p class="text-xs font-medium text-slate-300">
    تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
    {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}
</p>

            {{-- الخط الفاصل التسويقي لشركة تيار --}}
            <div class="pt-3 mt-3 border-t border-slate-700/50">
                <p class="text-[10px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr" class="font-mono text-slate-400">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection