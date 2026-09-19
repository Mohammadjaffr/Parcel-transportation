@extends('receipts.layout')

@section('title', 'سند إرسال طرد - ' . ($bond_number ?? ''))

@section('content')
    <style>
        /* إعدادات الطباعة المخصصة لـ A5 بالعرض (Sticker/Landscape) */
        @media print {
            @page {
                size: A5 landscape;
                margin: 0.2cm;
            }

            body {
                background: #fff;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .print-no-shadow {
                box-shadow: none !important;
            }
        }

        /* حاوية الملصق */
        .sticker-container {
            width: 100%;
            max-width: 210mm;
            min-height: 148mm;
            margin: 0 auto;
            background: #ffffff;
            font-family: 'Tajawal', sans-serif;
        }
    </style>

    <div class="flex flex-col my-4 overflow-hidden border border-slate-200 shadow-xl sticker-container rounded-[2rem] print-no-shadow print:my-0 print:border-0 print:rounded-none">

        {{-- 1. الترويسة العلوية --}}
        <div class="flex justify-between items-start p-5 pb-4 bg-slate-50/50">
            
            {{-- بيانات فرع الإرسال --}}
            <div class="w-1/3">
                <p class="text-[10px] font-bold text-slate-400 mb-1">محطة الإصدار</p>
                <div class="flex gap-2 items-center">
                    <div class="w-1 h-10 rounded-full bg-slate-300 shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-sm font-black truncate text-slate-800" title="{{ $sender_branch ?? '---' }}">{{ $sender_branch ?? '---' }}</h2>
                        <p class="text-[10px] font-bold text-slate-500 mt-0.5 truncate" dir="ltr">{{ $sender_branch_phone ?? '---' }}</p>
                        <p class="text-[10px] font-medium text-slate-400 truncate" title="{{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}">{{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}</p>
                    </div>
                </div>
            </div>

            {{-- الشعار والشركة --}}
            <div class="flex flex-col justify-center items-center w-1/3">
                @php
                    $logo_path = !empty($company['logo']) ? $company['logo'] : public_path('assets/image/icon_without_bg.png');
                @endphp
                <img src="{{ $logo_path }}" alt="Logo" class="object-contain mb-1.5 h-14 drop-shadow-sm">
                <span class="inline-flex items-center px-3 py-1 text-[10px] font-black text-orange-700 bg-orange-100 rounded-full">
                    {{ $title ?? 'سند استلام طرد (نسخة المرسل)' }}
                </span>
            </div>

            {{-- الباركود --}}
            {{-- ================= Barcode الحقيقي ================= --}}
<div class="flex flex-col items-end w-1/3">

    <div
        class="
            w-full max-w-[190px]
            p-2
            bg-white
            border border-slate-200
            rounded-xl
            text-center
        "
    >

        {{-- Barcode Code 128 --}}
        <div
            class="flex overflow-hidden justify-center items-center w-full"
            dir="ltr"
        >
            {!! DNS1D::getBarcodeSVG(
                $bond_number,
                'C128',
                1.8,
                38,
                'black',
                false
            ) !!}
        </div>

        {{-- الرقم المقروء بشرياً --}}
        <p
            class="
                mt-1.5
                text-[11px]
                font-mono
                font-black
                text-slate-900
                tracking-[0.12em]
            "
            dir="ltr"
        >
            {{ $bond_number }}
        </p>

    </div>

</div>
        </div>

        {{-- 2. شريط التتبع السريع (بدون حدود قاسية) --}}
        <div class="flex justify-between items-center px-5 py-3 mx-4 my-2 rounded-2xl bg-slate-100/50">
            <div>
                <p class="text-[9px] font-bold text-slate-400">رقم السند</p>
                <p class="font-mono text-sm font-black text-rose-600">{{ $bond_number ?? '---' }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold text-slate-400">تاريخ الإصدار</p>
                <p class="text-[11px] font-bold text-slate-800">{{ $date ?? '' }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold text-slate-400">نوع الشحنة</p>
                <p class="text-[11px] font-black text-slate-800">{{ $package_type ?? 'طرد عادي' }}</p>
            </div>
            <div class="px-3 py-1.5 bg-white rounded-lg border shadow-sm border-slate-100">
                <p class="text-[9px] font-bold text-teal-600">الوجهة النهائية</p>
                <p class="text-xs font-black text-teal-700">{{ $receiver_branch ?? '---' }}</p>
            </div>
        </div>

        {{-- 3. البطاقات الذكية (المرسل والمستلم) --}}
        <div class="flex gap-4 px-4 py-2">
            
            {{-- بطاقة المرسل --}}
            <div class="overflow-hidden relative flex-1 p-4 bg-white rounded-2xl border border-emerald-100 shadow-sm">
                <div class="absolute top-0 right-0 w-1 h-full bg-emerald-500"></div>
                <h3 class="flex items-center gap-1.5 text-[10px] font-black text-emerald-600 mb-3 bg-emerald-50 inline-block px-2 py-1 rounded-md">
                    المُرسل
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 mb-0.5">الاسم</p>
                        <p class="text-xs font-black text-slate-900">{{ $sender_name ?? '---' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 mb-0.5">رقم الهاتف</p>
                        <p class="text-[13px] font-sans font-black text-slate-800" dir="rtl">{{ $sender_phone ?? '---' }}</p>
                    </div>
                </div>
            </div>

            {{-- أيقونة اتجاه الشحن --}}
            <div class="flex justify-center items-center pt-8">
                <div class="flex justify-center items-center w-8 h-8 rounded-full border bg-slate-50 border-slate-100 text-slate-300">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
            </div>

            {{-- بطاقة المستلم --}}
            <div class="overflow-hidden relative flex-1 p-4 bg-white rounded-2xl border border-blue-100 shadow-sm">
                <div class="absolute top-0 right-0 w-1 h-full bg-blue-500"></div>
                <h3 class="flex items-center gap-1.5 text-[10px] font-black text-blue-600 mb-3 bg-blue-50 inline-block px-2 py-1 rounded-md">
                    المُستلم
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 mb-0.5">الاسم</p>
                        <p class="text-xs font-black text-slate-900">{{ $receiver_name ?? '---' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 mb-0.5">رقم الهاتف</p>
                        <p class="text-[13px] font-sans font-black text-slate-800" dir="rtl">{{ $receiver_phone ?? '---' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. المالية والتفاصيل (تصميم مدمج ونظيف) --}}
        <div class="flex gap-4 px-4 py-2 mt-2">
            {{-- تفاصيل الطرد --}}
            <div class="p-3 w-1/2 rounded-2xl border bg-slate-50 border-slate-100">
                <p class="text-[10px] font-bold text-slate-400 border-b border-slate-200 pb-1.5 mb-1.5">محتوى الشحنة</p>
                <div class="text-[10px] font-medium text-slate-700 leading-relaxed">
                    @if(!empty($weight))
                        <span class="inline-block px-1.5 py-0.5 mr-1 mb-1 bg-white rounded border border-slate-200">الوزن: <span class="font-bold">{{ $weight }}</span></span>
                    @endif
                    @if(!empty($honey_details))
                        <span class="inline-block px-1.5 py-0.5 mr-1 mb-1 text-amber-800 bg-amber-50 rounded border border-amber-200">العسل: <span class="font-bold">{{ $honey_details }}</span></span>
                    @endif
                    <div class="mt-1">
                        <span class="text-slate-500">الوصف:</span> <span class="font-bold">{{ $notes ?? 'لا توجد' }}</span>
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
                $paymentClass = $paymentColors[$payment_key ?? 'prepaid'] ?? 'bg-slate-50 text-slate-700 border-slate-200';
            @endphp
            
            <div class="flex justify-between items-center p-3 w-1/2 bg-white rounded-2xl border shadow-sm border-slate-100">
                <div>
                    <p class="text-[9px] font-bold text-slate-400 mb-1">الدفع</p>
                    <span class="inline-flex px-2 py-1 rounded-md text-[9px] font-black border {{ $paymentClass }}">
                        {{ $payment_method ?? '---' }}
                    </span>
                </div>
                
                <div class="text-center">
                    <p class="text-[9px] font-bold text-slate-400 mb-0.5">الإجمالي</p>
                    <p class="font-sans text-sm font-black text-slate-800">{{ $total_amount ?? 0 }} <span class="text-[8px] font-normal text-slate-500">ر.ي</span></p>
                </div>
                
                <div class="text-left">
                    <p class="text-[9px] font-bold text-slate-400 mb-0.5">المتبقي</p>
                    @if (($payment_key ?? '') == 'customer_credit')
                        <p class="font-sans text-sm font-black text-rose-600">{{ $total_amount ?? 0 }} <span class="text-[8px] font-normal text-slate-500">ر.ي</span></p>
                    @else
                        <p class="font-sans text-sm font-black text-rose-600">{{ $remaining_amount ?? 0 }} <span class="text-[8px] font-normal text-slate-500">ر.ي</span></p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 5. التذييل (نظيف ومدمج) --}}
        <div class="mt-auto bg-slate-900 rounded-b-[2rem] print:rounded-none overflow-hidden">
            @if (!empty($terms_and_conditions) && is_array($terms_and_conditions))
                <div class="px-5 py-2 flex items-center gap-3 text-[8px] font-medium text-slate-400 border-b border-slate-800 bg-slate-800/50">
                    <span class="font-bold text-slate-500 shrink-0">الشروط:</span>
                    <div class="flex gap-4">
                        @foreach ($terms_and_conditions as $term)
                            <span>- {{ $term }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

                  <div class="bg-slate-900 text-slate-300 p-1.5 px-3 flex justify-between items-center text-[8.5px]">
                <div>
                    تم الإنشاء بواسطة: <span class="font-bold text-white">{{ $creator_name ?? 'مسؤول النظام' }}</span> | وقت الطباعة: <span dir="ltr" class="font-mono">{{ $print_date ?? now()->timezone('Asia/Aden')->format('Y-m-d h:i A') }}</span>
                </div>
                <div>
                    تطوير <span class="font-bold text-white">شركة تيار</span> | النظام: <span class="font-black text-white">مُرسَل</span>
                </div>
            </div>
        </div>

    </div>
@endsection