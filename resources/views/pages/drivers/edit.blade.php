@extends('layouts.app')
@section('title', 'تعديل بيانات السائق')
@section('Breadcrumb', 'تعديل سائق')
@section('content')

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

    <form action="{{ route('drivers.update', $driver->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- اسم السائق -->
            <div class="mt-3">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    اسم السائق
                </label>

                <input type="text" name="name" value="{{ old('name', $driver->name) }}"
                       class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                       placeholder="اكتب اسم السائق">

                @error('name')
                <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- رقم الهاتف -->
            <div class="mt-3">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    رقم الهاتف
                </label>

                <input type="text" name="phone" value="{{ old('phone', $driver->phone) }}"
                       class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                       placeholder="رقم الهاتف">
            </div>

            <!-- المدينة -->
            <div class="mt-3">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    المدينة
                </label>

                <input type="text" name="city" value="{{ old('city', $driver->city) }}"
                       class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                       placeholder="مدينة السائق">
            </div>

            <!-- الحالة -->
            <div class="mt-3">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">حالة السائق</label>

                <select name="status"
                        class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                    <option value="active"   {{ $driver->status == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ $driver->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>

                @error('status')
                <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <!-- زر حفظ التعديلات -->
        <div class="mt-6">
            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto">
                حفظ التغييرات
            </button>
        </div>

    </form>

</div>

@endsection
