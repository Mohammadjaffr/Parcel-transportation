@extends('layouts.app')

@section('title', 'الاستلام السريع عبر الباركود')
@section('Breadcrumb', 'إدارة الشحنات / الاستلام السريع بالباركود')

@section('content')
    <div class="space-y-6 max-w-7xl mx-auto font-body" dir="rtl">
        {{-- الهيدر العلوي للصفحة --}}
        <div class="flex flex-col gap-4 justify-between items-start p-5 bg-white rounded-2xl border border-slate-100 shadow-sm md:flex-row md:items-center dark:bg-boxdark dark:border-slate-800">
            <div class="flex items-center gap-4">
                <a href="{{ route('shipment.incoming.index') }}" 
                   class="flex justify-center items-center w-11 h-11 text-slate-500 rounded-xl border border-slate-100 shadow-sm transition-all bg-slate-50 dark:bg-slate-800 hover:text-[#fb6514] dark:border-slate-700 active:scale-90">
                    <span class="material-symbols-outlined text-[22px]">arrow_forward</span>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white font-headline">
                            الاستلام السريع عبر الباركود
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                            مستمر ولحظي
                        </span>
                    </div>
                    <p class="mt-1 text-xs md:text-sm font-medium text-slate-500 dark:text-slate-400">
                        قم بتوجيه قارئ الباركود نحو الشحنات ليتم تسجيلها في المستودع فوراً دون الحاجة للنقر أو تحديث الصفحة.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <a href="{{ route('shipmentpackage.incoming.index') }}" 
                   class="inline-flex items-center gap-2 px-4 h-11 text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-700 transition-all">
                    <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                    <span>الإرساليات الواردة</span>
                </a>
            </div>
        </div>

        {{-- تضمين مكون Livewire للاستلام المباشر --}}
        <livewire:scan-shipment />
    </div>
@endsection
