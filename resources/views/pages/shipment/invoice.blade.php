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
            margin: 0;
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        /* Header Styling */
        .header-title {
            font-size: 45px;
            font-weight: bold;
            color: #fb6514;
            margin-bottom: 3px;
        }

        .header-subtitle {
            font-size: 15px;
            font-weight: 600;
            margin-top: 3px;
            color: #333;
        }

        .header-info {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
            line-height: 1.4;
            font-weight: bold;
         
        }

        .header-phones {
            font-size: 11px;
            margin-top: 6px;
            font-weight: bold;
            color: #000;
        }

        .header-separator {
            border-bottom: 3px solid #fb6514;
            margin: 15px 0;
        }

        /* Title Box */
        .receipt-title-box {
            background-color: #fb6514;
            color: #fff;
            padding: 10px 30px;
            border-radius: 8px;
            font-size: 22px;
            font-weight: bold;
            display: inline-block;
            margin: 10px auto;
            box-shadow: 0 2px 4px rgba(251, 101, 20, 0.3);
        }

        .amount-date-section {
            font-size: 14px;
            font-weight: bold;
        }

        .amount-label {
            color: #fb6514;
            font-weight: bold;
        }

        /* Branch Badge */
        .branch-name-large {
            font-size: 19px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            background: #fff8f4;
            /* border: 2px solid #fb6514; */
            border-radius: 8px;
            color: #fb6514;
        }

        /* Main Content Box */
        .main-box {
            border: 2px solid #000;
            border-radius: 12px;
            padding: 20px;
            margin: 15px 0;
            background: #fffbf8;
        }

        .data-label {
            font-weight: bold;
            color: #fb6514;
            font-size: 13px;
        }

        .dotted-line {
            border-bottom: 1.5px dotted #aaa;
            display: inline-block;
            min-width: 150px;
            text-align: center;
            padding: 2px 8px;
            margin: 0 5px;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .data-table td {
            padding: 5px 0;
        }

        .code-box {
            background: #fef3c7;
            padding: 4px 10px;
            border-radius: 5px;
            font-weight: bold;
            color: #92400e;
            display: inline-block;
        }

        /* Footer */
        .disclaimer {
            font-size: 10px;
            line-height: 1.7;
            margin-top: 15px;
            padding: 12px;
            background: #fff8f4;
            border-right: 3px solid #fb6514;
            border-radius: 5px;
            color: #555;
        }

        .footer-box {
            background-color: #fb6514;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            margin-top: 12px;
            font-size: 12px;
            border-radius: 5px;
        }

        .footer-phones {
            direction: ltr;
            display: inline-block;
            margin-top: 3px;
        }
    </style>

</head>

<body>

    <!-- Header Section -->
    <table style="width: 100%; margin-bottom: 5px;">
        <tr>
            <!-- Right: Company Info -->
            <td style="width: 35%; text-align: right; padding-left: 10px;">
                <div class="header-title">الزاجل</div>
                <div class="header-subtitle">للنقل والشحن السريع</div>
                <div class="header-info">الى جميع المحافظات ودول الخليج</div>
                <div class="header-info">رقم السند: <span style="color: #fb6514;">{{ $shipment->bond_number }}</span></div>
                <div class="header-phones">
                    للتواصل / 774996316 - 772038561<br>735637947
                </div>
            </td>

            <!-- Center: Logo -->
            <td style="width: 30%; text-align: center;">
                <img src="{{ public_path('images/new.svg') }}" style="width: 200px; height: auto;">
            </td>

            <!-- Left: Branch Info -->
            <td style="width: 35%; text-align: left; padding-right: 10px;">
                <div style="font-weight: bold; font-size: 13px; color: #333; margin-bottom: 4px;">
                    فرع / القطن -عمارة شظي - خلف بنك التظامن
                </div>
                <div style="font-weight: bold; font-size: 12px; color: #000;">
                    781216757 - 773136727 - 730831802
                </div>
                <div class="header-info">فرع / المكلا - اربعين شقة - بجانب بنك ا مجاد</div>
                <div class="header-phones">
                    للتواصل / 774996316 - 772038561<br>735637947
                </div>
                 <div style="margin-top: 5px; font-size: 12px; font-weight: bold;">خدمة الشحن إلى جميع المحافظات ودول
                    الخليج</div>
            </td>
        </tr>
    </table>

    <!-- Separator Line -->
    <div class="header-separator"></div>

    <!-- Amount and Date Row -->
    <table style="width: 100%; margin-bottom: 5px;">
        <tr>
            <!-- Amount -->
            <td style="width: 32%; text-align: right; vertical-align: middle;">
                <div class="amount-date-section">
                    <span class="amount-label">المبلغ:</span>
                    <span style="color: #000;">{{ number_format($shipment->total_amount, 2) }}</span>
                    @if ($shipment->payment_method == 'prepaid')
                        <span style="font-size: 11px;">(نقداً)</span>
                    @elseif ($shipment->payment_method == 'cod')
                        <span style="font-size: 11px;">(أجل على المستلم)</span>
                    @elseif ($shipment->payment_method == 'customer_credit')
                        <span style="font-size: 11px;">(أجل على العميل)</span>
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

            <!-- Title -->
            <td style="width: 36%; text-align: center; vertical-align: middle;">
                <div class="receipt-title-box">سند إستلام</div>
            </td>

            <!-- Date -->
            <td style="width: 32%; text-align: left; vertical-align: middle;">
                <div class="amount-date-section">
                    <span class="amount-label">التاريخ:</span>
                    <span style="font-weight: normal;">{{ $shipment->created_at->format('Y-m-d') }}</span> م
                </div>
            </td>
        </tr>
    </table>

    <!-- Branch Badge -->
    <div class="branch-name-large">
        فرع {{ $shipment->senderBranch->name ?? '........' }}
    </div>

    <!-- Main Content Box -->
    <div class="main-box">
        <table class="data-table">
            <!-- Sender & Receiver Names -->
            <tr>
                <td style="width: 48%; text-align: right;">
                    <span class="data-label">اسم المرسل:</span>
                    <span class="dotted-line" style="min-width: 160px;">
                        {{ $shipment->senderCustomer->name ?? ($shipment->sender_name ?? '') }}
                    </span>
                </td>
                <td style="width: 52%; text-align: right;">
                    <span class="data-label">جوال:</span>
                    <span class="dotted-line" style="min-width: 140px;">
                        {{ $shipment->senderCustomer->phone ?? ($shipment->sender_phone ?? '') }}
                    </span>
                </td>
            </tr>

            <tr>
                <td style="text-align: right;">
                    <span class="data-label">اسم المستلم:</span>
                    <span class="dotted-line" style="min-width: 160px;">
                        {{ $shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '') }}
                    </span>
                </td>
                <td style="text-align: right;">
                    <span class="data-label">جوال:</span>
                    <span class="dotted-line" style="min-width: 140px;">
                        {{ $shipment->receiverCustomer->phone ?? ($shipment->receiver_phone ?? '') }}
                    </span>
                </td>
            </tr>

            <!-- Separator -->
            <tr>
                <td colspan="2" style="height: 5px;"></td>
            </tr>

            <!-- Items & Route -->
            <tr>
                <td style="text-align: right;">
                    <span class="data-label">عدد جوالين العسل:</span>
                    <span class="dotted-line" style="min-width: 70px;">
                        {{ $shipment->no_gallons_honey ?: '....' }}
                    </span>
                </td>
                <td style="text-align: right;">
                    <span class="data-label">الجهة:</span>
                    <span class="dotted-line" style="min-width: 70px;">
                        {{ $shipment->senderBranch->name ?? '' }}
                    </span>
                    <span class="data-label">إلى:</span>
                    <span class="dotted-line" style="min-width: 70px;">
                        {{ $shipment->receiverBranch->name ?? '' }}
                    </span>
                </td>
            </tr>

            <tr>
                <td style="text-align: right;">
                    <span class="data-label">عدد العلب قروف:</span>
                    <span class="dotted-line" style="min-width: 70px;">
                        {{ $shipment->no_honey_jars ?: '....' }}
                    </span>
                </td>
                <td style="text-align: right;">
                    <span class="data-label">الرمز:</span>
                    <span class="code-box">{{ $shipment->code ?? '....' }}</span>
                </td>
            </tr>

            <!-- Separator -->
            <tr>
                <td colspan="2" style="height: 5px;"></td>
            </tr>

            <!-- Package Type & Notes -->
            <tr>
                <td colspan="2" style="text-align: right;">
                    <span class="data-label">نوع الرسالة:</span>
                    <span class="dotted-line" style="min-width: 450px;">
                        {{ $shipment->package_type ?? '....' }}
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right;">
                    <span class="data-label">ملاحظات:</span>
                    <span class="dotted-line" style="min-width: 450px;">
                        {{ $shipment->notes ?? '....' }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Disclaimer -->
    <div class="disclaimer">
        * نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا . * نحن غير مسؤولين عن الأشياء الثمينة الممنوع إرسالها
        في الطرود .<br>
        * نحن غير مسؤولين عن بقاء الطرود أكثر من شهر .<br>
        * نحن غير مسؤولين عن الحريق وحوادث السير .<br>
        * التأكد من بيانات السند قبل المغادرة .
    </div>

    <!-- Footer -->
    <div class="footer-box">
        أرقام الإدارة العامة لجميع الفروع
        <br>
        <span class="footer-phones">
            781216757 - 773136727 - 774996316 - 773374176
        </span>
    </div>

</body>

</html>
