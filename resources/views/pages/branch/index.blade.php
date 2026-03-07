@extends('layouts.app')
@section('title', 'قائمة المكاتب')
@section('Breadcrumb', 'إدارة المكاتب')
@section('addButton')
    <x-modals.success-modal />
    <x-modals.error-modal />
    @if (Auth::user()->type != 'user')
        <div class="flex w-full md:justify-end">
            <button @click="$dispatch('open-create-modal')"
                class="flex gap-2 justify-center items-center px-3 w-full h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-brand-500 hover:bg-brand-500 shadow-brand-500/20 active:scale-95 md:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                إضافة فرع جديد
            </button>
        </div>
    @endif
@endsection
@section('content')
    <div class="space-y-6 font-outfit" dir="rtl" x-data="{
        search: '',
        filterCity: 'all',
        createModalOpen: false,
        editModalOpen: false,
        isFetching: null,
        editBranch: {
            code: '',
            name: '',
            city: '',
            address: '',
            phone: ''
        },
        showRow(city, name, code) {
            const matchesSearch = name.toLowerCase().includes(this.search.toLowerCase()) || code.toLowerCase().includes(this.search.toLowerCase());
            const matchesCity = this.filterCity === 'all' || city === this.filterCity;
            return matchesSearch && matchesCity;
        },
        async openEditModal(branchCode) {
            this.isFetching = branchCode;
            try {
                const response = await fetch(`/branch/${branchCode}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                this.editBranch = {
                    code: data.code,
                    name: data.name,
                    city: data.city,
                    address: data.address,
                    phone: data.phone
                };
                this.editModalOpen = true;
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ أثناء جلب البيانات');
            } finally {
                this.isFetching = null;
            }
        }
    }"
        @open-create-modal.window="createModalOpen = true">

        {{-- Modal إضافة فرع --}}
        @include('pages.branch.create-branch-modal')

        {{-- Modal تعديل فرع --}}
        @include('pages.branch.edit-branch-modal')

        {{-- قسم الإحصائيات --}}
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-3 md:gap-6">
            <div @click="filterCity = 'all'"
                :class="filterCity === 'all' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-gray-100'"
                class="relative flex cursor-pointer flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border transition-all hover:shadow-md shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gray-50 rounded-xl dark:bg-gray-800 text-brand-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">إجمالي
                        المكاتب</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $totalBranches }}</h4>
                </div>
            </div>

            <div
                class="relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 transition-all shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 rounded-xl bg-success-50 dark:bg-success-500/10 text-success-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">المدن
                        المغطاة</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $totalCities }}</h4>
                </div>
            </div>

            <div
                class="relative flex flex-col items-start justify-between rounded-2xl bg-white p-5 dark:bg-white/[0.03] border border-gray-100 transition-all shadow-theme-sm">
                <div
                    class="flex justify-center items-center w-10 h-10 text-orange-500 bg-orange-50 rounded-xl dark:bg-orange-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6h1.5m-1.5 3h1.5m-1.5 3h1.5M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                </div>
                <div class="mt-3">
                    <span class="font-bold tracking-widest text-gray-500 uppercase text-theme-xs dark:text-gray-400">آخر فرع
                        مضاف</span>
                    <h4 class="text-xl font-black dark:text-white">{{ $branches->first()->name ?? '---' }}</h4>
                </div>
            </div>
        </div>

        {{-- قسم البحث والأزرار --}}


        {{-- الجدول --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-theme-sm overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 items-center bg-white dark:bg-white/[0.03] p-4 rounded-2xl gap-6">
                <div class="relative w-full group border border-brand-500 ring-2 ring-brand-500/20 rounded-2xl">
                    <input type="text" x-model="search" placeholder="ابحث باسم المكتب أو الرمز..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-gray-50 rounded-xl border-none transition-all dark:bg-gray-900 focus:ring-2 focus:ring-brand-500/20 dark:text-white">
                    <div
                        class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 group-focus-within:text-brand-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

            </div>
            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                @forelse ($branches as $branch)
                    @php
                        $credit = $branch->total_credit ?? 0;
                        $debit = $branch->total_debit ?? 0;
                    @endphp
                    <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800"
                        x-show="showRow('{{ $branch->city }}', '{{ $branch->name }}', '{{ $branch->code }}')" x-transition>

                        {{-- Header: Code + Name + Actions --}}
                        <div class="flex justify-between items-start">
                            <div class="flex gap-3 items-center">
                                <span
                                    class="px-3 py-1.5 text-xs font-black rounded-lg border bg-brand-50 dark:bg-brand-500/10 text-brand-500 border-brand-100 dark:border-brand-500/20">
                                    {{ $branch->code }}
                                </span>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-black text-gray-900 dark:text-white">{{ $branch->name }}</span>
                                    <x-phone-number :value="$branch->phone" class="mt-0.5 text-xs text-gray-500 font-medium" />
                                </div>
                            </div>
                            <div class="flex gap-1 items-center">
                                <a href="{{ route('branch.show', $branch->code) }}"
                                    class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-brand-500 dark:bg-gray-900 dark:border-gray-800"
                                    title="عرض التفاصيل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('branch.edit', $branch->code) }}"
                                    class="p-2 text-gray-400 bg-white rounded-lg border border-gray-100 shadow-sm transition-colors hover:text-brand-500 dark:bg-gray-900 dark:border-gray-800"
                                    title="تعديل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- City / Address --}}
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $branch->city }}</span>
                            <span class="text-[10px] text-gray-400">{{ $branch->address }}</span>
                        </div>

                        {{-- Credit/Debit --}}
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-medium text-gray-400">له (دائن)</span>
                                <span
                                    class="px-2 py-0.5 text-xs font-bold text-green-600 bg-green-50 rounded-md border border-green-100 dark:bg-green-500/10 dark:border-green-500/20"
                                    dir="ltr">
                                    {{ number_format($credit, 2) }}
                                </span>
                            </div>
                            <div class="flex flex-col gap-1 items-end">
                                <span class="text-[10px] font-medium text-gray-400">عليه (مدين)</span>
                                <span
                                    class="px-2 py-0.5 text-xs font-bold text-red-500 bg-red-50 rounded-md border border-red-100 dark:bg-red-500/10 dark:border-red-500/20"
                                    dir="ltr">
                                    {{ number_format($debit, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="py-12 text-center rounded-xl border border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-800">
                        <div class="flex flex-col justify-center items-center">
                            <div class="p-3 mb-3 bg-white rounded-full shadow-sm dark:bg-gray-800">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">لا توجد مكاتب</h4>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">لا توجد مكاتب مسجلة حالياً..</p>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto px-4 pb-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em]">
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4 text-center">الرمز</th>
                            <th class="px-6 py-4">اسم المكتب</th>
                            <th class="px-6 py-4">المدينة / العنوان</th>

                            {{-- ✅ الأعمدة المالية الجديدة --}}
                            <th class="px-6 py-4 text-green-600">له (دائن)</th>
                            <th class="px-6 py-4 text-red-500">عليه (مدين)</th>
                            {{-- <th class="px-6 py-4">الصافي</th> --}}

                            {{-- <th class="px-6 py-4">رقم الهاتف</th> --}}
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @forelse ($branches as $branch)
                            {{-- حساب الأرصدة قبل العرض --}}
                            @php
                                $credit = $branch->total_credit ?? 0; // له
                                $debit = $branch->total_debit ?? 0; // عليه
                                // $balance = $credit - $debit;          // الصافي
                            @endphp

                            <tr x-show="showRow('{{ $branch->city }}', '{{ $branch->name }}', '{{ $branch->code }}')"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="bg-white rounded-2xl border border-transparent shadow-sm transition-all dark:bg-gray-900 hover:shadow-md hover:border-gray-100 dark:hover:border-gray-800">

                                <td class="px-6 py-5 border-r first:rounded-r-2xl border-y dark:border-gray-800/50">
                                    <span class="text-xs font-black text-gray-400">{{ $loop->iteration }}</span>
                                </td>

                                <td class="px-6 py-5 text-center border-y dark:border-gray-800/50">
                                    <span
                                        class="px-3 py-1.5 text-xs font-black rounded-lg border bg-brand-50 dark:bg-brand-500/10 text-brand-500 border-brand-100 dark:border-brand-500/20">
                                        {{ $branch->code }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-gray-900 dark:text-white">{{ $branch->name }}</span>
                                        <x-phone-number :value="$branch->phone"
                                            class="mt-0.5 text-xs text-gray-500 font-medium" />
                                    </div>
                                </td>

                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $branch->city }}</span>
                                        <span class="mt-0.5 text-xs text-gray-400">{{ $branch->address }}</span>
                                    </div>
                                </td>

                                {{-- ✅ عمود له (Credit) --}}
                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <span
                                        class="px-2 py-1 text-sm font-bold text-green-600 bg-green-50 rounded-md border border-green-100 dark:bg-green-500/10 dark:border-green-500/20"
                                        dir="ltr">
                                        {{ number_format($credit, 2) }}
                                    </span>
                                </td>

                                {{-- ✅ عمود عليه (Debit) --}}
                                <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <span
                                        class="px-2 py-1 text-sm font-bold text-red-500 bg-red-50 rounded-md border border-red-100 dark:bg-red-500/10 dark:border-red-500/20"
                                        dir="ltr">
                                        {{ number_format($debit, 2) }}
                                    </span>
                                </td>

                                {{-- <td class="px-6 py-5 border-y dark:border-gray-800/50">
                                    <x-phone-number :value="$branch->phone" />
                                </td> --}}

                                <td
                                    class="px-6 py-5 text-center border-l last:rounded-l-2xl border-y dark:border-gray-800/50">
                                    <div class="flex gap-2 justify-center items-center">
                                        {{-- زر العرض --}}
                                        <a href="{{ route('branch.show', $branch->code) }}"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400"
                                            title="عرض التفاصيل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        {{-- زر التعديل --}}
                                        {{-- <button type="button" @click="openEditModal('{{ $branch->code }}')"
                                            :disabled="isFetching === '{{ $branch->code }}'"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400"
                                            title="تعديل">
                                            <template x-if="isFetching !== '{{ $branch->code }}'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </template>
                                            <template x-if="isFetching === '{{ $branch->code }}'">
                                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </template>
                                        </button> --}}
                                        <a href="{{ route('branch.edit', $branch->code) }}"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400"
                                            title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>


                                        {{-- زر الحذف --}}
                                        <button type="button"
                                            @click="$dispatch('open-warning-modal', {
                                                        url: '{{ route('branch.destroy', ['branch' => $branch->code]) }}',
                                                        title: 'هل أنت متأكد من حذف هذا الفرع؟',
                                                        message: 'سيتم حذف الفرع نهائياً. لا يمكن التراجع عن هذا الإجراء.',
                                                        confirmButtonText: 'نعم، احذف',
                                                        cancelButtonText: 'إلغاء'
                                                    })"
                                            class="inline-flex p-2 text-gray-400 rounded-lg transition-all hover:bg-white hover:text-brand-600 hover:shadow-sm dark:hover:bg-gray-800 dark:hover:text-brand-400"
                                            title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        {{-- زر تصفية حساب الفرع --}}
                                        @include('pages.branch.claeramountbranch', [
                                            'branch' => $branch,
                                            'branchCredit' => $credit, // له (دائن)
                                            'branchDebit' => $debit, // عليه (مدين)
                                        ])
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-20 italic text-center text-gray-400">لا توجد مكاتب مسجلة
                                    حالياً..</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($branches->hasPages())
                <div class="p-8 border-t border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-800">
                    {{ $branches->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
