@extends('layouts.app')
@section('title', 'قائمة الفروع')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
        search: '',
        filterCity: 'all',
        createModalOpen: false,
        editModalOpen: false,
        isFetching: null,
        editBranch: {
            code: '',
            name: '',
            city: '',
            address: '',
            phone: ''
        },
        showRow(city, name, code) {
            const matchesSearch = name.toLowerCase().includes(this.search.toLowerCase()) || code.toLowerCase().includes(this.search.toLowerCase());
            const matchesCity = this.filterCity === 'all' || city === this.filterCity;
            return matchesSearch && matchesCity;
        },
        async openEditModal(branchCode) {
            this.isFetching = branchCode;
            try {
                const response = await fetch(`/branch/${branchCode}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                this.editBranch = {
                    code: data.code,
                    name: data.name,
                    city: data.city,
                    address: data.address,
                    phone: data.phone
                };
                this.editModalOpen = true;
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ أثناء جلب البيانات');
            } finally {
                this.isFetching = null;
            }
        }
    }">

        {{-- Modal إضافة فرع --}}
        @include('pages.branch.create-branch-modal')

        {{-- Modal تعديل فرع --}}
        @include('pages.branch.edit-branch-modal')

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6">
            <div @click="filterCity = 'all'"
                :class="filterCity === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-50 dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">إجمالي
                        الفروع</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $totalBranches }}</h4>
                </div>
            </div>

            <div
                class="relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 transition-all shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">المدن
                        المغطاة</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $totalCities }}</h4>
                </div>
            </div>

            <div
                class="relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 transition-all shadow-theme-sm">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="text-theme-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest">آخر فرع
                        مضاف</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $branches->first()->name ?? '---' }}</h4>
                </div>
            </div>
        </div>

        <div
            class="grid grid-cols-1 md:grid-cols-2 items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm gap-6">
            <div class="relative group w-full">
                <input type="text" x-model="search" placeholder="ابحث باسم الفرع أو الرمز..."
                    class="w-full h-12 pr-11 pl-4 rounded-xl border-none bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all text-sm font-medium dark:text-white placeholder-gray-400">
                <div
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 group-focus-within:text-brand-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            @if (Auth::user()->type == 'super_admin')
                <div class="flex md:justify-end w-full">
                    <button @click="createModalOpen = true"
                        class="h-12 px-8 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl transition-all shadow-lg shadow-brand-500/20 active:scale-95 text-sm font-bold w-full md:w-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        إضافة فرع جديد
                    </button>
                </div>
            @endif

        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full border-separate border-spacing-y-3 text-right">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="py-4 px-6">#</th>
                            <th class="py-4 px-6 text-center">الرمز</th>
                            <th class="py-4 px-6">اسم الفرع</th>
                            <th class="py-4 px-6">المدينة / العنوان</th>
                            <th class="py-4 px-6">رقم الهاتف</th>
                            <th class="py-4 px-6 text-center">الشحنات المستقبلة</th>
                            <th class="py-4 px-6 text-center">الشحنات المرسلة</th>
                            <th class="py-4 px-6 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse ($branches as $branch)
                            <tr x-show="showRow('{{ $branch->city }}', '{{ $branch->name }}', '{{ $branch->code }}')"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-md transition-all border border-transparent hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="py-5 px-6 first:rounded-r-2xl border-y border-r dark:border-gray-800/50">
                                    <span class="text-xs font-black text-gray-400">{{ $loop->iteration }}</span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <span
                                        class="px-3 py-1.5 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-xs font-black text-brand-600 border border-brand-100 dark:border-brand-500/20">
                                        {{ $branch->code }}
                                    </span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50">
                                    <span
                                        class="text-sm font-black text-gray-900 dark:text-white">{{ $branch->name }}</span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $branch->city }}</span>
                                        <span class="text-xs text-gray-400 mt-0.5">{{ $branch->address }}</span>
                                    </div>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50">
                                    <span
                                        class="text-sm font-black text-gray-600 dark:text-gray-400">{{ $branch->phone }}</span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <span
                                        class="px-3 py-1.5 bg-blue-50 dark:bg-blue-500/10 rounded-lg text-xs font-black text-blue-600 border border-blue-100 dark:border-blue-500/20">
                                        {{ $branch->sent_shipments_count ?? 0 }}
                                    </span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <span
                                        class="px-3 py-1.5 bg-green-50 dark:bg-green-500/10 rounded-lg text-xs font-black text-green-600 border border-green-100 dark:border-green-500/20">
                                        {{ $branch->received_shipments_count ?? 0 }}
                                    </span>
                                </td>

                                <td
                                    class="py-5 px-6 last:rounded-l-2xl border-y border-l dark:border-gray-800/50 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" @click="openEditModal('{{ $branch->code }}')"
                                            :disabled="isFetching === '{{ $branch->code }}'"
                                            class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all disabled:opacity-50"
                                            title="تعديل">
                                            <template x-if="isFetching !== '{{ $branch->code }}'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </template>
                                            <template x-if="isFetching === '{{ $branch->code }}'">
                                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </template>
                                        </button>

                                        <button type="button"
                                            @click="$dispatch('open-warning-modal', {
                                                url: '{{ route('branch.destroy', ['branch' => $branch->code]) }}',
                                                title: 'هل أنت متأكد من حذف هذا الفرع؟',
                                                message: 'سيتم حذف الفرع نهائياً. لا يمكن التراجع عن هذا الإجراء.',
                                                confirmButtonText: 'نعم، احذف',
                                                cancelButtonText: 'إلغاء'
                                            })"
                                            class="p-2 text-gray-400 hover:text-error-500 hover:bg-error-50 rounded-xl transition-all"
                                            title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-20 text-center text-gray-400 italic">لا توجد فروع مسجلة
                                    حالياً..</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($branches->hasPages())
                <div class="p-8 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800">
                    {{ $branches->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
