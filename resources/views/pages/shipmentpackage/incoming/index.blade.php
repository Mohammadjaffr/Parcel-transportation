@extends('layouts.app')

@section('title', 'الشحنات المستقبلة (الواردة)')

@section('content')
<div class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl">

    {{-- ================= الشريط العلوي (Sticky Header) ================= --}}
    <div class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
        <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
            <div class="flex gap-4 items-center">
                <a href="{{ route('shipmentpackage.index') }}"
                    class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
                <div>
                    <h1 class="text-xl font-black md:text-2xl font-headline text-on-surface dark:text-white">الشحنات المستقبلة</h1>
                    <p class="text-[11px] md:text-xs text-gray-500 dark:text-bodydark mt-0.5">الإرساليات الواردة لفرعك</p>
                </div>
            </div>
            
            <a href="{{ route('shipmentpackage.incoming.create') }}"
                class="flex gap-2 justify-center items-center px-5 w-full h-11 text-sm font-bold text-white bg-orange-500 rounded-xl shadow-md transition-all hover:bg-orange-600 shadow-orange-500/20 active:scale-95 md:w-auto">
                <span class="text-[22px] material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">add_box</span>
                <span class="hidden md:inline">استلام إرسالية جديدة</span>
            </a>
        </div>
    </div>

    {{-- ================= محتوى الصفحة ================= --}}
    <div class="p-4 mx-auto mt-4 max-w-7xl md:p-6">
        
        <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden transition-colors">
            
            <div class="flex justify-between items-center p-5 bg-white border-b border-gray-100 dark:border-boxdark-2 dark:bg-boxdark">
                <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                    <div class="flex justify-center items-center w-8 h-8 text-blue-500 bg-blue-50 rounded-lg shadow-sm dark:bg-blue-500/10 shrink-0">
                        <span class="material-symbols-outlined text-[20px]">move_to_inbox</span>
                    </div>
                    قائمة الإرساليات الواردة
                </h3>
            </div>

            {{-- ===== عرض الديسكتوب (Data Table) ===== --}}
            <div class="hidden overflow-x-auto p-5 md:block">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] bg-surface dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark">
                            <th class="px-6 py-4">رقم الإرسالية</th>
                            <th class="px-6 py-4">واردة من فرع</th>
                            <th class="px-6 py-4">السائق</th>
                            <th class="px-6 py-4 text-center">عدد الطرود</th>
                            <th class="px-6 py-4 text-center">الحالة</th>
                            <th class="px-6 py-4 text-center">التفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-boxdark-2">
                        @forelse($packages as $package)
                            <tr class="relative transition-all hover:bg-surface/50 dark:hover:bg-boxdark-2/50 group">
                                
                                {{-- 1. رقم الإرسالية --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-3 items-center">
                                        <div class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark dark:border-boxdark group-hover:text-primary">
                                            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-mono text-sm font-black tracking-tight text-on-surface dark:text-white">{{ $package->tracking_number }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- 2. الفرع المرسل --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-2 items-center text-gray-800 dark:text-gray-200">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">store</span>
                                        <span class="text-xs font-bold truncate max-w-[150px]" title="{{ $package->senderBranch->name ?? 'غير محدد' }}">
                                            {{ $package->senderBranch->name ?? 'غير محدد' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- 3. السائق --}}
                                <td class="px-6 py-4">
                                    <div class="flex gap-2 items-center text-gray-800 dark:text-gray-200">
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">local_shipping</span>
                                        <span class="text-xs font-bold truncate max-w-[150px]" title="{{ $package->driver->name ?? 'سائق غير محدد' }}">
                                            {{ $package->driver->name ?? 'سائق غير محدد' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- 4. عدد الطرود --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex gap-1.5 items-center px-3 py-1.5 text-xs font-black rounded-lg bg-primary-container dark:bg-primary/10 text-primary">
                                        <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                                        {{ $package->shipments_count }} طرود
                                    </span>
                                </td>

                                {{-- 5. الحالة --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1.5 rounded-lg text-xs font-black border 
                                        {{ $package->status == 'delivered' ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' }}">
                                        {{ $package->status == 'delivered' ? 'تم الاستلام' : 'قيد المعالجة' }}
                                    </span>
                                </td>

                                {{-- 6. الإجراءات --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center">
                                        <a href="{{ route('shipmentpackage.incoming.show', $package->id) }}" title="التفاصيل"
                                            class="inline-flex justify-center items-center w-8 h-8 text-gray-400 rounded-lg border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:text-gray-500 hover:bg-primary-container hover:text-primary dark:hover:bg-primary/20 dark:hover:text-primary active:scale-95 dark:border-boxdark">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-20 text-center">
                                    <div class="flex flex-col justify-center items-center">
                                        <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                            <span class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">move_to_inbox</span>
                                        </div>
                                        <p class="text-sm font-bold text-gray-500 dark:text-bodydark font-headline">لا توجد إرساليات واردة</p>
                                        <p class="mt-1 text-xs font-bold text-gray-400">لم تقم باستلام أي إرساليات بعد.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===== عرض الموبايل (بطاقات Cards) ===== --}}
            <div class="flex flex-col gap-4 p-4 md:hidden">
                @forelse($packages as $package)
                    <a href="{{ route('shipmentpackage.incoming.show', $package->id) }}" 
                        class="block bg-surface dark:bg-boxdark-2 rounded-2xl border border-gray-100 dark:border-boxdark shadow-sm overflow-hidden relative active:scale-[0.98] transition-transform hover:border-primary/30">
                        
                        {{-- شريط جانبي لوني يوضح الحالة --}}
                        <div class="absolute right-0 top-0 bottom-0 w-1.5 {{ $package->status == 'delivered' ? 'bg-emerald-500' : 'bg-amber-500' }}"></div>

                        <div class="p-4 pr-5">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider block">رقم الإرسالية</span>
                                    <h3 class="font-mono text-sm font-black tracking-tight text-on-surface dark:text-white">{{ $package->tracking_number }}</h3>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black border
                                    {{ $package->status == 'delivered' ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' }}">
                                    {{ $package->status == 'delivered' ? 'تم الاستلام' : 'قيد المعالجة' }}
                                </span>
                            </div>

                            <div class="flex gap-2 items-center p-3 mb-4 bg-white rounded-xl border border-gray-50 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">store</span>
                                <div class="flex flex-col flex-1">
                                    <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 mb-0.5">واردة من فرع</p>
                                    <p class="text-xs font-bold text-on-surface dark:text-white truncate max-w-[200px]">{{ $package->senderBranch->name ?? 'غير محدد' }}</p>
                                </div>
                            </div>

                            <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-boxdark">
                                <div class="flex gap-1.5 items-center text-gray-500 dark:text-bodydark">
                                    <span class="material-symbols-outlined text-[16px] text-gray-400">local_shipping</span>
                                    <span class="text-[11px] font-bold truncate max-w-[120px]">{{ $package->driver->name ?? 'سائق غير محدد' }}</span>
                                </div>
                                
                                <div class="flex gap-1.5 items-center px-3 py-1.5 text-white rounded-lg border shadow-sm bg-boxdark dark:bg-black/30 dark:border-boxdark-2">
                                    <span class="material-symbols-outlined text-[14px] text-gray-300">inventory_2</span>
                                    <span class="text-[11px] font-black">{{ $package->shipments_count }} طرود</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                        <div class="flex justify-center items-center mb-4 w-16 h-16 text-gray-300 bg-white rounded-full border border-gray-50 shadow-sm dark:bg-boxdark dark:text-gray-600 dark:border-boxdark-2">
                            <span class="material-symbols-outlined text-[32px]">move_to_inbox</span>
                        </div>
                        <p class="text-sm font-bold text-gray-500 dark:text-bodydark font-headline">لا توجد إرساليات واردة</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-1">لم تقم باستلام أي إرساليات بعد.</p>
                    </div>
                @endforelse
            </div>

            {{-- الترقيم --}}
            @if(method_exists($packages, 'hasPages') && $packages->hasPages())
                <div class="p-5 border-t border-gray-50 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                    <div class="hidden md:block">
                        {{ $packages->links('vendor.pagination.tailwind') }}
                    </div>
                    <div class="md:hidden">
                        {{ $packages->links('vendor.pagination.mobile') }}
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection