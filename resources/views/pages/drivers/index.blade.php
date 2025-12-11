@extends('layouts.app')
@section('title', 'السائقين')
@section('Breadcrumb', 'إدارة السائقين')
@section('content')
 <x-modals.success-modal />
    <x-modals.error-modal />
<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">قائمة السائقين</h3>

        <a href="{{ route('drivers.create') }}"
           class="bg-brand-500 hover:bg-brand-600 text-white text-sm py-2 px-4 rounded-lg">
            + إضافة سائق جديد
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        #
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        الاسم
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        الهاتف
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        المدينة
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        الحالة
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        عدد الطرود
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        الإجراءات
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($drivers as $driver)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $loop->iteration }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $driver->name }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $driver->phone }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $driver->city }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($driver->status == 'active')
                                <span class="rounded-full bg-success-50 px-2 py-0.5 text-success-600 text-xs font-medium">
                                    نشط
                                </span>
                            @else
                                <span class="rounded-full bg-error-50 px-2 py-0.5 text-error-600 text-xs font-medium">
                                    غير نشط
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $driver->shipments->count() }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex mx-5 space-x-reverse space-x-2">

                                <!-- زر التعديل -->
                                <a href="{{ route('drivers.edit', $driver->id) }}"
                                class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 mx-2"
                                title="تعديل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                <!-- زر عرض الطرود -->
                                <a href="{{ route('drivers.shipments', $driver->id) }}"
                                class="text-success-600 hover:text-success-900 dark:text-success-400"
                                title="عرض الطرود الخاصة بالسائق">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7"
                            class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            لا يوجد سائقين حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>

@endsection
