@extends('receipts.layout')

@section('title', $title)

@push('styles')
    <style>
        @media print {
            @page {
                size: A4 landscape; /* يفضل landscape عشان يعطي مساحة أكبر للمكان والملاحظات */
            }
        }
        /* كلاسات مخصصة لكسر النصوص الطويلة داخل الجدول */
        .text-wrap-custom {
            white-space: normal !important;
            word-wrap: break-word !important;
            word-break: break-word !important;
            line-height: 1.6;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-6xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        {{-- Header --}}
        <div class="p-6 bg-gradient-to-l to-white border-b from-indigo-50/50 sm:p-8 border-slate-100">
            <div class="flex flex-col gap-6 justify-between items-start sm:flex-row sm:items-center">
                <div class="flex gap-4 items-center">
                    @if(!empty($company['logo']))
                        <div class="flex justify-center items-center p-2 w-16 h-16 bg-white rounded-2xl border shadow-sm border-slate-100">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-full h-full">
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-slate-800">{{ $company['name'] ?? 'شركة مرسال' }}</h1>
                        <p class="mt-1 text-sm font-medium text-slate-500">{{ $title }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="inline-flex justify-center items-center px-4 py-2 mb-2 text-sm font-bold text-indigo-700 bg-indigo-50 rounded-xl border border-indigo-100">
                        كشف ركاب مُخصص للسائق
                    </div>
                    <div class="flex gap-1.5 justify-end items-center mt-1 text-xs font-medium text-slate-400">
                        <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            {{-- Grouped by Drivers --}}
            @forelse($drivers ?? [] as $driver)
                <div class="mb-10 page-break-inside-avoid">
                    {{-- Driver Card Header --}}
                    <div class="flex flex-col gap-4 justify-between items-start p-4 rounded-t-2xl border border-b-0 sm:flex-row sm:items-center bg-slate-50 border-slate-200">
                  
                        <div class="flex gap-4 text-xs font-bold text-slate-600">
                            <span class="px-3 py-1.5 bg-white rounded-lg border border-slate-200">الركاب : <strong>{{ $driver['total_passengers_count'] }}</strong></span>
                            <span class="px-3 py-1.5 bg-white rounded-lg border border-slate-200">إجمالي الأشخاص: <strong>{{ $driver['total_count'] }}</strong></span>
                        </div>
                    </div>

                    {{-- Passengers Table (Driver Version) --}}
                    <div class="overflow-x-auto rounded-b-2xl border border-slate-200">
                        <table class="w-full text-sm text-right premium-table">
                            <thead>
                                <tr>
                                    <th class="w-10 text-center">#</th>
                                    <th class="w-24 text-center">التاريخ</th>
                                    <th class="w-20 text-center">اليوم</th>
                                    <th class="w-28 text-center">رقم الراكب</th>
                                    <th class="w-16 text-center">العدد</th>
                                    <th class="w-[18%] text-right">مكان الركوب</th>
                                    <th class="w-[18%] text-right">الوجهة</th>
                                    <th class="w-[20%] text-right">الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($driver['passengers'] as $passenger)
                                    <tr class="transition-colors hover:bg-slate-50">
                                        <td class="font-bold text-center text-slate-400">{{ $loop->iteration }}</td>
                                        <td class="font-bold text-center text-slate-700" dir="ltr">{{ $passenger['date'] }}</td>
                                        <td class="font-bold text-center text-indigo-600">{{ $passenger['day'] }}</td>
                                        <td class="font-bold text-center text-slate-800" dir="ltr">{{ $passenger['passenger_number'] }}</td>
                                        <td class="font-black text-center text-slate-800 bg-slate-50/50">{{ $passenger['count'] }}</td>
                                        
                                        {{-- تم استخدام كلاس text-wrap-custom لتوسيع الحقل والسماح بكسر النص --}}
                                        <td class="font-bold text-slate-700 text-wrap-custom">{{ $passenger['pickup_location'] }}</td>
                                        <td class="font-bold text-slate-700 text-wrap-custom">{{ $passenger['destination'] }}</td>
                                        <td class="text-xs font-medium text-slate-600 text-wrap-custom">{{ $passenger['note'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-12 font-medium text-center rounded-2xl border border-slate-200 text-slate-400 bg-slate-50/50">
                    لا توجد بيانات ركاب لعرضها.
                </div>
            @endforelse
            @if(!empty($drivers))
                <div class="overflow-hidden mb-8 rounded-2xl border border-slate-200">
                    <div class="grid grid-cols-2 gap-4 p-4 text-center divide-y sm:divide-y-0 sm:divide-x sm:divide-x-reverse bg-slate-50 sm:grid-cols-2 divide-slate-200">
                        
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي الركاب</p>
                            <p class="text-lg font-black text-slate-800">{{ $total_passengers ?? 0 }}</p>
                        </div>
                      
                       

                     <div>
    <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي العمولات</p>
    <p class="text-lg font-black text-slate-800">{{ number_format($totalCommissionall ?? 0, 0) }} ر.ي</p>
</div>
                        
                    </div>
                </div>
            @endif
        </div>

           <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? date('Y-m-d h:i A') }}
            </p>
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