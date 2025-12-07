<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>

        @font-face {
            font-family: 'amiri';
            src: url('{{ resource_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }

        body {
            font-family: 'amiri' !important;
            width: 80mm;
            margin: 0 auto;
            padding: 0;
            direction: rtl;
            unicode-bidi: bidi-override;
            font-size: 14px;
            text-align: right;
        }

        .wrapper {
            padding: 5px 0;
        }

        .header {
            text-align: center;
            font-weight: bold;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            margin: 8px 0 4px;
        }

        table {
            width: 100%;
            font-size: 13px;
        }

        td {
            padding: 4px 0;
            unicode-bidi: bidi-override;
            text-align: right;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
        }

        .separator {
            border-bottom: 1px dashed #000;
            margin: 6px 0;
        }

    </style>
</head>

<body>

<div class="wrapper">

    <div class="header">
        <div>مكتب الشحن</div>
        <div>القطن - شارع رئيسي</div>
        <div>هاتف: 0530000000</div>
    </div>

    <div class="separator"></div>

    <div class="section-title">بيانات المرسل:</div>
    <table>
        <tr>
            <td>الاسم:</td>
            <td>{{ $shipment->sender_name }}</td>
        </tr>
        <tr>
            <td>الهاتف:</td>
            <td>{{ $shipment->sender_phone }}</td>
        </tr>
        <tr>
            <td>من:</td>
            <td>{{ $shipment->from_city }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <div class="section-title">بيانات المستلم:</div>
    <table>
        <tr>
            <td>الاسم:</td>
            <td>{{ $shipment->receiver_name }}</td>
        </tr>
        <tr>
            <td>الهاتف:</td>
            <td>{{ $shipment->receiver_phone }}</td>
        </tr>
        <tr>
            <td>إلى:</td>
            <td>{{ $shipment->to_city }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <table>
        <tr>
            <td>نوع الطرد:</td>
            <td>{{ $shipment->package_type }}</td>
        </tr>
        <tr>
            <td>طريقة الدفع:</td>
            <td>{{ $shipment->payment_method == 'prepaid' ? 'مدفوع مقدماً' : 'عند الاستلام' }}</td>
        </tr>
        <tr>
            <td>الملاحظات:</td>
            <td>{{ $shipment->notes ?? '-' }}</td>
        </tr>
    </table>

    <div class="separator"></div>

    <div class="footer">
        شكراً لاستخدامك خدمتنا
        <br>
        {{ date('Y') }}
    </div>

</div>

</body>
</html>
