@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('Breadcrumb', 'معلومات المستخدم')
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
                <div>
                    <h4 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                        عوض لشرم  
                    </h4>
                    <div class="flex items-center gap-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            +967780236552
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
                السائق
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
                <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                  <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                </div>
                <div>
                  <p
                    class="font-medium text-gray-800 text-theme-sm dark:text-white/90"
                  >
                    أحمد شرجبي
                  </p>
                  <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                    +967780236552
                  </span>
                </div>
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
                <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                  <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                </div>
                <div>
                  <p
                    class="font-medium text-gray-800 text-theme-sm dark:text-white/90"
                  >
                    أحمد شرجبي
                  </p>
                  <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                    +967780236552
                  </span>
                </div>
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
                <div class="h-[50px] w-[50px] overflow-hidden rounded-md">
                  <img src="{{ asset('tailadmin/build/src/images/user/SO.jpg') }}" alt="Product" />
                </div>
                <div>
                  <p
                    class="font-medium text-gray-800 text-theme-sm dark:text-white/90"
                  >
                    أحمد شرجبي
                  </p>
                  <span class="text-gray-500 text-theme-xs dark:text-gray-400">
                    +967780236552
                  </span>
                </div>
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
