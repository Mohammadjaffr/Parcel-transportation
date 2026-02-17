@props(['name', 'defaultCode' => 'YE', 'value' => null, 'model' => null, 'dynamicName' => null])

<div x-data="{
    open: false,
    search: '',
    countries: @js(array_values(config('countries'))),
    selectedCountry: null,
    localPhoneNumber: '',
    fullPhoneNumber: '',
    init() {
        this.selectedCountry = this.countries.find(c => c.code === '{{ $defaultCode }}') || this.countries[0];

        // Parse initial value from server-side (old input or DB value passed as prop)
        const initialValue = '{{ $value }}';
        if (initialValue) {
            this.parsePhoneNumber(initialValue);
        }

        this.$watch('localPhoneNumber', value => {
            this.updateFullPhoneNumber();
        });
        
        this.$watch('selectedCountry', value => {
            this.updateFullPhoneNumber();
        });
    },
    parsePhoneNumber(fullNumber) {
        if (!fullNumber) {
            this.localPhoneNumber = '';
            // Don't reset fullPhoneNumber here as it might be used by other parts
            return;
        }
        
        // Loop guard
        if (fullNumber === this.fullPhoneNumber) return;

        // Try to match dial code
        for (let country of this.countries) {
            const dialCode = country.dial_code.replace('+', '');
            if (fullNumber.startsWith(dialCode) || fullNumber.startsWith('+' + dialCode)) {
                this.selectedCountry = country;
                const cleanDial = dialCode;
                const cleanFull = fullNumber.replace('+', '');
                if (cleanFull.startsWith(cleanDial)) {
                    this.localPhoneNumber = cleanFull.substring(cleanDial.length);
                } else {
                    this.localPhoneNumber = fullNumber;
                }
                this.updateFullPhoneNumber(); 
                return;
            }
        }
        // Fallback
        this.localPhoneNumber = fullNumber;
        this.updateFullPhoneNumber();
    },
    get filteredCountries() {
        if (this.search === '') return this.countries;
        return this.countries.filter(c => 
            c.name.toLowerCase().includes(this.search.toLowerCase()) || 
            c.dial_code.includes(this.search)
        );
    },
    updateFullPhoneNumber() {
        if (!this.selectedCountry) return;
        const dialCode = this.selectedCountry.dial_code.replace('+', '');
        this.fullPhoneNumber = dialCode + this.localPhoneNumber;
        
        // Update external model if provided
        @if($model)
            if ('{{ $model }}'.includes('.')) {
                try {
                    let parts = '{{ $model }}'.split('.');
                    let obj = this; 
                    for (let i = 0; i < parts.length - 1; i++) {
                        if (obj[parts[i]] === undefined) break;
                        obj = obj[parts[i]];
                    }
                    if (obj[parts[parts.length - 1]] !== this.fullPhoneNumber) {
                        obj[parts[parts.length - 1]] = this.fullPhoneNumber;
                    }
                } catch (e) {
                    console.error('Error updating model {{ $model }}', e);
                }
            } else {
                 if (this.{{ $model }} !== this.fullPhoneNumber) {
                    this.{{ $model }} = this.fullPhoneNumber;
                 }
            }
        @endif
    }
}" class="relative" @if($model) x-effect="parsePhoneNumber({{ $model }})" @endif>

    {{-- Hidden Input for Form Submission --}}
    <input type="hidden" @if($dynamicName) :name="{{ $dynamicName }}" @else name="{{ $name }}" @endif
        x-model="fullPhoneNumber">

    {{-- Main Container - Matches Original Design --}}
    <div class="flex h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-theme-xs">

        {{-- Country Button --}}
        <button type="button" @click="open = !open"
            class="flex items-center gap-2 px-3 bg-gray-50 dark:bg-gray-700 rounded-r-lg border-l border-gray-300 dark:border-gray-600">

            <template x-if="selectedCountry">
                <svg class="w-5 h-auto rounded-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                    x-html="selectedCountry.svg"></svg>
            </template>
        </button>

        {{-- Phone Input --}}
        <input type="tel" x-model="localPhoneNumber" placeholder="780236551" dir="ltr" autocomplete="off"
            class="flex-grow bg-transparent px-3 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none rounded-l-lg text-left">
    </div>

    {{-- Dropdown Menu --}}
    <div x-show="open" @click.outside="open = false" x-transition
        class="absolute z-20 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700 max-h-60"
        style="display: none;">

        <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
            class="w-full px-4 py-2 border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500">

        <div class="overflow-y-auto max-h-48 custom-scrollbar">
            <template x-for="country in filteredCountries" :key="country.code">
                <div @click="selectedCountry = country; open = false; search = ''"
                    class="flex items-center gap-3 p-2 px-4 transition-colors duration-150 cursor-pointer hover:bg-sky-50 dark:hover:bg-gray-700">

                    <svg class="w-5 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>

                    <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                        x-text="country.name"></span>

                    <span class="text-xs tracking-wider text-gray-500 dark:text-gray-400"
                        x-text="country.dial_code"></span>
                </div>
            </template>
        </div>
    </div>
</div>