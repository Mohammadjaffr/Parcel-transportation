@extends('layouts.app')
@section('title', 'إضافة فرع جديد')
@section('Breadcrumb', 'إضافة فرع جديد')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

        <form action="{{ route('branch.store') }}" method="POST" x-data="{ isSubmitting: false }"
            @submit="isSubmitting = true">
            @csrf

            <!-- الشبكة الرئيسية -->
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-4">

                <!-- اسم الفرع -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">اسم الفرع</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
                bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
                focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="ادخل اسم الفرع">
                    @error('name')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- المنطقة -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">العنوان</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
                bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
                focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="ادخل العنوان">
                    @error('address')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- الهاتف -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">المدينه</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
                bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
                focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="ادخل المدينه">
                    @error('city')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
                bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
                focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="رقم هاتف الفرع">
                    @error('phone')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <!-- رمز الفرع -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">رمز الفرع</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300
                bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs
                focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                        placeholder="ادخل رمز الفرع">
                    @error('code')
                        <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <!-- زر الإضافة -->
            <div class="mt-6">
                <button type="submit" :disabled="isSubmitting"
                    class="flex justify-center items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto disabled:opacity-75 disabled:cursor-not-allowed">
                    <span x-show="!isSubmitting">إضافة الفرع</span>
                    <span x-show="isSubmitting" class="flex gap-2 items-center">
                        <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        جاري الإضافة...
                    </span>
                </button>
            </div>

        </form>

    </div>

@endsection