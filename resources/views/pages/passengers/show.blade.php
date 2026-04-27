@extends('layouts.app')

@section('title', 'تفاصيل الراكب')

@section('content')
    <div class="flex flex-col gap-6 p-4 pb-24 mx-auto max-w-7xl min-h-screen md:p-6 bg-surface dark:bg-boxdark-2 font-body"
        dir="rtl">

        {{-- ================= Header Section ================= --}}
        <div class="flex flex-col gap-4 justify-between items-start md:flex-row md:items-center">
            <div class="flex gap-4 items-center">
                <a href="{{ route('passengers.index') }}"
                    class="flex justify-center items-center w-10 h-10 text-gray-500 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark-2 dark:border-boxdark hover:text-primary hover:border-primary/30 active:scale-95">
                    <span class="text-[20px] material-symbols-outlined rtl:rotate-180">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-black tracking-tight md:text-3xl font-headline text-on-surface dark:text-white">
                        تفاصيل الراكب
                    </h1>
                    <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-bodydark">
                        رقم الراكب: <span class="font-bold text-primary">{{ $passenger->passenger_number }}</span>
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            {{-- <div class="flex gap-2 w-full md:w-auto">
                <button type="button" onclick="window.print()"
                    class="flex gap-2 justify-center items-center px-4 w-full h-12 text-sm font-bold text-gray-700 bg-white rounded-xl border border-gray-200 shadow-sm transition-all dark:bg-boxdark dark:text-gray-200 dark:border-boxdark-2 hover:bg-gray-50 dark:hover:bg-boxdark/80 active:scale-95 md:w-auto">
                    <span class="text-[20px] material-symbols-outlined">print</span>
                    <span class="hidden sm:inline">طباعة</span>
                </button>
            </div> --}}
        </div>

        {{-- ================= Details Content ================= --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            
            {{-- Main Info Card --}}
            <div class="lg:col-span-2">
                <div class="overflow-hidden bg-white rounded-[2rem] border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2">
                    <div class="flex items-center px-6 py-5 border-b border-gray-50 dark:border-boxdark-2 bg-surface/30 dark:bg-boxdark-2/30">
                        <div class="flex justify-center items-center ml-3 w-10 h-10 rounded-xl bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                            <span class="text-[20px] material-symbols-outlined">info</span>
                        </div>
                        <h2 class="text-lg font-black text-on-surface dark:text-white font-headline">البيانات الأساسية</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {{-- Date & Day --}}
                            <div class="flex gap-4 p-4 rounded-2xl bg-surface dark:bg-boxdark-2">
                                <div class="flex justify-center items-center w-12 h-12 bg-white rounded-xl shadow-sm dark:bg-boxdark shrink-0">
                                    <span class="text-gray-400 dark:text-gray-500 material-symbols-outlined">calendar_month</span>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <span class="mb-1 text-xs font-bold text-gray-500 dark:text-gray-400">التاريخ واليوم</span>
                                    <span class="text-sm font-black text-on-surface dark:text-white">{{ $passenger->date }} - {{ $passenger->day }}</span>
                                </div>
                            </div>

                            {{-- Location --}}
                            <div class="flex gap-4 p-4 rounded-2xl bg-surface dark:bg-boxdark-2">
                                <div class="flex justify-center items-center w-12 h-12 bg-white rounded-xl shadow-sm dark:bg-boxdark shrink-0">
                                    <span class="text-gray-400 dark:text-gray-500 material-symbols-outlined">location_on</span>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <span class="mb-1 text-xs font-bold text-gray-500 dark:text-gray-400">المكان</span>
                                    <span class="text-sm font-black text-on-surface dark:text-white">{{ $passenger->location }}</span>
                                </div>
                            </div>

                            {{-- Count --}}
                            <div class="flex gap-4 p-4 rounded-2xl bg-surface dark:bg-boxdark-2">
                                <div class="flex justify-center items-center w-12 h-12 bg-white rounded-xl shadow-sm dark:bg-boxdark shrink-0">
                                    <span class="text-gray-400 dark:text-gray-500 material-symbols-outlined">group</span>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <span class="mb-1 text-xs font-bold text-gray-500 dark:text-gray-400">العدد</span>
                                    <span class="text-sm font-black text-on-surface dark:text-white">{{ $passenger->count }} ركاب</span>
                                </div>
                            </div>

                            {{-- Total Commission --}}
                            <div class="flex gap-4 p-4 rounded-2xl bg-surface dark:bg-boxdark-2">
                                <div class="flex justify-center items-center w-12 h-12 bg-white rounded-xl shadow-sm dark:bg-boxdark shrink-0">
                                    <span class="text-gray-400 dark:text-gray-500 material-symbols-outlined">payments</span>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <span class="mb-1 text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي العمولة</span>
                                    <span class="text-sm font-black text-primary">{{ $passenger->total_commission }}</span>
                                </div>
                            </div>

                            {{-- Broker --}}
                            <div class="flex gap-4 p-4 rounded-2xl bg-surface dark:bg-boxdark-2 md:col-span-2">
                                <div class="flex justify-center items-center w-12 h-12 bg-white rounded-xl shadow-sm dark:bg-boxdark shrink-0">
                                    <span class="text-gray-400 dark:text-gray-500 material-symbols-outlined">handshake</span>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <span class="mb-1 text-xs font-bold text-gray-500 dark:text-gray-400">الوسيط</span>
                                    @if($passenger->broker)
                                        <span class="text-sm font-black text-on-surface dark:text-white">{{ $passenger->broker }}</span>
                                    @else
                                        <span class="text-sm font-bold text-gray-400 dark:text-gray-600">لا يوجد وسيط مسجل</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes Section --}}
                @if($passenger->note)
                <div class="mt-6 overflow-hidden bg-white rounded-[2rem] border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2">
                    <div class="flex items-center px-6 py-5 border-b border-gray-50 dark:border-boxdark-2 bg-surface/30 dark:bg-boxdark-2/30">
                        <div class="flex justify-center items-center ml-3 w-10 h-10 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10 shrink-0">
                            <span class="text-[20px] material-symbols-outlined">description</span>
                        </div>
                        <h2 class="text-lg font-black text-on-surface dark:text-white font-headline">ملاحظات</h2>
                    </div>
                    <div class="p-6">
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            {{ $passenger->note }}
                        </p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar Cards --}}
            <div class="flex flex-col gap-6">
                
                {{-- Driver Card --}}
                <div class="overflow-hidden bg-white rounded-[2rem] border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 relative">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-primary/40"></div>
                    <div class="flex items-center px-6 py-5 border-b border-gray-50 dark:border-boxdark-2">
                        <div class="flex justify-center items-center ml-3 w-10 h-10 rounded-xl bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                            <span class="text-[20px] material-symbols-outlined">local_taxi</span>
                        </div>
                        <h2 class="text-lg font-black text-on-surface dark:text-white font-headline">بيانات السائق</h2>
                    </div>
                    
                    <div class="p-6">
                        @if($passenger->driver)
                            <div class="flex flex-col items-center text-center">
                                <div class="flex justify-center items-center w-20 h-20 mb-4 bg-surface dark:bg-boxdark-2 rounded-full border-[3px] border-white dark:border-boxdark shadow-md">
                                    <span class="text-4xl text-gray-400 material-symbols-outlined dark:text-gray-500">person</span>
                                </div>
                                <h3 class="mb-1 text-lg font-black text-on-surface dark:text-white">{{ $passenger->driver->name }}</h3>
                                <div class="flex gap-1.5 items-center px-4 py-2 mt-2 text-gray-500 rounded-xl dark:text-gray-400 bg-surface dark:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[16px]">call</span>
                                    <span class="font-mono text-sm font-bold dir-ltr">{{ $passenger->driver->phone }}</span>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col justify-center items-center py-6 text-center">
                                <div class="flex justify-center items-center mb-4 w-16 h-16 bg-gray-50 rounded-full border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                    <span class="text-3xl text-gray-300 dark:text-gray-600 material-symbols-outlined">person_off</span>
                                </div>
                                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400">لا يوجد سائق مرتبط</h3>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Status / Meta Card --}}
                <div class="overflow-hidden bg-white rounded-[2rem] border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2">
                    <div class="flex items-center px-6 py-5 border-b border-gray-50 dark:border-boxdark-2">
                        <div class="flex justify-center items-center ml-3 w-10 h-10 text-gray-500 rounded-xl bg-surface dark:bg-boxdark-2 shrink-0">
                            <span class="text-[20px] material-symbols-outlined">schedule</span>
                        </div>
                        <h2 class="text-lg font-black text-on-surface dark:text-white font-headline">معلومات السجل</h2>
                    </div>
                    
                    <div class="flex flex-col gap-4 p-6">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">تاريخ الإضافة</span>
                            <span class="text-sm font-bold text-on-surface dark:text-white">{{ $passenger->created_at ? $passenger->created_at->format('Y-m-d h:i A') : 'غير متوفر' }}</span>
                        </div>
                        <div class="w-full h-px bg-gray-50 dark:bg-boxdark-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400">آخر تحديث</span>
                            <span class="text-sm font-bold text-on-surface dark:text-white">{{ $passenger->updated_at ? $passenger->updated_at->format('Y-m-d h:i A') : 'غير متوفر' }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
            }
            .bg-surface, .dark\:bg-boxdark-2, .dark\:bg-boxdark, .bg-white {
                background: white !important;
                border-color: #f3f4f6 !important;
            }
            .text-on-surface, .dark\:text-white {
                color: black !important;
            }
            button, a {
                display: none !important;
            }
            .shadow-sm, .shadow-md {
                box-shadow: none !important;
            }
        }
    </style>
@endsection
