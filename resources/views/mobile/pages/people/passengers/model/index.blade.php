@extends('mobile.layouts.app')

@section('title', 'إدارة الركاب')

@section('content')

    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        searchQuery: '',
    
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
        deletePassengerData: { id: '', passenger_number: '', url: '' },
    
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
            const days = ['الاحد', 'الاثنين', 'الثلاثاء', 'الاربعاء', 'الخميس', 'الجمعة', 'السبت'];
            const dateObj = new Date(dateString);
            return isNaN(dateObj.getTime()) ? '' : days[dateObj.getDay()];
        }
    }"
        class="flex relative flex-col gap-6 p-4 pb-28 mx-auto max-w-7xl min-h-screen md:p-6 bg-surface dark:bg-boxdark-2 font-body"
        dir="rtl">

        {{-- ================= Header Section ================= --}}
        <div class="flex justify-between items-center mb-2">
            <div>
                <h1 class="text-2xl font-black tracking-tight md:text-3xl font-headline text-on-surface dark:text-white">
                    الركاب</h1>
                <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-bodydark">
                    إجمالي <span class="font-black text-primary">{{ $passengers->total() }}</span> راكب مسجل
                </p>
            </div>

            {{-- Mobile Top Button (Matches the image) --}}
            <button type="button" @click="showCreateModal = true"
                class="flex justify-center items-center w-14 h-14 text-white rounded-[1.2rem] shadow-lg transition-all bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-95 lg:hidden shrink-0">
                <span class="text-[28px] material-symbols-outlined"
                    style="font-variation-settings: 'FILL' 1;">person_add</span>
            </button>

            {{-- Desktop Button --}}
            <button type="button" @click="showCreateModal = true"
                class="hidden gap-2 justify-center items-center px-5 h-12 text-sm font-bold text-white rounded-2xl shadow-lg transition-all lg:flex bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-95">
                <span class="text-[22px] material-symbols-outlined"
                    style="font-variation-settings: 'FILL' 1;">group_add</span>
                <span>إضافة راكب جديد</span>
            </button>
        </div>

        {{-- ================= Search & Data Section ================= --}}
        <div
            class="bg-transparent lg:bg-white dark:lg:bg-boxdark lg:rounded-[2rem] lg:border border-gray-100 dark:border-boxdark-2 lg:shadow-sm overflow-hidden transition-colors">

            {{-- Search Bar --}}
            <div
                class="mb-4 border-gray-100 lg:mb-0 lg:p-5 lg:bg-white lg:border-b dark:border-boxdark-2 dark:lg:bg-boxdark">
                <div
                    class="relative w-full rounded-[1.2rem] border border-gray-200 transition-all md:w-96 dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-white dark:bg-boxdark lg:bg-surface dark:lg:bg-boxdark-2 shadow-sm lg:shadow-none">
                    <input type="text" x-model="searchQuery" placeholder="ابحث برقم الراكب، المكان..."
                        class="pr-12 pl-12 w-full h-14 lg:h-12 text-sm font-bold placeholder-gray-400 bg-transparent rounded-[1.2rem] border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                    <span
                        class="absolute right-4 top-1/2 text-gray-400 transition-colors -translate-y-1/2 material-symbols-outlined group-focus-within:text-primary">search</span>

                    <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                        class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-gray-100 rounded-xl transition-all -translate-y-1/2 dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="text-[18px] material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            {{-- ===== Mobile View (Cards Matching The Custom Design) ===== --}}
            <div class="flex flex-col gap-4 lg:hidden">
                @forelse ($passengers as $passenger)
                    <div x-show="searchQuery === '' || '{{ $passenger->passenger_number }}'.includes(searchQuery) || '{{ $passenger->location }}'.includes(searchQuery)"
                        x-transition
                        class="p-4 bg-white rounded-[1.5rem] shadow-[0_2px_10px_rgba(0,0,0,0.02)] border border-gray-50 transition-all dark:bg-boxdark dark:border-boxdark-2 relative">

                        <div class="flex justify-between items-start">
                            {{-- Right Section: Avatar & Info --}}
                            <div class="flex gap-3">
                                {{-- Avatar Box (First 2 chars of passenger number) --}}
                                <div
                                    class="flex justify-center items-center w-14 h-14 text-lg font-black rounded-[1rem] bg-[#FFF4EA] text-primary dark:bg-primary/10 shrink-0 font-headline uppercase">
                                    {{ mb_substr($passenger->passenger_number, 0, 2) }}
                                </div>

                                {{-- Name & Details --}}
                                <div class="flex flex-col gap-1 pt-1">
                                    <h3 class="text-sm font-black text-on-surface dark:text-white font-headline">
                                        {{ $passenger->passenger_number }}</h3>
                                    <div
                                        class="flex gap-1.5 items-center text-xs font-bold text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                                        <span>{{ $passenger->location }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Left Section: Menu & Time --}}
                            <div class="flex gap-2 items-center pt-1">
                                <span
                                    class="text-[11px] font-bold text-gray-400 dark:text-gray-500">{{ $passenger->date }}</span>

                                {{-- 3 Dots Dropdown Menu --}}
                                <div x-data="{ menuOpen: false }" class="relative">
                                    <button type="button" @click="menuOpen = !menuOpen" @click.outside="menuOpen = false"
                                        class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-full transition-colors hover:text-gray-600 dark:hover:text-gray-300 active:bg-gray-50 dark:active:bg-boxdark-2">
                                        <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                    </button>

                                    {{-- Menu Box --}}
                                    <div x-show="menuOpen" x-transition x-cloak
                                        class="absolute left-0 top-full mt-1 w-36 bg-white rounded-xl shadow-[0_5px_15px_rgba(0,0,0,0.08)] border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark z-[99999] py-1 overflow-hidden">
                                        <a href="{{ route('passengers.show', $passenger->id) }}"
                                            class="flex gap-2 items-center px-4 py-2.5 text-sm font-bold text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-boxdark">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-gray-400">visibility</span>
                                            عرض السجل
                                        </a>
                                        <button type="button"
                                            @click="openEditModal({{ $passenger->id }}, {{ json_encode($passenger->date) }}, {{ json_encode($passenger->day) }}, {{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->location) }}, {{ $passenger->count ?? 'null' }}, {{ $passenger->total_commission ?? 'null' }}, {{ json_encode($passenger->broker) }}, {{ json_encode($passenger->driver_id) }}, {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($passenger->note) }}); menuOpen = false"
                                            class="flex gap-2 items-center px-4 py-2.5 w-full text-sm font-bold text-right text-gray-700 transition-colors hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-boxdark">
                                            <span class="material-symbols-outlined text-[18px] text-gray-400">edit</span>
                                            تعديل
                                        </button>
                                        <div class="mx-2 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>
                                        <button type="button"
                                            @click="openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }}); menuOpen = false"
                                            class="flex gap-2 items-center px-4 py-2.5 w-full text-sm font-bold text-right transition-colors text-error hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                            حذف
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom Stats Section (Divided Columns) --}}
                        <div
                            class="flex justify-between items-center p-3 mt-4 bg-gray-50 rounded-xl divide-x divide-x-reverse divide-gray-200 dark:bg-boxdark-2 dark:divide-gray-700">
                            {{-- Col 1: Count --}}
                            <div class="flex flex-col flex-1 gap-1 items-center">
                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">العدد</span>
                                <span
                                    class="text-sm font-black text-gray-800 dark:text-white font-headline">{{ $passenger->count }}</span>
                            </div>

                            {{-- Col 2: Commission --}}
                            <div class="flex flex-col flex-1 gap-1 items-center">
                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">العمولة</span>
                                <span
                                    class="text-sm font-black text-primary font-headline">{{ $passenger->total_commission }}</span>
                            </div>

                            {{-- Col 3: Driver/Broker --}}
                            <div class="flex flex-col flex-1 gap-1 items-center">
                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500">السائق</span>
                                <span
                                    class="text-xs font-black text-gray-800 dark:text-white font-headline truncate max-w-[80px] text-center"
                                    title="{{ $passenger->driver->name ?? 'غير محدد' }}">
                                    {{ $passenger->driver->name ?? 'غير محدد' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col justify-center items-center py-16 text-center bg-white rounded-2xl border-2 border-gray-100 border-dashed dark:bg-boxdark dark:border-boxdark-2">
                        <span
                            class="text-[48px] material-symbols-outlined text-gray-300 dark:text-gray-600 mb-4">group_off</span>
                        <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لم نعثر على أي ركاب مسجلين</p>
                    </div>
                @endforelse

                <div x-show="searchQuery !== '' && !Array.from(document.querySelectorAll('.space-y-4 > div[x-show]')).some(el => el.style.display !== 'none')"
                    style="display: none;"
                    class="flex flex-col justify-center items-center py-16 text-center bg-white rounded-2xl border-2 border-gray-100 border-dashed dark:bg-boxdark dark:border-boxdark-2">
                    <span
                        class="text-[48px] material-symbols-outlined text-gray-300 dark:text-gray-600 mb-4">search_off</span>
                    <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا يوجد نتائج تطابق بحثك</p>
                </div>
            </div>

            {{-- ===== Desktop View (Data Table) ===== --}}
            <div class="hidden overflow-x-auto px-6 pb-6 mt-4 lg:block">
                {{-- (يظل كود الجدول الخاص بالديسكتوب كما هو بدون تغيير) --}}
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr
                            class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] dark:text-bodydark bg-surface dark:bg-boxdark-2 border-b border-gray-100 dark:border-boxdark">
                            <th class="px-6 py-5">التاريخ واليوم</th>
                            <th class="px-6 py-5">رقم الراكب</th>
                            <th class="px-6 py-5">المكان</th>
                            <th class="px-6 py-5">العدد وإجمالي العمولة</th>
                            <th class="px-6 py-5">الوسيط والسائق</th>
                            <th class="px-6 py-5 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @foreach ($passengers as $passenger)
                            <tr x-show="searchQuery === '' || '{{ $passenger->passenger_number }}'.includes(searchQuery) || '{{ $passenger->location }}'.includes(searchQuery) || '{{ $passenger->broker }}'.includes(searchQuery)"
                                x-transition
                                class="rounded-2xl border border-transparent shadow-sm transition-all bg-surface dark:bg-boxdark-2 hover:shadow-md hover:border-gray-200 dark:hover:border-boxdark group">

                                <td
                                    class="px-6 py-4 border-r border-gray-50 border-y dark:border-boxdark-2 first:rounded-r-2xl">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="text-sm font-black text-on-surface dark:text-white font-headline">{{ $passenger->date }}</span>
                                        <span class="text-xs text-gray-500 dark:text-bodydark">{{ $passenger->day }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <span
                                        class="font-mono text-sm font-bold tracking-wider text-gray-600 dark:text-gray-300">{{ $passenger->passenger_number }}</span>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <span
                                        class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ $passenger->location }}</span>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">العدد: <span
                                                class="font-bold">{{ $passenger->count }}</span></span>
                                        <span class="text-xs text-gray-500 dark:text-bodydark">العمولة: <span
                                                class="font-bold">{{ $passenger->total_commission }}</span></span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">الوسيط: <span
                                                class="font-bold">{{ $passenger->broker ?: 'لا يوجد' }}</span></span>
                                        <span class="text-xs font-bold text-primary">السائق:
                                            {{ $passenger->driver->name ?? 'غير محدد' }}</span>
                                    </div>
                                </td>

                                <td
                                    class="px-6 py-4 text-center border-l border-gray-50 border-y dark:border-boxdark-2 last:rounded-l-2xl">
                                    <div class="flex gap-2 justify-center items-center">
                                        <button
                                            @click="openEditModal({{ $passenger->id }}, {{ json_encode($passenger->date) }}, {{ json_encode($passenger->day) }}, {{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->location) }}, {{ $passenger->count ?? 'null' }}, {{ $passenger->total_commission ?? 'null' }}, {{ json_encode($passenger->broker) }}, {{ json_encode($passenger->driver_id) }}, {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($passenger->note) }})"
                                            title="تعديل"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-primary-container hover:text-primary hover:border-primary/20 dark:hover:bg-primary/10 dark:hover:text-primary active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <a href="{{ route('passengers.show', $passenger->id) }}" title="عرض التفاصيل"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-indigo-50 hover:text-indigo-500 hover:border-indigo-200 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-400 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($passengers->hasPages())
                <div
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 lg:rounded-b-[2rem]">
                    {{ $passengers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- Floating Action Button (FAB) Bottom Left Mobile --}}
        <button type="button" @click="showCreateModal = true"
            class="flex fixed left-6 bottom-20 z-40 justify-center items-center w-14 h-14 text-white rounded-[1.2rem] shadow-[0_8px_20px_rgba(220,104,3,0.35)] transition-transform bg-primary hover:scale-105 active:scale-95 lg:hidden">
            <span class="text-[32px] font-bold material-symbols-outlined">add</span>
        </button>


        {{-- ======================== Responsive Modals (Bottom Sheets) ======================== --}}
        {{-- (أكواد النوافذ المنبثقة showCreateModal, showEditModal, showDeleteModal تظل كما كتبناها في الرد السابق تماماً دون تغيير) --}}
        {{-- سأرفق نافذة الإضافة كمثال هنا لتكتمل البنية --}}

        {{-- 1. Create Passenger Modal --}}
        <div x-show="showCreateModal" x-cloak
            class="fixed inset-0 z-[99999] flex justify-center items-end md:items-center pointer-events-none md:p-6">

            <div x-show="showCreateModal" x-transition.opacity.duration.300ms
                class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div x-show="showCreateModal" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="translate-y-full md:translate-y-8 md:opacity-0 md:scale-95"
                x-transition:enter-end="translate-y-0 md:opacity-100 md:scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-y-0 md:opacity-100 md:scale-100"
                x-transition:leave-end="translate-y-full md:translate-y-8 md:opacity-0 md:scale-95"
                class="relative w-full max-w-2xl bg-white dark:bg-boxdark rounded-t-[2rem] md:rounded-[2rem] shadow-2xl border-t md:border border-gray-100 dark:border-boxdark-2 pointer-events-auto flex flex-col max-h-[95vh] md:max-h-[90vh]">

                <div class="flex justify-center pt-3 pb-1 w-full md:hidden" @click="closeModals()">
                    <div class="w-12 h-1.5 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                </div>

                <div class="overflow-y-auto flex-1 p-6 md:p-8 custom-scrollbar">
                    <div class="flex justify-between items-center pb-4 mb-8 border-b border-gray-50 dark:border-boxdark-2">
                        <h3
                            class="flex gap-2 items-center text-xl font-black font-headline text-on-surface dark:text-white">
                            <div
                                class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary-container dark:bg-primary/10 text-primary">
                                <span class="material-symbols-outlined text-[20px]">person_add</span>
                            </div>
                            إضافة راكب جديد
                        </h3>
                        <button type="button" @click="closeModals()"
                            class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <form action="{{ route('passengers.store') }}" method="POST" class="space-y-6">
                        @csrf
                        {{-- محتوى الفورم كما هو في الرد السابق --}}
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">التاريخ <span
                                        class="text-error">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">calendar_today</span>
                                    <input type="date" name="date" required x-model="createPassengerData.date"
                                        @input="createPassengerData.day = getArabicDayName(createPassengerData.date)"
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اليوم <span
                                        class="text-error">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">today</span>
                                    <select
                                        class="pr-12 pl-4 w-full h-14 text-sm bg-gray-50 rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none pointer-events-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40"
                                        name="day" id="day" required x-model="createPassengerData.day"
                                        readonly>
                                        <option value="">(يتم تحديده تلقائياً)</option>
                                        <option value="السبت">السبت</option>
                                        <option value="الاحد">الاحد</option>
                                        <option value="الاثنين">الاثنين</option>
                                        <option value="الثلاثاء">الثلاثاء</option>
                                        <option value="الاربعاء">الاربعاء</option>
                                        <option value="الخميس">الخميس</option>
                                        <option value="الجمعة">الجمعة</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم الراكب
                                    <span class="text-error">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">tag</span>
                                    <input type="text" name="passenger_number" required placeholder="رقم الراكب"
                                        x-model="createPassengerData.passenger_number"
                                        class="pr-12 pl-4 w-full h-14 font-mono text-sm font-bold text-left rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40"
                                        dir="ltr">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">المكان <span
                                        class="text-error">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">location_on</span>
                                    <input type="text" name="location" required placeholder="مكان التواجد"
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">العدد <span
                                        class="text-error">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">group</span>
                                    <input type="number" name="count" required placeholder="1"
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">إجمالي العمولة
                                    <span class="text-error">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">payments</span>
                                    <input type="number" step="0.01" name="total_commission" required
                                        placeholder="0.00"
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="flex gap-2 justify-center items-center mt-8 w-full h-14 font-black text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95">
                            <span class="material-symbols-outlined">save</span>
                            حفظ الراكب
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- يمكنك استدعاء باقي النوافذ المنبثقة (Edit, Delete) هنا بنفس الطريقة تماماً كالسابق --}}

    </div>
@endsection
