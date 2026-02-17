{{-- Modal: إضافة طرد جديد --}}
<div x-show="showAddItemModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4">

    {{-- Overlay --}}
    <div class="fixed inset-0" style="background: rgba(0,0,0,0.5);" @click="showAddItemModal = false"></div>

    {{-- Modal Content --}}
    <div x-show="showAddItemModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden z-10"
        style="width: 520px; max-width: 95vw;">

        {{-- Header --}}
        <div
            class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex items-center gap-2">
                <div
                    class="flex items-center justify-center w-7 h-7 rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white">إضافة طرد جديد</h3>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">بيان #{{ $receipt->number }}</p>
                </div>
            </div>
            <button @click="showAddItemModal = false" type="button"
                class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('receipts.add-item', $receipt->id) }}">
            @csrf
            <div class="p-4" dir="rtl">

                {{-- رقم السند + نوع الطرد --}}
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <div style="flex: 1;">
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                            رقم السند <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="number" required
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
                            placeholder="رقم السند">
                    </div>
                    <div style="flex: 1;">
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                            نوع الطرد <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="package_type" required
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
                            placeholder="صندوق، مغلف، ...">
                    </div>
                </div>

                {{-- المرسل + المستلم --}}
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <div style="flex: 1;">
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                            اسم المرسل
                        </label>
                        <input type="text" name="sender_name"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
                            placeholder="اسم المرسل">
                    </div>
                    <div style="flex: 1;">
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                            اسم المستلم <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="receiver_name" required
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
                            placeholder="اسم المستلم">
                    </div>
                </div>

                {{-- رقم الهاتف + ملاحظات --}}
                <div style="display: flex; gap: 10px;">
                    <div style="flex: 1;" x-data="{
                        rcSelected: { dial_code: '+967' }, // Default for initial calculate
                        rcLocal: '',
                        get fullPhone() {
                            // This computed property is used by the hidden input
                            // The component updates rcSelected and rcLocal
                            return (this.rcSelected?.dial_code || '+967').replace('+', '') + (this.rcLocal || '');
                        }
                    }">
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                            رقم هاتف المستلم <span class="text-red-500">*</span>
                        </label>

                        {{-- Hidden input: full phone --}}
                        <input type="hidden" name="receiver_phone" :value="fullPhone">

                        {{-- Reusable Component --}}
                        {{-- We bind Alpine variables to the component's internal state --}}
                        {{-- Since component has its own x-data scope, we need to sync.
                        Actually, better approach:
                        The component should accept x-model for the local number,
                        and emit event or use a modelable for the selected country?

                        Let's adjust usage to match the component's x-data.
                        The component defines `localPhoneNumber` and `selectedCountry`.
                        If we nest x-data, scopes don't share easily unless we use $dispatch or window global.

                        Better: Let the component handle the UI, and we just need the RESULT (full phone).
                        If we put the hidden input INSIDE the component? No, name attribute needs to be flexible.

                        Let's Refactor Component Strategy:
                        The component currently HAS x-data.
                        If we use it here inside another x-data, we have nested scopes.

                        Simple fix: The component should PROBABLY not have `x-data` on the root if we want to control it
                        from outside,
                        OR we pass the parent's data objects to it.

                        However, to keep it simple as requested:
                        I will inline the logic using the SHARED CONFIG, but using the clean markup structure I
                        designed?

                        Wait, the user wants 'this dropdown' to contain countries from the file.
                        The `x-country-select` I created has `x-data` which isolates it.

                        Let's look at `x-country-select` again.
                        It has `search`, `countries` from config.

                        I will use `x-country-select` but I need to ensure `receiver_phone` is populated.
                        The component I wrote doesn't emit the full phone.

                        I should Update `x-country-select` to accept a `name` prop for the hidden input?
                        Or better, just put the logic here using `config('countries')` directly,
                        since the user asked for 'this dropdown' to use the file.

                        Using the component is cleaner IF it handles the form submission value.

                        Let's try to use the component and pass `name="receiver_phone"`.
                        But the component I wrote has `input type="tel"` but no hidden input for full phone.

                        Correction: I will Modify `x-country-select` to support `name` for full phone submission.

                        For now, let's Replace this block with a version that uses `config('countries')` directly
                        but keeps the local Alpine logic, just fetching data from config.
                        This is safest to avoid component scope issues.
                        --}}

                        <div x-data="{
                            rcOpen: false,
                            rcSearch: '',
                            rcCountries: @js(array_values(config('countries'))),
                            rcSelected: null,
                            rcLocal: '',
                            init() {
                                this.rcSelected = this.rcCountries.find(c => c.code === 'YE') || this.rcCountries[0];
                            },
                            get rcFiltered() {
                                if (this.rcSearch === '') return this.rcCountries;
                                return this.rcCountries.filter(c => c.name.toLowerCase().includes(this.rcSearch.toLowerCase()) || c.dial_code.includes(this.rcSearch));
                            },
                            get fullPhone() {
                                return (this.rcSelected?.dial_code.replace('+', '') || '') + this.rcLocal;
                            }
                        }" class="relative">

                            {{-- Hidden input: full phone --}}
                            <input type="hidden" name="receiver_phone" :value="fullPhone">

                            <div
                                class="flex h-[30px] w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 overflow-visible">
                                {{-- Country code button --}}
                                <button type="button" @click="rcOpen = !rcOpen"
                                    class="flex items-center gap-1 px-2 bg-gray-100 dark:bg-gray-800 border-l border-gray-200 dark:border-gray-600 rounded-r-lg shrink-0">

                                    <template x-if="rcSelected">
                                        <svg class="w-4 h-auto rounded-sm" viewBox="0 0 36 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" x-html="rcSelected.svg"></svg>
                                    </template>

                                    <svg class="h-2.5 w-2.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {{-- Phone number input --}}
                                <input type="tel" x-model="rcLocal" placeholder="7xxxxxxxx" dir="ltr" autocomplete="off"
                                    required
                                    class="flex-grow bg-transparent px-2 text-xs font-mono text-gray-800 dark:text-white focus:outline-none focus:ring-0 border-none rounded-l-lg text-left">
                            </div>

                            {{-- Country dropdown --}}
                            <div x-show="rcOpen" @click.outside="rcOpen = false" x-transition
                                class="absolute z-40 w-full mt-1 overflow-hidden bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-gray-800 dark:border-gray-700 max-h-48"
                                style="display: none;">
                                <input type="text" x-model="rcSearch" placeholder="ابحث..."
                                    class="w-full px-3 py-1.5 border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500 text-xs text-right">
                                <div class="overflow-y-auto max-h-32 custom-scrollbar">
                                    <template x-for="country in rcFiltered" :key="country.code">
                                        <div @click="rcSelected = country; rcOpen = false; rcSearch = ''"
                                            class="flex items-center gap-2 p-2 px-3 cursor-pointer hover:bg-brand-50 dark:hover:bg-gray-700 transition-colors">

                                            <svg class="w-4 h-auto rounded-sm" viewBox="0 0 36 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>

                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300"
                                                x-text="country.name"></span>
                                            <span class="text-[10px] text-gray-400 mr-auto font-mono dir-ltr"
                                                x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1">
                            ملاحظات
                        </label>
                        <input type="text" name="item_notes"
                            class="w-full px-3 py-1.5 text-xs bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all"
                            placeholder="ملاحظات (اختياري)">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div
                class="flex items-center justify-end gap-2 px-4 py-2.5 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <button type="button" @click="showAddItemModal = false"
                    class="px-3 py-1.5 text-[11px] font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                    إلغاء
                </button>
                <button type="submit"
                    class="px-3 py-1.5 text-[11px] font-bold text-white bg-brand-500 hover:bg-brand-600 rounded-lg shadow-lg shadow-brand-500/20 transition-all active:scale-95">
                    <svg class="w-3 h-3 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    إضافة الطرد
                </button>
            </div>
        </form>
    </div>
</div>