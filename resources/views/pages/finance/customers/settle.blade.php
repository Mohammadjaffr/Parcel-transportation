@extends('layouts.app')
@section('title', 'تسوية حساب عميل')
@section('Breadcrumb', 'تسوية حساب عميل')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">تسوية حساب: {{ $customer->name }}</h2>
            </div>
            <a href="{{ route('finance.customers.index') }}"
                class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">
                &larr; عودة للقائمة
            </a>
        </div>

        <!-- بطاقة الرصيد -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">الرصيد الحالي</h3>
            @if ($balance > 0)
                <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                    عليه {{ number_format($balance, 2) }} ر.ي
                </div>
                <p class="text-xs text-gray-400 mt-1">المبلغ المطلوب من العميل</p>
            @elseif ($balance < 0)
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                    له {{ number_format(abs($balance), 2) }} ر.ي
                </div>
                <p class="text-xs text-gray-400 mt-1">المبلغ الفائض للعميل (رصيد دائن)</p>
            @else
                <div class="text-3xl font-bold text-gray-600 dark:text-gray-400">
                    0.00 ر.ي
                </div>
                <p class="text-xs text-gray-400 mt-1">الحساب مسوى بالكامل</p>
            @endif
        </div>

        <!-- نموذج التسوية -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <form action="{{ route('finance.customers.storeSettlement', $customer->id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">المبلغ المحصل /
                        المدفوع</label>
                    <input type="number" step="0.01" name="amount" value="{{ abs($balance) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">أدخل المبلغ الموجب دائماً. سيتم تسجيله كدفعة (Credit) تخفض من
                        المديونية.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الملاحظات</label>
                    <textarea name="notes" rows="3"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary-500 focus:border-primary-500">تسوية رصيد</textarea>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        تأكيد التسوية
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
