@extends('layouts.app')
@section('title', 'تقارير الشحنات')
@section('Breadcrumb', 'تقارير الشحنات')
@section('content')
    <style>
        @media print {

            body {
                direction: rtl;
                font-family: 'Tahoma', sans-serif;
            }

            /* إخفاء أي شيء غير التقرير */
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            table {
                border-collapse: collapse;
                width: 100%;
                font-size: 12px;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 6px;
                text-align: right;
            }

            h2 {
                margin-bottom: 10px;
            }
        }
    </style>


    <div class="space-y-6">
        <!-- إحصائيات سريعة -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">إجمالي الشحنات</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ number_format($totalShipments) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">إجمالي ألاجل</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ number_format($totalCod, 2) }} <span class="text-sm">ر.ي</span>
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full bg-success-50 dark:bg-success-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-success-500 dark:text-success-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">شحنات دفع مقدم</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ number_format($totalPrepaid) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- رأس الصفحة -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">تقارير الشحنات</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    تحليل وتتبع جميع الشحنات في النظام
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('reports.index') }}"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 
                           dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                    العودة للتقارير
                </a>
                <button onclick="window.print()" class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm print:hidden">
                    طباعة التقرير
                </button>

            </div>
        </div>

        {{-- <!-- فلترة البحث -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">فلترة النتائج</h3>
            
            <form method="GET" action="{{ route('reports.shipments') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- الفرع -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">الفرع</label>
                    <select name="branch_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">كل الفروع</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- تاريخ البداية -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">من تاريخ</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                
                <!-- تاريخ النهاية -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                
                <!-- طريقة الدفع -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">طريقة الدفع</label>
                    <select name="payment_method" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">الكل</option>
                        <option value="prepaid" {{ request('payment_method') == 'prepaid' ? 'selected' : '' }}>دفع مقدم</option>
                        <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>آجل</option>
                    </select>
                </div>
                
                <!-- أزرار -->
                <div class="md:col-span-4 flex justify-end gap-3 mt-2">
                    <a href="{{ route('reports.shipments') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        مسح الفلاتر
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-brand-500 hover:bg-brand-500 text-white rounded-lg">
                        تطبيق الفلتر
                    </button>
                </div>
            </form>
        </div> --}}



        <!-- جدول الشحنات -->
        <div
            class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">قائمة الشحنات</h3>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        عرض {{ $shipments->count() }} من {{ $totalShipments }}
                    </div>
                </div>
            </div>

            <div id="print-area">

                <h2 class="text-xl font-bold mb-4 text-center">
                    تقرير الشحنات
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right border border-gray-300">
                        <thead>
                            <tr>
                                <th class="p-2 border">الرمز</th>
                                <th class="p-2 border">المرسل</th>
                                <th class="p-2 border">المستلم</th>
                                <th class="p-2 border">الفرع</th>
                                <th class="p-2 border">طريقة الدفع</th>
                                <th class="p-2 border">المبلغ</th>
                                <th class="p-2 border">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shipments as $shipment)
                                <tr>
                                    <td class="p-2 border">#{{ $shipment->code }}</td>
                                    <td class="p-2 border">
                                        {{ $shipment->sender_name }}<br>
                                        {{ $shipment->sender_phone }}
                                    </td>
                                    <td class="p-2 border">
                                        {{ $shipment->receiver_name }}<br>
                                        {{ $shipment->receiver_phone }}
                                    </td>
                                    <td class="p-2 border">{{ $shipment->branch->name ?? '—' }}</td>
                                    <td class="p-2 border">
                                        {{ $shipment->payment_method == 'cod' ? 'آجل' : 'مقدم' }}
                                    </td>
                                    <td class="p-2 border">
                                        {{ $shipment->cod_amount > 0 ? number_format($shipment->cod_amount, 2) . ' ر.س' : '—' }}
                                    </td>
                                    <td class="p-2 border">
                                        {{ $shipment->created_at->format('Y-m-d') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center">
                                        لا توجد بيانات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- الترقيم -->
            @if ($shipments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $shipments->links() }}
                </div>
            @endif
        </div>

    </div>

@endsection
