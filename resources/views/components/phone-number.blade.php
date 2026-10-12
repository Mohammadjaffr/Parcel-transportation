@props(['value' => null])

@php
    $countries = config('countries', []);
    $detectedCode = 'YE'; // Default
    $clean = $value ?: '---';

    if (!empty($value) && $value !== '---') {
        foreach ($countries as $code => $country) {
            $rawCode = ltrim($country['dial_code'] ?? '', '+');
            if ($rawCode && preg_match('/^(\+|00)?' . $rawCode . '/', (string) $value)) {
                $detectedCode = $code;
                $clean = preg_replace('/^(\+|00)?' . $rawCode . '/', '', (string) $value);
                break;
            }
        }
    }
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 justify-end']) }} dir="ltr">
    @if(!empty($value) && $value !== '---')
        <x-country-flag :code="$detectedCode" class="w-5 h-auto rounded-sm" />
    @endif

    <span class="font-mono">
        {{ $clean }}
    </span>
</div>