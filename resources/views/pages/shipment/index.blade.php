@extends('layouts.app')
@section('title', 'قائمة الطرود')
@section('Breadcrumb', 'قائمة الطرود')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div class="space-y-6 font-outfit" dir="rtl">
        <div
            class="flex flex-col md:flex-row  justify-between bg-white dark:bg-white/[0.03] p-6 rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm gap-4">

            <div class="flex items-start gap-4">
                <div
                    class="w-12 h-12 bg-brand-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-brand-500/30 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white leading-tight">جميع الطرود</h2>
                    <p class="text-theme-xs text-gray-500 font-bold uppercase tracking-widest">إدارة الشحنات والعمليات
                        اللوجستية</p>
                </div>
            </div>

            <div class="w-full md:w-auto">
                <a href="{{ route('shipment.create') }}"
                    class="h-12 px-6 flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white rounded-xl transition-all shadow-lg shadow-brand-500/20 active:scale-95 text-sm font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    تسجيل طرد جديد
                </a>
            </div>
        </div>

        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="overflow-x-auto px-4 pb-4">
                <table class="w-full border-separate border-spacing-y-3 text-right">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="py-4 px-6">رقم السند</th>
                            <th class="py-4 px-6">الأطراف (مرسل/مستلم)</th>
                            <th class="py-4 px-6 text-center">خط السير (الوجهة)</th>
                            <th class="py-4 px-6 text-center">النوع وحالة الدفع</th>
                            <th class="py-4 px-6 text-center">حالة الطرد</th>
                            <th class="py-4 px-6 text-left">التكلفة</th>
                            <th class="py-4 px-6 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse ($requests as $request)
                            <tr
                                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-md transition-all group border border-transparent hover:border-gray-100 dark:hover:border-gray-800">
                                <td class="py-5 px-6 first:rounded-r-2xl border-y border-r dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1.5 bg-gray-50 dark:bg-gray-800 rounded-lg text-xs font-black text-brand-500 border border-gray-100 dark:border-gray-700 shadow-inner">
                                        #{{ $request->bond_number }}
                                    </span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-400">من:</span>
                                                <span
                                                    class="text-sm font-black text-gray-900 dark:text-white">{{ $request->senderCustomer->name ?? '-' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs font-bold text-gray-400">إلى:</span>
                                                <span
                                                    class="text-sm font-black text-gray-600 dark:text-gray-400">{{ $request->receiverCustomer->name ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <div
                                        class="inline-flex items-center gap-2 bg-gray-50 dark:bg-gray-800 px-3 py-1 rounded-full border border-gray-100 dark:border-gray-700 text-[10px] font-black uppercase text-gray-500">
                                        <span>{{ $request->senderBranch->name ?? '-' }}</span>
                                        <svg class="w-3 h-3 text-brand-500 rotate-180" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="3" />
                                        </svg>
                                        <span>{{ $request->receiverBranch->name ?? '-' }}</span>
                                    </div>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $request->package_type }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase {{ $request->payment_method == 'prepaid' ? 'bg-success-50 text-success-600' : 'bg-warning-50 text-warning-600' }}">
                                            {{ $request->payment_method == 'prepaid' ? 'مدفوع مقدماً' : 'آجل (عند الاستلام)' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-center">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-warning-500 text-white shadow-warning-500/20',
                                            'in_transit' => 'bg-blue-light-500 text-white shadow-blue-500/20',
                                            'delivered' => 'bg-success-500 text-white shadow-success-500/20',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'قيد الانتظار',
                                            'in_transit' => 'في الطريق',
                                            'delivered' => 'تم التسليم',
                                        ];
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-lg text-[10px] font-black uppercase shadow-lg {{ $statusClasses[$request->status] ?? 'bg-gray-500' }}">
                                        {{ $statusLabels[$request->status] ?? $request->status }}
                                    </span>
                                </td>

                                <td class="py-5 px-6 border-y dark:border-gray-800/50 text-left">
                                    <span class="text-base font-black text-gray-900 dark:text-white">
                                        {{ number_format($request->total_amount, 2) }}
                                        <small class="text-[10px] font-bold text-gray-400 mr-0.5">ر.ي</small>
                                    </span>
                                </td>

                                <td
                                    class="py-5 px-6 last:rounded-l-2xl border-y border-l dark:border-gray-800/50 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('shipment.show', $request->id) }}"
                                            class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 rounded-xl transition-all"
                                            title="التفاصيل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('shipment.invoice', $request->id) }}" target="_blank"
                                            class="p-2 text-gray-400 hover:text-success-500 hover:bg-success-50 rounded-xl transition-all"
                                            title="طباعة الفاتورة">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('shipment.edit', $request->id) }}"
                                            class="p-2 text-gray-400 hover:text-warning-500 hover:bg-warning-50 rounded-xl transition-all"
                                            title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-32 text-center">
                                    <div
                                        class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-gray-900 text-gray-300 mb-4 border-2 border-dashed border-gray-200 dark:border-gray-800">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-800 dark:text-white uppercase tracking-widest">
                                        السجل نظيف</h3>
                                    <p class="text-gray-400 mt-1 font-medium text-sm text-center">لا توجد طرود مسجلة في
                                        قاعدة البيانات حالياً.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
                <div class="p-8 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-800">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 bg-gray-950/40 backdrop-blur-2xl hidden items-center justify-center z-[9999]">
        <div
            class="p-8 m-4 w-full max-w-sm bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-2xl transition-all scale-100">
            <div class="text-center space-y-4">
                <div
                    class="w-20 h-20 bg-error-50 dark:bg-error-500/10 rounded-[2rem] flex items-center justify-center mx-auto text-error-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white leading-none">حذف الطرد؟</h3>
                <p class="text-sm font-bold text-gray-500 leading-relaxed italic px-4" id="deleteMessage"></p>

                {{-- <div class="flex flex-col gap-2 pt-4">
                    <form id="deleteForm" method="POST" class="w-full">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-full h-12 bg-error-500 hover:bg-error-600 text-white font-black rounded-2xl shadow-lg shadow-error-500/20 transition-all">تأكيد
                            الحذف النهائي</button>
                    </form>
                    <button type="button" onclick="hideDeleteModal()"
                        class="w-full h-12 bg-gray-50 dark:bg-gray-800 text-gray-500 font-black rounded-2xl hover:bg-gray-100 transition-all">إغلاق</button>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
