@props(['name', 'defaultCode' => 'YE', 'model' => null])

<div x-data="{
    open: false,
    search: '',
    countries: @js(array_values(config('countries'))),
    selectedCountry: null,
    localPhoneNumber: '',
    init() {
        this.selectedCountry = this.countries.find(c => c.code === '{{ $defaultCode }}') || this.countries[0];
        
        // Initial setup for existing values if needed
        this.$watch('localPhoneNumber', value => {
            this.updateHiddenInput();
        });
        this.$watch('selectedCountry', value => {
            this.updateHiddenInput();
        });
    },
    get filteredCountries() {
        if (this.search === '') return this.countries;
        return this.countries.filter(c => 
            c.name.toLowerCase().includes(this.search.toLowerCase()) || 
            c.dial_code.includes(this.search)
        );
    },
    updateHiddenInput() {
        // Dispatch event or update hidden input logic if integrated deeper
    }
}" class="relative w-full">

    {{-- Main Container --}}
    <div
        class="flex h-[38px] w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 overflow-visible transition-all focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500">

        {{-- Country Button --}}
        <button type="button" @click="open = !open"
            class="flex items-center gap-2 px-3 bg-gray-100 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-600 rounded-r-xl shrink-0 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">

            <template x-if="selectedCountry">
                {{-- We use x-html to render the SVG from data, but since our data has raw SVG string, duplicate logic
                or use component?
                Alpine cannot easily render Blade component dynamically inside x-html.
                However, we can just use the SVG string from JSON. --}}
                <svg class="w-5 h-auto rounded-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                    x-html="selectedCountry.svg"></svg>
            </template>

            <span class="text-xs font-bold text-gray-500 dir-ltr" x-text="selectedCountry?.dial_code"></span>

            <svg class="h-3 w-3 text-gray-400 transition-transform duration-200" :class="{'rotate-180': open}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Phone Input --}}
        <input type="tel" {{ $attributes->whereStartsWith('x-model') }} placeholder="7xxxxxxxx" dir="ltr"
            autocomplete="off"
            class="flex-grow bg-transparent px-3 text-sm font-mono text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none rounded-l-xl text-left placeholder-gray-400">
    </div>

    {{-- Dropdown Menu --}}
    <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full mt-1 overflow-hidden bg-white border border-gray-100 rounded-xl shadow-xl dark:bg-gray-800 dark:border-gray-700 ring-1 ring-black/5"
        style="display: none;">

        {{-- Search - Only show if > 5 countries --}}
        <div class="p-2 border-b border-gray-100 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800 z-10"
            x-show="countries.length > 5">
            <input type="text" x-model="search" placeholder="بحث..."
                class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500/50">
        </div>

        <div class="overflow-y-auto max-h-48 custom-scrollbar">
            <template x-for="country in filteredCountries" :key="country.code">
                <div @click="selectedCountry = country; open = false; search = ''"
                    class="flex items-center gap-3 p-2.5 px-3 cursor-pointer transition-colors group"
                    :class="selectedCountry?.code === country.code ? 'bg-brand-50 dark:bg-brand-500/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">

                    <svg class="w-5 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>

                    <div class="flex flex-col flex-grow">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200" x-text="country.name"></span>
                    </div>

                    <span
                        class="text-[10px] font-mono font-bold text-gray-400 bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded dir-ltr"
                        x-text="country.dial_code"></span>

                    <svg x-show="selectedCountry?.code === country.code" class="w-4 h-4 text-brand-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </template>
        </div>
    </div>
</div>