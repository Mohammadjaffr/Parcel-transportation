<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 7mm 7mm 9mm 7mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: {!! $design['font_family'] ?? "'aealarabiya', 'dejavusans', sans-serif" !!};
            direction: rtl;
            margin: 0;
            padding: 0;
            color: #1e293b;
            font-size: 8.2pt;
            line-height: 1.35;
            background: #ffffff;
        }

        .page {
            width: 100%;
        }

        .text-brand {
            color: {{ $design['primary_color'] ?? '#fb6514' }};
        }

        .danger {
            color: #dc2626;
            font-weight: bold;
        }

        .success {
            color: #16a34a;
            font-weight: bold;
        }

        .muted {
            color: #64748b;
        }

        .soft-text {
            color: #64748b;
            font-size: 6.8pt;
            line-height: 1.25;
            font-weight: normal;
        }

        .ltr {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
            font-family: dejavusans, sans-serif;
        }

        .money {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
            white-space: nowrap;
            font-family: dejavusans, sans-serif;
            font-weight: bold;
        }

        .nowrap {
            white-space: nowrap;
        }

        .wrap {
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid {{ $design['primary_color'] ?? '#fb6514' }};
            margin-bottom: 8px;
            padding-bottom: 6px;
        }

        .brand-name {
            color: {{ $design['primary_color'] ?? '#fb6514' }};
            font-size: 17pt;
            font-weight: bold;
            line-height: 1.1;
            margin: 0;
        }

        .brand-subtitle {
            color: #1e293b;
            font-size: 8pt;
            font-weight: bold;
            margin-top: 2px;
        }

        .print-date {
            margin-top: 5px;
            color: #475569;
            font-size: 7pt;
        }

        .document-title-box {
            background-color: #1e293b;
            color: #ffffff;
            padding: 5px 24px;
            font-size: 10pt;
            font-weight: bold;
            display: inline-block;
            margin-top: 5px;
            border-radius: 2px;
        }

        .company-side {
            font-size: 7.6pt;
            line-height: 1.55;
            color: #334155;
            font-weight: bold;
        }

        .company-phone {
            color: #475569;
            font-size: 7pt;
            font-weight: normal;
        }

        .customer-box {
            width: 100%;
            border: 1px solid #dbe3ef;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .customer-box td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .label {
            color: #64748b;
            font-size: 7pt;
            font-weight: bold;
            white-space: nowrap;
        }

        .value {
            color: #0f172a;
            font-size: 8.5pt;
            font-weight: bold;
        }

        .section-title {
            border-right: 4px solid {{ $design['primary_color'] ?? '#fb6514' }};
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
            padding: 5px 8px;
            font-size: 9pt;
            font-weight: bold;
            margin-top: 9px;
            margin-bottom: 5px;
            background: #ffffff;
            page-break-after: avoid;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 7px;
            table-layout: fixed;
            page-break-inside: auto;
        }

        .table thead {
            display: table-header-group;
        }

        .table tfoot {
            display: table-footer-group;
        }

        .table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .table th {
            background-color: #1e293b;
            color: #ffffff;
            border: 1px solid #0f172a;
            padding: 5px 3px;
            font-size: 7.2pt;
            text-align: center;
            font-weight: bold;
            line-height: 1.25;
        }

        .table td {
            border: 1px solid #dbe3ef;
            padding: 4px 4px;
            font-size: 7.15pt;
            text-align: center;
            vertical-align: middle;
            line-height: 1.3;
        }

        .table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .financial-desc {
            text-align: right;
            color: #64748b;
            font-size: 6.7pt;
            line-height: 1.22;
            font-weight: normal;
        }

        .package-type {
            color: #334155;
            font-size: 7pt;
            font-weight: bold;
            line-height: 1.25;
        }

        .summary-strip {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            margin-bottom: 8px;
        }

        .summary-strip td {
            border: 1px solid #dbe3ef;
            background-color: #f8fafc;
            padding: 6px 8px;
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
        }

        .summary-strip .summary-label {
            color: #64748b;
            font-size: 7pt;
            font-weight: bold;
        }

        .summary-strip .summary-value {
            font-size: 9pt;
            font-weight: bold;
        }

        .muted-box {
            text-align: center;
            border: 1px dashed #cbd5e1;
            padding: 12px;
            color: #94a3b8;
            font-size: 8.5pt;
            margin-bottom: 8px;
        }

        .signatures-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .sig-title {
            font-size: 8.2pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 24px;
            text-align: center;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 8px;
            padding-top: 6px;
            text-align: center;
            font-size: 7pt;
            color: #94a3b8;
        }
    </style>
</head>

<body>
<div class="page">

    {{-- Header --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="35%" valign="top" align="right">
                <div class="brand-name">{{ $company['name'] ?? 'اسم الشركة' }}</div>
                <div class="brand-subtitle">للنقل والشحن السريع</div>

                <div class="print-date">
                    تاريخ الطباعة:
                    <span class="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                </div>
            </td>

            <td width="30%" valign="top" align="center">
                @if(!empty($company['logo']))
                    <img src="{{ $company['logo'] }}" height="56" alt="Logo">
                    <div style="height: 4px;"></div>
                @endif

                <div class="document-title-box">{{ $title ?? 'كشف حساب عميل شامل' }}</div>
            </td>

            <td width="35%" valign="top" align="center" class="company-side">
                @if(!empty($company['main_branch']))
                    <div>{{ $company['main_branch']['title'] }}</div>
                    <div class="company-phone">
                        <span class="ltr">{{ $company['main_branch']['phones'] }}</span>
                    </div>
                @endif

                @if(!empty($company['headquarters']))
                    <div style="margin-top: 4px;">{{ $company['headquarters']['title'] }}</div>
                    <div class="company-phone">
                        <span class="ltr">{{ $company['headquarters']['phones'] }}</span>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Customer Info --}}
    <table class="customer-box" cellpadding="0" cellspacing="0">
        <tr>
            <td width="14%" class="label">اسم العميل</td>
            <td width="28%" class="value">{{ $customer['name'] ?? '---' }}</td>

            <td width="13%" class="label">رقم الهاتف</td>
            <td width="20%" class="value">
                <span class="ltr">{{ $customer['phone'] ?? '---' }}</span>
            </td>

            <td width="10%" class="label">المكتب / الفرع</td>
            <td width="15%" class="value">{{ $customer['branch'] ?? '---' }}</td>
        </tr>
    </table>

    {{-- Financial Statement --}}
    <div class="section-title">أولاً: كشف الحركات المالية</div>

    @if(!empty($statement['entries']) && count($statement['entries']) > 0)
        <table class="table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="6%">#</th>
                    <th width="13%">التاريخ</th>
                    <th width="16%">نوع الحركة</th>
                    <th width="47%">البيان</th>
                    <th width="18%">المبلغ</th>
                </tr>
            </thead>

            <tbody>
                @foreach($statement['entries'] as $entry)
                    @php
                        $amount = (float)($entry['debit'] ?? 0) > 0
                            ? (float)($entry['debit'] ?? 0)
                            : (float)($entry['credit'] ?? 0);

                        $isDebit = (float)($entry['debit'] ?? 0) > 0;
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            <span class="ltr">{{ $entry['date_formatted'] ?? '---' }}</span>
                        </td>

                        <td class="wrap">
                            {{ $entry['movement_type'] ?? '---' }}
                        </td>

                        <td class="financial-desc">
                            {{ $entry['description'] ?? '---' }}

                            @if(!empty($entry['reference']) && $entry['reference'] !== '---')
                                <span class="muted">
                                    - مرجع:
                                    <span class="ltr">{{ $entry['reference'] }}</span>
                                </span>
                            @endif

                            @if(!empty($entry['notes']))
                                <br>
                                <span>{{ $entry['notes'] }}</span>
                            @endif
                        </td>

                        <td class="{{ $isDebit ? 'danger' : 'success' }}">
                            <span class="money">{{ number_format($amount, 0) }} ر.ي</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-strip" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%" align="right">
                    <span class="summary-label">إجمالي المدفوع:</span>
                    <span class="summary-value success">
                        <span class="money">{{ $summary['total_credit'] ?? 0 }} ر.ي</span>
                    </span>
                </td>

                <td width="50%" align="left">
                    <span class="summary-label">المتبقي عليه:</span>
                    <span class="summary-value {{ ($summary['is_debtor'] ?? false) ? 'danger' : 'success' }}">
                        <span class="money">{{ $summary['final_balance'] ?? 0 }} ر.ي</span>
                    </span>
                </td>
            </tr>
        </table>
    @else
        <div class="muted-box">
            لا توجد حركات مالية مسجلة لهذا العميل.
        </div>

        <table class="summary-strip" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%" align="right">
                    <span class="summary-label">إجمالي المدفوع:</span>
                    <span class="summary-value success">
                        <span class="money">{{ $summary['total_credit'] ?? 0 }} ر.ي</span>
                    </span>
                </td>

                <td width="50%" align="left">
                    <span class="summary-label">المتبقي عليه:</span>
                    <span class="summary-value {{ ($summary['is_debtor'] ?? false) ? 'danger' : 'success' }}">
                        <span class="money">{{ $summary['final_balance'] ?? 0 }} ر.ي</span>
                    </span>
                </td>
            </tr>
        </table>
    @endif

    {{-- Shipments --}}
    <div class="section-title">ثانياً: تفاصيل الطرود المرتبطة بالعميل</div>

    @if(!empty($shipments) && count($shipments) > 0)
        <table class="table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th width="6%">#</th>
                    <th width="13%">التاريخ</th>
                    <th width="31%">نوع الطرد</th>
                    <th width="18%">طريقة الدفع</th>
                    <th width="14%">مرسل / مستقبل</th>
                    <th width="18%">المبلغ</th>
                </tr>
            </thead>

            <tbody>
                @foreach($shipments as $shipment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            <span class="ltr">{{ $shipment['date'] ?? '---' }}</span>
                        </td>

                        <td class="text-right">
                            <div class="package-type">
                                {{ $shipment['package_type'] ?? '---' }}

                                @if(!empty($shipment['weight']))
                                    <span class="soft-text">
                                        - {{ $shipment['weight'] }}
                                    </span>
                                @endif
                            </div>

                            @if(!empty($shipment['bond_number']) && $shipment['bond_number'] !== '---')
                                <div class="soft-text">
                                    سند:
                                    <span class="ltr">{{ $shipment['bond_number'] }}</span>
                                </div>
                            @endif
                        </td>

                        <td class="soft-text">
                            {{ $shipment['payment_method'] ?? '---' }}
                        </td>

                        <td class="nowrap">
                            {{ $shipment['direction_label'] ?? '---' }}
                        </td>

                        <td>
                            <span class="money">{{ number_format((float)($shipment['total_amount'] ?? 0), 0) }} ر.ي</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-strip" cellpadding="0" cellspacing="0">
            <tr>
                <td width="33.3%" align="right">
                    <span class="summary-label">إجمالي الطرود:</span>
                    <span class="summary-value">
                        <span class="ltr">{{ $summary['total_shipments'] ?? 0 }}</span>
                    </span>
                </td>

                <td width="33.4%" align="center">
                    <span class="summary-label">عدد المرسلة:</span>
                    <span class="summary-value">
                        <span class="ltr">{{ $summary['sent_count'] ?? 0 }}</span>
                    </span>
                </td>

                <td width="33.3%" align="left">
                    <span class="summary-label">عدد المستلمة:</span>
                    <span class="summary-value">
                        <span class="ltr">{{ $summary['received_count'] ?? 0 }}</span>
                    </span>
                </td>
            </tr>
        </table>
    @else
        <div class="muted-box">
            لا توجد طرود مرتبطة بهذا العميل.
        </div>

        <table class="summary-strip" cellpadding="0" cellspacing="0">
            <tr>
                <td width="33.3%" align="right">
                    <span class="summary-label">إجمالي الطرود:</span>
                    <span class="summary-value"><span class="ltr">0</span></span>
                </td>

                <td width="33.4%" align="center">
                    <span class="summary-label">عدد المرسلة:</span>
                    <span class="summary-value"><span class="ltr">0</span></span>
                </td>

                <td width="33.3%" align="left">
                    <span class="summary-label">عدد المستلمة:</span>
                    <span class="summary-value"><span class="ltr">0</span></span>
                </td>
            </tr>
        </table>
    @endif

    {{-- Signatures --}}
    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="33.3%" align="center">
                <div class="sig-title">توقيع المحاسب</div>
                ______________________
            </td>

            <td width="33.4%" align="center">
                <div class="sig-title">توقيع العميل</div>
                ______________________
            </td>

            <td width="33.3%" align="center">
                <div class="sig-title">ختم الفرع</div>
                ______________________
            </td>
        </tr>
    </table>

    <div class="footer">
        تم إنشاء هذا الكشف إلكترونياً عبر نظام {{ $company['name'] ?? 'الشركة' }}
        &nbsp;|&nbsp;
        بواسطة: {{ $creator_name ?? 'مسؤول النظام' }}
    </div>

</div>
</body>

</html>