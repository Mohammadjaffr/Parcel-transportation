@extends('layouts.app')
@section('title', 'تعديل الفرع')
@section('Breadcrumb', 'تعديل الفرع')
@section('content')

<x-modals.success-modal />
<x-modals.error-modal />

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

    <form action="{{ route('branch.update', $branch->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6">

            <div class="space-y-4 w-full md:col-span-2">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات الفرع</h3>

                <!-- اسم الفرع -->
                <div class="mt-3">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">اسم الفرع</label>
                    <input type="text" name="name" value="{{ old('name', $branch->name) }}"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
                        bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
                        focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="اسم الفرع">
                    <div class="text-sm text-error-600 mt-1">
                        @error('name')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <!-- المنطقة -->
                <div class="mt-3">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">المنطقة</label>
                    <input type="text" name="region" value="{{ old('region', $branch->region) }}"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
                        bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
                        focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="المنطقة">
                    <div class="text-sm text-error-600 mt-1">
                        @error('region')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <!-- الهاتف -->
                <div class="mt-3">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
                        bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
                        focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="رقم هاتف الفرع">
                    <div class="text-sm text-error-600 mt-1">
                        @error('phone')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        <!-- الأزرار -->
        <div class="mt-6 flex flex-col md:flex-row gap-4">
            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto">
                تحديث الفرع
            </button>

            <a href="{{ route('branch.index') }}"
                class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600
                text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto text-center">
                رجوع للقائمة
            </a>
        </div>
    </form>

</div>

@endsection
