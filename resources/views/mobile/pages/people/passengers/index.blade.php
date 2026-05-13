@extends('mobile.layouts.app')

@section('title', 'الركاب')

@section('content')
    <div x-data="passengerMobileRegistry()"
        class="flex relative flex-col gap-6 pb-24 min-h-screen font-body bg-slate-50/50">

        <div class="flex justify-between items-center px-4 mt-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight font-headline text-slate-800">الركاب</h1>
                <p class="mt-0.5 text-xs font-semibold text-slate-400">
                    إجمالي <span class="text-primary">{{ $passengers->total() ?? 0 }}</span> راكب مسجل
                </p>
            </div>
            <a href="{{ route('passengers.create') }}">
                <button type="button"
                    class="flex justify-center items-center w-12 h-12 text-white rounded-[1rem] shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95">
                    <span class="text-2xl material-symbols-outlined"
                        style="font-variation-settings: 'FILL' 1;">person_add</span>
                </button>
            </a>
                 <a href="{{ route('receipt.generate', ['type' => 'passenger', 'id' => 'all']) }}" target="_blank"
                        class="inline-flex gap-2 items-center px-5 h-12 text-sm font-black rounded-2xl transition-all border-2 border-primary text-primary hover:bg-primary hover:text-white active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                    </a>
        </div>

        <div class="px-4">
            <div class="relative group">
                <span
                    class="absolute right-4 top-1/2 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</span>
                <input type="text" x-model="searchQuery" placeholder="ابحث برقم الراكب، العميل، السائق أو المكان..."
                    class="w-full h-14 pr-12 pl-12 rounded-[1.25rem] border-none bg-white shadow-sm ring-1 ring-slate-200/60 focus:ring-2 focus:ring-primary/40 transition-all font-headline text-sm text-slate-700 outline-none">

                <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                    class="flex absolute left-4 top-1/2 justify-center items-center w-8 h-8 rounded-xl transition-transform -translate-y-1/2 bg-slate-50 text-slate-400 active:scale-95">
                    <span class="text-lg material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <div class="pl-4 pr-4 -mt-2">
            <div class="flex gap-2 overflow-x-auto pb-2 custom-scrollbar"
                style="scrollbar-width: none; -ms-overflow-style: none;">
                <style>
                    .custom-scrollbar::-webkit-scrollbar {
                        display: none;
                    }
                </style>

                <button type="button" @click="selectedStatus = ''" :class="selectedStatus === '' ? 'bg-slate-800 text-white shadow-md' :
                            'bg-white text-slate-500 border border-slate-200'"
                    class="px-4 py-2.5 text-xs font-bold rounded-[1rem] whitespace-nowrap transition-all font-headline active:scale-95">
                    الكل
                </button>
                <button type="button" @click="selectedStatus = 'pending'" :class="selectedStatus === 'pending' ? 'bg-slate-500 text-white shadow-md' :
                            'bg-white text-slate-500 border border-slate-200'"
                    class="px-4 py-2.5 text-xs font-bold rounded-[1rem] whitespace-nowrap transition-all font-headline active:scale-95">
                    قيد الانتظار
                </button>
                <button type="button" @click="selectedStatus = 'completed'" :class="selectedStatus === 'completed' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' :
                            'bg-white text-slate-500 border border-slate-200'"
                    class="px-4 py-2.5 text-xs font-bold rounded-[1rem] whitespace-nowrap transition-all font-headline active:scale-95">
                    مكتمل
                </button>
                <button type="button" @click="selectedStatus = 'cancel'" :class="selectedStatus === 'cancel' ? 'bg-rose-500 text-white shadow-md shadow-rose-500/20' :
                            'bg-white text-slate-500 border border-slate-200'"
                    class="px-4 py-2.5 text-xs font-bold rounded-[1rem] whitespace-nowrap transition-all font-headline active:scale-95">
                    ملغي
                </button>
            </div>
        </div>

        <div class="px-4 space-y-4">
            @forelse($passengers as $passenger)
                @php
                    $rawStatus = strtolower($passenger->status ?? 'pending');
                    if ($rawStatus == 'completed' || $rawStatus == 'مكتمل') {
                        $statusLabel = 'مكتمل';
                        $statusKey = 'completed';
                        $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                    } elseif ($rawStatus == 'cancel' || $rawStatus == 'ملغي') {
                        $statusLabel = 'ملغي';
                        $statusKey = 'cancel';
                        $statusClass = 'bg-rose-50 text-rose-600 border-rose-100';
                    } else {
                        $statusLabel = 'قيد الانتظار';
                        $statusKey = 'pending';
                        $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
                    }
                @endphp

                <div x-show="matchSearch({{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->location) }}, {{ json_encode($passenger->customer->name ?? '') }}, {{ json_encode($passenger->customer->phone ?? '') }}, {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($statusKey) }})"
                    class="bg-white rounded-[1.75rem] p-5 shadow-sm border border-slate-100 relative active:scale-[0.98] transition-all passenger-card">

                    <div class="absolute top-5 left-5 z-20 flex gap-2 items-center">
                        <span
                            class="px-3 py-1.5 text-[10px] font-bold rounded-xl border shadow-sm font-headline {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" type="button"
                                class="flex justify-center items-center w-8 h-8 rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 active:scale-95 transition-all shadow-sm border border-slate-100">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 top-full mt-2 z-[60] w-48 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden py-1">
                                <a href="{{ route('passengers.show', $passenger->id) }}"
                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-slate-700 transition-colors hover:bg-blue-50 hover:text-blue-600 font-headline">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    عرض التفاصيل
                                </a>
                                @if ($statusKey === 'pending')
                                    <button type="button" @click="open = false; openEditModal({
                                                id: {{ $passenger->id }},
                                                date: {{ json_encode($passenger->date) }},
                                                status: {{ json_encode($statusKey) }},
                                                passenger_number: {{ json_encode($passenger->passenger_number) }},
                                                customer_id: {{ json_encode($passenger->customer_id) }},
                                                customer_name: {{ json_encode($passenger->customer->name ?? '') }},
                                                customer_phone: {{ json_encode($passenger->customer->phone ?? '') }},
                                                driver_id: {{ json_encode($passenger->driver_id) }},
                                                driver_name: {{ json_encode($passenger->driver->name ?? '') }},
                                                driver_phone: {{ json_encode($passenger->driver->phone ?? '') }},
                                                location: {{ json_encode($passenger->location) }},
                                                count: {{ $passenger->count ?? 1 }},
                                                total_commission: {{ $passenger->total_commission ?? 0 }},
                                                note: {{ json_encode($passenger->note) }}
                                            })"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-slate-700 transition-colors hover:bg-primary/10 hover:text-primary font-headline">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                        تعديل البيانات
                                    </button>
                                @endif
                                <div class="mx-3 my-1 h-px bg-slate-100"></div>

                                @if ($statusKey === 'pending')
                                    <button type="button" @click="open = false; openStatusModal({
                                                    id: {{ $passenger->id }},
                                                    date: {{ json_encode($passenger->date) }},
                                                    status: {{ json_encode($statusKey) }},
                                                    passenger_number: {{ json_encode($passenger->passenger_number) }},
                                                    location: {{ json_encode($passenger->location) }},
                                                    count: {{ $passenger->count ?? 1 }},
                                                    total_commission: {{ $passenger->total_commission ?? 0 }},
                                                    customer_phone: {{ json_encode($passenger->customer->phone ?? '') }},
                                                    customer_name: {{ json_encode($passenger->customer->name ?? '') }},
                                                    driver_phone: {{ json_encode($passenger->driver->phone ?? '') }},
                                                    driver_name: {{ json_encode($passenger->driver->name ?? '') }},
                                                    note: {{ json_encode($passenger->note) }}
                                                })"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-slate-700 transition-colors hover:bg-emerald-50 hover:text-emerald-600 font-headline">
                                        <span class="material-symbols-outlined text-[18px]">fact_check</span>
                                        تعيين الحالة
                                    </button>
                                    <div class="mx-3 my-1 h-px bg-slate-100"></div>
                                @endif

                                {{-- <button type="button"
                                    @click="open = false; openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 font-headline">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    حذف الراكب
                                </button> --}}
                            </div>
                        </div>
                    </div>

                    <div class="flex relative z-10 gap-4 items-center mb-5 pr-1">
                        <div
                            class="flex justify-center items-center w-12 h-12 text-xl font-black bg-primary/10 rounded-[1rem] border shadow-inner text-primary font-headline border-primary/5 shrink-0">
                            {{ mb_substr($passenger->customer->name ?? 'ع', 0, 1, 'UTF-8') }}
                        </div>
                        <div class="flex-1 min-w-0 pr-1">
                            <h3 class="text-base font-black truncate text-slate-800 font-headline pr-2">
                                {{ $passenger->customer->name ?? 'عميل غير محدد' }}
                            </h3>
                            <div class="flex gap-1.5 items-center text-slate-500 mt-1.5 pr-2">
                                <span class="material-symbols-outlined text-[14px]">person</span>
                                <span class="text-[11px] font-bold text-slate-500">العميل</span>
                                <span class="mx-1 text-slate-300">|</span>
                                <div class="font-mono text-xs font-bold text-slate-500 dir-ltr">
                                    <x-phone-number :value="$passenger->customer->phone ?? ''" class="text-slate-500" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="grid grid-cols-2 gap-y-3 gap-x-4 p-4 mb-4 rounded-[1.25rem] bg-slate-50/80 border border-slate-100/60">
                        <div class="flex flex-col gap-1">
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">location_on</span> المكان
                            </span>
                            <span class="text-xs font-bold text-slate-700 truncate pr-1">{{ $passenger->location }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">calendar_today</span> التاريخ
                            </span>
                            <span class="text-xs font-bold text-slate-700 pr-1">{{ $passenger->date }}</span>
                        </div>

                        <div class="flex flex-col gap-1 pt-3 border-t border-slate-200/60">
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">group</span> العدد
                            </span>
                            <span class="text-xs font-bold text-slate-700 pr-1">{{ $passenger->count }} راكب</span>
                        </div>

                        <div class="flex flex-col gap-1 pt-3 border-t border-slate-200/60">
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">payments</span> العمولة
                            </span>
                            <span
                                class="text-xs font-black text-amber-500 pr-1">{{ number_format($passenger->total_commission, 0) }}</span>
                        </div>

                        <div class="flex flex-col gap-1 col-span-2 pt-3 border-t border-slate-200/60">
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">airline_seat_recline_normal</span> بيانات الراكب
                            </span>
                            <div
                                class="flex justify-between items-center bg-white p-2.5 rounded-xl border border-slate-100 shadow-sm mt-1">
                                <span class="text-xs font-bold text-slate-700 truncate pr-1">رقم هاتف الراكب</span>
                                <span class="text-primary font-mono text-xs dir-ltr font-black bg-primary/5 px-2 py-1 rounded-lg">
                                    <x-phone-number :value="$passenger->passenger_number" class="text-primary" />
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1 col-span-2 pt-3 border-t border-slate-200/60">
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400">
                                <span class="material-symbols-outlined text-[14px]">local_taxi</span> بيانات السائق
                            </span>
                            <div
                                class="flex justify-between items-center bg-white p-2.5 rounded-xl border border-slate-100 shadow-sm mt-1">
                                <span class="text-xs font-bold text-primary truncate">
                                    {{ $passenger->driver->name ?? 'غير محدد' }}
                                </span>
                                @if ($passenger->driver && $passenger->driver->phone)
                                    <span
                                        class="text-slate-500 font-mono text-xs dir-ltr font-bold bg-slate-50 px-2 py-1 rounded-lg">
                                        <x-phone-number :value="$passenger->driver->phone" class="text-primary" />
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>


                </div>
            @empty
                <div
                    class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200 shadow-sm">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-300">
                        <span class="text-6xl material-symbols-outlined">group_off</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لم نعثر على أي ركاب</p>
                </div>
            @endforelse

            <div x-show="(searchQuery !== '' || selectedStatus !== '') && !Array.from(document.querySelectorAll('.passenger-card')).some(el => el.style.display !== 'none')"
                style="display: none;"
                class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200 shadow-sm">
                <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-300">
                    <span class="text-6xl material-symbols-outlined">search_off</span>
                </div>
                <p class="text-lg font-bold font-headline text-slate-400">لا توجد نتائج مطابقة لفلترك</p>
            </div>
        </div>

        <div class="px-4 mt-4" x-show="searchQuery === '' && selectedStatus === ''">
            {{ $passengers->links('vendor.pagination.mobile') }}
        </div>

        {{-- ================= Create Modal (Bottom Sheet) ================= --}}
        <div x-show="showCreateModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-8 max-w-xl mx-auto pointer-events-auto overflow-y-auto max-h-[90vh] custom-scrollbar">
                <div @click="closeModals()"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">إضافة راكب جديد</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-[1rem] transition-colors bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-rose-500">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('passengers.store') }}" method="POST" class="space-y-8">
                    @csrf

                    {{-- القسم الأول: معلومات الرحلة --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-400">معلومات الرحلة</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">التاريخ <span
                                        class="text-rose-500">*</span></label>
                                <input type="date" name="date" required value="{{ now()->format('Y-m-d') }}"
                                    class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                            </div>
                            <div>
                                <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">الحالة <span
                                        class="text-rose-500">*</span></label>
                                <select name="status" required
                                    class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                                    <option value="pending">قيد الانتظار</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="cancel">ملغي</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">المكان <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="location" required placeholder="مثال: عدن، حضرموت..."
                                class="px-4 w-full h-14 text-sm rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                        </div>
                    </div>

                    {{-- القسم الثاني: رقم الراكب --}}
                    <div class="space-y-3">
                        <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">رقم الراكب <span
                                class="text-rose-500">*</span></label>
                        <div x-data="phonePickerOnly({ countries: {{ Js::from(array_values(config('countries', []))) }} })"
                            class="relative">
                            <input type="hidden" name="passenger_number" :value="fullPhone">

                            <div class="flex relative rounded-[1rem] ring-1 transition-all bg-white focus-within:ring-2 focus-within:ring-primary ring-slate-200"
                                dir="ltr">
                                <button type="button" @click="openDropdown = !openDropdown"
                                    class="flex gap-2 items-center px-4 py-3 bg-slate-50/50 rounded-l-[1rem] border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors">
                                    <template x-if="selectedCountry?.svg">
                                        <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                            x-html="selectedCountry.svg"></div>
                                    </template>
                                    <span class="text-sm font-bold text-slate-700 font-mono"
                                        x-text="selectedCountry?.dial_code"></span>
                                </button>

                                <input type="tel" x-model="phone" @input="handlePhoneInput" placeholder="7XXXXXXXX" required
                                    class="flex-1 px-4 w-full h-14 text-base font-bold text-left bg-transparent border-none outline-none font-headline text-slate-800 placeholder-slate-300">

                                <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition x-cloak
                                    class="absolute top-[calc(100%+8px)] left-0 z-50 w-[280px] max-h-60 bg-white rounded-[1rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden"
                                    dir="rtl">
                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in countries" :key="country.code">
                                            <div @click="selectCountry(country)"
                                                class="flex gap-3 items-center p-3.5 px-4 transition-colors cursor-pointer hover:bg-slate-50">
                                                <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden shrink-0"
                                                    x-html="country.svg"></div>
                                                <span class="flex-grow text-sm font-bold text-slate-700 font-headline"
                                                    x-text="country.name"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- القسم الثالث: بيانات العميل --}}
                    <div class="space-y-3">
                        <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">العميل <span
                                class="text-rose-500">*</span></label>
                        <div x-data="recordSelect({
                                records: {{ Js::from($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone])->values()) }},
                                countries: {{ Js::from(array_values(config('countries', []))) }}
                            })" class="space-y-3">

                            <input type="hidden" name="customer_id" :value="selectedId">
                            <input type="hidden" name="customer_phone" :value="fullPhone">

                            <div class="relative">
                                <div class="flex relative rounded-[1rem] ring-1 transition-all bg-white focus-within:ring-2 focus-within:ring-primary"
                                    :class="isExisting ? 'ring-primary border-primary bg-primary/5' : 'ring-slate-200'"
                                    dir="ltr">
                                    <button type="button" @click="openDropdown = !openDropdown"
                                        class="flex gap-2 items-center px-4 py-3 bg-slate-50/50 rounded-l-[1rem] border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors">
                                        <template x-if="selectedCountry?.svg">
                                            <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                x-html="selectedCountry.svg"></div>
                                        </template>
                                        <span class="text-sm font-bold text-slate-700 font-mono"
                                            x-text="selectedCountry?.dial_code"></span>
                                    </button>

                                    <input type="tel" x-model="phone" @input="handlePhoneInput" @focus="showDropdown = true"
                                        placeholder="رقم العميل" required
                                        class="flex-1 px-4 w-full h-14 text-base text-left bg-transparent border-none outline-none font-headline placeholder-slate-300"
                                        :class="isExisting ? 'font-bold text-primary' : 'font-bold text-slate-800'">

                                    <button type="button" x-show="isExisting" @click="resetSelection"
                                        class="absolute right-3 top-1/2 z-10 flex justify-center items-center w-7 h-7 bg-white rounded-full -translate-y-1/2 text-slate-400 hover:text-rose-500 shadow-sm border border-slate-200 transition-colors"><span
                                            class="material-symbols-outlined text-[16px]">close</span></button>

                                    <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition x-cloak
                                        class="absolute top-[calc(100%+8px)] left-0 z-50 w-[280px] max-h-60 bg-white rounded-[1rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden"
                                        dir="rtl">
                                        <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                            <template x-for="country in countries" :key="country.code">
                                                <div @click="selectCountry(country)"
                                                    class="flex gap-3 items-center p-3.5 px-4 transition-colors cursor-pointer hover:bg-slate-50">
                                                    <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                        x-html="country.svg"></div>
                                                    <span class="flex-grow text-sm font-bold text-slate-700 font-headline"
                                                        x-text="country.name"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="showDropdown && phone.length > 0 && !isExisting"
                                    @click.outside="showDropdown = false" x-transition x-cloak
                                    class="absolute z-[60] w-full mt-2 bg-white rounded-[1rem] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                    <template x-for="record in filteredRecords" :key="record.id">
                                        <button type="button" @click="selectRecord(record)"
                                            class="flex justify-between items-center w-full p-3.5 text-right border-b hover:bg-slate-50 border-slate-50 transition-colors">
                                            <span
                                                class="material-symbols-outlined text-slate-300 text-[20px]">chevron_left</span>
                                            <div class="flex flex-col items-end gap-0.5">
                                                <span class="text-sm font-bold text-slate-800 font-headline"
                                                    x-text="record.name"></span>
                                                <span class="text-xs text-slate-500 font-mono dir-ltr"
                                                    x-text="record.phone"></span>
                                            </div>
                                        </button>
                                    </template>
                                    <div x-show="filteredRecords.length === 0" class="p-4 text-center bg-slate-50/50">
                                        <span class="text-xs font-bold text-slate-500">تسجيل عميل جديد</span>
                                    </div>
                                </div>
                            </div>

                            <input type="text" name="customer_name" x-model="nameInput" :readonly="isExisting"
                                :required="!isExisting" placeholder="اسم العميل"
                                :class="isExisting ?
                                        'bg-slate-100 text-slate-500 cursor-not-allowed border-none ring-1 ring-slate-200' :
                                        'bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/40 ring-1 ring-slate-200 text-slate-800'"
                                class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] transition-all outline-none font-headline">
                        </div>
                    </div>

                    {{-- القسم الرابع: بيانات السائق --}}
                    <div class="space-y-3">
                        <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">السائق <span
                                class="text-rose-500">*</span></label>
                        <div x-data="recordSelect({
                                records: {{ Js::from($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values()) }},
                                countries: {{ Js::from(array_values(config('countries', []))) }}
                            })" class="space-y-3">

                            <input type="hidden" name="driver_id" :value="selectedId">
                            <input type="hidden" name="driver_phone" :value="fullPhone">

                            <div class="relative">
                                <div class="flex relative rounded-[1rem] ring-1 transition-all bg-white focus-within:ring-2 focus-within:ring-primary"
                                    :class="isExisting ? 'ring-primary border-primary bg-primary/5' : 'ring-slate-200'"
                                    dir="ltr">
                                    <button type="button" @click="openDropdown = !openDropdown"
                                        class="flex gap-2 items-center px-4 py-3 bg-slate-50/50 rounded-l-[1rem] border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors">
                                        <template x-if="selectedCountry?.svg">
                                            <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                x-html="selectedCountry.svg"></div>
                                        </template>
                                        <span class="text-sm font-bold text-slate-700 font-mono"
                                            x-text="selectedCountry?.dial_code"></span>
                                    </button>

                                    <input type="tel" x-model="phone" @input="handlePhoneInput" @focus="showDropdown = true"
                                        placeholder="رقم السائق" required
                                        class="flex-1 px-4 w-full h-14 text-base text-left bg-transparent border-none outline-none font-headline placeholder-slate-300"
                                        :class="isExisting ? 'font-bold text-primary' : 'font-bold text-slate-800'">

                                    <button type="button" x-show="isExisting" @click="resetSelection"
                                        class="absolute right-3 top-1/2 z-10 flex justify-center items-center w-7 h-7 bg-white rounded-full -translate-y-1/2 text-slate-400 hover:text-rose-500 shadow-sm border border-slate-200 transition-colors"><span
                                            class="material-symbols-outlined text-[16px]">close</span></button>

                                    <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition x-cloak
                                        class="absolute top-[calc(100%+8px)] left-0 z-50 w-[280px] max-h-60 bg-white rounded-[1rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden"
                                        dir="rtl">
                                        <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                            <template x-for="country in countries" :key="country.code">
                                                <div @click="selectCountry(country)"
                                                    class="flex gap-3 items-center p-3.5 px-4 transition-colors cursor-pointer hover:bg-slate-50">
                                                    <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                        x-html="country.svg"></div>
                                                    <span class="flex-grow text-sm font-bold text-slate-700 font-headline"
                                                        x-text="country.name"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="showDropdown && phone.length > 0 && !isExisting"
                                    @click.outside="showDropdown = false" x-transition x-cloak
                                    class="absolute z-[60] w-full mt-2 bg-white rounded-[1rem] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                    <template x-for="record in filteredRecords" :key="record.id">
                                        <button type="button" @click="selectRecord(record)"
                                            class="flex justify-between items-center w-full p-3.5 text-right border-b hover:bg-slate-50 border-slate-50 transition-colors">
                                            <span
                                                class="material-symbols-outlined text-slate-300 text-[20px]">chevron_left</span>
                                            <div class="flex flex-col items-end gap-0.5">
                                                <span class="text-sm font-bold text-slate-800 font-headline"
                                                    x-text="record.name"></span>
                                                <span class="text-xs text-slate-500 font-mono dir-ltr"
                                                    x-text="record.phone"></span>
                                            </div>
                                        </button>
                                    </template>
                                    <div x-show="filteredRecords.length === 0" class="p-4 text-center bg-slate-50/50">
                                        <span class="text-xs font-bold text-slate-500">تسجيل سائق جديد</span>
                                    </div>
                                </div>
                            </div>

                            <input type="text" name="driver_name" x-model="nameInput" :readonly="isExisting"
                                :required="!isExisting" placeholder="اسم السائق"
                                :class="isExisting ?
                                        'bg-slate-100 text-slate-500 cursor-not-allowed border-none ring-1 ring-slate-200' :
                                        'bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/40 ring-1 ring-slate-200 text-slate-800'"
                                class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] transition-all outline-none font-headline">
                        </div>
                    </div>

                    {{-- القسم الخامس: الحسابات والملاحظات --}}
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">العدد <span
                                        class="text-rose-500">*</span></label>
                                <input type="number" name="count" required placeholder="1" value="1"
                                    class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                            </div>
                            <div>
                                <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">العمولة <span
                                        class="text-rose-500">*</span></label>
                                <input type="number" name="total_commission" step="0.01" required placeholder="0.00"
                                    class="px-4 w-full h-14 text-sm rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-amber-600 font-black">
                            </div>
                        </div>

                        <div>
                            <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">ملاحظات</label>
                            <textarea name="note" rows="2" placeholder="ملاحظات إضافية..."
                                class="py-4 px-4 w-full text-sm rounded-[1rem] border-none ring-1 transition-all outline-none resize-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline"></textarea>
                        </div>
                    </div>

                    <button type="submit"
                        class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-[1rem] shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95">
                        <span class="material-symbols-outlined">save</span>
                        حفظ البيانات
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= Edit Modal (Bottom Sheet) ================= --}}
        <div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-8 max-w-xl mx-auto pointer-events-auto overflow-y-auto max-h-[90vh] custom-scrollbar">
                <div @click="closeModals()"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات الراكب</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-[1rem] transition-colors bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-rose-500">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <template x-if="showEditModal">
                    <form :action="editPassengerData.url" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        {{-- القسم الأول: معلومات الرحلة --}}
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-400">معلومات الرحلة</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">التاريخ
                                        <span class="text-rose-500">*</span></label>
                                    <input type="date" name="date" required x-model="editPassengerData.date"
                                        class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                                </div>
                                <div>
                                    <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">الحالة
                                        <span class="text-rose-500">*</span></label>
                                    <select name="status" required x-model="editPassengerData.status"
                                        class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                                        <option value="pending">قيد الانتظار</option>
                                        <option value="completed">مكتمل</option>
                                        <option value="cancel">ملغي</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">المكان <span
                                        class="text-rose-500">*</span></label>
                                <input type="text" name="location" required placeholder="المكان"
                                    x-model="editPassengerData.location"
                                    class="px-4 w-full h-14 text-sm rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                            </div>
                        </div>

                        {{-- القسم الثاني: رقم الراكب --}}
                        <div class="space-y-3">
                            <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">رقم الراكب <span
                                    class="text-rose-500">*</span></label>
                            <div x-data="phonePickerOnly({ countries: {{ Js::from(array_values(config('countries', []))) }}, initialPhone: editPassengerData.passenger_number })"
                                class="relative">
                                <input type="hidden" name="passenger_number" :value="fullPhone">

                                <div class="flex relative rounded-[1rem] ring-1 transition-all bg-white focus-within:ring-2 focus-within:ring-primary ring-slate-200"
                                    dir="ltr">
                                    <button type="button" @click="openDropdown = !openDropdown"
                                        class="flex gap-2 items-center px-4 py-3 bg-slate-50/50 rounded-l-[1rem] border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors">
                                        <template x-if="selectedCountry?.svg">
                                            <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                x-html="selectedCountry.svg"></div>
                                        </template>
                                        <span class="text-sm font-bold text-slate-700 font-mono"
                                            x-text="selectedCountry?.dial_code"></span>
                                    </button>

                                    <input type="tel" x-model="phone" @input="handlePhoneInput" placeholder="7XXXXXXXX"
                                        required
                                        class="flex-1 px-4 w-full h-14 text-base font-bold text-left bg-transparent border-none outline-none font-headline text-slate-800 placeholder-slate-300">

                                    <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition x-cloak
                                        class="absolute top-[calc(100%+8px)] left-0 z-50 w-[280px] max-h-60 bg-white rounded-[1rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden"
                                        dir="rtl">
                                        <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                            <template x-for="country in countries" :key="country.code">
                                                <div @click="selectCountry(country)"
                                                    class="flex gap-3 items-center p-3.5 px-4 transition-colors cursor-pointer hover:bg-slate-50">
                                                    <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden shrink-0"
                                                        x-html="country.svg"></div>
                                                    <span class="flex-grow text-sm font-bold text-slate-700 font-headline"
                                                        x-text="country.name"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- القسم الثالث: بيانات العميل --}}
                        <div class="space-y-3">
                            <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">العميل <span
                                    class="text-rose-500">*</span></label>
                            <div x-data="recordSelect({
                                    records: {{ Js::from($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'phone' => $c->phone])->values()) }},
                                    countries: {{ Js::from(array_values(config('countries', []))) }},
                                    initialId: editPassengerData.customer_id,
                                    initialName: editPassengerData.customer_name,
                                    initialPhone: editPassengerData.customer_phone
                                })" class="space-y-3">

                                <input type="hidden" name="customer_id" :value="selectedId">
                                <input type="hidden" name="customer_phone" :value="fullPhone">

                                <div class="relative">
                                    <div class="flex relative rounded-[1rem] ring-1 transition-all bg-white focus-within:ring-2 focus-within:ring-primary"
                                        :class="isExisting ? 'ring-primary border-primary bg-primary/5' : 'ring-slate-200'"
                                        dir="ltr">
                                        <button type="button" @click="openDropdown = !openDropdown"
                                            class="flex gap-2 items-center px-4 py-3 bg-slate-50/50 rounded-l-[1rem] border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors">
                                            <template x-if="selectedCountry?.svg">
                                                <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                    x-html="selectedCountry.svg"></div>
                                            </template>
                                            <span class="text-sm font-bold text-slate-700 font-mono"
                                                x-text="selectedCountry?.dial_code"></span>
                                        </button>

                                        <input type="tel" x-model="phone" @input="handlePhoneInput"
                                            @focus="showDropdown = true" placeholder="رقم العميل" required
                                            class="flex-1 px-4 w-full h-14 text-base text-left bg-transparent border-none outline-none font-headline placeholder-slate-300"
                                            :class="isExisting ? 'font-bold text-primary' : 'font-bold text-slate-800'">

                                        <button type="button" x-show="isExisting" @click="resetSelection"
                                            class="absolute right-3 top-1/2 z-10 flex justify-center items-center w-7 h-7 bg-white rounded-full -translate-y-1/2 text-slate-400 hover:text-rose-500 shadow-sm border border-slate-200 transition-colors"><span
                                                class="material-symbols-outlined text-[16px]">close</span></button>

                                        <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition
                                            x-cloak
                                            class="absolute top-[calc(100%+8px)] left-0 z-50 w-[280px] max-h-60 bg-white rounded-[1rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden"
                                            dir="rtl">
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                                <template x-for="country in countries" :key="country.code">
                                                    <div @click="selectCountry(country)"
                                                        class="flex gap-3 items-center p-3.5 px-4 transition-colors cursor-pointer hover:bg-slate-50">
                                                        <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                            x-html="country.svg"></div>
                                                        <span
                                                            class="flex-grow text-sm font-bold text-slate-700 font-headline"
                                                            x-text="country.name"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="showDropdown && phone.length > 0 && !isExisting"
                                        @click.outside="showDropdown = false" x-transition x-cloak
                                        class="absolute z-[60] w-full mt-2 bg-white rounded-[1rem] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                        <template x-for="record in filteredRecords" :key="record.id">
                                            <button type="button" @click="selectRecord(record)"
                                                class="flex justify-between items-center w-full p-3.5 text-right border-b hover:bg-slate-50 border-slate-50 transition-colors">
                                                <span
                                                    class="material-symbols-outlined text-slate-300 text-[20px]">chevron_left</span>
                                                <div class="flex flex-col items-end gap-0.5">
                                                    <span class="text-sm font-bold text-slate-800 font-headline"
                                                        x-text="record.name"></span>
                                                    <span class="text-xs text-slate-500 font-mono dir-ltr"
                                                        x-text="record.phone"></span>
                                                </div>
                                            </button>
                                        </template>
                                        <div x-show="filteredRecords.length === 0" class="p-4 text-center bg-slate-50/50">
                                            <span class="text-xs font-bold text-slate-500">تسجيل عميل جديد</span>
                                        </div>
                                    </div>
                                </div>

                                <input type="text" name="customer_name" x-model="nameInput" :readonly="isExisting"
                                    :required="!isExisting" placeholder="اسم العميل"
                                    :class="isExisting ?
                                            'bg-slate-100 text-slate-500 cursor-not-allowed border-none ring-1 ring-slate-200' :
                                            'bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/40 ring-1 ring-slate-200 text-slate-800'"
                                    class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] transition-all outline-none font-headline">
                            </div>
                        </div>

                        {{-- القسم الرابع: بيانات السائق --}}
                        <div class="space-y-3">
                            <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">السائق <span
                                    class="text-rose-500">*</span></label>
                            <div x-data="recordSelect({
                                    records: {{ Js::from($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values()) }},
                                    countries: {{ Js::from(array_values(config('countries', []))) }},
                                    initialId: editPassengerData.driver_id,
                                    initialName: editPassengerData.driver_name,
                                    initialPhone: editPassengerData.driver_phone
                                })" class="space-y-3">

                                <input type="hidden" name="driver_id" :value="selectedId">
                                <input type="hidden" name="driver_phone" :value="fullPhone">

                                <div class="relative">
                                    <div class="flex relative rounded-[1rem] ring-1 transition-all bg-white focus-within:ring-2 focus-within:ring-primary"
                                        :class="isExisting ? 'ring-primary border-primary bg-primary/5' : 'ring-slate-200'"
                                        dir="ltr">
                                        <button type="button" @click="openDropdown = !openDropdown"
                                            class="flex gap-2 items-center px-4 py-3 bg-slate-50/50 rounded-l-[1rem] border-r border-slate-200 shrink-0 hover:bg-slate-100 transition-colors">
                                            <template x-if="selectedCountry?.svg">
                                                <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                    x-html="selectedCountry.svg"></div>
                                            </template>
                                            <span class="text-sm font-bold text-slate-700 font-mono"
                                                x-text="selectedCountry?.dial_code"></span>
                                        </button>

                                        <input type="tel" x-model="phone" @input="handlePhoneInput"
                                            @focus="showDropdown = true" placeholder="رقم السائق" required
                                            class="flex-1 px-4 w-full h-14 text-base text-left bg-transparent border-none outline-none font-headline placeholder-slate-300"
                                            :class="isExisting ? 'font-bold text-primary' : 'font-bold text-slate-800'">

                                        <button type="button" x-show="isExisting" @click="resetSelection"
                                            class="absolute right-3 top-1/2 z-10 flex justify-center items-center w-7 h-7 bg-white rounded-full -translate-y-1/2 text-slate-400 hover:text-rose-500 shadow-sm border border-slate-200 transition-colors"><span
                                                class="material-symbols-outlined text-[16px]">close</span></button>

                                        <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition
                                            x-cloak
                                            class="absolute top-[calc(100%+8px)] left-0 z-50 w-[280px] max-h-60 bg-white rounded-[1rem] border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden"
                                            dir="rtl">
                                            <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                                <template x-for="country in countries" :key="country.code">
                                                    <div @click="selectCountry(country)"
                                                        class="flex gap-3 items-center p-3.5 px-4 transition-colors cursor-pointer hover:bg-slate-50">
                                                        <div class="w-6 h-auto rounded-sm shadow-sm overflow-hidden"
                                                            x-html="country.svg"></div>
                                                        <span
                                                            class="flex-grow text-sm font-bold text-slate-700 font-headline"
                                                            x-text="country.name"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="showDropdown && phone.length > 0 && !isExisting"
                                        @click.outside="showDropdown = false" x-transition x-cloak
                                        class="absolute z-[60] w-full mt-2 bg-white rounded-[1rem] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                        <template x-for="record in filteredRecords" :key="record.id">
                                            <button type="button" @click="selectRecord(record)"
                                                class="flex justify-between items-center w-full p-3.5 text-right border-b hover:bg-slate-50 border-slate-50 transition-colors">
                                                <span
                                                    class="material-symbols-outlined text-slate-300 text-[20px]">chevron_left</span>
                                                <div class="flex flex-col items-end gap-0.5">
                                                    <span class="text-sm font-bold text-slate-800 font-headline"
                                                        x-text="record.name"></span>
                                                    <span class="text-xs text-slate-500 font-mono dir-ltr"
                                                        x-text="record.phone"></span>
                                                </div>
                                            </button>
                                        </template>
                                        <div x-show="filteredRecords.length === 0" class="p-4 text-center bg-slate-50/50">
                                            <span class="text-xs font-bold text-slate-500">تسجيل سائق جديد</span>
                                        </div>
                                    </div>
                                </div>

                                <input type="text" name="driver_name" x-model="nameInput" :readonly="isExisting"
                                    :required="!isExisting" placeholder="اسم السائق"
                                    :class="isExisting ?
                                            'bg-slate-100 text-slate-500 cursor-not-allowed border-none ring-1 ring-slate-200' :
                                            'bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/40 ring-1 ring-slate-200 text-slate-800'"
                                    class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] transition-all outline-none font-headline">
                            </div>
                        </div>

                        {{-- القسم الخامس: الحسابات والملاحظات --}}
                        <div class="space-y-3 pt-4 border-t border-slate-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">العدد
                                        <span class="text-rose-500">*</span></label>
                                    <input type="number" name="count" required placeholder="1"
                                        x-model="editPassengerData.count"
                                        class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                                </div>
                                <div>
                                    <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">العمولة
                                        <span class="text-rose-500">*</span></label>
                                    <input type="number" name="total_commission" step="0.01" required placeholder="0.00"
                                        x-model="editPassengerData.total_commission"
                                        class="px-4 w-full h-14 text-sm rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-amber-600 font-black">
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">ملاحظات</label>
                                <textarea name="note" rows="2" placeholder="ملاحظات إضافية..."
                                    x-model="editPassengerData.note"
                                    class="py-4 px-4 w-full text-sm rounded-[1rem] border-none ring-1 transition-all outline-none resize-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline"></textarea>
                            </div>
                        </div>

                        <button type="submit"
                            class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-[1rem] shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95">
                            <span class="material-symbols-outlined">update</span>
                            حفظ التعديلات
                        </button>
                    </form>
                </template>
            </div>
        </div>

        {{-- ================= Status Modal (Bottom Sheet) ================= --}}
        <div x-show="showStatusModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-8 max-w-xl mx-auto pointer-events-auto text-right">
                <div @click="closeModals()"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black font-headline text-slate-800">تعيين حالة الراكب</h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 rounded-[1rem] transition-colors bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-rose-500">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="statusPassengerData.url" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block px-1 mb-2 text-xs font-bold text-slate-500 font-headline">تحديث الحالة
                            إلى:</label>
                        <select name="status" x-model="statusPassengerData.status" required
                            class="px-4 w-full h-14 text-sm font-bold rounded-[1rem] border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-200 focus:ring-2 focus:ring-primary/40 font-headline text-slate-700">
                            <option value="pending">قيد الانتظار</option>
                            <option value="completed">مكتمل</option>
                            <option value="cancel">ملغي</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-[1rem] shadow-lg transition-all bg-emerald-500 font-headline shadow-emerald-500/30 active:scale-95">
                        <span class="material-symbols-outlined">fact_check</span>
                        حفظ الحالة
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= Delete Modal (Bottom Sheet) ================= --}}
        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-8 text-center pointer-events-auto">
                <div @click="closeModals()"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>

                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 text-rose-500 rounded-[1.5rem] shadow-sm">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>
                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد الحذف</h3>
                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من حذف الراكب رقم:<br>
                    <span class="text-base font-bold text-slate-800 font-mono dir-ltr inline-block"
                        x-text="deletePassengerData.passenger_number"></span>؟<br>
                    <span class="inline-block mt-2 text-rose-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>

                <form :action="deletePassengerData.url" method="POST" class="flex gap-3 w-full">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="closeModals()"
                        class="flex-1 h-14 text-sm font-bold text-slate-600 rounded-[1rem] transition-all bg-slate-50 hover:bg-slate-100 active:scale-95 font-headline">تراجع</button>
                    <button type="submit"
                        class="flex-1 h-14 text-sm font-bold text-white rounded-[1rem] shadow-lg transition-all bg-rose-500 hover:bg-rose-600 shadow-rose-500/30 active:scale-95 font-headline">نعم،
                        احذف</button>
                </form>
            </div>
        </div>
    </div>
    </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('passengerMobileRegistry', () => ({
                searchQuery: '',
                selectedStatus: '',
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                showStatusModal: false,
                deletePassengerData: {
                    id: null,
                    passenger_number: '',
                    url: ''
                },
                statusPassengerData: {
                    id: '',
                    status: 'pending',
                    url: '',
                    date: '',
                    passenger_number: '',
                    location: '',
                    count: 1,
                    total_commission: 0,
                    customer_phone: '',
                    customer_name: '',
                    driver_phone: '',
                    driver_name: '',
                    note: ''
                },
                editPassengerData: {
                    id: null,
                    date: '',
                    status: '',
                    passenger_number: '',
                    customer_id: null,
                    customer_name: '',
                    customer_phone: '',
                    location: '',
                    count: null,
                    total_commission: null,
                    driver_id: null,
                    driver_name: '',
                    driver_phone: '',
                    note: '',
                    url: ''
                },
                matchSearch(number, location, cName, cPhone, dName, dPhone, statusKey) {
                    if (this.selectedStatus !== '' && statusKey !== this.selectedStatus) {
                        return false;
                    }

                    if (this.searchQuery.trim() === '') return true;

                    const query = this.searchQuery.toLowerCase().trim();
                    const cleanQuery = query.replace(/^(\+967|967|00967|0)/, '');

                    const check = (str) => {
                        if (!str) return false;
                        const cleanStr = String(str).toLowerCase();
                        return cleanStr.includes(query) || (cleanQuery !== '' && cleanStr.includes(
                            cleanQuery));
                    };

                    const statusMap = {
                        'pending': 'قيد الانتظار',
                        'completed': 'مكتمل',
                        'cancel': 'ملغي'
                    };
                    const arabicStatus = statusMap[statusKey] || statusKey;

                    return check(number) || check(location) || check(cName) || check(cPhone) || check(
                        dName) || check(dPhone) || check(arabicStatus);
                },
                openEditModal(data) {
                    this.editPassengerData = {
                        ...data,
                        url: '{{ url('passengers') }}/' + data.id
                    };
                    this.showEditModal = true;
                },
                openDeleteModal(id, passengerNumber) {
                    this.deletePassengerData = {
                        id: id,
                        passenger_number: passengerNumber,
                        url: '{{ url('passengers') }}/' + id
                    };
                    this.showDeleteModal = true;
                },
                openStatusModal(passenger) {
                    this.statusPassengerData = {
                        ...passenger,
                        url: '{{ url('passengers') }}/' + passenger.id + '/status'
                    };
                    this.showStatusModal = true;
                },
                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showDeleteModal = false;
                    this.showStatusModal = false;
                }
            }));

            Alpine.data('recordSelect', ({
                records,
                countries,
                initialId = null,
                initialName = '',
                initialPhone = ''
            }) => ({
                records: records || [],
                countries: countries || [],
                openDropdown: false,
                selectedCountry: null,
                phone: '',
                fullPhone: '',
                nameInput: initialName || '',
                selectedId: initialId || '',
                isExisting: initialId ? true : false,
                filteredRecords: [],
                showDropdown: false,

                init() {
                    const yemen = this.countries.find(c => c.dial_code === '+967');
                    this.selectedCountry = yemen || (this.countries.length > 0 ? this.countries[0] : {
                        dial_code: '+967'
                    });

                    if (initialPhone) {
                        let phoneStr = String(initialPhone);
                        if (!phoneStr.startsWith('+')) {
                            phoneStr = '+' + phoneStr;
                        }

                        let matchedCountry = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length)
                            .find(c => phoneStr.startsWith(c.dial_code));

                        if (matchedCountry) {
                            this.selectedCountry = matchedCountry;
                            this.phone = phoneStr.substring(matchedCountry.dial_code.length);
                        } else {
                            this.phone = initialPhone;
                        }
                        this.updateFullPhone();
                    }
                },

                selectCountry(country) {
                    this.selectedCountry = country;
                    this.openDropdown = false;
                    this.updateFullPhone();
                    this.searchRecord();
                },

                handlePhoneInput() {
                    this.phone = this.phone.replace(/[^0-9]/g, '');
                    this.updateFullPhone();
                    this.searchRecord();
                },

                updateFullPhone() {
                    this.fullPhone = this.phone ? (this.selectedCountry.dial_code + this.phone) : '';
                },

                searchRecord() {
                    this.selectedId = '';
                    this.isExisting = false;

                    if (this.phone.trim() === '') {
                        this.filteredRecords = [];
                        this.showDropdown = false;
                        // تم التعديل هنا: يتم مسح الاسم فقط إذا لم يكن هناك ID مسبق والمسح تم من قبل المستخدم
                        if (!initialId) this.nameInput = '';
                        return;
                    }

                    let query = this.fullPhone.replace(/[^\d]/g, '');
                    let cleanQuery = this.phone.replace(/[^\d]/g, '');

                    this.filteredRecords = this.records.filter(r => {
                        if (!r.phone) return false;
                        let p = String(r.phone).replace(/[^\d]/g, '');
                        return p.includes(query) || p.includes(cleanQuery);
                    });

                    this.showDropdown = true;

                    // تم التعديل هنا: السماح بالتحديد التلقائي إذا تطابق الرقم دون انتظار إغلاق القائمة
                    const exactMatch = this.records.find(r => {
                        let p = String(r.phone).replace(/[^\d]/g, '');
                        return p === query || p === cleanQuery;
                    });

                    if (exactMatch) {
                        this.selectRecord(exactMatch);
                    }
                },

                selectRecord(record) {
                    this.selectedId = record.id;
                    this.nameInput = record.name;
                    this.isExisting = true;
                    this.showDropdown = false;

                    let dialCode = this.selectedCountry ? String(this.selectedCountry.dial_code)
                        .replace('+', '') : '';
                    let searchDialCode = String(this.selectedCountry.dial_code);

                    if (record.phone && String(record.phone).startsWith(searchDialCode)) {
                        this.phone = String(record.phone).substring(searchDialCode.length);
                    } else if (record.phone && String(record.phone).startsWith(dialCode)) {
                        this.phone = String(record.phone).substring(dialCode.length);
                    } else {
                        this.phone = record.phone;
                    }
                    this.updateFullPhone();
                },

                resetSelection() {
                    this.selectedId = '';
                    this.phone = '';
                    this.nameInput = '';
                    this.isExisting = false;
                    this.showDropdown = false;
                    this.updateFullPhone();
                }
            }));

            Alpine.data('phonePickerOnly', ({
                countries,
                initialPhone = ''
            }) => ({
                countries: countries || [],
                openDropdown: false,
                selectedCountry: null,
                phone: '',
                fullPhone: '',

                init() {
                    const yemen = this.countries.find(c => c.dial_code === '+967');
                    this.selectedCountry = yemen || (this.countries.length > 0 ? this.countries[0] : {
                        dial_code: '+967'
                    });

                    if (initialPhone) {
                        let phoneStr = String(initialPhone);
                        if (!phoneStr.startsWith('+')) {
                            phoneStr = '+' + phoneStr;
                        }

                        let matchedCountry = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length)
                            .find(c => phoneStr.startsWith(c.dial_code));

                        if (matchedCountry) {
                            this.selectedCountry = matchedCountry;
                            this.phone = phoneStr.substring(matchedCountry.dial_code.length);
                        } else {
                            this.phone = initialPhone;
                        }
                        this.updateFullPhone();
                    }
                },

                selectCountry(country) {
                    this.selectedCountry = country;
                    this.openDropdown = false;
                    this.updateFullPhone();
                },

                handlePhoneInput() {
                    this.phone = this.phone.replace(/[^0-9]/g, '');
                    this.updateFullPhone();
                },

                updateFullPhone() {
                    this.fullPhone = this.phone ? (this.selectedCountry.dial_code + this.phone) : '';
                }
            }));
        });
    </script>
@endsection