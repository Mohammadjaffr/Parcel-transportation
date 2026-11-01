@extends('receipts.layout')

@section('title', 'كشف حساب سائق - ' . ($driver['name'] ?? ''))

@section('content')
    @php
        $driverName = $driver['name'] ?? '---';
        $driverPhone = $driver['phone'] ?? '---';
        $driverBranch = $driver['branch'] ?? '---';

        $statementEntries = $statement['entries'] ?? [];

        $sumTotalDebit = $summary['total_debit'] ?? '0';
        $sumTotalCredit = $summary['total_credit'] ?? '0';
        $sumFinalBalance = $summary['final_balance'] ?? '0';
        $balanceStatus = $summary['balance_status'] ?? 'مصفى';
        $isDebtor = $summary['is_debtor'] ?? false;
        $isCreditor = $summary['is_creditor'] ?? false;
    @endphp

    <div
        class="max-w-4xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        <div class="p-6 border-b bg-white border-slate-100">
            @include('receipts.partials.manifest_header')
        </div>

        <div class="p-6 sm:p-8">
            <!-- 1. Driver Details Card -->
            <div class="flex flex-wrap gap-y-4 p-5 mb-8 bg-white rounded-2xl border shadow-sm border-slate-200">
                <div class="w-full sm:w-1/3">
                    <p class="mb-1 text-xs font-bold tracking-wider uppercase text-slate-400">اسم السائق</p>
                    <p class="text-lg font-black text-slate-800">{{ $driverName }}</p>
                </div>
                <div class="pr-6 w-full border-r sm:w-1/3 border-slate-100">
                    <p class="mb-1 text-xs font-bold tracking-wider uppercase text-slate-400">رقم الهاتف</p>
                    <p class="font-bold text-slate-800" dir="ltr">{{ $driverPhone }}</p>
                </div>
                <div class="pr-6 w-full border-r sm:w-1/3 border-slate-100">
                    <p class="mb-1 text-xs font-bold tracking-wider uppercase text-slate-400">الفرع المرتبط</p>
                    <p class="font-bold text-slate-800">{{ $driverBranch }}</p>
                </div>
            </div>

            <!-- 2. Financial Movements Table -->
            <div class="mb-8">
                <h2 class="flex gap-2 items-center mb-4 text-lg font-black text-slate-800">
                    <span class="w-2 h-6 rounded-full bg-brand-500"></span>
                    الحركات والقيود المالية (كشف الحساب)
                </h2>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="w-full text-sm text-right premium-table">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-black">
                                <th class="w-12 py-3 px-3 text-center">#</th>
                                <th class="w-28 py-3 px-3">التاريخ</th>
                                <th class="w-28 py-3 px-3">نوع الحركة</th>
                                <th class="py-3 px-3">البيان</th>
                                <th class="w-24 py-3 px-3 text-center">عليه (مدين)</th>
                                <th class="w-24 py-3 px-3 text-center">له / سداد (دائن)</th>
                                <th class="w-28 py-3 px-3 text-center">الرصيد التراكمي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($statementEntries as $entry)
                                @php
                                    $entryDebit = (float) ($entry['debit'] ?? 0);
                                    $entryCredit = (float) ($entry['credit'] ?? 0);
                                    $balanceAtRow = (float) ($entry['balance'] ?? 0);
                                    $isDebitEntry = $entryDebit > 0;
                                @endphp
                                <tr class="transition-colors hover:bg-slate-50/70">
                                    <td class="font-bold text-center text-slate-400 py-3 px-3">{{ $loop->iteration }}</td>
                                    <td class="text-slate-600 py-3 px-3" dir="ltr">{{ $entry['date'] ?? '---' }}</td>
                                    <td class="font-bold py-3 px-3">
                                        @if($isDebitEntry)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs text-rose-700 bg-rose-50 border border-rose-100 rounded-lg">
                                                قيد / عهدة
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg">
                                                قبض / توريد
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-slate-800">{{ $entry['description'] ?? '---' }}</div>
                                        @if(!empty($entry['reference']) && $entry['reference'] !== '---')
                                            <div class="mt-0.5 text-xs text-slate-400">مرجع: <span dir="ltr">{{ $entry['reference'] }}</span></div>
                                        @endif
                                    </td>
                                    <td class="text-center font-bold text-rose-600 py-3 px-3" dir="ltr">
                                        {{ $entryDebit > 0 ? number_format($entryDebit, 0) : '-' }}
                                    </td>
                                    <td class="text-center font-bold text-emerald-600 py-3 px-3" dir="ltr">
                                        {{ $entryCredit > 0 ? number_format($entryCredit, 0) : '-' }}
                                    </td>
                                    <td class="text-center font-black py-3 px-3 {{ $balanceAtRow > 0 ? 'text-rose-600' : ($balanceAtRow < 0 ? 'text-emerald-600' : 'text-slate-600') }}" dir="ltr">
                                        {{ number_format(abs($balanceAtRow), 0) }}
                                        <span class="text-[10px] font-normal text-slate-400">
                                            {{ $balanceAtRow > 0 ? 'عليه' : ($balanceAtRow < 0 ? 'له' : 'مصفى') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 font-medium text-center text-slate-400 bg-slate-50/50">
                                        لا توجد حركات مالية مسجلة لهذا السائق.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Financial Totals Summary Bar -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 border-t bg-slate-50 border-slate-200">
                        <div class="flex items-center justify-between sm:justify-start gap-2 p-2 bg-white rounded-xl border border-slate-200">
                            <span class="text-xs font-bold text-slate-500">إجمالي ما على السائق:</span>
                            <span class="text-base font-black text-rose-600" dir="ltr">{{ $sumTotalDebit }} ر.ي</span>
                        </div>

                        <div class="flex items-center justify-between sm:justify-start gap-2 p-2 bg-white rounded-xl border border-slate-200">
                            <span class="text-xs font-bold text-slate-500">إجمالي ما سدده السائق:</span>
                            <span class="text-base font-black text-emerald-600" dir="ltr">{{ $sumTotalCredit }} ر.ي</span>
                        </div>

                        <div class="flex items-center justify-between sm:justify-start gap-2 p-2 bg-white rounded-xl border {{ $isDebtor ? 'border-rose-200 bg-rose-50/20' : ($isCreditor ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200') }}">
                            <span class="text-xs font-bold text-slate-500">الرصيد المتبقي:</span>
                            <span class="text-base font-black {{ $isDebtor ? 'text-rose-600' : ($isCreditor ? 'text-emerald-600' : 'text-slate-700') }}" dir="ltr">
                                {{ $sumFinalBalance }} ر.ي
                            </span>
                            <span class="text-[10px] font-black px-2 py-0.5 rounded-lg {{ $isDebtor ? 'bg-rose-100 text-rose-700' : ($isCreditor ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ $balanceStatus }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}
            </p>

            <div class="pt-3 mt-3 border-t border-slate-700/50">
                <p class="text-[10px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr" class="font-mono text-slate-400">{{ config('app.company_phone') }}</span>
                </p>
            </div>
        </div>
    </div>
@endsection

