<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "dejavusans";
            direction: rtl;
            text-align: right;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 8px;
        }

        .header td {
            font-size: 12px;
            padding: 2px 10px;
            border: none !important;
        }

        .company-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }

        .gray {
            color: #000;
            font-size: 12px;
            line-height: 1.4;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #f3f3f3;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header">
        <tr>
            <td style="width: 33%;">
                <div class="company-title">الزاجل</div>
                الزاجل للنقل والشحن السريع <br>
                إلى جميع المحافظات ودول الخليج <br>
                للتواصل: <br>
                781216757 - 730831802 <br>
                773136727 - 781989021
            </td>

            <td style="width: 34%; text-align:center;">
                <img src="{{ public_path('images/new.svg') }}" style="width:120px; height:120px;">
            </td>

            <td style="width: 33%; font-size:12px; text-align:center;">
                فرع المكلا - ملاعب العمودي - مقابل حديقة القصر <br>
                735637947 - 774996316 - 772038561
            </td>
        </tr>
    </table>

    <!-- MAIN TITLE -->
    <div class="title">
        كشف رسائل السائق: {{ $driver->name }}
    </div>

    <p style="font-size:13px; margin:0 0 10px 0;">
        عدد الرسائل: {{ $shipments->count() }}
    </p>

    <!-- SHIPMENTS TABLE -->
    <table>
        <thead>
            <tr>
                <th>رقم السند</th>
                <th>اسم المرسل</th>
                <th>اسم المستلم</th>
                <th>رقم المستلم</th>
                <th>نوع الرسالة</th>
                <th>المنطقة</th>
                <th>ملاحظات</th>
            
            </tr>
        </thead>

        <tbody>
            @foreach($shipments as $s)
                <tr>
                    <td>{{ $s->bond_number }}</td>
                    <td>{{ $s->sender_name }}</td>
                    <td>{{ $s->receiver_name }}</td>
                    <td>{{ $s->receiver_phone }}</td>
                    <td>{{ $s->package_type }}</td>
                    <td> {{ $s->to_city }}</td>

             

                    <td>{{ $s->note}}</td>

                </tr>
            @endforeach
        </tbody>
    </table>


    <!-- TOTAL & SIGN -->
    <table style="margin-top:10px; border:none;">
        <tr>
            <td style="border:none;text-align:right;font-weight:bold; font-size:13px;">
                إجمالي الآجل: {{ number_format($totalCOD,2) }} ر.ي
            </td>
            <td style="border:none;text-align:left;font-size:13px; margin-top: 12px;">
                توقيع السائق: __________________________
            </td>
        </tr>
    </table>

</body>
</html>
