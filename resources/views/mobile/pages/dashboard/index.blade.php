@extends('mobile.layouts.app')

@section('title', 'الرئيسية - بساط')

@section('content')
<div class="space-y-6">
    
    <div class="flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm dark:bg-gray-800">
        <div>
            <h1 class="text-lg font-bold text-gray-800 dark:text-white">أهلاً بك، {{ auth()->user()->name ?? 'مستخدم بساط' }}!</h1>
            <p class="text-xs text-gray-500 italic">نظرة عامة على نشاطك اليوم</p>
        </div>
        <div class="bg-orange-100 p-2 rounded-full dark:bg-orange-900/30">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded-2xl shadow-sm border-b-4 border-blue-500 dark:bg-gray-800">
            <span class="text-xs text-gray-400 block mb-1">إجمالي الطرود</span>
            <span class="text-xl font-black text-gray-700 dark:text-white">128</span>
        </div>
        
        <div class="bg-white p-4 rounded-2xl shadow-sm border-b-4 border-green-500 dark:bg-gray-800">
            <span class="text-xs text-gray-400 block mb-1">تم التسليم</span>
            <span class="text-xl font-black text-gray-700 dark:text-white">94</span>
        </div>

        <div class="col-span-2 bg-gradient-to-r from-orange-500 to-orange-600 p-4 rounded-2xl shadow-lg text-white">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-sm opacity-80 block mb-1">الرصيد الحالي</span>
                    <span class="text-2xl font-bold">{{ number_format($systemBalance ?? 0, 2) }} ر.س</span>
                </div>
                <button class="bg-white/20 p-2 rounded-lg backdrop-blur-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden dark:bg-gray-800">
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h2 class="font-bold text-gray-800 dark:text-white">أحدث الشحنات</h2>
            <a href="#" class="text-xs text-orange-500 font-medium">عرض الكل</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">الرقم</th>
                        <th class="px-4 py-3 font-medium">الحالة</th>
                        <th class="px-4 py-3 font-medium">المبلغ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr>
                        <td class="px-4 py-4 text-gray-700 dark:text-gray-300">#BS-992</td>
                        <td class="px-4 py-4">
                            <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded text-[10px] dark:bg-blue-900/30">قيد التوصيل</span>
                        </td>
                        <td class="px-4 py-4 font-bold">45.00</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-4 text-gray-700 dark:text-gray-300">#BS-991</td>
                        <td class="px-4 py-4">
                            <span class="bg-green-100 text-green-600 px-2 py-1 rounded text-[10px] dark:bg-green-900/30">تم</span>
                        </td>
                        <td class="px-4 py-4 font-bold">120.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection