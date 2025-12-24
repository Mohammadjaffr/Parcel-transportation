@extends('layouts.app')
@section('title', isset($customer) ? 'تعديل عميل' : 'إضافة عميل جديد')
@section('Breadcrumb', isset($customer) ? 'تعديل عميل' : 'إضافة عميل جديد')
@section('content')

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
        <form method="POST"
              action="{{  route('customers.store') }}"
              class="space-y-6">
            
            @csrf
            {{-- @isset($customer) @method('PUT') @endisset --}}

            <!-- الشبكة الرئيسية: عمود واحد في الموبايل وعمودين في الشاشات الأكبر -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                
                <!-- المعلومات الأساسية -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">المعلومات الأساسية</h3>

                    <!-- اسم العميل -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">اسم العميل</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="أدخل اسم العميل">
                        <div class="text-sm text-error-600 mt-1">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                    <!-- رقم الهاتف -->
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">رقم الهاتف</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="أدخل رقم الهاتف">
                        <div class="text-sm text-error-600 mt-1">
                            @error('phone')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- المعلومات الإضافية -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-400">المعلومات الإضافية</h3>

                    <!-- رقم الواتساب -->
                 <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">رقم الواتساب</label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $customer->whatsapp_number ?? '') }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
                            placeholder="أدخل رقم الواتساب">
                        <div class="text-sm text-error-600 mt-1">
                            @error('whatsapp_number')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <!-- زر الحفظ -->
            <div class="mt-8 pt-6  border-gray-200 dark:border-gray-700">
                <div class="flex justify-start gap-4">
                    <a href="{{ route('customers.index') }}"
                        class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit"
                        class="bg-brand-500 hover:bg-brand-600 text-white font-medium px-6 py-2.5 rounded-lg transition-colors w-full">
                        {{ isset($customer) ? 'تحديث العميل' : 'إضافة العميل' }}
                    </button>
                </div>
            </div>

        </form>
    </div>

@endsection