@extends('receipts.layout')

@section('title', 'سند إرسال طرد - ' . ($bond_number ?? ''))

@section('content')
    <style>
        @page {
            size: A5 landscape;
            margin: 0;
        }

        @media print {
            html,
            body {
                width: 210mm !important;
                height: 148mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                overflow: hidden !important;
            }

            .sticker-wrapper {
                width: 210mm !important;
                height: 148mm !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                display: block !important;
            }

            .sticker-container {
                width: 210mm !important;
                min-width: 210mm !important;
                height: 148mm !important;
                min-height: 148mm !important;
                max-height: 148mm !important;
                box-sizing: border-box !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                transform: none !important;
                display: flex !important;
                flex-direction: column !important;
                overflow: hidden !important;
            }

            .print-no-shadow {
                box-shadow: none !important;
            }

            .barcode-wrapper svg {
                width: 50px !important;
                height: 50px !important;
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
            width: 210mm;
            min-width: 210mm;
            height: 148mm;
            min-height: 148mm;
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
        <div class="sticker-container my-4 overflow-hidden border border-slate-200 shadow-xl rounded-[2rem] print-no-shadow print:my-0 print:border-0 print:rounded-none print:shadow-none">

            {{-- 1. الترويسة العلوية --}}
            <div class="flex justify-between items-start px-4 py-3 pb-2 bg-white border-b-2 shrink-0 border-slate-100 print:border-slate-300 ">

                {{-- بيانات الشركة والفروع --}}
                <div class="flex flex-col gap-1 w-1/3 min-w-0 ">
                    <h1 class="text-[14px] font-black tracking-tight leading-tight text-slate-900 truncate mb-0.5" title="{{ $company['name'] ?? 'شركة النقل' }}">
                        {{ $company['name'] ?? 'شركة النقل' }}
                    </h1>
                    
                    <div class="flex flex-col gap-1 border-r-2 border-rose-500 pr-2 ">
                        @if(!empty($company['main_branch']))
                            <div>
                                <p class="text-[10px] font-bold text-slate-800 truncate" title="{{ $company['main_branch']['title'] }}">
                                    {{ $company['main_branch']['title'] }}
                                </p>
                                <p class="text-[9px] font-medium text-slate-600 truncate mt-0.5" dir="rtl">
                                    هاتف: <span class="font-sans font-black text-slate-900 ">{{ $company['main_branch']['phones'] }}</span>
                                </p>
                            </div>
                        @else
                            <div>
                                <p class="text-[10px] font-bold text-slate-800 truncate">
                                    الفرع: {{ $sender_branch ?? 'المركز الرئيسي' }}
                                </p>
                                <p class="text-[9px] font-medium text-slate-600 truncate mt-0.5" dir="rtl">
                                    هاتف: <span class="font-sans font-black text-slate-900 ">{{ $sender_branch_phone ?? '---' }}</span>
                                </p>
                            </div>
                        @endif

                        @if(!empty($company['headquarters']) && ($company['main_branch']['title'] ?? '') !== $company['headquarters']['title'])
                            <div>
                                <p class="text-[9px] font-bold text-slate-600 truncate" title="{{ $company['headquarters']['title'] }}">
                                    {{ $company['headquarters']['title'] }}
                                </p>
                                <p class="text-[8px] font-medium text-slate-500 truncate mt-0.5" dir="rtl">
                                    هاتف: <span class="font-sans font-bold text-slate-800 ">{{ $company['headquarters']['phones'] }}</span>
                                </p>
                            </div>
                        @endif

                        @if(!empty($company['other_phones']))
                            <div>
                                <p class="text-[8px] font-bold text-slate-400">فروع أخرى:</p>
                                <p class="text-[7px] font-medium text-slate-500 line-clamp-2 leading-relaxed mt-0.5" dir="rtl">
                                    <span class="font-sans">{{ $company['other_phones'] }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- الشعار وعنوان السند --}}
                <div class="flex flex-col justify-start items-center w-1/3 shrink-0 gap-2 ">
                    @php
                        $logo_path = !empty($company['logo']) ? $company['logo'] : public_path('assets/image/icon_without_bg.png');
                    @endphp
                    <div class="flex justify-center items-center w-16 h-16 print-no-shadow">
                        <img src="{{ $logo_path }}" alt="Logo" class="object-contain w-full h-full drop-shadow-sm">
                    </div>
                    <div class="inline-flex flex-col justify-center items-center px-4 py-1 rounded-xl border-2 shadow-sm border-slate-800 bg-slate-50 print:bg-white print:border-black print-no-shadow">
                        <h2 class="text-[12px] font-black text-slate-900 tracking-wide" title="{{ $title ?? 'سند ارسال طرد' }}">
                            {{ $title ?? 'سند ارسال طرد' }}
                        </h2>
                    </div>
                </div>

                {{-- الباركود ومعلومات الشحنة --}}
                <div class="flex flex-col items-end justify-start w-1/3 shrink-0 gap-1.5 ">
                    <div class="flex flex-row-reverse gap-2 items-center p-1.5 bg-white rounded-xl border shadow-sm border-slate-200 print-no-shadow w-full justify-between">
                        <div class="flex flex-col justify-center items-center min-w-0 pr-2 border-r border-slate-200 flex-1">
                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">رقم السند</span>
                            <p class="text-[14px] font-mono font-black text-rose-600 tracking-widest truncate" dir="ltr">
                                {{ $bond_number }}
                            </p>
                        </div>
                        <div class="flex justify-center items-center shrink-0 pl-1.5 barcode-wrapper">
                            {!! \Milon\Barcode\Facades\DNS2DFacade::getBarcodeSVG((string) $tracking_code, 'QRCODE', 2.3, 2.3, 'black', false) !!}
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center w-full px-2 py-1 bg-slate-50 rounded-lg border border-slate-100 print-no-shadow">
                        <div class="flex flex-col items-center flex-1 border-r border-slate-200 px-1 ">
                            <span class="text-[7px] font-bold text-slate-400">الإصدار</span>
                            <span class="text-[8px] font-bold text-slate-800 truncate" dir="ltr">{{ $date ?? '' }}</span>
                        </div>
                        <div class="flex flex-col items-center flex-1 border-r border-slate-200 px-1 ">
                            <span class="text-[7px] font-bold text-slate-400">النوع</span>
                            <span class="text-[8px] font-black text-slate-800 truncate" title="{{ $package_type ?? 'طرد عادي' }}">{{ $package_type ?? 'طرد عادي' }}</span>
                        </div>
                        <div class="flex flex-col items-center flex-1 px-1 ">
                            <span class="text-[7px] font-bold text-teal-600">الوجهة</span>
                            <span class="text-[8.5px] font-black text-teal-700 truncate" title="{{ $receiver_branch ?? '---' }}">{{ $receiver_branch ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- المنطقة الوسطى المرنة --}}
            <div class="flex flex-col px-4 py-3 min-h-0 gap-3 print:block">

                {{-- 3. البطاقات الذكية (المرسل والمستلم) --}}
                <div class="flex gap-4 shrink-0 px-1 ">
                    {{-- بطاقة المرسل --}}
                    <div class="relative flex-1 p-2.5 bg-white rounded-xl border-2 border-emerald-100 shadow-sm print-no-shadow">
                        <div class="absolute top-0 right-0 w-1.5 h-full bg-emerald-500 rounded-r-xl"></div>
                        <h3 class="flex items-center gap-1.5 text-[10px] font-black text-emerald-700 mb-1.5 bg-emerald-50 inline-block px-2 py-0.5 rounded-md">
                            المُرسل
                        </h3>
                        <div class="space-y-1.5 ">
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-slate-400 mb-0.5 ">الاسم</p>
                                <p class="text-[12px] leading-tight font-black text-slate-900 line-clamp-2" title="{{ $sender_name ?? '---' }}">{{ $sender_name ?? '---' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-slate-400 mb-0.5 ">رقم الهاتف</p>
                                <p class="font-sans text-[13px] font-black truncate text-slate-800" dir="rtl" title="{{ $sender_phone ?? '---' }}">{{ $sender_phone ?? '---' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- أيقونة اتجاه الشحن --}}
                    <div class="flex justify-center items-center shrink-0">
                        <div class="flex justify-center items-center w-8 h-8 rounded-full border-2 bg-slate-50 border-slate-100 text-slate-400 print-no-shadow">
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- بطاقة المستلم --}}
                    <div class="relative flex-1 p-2.5 bg-white rounded-xl border-2 border-blue-100 shadow-sm print-no-shadow">
                        <div class="absolute top-0 right-0 w-1.5 h-full bg-blue-500 rounded-r-xl"></div>
                        <h3 class="flex items-center gap-1.5 text-[10px] font-black text-blue-700 mb-1.5 bg-blue-50 inline-block px-2 py-0.5 rounded-md">
                            المُستلم
                        </h3>
                        <div class="space-y-1.5 ">
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-slate-400 mb-0.5 ">الاسم</p>
                                <p class="text-[12px] leading-tight font-black text-slate-900 line-clamp-2" title="{{ $receiver_name ?? '---' }}">{{ $receiver_name ?? '---' }}</p>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] font-bold text-slate-400 mb-0.5 ">رقم الهاتف</p>
                                <p class="font-sans text-[13px] font-black truncate text-slate-800" dir="rtl" title="{{ $receiver_phone ?? '---' }}">{{ $receiver_phone ?? '---' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. تفاصيل الشحنة والمالية --}}
                <div class="flex bg-white rounded-xl border-2 border-slate-100 shadow-sm print-no-shadow mx-1 print:mx-1 mt-2 ">
                    
                    {{-- القسم الأيمن: محتوى الشحنة --}}
                    <div class="flex flex-col flex-1 p-2.5 ">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-1.5 print:pb-1 mb-2 ">
                            <h3 class="text-[10px] font-black text-slate-700 flex items-center gap-1 ">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                محتوى الشحنة
                            </h3>
                            @if (!empty($weight))
                                <span class="inline-flex px-1.5 py-0.5 text-[9px] font-bold text-slate-600 bg-slate-100 rounded print:rounded-md border border-slate-200">
                                    الوزن: {{ $weight }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex flex-col gap-2 flex-1">
                            @if (!empty($honey_details))
                                <div class="inline-flex px-2 py-1 text-[9px] font-bold text-amber-800 bg-amber-50 rounded-md border border-amber-200 w-fit">
                                    <span class="text-amber-600 ml-1 ">العسل:</span> {{ $honey_details }}
                                </div>
                            @endif
                            <div class="text-[10px] font-medium text-slate-600 bg-slate-50/50 p-1.5 rounded-md border border-slate-100/50 flex-1">
                                <span class="text-slate-400 font-bold ml-0.5 ">الوصف:</span>
                                <span class="leading-relaxed" title="{{ $notes ?? 'لا توجد' }}">{{ $notes ?? 'لا توجد' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- القسم الأيسر: المالية --}}
                    @php
                        $paymentColors = [
                            'prepaid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'cod' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'partial_payment' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'customer_credit' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ];
                        $paymentClass = $paymentColors[$payment_key ?? 'prepaid'] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                    @endphp
                    <div class="flex flex-col w-[190px] print:w-[35%] bg-slate-50 border-r-2 border-slate-100 p-2.5 ">
                        <div class="flex justify-between items-center border-b border-slate-200 pb-1.5 print:pb-1 mb-2 ">
                            <h3 class="text-[10px] font-black text-slate-700 flex items-center gap-1 ">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                المالية
                            </h3>
                            <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-black border {{ $paymentClass }} line-clamp-1">
                                {{ $payment_method ?? '---' }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-2 justify-center flex-1">
                            <div class="flex justify-between items-center">
                                <p class="text-[10px] font-bold text-slate-500">الإجمالي:</p>
                                <p class="font-sans text-[13px] font-black text-slate-800">{{ $total_amount ?? 0 }} <span class="text-[8px] font-normal text-slate-500">ر.ي</span></p>
                            </div>
                            <div class="flex justify-between items-center pt-1.5 border-t border-slate-200/60">
                                <p class="text-[10px] font-bold text-slate-500">المتبقي:</p>
                                @if (($payment_key ?? '') == 'customer_credit')
                                    <p class="font-sans text-[13px] font-black text-rose-600">{{ $total_amount ?? 0 }} <span class="text-[8px] font-normal text-slate-500">ر.ي</span></p>
                                @else
                                    <p class="font-sans text-[13px] font-black text-rose-600">{{ $remaining_amount ?? 0 }} <span class="text-[8px] font-normal text-slate-500">ر.ي</span></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. التذييل --}}
            <div class="mt-auto border-t-2 border-slate-100 bg-white print:border-slate-300 rounded-b-[2rem] print:rounded-none overflow-hidden print:block ">
                @if (!empty($terms_and_conditions) && is_array($terms_and_conditions))
                    <div class="flex flex-col gap-1.5 px-6 py-2 border-b bg-slate-50/80 border-slate-100">
                        <h3 class="text-[9px] font-black text-slate-700 uppercase tracking-wider">الشروط والأحكام:</h3>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-[8px] font-medium text-slate-600">
                            @foreach ($terms_and_conditions as $term)
                                <div class="flex gap-1.5 items-start">
                                    <span class="w-1 h-1 rounded-full bg-slate-400 shrink-0 mt-1 "></span>
                                    <span class="leading-tight break-words" title="{{ $term }}">{{ $term }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- بيانات النظام المطبوعة --}}
                <div class="bg-slate-100 text-slate-500 p-1.5 px-5 flex justify-between items-center text-[7.5px] border-t border-slate-200/60">
                    <div class="truncate">
                        تم الإنشاء بواسطة: <span class="font-bold text-slate-700">{{ $creator_name ?? 'مسؤول النظام' }}</span> |
                        وقت الطباعة: <span dir="ltr" class="font-mono text-slate-600">{{ $print_date ?? now()->timezone('Asia/Aden')->format('Y-m-d h:i A') }}</span>
                    </div>
                    <div class="pl-2 shrink-0">
                        تطوير <span class="font-bold text-slate-700">شركة تيار</span> | النظام: <span class="font-black text-slate-700">مُرسَل</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

