<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: 'dejavusans';
            direction: rtl;
            text-align: right;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice {
            border: 1px solid #000;
            padding: 8px;
        }

        .box table td {
            /* border: 1px solid #333; */
            padding: 4px;
        }

        .center-title {
            text-align: center;
            font-size: 18px;
            margin: 8px 0 10px;
            font-weight: bold;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 11px;
        }
    </style>

</head>

<body>

    <div class="invoice">

        <!-- HEADER -->
        <table style="width:100%; text-align:center; margin-bottom:5px;">
            <tr>
                <!-- فرع المكلا -->
                <td style="width:35%; font-size:11px; vertical-align:top;">
                    فرع المكلا – ملاعب العمودي – مقابل حديقة القصر <br>
                    735637947 - 774996316 - 772038561
                </td>

                <!-- اللوجو -->
                <td style="width:34%; vertical-align:middle;">
                    <img src="{{ public_path('images/new.svg') }}"
                        style="width:150px; height:150px; object-fit:contain; margin:0 auto;">
                </td>

                <!-- معلومات الشركة -->
                <td style="width:33%; font-size:11px; vertical-align:top;">
                    <span style="font-size:18px; font-weight:bold;">الزاجل</span> <br>
                    الزاجل للنقل والشحن السريع <br>
                    إلى جميع المحافظات ودول الخليج <br>
                    للتواصل:<br>
                    781216757 - 730831802 <br>
                    773136727 - 781989021
                </td>
            </tr>
        </table>

        <!-- العنوان الرئيسي -->
        <div class="center-title">
            سند إستلام - فرع {{ $shipment->to_city }}
        </div>

        <!-- تاريخ + مبلغ -->
        <table style="margin-bottom:5px;">
            <tr>
                <td style="font-size: 16px;">
                    المبلغ: ( {{ number_format($shipment->cod_amount, 2) }}
                    @if ($shipment->payment_method == 'prepaid')
                        نقداً
                    @else
                        آجلاً
                    @endif
                    )
                </td>

                <td style="text-align:left; font-size:16px;">
                    التاريخ: {{ now()->format('Y-m-d') }}
                </td>
            </tr>
        </table>

        <!-- بيانات المرسل والمستلم -->
        <div class="box">
            <table>
                <tr>
                    <td>اسم المرسل: {{ $shipment->sender_name }}</td>
                    <td>جوال: {{ $shipment->sender_phone }}</td>
                </tr>

                <tr>
                    <td>اسم المستلم: {{ $shipment->receiver_name }}</td>
                    <td>جوال: {{ $shipment->receiver_phone }}</td>
                </tr>

                <tr>
                    <td colspan="2">
                        الجهة / من {{ $shipment->from_city }} إلى {{ $shipment->to_city }}
                    </td>
                </tr>

                <tr>
                    <td>عدد جوالين العسل: -{{ $shipment->no_gallons_honey }}</td>
                    <td>عدد العلب قروف: -{{ $shipment->no_honey_jars }}</td>
                </tr>

                <tr>
                    <td>الرمز: {{ $shipment->code }}</td>
                    <td>نوع الرسالة: {{ $shipment->package_type }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا • نحن غير مسؤولين عن ضياع أو تأخر أو تلف البضاعة
            <br>
            التأكد من بيانات المستلم قبل المغادرة
        </div>

    </div>

</body>

</html>
