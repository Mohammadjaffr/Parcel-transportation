@extends('layouts.app')

@section('title', 'اختيار العميل')
@section('Breadcrumb', 'اختيار العميل')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">

    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-6">
        اختر العميل
    </h2>

    <form action="{{ route('shipments.createCustomer') }}" method="GET" class="space-y-6">

        {{-- اختيار العميل --}}
        <div>
            <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                العميل
            </label>
            <select name="customer_id" required
                class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-600
                       bg-white dark:bg-gray-900 px-4 text-sm
                       text-gray-700 dark:text-gray-200
                       focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                <option disabled selected>اختر العميل</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">
                        {{ $customer->name }} - {{ $customer->phone }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- دور العميل --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                دور العميل في الشحنة
            </label>

            <div class="flex gap-8">
                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    <input type="radio" name="role" value="sender" checked
                        class="text-brand-500 focus:ring-brand-500">
                    العميل مرسل
                </label>

                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                    <input type="radio" name="role" value="receiver"
                        class="text-brand-500 focus:ring-brand-500">
                    العميل مستلم
                </label>
            </div>
        </div>

        {{-- زر المتابعة --}}
        <div class="pt-4">
            <button type="submit"
                class="w-full bg-brand-500 hover:bg-brand-600
                       text-white font-medium py-2.5 rounded-lg">
                متابعة إلى إنشاء الطرد
            </button>
        </div>

    </form>

</div>
@endsection
