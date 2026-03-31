@extends('mobile.layouts.app')

@section('title', 'إدارة السائقين')

@section('content')
    <div class="flex flex-col gap-5 pb-24">

        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-headline font-bold text-on-surface">إدارة السائقين</h1>
                <p class="text-on-surface-variant text-sm">إجمالي السائقين: <span class="text-primary font-bold">{{ $drivers->count() }}</span>
                </p>
            </div>

            <a href="#"
                class="flex items-center gap-1.5 px-4 py-2.5 bg-primary text-white rounded-2xl hover:bg-primary-hover active:scale-95 transition-all text-sm font-headline font-bold shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-xl">add</span>
                إضافة سائق
            </a>
        </div>

        <div class="relative w-full">
            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" placeholder="ابحث باسم السائق أو رقم الهاتف..."
                class="w-full h-12 pr-12 pl-4 bg-surface-container-low border border-slate-100 rounded-2xl text-sm font-headline focus:outline-none focus:border-primary focus:bg-white transition-all">
        </div>

        <div class="flex flex-col gap-4">
            @forelse ($drivers as $driver)
                <div
                    class="bg-surface-container-lowest border border-slate-100/70 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-primary-container text-primary rounded-2xl flex items-center justify-center font-headline font-bold text-lg">
                                @php
                                    $words = explode(' ', $driver->name);
                                    $first_char = mb_substr($words[0], 0, 1, 'utf-8');
                                    $second_char = isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '';
                                    echo $first_char . ' ' . $second_char;
                                @endphp
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="font-headline font-bold text-on-surface text-base group-hover:text-primary transition-colors">
                                    {{ $driver->name }}</span>
                            </div>
                        </div>

                        <button
                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-500 hover:bg-primary/10 hover:text-primary transition-colors active:scale-90">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                    </div>

                    <hr class="border-dashed border-slate-100 mb-4">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-600 text-sm tracking-wider"><x-phone-number :value="$driver->phone" /></span>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="https://wa.me/{{ $driver->phone }}"
                                class="w-10 h-10 flex items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 active:scale-90 transition-all"
                                title="واتساب">
                                <span class="material-symbols-outlined text-[20px]"
                                    style="font-variation-settings: 'FILL' 1;">chat</span>
                            </a>
                            <a href="tel:{{ $driver->phone }}"
                                class="w-10 h-10 flex items-center justify-center rounded-2xl bg-primary-container text-primary hover:bg-primary hover:text-white active:scale-90 transition-all shadow-sm shadow-primary/20"
                                title="اتصال هاتفي">
                                <span class="material-symbols-outlined text-[20px]"
                                    style="font-variation-settings: 'FILL' 1;">call</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
    </div>
@endsection