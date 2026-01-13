<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        /* إعدادات الخط والصفحة */
        body {
            font-family: 'aealarabiya', 'dejavusans', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.4;
        }

        @page {
            margin: 10mm;
        }

        /* الهيدر الاحترافي */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #fb6514;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .brand-name {
            color: #fb6514;
            font-size: 26pt;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }

        .brand-subtitle {
            color: #333;
            font-size: 12pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .document-title-badge {
            background-color: #333;
            color: #fff;
            padding: 4px 15px;
            font-size: 11pt;
            display: inline-block;
            margin-top: 10px;
            border-radius: 4px;
        }

        .header-info-text {
            font-size: 9pt;
            color: #555;
            line-height: 1.6;
        }

        .header-phones {
            color: #fb6514;
            font-weight: bold;
            font-size: 10pt;
            margin-top: 5px;
        }

        /* بيانات الرحلة */
        .trip-info-box {
            width: 100%;
            background-color: #fcfcfc;
            border: 1px solid #eee;
            /* border-right: 2px solid #fb6514; */
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .trip-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .label {
            color: #fb6514;
            font-weight: bold;
            width: 90px;
            font-size: 10pt;
        }

        .value {
            font-weight: bold;
            font-size: 11pt;
            color: #222;
        }

        /* جدول الطرود */
        .manifest-table {
            width: 100%;
            border-collapse: collapse;
        }

        .manifest-table th {
            background-color: #333;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 1px solid #333;
            padding: 8px 4px;
            font-size: 9.5pt;
        }

        .manifest-table td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
            font-size: 9pt;
            vertical-align: middle;
        }

        .manifest-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* قسم التواقيع المصغر - 3 أعمدة */
        .signatures-container {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        .sig-cell {
            width: 33.33%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }

        .sig-title {
            font-size: 10pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 35px;
            display: block;
        }

        .sig-line {
            width: 80%;
            margin: 10px auto;
        }

        .notes-cell {
            font-size: 8pt;
            color: #666;
            max-width: 150px;
        }

        .total-summary {
            margin-top: 15px;
            text-align: left;
            font-weight: bold;
            font-size: 11pt;
            padding: 5px 10px;
            background-color: #eee;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td width="35%" style="vertical-align: top;">
                <h1 class="brand-name">الـزاجـل</h1>
                <div class="brand-subtitle">للنقل والشحن السريع</div>
                <div class="document-title-badge">كشف حمولة الرسائل</div>
                <div style="font-size: 8.5pt; margin-top: 8px; color: #666;">
                    تاريخ الطباعة: {{ date('Y-m-d H:i') }}<br>
                    رقم التتبع: <span style="color:#000; font-weight:bold;">{{ $package->tracking_number }}</span>
                </div>
            </td>

            <td width="30%" style="text-align: center; vertical-align: middle;">
                <img src="{{ public_path('images/new.svg') }}" style="width: 90px; height: auto;">
            </td>

            <td width="35%" style="text-align: left; vertical-align: top;" class="header-info-text">
                <div style="font-weight: bold; color: #222; font-size: 10pt;">الفرع الرئيسي / حضرموت - القطن</div>
                <div style="margin-top: 2px;">خدمة الشحن إلى جميع المحافظات ودول الخليج</div>
                <div class="header-phones">
                    781216757 - 730831802<br>
                    773136727 - 781989021
                </div>
            </td>
        </tr>
    </table>

    <div class="trip-info-box">
        <table class="trip-info-table">
            <tr>
                <td class="label">اسم السائق:</td>
                <td class="value">{{ $package->driver_name }}</td>
                <td class="label">رقم الجوال:</td>
                <td class="value" style="direction: ltr; text-align: right;">{{ $package->driver_phone }}</td>
            </tr>
            <tr>
                <td class="label">فرع المصدر:</td>
                <td class="value">{{ auth()->user()->branch_name ?? 'المركز الرئيسي' }}</td>
                <td class="label">إجمالي الطرود:</td>
                <td class="value">{{ $package->shipments->count() }} طرد صادر</td>
            </tr>
        </table>
    </div>

    <table class="manifest-table">
        <thead>
            <tr>
                <th width="10%">السند</th>
                <th width="11%">المرسل</th>
                <th width="13%">المستلم</th>
                <th width="10%">جوال المستلم</th>
                <th width="12%">من</th>
                <th width="10%">إلى</th>
                <th width="13%">نوع الطرد</th>
                <th width="10%">المبلغ</th>
                <th width="11%">ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($package->shipments as $shipment)
                <tr>
                    <td style="font-weight: bold; color: #fb6514;">#{{ $shipment->bond_number }}</td>
                    <td>{{ $shipment->senderCustomer->name }}</td>
                    <td style="font-weight: bold;">{{ $shipment->receiverCustomer->name }}</td>
                    <td style="direction: ltr;">{{ $shipment->receiverCustomer->phone }}</td>
                    <td>{{ $shipment->senderBranch->name }}</td>
                    <td style="font-weight: bold; background-color: #fff4ee;">{{ $shipment->receiverBranch->name }}
                    </td>
                    <td>{{ $shipment->package_type }}</td>
                    <td>
                        @switch($shipment->payment_method)
                            @case('prepaid')
                                <span class="text-success-500 font-bold">مدفوع</span>
                            @break

                            @case('cod')
                                  
                                        الإجمالي:
                                        <span class="font-bold">
                                            {{ number_format($shipment->total_amount, 0) }}
                                        </span>
                                            @break

                            @case('partial_payment')
                                 
                                        الإجمالي:
                                        <span class="font-bold">
                                            {{ number_format($shipment->total_amount, 0) }}
                                        </span>

                                    <div class="text-success-500">
                                        مدفوع:
                                        {{ number_format($shipment->partial_amount, 0) }}
                                    </div>

                                        المتبقي:
                                        {{ number_format($shipment->total_amount - $shipment->partial_amount, 0) }}
                            @break

                            @case('customer_credit')
                                <span class="text-orange-500 font-bold">على الحساب</span>
                            @break
                        @endswitch
                    </td>

                    <td class="notes-cell">{{ $shipment->notes ?? '-' }}</td>
                </tr>
            @endforeach
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
        style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #999; border-top: 1px solid #eee; padding-top: 5px;">
        نظام الزاجل الذكي - طبع بواسطة: {{ auth()->user()->name }} - التاريخ: {{ date('Y-m-d H:i') }}
    </div>

</body>

</html>
