<style>
.orange-focus:focus {
    border-color: #f79009 !important;
    box-shadow: 0 0 0 2px #f79009 !important;
    outline: none !important;
}
</style>
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'orange-focus inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
