<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>سند استلام رقم {{ $shipment->id }}</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;700;800&display=swap');

        body {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .invoice {
            width: 95%;
            margin: auto;
            padding: 15px;
            border: 1px solid #000;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* border-bottom: 3px solid #000; */
            padding-bottom: 10px;
        }

        .branch-info {
            font-size: 14px;
            font-weight: 700;
            text-align: center;
        }

        .logo-box {
            width: 130px;
            height: 130px;
            /* border: 1px solid #000; */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }

        .company-title {
            font-size: 28px;
            font-weight: 800;
        }

        .small-text {
            font-size: 14px;
        }

        table {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
            font-size: 17px;
        }

        td {
            padding: 6px;
            text-align: right;
            
        }

        .box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 7px;
            min-height: 140px;
        }
        tr {
            margin: 10px 20px;
        }

        .footer {
            text-align: center;
            margin-top: 8px;
            font-weight: bold;
            font-size: 13px;
        }

        .center-title {
            text-align: center;
            font-size: 20px;
            padding: 10px;
            border: 1px solid #000;
            font-weight: bold;
            width: fit-content;
            margin: 20px 20px;
        }

        .payment-box td {
            font-weight: bold;
        }
    </style>

</head>

<body>

    <div class="invoice">

        <!-- HEADER -->
        <div class="header">

            <div class="branch-info">
                فرع المكلا – ملاعب العمودي – مقابل حديقة القصر <br>
                735637947 - 774996316 - 772038561
            </div>

            <div class="logo-box">
                {{-- <img src="{{ asset('assets/images/logo.png') }}" alt="logo" style="width: 100px;"> --}}
            </div>

            <div class="branch-info">
                <h1>الزاجل</h1>
                الزاجل للنقل والشحن السريع <br>
                إلى جميع المحافظات ودول الخليج <br>
                للتواصل:<br>
                781216757 - 730831802 <br>
                773136727 - 781989021
            </div>

        </div>

        <div class="center-title">سند إستلام - فرع {{ $shipment->to_city }}</div>

        <!-- Date + Amount -->
        <table>
            <tr>
                <td style="font-size: 18px;">المبلغ: ( {{ number_format($shipment->cod_amount, 2) }}@if ($shipment->payment_method == 'prepaid')
                        نقداً
                    @else
                        آجلاً
                    @endif) </td>
                <td style="text-align:left;font-size:18px;">التاريخ: {{ now()->format('Y-m-d') }}</td>
            </tr>
            
        </table>

        <!-- Sender / Receiver -->
        <div class="box">
            <table>
                <tr>
                    <td>جوال: {{ $shipment->sender_phone }}</td>
                    <td>اسم المرسل: {{ $shipment->sender_name }}</td>
                </tr>
                <tr>
                    <td>جوال: {{ $shipment->receiver_phone }}</td>
                    <td>اسم المستلم: {{ $shipment->receiver_name }}</td>

                </tr>
                <tr>
                    <td colspan="2">
                        الجهة / من {{ $shipment->from_city }} إلى {{ $shipment->to_city }}
                    </td>
                </tr>
                <tr>
                    <td>عدد جوالين العسل: -</td>
                    <td>عدد العلب قروف: -</td>
                </tr>
                <tr>
                    <td colspan="2">نوع الرسالة: {{ $shipment->package_type }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer Notes -->
        <div class="footer">
            نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا • نحن غير مسؤولين عن ضياع أو تأخر أو تلف البضاعة
            <br>
            التأكد من بيانات المستلم قبل المغادرة
        </div>

    </div>

</body>

</html>
