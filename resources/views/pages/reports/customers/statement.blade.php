@extends('layouts.app')
@section('title','كشف حساب عميل')

@section('content')
<div class="space-y-6">

<div class="grid grid-cols-3 gap-4">
    <x-stat title="مدين" :value="$debit" color="error"/>
    <x-stat title="دائن" :value="$credit" color="success"/>
    <x-stat title="الرصيد" :value="$balance" :color="$balance>0?'error':'success'"/>
</div>

<table class="w-full text-sm">
<thead>
<tr class="bg-gray-100">
    <th>التاريخ</th>
    <th>البيان</th>
    <th>مدين</th>
    <th>دائن</th>
</tr>
</thead>
<tbody>
@foreach($transactions as $t)
<tr>
    <td>{{ $t->created_at->format('Y-m-d') }}</td>
    <td>{{ $t->description }}</td>
    <td>{{ $t->type=='debit' ? $t->amount : '-' }}</td>
    <td>{{ $t->type=='credit' ? $t->amount : '-' }}</td>
</tr>
@endforeach
</tbody>
</table>

</div>
@endsection
