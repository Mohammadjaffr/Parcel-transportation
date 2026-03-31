@extends('mobile.layouts.app')

@section('title', 'إدارة الأفراد')

@section('content')
<div class="flex flex-col gap-6">
    <div class="mb-2">
        <h1 class="text-2xl font-headline font-bold text-on-surface">إدارة الأفراد</h1>
        <p class="text-on-surface-variant text-sm">اختر الفئة التي تريد إدارتها</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        
        <a href="{{ route('drivers.index') }}" class="flex items-center gap-5 p-6 bg-surface-container-lowest rounded-3xl shadow-sm border border-slate-100 active:scale-95 transition-all group">
            <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">directions_car</span>
            </div>
            <div class="flex flex-col">
                <span class="font-headline font-bold text-lg text-on-surface">السائقين</span>
                <span class="text-xs text-on-surface-variant">إدارة بيانات ومستندات السائقين</span>
            </div>
            <span class="material-symbols-outlined mr-auto text-slate-300">chevron_left</span>
        </a>
        @if (Auth::user()->type != 'user')
        <a href="{{ route('users.index') }}" class="flex items-center gap-5 p-6 bg-surface-container-lowest rounded-3xl shadow-sm border border-slate-100 active:scale-95 transition-all group">
            <div class="w-14 h-14 bg-secondary-container/10 text-secondary rounded-2xl flex items-center justify-center group-hover:bg-secondary-container group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">badge</span>
            </div>
            <div class="flex flex-col">
                <span class="font-headline font-bold text-lg text-on-surface">المستخدمين</span>
                <span class="text-xs text-on-surface-variant">إدارة موظفي النظام والصلاحيات</span>
            </div>
            <span class="material-symbols-outlined mr-auto text-slate-300">chevron_left</span>
        </a>
        @endif
        <a href="{{ route('customers.index') }}" class="flex items-center gap-5 p-6 bg-surface-container-lowest rounded-3xl shadow-sm border border-slate-100 active:scale-95 transition-all group">
            <div class="w-14 h-14 bg-tertiary-fixed/30 text-tertiary rounded-2xl flex items-center justify-center group-hover:bg-tertiary group-hover:text-white transition-colors">
                <span class="material-symbols-outlined text-3xl">person_search</span>
            </div>
            <div class="flex flex-col">
                <span class="font-headline font-bold text-lg text-on-surface">العملاء</span>
                <span class="text-xs text-on-surface-variant">قائمة العملاء وبيانات الاتصال</span>
            </div>
            <span class="material-symbols-outlined mr-auto text-slate-300">chevron_left</span>
        </a>

    </div>
</div>
@endsection