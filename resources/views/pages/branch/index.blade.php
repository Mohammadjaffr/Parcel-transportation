@extends('layouts.app')
@section('title', 'قائمة الفروع')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
        search: '',
        filterCity: 'all',
        showRow(city, name, code) {
            const matchesSearch = name.includes(this.search) || code.includes(this.search);
            const matchesCity = this.filterCity === 'all' || city === this.filterCity;
            return matchesSearch && matchesCity;
        }
    }">

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

            <div class="flex md:justify-end w-full">
                <a href="{{ route('branch.create') }}"
                    class="h-12 px-8 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl transition-all shadow-lg shadow-brand-500/20 active:scale-95 text-sm font-bold w-full md:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    إضافة فرع جديد
                </a>
            </div>
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

                                <td
                                    class="py-5 px-6 last:rounded-l-2xl border-y border-l dark:border-gray-800/50 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('branch.edit', ['branch' => $branch->code]) }}"
                                            class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-xl transition-all"
                                            title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

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
                                <td colspan="6" class="py-20 text-center text-gray-400 italic">لا توجد فروع مسجلة
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
