<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: 70mm 120mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
            width: 70mm;
            height: 120mm;
            background: #fff;
            font-family: 'aealarabiya', 'dejavusans', sans-serif;
            direction: rtl;
        }

        .sticker-card {
            width: 70mm;
            height: 120mm;
            padding: 2mm 1mm;
        }

        /* ===== الهيدر ===== */
        .header-table {
            width: 100%;
            border-bottom: 1.2px solid #fb6514;
            margin-bottom: 0.5mm;
            table-layout: fixed;
        }

        .company-cell {
            width: 33%;
            font-size: 6.2pt;
            line-height: 1.2;
        }

        .header-title {
            font-size: 7.5pt;
            font-weight: bold;
        }

        .header-subtitle {
            font-size: 6pt;
            margin-top: .1mm;
        }

        .header-extra {
            font-size: 5pt;
            margin-top: .1mm;
        }

        .header-phones {
            font-size: 5pt;
            font-weight: bold;
            margin-top: .1mm;
            direction: ltr;
        }

        .logo-cell {
            width: 34%;
            text-align: center;
        }

        .logo-cell img {
            width: 10mm;
        }

        .branch-cell {
            width: 33%;
            text-align: left;
            font-size: 6.2pt;
            font-weight: bold;
            line-height: 1.2;
        }

        /* ===== الجداول ===== */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.5mm;
        }

        .info-grid td {
            padding: .25mm 0;
            border-bottom: 1px solid #eee;
        }

        .label {
            font-size: 6.3pt;
            font-weight: bold;
            color: #fb6514;
            white-space: nowrap;
        }

        .value {
            font-size: 8pt;
            font-weight: 900;
        }

        .value-small {
            font-size: 7pt;
            font-weight: 700;
        }

        /* ===== الإدارة العامة (مضغوطة) ===== */
        .admin-text {
            font-size: 5.4pt;
            font-weight: 700;
            text-align: center;
            margin-top: 0.3mm;
            line-height: 1.1;
        }

        .admin-text .phones {
            font-weight: 800;
            margin-bottom: .3mm;
        }

        .admin-text .nums {
            direction: ltr;
            unicode-bidi: bidi-override;
            display: inline-block;
        }

        .admin-text .note {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="sticker-card">

        <!-- ===== الهيدر ===== -->
        <table class="header-table">
            <tr>
                <td class="company-cell">
                    <div class="header-title">الزاجل</div>
                    <div class="header-subtitle">للنقل والشحن السريع</div>
                    <div class="header-extra">الى جميع المحافظات ودول الخليج</div>
                    <div class="header-extra">الفرع الرئيسي / حضرموت - القطن</div>
                    <div class="header-phones">
                        781216757 - 730831802<br>
                        773136727 - 781989021
                    </div>
                </td>

                <td class="logo-cell">
                    <img src="{{ public_path('images/new.svg') }}">
                </td>

                <td class="branch-cell">
                    فرع {{ $shipment->senderBranch->name ?? '...' }}<br>
                    {{ $shipment->senderBranch->phone ?? '' }}
                </td>
            </tr>
        </table>

        <!-- ===== بيانات السند ===== -->
        <table class="info-grid">
            <tr>
                <td><span class="label">رقم السند:</span></td>
                <td><span class="value">{{ $shipment->bond_number ?? '...' }}</span></td>
                <td><span class="label">نوع الشحنة:</span></td>
                <td><span class="value-small">{{ $shipment->package_type ?? '...' }}</span></td>
                <td><span class="label">الرمز:</span></td>
                <td><span class="value">{{ $shipment->code ?? '...' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">اسم المرسل:</span></td>
                <td><span class="value">{{ $shipment->senderCustomer->name ?? '...' }}</span></td>
                <td><span class="label">رقم المرسل:</span></td>
                <td><span class="value-small">{{ $shipment->senderCustomer->phone ?? '...' }}</span></td>
                <td><span class="label">اسم المستلم:</span></td>
                <td><span class="value">{{ $shipment->receiverCustomer->name ?? '...' }}</span></td>
            </tr>
            <tr>
                <td><span class="label">ملاحظه:</span></td>
                <td><span class="value">{{ $shipment->notes ?? '...' }}</span></td>
                <td><span class="label">التاريخ:</span></td>
                <td><span class="value">{{ $shipment->created_at->format('Y-m-d') }}</span></td>
            </tr>
        </table>

        <!-- ===== من / إلى ===== -->
        <table class="info-grid">
            <tr>
                <td><span class="label">من فرع:</span></td>
                <td><span class="value-small">{{ $shipment->senderBranch->name ?? '...' }}</span></td>
                <td><span class="label">إلى فرع:</span></td>
                <td><span class="value-small">{{ $shipment->receiverBranch->name ?? '...' }}</span></td>
            </tr>
        </table>

        <!-- ===== الإدارة العامة + الشروط (تظهر بالكامل) ===== -->
        <div class="admin-text">
            <div class="phones">
                أرقام الإدارة العامة لجميع الفروع:
                <span class="nums">781216757 - 773136727 - 774996316 - 773374176</span>
            </div>

            <div class="note">
                * نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا . * نحن غير مسؤولين عن الأشياء الثمينة الممنوع
                إرسالها
                في الطرود .
                * نحن غير مسؤولين عن بقاء الطرود أكثر من شهر .
                <br>
                * نحن غير مسؤولين عن الحريق وحوادث السير .
                * التأكد من بيانات السند قبل المغادرة .
                * غير مسؤولين عن بقاء الطرود أكثر من شهر أو الحريق وحوادث السير، والتأكد من بيانات السند قبل المغادرة.

            </div>
        </div>

    </div>
</body>

</html>
