@extends('layouts.app')
@section('title', 'كشف حساب: ' . $customer->name)

@section('content')
    <div class="p-4 md:p-6 lg:p-8 bg-[#F8F9FC] dark:bg-gray-950 min-h-screen font-outfit" dir="rtl">
        <div class="max-w-[1400px] mx-auto space-y-4">
            <div class="lg:col-span-8 grid grid-cols-1 xl:grid-cols-3 gap-4 my-4">
                <div
                    class="bg-white dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center text-brand-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">إجمالي ما عليه</p>
                        <h4 class="text-xl font-black dark:text-white"> {{ number_format($debit, 2) }}
                        </h4>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm flex items-center gap-4 border-r-4 border-r-error-500">
                    <div
                        class="w-12 h-12 rounded-xl bg-error-50 dark:bg-error-500/10 flex items-center justify-center text-error-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-error-500 uppercase tracking-widest">المديونين</p>
                        <h4 class="text-xl font-black dark:text-white"> {{ number_format($credit, 2) }}
                        </h4>
                    </div>
                </div>


            </div>
            <div
                class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-white dark:bg-white/[0.03] p-6 rounded-2xl border my-4 border-gray-100 dark:border-gray-800 shadow-theme-sm">
                <div class="flex items-center gap-5">
                    <div
                        class="w-16 h-16 bg-brand-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 text-2xl font-black">
                        {{ mb_substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">{{ $customer->name }}
                        </h2>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                            <span class="text-theme-xs font-bold text-gray-500 flex items-center gap-1">
                                <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $customer->phone }}
                            </span>
                            @if ($customer->whatsapp_number)
                                <span class="text-theme-xs font-bold text-green-500 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                    واتساب: {{ $customer->whatsapp_number }}
                                </span>
                            @endif
                            <span
                                class="text-[10px] px-2 py-0.5 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded-md font-black uppercase tracking-tighter">
                                فرع: {{ $customer->branch_code }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('customers.index') }}"
                        class="h-11 px-5 flex items-center justify-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm shadow-theme-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 12H5m7 7l-7-7 7-7" />
                        </svg>
                        العودة للعملاء
                    </a>
                    <a href="{{ route('customers.edit', $customer->id) }}"
                        class="h-11 px-5 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-bold rounded-xl transition-all shadow-lg shadow-brand-500/20 text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        تعديل البيانات
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">



                <div
                    class="group bg-white dark:bg-white/[0.03] p-1 rounded-2xl border   border-gray-100 dark:border-gray-800 shadow-theme-sm transition-all duration-500 hover:shadow-theme-md {{ $balance > 0 ? 'border-b-4 border-b-error-500' : 'border-b-4 border-b-success-500' }}">
                    <div
                        class="p-6 flex items-center justify-between bg-gradient-to-br {{ $balance > 0 ? 'from-error-50/30 dark:from-error-500/10' : 'from-success-50/30 dark:from-success-500/10' }} rounded-[2.3rem]">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">صافي الرصيد
                                ({{ $balance > 0 ? 'مدين' : 'دائن' }})</p>
                            <h3
                                class="text-3xl font-black {{ $balance > 0 ? 'text-error-600' : 'text-success-600' }} leading-none">
                                {{ number_format(abs($balance), 2) }}
                                <small
                                    class="text-xs font-bold mr-1 italic opacity-60 uppercase text-gray-400 ">ر.ي</small>
                            </h3>
                        </div>
                        <div
                            class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-theme-sm flex items-center justify-center {{ $balance > 0 ? 'text-error-500' : 'text-success-500' }}">
                            @if ($balance > 0)
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>

                <div
                    class="group bg-white dark:bg-white/[0.03] p-1 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm transition-all duration-500 hover:shadow-theme-md {{ $balance > 0 ? 'border-b-4 border-b-error-500' : 'border-b-4 border-b-success-500' }}">
                    <div
                        class="p-6 flex items-center justify-between bg-gradient-to-br {{ $balance > 0 ? 'from-error-50/30 dark:from-error-500/10' : 'from-success-50/30 dark:from-success-500/10' }} rounded-[2.3rem]">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">صافي الرصيد
                                ({{ $balance > 0 ? 'مدين' : 'دائن' }})</p>
                            <h3
                                class="text-3xl font-black {{ $balance > 0 ? 'text-error-600' : 'text-success-600' }} leading-none">
                                {{ number_format(abs($balance), 2) }}
                                <small
                                    class="text-xs font-bold mr-1 italic opacity-60 uppercase text-gray-400 ">ر.ي</small>
                            </h3>
                        </div>
                        <div
                            class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl shadow-theme-sm flex items-center justify-center {{ $balance > 0 ? 'text-error-500' : 'text-success-500' }}">
                            @if ($balance > 0)
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-white/[0.02] rounded-2xl border my-4 border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
                <div
                    class="p-8 border-b border-gray-50 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-8 bg-brand-500 rounded-full"></div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">سجل حركات
                            الحساب</h3>
                    </div>
                    <div
                        class="px-4 py-1.5 bg-gray-50 dark:bg-gray-900 rounded-full text-[11px] font-black text-gray-400 uppercase tracking-widest border border-gray-100 dark:border-gray-800">
                        عدد الحركات: {{ $transactions->total() }}
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">تاريخ
                                    العملية</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">نوع
                                    العملية</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">قيمة
                                    المبلغ</th>
                                <th class="px-8 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                                    التفاصيل / البيان</th>
                                <th
                                    class="px-8 py-5 text-center text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                                    المرجع</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($transactions as $t)
                                <tr
                                    class="group hover:bg-brand-50/30 dark:hover:bg-brand-500/5 transition-all duration-300">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-black text-gray-900 dark:text-white">{{ $t->created_at->format('Y-m-d') }}</span>
                                            <span
                                                class="text-[10px] font-bold text-gray-400 mt-0.5 tracking-wide">{{ $t->created_at->format('h:i A') }}</span>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-2 h-2 rounded-full {{ $t->type === 'debit' ? 'bg-error-500 shadow-[0_0_8px_rgba(240,68,56,0.5)]' : 'bg-success-500 shadow-[0_0_8px_rgba(18,183,106,0.5)]' }}">
                                            </div>
                                            <span
                                                class="text-xs font-black uppercase {{ $t->type === 'debit' ? 'text-error-600' : 'text-success-600' }}">
                                                {{ $t->type === 'debit' ? 'سحب (عليه)' : 'إيداع (دفع)' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-8 py-6">
                                        <span
                                            class="text-lg font-black {{ $t->type === 'debit' ? 'text-error-600' : 'text-success-600' }}">
                                            {{ $t->type === 'debit' ? '-' : '+' }}{{ number_format($t->amount, 2) }}
                                            <small class="text-[10px] font-bold opacity-50 mr-0.5">ر.ي</small>
                                        </span>
                                    </td>

                                    <td class="px-8 py-6">
                                        <p class="text-sm font-bold text-gray-600 dark:text-gray-400 line-clamp-1 max-w-[300px]"
                                            title="{{ $t->description }}">
                                            {{ $t->description ?? 'بدون بيان مالي..' }}
                                        </p>
                                    </td>

                                    <td class="px-8 py-6 text-center">
                                        <span
                                            class="px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg text-[10px] font-black text-gray-400 border border-gray-200 dark:border-gray-700 shadow-inner">
                                            {{ $t->reference_id ? '#' . $t->reference_id : '---' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-32 text-center group">
                                        <div class="relative inline-block">
                                            <div
                                                class="absolute inset-0 bg-brand-500/10 rounded-full blur-3xl animate-pulse">
                                            </div>
                                            <svg class="w-24 h-24 text-gray-200 dark:text-gray-800 relative z-10 mx-auto mb-4 group-hover:scale-110 transition-transform duration-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                            </svg>
                                        </div>
                                        <h3
                                            class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-widest">
                                            السجل فارغ</h3>
                                        <p class="text-gray-400 mt-2 font-medium">لا توجد حركات مالية مسجلة لهذا الحساب
                                            حالياً.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($transactions->hasPages())
                    <div class="p-8 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-50 dark:border-gray-800">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* تحسينات بصرية إضافية لمخطط الحسابات */
        .shadow-theme-sm {
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
        }

        .shadow-inner {
            box-shadow: inset 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        h2,
        h3,
        h4 {
            letter-spacing: -0.02em;
        }

        .font-black {
            font-weight: 900;
        }
    </style>
@endsection
