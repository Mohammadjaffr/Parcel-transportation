@extends('layouts.app')
@section('title', 'دفتر الصندوق والتدفقات النقدية')

@section('content')
<x-modals.success-modal />
<x-modals.error-modal />

<div class="space-y-6 font-body" dir="rtl" x-data="cashLedgerHandler()">

    {{-- 1. بطاقات المؤشرات والإحصائيات اللحظية (KPI Cards) --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        
        {{-- كرت إجمالي الوارد --}}
        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-gray-400">إجمالي الوارد للفترة </span>
                <span class="flex justify-center items-center w-8 h-8 font-black text-emerald-600 bg-emerald-50 rounded-lg dark:bg-emerald-500/10">↓</span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-emerald-600">{{ number_format($totalIncome, 2) }}</span>
                <span class="mr-1 text-xs font-bold text-gray-400">ر.ي</span>
            </div>
        </div>

        {{-- كرت إجمالي المنصرف --}}
        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-gray-400">إجمالي المنصرف للفترة </span>
                <span class="flex justify-center items-center w-8 h-8 font-black text-rose-600 bg-rose-50 rounded-lg dark:bg-rose-500/10">↑</span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-rose-600">{{ number_format($totalExpense, 2) }}</span>
                <span class="mr-1 text-xs font-bold text-gray-400">ر.ي</span>
            </div>
        </div>

        {{-- كرت صافي حركة الفترة --}}
        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-gray-400">صافي الفترة (الفارق)</span>
                <span class="flex justify-center items-center w-8 h-8 text-gray-600 bg-gray-50 rounded-lg dark:bg-gray-800 dark:text-gray-300">⚖</span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black {{ $netPeriod >= 0 ? 'text-gray-900 dark:text-white' : 'text-rose-600' }}">
                    {{ number_format($netPeriod, 2) }}
                </span>
                <span class="mr-1 text-xs font-bold text-gray-400">ر.ي</span>
            </div>
        </div>

        {{-- كرت عدد الحركات --}}
        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-900 dark:border-gray-800">
            <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-gray-400">عدد العمليات</span>
                <span class="flex justify-center items-center w-8 h-8 font-bold text-blue-600 bg-blue-50 rounded-lg dark:bg-blue-500/10">#</span>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-gray-800 dark:text-gray-200">{{ number_format($totalCount) }}</span>
                <span class="mr-1 text-xs font-bold text-gray-400">عملية</span>
            </div>
        </div>
    </div>

    {{-- 2. شريط الفلترة والأزرار التشغيلية السريعة --}}
    <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-900 dark:border-gray-800">
        <form method="GET" action="{{ route('cash.ledger.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6">
            
            {{-- فلتر الفرع للمدراء فقط --}}
            @if(in_array(auth()->user()->type, ['admin', 'super_admin']) && $branches->isNotEmpty())
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-500">الفرع</label>
                    <select name="branch_id" class="w-full text-sm bg-gray-50 rounded-xl border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <option value="">كافة الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- من تاريخ --}}
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500">من تاريخ</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm bg-gray-50 rounded-xl border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            </div>

            {{-- إلى تاريخ --}}
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm bg-gray-50 rounded-xl border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            </div>

            {{-- نوع السند --}}
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500">نوع الحركة</label>
                <select name="type" class="w-full text-sm bg-gray-50 rounded-xl border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <option value="">الكل (وارد ومنصرف)</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>وارد فقط (🟢)</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>منصرف فقط (🔴)</option>
                </select>
            </div>

            {{-- التصنيف المالي --}}
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500">التصنيف المالي</label>
                <select name="cash_category_id" class="w-full text-sm bg-gray-50 rounded-xl border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <option value="">كافة التصنيفات</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('cash_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->type === 'income' ? 'وارد' : 'منصرف' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- طريقة الدفع --}}
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-500">طريقة الدفع</label>
                <select name="payment_method" class="w-full text-sm bg-gray-50 rounded-xl border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                    <option value="">الكل</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقداً</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>حوالة</option>
                </select>
            </div>

            {{-- حقل البحث والإجراءات --}}
            <div class="flex flex-wrap gap-2 items-end lg:col-span-6">
                <div class="flex-1 min-w-[240px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم السند، رقم الإيداع، أو البيان..." class="w-full text-sm bg-gray-50 rounded-xl border-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                </div>
                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-gray-900 rounded-xl transition-colors hover:bg-gray-800">
                    تصفية
                </button>
                <a href="{{ route('cash.ledger.index') }}" class="px-4 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                    إعادة ضبط
                </a>

                {{-- أزرار فتح النوافذ السريعة --}}
                <div class="flex gap-2 mr-auto">
                    <button type="button" @click="openCreateModal('income')" class="flex gap-1.5 items-center px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-xl shadow-sm transition-colors hover:bg-emerald-700">
                        <span>+</span> سند قبض 
                    </button>
                    <button type="button" @click="openCreateModal('expense')" class="flex gap-1.5 items-center px-4 py-2.5 text-sm font-bold text-white bg-rose-600 rounded-xl shadow-sm transition-colors hover:bg-rose-700">
                        <span>-</span> سند صرف 
                    </button>
                    <a href="{{ route('cash.ledger.export', request()->query()) }}" class="flex gap-1.5 items-center px-3 py-2 text-xs font-bold text-white bg-[#107c41] rounded-lg shadow-sm transition-all hover:bg-[#0c5e31] hover:scale-105">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.504 3.033L5.435 4.544C5.19 4.585 5 4.793 5 5.042v14.416c0 .249.19.457.435.498l9.069 1.511c.3.05.572-.18.572-.483V3.516c0-.303-.272-.533-.572-.483zM9.544 16.513l-1.34-3.136-1.42 3.136H5.4l2.128-4.316L5.514 8.013h1.411l1.24 3.018 1.34-3.018h1.34l-2.008 4.254 2.127 4.246h-1.42z"/>
                            <path d="M15.548 3.327v17.846h5.816c.35 0 .636-.286.636-.636V3.963c0-.35-.286-.636-.636-.636h-5.816zm2.348 11.233H16.64v-1.251h1.256v1.251zm0-2.484H16.64v-1.25h1.256v1.25zm0-2.484H16.64V8.342h1.256v1.25zm2.49 4.968h-1.256v-1.251h1.256v1.251zm0-2.484h-1.256v-1.25h1.256v1.25zm0-2.484h-1.256V8.342h1.256v1.25z"/>
                        </svg>
                        تصدير
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- 3. جدول دفتر الحركات اليومية --}}
    <div class="overflow-hidden bg-white rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-900 dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead class="font-bold text-gray-500 bg-gray-50 border-b border-gray-100 dark:bg-gray-800/50 dark:border-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="p-4">رقم السند</th>
                        <th class="p-4">التاريخ</th>
                        @if(in_array(auth()->user()->type, ['admin', 'super_admin']))
                            <th class="p-4">الفرع</th>
                        @endif
                        <th class="p-4">التصنيف / البيان</th>
                        <th class="p-4">طريقة الدفع</th>
                        <th class="p-4">الوارد </th>
                        <th class="p-4">المنصرف </th>
                        <th class="p-4">المسؤول</th>
                        <th class="p-4">ملاحظات</th>
                        <th class="p-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="font-medium divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($transactions as $trx)
                        <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                            <td class="p-4 font-mono font-bold text-gray-800 dark:text-gray-200">
                                {{ $trx->receipt_number }}
                            </td>
                            <td class="p-4 text-gray-500 whitespace-nowrap">
                                {{ $trx->transaction_date->format('Y-m-d') }}
                            </td>
                            @if(in_array(auth()->user()->type, ['admin', 'super_admin']))
                                <td class="p-4 text-gray-600 dark:text-gray-300">
                                    {{ $trx->branch->name ?? '-' }}
                                </td>
                            @endif
                            <td class="p-4">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $trx->category->name }}</span>
                                @if($trx->reference_number)
                                    <span class="block font-mono text-xs text-gray-400">مرجع: {{ $trx->reference_number }}</span>
                                @endif
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if($trx->payment_method === 'cash')
                                    <span class="px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-50 rounded-md dark:bg-amber-500/10 dark:text-amber-400">نقداً (كاش)</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-bold text-blue-700 bg-blue-50 rounded-md dark:bg-blue-500/10 dark:text-blue-400">تحويل بنكي</span>
                                @endif
                            </td>
                            <td class="p-4 font-bold text-emerald-600 whitespace-nowrap">
                                {{ $trx->type === 'income' ? number_format($trx->amount, 2) : '-' }}
                            </td>
                            <td class="p-4 font-bold text-rose-600 whitespace-nowrap">
                                {{ $trx->type === 'expense' ? number_format($trx->amount, 2) : '-' }}
                            </td>
                            <td class="p-4 text-gray-600 whitespace-nowrap dark:text-gray-400">
                                {{ $trx->user->name ?? '-' }}
                            </td>
                            <td class="p-4 text-xs text-gray-500 max-w-[200px] truncate">
                                {{ $trx->notes ?? '-' }}
                            </td>
                            <td class="p-4 text-center whitespace-nowrap">
                                <div class="flex gap-2 justify-center items-center">
                                    <button type="button" @click="viewDetails({{ $trx->id }})" class="px-3 py-1.5 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                                        تفاصيل
                                    </button>
                                    
                                    <a href="{{ route('cash.ledger.receipt', $trx->id) }}" 
                                       target="_blank" 
                                       title="طباعة السند"
                                       class="inline-flex gap-1.5 justify-center items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-primary bg-primary/10 hover:bg-primary hover:text-white dark:bg-primary/20 dark:text-primary dark:hover:text-white dark:hover:bg-primary">
                                       
                                        <span class="material-symbols-outlined text-[16px]">
                                            print
                                        </span>
                                        <span>طباعة</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ in_array(auth()->user()->type, ['admin', 'super_admin']) ? 10 : 9 }}" class="p-12 font-bold text-center text-gray-400">
                                لا توجد حركات مالية مطابقة للشروط المحددة خلال هذه الفترة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- الترقيم --}}
        @if($transactions->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    {{-- تضمين المودالات --}}
    @include('pages.finance.cash_ledger.modals.create-transaction-modal')
    @include('pages.finance.cash_ledger.modals.details-transaction-modal')

</div>

<script>
function cashLedgerHandler() {
    return {
        showCreateModal: false,
        showDetailsModal: false,
        createType: 'income',
        rawCategories: @js($categories),
        details: {},

        // حالة القائمة المنسدلة القابلة للبحث
        categoryDropdownOpen: false,
        categorySearchText: '',
        selectedCategoryId: '',
        selectedCategoryName: '',

        get availableCategories() {
            return this.rawCategories.filter(c => c.type === this.createType);
        },

        get filteredCategories() {
            if (!this.categorySearchText || this.categorySearchText.trim() === '') {
                return this.availableCategories;
            }
            const query = this.categorySearchText.trim().toLowerCase();
            return this.availableCategories.filter(c => c.name.toLowerCase().includes(query));
        },

        selectCategory(category) {
            this.selectedCategoryId = category.id;
            this.selectedCategoryName = category.name;
            this.categoryDropdownOpen = false;
            this.categorySearchText = '';
        },

        clearCategory() {
            this.selectedCategoryId = '';
            this.selectedCategoryName = '';
            this.categorySearchText = '';
        },

        toggleCategoryDropdown() {
            this.categoryDropdownOpen = !this.categoryDropdownOpen;
            if (this.categoryDropdownOpen) {
                this.categorySearchText = '';
                this.$nextTick(() => {
                    this.$refs.categorySearchInput?.focus();
                });
            }
        },

        openCreateModal(type) {
            this.createType = type;
            this.selectedCategoryId = '';
            this.selectedCategoryName = '';
            this.categorySearchText = '';
            this.categoryDropdownOpen = false;
            this.showCreateModal = true;
        },

        async viewDetails(id) {
            try {
                const response = await fetch(`{{ url('finance/cash-ledger') }}/${id}/details`);
                const res = await response.json();
                if (res.success) {
                    this.details = res.data;
                    this.showDetailsModal = true;
                }
            } catch (error) {
                alert('تعذر جلب تفاصيل السند.');
            }
        }
    }
}
</script>
@endsection