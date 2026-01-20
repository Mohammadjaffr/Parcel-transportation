@extends('layouts.app')
@section('title', 'Transaction Category Settings')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <x-modals.warning-modal />

    <div class="space-y-6 font-outfit" dir="rtl">
        
        {{-- Header --}}
        <div class="flex justify-between items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white">إدارة فئات المعاملات</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إضافة وتعديل فئات الإيرادات والمصروفات</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="flex gap-2 justify-center items-center px-6 h-10 text-sm font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-xl transition-all hover:bg-gray-200 dark:hover:bg-gray-700 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                العودة للمعاملات
            </a>
        </div>

        {{-- Add New Category Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm p-8">
            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6">إضافة فئة جديدة</h3>
            
            <form method="POST" action="{{ route('transaction-categories.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Category Name --}}
                    <div class="form-group">
                        <label for="name" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            اسم الفئة <span class="text-danger-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="w-full h-12 px-4 text-sm font-medium bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white" 
                            placeholder="مثال: رواتب الموظفين"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div class="form-group">
                        <label for="type" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            النوع <span class="text-danger-500">*</span>
                        </label>
                        <select name="type" id="type" class="w-full h-12 px-4 text-sm font-medium bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white" required>
                            <option value="">-- اختر النوع --</option>
                            <option value="in" {{ old('type') === 'in' ? 'selected' : '' }}>إيراد / دخل</option>
                            <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>مصروف / خروج</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- System Code (Optional, for advanced users) --}}
                    <div class="form-group">
                        <label for="code" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                            الرمز البرمجي (اختياري)
                        </label>
                        <input 
                            type="text" 
                            name="code" 
                            id="code" 
                            class="w-full h-12 px-4 text-sm font-medium bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white" 
                            placeholder="SYSTEM_CODE"
                            value="{{ old('code') }}"
                        >
                        <p class="mt-1 text-xs text-gray-500">للربط مع النظام فقط - اتركه فارغاً للاستخدام العادي</p>
                        @error('code')
                            <p class="mt-1 text-xs text-danger-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="flex justify-end mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit" class="px-8 h-12 text-sm font-bold text-white bg-brand-500 rounded-xl shadow-lg transition-all hover:bg-brand-600 shadow-brand-500/20 active:scale-95">
                        <svg class="inline-block w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        إضافة الفئة
                    </button>
                </div>
            </form>
        </div>

        {{-- Categories Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="px-8 pt-8 pb-4">
                <h3 class="text-lg font-black text-gray-900 dark:text-white">الفئات الموجودة</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">إجمالي {{ $categories->count() }} فئة</p>
            </div>

            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">اسم الفئة</th>
                            <th class="px-6 py-4 text-center">النوع</th>
                            <th class="px-6 py-4 text-center">الرمز البرمجي</th>
                            <th class="px-6 py-4 text-center">عدد المعاملات</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse ($categories as $category)
                            <tr class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">
                                
                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <span class="text-xs font-black text-gray-400">{{ $loop->iteration }}</span>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <span class="text-sm font-black text-gray-900 dark:text-white">{{ $category->name }}</span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    @if($category->type === 'in')
                                        <span class="px-3 py-1.5 text-xs font-black rounded-lg border bg-success-50 dark:bg-success-500/10 text-success-500 border-success-100 dark:border-success-500/20">
                                            إيراد
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 text-xs font-black rounded-lg border bg-danger-50 dark:bg-danger-500/10 text-danger-500 border-danger-100 dark:border-danger-500/20">
                                            مصروف
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    @if($category->code)
                                        <code class="px-2 py-1 text-xs font-mono bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded">{{ $category->code }}</code>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span class="px-3 py-1.5 text-xs font-black rounded-lg border border-purple-100 bg-purple-50 text-purple-500 dark:bg-purple-500/10 dark:border-purple-500/20">
                                        {{ $category->transactions()->count() }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <form method="POST" action="{{ route('transaction-categories.update', $category) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="toggle_status" value="1">
                                        <button type="submit" class="px-3 py-1.5 text-xs font-black rounded-lg border transition-all {{ $category->is_active ? 'bg-success-50 dark:bg-success-500/10 text-success-500 border-success-100 dark:border-success-500/20 hover:bg-success-100' : 'bg-gray-50 dark:bg-gray-800 text-gray-500 border-gray-200 dark:border-gray-700 hover:bg-gray-100' }}">
                                            {{ $category->is_active ? 'نشط' : 'معطل' }}
                                        </button>
                                    </form>
                                </td>

                                <td class="px-6 py-5 text-center border-l last:rounded-l-2xl border-y dark:border-gray-800/50">
                                    <form method="POST" action="{{ route('transaction-categories.destroy', $category) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="button" 
                                            onclick="if(confirm('هل أنت متأكد من حذف هذه الفئة؟')) { this.closest('form').submit(); }"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-danger-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-danger-400"
                                            title="حذف"
                                            {{ $category->transactions()->count() > 0 ? 'disabled' : '' }}
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-20 italic text-center text-gray-400">
                                    لا توجد فئات مسجلة حالياً..
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
            document.addEventListener('DOMContentLoaded', function() {
                window.dispatchEvent(new CustomEvent('open-error-modal', {
                    detail: {
                        message: '{{ session('error') }}'
                    }
                }));
            });
        </script>
    @endif
@endsection
