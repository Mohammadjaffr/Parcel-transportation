@extends('mobile.layouts.app')

@section('title', 'الصندوق المالي')

@section('content')
<div class="flex flex-col gap-6 px-2" dir="rtl">
    <div class="px-2 mt-4 mb-2">
        <h1 class="text-2xl font-bold font-headline text-on-surface">الصندوق المالي</h1>
        <p class="mt-1 text-sm text-on-surface-variant">اختر نوع السجلات التي تريد عرضها</p>
    </div>

    <div class="grid grid-cols-1 gap-4 px-2">
        <a href="{{ route('cash.ledger.index') }}" class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
            <div class="flex justify-center items-center w-14 h-14 text-emerald-600 rounded-2xl transition-colors bg-emerald-500/10 group-hover:bg-emerald-500 group-hover:text-white">
                <span class="text-3xl material-symbols-outlined">receipt_long</span>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold font-headline text-on-surface">دفتر الحسابات</span>
                <span class="mt-1 text-xs text-on-surface-variant">متابعة الوارد والمنصرف من الصندوق</span>
            </div>
            <span class="mr-auto transition-transform material-symbols-outlined text-slate-300 group-hover:-translate-x-1">chevron_left</span>
        </a>

        <a href="{{ route('cash-categories.index') }}" class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
            <div class="flex justify-center items-center w-14 h-14 text-indigo-600 rounded-2xl transition-colors bg-indigo-500/10 group-hover:bg-indigo-500 group-hover:text-white">
                <span class="text-3xl material-symbols-outlined">category</span>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold font-headline text-on-surface">فئات الحسابات</span>
                <span class="mt-1 text-xs text-on-surface-variant">إدارة وتصنيف بنود الإيرادات والمصروفات</span>
            </div>
            <span class="mr-auto transition-transform material-symbols-outlined text-slate-300 group-hover:-translate-x-1">chevron_left</span>
        </a>
    </div>
    
</div>
@endsection
