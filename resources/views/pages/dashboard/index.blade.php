@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')

@php
    $user = auth()->user();
    $branchName = $user->branch->name ?? 'الفرع الرئيسي';

    $statusConfig = [
        'pending' => [
            'bg' => 'bg-amber-50 dark:bg-amber-500/10',
            'text' => 'text-amber-600 dark:text-amber-400',
            'border' => 'border-amber-100 dark:border-amber-500/20',
            'icon' => 'inventory_2',
            'label' => 'بالمستودع',
        ],
        'received_at_branch' => [
            'bg' => 'bg-amber-50 dark:bg-amber-500/10',
            'text' => 'text-amber-600 dark:text-amber-400',
            'border' => 'border-amber-100 dark:border-amber-500/20',
            'icon' => 'inventory_2',
            'label' => 'بالمستودع',
        ],
        'in_transit' => [
            'bg' => 'bg-blue-50 dark:bg-blue-500/10',
            'text' => 'text-blue-600 dark:text-blue-400',
            'border' => 'border-blue-100 dark:border-blue-500/20',
            'icon' => 'local_shipping',
            'label' => 'في الطريق',
        ],
        'out_for_delivery' => [
            'bg' => 'bg-blue-50 dark:bg-blue-500/10',
            'text' => 'text-blue-600 dark:text-blue-400',
            'border' => 'border-blue-100 dark:border-blue-500/20',
            'icon' => 'two_wheeler',
            'label' => 'مع المندوب',
        ],
        'delivered' => [
            'bg' => 'bg-emerald-50 dark:bg-emerald-500/10',
            'text' => 'text-emerald-600 dark:text-emerald-400',
            'border' => 'border-emerald-100 dark:border-emerald-500/20',
            'icon' => 'done_all',
            'label' => 'تم التسليم',
        ],
        'returned' => [
            'bg' => 'bg-rose-50 dark:bg-rose-500/10',
            'text' => 'text-rose-600 dark:text-rose-400',
            'border' => 'border-rose-100 dark:border-rose-500/20',
            'icon' => 'assignment_return',
            'label' => 'مرتجع',
        ],
        'cancelled' => [
            'bg' => 'bg-slate-50 dark:bg-slate-500/10',
            'text' => 'text-slate-600 dark:text-slate-400',
            'border' => 'border-slate-100 dark:border-slate-500/20',
            'icon' => 'cancel',
            'label' => 'ملغي',
        ],
    ];
@endphp

<div class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl">

    {{-- ================= 1. Header / Welcome ================= --}}
    <div class="relative overflow-hidden p-6 mx-auto w-full max-w-7xl rounded-[2rem] bg-gradient-to-br from-slate-900 to-slate-800 shadow-lg">
        <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full blur-3xl bg-primary/20"></div>
        <div class="absolute -bottom-16 -left-16 w-56 h-56 rounded-full blur-3xl bg-emerald-500/20"></div>

        <div class="flex relative z-10 flex-col gap-6 justify-between md:flex-row md:items-center">
            <div class="min-w-0">
                <p class="mb-2 text-xs font-bold text-slate-400">
                    {{ now()->translatedFormat('l، d F Y') }}
                </p>

                <h1 class="text-2xl font-black text-white md:text-3xl font-headline">
                    أهلاً، {{ $user->name ?? 'المستخدم' }}
                </h1>

                <div class="flex flex-wrap gap-2 items-center mt-3">
                    <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[11px] font-black text-slate-200 rounded-xl border border-white/10 bg-white/10 backdrop-blur-sm shadow-sm">
                        <span class="material-symbols-outlined text-[15px]">storefront</span>
                        {{ $branchName }}
                    </span>

                    <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[11px] font-black text-slate-300 rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm shadow-sm">
                        <span class="material-symbols-outlined text-[15px]">query_stats</span>
                        {{ $periodName }}
                    </span>
                </div>
            </div>

            <div class="flex gap-3 items-center">
                <div class="hidden flex-col items-end md:flex">
                    <span class="text-[11px] font-bold text-slate-400">لوحة التحكم</span>
                    <span class="text-sm font-black text-white">ملخص عمليات الفرع</span>
                </div>
                <div class="flex justify-center items-center w-14 h-14 rounded-2xl border shadow-inner backdrop-blur-md bg-white/10 border-white/20">
                    <span class="material-symbols-outlined text-white text-[28px]">dashboard</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= 2. Period Filter ================= --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="flex flex-col gap-3 justify-between md:flex-row md:items-center">
            <div>
                <h2 class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white">
                    <span class="material-symbols-outlined text-primary text-[22px]">monitoring</span>
                    مؤشرات الأداء
                </h2>
            </div>

            <div class="flex overflow-x-auto gap-2 pb-1 custom-scrollbar">
                @foreach(['today' => 'اليوم', 'this_week' => 'هذا الأسبوع', 'this_month' => 'هذا الشهر', 'last_month' => 'الشهر الماضي', 'all' => 'الكل'] as $key => $label)
                    <a href="{{ request()->fullUrlWithQuery(['period' => $key]) }}"
                        class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-[12px] font-bold transition-all border
                        {{ $period == $key 
                            ? ($key == 'all' ? 'bg-primary text-white border-primary shadow-md' : 'bg-slate-800 text-white border-slate-800 shadow-md dark:bg-white dark:text-slate-900 dark:border-white') 
                            : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50 dark:bg-boxdark dark:text-bodydark dark:border-boxdark-2 dark:hover:bg-boxdark-2' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ================= 3. Financial & Passengers Grid (العملاء والركاب) ================= --}}
    <div class="grid grid-cols-1 gap-4 mx-auto w-full max-w-7xl md:grid-cols-2 xl:grid-cols-4">
        
        {{-- ديون العملاء --}}
        <a href="{{ route('customers.index') }}" class="relative overflow-hidden p-5 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 group transition-transform hover:-translate-y-1 block">
            <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-rose-50 rounded-full transition-transform dark:bg-rose-500/10 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 text-rose-500 mb-3 dark:text-rose-400">
                    <span class="material-symbols-outlined text-[20px]">money_off</span>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">عملاء عليهم ديون</span>
                </div>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white font-headline">
                    {{ number_format($debtorsCount ?? 0) }}
                </h3>
            </div>
        </a>

        {{-- أرصدة العملاء --}}
        <a href="{{ route('customers.index') }}" class="relative overflow-hidden p-5 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 group transition-transform hover:-translate-y-1 block">
            <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-emerald-50 rounded-full transition-transform dark:bg-emerald-500/10 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 text-emerald-500 mb-3 dark:text-emerald-400">
                    <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">عملاء لهم أرصدة</span>
                </div>
                <h3 class="text-3xl font-black text-slate-800 dark:text-white font-headline">
                    {{ number_format($creditorsCount ?? 0) }}
                </h3>
            </div>
        </a>

        {{-- 🛡️ حماية الواجهة: لا يظهر كرت الركاب إلا لمن يملك خدمة الركاب --}}
        @hasservice('Passengers')
        {{-- تنبيه الركاب (يأخذ مساحة عمودين) --}}
        <a href="{{ route('passengers.index') }}" class="xl:col-span-2 relative overflow-hidden p-5 rounded-[1.5rem] shadow-[0_8px_20px_rgba(99,102,241,0.2)] bg-indigo-500 transition-transform active:scale-[0.99] group flex items-center justify-between">
            <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full transition-transform bg-white/10 group-hover:scale-150"></div>
            <div class="absolute -top-10 right-24 w-24 h-24 rounded-full bg-white/10 blur-xl"></div>

            <div class="relative z-10 flex items-center gap-4 text-white">
                <div class="flex justify-center items-center w-14 h-14 rounded-2xl border backdrop-blur-sm bg-white/20 border-white/20 shrink-0">
                    <span class="material-symbols-outlined text-[28px]">hail</span>
                </div>
                <div>
                    <p class="mb-1 text-xs font-bold text-indigo-100">إدارة الركاب والرحلات</p>
                    <div class="flex gap-2 items-end">
                        <span class="text-3xl font-black font-headline">{{ number_format($pendingPassengersCount ?? 0) }}</span>
                        <span class="mb-1.5 text-xs font-bold text-indigo-100">راكب قيد الانتظار</span>
                    </div>
                </div>
            </div>
            <div class="relative z-10 inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 group-hover:bg-white/20 group-hover:text-white transition-all">
                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">arrow_forward</span>
            </div>
        </a>
        @endhasservice
    </div>

    {{-- ================= 4. Shipments KPI Grid (إحصائيات الطرود) ================= --}}
    <div class="grid grid-cols-1 gap-4 mx-auto w-full max-w-7xl sm:grid-cols-2 xl:grid-cols-4">
        
        {{-- بالمستودع --}}
        <div class="relative overflow-hidden p-5 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 group">
            <div class="absolute -right-8 -bottom-8 w-24 h-24 bg-amber-50 rounded-full transition-transform dark:bg-amber-500/10 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <div class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10">
                        <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-black rounded-lg text-amber-600 bg-amber-50 dark:bg-amber-500/10 dark:text-amber-400">توزيع</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">بالمستودع للتوزيع</span>
                    <h3 class="mt-1 text-3xl font-black text-slate-800 dark:text-white font-headline">{{ number_format($stats['pending'] ?? 0) }}</h3>
                </div>
            </div>
        </div>

        {{-- في الطريق --}}
        <div class="relative overflow-hidden p-5 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 group">
            <div class="absolute -right-8 -bottom-8 w-24 h-24 bg-blue-50 rounded-full transition-transform dark:bg-blue-500/10 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <div class="flex justify-center items-center w-12 h-12 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                        <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-black text-blue-600 bg-blue-50 rounded-lg dark:bg-blue-500/10 dark:text-blue-400">نشط</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">في الطريق</span>
                    <h3 class="mt-1 text-3xl font-black text-slate-800 dark:text-white font-headline">{{ number_format($stats['with_driver'] ?? 0) }}</h3>
                </div>
            </div>
        </div>

        {{-- تم التسليم --}}
        <div class="relative overflow-hidden p-5 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2 group">
            <div class="absolute -right-8 -bottom-8 w-24 h-24 bg-emerald-50 rounded-full transition-transform dark:bg-emerald-500/10 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <div class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                        <span class="material-symbols-outlined text-[24px]">task_alt</span>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-black text-emerald-600 bg-emerald-50 rounded-lg dark:bg-emerald-500/10 dark:text-emerald-400">مكتمل</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">تم التسليم بنجاح</span>
                    <h3 class="mt-1 text-3xl font-black text-slate-800 dark:text-white font-headline">{{ number_format($stats['delivered'] ?? 0) }}</h3>
                </div>
            </div>
        </div>

        {{-- المرتجعات --}}
        <div class="relative overflow-hidden p-5 rounded-[1.5rem] border border-rose-100 shadow-sm bg-rose-50 dark:bg-rose-500/10 dark:border-rose-500/20 group">
            <div class="absolute -right-8 -bottom-8 w-24 h-24 rounded-full transition-transform bg-rose-100/70 dark:bg-rose-500/10 group-hover:scale-150"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start">
                    <div class="flex justify-center items-center w-12 h-12 text-rose-500 bg-white rounded-xl shadow-sm dark:bg-boxdark dark:text-rose-400">
                        <span class="material-symbols-outlined text-[24px]">assignment_return</span>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-black text-rose-600 bg-white rounded-lg dark:bg-boxdark dark:text-rose-400">متابعة</span>
                </div>
                <div class="mt-4">
                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400">مرتجعات معلقة</span>
                    <h3 class="mt-1 text-3xl font-black text-rose-600 dark:text-rose-400 font-headline">{{ number_format($stats['returned'] ?? 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= 5. Latest Shipments Table ================= --}}
    <div class="mx-auto w-full max-w-7xl">
        <div class="overflow-hidden bg-white rounded-[2rem] border border-slate-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
            
            <div class="flex flex-col gap-3 justify-between p-6 border-b border-slate-100 md:flex-row md:items-center dark:border-boxdark-2">
                <div>
                    <h2 class="flex gap-2 items-center text-lg font-black text-slate-800 dark:text-white">
                        <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                        آخر التحديثات في الفرع
                    </h2>
                    <p class="mt-1 text-xs font-bold text-slate-500 dark:text-slate-400">آخر الطرود المرتبطة بالفرع الحالي</p>
                </div>
                <a href="{{ route('shipment.outgoing.index') }}"
                    class="inline-flex gap-2 justify-center items-center px-5 h-10 text-xs font-bold rounded-xl border transition-all text-primary bg-primary/5 border-primary/10 hover:bg-primary hover:text-white dark:bg-primary/10 dark:text-primary dark:hover:bg-primary dark:hover:text-white active:scale-95">
                    عرض الكل
                    <span class="material-symbols-outlined text-[16px] rtl:rotate-180">arrow_forward</span>
                </a>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-500 uppercase tracking-[0.1em] bg-slate-50/80 dark:bg-boxdark-2 dark:text-slate-400 border-b border-slate-100 dark:border-boxdark-2">
                            <th class="px-6 py-4">رقم السند</th>
                            <th class="px-6 py-4">العملاء</th>
                            <th class="px-6 py-4 text-center">المسار</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">التاريخ</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-boxdark-2">
                        @forelse($latestShipments as $shipment)
                            @php
                                $currentStatus = $statusConfig[$shipment->status] ?? $statusConfig['cancelled'];
                                $senderName = $shipment->senderCustomer->name ?? 'عميل';
                                $receiverName = $shipment->receiverCustomer->name ?? 'مستلم';
                                $senderBranch = $shipment->senderBranch->name ?? $shipment->sender_branch_code ?? '---';
                                $receiverBranch = $shipment->receiverBranch->name ?? $shipment->receiver_branch_code ?? '---';
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/70 dark:hover:bg-boxdark-2/50 group">
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-11 h-11 rounded-xl {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} shrink-0 transition-transform group-hover:scale-110">
                                            <span class="material-symbols-outlined text-[22px]">{{ $currentStatus['icon'] }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="block font-mono text-sm font-black text-slate-800 dark:text-white">
                                                {{ $shipment->bond_number ?? '---' }}
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">
                                                {{ $shipment->created_at?->format('h:i A') }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs font-black text-slate-800 dark:text-white">
                                            {{ \Illuminate\Support\Str::limit($senderName, 24) }}
                                        </span>
                                        <span class="flex gap-1.5 items-center text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                            <span class="material-symbols-outlined text-[13px] rtl:rotate-180">arrow_left_alt</span>
                                            {{ \Illuminate\Support\Str::limit($receiverName, 24) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex gap-2 items-center px-3 py-1.5 text-[11px] font-black text-slate-600 bg-slate-50 rounded-xl border border-slate-100 dark:bg-boxdark-2 dark:border-boxdark dark:text-slate-300">
                                        <span>{{ $senderBranch }}</span>
                                        <span class="material-symbols-outlined text-[15px] text-slate-300 rtl:rotate-180">arrow_right_alt</span>
                                        <span class="text-primary">{{ $receiverBranch }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-[10px] font-black rounded-xl border {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} {{ $currentStatus['border'] }}">
                                        <span class="material-symbols-outlined text-[14px]">{{ $currentStatus['icon'] }}</span>
                                        {{ $currentStatus['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-black text-slate-600 dark:text-slate-300">
                                        {{ $shipment->created_at?->format('Y-m-d') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                                        class="inline-flex justify-center items-center w-9 h-9 text-slate-400 bg-white rounded-xl border border-slate-100 shadow-sm transition-all hover:text-primary hover:border-primary/30 hover:shadow-md dark:bg-boxdark dark:border-boxdark-2 active:scale-95">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-20 text-center">
                                    <div class="flex flex-col justify-center items-center">
                                        <div class="flex justify-center items-center mb-4 w-16 h-16 bg-slate-50 rounded-2xl border border-slate-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span class="material-symbols-outlined text-[32px] text-slate-300 dark:text-slate-600">inbox</span>
                                        </div>
                                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400">لا توجد طرود حديثة في هذا الفرع.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tablet / Mobile Cards (للتجاوب مع الشاشات الصغيرة) --}}
            <div class="flex flex-col gap-3 p-4 lg:hidden">
                @forelse($latestShipments as $shipment)
                    @php
                        $currentStatus = $statusConfig[$shipment->status] ?? $statusConfig['cancelled'];
                        $senderName = $shipment->senderCustomer->name ?? 'عميل';
                        $receiverName = $shipment->receiverCustomer->name ?? 'مستلم';
                        $senderBranch = $shipment->senderBranch->name ?? $shipment->sender_branch_code ?? '---';
                        $receiverBranch = $shipment->receiverBranch->name ?? $shipment->receiver_branch_code ?? '---';
                    @endphp
                    <a href="{{ route('shipment.outgoing.show', $shipment->id) }}"
                        class="block p-4 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm transition-transform dark:bg-boxdark-2 dark:border-boxdark active:scale-[0.98]">
                        <div class="flex gap-3 justify-between items-start">
                            <div class="flex gap-3 items-center min-w-0">
                                <div class="flex justify-center items-center w-12 h-12 rounded-[1rem] {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} shrink-0">
                                    <span class="material-symbols-outlined text-[24px]">{{ $currentStatus['icon'] }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-mono text-sm font-black text-slate-800 dark:text-white">{{ $shipment->bond_number ?? '---' }}</p>
                                    <p class="mt-1 text-[10px] font-bold text-slate-400 dark:text-slate-500">
                                        {{ $senderName }} <span class="mx-1 text-slate-300">»</span> {{ $receiverName }}
                                    </p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black border shrink-0 {{ $currentStatus['bg'] }} {{ $currentStatus['text'] }} {{ $currentStatus['border'] }}">
                                {{ $currentStatus['label'] }}
                            </span>
                        </div>
                        <div class="flex justify-between items-end pt-3 mt-3 border-t border-slate-100 dark:border-boxdark">
                            <div class="min-w-0">
                                <span class="block mb-1 text-[10px] font-bold text-slate-400">المسار</span>
                                <div class="flex gap-1.5 items-center text-xs font-black text-slate-600 dark:text-slate-300">
                                    <span>{{ $senderBranch }}</span>
                                    <span class="material-symbols-outlined text-[14px] text-slate-300 rtl:rotate-180">arrow_right_alt</span>
                                    <span class="text-primary">{{ $receiverBranch }}</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400">{{ $shipment->created_at?->format('Y-m-d') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col justify-center items-center py-10 text-center bg-white rounded-[1.5rem] border border-slate-100 border-dashed dark:bg-boxdark-2 dark:border-boxdark">
                        <span class="mb-2 text-3xl text-slate-300 material-symbols-outlined dark:text-slate-600">inbox</span>
                        <p class="text-xs font-bold text-slate-400">لا توجد طرود حديثة في هذا الفرع.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection