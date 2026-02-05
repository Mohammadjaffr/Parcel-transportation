@extends('layouts.app')
@section('title', 'إعدادات فئات المعاملات')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <x-modals.warning-modal />

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
                    filterType: 'all',
                    searchQuery: '',
                    editModalOpen: false,
                    categoryToEdit: { id: null, name: '' },
                    openEditModal(id, name) {
                        this.categoryToEdit = { id: id, name: name };
                        this.editModalOpen = true;
                    },
                    closeEditModal() {
                        this.editModalOpen = false;
                        this.categoryToEdit = { id: null, name: '' };
                    }
                }">
        {{-- Filter Cards --}}
        <div class="flex gap-6">

            {{-- All Categories --}}
            <div @click="filterType = 'all'" :class="filterType === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' :
                                'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        الفئات</span>
                    <h4 class="text-xl font-black text-brand-500">{{ $categories->count() }}</h4>
                </div>
            </div>

            {{-- Income Categories --}}
            <div @click="filterType = 'in'" :class="filterType === 'in' ? 'border-success-500 ring-2 ring-success-500/20' :
                                'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 11l5-5m0 0l5 5m-5-5v12" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">فئات
                        الإيرادات</span>
                    <h4 class="text-xl font-black text-success-500">{{ $categories->where('type', 'in')->count() }}</h4>
                </div>
            </div>

            {{-- Expense Categories --}}
            <div @click="filterType = 'out'" :class="filterType === 'out' ? 'border-error-500 ring-2 ring-error-500/20' :
                                'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-error-50 dark:bg-error-500/10 text-error-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">فئات
                        المصروفات</span>
                    <h4 class="text-xl font-black text-error-500">{{ $categories->where('type', 'out')->count() }}</h4>
                </div>
            </div>
            <div @click="filterType = 'active'" :class="filterType === 'active' ? 'border-success-500 ring-2 ring-success-500/20' :
                                'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span
                        class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">نشطة</span>
                    <h4 class="text-xl font-black text-success-500">{{ $categories->where('is_active', true)->count() }}
                    </h4>
                </div>
            </div>

            {{-- Inactive Categories --}}
            <div @click="filterType = 'inactive'" :class="filterType === 'inactive' ? 'border-gray-500 ring-2 ring-gray-500/20' :
                                'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 text-gray-500 bg-gray-100 rounded-xl dark:bg-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span
                        class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">معطلة</span>
                    <h4 class="text-xl font-black text-gray-500">{{ $categories->where('is_active', false)->count() }}</h4>
                </div>
            </div>
        </div>
        {{-- Header --}}
        <div
            class="flex flex-col gap-4 justify-between items-start p-6 bg-white rounded-2xl border border-gray-100 md:flex-row md:items-center dark:bg-white/[0.03] dark:border-gray-800 shadow-theme-sm">
            <div class="flex gap-4 items-center">
                <div class="flex justify-center items-center w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">إدارة فئات المعاملات</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إضافة وتعديل فئات الإيرادات والمصروفات</p>
                </div>
            </div>
            <div class="flex gap-3">
                {{-- Add Category Modal Button --}}
                @include('transaction_categories.modals.create-category-modal')

                <a href="{{ route('transactions.index') }}"
                    class="flex gap-2 justify-center items-center px-5 h-11 text-sm font-semibold rounded-xl transition-all duration-200 text-brand-500 bg-brand-50 dark:text-brand-400 dark:bg-brand-500/10 hover:bg-brand-100 dark:hover:bg-brand-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة للمعاملات
                </a>
            </div>
        </div>

        {{-- Search Bar --}}
        <div
            class="bg-white rounded-2xl border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800 shadow-theme-sm p-6">
            <div class="relative">
                <div class="flex absolute inset-y-0 right-0 items-center pr-4 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="searchQuery"
                    class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                    placeholder="ابحث عن فئة بالاسم...">
                <div x-show="searchQuery.length > 0" class="flex absolute inset-y-0 left-0 items-center pl-4">
                    <button @click="searchQuery = ''"
                        class="p-1 text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-300">
                        <div
                            class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 group-focus-within:text-brand-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </button>
                </div>
            </div>
        </div>



        {{-- Categories Table --}}
        <div
            class="bg-white rounded-2xl border border-gray-100 dark:bg-white/[0.03] dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div
                class="flex flex-col gap-4 justify-between p-6 border-b border-gray-100 md:flex-row md:items-center dark:border-gray-800">
                <div class="flex gap-3 items-center">
                    <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                        <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">الفئات الموجودة</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي <span
                                class="font-bold text-brand-500">{{ $categories->count() }}</span> فئة</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px]">
                    <thead class="bg-gray-50/80 dark:bg-gray-900/50">
                        <tr>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                #</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase dark:text-gray-400">
                                اسم الفئة</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                النوع</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                الرمز البرمجي</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                عدد المعاملات</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                الحالة</th>
                            <th
                                class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase dark:text-gray-400">
                                الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($categories as $category)
                            <tr x-show="(filterType === 'all' || filterType === '{{ $category->type }}' || (filterType === 'active' && {{ $category->is_active ? 'true' : 'false' }}) || (filterType === 'inactive' && {{ $category->is_active ? 'false' : 'true' }})) && '{{ $category->name }}'.toLowerCase().includes(searchQuery.toLowerCase())"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="transition-colors hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex justify-center items-center w-8 h-8 text-xs font-bold text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $category->name }}</span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($category->type === 'in')
                                        <span
                                            class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-full bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                            </svg>
                                            إيراد
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-bold rounded-full bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                            </svg>
                                            مصروف
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($category->code)
                                        <code
                                            class="px-3 py-1.5 font-mono text-xs font-medium text-gray-600 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-400">{{ $category->code }}</code>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex justify-center items-center min-w-[36px] px-3 py-1.5 text-xs font-bold rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                        {{ $category->transactions()->count() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('transaction-categories.update', $category) }}"
                                        class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="toggle_status" value="1">
                                        <button type="submit"
                                            class="inline-flex gap-2 items-center px-4 py-2 text-xs font-bold rounded-full border-2 transition-all duration-200 {{ $category->is_active ? 'bg-success-50 text-success-600 border-success-200 hover:bg-success-100 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/30 dark:hover:bg-success-500/20' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700' }}">
                                            <span
                                                class="w-2 h-2 rounded-full {{ $category->is_active ? 'bg-success-500' : 'bg-gray-400' }}"></span>
                                            {{ $category->is_active ? 'نشط' : 'معطل' }}
                                        </button>
                                    </form>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex gap-2 justify-center items-center">
                                        {{-- Edit Button --}}
                                        <button type="button"
                                            @click="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-all duration-200 hover:bg-brand-50 hover:text-brand-500 dark:hover:bg-brand-500/10 dark:hover:text-brand-400"
                                            title="تعديل الفئة">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- Delete Button --}}
                                        @if ($category->transactions()->count() == 0)
                                            <form method="POST" action="{{ route('transaction-categories.destroy', $category) }}"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="if(confirm('هل أنت متأكد من حذف هذه الفئة؟')) { this.closest('form').submit(); }"
                                                    class="inline-flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl transition-all duration-200 hover:bg-error-50 hover:text-error-500 dark:hover:bg-error-500/10 dark:hover:text-error-400"
                                                    title="حذف الفئة">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span
                                                class="inline-flex justify-center items-center w-10 h-10 text-gray-300 rounded-xl cursor-not-allowed dark:text-gray-600"
                                                title="لا يمكن حذف فئة مرتبطة بمعاملات">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="flex justify-center items-center mb-4 w-16 h-16 bg-gray-100 rounded-2xl dark:bg-gray-800">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">لا توجد فئات
                                            مسجلة حالياً</p>
                                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">اضغط على زر "إضافة فئة
                                            جديدة" للبدء</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($categories->hasPages())
                <div class="p-6 border-t border-gray-100 dark:border-gray-800">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

        {{-- Edit Category Modal --}}
        @include('transaction_categories.modals.edit-category-modal')
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.dispatchEvent(new CustomEvent('open-success-modal', {
                    detail: {
                        message: '{{ session('success') }}'
                    }
                }));
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.dispatchEvent(new CustomEvent('open-error-modal', {
                    detail: {
                        message: '{{ session('error') }}'
                    }
                }));
            });
        </script>
    @endif
@endsection