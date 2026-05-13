@extends('receipts.layout')

@section('title', 'سند تسليم طرد (المستلم) - ' . ($bond_number ?? ''))

@section('content')
<div class="max-w-2xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">
    
    <!-- Header -->
    <div class="bg-gradient-to-l from-teal-50/50 to-white p-6 sm:p-8 border-b border-slate-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5">
                <div class="flex items-center gap-4">
                    @if (!empty($company['logo']))
                        <div
                            class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center p-2 border border-slate-100">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="w-full h-full object-contain">
                        @else
                            <div
                                class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center p-2 border border-slate-100">
                                <img src="{{ public_path('assets/image/icon_without_bg.png') }}" alt="Logo"
                                    class="w-full h-full object-contain">
                            </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-800 tracking-tight">{{ $company['name'] ?? 'شركة مرسال' }}
                    </h1>
                    <p class="text-slate-500 font-medium text-xs mt-1">
                        {{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}</p>
                    <p class="text-slate-500 font-bold text-xs mt-1">رقم السند :{{ $bond_number ?? '---' }}</p>
                    <p class="text-slate-500 font-medium text-xs mt-1">رقم الفرع :{{ $sender_branch_phone ?? '---' }}</p>


                </div>
            </div>
            <div class="text-left">
                <div
                    class="inline-flex items-center px-3 py-1.5 bg-orange-50 text-orange-700 rounded-xl font-bold text-xs border border-orange-100">
                    {{ $title ?? 'سند استلام طرد' }}
                </div>
                <div class="text-slate-400 text-xs font-medium mt-1.5" dir="ltr">{{ $date ?? date('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="p-6 sm:p-8 space-y-6">
        <!-- Bond Number Badge -->
    

        <!-- Sender & Receiver -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Sender -->
            <div class="bg-slate-50/80 rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-200 flex items-center justify-center">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    </div>
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">المرسل</h3>
                </div>
                <div class="space-y-2">
                    <div><span class="text-xs text-slate-400">الاسم:</span> <span class="text-slate-800 font-bold text-sm">{{ $sender_name ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">الهاتف:</span> <span class="text-slate-800 font-bold text-sm" dir="ltr">{{ $sender_phone ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">الفرع:</span> <span class="text-slate-700 font-medium text-sm">{{ $sender_branch ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">المكتب:</span> <span class="text-slate-700 font-medium text-sm">{{ $sender_office ?? '---' }}</span></div>
                </div>
            </div>

            <!-- Receiver (Highlighted) -->
            <div class="bg-teal-50/50 rounded-2xl border-2 border-teal-200 p-5 ring-2 ring-teal-100/50">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                    </div>
                    <h3 class="text-xs font-bold text-teal-600 uppercase tracking-wider">المستلم</h3>
                </div>
                <div class="space-y-2">
                    <div><span class="text-xs text-slate-400">الاسم:</span> <span class="text-slate-800 font-bold text-sm">{{ $receiver_name ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">الهاتف:</span> <span class="text-slate-800 font-bold text-sm" dir="ltr">{{ $receiver_phone ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">الفرع:</span> <span class="text-slate-700 font-medium text-sm">{{ $receiver_branch ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">المكتب:</span> <span class="text-slate-700 font-medium text-sm">{{ $receiver_office ?? '---' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Package Details -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="text-sm font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-1.5 h-5 rounded-full bg-teal-500"></span>
                تفاصيل الطرد
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-400 font-bold mb-1">نوع الطرد</p>
                    <p class="text-slate-800 font-bold">{{ $package_type ?? '---' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold mb-1">الوزن</p>
                    <p class="text-slate-800 font-bold">{{ $weight ?? 'غير محدد' }}</p>
                </div>
                @if(!empty($honey_details))
                <div class="col-span-2">
                    <p class="text-xs text-slate-400 font-bold mb-1">تفاصيل العسل</p>
                    <p class="text-amber-700 font-bold">{{ $honey_details }}</p>
                </div>
                @endif
                @if(!empty($notes) && $notes !== 'لا توجد ملاحظات إضافية')
                <div class="col-span-2">
                    <p class="text-xs text-slate-400 font-bold mb-1">ملاحظات</p>
                    <p class="text-slate-600 text-sm">{{ $notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5">
            <h3 class="text-sm font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-1.5 h-5 rounded-full bg-emerald-500"></span>
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
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-xs text-slate-400 font-bold mb-1">طريقة الدفع</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold border {{ $paymentClass }}">
                        {{ $payment_method ?? '---' }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold mb-1">الإجمالي</p>
                    <p class="text-slate-800 font-black text-lg" dir="ltr">{{ $total_amount ?? 0 }} ر.ي</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold mb-1">المدفوع</p>
                    <p class="text-emerald-600 font-black text-lg" dir="ltr">{{ $partial_amount ?? 0 }} ر.ي</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-bold mb-1">المتبقي</p>
                    <p class="text-rose-600 font-black text-lg" dir="ltr">{{ $remaining_amount ?? 0 }} ر.ي</p>
                </div>
            </div>
        </div>

        <!-- Terms -->
        @if(!empty($terms_and_conditions) && is_array($terms_and_conditions))
        <div class="bg-amber-50/30 rounded-2xl border border-amber-100 p-5">
            <h3 class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-3">الشروط والأحكام</h3>
            <ul class="list-disc list-inside text-xs text-slate-500 space-y-1.5">
                @foreach($terms_and_conditions as $term)
                    <li>{{ $term }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 mt-10 mb-2">
            <div class="text-center">
                <p class="text-sm font-bold text-slate-500 mb-8">توقيع المستلم</p>
                <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-slate-500 mb-8">توقيع الموظف</p>
                <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
            </div>
        </div>
    </div>
    
    <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
        {{-- بيانات الفاتورة --}}
        <p class="text-xs font-medium text-slate-300">
            تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
            {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة:
            {{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}
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