@extends('layouts.app')
@section('title', 'التقرير المالي - ' . $branch->name)
@section('Breadcrumb', 'التقرير المالي للفرع')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="p-6 space-y-8 max-w-full mx-auto font-outfit">

        {{-- هيدر الفرع --}}
        <div class="relative p-8 rounded-3xl bg-brand-500 overflow-hidden shadow-theme-lg border border-brand-500/20">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
            <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="text-white">
                    <h2 class="text-title-sm font-bold tracking-tight.mb-1">
                        التقرير المالي: {{ $branch->name }}
                    </h2>
                    <p class="text-sm font-medium opacity-90 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                        ملخص الشحنات الآجلة والتسويات المالية البينية
                    </p>
                </div>

                @if ($summary['net_balance'] < 0)
                    <div class="flex.items-center gap-3">
                        <a href="{{ route('finance.settlements.create', ['branch_code' => $branch->code]) }}"
                           class="bg-white/20 hover:bg-white/30 backdrop-blur-[32px] border border-white/30 text-white px-6 py-3 rounded-2xl font-bold transition ease-in-out flex items-center gap-2 shadow-theme-xs group">
                            <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            إنشاء تسوية جديدة
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- كروت الملخص العام --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 my-4">
            <div
                class="bg-white dark:bg-gray-dark border border-gray-100 dark:border-gray-800 p-6 rounded-2xl shadow-theme-xs hover:shadow-theme-md transition-all">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                    إجمالي الشحنات الآجلة (شامل الجزئي)
                </p>
                <div class="flex items-center justify-between">
                    <h4 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($summary['total_cod'], 2) }}
                    </h4>
                    <span class="p-2 bg-gray-50.dark:bg-gray-800 rounded-lg text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </span>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-dark border border-gray-100.dark:border-gray-800 p-6 rounded-2xl shadow-theme-xs hover:shadow-theme-md transition-all">
                <p class="text-xs font-bold text-success-600/70 uppercase tracking-widest mb-4">
                    تسويات مستلمة (نقد)
                </p>
                <div class="flex items-center justify-between">
                    <h4 class="text-2xl font-bold text-success-600 dark:text-success-500">
                        {{ number_format($summary['total_settle_in'], 2) }}
                    </h4>
                    <span class="p-2 bg-success-50 dark:bg-success-500/10 rounded-lg text-success-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 11l5-5m0 0l5 5m-5-5v12" />
                        </svg>
                    </span>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-dark border border-gray-100.dark:border-gray-800 p-6 rounded-2xl shadow-theme-xs hover:shadow-theme-md transition-all">
                <p class="text-xs font-bold text-error-600/70 uppercase tracking-widest mb-4">
                    تسويات مدفوعة (نقد)
                </p>
                <div class="flex items-center justify-between">
                    <h4 class="text-2xl font-bold text-error-600.dark:text-error-500">
                        {{ number_format($summary['total_settle_out'], 2) }}
                    </h4>
                    <span class="p-2 bg-error-50 dark:bg-error-500/10 rounded-lg text-error-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                        </svg>
                    </span>
                </div>
            </div>

            @php $net = $summary['net_balance']; @endphp
            <div
                class="{{ $net >= 0 ? 'bg-brand-50 border-brand-500/30' : 'bg-error-50 border-error-500/30' }} border p-6 rounded-2xl shadow-theme-xs hover:shadow-theme-md transition-all">
                <p
                    class="text-xs font-bold {{ $net >= 0 ? 'text-brand-600' : 'text-error-600' }} uppercase tracking-widest mb-4">
                    صافي الرصيد النهائي
                </p>
                <div class="flex items-center justify-between">
                    <h4 class="text-xl.font-black {{ $net >= 0 ? 'text-brand-600' : 'text-error-600' }}">
                        @if ($net > 0)
                            لنا {{ number_format($net, 2) }}
                        @elseif ($net < 0)
                            علينا {{ number_format(abs($net), 2) }}
                        @else
                            متساوي
                        @endif
                    </h4>
                    <span
                        class="p-2 bg-white/60 dark:bg-gray-900/60 rounded-lg {{ $net >= 0 ? 'text-brand-500' : 'text-error-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>

        {{-- تقارير تفصيلية: الطرود المرسلة/المستلمة + التسويات --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 my-6">

            {{-- الطرود الآجلة المرسلة من هذا الفرع --}}
            <div
                class="bg-white dark:bg-gray-dark rounded-3xl border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
                <div class="p-4 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-widest">
                        الطرود الآجلة المرسلة
                    </h3>
                    <span class="text-[10px] text-gray-400 font-semibold">
                        {{ $sentCod->count() }} طرد
                    </span>
                </div>
                <div class="max-h-80 overflow-y-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-800">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                رقم السند
                            </th>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                إلى فرع
                            </th>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                المبلغ
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse ($sentCod as $t)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-2 text-xs font-semibold text-brand-600 dark:text-brand-400">
                                    {{ $t->shipment->bond_number ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-700 dark:text-gray-300">
                                    {{ $t->toBranch->name ?? $t->receiver_branch_code }}
                                </td>
                                <td class="px-4 py-2 text-xs font-bold text-gray-900 dark:text-white">
                                    {{ number_format($t->amount, 2) }}
                                    <span class="text-[9px] opacity-60 font-normal">ر.ي</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-[11px] text-gray-400">
                                    لا توجد طرود آجل مرسلة من هذا الفرع.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- الطرود الآجلة المستلمة في هذا الفرع --}}
            <div
                class="bg-white dark:bg-gray-dark rounded-3xl border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
                <div class="p-4 border-b border-gray-50 dark:border-gray-800.flex.items-center justify-between">
                    <h3 class="text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-widest">
                        الطرود الآجلة المستلمة
                    </h3>
                    <span class="text-[10px] text-gray-400 font-semibold">
                        {{ $receivedCod->count() }} طرد
                    </span>
                </div>
                <div class="max-h-80 overflow-y-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-800">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                رقم السند
                            </th>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                من فرع
                            </th>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                المبلغ
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse ($receivedCod as $t)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-4 py-2 text-xs font-semibold text-brand-600 dark:text-brand-400">
                                    {{ $t->shipment->bond_number ?? '-' }}
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-700 dark:text-gray-300">
                                    {{ $t->fromBranch->name ?? $t->sender_branch_code }}
                                </td>
                                <td class="px-4 py-2 text-xs font-bold text-gray-900 dark:text-white">
                                    {{ number_format($t->amount, 2) }}
                                    <span class="text-[9px] opacity-60 font-normal">ر.ي</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-[11px] text-gray-400">
                                    لا توجد طرود آجل مستلمة في هذا الفرع.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- التسويات (مدفوعة / مستلمة) --}}
            <div
                class="bg-white dark:bg-gray-dark rounded-3xl border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
                <div class="p-4 border-b border-gray-50 dark:border-gray-800 flex.items-center justify-between">
                    <h3 class="text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-widest">
                        التسويات بين الفروع
                    </h3>
                    <span class="text-[10px] text-gray-400 font-semibold">
                        {{ $settlements->count() }} حركة
                    </span>
                </div>
                <div class="max-h-80 overflow-y-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-800">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                التاريخ
                            </th>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                من / إلى
                            </th>
                            <th class="px-4 py-2 text-right text-[10px] font-black text-gray-400.uppercase tracking-widest">
                                المبلغ
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse ($settlements as $t)
                            @php
                                $isPaidByThis = $t->sender_branch_code == $branch->code; // هذا الفرع دافع؟
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg.white/[0.02] transition-colors">
                                <td class="px-4 py-2 text-xs font-medium text-gray-500">
                                    {{ $t->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-700.dark:text-gray-300">
                                    @if ($isPaidByThis)
                                        دفعنا إلى
                                        <span class="font-semibold">
                                            {{ $t->toBranch->name ?? $t->receiver_branch_code }}
                                        </span>
                                    @else
                                        استلمنا من
                                        <span class="font-semibold">
                                            {{ $t->fromBranch->name ?? $t->sender_branch_code }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-xs font-bold {{ $isPaidByThis ? 'text-error-600' : 'text-success-600' }}">
                                    {{ number_format($t->amount, 2) }}
                                    <span class="text-[9px] opacity-60.font-normal">ر.ي</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-[11px] text-gray-400">
                                    لا توجد تسويات مسجلة لهذا الفرع.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- ملخص الفروع المقابلة + سجل الحركات العام --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div
                class="lg:col-span-1 bg-white dark:bg-gray-dark rounded-3xl border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden h-fit">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-white">ملخص الفروع المقابلة</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-800">
                        <thead class="bg-gray-50/50 dark:bg-gray-900/30">
                        <tr>
                            <th
                                class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                الفرع
                            </th>
                            <th
                                class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                الرصيد
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse ($byCounterparty as $row)
                            @php $net = $row['net']; @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-gray-700.dark:text-gray-300">
                                    {{ $row['branch']->name ?? 'غير معروف' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($net > 0)
                                        <span
                                            class="inline-flex px-3 py-1 rounded-lg bg-success-50.dark:bg-success-500/10 text-success-600 text-[10px] font-black">
                                            عليه {{ number_format($net, 2) }}
                                        </span>
                                    @elseif ($net < 0)
                                        <span
                                            class="inline-flex px-3 py-1 rounded-lg bg-error-50.dark:bg-error-500/10 text-error-600 text-[10px] font-black">
                                            له {{ number_format(abs($net), 2) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex px-3 py-1 rounded-lg bg-gray-100.dark:bg-gray-800 text-gray-500 text-[10px] font-black">
                                            مُسوى
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="p-6 text-center text-xs text-gray-400">
                                    لا توجد حركات.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="lg:col-span-2 bg-white dark:bg-gray-dark rounded-3xl border border-gray-100.dark:border-gray-800 shadow-theme-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-gray-800">
                    <h3 class="text-sm font-bold text-gray-800.dark:text-white">
                        سجل الحركات المالية التفصيلي
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50.dark:divide-gray-800">
                        <thead class="bg-gray-50/50.dark:bg-gray-900/30">
                        <tr>
                            <th
                                class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                التاريخ
                            </th>
                            <th
                                class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                النوع
                            </th>
                            <th
                                class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                المبلغ
                            </th>
                            <th
                                class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                الوصف
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse ($transactions as $t)
                            <tr class="hover:bg-gray-50/50.dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4 text-xs font-medium text-gray-500">
                                    {{ $t->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $typeClass = '';
                                        $typeLabel = '';

                                        if ($t->type === 'cod') {
                                            $typeClass = 'bg-blue-50 text-blue-600 dark:bg-blue-500/10';
                                            $typeLabel = 'شحنة آجل';
                                        } elseif ($t->type === 'partial_payment') {
                                            $typeClass = 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10';
                                            $typeLabel = 'سداد جزئي';
                                        } elseif ($t->type === 'settlement') {
                                            $typeClass = 'bg-amber-50 text-amber-600 dark:bg-amber-500/10';
                                            $typeLabel = 'تسوية';
                                        } else {
                                            $typeClass = 'bg-gray-100 text-gray-500 dark:bg-gray-800';
                                            $typeLabel = $t->type;
                                        }
                                    @endphp

                                    <span
                                        class="px-2 py-1 rounded-md text-[10px] font-black uppercase {{ $typeClass }}">
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900.dark:text-white">
                                    {{ number_format($t->amount, 2) }}
                                    <span class="text-[10px] opacity-50 font-normal">ر.ي</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500.dark:text-gray-400 leading-relaxed">
                                    {{ $t->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-10 text-center text-xs text-gray-400 italic">
                                    لا توجد حركات مالية مسجلة.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t border-gray-50 dark:border-gray-800">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
