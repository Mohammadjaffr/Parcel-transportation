@extends('mobile.layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
    <div class="flex flex-col gap-6 pb-24">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-headline font-bold text-on-surface">إدارة المستخدمين</h1>
                <p class="text-on-surface-variant text-sm">تنظيم صلاحيات وحسابات النظام</p>
            </div>

            <a href="#"
                class="flex items-center gap-1.5 px-4 py-2.5 bg-primary text-white rounded-2xl hover:bg-primary-hover active:scale-95 transition-all text-sm font-headline font-bold shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-xl">person_add</span>
                إضافة
            </a>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div
                class="bg-surface-container-lowest border border-slate-100 rounded-2xl p-3 flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-primary mb-1 text-[22px]">group</span>
                <span class="text-xl font-bold font-headline text-on-surface">{{ $users->count() }}</span>
                <span class="text-[10px] font-headline text-slate-500 font-medium">المستخدمين</span>
            </div>

            <div
                class="bg-surface-container-lowest border border-slate-100 rounded-2xl p-3 flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-emerald-500 mb-1 text-[22px]"
                    style="font-variation-settings: 'FILL' 1;">bolt</span>
                <span class="text-xl font-bold font-headline text-emerald-600">1</span>
                <span class="text-[10px] font-headline text-slate-500 font-medium">نشط حالياً</span>
            </div>

            <div
                class="bg-surface-container-lowest border border-slate-100 rounded-2xl p-3 flex flex-col items-center justify-center text-center">
                <span class="material-symbols-outlined text-red-500 mb-1 text-[22px]">block</span>
                <span class="text-xl font-bold font-headline text-red-600">0</span>
                <span class="text-[10px] font-headline text-slate-500 font-medium">محظورة</span>
            </div>
        </div>

        <div class="relative w-full">
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" placeholder="ابحث بالاسم أو رقم الهاتف..."
                class="w-full h-12 pr-12 pl-4 bg-surface-container-low border border-slate-100 rounded-xl text-sm font-headline focus:outline-none focus:border-primary focus:bg-white transition-all">
        </div>

        <div class="flex flex-col gap-4">
            @forelse ($users as $user)
                <div
                    class="bg-surface-container-lowest border border-slate-100/70 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all group relative">

                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-primary-container text-primary rounded-2xl flex items-center justify-center font-headline font-bold text-lg">
                                @php
                                    $words = explode(' ', $user->name);
                                    $first_char = mb_substr($words[0], 0, 1, 'utf-8');
                                    $second_char = isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '';
                                    echo $first_char . ' ' . $second_char;
                                @endphp
                            </div>

                            <div class="flex flex-col">
                                <span
                                    class="font-headline font-bold text-on-surface text-base group-hover:text-primary transition-colors">
                                    {{ $user->name }}
                                </span>
                                <span class="text-xs text-slate-500 font-bold tracking-wider mt-0.5"><x-phone-number :value="$user->phone" /></span>
                            </div>
                        </div>

                        <button
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-500 hover:bg-primary/10 hover:text-primary transition-colors active:scale-90">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                    </div>

                    <hr class="border-dashed border-slate-100 my-4">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="px-2.5 py-1 text-[11px] font-bold font-headline bg-amber-50 text-amber-600 rounded-lg flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">shield</span>
                                مدير نظام
                            </span>

                            <span
                                class="px-2.5 py-1 text-[11px] font-bold font-headline bg-emerald-50 text-emerald-600 rounded-lg flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                نشط
                            </span>
                        </div>

                        <span class="material-symbols-outlined text-slate-300 text-xl">admin_panel_settings</span>
                    </div>
                </div>
            @empty
            @endforelse


        </div>
    </div>
@endsection