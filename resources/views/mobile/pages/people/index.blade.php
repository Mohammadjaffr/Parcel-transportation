@extends('mobile.layouts.app')

@section('title', 'إدارة الأفراد')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="mb-2">
            <h1 class="text-2xl font-bold font-headline text-on-surface">إدارة الأفراد</h1>
            <p class="text-sm text-on-surface-variant">اختر الفئة التي تريد إدارتها</p>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @hasservice('Drivers')
                <a href="{{ route('drivers.index') }}"
                    class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
                    <div
                        class="flex justify-center items-center w-14 h-14 rounded-2xl transition-colors bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white">
                        <span class="text-3xl material-symbols-outlined">directions_car</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold font-headline text-on-surface">السائقين</span>
                        <span class="text-xs text-on-surface-variant">إدارة بيانات ومستندات السائقين</span>
                    </div>
                    <span class="mr-auto material-symbols-outlined text-slate-300">chevron_left</span>
                </a>
            @endhasservice
            @hasservice('Users')
            @if (Auth::user()->type != 'user')
                <a href="{{ route('users.index') }}"
                    class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
                    <div
                        class="flex justify-center items-center w-14 h-14 rounded-2xl transition-colors bg-secondary-container/10 text-secondary group-hover:bg-secondary-container group-hover:text-white">
                        <span class="text-3xl material-symbols-outlined">badge</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-bold font-headline text-on-surface">المستخدمين</span>
                        <span class="text-xs text-on-surface-variant">إدارة موظفي النظام والصلاحيات</span>
                    </div>
                    <span class="mr-auto material-symbols-outlined text-slate-300">chevron_left</span>
                </a>
            @endif
            @endhasservice
            @hasservice('Customers')
            <a href="{{ route('customers.index') }}"
                class="flex gap-5 items-center p-6 rounded-3xl border shadow-sm transition-all bg-surface-container-lowest border-slate-100 active:scale-95 group">
                <div
                    class="flex justify-center items-center w-14 h-14 rounded-2xl transition-colors bg-tertiary-fixed/30 text-tertiary group-hover:bg-tertiary group-hover:text-white">
                    <span class="text-3xl material-symbols-outlined">person_search</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-bold font-headline text-on-surface">العملاء</span>
                    <span class="text-xs text-on-surface-variant">قائمة العملاء وبيانات الاتصال</span>
                </div>
                <span class="mr-auto material-symbols-outlined text-slate-300">chevron_left</span>
            </a>
            @endhasservice
           
        </div>
    </div>
@endsection
