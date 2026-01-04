@extends('layouts.app')
@section('title', 'حسابات العملاء')
@section('Breadcrumb', 'حسابات العملاء')

@section('content')
    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">حسابات العملاء</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">متابعة مديونيات ومستحقات العملاء.</p>
            </div>
            <div
                class="bg-primary-50 dark:bg-primary-900/20 px-4 py-2 rounded-lg border border-primary-100 dark:border-primary-800">
                <span class="text-sm text-primary-600 dark:text-primary-300 font-medium">إجمالي المديونية:</span>
                <span
                    class="text-lg font-bold text-primary-700 dark:text-primary-400 mr-2">{{ number_format($totalReceivables ?? 0, 2) }}
                    ر.ي</span>
            </div>
        </div>

        <div
            class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">العميل
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">رقم الهاتف
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">الرصيد
                                الحالي</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ $customer->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $customer->phone }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if ($customer->balance > 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                            عليه {{ number_format($customer->balance, 2) }}
                                        </span>
                                    @elseif($customer->balance < 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            له {{ number_format(abs($customer->balance), 2) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                            --
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('finance.customers.settle', $customer->id) }}"
                                        class="text-primary-600 hover:text-primary-900 text-sm font-medium">تسوية</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    لا يوجد عملاء عليهم مديونيات حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
@endsection
