{{-- ======================== Create Driver Modal ======================== --}}
<div x-show="showCreateModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-99999 flex items-center justify-center p-4" style="display: none;"
    @keydown.escape.window="showCreateModal = false">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showCreateModal = false"></div>

    {{-- Modal Panel --}}
    <div x-show="showCreateModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-2xl overflow-hidden">

        <form action="{{ route('drivers.store') }}" method="POST" x-data="{ isSubmitting: false }"
            @submit="isSubmitting = true">
            @csrf

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">إضافة سائق جديد</h3>
                </div>
                <button type="button" @click="showCreateModal = false"
                    class="p-2 text-gray-400 rounded-xl transition-all hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-5 space-y-5">
                {{-- اسم السائق --}}
                <div>
                    <label for="driver_name"
                        class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        اسم السائق <span class="text-error-500">*</span>
                    </label>
                    <input type="text" id="driver_name" name="name" value="{{ old('name') }}" required
                        autocomplete="off" placeholder="أدخل اسم السائق"
                        class="w-full px-4 py-2.5 text-sm text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none">
                    @error('name')
                        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- رقم الهاتف --}}
                <div>
                    <label for="driver_phone"
                        class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        رقم الهاتف
                    </label>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: [
                            { name: 'Yemen', code: 'YE', dial_code: '+967' }
                        ],
                        selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                        localPhoneNumber: '',
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }" class="relative">

                        {{-- Hidden input sent to server --}}
                        <input type="hidden" name="phone"
                            :value="selectedCountry.dial_code.replace('+', '') + localPhoneNumber">

                        <div
                            class="flex h-11 w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 overflow-hidden">
                            {{-- Country code button --}}
                            <button type="button" @click="open = !open"
                                class="flex items-center gap-2 px-3 bg-gray-100 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-600 rounded-r-xl">
                                <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                    alt="Flag" class="w-5 h-auto">
                                <span class="text-xs font-bold text-gray-500" x-text="selectedCountry.dial_code"></span>
                                <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Phone number input --}}
                            <input id="driver_phone" type="tel" x-model="localPhoneNumber" placeholder="780236551"
                                autocomplete="off"
                                class="flex-grow bg-transparent px-3 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none rounded-l-xl text-left"
                                dir="ltr">
                        </div>

                        {{-- Dropdown panel --}}
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute z-20 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-gray-800 dark:border-gray-700 max-h-60">
                            <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                class="w-full px-4 py-2 border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500 text-sm">
                            <div class="overflow-y-auto max-h-48">
                                <template x-for="country in filteredCountries" :key="country.code">
                                    <div @click="selectedCountry = country; open = false"
                                        class="flex items-center gap-3 p-2 px-4 cursor-pointer hover:bg-brand-50 dark:hover:bg-gray-700 transition-colors">
                                        <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`" alt=""
                                            class="w-5">
                                        <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="country.name"></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400"
                                            x-text="country.dial_code"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    @error('phone')
                        <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Modal Footer --}}
            <div
                class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <button type="button" @click="showCreateModal = false"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    إلغاء
                </button>
                <button type="submit" :disabled="isSubmitting"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-brand-500 rounded-xl hover:bg-brand-600 shadow-sm hover:shadow-md transition-all active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                    <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                    <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ السائق'"></span>
                </button>
            </div>

        </form>
    </div>
</div>