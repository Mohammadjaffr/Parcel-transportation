@extends('layouts.app')
@section('title', 'المكاتب غير الموثوقة')
@section('Breadcrumb', 'إدارة المكاتب غير الموثوقة')
@section('addButton')
    <button @click="$dispatch('open-create-office-modal')"
        class="inline-flex gap-2 items-center px-4 py-2 text-sm font-semibold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        إضافة مكتب جديد
    </button>
@endsection

@section('content')
      <div x-data="officeFilter()" @open-create-office-modal.window="showCreateModal = true" class="space-y-6 font-outfit" dir="rtl">

        {{-- Modals --}}
        {{-- @include('pages.office.unverified.modals.create') --}}
        {{-- @include('pages.office.unverified.modals.edit') --}}

        {{-- الإحصائيات --}}
        <div class="grid grid-cols-1 gap-4 md:gap-6">
            <div @click="statusFilter = 'all'"
                :class="statusFilter === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-gray-800'"
                class="relative flex flex-col items-start justify-between p-5 transition-all bg-white border cursor-pointer rounded-2xl dark:bg-white/[0.03] hover:shadow-md shadow-theme-sm dark:border-gray-800">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/10 text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        إجمالي المكاتب (في هذه الصفحة)
                    </span>
                    <h4 class="text-xl font-black dark:text-white" x-text="search === '' ? '{{ $offices->count() }} من {{ $offices->total() ?? 0 }}' : filteredoffices.length"></h4>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-boxdark rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden transition-colors">

            {{-- شريط البحث --}}
            <div class="p-4 w-full bg-white rounded-2xl border-b border-gray-100 dark:bg-transparent dark:border-gray-800">
                <div class="relative rounded-2xl border border-gray-200 transition-all group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 dark:border-gray-700">
                    <input type="text" x-model.debounce.300ms="search"
                        placeholder="ابحث باسم المكتب أو رقم الهاتف (في الصفحة الحالية)..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 rounded-2xl border-none transition-all outline-none bg-gray-50/50 dark:bg-gray-900 focus:ring-0 dark:text-white">
                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors group-focus-within:text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                <template x-for="(office, index) in filteredoffices" :key="office.id">
                    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 transition-all bg-gray-50/50 dark:bg-gray-800/50 dark:border-gray-700 hover:border-primary/30 hover:shadow-sm">
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full shadow-sm bg-primary"
                                    x-text="office.name ? office.name.charAt(0) : '?'"></div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="office.name"></span>
                                    <div class="flex gap-1 items-center mt-0.5 text-xs text-gray-500 dark:text-gray-400" dir="ltr">
                                        <span class="material-symbols-outlined text-[14px]">call</span>
                                        <span x-text="office.phone || 'غير مدخل'"></span>
                                    </div>
                                    <div class="flex gap-1 items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                                        <span x-text="office.address || 'غير مدخل'"></span>
                                    </div>
                                </div>
                            </div>
                            <button @click="openEditModal(office)"
                                class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-primary hover:border-primary/30 dark:bg-gray-900 dark:border-gray-800">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                مكتب غير موثوق
                            </span>
                            <a :href="`/offices/${office.id}/shipments`" class="text-xs font-semibold transition-colors text-primary hover:text-primary-hover">
                                عرض الشحنات &larr;
                            </a>
                        </div>
                    </div>
                </template>

                {{-- حالة عدم وجود نتائج (موبايل) --}}
                <div x-show="filteredoffices.length === 0" x-cloak
                    class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-700">
                    <div class="flex flex-col justify-center items-center">
                        <div class="p-3 mb-3 bg-white rounded-full shadow-sm dark:bg-gray-900">
                            <span class="text-3xl text-gray-400 material-symbols-outlined">search_off</span>
                        </div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">لا توجد نتائج</h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">تأكد من كتابة اسم المكتب أو رقمه بشكل صحيح.</p>
                    </div>
                </div>
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 mt-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] dark:text-bodydark2">
                            <th class="px-6 py-4 w-14">#</th>
                            <th class="px-6 py-4">اسم المكتب</th>
                            <th class="px-6 py-4">رقم التواصل</th>
                            <th class="px-6 py-4">العنوان</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        <template x-for="(office, index) in filteredoffices" :key="office.id">
                            <tr class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900/50 hover:shadow-md hover:border-primary/30 dark:hover:border-primary/30">
                                <td class="px-6 py-5 border-r border-gray-100 border-y dark:border-gray-800 first:rounded-r-2xl">
                                    <span class="text-sm font-bold text-gray-400 dark:text-gray-500" x-text="(index + 1).toString().padStart(2, '0')"></span>
                                </td>
                                <td class="px-6 py-5 border-gray-100 border-y dark:border-gray-800">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-full shadow-sm bg-primary" x-text="office.name ? office.name.charAt(0) : '?'"></div>
                                        <span class="text-sm font-black text-gray-900 dark:text-white" x-text="office.name"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-gray-100 border-y dark:border-gray-800">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-400" dir="ltr">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">call</span>
                                        <span class="text-sm font-bold" x-text="office.phone || '---'"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 border-gray-100 border-y dark:border-gray-800">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">location_on</span>
                                        <span class="text-sm font-bold" x-text="office.address || '---'"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center border-gray-100 border-y dark:border-gray-800">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        مكتب غير موثوق
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100 border-y dark:border-gray-800 last:rounded-l-2xl">
                                    <div class="flex gap-2 justify-center items-center">
                                        <button @click="openEditModal(office)" title="تعديل بيانات المكتب"
                                            class="inline-flex p-2 text-gray-400 bg-gray-50 rounded-lg transition-all dark:bg-gray-800 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        <a :href="`/offices/${office.id}/shipments`" title="عرض الشحنات المرتبطة"
                                            class="inline-flex p-2 text-gray-400 bg-gray-50 rounded-lg transition-all dark:bg-gray-800 hover:bg-primary/10 hover:text-primary dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredoffices.length === 0" x-cloak>
                            <td colspan="6" class="py-20 text-center">
                                <div class="flex flex-col justify-center items-center">
                                    <span class="mb-2 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                                    <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">لا توجد نتائج تطابق بحثك في هذه الصفحة.</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($offices->hasPages())
                <div class="px-6 pt-4 pb-6 mt-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $offices->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function officeFilter() {
            return {
                search: "",
                statusFilter: "all",
                offices: @json($offices->items()),
                showCreateModal: false,
                showEditModal: false,
                editoffice: {
                    id: null,
                    name: '',
                    phone: '',
                    address: '',
                    url: ''
                },

                get filteredoffices() {
                    let result = this.offices;

                    if (this.search.trim() !== "") {
                        const searchTerm = this.search.toLowerCase().trim();
                        result = result.filter(office => {
                            const nameMatches = (office.name || "").toLowerCase().includes(searchTerm);
                            const phoneMatches = (office.phone || "").includes(searchTerm);
                            return nameMatches || phoneMatches;
                        });
                    }

                    return result;
                },

                openEditModal(office) {
                    this.editoffice = {
                        id: office.id,
                        name: office.name,
                        phone: office.phone || '',
                        address: office.address || '',
                        url: '/offices/' + office.id
                    };
                    this.showEditModal = true;
                }
            }
        }
    </script>
@endsection