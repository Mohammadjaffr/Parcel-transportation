@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'معلومات المستخدم')
@section('content')

    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
            <div
                class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
                    </svg>
                </div>
                <div class="mt-3 w-full">
                    <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي المستخدمين</span>
                    <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $users->count() }}</h4>
                </div>
            </div>

            <div
                class=" flex  m:hidden flex-col items-start justify-between rounded-xl p-4  transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

            </div>

            <div
                class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

            </div>

            <div
                class="flex  m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

            </div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
            <div class="mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">


                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between">

                    <div class="flex flex-col w-full xl:flex-row xl:justify-between">
                        <div class="flex gap-6">
                            <div>
                                <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                                    {{ Auth()->user()->name }}
                                </h4>
                                <div class="flex items-center gap-3">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ Auth()->user()->phone }}
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        <span class="text-warning-500 dark:text-warning/90">الطلبات</span>
                    </h3>
                </div>
                <div class="w-full overflow-x-auto">
                    <table class="min-w-full">
                        <!-- table header start -->
                        <thead>
                            <tr class="border-gray-100 border-y dark:border-gray-800">
                                <th class="py-3">
                                    <div class="flex items-center">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            رقم المستخدم
                                        </p>
                                    </div>
                                </th>
                                <th class="py-3">
                                    <div class="flex items-center">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            المستخدم
                                        </p>
                                    </div>
                                </th>

                                <th class="py-3">
                                    <div class="flex items-center col-span-2">
                                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                            حالة المستخدم
                                        </p>
                                    </div>
                                </th>

                            </tr>
                        </thead>
                        <!-- table header end -->

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($users as $index => $u)
                                <!-- 1 -->
                                <tr>
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                                {{ $index + 1 }}
                                            </p>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center">
                                            <div class="flex items-center gap-3">
                                                <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                                                    <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}"
                                                        alt="Product" />
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                                        {{ $u->name }}
                                                    </p>
                                                    <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                                                        {{ $u->phone }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 sm:px-6">
                                        <div x-data="userToggle({{ $u->id }}, {{ $u->is_banned }})">
                                            <label
                                                class="flex cursor-pointer items-center gap-3 text-sm font-medium text-gray-700 select-none dark:text-gray-400">

                                                <div class="relative">
                                                    <input type="checkbox" class="sr-only" @change="toggle()">

                                                    <div class="block h-6 w-11 rounded-full transition-all duration-300"
                                                        :class="status ? 'bg-success-500' : 'bg-error-500'">
                                                    </div>

                                                    <div class="shadow-theme-sm absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear"
                                                        :class="status ? 'translate-x-full' : 'translate-x-0'">
                                                    </div>
                                                </div>

                                            </label>
                                        </div>
                                    </td>


                                </tr>
                            @endforeach


                            <!-- table body end -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    <script>
        function userToggle(userId, currentStatus) {
            return {
                status: currentStatus, // 1 أو 0

                async toggle() {
                    try {
                        const response = await fetch(`/users/toggle-status/${userId}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json",
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.status = this.status ? 0 : 1;
                        }

                    } catch (error) {
                        console.error("خطأ في التحديث", error);
                    }
                }
            }
        }
    </script>


@endsection
