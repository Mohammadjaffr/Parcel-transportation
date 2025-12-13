@extends('layouts.app')
@section('title','Dashboard التقارير')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

<div class="bg-white p-5 rounded shadow">
    <h4>الشحنات</h4>
    <p class="text-2xl font-bold">{{ $shipmentsCount }}</p>
</div>

<div class="bg-white p-5 rounded shadow">
    <h4>الإيرادات</h4>
    <p class="text-2xl font-bold">{{ number_format($revenue,2) }}</p>
</div>

<div class="bg-white p-5 rounded shadow">
    <h4>مديونية العملاء</h4>
    <p class="text-2xl font-bold">{{ number_format($customersDebt,2) }}</p>
</div>

<div class="bg-white p-5 rounded shadow">
    <h4>مديونية الفروع</h4>
    <p class="text-2xl font-bold">{{ number_format($branchesNet,2) }}</p>
</div>

</div>
@endsection
