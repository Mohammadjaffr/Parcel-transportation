@extends('layouts.app')
@section('title', 'السائقين')
@section('Breadcrumb', 'إدارة السائقين')
@section('addButton')
    <button @click="$dispatch('open-create-modal')"
        class="inline-flex gap-2 items-center px-4 py-2 text-sm font-semibold text-white rounded-xl transition-all bg-brand-500 hover:bg-brand-600 hover:shadow-md hover:shadow-brand-500/20 active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        إضافة سائق جديد
    </button>
@endsection

@section('content')

    <div x-data="driverFilter()" @open-create-modal.window="showCreateModal = true" class="space-y-6 font-outfit"
        dir="rtl">

        {{-- Modals --}}
        @include('pages.drivers.modals.create')
        @include('pages.drivers.modals.edit')

        {{-- الإحصائيات (بطاقات متناسقة) --}}
        {{-- الإحصائيات (بطاقات الفلترة التفاعلية) --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            {{-- إجمالي السائقين --}}
            <div @click="statusFilter = 'all'"
                :class="statusFilter === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' :
                    'border-gray-100 hover:border-brand-300'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-xl dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        السائقين</span>
                    <h4 class="text-xl font-black dark:text-white" x-text="drivers.length"></h4>
                </div>
            </div>

            {{-- متاحين للعمل --}}
            <div @click="statusFilter = 'available'"
                :class="statusFilter === 'available' ? 'border-success-500 ring-2 ring-success-500/20' :
                    'border-gray-100 hover:border-success-300'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 21.48V22" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">متاحين
                        للعمل</span>
                    {{-- ملاحظة: استبدل 'status' باسم الحقل الفعلي في قاعدة بياناتك الذي يحدد حالة السائق --}}
                    <h4 class="text-xl font-black dark:text-white"
                        x-text="drivers.filter(d => d.status === 'available').length || 0"></h4>
                </div>
            </div>

            {{-- في رحلة التوصيل --}}
            <div @click="statusFilter = 'on_trip'"
                :class="statusFilter === 'on_trip' ? 'border-warning-500 ring-2 ring-warning-500/20' :
                    'border-gray-100 hover:border-warning-300'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">في رحلة
                        التوصيل</span>
                    <h4 class="text-xl font-black dark:text-white"
                        x-text="drivers.filter(d => d.status === 'on_trip').length || 0"></h4>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">

            {{-- شريط البحث --}}
            <div class="w-full p-4 bg-white dark:bg-white/[0.03] rounded-2xl">
                <div class="relative rounded-2xl border ring-2 group border-brand-500 ring-brand-500/20">
                    <input type="text" x-model="search" @input.debounce.300ms="filterNow"
                        placeholder="ابحث باسم السائق أو رقم الهاتف..."
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

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                <template x-for="(driver, index) in filteredDrivers" :key="driver.id">
                    <div
                        class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full shadow-sm bg-brand-500 dark:text-brand-300"
                                    x-text="driver.name ? driver.name.charAt(0) : '?'"></div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white"
                                        x-text="driver.name"></span>
                                    <div class="flex gap-1 items-center mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                                        dir="ltr">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span x-text="driver.phone || 'غير مدخل'"></span>
                                    </div>
                                </div>
                            </div>
                            <button @click="openEditModal(driver)"
                                class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-brand-500 hover:border-brand-200 dark:bg-gray-900 dark:border-gray-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>
                                سائق معتمد
                            </span>
                            <a :href="`/drivers/${driver.id}/shipments`"
                                class="text-xs font-semibold transition-colors text-brand-500 hover:text-brand-600">
                                عرض الشحنات &larr;
                            </a>
                        </div>
                    </div>
                </template>

                {{-- حالة عدم وجود نتائج --}}
                <div x-show="filteredDrivers.length === 0"
                    class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                    <div class="flex flex-col justify-center items-center">
                        <div class="p-3 mb-3 bg-white rounded-full shadow-sm dark:bg-gray-800">
                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">تأكد من كتابة اسم السائق أو رقمه بشكل
                            صحيح.</p>
                    </div>
                </div>
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4 w-14">#</th>
                            <th class="px-6 py-4">اسم السائق</th>
                            <th class="px-6 py-4">رقم التواصل</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        <template x-for="(driver, index) in filteredDrivers" :key="driver.id">
                            <tr
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="px-6 py-5 border-r border-y first:rounded-r-2xl dark:border-gray-800/50">
                                    <span class="text-sm font-bold text-gray-400 dark:text-gray-500"
                                        x-text="(index + 1).toString().padStart(2, '0')"></span>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full shadow-sm bg-brand-500 dark:text-brand-300"
                                            x-text="driver.name ? driver.name.charAt(0) : '?'"></div>
                                        <span class="text-sm font-black text-gray-900 dark:text-white"
                                            x-text="driver.name"></span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-400" dir="ltr">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <span class="text-sm font-bold" x-text="driver.phone || '---'"></span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>
                                        سائق معتمد
                                    </span>
                                </td>

                                <td
                                    class="px-6 py-5 text-center border-l border-y last:rounded-l-2xl dark:border-gray-800/50">
                                    <div class="flex gap-2 justify-center items-center">
                                        {{-- زر التعديل --}}
                                        <button @click="openEditModal(driver)" title="تعديل بيانات السائق"
                                            class="inline-flex p-2 text-gray-400 bg-gray-50 rounded-lg transition-all dark:bg-gray-800 hover:bg-brand-50 hover:text-brand-500 dark:hover:bg-brand-500/10 dark:hover:text-brand-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        {{-- زر الشحنات --}}
                                        <a :href="`/drivers/${driver.id}/shipments`" title="عرض الشحنات المرتبطة"
                                            class="inline-flex p-2 text-gray-400 bg-gray-50 rounded-lg transition-all dark:bg-gray-800 hover:bg-success-50 hover:text-success-600 dark:hover:bg-success-500/10 dark:hover:text-success-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20 12H4m16 0l-4-4m4 4l-4 4" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredDrivers.length === 0">
                            <td colspan="5" class="py-20 text-center">
                                <div class="italic text-gray-400">لا توجد نتائج تطابق بحثك حالياً..</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($drivers->hasPages())
                <div class="px-6 pb-6">
                    {{ $drivers->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function driverFilter() {
            return {
                search: "",
                statusFilter: "all", // الحالة الافتراضية لعرض الكل
                drivers: @json($drivers->items()),

                showCreateModal: false,
                showEditModal: false,
                editDriver: {
                    id: null,
                    name: '',
                    phone: '',
                    url: ''
                },

                // استخدام Getter يجعل الفلترة ذكية وتتحدث تلقائياً بدون تعارض
                get filteredDrivers() {
                    let result = this.drivers;

                    // 1. فلترة حسب الحالة (الكروت)
                    if (this.statusFilter !== 'all') {
                        // ملاحظة: تأكد أن لديك حقل status في الداتابيز يحمل القيم 'available' أو 'on_trip'
                        // وإذا كان اسم الحقل مختلف، قم بتغيير d.status إلى اسم الحقل الخاص بك
                        result = result.filter(d => d.status === this.statusFilter);
                    }

                    // 2. فلترة حسب البحث (الاسم أو الهاتف)
                    if (this.search.trim() !== "") {
                        const searchTerm = this.search.toLowerCase().trim();
                        result = result.filter(driver => {
                            const nameMatches = (driver.name || "").toLowerCase().includes(searchTerm);
                            const phoneMatches = (driver.phone || "").includes(searchTerm);
                            return nameMatches || phoneMatches;
                        });
                    }

                    return result;
                },

                openEditModal(driver) {
                    this.editDriver = {
                        id: driver.id,
                        name: driver.name,
                        phone: driver.phone || '',
                        url: '/drivers/' + driver.id
                    };
                    this.showEditModal = true;
                }
            }
        }
    </script>
@endsection
