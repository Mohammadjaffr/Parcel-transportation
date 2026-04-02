@extends('mobile.layouts.app')

@section('title', 'إدارة المكاتب')

@section('content')
<div class="flex flex-col gap-6">
    <div class="mb-2">
        <h1 class="text-2xl font-bold font-headline text-on-surface">إدارة المكاتب</h1>
        <p class="text-sm text-on-surface-variant">استعرض المكاتب حسب نوع التوثيق</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        
        <a href="" class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
            <div class="flex justify-center items-center w-14 h-14 rounded-2xl transition-colors bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white">
                <span class="text-3xl material-symbols-outlined">verified</span> </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold font-headline text-on-surface">مكاتب موثوقة</span>
                <span class="text-xs text-on-surface-variant">مكاتب مشتركة في النظام وتتلقى الإشعارات مباشرة</span>
            </div>
            <span class="mr-auto material-symbols-outlined text-slate-300">chevron_left</span>
        </a>

        <a href="{{ route('offices.unverified.index') }}" class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
            <div class="flex justify-center items-center w-14 h-14 rounded-2xl transition-colors bg-secondary-container/10 text-secondary group-hover:bg-secondary-container group-hover:text-white">
                <span class="text-3xl material-symbols-outlined">domain</span> </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold font-headline text-on-surface">مكاتب غير موثوقة</span>
                <span class="text-xs text-on-surface-variant">مكاتب مضافة يدوياً للشحن إليها (بدون حساب)</span>
            </div>
            <span class="mr-auto material-symbols-outlined text-slate-300">chevron_left</span>
        </a>

    </div>
</div>
@endsection