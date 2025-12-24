@extends('layouts.app')
@section('title', 'كشف حساب عميل')
@section('Breadcrumb', 'كشف حساب عميل')
@section('content')

    <div class="space-y-6">

        <!-- رأس الصفحة -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">كشف حساب العميل</h2>
                <div class="flex items-center gap-4 mt-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">العميل:</span>
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $customer->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">الهاتف:</span>
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $customer->phone }}</span>
                    </div>
                    @if($customer->whatsapp_number)
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">رقم الواتساب:</span>
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $customer->whatsapp_number }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('customers.index') }}"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-700 
                           dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                    العودة للقائمة
                </a>
                <a href="{{ route('customers.edit', $customer->id) }}"
                    class="px-4 py-2 bg-warning-50 dark:bg-warning-900/30 text-warning-700 dark:text-warning-300 
                           border border-warning-200 dark:border-warning-800 rounded-lg hover:bg-warning-100 
                           dark:hover:bg-warning-900/50 transition-colors text-sm">
                    تعديل العميل
                </a>
            </div>
        </div>

        <!-- بطاقات الإحصائيات -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- عليه -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">عليه</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ number_format($debit, 2) }} <span class="text-sm">ر.ي</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-error-50 dark:bg-error-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-error-500 dark:text-error-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">إجمالي المديونيات</p>
            </div>

            <!-- دفع -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">دفع</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ number_format($credit, 2) }} <span class="text-sm">ر.ي</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-success-50 dark:bg-success-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-success-500 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">إجمالي المدفوعات</p>
            </div>

            <!-- الرصيد -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-sm 
                {{ $balance > 0 ? 'border-error-200 dark:border-error-800' : 'border-success-200 dark:border-success-800' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">الرصيد الحالي</p>
                        <p class="text-2xl font-bold {{ $balance > 0 ? 'text-error-500 dark:text-error-400' : 'text-success-500 dark:text-success-400' }}">
                            {{ number_format(abs($balance), 2) }} <span class="text-sm">ر.ي</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-full {{ $balance > 0 ? 'bg-error-50 dark:bg-error-900/20' : 'bg-success-50 dark:bg-success-900/20' }} 
                        flex items-center justify-center">
                        @if($balance > 0)
                            <svg class="w-6 h-6 text-error-500 dark:text-error-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-success-500 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                </div>
                <p class="text-xs {{ $balance > 0 ? 'text-error-500 dark:text-error-400' : 'text-success-500 dark:text-success-400' }} mt-3 font-medium">
                    {{ $balance > 0 ? 'مدين' : 'دائن' }}
                </p>
            </div>
        </div>

        <!-- حركات الحساب -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- رأس الجدول -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">حركات الحساب</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">سجل جميع المعاملات المالية للعميل</p>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        إجمالي النتائج: {{ $transactions->total() }}
                    </div>
                </div>
            </div>

            <!-- جدول الحركات -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">التاريخ</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">النوع</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">المبلغ</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">الوصف</th>
                            <th class="p-4 font-semibold text-gray-700 dark:text-gray-300">رقم المرجع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($transactions as $t)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <!-- التاريخ -->
                            <td class="p-4 text-gray-800 dark:text-gray-200">
                                <div class="font-medium">{{ $t->created_at->format('Y-m-d') }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $t->created_at->format('h:i A') }}</div>
                            </td>
                            
                            <!-- النوع -->
                            <td class="p-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $t->type === 'debit' 
                                        ? 'bg-error-100 text-error-800 dark:bg-error-900/30 dark:text-error-300' 
                                        : 'bg-success-100 text-success-800 dark:bg-success-900/30 dark:text-success-300' }}">
                                    {{ $t->type === 'debit' ? 'عليه' : 'دفع' }}
                                </span>
                            </td>
                            
                            <!-- المبلغ -->
                            <td class="p-4 font-medium {{ $t->type === 'debit' ? 'text-error-500 dark:text-error-400' : 'text-success-500 dark:text-success-400' }}">
                                {{ number_format($t->amount, 2) }} ر.ي
                            </td>
                            
                            <!-- الوصف -->
                            <td class="p-4 text-gray-800 dark:text-gray-200">
                                {{ $t->description ?? '—' }}
                            </td>
                            
                            <!-- رقم المرجع -->
                            <td class="p-4 text-gray-500 dark:text-gray-400 text-xs">
                                @if($t->reference_id)
                                    #{{ $t->reference_id }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                              d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">لا توجد حركات حسابية</p>
                                    <p class="text-sm">لم تتم أي معاملات مالية لهذا العميل بعد</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- الترقيم -->
        @if($transactions->hasPages())
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            {{ $transactions->links() }}
        </div>
        @endif

    </div>

@endsection