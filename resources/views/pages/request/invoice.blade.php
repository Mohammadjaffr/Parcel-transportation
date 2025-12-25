<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: 'aealarabiya', 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 12px;
            color: #000;
            margin: 50% auto; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
        }

        .header-subtitle {
            font-size: 14px;
            margin-top: 5px;
        }

        .header-phones {
            font-size: 12px;
            margin-top: 5px;
            font-weight: bold;
        }

        .receipt-title-box {
            background-color: #000;
            color: #fff;
            padding: 5px 15px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
            margin: 10px auto;
        }

        .branch-name-large {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .main-box {
            border: 1px solid #000;
            border-radius: 20px;
            padding: 15px;
            margin-top: 10px;
            position: relative;
        }

        .info-row {
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: bold;
        }

        .dotted-line {
            border-bottom: 1px dotted #999;
            display: inline-block;
            width: 150px;
            text-align: center;
            margin-right: 5px;
            margin-left: 5px;
        }

        .footer-box {
            background-color: #000;
            color: #fff;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
            font-size: 12px;
        }

        .disclaimer {
            font-size: 10px;
            font-weight: bold;
            margin-top: 10px;
            line-height: 1.6;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            width: 200px;
            z-index: -1;
        }

        .watermark2 {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            width: 300px;
            z-index: -1;
        }
    </style>

</head>

<body>

    <!-- Header Section -->
    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <!-- Right: Company Info (First TD in RTL) -->
            <td style="width: 35%; text-align: right;">
                <div class="header-title">الزاجل</div>
                <div class="header-subtitle">للنقل والشحن السريع</div>
                <div style="font-size: 11px;">الى جميع المحافظات ودول الخليج</div>
                <div style="font-size: 11px; margin-top: 4px;">الفرع الرئيسي / حضرموت - القطن - المرقدة</div>
                <div class="header-phones">
                    للتواصل / 781216757 - 730831802<br>
                    773136727 - 781989021
                </div>
            </td>

            <!-- Center: Logo -->
            <td style="width: 30%; text-align: center;">
                <img src="{{ public_path('images/new.svg') }}" class="watermark2">
            </td>

            <!-- Left: Branch Info (Last TD in RTL) -->
            <td style="width: 35%; text-align: left; padding-top: 10px;">
                <div style="font-weight: bold; font-size: 13px; margin-bottom: 5px;">
                    فرع {{ $shipment->senderBranch->name ?? '........' }} - {{ $shipment->senderBranch->address ?? '' }}
                </div>
                <div style="font-weight: bold; font-size: 12px;">
                    {{ $shipment->senderBranch->phone ?? '' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Amount and Date Row -->
    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <!-- Right: Amount (First TD in RTL) -->
            <td style="width: 30%; text-align: right; vertical-align: middle;">
                <div style="font-weight: bold; font-size: 14px;">
                    المبلغ/ ( {{ number_format($shipment->total_amount, 2) }} )

                    @if ($shipment->payment_method == 'prepaid')
                        نقداً
                    @elseif ($shipment->payment_method == 'cod')
                        أجل على المستلم
                    @elseif ($shipment->payment_method == 'customer_credit')
                        أجل على العميل
                    @elseif ($shipment->payment_method == 'partial_payment')
                        @php
                            $paid = $shipment->payments->sum('amount');
                            $remaining = $shipment->total_amount - $paid;
                        @endphp
                        <span style="font-size: 10px;">
                            (نقد:{{ number_format($paid) }}/أجل:{{ number_format($remaining) }})
                        </span>
                    @endif
                </div>
            </td>

            <td style="width: 40%; text-align: center; vertical-align: middle;">
                <div class="receipt-title-box">سند إستلام</div>
            </td>

            <!-- Left: Date (Last TD in RTL) -->
            <td style="width: 30%; text-align: left; vertical-align: middle;">
                <div style="font-weight: bold; font-size: 14px;">
                    التاريخ: <span style="font-weight: normal;">{{ $shipment->created_at->format('Y-m-d') }}</span> م
                </div>
            </td>
        </tr>
    </table>

    <!-- Branch Name Center -->
    <div class="branch-name-large">
        فرع {{ $shipment->senderBranch->name ?? '........' }}
    </div>

    <!-- Main Content Box -->
    <div class="main-box">
        <!-- Watermark/Background Logo -->
        {{-- <img src="{{ public_path('images/new.svg') }}" class="watermark" /> --}}

        <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
            <tr>
                <!-- Right column -->
                <td style="width: 45%; text-align: right;">
                    <strong>اسم المرسل /</strong>
                    <span class="dotted-line" style="width: 180px;">{{ $shipment->sender_name }}</span>
                </td>
                <!-- Left column -->
                <td style="width: 55%; text-align: right;">
                    <strong>جوال /</strong>
                    <span class="dotted-line" style="width: 150px;">{{ $shipment->sender_phone }}</span>
                </td>
            </tr>

            <tr>
                <td style="width: 45%; text-align: right;">
                    <strong>اسم المستلم /</strong>
                    <span class="dotted-line" style="width: 180px;">{{ $shipment->receiver_name }}</span>
                </td>
                <td style="width: 55%; text-align: right;">
                    <strong>جوال /</strong>
                    <span class="dotted-line" style="width: 150px;">{{ $shipment->receiver_phone }}</span>
                </td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
            <tr>
                <td style="width: 45%; text-align: right;">
                    <strong>عدد جوالين العسل /</strong>
                    <span class="dotted-line" style="width: 60px;">{{ $shipment->no_gallons_honey ?: '....' }}</span>
                </td>
                <td style="width: 55%; text-align: right;">
                    <strong>الجهة / من</strong>
                    <span class="dotted-line" style="width: 80px;">{{ $shipment->senderBranch->name }}</span>
                    <strong>إلى</strong>
                    <span class="dotted-line" style="width: 80px;">{{ $shipment->receiverBranch->name }}</span>
                </td>
            </tr>

            <tr>
                <td style="width: 45%; text-align: right;">
                    <strong>عدد العلب قروف /</strong>
                    <span class="dotted-line" style="width: 60px;">{{ $shipment->no_honey_jars ?: '....' }}</span>
                </td>
                <td style="width: 55%; text-align: right;">
                <strong>الرمز  /</strong>
                    <span style="display:inline-block; width: 100px; text-align:center;">{{ $shipment->code }}</span>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align: right;">
                    <strong>نوع الرسالة /</strong>
                    <span class="dotted-line" style="width: 400px;">{{ $shipment->package_type }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;">
                    <strong>ملاحظات /</strong>
                    <span class="dotted-line" style="width: 400px;">{{ $shipment->notes }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer Disclaimer -->
    <div class="disclaimer">
        * نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا . * نحن غير مسؤولين عن الأشياء الثمينة الممنوع إرسالها
        في الطرود .<br>
        * نحن غير مسؤولين عن بقاء الطرود أكثر من شهر .<br>
        * نحن غير مسؤولين عن الحريق وحوادث السير .<br>
        * التأكد من بيانات السند قبل المغادرة .
    </div>

    <!-- Footer Black Box -->
    <div class="footer-box">
        أرقام الإدارة العامة لجميع الفروع
        <br>
        <span style="direction: ltr; display: inline-block;">781216757 - 773136727 - 774996316 - 773374176</span>
    </div>

</body>

</html>
