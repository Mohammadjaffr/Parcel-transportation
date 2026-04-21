<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: {!! $design['font_family'] ?? "'aealarabiya', 'dejavusans', sans-serif" !!};
            direction: rtl;
            margin: 0;
            padding: 0;
            color: #334155;
            font-size: 10pt;
            line-height: 1.6;
            background: #ffffff;
        }

        .text-brand {
            color: {{ $design['primary_color'] ?? '#fb6514' }};
        }

        .bg-brand {
            background-color: {{ $design['primary_color'] ?? '#fb6514' }};
        }

        .text-dark {
            color: #0f172a;
        }

        .text-muted {
            color: #64748b;
        }

        .text-light {
            color: #94a3b8;
        }

        .bg-soft {
            background-color: {{ $design['bg_color'] ?? '#fff4ee' }};
        }

        .bg-slate {
            background-color: #1e293b;
        }

        .font-bold {
            font-weight: bold;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid {{ $design['primary_color'] ?? '#fb6514' }};
            margin-bottom: 18px;
            padding-bottom: 10px;
        }

        .brand-name {
            color: {{ $design['primary_color'] ?? '#fb6514' }};
            font-size: 23pt;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }

        .brand-subtitle {
            color: #334155;
            font-size: 11pt;
            font-weight: bold;
            margin-top: 3px;
        }

        .document-title-box {
            background-color: #1e293b;
            color: #fff;
            padding: 6px 15px;
            font-size: 12pt;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            margin-top: 8px;
        }

        .info-card {
            width: 100%;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            margin-bottom: 18px;
            border-radius: 6px;
        }

        .info-card-inner {
            padding: 12px;
        }

        .label {
            color: #64748b;
            font-size: 9pt;
        }
        
        .label-ltr {
            color: #64748b;
            font-size: 9pt;
            direction: ltr;
            display: inline-block;
        }

        .value {
            color: #0f172a;
            font-size: 11pt;
            font-weight: bold;
        }

        .value-ltr {
            color: #0f172a;
            font-size: 11pt;
            font-weight: bold;
            direction: ltr;
            display: inline-block;
        }

        .section-title {
            background-color: #f8fafc;
            border-right: 5px solid {{ $design['primary_color'] ?? '#fb6514' }};
            color: #0f172a;
            padding: 8px 12px;
            font-size: 11pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .table th {
            background-color: #1e293b;
            color: #ffffff;
            border: 1px solid #0f172a;
            padding: 9px 5px;
            font-size: 9pt;
            text-align: center;
            font-weight: bold;
        }

        .table td {
            border: 1px solid #e2e8f0;
            padding: 8px 5px;
            font-size: 8.8pt;
            text-align: center;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .danger {
            color: #dc2626;
            font-weight: bold;
        }

        .success {
            color: #16a34a;
            font-weight: bold;
        }

        .muted-box {
            text-align: center;
            border: 1.5px dashed #cbd5e1;
            padding: 18px;
            color: #94a3b8;
            font-size: 10pt;
            margin-bottom: 15px;
        }

        .signatures-table {
            width: 100%;
            margin-top: 40px;
        }

        .sig-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 40px;
            text-align: center;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 18px;
            padding-top: 10px;
            text-align: center;
            font-size: 8.5pt;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    {{-- الهيدر --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="35%" valign="top" align="right">
                <div class="brand-name">{{ $company['name'] ?? 'اسم الشركة' }}</div>
                <div class="brand-subtitle">للنقل والشحن السريع</div>
                <div class="label" style="margin-top: 8px;">
                    تاريخ الطباعة:
                    <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span>
                </div>
            </td>

            <td width="30%" valign="top" align="center">
                @if(!empty($company['logo']))
                    <img src="{{ $company['logo'] }}" height="72" alt="Logo">
                    <div style="height: 10px;"></div>
                @endif

                <div class="document-title-box">{{ $title ?? 'كشف حمولة الرسائل' }}</div>
            </td>

            <td width="35%" valign="top" align="center" style="line-height: 1.7;">
                @if(!empty($company['main_branch']))
                    <div class="value">{{ $company['main_branch']['title'] }}</div>
                    <div class="label" style="margin-top: 2px;">
                        <span dir="ltr">{{ $company['main_branch']['phones'] }}</span>
                    </div>
                @endif

                @if(!empty($company['headquarters']))
                    <div class="label" style="margin-top: 6px;">{{ $company['headquarters']['title'] }}</div>
                    <div class="label">
                        <span dir="ltr">{{ $company['headquarters']['phones'] }}</span>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- بيانات الرحلة / الإرسالية --}}
    <div class="info-card">
        <div class="info-card-inner">
            <table width="100%" cellpadding="4" cellspacing="0">
                <tr>
                    <td width="15%" class="label">رقم الرحلة/المنيفست:</td>
                    <td width="35%" class="value">{{ $package_number ?? '---' }}</td>

                    <td width="15%" class="label">تاريخ الرحلة:</td>
                    <td width="35%" class="value-ltr">{{ $date ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="label">اسم السائق:</td>
                    <td class="value">{{ $driver_name ?? '---' }}</td>

                    <td class="label">رقم الجوال:</td>
                    <td class="value-ltr">{{ $driver_phone ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="label">فرع الإرسال:</td>
                    <td class="value">{{ $package_sender_branch ?? '---' }}</td>

                    <td class="label">إجمالي الطرود:</td>
                    <td class="value" style="color: {{ $design['primary_color'] ?? '#fb6514' }};">{{ $total_shipments ?? 0 }} طرد</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- جدول الشحنات --}}
    <div class="section-title">تفاصيل الشحنات المرفقة بالرحلة</div>

    @if(!empty($shipments) && count($shipments) > 0)
        <table class="table" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th width="4%">#</th>
                    <th width="12%">رقم السند/التتبع</th>
                    <th width="16%">بيانات المرسل</th>
                    <th width="16%">بيانات المستلم</th>
                    <th width="12%">الوجهة</th>
                    <th width="10%">الدفع</th>
                    <th width="10%">المبلغ</th>
                    <th width="20%">البيان والملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $shipment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-bold text-brand" style="font-size: 9.5pt;">
                            {{ $shipment['bond_number'] }}
                            @if($shipment['tracking_code'] && $shipment['tracking_code'] !== '---')
                                <br><span class="label" style="font-size: 8pt;">{{ $shipment['tracking_code'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="font-bold">{{ $shipment['sender_name'] }}</div>
                            <div class="label-ltr">{{ $shipment['sender_phone'] }}</div>
                        </td>
                        <td>
                            <div class="font-bold">{{ $shipment['receiver_name'] }}</div>
                            <div class="label-ltr">{{ $shipment['receiver_phone'] }}</div>
                        </td>
                        <td>
                            <div class="font-bold">{{ $shipment['receiver_branch'] }}</div>
                        </td>
                        <td>
                            <div class="label" style="font-size: 8.5pt;">{{ $shipment['payment_method'] }}</div>
                        </td>
                        <td>
                            <div class="font-bold">{{ $shipment['total_amount'] }}</div>
                            @if((int)str_replace(',', '', $shipment['partial_amount']) > 0)
                                <div class="label" style="font-size: 7.5pt; color: #16a34a;">مدفوع: {{ $shipment['partial_amount'] }}</div>
                                <div class="label" style="font-size: 7.5pt; color: #dc2626;">متبقي: {{ $shipment['remaining_amount'] }}</div>
                            @endif
                        </td>
                        <td class="text-right" style="font-size: 8.5pt;">
                            @if($shipment['package_type'] || $shipment['weight'])
                                <div>
                                    <span class="font-bold">النوع:</span> {{ $shipment['package_type'] }}
                                    @if($shipment['weight']) | {{ $shipment['weight'] }} @endif
                                </div>
                            @endif
                            
                            @if($shipment['honey_details'])
                                <div style="color: #d97706; font-weight: bold; margin-top: 3px;">{{ $shipment['honey_details'] }}</div>
                            @endif

                            @if($shipment['notes'])
                                <div class="text-muted" style="margin-top: 3px;">{{ $shipment['notes'] }}</div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="muted-box">
            لا توجد شحنات مسجلة ضمن هذه الرحلة
        </div>
    @endif

    {{-- التوقيعات --}}
    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="33.3%" align="center">
                <div class="sig-title">توقيع مسؤول الحركة (المرسل)</div>
                ______________________
            </td>
            <td width="33.4%" align="center">
                <div class="sig-title">توقيع السائق (المستلم للعهدة)</div>
                ______________________
            </td>
            <td width="33.3%" align="center">
                <div class="sig-title">توقيع فرع الوصول (الاستلام)</div>
                ______________________
            </td>
        </tr>
    </table>

    {{-- الفوتر --}}
    <div class="footer">
        تم إنشاء هذا المستند إلكترونياً عبر نظام {{ $company['name'] ?? 'الشركة' }}
        &nbsp;|&nbsp;
        بواسطة: {{ $creator_name ?? 'مسؤول النظام' }}
    </div>

</body>

</html>