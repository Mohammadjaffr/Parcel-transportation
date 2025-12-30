<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <style>
        :root {
            --brand-color: #fb6514;
            --text-main: #222;
            --text-muted: #666;
            --border-soft: #e4e4e7;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'aealarabiya', 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 13px;
            color: var(--text-main);
            margin: 0;
            padding: 24px;
            background: #f5f5f7;
        }

        .page {
            max-width: 900px;
            margin: 0 auto;
        }

        .receipt-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid var(--border-soft);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            padding: 22px 26px 20px;
            position: relative;
            overflow: hidden;
        }

        /* خط علوي بلون البراند */
        .receipt-card::before {
            content: "";
            position: absolute;
            inset-inline: 0;
            top: 0;
            height: 4px;
            background: linear-gradient(90deg, #fb6514, #f97316);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        /* الهيدر */

        .header-table {
            width: 100%;
            margin-bottom: 14px;
        }

        .header-company {
            width: 34%;
            text-align: right;
            padding-inline-end: 12px;
        }

        .header-logo {
            width: 32%;
            text-align: center;
            position: relative;
        }

        .header-branch {
            width: 34%;
            text-align: left;
            padding-inline-start: 12px;
        }

        .header-title {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .header-subtitle {
            font-size: 13px;
            margin-top: 4px;
            color: var(--text-muted);
        }

        .header-extra {
            font-size: 11px;
            margin-top: 3px;
            color: var(--text-muted);
        }

        .header-phones {
            font-size: 11px;
            margin-top: 6px;
            font-weight: bold;
        }

        .header-branch-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .header-branch-phone {
            font-size: 12px;
            font-weight: bold;
            color: var(--text-muted);
        }

        .logo-img {
            width: 130px;
            opacity: 0.95;
        }

        .receipt-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 14px;
            border-radius: 999px;
            background: #111827;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            box-shadow: 0 3px 8px rgba(15, 23, 42, 0.35);
        }

        /* صف المبلغ / التاريخ / رقم السند */

        .meta-table {
            margin-bottom: 12px;
            border-radius: 10px;
            border: 1px solid var(--border-soft);
            padding: 8px 10px;
        }

        .meta-label {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
        }

        .meta-highlight {
            color: var(--brand-color);
            font-weight: 800;
        }

        .meta-center {
            text-align: center;
            border-inline: 1px solid var(--border-soft);
        }

        .branch-name-large {
            font-size: 16px;
            font-weight: 800;
            text-align: center;
            margin: 10px 0 4px;
            padding: 6px 10px;
            border-radius: 999px;
            /* border: 1px dashed rgba(148, 163, 184, 0.7); */
            background: linear-gradient(90deg, rgba(251, 101, 20, 0.05), rgba(15, 23, 42, 0.01));
        }

        /* الصندوق الرئيسي */

        .main-box {
            border-radius: 16px;
            border: 1px solid var(--border-soft);
            padding: 14px 16px 12px;
            margin-top: 8px;
            position: relative;
            background: radial-gradient(circle at 85% 10%, rgba(251, 101, 20, 0.06), transparent 55%);
        }

        .watermark {
            position: absolute;
            inset: 8% 0 0;
            margin: auto;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            width: 260px;
            z-index: -1;
        }

        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            position: relative;
            z-index: 1;
        }

        .info-label {
            font-weight: 700;
            font-size: 13px;
            color: var(--text-main);
        }

        .dotted-line {
            /* border-bottom: 1px dotted #9ca3af; */
            display: inline-block;
            min-width: 110px;
            text-align: center;
            margin: 0 6px;
            padding-bottom: 1px;
            font-weight: 600;
            color: #111827;
        }

        .small-line {
            min-width: 60px;
        }

        .code-box {
            display: inline-block;
            min-width: 110px;
            padding: 2px 8px;
            text-align: center;
            border-radius: 999px;
            border: 1px solid #111827;
            font-weight: 800;
            letter-spacing: 1px;
        }

        /* الملاحظات والتذييل */

        .notes-title {
            font-weight: 700;
            margin-inline-end: 4px;
        }

        .disclaimer {
            font-size: 10px;
            font-weight: 600;
            margin-top: 12px;
            padding-top: 8px;
            /* border-top: 1px dashed #d1d5db; */
            line-height: 1.7;
            color: var(--text-muted);
        }

        .footer-box {
            background-color: #111827;
            color: #fff;
            padding: 6px 10px;
            text-align: center;
            font-weight: 700;
            margin-top: 10px;
            font-size: 11px;
            border-radius: 10px;
        }

        .footer-box span {
            direction: ltr;
            display: inline-block;
            font-family: 'dejavusans', sans-serif;
            letter-spacing: 0.5px;
        }
    </style>

</head>

<body>
    <div class="page">
        <div class="receipt-card">

            <!-- Header Section -->
            <table class="header-table">
                <tr>
                    <!-- Right: Company Info -->
                    <td class="header-company">
                        <div class="header-title">الزاجل</div>
                        <div class="header-subtitle">للنقل والشحن السريع</div>
                        <div class="header-extra">الى جميع المحافظات ودول الخليج</div>
                        <div class="header-extra">الفرع الرئيسي / حضرموت - القطن - المرقدة</div>
                        <div class="header-phones">
                            للتواصل / 781216757 - 730831802<br>
                            773136727 - 781989021
                        </div>
                    </td>

                    <!-- Center: Logo + Badge -->
                    <td class="header-logo">
                        <img src="{{ public_path('images/new.svg') }}" class="logo-img" alt="Logo">
                        <div class="receipt-badge">سند إستلام</div>
                    </td>

                    <!-- Left: Branch Info -->
                    <td class="header-branch">
                        <div class="header-branch-name">
                            فرع {{ $shipment->senderBranch->name ?? '........' }} - {{ $shipment->senderBranch->address ?? '' }}
                        </div>
                        <div class="header-branch-phone">
                            {{ $shipment->senderBranch->phone ?? '' }}
                        </div>
                    </td>
                </tr>
            </table>

            <!-- Amount / Date / Bond Row -->
            <table class="meta-table">
                <tr>
                    <td style="width: 33%;">
                        <div class="meta-label">رقم السند</div>
                        <div class="meta-value">#{{ $shipment->bond_number }}</div>
                    </td>

                    <td class="meta-center" style="width: 34%;">
                        <div class="meta-label">التاريخ</div>
                        <div class="meta-value">
                            {{ $shipment->created_at->format('Y-m-d') }} <span class="meta-highlight">م</span>
                        </div>
                    </td>

                    <td style="width: 33%; text-align: left;">
                        <div class="meta-label">المبلغ</div>
                        <div class="meta-value">
                            <span class="meta-highlight">{{ number_format($shipment->total_amount, 2) }}</span>

                            @php
                                $paid = $shipment->payments->sum('amount');
                                $remaining = $shipment->total_amount - $paid;
                            @endphp

                            @if ($shipment->payment_method == 'prepaid')
                                <span style="font-size: 11px;"> / نقداً</span>
                            @elseif ($shipment->payment_method == 'cod')
                                <span style="font-size: 11px;"> / أجل على المستلم</span>
                            @elseif ($shipment->payment_method == 'customer_credit')
                                <span style="font-size: 11px;"> / أجل على العميل</span>
                            @elseif ($shipment->payment_method == 'partial_payment')
                                <span style="font-size: 11px;">
                                    / نقد:{{ number_format($paid) }} - أجل:{{ number_format($remaining) }}
                                </span>
                            @endif
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
                {{-- خلفية شفافة للشعار --}}
                {{-- <img src="{{ public_path('images/new.svg') }}" class="watermark" /> --}}

                <table class="info-table">
                    <tr>
                        <td style="width: 50%; text-align: right;">
                            <span class="info-label">اسم المرسل /</span>
                            <span class="dotted-line" style="min-width: 180px;">
                                {{ $shipment->senderCustomer->name ?? $shipment->sender_name ?? '' }}
                            </span>
                        </td>
                        <td style="width: 50%; text-align: right;">
                            <span class="info-label">جوال المرسل /</span>
                            <span class="dotted-line" style="min-width: 140px;">
                                {{ $shipment->senderCustomer->phone ?? $shipment->sender_phone ?? '' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: right;">
                            <span class="info-label">اسم المستلم /</span>
                            <span class="dotted-line" style="min-width: 180px;">
                                {{ $shipment->receiverCustomer->name ?? $shipment->receiver_name ?? '' }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <span class="info-label">جوال المستلم /</span>
                            <span class="dotted-line" style="min-width: 140px;">
                                {{ $shipment->receiverCustomer->phone ?? $shipment->receiver_phone ?? '' }}
                            </span>
                        </td>
                    </tr>
                </table>

                <table class="info-table">
                    <tr>
                        <td style="width: 50%; text-align: right;">
                            <span class="info-label">عدد جوالين العسل /</span>
                            <span class="dotted-line small-line">
                                {{ $shipment->no_gallons_honey ?: '....' }}
                            </span>
                        </td>
                        <td style="width: 50%; text-align: right;">
                            <span class="info-label">الجهة / من</span>
                            <span class="dotted-line small-line">
                                {{ $shipment->senderBranch->name ?? '' }}
                            </span>
                            <span class="info-label">إلى</span>
                            <span class="dotted-line small-line">
                                {{ $shipment->receiverBranch->name ?? '' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: right;">
                            <span class="info-label">عدد العلب قروف /</span>
                            <span class="dotted-line small-line">
                                {{ $shipment->no_honey_jars ?: '....' }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <span class="info-label">الرمز /</span>
                            <span class="code-box">
                                {{ $shipment->code }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: right;">
                            <span class="info-label">نوع الرسالة /</span>
                            <span class="dotted-line" style="min-width: 380px;">
                                {{ $shipment->package_type }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">
                            <span class="notes-title">ملاحظات /</span>
                            <span class="dotted-line" style="min-width: 380px;">
                                {{ $shipment->notes }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Footer Disclaimer -->
            <div class="disclaimer">
                * نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا.<br>
                * نحن غير مسؤولين عن الأشياء الثمينة الممنوع إرسالها في الطرود.<br>
                * نحن غير مسؤولين عن بقاء الطرود أكثر من شهر.<br>
                * نحن غير مسؤولين عن الحريق وحوادث السير.<br>
                * يرجى التأكد من بيانات السند قبل المغادرة.
            </div>

            <!-- Footer Black Box -->
            <div class="footer-box">
                أرقام الإدارة العامة لجميع الفروع
                <br>
                <span>781216757 - 773136727 - 774996316 - 773374176</span>
            </div>

        </div>
    </div>

</body>

</html>
