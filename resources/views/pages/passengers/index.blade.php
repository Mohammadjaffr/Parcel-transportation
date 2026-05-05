@extends('layouts.app')

@section('title', 'إدارة الركاب')
@section('Breadcrumb', 'إدارة الركاب')

@section('addButton')
    <button x-data @click="$dispatch('open-create-passenger-modal')"
        class="inline-flex gap-2 items-center px-4 py-2 text-sm font-semibold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95">
        <span class="material-symbols-outlined text-[20px]">add</span>
        إضافة راكب جديد
    </button>
@endsection

@section('content')

    <div x-data="passengerRegistry()"
         @open-create-passenger-modal.window="openCreateModal()"
         class="pb-24 space-y-6 min-h-screen font-body lg:pb-12"
         dir="rtl">

        {{-- ====================== Stats Cards ====================== --}}
        <div class="grid grid-cols-1 gap-2 mx-auto max-w-7xl xl:grid-cols-3 md:gap-6">

            {{-- إجمالي الركاب --}}
            <div @click="filterStatus = 'all'; updateVisibility()"
                :class="filterStatus === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-boxdark-2'"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md">

                <div class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]">groups</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-gray-500 uppercase dark:text-bodydark">
                        إجمالي الركاب
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ $passengers->total() }}
                    </h4>
                </div>
            </div>

            {{-- إجمالي العدد --}}
            <div @click="filterStatus = 'all'; updateVisibility()"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md border-r-emerald-500 dark:border-r-emerald-500 hover:border-emerald-300 dark:border-boxdark-2">

                <div class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl dark:bg-emerald-500/10">
                    <span class="material-symbols-outlined text-[24px]">group_add</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-emerald-500 uppercase">
                        إجمالي العدد
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($passengers->getCollection()->sum('count'), 0) }}
                    </h4>
                </div>
            </div>

            {{-- إجمالي العمولة --}}
            <div @click="filterStatus = 'all'; updateVisibility()"
                class="flex relative flex-col justify-between items-start p-5 bg-white rounded-2xl border border-r-4 border-gray-100 shadow-sm transition-all cursor-pointer dark:bg-boxdark hover:shadow-md border-r-amber-500 dark:border-r-amber-500 hover:border-amber-300 dark:border-boxdark-2">

                <div class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10">
                    <span class="material-symbols-outlined text-[24px]">payments</span>
                </div>

                <div class="mt-4">
                    <span class="text-xs font-bold tracking-widest text-amber-500 uppercase">
                        إجمالي العمولة
                    </span>

                    <h4 class="mt-1 text-2xl font-black text-on-surface dark:text-white">
                        {{ number_format($passengers->getCollection()->sum('total_commission'), 0) }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- ====================== Search & Table Section ====================== --}}
        <div class="bg-white dark:bg-boxdark my-4 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-visible transition-colors max-w-7xl mx-auto">

            {{-- Search --}}
            <div class="p-5 w-full border-b border-gray-100 md:p-6 dark:border-boxdark-2">
                <div class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-96 dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">

                    <input type="text"
                        x-model="searchQuery"
                        @input.debounce.300ms="updateVisibility()"
                        placeholder="ابحث برقم الراكب، المكان، الوسيط، أو السائق..."
                        class="pr-12 pl-12 w-full h-12 text-sm font-medium placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>

                    <button type="button"
                        x-show="searchQuery.length > 0"
                        @click="searchQuery = ''; updateVisibility()"
                        x-cloak
                        class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-gray-100 rounded-xl transition-all -translate-y-1/2 dark:bg-boxdark hover:text-error active:scale-95">
                        <span class="text-[18px] material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            {{-- ====================== Mobile View ====================== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse ($passengers as $passenger)
                    <div class="flex flex-col gap-4 p-5 rounded-2xl border border-gray-100 transition-all passenger-row bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30 hover:shadow-sm"
                        x-show="showRow(
                            '{{ $passenger->passenger_number }}',
                            '{{ $passenger->location }}',
                            '{{ $passenger->broker }}',
                            '{{ $passenger->driver->name ?? '' }}'
                        )">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center min-w-0">
                                <div class="flex justify-center items-center w-12 h-12 text-lg font-black text-white rounded-xl shadow-inner bg-primary shrink-0">
                                    <span class="material-symbols-outlined text-[22px]">person</span>
                                </div>

                                <div class="flex flex-col gap-1 min-w-0">
                                    <span class="text-sm font-black truncate text-on-surface dark:text-white font-headline">
                                        {{ $passenger->passenger_number }}
                                    </span>

                                    <div class="flex gap-1.5 items-center text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                        <span>{{ $passenger->date }} - {{ $passenger->day }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile Actions --}}
                            <div x-data="{ menuOpen: false }" class="relative shrink-0">
                                <button @click="menuOpen = !menuOpen"
                                    @click.away="menuOpen = false"
                                    class="p-2 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="menuOpen"
                                    x-transition
                                    x-cloak
                                    class="absolute left-0 top-full z-[999] py-1.5 mt-2 w-52 rounded-2xl border border-gray-100 shadow-lg backdrop-blur-md bg-white/95 dark:bg-boxdark-2/95 dark:border-boxdark overflow-hidden">

                                    <a href="{{ route('passengers.show', $passenger->id) }}"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark dark:hover:text-blue-400">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        عرض التفاصيل
                                    </a>

                                    <button type="button"
                                        @click="menuOpen = false; openEditModal({{ $passenger->id }}, {{ json_encode($passenger->date) }}, {{ json_encode($passenger->day) }}, {{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->location) }}, {{ $passenger->count ?? 'null' }}, {{ $passenger->total_commission ?? 'null' }}, {{ json_encode($passenger->broker) }}, {{ json_encode($passenger->driver_id) }}, {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($passenger->note) }})"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                        تعديل البيانات
                                    </button>

                                    <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                    <button type="button"
                                        @click="menuOpen = false; openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                                        class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                        حذف الراكب
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">المكان</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $passenger->location ?: 'غير محدد' }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">السائق</span>
                                <span class="text-xs font-bold text-primary">
                                    {{ $passenger->driver->name ?? 'غير محدد' }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">العدد</span>
                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ $passenger->count ?? 0 }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-gray-400 dark:text-gray-500">العمولة</span>
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400">
                                    {{ number_format($passenger->total_commission ?? 0, 0) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-boxdark">
                            <span class="px-2.5 py-1 rounded-lg bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 shadow-sm text-gray-500 dark:text-gray-300 text-[10px] font-black uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px] text-primary">handshake</span>
                                {{ $passenger->broker ?: 'لا يوجد وسيط' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col gap-3 items-center py-16 text-center text-gray-400 rounded-2xl border-2 border-gray-100 border-dashed dark:text-bodydark dark:border-boxdark-2 bg-surface dark:bg-boxdark-2">
                        <span class="material-symbols-outlined text-[40px] opacity-30">group_off</span>
                        <p class="text-sm font-bold">لا توجد بيانات ركاب مطابقة..</p>
                    </div>
                @endforelse

                <div x-show="visibleCount === 0 && {{ $passengers->count() }} > 0"
                    x-cloak
                    class="py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <div class="flex flex-col justify-center items-center">
                        <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                        <h4 class="text-sm font-black text-on-surface dark:text-white font-headline">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs font-bold text-gray-500 dark:text-bodydark">
                            لا توجد نتائج تطابق بحثك في هذه الصفحة.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ====================== Desktop View ====================== --}}
            <div class="hidden overflow-visible w-full lg:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-500 uppercase tracking-[0.1em] bg-gray-50/80 dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark-2">
                            <th class="px-6 py-4">التاريخ</th>
                            <th class="px-6 py-4">رقم الراكب</th>
                            <th class="px-6 py-4 text-center">المكان</th>
                            <th class="px-6 py-4 text-center">العدد والعمولة</th>
                            <th class="px-6 py-4 text-center">السائق والوسيط</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-boxdark-2">
                        @forelse ($passengers as $passenger)
                            <tr class="transition-colors hover:bg-gray-50/80 dark:hover:bg-boxdark-2/50 group passenger-row"
                                x-show="showRow(
                                    '{{ $passenger->passenger_number }}',
                                    '{{ $passenger->location }}',
                                    '{{ $passenger->broker }}',
                                    '{{ $passenger->driver->name ?? '' }}'
                                )">

                                {{-- التاريخ --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-black text-gray-800 dark:text-white">
                                            {{ $passenger->date }}
                                        </span>
                                        <span class="text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                            {{ $passenger->day }}
                                        </span>
                                    </div>
                                </td>

                                {{-- رقم الراكب --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-4 items-center">
                                        <div class="flex justify-center items-center w-11 h-11 text-white rounded-lg shadow-inner bg-primary">
                                            <span class="material-symbols-outlined text-[20px]">person</span>
                                        </div>

                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-black text-gray-800 dark:text-white">
                                                {{ $passenger->passenger_number }}
                                            </span>
                                            <span class="text-[11px] font-bold text-gray-500 dark:text-bodydark">
                                                رقم الراكب
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- المكان --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                        {{ $passenger->location ?: 'غير محدد' }}
                                    </span>
                                </td>

                                {{-- العدد والعمولة --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="px-3 py-1.5 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                            العدد: {{ $passenger->count ?? 0 }}
                                        </span>

                                        <span class="text-xs font-black text-amber-600 dark:text-amber-400">
                                            {{ number_format($passenger->total_commission ?? 0, 0) }}
                                        </span>
                                    </div>
                                </td>

                                {{-- السائق والوسيط --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-1 items-center">
                                        <span class="text-xs font-black text-primary">
                                            {{ $passenger->driver->name ?? 'غير محدد' }}
                                        </span>

                                        <span class="px-2.5 py-1 text-[10px] font-bold text-gray-500 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-boxdark dark:text-gray-300 dark:border-boxdark-2">
                                            {{ $passenger->broker ?: 'لا يوجد وسيط' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- الإجراءات --}}
                                <td class="relative px-6 py-4 text-center">
                                    <div x-data="{ open: false }"
                                         class="inline-block relative text-right"
                                         @click.away="open = false">

                                        <button @click="open = !open"
                                            type="button"
                                            title="خيارات"
                                            class="inline-flex justify-center items-center w-9 h-9 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-all hover:bg-gray-100 hover:text-gray-600 hover:border-gray-200 dark:bg-boxdark dark:border-boxdark-2 dark:hover:bg-boxdark-2 dark:hover:text-gray-300 active:scale-95">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        <div x-show="open"
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute left-0 top-full mt-2 z-[999] w-52 bg-white/95 backdrop-blur-md rounded-xl border border-gray-100 shadow-xl dark:bg-boxdark/95 dark:border-boxdark-2 focus:outline-none origin-top-left overflow-hidden"
                                            style="display: none;">

                                            <div class="py-1" role="menu">
                                                <a href="{{ route('passengers.show', $passenger->id) }}"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-boxdark-2 dark:hover:text-blue-400">
                                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                                    عرض التفاصيل
                                                </a>

                                                <button type="button"
                                                    @click="open = false; openEditModal({{ $passenger->id }}, {{ json_encode($passenger->date) }}, {{ json_encode($passenger->day) }}, {{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->location) }}, {{ $passenger->count ?? 'null' }}, {{ $passenger->total_commission ?? 'null' }}, {{ json_encode($passenger->broker) }}, {{ json_encode($passenger->driver_id) }}, {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($passenger->note) }})"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-gray-700 transition-colors dark:text-gray-200 hover:bg-primary/10 hover:text-primary dark:hover:bg-boxdark-2 dark:hover:text-primary">
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                    تعديل البيانات
                                                </button>

                                                <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                                <button type="button"
                                                    @click="open = false; openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                                                    class="flex gap-3 items-center px-4 py-2.5 w-full text-xs font-bold text-rose-600 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    حذف الراكب
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col gap-4 justify-center items-center">
                                        <div class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                            <span class="material-symbols-outlined text-[28px] text-gray-400">group_off</span>
                                        </div>

                                        <div>
                                            <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                                لا توجد بيانات للركاب
                                            </h3>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                                لم نعثر على أي ركاب مسجلين في النظام حالياً.
                                            </p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr x-show="visibleCount === 0 && {{ $passengers->count() }} > 0" x-cloak>
                            <td colspan="6" class="py-24 text-center">
                                <div class="flex flex-col gap-4 justify-center items-center">
                                    <div class="flex justify-center items-center w-16 h-16 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                                        <span class="material-symbols-outlined text-[28px] text-gray-400">search_off</span>
                                    </div>

                                    <div>
                                        <h3 class="mb-1 text-base font-bold text-gray-800 dark:text-white">
                                            لا توجد نتائج مطابقة
                                        </h3>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            لم نعثر على ركاب يطابقون كلمة البحث المدخلة.
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($passengers->hasPages())
                <div class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-gray-50/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $passengers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- ======================== Modals ======================== --}}
        {{-- اترك مودالات الإضافة والتعديل والحذف كما هي من كودك الحالي بدون تغيير --}}
        {{-- لأن التعديل هنا كان فقط لتوحيد عرض الجدول والكروت والإجراءات --}}
        
        {{-- ضع هنا نفس كود المودالات الموجود عندك:
            1. Create Passenger Modal
            2. Edit Passenger Modal
            3. Delete Confirmation Modal
        --}}

    </div>
@endsection

@section('script')
    <script>
        function passengerRegistry() {
            return {
                showCreateModal: false,
                showEditModal: false,
                showDeleteModal: false,
                searchQuery: '',
                filterStatus: 'all',
                visibleCount: {{ $passengers->count() }},

                createPassengerData: {
                    date: '',
                    day: '',
                    passenger_number: ''
                },

                editPassengerData: {
                    id: '',
                    date: '',
                    day: '',
                    passenger_number: '',
                    location: '',
                    count: '',
                    total_commission: '',
                    broker: '',
                    driver_id: '',
                    driver_name: '',
                    driver_phone: '',
                    note: '',
                    url: ''
                },

                deletePassengerData: {
                    id: '',
                    passenger_number: '',
                    url: ''
                },

                openCreateModal() {
                    this.createPassengerData = {
                        date: '',
                        day: '',
                        passenger_number: ''
                    };

                    this.showCreateModal = true;
                },

                openEditModal(id, date, day, passenger_number, location, count, total_commission, broker, driver_id, driver_name, driver_phone, note) {
                    this.editPassengerData = {
                        id: id,
                        date: date,
                        day: day,
                        passenger_number: passenger_number,
                        location: location,
                        count: count,
                        total_commission: total_commission,
                        broker: broker,
                        driver_id: driver_id,
                        driver_name: driver_name,
                        driver_phone: driver_phone,
                        note: note,
                        url: '{{ route('passengers.index') }}/' + id
                    };

                    this.showEditModal = true;
                },

                openDeleteModal(id, passenger_number) {
                    this.deletePassengerData = {
                        id: id,
                        passenger_number: passenger_number,
                        url: '{{ route('passengers.index') }}/' + id
                    };

                    this.showDeleteModal = true;
                },

                closeModals() {
                    this.showCreateModal = false;
                    this.showEditModal = false;
                    this.showDeleteModal = false;
                },

                getArabicDayName(dateString) {
                    if (!dateString) return '';

                    const days = [
                        'الاحد',
                        'الاثنين',
                        'الثلاثاء',
                        'الاربعاء',
                        'الخميس',
                        'الجمعة',
                        'السبت'
                    ];

                    const dateObj = new Date(dateString);

                    return isNaN(dateObj.getTime()) ? '' : days[dateObj.getDay()];
                },

                showRow(passengerNumber, location, broker, driverName) {
                    const query = this.searchQuery.toLowerCase().trim();

                    if (!query) {
                        return true;
                    }

                    return String(passengerNumber || '').toLowerCase().includes(query)
                        || String(location || '').toLowerCase().includes(query)
                        || String(broker || '').toLowerCase().includes(query)
                        || String(driverName || '').toLowerCase().includes(query);
                },

                updateVisibility() {
                    this.$nextTick(() => {
                        this.visibleCount = document.querySelectorAll('.passenger-row:not([style*="display: none"])').length;
                    });
                }
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('driverSelect', (driversList, countriesList, initialData) => ({
                drivers: driversList || [],
                countries: countriesList || [],
                filteredDrivers: [],
                localPhoneNumber: '',
                nameInput: '',
                selectedDriverId: null,
                selectedCountry: null,
                openCountryDropdown: false,
                searchCountryQuery: '',
                showDriverDropdown: false,

                init() {
                    if (initialData && initialData.phone) {
                        let phone = initialData.phone;

                        if (/^\d/.test(phone)) {
                            phone = '+' + phone;
                        }

                        this.selectedCountry = this.countries.find(c => phone.startsWith(c.dial_code))
                            || this.countries.find(c => c.code === 'YE')
                            || this.countries[0];

                        if (this.selectedCountry && phone.startsWith(this.selectedCountry.dial_code)) {
                            this.localPhoneNumber = phone.substring(this.selectedCountry.dial_code.length);
                        } else {
                            this.localPhoneNumber = initialData.phone;
                        }

                        this.selectedDriverId = initialData.id;
                        this.nameInput = initialData.name || '';
                    } else {
                        this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
                    }
                },

                get filteredCountries() {
                    if (this.searchCountryQuery === '') {
                        return this.countries;
                    }

                    const query = this.searchCountryQuery.toLowerCase();

                    return this.countries.filter(c =>
                        String(c.name || '').toLowerCase().includes(query)
                        || String(c.dial_code || '').includes(query)
                    );
                },

                get fullPhoneNumber() {
                    if (!this.localPhoneNumber) {
                        return '';
                    }

                    let dialCode = this.selectedCountry
                        ? String(this.selectedCountry.dial_code || '').replace('+', '')
                        : '';

                    return dialCode + this.localPhoneNumber;
                },

                searchDriver() {
                    this.selectedDriverId = null;

                    let query = this.fullPhoneNumber.trim();

                    if (this.localPhoneNumber.trim() === '') {
                        this.filteredDrivers = [];
                        this.showDriverDropdown = false;
                        return;
                    }

                    this.filteredDrivers = this.drivers.filter(driver => {
                        return driver.phone && String(driver.phone).includes(query);
                    });

                    this.showDriverDropdown = true;
                },

                selectDriver(driver) {
                    this.selectedDriverId = driver.id;

                    let dialCode = this.selectedCountry
                        ? String(this.selectedCountry.dial_code || '').replace('+', '')
                        : '';

                    if (driver.phone && String(driver.phone).startsWith(dialCode)) {
                        this.localPhoneNumber = String(driver.phone).substring(dialCode.length);
                    } else {
                        this.localPhoneNumber = driver.phone;
                    }

                    this.nameInput = driver.name;
                    this.showDriverDropdown = false;
                },

                resetSelection() {
                    this.selectedDriverId = null;
                    this.localPhoneNumber = '';
                    this.nameInput = '';
                    this.showDriverDropdown = false;
                }
            }));
        });
    </script>
@endsection