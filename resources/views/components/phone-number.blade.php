@props(['value'])

@php
    $countries = config('countries');
    $detectedCode = 'YE'; // Default
    $clean = $value;

    foreach ($countries as $code => $country) {
        $rawCode = ltrim($country['dial_code'], '+');
        if (preg_match('/^(\+|00)?' . $rawCode . '/', $value)) {
            $detectedCode = $code;
            $clean = preg_replace('/^(\+|00)?' . $rawCode . '/', '', $value);
            break;
        }
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 justify-end']) }} dir="ltr">
    <x-country-flag :code="$detectedCode" class="w-5 h-auto rounded-sm" />

    <span class="font-mono">
        {{ $clean }}
    </span>
</div>