@extends('layouts.app')
@section('title', 'حسابات العملاء')
@section('Breadcrumb', 'حسابات العملاء')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">حسابات العملاء</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة مديونيات ومستحقات العملاء.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="bg-primary-50 dark:bg-primary-900/20 px-4 py-2 rounded-lg border border-primary-100 dark:border-primary-800 shadow-sm">
                    <span class="text-sm text-primary-600 dark:text-primary-300 font-medium ml-2">صافي المديونية:</span>
                    <span dir="ltr" class="text-lg font-bold text-primary-700 dark:text-primary-400">
                        {{ number_format($totalReceivables ?? 0, 2) }}
                    </span>
                    <span class="text-xs text-primary-600 dark:text-primary-400 mr-1">ر.ي</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm">
            <form method="GET" action="{{ route('finance.customers.index') }}" class="relative">
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           class="block w-full pr-10 pl-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white dark:bg-gray-800 dark:border-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-gray-900 dark:text-white" 
                           placeholder="بحث باسم العميل أو رقم الهاتف...">
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">العميل</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">رقم الهاتف</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">الرصيد الحالي</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-primary-600 font-bold text-xs">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                        <div class="mr-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <span dir="ltr">{{ $customer->phone }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if ($customer->balance > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                            عليه: {{ number_format($customer->balance, 2) }}
                                        </span>
                                    @elseif($customer->balance < 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            له: {{ number_format(abs($customer->balance), 2) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                            0.00
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('finance.customers.settle', $customer->id) }}" 
                                       class="text-primary-600 hover:text-primary-900 dark:hover:text-primary-400 font-bold transition">
                                       تسوية / سداد
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('finance.customers.show', $customer->id) }}" 
                                       class="text-gray-500 hover:text-gray-900 dark:hover:text-gray-400 font-bold transition">
                                       عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <p>لا توجد بيانات عملاء مطابقة.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
@endsection