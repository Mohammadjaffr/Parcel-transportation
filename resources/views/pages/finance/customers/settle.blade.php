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
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition">
                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
                عودة للقائمة
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 text-center">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">الرصيد الحالي</h3>
            
            @if ($balance > 0)
                <div class="text-4xl font-extrabold text-red-600 dark:text-red-500 tracking-tight">
                    {{ number_format($balance, 2) }} <span class="text-lg font-medium text-gray-400">ر.ي</span>
                </div>
                <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                    مديونية (عليه)
                </div>
            @elseif ($balance < 0)
                <div class="text-4xl font-extrabold text-green-600 dark:text-green-500 tracking-tight">
                    {{ number_format(abs($balance), 2) }} <span class="text-lg font-medium text-gray-400">ر.ي</span>
                </div>
                <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                    رصيد دائن (له)
                </div>
            @else
                <div class="text-4xl font-extrabold text-gray-400 tracking-tight">
                    0.00
                </div>
                <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                    خالص
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <form action="{{ route('finance.customers.storeSettlement', $customer->id) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">المبلغ المدفوع (للسداد)</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="number" step="0.01" name="amount" value="{{ $balance > 0 ? $balance : '' }}" required
                            class="block w-full pr-3 pl-12 py-3 border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 sm:text-lg font-bold text-gray-900 dark:bg-gray-900 dark:border-gray-600 dark:text-white"
                            placeholder="0.00">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">ر.ي</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5 mx-1">
                        سيتم تسجيل هذا المبلغ كعملية "دائن" (Credit) في كشف حساب العميل لإنقاص المديونية.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">ملاحظات / بيان العملية</label>
                    <textarea name="notes" rows="3"
                        class="block w-full p-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:ring-primary-500 focus:border-primary-500 dark:text-white sm:text-sm"
                        placeholder="مثال: دفعة نقدية، تحويل بنكي...">تسوية رصيد</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        تأكيد وحفظ التسوية
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection