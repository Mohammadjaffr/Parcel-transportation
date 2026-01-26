<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        /* إعدادات الخط والصفحة */
        body {
            font-family: 'aealarabiya', 'dejavusans', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.4;
        }

        @page {
            margin: 10mm;
        }

        /* الهيدر الاحترافي */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #fb6514;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .brand-name {
            color: #fb6514;
            font-size: 32pt;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }

        .brand-subtitle {
            color: #333;
            font-size: 15pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .document-title-badge {
            background-color: #333;
            color: #fff;
            padding: 4px 15px;
            font-size: 13pt;
            display: inline-block;
            margin-top: 10px;
            margin-left: 10px;
            border-radius: 4px;
        }

        .header-info-text {
            font-size: 11pt;
            color: #555;
            line-height: 1.6;
        }

        .header-phones {
            color: #fb6514;
            font-weight: bold;
            font-size: 12pt;
            margin-top: 5px;
        }

        /* بيانات المعاملة */
        .trip-info-box {
            width: 100%;
            background-color: #fcfcfc;
            border: 1px solid #eee;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .trip-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .trip-info-table tr td {
            padding: 6px 0;
        }

        .label {
            color: #fb6514;
            font-weight: bold;
            width: 120px;
            font-size: 12pt;
        }

        .value {
            font-weight: bold;
            font-size: 13pt;
            color: #222;
        }

        /* صندوق المبلغ المميز */
        .amount-highlight-box {
            width: 100%;
            background-color: {{ $transaction->category && $transaction->category->type == 'in' ? '#e8f5e9' : '#ffebee' }};
            border: 2px solid {{ $transaction->category && $transaction->category->type == 'in' ? '#4caf50' : '#f44336' }};
            border-radius: 4px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }

        .amount-highlight-box .amount-label {
            font-size: 13pt;
            color: #333;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .amount-highlight-box .amount-value {
            font-size: 22pt;
            font-weight: bold;
            color: {{ $transaction->category && $transaction->category->type == 'in' ? '#2e7d32' : '#c62828' }};
        }

        /* قسم التواقيع - 3 أعمدة */
        .signatures-container {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        .sig-cell {
            width: 33.33%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }

        .sig-title {
            font-size: 12pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 35px;
            display: block;
        }

        .sig-line {
            width: 80%;
            margin: 10px auto;
            border-top: 1px solid #333;
        }

        /* الفوتر */
        .footer-text {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10pt;
            color: #666;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    {{-- الهيدر الاحترافي --}}
    <table class="header-table">
        <tr>
            <td width="35%" style="vertical-align: top;">
                <h1 class="brand-name">الـزاجـل</h1>
                <div class="brand-subtitle">للنقل والشحن السريع</div>
                <div class="document-title-badge">
                    @if ($transaction->category && $transaction->category->type == 'in')
                        سند قبض
                    @else
                        سند صرف
                    @endif
                </div>
                <div style="font-size: 10.5pt; margin-top: 8px; color: #666;">
                    {{ $transaction->category && $transaction->category->type == 'in' ? 'Payment Receipt' : 'Payment Voucher' }}<br>
                    رقم السند: {{ $transaction->receipt_number ?? $transaction->id }}
                </div>
            </td>
            <td width="30%" style="text-align: center; vertical-align: middle;">
                <img src="{{ public_path('images/new.svg') }}" style="width: 200px; height: auto;">
            </td>
            <td width="35%" style="text-align: left; vertical-align: top;" class="header-info-text">
                <div style="font-weight: bold; font-size: 12px; color: #333; margin-bottom: 4px;">
                    فرع / القطن -عمارة شظي - خلف بنك التضامن
                </div>
                <div style="font-weight: bold; font-size: 9px; color: #000;">
                    781216757 - 773136727 - 730831802
                </div>
                <div class="header-info">الفرع / المكلا - اربعين شقة - بجانب بنك المجاد</div>
                <div style="margin-top: 2px; font-size: 12px;">خدمة الشحن إلى جميع المحافظات ودول الخليج</div>
                <div class="header-phones">
                    للتواصل / 774996316 - 772038561<br>735637947
                </div>
            </td>
        </tr>
    </table>

    {{-- بيانات المعاملة --}}
    <div class="trip-info-box">
        <table class="trip-info-table">
            <tr>
                <td class="label">التاريخ:</td>
                <td class="value">{{ $transaction->created_at->format('Y-m-d h:i A') }}</td>
                <td class="label" style="padding-right: 20px;">الفرع:</td>
                <td class="value">{{ $transaction->branch ? $transaction->branch->name : $transaction->branch_code }}
                </td>
            </tr>
            <tr>
                <td class="label">الفئة:</td>
                <td class="value">{{ $transaction->category ? $transaction->category->name : 'غير محدد' }}</td>
                @if ($transaction->reference_number)
                    <td class="label" style="padding-right: 20px;">رقم المرجع:</td>
                    <td class="value">{{ $transaction->reference_number }}</td>
                @else
                    <td colspan="2"></td>
                @endif
            </tr>
            @if ($transaction->customer)
                <tr>
                    <td class="label">اسم العميل:</td>
                    <td class="value">{{ $transaction->customer->name }}</td>
                    <td class="label" style="padding-right: 20px;">رقم الهاتف:</td>
                    <td class="value">{{ $transaction->customer->phone }}</td>
                </tr>
            @endif
            @if ($transaction->user)
                <tr>
                    <td class="label">تم الإنشاء بواسطة:</td>
                    <td class="value" colspan="3">{{ $transaction->user->name }}</td>
                </tr>
            @endif
        </table>
    </div>

    {{-- صندوق المبلغ المميز --}}
    <div class="amount-highlight-box">
        <div class="amount-label">
            @if ($transaction->category && $transaction->category->type == 'in')
                المبلغ المستلم
            @else
                المبلغ المدفوع
            @endif
        </div>
        <div class="amount-value"><span dir="ltr" class="font-bold text-red-600">
                {{ number_format($transaction->amount) }}
            </span> ر.ي</div>
    </div>

    {{-- البيان --}}
    @if ($transaction->description)
        <div class="trip-info-box">
            <table class="trip-info-table">
                <tr>
                    <td class="label">البيان:</td>
                    <td class="value">{{ $transaction->description }}</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- التواقيع - 3 أعمدة --}}
    <table class="signatures-container">
        <tr>
            <td class="sig-cell">
                <span class="sig-title">المستلم / الدافع</span>
                <div class="sig-line"></div>
            </td>
            <td class="sig-cell">
                <span class="sig-title">المحاسب</span>
                <div class="sig-line"></div>
            </td>
            <td class="sig-cell">
                <span class="sig-title">المدير</span>
                <div class="sig-line"></div>
            </td>
        </tr>
    </table>

    {{-- الفوتر --}}
    <div class="footer-text">
        هذا سند الكتروني تم إنشاؤه تلقائياً من نظام إدارة الشحنات<br>
        تاريخ الطباعة: {{ now()->format('Y-m-d h:i A') }}
    </div>
</body>

</html>
