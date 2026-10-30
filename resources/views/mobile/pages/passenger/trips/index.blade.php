@extends('mobile.layouts.app')

@section('title', 'إدارة الرحلات')

@section('content')

    <div class="flex flex-col pt-4 pb-24 min-h-screen bg-slate-50/50 dark:bg-black font-headline" dir="rtl" x-data="tripsDataMobile()">

        {{-- ================= الهيدر وزر إضافة رحلة جديدة ================= --}}
        <div class="flex justify-between items-center px-4 mb-4">
            <div>
                <h1 class="text-lg font-black font-headline text-slate-800 dark:text-white">قائمة الرحلات الحالية</h1>
                <p class="text-xs text-gray-400 mt-0.5">إدارة وتجميع الركاب داخل الرحلات</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('receipt.generate', ['type' => 'all_trips', 'id' => 'branch_id:' . auth()->user()->branch_id]) }}" target="_blank"
                    class="flex justify-center items-center w-11 h-11 bg-primary/10 text-primary border border-primary/20 rounded-[1rem] shadow-sm transition-all active:scale-90 hover:bg-primary hover:text-white"
                    title="طباعة الكشف">
                    <span class="material-symbols-outlined text-[24px]">print</span>
                </a>
                {{-- زر إضافة رحلة جديدة --}}
                <a href="{{ route('trips.create') }}"
                    class="flex justify-center items-center w-11 h-11 bg-primary text-white rounded-full shadow-md shadow-primary/20 transition-all active:scale-90"
                    title="إنشاء رحلة جديدة">
                    <span class="material-symbols-outlined text-[24px]">add</span>
                </a>
            </div>
        </div>

        {{-- ================= شريط البحث السريع والفلترة ================= --}}
        <div class="px-4 mb-4">
            <div class="relative flex items-center bg-white dark:bg-boxdark rounded-2xl border border-slate-200/80 dark:border-boxdark-2 shadow-sm focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all p-1">
                <div class="pr-3 pl-2 text-slate-400 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input type="text" x-model="searchQuery" @input.debounce.250ms="updateVisibility()"
                    placeholder="ابحث برقم الرحلة، اسم السائق، الهاتف، اليوم..."
                    class="w-full h-10 text-xs font-bold text-slate-700 dark:text-gray-200 bg-transparent border-0 outline-none focus:ring-0 placeholder-slate-400">
                <button type="button" x-show="searchQuery.length > 0" x-cloak
                    @click="searchQuery = ''; updateVisibility()"
                    class="flex justify-center items-center w-8 h-8 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 active:scale-95 ml-1">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>
            <div class="flex justify-between items-center px-1 mt-2 text-[11px] font-bold text-slate-400">
                <span>النتائج المعروضة: <strong class="text-primary font-black" x-text="visibleCount"></strong> من <span>{{ $trips->count() }}</span></span>
                <span x-show="searchQuery.length > 0" x-cloak class="text-primary cursor-pointer hover:underline" @click="searchQuery = ''; updateVisibility()">إعادة تعيين</span>
            </div>
        </div>

        {{-- ================= حاوية الرحلات الأساسية ================= --}}
        <div class="px-4 space-y-4">

            {{-- رسالة عند عدم وجود نتائج للبحث --}}
            <div x-show="visibleCount === 0 && searchQuery.length > 0" x-cloak
                class="w-full bg-white dark:bg-boxdark rounded-[2rem] p-8 border border-slate-100 dark:border-boxdark-2 text-center">
                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">search_off</span>
                <p class="text-xs font-bold text-slate-500">لا توجد رحلات تطابق بحثك</p>
                <button type="button" @click="searchQuery = ''; updateVisibility()"
                    class="mt-3 px-4 py-1.5 text-xs font-bold text-primary bg-primary/10 rounded-xl active:scale-95">
                    مسح البحث
                </button>
            </div>

            @forelse($trips as $trip)
                @php
                    $driverName = $trip->driver->name ?? 'سائق غير معين';
                    $driverPhone = $trip->driver->phone ?? '';
                    $dayName = $trip->created_at->locale('ar')->translatedFormat('l');
                    $tripOfficeComm = $trip->passengers->sum('office_commission');
                    $tripOtherComm = $trip->passengers->sum('other_office_commission');
                @endphp
                {{-- بطاقة كرت الرحلة الواحدة مع تهيئة النطاق المستقل للقائمة المنسدلة والفلترة --}}
                <div x-data="{ openMenu: false }"
                    x-show="showRow(@js($driverName), @js($driverPhone), @js((string) $trip->id), @js($dayName))"
                    class="trip-mobile-card w-full bg-white dark:bg-boxdark rounded-[2rem] shadow-sm p-5 border border-gray-100 dark:border-boxdark-2 relative transition-all hover:shadow-md">

                    {{-- خانة الرحلة والتاريخ واليوم --}}
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-100 dark:border-boxdark-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 text-xs font-black rounded-xl bg-primary/10 text-primary font-headline">
                                رحلة #{{ $trip->id }}
                            </span>
                            <span class="text-xs font-black text-slate-700 dark:text-gray-200">
                                {{ $dayName }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1 text-[11px] font-bold text-gray-400 font-mono">
                            <span class="material-symbols-outlined text-[14px]">schedule</span>
                            <span>{{ $trip->created_at->format('Y-m-d h:i A') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-start mb-4">
                        {{-- معلومات السائق كعنوان رئيسي للكرت --}}
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center border border-emerald-100 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                                <span class="material-symbols-outlined text-[26px]">local_taxi</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h3 class="text-sm font-black text-slate-800 dark:text-white">
                                        {{ $driverName }}
                                    </h3>
                                    @if ($trip->driver?->vehicle_type)
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-700/50">
                                            {{ $trip->driver->vehicle_type }}
                                        </span>
                                    @endif
                                </div>
                                @if ($driverPhone)
                                    <span class="text-xs text-gray-400 font-mono block text-right mt-0.5"
                                        style="direction: ltr;"> <x-phone-number :value="$driverPhone"
                                            class="text-xs text-primary !justify-start" /></span>
                                @else
                                    <span class="text-xs text-amber-500 font-medium block mt-0.5">يرجى تعيين سائق
                                        للرحلة</span>
                                @endif
                            </div>
                        </div>

                        {{-- ================= القائمة المنسدلة الثلاث نقاط الذكية ================= --}}
                        <div class="relative">
                            <button @click="openMenu = !openMenu" @click.away="openMenu = false" type="button"
                                class="flex justify-center items-center w-8 h-8 text-slate-400 hover:text-slate-600 dark:hover:text-white bg-slate-50 dark:bg-boxdark-2 rounded-full transition-all active:scale-90">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>

                            {{-- صندوق الخيارات --}}
                            <div x-show="openMenu" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 mt-2 w-44 bg-white dark:bg-boxdark border border-slate-100 dark:border-boxdark-2 rounded-2xl shadow-xl z-30 overflow-hidden py-1">

                                {{-- خيار عرض التفاصيل --}}
                                <a href="{{ route('trips.show', $trip->id) }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-blue-500">visibility</span>
                                    عرض التفاصيل
                                </a>

                                {{-- خيار تعديل الرحلة --}}
                                <a href="{{ route('trips.edit', $trip->id) }}"
                                    class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors">
                                    <span class="material-symbols-outlined text-[16px] text-amber-500">edit</span>
                                    تعديل الرحلة
                                </a>

                                {{-- خيار طباعة السند --}}
                                <a href="{{ route('receipt.generate', ['type' => 'trip', 'id' => $trip->id]) }}"
                                    target="_blank"
                                    class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-gray-300 hover:bg-slate-50 dark:hover:bg-boxdark-2 transition-colors border-t border-slate-50 dark:border-boxdark-2">
                                    <span class="material-symbols-outlined text-[16px] text-emerald-500">print</span>
                                    طباعة الكشف / السند
                                </a>

                                <a href="{{ $trip->driver_pdf_link }}" target="_blank"
                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-emerald-600 transition-colors hover:bg-emerald-50 dark:hover:bg-emerald-500/10">
                                     <span class="material-symbols-outlined text-[18px]">send</span>
                                    إرسال للسائق
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-50 dark:border-boxdark-2 my-3">

                    {{-- الإحصائيات السريعة للرحلة --}}
                    <div class="grid grid-cols-2 gap-3">

                        <div
                            class="p-3 rounded-2xl bg-slate-50 dark:bg-boxdark-2/60 border border-slate-100/50 dark:border-boxdark-2 flex flex-col items-center justify-center">
                            <span class="text-xs text-gray-400 font-bold mb-1">إجمالي الركاب</span>
                            <span class="text-base font-black text-slate-700 dark:text-gray-300 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px] text-gray-400">groups</span>
                                <span>{{ $trip->passengers->sum('count') }}</span>
                            </span>
                        </div>

                        <div
                            class="p-3 rounded-2xl bg-slate-50 dark:bg-boxdark-2/60 border border-slate-100/50 dark:border-boxdark-2 flex flex-col items-center justify-center">
                            <span class="text-xs text-gray-400 font-bold mb-1">عمولة الركاب</span>
                            <span class="text-base font-black text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <span class="font-sans font-black">{{ number_format($tripOfficeComm, 0) }}</span>
                                <span class="text-[10px] font-bold">ر.ي</span>
                            </span>
                            @if ($tripOtherComm > 0)
                                <span class="text-[9px] font-bold text-amber-600 dark:text-amber-400 mt-0.5">
                                    أخرى: {{ number_format($tripOtherComm, 0) }} ر.ي
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                {{-- حالة عدم وجود رحلات مضافة --}}
                <div
                    class="w-full bg-white dark:bg-boxdark rounded-[2.5rem] p-12 border border-gray-100 dark:border-boxdark-2 text-center flex flex-col items-center justify-center">
                    <div
                        class="w-16 h-16 rounded-full bg-slate-100 dark:bg-boxdark-2 flex items-center justify-center text-gray-400 mb-3">
                        <span class="material-symbols-outlined text-[32px]">explore_off</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-700 dark:text-white">لا توجد رحلات مضافة حالياً</h3>
                    <p class="text-xs text-gray-400 mt-1 max-w-[200px] mx-auto leading-relaxed">قم بإنشاء أول رحلة الآن
                        لتبدأ بتوزيع الركاب بداخلها.</p>
                    <a href="{{ route('trips.create') }}"
                        class="mt-4 px-6 h-10 bg-primary text-white text-xs font-black rounded-xl shadow-md shadow-primary/20 flex items-center gap-1 transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">add</span> إنشاء أول رحلة
                    </a>
                </div>
            @endforelse

            {{-- الترقيم وصفحات التنقل (Pagination) --}}
            <div class="pt-4 custom-pagination">
                {{ $trips->links() }}
            </div>

        </div>
    </div>

@endsection

@section('script')
    <script>
        function tripsDataMobile() {
            return {
                searchQuery: '',
                visibleCount: {{ $trips->count() }},

                init() {
                    this.updateVisibility();
                },

                showRow(driverName, driverPhone, tripId = '', dayName = '') {
                    const query = this.searchQuery.toLowerCase().trim();
                    if (!query) return true;

                    const cleanQuery = query.replace(/^(\+967|967|00967|0)/, '');
                    const check = (str) => {
                        if (!str) return false;
                        const cleanStr = String(str).toLowerCase();
                        return cleanStr.includes(query) || (cleanQuery !== '' && cleanStr.includes(cleanQuery));
                    };

                    return check(driverName) || check(driverPhone) || check(tripId) || check(dayName);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.trip-mobile-card:not([style*="display: none"])').length;
                    });
                }
            }
        }
    </script>
@endsection
