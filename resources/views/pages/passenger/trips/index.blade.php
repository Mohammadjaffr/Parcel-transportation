@extends('layouts.app')

@section('title', 'إدارة الرحلات')
@section('Breadcrumb', 'إدارة الرحلات')

@section('content')
    <div x-data="tripsData()" class="pb-24 space-y-6 min-h-screen font-body lg:pb-12" dir="rtl">

        {{-- Header --}}
        <div class="mx-auto w-full max-w-7xl">
            <div class="flex gap-4 justify-between items-start">
                <div class="text-right">
                    <h1 class="text-2xl font-black font-headline text-slate-800 dark:text-white">إدارة الرحلات</h1>
                    <p class="mt-1 text-sm font-bold text-gray-500 dark:text-gray-400">
                        إجمالي {{ $trips->total() }} رحلة مسجلة
                    </p>
                </div>
                <div class="flex gap-2 items-center shrink-0">
                    <a :href="'{{ route('receipt.generate', ['type' => 'all_trips', 'id' => '__ID__']) }}'.replace('__ID__',
                        getPrintUrl())"
                        target="_blank"
                        class="inline-flex gap-2 items-center px-5 h-12 text-sm font-black rounded-2xl border-2 transition-all border-primary text-primary hover:bg-primary hover:text-white active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        <span>طباعة الكشف</span>
                    </a>
                    <a href="{{ route('trips.create') }}"
                        class="inline-flex gap-2.5 items-center px-5 h-12 text-sm font-black text-white rounded-2xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/25 active:scale-95">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span>إنشاء رحلة جديدة</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Search & Table --}}
        <div
            class="bg-white dark:bg-boxdark my-4 rounded-2xl border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="flex flex-col gap-4 justify-between items-stretch md:flex-row md:items-center">
                    <div
                        class="relative flex flex-row items-center px-3 w-full gap-3 rounded-2xl border border-gray-200 transition-all md:w-[420px] dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-slate-50 dark:bg-boxdark-2">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="updateVisibility()"
                            placeholder="ابحث برقم أو اسم السائق..."
                            class="flex-1 px-3 w-full h-12 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-body text-gray-600 dark:text-gray-300">
                        <div
                            class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                            <span class="material-symbols-outlined text-[22px]">search</span>
                        </div>
                        <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; updateVisibility()"
                            x-cloak
                            class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all -translate-y-1/2 dark:bg-boxdark dark:border-boxdark-2 hover:text-error active:scale-95">
                            <span class="text-[18px] material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="flex gap-2 items-center text-xs font-bold text-gray-500 dark:text-bodydark font-body">
                        <span class="inline-flex justify-center items-center w-8 h-8 rounded-xl bg-primary/10 text-primary">
                            <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                        </span>
                        <span>النتائج المعروضة: <span class="text-primary font-bold" x-text="visibleCount"></span> من
                            <span>{{ $trips->count() }}</span></span>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-visible w-full">
                <table class="table-auto w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-boxdark-2 border-b border-gray-100 dark:border-boxdark-2">
                            <th
                                class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider font-headline">
                                الرحلة والتاريخ</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider font-headline">
                                بيانات السائق</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider font-headline text-center">
                                إجمالي الركاب</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-wider font-headline text-center">
                                الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse($trips as $trip)
                            @php
                                $driverName = $trip->driver->name ?? 'سائق غير معين';
                                $driverPhone = $trip->driver->phone ?? '';
                                $avatarLetters = mb_substr(preg_replace('/[^a-zA-Zأ-ي]/u', '', $driverName), 0, 2);
                                if (empty($avatarLetters)) {
                                    $avatarLetters = 'س';
                                }
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/80 dark:hover:bg-boxdark-2/50 group trip-row"
                                x-show="showRow(@js($driverName), @js($driverPhone))">

                                {{-- Date & ID --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex flex-col gap-1">
                                        <span class="font-body text-sm font-bold text-gray-800 dark:text-white">رحلة
                                            #{{ $trip->id }}</span>
                                        <span
                                            class="font-body text-xs text-gray-500 dark:text-gray-400">{{ $trip->created_at->format('Y-m-d h:i A') }}</span>
                                    </div>
                                </td>

                                {{-- Driver --}}
                                <td class="px-6 py-4 align-top">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex items-center justify-center w-11 h-11 rounded-full bg-primary/10 text-primary font-headline font-black uppercase shadow-sm shrink-0">
                                            {{ $avatarLetters }}
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span class="font-body text-sm font-bold text-gray-800 dark:text-white">
                                                {{ $driverName }}
                                            </span>
                                            @if ($driverPhone)
                                                <span
                                                    class="font-body text-xs text-gray-500 dark:text-gray-400 dir-ltr text-right font-mono block mt-0.5"
                                                    style="direction: ltr;">
                                                    <x-phone-number :value="$driverPhone"
                                                        class="text-gray-500 dark:text-gray-400" />
                                                </span>
                                            @else
                                                <span class="text-[10px] text-amber-500 font-bold block mt-0.5">يرجى تعيين
                                                    سائق للرحلة</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Passengers count --}}
                                <td class="px-6 py-4 align-middle text-center">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-black bg-slate-100 text-slate-700 dark:bg-boxdark-2 dark:text-gray-300">
                                        <span class="material-symbols-outlined text-[18px] text-gray-400">groups</span>
                                        {{ $trip->passengers->sum('count') }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 align-middle text-center relative" x-data="{ openMenu: false }">
                                    <div class="flex items-center justify-center">
                                        <button @click="openMenu = !openMenu" @click.away="openMenu = false" type="button"
                                            class="flex justify-center items-center w-8 h-8 text-slate-400 hover:text-slate-600 dark:hover:text-white bg-slate-50 dark:bg-boxdark-2 rounded-full transition-all active:scale-90">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        {{-- Dropdown Menu --}}
                                        <div x-show="openMenu" x-cloak x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="absolute left-6 top-12 mt-2 w-44 bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-2xl shadow-xl z-30 overflow-hidden py-1 text-right">

                                            <a href="{{ route('trips.show', $trip->id) }}"
                                                class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors">
                                                <span
                                                    class="material-symbols-outlined text-[16px] text-blue-500">visibility</span>
                                                عرض التفاصيل
                                            </a>

                                            <a href="{{ route('trips.edit', $trip->id) }}"
                                                class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors">
                                                <span
                                                    class="material-symbols-outlined text-[16px] text-amber-500">edit</span>
                                                تعديل الرحلة
                                            </a>

                                            <a href="{{ route('receipt.generate', ['type' => 'trip', 'id' => $trip->uuid]) }}"
                                                target="_blank"
                                                class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors border-t border-slate-50 dark:border-boxdark-2">
                                                <span
                                                    class="material-symbols-outlined text-[16px] text-emerald-500">print</span>
                                                طباعة الكشف / السند
                                            </a>

                                            <a href="{{ $trip->driver_pdf_link }}" target="_blank"
                                                class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                                <span class="material-symbols-outlined text-[18px]">send</span> إرسال
                                                للسائق
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div
                                            class="flex justify-center items-center w-16 h-16 bg-slate-50 rounded-full border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark text-gray-400">
                                            <span class="material-symbols-outlined text-[32px]">explore_off</span>
                                        </div>
                                        <div>
                                            <h3
                                                class="mb-1 font-headline text-base font-black text-slate-700 dark:text-white">
                                                لا توجد رحلات مضافة حالياً</h3>
                                            <p class="font-body text-sm font-medium text-gray-500 dark:text-gray-400">قم
                                                بإنشاء أول رحلة الآن لتبدأ بتوزيع الركاب بداخلها.</p>
                                        </div>
                                        <a href="{{ route('trips.create') }}"
                                            class="mt-2 px-6 h-12 bg-primary text-white text-sm font-black rounded-xl shadow-md shadow-primary/20 flex items-center gap-2 transition-all active:scale-95">
                                            <span class="material-symbols-outlined text-[20px]">add</span> إنشاء أول رحلة
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $trips->count() }} > 0" x-cloak>
                            <td colspan="4" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div
                                        class="flex justify-center items-center w-16 h-16 bg-slate-50 rounded-full border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark text-gray-400">
                                        <span class="material-symbols-outlined text-[32px]">search_off</span>
                                    </div>
                                    <div>
                                        <h3 class="mb-1 font-headline text-base font-black text-slate-700 dark:text-white">
                                            لا توجد نتائج مطابقة</h3>
                                        <p class="font-body text-sm font-medium text-gray-500 dark:text-gray-400">لم نعثر
                                            على رحلات تطابق بحثك.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($trips->hasPages())
                <div
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-slate-50/50 dark:bg-boxdark-2/50 rounded-b-2xl custom-pagination">
                    {{ $trips->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script>
        function tripsData() {
            return {
                searchQuery: '',
                visibleCount: {{ $trips->count() }},

                init() {
                    this.updateVisibility();
                },

                getPrintUrl() {
                    return 'all';
                },

                showRow(driverName, driverPhone) {
                    const query = this.searchQuery.toLowerCase().trim();
                    if (!query) return true;

                    const cleanQuery = query.replace(/^(\+967|967|00967|0)/, '');
                    const check = (str) => {
                        if (!str) return false;
                        const cleanStr = String(str).toLowerCase();
                        return cleanStr.includes(query) || (cleanQuery !== '' && cleanStr.includes(cleanQuery));
                    };

                    return check(driverName) || check(driverPhone);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.trip-row:not([style*="display: none"])')
                            .length;
                    });
                }
            }
        }
    </script>
@endsection
