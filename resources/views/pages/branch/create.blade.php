@extends('layouts.app')
@section('title', 'إضافة فرع جديد')
@section('Breadcrumb', 'إضافة فرع جديد')
@section('content')

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

        <form action="{{ route('branch.store') }}" method="POST">
            @csrf

            <!-- الشبكة الرئيسية -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

                <!-- اسم الفرع -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">اسم الفرع</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
            bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
            focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="ادخل اسم الفرع">
                    @error('name')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- المنطقة -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">المنطقة</label>
                    <input type="text" name="region" value="{{ old('region') }}"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
            bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
            focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="ادخل المنطقة">
                    @error('region')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الهاتف -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
            bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
            focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="رقم هاتف الفرع">
                    @error('phone')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <!-- زر الإضافة -->
            <div class="mt-6">
                <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto">
                    إضافة الفرع
                </button>
            </div>

        </form>

    </div>

@endsection
