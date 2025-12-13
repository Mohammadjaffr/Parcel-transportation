@extends('layouts.app')
@section('title', 'العملاء')
@section('Breadcrumb', 'العملاء')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="space-y-6">

        <!-- العنوان والأزرار -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">العملاء</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    إدارة عملاء الفرع
                </p>
            </div>

            <a href="{{ route('customers.create') }}"
                class="bg-brand-500 hover:bg-brand-600 text-white font-medium px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                عميل جديد
            </a>
        </div>

        <!-- جدول العملاء -->
        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full text-sm text-right">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="px-5 py-4 font-semibold">العميل</th>
                            <th class="px-5 py-4 font-semibold">الهاتف</th>
                            <th class="px-5 py-4 font-semibold">النوع</th>
                            <th class="px-5 py-4 font-semibold">حد الائتمان</th>
                            <th class="px-5 py-4 font-semibold">الحالة</th>
                            <th class="px-5 py-4 font-semibold text-center">إجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($customers as $customer)
                            @php
                                $balance = ($customer->debit_sum ?? 0) - ($customer->cerrorit_sum ?? 0);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                                <!-- الاسم -->
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $customer->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $customer->type === 'company' ? 'شركة' : 'فرد' }}
                                    </div>
                                </td>

                                <!-- الهاتف -->
                                <td class="px-5 py-4 font-mono">{{ $customer->phone }}</td>

                                <!-- النوع -->
                                <td class="px-5 py-4">
                                    <span
                                        class="px-3 py-1 text-xs rounded-full
            {{ $customer->type === 'company' ? 'bg-brand-100 text-brand-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $customer->type === 'company' ? 'شركة' : 'فرد' }}
                                    </span>
                                </td>

                                <!-- الائتمان -->
                                <td class="px-5 py-4">
                                    {{ number_format($customer->cerrorit_limit ?? 0, 2) }} ر.س
                                </td>

                                <!-- الحالة -->
                                <td class="px-5 py-4">
                                    @if ($balance > 0)
                                        <span class="px-3 py-1 text-xs rounded-full bg-error-100 text-error-700">
                                            مدين {{ number_format($balance, 2) }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-success-100 text-success-700">
                                            دائن {{ number_format(abs($balance), 2) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- أزرار -->
                                <td class="px-5 py-4 text-center">
                                    <div class="flex justify-center gap-2">

                                        <a href="{{ route('customers.show', $customer->id) }}"
                                            class="px-3 py-1.5 rounded-lg text-xs bg-brand-100 text-brand-700 hover:bg-brand-200">
                                            كشف
                                        </a>

                                        <a href="{{ route('customers.edit', $customer->id) }}"
                                            class="px-3 py-1.5 rounded-lg text-xs bg-warning-100 text-warning-700 hover:bg-warning-200">
                                            تعديل
                                        </a>

                                        <form method="POST" action="{{ route('customers.destroy', $customer->id) }}">
                                            @csrf @method('DELETE')
                                            <button
                                                class="px-3 py-1.5 rounded-lg text-xs bg-error-100 text-error-500 hover:bg-error-200">
                                                حذف
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    لا يوجد عملاء بعد
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <!-- الترقيم -->
        @if ($customers->hasPages())
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                {{ $customers->links() }}
            </div>
        @endif

    </div>

@endsection
