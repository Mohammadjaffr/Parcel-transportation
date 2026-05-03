@props(['status' => 'pending'])

@php
    // إعداد مصفوفات لتحديد اللون، الأيقونة، والنص العربي لكل حالة
    $statusColors = [
        'pending'            => 'bg-amber-50 text-amber-600 ring-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400',
        'received_at_branch' => 'bg-indigo-50 text-indigo-600 ring-indigo-500/20 dark:bg-indigo-500/10 dark:text-indigo-400',
        'in_transit'         => 'bg-purple-50 text-purple-600 ring-purple-500/20 dark:bg-purple-500/10 dark:text-purple-400',
        'delivered'          => 'bg-emerald-50 text-emerald-600 ring-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400',
        'returned'           => 'bg-rose-50 text-rose-600 ring-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400',
        'canceled'           => 'bg-slate-50 text-slate-600 ring-slate-500/20 dark:bg-slate-500/10 dark:text-slate-400',
    ];

    $statusIcons = [
        'pending'            => 'schedule',
        'received_at_branch' => 'storefront',
        'in_transit'         => 'local_shipping',
        'delivered'          => 'check_circle',
        'returned'           => 'assignment_return',
        'canceled'           => 'cancel',
    ];

    $statusLabels = [
        'pending'            => 'قيد الانتظار',
        'received_at_branch' => 'في الفرع',
        'in_transit'         => 'قيد التوصيل',
        'delivered'          => 'تم التسليم',
        'returned'           => 'مرتجعة',
        'canceled'           => 'ملغاة',
    ];

    $colorClass    = $statusColors[$status] ?? $statusColors['pending'];
    $icon          = $statusIcons[$status] ?? 'info';
    $label         = $statusLabels[$status] ?? $status;
@endphp

<div class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-xl ring-1 ring-inset {{ $colorClass }}">
    <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
    <span>{{ $label }}</span>
</div>