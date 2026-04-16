@extends('layouts.app')

@section('title', 'تفاصيل المكتب: ' . $office->name)

@section('content')
<div class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl">

    {{-- ================= الشريط العلوي (Sticky Header) ================= --}}
    <div class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
        <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
            <div class="flex gap-4 items-center">
                <a href="{{ route('offices.unverified.index') }}"
                    class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
                <div>
                    <h1 class="text-xl font-black md:text-2xl font-headline text-on-surface dark:text-white">{{ $office->name }}</h1>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-bodydark">مكتب خارجي (غير موثوق)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= محتوى الصفحة (Grid Layout) ================= --}}
    <div class="grid grid-cols-1 gap-6 items-start p-4 mx-auto max-w-7xl md:p-6 xl:grid-cols-12">
        
        {{-- ================= الجانب الأيمن: الإحصائيات السريعة (Sidebar) ================= --}}
        <div class="xl:col-span-4 flex flex-col gap-4 xl:sticky xl:top-[5.5rem]">
            <div class="grid grid-cols-2 gap-4 xl:grid-cols-1">
                {{-- إجمالي الطرود --}}
                <div class="flex overflow-hidden relative gap-4 items-center p-5 bg-white rounded-2xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 group hover:shadow-md hover:border-primary/30">
                    <div class="absolute -bottom-4 -left-4 transition-transform duration-500 pointer-events-none text-primary/5 dark:text-primary/5 group-hover:scale-110">
                        <span class="material-symbols-outlined text-[80px]">inventory_2</span>
                    </div>
                    <div class="flex relative z-10 justify-center items-center w-12 h-12 rounded-xl shadow-sm bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                        <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[11px] font-bold text-gray-500 dark:text-bodydark mb-1">الطرود المرسلة</p>
                        <p class="text-2xl font-black leading-none text-on-surface dark:text-white font-headline">
                            {{ $shipments->total() }} <span class="text-[10px] font-bold text-gray-400">طرد</span>
                        </p>
                    </div>
                </div>

                {{-- الفروع المسجلة --}}
                <div class="flex overflow-hidden relative gap-4 items-center p-5 bg-white rounded-2xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark dark:border-boxdark-2 group hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-500/30">
                    <div class="absolute -bottom-4 -left-4 transition-transform duration-500 pointer-events-none text-emerald-500/5 dark:text-emerald-500/5 group-hover:scale-110">
                        <span class="material-symbols-outlined text-[80px]">account_tree</span>
                    </div>
                    <div class="flex relative z-10 justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-xl shadow-sm dark:bg-emerald-500/10 shrink-0">
                        <span class="material-symbols-outlined text-[24px]">account_tree</span>
                    </div>
                    <div class="relative z-10">
                        <p class="text-[11px] font-bold text-gray-500 dark:text-bodydark mb-1">الفروع المسجلة</p>
                        <p class="text-2xl font-black leading-none text-on-surface dark:text-white font-headline">
                            {{ $office->branches->count() }} <span class="text-[10px] font-bold text-gray-400">فرع</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= الجانب الأيسر: سجل الطرود الموجهة للمكتب ================= --}}
        <div class="flex flex-col gap-6 xl:col-span-8">
            
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden">
                
                {{-- هيدر السجل --}}
                <div class="flex justify-between items-center p-5 border-b border-gray-50 md:p-6 dark:border-boxdark-2">
                    <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                        <div class="flex justify-center items-center w-8 h-8 rounded-lg shadow-sm bg-primary-container dark:bg-primary/10 text-primary shrink-0">
                            <span class="material-symbols-outlined text-[20px]">history</span>
                        </div>
                        سجل الطرود الموجهة للمكتب
                    </h3>
                </div>

                {{-- عرض الديسكتوب (جدول احترافي) --}}
                <div class="hidden overflow-x-auto p-5 md:block">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] bg-surface dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark">
                                <th class="px-6 py-4">رقم السند</th>
                                <th class="px-6 py-4">المرسل إليه</th>
                                <th class="px-6 py-4 text-center">الفرع الوجهة</th>
                                <th class="px-6 py-4 text-center">التاريخ والوقت</th>
                                <th class="px-6 py-4 text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-boxdark-2">
                            @forelse($shipments as $shipment)
                                <tr class="transition-all hover:bg-surface/50 dark:hover:bg-boxdark-2/50 group">
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3 items-center">
                                            <div class="flex justify-center items-center w-10 h-10 text-gray-400 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark dark:border-boxdark group-hover:text-primary">
                                                <span class="material-symbols-outlined text-[20px]">package_2</span>
                                            </div>
                                            <span class="font-mono text-sm font-black text-on-surface dark:text-white">
                                                {{ $shipment->bond_number }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">
                                            {{ $shipment->receiverCustomer->name ?? 'غير محدد' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex gap-1.5 items-center px-2.5 py-1 text-xs font-bold rounded-md text-primary bg-primary-container dark:bg-primary/10 dark:text-primary">
                                            <span class="material-symbols-outlined text-[14px]">store</span>
                                            {{ $shipment->receiverOfficeBranch->name ?? '---' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $shipment->created_at->format('Y/m/d') }}</span>
                                            <span class="text-[10px] text-gray-400 dark:text-bodydark">{{ $shipment->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 border-amber-100 dark:border-amber-500/20',
                                                'in_transit' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border-blue-100 dark:border-blue-500/20',
                                                'delivered' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20',
                                                'returned' => 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border-rose-100 dark:border-rose-500/20',
                                            ];
                                            $statusLabels = ['pending' => 'قيد الانتظار', 'in_transit' => 'في الطريق', 'delivered' => 'تم التسليم', 'returned' => 'مرتجعة'];
                                        @endphp
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-black border {{ $statusClasses[$shipment->status] ?? 'bg-surface text-gray-600 dark:bg-boxdark-2 dark:text-gray-400' }}">
                                            {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                                        </span>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                                                <span class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">inventory_2</span>
                                            </div>
                                            <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا توجد طرود مسجلة لهذا المكتب حالياً</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- عرض الموبايل (بطاقات) --}}
                <div class="flex flex-col gap-4 p-4 md:hidden">
                    @forelse($shipments as $shipment)
                        <div class="overflow-hidden relative rounded-2xl border border-gray-100 shadow-sm transition-all bg-surface dark:bg-boxdark-2 dark:border-boxdark hover:border-primary/30">
                            
                            {{-- شريط لوني علوي يوضح الحالة --}}
                            <div class="absolute top-0 inset-x-0 h-1 opacity-80
                                @if($shipment->status == 'delivered') bg-emerald-500 
                                @elseif($shipment->status == 'in_transit') bg-blue-500 
                                @elseif($shipment->status == 'returned') bg-rose-500 
                                @else bg-amber-500 @endif">
                            </div>

                            <div class="flex justify-between items-start p-4 mt-1">
                                <div class="flex gap-3 items-center">
                                    <div class="flex justify-center items-center w-10 h-10 bg-white rounded-xl border border-gray-50 shadow-sm dark:bg-boxdark dark:border-boxdark-2 shrink-0">
                                        <span class="material-symbols-outlined text-gray-500 dark:text-bodydark text-[20px]">package_2</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-sm font-black tracking-tight text-on-surface dark:text-white font-headline">{{ $shipment->bond_number }}</h3>
                                        <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 mt-0.5 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                                            {{ $shipment->created_at->format('Y/m/d - H:i') }}
                                        </p>
                                    </div>
                                </div>

                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-amber-50 text-amber-600 ring-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400',
                                        'in_transit' => 'bg-blue-50 text-blue-600 ring-blue-500/20 dark:bg-blue-500/10 dark:text-blue-400',
                                        'delivered' => 'bg-emerald-50 text-emerald-600 ring-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                                        'returned' => 'bg-rose-50 text-rose-600 ring-rose-500/20 dark:bg-rose-500/10 dark:text-rose-400',
                                    ];
                                    $statusLabels = ['pending' => 'قيد الانتظار', 'in_transit' => 'في الطريق', 'delivered' => 'تم التسليم', 'returned' => 'مرتجعة'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-[9px] font-black ring-1 ring-inset {{ $statusClasses[$shipment->status] ?? 'bg-white text-gray-600 dark:bg-boxdark dark:text-gray-400' }}">
                                    {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                                </span>
                            </div>

                            <div class="flex overflow-hidden relative items-center h-4">
                                <div class="absolute -right-2 w-4 h-4 bg-white rounded-full border-l border-gray-100 shadow-inner dark:bg-boxdark dark:border-boxdark-2"></div>
                                <div class="w-full border-t-[1.5px] border-dashed border-gray-200 dark:border-boxdark-2"></div>
                                <div class="absolute -left-2 w-4 h-4 bg-white rounded-full border-r border-gray-100 shadow-inner dark:bg-boxdark dark:border-boxdark-2"></div>
                            </div>

                            <div class="p-4 space-y-4">
                                <div class="flex gap-4 justify-between items-start">
                                    <div class="flex gap-3 items-stretch w-1/2">
                                        <div class="flex flex-col items-center mt-1">
                                            <div class="w-2 h-2 bg-white rounded-full border-2 border-gray-300 dark:border-gray-500 dark:bg-boxdark"></div>
                                            <div class="w-[1px] h-8 bg-gray-200 dark:bg-boxdark-2 my-0.5"></div>
                                            <div class="w-2 h-2 bg-white rounded-full border-2 border-primary dark:bg-boxdark"></div>
                                        </div>
                                        <div class="flex flex-col justify-between">
                                            <div>
                                                <p class="text-[8px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-tighter">المستلم</p>
                                                <p class="text-[11px] font-bold text-on-surface dark:text-white truncate max-w-[100px]">{{ $shipment->receiverCustomer->name ?? 'غير محدد' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[8px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-tighter">الوجهة</p>
                                                <p class="text-[11px] font-bold text-primary truncate max-w-[100px]">{{ $shipment->receiverOfficeBranch->name ?? '---' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 justify-center p-3 w-1/2 bg-white rounded-xl border border-gray-100 dark:bg-boxdark dark:border-boxdark-2">
                                        <div class="flex justify-between items-center text-[10px]">
                                            <span class="font-bold text-gray-400 dark:text-gray-500 text-[9px]">الوزن:</span>
                                            <span class="font-black text-on-surface dark:text-white">{{ $shipment->weight ?? '0' }} كجم</span>
                                        </div>
                                        <div class="flex justify-between items-center text-[10px]">
                                            <span class="font-bold text-gray-400 dark:text-gray-500 text-[9px]">النوع:</span>
                                            <span class="font-black text-on-surface dark:text-white">
                                                @if($shipment->package_type == 'carton') كرتون
                                                @elseif($shipment->package_type == 'bag') كيس 
                                                @else أخرى @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- كبسولة الفوتر המظلمة (المالية) --}}
                                <div class="flex justify-between items-center p-3 rounded-2xl shadow-lg bg-boxdark dark:bg-black/30 dark:border dark:border-boxdark-2">
                                    <div class="flex gap-2 items-center">
                                        <div class="flex justify-center items-center w-8 h-8 text-amber-400 rounded-xl bg-boxdark-2 dark:bg-boxdark">
                                            <span class="material-symbols-outlined text-[16px]">payments</span>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-black text-gray-400">الإجمالي</p>
                                            <p class="text-[11px] font-black text-white">{{ number_format($shipment->total_amount, 0) }} ريال</p>
                                        </div>
                                    </div>

                                    <a href="{{ route('shipment.show', $shipment->id) }}" class="h-8 px-3 bg-white/10 hover:bg-white/20 text-white rounded-lg text-[10px] font-black flex items-center gap-1 transition-all active:scale-95 border border-white/5">
                                        التفاصيل
                                        <span class="material-symbols-outlined text-[14px]">arrow_back_ios</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col justify-center items-center py-16 text-center rounded-2xl border-2 border-gray-100 border-dashed bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                            <div class="flex justify-center items-center mb-4 w-16 h-16 text-gray-300 bg-white rounded-full border border-gray-50 shadow-sm dark:bg-boxdark dark:text-gray-600 dark:border-boxdark-2">
                                <span class="text-4xl material-symbols-outlined">inventory_2</span>
                            </div>
                            <p class="text-sm font-bold text-gray-500 font-headline dark:text-bodydark">لا توجد طرود مسجلة</p>
                        </div>
                    @endforelse
                </div>

                {{-- الترقيم --}}
                @if($shipments->hasPages())
                    <div class="p-5 border-t border-gray-50 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                        <div class="hidden md:block">
                            {{ $shipments->links('vendor.pagination.tailwind') }}
                        </div>
                        <div class="md:hidden">
                            {{ $shipments->links('vendor.pagination.mobile') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection