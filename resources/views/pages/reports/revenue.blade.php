@extends('layouts.app')
@section('title', 'تقرير الإيرادات')
@section('Breadcrumb', 'تقرير الإيرادات')

@section('content')

<div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow-lg">
    <!-- رأس الصفحة مع زر التصدير -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
            تقرير الإيرادات
        </h1>
        
        <a href="{{ route('reports.revenue.pdf', request()->query()) }} " target="_blank"
           class="inline-flex items-center gap-2 bg-error-500 hover:bg-error-600 text-white px-5 py-3 rounded-lg transition duration-200 shadow hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            تصدير PDF
        </a>
    </div>

    <!-- بطاقة الفلترة -->
    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-xl mb-8 border border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">فلترة النتائج</h2>
        
        <form class="grid grid-cols-1 xl:grid-cols-1 gap-5">
{{--             
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">من تاريخ</label>
                <input type="date" name="from_date" value="{{ $request->from_date }}" 
                       class="w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ $request->to_date }}" 
                       class="w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
            </div> --}}

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الفرع</label>
                <select name="branch" 
                        class="w-full h-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                    <option value="">جميع الفروع</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch }}" {{ $branch == $request->branch ? 'selected':'' }}>
                            {{ $branch }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" 
                        class="w-full h-12 bg-brand-500 hover:bg-brand-600 text-white font-medium rounded-lg transition duration-200 shadow hover:shadow-md flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    بحث
                </button>
            </div>

        </form>
    </div>
    
    <!-- بطاقة ملخص الإيرادات -->
    <div class="bg-gradient-to-r from-brand-50 to-brand-100 dark:from-brand-900/20 dark:to-brand-800/20 p-5 rounded-xl mb-8 border border-brand-200 dark:border-brand-800">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">ملخص الإيرادات</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">للشحنات المدفوعة خلال الفترة المحددة</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="text-3xl font-bold text-success-600 dark:text-success-400">
                    {{ number_format($totalRevenue, 2) }} <span class="text-lg">ر.ي</span>
                </span>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 text-left sm:text-right">إجمالي الإيرادات</p>
            </div>
        </div>
    </div>

    <!-- جدول البيانات -->
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="py-4 px-6 text-right font-semibold text-gray-700 dark:text-gray-300">#</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-700 dark:text-gray-300">التاريخ</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-700 dark:text-gray-300">المرسل</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-700 dark:text-gray-300">المستلم</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-700 dark:text-gray-300">المبلغ</th>
                        <th class="py-4 px-6 text-right font-semibold text-gray-700 dark:text-gray-300">ملاحظات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($shipments as $shipment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-150">
                            <td class="py-4 px-6 text-gray-700 dark:text-gray-300">{{ $loop->iteration }}</td>
                            <td class="py-4 px-6">
                                <div class="text-gray-700 dark:text-gray-300">{{ $shipment->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $shipment->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="py-4 px-6 text-gray-700 dark:text-gray-300">{{ $shipment->sender_name }}</td>
                            <td class="py-4 px-6 text-gray-700 dark:text-gray-300">{{ $shipment->receiver_name }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400 font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ number_format($shipment->cod_amount, 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-gray-600 dark:text-gray-400">
                                @if($shipment->notes)
                                    <div class="max-w-xs truncate" title="{{ $shipment->notes }}">
                                        {{ $shipment->notes }}
                                    </div>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-lg font-medium">لا توجد بيانات</p>
                                    <p class="text-sm mt-1">لم يتم العثور على شحنات تطابق معايير البحث</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- الترقيم -->
    <div class="mt-6">
        {{ $shipments->links() }}
    </div>
</div>

@endsection