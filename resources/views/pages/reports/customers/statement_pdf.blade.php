<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans; font-size: 12px; }
table { width:100%; border-collapse: collapse; }
th,td { border:1px solid #ddd; padding:6px; text-align:center; }
th { background:#4f46e5; color:#fff; }
.header { text-align:center; margin-bottom:20px; }
.summary { margin:15px 0; }
</style>
</head>
<body>

<div class="header">
    <h2>كشف حساب عميل</h2>
    <p>{{ $customer->name }} - {{ $customer->phone }}</p>
</div>

<div class="summary">
    <strong>مدين:</strong> {{ number_format($debit,2) }} |
    <strong>دائن:</strong> {{ number_format($credit,2) }} |
    <strong>الرصيد:</strong> {{ number_format($balance,2) }}
</div>

<table>
<thead>
<tr>
    <th>#</th>
    <th>التاريخ</th>
    <th>البيان</th>
    <th>مدين</th>
    <th>دائن</th>
</tr>
</thead>
<tbody>
@foreach($transactions as $t)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $t->created_at->format('Y-m-d') }}</td>
    <td>{{ $t->description }}</td>
    <td>{{ $t->type=='debit' ? $t->amount : '-' }}</td>
    <td>{{ $t->type=='credit' ? $t->amount : '-' }}</td>
</tr>
@endforeach
</tbody>
</table>

</body>
</html>
