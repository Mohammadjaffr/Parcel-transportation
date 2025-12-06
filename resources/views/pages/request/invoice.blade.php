<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>فاتورة الشحنة رقم {{ $shipment->id }}</title>

    <style>
        body {
            font-family: 'Tahoma';
            line-height: 1.8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <h2>فاتورة الشحنة رقم #{{ $shipment->id }}</h2>

    <table>
        <tr>
            <th>اسم المرسل</th>
            <td>{{ $shipment->sender_name }}</td>
        </tr>
        <tr>
            <th>هاتف المرسل</th>
            <td>{{ $shipment->sender_phone }}</td>
        </tr>
        <tr>
            <th>من مدينة</th>
            <td>{{ $shipment->from_city }}</td>
        </tr>
        <tr>
            <th>اسم المستلم</th>
            <td>{{ $shipment->receiver_name }}</td>
        </tr>
        <tr>
            <th>هاتف المستلم</th>
            <td>{{ $shipment->receiver_phone }}</td>
        </tr>
        <tr>
            <th>إلى مدينة</th>
            <td>{{ $shipment->to_city }}</td>
        </tr>
        <tr>
            <th>نوع الطرد</th>
            <td>{{ $shipment->package_type }}</td>
        </tr>
        <tr>
            <th>الفرع</th>
            <td>{{ $shipment->branch }}</td>
        </tr>
        <tr>
            <th>طريقة الدفع</th>
            <td>{{ $shipment->payment_method == 'prepaid' ? 'مدفوع مقدماً' : 'الدفع عند الاستلام' }}</td>
        </tr>
        <tr>
            <th>الملاحظات</th>
            <td>{{ $shipment->notes ?? '-' }}</td>
        </tr>
    </table>

    <br><br>
    <p style="text-align:center">شكراً لاستخدامك خدمتنا</p>

</body>

</html>
