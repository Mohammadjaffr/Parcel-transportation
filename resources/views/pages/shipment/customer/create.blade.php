@extends('layouts.app')

@section('title', 'إنشاء عميل جديد')
@section('Breadcrumb', 'إنشاء عميل جديد')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">

    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-6">
        بيانات العميل العام
    </h2>

    <form action="{{ route('shipments.storeCustomer') }}" method="POST" class="space-y-6">
        @csrf

        {{-- اسم العميل --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                اسم العميل
            </label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-900 px-4 text-sm
                       text-gray-700 dark:text-gray-200
                       focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                placeholder="اسم العميل">
            @error('name')
                <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- رقم الهاتف --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                رقم الهاتف
            </label>
            <input type="text" name="phone" value="{{ old('phone') }}" required
                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-900 px-4 text-sm
                       text-gray-700 dark:text-gray-200
                       focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                placeholder="رقم الهاتف">
            @error('phone')
                <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- دور العميل --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                دور العميل في الشحنة
            </label>
            <div class="flex gap-8">
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="role" value="sender" checked>
                    مرسل
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="radio" name="role" value="receiver">
                    مستلم
                </label>
            </div>
            @error('role')
                <div class="text-sm text-error-600 mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- زر الحفظ --}}
        <div class="pt-4">
            <button type="submit"
                class="w-full bg-brand-500 hover:bg-brand-600
                       text-white font-medium py-2.5 rounded-lg">
                حفظ والمتابعة لإنشاء الطرد
            </button>
        </div>

    </form>

</div>
@endsection
