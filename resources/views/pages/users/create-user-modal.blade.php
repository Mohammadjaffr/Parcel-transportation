<div x-data="{isModalOpen: @if(session('isModalOpen')) true @else false @endif, isLoading: false }">

    <button @click="isModalOpen = true"
        class="flex justify-center hover:bg-brand-600 px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        إضافة مستخدم جديد
    </button>

    <div x-show="isModalOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
        style="display: none;">
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
        </div>

        <div @click.outside="isModalOpen = false"
            class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

            <form method="POST" action="{{ route('users.store') }}" @submit="isLoading = true">
                @csrf
                <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90">
                    إضافة مستخدم جديد
                </h4>

                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                    <div class="sm:col-span-2">
                        <label for="code" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            أسم المستخدم <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>
                        <input type="text" id="name" name="name" required placeholder="مثال: أحمد شرجبي"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Main container for the phone input component -->
                    <div class="col-span-1">
                        <label for="phone_number_display"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            رقم الجوال <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>

                        <div x-data="{
                            open: false,
                            search: '',
                            countries: [
                                { name: 'Yemen', code: 'YE', dial_code: '+967' }
                            ],
                            selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                            localPhoneNumber: '', // <-- 1. المتغير الجديد لتخزين رقم الجوال المحلي

                            get filteredCountries() {
                                if (this.search === '') return this.countries;
                                return this.countries.filter(country => {
                                    const searchLower = this.search.toLowerCase();
                                    return country.name.toLowerCase().includes(searchLower) || country.dial_code.includes(searchLower);
                                });
                            }
                        }" class="relative">

                            <!-- 2. الحقل المخفي الذي سيتم إرساله إلى الخادم -->
                            <input type="hidden" name="phone"
                                :value="selectedCountry.dial_code.replace('+', '') + localPhoneNumber">

                            <!-- This is the main visible input group -->
                            <div
                                class="flex h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-theme-xs">

                                <!-- The dropdown button -->
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-2 px-3 bg-gray-50 dark:bg-gray-700 rounded-r-lg border-l border-gray-300 dark:border-gray-600">
                                    <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                        alt="Flag" class="w-5 h-auto">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- 3. حقل الإدخال المرئي (تم ربطه بـ x-model وإزالة name) -->
                                <input id="phone_number_display" type="tel" x-model="localPhoneNumber"
                                    placeholder="780236551" required
                                    class="flex-grow bg-transparent px-3 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none rounded-l-lg text-left"
                                    dir="ltr">
                            </div>

                            <!-- The Dropdown Panel (no changes here) -->
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute z-20 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700 max-h-60">

                                <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                    class="w-full px-4 py-2 border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500">

                                <div class="overflow-y-auto max-h-48">
                                    <template x-for="country in filteredCountries" :key="country.code">
                                        <div @click="selectedCountry = country; open = false"
                                            class="flex items-center gap-3 p-2 px-4 transition-colors duration-150 cursor-pointer hover:bg-sky-50 dark:hover:bg-gray-700">
                                            <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                                alt="" class="w-5">
                                            <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                                                x-text="country.name"></span>
                                            <span class="text-xs tracking-wider text-gray-500 dark:text-gray-400"
                                                x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <p class="mt-1 text-xs text-warning-500 dark:text-warning/90">
                               و سيتم استخدام هذا الرقم لتسجيل الدخول والتواصل واتساب.
                            </p>
                        </div>
                        
                    </div>



                    <div class="col-span-1">
                        <label for="phone_number_display"
                            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            رقم اضافي <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">(اختياري)</span>
                        </label>

                        <div x-data="{
                            open: false,
                            search: '',
                            countries: [
                                { name: 'Yemen', code: 'YE', dial_code: '+967' }
                            ],
                            selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                            localPhoneNumber: '', // <-- 1. المتغير الجديد لتخزين رقم الجوال المحلي

                            get filteredCountries() {
                                if (this.search === '') return this.countries;
                                return this.countries.filter(country => {
                                    const searchLower = this.search.toLowerCase();
                                    return country.name.toLowerCase().includes(searchLower) || country.dial_code.includes(searchLower);
                                });
                            }
                        }" class="relative">

                            <!-- 2. الحقل المخفي الذي سيتم إرساله إلى الخادم -->
                            <input type="hidden" name="whatsapp_number"
                                :value="selectedCountry.dial_code.replace('+', '') + localPhoneNumber">

                            <!-- This is the main visible input group -->
                            <div
                                class="flex h-11 w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-theme-xs">

                                <!-- The dropdown button -->
                                <button type="button" @click="open = !open"
                                    class="flex items-center gap-2 px-3 bg-gray-50 dark:bg-gray-700 rounded-r-lg border-l border-gray-300 dark:border-gray-600">
                                    <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                        alt="Flag" class="w-5 h-auto">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- 3. حقل الإدخال المرئي (تم ربطه بـ x-model وإزالة name) -->
                                <input id="whatsapp_number_display" type="tel" x-model="localPhoneNumber"
                                    placeholder="780236551"
                                    class="flex-grow bg-transparent px-3 text-sm text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none rounded-l-lg text-left"
                                    dir="ltr">
                            </div>

                            <!-- The Dropdown Panel (no changes here) -->
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute z-20 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700 max-h-60">

                                <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                    class="w-full px-4 py-2 border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500">

                                <div class="overflow-y-auto max-h-48">
                                    <template x-for="country in filteredCountries" :key="country.code">
                                        <div @click="selectedCountry = country; open = false"
                                            class="flex items-center gap-3 p-2 px-4 transition-colors duration-150 cursor-pointer hover:bg-sky-50 dark:hover:bg-gray-700">
                                            <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                                alt="" class="w-5">
                                            <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                                                x-text="country.name"></span>
                                            <span class="text-xs tracking-wider text-gray-500 dark:text-gray-400"
                                                x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            كلمة السر <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                        </label>
                        <input type="text" id="password" name="password"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-warning-500 dark:text-warning/90">
                            المستخدم يستطيع تسجيل الدخول من خلال التطبيق باستخدام كلمة السر.
                        </p>
                    </div>



                </div>

                <div class="flex items-center justify-end w-full gap-3 mt-6">
                    <button @click="isModalOpen = false" type="button"
                        class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
                        إغلاق
                    </button>
                    <button type="submit" :disabled="isLoading"
                        class="flex items-center justify-center gap-2 hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed transition-all">
                        <!-- Loading Spinner -->
                        <svg x-show="isLoading" class="animate-spin h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isLoading ? 'جاري الإنشاء...' : 'إنشاء المستخدم'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>