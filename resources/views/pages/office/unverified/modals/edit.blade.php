{{-- ======================== Edit Office Modal ======================== --}}
<div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999" style="display: none;"
    @keydown.escape.window="showEditModal = false">

    {{-- Backdrop --}}
    <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="showEditModal = false"></div>

    {{-- Modal Panel --}}
    <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

        <form :action="editoffice.url" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf
            @method('PUT')

            {{-- Modal Header --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex gap-3 items-center">
                    <div
                        class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">تعديل بيانات المكتب</h3>
                </div>
                <button type="button" @click="showEditModal = false"
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
                    <label for="edit_office_name"
                        class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        اسم المكتب <span class="text-error-500">*</span>
                    </label>
                    <input type="text" id="edit_office_name" name="name" x-model="editoffice.name" required
                        autocomplete="off" placeholder="أدخل اسم المكتب"
                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:border-2 focus:brand-500 dark:border-gray-600 dark:text-white">
                </div>

                {{-- رقم الهاتف --}}
                <div>
                    <label for="edit_office_phone"
                        class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        رقم الهاتف
                    </label>

                    <div x-data="{
                        open: false,
                        search: '',
                        countries: @js(array_values(config('countries'))),
                        selectedCountry: null,
                        localPhoneNumber: '',
                        init() {
                            this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                            // Watch for edits
                            this.$watch('editoffice', (val) => {
                                if (val && val.phone) this.parsePhone(val.phone);
                            });
                        },
                        parsePhone(phone) {
                            let p = phone || '';
                            let found = null;
                    
                            // Sort countries by dial_code length desc to match longest prefix first
                            const sorted = this.countries.slice().sort((a, b) => b.dial_code.length - a.dial_code.length);
                    
                            for (const c of sorted) {
                                // Match: +966, 00966, 966
                                const code = c.dial_code.replace('+', '');
                                const regex = new RegExp(`^(\\+|00)?${code}`);
                                if (regex.test(p)) {
                                    found = c;
                                    // Remove prefix
                                    p = p.replace(regex, '');
                                    break;
                                }
                            }
                    
                            if (found) {
                                this.selectedCountry = found;
                                this.localPhoneNumber = p;
                            } else {
                                // Default to init country (YE) and keep full phone if no match
                                this.localPhoneNumber = p;
                            }
                        },
                        get filteredCountries() {
                            if (this.search === '') return this.countries;
                            return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                        }
                    }" x-init="if (typeof editoffice !== 'undefined' && editoffice.phone) parsePhone(editoffice.phone)" class="relative">

                        {{-- Hidden input sent to server --}}
                        <input type="hidden" name="phone"
                            :value="(selectedCountry?.dial_code.replace('+', '') || '') + localPhoneNumber">

                        <div
                            class="flex overflow-hidden w-full h-11 bg-gray-50 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-900">
                            {{-- Country code button --}}
                            <button type="button" @click="open = !open"
                                class="flex gap-2 items-center px-3 bg-gray-100 rounded-r-xl border-l border-gray-200 dark:bg-gray-800 dark:border-gray-600 shrink-0">

                                <template x-if="selectedCountry">
                                    <svg class="w-5 h-auto rounded-sm" viewBox="0 0 36 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                </template>

                                <span class="text-xs font-bold text-gray-500 dir-ltr"
                                    x-text="selectedCountry?.dial_code"></span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Phone number input --}}
                            <input id="edit_office_phone" type="tel" x-model="localPhoneNumber"
                                placeholder="780236551" autocomplete="off"
                                class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border-gray-300 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                                dir="ltr">
                        </div>

                        {{-- Dropdown panel --}}
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="overflow-hidden absolute z-20 mt-1 w-full max-h-60 bg-white rounded-xl border border-gray-200 shadow-lg dark:bg-gray-800 dark:border-gray-700"
                            style="display: none;">
                            <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                class="px-4 py-2 w-full h-8 text-sm border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500">
                            <div class="overflow-y-auto max-h-20 custom-scrollbar">
                                <template x-for="country in filteredCountries" :key="country.code">
                                    <div @click="selectedCountry = country; open = false; search = ''"
                                        class="flex gap-3 items-center p-2 px-4 transition-colors cursor-pointer hover:bg-brand-50 dark:hover:bg-gray-700">

                                        <svg class="w-5 h-auto rounded-sm" viewBox="0 0 36 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>

                                        <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="country.name"></span>
                                        <span class="font-mono text-xs text-gray-500 dark:text-gray-400 dir-ltr"
                                            x-text="country.dial_code"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="edit_office_address"
                        class="block mb-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        العنوان
                    </label>
                    <input type="text" id="edit_office_address" name="address" x-model="editoffice.address"
                        autocomplete="off" placeholder="أدخل العنوان"
                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:border-2 focus:brand-500 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            {{-- Modal Footer --}}
              <div
                class="flex gap-3 justify-between px-6 py-4 bg-gray-50 border-t border-gray-100 items-be dark:border-gray-700 dark:bg-gray-900/50">
                <button type="button" @click="showEditModal = false"
                    class="px-5 py-2.5 w-full text-sm font-semibold text-gray-700 bg-white rounded-xl border border-gray-200 transition-all dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                    إلغاء
                </button>
                <button type="submit" :disabled="isSubmitting"
                    class="flex gap-2 justify-center items-center px-5 py-2.5 w-full text-sm font-bold text-center text-white rounded-xl shadow-sm transition-all bg-brand-500 hover:bg-brand-600 hover:shadow-md active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                    <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ المكتب'"></span>
                </button>
            </div>

        </form>
    </div>
</div>
