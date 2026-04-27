@extends('layouts.app')

@section('title', 'إدارة الركاب')

@section('content')

    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        searchQuery: '',
    
        editPassengerData: { 
            id: '', date: '', day: '', passenger_number: '', location: '', count: '', total_commission: '', broker: '', driver_id: '', driver_name: '', driver_phone: '', note: '', url: '' 
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
        }
    }"
        class="flex relative flex-col gap-6 p-4 pb-24 mx-auto max-w-7xl min-h-screen md:p-6 bg-surface dark:bg-boxdark-2 font-body"
        dir="rtl">

        {{-- ================= Header Section ================= --}}
        <div class="flex flex-col gap-4 justify-between items-start md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-black tracking-tight md:text-3xl font-headline text-on-surface dark:text-white">
                    الركاب</h1>
                <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-bodydark">
                    إجمالي <span class="font-black text-primary">{{ $passengers->total() }}</span> راكب مسجل
                </p>
            </div>

            <button type="button" @click="showCreateModal = true"
                class="flex gap-2 justify-center items-center px-5 w-full h-12 text-sm font-bold text-white rounded-2xl shadow-lg transition-all bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-95 md:w-auto">
                <span class="text-[22px] material-symbols-outlined"
                    style="font-variation-settings: 'FILL' 1;">group_add</span>
                <span class="hidden md:inline">إضافة راكب جديد</span>
            </button>
        </div>

        {{-- ================= Search & Data Section ================= --}}
        <div
            class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden transition-colors">

            {{-- Search Bar --}}
            <div class="p-5 bg-white border-b border-gray-100 dark:border-boxdark-2 dark:bg-boxdark">
                <div
                    class="relative w-full rounded-2xl border border-gray-200 transition-all md:w-96 dark:border-boxdark-2 group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">
                    <input type="text" x-model="searchQuery" placeholder="ابحث برقم الراكب، المكان، أو الوسيط..."
                        class="pr-12 pl-12 w-full h-12 text-sm font-medium placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">

                    <span
                        class="absolute right-4 top-1/2 text-gray-400 transition-colors -translate-y-1/2 material-symbols-outlined group-focus-within:text-primary">search</span>

                    <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                        class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-gray-100 rounded-xl transition-all -translate-y-1/2 dark:bg-boxdark hover:text-error active:scale-95">
                        <span class="text-[18px] material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse ($passengers as $passenger)
                    <div x-show="searchQuery === '' || '{{ $passenger->passenger_number }}'.includes(searchQuery) || '{{ $passenger->location }}'.includes(searchQuery) || '{{ $passenger->broker }}'.includes(searchQuery)"
                        x-transition
                        class="overflow-hidden relative p-5 rounded-2xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30">

                        <div class="flex justify-between items-start mb-4">
                            <div class="flex gap-3 items-center w-full">
                                <div class="flex justify-center items-center w-12 h-12 text-lg font-black rounded-xl bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                                <div class="flex flex-col w-full">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-sm font-black text-on-surface dark:text-white font-headline">{{ $passenger->passenger_number }}</h3>
                                        <span class="text-xs font-semibold text-gray-500 dark:text-bodydark">{{ $passenger->date }} - {{ $passenger->day }}</span>
                                    </div>
                                    <div class="flex gap-1.5 items-center mt-1 text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                                        <span class="text-xs font-bold">{{ $passenger->location }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                            <div class="flex flex-col p-2 rounded-lg border border-gray-50 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <span class="text-[10px] text-gray-400 dark:text-gray-500">العدد والعمولة</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $passenger->count }} | {{ $passenger->total_commission }}</span>
                            </div>
                            <div class="flex flex-col p-2 rounded-lg border border-gray-50 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <span class="text-[10px] text-gray-400 dark:text-gray-500">الوسيط</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">{{ $passenger->broker ?: 'لا يوجد' }}</span>
                            </div>
                            <div class="flex flex-col col-span-2 p-2 rounded-lg border border-gray-50 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                <span class="text-[10px] text-gray-400 dark:text-gray-500">السائق</span>
                                <span class="font-bold text-primary">{{ $passenger->driver->name ?? 'غير محدد' }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 items-center pt-4 border-t border-gray-100 dark:border-boxdark">
                            <div class="flex-1"></div>
                            <a href="{{ route('passengers.show', $passenger->id) }}"
                                class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 transition-all dark:bg-boxdark dark:border-boxdark-2 hover:text-indigo-500 hover:border-indigo-500/30 active:scale-95 shrink-0">
                                <span class="text-[18px] material-symbols-outlined">visibility</span>
                            </a>
                            <button type="button"
                                @click="openEditModal({{ $passenger->id }}, {{ json_encode($passenger->date) }}, {{ json_encode($passenger->day) }}, {{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->location) }}, {{ $passenger->count ?? 'null' }}, {{ $passenger->total_commission ?? 'null' }}, {{ json_encode($passenger->broker) }}, {{ json_encode($passenger->driver_id) }}, {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($passenger->note) }})"
                                class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 transition-all dark:bg-boxdark dark:border-boxdark-2 hover:text-primary hover:border-primary/30 active:scale-95 shrink-0">
                                <span class="text-[18px] material-symbols-outlined">edit</span>
                            </button>
                            <button type="button"
                                @click="openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                                class="flex justify-center items-center w-10 h-10 text-rose-500 bg-rose-50 rounded-xl transition-all dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 active:scale-95 shrink-0">
                                <span class="text-[18px] material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="flex flex-col justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <span
                            class="text-[48px] material-symbols-outlined text-gray-300 dark:text-gray-600 mb-4">group_off</span>
                        <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لم نعثر على أي ركاب مسجلين</p>
                    </div>
                @endforelse

                <div x-show="searchQuery !== '' && !Array.from(document.querySelectorAll('.space-y-4 > div[x-show]')).some(el => el.style.display !== 'none')"
                    style="display: none;"
                    class="flex flex-col justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                    <span
                        class="text-[48px] material-symbols-outlined text-gray-300 dark:text-gray-600 mb-4">search_off</span>
                    <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا يوجد نتائج تطابق بحثك</p>
                </div>
            </div>

            {{-- ===== Desktop View (Data Table) ===== --}}
            <div class="hidden overflow-x-auto px-6 pb-6 mt-4 lg:block">
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

                                <td class="px-6 py-4 border-r border-gray-50 border-y dark:border-boxdark-2 first:rounded-r-2xl">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-black text-on-surface dark:text-white font-headline">{{ $passenger->date }}</span>
                                        <span class="text-xs text-gray-500 dark:text-bodydark">{{ $passenger->day }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <span class="font-mono text-sm font-bold tracking-wider text-gray-600 dark:text-gray-300">{{ $passenger->passenger_number }}</span>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <span class="text-sm font-bold text-gray-600 dark:text-gray-300">{{ $passenger->location }}</span>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">العدد: <span class="font-bold">{{ $passenger->count }}</span></span>
                                        <span class="text-xs text-gray-500 dark:text-bodydark">العمولة: <span class="font-bold">{{ $passenger->total_commission }}</span></span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm text-gray-600 dark:text-gray-300">الوسيط: <span class="font-bold">{{ $passenger->broker ?: 'لا يوجد' }}</span></span>
                                        <span class="text-xs font-bold text-primary">السائق: {{ $passenger->driver->name ?? 'غير محدد' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center border-l border-gray-50 border-y dark:border-boxdark-2 last:rounded-l-2xl">
                                    <div class="flex gap-2 justify-center items-center">
                                        <button
                                            @click="openEditModal({{ $passenger->id }}, {{ json_encode($passenger->date) }}, {{ json_encode($passenger->day) }}, {{ json_encode($passenger->passenger_number) }}, {{ json_encode($passenger->location) }}, {{ $passenger->count ?? 'null' }}, {{ $passenger->total_commission ?? 'null' }}, {{ json_encode($passenger->broker) }}, {{ json_encode($passenger->driver_id) }}, {{ json_encode($passenger->driver->name ?? '') }}, {{ json_encode($passenger->driver->phone ?? '') }}, {{ json_encode($passenger->note) }})"
                                            title="تعديل"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-primary-container hover:text-primary hover:border-primary/20 dark:hover:bg-primary/10 dark:hover:text-primary active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <a href="{{ route('passengers.show', $passenger->id) }}"
                                            title="عرض التفاصيل"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-indigo-50 hover:text-indigo-500 hover:border-indigo-200 dark:hover:bg-indigo-500/10 dark:hover:text-indigo-400 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>

                                        <button
                                            @click="openDeleteModal({{ $passenger->id }}, {{ json_encode($passenger->passenger_number) }})"
                                            title="حذف"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 dark:hover:bg-rose-500/10 dark:hover:text-rose-400 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
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
                    class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $passengers->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- ======================== Desktop/Centered Modals ======================== --}}

        {{-- 1. Create Passenger Modal --}}
        <div x-show="showCreateModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-2xl bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto flex flex-col max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-8 border-b border-gray-50 dark:border-boxdark-2">
                    <h3 class="flex gap-2 items-center text-xl font-black font-headline text-on-surface dark:text-white">
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
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Date -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">التاريخ <span class="text-error">*</span></label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">calendar_today</span>
                                <input type="date" name="date" required
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>
                        
                        <!-- Day -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اليوم <span class="text-error">*</span></label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">today</span>
                                <input type="text" name="day" required placeholder="مثلاً: الأحد"
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <!-- Passenger Number -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم الراكب <span class="text-error">*</span></label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">tag</span>
                                <input type="text" name="passenger_number" required placeholder="رقم الراكب"
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">المكان <span class="text-error">*</span></label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">location_on</span>
                                <input type="text" name="location" required placeholder="مكان التواجد"
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <!-- Count -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">العدد <span class="text-error">*</span></label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">group</span>
                                <input type="number" name="count" required placeholder="1"
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <!-- Total Commission -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">إجمالي العمولة <span class="text-error">*</span></label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">payments</span>
                                <input type="number" step="0.01" name="total_commission" required placeholder="0.00"
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <!-- Broker -->
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">الوسيط</label>
                            <div class="relative">
                                <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">handshake</span>
                                <input type="text" name="broker" placeholder="اسم الوسيط (اختياري)"
                                    class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                            </div>
                        </div>

                        <!-- Driver Details (Search / Add) -->
                        <div class="md:col-span-2" x-data="driverSelect({{ $drivers }}, @js(array_values(config('countries', []))))">
                            <h4 class="pb-2 mb-3 text-sm font-bold text-gray-700 border-b border-gray-100 dark:text-gray-200 dark:border-boxdark-2">بيانات السائق</h4>
                            <div class="grid relative grid-cols-1 gap-4 md:grid-cols-2">
                                <input type="hidden" name="driver_id" x-model="selectedDriverId">
                                <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                                {{-- رقم الهاتف --}}
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">رقم الهاتف (اختياري)</label>
                                    <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40 focus-within:border-primary"
                                        :class="selectedDriverId ? 'bg-primary-container dark:bg-primary/10 ring-primary/30' : ''"
                                        style="direction: ltr;">

                                        <div class="relative h-full" @click.away="openCountryDropdown = false">
                                            <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                                class="flex gap-2 items-center px-3 h-14 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                                <template x-if="selectedCountry?.svg">
                                                    <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden" x-html="selectedCountry.svg"></div>
                                                </template>
                                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300" x-text="selectedCountry?.dial_code"></span>
                                            </button>

                                            {{-- Country Dropdown --}}
                                            <div x-show="openCountryDropdown" x-cloak x-transition
                                                class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                                <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                    <input type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                        class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                        dir="rtl">
                                                </div>
                                                <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                    <template x-for="country in filteredCountries" :key="country.code">
                                                        <button type="button" @click="selectedCountry = country; openCountryDropdown = false; searchDriver()"
                                                            class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                            <div class="w-5 h-auto rounded-[2px] overflow-hidden" x-html="country.svg"></div>
                                                            <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200" x-text="country.name"></span>
                                                            <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500" x-text="country.dial_code"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="tel" x-model="localPhoneNumber" @input="searchDriver"
                                            @focus="showDriverDropdown = true" @click.away="showDriverDropdown = false"
                                            placeholder="7XXXXXXXX" inputmode="numeric" autocomplete="off"
                                            :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                            class="flex-1 px-3 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                            :class="selectedDriverId ? 'font-bold text-primary' : ''">

                                        <button type="button" x-show="selectedDriverId" @click="resetSelection"
                                            class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                            <span class="material-symbols-outlined text-[16px]">close</span>
                                        </button>
                                    </div>

                                    {{-- Driver Search Dropdown --}}
                                    <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId"
                                        x-transition x-cloak
                                        class="absolute top-[4.5rem] right-0 w-full md:w-[calc(50%-0.5rem)] bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                        <template x-for="driver in filteredDrivers" :key="driver.id">
                                            <button type="button" @click="selectDriver(driver)"
                                                class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="text-sm font-bold text-on-surface dark:text-white" x-text="driver.name"></span>
                                                    <span class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right" x-text="driver.phone"></span>
                                                </div>
                                                <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                            </button>
                                        </template>
                                        <div x-show="filteredDrivers.length === 0" class="px-4 py-3 text-center bg-surface dark:bg-boxdark-2">
                                            <span class="text-xs font-bold text-gray-500 dark:text-bodydark">سائق جديد، سيتم حفظه تلقائياً.</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- الاسم --}}
                                <div>
                                    <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">اسم السائق <span class="text-error" x-show="localPhoneNumber.length > 0 && !selectedDriverId">*</span></label>
                                    <input type="text" name="driver_name" x-model="nameInput" :required="localPhoneNumber.length > 0 && !selectedDriverId"
                                        :readonly="selectedDriverId !== null" placeholder="الاسم..."
                                        class="px-4 w-full h-14 text-sm rounded-xl border transition-colors outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white"
                                        :class="selectedDriverId ?
                                            'bg-surface border-transparent text-gray-500 cursor-not-allowed opacity-80' :
                                            'bg-white border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20'">
                                </div>
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">ملاحظات</label>
                            <div class="relative">
                                <span class="absolute top-4 right-4 text-gray-400 material-symbols-outlined dark:text-gray-500">description</span>
                                <textarea name="note" rows="3" placeholder="أي ملاحظات إضافية..."
                                    class="py-4 pr-12 pl-4 w-full text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none resize-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40"></textarea>
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

        {{-- 2. Edit Passenger Modal --}}
        <div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-2xl bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-6 md:p-8 pointer-events-auto flex flex-col max-h-[90vh] overflow-y-auto custom-scrollbar">

                <div class="flex justify-between items-center pb-4 mb-8 border-b border-gray-50 dark:border-boxdark-2">
                    <h3 class="flex gap-2 items-center text-xl font-black font-headline text-on-surface dark:text-white">
                        <div
                            class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl shadow-sm bg-surface dark:bg-boxdark-2 dark:text-bodydark">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </div>
                        تعديل بيانات الراكب
                    </h3>
                    <button type="button" @click="closeModals()"
                        class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-colors bg-surface dark:bg-boxdark-2 hover:text-error active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <template x-if="showEditModal">
                    <form :action="editPassengerData.url" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Date -->
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">التاريخ <span class="text-error">*</span></label>
                                <div class="relative">
                                    <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">calendar_today</span>
                                    <input type="date" name="date" x-model="editPassengerData.date" required
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>
                            
                            <!-- Day -->
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">اليوم <span class="text-error">*</span></label>
                                <div class="relative">
                                    <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">today</span>
                                    <input type="text" name="day" x-model="editPassengerData.day" required
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>

                            <!-- Passenger Number -->
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">رقم الراكب <span class="text-error">*</span></label>
                                <div class="relative">
                                    <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">tag</span>
                                    <input type="text" name="passenger_number" x-model="editPassengerData.passenger_number" required
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">المكان <span class="text-error">*</span></label>
                                <div class="relative">
                                    <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">location_on</span>
                                    <input type="text" name="location" x-model="editPassengerData.location" required
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>

                            <!-- Count -->
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">العدد <span class="text-error">*</span></label>
                                <div class="relative">
                                    <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">group</span>
                                    <input type="number" name="count" x-model="editPassengerData.count" required
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>

                            <!-- Total Commission -->
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">إجمالي العمولة <span class="text-error">*</span></label>
                                <div class="relative">
                                    <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">payments</span>
                                    <input type="number" step="0.01" name="total_commission" x-model="editPassengerData.total_commission" required
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>

                            <!-- Broker -->
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">الوسيط</label>
                                <div class="relative">
                                    <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-gray-500">handshake</span>
                                    <input type="text" name="broker" x-model="editPassengerData.broker"
                                        class="pr-12 pl-4 w-full h-14 text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40">
                                </div>
                            </div>

                            <!-- Driver Details (Search / Add) -->
                            <div class="md:col-span-2" x-data="driverSelect({{ $drivers }}, @js(array_values(config('countries', []))), { id: editPassengerData.driver_id, name: editPassengerData.driver_name, phone: editPassengerData.driver_phone })">
                                <h4 class="pb-2 mb-3 text-sm font-bold text-gray-700 border-b border-gray-100 dark:text-gray-200 dark:border-boxdark-2">بيانات السائق</h4>
                                <div class="grid relative grid-cols-1 gap-4 md:grid-cols-2">
                                    <input type="hidden" name="driver_id" x-model="selectedDriverId">
                                    <input type="hidden" name="driver_phone" :value="fullPhoneNumber">

                                    {{-- رقم الهاتف --}}
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">رقم الهاتف (اختياري)</label>
                                        <div class="flex overflow-visible relative items-center bg-white rounded-xl ring-1 ring-gray-200 transition-all group dark:bg-boxdark dark:ring-boxdark-2 focus-within:ring-2 focus-within:ring-primary/40 focus-within:border-primary"
                                            :class="selectedDriverId ? 'bg-primary-container dark:bg-primary/10 ring-primary/30' : ''"
                                            style="direction: ltr;">

                                            <div class="relative h-full" @click.away="openCountryDropdown = false">
                                                <button type="button" @click="openCountryDropdown = !openCountryDropdown"
                                                    class="flex gap-2 items-center px-3 h-14 rounded-l-xl border-r border-gray-200 transition-colors bg-surface dark:bg-boxdark-2 dark:border-boxdark shrink-0 hover:bg-gray-100 dark:hover:bg-boxdark">
                                                    <template x-if="selectedCountry?.svg">
                                                        <div class="w-5 h-auto rounded-[2px] shadow-sm overflow-hidden" x-html="selectedCountry.svg"></div>
                                                    </template>
                                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300" x-text="selectedCountry?.dial_code"></span>
                                                </button>

                                                {{-- Country Dropdown --}}
                                                <div x-show="openCountryDropdown" x-cloak x-transition
                                                    class="absolute top-full left-0 mt-2 w-64 bg-white dark:bg-boxdark-2 rounded-xl shadow-xl border border-gray-100 dark:border-boxdark z-[60] overflow-hidden">
                                                    <div class="p-2 border-b border-gray-50 dark:border-boxdark">
                                                        <input type="text" x-model="searchCountryQuery" placeholder="بحث..."
                                                            class="px-3 w-full h-9 text-xs rounded-lg outline-none bg-surface dark:bg-boxdark focus:ring-1 ring-primary/30 text-on-surface dark:text-white"
                                                            dir="rtl">
                                                    </div>
                                                    <div class="overflow-y-auto max-h-48 custom-scrollbar" dir="ltr">
                                                        <template x-for="country in filteredCountries" :key="country.code">
                                                            <button type="button" @click="selectedCountry = country; openCountryDropdown = false; searchDriver()"
                                                                class="flex gap-3 items-center px-3 py-2 w-full text-left transition-colors hover:bg-surface dark:hover:bg-boxdark">
                                                                <div class="w-5 h-auto rounded-[2px] overflow-hidden" x-html="country.svg"></div>
                                                                <span class="flex-1 text-xs font-bold text-gray-700 truncate dark:text-gray-200" x-text="country.name"></span>
                                                                <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500" x-text="country.dial_code"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="tel" x-model="localPhoneNumber" @input="searchDriver"
                                                @focus="showDriverDropdown = true" @click.away="showDriverDropdown = false"
                                                placeholder="7XXXXXXXX" inputmode="numeric" autocomplete="off"
                                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                                class="flex-1 px-3 w-full h-14 text-sm text-left bg-transparent border-0 outline-none focus:ring-0 font-headline text-on-surface dark:text-white"
                                                :class="selectedDriverId ? 'font-bold text-primary' : ''">

                                            <button type="button" x-show="selectedDriverId" @click="resetSelection"
                                                class="absolute right-2 z-10 p-1 text-gray-400 rounded-full transition-colors bg-white/80 dark:bg-boxdark/80 hover:text-error">
                                                <span class="material-symbols-outlined text-[16px]">close</span>
                                            </button>
                                        </div>

                                        {{-- Driver Search Dropdown --}}
                                        <div x-show="showDriverDropdown && localPhoneNumber.length > 0 && !selectedDriverId"
                                            x-transition x-cloak
                                            class="absolute top-[4.5rem] right-0 w-full md:w-[calc(50%-0.5rem)] bg-white dark:bg-boxdark border border-gray-100 dark:border-boxdark-2 rounded-xl shadow-lg z-[55] overflow-hidden max-h-48 overflow-y-auto custom-scrollbar">
                                            <template x-for="driver in filteredDrivers" :key="driver.id">
                                                <button type="button" @click="selectDriver(driver)"
                                                    class="flex justify-between items-center px-4 py-3 w-full text-right border-b border-gray-50 transition-colors hover:bg-surface dark:hover:bg-boxdark-2 dark:border-boxdark">
                                                    <div class="flex flex-col gap-0.5">
                                                        <span class="text-sm font-bold text-on-surface dark:text-white" x-text="driver.name"></span>
                                                        <span class="text-[10px] font-mono text-gray-500 dark:text-bodydark dir-ltr text-right" x-text="driver.phone"></span>
                                                    </div>
                                                    <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-[18px]">arrow_back_ios</span>
                                                </button>
                                            </template>
                                            <div x-show="filteredDrivers.length === 0" class="px-4 py-3 text-center bg-surface dark:bg-boxdark-2">
                                                <span class="text-xs font-bold text-gray-500 dark:text-bodydark">سائق جديد، سيتم حفظه تلقائياً.</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- الاسم --}}
                                    <div>
                                        <label class="block mb-1.5 text-xs font-bold text-gray-500 dark:text-gray-400">اسم السائق <span class="text-error" x-show="localPhoneNumber.length > 0 && !selectedDriverId">*</span></label>
                                        <input type="text" name="driver_name" x-model="nameInput" :required="localPhoneNumber.length > 0 && !selectedDriverId"
                                            :readonly="selectedDriverId !== null" placeholder="الاسم..."
                                            class="px-4 w-full h-14 text-sm rounded-xl border transition-colors outline-none dark:bg-boxdark-2 dark:border-boxdark dark:text-white"
                                            :class="selectedDriverId ?
                                                'bg-surface border-transparent text-gray-500 cursor-not-allowed opacity-80' :
                                                'bg-white border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20'">
                                    </div>
                                </div>
                            </div>

                            <!-- Note -->
                            <div class="md:col-span-2">
                                <label class="block mb-2 text-sm font-bold text-gray-600 dark:text-gray-300">ملاحظات</label>
                                <div class="relative">
                                    <span class="absolute top-4 right-4 text-gray-400 material-symbols-outlined dark:text-gray-500">description</span>
                                    <textarea name="note" x-model="editPassengerData.note" rows="3"
                                        class="py-4 pr-12 pl-4 w-full text-sm rounded-xl border-none ring-1 ring-gray-200 transition-all outline-none resize-none bg-surface dark:bg-boxdark-2 text-on-surface dark:text-white focus:bg-white dark:focus:bg-boxdark dark:ring-boxdark-2 focus:ring-2 focus:ring-primary/40"></textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="flex gap-2 justify-center items-center mt-8 w-full h-14 font-black text-white rounded-xl shadow-lg transition-all bg-primary shadow-primary/30 active:scale-95">
                            <span class="material-symbols-outlined">update</span>
                            حفظ التعديلات
                        </button>
                    </form>
                </template>
            </div>
        </div>

        {{-- 3. Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">

            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80"
                @click="closeModals()"></div>

            <div
                class="relative w-full max-w-md bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-8 text-center pointer-events-auto flex flex-col">

                {{-- أيقونة التحذير --}}
                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-error rounded-[1.5rem] shadow-sm">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">تأكيد الحذف</h3>

                <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                    هل أنت متأكد من أنك تريد حذف الراكب رقم <br>
                    <span class="text-base font-bold text-on-surface dark:text-white font-headline"
                        x-text="deletePassengerData.passenger_number"></span>؟<br>
                    <span class="inline-block mt-2 text-error/80 dark:text-error">لا يمكن التراجع عن هذا الإجراء.</span>
                </p>

                <form :action="deletePassengerData.url" method="POST" class="flex gap-3 w-full">
                    @csrf
                    @method('DELETE')

                    <button type="button" @click="closeModals()"
                        class="flex-1 h-12 text-sm font-bold text-gray-600 rounded-xl transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-boxdark font-headline active:scale-95">
                        تراجع
                    </button>

                    <button type="submit"
                        class="flex-1 h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-error hover:bg-error/90 shadow-error/30 active:scale-95 font-headline">
                        نعم، احذف
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script>
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
                        if (/^\d/.test(phone)) phone = '+' + phone;

                        this.selectedCountry = this.countries.find(c => phone.startsWith(c.dial_code)) || this.countries.find(c => c.code === 'YE') || this.countries[0];
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
                    if (this.searchCountryQuery === '') return this.countries;
                    const query = this.searchCountryQuery.toLowerCase();
                    return this.countries.filter(c => c.name.toLowerCase().includes(query) || c.dial_code.includes(query));
                },
                get fullPhoneNumber() {
                    if (!this.localPhoneNumber) return '';
                    let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';
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
                    this.filteredDrivers = this.drivers.filter(d => {
                        return d.phone && String(d.phone).includes(query);
                    });
                    this.showDriverDropdown = true;
                },
                selectDriver(driver) {
                    this.selectedDriverId = driver.id;
                    let dialCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';
                    if (driver.phone && driver.phone.startsWith(dialCode)) {
                        this.localPhoneNumber = driver.phone.substring(dialCode.length);
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