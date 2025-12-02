@props(['disabled' => false])

<style>
.orange-focus:focus {
    border-color: #f79009 !important;
    box-shadow: 0 0 0 2px #f79009 !important;
    outline: none !important;
}
</style>

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 rounded-md shadow-sm orange-focus']) }}>