@extends('layouts.app')
@section('title', 'المكاتب غير الموثوقة')
@section('Breadcrumb', 'إدارة المكاتب غير الموثوقة')

@section('addButton')
    <a href="{{ route('offices.create') }}"
        class="flex gap-2 justify-center items-center px-4 py-2.5 text-sm font-bold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95">
        <span class="material-symbols-outlined text-[20px]">add_box</span>
        إضافة مكتب جديد
    </a>
@endsection

@section('content')
    <div x-data="officeFilter()" @open-create-office-modal.window="showCreateModal = true" class="space-y-6 font-outfit" dir="rtl">

        {{-- ===== الإحصائيات ===== --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div @click="statusFilter = 'all'"
                :class="statusFilter === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-gray-800'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border transition-all cursor-pointer dark:bg-boxdark hover:shadow-md shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 text-primary">
                    <span class="material-symbols-outlined text-[22px]">apartment</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        المكاتب (في هذه الصفحة)
                    </span>
                    <h4 class="mt-1 text-2xl font-black dark:text-white" x-text="search === '' ? '{{ $offices->count() }}' : filteredoffices.length"></h4>
                </div>
            </div>
        </div>

        {{-- ===== الحاوية الرئيسية (بحث + جدول) ===== --}}
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800">

            {{-- شريط البحث --}}
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 dark:bg-gray-900/30 dark:border-gray-800">
                <div class="relative flex-1 group">
                    <input type="text" x-model.debounce.300ms="search"
                        placeholder="ابحث باسم المكتب أو رقم الهاتف..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-white rounded-xl border border-gray-200 transition-all outline-none dark:bg-gray-900 dark:border-gray-700 focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white">
                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                <template x-for="(office, index) in filteredoffices" :key="office.id">
                    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 transition-colors bg-gray-50/50 dark:bg-gray-800/50 dark:border-gray-700 hover:border-primary/30 hover:shadow-sm">
                        
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-xl shadow-sm bg-primary"
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
                            
                            <a :href="`/offices/${office.id}/edit`" class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-primary dark:bg-gray-900 dark:border-gray-800">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                        </div>

                        <div class="flex justify-between items-center pt-3 mt-1 border-t border-gray-100 dark:border-gray-700/50">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
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
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-right">
                    <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] border-b border-gray-100 dark:border-gray-800">
                            <th class="px-6 py-4 w-16">#</th>
                            <th class="px-6 py-4">اسم المكتب</th>
                            <th class="px-6 py-4">رقم التواصل</th>
                            <th class="px-6 py-4">العنوان</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800/50">
                        <template x-for="(office, index) in filteredoffices" :key="office.id">
                            <tr class="bg-white transition-colors hover:bg-gray-50/50 dark:bg-transparent dark:hover:bg-gray-800/30 group">
                                
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-400 dark:text-gray-500" x-text="(index + 1).toString().padStart(2, '0')"></span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-sm font-bold text-white rounded-xl shadow-sm bg-primary" x-text="office.name ? office.name.charAt(0) : '?'"></div>
                                        <span class="text-sm font-black text-gray-900 dark:text-white" x-text="office.name"></span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-400" dir="ltr">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">call</span>
                                        <span class="text-sm font-bold" x-text="office.phone || '---'"></span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">location_on</span>
                                        <span class="text-sm font-bold" x-text="office.address || '---'"></span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        مكتب غير موثوق
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex gap-2 justify-center items-center opacity-0 transition-opacity group-hover:opacity-100">
                                        <a :href="`/offices/${office.id}/edit`" title="تعديل بيانات المكتب"
                                            class="flex justify-center items-center w-8 h-8 text-gray-500 rounded-lg transition-colors hover:text-primary hover:bg-primary/10 dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                        <a :href="`/offices/${office.id}/shipments`" title="عرض الشحنات المرتبطة"
                                            class="flex justify-center items-center w-8 h-8 text-gray-500 rounded-lg transition-colors hover:text-primary hover:bg-primary/10 dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State (Desktop) --}}
                        <tr x-show="filteredoffices.length === 0" x-cloak>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col justify-center items-center">
                                    <div class="flex justify-center items-center mb-4 w-16 h-16 bg-gray-50 rounded-full dark:bg-gray-800/50">
                                        <span class="text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">search_off</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">لا توجد نتائج</h4>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">تأكد من كتابة اسم المكتب أو رقمه بشكل صحيح.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- ===== شريط الترقيم (Pagination) المخصص ===== --}}
            @if ($offices->hasPages())
                <div class="flex justify-center items-center px-6 py-6 w-full border-t border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/20">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex gap-2 justify-center items-center">
                        
                        {{-- زر الصفحة السابقة --}}
                        @if ($offices->onFirstPage())
                            <span class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-xl border border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600 shrink-0">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_left</span>
                            </span>
                        @else
                            <a href="{{ $offices->previousPageUrl() }}" class="flex justify-center items-center w-10 h-10 text-gray-600 bg-white rounded-xl border border-gray-200 shadow-sm transition-colors hover:bg-primary/5 hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 shrink-0">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_left</span>
                            </a>
                        @endif

                        {{-- أرقام الصفحات --}}
                        <div class="flex gap-1 items-center px-2 py-1.5 bg-white rounded-2xl border border-gray-200 shadow-sm dark:bg-boxdark dark:border-gray-700">
                            @foreach ($offices->elements() as $element)
                                @if (is_string($element))
                                    <span class="flex justify-center items-center w-8 h-8 text-sm font-bold text-gray-400">{{ $element }}</span>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $offices->currentPage())
                                            <span class="flex items-center justify-center w-8 h-8 text-sm font-black !text-white border shadow-md bg-primary shadow-primary/30 rounded-xl border-primary shrink-0">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="flex justify-center items-center w-8 h-8 text-sm font-bold text-gray-500 bg-transparent rounded-xl border border-transparent transition-colors hover:bg-primary/10 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800 shrink-0">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </div>

                        {{-- زر الصفحة التالية --}}
                        @if ($offices->hasMorePages())
                            <a href="{{ $offices->nextPageUrl() }}" class="flex justify-center items-center w-10 h-10 text-gray-600 bg-white rounded-xl border border-gray-200 shadow-sm transition-colors hover:bg-primary/5 hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 shrink-0">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_right</span>
                            </a>
                        @else
                            <span class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-xl border border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600 shrink-0">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_right</span>
                            </span>
                        @endif
                        
                    </nav>
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
                }
            }
        }
    </script>
@endsection