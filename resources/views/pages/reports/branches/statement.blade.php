@extends('layouts.app')
@section('title','كشف حساب فرع')

@section('content')
<div class="space-y-6">

<h2 class="text-xl font-bold">{{ $branch->name }}</h2>

<div class="bg-gray-100 p-4 rounded">
    <strong>الرصيد النهائي:</strong>
    {{ number_format($net,2) }}
</div>

<table class="w-full text-sm">
<thead class="bg-gray-200">
<tr>
    <th>التاريخ</th>
    <th>من</th>
    <th>إلى</th>
    <th>النوع</th>
    <th>المبلغ</th>
</tr>
</thead>
<tbody>
@foreach($transactions as $t)
<tr>
    <td>{{ $t->created_at->format('Y-m-d') }}</td>
    <td>{{ optional($t->fromBranch)->name }}</td>
    <td>{{ optional($t->toBranch)->name }}</td>
    <td>{{ $t->type }}</td>
    <td>{{ number_format($t->amount,2) }}</td>
</tr>
@endforeach
</tbody>
</table>

<a target="_blank"
   href="{{ route('reports.branches.statement.pdf',$branch->id) }}"
   class="btn btn-danger mt-4">
   تصدير PDF
</a>

</div>
@endsection
