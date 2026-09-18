@extends('layouts.app')
@section('title', 'فئات الصندوق والتدفقات')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-body" dir="rtl" x-data="{
        editModalOpen: false,
        activeCategory: { id: '', name: '', type: '' },
        openEditModal(category) {
            this.activeCategory = Object.assign({}, category);
            this.editModalOpen = true;
        }
    }">

        {{-- رأس الصفحة والإحصائيات السريعة --}}
        <div
            class="flex flex-col gap-4 justify-between items-start p-6 bg-white rounded-2xl border border-gray-100 shadow-sm sm:flex-row sm:items-center dark:bg-gray-900 dark:border-gray-800">
            <div>
                <div class="flex gap-3 items-center">
                    <div
                        class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary/10 dark:bg-primary/20 text-primary">
                        <span class="material-symbols-outlined text-[28px]">category</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">فئات وحركات الصندوق</h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">إدارة وتصنيف بنود القبض (الإيرادات) وبنود
                            الصرف (المصروفات)</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 items-center w-full sm:w-auto">
                <button type="button" @click="$dispatch('open-create-category-modal')"
                    class="inline-flex gap-2 justify-center items-center px-5 w-full h-11 text-sm font-bold text-white rounded-xl shadow-md transition-all sm:w-auto bg-primary hover:opacity-95 shadow-primary/20">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                    <span>إضافة فئة جديدة</span>
                </button>
            </div>
        </div>

        {{-- فلترة التصنيفات حسب النوع --}}
        <div class="flex gap-2 items-center pb-3 border-b border-gray-200 dark:border-gray-800">
            <a href="{{ route('cash-categories.index') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request('type') ? 'bg-primary text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                الكل ({{ \App\Models\CashCategory::count() }})
            </a>
            <a href="{{ route('cash-categories.index', ['type' => 'income']) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('type') === 'income' ? 'bg-emerald-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <span class="w-2 h-2 bg-emerald-400 rounded-full"></span>
                <span>سندات قبض (إيراد)</span>
            </a>
            <a href="{{ route('cash-categories.index', ['type' => 'expense']) }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('type') === 'expense' ? 'bg-red-500 text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                <span>سندات صرف (مصروف)</span>
            </a>
        </div>

        {{-- جدول الفئات --}}
        <div
            class="overflow-hidden bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead
                        class="text-xs font-bold text-gray-500 uppercase border-b border-gray-100 bg-gray-50/75 dark:bg-gray-800/50 dark:text-gray-400 dark:border-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-4">#</th>
                            <th scope="col" class="px-6 py-4">اسم الفئة</th>
                            <th scope="col" class="px-6 py-4">نوع الحركة</th>
                            <th scope="col" class="px-6 py-4">الحالة</th>
                            <th scope="col" class="px-6 py-4">تاريخ الإنشاء</th>
                            <th scope="col" class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($categories as $index => $category)
                            <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
                                <td class="px-6 py-4 text-xs font-bold text-gray-400 dark:text-gray-500">
                                    {{ $categories->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-black text-gray-900 dark:text-white">
                                        {{ $category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($category->type === 'income')
                                        <span
                                            class="inline-flex gap-1.5 items-center px-3 py-1 text-xs font-bold text-emerald-700 bg-emerald-50 rounded-lg border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                            <span class="material-symbols-outlined text-[16px]">arrow_downward</span>
                                            <span>قبض (إيراد)</span>
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex gap-1.5 items-center px-3 py-1 text-xs font-bold text-red-700 bg-red-50 rounded-lg border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
                                            <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                                            <span>صرف (مصروف)</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('cash-categories.update', $category) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="toggle_status" value="1">
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold cursor-pointer transition-colors {{ $category->is_active ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20' : 'bg-gray-100 text-gray-600 border border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                            <span>{{ $category->is_active ? 'نشط' : 'معطل' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ $category->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2 justify-center items-center">
                                        {{-- زر التعديل --}}
                                        <button type="button"
                                            @click="openEditModal({ id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', type: '{{ $category->type }}' })"
                                            class="p-2 text-gray-500 rounded-xl transition-all hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800"
                                            title="تعديل">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>

                                        {{-- زر الحذف --}}
                                        <form action="{{ route('cash-categories.destroy', $category) }}" method="POST"
                                            onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه الفئة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-gray-500 rounded-xl transition-all hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                                title="حذف">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <div class="flex flex-col gap-2 justify-center items-center">
                                        <span
                                            class="text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">category</span>
                                        <span class="text-sm font-bold">لا توجد فئات مسجلة حالياً</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>

        {{-- تضمين المودالات --}}
        @include('pages.cash_categories.modals.create-category-modal')
        @include('pages.cash_categories.modals.edit-category-modal')
    </div>
@endsection
