@extends('layouts.app')
@section('title', 'سجل التعديلات')
@section('Breadcrumb', 'سجل التعديلات')

@section('content')

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">

    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-6 text-center">
        سجل التعديلات على الطرود
    </h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        #
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        العملية
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        تفاصيل
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        المستخدم
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        التاريخ
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        رقم الطرد
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-300">
                        {{ $loop->iteration }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-brand-600 dark:text-brand-400">
                        {{ $log->action }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-300">
                        {{ $log->description ?? 'لا يوجد وصف' }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-400">
                        {{ $log->admin?->name ?? 'نظام' }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $log->created_at->format('Y-m-d H:i') }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        @if($log->model_id)
                        <a href="{{ route('request.show', $log->model_id) }}"
                            class="text-success-600 hover:text-success-500 dark:text-success-400 dark:hover:text-success-300">
                            عرض الطرد
                        </a>
                        @else
                        <span class="text-gray-400">غير مرتبط</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        لا توجد سجلات حتى الآن.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($logs->hasPages())
    <div class="mt-6">
        {{ $logs->links() }}
    </div>
    @endif

</div>

@endsection
