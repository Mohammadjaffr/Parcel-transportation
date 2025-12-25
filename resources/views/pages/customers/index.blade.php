@extends('layouts.app')
@section('title', 'العملاء')
@section('Breadcrumb', 'العملاء')
@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />
    <div class="space-y-6">

        <!-- العنوان والأزرار -->
        <div class="flex flex-col gap-4 justify-between items-start sm:flex-row sm:items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">العملاء</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    إدارة عملاء الفرع
                </p>
            </div>

            <a href="{{ route('customers.create') }}"
                class="flex gap-2 items-center px-4 py-2.5 font-medium text-white rounded-lg transition-colors bg-brand-500 hover:bg-brand-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                عميل جديد
            </a>
        </div>

        <!-- جدول العملاء -->
        <div
            class="overflow-hidden bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full text-sm text-right">
                    <thead class="bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-5 py-4 font-semibold">العميل</th>
                            <th class="px-5 py-4 font-semibold">الهاتف</th>
                            <th class="px-5 py-4 font-semibold">الفرع</th>
                            <th class="px-5 py-4 font-semibold">رقم الوتساب</th>
                            <th class="px-5 py-4 font-semibold text-center">إجراءات</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($customers as $customer)
                            @php
                                $balance = ($customer->debit_sum ?? 0) - ($customer->cerrorit_sum ?? 0);
                            @endphp
                            <tr class="transition hover:bg-gray-400 dark:hover:bg-gray-800/40">

                                <!-- الاسم -->
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">
                                        {{ $customer->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $customer->branch_code }}
                                    </div>
                                </td>

                                <!-- الهاتف -->
                                <td class="px-5 py-4 font-mono">
                                    <span class="dark:text-white"> {{ $customer->phone }}
                                    </span>

                                </td>

                                <!-- الفرع -->
                                <td class="px-5 py-4">
                                    <span
                                        class="px-3 py-1 text-xs rounded-full dark:text-white
            {{ $customer->branch_code === 'company' ? 'bg-brand-100 text-brand-700' : 'bg-brand-100 text-brand-400' }}">
                                        {{ $customer->branch_code }}
                                    </span>
                                </td>

                                <!-- رقم الوتساب -->
                                <td class="px-5 py-4">
                                    <span class="dark:text-white">
                                        {{ $customer->whatsapp_number }}

                                    </span>
                                </td>



                                <!-- أزرار -->
                                <td class="px-5 py-4 text-center">
                                    <div class="flex gap-2 justify-center">

                                        <a href="{{ route('customers.show', $customer->id) }}"
                                            class="px-3 py-1.5 text-xs rounded-lg bg-brand-100 text-brand-700 hover:bg-brand-200 dark:text-white">
                                            كشف
                                        </a>

                                        <a href="{{ route('customers.edit', $customer->id) }}"
                                            class="px-3 py-1.5 text-xs rounded-lg bg-warning-100 text-warning-700 hover:bg-warning-200">
                                            تعديل
                                        </a>

                                        <form method="POST" action="{{ route('customers.destroy', $customer->id) }}">
                                            @csrf @method('DELETE')
                                            <button
                                                class="px-3 py-1.5 text-xs rounded-lg bg-error-100 text-error-500 hover:bg-error-200">
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
            <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                {{ $customers->links() }}
            </div>
        @endif

    </div>

@endsection
