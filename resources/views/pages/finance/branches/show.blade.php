@extends('layouts.app')
@section('title', 'التقرير المالي - ' . $branch->name)
@section('Breadcrumb', 'التقرير المالي للفرع')

@section('content')
<div class="space-y-5 sm:space-y-6">

    <!-- الهيدر + كروت الملخص -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                التقرير المالي للفرع: {{ $branch->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                ملخص الشحنات الآجلة والتسويات مع باقي الفروع.
            </p>
        </div>
        @if ($summary['net_balance'] < 0)

        <div>
            <a href="{{ route('finance.settlements.create') }}"
               class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                + تسوية جديدة بين الفروع
            </a>
        </div>
        @endif
        
    </div>

    <!-- كروت الملخص -->
    <div class="grid grid-cols-1 gap-2 xl:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">إجمالي الشحنات الآجلة</p>
            <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ number_format($summary['total_cod'], 2) }} ر.ي
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">تسويات مستلمة</p>
            <p class="mt-2 text-lg font-semibold text-success-500 dark:text-success-400">
                {{ number_format($summary['total_settle_in'], 2) }} ر.ي
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">تسويات مدفوعة</p>
            <p class="mt-2 text-lg font-semibold text-error-500 dark:text-error-400">
                {{ number_format($summary['total_settle_out'], 2) }} ر.ي
            </p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-xs text-gray-500 dark:text-gray-400">الرصيد النهائي</p>
            <p class="mt-2 text-lg font-semibold">
                @php $net = $summary['net_balance']; @endphp
                @if ($net > 0)
                    <span class="text-success-600 dark:text-success-400">
                        له {{ number_format($net, 2) }} ر.ي
                    </span>
                @elseif ($net < 0)
                    <span class="text-error-600 dark:text-error-400">
                        عليه {{ number_format(abs($net), 2) }} ر.ي
                    </span>
                @else
                    <span class="text-gray-700 dark:text-gray-200">
                        متساوي
                    </span>
                @endif
            </p>
        </div>
    </div>

    <!-- جدول: ملخص حسب الفرع المقابل -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">
            ملخص العلاقة مع الفروع الأخرى
        </h3>

        <div class="max-w-full overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/40">
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                            الفرع المقابل
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                            الرصيد
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($byCounterparty as $row)
                        @php $net = $row['net']; @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">
                                {{ $row['branch']->name ?? 'غير معروف' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($net > 0)
                                    <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-400">
                                        هذا الفرع له {{ number_format($net, 2) }} ر.ي
                                    </span>
                                @elseif ($net < 0)
                                    <span class="rounded-full bg-error-50 px-2 py-0.5 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-400">
                                        هذا الفرع عليه {{ number_format(abs($net), 2) }} ر.ي
                                    </span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        لا يوجد رصيد
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                لا توجد حركات مالية مع فروع أخرى.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- جدول: كل الحركات -->
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white/90">
            جميع الحركات المالية للفرع
        </h3>

        <div class="max-w-full overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-y border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/40">
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">التاريخ</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">النوع</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">من</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">إلى</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">المبلغ</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">الوصف</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($transactions as $t)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $t->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($t->type === 'cod')
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                                        شحنة آجل
                                    </span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
                                        تسوية
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $t->fromBranch->name ?? 'غير معروف' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $t->toBranch->name ?? 'غير معروف' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">
                                {{ number_format($t->amount, 2) }} ر.ي
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $t->description }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                لا توجد حركات مالية لهذا الفرع.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>

</div>
@endsection
