@extends('receipts.layout')

@section('title', 'كشف حساب العميل')

@section('content')
    @php
        // === التوافق مع كلا مصدري البيانات ===
        // المصدر 1: CustomerController::accountStatement (يمرر المتغيرات مباشرة)
        // المصدر 2: CustomerAccountStatementReceipt.php (يمرر بيانات منظمة)

        // بيانات العميل
        $custName = $customer['name'] ?? ($customer->name ?? '---');
        $custPhone = $customer['phone'] ?? ($customer->phone ?? '---');
        $custBranch = $customer['branch'] ?? null;
        $custBranchName = is_array($custBranch) ? ($custBranch['name'] ?? '---') : ($custBranch ? ($custBranch->name ?? '---') : ($customer->branch->name ?? '---'));

        // الحركات المالية
        $statementEntries = $statement['entries'] ?? $entries ?? [];

        // الطرود
        $shipmentRows = $shipments ?? [];

        // المجاميع (من summary أو من المتغيرات المباشرة)
        $sumTotalCredit = $summary['total_credit'] ?? (isset($totalCredit) ? number_format($totalCredit, 0) : '0');
        $sumFinalBalance = $summary['final_balance'] ?? (isset($finalBalance) ? number_format(abs($finalBalance), 0) : '0');
        $sumIsDebtor = $summary['is_debtor'] ?? ($isDebtor ?? false);
        $sumTotalShipments = $summary['total_shipments'] ?? ($totalShipments ?? 0);
        $sumSentCount = $summary['sent_count'] ?? ($sentCount ?? 0);
        $sumReceivedCount = $summary['received_count'] ?? ($receivedCount ?? 0);

        // بيانات الشركة (من ReceiptController)
        $companyName = $company['name'] ?? (auth()->user()->app->name ?? 'مرسال');
        $companyLogo = $company['logo'] ?? null;
        $mainBranchTitle = $company['main_branch']['title'] ?? 'المركز الرئيسي';
    @endphp

    <div
        class="max-w-4xl w-full mx-auto bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] print-no-shadow overflow-hidden border border-slate-100 print-border my-8 print:my-0">

        <!-- 1. Header Section -->
        <div class="bg-slate-50 p-6 sm:p-8 border-b border-slate-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                <div class="flex items-center gap-4">
                    @if(!empty($companyLogo))
                        <div
                            class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center p-2 border border-slate-100">
                            <img src="{{ $companyLogo }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black text-brand-600 tracking-tight">{{ $companyName }}</h1>
                        <p class="text-slate-500 font-medium text-sm mt-1">للنقل والشحن السريع - {{ $mainBranchTitle }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div
                        class="inline-flex items-center justify-center px-4 py-2 bg-brand-50 text-brand-700 rounded-xl font-bold text-sm mb-2 border border-brand-100">
                        {{ $title ?? 'كشف حساب عميل' }}
                    </div>
                    <div class="text-slate-400 text-xs font-medium flex items-center gap-1.5 justify-end">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <!-- 2. Customer Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-8 flex flex-wrap gap-y-4 shadow-sm">
                <div class="w-full sm:w-1/3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">اسم العميل</p>
                    <p class="text-slate-800 font-black text-lg">{{ $custName }}</p>
                </div>
                <div class="w-full sm:w-1/3 border-r border-slate-100 pr-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">رقم الهاتف</p>
                    <p class="text-slate-800 font-bold" dir="ltr">{{ $custPhone }}</p>
                </div>
                <div class="w-full sm:w-1/3 border-r border-slate-100 pr-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">الفرع المرتبط</p>
                    <p class="text-slate-800 font-bold">{{ $custBranchName }}</p>
                </div>
            </div>

            <!-- 3. Financial Movements Table -->
            <div class="mb-8">
                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-2 h-6 rounded-full bg-brand-500"></span>
                    الحركات المالية (كشف الحساب)
                </h2>

                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-sm text-right premium-table">
                        <thead>
                            <tr>
                                <th class="w-12 text-center">#</th>
                                <th class="w-32">التاريخ</th>
                                <th class="w-32">النوع</th>
                                <th>البيان</th>
                                <th class="w-32 text-center">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statementEntries as $entry)
                                @php
                                    $entryDebit = (float) ($entry['debit'] ?? 0);
                                    $entryCredit = (float) ($entry['credit'] ?? 0);
                                    $amount = $entryDebit > 0 ? $entryDebit : $entryCredit;
                                    $isDebitEntry = $entryDebit > 0;

                                    // تاريخ الحركة
                                    $entryDate = $entry['date_formatted'] ?? (isset($entry['date']) ? \Carbon\Carbon::parse($entry['date'])->format('Y-m-d') : '---');

                                    // نوع الحركة
                                    $movementType = $entry['movement_type'] ?? $entry['type'] ?? '---';
                                @endphp
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="text-slate-600" dir="ltr">{{ $entryDate }}</td>
                                    <td class="font-bold text-slate-700">
                                        @if(in_array($movementType, ['shipment', 'شحنة', 'قيد مديونية']))
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-600 text-xs">{{ $movementType }}</span>
                                        @elseif(in_array($movementType, ['payment', 'دفعة', 'سداد / تحصيل']))
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded bg-emerald-50 text-emerald-600 text-xs">{{ $movementType }}</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded bg-slate-50 text-slate-600 text-xs">{{ $movementType }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-slate-800 font-bold">{{ $entry['description'] ?? '---' }}</div>
                                        @if(!empty($entry['reference']) && $entry['reference'] !== '---')
                                            <div class="text-xs text-slate-400 mt-0.5">مرجع: <span
                                                    dir="ltr">{{ $entry['reference'] }}</span></div>
                                        @endif
                                        @if(!empty($entry['notes']))
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $entry['notes'] }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center font-black {{ $isDebitEntry ? 'text-rose-600' : 'text-emerald-600' }}"
                                        dir="ltr">
                                        {{ number_format($amount, 0) }} {{ $isDebitEntry ? '(-)' : '(+)' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 font-medium bg-slate-50/50">
                                        لا توجد حركات مالية مسجلة لهذا العميل.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Financial Totals -->
                    <div
                        class="bg-slate-50 border-t border-slate-200 p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 font-bold text-sm">إجمالي الدفعات المسددة:</span>
                            <span class="text-emerald-600 font-black text-lg" dir="ltr">{{ $sumTotalCredit }} ر.ي</span>
                        </div>
                        <div
                            class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
                            <span class="text-slate-500 font-bold">الرصيد المتبقي:</span>
                            <span class="{{ $sumIsDebtor ? 'text-rose-600' : 'text-emerald-600' }} font-black text-xl"
                                dir="ltr">
                                {{ $sumFinalBalance }} ر.ي
                            </span>
                            @if(isset($summary['balance_status']))
                                <span
                                    class="text-xs font-bold {{ $sumIsDebtor ? 'text-rose-500' : 'text-emerald-500' }} bg-{{ $sumIsDebtor ? 'rose' : 'emerald' }}-50 px-2 py-0.5 rounded-lg">
                                    {{ $summary['balance_status'] }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Shipments Table -->
            <div class="mb-8">
                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                    <span class="w-2 h-6 rounded-full bg-indigo-500"></span>
                    تفاصيل الطرود (الشحنات)
                </h2>

                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-sm text-right premium-table">
                        <thead>
                            <tr>
                                <th class="w-12 text-center">#</th>
                                <th class="w-28">التاريخ</th>
                                <th class="w-28">رقم السند</th>
                                <th>نوع الطرد</th>
                                <th class="w-24 text-center">الدور</th>
                                <th class="w-28 text-center">طريقة الدفع</th>
                                <th class="w-28 text-center">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipmentRows as $shipment)
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="text-center font-bold text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="text-slate-600" dir="ltr">{{ $shipment['date'] ?? '---' }}</td>
                                    <td class="text-slate-700 font-bold" dir="ltr">{{ $shipment['bond_number'] ?? '---' }}</td>
                                    <td>
                                        <div class="text-slate-800 font-bold">{{ $shipment['package_type'] ?? '---' }}</div>
                                        @if(!empty($shipment['weight']))
                                            <div class="text-xs text-slate-400 mt-0.5">الوزن: {{ $shipment['weight'] }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $dirLabel = $shipment['direction_label'] ?? $shipment['direction'] ?? '---';
                                            $dirColor = ($dirLabel === 'مرسل' || $dirLabel === 'sent') ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700';
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold {{ $dirColor }}">
                                            {{ $dirLabel }}
                                        </span>
                                    </td>
                                    <td class="text-center text-slate-600 font-bold text-xs">
                                        {{ $shipment['payment_method'] ?? '---' }}</td>
                                    <td class="text-center font-black text-slate-800" dir="ltr">
                                        {{ is_numeric($shipment['total_amount'] ?? null) ? number_format((float) $shipment['total_amount'], 0) : ($shipment['total_amount'] ?? '0') }}
                                        ر.ي
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-400 font-medium bg-slate-50/50">
                                        لا توجد شحنات مسجلة لهذا العميل.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Shipment Stats -->
                    <div
                        class="bg-slate-50 border-t border-slate-200 p-4 grid grid-cols-3 gap-4 text-center divide-x divide-x-reverse divide-slate-200">
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1">إجمالي الطرود</p>
                            <p class="text-slate-800 font-black text-lg">{{ $sumTotalShipments }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1">الطرود المرسلة</p>
                            <p class="text-slate-800 font-black text-lg">{{ $sumSentCount }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase mb-1">الطرود المستلمة</p>
                            <p class="text-slate-800 font-black text-lg">{{ $sumReceivedCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Signatures -->
            <div class="grid grid-cols-3 gap-8 mt-12 mb-4">
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 mb-8">توقيع المحاسب</p>
                    <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 mb-8">توقيع العميل</p>
                    <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 mb-8">ختم الفرع</p>
                    <div class="border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            {{-- بيانات الفاتورة --}}
            <p class="text-xs font-medium text-slate-300">
                تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
                {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? now()->format('Y-m-d H:i') }}
            </p>

            {{-- الخط الفاصل التسويقي لشركة تيار --}}
            <div class="mt-3 pt-3 border-t border-slate-700/50">
                <p class="text-[10px] font-bold text-slate-500">
                    تطوير <span class="text-slate-400">شركة تيار</span> للأنظمة وتقنية المعلومات
                    <span class="mx-1">|</span>
                    لطلب النظام: <span dir="ltr" class="text-slate-400 font-mono">+967 780 261 952</span>
                </p>
            </div>
        </div>
    </div>
@endsection