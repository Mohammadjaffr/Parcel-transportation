@extends('layouts.app')
@section('title', 'ملف العميل: ' . $customer->name)

@section('content')
    <div class="space-y-6">

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-2xl bg-primary-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-primary-600/20">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $customer->name }}</h2>
                        <div class="flex items-center gap-3 mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <span dir="ltr">{{ $customer->phone }}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span>الفرع: {{ $customer->branch_code }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('finance.customers.index') }}" 
                       class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        عودة للقائمة
                    </a>
                    <a href="{{ route('finance.customers.settle', $customer->id) }}" 
                       class="px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition shadow-md shadow-primary-600/20">
                        عمل تسوية / سداد
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border-r-4 border-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي ما أخذه (مسحوبات)</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                            {{ number_format($totalDebit, 2) }} <span class="text-xs text-gray-400">ر.ي</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border-r-4 border-green-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي ما دفعه (سداد)</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                            {{ number_format($totalCredit, 2) }} <span class="text-xs text-gray-400">ر.ي</span>
                        </h3>
                    </div>
                    <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border-r-4 {{ $currentBalance > 0 ? 'border-red-600' : ($currentBalance < 0 ? 'border-green-600' : 'border-gray-400') }}">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">صافي الرصيد الحالي</p>
                        <h3 class="text-2xl font-bold mt-1 {{ $currentBalance > 0 ? 'text-red-600' : ($currentBalance < 0 ? 'text-green-600' : 'text-gray-800') }}">
                            {{ number_format(abs($currentBalance), 2) }} <span class="text-xs text-gray-400">ر.ي</span>
                        </h3>
                        <p class="text-xs mt-1 font-bold {{ $currentBalance > 0 ? 'text-red-500' : ($currentBalance < 0 ? 'text-green-500' : 'text-gray-400') }}">
                            @if($currentBalance > 0)
                                متبقي عليه (مديون)
                            @elseif($currentBalance < 0)
                                له رصيد فائض (دائن)
                            @else
                                الحساب خالص (0)
                            @endif
                        </p>
                    </div>
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                <h3 class="font-bold text-gray-800 dark:text-white">سجل حركات الحساب</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">تاريخ العملية</th>
                            <th class="px-6 py-4">نوع العملية</th>
                            <th class="px-6 py-4">المبلغ</th>
                            <th class="px-6 py-4">البيان / التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $transaction->created_at->format('Y-m-d h:i A') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($transaction->type == 'debit')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                            عليه (مدين)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            له (دائن/سداد)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold {{ $transaction->type == 'debit' ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                                    {{ $transaction->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-gray-500">
                                    لا توجد حركات مسجلة لهذا العميل.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection