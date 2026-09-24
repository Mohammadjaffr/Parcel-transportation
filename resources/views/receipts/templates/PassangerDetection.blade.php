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
    <div dir="rtl" class="w-full bg-white overflow-hidden print-no-shadow print:border-none print:my-0 print:rounded-none {{ !empty($is_pdf) ? 'max-w-none my-0 rounded-none shadow-none border-none' : 'max-w-7xl mx-auto sm:rounded-[1.5rem] shadow-2xl shadow-indigo-100/50 border border-slate-200 my-8' }}">

        {{-- الترويسة الرئيسية الأنيقة --}}
        <div class="relative px-6 py-4 border-b border-slate-200 bg-slate-50/50 print:bg-transparent">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500 opacity-[0.03] rounded-bl-full print:hidden"></div>

            <div class="flex relative z-10 justify-between items-center">
                
                {{-- يمين: بيانات الشركة --}}
                <div class="flex-1 text-right">
                    <h1 class="mb-2 text-2xl font-black tracking-tight text-slate-900">
                        {{ $company['name'] ?? 'شركة مرسال' }}
                    </h1>
                    <div class="inline-flex flex-col gap-1.5 text-xs font-bold text-slate-600">
                        <span class="flex gap-1.5 items-center px-2 py-1 rounded-md border bg-slate-100 border-slate-200">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                            {{ $company['main_branch']['title'] ?? 'المركز الرئيسي' }}
                        </span>
                    </div>
                </div>

                {{-- وسط: الشعار والبطاقة --}}
                <div class="flex flex-col justify-center items-center px-4 shrink-0">
                    @if (!empty($company['logo']))
                        <div class="flex justify-center items-center mb-3 w-28 h-28 bg-white rounded-3xl border-2 shadow-lg shadow-slate-200/50 border-slate-100 print:w-20 print:h-20 print:shadow-none print:border-slate-200 print:mb-1">
                            <img src="{{ $company['logo'] }}" alt="Logo" class="object-contain w-[80%] h-[80%]">
                        </div>
                    @endif
                    <div class="inline-flex justify-center items-center px-6 py-1.5 text-sm font-black text-indigo-900 bg-indigo-100 rounded-full border border-indigo-200 shadow-sm print-badge">
                        {{ $title ?? 'كشف ركاب مخصص للسائق' }}
                    </div>
                </div>

                {{-- يسار: بيانات الكشف --}}
                <div class="flex flex-col flex-1 gap-2 items-end text-left">
                    <div class="p-3 w-4/5 bg-white rounded-xl border shadow-sm border-slate-200 print:w-full print:shadow-none print:bg-transparent print:p-0 print:border-none">
                        <div class="flex justify-between items-center pb-1.5 mb-1.5 text-xs border-b border-dashed border-slate-200">
                            <span class="font-sans font-bold text-slate-800" dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                            <span class="font-semibold text-slate-500">تاريخ الطباعة</span>
                        </div>
                        <div class="flex justify-between items-center pb-1.5 mb-1.5 text-xs border-b border-dashed border-slate-200">
                            <span class="font-bold text-slate-800">{{ $total_passengers ?? 0 }}</span>
                            <span class="font-semibold text-slate-500">إجمالي الركاب</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-800">{{ count($drivers ?? []) }}</span>
                            <span class="font-semibold text-slate-500">عدد السائقين</span>
                        </div>
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
                    <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm print:rounded-none print:shadow-none print:border-t-2 print:border-b-2 print:border-l-0 print:border-r-0 print:border-slate-800">
                        <table class="w-full text-xs text-center bg-white divide-y divide-slate-200">
                            <thead class="bg-slate-50 print:bg-slate-100">
                                <tr>
                                    <th class="px-2 py-3 w-10 font-black border-l text-slate-500 border-slate-200">#</th>
                                    <th class="px-2 py-3 w-24 font-black border-l text-slate-500 border-slate-200">التاريخ</th>
                                    <th class="px-2 py-3 w-20 font-black border-l text-slate-500 border-slate-200">اليوم</th>
                                    <th class="px-2 py-3 w-28 font-black border-l text-slate-500 border-slate-200">رقم الراكب</th>
                                    <th class="px-2 py-3 w-16 font-black border-l text-slate-500 border-slate-200">العدد</th>
                                    <th class="px-2 py-3 font-black border-l text-slate-500 border-slate-200 w-[18%]">مكان الركوب</th>
                                    <th class="px-2 py-3 font-black border-l text-slate-500 border-slate-200 w-[18%]">الوجهة</th>
                                    <th class="px-2 py-3 font-black text-slate-500 w-[20%]">الملاحظات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 print:divide-slate-200">
                                @foreach($driver['passengers'] as $passenger)
                                    <tr class="transition-colors hover:bg-slate-50/50">
                                        <td class="px-2 py-2 font-black border-l text-slate-400 border-slate-100 print:border-slate-200">{{ $loop->iteration }}</td>
                                        <td class="px-2 py-2 font-sans font-bold border-l text-slate-700 border-slate-100 print:border-slate-200" dir="ltr">{{ $passenger['date'] }}</td>
                                        <td class="px-2 py-2 font-bold border-l text-indigo-600 border-slate-100 print:border-slate-200">{{ $passenger['day'] }}</td>
                                        <td class="px-2 py-2 font-sans font-bold border-l text-slate-800 border-slate-100 print:border-slate-200" dir="ltr">{{ $passenger['passenger_number'] }}</td>
                                        <td class="px-2 py-2 font-black border-l text-slate-800 bg-slate-50/50 border-slate-100 print:border-slate-200">{{ $passenger['count'] }}</td>
                                        
                                        {{-- تم استخدام كلاس text-wrap-custom لتوسيع الحقل والسماح بكسر النص --}}
                                        <td class="px-2 py-2 font-bold border-l text-slate-700 text-wrap-custom border-slate-100 print:border-slate-200">{{ $passenger['pickup_location'] }}</td>
                                        <td class="px-2 py-2 font-bold border-l text-slate-700 text-wrap-custom border-slate-100 print:border-slate-200">{{ $passenger['destination'] }}</td>
                                        <td class="px-2 py-2 text-xs font-medium text-slate-600 text-wrap-custom">{{ $passenger['note'] }}</td>
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
                <div class="mt-6 w-full print:mt-4">
                    <div class="overflow-hidden rounded-xl border shadow-sm border-slate-200 print:rounded-none print:shadow-none print:border-t-2 print:border-b-2 print:border-l-0 print:border-r-0 print:border-slate-800">
                        <div class="grid grid-cols-2 text-center divide-x divide-x-reverse divide-slate-200">
                            
                            <div class="p-3 bg-slate-50 print:bg-slate-100">
                                <p class="mb-1 text-xs font-bold uppercase text-slate-500">إجمالي الركاب</p>
                                <p class="text-xl font-black text-slate-800">{{ $total_passengers ?? 0 }}</p>
                            </div>
                          
                            <div class="p-3 bg-slate-50 print:bg-slate-100">
                                <p class="mb-1 text-xs font-bold uppercase text-slate-500">إجمالي العمولات</p>
                                <p class="text-xl font-black text-emerald-600">{{ number_format($totalCommissionall ?? 0, 0) }} ر.ي</p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- منطقة التواقيع --}}
        @if(!empty($drivers))
            <div class="w-full border-t-2 border-slate-900 print:border-black"></div>
            <div class="flex justify-between items-start px-8 mt-4 mb-8 text-sm font-bold text-slate-500 print:px-4">
                <div class="flex flex-col items-center text-center">
                    <span class="mb-6 font-black text-slate-700 print:text-black">توقيع السائق</span>
                    <span class="w-32 border-b-2 border-dashed border-slate-300 print:border-slate-400"></span>
                </div>
                <div class="flex flex-col items-center text-center">
                    <span class="mb-6 font-black text-slate-700 print:text-black">ختم المكتب</span>
                    <span class="w-32 border-b-2 border-dashed border-slate-300 print:border-slate-400"></span>
                </div>
                <div class="flex flex-col items-center text-center">
                    <span class="mb-6 font-black text-slate-700 print:text-black">الموظف المختص</span>
                    <span class="w-32 border-b-2 border-dashed border-slate-300 print:border-slate-400"></span>
                </div>
            </div>
        @endif

        <div class="p-4 text-center bg-slate-800 sm:rounded-b-[1.5rem] print:hidden">
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