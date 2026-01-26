@extends('layouts.app')
@section('title', 'تعديل الفرع')
@section('Breadcrumb', 'تعديل الفرع')
@section('content')

    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800">

        <form action="{{ route('branch.update', $branch->code) }}" method="POST" x-data="{ isSubmitting: false }"
            @submit="isSubmitting = true">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-2">

                <div class="space-y-4 w-full md:col-span-2">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">بيانات الفرع</h3>

                    <!-- اسم الفرع -->
                    <div class="mt-3">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">اسم الفرع</label>
                        <input type="text" name="name" value="{{ old('name', $branch->name) }}" class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="اسم الفرع">
                        <div class="mt-1 text-sm text-error-600">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <!-- المدينه -->
                    <div class="mt-3">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">المدينه</label>
                        <input type="text" name="city" value="{{ old('city', $branch->city) }}" class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="ادخل المدينه">
                        <div class="mt-1 text-sm text-error-600">
                            @error('city')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <!-- الهاتف -->
                    <div class="mt-3">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">العنوان</label>
                        <input type="text" name="address" value="{{ old('address', $branch->address) }}" class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="ادخل العنوان">
                        <div class="mt-1 text-sm text-error-600">
                            @error('address')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">الهاتف</label>
                        <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="رقم هاتف الفرع">
                        <div class="mt-1 text-sm text-error-600">
                            @error('phone')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                    <!-- رمز الفرع -->
                    <div class="mt-3">
                        <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">رمز الفرع</label>
                        <input type="text" name="code" value="{{ old('code', $branch->code) }}" class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:ring-brand-500 focus:border-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="رمز الفرع">
                        <div class="mt-1 text-sm text-error-600">
                            @error('code')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <!-- الأزرار -->
            <div class="flex flex-col gap-4 mt-6 md:flex-row">
                <button type="submit" :disabled="isSubmitting"
                    class="flex gap-2 justify-center items-center px-4 py-2 w-full font-medium text-white rounded-lg bg-brand-500 hover:bg-brand-600 md:w-auto disabled:opacity-75 disabled:cursor-not-allowed">
                    <span x-show="!isSubmitting">تحديث الفرع</span>
                    <span x-show="isSubmitting" class="flex gap-2 items-center">
                        <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        جاري التحديث...
                    </span>
                </button>

                <a href="{{ route('branch.index') }}" class="px-4 py-2 w-full font-medium text-center text-gray-800 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white md:w-auto">
                    رجوع للقائمة
                </a>
            </div>
        </form>

    </div>

@endsection