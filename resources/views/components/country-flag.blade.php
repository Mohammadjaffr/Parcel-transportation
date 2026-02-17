@props(['code', 'class' => 'w-5 h-auto rounded-sm'])

@php
    $countries = config('countries');
    $svg = $countries[$code]['svg'] ?? null;
@endphp

@if($svg)
    <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        {!! $svg !!}
    </svg>
@endif