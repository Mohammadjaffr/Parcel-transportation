<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center
        }

        th {
            background: #1f2937;
            color: #fff
        }
    </style>
</head>

<body>

    <h3 style="text-align:center">كشف حساب فرع: {{ $branch->name }}</h3>
    <p><strong>الرصيد النهائي:</strong> {{ number_format($net, 2) }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>التاريخ</th>
                <th>من</th>
                <th>إلى</th>
                <th>النوع</th>
                <th>المبلغ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $t)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $t->created_at->format('Y-m-d') }}</td>
                    <td>{{ optional($t->fromBranch)->name }}</td>
                    <td>{{ optional($t->toBranch)->name }}</td>
                    <td>{{ $t->type }}</td>
                    <td>{{ number_format($t->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
