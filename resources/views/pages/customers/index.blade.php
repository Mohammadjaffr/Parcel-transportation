@extends('layouts.app')
@section('title', 'إدارة العملاء')

@section('content')
<div class="p-4 md:p-6 lg:p-8 bg-[#F8F9FC] dark:bg-gray-950 min-h-screen font-outfit" dir="rtl" x-data="{ search: '' }">
    <div class="max-w-[1600px] mx-auto space-y-8">

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-end">
            <div class="lg:col-span-4 space-y-2">
                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">إدارة <span class="text-brand-500">العملاء</span></h2>
                <p class="text-gray-500 text-sm italic">مراقبة مديونيات العملاء والعمليات المالية</p>
            </div>

            <div class="lg:col-span-8 grid grid-cols-1 xl:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center text-brand-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">الإجمالي</p>
                        <h4 class="text-xl font-black dark:text-white">{{ $customers->total() }}</h4>
                    </div>
                </div>

                <div class="bg-white dark:bg-white/[0.03] p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-theme-sm flex items-center gap-4 border-r-4 border-r-error-500">
                    <div class="w-12 h-12 rounded-xl bg-error-50 dark:bg-error-500/10 flex items-center justify-center text-error-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-error-500 uppercase tracking-widest">المديونين</p>
                        <h4 class="text-xl font-black dark:text-white">{{ $customers->filter(fn($c) => ($c->debit_sum ?? 0) > ($c->credit_sum ?? 0))->count() }}</h4>
                    </div>
                </div>

              
            </div>
              <a href="{{ route('customers.create') }}" class="bg-brand-500 hover:bg-brand-600 text-white p-4 my-4 rounded-2xl shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2 transition-all active:scale-95 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                    إضافة عميل
                </a>
        </div>

        <div class="bg-white dark:bg-gray-700 rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            
            <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="relative w-full sm:w-96 group">
                    <input type="text" x-model="search" placeholder="ابحث بالاسم، الهاتف..." 
                           class="w-full h-12 pr-11 pl-4 rounded-xl border-none bg-gray-50 dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 transition-all text-sm font-medium dark:text-white">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 group-focus-within:text-brand-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
                <div class="text-theme-xs font-bold text-gray-400 uppercase tracking-widest">قائمة بيانات الفروع</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                            <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">العميل</th>
                            <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">بيانات التواصل</th>
                            <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">الفرع</th>
                            <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">الرصيد المالي</th>
                            <th class="px-6 py-5 text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">الحالة</th>
                            <th class="px-6 py-5 text-center text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($customers as $customer)
                        @php
                            $balance = ($customer->debit_sum ?? 0) - ($customer->credit_sum ?? 0);
                            $is_debtor = $balance > 0;
                        @endphp
                        <tr x-show="search === '' || '{{ strtolower($customer->name) }}'.includes(search.toLowerCase()) || '{{ $customer->phone }}'.includes(search)"
                            class="hover:bg-brand-50/30 dark:hover:bg-brand-500/5 transition-colors group">
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-brand-500 font-black text-sm border border-gray-200 dark:border-gray-700">
                                        {{ mb_substr($customer->name, 0, 1) }}
                                    </div>
                                    <div class="font-bold text-gray-900 dark:text-white group-hover:text-brand-600 transition-colors">{{ $customer->name }}</div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-mono font-bold text-gray-600 dark:text-gray-400">{{ $customer->phone }}</span>
                                    @if($customer->whatsapp_number)
                                        <span class="text-[10px] text-success-500 font-bold flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span> واتساب
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 text-[10px] font-black uppercase">
                                    {{ $customer->branch_code }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm font-black {{ $is_debtor ? 'text-error-600' : 'text-success-600' }}">
                                    {{ number_format(abs($balance), 2) }}
                                    <small class="text-[10px] mr-0.5">ر.ي</small>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $is_debtor ? 'bg-error-50 text-error-700 dark:bg-error-500/10' : 'bg-success-50 text-success-700 dark:bg-success-500/10' }}">
                                    {{ $is_debtor ? 'مديون' : 'خالص' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('customers.show', $customer->id) }}" class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 rounded-xl transition-all" title="كشف الحساب">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="p-2 text-gray-400 hover:text-warning-500 hover:bg-warning-50 dark:hover:bg-warning-500/10 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    {{-- <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" class="inline" onsubmit="return confirm('حذف العميل؟')">
                                        @csrf @method('DELETE')
                                        <button class="p-2 text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-20 text-center text-gray-400 font-medium italic">لا توجد بيانات مسجلة حالياً..</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    /* تحسينات إضافية للجداول لتناسب فخامة الكروت */
    table thead th { position: sticky; top: 0; z-index: 10; }
    .shadow-theme-sm { box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04); }
</style>
@endsection