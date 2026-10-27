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

        $activeBranchAddresses = $branch_addresses ?? ($company['branch_addresses'] ?? []);

        // 2. شروط وأحكام السند (عرض جميع الشروط كاملة)
        $filteredTerms = [];
        if (!empty($terms_and_conditions) && is_array($terms_and_conditions)) {
            foreach ($terms_and_conditions as $term) {
                $trimmed = trim($term);
                if ($trimmed !== '') {
                    $filteredTerms[] = $trimmed;
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
            background-color:
                {{ $primary }};
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
            color:
                {{ $primary }};
        }

        .bg-primary {
            background-color:
                {{ $primary }};
        }

        .border-primary {
            border-color:
                {{ $primary }};
        }

        .text-gray {
            color: #475569;
        }

        /* 1. الترويسة */
        .header-table {
            height: 25.5mm;
            margin-bottom: 1.5mm;
        }

        /* خط فاصل أخضر ديكوري أسفل الترويسة */
        .header-divider {
            height: 1.5px;
            background-color: {{ $primary }};
            margin-bottom: 2.2mm;
            width: 100%;
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
            color:
                {{ $primary }};
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
            margin-bottom: 1.5mm;
            height: 44mm;
            min-height: 44mm;
            max-height: 44mm;
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
            box-sizing: border-box;
        }

        .details-grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 0.5mm;
        }

        .details-grid-table td {
            padding: 0.8mm 2mm;
            height: 5mm;
            vertical-align: middle;
        }

        .details-notes-container {
            margin-top: auto;
            border-top: 1px dashed #cbd5e1;
            background: #f8fafc;
            padding: 1.2mm 2.5mm;
            min-height: 7.5mm;
            max-height: 8.5mm;
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
            box-sizing: border-box;
        }

        .finance-header {
            background:
                {{ $primary }};
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: bold;
            text-align: center;
            padding: 0;
            height: 5mm;
            min-height: 5mm;
            max-height: 5mm;
            line-height: 5mm;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-sizing: border-box;
        }

        .finance-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            flex-grow: 1;
        }

        .finance-table td {
            padding: 0.2mm 2mm;
            font-size: 8pt;
            line-height: 1.2;
            font-weight: bold;
            border-bottom: 1px solid #f1f5f9;
            height: 4.8mm;
            vertical-align: middle;
            box-sizing: border-box;
        }

        .finance-table tr:last-child td {
            border-bottom: none;
        }

        .finance-payment-method {
            margin-top: auto;
            background: #ecfdf5;
            border-top: 1px dashed {{ $primary }};
            height: 5.8mm;
            min-height: 5.8mm;
            max-height: 5.8mm;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 8pt;
            font-weight: bold;
            color:
                {{ $primary }};
            padding: 0 2mm;
            box-sizing: border-box;
            flex-shrink: 0;
        }

        /* 5. الشروط والأحكام */
        /* 5. الشروط والأحكام */
        .terms-section {
            height: auto;
            overflow: visible;
            margin-bottom: 1.5mm;
        }

        .terms-table {
            width: 100%;
            font-size: 7pt;
            /* حجم خط مثالي ليتسع حتى 12 شرطاً بأريحية */
            line-height: 1.3;
            color: #334155;
            font-weight: 600;
            table-layout: fixed;
            direction: rtl;
        }

        .terms-table td {
            width: 50%;
            padding: 0.5mm 1.5mm;
            vertical-align: top;
            text-align: right;
        }

        /* 6. الفوتر */
        .footer-bar {
            height: 5.5mm;
            background:
                {{ $primary }};
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
        <button type="button" onclick="window.print()" class="btn-action btn-print"
            title="طباعة السند فورياً عبر المتصفح">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            <span>طباعة السند</span>
        </button>

        <button type="button"
            onclick="if(window.history.length > 1){ window.history.back(); } else { window.close(); }"
            class="btn-action btn-close" title="إغلاق الصفحة">
            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
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
            <!-- 1. HEADER (الترويسة الرئيسية بتصميم متناسق وموزع بدقة) -->
            <table class="header-table" style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <tr>
                    {{-- يمين: بيانات الشركة والخدمة والفرع مع خط عمودي أنيق --}}
                    <td style="width: 38%; text-align: right; vertical-align: top;">
                        <div style="margin-bottom: 1px;">
                            <h1
                                style="color: {{ $primary }}; font-size: 15pt; font-weight: 900; line-height: 1.15; margin: 0; padding: 0;">
                                {{ $company['name'] ?? 'شركة النقل' }}
                            </h1>
                        </div>
                        <div
                            style="font-size: 8pt; font-weight: 700; color: #475569; margin-top: 1px; margin-bottom: 2.5px; line-height: 1.2;">
                            لخدمات النقل البري وشحن الطرود والبضائع
                        </div>

                        <div
                            style="border-right: 2.5px solid {{ $primary }}; padding-right: 6px; margin-top: 2px;">
                            @if (empty($activeBranchAddresses))
                                @if (!empty($company['headquarters']))
                                    <div
                                        style="margin-bottom: 1.5px; font-size: 8pt; font-weight: 800; color: #1e293b; line-height: 1.25;">
                                        {{ $company['headquarters']['title'] }}
                                        @if (!empty($company['headquarters']['phones']))
                                            <span style="color: #475569; font-weight: bold;"> - هاتف: </span>
                                            <span dir="ltr"
                                                style="font-family: sans-serif; color: #0f172a; font-weight: 800;">{{ $company['headquarters']['phones'] }}</span>
                                        @endif
                                    </div>
                                @elseif (!empty($company['main_branch']))
                                    <div
                                        style="margin-bottom: 1.5px; font-size: 8pt; font-weight: 800; color: #1e293b; line-height: 1.25;">
                                        {{ $company['main_branch']['title'] }}
                                        @if (!empty($company['main_branch']['phones']))
                                            <span style="color: #475569; font-weight: bold;"> - هاتف: </span>
                                            <span dir="ltr"
                                                style="font-family: sans-serif; color: #0f172a; font-weight: 800;">{{ $company['main_branch']['phones'] }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div
                                        style="margin-bottom: 1.5px; font-size: 8pt; font-weight: 800; color: #1e293b; line-height: 1.25;">
                                        {{ $user_branch ?? 'المركز الرئيسي' }}
                                    </div>
                                @endif

                                @if (!empty($company['other_phones']))
                                    <div
                                        style="font-size: 7.5pt; font-weight: bold; color: #64748b; line-height: 1.2; margin-bottom: 1.5px;">
                                        فروع أخرى: <span dir="ltr"
                                            style="font-family: sans-serif; color: #334155; font-weight: 700;">{{ $company['other_phones'] }}</span>
                                    </div>
                                @endif
                            @endif

                            {{-- شارة الفرع المتميزة والواضحة --}}
                            <div style="margin-top: 2.5px;">
                                <span
                                    style="display: inline-block; background-color: rgba(5, 150, 105, 0.08); color: {{ $primary }}; border: 1px solid {{ $primary }}; padding: 1.5px 8px; border-radius: 12px; font-size: 7.5pt; font-weight: 800; line-height: 1.2;">
                                    الفرع:
                                    {{ $sender_branch ?? ($company['main_branch']['title'] ?? ($user_branch ?? 'المركز الرئيسي')) }}
                                </span>
                            </div>
                        </div>
                    </td>

                    {{-- وسط: الشعار متناسق وعنوان السند في بانر أخضر دائري بارز ومحكم --}}
                    <td style="width: 24%; text-align: center; vertical-align: middle;">
                        @if ($logo)
                            <div style="margin-bottom: 1.5mm; text-align: center;">
                                <img src="{{ $logo }}"
                                    style="max-width: 32mm; max-height: 15mm; object-fit: contain; margin: 0 auto; display: block;"
                                    alt="Logo">
                            </div>
                        @else
                            <div style="height: 10mm;"></div>
                        @endif
                        <div style="text-align: center;">
                            <span
                                style="display: inline-block; background-color: {{ $primary }}; color: #ffffff; padding: 2.5px 12px; border-radius: 20px; font-size: 10.5pt; font-weight: 900; letter-spacing: 0.5px; box-shadow: 0 1px 3px rgba(0,0,0,0.12); white-space: nowrap;">
                                {{ $title ?? 'سند إرسال طرد' }}
                            </span>
                        </div>
                    </td>

                    {{-- يسار: بيانات السند والفرع + رمز الـ QR Code في أقصى الزاوية العلوية (بدون باركود سفلي تماماً) --}}
                    <td style="width: 38%; vertical-align: top; text-align: left;">
                        <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                            <tr>
                                {{-- بيانات السند والفرع على اليمين باتجاه الشعار --}}
                                <td style="text-align: right; vertical-align: top; padding-left: 6px;">
                                    <div
                                        style="color: {{ $primary }}; font-size: 10.5pt; font-weight: 900; margin-bottom: 2px; white-space: nowrap;">
                                        رقم السند: #{{ $bond_number ?? '---' }}
                                    </div>
                                    <div
                                        style="font-size: 8.5pt; font-weight: 800; color: #1e293b; margin-bottom: 2px;">
                                        التاريخ: <span dir="ltr"
                                            style="font-family: sans-serif;">{{ $date ?? '---' }}</span>
                                    </div>

                                    @if (!empty($hijri_date))
                                        <div
                                            style="font-size: 8pt; font-weight: bold; color: #64748b; margin-bottom: 2px;">
                                            الموافق: <span dir="ltr"
                                                style="font-family: sans-serif;">{{ $hijri_date }}</span>
                                        </div>
                                    @endif

                                    @if (!empty($sender_branch_phone))
                                        <div
                                            style="font-size: 8pt; font-weight: bold; color: #475569; margin-top: 2px;">
                                            هاتف الفرع: <span dir="ltr"
                                                style="color: #0f172a; font-family: sans-serif; font-weight: 800;">{{ $sender_branch_phone }}</span>
                                        </div>
                                    @elseif (!empty($company['main_branch']['phones']))
                                        <div
                                            style="font-size: 8pt; font-weight: bold; color: #475569; margin-top: 2px;">
                                            هاتف الفرع: <span dir="ltr"
                                                style="color: #0f172a; font-family: sans-serif; font-weight: 800;">{{ $company['main_branch']['phones'] }}</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- رمز الـ QR Code في أقصى الزاوية العلوية اليسرى بمحاذاة الحافة تماماً --}}
                                <td style="width: 23mm; text-align: left; vertical-align: top; padding: 0;">
                                    @if (!empty($bond_number))
                                        @php
                                            $qrCodeSvg = null;
                                            try {
                                                if (class_exists(\Milon\Barcode\Facades\DNS2DFacade::class)) {
                                                    $qrCodeSvg = \Milon\Barcode\Facades\DNS2DFacade::getBarcodeSVG(
                                                        (string) ($tracking_code ?? $bond_number),
                                                        'QRCODE',
                                                        2.0,
                                                        2.0,
                                                        'black',
                                                        false,
                                                    );
                                                }
                                            } catch (\Throwable $e) {
                                                $qrCodeSvg = null;
                                            }
                                        @endphp
                                        @if (!empty($qrCodeSvg))
                                            <div
                                                style="display: inline-block; padding: 2px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.06); line-height: 0;">
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

            <!-- خط فاصل أخضر أفقي متصل وأنيق أسفل الترويسة -->
            <div class="header-divider"></div>

            <!-- عناوين الفروع المعتمدة في أعلى السند -->
            @if(!empty($activeBranchAddresses) && count($activeBranchAddresses) > 0)
                <div class="branch-addresses-bar" style="margin-top: 1mm; margin-bottom: 1.5mm; padding: 2px 5px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px;">
                    @foreach($activeBranchAddresses as $branchItem)
                        <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 1px; font-size: 7pt; font-weight: 700; color: #0f172a; line-height: 1.25;">
                            <span style="background-color: #0f172a; color: #ffffff; padding: 1px 6px; border-radius: 3px; font-weight: 900; font-size: 6.5pt; white-space: nowrap; flex-shrink: 0;">
                                {{ $branchItem['name'] ?? '' }}
                            </span>
                            <span style="flex-grow: 1; word-break: break-word;">
                                {{ $branchItem['address'] ?? '' }}
                                @if(!empty($branchItem['phone']))
                                    <span style="color: #475569; margin-right: 4px;"> - هاتف: <span dir="ltr" style="font-family: sans-serif; font-weight: bold;">{{ $branchItem['phone'] }}</span></span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

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
                            <div class="box-value" dir="ltr" style="text-align: right; font-family: sans-serif;">
                                {{ $sender_phone ?? '---' }}</div>
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
                            <div class="box-value" dir="ltr"
                                style="text-align: right; font-family: sans-serif;">
                                {{ $receiver_phone ?? '---' }}</div>
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
                    @php
                        $shipmentFields = [];
                        if (!empty($package_type) && $package_type !== '---') {
                            $shipmentFields[] = ['label' => 'نوع الطرد:', 'value' => $package_type];
                        }
                        if (!empty($parcels_quantity)) {
                            $shipmentFields[] = ['label' => 'عدد الطرود:', 'value' => $parcels_quantity];
                        }
                        if (!empty($weight) && $weight !== '---') {
                            $shipmentFields[] = ['label' => 'الوزن/العدد:', 'value' => $weight];
                        }
                        if (!empty($honey_details) && $honey_details !== '---') {
                            $shipmentFields[] = ['label' => 'تفاصيل إضافية:', 'value' => $honey_details];
                        }
                        if (!empty($show_other_fee_in_receipt) && !empty($other_fee) && (float) $other_fee > 0) {
                            $feeText = number_format((float) $other_fee, 0) . ' ر.ي';

                            if (!empty($other_fee_description)) {
                                $feeText .= ' (' . $other_fee_description . ')';
                            }

                            $shipmentFields[] = ['label' => 'أجور أخرى:', 'value' => $feeText];
                        }
                    @endphp

                    @if (!empty($shipmentFields))
                        <table class="details-grid-table">
                            @foreach (array_chunk($shipmentFields, 2) as $row)
                                <tr>
                                    @foreach ($row as $field)
                                        <td style="width: 50%;">
                                            <span
                                                style="font-size: 8.5pt; color: #475569; font-weight: bold;">{{ $field['label'] }}</span>
                                            <span
                                                style="font-size: 9.5pt; font-weight: 800; color: #0f172a; margin-right: 2px;">{{ $field['value'] }}</span>
                                        </td>
                                    @endforeach
                                    @if (count($row) === 1)
                                        <td style="width: 50%;"></td>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    @endif

                    {{-- خانة الملاحظات المعزولة في سطر مستقل أنيق --}}
                    <div class="details-notes-container"
                        style="display: flex; align-items: flex-start; gap: 4px; height: auto;">
                        <span
                            style="font-size: 8.5pt; color: #475569; font-weight: bold; white-space: nowrap; flex-shrink: 0;">
                            الملاحظات:
                        </span>
                        <div
                            style="font-size: 8.5pt; font-weight: 600; color: #1e293b; line-height: 1.4; word-break: break-word; flex-grow: 1;">
                            {{ $notes ?? 'لا توجد ملاحظات إضافية' }}
                        </div>
                    </div>
                </div>

                {{-- تفاصيل المبلغ والمالية --}}
                <div class="finance-box">
                    <div class="finance-header">تفاصيل المبلغ</div>
                    <table class="finance-table">
                        @if (!empty($other_fee) && (float) $other_fee > 0)
                            <tr>
                                <td style="color: #475569; width: 45%;">تكلفة النقل</td>
                                <td style="color: {{ $primary }}; text-align: left; font-weight: 900; font-size: 9pt;"
                                    dir="ltr">
                                    {{ $transport_fee }}
                                </td>
                            </tr>
                            @if (!empty($show_other_fee_in_receipt) && !empty($other_fee_description))
                                <tr>
                                    <td style="color: #475569;">
                                        أجور أخرى

                                        <span
                                            style="font-size: 7.5pt; color: #64748b; font-weight: normal; margin-right: 2px;">({{ $other_fee_description }})</span>

                                    </td>
                                    <td style="text-align: left; color: #111827; font-weight: 800; font-size: 8.5pt;"
                                        dir="ltr">
                                        {{ number_format((float) ($other_fee ?? 0), 0) }}
                                    </td>

                                </tr>
                                <tr>
                                    <td style="color: #0f172a; font-weight: 900;">الإجمالي</td>
                                    <td style="color: {{ $primary }}; text-align: left; font-weight: 900; font-size: 9.5pt;"
                                        dir="ltr">
                                        {{ $total_amount ?? '0' }}
                                    </td>
                                </tr>
                            @endif

                        @else
                            <tr>
                                <td style="color: #475569; width: 45%;">الإجمالي</td>
                                <td style="color: {{ $primary }}; text-align: left; font-weight: 900; font-size: 9.5pt;"
                                    dir="ltr">
                                    {{ $total_amount ?? '0' }}
                                </td>
                            </tr>
                        @endif
                        
                      {{-- يظهر سطر "المسدد" فقط إذا كانت طريقة الدفع: دفع جزئي --}}
                        @if(isset($payment_key) && $payment_key === 'partial_payment')
                        <tr>
                            <td style="color: #475569;">المسدد</td>
                            <td style="text-align: left; color: #111827; font-weight: 800; font-size: 8.5pt;" dir="ltr">
                                {{ $partial_amount ?? '0' }}
                            </td>
                        </tr>
                        @endif
                        
                        <tr>
                            <td style="color: {{ $primary ?? '#059669' }};">المتبقي</td>
                            <td style="color: {{ $primary ?? '#059669' }}; text-align: left; font-weight: 900; font-size: 9.5pt;" dir="ltr">
                                {{-- إذا كان الدفع مقدماً اجعل المتبقي 0، وإلا اعرض القيمة القادمة من الكنترولر --}}
                                {{ (isset($payment_key) && $payment_key === 'prepaid') ? '0' : ($remaining_amount ?? '0') }}
                            </td>
                        </tr>
                    </table>

                    {{-- شريط طريقة الدفع في الأسفل بشكل متناسق ومريح --}}
                    <div class="finance-payment-method">
                        <span style="color: #475569; font-size: 8pt;">طريقة الدفع:</span>
                        <span
                            style="color: {{ isset($is_price_agreed) && !$is_price_agreed ? '#d97706' : $primary }}; font-weight: 900; font-size: 8.5pt;">{{ $payment_method ?? '---' }}</span>
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
                                    <td>
                                        <!-- رقم الشرط معزول في اتجاهه ومميز بلون الهوية -->
                                        <span
                                            style="display: inline-block; font-weight: 800; color: {{ $primary }}; margin-left: 3px; unicode-bidi: isolate;">
                                            {{ $termCounter++ }}-
                                        </span>
                                        <!-- النص يحدد اتجاهه تلقائياً دون عكس الترقيم -->
                                        <span dir="auto"
                                            style="word-break: break-word;">{{ $term }}</span>
                                    </td>
                                @endforeach
                                @if (count($row) === 1)
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td><span style="font-weight: bold; color: {{ $primary }};">1-</span> أي سند لا يحمل
                                ختم المكتب غير مقبول.</td>
                            <td><span style="font-weight: bold; color: {{ $primary }};">2-</span> المكتب غير
                                مسؤول عن الطرد بعد شهر من استلامه.</td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold; color: {{ $primary }};">3-</span> المكتب غير
                                مسؤول عن الإجراءات الأمنية والجمركية.</td>
                            <td><span style="font-weight: bold; color: {{ $primary }};">4-</span> نحن غير مسؤولين
                                عن الحريق وحوادث السير.</td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold; color: {{ $primary }};">5-</span> الرجاء التأكد
                                من بيانات السند قبل المغادرة.</td>
                            <td></td>
                        </tr>
                    @endif
                </table>
            </div>

            <!-- 6. FOOTER (الفوتر وشريط الاتصال) -->
            <div class="footer-bar">
                أرقام الإدارة العامة لجميع الفروع: <span style="margin-right: 4px; font-family: sans-serif;"
                    dir="ltr">{{ $company['phone'] ?? ($company['headquarters']['phones'] ?? ($company['main_branch']['phones'] ?? '')) }}</span>
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
