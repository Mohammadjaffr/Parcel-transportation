<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .subtitle {
            font-size: 14px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 35%;
        }

        .value {
            width: 65%;
        }

        .amount-box {
            background-color:
                {{ $transaction->category && $transaction->category->type == 'in' ? '#e8f5e9' : '#ffebee' }}
            ;
            border: 2px solid
                {{ $transaction->category && $transaction->category->type == 'in' ? '#4caf50' : '#f44336' }}
            ;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .amount-text {
            font-size: 20px;
            font-weight: bold;
            color:
                {{ $transaction->category && $transaction->category->type == 'in' ? '#2e7d32' : '#c62828' }}
            ;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-size: 10px;
            color: #666;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
        }

        .signature-line {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-line div {
            width: 150px;
            border-top: 1px solid #333;
            margin: 40px auto 10px;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <div class="title">
            @if($transaction->category && $transaction->category->type == 'in')
                سند قبض
            @else
                سند صرف
            @endif
        </div>
        <div class="subtitle">
            {{ $transaction->category && $transaction->category->type == 'in' ? 'Payment Receipt' : 'Payment Voucher' }}
        </div>
    </div>

    {{-- Transaction Info --}}
    <table class="info-table">
        <tr>
            <td class="label">رقم السند</td>
            <td class="value">{{ $transaction->id }}</td>
        </tr>
        <tr>
            <td class="label">التاريخ</td>
            <td class="value">{{ $transaction->created_at->format('Y-m-d h:i A') }}</td>
        </tr>
        <tr>
            <td class="label">الفرع</td>
            <td class="value">{{ $transaction->branch ? $transaction->branch->name : $transaction->branch_code }}</td>
        </tr>
        <tr>
            <td class="label">الفئة</td>
            <td class="value">{{ $transaction->category ? $transaction->category->name : 'غير محدد' }}</td>
        </tr>
        @if($transaction->reference_number)
            <tr>
                <td class="label">رقم المرجع</td>
                <td class="value">{{ $transaction->reference_number }}</td>
            </tr>
        @endif
    </table>

    {{-- Customer/Related Info --}}
    @if($transaction->customer)
        <table class="info-table">
            <tr>
                <td class="label">اسم العميل</td>
                <td class="value">{{ $transaction->customer->name }}</td>
            </tr>
            <tr>
                <td class="label">رقم الهاتف</td>
                <td class="value">{{ $transaction->customer->phone }}</td>
            </tr>
        </table>
    @endif

    {{-- Amount Box --}}
    <div class="amount-box">
        <div class="amount-text">
            @if($transaction->category && $transaction->category->type == 'in')
                المبلغ المستلم:
            @else
                المبلغ المدفوع:
            @endif
            {{ number_format($transaction->amount, 2) }} ر.ي
        </div>
    </div>

    {{-- Description --}}
    @if($transaction->description)
        <table class="info-table">
            <tr>
                <td class="label">البيان</td>
                <td class="value">{{ $transaction->description }}</td>
            </tr>
        </table>
    @endif

    {{-- Created By --}}
    @if($transaction->user)
        <table class="info-table">
            <tr>
                <td class="label">تم الإنشاء بواسطة</td>
                <td class="value">{{ $transaction->user->name }}</td>
            </tr>
        </table>
    @endif

    {{-- Signatures --}}
    <div class="signature-section">
        <div class="signature-line">
            <div></div>
            <p>المستلم / الدافع</p>
        </div>
        <div class="signature-line">
            <div></div>
            <p>المحاسب</p>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>هذا سند الكتروني تم إنشاؤه تلقائياً من نظام إدارة الشحنات</p>
        <p>تاريخ الطباعة: {{ now()->format('Y-m-d h:i A') }}</p>
    </div>
</body>

</html>