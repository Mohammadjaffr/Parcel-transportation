@extends('layouts.app')
@section('title', 'إدارة الرحلات والشحنات')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
        search: '',
        openModal: false,
        selectedCount: 0,
        showRow(tracking, driver) {
            return tracking.toLowerCase().includes(this.search.toLowerCase()) ||
                driver.toLowerCase().includes(this.search.toLowerCase());
        }
    }">

        <div
            class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-center bg-white dark:bg-white/[0.03] p-6 rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="xl:col-span-4 flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-brand-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white leading-tight">شحنات الرحلات</h2>
                    <p class="text-theme-xs text-gray-500 font-bold uppercase tracking-widest italic">تجميع وإدارة الشحن
                        الجماعي</p>
                </div>
            </div>

            <div class="xl:col-span-8 flex flex-col md:flex-row justify-end gap-4">
                <div
                    class="px-6 py-3 bg-brand-50 dark:bg-brand-500/10 rounded-2xl border border-brand-100 dark:border-brand-500/20 flex items-center gap-4">
                    <span
                        class="text-brand-600 dark:text-brand-400 font-black text-2xl">{{ $pendingShipments->count() }}</span>
                    <span class="text-xs font-bold text-gray-500 uppercase leading-tight">طرد بانتظار <br>الشحن</span>
                </div>

                <button @click="openModal = true"
                    class="h-14 px-8 bg-brand-500 hover:bg-brand-600 text-white font-black rounded-2xl shadow-lg shadow-brand-500/20 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" />
                    </svg>
                    تجهيز رحلة جديدة
                </button>
            </div>
        </div>

        <div
            class="w-full bg-white dark:bg-white/[0.03] p-6 rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="relative group w-full">
                <input type="text" x-model="search" placeholder="ابحث برقم التتبع، اسم السائق..."
                    class="w-full h-12 pr-11 pl-4 rounded-xl border-none bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all text-sm font-medium dark:text-white placeholder-gray-400">
                <div
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 group-focus-within:text-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full border-separate border-spacing-y-3 text-right">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="py-4 px-6">رقم التتبع</th>
                            <th class="py-4 px-6">السائق المسؤول</th>
                            <th class="py-4 px-6 text-center">حمولة الطرود</th>
                            <th class="py-4 px-6 text-center">التاريخ</th>
                            <th class="py-4 px-6 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse($packages as $pkg)
                            <tr x-show="showRow('{{ $pkg->tracking_number }}', '{{ $pkg->driver_name }}')" x-transition
                                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-md transition-all border border-transparent hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="py-5 px-6 first:rounded-r-2xl border-y border-r dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1.5 bg-gray-50 dark:bg-gray-800 rounded-lg text-xs font-black text-brand-500 border border-gray-100 dark:border-gray-700 shadow-inner">
                                        #{{ $pkg->tracking_number }}
                                    </span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-900 dark:text-white">{{ $pkg->driver_name }}</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 font-mono">{{ $pkg->driver_phone }}</span>
                                    </div>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <span
                                        class="px-3 py-1 bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 rounded-lg text-xs font-black border border-brand-100/50">
                                        {{ $pkg->shipments_count }} طرود مشحونه
                                    </span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $pkg->created_at->format('Y-m-d') }}</span>
                                </td>

                                <td
                                    class="py-5 px-6 last:rounded-l-2xl border-y border-l dark:border-gray-800/50 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('shipmentpackage.show', $pkg->id) }}"
                                            class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 rounded-xl transition-all"
                                            title="عرض التفاصيل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400 italic">
                                        <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <span>لا توجد رحلات مسجلة حالياً..</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($packages->hasPages())
                <div class="p-8 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>

        <div x-show="openModal" class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 z-99999 modal"
            x-cloak x-transition>
            <div class="fixed inset-0 w-full h-full bg-gray-400/50 backdrop-blur-[32px]" @click="openModal = false"></div>
            <div @click.away="openModal = false"
                class="relative w-full max-w-[600px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

                <form action="{{ route('shipmentpackage.store') }}" method="POST">
                    @csrf
                    <div
                        class="p-8 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-white/[0.02]">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">تجميع شحنة جماعية
                            </h3>
                            <p class="text-xs text-brand-500 font-black uppercase tracking-widest mt-1">فرع المصدر:
                                {{ auth()->user()->branch_code }}</p>
                        </div>
                        <button type="button" @click="openModal = false"
                            class="w-12 h-12 flex items-center justify-center bg-white dark:bg-gray-800 rounded-2xl text-gray-400 hover:text-error-500 transition-all shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-width="3" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-4">
                        <div class="grid grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-2">اسم
                                    السائق</label>
                                <input type="text" name="driver_name" required placeholder="الاسم الكامل"
                                    class="w-full h-12 px-4 bg-gray-50 dark:bg-gray-800 border-none rounded-xl focus:ring-2 focus:ring-brand-500/20 text-sm font-bold dark:text-white shadow-inner">
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-2">هاتف
                                    التواصل (السائق)</label>
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
                                        return this.countries.filter(country => {
                                            const searchLower = this.search.toLowerCase();
                                            return country.name.toLowerCase().includes(searchLower) || country.dial_code.includes(searchLower);
                                        });
                                    }
                                }" class="relative w-full">
                                    <input type="hidden" name="driver_phone"
                                        :value="selectedCountry.dial_code.replace('+', '') + localPhoneNumber">

                                    <div
                                        class="flex h-12 w-full rounded-xl bg-gray-50 dark:bg-gray-800 shadow-inner border-none overflow-hidden focus-within:ring-2 focus-within:ring-brand-500/20 transition-all">
                                        <button type="button" @click="open = !open"
                                            class="flex items-center gap-2 px-3 bg-gray-100 dark:bg-gray-700/50 border-l border-gray-200 dark:border-gray-700 transition-colors hover:bg-gray-200 dark:hover:bg-gray-700">
                                            <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                                alt="Flag" class="w-5 h-auto rounded-sm">
                                            <svg class="h-4 w-4 text-gray-400 transition-transform"
                                                :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        <input type="tel" x-model="localPhoneNumber" placeholder="7XXXXXXXX"
                                            class="flex-grow bg-transparent px-4 text-sm font-bold text-gray-800 dark:text-white focus:outline-none border-none text-left"
                                            dir="ltr">
                                    </div>

                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute z-50 w-full mt-2 overflow-hidden bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-xl max-h-48 overflow-y-auto custom-scrollbar">
                                        <div class="p-2 sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-50 dark:border-gray-800">
                                            <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border-none rounded-lg text-xs font-bold focus:ring-1 focus:ring-brand-500/30">
                                        </div>
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false"
                                                class="flex items-center justify-between p-3 px-4 transition-all cursor-pointer hover:bg-brand-50 dark:hover:bg-brand-500/10 group">
                                                <div class="flex items-center gap-3">
                                                    <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                                        class="w-5 rounded-sm">
                                                    <span class="text-xs font-black text-gray-700 dark:text-gray-300 group-hover:text-brand-600"
                                                        x-text="country.name"></span>
                                                </div>
                                                <span class="text-[10px] font-black tracking-widest text-gray-400"
                                                    x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center px-2">
                                <p class="text-xs font-black text-brand-500 uppercase tracking-widest">الطرود المتاحة
                                    للشحن:</p>
                                <span class="text-[10px] font-bold text-gray-400 uppercase"
                                    x-text="`المحدد حالياً (${selectedCount}) طرود` "></span>
                            </div>

                            <div class="max-h-56 overflow-y-auto pr-2 custom-scrollbar space-y-2">
                                @forelse($pendingShipments as $shipment)
                                    <label
                                        class="flex items-center justify-between p-4 bg-gray-50 dark:bg-white/[0.03] rounded-2xl cursor-pointer my-4 hover:bg-brand-50/50 dark:hover:bg-brand-500/10 transition-all border-2 border-transparent has-[:checked]:border-brand-500 group relative">
                                        <div class="flex items-center gap-4">
                                            <input type="checkbox" name="selected_ids[]" value="{{ $shipment->id }}"
                                                @change="selectedCount = $el.checked ? selectedCount + 1 : selectedCount - 1"
                                                class="w-6 h-6 rounded-lg border-gray-300 text-brand-500 focus:ring-brand-500/20 transition-all">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-gray-900 dark:text-white">
                                                    رقم السند : #{{ $shipment->bond_number }}</span>
                                                <span class="text-sm font-black text-gray-900 dark:text-white">المرسل :
                                                    {{ $shipment->senderCustomer->name }}</span>

                                                <span
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">الوجهة:
                                                    {{ $shipment->receiverBranch->name }}</span>
                                            </div>
                                        </div>
                                        <div class="text-left flex flex-col items-end">
                                            <div class="text-xs font-black text-brand-500">
                                                {{ number_format($shipment->total_amount) }} ر.ي</div>
                                            <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                                {{ $shipment->package_type }}</div>
                                        </div>
                                    </label>
                                @empty
                                    <div
                                        class="py-12 text-center border-2 border-dashed border-gray-100 dark:border-gray-800 rounded-3xl">
                                        <p class="text-gray-400 text-xs font-bold italic px-10">لا توجد طرود في حالة "قيد
                                            الانتظار" بفرعك حالياً.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50/50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800">
                        <button type="submit" :disabled="selectedCount === 0"
                            :class="selectedCount === 0 ? 'opacity-50 cursor-not-allowed grayscale' :
                                'hover:bg-brand-600 shadow-brand-500/25'"
                            class="w-full h-16 bg-brand-500 text-white font-black rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-3">
                            إتمام جدولة الرحلة وشحن الطرود
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* تنسيق السكرول بار داخل قائمة الطرود */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.4s ease-out forwards;
        }
    </style>
@endsection
