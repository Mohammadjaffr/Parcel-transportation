@extends('layouts.app')
@section('title', 'الشحنات المستقبلة (الواردة)')
@section('Breadcrumb', 'إدارة حركة الشحنات')

@section('addButton')
    {{-- زر اختصار لاستلام إرسالية جديدة --}}
    <div class="flex gap-3 items-center">
        {{-- زر الرجوع (اختياري لو أردت الإبقاء عليه بجوار زر الإضافة) --}}

        <a href="{{ route('shipmentpackage.incoming.create') }}"
            class="flex gap-2 justify-center items-center px-5 h-12 text-sm font-bold text-white rounded-xl shadow-lg transition-all bg-primary hover:bg-primary-hover shadow-primary/20 active:scale-95 shrink-0">
            <span class="material-symbols-outlined text-[22px]">add_box</span>
            <span class="hidden sm:block">استلام إرسالية</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6 font-outfit" dir="rtl" x-data="{ search: '' }">

        {{-- ===== الحاوية الرئيسية (شريط البحث + البيانات) ===== --}}
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-[2rem] dark:bg-boxdark dark:border-gray-800 transition-colors">

            {{-- شريط البحث --}}
            <div class="p-5 border-b border-gray-100 bg-gray-50/50 dark:bg-gray-900/30 dark:border-gray-800">
                <div class="relative flex-1 group">
                    <input type="text" x-model="search"
                        placeholder="ابحث برقم الإرسالية، السائق، أو الفرع المرسل..."
                        class="pr-11 pl-4 w-full h-12 text-sm font-medium placeholder-gray-400 bg-white rounded-xl border border-gray-200 transition-all outline-none dark:bg-gray-900 dark:border-gray-700 focus:border-primary focus:ring-2 focus:ring-primary/20 dark:text-white">
                    <div class="flex absolute inset-y-0 right-0 items-center pr-4 text-gray-400 transition-colors pointer-events-none group-focus-within:text-primary">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>
                </div>
            </div>

            {{-- ===== Mobile View (Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 lg:hidden">
                @forelse($packages as $package)
                    <a href="{{ route('shipmentpackage.incoming.show', $package->id) }}" 
                        class="block bg-white rounded-2xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-md hover:border-primary/30 overflow-hidden relative active:scale-[0.98] transition-all dark:bg-gray-800/40 dark:border-gray-700 group">
                        
                        {{-- شريط جانبي لوني يوضح الحالة --}}
                        <div class="absolute top-0 bottom-0 right-0 w-1.5 transition-colors {{ $package->status == 'delivered' ? 'bg-success-500' : 'bg-warning-500' }}"></div>

                        <div class="p-4 pr-5">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="block mb-1 text-[10px] font-black tracking-wider text-gray-400 uppercase dark:text-gray-500">رقم الإرسالية</span>
                                    <h3 class="font-mono text-sm font-black text-gray-900 transition-colors dark:text-white group-hover:text-primary">{{ $package->tracking_number }}</h3>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black inline-flex items-center gap-1 {{ $package->status == 'delivered' ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $package->status == 'delivered' ? 'bg-success-500' : 'bg-warning-500 animate-pulse' }}"></span>
                                    {{ $package->status == 'delivered' ? 'تم الاستلام' : 'قيد المعالجة' }}
                                </span>
                            </div>

                            <div class="flex gap-3 items-center p-3 mb-4 rounded-xl border border-dashed bg-gray-50/50 border-gray-200/70 dark:bg-gray-900/50 dark:border-gray-700">
                                <div class="flex justify-center items-center w-8 h-8 rounded-lg bg-primary/10 text-primary dark:bg-primary/20 shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">store</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500">واردة من فرع</p>
                                    <p class="text-xs font-bold text-gray-800 truncate dark:text-gray-200">{{ $package->senderBranch->name ?? 'غير محدد' }}</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700/50">
                                <div class="flex gap-1.5 items-center text-gray-500 dark:text-gray-400">
                                    <span class="material-symbols-outlined text-[16px]">local_shipping</span>
                                    <span class="text-[11px] font-bold truncate max-w-[120px]">{{ $package->driver->name ?? 'سائق غير محدد' }}</span>
                                </div>
                                
                                <div class="flex gap-1.5 items-center px-2.5 py-1 text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-700 dark:text-gray-300">
                                    <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                    <span class="text-[11px] font-black">{{ $package->shipments_count ?? 0 }} طرود</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col justify-center items-center py-16 rounded-2xl border-2 border-gray-100 border-dashed bg-gray-50/50 dark:bg-gray-800/20 dark:border-gray-700">
                        <div class="flex justify-center items-center mb-3 w-16 h-16 text-gray-400 bg-white rounded-full shadow-sm dark:bg-gray-900 dark:text-gray-500">
                            <span class="text-3xl material-symbols-outlined">move_to_inbox</span>
                        </div>
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 font-headline">لا توجد إرساليات واردة</p>
                        <p class="mt-1 text-[11px] font-bold text-gray-400">لم تقم باستلام أي إرساليات في فرعك بعد.</p>
                    </div>
                @endforelse
            </div>

            {{-- ===== Desktop View (Table) ===== --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full text-right">
                    <thead class="bg-gray-50/50 dark:bg-gray-800/50">
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] border-b border-gray-100 dark:border-gray-800 dark:text-gray-500">
                            <th class="px-6 py-4">رقم الإرسالية</th>
                            <th class="px-6 py-4">واردة من (الفرع)</th>
                            <th class="px-6 py-4">السائق الناقل</th>
                            <th class="px-6 py-4 text-center">عدد الطرود</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                        @forelse($packages as $package)
                            <tr class="bg-white transition-colors hover:bg-gray-50/50 dark:bg-transparent dark:hover:bg-gray-800/30 group">
                                
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-xl transition-colors dark:bg-gray-800 group-hover:bg-primary/10 group-hover:text-primary">
                                            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                        </div>
                                        <span class="font-mono text-sm font-black text-gray-900 transition-colors dark:text-white group-hover:text-primary">{{ $package->tracking_number }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex gap-2 items-center text-gray-700 dark:text-gray-300">
                                        <span class="material-symbols-outlined text-[18px] text-gray-400">store</span>
                                        <span class="text-sm font-bold">{{ $package->senderBranch->name ?? 'غير محدد' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex gap-2 items-center text-gray-600 dark:text-gray-400">
                                        <span class="material-symbols-outlined text-[18px] text-gray-400">local_shipping</span>
                                        <span class="text-sm font-bold">{{ $package->driver->name ?? 'سائق غير محدد' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex justify-center items-center px-3 h-8 text-xs font-black text-gray-700 bg-gray-100 rounded-lg dark:bg-gray-800 dark:text-gray-300">
                                        {{ $package->shipments_count ?? 0 }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase rounded-lg {{ $package->status == 'delivered' ? 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' : 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $package->status == 'delivered' ? 'bg-success-500' : 'bg-warning-500 animate-pulse' }}"></span>
                                        {{ $package->status == 'delivered' ? 'تم الاستلام' : 'قيد المعالجة' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex gap-2 justify-center items-center opacity-0 transition-opacity group-hover:opacity-100">
                                        <a href="{{ route('shipmentpackage.incoming.show', $package->id) }}" title="عرض التفاصيل واستلام الطرود"
                                            class="flex justify-center items-center w-9 h-9 text-gray-400 bg-white rounded-lg border border-gray-200 shadow-sm transition-colors hover:text-primary hover:border-primary/30 hover:bg-primary/5 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-primary/20">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col justify-center items-center">
                                        <div class="flex justify-center items-center mb-4 w-16 h-16 bg-gray-50 rounded-full dark:bg-gray-800/50">
                                            <span class="text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">move_to_inbox</span>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">لا توجد إرساليات واردة</h4>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">لم يتم استلام أي إرساليات لفرعك حتى الآن.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===== الترقيم (Pagination) ===== --}}
            @if($packages->hasPages())
                <div class="flex justify-center items-center px-6 py-6 w-full border-t border-gray-100 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/20">
                    <nav role="navigation" aria-label="Pagination Navigation" class="flex gap-2 justify-center items-center">
                        
                        {{-- زر الصفحة السابقة --}}
                        @if ($packages->onFirstPage())
                            <span class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-xl border border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600 shrink-0">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_left</span>
                            </span>
                        @else
                            <a href="{{ $packages->previousPageUrl() }}" class="flex justify-center items-center w-10 h-10 text-gray-600 bg-white rounded-xl border border-gray-200 shadow-sm transition-colors hover:bg-primary/5 hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 shrink-0">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_left</span>
                            </a>
                        @endif

                        {{-- أرقام الصفحات --}}
                        <div class="flex gap-1 items-center px-2 py-1.5 bg-white rounded-2xl border border-gray-200 shadow-sm dark:bg-boxdark dark:border-gray-700">
                            @foreach ($packages->elements() as $element)
                                @if (is_string($element))
                                    <span class="flex justify-center items-center w-8 h-8 text-sm font-bold text-gray-400">{{ $element }}</span>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $packages->currentPage())
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
                        @if ($packages->hasMorePages())
                            <a href="{{ $packages->nextPageUrl() }}" class="flex justify-center items-center w-10 h-10 text-gray-600 bg-white rounded-xl border border-gray-200 shadow-sm transition-colors hover:bg-primary/5 hover:text-primary hover:border-primary/30 dark:bg-boxdark dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 shrink-0">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_right</span>
                            </a>
                        @else
                            <span class="flex justify-center items-center w-10 h-10 text-gray-400 bg-gray-50 rounded-xl border border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600 shrink-0">
                                <span class="material-symbols-outlined text-[20px] rtl:rotate-180">chevron_right</span>
                            </span>
                        @endif
                        
                    </nav>
                </div>
            @endif

        </div>
    </div>
@endsection