<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        /* إعدادات الخط والصفحة */
        body {
            font-family: {!! $design['font_family'] ?? "'aealarabiya', 'dejavusans', sans-serif" !!};
            direction: rtl;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.4;
        }

        @page {
            margin: 10mm;
        }

        /* الهيدر الاحترافي متوافق مع TCPDF */
        .header-table {
            width: 100%;
            border-bottom: 3px solid {{ $design['primary_color'] ?? '#fb6514' }};
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .brand-name {
            color: {{ $design['primary_color'] ?? '#fb6514' }};
            font-size: 45pt;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }

        .brand-subtitle {
            color: #333;
            font-size: 15pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .document-title-badge {
            background-color: {{ $design['secondary_color'] ?? '#333' }};
            color: #fff;
            padding: 4px 15px;
            font-size: 13pt;
            display: inline-block;
            margin-top: 10px;
            border-radius: 4px;
        }

        .header-info-text {
            font-size: 11pt;
            color: #555;
            line-height: 1.6;
        }

        .header-phones {
            color: {{ $design['primary_color'] ?? '#fb6514' }};
            font-weight: bold;
            font-size: 12pt;
            margin-top: 5px;
        }

        /* بيانات السند (تم تغييرها من بيانات رحلة) */
        .trip-info-box {
            width: 100%;
            background-color: {{ $design['bg_color'] ?? '#fcfcfc' }};
            border: 1px solid #eee;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .trip-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .label {
            color: {{ $design['primary_color'] ?? '#fb6514' }};
            font-weight: bold;
            width: 90px;
            font-size: 12pt;
        }

        .value {
            font-weight: bold;
            font-size: 13pt;
            color: #222;
        }

        /* جدول الطرود */
        .manifest-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .manifest-table th {
            background-color: {{ $design['secondary_color'] ?? '#333' }};
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 1px solid {{ $design['secondary_color'] ?? '#333' }};
            padding: 6px 2px;
            font-size: 9.5pt;
        }

        .manifest-table td {
            border: 1px solid #ddd;
            padding: 6px 2px;
            text-align: center;
            font-size: 9.5pt;
            vertical-align: middle;
            word-wrap: break-word;
        }

        /* قسم التواقيع المصغر - 3 أعمدة */
        .signatures-container {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }

        .sig-cell {
            width: 33.33%;
            text-align: center;
            padding: 5px;
            vertical-align: top;
        }

        .sig-title {
            font-size: 11pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 25px;
            display: block;
        }

        .sig-line {
            width: 70%;
            margin: 5px auto;
            border-bottom: 1px solid #000;
        }

        .notes-cell {
            font-size: 9.5pt;
            color: #666;
            text-align: right !important;
            /* لمنداخذ الملاحظات يمين */
            padding-right: 5px !important;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td width="35%" style="vertical-align: top;">
                <h1 class="brand-name">{{ $company['name'] }}</h1>
                <div class="brand-subtitle">للنقل والشحن السريع</div>
                <div class="document-title-badge">{{ $title }}</div>
                <div style="font-size: 9.5pt; margin-top: 8px; color: #666; line-height: 1.5;">
                    تاريخ الطباعة: <span dir="ltr">{{ $print_date }}</span><br>
                    رقم التتبع: <span
                        style="background-color: {{ $design['bg_color'] ?? '#ffd8b1' }}; padding: 1px 6px; border-radius: 4px; color:#000; font-weight:bold;">{{ $package_number }}</span>
                </div>
            </td>

            <td width="30%" align="center" valign="top">
                <img src="{{ $company['logo'] }}" height="85" alt="Logo" />

                <div style="height: 38px; line-height: 38px; ">&nbsp;</div>

                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 25px;"></td> 
                        <td align="center" style="width: 200px;">
                            <table border="1" cellpadding="3" style="border-color: #333333; width: 200px; border-collapse: collapse;">
                                <tr>
                                    <td align="center" style="font-weight: bold; font-size: 13pt; background-color: #ffffff; color: #333;">
                                        كشف الفرع
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 0px;"></td> 
                    </tr>
                </table>
            </td>

            <td width="35%" align="center" valign="top" style="line-height: 1.4;">
                @if (!empty($company['main_branch']))
                    <div style="font-size: 10pt; font-weight: bold; color: #333;">
                        {{ $company['main_branch']['title'] }}</div>
                    <div style="font-size: 10pt; color: #555; margin-bottom: 6px;">
                        <span dir="ltr" style="font-weight: bold;">{{ $company['main_branch']['phones'] }}</span>
                    </div>
                @endif

                @if (!empty($company['headquarters']))
                    <div
                        style="font-size: 10pt; font-weight: bold; color: {{ $design['primary_color'] ?? '#fb6514' }};">
                        {{ $company['headquarters']['title'] }}</div>
                    <div style="font-size: 11pt; margin-bottom: 6px;">
                        <span dir="ltr"
                            style="font-weight: bold; color: {{ $design['primary_color'] ?? '#fb6514' }};">{{ $company['headquarters']['phones'] }}</span>
                    </div>
                @endif

                @if (!empty($company['other_phones']))
                    <div style="font-size: 9pt; color: #777; margin-bottom: 6px;">
                        أرقام الفروع:
                        <span dir="ltr"
                            style="font-weight: bold; color: {{ $design['primary_color'] ?? '#fb6514' }}; line-height: 1.5;">{{ $company['other_phones'] }}</span>
                    </div>
                @endif

                <div style="font-size: 9pt; font-weight: bold; color: #333; margin-top: 8px;">خدمة الشحن إلى جميع
                    المحافظات ودول الخليج</div>
            </td>
        </tr>
    </table>

    <table
        style="width: 100%; border-top: 2px solid {{ $design['primary_color'] ?? '#fb6514' }}; margin-bottom: 10px; margin-top: -5px; padding-top: 5px;">
        <tr>
            <td width="50%" style="text-align: right; padding: 3px 5px; border: none;">
                <span
                    style="color: {{ $design['primary_color'] ?? '#fb6514' }}; font-weight: bold; font-size: 10pt;">اسم
                    السائق:</span>
                <span style="font-weight: bold; font-size: 11pt; color: #333;">{{ $driver_name }}</span>
            </td>
            <td width="50%" style="text-align: right; padding: 3px 5px; border: none;">
                <span
                    style="color: {{ $design['primary_color'] ?? '#fb6514' }}; font-weight: bold; font-size: 10pt;">رقم
                    الجوال:</span>
                <span dir="ltr"
                    style="font-weight: bold; font-size: 10pt; color: #333;">{{ $driver_phone }}</span>
            </td>
        </tr>
        <tr>
            <td width="50%" style="text-align: right; padding: 3px 5px; border: none;">
                <span
                    style="color: {{ $design['primary_color'] ?? '#fb6514' }}; font-weight: bold; font-size: 10pt;">فرع
                    المرسل:</span>
                <span style="font-weight: bold; font-size: 11pt; color: #333;">{{ $package_sender_branch }}</span>
            </td>
            <td width="50%" style="text-align: right; padding: 3px 5px; border: none;">
                <span
                    style="color: {{ $design['primary_color'] ?? '#fb6514' }}; font-weight: bold; font-size: 10pt;">إجمالي
                    الطرود:</span>
                <span style="font-weight: bold; font-size: 11pt; color: #333;">{{ $total_shipments }} <span
                        style="font-size: 9pt;">طرد صادر</span></span>
            </td>
        </tr>
    </table>

    <table class="manifest-table">
        <thead>
            <tr>
                <th width="12%">السند</th>
                <th width="10%">المرسل</th>
                <th width="10%">جوال المرسل</th>
                <th width="9%">المستلم</th>
                <th width="10%">جوال المستلم</th>
                <th width="9%">من</th>
                <th width="10%">إلى</th>
                <th width="10%">نوع الطرد</th>
                <th width="10%">المبلغ</th>
                <th width="10%">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $s)
                <tr>
                    <td style="font-weight: bold; color: {{ $design['primary_color'] ?? '#fb6514' }};" dir="ltr"
                        nowrap="nowrap">{{ $s['bond_number'] }}<br><span
                            style="font-size:10px;">{{ $s['tracking_code'] }}</span></td>
                    <td>{{ $s['sender_name'] }}</td>
                    <td style="direction: ltr;" nowrap="nowrap">{{ $s['sender_phone'] }}</td>

                    <td style="font-weight: bold;">{{ $s['receiver_name'] }}</td>
                    <td style="direction: ltr;" nowrap="nowrap">{{ $s['receiver_phone'] }}</td>

                    <td>{{ $s['sender_branch'] }}</td>
                    <td style="font-weight: bold; background-color: {{ $design['bg_color'] ?? '#fff4ee' }};">{{ $s['receiver_branch'] }}</td>

                    <td>
                        <div style="font-weight: bold;">{{ $s['package_type'] }}</div>
                        @if ($s['weight'])
                            <div style="font-size: 8pt; color: #666; margin-top:2px;">({{ $s['weight'] }})</div>
                        @endif
                    </td>

                    <td>
                        @if ($s['payment_key'] === 'prepaid')
                            <span style="font-weight: bold; color: green;">محاسب</span>
                        @elseif($s['payment_key'] === 'cod')
                            <div style="font-size: 9pt;">الإجمالي:</div>
                            <div style="font-weight: bold;">{{ $s['total_amount'] }}</div>
                        @elseif($s['payment_key'] === 'partial_payment')
                            <div style="font-size: 8pt;">الإجمالي: <span
                                    style="font-weight: bold;">{{ $s['total_amount'] }}</span></div>
                            <div style="font-size: 8pt; color: green;">م: {{ $s['partial_amount'] }}</div>
                            <div style="font-size: 8pt; color: red;">ب: {{ $s['remaining_amount'] }}</div>
                        @elseif($s['payment_key'] === 'customer_credit')
                            <span style="font-weight: bold; color: {{ $design['primary_color'] ?? '#fb6514' }};">على
                                الحساب</span>
                        @else
                            {{ $s['payment_method'] }}
                        @endif
                    </td>

                    <td class="notes-cell">
                        {{ $s['notes'] }}
                        @if ($s['honey_details'])
                            <div style="font-size: 8pt; color: #777; text-align: left; margin-top: 10px; width: 100%;">
                                {{ $s['honey_details'] }}
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="padding: 15px; font-weight: bold; color: #777;">لا توجد طرود في هذه
                        الإرسالية.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signatures-container">
        <tr>
            <td class="sig-cell">
                <span class="sig-title">توقيع السائق</span>
                <div class="sig-line"></div>
            </td>
            <td class="sig-cell">
                <span class="sig-title">مسؤول الصادر</span>
                <div class="sig-line"></div>
            </td>
            <td class="sig-cell">
                <span class="sig-title">ختم الفرع</span>
                <div class="sig-line" style="border: none;"></div>
            </td>
        </tr>
    </table>

    <div
        style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10pt; color: #999; border-top: 1px solid #eee; padding-top: 5px;">
        نظام {{ $company['name'] }} الذكي - طبع بواسطة: {{ $creator_name }} - التاريخ: <span
            dir="ltr">{{ $print_date }}</span>
    </div>

</body>

</html>
