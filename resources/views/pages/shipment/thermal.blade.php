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
        }

        .sticker-card {
            width: 100mm;
            height: 70mm;
            padding: 2.5mm 3.5mm;
            position: relative;
        }

        /* ================= الهيدر ================= */
        .header-table {
            width: 100%;
            border-bottom: 1.2px solid #fb6514;
            margin-bottom: 1.5mm;
        }

        .company-cell {
            width: 35mm;
            text-align: right;
            vertical-align: middle;
            font-size: 6.8pt;
            line-height: 1.25;
            color: #333;
        }

        .header-title {
            font-size: 10pt;
            font-weight: bold;
        }

        .header-subtitle {
            font-size: 7.5pt;
            margin-top: 0.5mm;
        }

        .header-extra {
            font-size: 6.5pt;
            margin-top: 0.3mm;
        }

        .header-phones {
            font-size: 6.5pt;
            margin-top: 0.3mm;
            font-weight: bold;
        }

        .logo-cell {
            width: 28mm;
            text-align: center;
            vertical-align: middle;
        }

        .logo-cell img {
            width: 14mm;
        }

        .date-text {
            font-size: 7pt;
            color: #666;
            font-weight: bold;
            margin-top: 0.8mm;
        }

        .branch-cell {
            width: 35mm;
            text-align: left;
            vertical-align: middle;
            font-size: 6.8pt;
            font-weight: bold;
            line-height: 1.25;
            color: #333;
        }

        .branch-name {
            margin-bottom: 0.6mm;
        }

        /* =============== جداول البيانات =============== */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.2mm;
        }

        .info-grid td {
            padding: 0.7mm 0;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: middle;
        }

        .label {
            font-size: 7pt;
            font-weight: bold;
            color: #fb6514;
            white-space: nowrap;
        }

        .value {
            font-size: 8.5pt;
            font-weight: 900;
            color: #000;
        }

        .value-small {
            font-size: 7.5pt;
            font-weight: 700;
        }

        .admin-text {
            font-size: 6.8pt;
            font-weight: bold;
            text-align: center;
            margin-top: 2rem;
        }
    </style>
</head>

<body>

    <div class="sticker-card">

        {{-- =============== الهيدر =============== --}}
        <table class="header-table">
            <tr>
                <!-- يمين: معلومات الشركة -->
                <td class="company-cell">
                    <div class="header-title">الزاجل</div>
                    <div class="header-subtitle">للنقل والشحن السريع</div>
                    <div class="header-extra">الى جميع المحافظات ودول الخليج</div>
                    <div class="header-extra">الفرع الرئيسي / حضرموت - القطن - المرقدة</div>
                    <div class="header-phones">
                        للتواصل / 781216757 - 730831802<br>
                        773136727 - 781989021
                    </div>
                </td>

                <!-- منتصف: اللوجو + التاريخ -->
                <td class="logo-cell">
                    <img src="{{ public_path('images/new.svg') }}" alt="Logo">
                   
                </td>

                <!-- يسار: فرع المرسل -->
                <td class="branch-cell">
                    <div class="branch-name">
                        فرع {{ $shipment->senderBranch->name ?? '........' }}
                        - {{ $shipment->senderBranch->address ?? '' }}
                    </div>
                    <div>
                        {{ $shipment->senderBranch->phone ?? '' }}
                    </div>
                </td>
            </tr>
        </table>

        {{-- ========== بيانات السند (رقم + مبلغ + نوع + رمز) ========== --}}
   
        <table class="info-grid">
            <tr>
                <td style="width: 18%;">
                    <span class="label">رقم السند:</span>
                </td>
                <td style="width: 32%;">
                    <span class="value">#{{ $shipment->bond_number }}</span>
                </td>

                   <td><span class="label">نوع الشحنة:</span></td>
                <td><span class="value-small">{{ $shipment->package_type }}</span></td>
            </tr>

            <tr>
            

                <td><span class="label">رمز الشحنة:</span></td>
                <td><span class="value">{{ $shipment->code }}</span></td>
                 <td><span class="label">التاريخ:</span></td>
                <td><span class="value">{{ $shipment->created_at->format('Y-m-d') }}</span></td>
            </tr>
        </table>

        

        {{-- ========== من / إلى الفروع ========== --}}
        <table class="info-grid">
            <tr>
                <td style="width: 18%;"><span class="label">من فرع:</span></td>
                <td style="width: 32%;">
                    <span class="value-small">{{ $shipment->senderBranch->name ?? '...' }}</span>
                </td>

                <td style="width: 18%;"><span class="label">إلى فرع:</span></td>
                <td style="width: 32%;">
                    <span class="value-small">{{ $shipment->receiverBranch->name ?? '...' }}</span>
                </td>
            </tr>
        </table>

        {{-- ========== أرقام الإدارة العامة ========== --}}
        <div class="admin-text">
            أرقام الإدارة العامة لجميع الفروع:
            781216757 - 773136727 - 774996316 - 773374176
        </div>

    </div>

</body>

</html>
