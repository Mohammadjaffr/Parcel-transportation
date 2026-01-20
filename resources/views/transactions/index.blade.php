@extends('layouts.app')
@section('title', 'Cash Box - Transaction Management')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl">
        
        {{-- Balance Card --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            {{-- Current Balance --}}
            <div class="relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all shadow-theme-sm {{ $balance >= 0 ? 'border-success-200' : 'border-danger-200' }}">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl {{ $balance >= 0 ? 'bg-success-50 dark:bg-success-500/10 text-success-500' : 'bg-danger-50 dark:bg-danger-500/10 text-danger-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">الرصيد الحالي</span>
                    <h4 class="text-2xl font-black {{ $balance >= 0 ? 'text-success-500' : 'text-danger-500' }}">
                        {{ number_format($balance, 2) }} YER
                    </h4>
                </div>
            </div>

            {{-- Total Income --}}
            <div class="relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 transition-all shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي الإيرادات</span>
                    <h4 class="text-xl font-black text-success-500">{{ number_format($income, 2) }} YER</h4>
                </div>
            </div>

            {{-- Total Expenses --}}
            <div class="relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 transition-all shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 text-danger-500 bg-danger-50 rounded-xl dark:bg-danger-500/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي المصروفات</span>
                    <h4 class="text-xl font-black text-danger-500">{{ number_format($expense, 2) }} YER</h4>
                </div>
            </div>
        </div>

        {{-- Actions Bar --}}
        <div class="flex justify-between items-center bg-white dark:bg-white/[0.03] p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm">
            <h2 class="text-xl font-black text-gray-900 dark:text-white">قائمة المعاملات</h2>
            <div class="flex gap-3">
                <a href="{{ route('closings.create') }}" class="flex gap-2 justify-center items-center px-8 h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-purple-500 hover:bg-purple-600 shadow-purple-500/20 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    إقفال يومي
                </a>
                <a href="{{ route('transactions.create') }}" class="flex gap-2 justify-center items-center px-8 h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/20 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    إضافة معاملة جديدة
                </a>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">التاريخ</th>
                            <th class="px-6 py-4">الفئة</th>
                            <th class="px-6 py-4 text-center">النوع</th>
                            <th class="px-6 py-4 text-center">المبلغ</th>
                            <th class="px-6 py-4">الوصف</th>
                            <th class="px-6 py-4">المستخدم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse ($transactions as $transaction)
                            <tr class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">
                                
                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <span class="text-xs font-black text-gray-400">{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</span>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $transaction->created_at->format('Y-m-d') }}</span>
                                        <span class="mt-0.5 text-xs text-gray-400">{{ $transaction->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <span class="text-sm font-black text-gray-900 dark:text-white">{{ $transaction->category->name }}</span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    @if($transaction->category->type === 'in')
                                        <span class="px-3 py-1.5 text-xs font-black rounded-lg border bg-success-50 dark:bg-success-500/10 text-success-500 border-success-100 dark:border-success-500/20">
                                            إيراد
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 text-xs font-black rounded-lg border bg-danger-50 dark:bg-danger-500/10 text-danger-500 border-danger-100 dark:border-danger-500/20">
                                            مصروف
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span class="text-lg font-black {{ $transaction->category->type === 'in' ? 'text-success-500' : 'text-danger-500' }}">
                                        {{ $transaction->category->type === 'in' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->description ?? '—' }}</span>
                                </td>

                                <td class="px-6 py-5 border-l last:rounded-l-2xl border-y dark:border-gray-800/50">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $transaction->user->name }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-20 italic text-center text-gray-400">
                                    لا توجد معاملات مسجلة حالياً..
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="p-8 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.dispatchEvent(new CustomEvent('open-success-modal', {
                    detail: {
                        message: '{{ session('success') }}'
                    }
                }));
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.dispatchEvent(new CustomEvent('open-error-modal', {
                    detail: {
                        message: '{{ session('error') }}'
                    }
                }));
            });
        </script>
    @endif
@endsection
