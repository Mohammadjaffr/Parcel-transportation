@extends('layouts.app')
@section('title', 'الشحنات المستلمة')
@section('Breadcrumb', 'إدارة الرحلات والشحنات المستلمة')

@section('addButton')
    <a href="{{ route('receipts.create') }}"
        class="flex gap-2 justify-center items-center px-4 py-2.5 text-sm font-bold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95">
        <span class="material-symbols-outlined text-[20px]">add_box</span>
        إضافة بيان استلام
    </a>
@endsection

@section('content')

    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
        search: '',
        searchType: 'all',
        filterStatus: 'all',
        filterBranch: 'all',
        showRow(number, driver, branch, itemNumbers, deliveryStatus, branchCode) {
            if (this.filterStatus !== 'all' && deliveryStatus !== this.filterStatus) return false;
            if (this.filterBranch !== 'all' && branchCode != this.filterBranch) return false;
            const s = this.search.toLowerCase();
            if (!s) return true;
            if (this.searchType === 'receipt') {
                return number.toLowerCase().includes(s);
            } else if (this.searchType === 'item') {
                return itemNumbers.toLowerCase().includes(s);
            } else {
                return number.toLowerCase().includes(s) ||
                    driver.toLowerCase().includes(s) ||
                    branch.toLowerCase().includes(s) ||
                    itemNumbers.toLowerCase().includes(s);
            }
        }
    }">

        {{-- ===== بطاقات إحصائية ===== --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            {{-- إجمالي البيانات --}}
            <div @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'border-primary ring-2 ring-primary/20' : 'border-gray-100 hover:border-primary/50 dark:border-gray-800'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border transition-all cursor-pointer dark:bg-boxdark hover:shadow-md shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-primary/10 dark:bg-primary/20 text-primary">
                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي بيانات الاستلام</span>
                    <h4 class="mt-1 text-2xl font-black dark:text-white">{{ $totalReceipts ?? 0 }}</h4>
                </div>
            </div>

            {{-- إجمالي الطرود --}}
            <div class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border border-gray-100 transition-all dark:bg-boxdark dark:border-gray-800 hover:shadow-md shadow-theme-sm">
                <div class="flex justify-center items-center w-10 h-10 text-blue-500 bg-blue-50 rounded-xl dark:bg-blue-500/10">
                    <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">الطرود المستلمة</span>
                    <h4 class="mt-1 text-2xl font-black dark:text-white">{{ $totalItems ?? 0 }}</h4>
                </div>
            </div>

            {{-- شحنات مكتملة التسليم --}}
            <div @click="filterStatus = 'all_delivered'"
                :class="filterStatus === 'all_delivered' ? 'border-success-500 ring-2 ring-success-500/20' : 'border-gray-100 hover:border-success-300 dark:border-gray-800'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border border-r-4 transition-all cursor-pointer dark:bg-boxdark hover:shadow-md shadow-theme-sm border-r-success-500">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <span class="material-symbols-outlined text-[22px]">task_alt</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">مكتملة التسليم</span>
                    <h4 class="mt-1 text-2xl font-black dark:text-white">{{ $fullyDelivered ?? 0 }}</h4>
                </div>
            </div>

            {{-- شحنات بها طرود غير مسلمة --}}
            <div @click="filterStatus = 'has_pending'"
                :class="filterStatus === 'has_pending' ? 'border-warning-500 ring-2 ring-warning-500/20' : 'border-gray-100 hover:border-warning-300 dark:border-gray-800'"
                class="flex relative flex-col flex-1 justify-between items-start p-5 bg-white rounded-2xl border border-r-4 transition-all cursor-pointer dark:bg-boxdark hover:shadow-md shadow-theme-sm border-r-warning-500">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-warning-50 dark:bg-warning-500/10 text-warning-500">
                    <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                </div>
                <div class="mt-4">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">غير مكتملة</span>
                    <h4 class="mt-1 text-2xl font-black dark:text-white">{{ $hasPending ?? 0 }}</h4>
                </div>
            </div>
        </div>

        {{-- ===== الحاوية الرئيسية (بحث + جدول) ===== --}}
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800">
            
            {{-- شريط البحث والفلاتر --}}
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 dark:bg-gray-900/30 dark:border-gray-800">
                <div class="flex flex-col gap-3 md:flex-row">
                    
                    {{-- فلتر المكاتب --}}
                    <select x-model="filterBranch"
                        class="px-4 w-full h-12 text-sm font-bold text-gray-700 bg-white rounded-xl border border-gray-200 transition-all outline-none md:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="all">كل المكاتب المرسلة</option>
                        @foreach ($branches ?? [] as $branch)
                            <option value="{{ $branch->code }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>

                    {{-- نوع البحث --}}
                    <select x-model="searchType"
                        class="px-4 w-full h-12 text-sm font-bold text-gray-700 bg-white rounded-xl border border-gray-200 transition-all outline-none md:w-48 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="all">بحث عام</option>
                        <option value="receipt">برقم السند</option>
                        <option value="item">برقم الطرد</option>
                    </select>

                    {{-- مربع البحث --}}
                    <div class="relative flex-1 group">
                        <input type="text" x-model="search"
                            :placeholder="searchType === 'receipt' ? 'ابحث برقم سند الاستلام...' : (searchType === 'item' ? 'ابحث برقم الطرد...' : 'ابحث برقم السند، اسم السائق، المكتب...')"
                            class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-white rounded-xl border border-gray-200 transition-all outline-none dark:bg-gray-900 dark:border-gray-700 focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white">
                        <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                            <span class="material-symbols-outlined text-[22px]">search</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                @forelse($receipts as $receipt)
                    <div x-show="showRow('{{ $receipt->number }}', '{{ $receipt->driver->name ?? '' }}', '{{ $receipt->sourceBranch->name ?? '' }}', '{{ $receipt->items->pluck('number')->implode(',') }}', '{{ $receipt->items->count() > 0 && $receipt->items->every(fn($i) => $i->is_delivered) ? 'all_delivered' : 'has_pending' }}', '{{ $receipt->source_branch_code }}')"
                        x-transition
                        class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 transition-colors bg-gray-50/50 dark:bg-gray-800/50 dark:border-gray-800 hover:border-primary/30">

                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-white rounded-xl shadow-sm bg-primary">
                                    <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">#{{ $receipt->number }}</span>
                                    <span class="text-[11px] font-bold text-gray-500 mt-0.5">{{ $receipt->sourceBranch->name ?? '—' }}</span>
                                </div>
                            </div>
                            
                            <div class="flex gap-1 items-center">
                                <a href="{{ route('receipts.show', $receipt->id) }}" class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-primary dark:bg-gray-900 dark:border-gray-700">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                                <a href="{{ route('receipts.edit', $receipt->id) }}" class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-primary dark:bg-gray-900 dark:border-gray-700">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-3 mt-1 border-t border-gray-100 dark:border-gray-700/50">
                            <div class="flex gap-2 items-center">
                                @php
                                    $isDelivered = $receipt->items->count() > 0 && $receipt->items->every(fn($i) => $i->is_delivered);
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-black uppercase rounded-full {{ $isDelivered ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isDelivered ? 'bg-success-500' : 'bg-warning-500' }}"></span>
                                    {{ $isDelivered ? 'مكتمل' : 'غير مكتمل' }}
                                </span>
                                <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400">{{ $receipt->items->count() }} طرود</span>
                            </div>
                            <div class="flex items-center gap-1 text-[11px] font-medium text-gray-500">
                                <span class="material-symbols-outlined text-[14px]">directions_car</span>
                                {{ Str::limit($receipt->driver->name ?? '—', 15) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                        <span class="text-3xl text-gray-300 material-symbols-outlined dark:text-gray-600">inbox</span>
                        <p class="mt-2 text-sm font-bold text-gray-500">لا توجد بيانات استلام مسجلة</p>
                    </div>
                @endforelse
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-right">
                    <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] border-b border-gray-100 dark:border-gray-800">
                            <th class="px-6 py-4">رقم السند</th>
                            <th class="px-6 py-4">المكتب المرسل</th>
                            <th class="px-6 py-4">السائق المستلم</th>
                            <th class="px-6 py-4 text-center">حالة التسليم</th>
                            <th class="px-6 py-4 text-center">عدد الطرود</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                        @forelse($receipts as $receipt)
                            @php
                                $isDelivered = $receipt->items->count() > 0 && $receipt->items->every(fn($i) => $i->is_delivered);
                            @endphp
                            <tr x-show="showRow('{{ $receipt->number }}', '{{ $receipt->driver->name ?? '' }}', '{{ $receipt->sourceBranch->name ?? '' }}', '{{ $receipt->items->pluck('number')->implode(',') }}', '{{ $isDelivered ? 'all_delivered' : 'has_pending' }}', '{{ $receipt->source_branch_code }}')"
                                x-transition
                                class="bg-white transition-colors hover:bg-gray-50/50 dark:bg-transparent dark:hover:bg-gray-800/30 group">

                                {{-- رقم السند --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-xl transition-colors dark:bg-gray-800 group-hover:bg-primary/10 group-hover:text-primary">
                                            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                                        </div>
                                        <span class="text-sm font-black text-gray-900 dark:text-white">#{{ $receipt->number }}</span>
                                    </div>
                                </td>

                                {{-- المكتب المرسل --}}
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $receipt->sourceBranch->name ?? '—' }}</span>
                                </td>

                                {{-- السائق --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $receipt->driver->name ?? '—' }}</span>
                                        <span class="text-[11px] font-medium text-gray-500 dir-ltr text-right">{{ $receipt->driver->phone ?? '' }}</span>
                                    </div>
                                </td>

                                {{-- الحالة --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase rounded-lg {{ $isDelivered ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isDelivered ? 'bg-success-500' : 'bg-warning-500' }}"></span>
                                        {{ $isDelivered ? 'مكتمل' : 'غير مكتمل' }}
                                    </span>
                                </td>

                                {{-- عدد الطرود --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex justify-center items-center w-8 h-8 text-xs font-black text-gray-700 bg-gray-100 rounded-full dark:bg-gray-800 dark:text-gray-300">
                                        {{ $receipt->items->count() }}
                                    </span>
                                </td>

                                {{-- الإجراءات --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex gap-2 justify-center items-center opacity-0 transition-opacity group-hover:opacity-100">
                                        <a href="{{ route('receipts.show', $receipt->id) }}" title="التفاصيل"
                                            class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg transition-colors hover:text-primary hover:bg-primary/10 dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                        <a href="{{ route('receipts.edit', $receipt->id) }}" title="تعديل"
                                            class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg transition-colors hover:text-primary hover:bg-primary/10 dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col justify-center items-center">
                                        <div class="flex justify-center items-center mb-4 w-16 h-16 bg-gray-50 rounded-full dark:bg-gray-800/50">
                                            <span class="text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">inbox</span>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">لا توجد بيانات استلام</h4>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">لم يتم تسجيل أي بيانات استلام بعد.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= شريط الترقيم (Pagination) ================= --}}
            @if ($receipts->hasPages())
                <div class="flex justify-center items-center px-6 py-6 w-full border-t border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/20">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex gap-2 justify-center items-center">
                        
                        {{-- زر الصفحة السابقة --}}
                        @if ($receipts->onFirstPage())
                            <span class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-xl border border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_left</span>
                            </span>
                        @else
                            <a href="{{ $receipts->previousPageUrl() }}" class="flex justify-center items-center w-10 h-10 text-gray-600 bg-white rounded-xl border border-gray-200 shadow-sm transition-colors hover:bg-primary/5 hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_left</span>
                            </a>
                        @endif

                        {{-- أرقام الصفحات --}}
                        <div class="flex gap-1 items-center px-2 py-1.5 bg-white rounded-2xl border border-gray-200 shadow-sm dark:bg-boxdark dark:border-gray-700">
                            @foreach ($receipts->elements() as $element)
                                @if (is_string($element))
                                    <span class="flex justify-center items-center w-8 h-8 text-sm font-bold text-gray-400">{{ $element }}</span>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $receipts->currentPage())
                                            <span class="flex items-center justify-center w-8 h-8 text-sm font-black !text-white border shadow-md bg-primary shadow-primary/30 rounded-xl border-primary shrink-0">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="flex justify-center items-center w-8 h-8 text-sm font-bold text-gray-500 bg-transparent rounded-xl border border-transparent transition-colors hover:bg-primary/10 hover:text-primary dark:text-gray-400 dark:hover:bg-gray-800 shrink-0">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </div>

                        {{-- زر الصفحة التالية --}}
                        @if ($receipts->hasMorePages())
                            <a href="{{ $receipts->nextPageUrl() }}" class="flex justify-center items-center w-10 h-10 text-gray-600 bg-white rounded-xl border border-gray-200 shadow-sm transition-colors hover:bg-primary/5 hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_right</span>
                            </a>
                        @else
                            <span class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-xl border border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_right</span>
                            </span>
                        @endif
                        
                    </nav>
                </div>
            @endif
        </div>

    </div>

@endsection