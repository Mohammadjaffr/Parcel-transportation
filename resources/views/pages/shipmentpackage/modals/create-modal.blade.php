<div x-show="openModal" class="flex fixed inset-0 justify-center items-center p-4 z-99999 modal" x-cloak x-transition>
    <div class="fixed inset-0 w-full h-full bg-gray-400/50 backdrop-blur-[32px]" @click="openModal = false"></div>
    <div @click.away="openModal = false"
        class="relative w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-2xl dark:bg-gray-900 dark:border-gray-800">

        <form action="{{ route('shipmentpackage.store') }}" method="POST" x-data="{ isLoading: false }"
            @submit="isLoading = true">
            @csrf

            {{-- Header ثابت --}}
            <div
                class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-white/[0.02]">
                <div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">انشاء شحنة جديدة</h3>
                    <p class="text-[10px] text-brand-500 font-bold uppercase tracking-widest mt-0.5">فرع المصدر:
                        {{ auth()->user()->branch_code }}
                    </p>
                </div>
                <button type="button" @click="openModal = false"
                    class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl shadow-sm transition-all dark:bg-gray-800 hover:text-error-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" />
                    </svg>
                </button>
            </div>

            {{-- معلومات السائق - ثابتة --}}
            <div class="p-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mr-1">اسم
                            السائق</label>
                        <input type="text" name="driver_name" required placeholder="الاسم الكامل"
                            class="px-3 w-full h-10 text-sm font-bold bg-gray-50 rounded-lg border-none dark:bg-gray-800 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mr-1">هاتف
                            السائق</label>
                        <div x-data="{
                                    open: false,
                                    countries: [{ name: 'Yemen', code: 'YE', dial_code: '+967' }],
                                    selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                                    localPhoneNumber: ''
                                }" class="relative w-full">
                            <input type="hidden" name="driver_phone"
                                :value="selectedCountry.dial_code.replace('+', '') + localPhoneNumber">

                            <div
                                class="flex overflow-hidden w-full h-10 bg-gray-50 rounded-lg border-none dark:bg-gray-800 focus-within:ring-2 focus-within:ring-brand-500/20">
                                <button type="button" @click="open = !open"
                                    class="flex gap-1 items-center px-2 bg-gray-100 border-l border-gray-200 dark:bg-gray-700/50 dark:border-gray-700">
                                    <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                        alt="Flag" class="w-4 h-auto rounded-sm">
                                    <svg class="w-3 h-3 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX"
                                    class="px-3 w-full h-10 text-sm font-bold text-right bg-transparent border-none dark:text-white focus:ring-0">
                            </div>

                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute z-50 mt-1 w-full bg-white rounded-lg border border-gray-100 shadow-xl dark:bg-gray-900 dark:border-gray-800">
                                <template x-for="country in countries" :key="country.code">
                                    <div @click="selectedCountry = country; open = false"
                                        class="flex justify-between items-center p-2 cursor-pointer hover:bg-brand-50 dark:hover:bg-brand-500/10">
                                        <div class="flex gap-2 items-center">
                                            <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                                class="w-4 rounded-sm">
                                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300"
                                                x-text="country.name"></span>
                                        </div>
                                        <span class="text-[10px] font-bold text-gray-400"
                                            x-text="country.dial_code"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- قائمة الطرود - مع سكرول وحجم ثابت --}}
            <div class="p-5">
                <div class="flex justify-between items-center mb-3">
                    <p class="text-xs font-bold uppercase text-brand-500">الطرود المتاحة للشحن:</p>
                    <span class="text-[10px] font-bold text-gray-400" x-text="`المحدد (${selectedCount})`"></span>
                </div>

                {{-- فلتر الفرع المستلم --}}
                <div class="mb-3">
                    <select x-model="selectedBranch"
                        class="flex justify-between items-center px-3 w-full h-11 text-sm text-right bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-900 dark:border-gray-700 hover:border-brand-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                        <option value="">كل الفروع</option>
                        @php
                            // استخراج الفروع الفريدة من الطرود المعلقة وترتيبها أبجدياً
                            $uniqueBranches = $pendingShipments
                                ->pluck('receiverBranch')
                                ->filter()
                                ->unique('code')
                                ->sortBy('name')
                                ->values();
                        @endphp
                        @foreach ($uniqueBranches as $branch)
                            <option value="{{ $branch->code }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- حاوية الطرود بارتفاع ثابت وسكرول --}}
                <div class="pr-1 space-y-2 custom-scrollbar"
                    style="height: 200px; max-height: 200px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #ccc #f1f1f1;">
                    @php
                        // ترتيب الطرود حسب الفرع المستلم
                        $sortedShipments = $pendingShipments->sortBy(function ($shipment) {
                            return $shipment->receiverBranch->name;
                        });
                    @endphp
                    @forelse($sortedShipments as $shipment)
                        <label x-show="selectedBranch === '' || selectedBranch == '{{ $shipment->receiver_branch_code }}'"
                            x-transition.duration.200ms data-branch="{{ $shipment->receiver_branch_code }}"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-white/[0.03] rounded-xl cursor-pointer hover:bg-brand-50/50 dark:hover:bg-brand-500/10 transition-all border-2 border-transparent has-[:checked]:border-brand-500 group">
                            <div class="flex gap-3 items-center">
                                <input type="checkbox" name="selected_ids[]" value="{{ $shipment->id }}"
                                    @change="selectedCount = $el.checked ? selectedCount + 1 : selectedCount - 1"
                                    class="w-5 h-5 rounded-md border-gray-300 text-brand-500 focus:ring-brand-500/20">
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-bold text-gray-900 dark:text-white">#{{ $shipment->bond_number }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $shipment->senderBranch->name }}
                                        ←
                                        {{ $shipment->receiverBranch->name }}</span>
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="text-xs font-bold text-brand-500">
                                    {{ number_format($shipment->total_amount) }} ر.ي
                                </div>
                                <div class="text-[9px] text-gray-400">{{ $shipment->package_type }}</div>
                            </div>
                        </label>
                    @empty
                        <div
                            class="py-8 text-center rounded-xl border-2 border-gray-100 border-dashed dark:border-gray-800">
                            <p class="text-xs italic text-gray-400">لا توجد طرود في حالة "قيد الانتظار"</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Footer ثابت --}}
            <div class="p-5 pt-3 bg-gray-50/50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800">
                <button type="submit" :disabled="selectedCount === 0 || isLoading" :class="selectedCount === 0 || isLoading ? 'opacity-50 cursor-not-allowed' :
                                'hover:bg-brand-600 shadow-brand-500/25'"
                    class="flex gap-2 justify-center items-center w-full h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-brand-500 active:scale-95">

                    {{-- Loading Spinner --}}
                    <svg x-show="isLoading" class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <span x-show="!isLoading" class="flex items-center gap-2">
                        إتمام جدولة الرحلة
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </span>

                    <span x-show="isLoading">جاري الحفظ...</span>
                </button>
            </div>
        </form>
    </div>
</div>