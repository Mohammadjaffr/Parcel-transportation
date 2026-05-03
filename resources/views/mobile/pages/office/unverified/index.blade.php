@extends('mobile.layouts.app')

@section('title', 'المكاتب غير الموثوقة')

@section('content')

    <div x-data="{
                    showDeleteModal: false,
                    searchQuery: '{{ addslashes(request('search', '')) }}',
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
                }" class="flex relative flex-col gap-6 pb-24 min-h-screen">

        <div class="flex justify-between items-center px-2">
            <div>
                <h1 class="text-2xl font-black font-headline text-slate-800">المكاتب غير الموثوقة</h1>
                <p class="text-xs font-semibold text-slate-400">إجمالي المكاتب: <span
                        class="font-bold text-primary">{{ $offices->total() }}</span></p>
            </div>

            <a href="{{ route('offices.create') }}"
                class="flex justify-center items-center w-12 h-12 text-white rounded-2xl shadow-xl transition-all bg-primary shadow-primary/20 active:scale-95">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add_business</span>
            </a>
        </div>

        <form action="{{ route('offices.unverified.index') }}" method="GET" class="px-2">
            <div class="relative group">
                <button type="submit"
                    class="absolute right-4 top-1/2 z-10 transition-colors -translate-y-1/2 material-symbols-outlined text-slate-400 group-focus-within:text-primary">search</button>
                <input type="text" name="search" value="{{ request('search') }}" x-model="searchQuery" placeholder="ابحث باسم المكتب، رقم الهاتف أو العنوان..."
                    class="pr-12 w-full h-14 text-sm bg-white rounded-[1.25rem] border-none ring-1 shadow-sm transition-all outline-none ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline text-slate-700">
            </div>
        </form>

        <div class="px-2 space-y-4">
            @forelse($offices as $office)
                @php
                    $searchData = collect([$office->name, $office->address, $office->phone]);
                    foreach($office->branches as $branch) {
                        $searchData->push($branch->name);
                        $searchData->push($branch->phone);
                        $searchData->push($branch->city);
                    }
                    $searchString = strtolower($searchData->filter()->implode(' '));
                @endphp
                <div x-data="{ expanded: false, searchString: '{{ addslashes($searchString) }}' }"
                    x-show="searchQuery === '' || searchString.includes(searchQuery.toLowerCase())"
                    class="bg-white rounded-[1.75rem] border border-slate-50 shadow-[0_8px_30px_rgb(0,0,0,0.02)] overflow-hidden transition-all duration-300">

                    <div @click="expanded = !expanded" class="relative p-5 transition-colors cursor-pointer active:bg-slate-50">

                        <div class="flex gap-4 items-center mt-2">
                            <div
                                class="flex justify-center items-center w-14 h-14 text-amber-500 bg-amber-50 rounded-2xl border shadow-inner border-amber-100/50 shrink-0">
                                <span class="text-2xl material-symbols-outlined">storefront</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex gap-2 items-center mb-1">
                                    <h3 class="text-base font-bold truncate text-slate-800 font-headline">{{ $office->name }}
                                    </h3>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary transition-transform"
                                        :class="expanded ? 'scale-110' : ''">
                                        {{ $office->branches->count() }} فرع
                                    </span>
                                </div>

                                <p class="flex gap-1 items-center mt-1 text-xs text-slate-500">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">location_on</span>
                                    <span class="truncate">{{ $office->address ?: 'لم يتم تحديد العنوان' }}</span>
                                </p>
                            </div>

                            <div class="transition-transform duration-300 text-slate-300"
                                :class="expanded ? 'rotate-180 text-primary' : ''">
                                <span class="material-symbols-outlined">expand_more</span>
                            </div>
                        </div>
                    </div>

                    <div x-show="expanded" x-collapse x-cloak
                        class="px-5 pt-2 pb-5 space-y-3 border-t bg-slate-50/50 border-slate-50">

                        <div
                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                            <span class="w-1 h-1 rounded-full bg-primary"></span>
                            قائمة الفروع المتاحة
                        </div>

                        @forelse($office->branches as $branch)
                            <div
                                class="flex justify-between items-center p-3 bg-white rounded-2xl border shadow-sm border-slate-100">
                                <div class="flex gap-3 items-center">
                                    <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary/5 text-primary">
                                        <span class="text-sm material-symbols-outlined">location_city</span>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-700 font-headline">{{ $branch->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $branch->city }}</div>
                                    </div>
                                </div>
                                <a href="tel:{{ $branch->phone }}"
                                    class="flex justify-center items-center w-8 h-8 text-emerald-600 bg-emerald-50 rounded-full transition-transform active:scale-90">
                                    <span class="text-sm material-symbols-outlined">call</span>
                                </a>
                            </div>
                        @empty
                            <div class="py-4 text-xs italic font-medium text-center text-slate-400">
                                لا توجد فروع مسجلة لهذا المكتب
                            </div>
                        @endforelse

                        <div class="flex gap-2 pt-2 mt-4 border-t border-slate-100">

                            <a href="{{ route('offices.edit', $office->id) }}"
                                class="flex justify-center items-center w-10 h-10 text-blue-500 bg-blue-50 rounded-xl transition-all active:scale-95">
                                <span class="text-xl material-symbols-outlined">edit_square</span>
                            </a>

                            <a href="{{ route('offices.show', $office->id) }}"
                                class="flex flex-1 gap-2 justify-center items-center h-10 text-xs font-bold bg-white rounded-xl border transition-all border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-primary active:scale-95">
                                <span class="text-sm material-symbols-outlined">visibility</span>
                                تفاصيل المكتب
                            </a>

                            {{-- <button @click.stop="openDeleteModal({{ $office }})"
                                class="flex justify-center items-center w-10 h-10 text-red-400 bg-red-50 rounded-xl transition-all active:scale-95">
                                <span class="text-xl material-symbols-outlined">delete_outline</span>
                            </button> --}}

                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="py-20 flex flex-col items-center justify-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100 shadow-sm mx-2">
                    <div class="flex justify-center items-center mb-6 w-24 h-24 rounded-full bg-slate-50 text-slate-200">
                        <span class="text-6xl material-symbols-outlined">storefront</span>
                    </div>
                    <p class="text-lg font-bold font-headline text-slate-400">لا توجد مكاتب مضافة حالياً</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination info --}}
        @if ($offices->hasPages())
            <div class="px-2 pb-6">
                {{ $offices->links('vendor.pagination.mobile') }}
            </div>
        @endif

        <div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-full"
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

            <div
                class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto text-center">
                <div @click="closeModals()"
                    class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
                </div>
                <div
                    class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-red-50 text-red-500 rounded-[1.5rem]">
                    <span class="text-4xl material-symbols-outlined">delete_forever</span>
                </div>
                <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد الحذف</h3>
                <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
                    هل أنت متأكد من أنك تريد حذف المكتب <br>
                    <span class="text-base font-bold text-slate-800 font-headline"
                        x-text="deleteOfficeData.name"></span>؟<br>
                    <span class="text-red-500/80">سيتم حذف جميع الفروع التابعة له أيضاً.</span>
                </p>
                <form :action="deleteOfficeData.url" method="POST" @submit="isSubmitting = true" class="flex gap-3 px-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="closeModals()"
                        class="flex-1 py-4 text-sm font-bold rounded-2xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 active:scale-95 font-headline">تراجع</button>
                    <button type="submit" :disabled="isSubmitting"
                        class="flex flex-1 gap-2 justify-center items-center py-4 text-sm font-bold text-white bg-red-500 rounded-2xl shadow-lg transition-all hover:bg-red-600 active:scale-95 font-headline disabled:opacity-70">
                        <span x-show="!isSubmitting">نعم، احذف</span>
                        <span x-show="isSubmitting"
                            class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection