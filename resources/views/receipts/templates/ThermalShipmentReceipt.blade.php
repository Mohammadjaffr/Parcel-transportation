@extends('receipts.layout')

@section('title', 'إيصال حراري - ' . ($bond_number ?? ''))

@push('styles')
<style>
    /* Thermal Receipt: Narrow width */
    @media print {
        @page { 
            size: 80mm auto;
            margin: 2mm;
        }
        body { padding: 0 !important; }
    }
    .thermal-receipt {
        max-width: 320px;
    }
</style>
@endpush

@section('content')
<div class="thermal-receipt w-full mx-auto bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0 print:rounded-none print:border-none print:shadow-none">
    
    <!-- Header -->
    <div class="text-center p-4 border-b border-dashed border-slate-300">
        @if(!empty($company['logo']))
        <div class="w-12 h-12 mx-auto rounded-xl bg-white shadow-sm flex items-center justify-center p-1 border border-slate-100 mb-2">
            <img src="{{ $company['logo'] }}" alt="Logo" class="w-full h-full object-contain">
        </div>
        @endif
        <h1 class="text-base font-black text-slate-800">{{ $company['name'] ?? 'مرسال' }}</h1>
        <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $company['main_branch']['title'] ?? '' }}</p>
        @if(!empty($company['main_branch']['phones']))
        <p class="text-xs text-slate-400 mt-1" dir="ltr">{{ $company['main_branch']['phones'] }}</p>
        @endif
    </div>

    <div class="p-4 space-y-3">
        <!-- Bond Info -->
        <div class="text-center bg-slate-800 text-white px-3 py-2 rounded-xl">
            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">رقم السند</p>
            <p class="text-lg font-black tracking-widest" dir="ltr">{{ $bond_number ?? '---' }}</p>
        </div>

        @if(isset($status) && $status === 'delivered')
        <div class="text-center">
            <span class="inline-block px-4 py-1.5 bg-green-100 text-green-700 font-black text-sm border-2 border-green-300 rounded-lg transform -rotate-2">تم التسليم</span>
        </div>
        @endif

        <div class="text-center text-xs text-slate-400" dir="ltr">{{ $date ?? date('Y-m-d H:i') }}</div>

        <!-- Divider -->
        <div class="border-t border-dashed border-slate-200"></div>

        <!-- Sender -->
        <div class="text-sm">
            <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">المرسل</p>
            <div class="flex justify-between">
                <span class="font-bold text-slate-800">{{ $sender_name ?? '---' }}</span>
                <span class="text-slate-500" dir="ltr">{{ $sender_phone ?? '---' }}</span>
            </div>
            <p class="text-xs text-slate-400">{{ $sender_branch ?? '' }}</p>
        </div>

        <div class="border-t border-dashed border-slate-200"></div>

        <!-- Receiver -->
       <!-- Receiver -->
        <div class="text-sm">
            <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">المستلم</p>
            <div class="flex justify-between">
                <span class="font-bold text-slate-800">{{ $receiver_name ?? '---' }}</span>
                <span class="text-slate-500" dir="ltr">{{ $receiver_phone ?? '---' }}</span>
            </div>
            
            <div class="mt-1 space-y-0.5">
                @if(!empty($receiver_office))
                    <p class="text-xs text-slate-500">المكتب: <span class="font-bold text-slate-700">{{ $receiver_office }}</span></p>
                @endif
              
                
                @if(!empty($receiver_branch))
                    <p class="text-xs text-slate-500">الفرع: <span class="font-bold text-slate-700">{{ $receiver_branch }}</span></p>
                @endif

                @if(empty($receiver_office) && empty($receiver_branch))
                    <p class="text-xs text-slate-400">الوجهة غير محددة</p>
                @endif
            </div>
        </div>

        <div class="border-t border-dashed border-slate-200"></div>

        <!-- Package -->
        <div class="text-sm space-y-1.5">
            <div class="flex justify-between">
                <span class="text-slate-500">نوع الطرد</span>
                <span class="font-bold text-slate-800">{{ $package_type ?? '---' }}</span>
            </div>
            @if(!empty($weight))
            <div class="flex justify-between">
                <span class="text-slate-500">الوزن</span>
                <span class="font-bold text-slate-800">{{ $weight }}</span>
            </div>
            @endif
            @if(!empty($honey_details))
            <div class="flex justify-between">
                <span class="text-slate-500">العسل</span>
                <span class="font-bold text-amber-700">{{ $honey_details }}</span>
            </div>
            @endif
        </div>

        <div class="border-t border-dashed border-slate-200"></div>

        <!-- Financial -->
        <div class="text-sm space-y-1.5">
            <div class="flex justify-between">
                <span class="text-slate-500">طريقة الدفع</span>
                <span class="font-bold text-slate-800">{{ $payment_method ?? '---' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">الإجمالي</span>
                <span class="font-black text-slate-800" dir="ltr">{{ $total_amount ?? 0 }} ر.ي</span>
            </div>
            @if(($payment_key ?? 'prepaid') === 'partial_payment')
            <div class="flex justify-between">
                <span class="text-slate-500">المدفوع</span>
                <span class="font-bold text-emerald-600" dir="ltr">{{ $partial_amount ?? 0 }} ر.ي</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">المتبقي</span>
                <span class="font-bold text-rose-600" dir="ltr">{{ $remaining_amount ?? 0 }} ر.ي</span>
            </div>
            @endif
        </div>

        @if(!empty($notes) && $notes !== 'لا توجد ملاحظات إضافية')
        <div class="border-t border-dashed border-slate-200"></div>
        <div class="text-xs text-slate-500">
            <p class="font-bold text-slate-600 mb-0.5">ملاحظات:</p>
            <p>{{ $notes }}</p>
        </div>
        @endif
    </div>
    
    <!-- Footer -->
   <div class="bg-slate-800 p-1 text-center rounded-b-[2rem]">
        {{-- بيانات الفاتورة --}}
     

        {{-- الخط الفاصل التسويقي لشركة تيار --}}
        <div class="mt-1 p-1 border-slate-700/50">
            <p class="text-[10px] font-bold text-slate-500">
                تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                <span class="mx-1">|</span>
                لطلب النظام: <span dir="ltr" class="text-slate-400 font-mono">{{ config('app.company_phone') }}</span>
            </p>
        </div>
    </div>
</div>
@endsection