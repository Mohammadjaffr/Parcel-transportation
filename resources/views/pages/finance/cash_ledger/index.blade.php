@extends('layouts.app')
@section('title', 'دفتر الصندوق والتدفقات النقدية')

@section('content')


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
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>وارد فقط </option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>منصرف فقط </option>
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
                        <th class="p-4">اليوم والتاريخ</th>
                        <th class="p-4">رقم العملية</th>
                        @if(in_array(auth()->user()->type, ['admin', 'super_admin']))
                            <th class="p-4">الفرع</th>
                        @endif
                        <th class="p-4">نوع العمليه ورقمها</th>
                        <th class="p-4">طريقة الدفع</th>
                        <th class="p-4">الوارد </th>
                        <th class="p-4">المنصرف </th>
                        <th class="p-4">المسؤول</th>
                        <th class="p-4">السائق / الملاحظات</th>
                        <th class="p-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="font-medium divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($transactions as $trx)
                        <tr class="transition-colors hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                            <td class="p-4 whitespace-nowrap">
                                <span class="block font-bold text-gray-800 dark:text-gray-200">
                                    {{ $trx->transaction_date->locale('ar')->translatedFormat('l') }}
                                </span>
                                <span class="font-mono text-xs text-gray-400">
                                    {{ $trx->transaction_date->format('Y-m-d') }}
                                </span>
                            </td>
                            <td class="p-4 font-mono font-bold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                <span dir="ltr">{{ $trx->formatted_receipt_number }}</span>
                            </td>
                            @if(in_array(auth()->user()->type, ['admin', 'super_admin']))
                                <td class="p-4 text-gray-600 dark:text-gray-300">
                                    {{ $trx->branch->name ?? '-' }}
                                </td>
                            @endif
                            <td class="p-4">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $trx->category->name }}</span>
                                @if($trx->related_entity)
                                    @if($trx->related_entity['url'])
                                        <a href="{{ $trx->related_entity['url'] }}" target="_blank"
                                            class="inline-flex items-center gap-1 mt-1 font-mono text-xs font-bold text-primary hover:underline hover:text-primary-hover dark:text-primary-hover"
                                            title="عرض التفاصيل">
                                            <span class="material-symbols-outlined text-[14px]">
                                                {{ $trx->related_entity['type'] === 'trip' ? 'explore' : ($trx->related_entity['type'] === 'shipment' ? 'package_2' : 'tag') }}
                                            </span>
                                            <span>{{ $trx->related_entity['label'] }}</span>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 mt-1 font-mono text-xs font-bold text-gray-600 dark:text-gray-400">
                                            <span class="material-symbols-outlined text-[14px] text-gray-400">
                                                {{ $trx->related_entity['type'] === 'trip' ? 'explore' : ($trx->related_entity['type'] === 'shipment' ? 'package_2' : 'tag') }}
                                            </span>
                                            <span>{{ $trx->related_entity['label'] }}</span>
                                        </span>
                                    @endif
                                @elseif($trx->reference_number)
                                    <span class="block mt-1 font-mono text-xs text-gray-400">مرجع: {{ $trx->reference_number }}</span>
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
                            <td class="p-4">
                                @if($trx->driver_name)
                                    <div class="whitespace-nowrap">
                                        <span class="block font-bold text-gray-800 dark:text-gray-200">
                                            {{ $trx->driver_name }}
                                        </span>
                                        @if($trx->driver_phone)
                                            <span class="block font-mono text-xs text-gray-400 dark:text-gray-500">{{ $trx->driver_phone }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex gap-2 justify-center items-center">
                                    @if(is_null($trx->source_id) && is_null($trx->source_type))
                                        <button type="button" 
                                            @click="openEditModal(@js([
                                                'id' => $trx->id,
                                                'type' => $trx->type,
                                                'branch_id' => $trx->branch_id,
                                                'cash_category_id' => $trx->cash_category_id,
                                                'category_name' => $trx->category?->name,
                                                'driver_id' => $trx->driver_id,
                                                'driver_name' => $trx->driver_name,
                                                'driver_phone' => $trx->driver_phone,
                                                'amount' => (float) $trx->amount,
                                                'payment_method' => $trx->payment_method,
                                                'transaction_date' => $trx->transaction_date->format('Y-m-d'),
                                                'reference_number' => $trx->reference_number ?? '',
                                                'notes' => $trx->notes ?? '',
                                                'attachment_path' => $trx->attachment_path,
                                            ]))" 
                                            class="px-3 py-1.5 text-xs font-bold text-amber-700 bg-amber-50 rounded-lg transition-colors hover:bg-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:hover:bg-amber-500/20 inline-flex items-center gap-1"
                                            title="تعديل السند">
                                            <span class="material-symbols-outlined text-[16px]">edit</span>
                                            <span>تعديل</span>
                                        </button>
                                    @endif

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

                                    @php
                                        $whatsappFallback = \App\Services\WhatsApp\WhatsAppLinkService::generate($trx, 'cash_ledger');
                                    @endphp
                                    <button type="button" 
                                            onclick="sendCashReceiptDirect({{ $trx->id }}, '{{ $trx->driver_phone ?? '' }}', this, '{{ $whatsappFallback ?? '' }}')"
                                            title="إرسال السند عبر الواتساب"
                                            class="inline-flex gap-1.5 justify-center items-center px-3 py-1.5 text-xs font-bold rounded-lg transition-all text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white dark:bg-emerald-500/10 dark:text-emerald-400 dark:hover:bg-emerald-600 dark:hover:text-white">
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                        </svg>
                                        <span>واتساب</span>
                                    </button>
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
        rawDrivers: @js($drivers ?? []),
        details: {},

        // حالة وضع التعديل
        isEditMode: false,
        editId: null,
        formData: {
            branch_id: '{{ auth()->user()->branch_id }}',
            amount: '',
            payment_method: 'cash',
            transaction_date: '{{ now()->toDateString() }}',
            reference_number: '',
            notes: '',
            attachment_path: null
        },

        // حالة قائمة التصنيفات
        categoryDropdownOpen: false,
        categorySearchText: '',
        selectedCategoryId: '',
        selectedCategoryName: '',

        // حالة قائمة السائقين
        driverDropdownOpen: false,
        driverSearchText: '',
        selectedDriverId: '',
        selectedDriverName: '',
        selectedDriverPhone: '',

        get formActionUrl() {
            if (this.isEditMode && this.editId) {
                return `{{ url('finance/cash-ledger') }}/${this.editId}`;
            }
            return `{{ route('cash.ledger.store') }}`;
        },

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

        // إدارة السائقين
        get filteredDrivers() {
            if (!this.driverSearchText || this.driverSearchText.trim() === '') {
                return this.rawDrivers;
            }
            const query = this.driverSearchText.trim().toLowerCase();
            return this.rawDrivers.filter(d => 
                (d.name && d.name.toLowerCase().includes(query)) ||
                (d.phone && d.phone.includes(query))
            );
        },

        selectDriver(driver) {
            this.selectedDriverId = driver.id;
            this.selectedDriverName = driver.name;
            this.selectedDriverPhone = driver.phone || '';
            this.driverDropdownOpen = false;
            this.driverSearchText = '';
        },

        clearDriver() {
            this.selectedDriverId = '';
            this.selectedDriverName = '';
            this.selectedDriverPhone = '';
            this.driverSearchText = '';
        },

        toggleDriverDropdown() {
            this.driverDropdownOpen = !this.driverDropdownOpen;
            if (this.driverDropdownOpen) {
                this.driverSearchText = '';
                this.$nextTick(() => {
                    this.$refs.driverSearchInput?.focus();
                });
            }
        },

        openCreateModal(type) {
            this.isEditMode = false;
            this.editId = null;
            this.createType = type;
            this.formData = {
                branch_id: '{{ auth()->user()->branch_id }}',
                amount: '',
                payment_method: 'cash',
                transaction_date: '{{ now()->toDateString() }}',
                reference_number: '',
                notes: '',
                attachment_path: null
            };
            this.selectedCategoryId = '';
            this.selectedCategoryName = '';
            this.categorySearchText = '';
            this.categoryDropdownOpen = false;

            this.clearDriver();
            this.driverDropdownOpen = false;

            this.showCreateModal = true;
        },

        openEditModal(item) {
            this.isEditMode = true;
            this.editId = item.id;
            this.createType = item.type;
            this.formData = {
                branch_id: item.branch_id,
                amount: item.amount,
                payment_method: item.payment_method,
                transaction_date: item.transaction_date,
                reference_number: item.reference_number || '',
                notes: item.notes || '',
                attachment_path: item.attachment_path || null
            };
            this.selectedCategoryId = item.cash_category_id;
            this.selectedCategoryName = item.category_name || '';
            if (!this.selectedCategoryName && this.selectedCategoryId) {
                const found = this.rawCategories.find(c => c.id == this.selectedCategoryId);
                if (found) this.selectedCategoryName = found.name;
            }
            this.categorySearchText = '';
            this.categoryDropdownOpen = false;

            this.selectedDriverId = item.driver_id || '';
            this.selectedDriverName = item.driver_name || '';
            this.selectedDriverPhone = item.driver_phone || '';
            if (!this.selectedDriverName && this.selectedDriverId) {
                const foundDriver = this.rawDrivers.find(d => d.id == this.selectedDriverId);
                if (foundDriver) {
                    this.selectedDriverName = foundDriver.name;
                    this.selectedDriverPhone = foundDriver.phone || '';
                }
            }
            this.driverSearchText = '';
            this.driverDropdownOpen = false;

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

// دالة إرسال السند عبر الواتساب بنفس لوجك إرسال الشحنات
async function sendCashReceiptDirect(trxId, defaultPhone, btn, fallbackLink = '') {
    if (!trxId || btn.disabled) return;

    let phone = defaultPhone ? defaultPhone.trim() : '';
    if (!phone) {
        phone = prompt('يرجى إدخال رقم الواتساب لإرسال السند (مثال: 771234567):');
        if (!phone || phone.trim() === '') {
            return;
        }
    }

    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');
    btn.innerHTML = `
        <span class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
        <span>جاري الإرسال...</span>
    `;

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const url = "{{ route('whatsapp.cashLedger', ':id') }}".replace(':id', trxId);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ phone: phone.trim() })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                <span class="text-emerald-600">تم الإرسال بنجاح ✅</span>
            `;
            showNotificationToast(data.message || 'تم إرسال السند بنجاح عبر الواتساب ✅', 'success');
        } else if (data.fallback && data.whatsapp_link) {
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                <span class="text-amber-500">جاري فتح الواتساب...</span>
            `;
            showNotificationToast(data.message || 'خدمة الواتساب غير متصلة، جاري فتح الواتساب كرسالة...', 'warning');
            window.open(data.whatsapp_link, '_blank');
        } else {
            if (fallbackLink) {
                window.open(fallbackLink, '_blank');
            }
            btn.innerHTML = `
                <span class="material-symbols-outlined text-[16px] text-amber-500">open_in_new</span>
                <span class="text-amber-500">تم التحويل للواتساب</span>
            `;
            showNotificationToast(data.message || 'تم فتح الواتساب للإرسال اليدوي', 'warning');
        }
    } catch (error) {
        console.error('WhatsApp direct send error:', error);
        if (fallbackLink) {
            window.open(fallbackLink, '_blank');
            showNotificationToast('تعذر الاتصال بالسيرفر، تم فتح الواتساب كرسالة.', 'warning');
        } else {
            showNotificationToast('حدث خطأ في الاتصال بالخادم. يرجى المحاولة لاحقاً.', 'error');
        }
    } finally {
        setTimeout(() => {
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = originalHtml;
        }, 3500);
    }
}

// دالة التنبيهات العائمة
function showNotificationToast(message, type = 'success') {
    const existingToast = document.getElementById('direct-whatsapp-toast');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.id = 'direct-whatsapp-toast';
    let bgClass = 'bg-emerald-600 shadow-emerald-600/25';
    let icon = 'check_circle';

    if (type === 'error') {
        bgClass = 'bg-red-600 shadow-red-600/25';
        icon = 'error';
    } else if (type === 'warning') {
        bgClass = 'bg-amber-600 shadow-amber-600/25';
        icon = 'open_in_new';
    }

    toast.className = `fixed bottom-6 left-6 z-[99999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-white font-bold text-sm transition-all duration-300 transform translate-y-0 ${bgClass}`;

    toast.innerHTML = `
        <span class="material-symbols-outlined text-[20px]">${icon}</span>
        <span>${message}</span>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>
@endsection