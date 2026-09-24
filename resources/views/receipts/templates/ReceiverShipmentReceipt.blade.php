@extends('receipts.layout')

@section('title', 'سند تسليم طرد (المستلم) - ' . ($bond_number ?? ''))

@section('content')
    <style>
        /* إعدادات الطباعة المخصصة لـ A5 بالعرض (Sticker/Landscape) */
        @media print {
            @page {
                size: A5 landscape;
                margin: 0;
                /* إلغاء هوامش المتصفح لمنع طباعة الرابط والتاريخ */
            }

            body {
                background: #fff;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                margin: 0;
            }

            .print-no-shadow {
                box-shadow: none !important;
            }

            .sticker-container {
                width: 210mm !important;
                height: 148mm !important;
                max-width: 210mm !important;
                max-height: 148mm !important;
                overflow: hidden !important;
                margin: 0 !important;
                border: none !important;
                border-radius: 0 !important;
            }
        }

        /* حاوية الملصق في وضع الشاشة العادي */
        .sticker-wrapper {
            width: 100%;
            overflow-x: auto;
            display: flex;
            justify-content: center;
        }

        .sticker-container {
            width: 210mm !important;
            min-width: 210mm !important;
            height: 148mm !important;
            min-height: 148mm !important;
            margin: 0 auto;
            background: #ffffff;
            font-family: 'Tajawal', sans-serif;
            display: flex;
            flex-direction: column;
            transform-origin: top center;
        }

        /* تصغير الملصق في شاشات الجوال لكي يظهر بالكامل بدون تمرير */
        @media (max-width: 768px) {
            .sticker-container {
                transform: scale(0.45);
                margin-bottom: -80mm;
                /* تعويض الفراغ الناتج عن التصغير */
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .sticker-container {
                transform: scale(0.7);
                margin-bottom: -40mm;
            }
        }
    </style>

    <div class="sticker-wrapper print:block">
        <div
            class="sticker-container my-4 overflow-hidden border border-slate-200 shadow-xl rounded-[2rem] print-no-shadow print:my-0 print:border-0 print:rounded-none print:shadow-none">

            {{-- 1. الترويسة العلوية (رسمية) --}}
            <div
                class="flex justify-between items-center p-4 pb-3 bg-white border-b-2 shrink-0 border-slate-100 print:border-slate-300">

                {{-- بيانات الشركة والفرع (يمين) --}}
                <div class="flex gap-3 items-center w-1/3 min-w-0">
                    @php
                        $logo_path = !empty($company['logo'])
                            ? $company['logo']
                            : public_path('assets/image/icon_without_bg.png');
                    @endphp
                    <div
                        class="flex justify-center items-center p-1.5 w-16 h-16 bg-white rounded-xl border border-slate-200 shrink-0 print-no-shadow">
                        <img src="{{ $logo_path }}" alt="Logo" class="object-contain w-full h-full">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-[13px] font-black tracking-tight leading-tight text-slate-900 truncate"
                            title="{{ $company['name'] ?? 'شركة النقل' }}">
                            {{ $company['name'] ?? 'شركة النقل' }}
                        </h1>
                        <p class="mt-1 text-[10px] font-bold text-slate-600 truncate">
                            الفرع: {{ $user_branch ?? 'المركز الرئيسي' }}
                        </p>
                        <p class="text-[9px] font-medium text-slate-400 mt-0.5 truncate" dir="rtl">
                            هاتف: <span class="font-sans">{{ $company['main_branch']['phones'] ?? '---' }}</span>
                        </p>
                    </div>
                </div>

                {{-- عنوان السند (وسط) --}}
                <div class="flex flex-col justify-center items-center w-1/3 shrink-0">
                    <div
                        class="inline-flex flex-col justify-center items-center px-6 py-2 rounded-lg border-2 shadow-sm border-slate-800 bg-slate-50 print:bg-white print:border-black print-no-shadow">
                        <h2 class="text-[14px] font-black text-slate-900 tracking-wide"
                            title="{{ $title ?? 'سند تسليم طرد (نسخة المستلم)' }}">
                            {{ $title ?? 'سند تسليم طرد (نسخة المستلم)' }}
                        </h2>
                    </div>
                </div>

                {{-- الباركود ورقم السند (يسار) --}}
                <div class="flex flex-col items-end w-1/3 shrink-0">
                    <div
                        class="flex gap-3 items-center p-1.5 pr-3 bg-white rounded-xl border shadow-sm border-slate-200 print-no-shadow">
                        <div class="flex flex-col justify-center items-end min-w-0">
                            <span class="text-[8px] font-bold text-slate-400 mb-0.5 uppercase tracking-wider">رقم
                                السند</span>
                            <p class="text-[13px] font-mono font-black text-rose-600 tracking-widest truncate"
                                dir="ltr">
                                {{ $bond_number }}
                            </p>
                        </div>
                        <div class="flex justify-center items-center shrink-0">
                            {!! \Milon\Barcode\Facades\DNS2DFacade::getBarcodeSVG(
                                (string) $tracking_code,
                                'QRCODE',
                                2.5,
                                2.5,
                                'black',
                                false,
                            ) !!}
                        </div>
                    </div>
                </div>

            </div>

            {{-- 2. شريط التتبع السريع --}}
            <div class="flex justify-between items-center px-4 py-2 mx-4 my-1.5 rounded-2xl shrink-0 bg-slate-100/50">
                <div class="min-w-0">
                    <p class="text-[8px] font-bold text-slate-400">رقم السند</p>
                    <p class="font-mono text-xs font-black text-rose-600 truncate">{{ $bond_number ?? '---' }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-[8px] font-bold text-slate-400">تاريخ الإصدار</p>
                    <p class="text-[10px] font-bold text-slate-800 truncate">{{ $date ?? '' }}</p>
                </div>
                <div class="min-w-0">
                    <p class="text-[8px] font-bold text-slate-400">نوع الشحنة</p>
                    <p class="text-[10px] font-black text-slate-800 truncate" title="{{ $package_type ?? 'طرد عادي' }}">
                        {{ $package_type ?? 'طرد عادي' }}</p>
                </div>
                <div
                    class="px-2 py-1 bg-white rounded-lg border shadow-sm border-slate-100 min-w-0 max-w-[150px] print-no-shadow">
                    <p class="text-[8px] font-bold text-teal-600 truncate">جهة القدوم</p>
                    <p class="text-[11px] leading-tight font-black text-teal-700 line-clamp-2"
                        title="{{ $sender_branch ?? '---' }}">{{ $sender_branch ?? '---' }}</p>
                </div>
            </div>

            {{-- المنطقة الوسطى المرنة (تأخذ المساحة المتبقية فقط وتمنع التمدد) --}}
            <div class="flex overflow-hidden flex-col flex-1 px-4 pb-2 min-h-0">

                {{-- 3. البطاقات الذكية (المرسل والمستلم) --}}
                <div class="flex gap-4 mb-2 shrink-0">
                    {{-- بطاقة المرسل --}}
                    <div
                        class="overflow-hidden relative flex-1 p-2.5 bg-white rounded-2xl border border-emerald-100 shadow-sm print-no-shadow">
                        <div class="absolute top-0 right-0 w-1 h-full bg-emerald-500"></div>
                        <h3
                            class="flex items-center gap-1.5 text-[9px] font-black text-emerald-600 mb-1.5 bg-emerald-50 inline-block px-2 py-0.5 rounded-md">
                            المُرسل
                        </h3>
                        <div class="space-y-1">
                            <div class="min-w-0">
                                <p class="text-[8px] font-bold text-slate-400 mb-0.5">الاسم</p>
                                <p class="text-[11px] leading-tight font-black text-slate-900 line-clamp-2"
                                    title="{{ $sender_name ?? '---' }}">{{ $sender_name ?? '---' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] font-bold text-slate-400 mb-0.5">رقم الهاتف</p>
                                <p class="font-sans text-xs font-black truncate text-slate-800" dir="rtl"
                                    title="{{ $sender_phone ?? '---' }}">{{ $sender_phone ?? '---' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- أيقونة اتجاه الشحن --}}
                    <div class="flex justify-center items-center mt-5 shrink-0">
                        <div
                            class="flex justify-center items-center w-6 h-6 rounded-full border bg-slate-50 border-slate-100 text-slate-300 print-no-shadow">
                            <svg class="w-3 h-3 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- بطاقة المستلم --}}
                    <div
                        class="overflow-hidden relative flex-1 p-2.5 bg-white rounded-2xl border border-blue-100 shadow-sm print-no-shadow">
                        <div class="absolute top-0 right-0 w-1 h-full bg-blue-500"></div>
                        <h3
                            class="flex items-center gap-1.5 text-[9px] font-black text-blue-600 mb-1.5 bg-blue-50 inline-block px-2 py-0.5 rounded-md">
                            المُستلم
                        </h3>
                        <div class="space-y-1">
                            <div class="min-w-0">
                                <p class="text-[8px] font-bold text-slate-400 mb-0.5">الاسم</p>
                                <p class="text-[11px] leading-tight font-black text-slate-900 line-clamp-2"
                                    title="{{ $receiver_name ?? '---' }}">{{ $receiver_name ?? '---' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[8px] font-bold text-slate-400 mb-0.5">رقم الهاتف</p>
                                <p class="font-sans text-xs font-black truncate text-slate-800" dir="rtl"
                                    title="{{ $receiver_phone ?? '---' }}">{{ $receiver_phone ?? '---' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- 4. المالية والتفاصيل --}}
                <div class="flex flex-col gap-1.5 shrink-0">
                    {{-- تفاصيل الطرد --}}
                    <div
                        class="flex overflow-hidden flex-col p-2 w-full min-h-0 rounded-2xl border bg-slate-50 border-slate-100 print-no-shadow">
                        <p class="text-[9px] font-bold text-slate-400 border-b border-slate-200 pb-1 mb-1 shrink-0">محتوى
                            الشحنة
                        </p>
                        <div class="text-[9px] font-medium text-slate-700 leading-snug overflow-hidden flex flex-col">
                            <div class="flex flex-wrap gap-1 mb-1 shrink-0">
                                @if (!empty($weight))
                                    <span
                                        class="inline-block px-1.5 py-0.5 bg-white rounded border border-slate-200 print-no-shadow">الوزن:
                                        <span class="font-bold">{{ $weight }}</span></span>
                                @endif
                                @if (!empty($honey_details))
                                    <span
                                        class="inline-block px-1.5 py-0.5 text-amber-800 bg-amber-50 rounded border border-amber-200 print-no-shadow">العسل:
                                        <span class="font-bold">{{ $honey_details }}</span></span>
                                @endif
                            </div>
                            <div class="overflow-hidden flex-1 min-h-0 text-ellipsis">
                                <span class="text-slate-500 shrink-0">الوصف:</span>
                                <span class="font-bold line-clamp-3"
                                    title="{{ $notes ?? 'لا توجد' }}">{{ $notes ?? 'لا توجد' }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- المالية --}}
                    @php
                        $paymentColors = [
                            'prepaid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'cod' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'partial_payment' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'customer_credit' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ];
                        $paymentClass =
                            $paymentColors[$payment_key ?? 'prepaid'] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                    @endphp

                    <div
                        class="flex justify-between items-center p-2 w-full bg-white rounded-2xl border shadow-sm border-slate-100 print-no-shadow shrink-0">
                        <div class="min-w-0">
                            <p class="text-[8px] font-bold text-slate-400 mb-1">الدفع</p>
                            <span
                                class="inline-flex px-1.5 py-0.5 rounded-md text-[8px] font-black border {{ $paymentClass }} line-clamp-2 leading-tight max-w-[80px]"
                                title="{{ $payment_method ?? '---' }}">
                                {{ $payment_method ?? '---' }}
                            </span>
                        </div>

                        <div class="px-1 min-w-0 text-center">
                            <p class="text-[8px] font-bold text-slate-400 mb-0.5">الإجمالي</p>
                            <p class="font-sans text-[13px] font-black text-slate-800 truncate"
                                title="{{ $total_amount ?? 0 }}">{{ $total_amount ?? 0 }} <span
                                    class="text-[7px] font-normal text-slate-500">ر.ي</span></p>
                        </div>

                        <div class="min-w-0 text-left">
                            <p class="text-[8px] font-bold text-slate-400 mb-0.5">المتبقي</p>
                            @if (($payment_key ?? '') == 'customer_credit')
                                <p class="font-sans text-[13px] font-black text-rose-600 truncate"
                                    title="{{ $total_amount ?? 0 }}">{{ $total_amount ?? 0 }} <span
                                        class="text-[7px] font-normal text-slate-500">ر.ي</span></p>
                            @else
                                <p class="font-sans text-[13px] font-black text-rose-600 truncate"
                                    title="{{ $remaining_amount ?? 0 }}">{{ $remaining_amount ?? 0 }} <span
                                        class="text-[7px] font-normal text-slate-500">ر.ي</span></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. التذييل (نظيف ورسمي) --}}
            <div
                class="shrink-0 mt-auto border-t-2 border-slate-100 bg-white print:border-slate-300 rounded-b-[2rem] print:rounded-none overflow-hidden flex flex-col justify-end">

                @if (!empty($terms_and_conditions) && is_array($terms_and_conditions))
                    <div class="flex flex-col gap-1 px-5 py-2 border-b bg-slate-50/80 border-slate-100">
                        <h3 class="text-[8px] font-black text-slate-700 uppercase tracking-wider">الشروط والأحكام:</h3>
                        <div class="flex flex-wrap gap-x-4 gap-y-0.5 text-[8px] font-medium text-slate-500">
                            @foreach ($terms_and_conditions as $term)
                                <div class="flex gap-1 items-center">
                                    <span class="w-1 h-1 rounded-full bg-slate-300 shrink-0"></span>
                                    <span class="leading-tight break-words"
                                        title="{{ $term }}">{{ $term }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif



                {{-- بيانات النظام المطبوعة --}}
                <div
                    class="bg-slate-100 text-slate-500 p-1.5 px-5 flex justify-between items-center text-[7.5px] border-t border-slate-200/60">
                    <div class="truncate">
                        تم الإنشاء بواسطة: <span
                            class="font-bold text-slate-700">{{ $creator_name ?? 'مسؤول النظام' }}</span> |
                        وقت الطباعة: <span dir="ltr"
                            class="font-mono text-slate-600">{{ $print_date ?? now()->timezone('Asia/Aden')->format('Y-m-d h:i A') }}</span>
                    </div>
                    <div class="pl-2 shrink-0">
                        تطوير <span class="font-bold text-slate-700">شركة تيار</span> | النظام: <span
                            class="font-black text-slate-700">مُرسَل</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection