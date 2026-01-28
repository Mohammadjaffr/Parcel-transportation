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
            margin: 0;
            padding: 0;
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
            padding: 0.5mm 1mm;
            display: flex;
            flex-direction: column;
        }

        /* ===== الهيدر ===== */
        .header-table {
            width: 100%;
            border-bottom: 1.2px solid #fb6514;
            margin-bottom: 0.5mm;
            table-layout: fixed;
            border-spacing: 0;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
            line-height: 1;
        }

        .company-cell {
            width: 38%;
            font-size: 5.5pt;
            line-height: 1.05;
        }

        .header-title {
             font-size: 30px;
            font-weight: bold;
            color: #fb6514;
            margin-bottom: 3px;
        }

        .header-subtitle {
            font-size: 6pt;
            margin-bottom: 0.1mm;
        }

        .header-extra {
            font-size: 5.2pt;
            margin-bottom: 0.05mm;
        }

        .header-phones {
            font-size: 5pt;
            font-weight: bold;
            margin-top: 0.2mm;
            direction: ltr;
            line-height: 1.1;
        }

        .logo-cell {
            width: 24%;
            text-align: center;
            padding: 0;
        }

        .logo-cell img {
            width: 6mm;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .branch-cell {
            width: 38%;
            text-align: left;
            font-size: 6pt;
            font-weight: bold;
            line-height: 1.15;
        }

        /* ===== الجداول ===== */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.3mm;
        }

        .info-grid td {
            padding: 0.1mm 0.5mm;
            border-bottom: 0.5px solid #eee;
            font-size: 6.5pt;
        }

        .label {
            font-size: 6.5pt;
            font-weight: bold;
            color: #fb6514;
            white-space: nowrap;
        }

        .value {
            font-size: 7.5pt;
            font-weight: 800;
        }

        .value-small {
            font-size: 7pt;
            font-weight: 700;
        }

        /* ===== الإدارة العامة (مضغوطة) ===== */
        .admin-text {
            font-size: 5.5pt;
            font-weight: 700;
            text-align: center;
            margin-top: 1mm;
            line-height: 1.12;
        }

        .admin-text .phones {
            font-weight: 800;
            /* margin-bottom: 0.4mm; */
        }

        .admin-text .nums {
            direction: ltr;
            unicode-bidi: bidi-override;
            display: inline-block;
        }

        .admin-text .note {
            margin: 0;
            line-height: 1.15;
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
                    <div class="header-extra">الفرع المكلا / اربعين شقة - جنب بنك ا مجاد</div>
                    <div class="header-phones">
                        772038561 - 735637947<br>
                        774996316

                    </div>
                </td>

                <td class="logo-cell">
                    <img src="{{ public_path('images/new.svg') }}" style="width: 200px; height: auto;">
                </td>

                <td class="branch-cell">
                    <div style="font-weight: bold;  margin-bottom: 5px;">
                        فرع / القطن -عمارة شظي - خلف بنك التظامن
                    </div>
                    <div style="font-weight: bold; ">
                        781216757 - 773136727 - 730831802
                    </div>
                    <div style=" margin-top: 4px;">الفرع / المكلا - اربعين شقة - بجانب بنك ا مجاد</div>
                    <div class="header-phones">
                        735637947 للتواصل / 774996316 - 772038561<br>

                    </div>
                    <div style="font-weight: bold; margin-top: 4px;border: 2px solid #fb6514;color: #000;text-align: center">
                        رقم السند: {{ $shipment->bond_number ?? '...' }}
                    </div>
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
                <td><span class="label">التاريخ:</span></td>
                <td><span class="value">{{ $shipment->created_at->format('Y-m-d') }}</span></td>
            </tr>
            <tr>
                <td><span class="label">اسم المرسل:</span></td>
                <td><span class="value">{{ $shipment->senderCustomer->name ?? '...' }}</span></td>
                <td><span class="label">رقم المرسل:</span></td>
                <td><span class="value-small">{{ $shipment->senderCustomer->phone ?? '...' }}</span></td>
                <td><span class="label">ملاحظه:</span></td>
                <td><span class="value">{{ $shipment->notes ?? '...' }}</span></td>

            </tr>
            <tr>

                <td><span class="label">اسم المستلم:</span></td>
                <td><span class="value">{{ $shipment->receiverCustomer->name ?? '...' }}</span></td>
                <td><span class="label">رقم المستلم:</span></td>
                <td><span class="value-small">{{ $shipment->receiverCustomer->phone ?? '...' }}</span></td>

                <td><span class="label">الرمز:</span></td>
                <td><span class="value">{{ $shipment->code ?? '...' }}</span></td>
            </tr>
        </table>

        <!-- ===== من / إلى ===== -->
        <table class="info-grid">
            <tr>
                <td><span class="label">من فرع:</span></td>
                <td><span class="value-small">{{ $shipment->senderBranch->name ?? '...' }}</span></td>
                   <td><span class="label">إلى فرع:</span></td>
                <td><span class="value-small">{{ $shipment->receiverBranch->name ?? '...' }}</span></td>
                <td><span class="label"></span></td>
                <td><span class="value-small"></span></td>
             
            </tr>
        </table>

        <!-- ===== الإدارة العامة + الشروط (تظهر بالكامل) ===== -->
        <div class="admin-text">
            <div class="phones" style="font-size: 7px;">
                أرقام الإدارة العامة لجميع الفروع:
                <span class="nums" style="font-size: 7px;color: #fb6514;">781216757 - 773136727 - 774996316 - 773374176</span>
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
