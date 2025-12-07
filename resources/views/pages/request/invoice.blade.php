<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>فاتورة الشحنة رقم {{ $shipment->id }}</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;700;800&display=swap');

        body {
            font-family: 'Tajawal', sans-serif !important;
            direction: rtl;
            text-align: right;
            font-size: 15px;
            background: #fff;
            margin: 0;
        }

        .invoice-container {
            width: 90%;
            margin: auto;
            padding: 20px;
            border: 2px solid #000;
        }

        .header {
            display: flex;
            justify-content: space-between;
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 15px;
        }

        .logo {
            width: 120px;
            height: 120px;
            border: 2px solid #000;
            font-size: 13px;
            color: #555;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .company-title {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .sub-title {
            font-size: 14px;
            font-weight: 400;
        }

        .invoice-title {
            font-weight: bold;
            text-align: center;
            font-size: 18px;
            margin: 5px 0 10px 0;
            border: 2px solid #000;
            width: fit-content;
            margin: auto;
            padding: 2px 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }

        td {
            padding: 8px;
            border: 1px solid #000;
            font-size: 15px;
            text-align: right;
        }

        .no-border {
            border: none !important;
        }

        .checkbox-group input {
            transform: scale(1.3);
            margin-left: 8px;
        }

        .footer {
            margin-top: 18px;
            font-weight: 700;
            text-align: center;
            font-size: 13px;
            /* border-top: 2px dashed #000; */
            padding-top: 10px;
        }
    </style>

</head>

<body>

    <div class="invoice-container">

        <!-- Header -->
        <div class="header">
            <div class="logo">
                    {{-- <img src="{{ asset('images/new.svg') }}" alt="Logo"
                        width="50"> --}}
            </div>

            <div>
                <div class="company-title">الزاجل للنقل والشحن السريع</div>
                <div class="sub-title">نقل سريع - تغطية لجميع المحافظات</div>
            </div>

            <div>
                التاريخ: {{ now()->format('Y-m-d') }}
                <br>
                رقم الفاتورة: {{ $shipment->id }}
            </div>
        </div>

        <div class="invoice-title">
            سند شحنة
        </div>

        <!-- Sender & Receiver info -->
        <table>
            <tr>

                <td>هاتف المرسل: {{ $shipment->sender_phone }}</td>
                <td>اسم المرسل: {{ $shipment->sender_name }}</td>
            </tr>
            <tr>
                <td>هاتف المستلم: {{ $shipment->receiver_phone }}</td>
                <td>اسم المستلم: {{ $shipment->receiver_name }}</td>

            </tr>
            <tr>
                <td>إلى مدينة: {{ $shipment->to_city }}</td>
                <td>من مدينة: {{ $shipment->from_city }}</td>
            </tr>
        </table>

        <!-- Shipment Details -->
        <table>
            <tr>
                <td>عدد الجوالين: -</td>
                <td>عدد الأكياس: -</td>
                <td>عدد الكراتين: -</td>
            </tr>
            <tr>
                <td colspan="3">نوع البضاعة: {{ $shipment->package_type }}</td>
            </tr>
        </table>

        <!-- Payment -->
        <table>
            <tr>
                <td>الفرع: {{ $shipment->branch }}</td>
                <td>
                    طريقة الدفع:
                    <label><input type="checkbox" {{ $shipment->payment_method == 'prepaid' ? 'checked' : '' }}>
                        نقداً</label>
                    <label><input type="checkbox" {{ $shipment->payment_method == 'cod' ? 'checked' : '' }}>
                        آجلاً</label>
                </td>
            </tr>
        </table>

        <!-- Notes -->
        <table>
            <tr>
                <td style="height:60px;">ملاحظات: {{ $shipment->notes ?? '-' }}</td>
            </tr>
        </table>

        <!-- Footer Warning -->
        <div class="footer">
            الشركة غير مسؤولة عن أي بضائع مخالفة لقوانين النقل
            <br>
            شكراً لتعاملكم معنا
        </div>

    </div>

</body>

</html>
