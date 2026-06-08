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
        <div class="bg-gradient-to-l from-indigo-50/50 to-white p-6 sm:p-8 border-b border-slate-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                <div class="flex items-center gap-4">
                    @if(!empty($company['logo']))
                        <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center p-2 border border-slate-100">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $company['name'] ?? 'شركة مرسال' }}</h1>
                        <p class="text-slate-500 font-medium text-sm mt-1">{{ $title }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center justify-center px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-sm mb-2 border border-indigo-100">
                        كشف ركاب مُخصص للسائق
                    </div>
                    <div class="text-slate-400 text-xs font-medium flex items-center gap-1.5 justify-end mt-1">
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
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-slate-50 border border-slate-200 rounded-t-2xl gap-4 border-b-0">
                  
                        <div class="flex gap-4 text-xs font-bold text-slate-600">
                            <span class="bg-white px-3 py-1.5 rounded-lg border border-slate-200">الركاب : <strong>{{ $driver['total_passengers_count'] }}</strong></span>
                            <span class="bg-white px-3 py-1.5 rounded-lg border border-slate-200">إجمالي الأشخاص: <strong>{{ $driver['total_count'] }}</strong></span>
                        </div>
                    </div>

                    {{-- Passengers Table (Driver Version) --}}
                    <div class="border border-slate-200 rounded-b-2xl overflow-x-auto">
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
                                        <td class="text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                                        <td class="text-center font-bold text-slate-700" dir="ltr">{{ $passenger['date'] }}</td>
                                        <td class="text-center font-bold text-indigo-600">{{ $passenger['day'] }}</td>
                                        <td class="text-center font-bold text-slate-800" dir="ltr">{{ $passenger['passenger_number'] }}</td>
                                        <td class="text-center font-black text-slate-800 bg-slate-50/50">{{ $passenger['count'] }}</td>
                                        
                                        {{-- تم استخدام كلاس text-wrap-custom لتوسيع الحقل والسماح بكسر النص --}}
                                        <td class="text-slate-700 font-bold text-wrap-custom">{{ $passenger['pickup_location'] }}</td>
                                        <td class="text-slate-700 font-bold text-wrap-custom">{{ $passenger['destination'] }}</td>
                                        <td class="text-slate-600 font-medium text-xs text-wrap-custom">{{ $passenger['note'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-12 border border-slate-200 rounded-2xl text-center text-slate-400 font-medium bg-slate-50/50">
                    لا توجد بيانات ركاب لعرضها.
                </div>
            @endforelse
        </div>

           <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? date('Y-m-d h:i A') }}
            </p>
            <div class="mt-3 pt-3 border-t border-slate-700/50">
                <p class="text-[10px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr" class="text-slate-400 font-mono">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection