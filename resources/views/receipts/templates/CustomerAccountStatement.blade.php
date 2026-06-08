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
        <div class="p-6 border-b bg-slate-50 sm:p-8 border-slate-100">
            <div class="flex flex-col gap-6 justify-between items-start sm:flex-row sm:items-center">
                <div class="flex gap-4 items-center">
                    @if(!empty($companyLogo))
                        <div
                            class="flex justify-center items-center p-2 w-16 h-16 bg-white rounded-2xl border shadow-sm border-slate-100">
                            <img src="{{ $companyLogo }}" alt="Logo" class="object-contain w-full h-full">
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-brand-600">{{ $companyName }}</h1>
                        <p class="mt-1 text-sm font-medium text-slate-500">للنقل والشحن السريع - {{ $mainBranchTitle }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <div
                        class="inline-flex justify-center items-center px-4 py-2 mb-2 text-sm font-bold rounded-xl border bg-brand-50 text-brand-700 border-brand-100">
                        {{ $title ?? 'كشف حساب عميل' }}
                    </div>
                    <div class="flex gap-1.5 justify-end items-center text-xs font-medium text-slate-400">
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
            <div class="flex flex-wrap gap-y-4 p-5 mb-8 bg-white rounded-2xl border shadow-sm border-slate-200">
                <div class="w-full sm:w-1/3">
                    <p class="mb-1 text-xs font-bold tracking-wider uppercase text-slate-400">اسم العميل</p>
                    <p class="text-lg font-black text-slate-800">{{ $custName }}</p>
                </div>
                <div class="pr-6 w-full border-r sm:w-1/3 border-slate-100">
                    <p class="mb-1 text-xs font-bold tracking-wider uppercase text-slate-400">رقم الهاتف</p>
                    <p class="font-bold text-slate-800">{{ $custPhone }}</p>
                </div>
                <div class="pr-6 w-full border-r sm:w-1/3 border-slate-100">
                    <p class="mb-1 text-xs font-bold tracking-wider uppercase text-slate-400">الفرع المرتبط</p>
                    <p class="font-bold text-slate-800">{{ $mainBranchTitle }}</p>
                </div>
            </div>

            <!-- 3. Financial Movements Table -->
            <div class="mb-8">
                <h2 class="flex gap-2 items-center mb-4 text-lg font-black text-slate-800">
                    <span class="w-2 h-6 rounded-full bg-brand-500"></span>
                    الحركات المالية (كشف الحساب)
                </h2>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
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
                                    <td class="font-bold text-center text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="text-slate-600" dir="ltr">{{ $entryDate }}</td>
                                    <td class="font-bold text-slate-700">
                                        @if(in_array($movementType, ['shipment', 'شحنة', 'قيد مديونية']))
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs text-blue-600 bg-blue-50 rounded">{{ $movementType }}</span>
                                        @elseif(in_array($movementType, ['payment', 'دفعة', 'سداد / تحصيل']))
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs text-emerald-600 bg-emerald-50 rounded">{{ $movementType }}</span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs rounded bg-slate-50 text-slate-600">{{ $movementType }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-bold text-slate-800">{{ $entry['description'] ?? '---' }}</div>
                                        @if(!empty($entry['reference']) && $entry['reference'] !== '---')
                                            <div class="mt-0.5 text-xs text-slate-400">مرجع: <span
                                                    dir="ltr">{{ $entry['reference'] }}</span></div>
                                        @endif
                                        @if(!empty($entry['notes']))
                                            <div class="mt-0.5 text-xs text-slate-400">{{ $entry['notes'] }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center font-black {{ $isDebitEntry ? 'text-rose-600' : 'text-emerald-600' }}"
                                        dir="ltr">
                                        {{ number_format($amount, 0) }} {{ $isDebitEntry ? '(-)' : '(+)' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 font-medium text-center text-slate-400 bg-slate-50/50">
                                        لا توجد حركات مالية مسجلة لهذا العميل.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Financial Totals -->
                    <div
                        class="flex flex-col gap-4 justify-between items-center p-4 border-t bg-slate-50 border-slate-200 sm:flex-row">
                        <div class="flex gap-2 items-center">
                            <span class="text-sm font-bold text-slate-500">إجمالي الدفعات المسددة:</span>
                            <span class="text-lg font-black text-emerald-600" dir="ltr">{{ $sumTotalCredit }} ر.ي</span>
                        </div>
                        <div
                            class="flex gap-2 items-center px-4 py-2 bg-white rounded-xl border shadow-sm border-slate-200">
                            <span class="font-bold text-slate-500">الرصيد المتبقي:</span>
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
                <h2 class="flex gap-2 items-center mb-4 text-lg font-black text-slate-800">
                    <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                    تفاصيل الطرود (الشحنات)
                </h2>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="w-full text-sm text-right premium-table">
                        <thead>
                            <tr>
                                <th class="w-12 text-center">#</th>
                                <th class="w-28">التاريخ</th>
                                <th class="w-28">رقم السند</th>
                                <th>نوع الطرد</th>
                                <th class="w-24 text-center">نوع الطرد</th>
                                <th class="w-28 text-center">طريقة الدفع</th>
                                <th class="w-28 text-center">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipmentRows as $shipment)
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="font-bold text-center text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="text-slate-600" dir="ltr">{{ $shipment['date'] ?? '---' }}</td>
                                    <td class="font-bold text-slate-700" dir="ltr">{{ $shipment['bond_number'] ?? '---' }}</td>
                                    <td>
                                        <div class="font-bold text-slate-800">{{ $shipment['package_type'] ?? '---' }}</div>
                                        @if(!empty($shipment['weight']))
                                            <div class="mt-0.5 text-xs text-slate-400">الوزن: {{ $shipment['weight'] }}</div>
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
                                    <td class="text-xs font-bold text-center text-slate-600">
                                        {{ $shipment['payment_method'] ?? '---' }}</td>
                                    <td class="font-black text-center text-slate-800" dir="ltr">
                                        {{ is_numeric($shipment['total_amount'] ?? null) ? number_format((float) $shipment['total_amount'], 0) : ($shipment['total_amount'] ?? '0') }}
                                        ر.ي
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 font-medium text-center text-slate-400 bg-slate-50/50">
                                        لا توجد شحنات مسجلة لهذا العميل.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Shipment Stats -->
                    <div
                        class="grid grid-cols-3 gap-4 p-4 text-center border-t divide-x divide-x-reverse bg-slate-50 border-slate-200 divide-slate-200">
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">إجمالي الطرود</p>
                            <p class="text-lg font-black text-slate-800">{{ $sumTotalShipments }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">الطرود المرسلة</p>
                            <p class="text-lg font-black text-slate-800">{{ $sumSentCount }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-bold uppercase text-slate-400">الطرود المستلمة</p>
                            <p class="text-lg font-black text-slate-800">{{ $sumReceivedCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Signatures -->
            <div class="grid grid-cols-3 gap-8 mt-12 mb-4">
                <div class="text-center">
                    <p class="mb-8 text-sm font-bold text-slate-500">توقيع المحاسب</p>
                    <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
                </div>
                <div class="text-center">
                    <p class="mb-8 text-sm font-bold text-slate-500">توقيع العميل</p>
                    <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
                </div>
                <div class="text-center">
                    <p class="mb-8 text-sm font-bold text-slate-500">ختم الفرع</p>
                    <div class="mx-auto w-3/4 border-b border-dashed border-slate-300"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-800 p-4 text-center rounded-b-[2rem]">
            {{-- بيانات الفاتورة --}}
            <p class="text-xs font-medium text-slate-300">
    تم الإنشاء إلكترونياً عبر نظام <span class="font-black text-white">مُرسَل</span> | بواسطة:
    {{ $creator_name ?? 'مسؤول النظام' }} | الطباعة: {{ $print_date ?? str_replace(['AM', 'PM'], ['صباحاً', 'مساءً'], now()->timezone('Asia/Aden')->format('Y-m-d h:i A')) }}
</p>

            {{-- الخط الفاصل التسويقي لشركة تيار --}}
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