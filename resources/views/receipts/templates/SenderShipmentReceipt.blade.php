<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند إرسال طرد - {{ $bond_number ?? '' }}</title>

    <!-- Google Fonts: Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    @php
        // 1. تحديد السمة اللونية لهوية الشركة ديناميكياً
        $primary = !empty($design['primary_color']) ? $design['primary_color'] : '#059669';

        $logo = $company['logo'] ?? null;

        // 2. فلترة شروط وأحكام السند
        $filteredTerms = [];
        if (!empty($terms_and_conditions) && is_array($terms_and_conditions)) {
            foreach ($terms_and_conditions as $term) {
                if (strpos($term, 'إثبات استلام') === false && strpos($term, 'البطاقة الشخصية') === false) {
                    $filteredTerms[] = trim($term);
                }
            }
        }
    @endphp

    <style>
        /* إعدادات حجم الورقة للطباعة A5 بالعرض بدقة تامة */
        @page {
            size: A5 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* تنسيق الصفحة على الشاشة العادية */
        body {
            background-color: #f1f5f9;
            direction: rtl;
            text-align: right;
            font-family: 'Tajawal', 'aealarabiya', 'DejaVu Sans', 'Arial', sans-serif;
            color: #111827;
            margin: 0;
            padding: 20px 0 80px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
        }

        /* حاوية السند كـ ورقة A5 Landscape حقيقية متناسقة في المنتصف */
        .receipt-container {
            width: 210mm;
            height: 148mm;
            min-width: 210mm;
            max-width: 210mm;
            min-height: 148mm;
            max-height: 148mm;
            margin: 0 auto;
            padding: 4.5mm 6.5mm;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        /* شريط أزرار التحكم والطباعة العائم (يظهر على الشاشة فقط) */
        .floating-actions-bar {
            position: fixed;
            bottom: 24px;
            left: 24px;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 22px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Tajawal', sans-serif;
            border-radius: 50px;
            cursor: pointer;
            border: none;
            outline: none;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.15);
            text-decoration: none;
        }

        .btn-print {
            background-color: {{ $primary }};
            color: #ffffff;
        }

        .btn-print:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
        }

        .btn-print:active {
            transform: translateY(0);
        }

        .btn-close {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-close:hover {
            background-color: #f8fafc;
            color: #0f172a;
            transform: translateY(-2px);
        }

        /* وضع الطباعة والتصدير كـ PDF (Direct Browser Print) */
        @media print {
            .no-print,
            .floating-actions-bar,
            .loading-overlay {
                display: none !important;
            }

            html,
            body {
                width: 210mm !important;
                height: 148mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                overflow: hidden !important;
            }

            .receipt-container {
                width: 210mm !important;
                height: 148mm !important;
                min-width: 210mm !important;
                max-width: 210mm !important;
                min-height: 148mm !important;
                max-height: 148mm !important;
                margin: 0 !important;
                padding: 4mm 6mm !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                overflow: hidden !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
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

        /* العلامة المائية */
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
            height: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* الألوان التنسيقية */
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

        /* 1. الترويسة */
        .header-table {
            height: 24mm;
            margin-bottom: 2mm;
        }

        /* 2. شريط العنوان */
        .title-bar {
            height: 7.5mm;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 11.5pt;
            font-weight: bold;
            background: {{ $primary }};
            margin-bottom: 2.2mm;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        /* 3. بيانات المرسل والمستلم */
        .split-section {
            display: flex;
            gap: 3.5mm;
            margin-bottom: 2.2mm;
            height: 25mm;
        }

        .split-box {
            width: 50%;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            background: #ffffff;
        }

        .box-header {
            background: #f8fafc;
            color: {{ $primary }};
            font-size: 9pt;
            font-weight: 800;
            padding: 1mm 2.5mm;
            border-bottom: 1px solid #e5e7eb;
        }

        .box-content {
            padding: 1mm 2mm;
        }

        .box-row {
            display: flex;
            margin-bottom: 1.5px;
            height: 5.5mm;
            align-items: center;
        }

        .box-label {
            width: 28%;
            font-size: 8.5pt;
            color: #475569;
            font-weight: bold;
        }

        .box-value {
            width: 72%;
            font-size: 9.5pt;
            font-weight: 800;
            color: #0f172a;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* 4. تفاصيل الشحنة والمبالغ */
        .details-finance-section {
            display: flex;
            gap: 3.5mm;
            margin-bottom: 2.2mm;
            height: 33mm;
            min-height: 33mm;
            max-height: 33mm;
        }

        .details-box {
            width: 65%;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .details-grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 0.5mm;
        }

        .details-grid-table td {
            padding: 1mm 2mm;
            height: 5.5mm;
            vertical-align: middle;
        }

        .details-notes-container {
            margin-top: auto;
            border-top: 1px dashed #cbd5e1;
            background: #f8fafc;
            padding: 1.2mm 2.5mm;
            min-height: 7.2mm;
            max-height: 7.5mm;
            display: flex;
            align-items: flex-start;
            gap: 4px;
            box-sizing: border-box;
        }

        .finance-box {
            width: 35%;
            border: 1.5px solid {{ $primary }};
            border-radius: 4px;
            overflow: hidden;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .finance-header {
            background: {{ $primary }};
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
            padding: 1.2mm;
            height: 5.2mm;
            min-height: 5.2mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .finance-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            flex-grow: 1;
        }

        .finance-table td {
            padding: 0.9mm 2mm;
            font-size: 8.5pt;
            font-weight: bold;
            border-bottom: 1px solid #f1f5f9;
            height: 5.2mm;
            vertical-align: middle;
        }

        .finance-table tr:last-child td {
            border-bottom: none;
        }

        .finance-payment-method {
            margin-top: auto;
            background: #ecfdf5;
            border-top: 1px dashed {{ $primary }};
            height: 6.2mm;
            min-height: 6.2mm;
            max-height: 6.2mm;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 8.5pt;
            font-weight: bold;
            color: {{ $primary }};
            padding: 0 2mm;
            box-sizing: border-box;
        }

        /* 5. الشروط والأحكام */
        .terms-section {
            height: 19mm;
            overflow: hidden;
            margin-bottom: 1.5mm;
        }

        .terms-table {
            width: 100%;
            font-size: 7.5pt;
            line-height: 1.25;
            color: #334155;
            font-weight: 600;
        }

        .terms-table td {
            width: 50%;
            padding-bottom: 1px;
            padding-left: 2mm;
            vertical-align: top;
        }

        /* 6. الفوتر */
        .footer-bar {
            height: 5.5mm;
            background: {{ $primary }};
            color: #ffffff;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8.5pt;
            font-weight: bold;
            border-radius: 4px;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- شريط أزرار الطباعة والتحكم (يختفي تلقائياً عند الطباعة) -->
    <div class="floating-actions-bar no-print">
        <button type="button" onclick="window.print()" class="btn-action btn-print" title="طباعة السند فورياً عبر المتصفح">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            <span>طباعة السند</span>
        </button>

        <button type="button" onclick="if(window.history.length > 1){ window.history.back(); } else { window.close(); }" class="btn-action btn-close" title="إغلاق الصفحة">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>إغلاق</span>
        </button>
    </div>

    <!-- حاوية سند الشحن بمقاس A5 بالعرض -->
    <div class="receipt-container">
        @if ($logo)
            <img src="{{ $logo }}" class="watermark" alt="Logo Watermark">
        @endif

        <div class="content-wrapper">
            <!-- 1. HEADER (الترويسة الرئيسية) -->
            <table class="header-table">
                <tr>
                    {{-- يمين: بيانات الشركة والفروع --}}
                    <td style="width: 38%; text-align: right; vertical-align: top;">
                        <div style="margin-bottom: 2px;">
                            <h1 style="color: {{ $primary }}; font-size: 16pt; font-weight: 900; line-height: 1.1; margin: 0; padding: 0;">
                                {{ $company['name'] ?? 'شركة النقل' }}
                            </h1>
                        </div>

                        <div style="border-right: 2.5px solid {{ $primary }}; padding-right: 6px; margin-top: 3px;">
                            @if (!empty($company['main_branch']))
                                <div style="margin-bottom: 2px;">
                                    <div style="font-size: 9.5pt; font-weight: bold; color: #111827;">
                                        {{ $company['main_branch']['title'] }}
                                    </div>
                                    <div style="font-size: 9pt; font-weight: bold; color: #475569;" dir="rtl">
                                        هاتف: <span style="color: #111827; font-family: sans-serif;" dir="ltr">{{ $company['main_branch']['phones'] }}</span>
                                    </div>
                                </div>
                            @else
                                <div style="margin-bottom: 2px;">
                                    <div style="font-size: 9.5pt; font-weight: bold; color: #111827;">
                                        الفرع: {{ $user_branch ?? 'المركز الرئيسي' }}
                                    </div>
                                    <div style="font-size: 9pt; font-weight: bold; color: #475569;" dir="rtl">
                                        هاتف: <span style="color: #111827; font-family: sans-serif;" dir="ltr">{{ $company['main_branch']['phones'] ?? '---' }}</span>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($company['headquarters']) && ($company['main_branch']['title'] ?? '') !== $company['headquarters']['title'])
                                <div style="margin-bottom: 2px;">
                                    <div style="font-size: 8.5pt; font-weight: bold; color: #111827;">
                                        {{ $company['headquarters']['title'] }}
                                    </div>
                                    <div style="font-size: 8.5pt; font-weight: bold; color: #475569;" dir="rtl">
                                        هاتف: <span style="color: #111827; font-family: sans-serif;" dir="ltr">{{ $company['headquarters']['phones'] }}</span>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($company['other_phones']))
                                <div>
                                    <div style="font-size: 8pt; font-weight: bold; color: #64748b;">فروع أخرى:</div>
                                    <div style="font-size: 7.5pt; font-weight: bold; color: #475569; line-height: 1.2;" dir="rtl">
                                        <span style="font-family: sans-serif;" dir="ltr">{{ $company['other_phones'] }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </td>

                    {{-- وسط: الشعار --}}
                    <td style="width: 22%; text-align: center; vertical-align: middle;">
                        @if ($logo)
                            <img src="{{ $logo }}" style="max-width: 36mm; max-height: 18mm; object-fit: contain; margin: 0 auto;" alt="Logo">
                        @endif
                    </td>

                    {{-- يسار: بيانات السند + رمز الـ QR Code في أقصى الزاوية اليسرى تماماً --}}
                    <td style="width: 40%; vertical-align: top; text-align: left;">
                        <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                            <tr>
                                {{-- بيانات السند على اليمين (باتجاه الشعار في المنتصف) --}}
                                <td style="text-align: right; vertical-align: middle; padding-left: 6px;">
                                    <div style="color: {{ $primary }}; font-size: 10.5pt; font-weight: 900; margin-bottom: 2px; white-space: nowrap;">
                                        رقم السند: #{{ $bond_number ?? '---' }}
                                    </div>
                                    <div style="font-size: 9pt; font-weight: bold; color: #111827; margin-bottom: 2px;">
                                        التاريخ: <span dir="ltr" style="font-family: sans-serif;">{{ $date ?? '---' }}</span>
                                    </div>
                                    <div style="font-size: 8.5pt; font-weight: bold; color: #475569;">
                                        الدفع: {{ $payment_method ?? '---' }}
                                    </div>
                                </td>

                                {{-- رمز الـ QR Code في أقصى طرف وزاوية الهيدر يساراً بمحاذاة الحافة تماماً --}}
                                <td style="width: 24mm; text-align: left; vertical-align: middle; padding: 0;">
                                    @if (!empty($bond_number))
                                        @php
                                            $qrCodeSvg = null;
                                            try {
                                                if (class_exists(\Milon\Barcode\Facades\DNS2DFacade::class)) {
                                                    // زيادة حجم الـ QR Code لـ 2.1 لسهولة وسرعة المسح الضوئي
                                                    $qrCodeSvg = \Milon\Barcode\Facades\DNS2DFacade::getBarcodeSVG((string) $bond_number, 'QRCODE', 2.1, 2.1, 'black', false);
                                                }
                                            } catch (\Throwable $e) {
                                                $qrCodeSvg = null;
                                            }
                                        @endphp
                                        @if (!empty($qrCodeSvg))
                                            <div style="display: inline-block; padding: 2px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.06); line-height: 0;">
                                                {!! $qrCodeSvg !!}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- 2. TITLE BAR (شريط عنوان السند) -->
            <div class="title-bar">
                {{ $title ?? 'سند إرسال طرد' }}
            </div>

            <!-- 3. SENDER & RECEIVER (بيانات المرسل والمستلم) -->
            <div class="split-section">
                {{-- بيانات المرسل --}}
                <div class="split-box">
                    <div class="box-header">بيانات المرسل</div>
                    <div class="box-content">
                        <div class="box-row">
                            <div class="box-label">الاسم:</div>
                            <div class="box-value">{{ $sender_name ?? '---' }}</div>
                        </div>
                        <div class="box-row">
                            <div class="box-label">الجوال:</div>
                            <div class="box-value" dir="ltr" style="text-align: right; font-family: sans-serif;">{{ $sender_phone ?? '---' }}</div>
                        </div>
                        <div class="box-row">
                            <div class="box-label">فرع الإرسال:</div>
                            <div class="box-value">{{ $sender_branch ?? '---' }}</div>
                        </div>
                    </div>
                </div>

                {{-- بيانات المستلم --}}
                <div class="split-box">
                    <div class="box-header">بيانات المستلم</div>
                    <div class="box-content">
                        <div class="box-row">
                            <div class="box-label">الاسم:</div>
                            <div class="box-value">{{ $receiver_name ?? '---' }}</div>
                        </div>
                        <div class="box-row">
                            <div class="box-label">الجوال:</div>
                            <div class="box-value" dir="ltr" style="text-align: right; font-family: sans-serif;">{{ $receiver_phone ?? '---' }}</div>
                        </div>
                        <div class="box-row">
                            <div class="box-label">الوجهة:</div>
                            <div class="box-value">{{ $receiver_branch ?? '---' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. SHIPMENT DETAILS & FINANCE (تفاصيل الشحنة والمبلغ) -->
            <div class="details-finance-section">
                {{-- تفاصيل الشحنة --}}
                <div class="details-box">
                    <div class="box-header">تفاصيل الشحنة</div>
                    <table class="details-grid-table">
                        <tr>
                            <td style="width: 50%;">
                                <span style="font-size: 8.5pt; color: #475569; font-weight: bold;">نوع الطرد:</span>
                                <span style="font-size: 9.5pt; font-weight: 800; color: #0f172a; margin-right: 2px;">{{ $package_type ?? '---' }}</span>
                            </td>
                            <td style="width: 50%;">
                                <span style="font-size: 8.5pt; color: #475569; font-weight: bold;">الوزن/العدد:</span>
                                <span style="font-size: 9.5pt; font-weight: 800; color: #0f172a; margin-right: 2px;">{{ $weight ?? '---' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-size: 8.5pt; color: #475569; font-weight: bold;">الرمز:</span>
                                <span style="font-size: 9.5pt; font-weight: 800; color: #0f172a; margin-right: 2px;">{{ $tracking_code ?? '---' }}</span>
                            </td>
                            <td>
                                <span style="font-size: 8.5pt; color: #475569; font-weight: bold;">تفاصيل إضافية:</span>
                                <span style="font-size: 9.5pt; font-weight: 800; color: #0f172a; margin-right: 2px;">{{ $honey_details ?? '---' }}</span>
                            </td>
                        </tr>
                    </table>

                    {{-- خانة الملاحظات المعزولة في سطر مستقل أنيق --}}
                    <div class="details-notes-container">
                        <span style="font-size: 8.5pt; color: #475569; font-weight: bold; white-space: nowrap; flex-shrink: 0;">الملاحظات:</span>
                        <div style="font-size: 8.5pt; font-weight: 600; color: #1e293b; line-height: 1.25; max-height: 5.5mm; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; word-break: break-word;">
                            {{ $notes ?? 'لا توجد ملاحظات إضافية' }}
                        </div>
                    </div>
                </div>

                {{-- تفاصيل المبلغ والمالية --}}
                <div class="finance-box">
                    <div class="finance-header">تفاصيل المبلغ</div>
                    <table class="finance-table">
                        <tr>
                            <td style="color: #475569; width: 45%;">الإجمالي</td>
                            <td style="color: {{ $primary }}; text-align: left; font-weight: 900; font-size: 10pt;" dir="ltr">
                                {{ $total_amount ?? '0' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color: #475569;">المسدد</td>
                            <td style="text-align: left; color: #111827; font-weight: 800; font-size: 9.5pt;" dir="ltr">
                                {{ $partial_amount ?? '0' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="color: {{ $primary }};">المتبقي</td>
                            <td style="color: {{ $primary }}; text-align: left; font-weight: 900; font-size: 10.5pt;" dir="ltr">
                                {{ $remaining_amount ?? '0' }}
                            </td>
                        </tr>
                    </table>

                    {{-- شريط طريقة الدفع في الأسفل بشكل متناسق ومريح --}}
                    <div class="finance-payment-method">
                        <span style="color: #475569; font-size: 8pt;">طريقة الدفع:</span>
                        <span style="color: {{ $primary }}; font-weight: 900; font-size: 9pt;">{{ $payment_method ?? '---' }}</span>
                    </div>
                </div>
            </div>

            <!-- 5. TERMS (الشروط والأحكام بترقيم تسلسلي دقيق وعمودين متناسقين) -->
            <div class="terms-section">
                <table class="terms-table">
                    @php $termCounter = 1; @endphp
                    @if (!empty($filteredTerms) && count($filteredTerms) > 0)
                        @foreach (array_chunk(array_values($filteredTerms), 2) as $row)
                            <tr>
                                @foreach ($row as $term)
                                    <td>{{ $termCounter++ }}- {{ $term }}</td>
                                @endforeach
                                @if (count($row) === 1)
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

            <!-- 6. FOOTER (الفوتر وشريط الاتصال) -->
            <div class="footer-bar">
                أرقام الإدارة العامة لجميع الفروع: <span style="margin-right: 4px; font-family: sans-serif;" dir="ltr">{{ $company['headquarters']['phones'] ?? ($company['main_branch']['phones'] ?? '') }}</span>
            </div>
        </div>
    </div>

    <!-- سكريبت الطباعة الفورية الصرفة بدون أي اتصال بالسيرفر -->
    <script>
        // تفعيل مفتاح الاختصار للطباعة Ctrl + P مباشرة
        document.addEventListener('keydown', function(event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 'p') {
                event.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>
