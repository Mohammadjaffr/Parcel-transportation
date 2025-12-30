<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        /* إعدادات الطباعة لمقاس 10x7 سم */
        @page {
            size: 100mm 70mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100mm;
            height: 70mm;
            background-color: #fff;
            font-family: 'aealarabiya', 'dejavusans', sans-serif;
            direction: rtl;
            overflow: hidden;
        }

        .sticker-card {
            width: 100mm;
            height: 70mm;
            padding: 3mm 4mm;
            position: relative;
        }

        /* الهيدر: اللوجو والبيانات الأساسية */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #fb6514;
            margin-bottom: 3mm;
        }

        .logo-cell {
            width: 18mm;
            vertical-align: middle;
            padding-bottom: 1mm;
        }

        .brand-cell {
            text-align: center;
            vertical-align: middle;
        }

        .contact-cell {
            width: 42mm;
            text-align: right;
            vertical-align: middle;
            font-size: 7.5pt;
            font-weight: bold;
            line-height: 1.3;
            color: #333;
        }

        .brand-title {
            color: #fb6514;
            font-size: 19pt;
            font-weight: 900;
            margin: 0;
            line-height: 1;
        }

        .date-text {
            font-size: 9pt;
            color: #666;
            font-weight: bold;
            margin-top: 1mm;
        }

        /* منطقة البيانات: توزيع أفقي Label بجانب Value */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2mm;
        }

        .info-grid td {
            padding: 2.5mm 0;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
        }

        .label {
            width: 25mm;
            font-size: 10pt;
            font-weight: bold;
            color: #fb6514;
        }

        .value {
            font-size: 13pt;
            font-weight: 900;
            color: #000;
        }

        /* منطقة الوجهة والرمز: صف واحد عرضي مدمج */
        .horizontal-footer {
            display: table;
            width: 100%;
            /* background: #f9f9f9; border: 1.2pt solid #fb6514; border-radius: 2.5mm; */
            position: absolute;
            bottom: 8mm;
            left: 4mm;
            right: 4mm;
            width: calc(100% - 8mm);
        }

        .footer-item {
            display: table-cell;
            vertical-align: middle;
            padding: 2mm;
            text-align: center;
        }

        /* .dest-box { width: 65%; border-left: 1pt solid #fb6514; text-align: right; padding-right: 3mm; } */
        .dest-text {
            font-size: 13pt;
            font-weight: 900;
            color: #000;
        }

        .code-box-small {
            font-size: 16pt;
            font-weight: 900;
            color: #fff;
            background: #000;
            padding: 1mm 4mm;
            border-radius: 1.5mm;
        }

        /* شريط الإدارة السفلي */
        .admin-footer {
            position: absolute;
            bottom: 2mm;
            left: 4mm;
            right: 4mm;
            background: #000;
            color: #fff;
            text-align: center;
            padding: 1.2mm;
            border-radius: 1mm;
            font-size: 8.5pt;
            font-weight: bold;
        }

        .hint {
            font-size: 7.5pt;
            color: #888;
            font-weight: bold;
            display: block;
            margin-bottom: 0.5mm;
        }
    </style>
</head>

<body>

    <div class="sticker-card">
        <table class="header-table">
            <tr>
                  <td class="contact-cell">
                {{-- <span class="logo-cell"><img src="{{ public_path('images/new.svg') }}" style="width: 15mm;"></span> --}}
                    فرع {{ $shipment->senderBranch->name }}<br>
                    للتواصل:<br>
                    781216757 - 730831802<br>
                    773136727 - 781989021
                    <br>
                    الى جميع المحافظات ودول الخليج
                    الفرع الرئيسي / حضرموت - القطن - المرقدة
                </td>
                <td class="brand-cell">
                    <div class="brand-title">الـزاجـل</div>
                    <div class="date-text">تاريخ السند: {{ $shipment->created_at->format('Y-m-d') }}</div>
                </td>
              
            </tr>
        </table>

        <table class="info-grid">
            <tr>
                <td class="label">رقم السند:</td>
                <td class="value">#{{ $shipment->bond_number }}</td>
            </tr>
            <tr>
                <td class="label">نوع الشحنة:</td>
                <td class="value">{{ $shipment->package_type }}</td>
            </tr>
            <tr>
                <td class="label">رمز الشحنة:</td>

                <td class="value">{{ $shipment->code }}</td>
            </tr>
        </table>

        <div class="horizontal-footer">
            <div class="footer-item dest-box">
                <span class="hint">الوجهة المقصودة:</span>
                <div class="dest-text">
                    <span style="color: #fb6514; ">من</span>

                    {{ $shipment->senderBranch->name }}
                    <span style="color: #fb6514; ">الى</span>
                    {{ $shipment->receiverBranch->name }}
                </div>
            </div>
            <div class="footer-item">
                <div>أرقام الإدارة العامة: 781216757 - 773136727 - 730831802</div>

            </div>
        </div>

    </div>

</body>

</html>
