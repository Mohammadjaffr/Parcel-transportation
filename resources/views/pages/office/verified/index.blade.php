@extends('layouts.app')

@section('title', 'المكاتب الموثوقة')
@section('Breadcrumb', 'الشبكة والمكاتب الموثوقة')

@section('content')
    <div x-data="{ searchQuery: '' }" class="space-y-6 font-outfit" dir="rtl">

        {{-- ===== Header & Search Section ===== --}}
        <div
            class="flex flex-col gap-4 justify-between items-start p-6 bg-white rounded-3xl border border-gray-100 md:flex-row md:items-center dark:bg-boxdark dark:border-gray-800 shadow-theme-sm">

            {{-- Info --}}
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">المكاتب الموثوقة</h1>
                <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-gray-400">
                    إجمالي المكاتب الموثوقة في الشبكة:
                    <span class="px-2 py-0.5 ml-1 text-sm font-black text-white rounded-lg bg-primary">
                        {{ $offices->total() }}
                    </span>
                </p>
            </div>
         
            {{-- Search Bar --}}
            <div class="w-full md:w-96">
                <div class="relative group">
                    <div
                        class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>
                    <input type="text" x-model="searchQuery" placeholder="ابحث باسم المكتب، أو الهاتف..."
                        class="pr-12 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-gray-50 rounded-xl border border-gray-200 transition-all outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                </div>
            </div>
        </div>

        {{-- ===== Grid Cards (Desktop View) ===== --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">

            @forelse($offices as $office)
                @php
                    $searchData = collect([$office->name, $office->phone ?? ''])
                        ->merge($office->branches->pluck('phone'))
                        ->merge($office->branches->pluck('name'))
                        ->filter()
                        ->join(' ');
                @endphp
                {{-- Office Card --}}
                <div x-data="{ isSending: false, searchData: {{ json_encode($searchData, JSON_UNESCAPED_UNICODE) }} }"
                    x-show="searchQuery === '' || searchData.toLowerCase().includes(searchQuery.toLowerCase())"
                    x-transition
                    class="flex flex-col overflow-hidden transition-all bg-white border border-gray-100 group rounded-[2rem] dark:bg-boxdark dark:border-gray-800 hover:shadow-lg hover:shadow-gray-200/50 dark:hover:shadow-none hover:border-primary/30">

                    {{-- Card Header (Office Info) --}}
                    <div class="p-6 border-b border-gray-50 dark:border-gray-800/50">
                        <div class="flex gap-4 justify-between items-start">

                            {{-- Logo & Name --}}
                            <div class="flex gap-4 items-center">
                                @if ($office->logo)
                                    <div
                                        class="overflow-hidden relative flex-shrink-0 w-16 h-16 bg-white rounded-2xl border border-gray-100 shadow-theme-xs dark:border-gray-700">
                                        <img src="{{ asset('storage/' . $office->logo) }}" alt="شعار {{ $office->name }}"
                                            class="object-cover w-full h-full">
                                    </div>
                                @else
                                    <div
                                        class="flex flex-shrink-0 justify-center items-center w-16 h-16 rounded-2xl border text-primary bg-primary/5 border-primary/10 shadow-theme-xs">
                                        <span class="text-[32px] material-symbols-outlined"
                                            style="font-variation-settings: 'FILL' 1;">verified</span>
                                    </div>
                                @endif

                                <div>
                                    <h3 class="text-lg font-black text-gray-900 truncate dark:text-white"
                                        title="{{ $office->name }}">
                                        {{ $office->name }}
                                    </h3>
                                    <div
                                        class="flex gap-1.5 items-center mt-1 text-xs font-bold text-gray-500 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-[16px] text-primary">storefront</span>
                                        <span>{{ $office->branches->count() }} فروع متاحة</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Card Body (Branches List) --}}
                    <div class="flex-1 p-6 bg-gray-50/30 dark:bg-gray-900/20">
                        <div
                            class="flex gap-2 items-center mb-4 text-xs font-black tracking-widest text-gray-400 uppercase dark:text-gray-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            الفروع التابعة
                        </div>

                        <div class="overflow-y-auto pr-1 space-y-3 max-h-48 custom-scrollbar">
                            @forelse($office->branches as $branch)
                                <div
                                    class="flex justify-between items-center p-3 bg-white rounded-2xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark dark:border-gray-700 hover:border-primary/20">
                                    <div class="flex gap-3 items-center">
                                        <div
                                            class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-xl dark:bg-gray-800 dark:text-gray-500">
                                            <span class="text-[18px] material-symbols-outlined">location_on</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                {{ $branch->name }}</div>
                                            <div class="text-[11px] font-semibold text-gray-400 mt-0.5">
                                                {{ $branch->city ?? 'غير محدد' }}</div>
                                        </div>
                                    </div>

                                    {{-- Call Action (Only if connected) --}}
                                    @if ($office->connection_status == 'accepted')
                                        <a href="tel:{{ $branch->phone }}" title="الاتصال بالفرع"
                                            class="flex justify-center items-center w-8 h-8 rounded-full transition-transform bg-success-50 text-success-600 hover:bg-success-100 hover:scale-110 dark:bg-success-500/10 dark:text-success-400">
                                            <span class="text-[16px] material-symbols-outlined">call</span>
                                        </a>
                                    @else
                                        <div title="غير متصل"
                                            class="flex justify-center items-center w-8 h-8 text-gray-300 bg-gray-50 rounded-full dark:bg-gray-800 dark:text-gray-600">
                                            <span class="text-[16px] material-symbols-outlined">lock</span>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div
                                    class="flex flex-col justify-center items-center py-6 text-gray-400 rounded-2xl border border-gray-100 border-dashed bg-white/50 dark:border-gray-700 dark:bg-boxdark/50">
                                    <span
                                        class="material-symbols-outlined text-[24px] mb-2 opacity-50">domain_disabled</span>
                                    <p class="text-xs font-bold">لا توجد فروع مسجلة</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Card Footer (Connection Status/Actions) --}}
                    <div class="p-4 border-t border-gray-100 bg-gray-50/80 dark:bg-gray-900/50 dark:border-gray-800">
                        @if ($office->connection_status == 'none')
                            <form action="{{ route('offices.connect', $office->id) }}" method="POST"
                                @submit="isSending = true">
                                @csrf
                                <button type="submit" :disabled="isSending"
                                    class="flex gap-2 justify-center items-center w-full h-11 text-sm font-bold text-white rounded-xl transition-all bg-primary hover:bg-primary-hover hover:shadow-lg hover:shadow-primary/20 active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                                    <span x-show="!isSending"
                                        class="material-symbols-outlined text-[20px]">person_add</span>
                                    <span x-show="isSending"
                                        class="animate-spin material-symbols-outlined text-[20px]">progress_activity</span>
                                    <span x-text="isSending ? 'جاري إرسال الطلب...' : 'إرسال طلب ربط الشبكة'"></span>
                                </button>
                            </form>
                        @elseif($office->connection_status == 'pending')
                            <div
                                class="flex gap-2 justify-center items-center w-full h-11 rounded-xl border border-dashed border-warning-200 bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:border-warning-500/20 dark:text-warning-500">
                                <span class="material-symbols-outlined text-[20px] animate-pulse">hourglass_empty</span>
                                <span class="text-sm font-bold">طلب الربط قيد الانتظار</span>
                            </div>
                        @elseif($office->connection_status == 'accepted')
                            <div
                                class="flex gap-2 justify-center items-center w-full h-11 rounded-xl border border-dashed border-success-200 bg-success-50 text-success-600 dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-500">
                                <span class="material-symbols-outlined text-[20px]"
                                    style="font-variation-settings: 'FILL' 1;">verified</span>
                                <span class="text-sm font-bold">متصل بالشبكة</span>
                            </div>
                        @endif
                    </div>

                </div>
            @empty
                {{-- Empty State --}}
                <div
                    class="flex flex-col items-center justify-center py-24 bg-white border border-gray-100 border-dashed lg:col-span-3 md:col-span-2 rounded-[2.5rem] dark:bg-boxdark dark:border-gray-800">
                    <div
                        class="flex justify-center items-center mb-6 w-24 h-24 text-gray-300 bg-gray-50 rounded-full dark:bg-gray-800 dark:text-gray-600">
                        <span class="text-5xl material-symbols-outlined"
                            style="font-variation-settings: 'FILL' 1;">verified</span>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white">لا توجد مكاتب موثوقة حالياً</h3>
                    <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">لم يتم العثور على أي مكاتب مسجلة في
                        شبكة الثقة حتى الآن.</p>
                </div>
            @endforelse

        </div>

        {{-- ===== Pagination ===== --}}
        @if ($offices->hasPages())
            <div class="flex justify-center mt-8">
                {{ $offices->links() }}
                {{-- ملاحظة: أزلنا 'vendor.pagination.mobile' ليعمل الترقيم الافتراضي للديسكتوب --}}
            </div>
        @endif

    </div>
@endsection
