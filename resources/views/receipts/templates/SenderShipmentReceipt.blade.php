@extends('receipts.layout')

@section('title', 'سند إرسال طرد - ' . ($bond_number ?? ''))

@section('content')
    @php
        // الألوان الديناميكية
        $primary = $design['primary_color'] ?? '#f97316';
        $logo = $company['logo'] ?? null;

        // فلترة ملاحظة الإخلاء من الشروط
        $filteredTerms = [];
        if (!empty($terms_and_conditions) && is_array($terms_and_conditions)) {
            foreach ($terms_and_conditions as $term) {
                if (strpos($term, 'إثبات استلام') === false && strpos($term, 'البطاقة الشخصية') === false) {
                    $filteredTerms[] = $term;
                }
            }
        }
    @endphp

    <style>
        @page {
            size: A5 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 210mm;
            height: 148mm;
            margin: 0;
            padding: 0;
            background: #ffffff !important;
            direction: rtl;
            text-align: right;
            font-family: 'aealarabiya', 'DejaVu Sans', 'Arial', sans-serif;
            color: #111827;
            overflow: hidden;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .receipt-container {
            width: 210mm;
            height: 148mm;
            margin: 0;
            padding: 4mm 6mm;
            box-sizing: border-box;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        @media print {

            html,
            body {
                width: 210mm !important;
                height: 148mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .receipt-container {
                width: 210mm !important;
                height: 148mm !important;
                margin: 0 !important;
                padding: 4mm 6mm !important;
                overflow: hidden !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                box-shadow: none !important;
            }
        }

        .receipt-container,
        .receipt-container table,
        .split-section,
        .details-finance-section,
        .signatures-section,
        .terms-section {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .watermark {
            width: 55mm;
            height: 30mm;
            position: absolute;
            left: 50%;
            top: 52%;
            transform: translate(-50%, -50%);
            opacity: 0.035;
            pointer-events: none;
            z-index: 1;
        }

        .content-wrapper {
            position: relative;
            z-index: 2;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Colors */
        .text-primary {
            color: {{ $primary }};
        }

        .bg-primary {
            background-color: {{ $primary }};
        }

        .border-primary {
            border-color: {{ $primary }};
        }

        .text-gray {
            color: #475569;
        }

        /* 1. Header */
        .header-table {
            height: 24mm;
            margin-bottom: 2.5mm;
        }

        /* 2. Title */
        .title-bar {
            height: 8mm;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 11pt;
            font-weight: bold;
            background: {{ $primary }};
            margin-bottom: 2.5mm;
            border-radius: 4px;
        }

        /* 4. Sender/Receiver */
        .split-section {
            display: flex;
            gap: 4mm;
            margin-bottom: 2.5mm;
            height: 25mm;
        }

        .split-box {
            width: 50%;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .box-header {
            background: #f8fafc;
            color: {{ $primary }};
            font-size: 8pt;
            font-weight: bold;
            padding: 1mm 2mm;
            border-bottom: 1px solid #e5e7eb;
        }

        .box-content {
            padding: 1mm 1.5mm;
        }

        .box-row {
            display: flex;
            margin-bottom: 2px;
            height: 5.5mm;
            align-items: center;
        }

        .box-label {
            width: 28%;
            font-size: 7.5pt;
            color: #475569;
            font-weight: bold;
        }

        .box-value {
            width: 72%;
            font-size: 8.5pt;
            font-weight: bold;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* 5. Details & Finance */
        .details-finance-section {
            display: flex;
            gap: 4mm;
            margin-bottom: 2.5mm;
            height: 33mm;
        }

        .details-box {
            width: 65%;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .finance-box {
            width: 35%;
            border: 1px solid {{ $primary }};
            border-radius: 4px;
            overflow: hidden;
        }

        .finance-header {
            background: {{ $primary }};
            color: #fff;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            padding: 1.5mm;
        }

        .finance-table td {
            padding: 1.5mm 2mm;
            font-size: 8.5pt;
            font-weight: bold;
            border-bottom: 1px solid #e5e7eb;
            height: 6.5mm;
            vertical-align: middle;
        }

        .finance-table tr:last-child td {
            border-bottom: none;
        }

        /* 6. Signatures */
        .signatures-section {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            height: 11mm;
            margin-bottom: 1.5mm;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 1.5mm;
        }

        .staff-row {
            display: flex;
            justify-content: space-between;
            font-size: 7.5pt;
            font-weight: bold;
            color: #475569;
            margin-bottom: 1.5mm;
        }

        .signs-row {
            display: flex;
            justify-content: space-between;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
        }

        .sign-box {
            width: 33.33%;
        }

        /* 7. Terms */
        .terms-section {
            height: 20mm;
            overflow: hidden;
            margin-bottom: 2mm;
        }

        .terms-table {
            width: 100%;
            font-size: 6.5pt;
            line-height: 1.15;
            color: #475569;
            font-weight: bold;
        }

        .terms-table td {
            width: 50%;
            padding-bottom: 1px;
            padding-left: 2mm;
            vertical-align: top;
        }

        /* 8. Footer */
        .footer-bar {
            height: 5.5mm;
            background: {{ $primary }};
            color: #fff;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 4px;
            margin-top: auto;
        }
    </style>

    <div class="receipt-container">
        @if ($logo)
            <img src="{{ $logo }}" class="watermark" alt="">
        @endif

        <div class="content-wrapper">
            <!-- 1. HEADER -->
            <table class="header-table">
                <tr>
                    <td style="width: 37%; text-align: right; vertical-align: top;">
                        <div style="margin-bottom: 2px;">
                            <h1
                                style="color: {{ $primary }}; font-size: 16pt; font-weight: 900; line-height: 1; margin: 0; padding: 0;">
                                {{ $company['name'] ?? 'شركة النقل' }}
                            </h1>
                        </div>

                        <div style="border-right: 2px solid {{ $primary }}; padding-right: 6px; margin-top: 4px;">
                            @if (!empty($company['main_branch']))
                                <div style="margin-bottom: 3px;">
                                    <div style="font-size: 9pt; font-weight: bold; color: #111827;">
                                        {{ $company['main_branch']['title'] }}
                                    </div>
                                    <div style="font-size: 8pt; font-weight: bold; color: #475569;" dir="rtl">
                                        هاتف: <span style="color: #111827;"
                                            dir="ltr">{{ $company['main_branch']['phones'] }}</span>
                                    </div>
                                </div>
                            @else
                                <div style="margin-bottom: 3px;">
                                    <div style="font-size: 9pt; font-weight: bold; color: #111827;">
                                        الفرع: {{ $user_branch ?? 'المركز الرئيسي' }}
                                    </div>
                                    <div style="font-size: 8pt; font-weight: bold; color: #475569;" dir="rtl">
                                        هاتف: <span style="color: #111827;"
                                            dir="ltr">{{ $company['main_branch']['phones'] ?? '---' }}</span>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($company['headquarters']) && ($company['main_branch']['title'] ?? '') !== $company['headquarters']['title'])
                                <div style="margin-bottom: 3px;">
                                    <div style="font-size: 8.5pt; font-weight: bold; color: #111827;">
                                        {{ $company['headquarters']['title'] }}
                                    </div>
                                    <div style="font-size: 8pt; font-weight: bold; color: #475569;" dir="rtl">
                                        هاتف: <span style="color: #111827;"
                                            dir="ltr">{{ $company['headquarters']['phones'] }}</span>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($company['other_phones']))
                                <div>
                                    <div style="font-size: 8pt; font-weight: bold; color: #64748b;">فروع أخرى:</div>
                                    <div style="font-size: 7.5pt; font-weight: bold; color: #475569; line-height: 1.2;"
                                        dir="rtl">
                                        <span dir="ltr">{{ $company['other_phones'] }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td style="width: 26%; text-align: center; vertical-align: top;">
                        @if ($logo)
                            <img src="{{ $logo }}"
                                style="width: 34mm; height: 15mm; object-fit: contain; margin: 0 auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="width: 37%; text-align: left; vertical-align: top;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="text-align: left; vertical-align: middle; padding-left: 4px;">
                                    <div
                                        style="color: {{ $primary }}; font-size: 9pt; font-weight: bold; margin-bottom: 2px;">
                                        رقم السند: #{{ $bond_number ?? '---' }}
                                    </div>
                                    <div style="font-size: 8pt; font-weight: bold; color: #111827; margin-bottom: 2px;"
                                        dir="ltr">
                                        التاريخ: {{ $date ?? '---' }}
                                    </div>
                                    <div style="font-size: 8pt; font-weight: bold; color: #475569;">
                                        الدفع: {{ $payment_method ?? '---' }}
                                    </div>
                                </td>
                                <td style="width: 22mm; text-align: left; vertical-align: middle;">
                                    @if (!empty($bond_number))
                                        {!! \Milon\Barcode\Facades\DNS2DFacade::getBarcodeSVG((string) $bond_number, 'QRCODE', 1.8, 1.8, 'black', false) !!}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- 2. TITLE BAR -->
            <div class="title-bar">
                {{ $title ?? 'سند إرسال طرد' }}
            </div>

            <!-- 4. SENDER & RECEIVER -->
            <div class="split-section">
                <div class="split-box">
                    <div class="box-header">بيانات المرسل</div>
                    <div class="box-content">
                        <div class="box-row">
                            <div class="box-label">الاسم:</div>
                            <div class="box-value">{{ $sender_name ?? '---' }}</div>
                        </div>
                        <div class="box-row">
                            <div class="box-label">الجوال:</div>
                            <div class="box-value" dir="ltr" style="text-align: right;">{{ $sender_phone ?? '---' }}
                            </div>
                        </div>
                        <div class="box-row">
                            <div class="box-label">فرع الإرسال:</div>
                            <div class="box-value">{{ $sender_branch ?? '---' }}</div>
                        </div>
                    </div>
                </div>
                <div class="split-box">
                    <div class="box-header">بيانات المستلم</div>
                    <div class="box-content">
                        <div class="box-row">
                            <div class="box-label">الاسم:</div>
                            <div class="box-value">{{ $receiver_name ?? '---' }}</div>
                        </div>
                        <div class="box-row">
                            <div class="box-label">الجوال:</div>
                            <div class="box-value" dir="ltr" style="text-align: right;">{{ $receiver_phone ?? '---' }}
                            </div>
                        </div>
                        <div class="box-row">
                            <div class="box-label">الوجهة:</div>
                            <div class="box-value">{{ $receiver_branch ?? '---' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. SHIPMENT DETAILS & FINANCE -->
            <div class="details-finance-section">
                <div class="details-box">
                    <div class="box-header">تفاصيل الشحنة</div>
                    <table style="width: 100%; margin-top: 1mm;">
                        <tr>
                            <td style="width: 50%; padding: 1.5mm;">
                                <span style="font-size: 7.5pt; color: #475569; font-weight: bold;">نوع الطرد:</span>
                                <span
                                    style="font-size: 8.5pt; font-weight: bold; margin-right: 2px;">{{ $package_type ?? '---' }}</span>
                            </td>
                            <td style="width: 50%; padding: 1.5mm;">
                                <span style="font-size: 7.5pt; color: #475569; font-weight: bold;">الوزن/العدد:</span>
                                <span
                                    style="font-size: 8.5pt; font-weight: bold; margin-right: 2px;">{{ $weight ?? '---' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 1.5mm;">
                                <span style="font-size: 7.5pt; color: #475569; font-weight: bold;">الرمز:</span>
                                <span
                                    style="font-size: 8.5pt; font-weight: bold; margin-right: 2px;">{{ $tracking_code ?? '---' }}</span>
                            </td>
                            <td style="padding: 1.5mm;">
                                <span style="font-size: 7.5pt; color: #475569; font-weight: bold;">تفاصيل إضافية:</span>
                                <span
                                    style="font-size: 8.5pt; font-weight: bold; margin-right: 2px;">{{ $honey_details ?? '---' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 1.5mm; border-top: 1px solid #e5e7eb;">
                                <span style="font-size: 7.5pt; color: #475569; font-weight: bold;">الملاحظات:</span>
                                <div
                                    style="font-size: 8.5pt; font-weight: bold; max-height: 8mm; overflow: hidden; line-height: 1.25; word-break: break-word;">
                                    {{ $notes ?? 'لا توجد ملاحظات إضافية' }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="finance-box">
                    <div class="finance-header">تفاصيل المبلغ</div>
                    <table class="finance-table">
                        <tr>
                            <td style="color: #475569; width: 45%;">الإجمالي</td>
                            <td style="color: {{ $primary }}; text-align: left;" dir="ltr">
                                {{ $total_amount ?? '0' }}</td>
                        </tr>
                        <tr>
                            <td style="color: #475569;">المسدد</td>
                            <td style="text-align: left;" dir="ltr">{{ $partial_amount ?? '0' }}</td>
                        </tr>
                        <tr>
                            <td style="color: {{ $primary }};">المتبقي</td>
                            <td style="color: {{ $primary }}; text-align: left;" dir="ltr">
                                {{ $remaining_amount ?? '0' }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"
                                style="text-align: center; font-size: 8pt; color: #475569; background: #f8fafc; border-top: 1px solid #e5e7eb;">
                                {{ $payment_method ?? '---' }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- 6. DRIVER & SIGNATURES -->


            <!-- 7. TERMS -->
            <div class="terms-section">
                <table class="terms-table">
                    @if (!empty($filteredTerms))
                        @foreach (array_chunk($filteredTerms, 2) as $chunk)
                            <tr>
                                @foreach ($chunk as $term)
                                    <td>{{ $loop->parent->index * 2 + $loop->iteration }}- {{ $term }}</td>
                                @endforeach
                                @if (count($chunk) == 1)
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>1- أي سند لا يحمل ختم المكتب غير مقبول.</td>
                            <td>2- المكتب غير مسؤول عن الطرد بعد شهر من استلامه.</td>
                        </tr>
                        <tr>
                            <td>3- المكتب غير مسؤول عن الإجراءات الأمنية والجمركية.</td>
                            <td>4- نحن غير مسؤولين عن الحريق وحوادث السير.</td>
                        </tr>
                        <tr>
                            <td>5- الرجاء التأكد من بيانات السند قبل المغادرة.</td>
                            <td></td>
                        </tr>
                    @endif
                </table>
            </div>

            <!-- 8. FOOTER -->
            <div class="footer-bar">
                أرقام الإدارة العامة لجميع الفروع: <span style="margin-right: 4px;"
                    dir="ltr">{{ $company['headquarters']['phones'] ?? ($company['main_branch']['phones'] ?? '') }}</span>
            </div>
        </div>
    </div>
@endsection
