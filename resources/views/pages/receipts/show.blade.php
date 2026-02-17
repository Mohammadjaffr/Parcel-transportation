@extends('layouts.app')
@section('title', 'تفاصيل بيان الاستلام')
@section('Breadcrumb', 'تفاصيل بيان الاستلام #' . $receipt->number)
@section('addButton')
    <div class="flex gap-3">
        <button @click="$dispatch('open-add-item-modal')" type="button"
            class="flex gap-2 justify-center items-center px-4 h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/20 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            إضافة طرد
        </button>
    </div>
    <x-modals.success-modal />
    <x-modals.error-modal />
@endsection

@section('content')
    <div class="space-y-6 font-outfit" dir="rtl"
        x-data="{ filter: 'all', showAddItemModal: false, showEditItemModal: false, editingItem: null, showDeleteModal: false, deleteUrl: '' }"
        @open-add-item-modal.window="showAddItemModal = true">
        {{-- =================== Statistics Cards =================== --}}
        <div class="flex gap-6">
            {{-- بيانات الشحنة --}}
            <div
                class="flex-1 relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 dark:border-gray-800 transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="mt-3 space-y-1.5 w-full">
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-0.5">
                        <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                            المكتب </span>
                        <h4 class="text-xl font-black text-brand-500">{{ $receipt->sourceBranch->name ?? '—' }}</h4>
                    </div>
                </div>
            </div>

            {{-- إجمالي الطرود --}}
            <div @click="filter = 'all'"
                :class="filter === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        إجمالي الطرود</span>
                    <h4 class="text-xl font-black text-brand-500">{{ $receipt->items->count() }}</h4>
                </div>
            </div>

            {{-- الطرود المسلّمة --}}
            <div @click="filter = 'delivered'"
                :class="filter === 'delivered' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        تم التسليم</span>
                    <h4 class="text-xl font-black text-success-500">
                        {{ $receipt->items->where('is_delivered', true)->count() }}
                    </h4>
                </div>
            </div>

            {{-- الطرود الغير مسلّمة --}}
            <div @click="filter = 'pending'"
                :class="filter === 'pending' ? 'border-gray-500 ring-2 ring-gray-500/20' : 'border-gray-100 dark:border-gray-800'"
                class="flex-1 relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">
                        لم يسلم</span>
                    <h4 class="text-xl font-black text-gray-500">
                        {{ $receipt->items->where('is_delivered', false)->count() }}
                    </h4>
                </div>
            </div>
        </div>


        {{-- =================== Items Table =================== --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div
                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">تفاصيل الطرود</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        المسلّمة:
                        <span
                            class="font-bold text-success-500">{{ $receipt->items->where('is_delivered', true)->count() }}</span>
                        من
                        <span class="font-bold text-brand-500">{{ $receipt->items->count() }}</span>
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px]">
                    <thead class="bg-gray-50/80 dark:bg-gray-900/50">
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4 text-right">رقم السند</th>
                            <th class="px-6 py-4 text-right">المرسل</th>
                            <th class="px-6 py-4 text-right">المستلم</th>
                            <th class="px-6 py-4 text-right">رقم الهاتف</th>
                            <th class="px-6 py-4 text-center">نوع الطرد</th>
                            <th class="px-6 py-4 text-right">ملاحظات</th>
                            <th class="px-6 py-4 text-center">حالة التسليم</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($receipt->items as $item)
                            <tr x-show="filter === 'all' || (filter === 'delivered' && {{ $item->is_delivered ? 'true' : 'false' }}) || (filter === 'pending' && {{ $item->is_delivered ? 'false' : 'true' }})"
                                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="transition-colors hover:bg-gray-50/50 dark:hover:bg-white/[0.02] {{ $item->is_delivered ? 'bg-success-50/30 dark:bg-success-500/5' : '' }}">
                                {{-- رقم السند --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1.5 text-xs font-black bg-gray-50 rounded-lg border border-gray-100 shadow-inner dark:bg-gray-800 text-brand-500 dark:border-gray-700">
                                        #{{ $item->number }}
                                    </span>
                                </td>

                                {{-- المرسل --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->sender_name ?? '—' }}</span>
                                </td>

                                {{-- المستلم --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->receiver_name }}</span>
                                </td>

                                {{-- رقم الهاتف --}}
                                <td class="px-6 py-4">
                                    <x-phone-number :value="$item->receiver_phone ?? '—'"
                                        class="text-sm text-gray-500 dark:text-gray-400" />
                                </td>

                                {{-- نوع الطرد --}}
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex px-3 py-1 text-xs font-bold rounded-full bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                        {{ $item->package_type }}
                                    </span>
                                </td>

                                {{-- ملاحظات --}}
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $item->item_notes ?? '—' }}</span>
                                </td>

                                {{-- حالة التسليم --}}
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('receipt-items.toggle-delivery', $item->id) }}"
                                        class="inline-block" x-data="{ loading: false }" @submit="loading = true">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" :disabled="loading"
                                            class="inline-flex gap-2 items-center px-4 py-2 text-xs font-bold rounded-full border-2 transition-all duration-200"
                                            :class="loading ? 'opacity-70 cursor-not-allowed bg-gray-100 text-gray-400' : '{{ $item->is_delivered ? 'bg-success-50 text-success-600 border-success-200 hover:bg-success-100 dark:bg-success-500/10 dark:text-success-400 dark:border-success-500/30 dark:hover:bg-success-500/20' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700' }}'">

                                            <svg x-show="loading" class="w-3 h-3 animate-spin"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>

                                            <span x-show="!loading"
                                                class="w-2 h-2 rounded-full {{ $item->is_delivered ? 'bg-success-500' : 'bg-gray-400' }}"></span>

                                            <span
                                                x-text="loading ? 'جاري التحويل...' : '{{ $item->is_delivered ? 'تم التسليم' : 'لم يسلم' }}'"></span>
                                        </button>
                                    </form>
                                </td>

                                {{-- الإجراءات --}}
                                <td class="px-6 py-4 text-center">
                                    <button @click="editingItem = {{ $item }}; showEditItemModal = true"
                                        class="p-2 text-gray-400 hover:text-brand-500 hover:bg-brand-50 rounded-lg transition-all"
                                        title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <button
                                        @click="deleteUrl = '{{ route('receipt-items.destroy', $item->id) }}'; showDeleteModal = true"
                                        class="p-2 text-gray-400 hover:text-error-500 hover:bg-error-50 rounded-lg transition-all"
                                        title="حذف">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-12 h-12 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <span class="text-sm italic">لا توجد طرود في هذا البيان</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal: إضافة طرد --}}
        @include('pages.receipts.modals.add-item')

        {{-- Modal: تعديل طرد --}}
        @include('pages.receipts.modals.edit-item')

        {{-- Modal: تأكيد الحذف --}}
        @include('components.modals.confirm-delete')
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Simple toast notification
                const toast = document.createElement('div');
                toast.className =
                    'fixed top-4 left-1/2 -translate-x-1/2 z-50 px-6 py-3 bg-success-500 text-white font-bold rounded-xl shadow-lg text-sm transition-all duration-300';
                toast.textContent = '{{ session('success') }}';
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 2500);
            });
        </script>
    @endif
@endsection