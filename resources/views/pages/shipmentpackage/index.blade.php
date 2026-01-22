@extends('layouts.app')
@section('title', 'إدارة الرحلات والشحنات')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
        search: '',
        openModal: false,
        selectedCount: 0,
        selectedBranch: '',
        showRow(tracking, driver) {
            return tracking.toLowerCase().includes(this.search.toLowerCase()) ||
                driver.toLowerCase().includes(this.search.toLowerCase());
        }
    }">

        <div
            class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="flex gap-4 items-center xl:col-span-4">
                <div
                    class="flex justify-center items-center w-12 h-12 text-white rounded-2xl shadow-lg bg-brand-500 shadow-brand-500/30 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black leading-tight text-gray-900 dark:text-white">شحنات الرحلات</h2>
                    <p class="italic font-bold tracking-widest text-gray-500 uppercase text-theme-xs">تجميع وإدارة الشحن
                        الجماعي</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 justify-end xl:col-span-8 md:flex-row">
                <div
                    class="flex gap-4 items-center px-6 py-3 rounded-2xl border bg-brand-50 dark:bg-brand-500/10 border-brand-100 dark:border-brand-500/20">
                    <span
                        class="text-2xl font-black text-brand-600 dark:text-brand-400">{{ $pendingShipments->count() }}</span>
                    <span class="text-xs font-bold leading-tight text-gray-500 uppercase">طرد بانتظار <br>الشحن</span>
                </div>

                <button @click="openModal = true"
                    class="flex gap-2 justify-center items-center px-8 h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/20 active:scale-95">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-width="3" />
                    </svg>
                    تجهيز رحلة جديدة
                </button>
            </div>
        </div>

        <div
            class="w-full bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div class="relative w-full group">
                <input type="text" x-model="search" placeholder="ابحث برقم التتبع، اسم السائق..."
                    class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                <div
                    class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 group-focus-within:text-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="overflow-hidden bg-white rounded-2xl border border-gray-100 dark:bg-gray-800 dark:border-gray-800 shadow-theme-sm">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">رقم التتبع</th>
                            <th class="px-6 py-4">السائق المسؤول</th>
                            <th class="px-6 py-4 text-center">حمولة الطرود</th>
                            <th class="px-6 py-4 text-center">التاريخ</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse($packages as $pkg)
                            <tr x-show="showRow('{{ $pkg->tracking_number }}', '{{ $pkg->driver_name }}')" x-transition
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1.5 text-xs font-black bg-gray-50 rounded-lg border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                                        #{{ $pkg->tracking_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-900 dark:text-white">{{ $pkg->driver_name }}</span>
                                        <span
                                            class="text-[10px] font-bold text-gray-400 font-mono">{{ $pkg->driver_phone }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1 text-xs font-black rounded-lg border bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border-brand-100/50">
                                        {{ $pkg->shipments_count }} طرود مشحونه
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $pkg->created_at->format('Y-m-d') }}</span>
                                </td>

                                <td
                                    class="px-6 py-5 text-center border-l last:rounded-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-2 justify-center items-center">
                                        <a href="{{ route('shipmentpackage.show', $pkg->id) }}"
                                            class="p-2 text-gray-400 rounded-xl transition-all hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10"
                                            title="عرض تفاصيل الشحنه">
                                            <svg class="w-5 h-5 text-gray-400 transition-colors group-hover:text-brand-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <div class="flex flex-col gap-2 items-center italic text-gray-400">
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
                <div class="p-8 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>

        <div x-show="openModal" class="flex fixed inset-0 justify-center items-center p-4 z-99999 modal" x-cloak
            x-transition>
            <div class="fixed inset-0 w-full h-full bg-gray-400/50 backdrop-blur-[32px]" @click="openModal = false"></div>
            <div @click.away="openModal = false"
                class="relative w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-2xl dark:bg-gray-900 dark:border-gray-800">

                <form action="{{ route('shipmentpackage.store') }}" method="POST">
                    @csrf

                    {{-- Header ثابت --}}
                    <div
                        class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-white/[0.02]">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white">انشاء شحنة جديدة</h3>
                            <p class="text-[10px] text-brand-500 font-bold uppercase tracking-widest mt-0.5">فرع المصدر:
                                {{ auth()->user()->branch_code }}</p>
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
                                            <svg class="w-3 h-3 text-gray-400" :class="open ? 'rotate-180' : ''"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                <label
                                    x-show="selectedBranch === '' || selectedBranch == '{{ $shipment->receiver_branch_code }}'"
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
                                            {{ number_format($shipment->total_amount) }} ر.ي</div>
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
                        <button type="submit" :disabled="selectedCount === 0"
                            :class="selectedCount === 0 ? 'opacity-50 cursor-not-allowed' :
                                'hover:bg-brand-600 shadow-brand-500/25'"
                            class="flex gap-2 justify-center items-center w-full h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-brand-500 active:scale-95">
                            إتمام جدولة الرحلة
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
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
