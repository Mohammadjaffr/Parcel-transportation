@extends('layouts.app')

@section('title', 'المكاتب غير الموثوقة')

@section('content')
   

    <div x-data="{
        showDeleteModal: false,
        searchQuery: '',
        isSubmitting: false,
        deleteOfficeData: { id: '', name: '', url: '' },

        openDeleteModal(office) {
            this.deleteOfficeData = {
                id: office.id,
                name: office.name,
                url: '/offices/' + office.id
            };
            this.showDeleteModal = true;
        },

        closeModals() {
            this.showDeleteModal = false;
        }
    }" class="flex relative flex-col gap-6 p-4 pb-24 mx-auto max-w-7xl min-h-screen md:p-6 bg-surface dark:bg-boxdark-2 font-body" dir="rtl">

        {{-- ================= Header Section ================= --}}
        <div class="flex flex-col gap-4 justify-between items-start md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-black tracking-tight md:text-3xl font-headline text-on-surface dark:text-white">المكاتب غير الموثوقة</h1>
                <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-bodydark">
                    إجمالي المكاتب: <span class="font-black text-primary">{{ $offices->total() }}</span>
                </p>
            </div>
            
            <a href="{{ route('offices.create') }}" 
                class="flex gap-2 justify-center items-center px-5 w-full h-12 text-sm font-bold text-white rounded-2xl shadow-lg transition-all bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-95 md:w-auto">
                <span class="text-[22px] material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add_business</span>
                <span class="hidden md:inline">إضافة مكتب جديد</span>
            </a>
        </div>

        {{-- ================= Search & Data Section ================= --}}
        <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden transition-colors">
            
            {{-- Search Bar --}}
            <div class="p-5 bg-white border-b border-gray-100 dark:border-boxdark-2 dark:bg-boxdark">
                <div class="relative w-full md:w-[28rem] rounded-2xl border border-gray-200 dark:border-boxdark-2 transition-all group focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 bg-surface dark:bg-boxdark-2">
                    <input type="text" x-model="searchQuery" 
                        placeholder="ابحث باسم المكتب، رقم الهاتف أو العنوان..."
                        class="pr-12 pl-12 w-full h-12 text-sm font-medium placeholder-gray-400 bg-transparent rounded-2xl border-none transition-all outline-none focus:ring-0 text-on-surface dark:text-white">
                    
                    <span class="absolute right-4 top-1/2 text-gray-400 transition-colors -translate-y-1/2 material-symbols-outlined group-focus-within:text-primary">search</span>
                    
                    <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''" style="display: none;"
                        class="flex absolute left-2 top-1/2 justify-center items-center w-8 h-8 text-gray-400 bg-gray-100 rounded-xl transition-all -translate-y-1/2 dark:bg-boxdark hover:text-error active:scale-95">
                        <span class="text-[18px] material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-5 lg:hidden">
                @forelse($offices as $office)
                    <div x-data="{ expanded: false }"
                        x-show="searchQuery === '' || '{{ $office->name }}'.includes(searchQuery) || '{{ $office->address }}'.includes(searchQuery) || '{{ $office->phone }}'.includes(searchQuery)"
                        class="overflow-hidden rounded-2xl border border-gray-100 shadow-sm transition-all duration-300 bg-surface dark:bg-boxdark-2 dark:border-boxdark">

                        <div @click="expanded = !expanded" class="relative p-5 transition-colors cursor-pointer active:bg-gray-50 dark:active:bg-boxdark">
                            <div class="flex gap-4 items-center">
                                <div class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl shadow-sm dark:text-amber-400 dark:bg-amber-500/10 shrink-0">
                                    <span class="text-[24px] material-symbols-outlined">storefront</span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex gap-2 items-center mb-1">
                                        <h3 class="text-sm font-black truncate text-on-surface dark:text-white font-headline">{{ $office->name }}</h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-container dark:bg-primary/10 text-primary dark:text-primary transition-transform"
                                            :class="expanded ? 'scale-110' : ''">
                                            {{ $office->branches->count() }} فرع
                                        </span>
                                    </div>
                                    <p class="flex gap-1 items-center mt-1 text-xs text-gray-500 dark:text-bodydark">
                                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                                        <span class="truncate">{{ $office->address ?: 'لم يتم تحديد العنوان' }}</span>
                                    </p>
                                </div>

                                <div class="text-gray-400 transition-transform duration-300 dark:text-gray-500 shrink-0"
                                    :class="expanded ? 'rotate-180 text-primary dark:text-primary' : ''">
                                    <span class="material-symbols-outlined">expand_more</span>
                                </div>
                            </div>
                        </div>

                        {{-- Expanded Content --}}
                        <div x-show="expanded" x-collapse x-cloak
                            class="px-5 pt-3 pb-5 space-y-3 bg-white border-t border-gray-100 dark:bg-boxdark dark:border-boxdark-2">
                            
                            <div class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                قائمة الفروع المتاحة
                            </div>

                            @forelse($office->branches as $branch)
                                <div class="flex justify-between items-center p-3 rounded-xl border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 rounded-lg bg-primary-container dark:bg-primary/5 text-primary">
                                            <span class="text-[20px] material-symbols-outlined">location_city</span>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-on-surface dark:text-white font-headline">{{ $branch->name }}</div>
                                            <div class="text-[10px] text-gray-500 dark:text-bodydark mt-0.5">{{ $branch->city }}</div>
                                        </div>
                                    </div>
                                    <a href="tel:{{ $branch->phone }}"
                                        class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl transition-transform dark:bg-emerald-500/10 dark:text-emerald-400 active:scale-95">
                                        <span class="text-[18px] material-symbols-outlined">call</span>
                                    </a>
                                </div>
                            @empty
                                <div class="py-4 text-xs italic font-medium text-center text-gray-400 rounded-xl dark:text-gray-500 bg-surface dark:bg-boxdark-2">
                                    لا توجد فروع مسجلة لهذا المكتب
                                </div>
                            @endforelse

                            <div class="flex gap-2 pt-3 mt-4 border-t border-gray-100 dark:border-boxdark-2">
                                <a href="{{ route('offices.edit', $office->id) }}"
                                    class="flex justify-center items-center w-11 h-11 text-gray-400 rounded-xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:text-primary dark:hover:text-primary active:scale-95">
                                    <span class="text-[20px] material-symbols-outlined">edit_square</span>
                                </a>

                                <a href="{{ route('offices.show', $office->id) }}"
                                    class="flex flex-1 gap-2 justify-center items-center h-11 text-xs font-bold text-gray-600 rounded-xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark dark:text-gray-300 hover:text-primary dark:hover:text-white active:scale-95">
                                    <span class="text-[18px] material-symbols-outlined">visibility</span>
                                    تفاصيل المكتب
                                </a>

                                <button @click.stop="openDeleteModal({{ $office }})"
                                    class="flex justify-center items-center w-11 h-11 text-rose-500 bg-rose-50 rounded-xl transition-all dark:bg-rose-500/10 hover:bg-rose-100 dark:hover:bg-rose-500/20 active:scale-95">
                                    <span class="text-[20px] material-symbols-outlined">delete_outline</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <span class="text-[48px] material-symbols-outlined text-gray-300 dark:text-gray-600 mb-4">storefront</span>
                        <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا توجد مكاتب مضافة حالياً</p>
                    </div>
                @endforelse
            </div>

            {{-- ===== Desktop View (Data Table) ===== --}}
            <div class="hidden overflow-x-auto px-6 pb-6 mt-4 lg:block">
                <table class="w-full text-right border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] dark:text-bodydark bg-surface dark:bg-boxdark-2 border-b border-gray-100 dark:border-boxdark">
                            <th class="px-6 py-5">المكتب الرئيسي</th>
                            <th class="px-6 py-5">الموقع الجغرافي</th>
                            <th class="px-6 py-5 text-center">عدد الفروع</th>
                            <th class="px-6 py-5 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-0">
                        @foreach($offices as $office)
                            <tr x-show="searchQuery === '' || '{{ $office->name }}'.includes(searchQuery) || '{{ $office->address }}'.includes(searchQuery) || '{{ $office->phone }}'.includes(searchQuery)" x-transition
                                class="rounded-2xl border border-transparent shadow-sm transition-all bg-surface dark:bg-boxdark-2 hover:shadow-md hover:border-gray-200 dark:hover:border-boxdark group">
                                
                                <td class="px-6 py-4 border-r border-gray-50 border-y dark:border-boxdark-2 first:rounded-r-2xl">
                                    <div class="flex gap-4 items-center">
                                        <div class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl shadow-sm dark:text-amber-400 dark:bg-amber-500/10">
                                            <span class="text-[24px] material-symbols-outlined">storefront</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-black text-on-surface dark:text-white font-headline">{{ $office->name }}</span>
                                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-gray-500 dark:text-bodydark" dir="ltr">
                                                <span class="material-symbols-outlined text-[14px]">call</span>
                                                <span>{{ $office->phone ?? '---' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-300">
                                        <span class="material-symbols-outlined text-[18px] text-gray-400 dark:text-gray-500">location_on</span>
                                        <span class="text-sm font-medium">{{ $office->address ?: 'لم يتم تحديد العنوان' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center border-gray-50 border-y dark:border-boxdark-2">
                                    <div class="inline-block relative cursor-help group/branches">
                                        <span class="px-3 py-1.5 rounded-lg bg-primary-container dark:bg-primary/10 text-primary font-black text-[10px]">
                                            {{ $office->branches->count() }} فرع مسجل
                                        </span>
                                        
                                        {{-- Tooltip للفروع --}}
                                        @if($office->branches->count() > 0)
                                            <div class="absolute bottom-full left-1/2 invisible z-10 p-3 mb-2 w-48 bg-white rounded-xl border border-gray-100 shadow-xl opacity-0 transition-all -translate-x-1/2 dark:bg-boxdark dark:border-boxdark-2 group-hover/branches:opacity-100 group-hover/branches:visible">
                                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 dark:border-boxdark-2 pb-2 mb-2">الفروع التابعة</div>
                                                <div class="flex overflow-y-auto flex-col gap-2 max-h-32 custom-scrollbar">
                                                    @foreach($office->branches as $branch)
                                                        <div class="flex justify-between items-center text-right">
                                                            <span class="text-xs font-bold text-on-surface dark:text-white">{{ $branch->name }}</span>
                                                            <span class="text-[10px] text-gray-500 dark:text-bodydark">{{ $branch->city }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center border-l border-gray-50 border-y dark:border-boxdark-2 last:rounded-l-2xl">
                                    <div class="flex gap-2 justify-center items-center">
                                        <a href="{{ route('offices.show', $office->id) }}" title="التفاصيل"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-primary-container hover:text-primary hover:border-primary/20 dark:hover:bg-primary/10 dark:hover:text-primary active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>

                                        <a href="{{ route('offices.edit', $office->id) }}" title="تعديل"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-blue-50 hover:text-blue-500 hover:border-blue-200 dark:hover:bg-blue-500/10 dark:hover:text-blue-400 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>

                                        <button @click="openDeleteModal({{ $office }})" title="حذف"
                                            class="inline-flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 dark:text-gray-500 hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 dark:hover:bg-rose-500/10 dark:hover:text-rose-400 active:scale-95">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Empty Search State --}}
            <div x-show="searchQuery !== '' && !Array.from(document.querySelectorAll('div[x-show], tr[x-show]')).some(el => el.style.display !== 'none')" 
                style="display: none;"
                class="flex flex-col justify-center items-center py-16 mt-4 text-center bg-white dark:bg-boxdark">
                <span class="text-[48px] material-symbols-outlined text-gray-300 dark:text-gray-600 mb-4">search_off</span>
                <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا يوجد نتائج تطابق بحثك</p>
            </div>

            {{-- Pagination --}}
            @if ($offices->hasPages())
                <div class="px-6 py-5 border-t border-gray-100 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    {{ $offices->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>

        {{-- ======================== Desktop/Centered Modals ======================== --}}

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 pointer-events-none">
            
            <div class="fixed inset-0 backdrop-blur-sm pointer-events-auto bg-slate-900/60 dark:bg-black/80" @click="closeModals()"></div>

            <div class="relative w-full max-w-md bg-white dark:bg-boxdark rounded-[2rem] shadow-2xl border border-gray-100 dark:border-boxdark-2 p-8 text-center pointer-events-auto flex flex-col">
                
                {{-- أيقونة التحذير --}}
                <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-rose-50 dark:bg-rose-500/10 text-error rounded-[1.5rem] shadow-sm">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>

                <h3 class="mb-3 text-2xl font-black font-headline text-on-surface dark:text-white">تأكيد الحذف</h3>
                
                <p class="mb-8 text-sm font-semibold leading-relaxed text-gray-500 dark:text-gray-400">
                    هل أنت متأكد من أنك تريد حذف المكتب <br>
                    <span class="text-base font-bold text-on-surface dark:text-white font-headline" x-text="deleteOfficeData.name"></span>؟<br>
                    <span class="inline-block mt-2 text-error/80 dark:text-error">سيتم حذف جميع الفروع التابعة له أيضاً. لا يمكن التراجع.</span>
                </p>

                <form :action="deleteOfficeData.url" method="POST" @submit="isSubmitting = true" class="flex gap-3 w-full">
                    @csrf
                    @method('DELETE')
                    
                    <button type="button" @click="closeModals()"
                        class="flex-1 h-12 text-sm font-bold text-gray-600 rounded-xl transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-boxdark font-headline active:scale-95">
                        تراجع
                    </button>
                    
                    <button type="submit" :disabled="isSubmitting"
                        class="flex flex-1 gap-2 justify-center items-center h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-error hover:bg-error/90 shadow-error/30 active:scale-95 font-headline disabled:opacity-70 disabled:shadow-none">
                        <span x-show="!isSubmitting">نعم، احذف</span>
                        <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection