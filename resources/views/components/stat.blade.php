@props(['title', 'value', 'color' => 'primary'])

@php
    $colorClasses = [
        'success' => 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
        'error' => 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
        'primary' => 'bg-primary/5 text-primary border-primary/10'
    ][$color] ?? 'bg-slate-50 text-slate-600 border-slate-100';

    $icon = [
        'success' => 'trending_up',
        'error' => 'trending_down',
    ][$color] ?? 'bar_chart';
@endphp

<div class="p-4 bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm flex items-center gap-3">
    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border {{ $colorClasses }}">
        <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
    </div>
    <div>
        <span class="text-[10px] text-gray-400 font-bold block">{{ $title }}</span>
        <span class="text-base font-black text-slate-800 dark:text-white">{{ $value }}</span>
    </div>
</div>