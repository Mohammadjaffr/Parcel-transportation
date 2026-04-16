<div x-show="openModal" class="flex fixed inset-0 justify-center items-center p-4 z-[99999] modal" x-cloak x-transition>
    <div class="fixed inset-0 w-full h-full backdrop-blur-sm transition-opacity bg-black/50" x-transition.opacity @click="openModal = false"></div>
    
    <div @click.away="openModal = false" x-transition.scale.95
        class="relative w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-2xl dark:bg-boxdark dark:border-boxdark font-body">

        <form action="{{ route('shipmentpackage.store') }}" method="POST" x-data="{ isLoading: false }"
            @submit="isLoading = true">
            @csrf

            {{-- Header --}}
            <div class="flex justify-between items-center px-5 py-4 rounded-t-2xl border-b border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2">
                <div>
                    <h3 class="text-lg font-black text-on-surface dark:text-white font-headline">انشاء شحنة جديدة</h3>
                    <p class="mt-0.5 text-[10px] font-bold tracking-widest uppercase text-primary">فرع المصدر:
                        {{ auth()->user()->branch_code }}
                    </p>
                </div>
                <button type="button" @click="openModal = false"
                    class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl shadow-sm transition-all dark:bg-boxdark dark:text-gray-500 hover:text-error dark:hover:text-error">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" />
                    </svg>
                </button>
            </div>

            {{-- معلومات السائق --}}
            <div class="p-5 pb-4 border-b border-gray-100 dark:border-boxdark-2">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="mr-1 text-[10px] font-bold tracking-widest text-gray-400 uppercase dark:text-gray-500">اسم السائق</label>
                        <input type="text" name="driver_name" required placeholder="الاسم الكامل"
                            class="px-3 w-full h-10 text-sm font-bold rounded-xl border-none transition-all outline-none bg-surface text-on-surface dark:bg-boxdark-2 dark:text-white focus:ring-2 focus:ring-primary/20">
                    </div>

                    <div class="space-y-1.5">
                        <label class="mr-1 text-[10px] font-bold tracking-widest text-gray-400 uppercase dark:text-gray-500">هاتف السائق</label>
                        <div x-data="{
                                open: false,
                                countries: [{ name: 'Yemen', code: 'YE', dial_code: '+967' }],
                                selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                                localPhoneNumber: ''
                            }" class="relative w-full">
                            
                            <input type="hidden" name="driver_phone"
                                :value="selectedCountry.dial_code.replace('+', '') + localPhoneNumber">

                            <div class="flex overflow-hidden w-full h-10 rounded-xl border-none transition-all bg-surface dark:bg-boxdark-2 focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary">
                                <button type="button" @click="open = !open"
                                    class="flex gap-1 items-center px-2 bg-gray-100 border-l border-gray-200 transition-colors dark:bg-boxdark dark:border-boxdark hover:bg-gray-200 dark:hover:bg-gray-800">
                                    <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                        alt="Flag" class="w-4 h-auto rounded-sm">
                                    <svg class="w-3 h-3 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX"
                                    class="px-3 w-full h-10 text-sm font-bold text-right bg-transparent border-none outline-none dark:text-white focus:ring-0 dir-ltr">
                            </div>

                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute z-50 mt-1 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark dark:border-boxdark-2">
                                <template x-for="country in countries" :key="country.code">
                                    <div @click="selectedCountry = country; open = false"
                                        class="flex justify-between items-center p-2 m-1 rounded-lg transition-colors cursor-pointer hover:bg-primary-container dark:hover:bg-boxdark-2">
                                        <div class="flex gap-2 items-center">
                                            <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                                class="w-4 rounded-sm">
                                            <span class="text-xs font-bold text-on-surface dark:text-gray-300"
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

            {{-- قائمة الطرود --}}
            <div class="p-5">
                <div class="flex justify-between items-center mb-3">
                    <p class="text-xs font-bold uppercase text-primary">الطرود المتاحة للشحن:</p>
                    <span class="text-[10px] font-bold text-gray-400 dark:text-bodydark" x-text="`المحدد (${selectedCount})`"></span>
                </div>

                {{-- فلتر الفرع المستلم --}}
                <div class="mb-3">
                    <select x-model="selectedBranch"
                        class="flex justify-between items-center px-3 w-full h-11 text-sm text-right rounded-xl border border-gray-200 transition-all appearance-none outline-none bg-surface text-on-surface dark:bg-boxdark-2 dark:border-boxdark dark:text-white hover:border-primary focus:border-primary focus:ring-2 focus:ring-primary/20">
                        <option value="">كل الفروع</option>
                        @php
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

                {{-- حاوية الطرود --}}
                <div class="pr-1 space-y-2 custom-scrollbar"
                    style="height: 200px; max-height: 200px; overflow-y: auto;">
                    @php
                        $sortedShipments = $pendingShipments->sortBy(function ($shipment) {
                            return $shipment->receiverBranch->name;
                        });
                    @endphp
                    @forelse($sortedShipments as $shipment)
                        <label x-show="selectedBranch === '' || selectedBranch == '{{ $shipment->receiver_branch_code }}'"
                            x-transition.duration.200ms data-branch="{{ $shipment->receiver_branch_code }}"
                            class="flex justify-between items-center p-3 border-2 border-transparent transition-all cursor-pointer rounded-xl bg-surface hover:bg-primary-container/50 has-[:checked]:border-primary has-[:checked]:bg-primary-container/30 dark:bg-boxdark-2 dark:hover:bg-boxdark dark:has-[:checked]:bg-primary/5 group">
                            
                            <div class="flex gap-3 items-center">
                                <input type="checkbox" name="selected_ids[]" value="{{ $shipment->id }}"
                                    @change="selectedCount = $el.checked ? selectedCount + 1 : selectedCount - 1"
                                    class="w-5 h-5 rounded-md border-gray-300 transition-colors text-primary focus:ring-primary/20 dark:border-boxdark dark:bg-boxdark">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-on-surface dark:text-white">#{{ $shipment->bond_number }}</span>
                                    <span class="text-[10px] text-gray-500 dark:text-bodydark">
                                        {{ $shipment->senderBranch->name }} ← {{ $shipment->receiverBranch->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="text-xs font-bold text-primary">
                                    {{ number_format($shipment->total_amount) }} ر.ي
                                </div>
                                <div class="text-[9px] text-gray-400 dark:text-gray-500">{{ $shipment->package_type }}</div>
                            </div>
                        </label>
                    @empty
                        <div class="py-8 text-center rounded-xl border-2 border-gray-100 border-dashed dark:border-boxdark-2">
                            <p class="text-xs italic text-gray-400 dark:text-bodydark">لا توجد طرود في حالة "قيد الانتظار"</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-5 pt-3 rounded-b-2xl border-t border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark-2">
                <button type="submit" :disabled="selectedCount === 0 || isLoading" 
                    :class="selectedCount === 0 || isLoading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary-hover shadow-primary/25'"
                    class="flex gap-2 justify-center items-center w-full h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-primary active:scale-95">

                    {{-- Loading Spinner --}}
                    <svg x-show="isLoading" class="w-5 h-5 text-white animate-spin" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                    <span x-show="!isLoading" class="flex gap-2 items-center">
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