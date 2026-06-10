@extends('receipts.layout')

@section('title', 'سند استلام طرد (المرسل) - ' . ($bond_number ?? ''))

@section('content')
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 0.6cm;
            }

            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-compact {
                padding: 1rem !important;
                gap: 0.75rem !important;
            }

            .print-grid-compact {
                gap: 0.5rem !important;
            }
        }
    </style>

    <div
        class="max-w-2xl w-full mx-auto bg-white rounded-[1.5rem] shadow-[0_4px_25px_rgb(0,0,0,0.03)] print-no-shadow overflow-hidden border border-slate-100 print:border-slate-200 my-6 print:my-0 print:rounded-none">

        <div
            class="p-5 bg-gradient-to-l from-orange-50/40 via-transparent to-transparent border-b border-slate-100 print-compact">
            <div class="flex items-start justify-between gap-4">

                <div class="flex items-start gap-3 min-w-0 flex-1">
                    @php
                        $logo_path = !empty($company['logo']) ? $company['logo'] : public_path('assets/image/icon_without_bg.png');
                    @endphp
                    <div
                        class="flex justify-center items-center p-1.5 w-16 h-16 bg-white rounded-xl border border-slate-100 shadow-sm shrink-0">
                        <img src="{{ $logo_path }}" alt="Logo" class="object-contain w-full h-full">
                    </div>

                    <div class="min-w-0 flex-1 pt-1">
                        <h1 class="text-base font-black tracking-tight text-slate-800 leading-tight">
                            {{ $company['name'] ?? 'شركة مرسل' }}
                        </h1>
                        <p class="mt-1 text-xs font-semibold text-slate-500 leading-relaxed">
                            {{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}
                        </p>
                        <p class="mt-1 text-[11px] font-medium text-slate-400">رقم الفرع: <span
                                class="font-sans text-slate-600">{{ $sender_branch_phone ?? '---' }}</span></p>
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2 shrink-0 pt-1">
                    <span
                        class="inline-flex items-center px-3 py-1 text-xs font-bold text-orange-700 bg-orange-50 rounded-lg border border-orange-100/70 shadow-sm">
                        {{ $title ?? 'سند استلام طرد' }}
                    </span>
                    <span
                        class="inline-flex items-center px-3 py-1 text-xs font-bold text-red-700 bg-red-50 rounded-lg border border-red-100 shadow-sm ">
                        رقم السند: {{ $bond_number ?? '---' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-5 space-y-4 print-compact">

            <div class="grid grid-cols-1 sm:grid-cols-2 print:grid-cols-2 gap-3 print-grid-compact">
                <div class="p-3.5 rounded-xl border border-emerald-100 bg-emerald-50/20 relative overflow-hidden">
                    <div
                        class="absolute top-0 left-0 bg-emerald-100/40 text-emerald-600 text-[9px] font-bold px-2 py-0.5 rounded-bl-lg">
                    </div>
                    <div class="flex gap-2 items-center mb-2 border-b border-emerald-100/50 pb-1.5">
                        <div class="flex justify-center items-center w-6 h-6 bg-emerald-100 rounded-md">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-black text-emerald-700">المرسل</h3>
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="flex gap-1">
                            <span class="text-slate-400">الاسم:</span>
                            <span class="font-bold text-slate-800">{{ $sender_name ?? '---' }}
                            </span>
                        </div>
                        <div class="flex gap-1"><span class="text-slate-400">الهاتف:</span> <span
                                class="font-bold font-sans text-slate-800" dir="ltr">{{ $sender_phone ?? '---' }}</span>
                        </div>
                        <div class="flex gap-1"><span class="text-slate-400">الفرع:</span> <span
                                class="font-medium text-slate-700">{{ $sender_branch ?? '---' }}</span></div>
                        <div class="flex gap-1"><span class="text-slate-400">المكتب:</span> <span
                                class="font-medium text-slate-600">{{ $sender_office ?? '---' }}</span></div>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl border border-blue-100 bg-blue-50/20 relative overflow-hidden">
                    <div
                        class="absolute top-0 left-0 bg-blue-100/40 text-blue-600 text-[9px] font-bold px-2 py-0.5 rounded-bl-lg">
                    </div>
                    <div class="flex gap-2 items-center mb-2 border-b border-blue-100/50 pb-1.5">
                        <div class="flex justify-center items-center w-6 h-6 bg-blue-100 rounded-md">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                        </div>
                        <h3 class="text-xs font-black text-blue-700">المستلم</h3>
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="flex gap-1">
                            <span class="text-slate-400">الاسم:</span>
                            <span class="font-bold text-slate-800">{{ $receiver_name ?? '---' }}</span>
                        </div>
                        <div class="flex gap-1">
                            <span class="text-slate-400">الهاتف:</span>
                            <span class="font-bold font-sans text-slate-800" dir="ltr">{{ $receiver_phone ?? '---' }}</span>
                        </div>
                        <div class="flex gap-1">
                            <span class="text-slate-400">الفرع:</span>
                            <span class="font-medium text-slate-700">{{ $receiver_branch ?? '---' }}</span>
                        </div>
                        <div class="flex gap-1">
                            <span class="text-slate-400">المكتب:</span>
                            <span class="font-medium text-slate-600">{{ $receiver_office ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 shadow-sm">
                <h3 class="flex gap-2 items-center mb-2.5 text-xs font-black text-slate-800">
                    <span class="w-1 h-3.5 bg-orange-500 rounded-full"></span>
                    تفاصيل الطرد
                </h3>
                <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-2">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400">نوع الطرد</p>
                        <p class="font-bold text-xs text-slate-800 mt-0.5">{{ $package_type ?? '---' }}</p>
                    </div>
                    @if(!empty($weight))
                        <div>
                            <p class="text-[11px] font-bold text-slate-400">الوزن</p>
                            <p class="font-bold text-xs text-slate-800 mt-0.5">{{ $weight }}</p>
                        </div>
                    @endif
                    @if (!empty($honey_details))
                        <div class="col-span-2 bg-amber-50/40 p-2 rounded-lg border border-amber-100/60">
                            <p class="text-[11px] font-bold text-amber-800">تفاصيل العسل</p>
                            <p class="font-bold text-xs text-amber-900 mt-0.5">{{ $honey_details }}</p>
                        </div>
                    @endif
                    @if (!empty($notes) && $notes !== 'لا توجد ملاحظات إضافية')
                        <div class="col-span-2 bg-slate-50 p-2 rounded-lg">
                            <p class="text-[11px] font-bold text-slate-400">ملاحظات</p>
                            <p class="text-xs text-slate-600 mt-0.5">{{ $notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="p-3.5 rounded-xl border bg-slate-50/50 border-slate-200/80">
                <h3 class="flex gap-2 items-center mb-2 text-xs font-black text-slate-800">
                    <span class="w-1 h-3.5 bg-emerald-500 rounded-full"></span>
                    البيانات المالية
                </h3>
                @php
                    $paymentColors = [
                        'prepaid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'cod' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'partial_payment' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'customer_credit' => 'bg-rose-50 text-rose-700 border-rose-200',
                    ];
                    $paymentClass = $paymentColors[$payment_key ?? 'prepaid'] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                @endphp
                <div class="grid grid-cols-4 gap-2 text-center border-t border-slate-200/60 pt-2.5">
                    <div>
                        <p class="mb-1 text-[11px] font-bold text-slate-400">طريقة الدفع</p>
                        <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $paymentClass }}">
                            {{ $payment_method ?? '---' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400">الإجمالي</p>
                        <p class="text-sm font-black text-slate-800 mt-0.5 font-sans">{{ $total_amount ?? 0 }} <span
                                class="text-[10px] font-normal text-slate-500">ر.ي</span></p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400">المدفوع</p>
                        @if ($payment_key == 'customer_credit')
                            <p class="text-sm font-black text-emerald-600 mt-0.5 font-sans">{{ $remaining_amount ?? 0 }} <span
                                    class="text-[10px] font-normal text-slate-500">ر.ي</span></p>
                        @else
                            <p class="text-sm font-black text-emerald-600 mt-0.5 font-sans">{{ $partial_amount ?? 0 }} <span
                                    class="text-[10px] font-normal text-slate-500">ر.ي</span></p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400">المتبقي</p>
                        @if ($payment_key == 'customer_credit')
                            <p class="text-sm font-black text-rose-600 mt-0.5 font-sans">{{ $total_amount ?? 0 }} <span
                                    class="text-[10px] font-normal text-slate-500">ر.ي</span></p>
                        @else
                            <p class="text-sm font-black text-rose-600 mt-0.5 font-sans">{{ $remaining_amount ?? 0 }} <span
                                    class="text-[10px] font-normal text-slate-500">ر.ي</span></p>
                        @endif
                    </div>
                </div>
            </div>

            @if (!empty($terms_and_conditions) && is_array($terms_and_conditions))
                <div class="p-3 rounded-xl border border-amber-100 bg-amber-50/10">
                    <h3 class="mb-1 text-[10px] font-black tracking-wider text-amber-800 uppercase">الشروط والأحكام</h3>
                    <ul
                        class="grid grid-cols-1 sm:grid-cols-2 print:grid-cols-2 gap-x-4 gap-y-0.5 text-[10px] list-disc list-inside text-slate-400 font-medium">
                        @foreach ($terms_and_conditions as $term)
                            <li class="truncate">{{ $term }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-6 mt-4 pt-2">
                <div class="text-center">
                    <p class="mb-4 text-[11px] font-bold text-slate-400">توقيع المرسل</p>
                    <div class="mx-auto w-2/3 border-b border-dashed border-slate-200"></div>
                </div>
                <div class="text-center">
                    <p class="mb-4 text-[11px] font-bold text-slate-400">توقيع الموظف</p>
                    <div class="mx-auto w-2/3 border-b border-dashed border-slate-200"></div>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 p-3 text-center rounded-b-[1.5rem] print:rounded-none">
            <p class="text-[10px] font-medium text-slate-400">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                <span class="text-slate-300">{{ $creator_name ?? 'مسؤول النظام' }}</span> | الطباعة:
                <span
                    class="text-slate-300 font-sans">{{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}</span>
            </p>

            <div class="pt-1.5 mt-1.5 border-t border-slate-800">
                <p class="text-[9px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr" class="font-mono text-slate-400">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>

    </div>
@endsection