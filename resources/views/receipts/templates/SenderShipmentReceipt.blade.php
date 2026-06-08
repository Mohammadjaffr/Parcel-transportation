@extends('receipts.layout')

@section('title', 'سند استلام طرد (المرسل) - ' . ($bond_number ?? ''))

@section('content')
    <div
        class="max-w-2xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        <!-- Header -->
        <div class="p-6 bg-gradient-to-l to-white border-b from-orange-50/50 sm:p-8 border-slate-100">
            <div class="flex flex-col gap-5 justify-between items-start sm:flex-row sm:items-center">
                <div class="flex gap-4 items-center">
                    @if (!empty($company['logo']))
                        <div
                            class="flex justify-center items-center p-2 w-14 h-14 bg-white rounded-2xl border shadow-sm border-slate-100">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-full h-full">
                        @else
                            <div
                                class="flex justify-center items-center p-2 w-14 h-14 bg-white rounded-2xl border shadow-sm border-slate-100">
                                <img src="{{ public_path('assets/image/icon_without_bg.png') }}" alt="Logo"
                                    class="object-contain w-full h-full">
                            </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-800">{{ $company['name'] ?? 'شركة مرسال' }}
                    </h1>
                    <p class="mt-1 text-xs font-medium text-slate-500">
                        {{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}</p>
                    <p class="mt-1 text-xs font-bold text-slate-500">رقم السند :{{ $bond_number ?? '---' }}</p>
                    <p class="mt-1 text-xs font-medium text-slate-500">رقم الفرع :{{ $sender_branch_phone ?? '---' }}</p>


                </div>
            </div>
            <div class="text-right">
                <div
                    class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-orange-700 bg-orange-50 rounded-xl border border-orange-100">
                    {{ $title ?? 'سند استلام طرد' }}
                </div>
                <div class="mt-1.5 text-xs font-medium text-slate-400" dir="ltr">{{ $date ?? date('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6 sm:p-8">
        <!-- Bond Number Badge -->


        <!-- Sender & Receiver -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Sender -->
            <div class="p-5 rounded-2xl border border-emerald-100 bg-emerald-50/50">
                <div class="flex gap-2 items-center mb-3">
                    <div class="flex justify-center items-center w-8 h-8 bg-emerald-100 rounded-lg">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold tracking-wider text-emerald-600 uppercase">المرسل</h3>
                </div>
                <div class="space-y-2">
                    <div><span class="text-xs text-slate-400">الاسم:</span> <span
                            class="text-sm font-bold text-slate-800">{{ $sender_name ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">الهاتف:</span> <span class="text-sm font-bold text-slate-800"
                            dir="ltr">{{ $sender_phone ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">الفرع:</span> <span
                            class="text-sm font-medium text-slate-700">{{ $sender_branch ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">المكتب:</span> <span
                            class="text-sm font-medium text-slate-700">{{ $sender_office ?? '---' }}</span></div>
                </div>
            </div>

            <!-- Receiver -->
            <div class="p-5 rounded-2xl border border-blue-100 bg-blue-50/50">
                <div class="flex gap-2 items-center mb-3">
                    <div class="flex justify-center items-center w-8 h-8 bg-blue-100 rounded-lg">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </div>
                    <h3 class="text-xs font-bold tracking-wider text-blue-600 uppercase">المستلم</h3>
                </div>
                <div class="space-y-2">
                    <div><span class="text-xs text-slate-400">الاسم:</span> <span
                            class="text-sm font-bold text-slate-800">{{ $receiver_name ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">الهاتف:</span> <span class="text-sm font-bold text-slate-800"
                            dir="ltr">{{ $receiver_phone ?? '---' }}</span>
                    </div>
                    <div><span class="text-xs text-slate-400">الفرع:</span> <span
                            class="text-sm font-medium text-slate-700">{{ $receiver_branch ?? '---' }}</span></div>
                    <div><span class="text-xs text-slate-400">المكتب:</span> <span
                            class="text-sm font-medium text-slate-700">{{ $receiver_office ?? '---' }}</span></div>
                </div>
            </div>
        </div>

        <!-- Package Details -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200">
            <h3 class="flex gap-2 items-center mb-4 text-sm font-black text-slate-800">
                <span class="w-1.5 h-5 bg-orange-500 rounded-full"></span>
                تفاصيل الطرد
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="mb-1 text-xs font-bold text-slate-400">نوع الطرد</p>
                    <p class="font-bold text-slate-800">{{ $package_type ?? '---' }}</p>
                </div>
                <div>
                    <p class="mb-1 text-xs font-bold text-slate-400">الوزن</p>
                    <p class="font-bold text-slate-800">{{ $weight ?? 'غير محدد' }}</p>
                </div>
                @if (!empty($honey_details))
                    <div class="col-span-2">
                        <p class="mb-1 text-xs font-bold text-slate-400">تفاصيل العسل</p>
                        <p class="font-bold text-amber-700">{{ $honey_details }}</p>
                    </div>
                @endif
                @if (!empty($notes) && $notes !== 'لا توجد ملاحظات إضافية')
                    <div class="col-span-2">
                        <p class="mb-1 text-xs font-bold text-slate-400">ملاحظات</p>
                        <p class="text-sm text-slate-600">{{ $notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="p-5 rounded-2xl border bg-slate-50 border-slate-200">
            <h3 class="flex gap-2 items-center mb-4 text-sm font-black text-slate-800">
                <span class="w-1.5 h-5 bg-emerald-500 rounded-full"></span>
                البيانات المالية
            </h3>
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
            <div class="grid grid-cols-2 gap-4 text-center sm:grid-cols-4">
                <div>
                    <p class="mb-1 text-xs font-bold text-slate-400">طريقة الدفع</p>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold border {{ $paymentClass }}">
                        {{ $payment_method ?? '---' }}
                    </span>
                </div>
                <div>
                    <p class="mb-1 text-xs font-bold text-slate-400">الإجمالي</p>
                    <p class="text-lg font-black text-slate-800" dir="ltr">{{ $total_amount ?? 0 }} ر.ي</p>
                </div>
                <div>
                    <p class="mb-1 text-xs font-bold text-slate-400">المدفوع</p>
                    @if ($payment_key == 'customer_credit')
                        <p class="text-lg font-black text-emerald-600" dir="ltr">{{ $remaining_amount ?? 0 }} ر.ي
                        </p>
                    @else
                        <p class="text-lg font-black text-emerald-600" dir="ltr">{{ $partial_amount ?? 0 }} ر.ي</p>
                    @endif

                </div>
                <div>

                    <p class="mb-1 text-xs font-bold text-slate-400">المتبقي</p>
                    @if ($payment_key == 'customer_credit')
                        <p class="text-lg font-black text-rose-600" dir="ltr">{{ $total_amount ?? 0 }} ر.ي</p>
                    @else
                        <p class="text-lg font-black text-rose-600" dir="ltr">{{ $remaining_amount ?? 0 }} ر.ي</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Terms -->
        @if (!empty($terms_and_conditions) && is_array($terms_and_conditions))
            <div class="p-5 rounded-2xl border border-amber-100 bg-amber-50/30">
                <h3 class="mb-3 text-xs font-bold tracking-wider text-amber-700 uppercase">الشروط والأحكام</h3>
                <ul class="space-y-1.5 text-xs list-disc list-inside text-slate-500">
                    @foreach ($terms_and_conditions as $term)
                        <li>{{ $term }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 mt-10 mb-2">
            <div class="text-center">
                <p class="mb-8 text-sm font-bold text-slate-500">توقيع المرسل</p>
                <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
            </div>
            <div class="text-center">
                <p class="mb-8 text-sm font-bold text-slate-500">توقيع الموظف</p>
                <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
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
