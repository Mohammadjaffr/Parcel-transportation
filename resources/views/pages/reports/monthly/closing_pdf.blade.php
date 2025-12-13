<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px
        }

        .card {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px
        }
    </style>
</head>

<body>

    <h2 style="text-align:center">الإقفال الشهري</h2>
    <p>الشهر: {{ $month }}</p>

    <div class="card">
        <strong>إجمالي الإيرادات:</strong> {{ number_format($revenue, 2) }}
    </div>

    <div class="card">
        <strong>مديونية العملاء:</strong> {{ number_format($customersDebit, 2) }}
    </div>

    @if (isset($branchesDebt))
        <div class="card">
            <strong>مديونية الفروع:</strong> {{ number_format($branchesDebt, 2) }}
        </div>
    @endif

    <p style="margin-top:30px;text-align:center">
        تم الإقفال بتاريخ {{ now()->format('Y-m-d H:i') }}
    </p>

</body>

</html>
