@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'معلومات السائق')
@section('content')

            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
              <div class="flex flex-col sm:flex-row gap-4 md:gap-6 flex-wrap mb-4">
     <div class="flex flex-col items-start justify-between rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03] transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
            <svg fill="#dc6803" width="20" height="20" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <path d="M26,26V4H18v6H12v6H6V26H2v2H30V26ZM8,26V18h4v8Zm6,0V12h4V26Zm6,0V6h4V26Z"></path>
            </svg>
        </div>
        <div class="mt-3 w-full">
            <span class="text-xs text-gray-500 dark:text-gray-400">إجمالي الطلبات</span>
            <h4 class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">50</h4>
        </div>
    </div>
  
    <div class=" flex  m:hidden flex-col items-start justify-between rounded-xl p-4  transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        
    </div>

    <div class="flex m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">

    </div>
  
    <div class="flex  m:hidden flex-col items-start justify-between rounded-xl transition hover:shadow-md flex-1 min-w-[150px] sm:min-w-[180px] lg:min-w-[200px]">
        
    </div>
              </div>
              <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
              <div class="mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
                
    
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between">
        
                  <div class="flex flex-col w-full xl:flex-row xl:justify-between">
                    <div class="flex gap-6">
                      <div class="relative w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800">
                    <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="user" class="object-cover w-full h-full" />
                </div>
                <div>
                    <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                        أحمد شرجبي (<span class="text-warning-500 dark:text-warning/90">نوها</span>)
                    </h4>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            +967780236552
                        </p>
                        <div class="h-3.5 w-px bg-gray-300 dark:bg-gray-700"></div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            عدن
                        </p>
                    </div>
                    <!-- التقييم -->
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex">
                            <svg class="w-4 h-4" fill="#dc6803" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-4 h-4" fill="#dc6803" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-4 h-4" fill="#dc6803" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-4 h-4" fill="#dc6803" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <svg class="w-4 h-4" fill="#dc6803" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">(4.8)</span>
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
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                رقم الطلب
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                العميل
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center col-span-2">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                السعر
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center col-span-2">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                حالة الطلب
              </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center col-span-2">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
              تاريخ الطلب
                </p>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 ml-1">
                          من
                    </p>
                </div>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <div class="flex items-center">
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 ml-1">
                        الى
                    </p>
                </div>
            </div>
          </th>
          <th class="py-3">
            <div class="flex items-center justify-center">
              <p
                class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"
              >
                الإجراءات
              </p>
            </div>
          </th>
        </tr>
      </thead>
      <!-- table header end -->

      <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
        <!-- 1 -->
        <tr>
            <td class="py-3">
                <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        1
                    </p>
                </div>
          </td>
          <td class="py-3">
            <div class="flex items-center">
              <div class="flex items-center gap-3">
                <div>
                  <p
                    class="font-medium text-gray-800 text-theme-sm dark:text-white/90"
                  >
                    عوض لشرم
                  </p>
                  <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                    +967780236551
                  </span>
                </div>
            </div>
          </td>
          <td class="py-3">
            <div class="flex items-center">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                9700
              </p>
            </div>
          </td>
        <td class="py-3">
            <div class="flex items-center">
              <p
                class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500"
              >
                ملغية
              </p>
            </div>
          </td>
          <td class="py-3">
            <div class="flex items-center">
              <p
                class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500"
              >
                2025/5/3
              </p>
            </div>
          </td>
          <td class="py-3">
            <div class="flex items-center justify-center space-x-2">
                <p class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
            المنصورة
                </p>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <p class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
            التواهي
                </p>
            </div>
        </td>
        <td class="py-3">
            <div class="flex items-center justify-center">
              <button class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                تفاصيل
              </button>
            </div>
          </td>
        </tr>

        <!-- 2 -->
        <tr>
            <td class="py-3">
                <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        2
                    </p>
                </div>
          </td>
          
          <td class="py-3">
            <div class="flex items-center">
              <div class="flex items-center gap-3">
                <div>
                  <p
                    class="font-medium text-gray-800 text-theme-sm dark:text-white/90"
                  >
                    عوض لشرم
                  </p>
                  <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                    +967780236551
                  </span>
                </div>
            </div>
          </td>
          <td class="py-3">
            <div class="flex items-center">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                8500
              </p>
            </div>
          </td>
        <td class="py-3">
            <div class="flex items-center">
              <p
                class="rounded-full bg-success-50 px-2 py-0.5 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500"
              >
                مكتملة
              </p>
            </div>
          </td>
                    <td class="py-3">
            <div class="flex items-center">
              <p
                class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500"
              >
                2025/5/3
              </p>
            </div>
          </td>
         <td class="py-3">
            <div class="flex items-center justify-center space-x-2">
                <p class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
            خورمكسر
                </p>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <p class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
            المعلا
                </p>
            </div>
        </td>
        <td class="py-3">
            <div class="flex items-center justify-center">
              <button class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                تفاصيل
              </button>
            </div>
          </td>
        </tr>

        <!-- 3 -->
        <tr>
            <td class="py-3">
                <div class="flex items-center">
                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                        3
                    </p>
                </div>
          </td>
          <td class="py-3">
            <div class="flex items-center">
              <div class="flex items-center gap-3">
                <div>
                  <p
                    class="font-medium text-gray-800 text-theme-sm dark:text-white/90"
                  >
                    عوض لشرم
                  </p>
                  <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                    +967780236551
                  </span>
                </div>
            </div>
          </td>
          <td class="py-3">
            <div class="flex items-center">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                10000
              </p>
            </div>
          </td>
        <td class="py-3">
            <div class="flex items-center">
              <p
                class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500"
              >
                بالانتظار
              </p>
            </div>
          </td>
              <td class="py-3">
            <div class="flex items-center">
              <p
                class="rounded-full bg-error-50 px-2 py-0.5 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500"
              >
                2025/5/3
              </p>
            </div>
          </td>
          <td class="py-3">
            <div class="flex items-center justify-center space-x-2">
                <p class="rounded-full bg-warning-50 px-2 py-0.5 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
            المنصورة
                </p>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <p class="rounded-full bg-blue-light-50 px-2 py-0.5 text-theme-xs font-medium text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500">
            عدن
                </p>
            </div>
        </td>
        <td class="py-3">
            <div class="flex items-center justify-center">
              <button class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-theme-xs font-medium text-gray-700 transition-all hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                تفاصيل
              </button>
            </div>
          </td>
        </tr>
        <!-- table body end -->
      </tbody>
    </table>
  </div>

              </div>
            </div>
            </div>
@endsection
