@extends('mobile.layouts.app')
@section('title', 'المالية والصندوق')

@section('content')
<x-modals.success-modal />
<x-modals.error-modal />

<div class="flex relative flex-col gap-6 px-4 pb-24 min-h-screen bg-slate-50/50" dir="rtl" x-data="cashLedgerHandler()">

    {{-- ================= 1. رأس الصفحة والعنوان ================= --}}
    <div class="flex justify-between items-center mt-6">
        <div class="flex flex-col">
            <h1 class="text-3xl font-black font-headline text-slate-800">دفتر الصندوق</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">
                إجمالي <span class="font-bold text-primary">{{ number_format($totalCount) }}</span> حركة مالية
            </p>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('cash.ledger.export', request()->query()) }}" class="w-12 h-12 bg-[#107c41] text-white rounded-[1rem] flex items-center justify-center shadow-[0_8px_20px_rgba(16,124,65,0.3)] active:scale-90 transition-transform shrink-0" title="تصدير">
                <span class="material-symbols-outlined text-[26px]">download</span>
            </a>
            <button @click="openCreateModal('income')" type="button" class="w-12 h-12 bg-emerald-500 text-white rounded-[1rem] flex items-center justify-center shadow-[0_8px_20px_rgba(16,185,129,0.3)] active:scale-90 transition-transform shrink-0">
                <span class="material-symbols-outlined text-[26px]">add</span>
            </button>
            <button @click="openCreateModal('expense')" type="button" class="w-12 h-12 bg-rose-500 text-white rounded-[1rem] flex items-center justify-center shadow-[0_8px_20px_rgba(244,63,94,0.3)] active:scale-90 transition-transform shrink-0">
                <span class="material-symbols-outlined text-[26px]">remove</span>
            </button>
        </div>
    </div>

    {{-- ================= 2. بطاقات الإحصائيات (Swipeable/Grid) ================= --}}
    <div class="grid grid-cols-2 gap-3 mt-2">
        <div class="bg-white rounded-[1.5rem] p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-emerald-50 rounded-full"></div>
            <div class="relative z-10 flex flex-col">
                <div class="flex items-center gap-1.5 text-emerald-500 mb-2">
                    <span class="material-symbols-outlined text-[16px]">south_west</span>
                    <span class="text-[11px] font-bold text-slate-500">الوارد</span>
                </div>
                <span class="text-xl font-headline font-black text-slate-800">{{ number_format($totalIncome, 2) }}</span>
            </div>
        </div>

        <div class="bg-white rounded-[1.5rem] p-4 border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-rose-50 rounded-full"></div>
            <div class="relative z-10 flex flex-col">
                <div class="flex items-center gap-1.5 text-rose-500 mb-2">
                    <span class="material-symbols-outlined text-[16px]">north_east</span>
                    <span class="text-[11px] font-bold text-slate-500">المنصرف</span>
                </div>
                <span class="text-xl font-headline font-black text-slate-800">{{ number_format($totalExpense, 2) }}</span>
            </div>
        </div>
        
        <div class="col-span-2 bg-slate-900 rounded-[1.5rem] p-4 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary/20 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div class="flex items-center gap-2 text-white/80 mb-1">
                    <span class="material-symbols-outlined text-[16px]">balance</span>
                    <span class="text-xs font-bold">الصافي للفترة</span>
                </div>
                <span class="text-2xl font-headline font-black {{ $netPeriod >= 0 ? 'text-white' : 'text-rose-400' }}">
                    {{ number_format($netPeriod, 2) }} <span class="text-xs text-slate-400 font-bold">ريال</span>
                </span>
            </div>
        </div>
    </div>

    {{-- ================= 3. الفلترة والبحث ================= --}}
    <form method="GET" action="{{ route('cash.ledger.index') }}" x-ref="filterForm" class="flex flex-col gap-3 mt-2">
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" x-model="searchQuery" placeholder="ابحث برقم السند، أو المرجع..."
                class="pr-4 pl-12 w-full h-14 text-sm bg-white rounded-2xl border-none shadow-[0_8px_30px_rgb(0,0,0,0.04)] outline-none focus:ring-2 focus:ring-primary/20 text-slate-700 placeholder-slate-400">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
        </div>

        <div x-data="{ showFilters: {{ request()->hasAny(['type', 'cash_category_id', 'start_date', 'end_date', 'branch_id', 'payment_method']) ? 'true' : 'false' }} }" class="flex flex-col gap-3">
            <button type="button" @click="showFilters = !showFilters" class="flex justify-between items-center px-4 h-12 bg-white rounded-2xl border border-slate-100 shadow-sm text-sm font-bold text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    فلاتر متقدمة
                </div>
                <span class="material-symbols-outlined transition-transform" :class="showFilters ? 'rotate-180' : ''">expand_more</span>
            </button>

            <div x-show="showFilters" x-collapse class="flex flex-col gap-3">
                @if(in_array(auth()->user()->type, ['admin', 'super_admin']) && isset($branches) && $branches->isNotEmpty())
                <select name="branch_id" class="w-full h-12 text-sm bg-white rounded-xl border border-slate-200 outline-none px-3 text-slate-700">
                    <option value="">كافة الفروع</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (isset($selectedBranchId) && $selectedBranchId == $branch->id) ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full h-12 text-xs bg-white rounded-xl border border-slate-200 outline-none px-3 text-slate-700" placeholder="من تاريخ">
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full h-12 text-xs bg-white rounded-xl border border-slate-200 outline-none px-3 text-slate-700" placeholder="إلى تاريخ">
                </div>
                
                <select name="type" class="w-full h-12 text-sm bg-white rounded-xl border border-slate-200 outline-none px-3 text-slate-700">
                    <option value="">الكل (وارد ومنصرف)</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>وارد فقط</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>منصرف فقط</option>
                </select>

                <select name="cash_category_id" class="w-full h-12 text-sm bg-white rounded-xl border border-slate-200 outline-none px-3 text-slate-700">
                    <option value="">كافة التصنيفات</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('cash_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->type === 'income' ? 'وارد' : 'منصرف' }})
                        </option>
                    @endforeach
                </select>

                <select name="payment_method" class="w-full h-12 text-sm bg-white rounded-xl border border-slate-200 outline-none px-3 text-slate-700">
                    <option value="">كافة طرق الدفع</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقداً</option>
                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>حوالة</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 h-12 bg-slate-800 text-white rounded-xl text-sm font-bold shadow-md">تطبيق</button>
                    <a href="{{ route('cash.ledger.index') }}" class="h-12 px-6 flex items-center justify-center bg-slate-100 text-slate-600 rounded-xl text-sm font-bold">إعادة ضبط</a>
                </div>
            </div>
        </div>
    </form>

    {{-- ================= 4. بطاقات الحركات المالية ================= --}}
    <div class="space-y-5 mt-2">
        @forelse($transactions as $trx)
            <div x-show="searchQuery === '' || '{{ $trx->receipt_number }}'.includes(searchQuery) || '{{ $trx->reference_number }}'.includes(searchQuery)"
                class="bg-white rounded-[24px] border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] overflow-visible transition-all duration-300 relative group">

                {{-- شريط لوني علوي خفيف --}}
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r {{ $trx->type === 'income' ? 'from-emerald-500/80 to-emerald-400/80' : 'from-rose-500/80 to-rose-400/80' }} rounded-t-[24px] opacity-70"></div>

                {{-- ================= الرأس (Header) ================= --}}
                <div class="flex justify-between items-start p-5">
                    <div class="flex gap-3 items-center">
                        <div class="w-11 h-11 rounded-[14px] {{ $trx->type === 'income' ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }} flex items-center justify-center border border-slate-100/80 group-hover:scale-105 transition-transform duration-300">
                            <span class="material-symbols-outlined text-[22px]">{{ $trx->type === 'income' ? 'south_west' : 'north_east' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-sm font-black tracking-tight text-slate-900 font-headline">
                                {{ $trx->receipt_number }}
                            </h3>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px]">schedule</span>
                                {{ $trx->transaction_date->format('Y/m/d') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2 items-center">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black {{ $trx->type === 'income' ? 'bg-emerald-50 text-emerald-600 ring-emerald-500/20' : 'bg-rose-50 text-rose-600 ring-rose-500/20' }} ring-1 ring-inset whitespace-nowrap max-w-[100px] truncate">
                            {{ $trx->category->name }}
                        </span>

                        {{-- قائمة الثلاث نقاط --}}
                        <div x-data="{ openMenu: false }" class="relative">
                            <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                class="flex justify-center items-center w-8 h-8 rounded-full transition-colors text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>

                            <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                class="absolute top-full left-0 mt-1.5 w-44 bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100/50 z-50 overflow-hidden py-1.5">

                                <button type="button" @click="viewDetails({{ $trx->id }}); openMenu = false"
                                    class="w-full flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary text-right">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    التفاصيل
                                </button>

                                <a href="{{ route('cash.ledger.receipt', $trx->id) }}" target="_blank"
                                    class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-slate-50 hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]">print</span>
                                    طباعة السند
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= الفاصل المقطّع ================= --}}
                <div class="flex overflow-hidden relative items-center h-4">
                    <div class="absolute -right-2 w-4 h-4 rounded-full border-l shadow-inner bg-slate-50/50 border-slate-200/60"></div>
                    <div class="w-full border-t-[1.5px] border-dashed border-slate-200/70"></div>
                    <div class="absolute -left-2 w-4 h-4 rounded-full border-r shadow-inner bg-slate-50/50 border-slate-200/60"></div>
                </div>

                {{-- ================= جسد البطاقة ================= --}}
                <div class="p-5 pt-4 space-y-5">
                    <div class="flex gap-4 justify-between items-start">
                        <div class="flex gap-3 items-stretch w-1/2">
                            <div class="flex flex-col flex-1 space-y-3">
                                @if(in_array(auth()->user()->type, ['admin', 'super_admin']))
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">الفرع</p>
                                    <p class="text-xs font-bold text-slate-800">{{ $trx->branch->name ?? '-' }}</p>
                                </div>
                                @endif
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">المسؤول</p>
                                    <p class="text-xs font-bold text-slate-800">{{ $trx->user->name ?? '-' }}</p>
                                </div>
                                @if($trx->reference_number)
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">المرجع</p>
                                    <p class="text-[10px] font-mono text-slate-800">{{ $trx->reference_number }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5 p-3 w-1/2 rounded-xl border bg-slate-50/70 border-slate-100/80 min-h-[80px]">
                            <div class="flex flex-col">
                                <span class="text-[9px] text-slate-400 font-bold mb-1">البيان / ملاحظات:</span>
                                <span class="text-[10px] font-bold text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ $trx->notes ?? 'لا يوجد' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- ================= كبسولة المالية ================= --}}
                    <div class="bg-slate-800 rounded-[18px] p-3.5 flex justify-between items-center shadow-lg shadow-slate-900/10">
                        <div class="flex gap-2.5 items-center">
                            <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-slate-700 text-slate-300">
                                <span class="material-symbols-outlined text-[18px]">{{ $trx->payment_method === 'cash' ? 'payments' : 'account_balance' }}</span>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-300 mb-0.5">طريقة الدفع</p>
                                <p class="text-[11px] font-bold text-white tracking-wide">
                                    {{ $trx->payment_method === 'cash' ? 'نقداً (كاش)' : 'تحويل بنكي' }}
                                </p>
                            </div>
                        </div>

                        <div class="pl-2 text-left">
                            <p class="text-[9px] font-bold text-slate-400 mb-0.5">المبلغ</p>
                            <p class="text-lg font-black tracking-tight leading-none {{ $trx->type === 'income' ? 'text-emerald-400' : 'text-rose-400' }} font-headline">
                                {{ number_format($trx->amount, 2) }} <span class="text-[10px] font-bold text-slate-300">ريال</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20 bg-white rounded-[24px] border-2 border-dashed border-slate-200/70 mt-4 shadow-sm">
                <div class="relative mb-4">
                    <div class="absolute inset-0 rounded-full blur-xl bg-primary/20"></div>
                    <div class="w-16 h-16 bg-gradient-to-br from-slate-50 to-slate-100 rounded-[18px] flex items-center justify-center border border-white shadow-sm relative z-10">
                        <span class="material-symbols-outlined text-[32px] text-slate-300">receipt_long</span>
                    </div>
                </div>
                <h3 class="text-sm font-black text-slate-700 font-headline">لا توجد حركات مالية</h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">لم نعثر على أي حركات تطابق بحثك حالياً.</p>
            </div>
        @endforelse

        @if(method_exists($transactions, 'hasPages') && $transactions->hasPages())
            <div class="mt-8">
                {{ $transactions->links('vendor.pagination.mobile') }}
            </div>
        @endif
    </div>

    {{-- تضمين المودالات --}}
    @include('mobile.pages.finance.cash_ledger.modals.create-transaction-modal')
    @include('mobile.pages.finance.cash_ledger.modals.details-transaction-modal')
</div>

<script>
function cashLedgerHandler() {
    return {
        searchQuery: '',
        showCreateModal: false,
        showDetailsModal: false,
        createType: 'income',
        rawCategories: @js($categories),
        details: {},

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