@extends('layouts.app')
@section('title', 'إضافة سائق جديد')
@section('Breadcrumb', 'إضافة سائق')
@section('content')

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

    <form action="{{ route('drivers.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">اسم السائق</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                    placeholder="اسم السائق">
                @error('name')
                    <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">رقم الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                    placeholder="رقم الهاتف">
                @error('phone')
                    <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">المدينة</label>
                <input type="text" name="city" value="{{ old('city') }}"
                    class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                    placeholder="المدينة">
                @error('city')
                    <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">الحالة</label>
                <select name="status" class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm dark:bg-dark-900">
                    <option value="active">نشط</option>
                    <option value="inactive">متوقف</option>
                </select>
                @error('status')
                    <p class="text-sm text-error-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="mt-6">
            <button type="submit"
                class="bg-brand-500 hover:bg-brand-600 text-white font-medium py-2 px-4 rounded-lg w-full md:w-auto">
                حفظ السائق
            </button>
        </div>

    </form>
</div>

@endsection
