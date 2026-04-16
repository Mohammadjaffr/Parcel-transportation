@extends('layouts.app')

@section('title', 'ملف العميل | ' . $customer->name)

@section('content')
<div class="pb-24 min-h-screen bg-surface dark:bg-boxdark-2 font-body lg:pb-12" dir="rtl">

    {{-- ================= الشريط العلوي (Sticky Header) ================= --}}
    <div class="sticky top-0 z-40 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90 dark:bg-boxdark/90 dark:border-boxdark-2">
        <div class="flex justify-between items-center px-4 py-4 mx-auto max-w-7xl md:px-6">
            <div class="flex gap-4 items-center">
                <a href="{{ route('customers.index') }}"
                    class="flex justify-center items-center w-10 h-10 text-gray-500 rounded-xl border border-gray-100 shadow-sm transition-colors bg-surface dark:bg-boxdark-2 dark:text-bodydark hover:text-primary dark:hover:text-white dark:border-boxdark active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
                <div>
                    <h1 class="text-xl font-black md:text-2xl font-headline text-on-surface dark:text-white">ملف العميل</h1>
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-bodydark">تفاصيل الحساب وسجل الشحنات</p>
                </div>
            </div>
              {{-- زر إضافة شحنة جديدة للعميل (يظهر في الديسكتوب بشكل بارز) --}}
            <a href="{{ route('shipmentpackage.outgoing.create') }}"
                class="hidden gap-2 justify-center items-center p-1 h-14 text-sm font-black text-white rounded-2xl shadow-lg transition-all 1 w-100 xl:flex bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-95">
                <span class="material-symbols-outlined text-[24px]">add_box</span>
                إنشاء شحنة جديدة
            </a>
            {{-- زر مراسلة العميل واتساب (يظهر كأيقونة في الموبايل ونص في الديسكتوب) --}}
            <a href="https://wa.me/{{ ltrim($customer->phone, '+') }}" target="_blank"
                class="flex gap-2 justify-center items-center px-3 h-10 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform md:px-4 dark:bg-emerald-500/10 dark:text-emerald-400 active:scale-95 dark:border-emerald-500/20 hover:shadow-md hover:bg-emerald-100 dark:hover:bg-emerald-500/20">
                <svg class="w-4 h-4 fill-emerald-500" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                <span class="hidden md:inline">مراسلة واتساب</span>
            </a>
        </div>
    </div>

    {{-- ================= محتوى الصفحة (Grid Layout) ================= --}}
    <div x-data="{ searchQuery: '' }" class="grid grid-cols-1 gap-6 items-start p-4 mx-auto max-w-7xl md:p-6 xl:grid-cols-12">
        
        {{-- ================= الجانب الأيمن: بيانات العميل (Sidebar) ================= --}}
        <div class="xl:col-span-4 flex flex-col gap-6 xl:sticky xl:top-[5.5rem]">
            
            {{-- بطاقة الهوية --}}
            <div class="bg-white dark:bg-boxdark p-6 rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm relative overflow-hidden flex flex-col items-center text-center gap-4">
                <div class="w-24 h-24 rounded-[1.5rem] bg-primary-container dark:bg-primary/10 text-primary flex items-center justify-center text-4xl font-black shadow-inner border border-primary/20 dark:border-primary/10 shrink-0">
                    @php
                        $words = explode(' ', $customer->name);
                        echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') . (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                    @endphp
                </div>
                <div>
                    <h2 class="text-xl font-black text-on-surface dark:text-white font-headline">{{ $customer->name }}</h2>
                    <div class="flex gap-2 justify-center items-center mt-2 text-gray-500 dark:text-bodydark">
                        <span class="material-symbols-outlined text-[16px]">phone_iphone</span>
                        <p class="font-mono text-sm font-bold tracking-wider dir-ltr">{{ $customer->phone }}</p>
                    </div>
                </div>
            </div>

            {{-- الداشبورد المالي للعميل --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm p-6">
                <h3 class="flex gap-2 items-center mb-5 text-base font-black text-on-surface dark:text-white">
                    <span class="material-symbols-outlined text-primary bg-primary-container dark:bg-primary/10 p-1.5 rounded-lg text-[18px]">account_balance_wallet</span>
                    الملخص المالي
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    {{-- إجمالي المستحق عليه --}}
                    <div class="col-span-2 bg-surface dark:bg-boxdark-2 p-5 rounded-[1.5rem] border border-gray-100 dark:border-boxdark shadow-sm flex items-center justify-between transition-all hover:shadow-md hover:border-gray-200 dark:hover:border-gray-700">
                        <div>
                            <p class="mb-1 text-xs font-bold text-gray-500 dark:text-bodydark">المديونية (متبقي عليه)</p>
                            <p class="text-3xl font-black font-headline {{ $grandTotalRemaining > 0 ? 'text-rose-500 dark:text-rose-400' : 'text-on-surface dark:text-white' }}">
                                {{ number_format($grandTotalRemaining, 0) }} <span class="text-xs text-gray-400">ريال</span>
                            </p>
                        </div>
                        <div class="w-12 h-12 rounded-xl {{ $grandTotalRemaining > 0 ? 'bg-rose-50 text-rose-500 dark:bg-rose-500/10 dark:text-rose-400' : 'bg-white dark:bg-boxdark text-gray-400 dark:text-gray-500' }} flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-[24px]">{{ $grandTotalRemaining > 0 ? 'warning' : 'check_circle' }}</span>
                        </div>
                    </div>

                    {{-- إجمالي قيمة الشحنات --}}
                    <div class="bg-surface dark:bg-boxdark-2 p-4 rounded-[1.5rem] border border-gray-100 dark:border-boxdark shadow-sm flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-gray-500 dark:text-bodydark mb-1">إجمالي شحناته</p>
                        <p class="text-xl font-black text-gray-700 dark:text-gray-200 font-headline">{{ number_format($grandTotalCost, 0) }}</p>
                    </div>

                    {{-- إجمالي ما تم سداده --}}
                    <div class="bg-surface dark:bg-boxdark-2 p-4 rounded-[1.5rem] border border-gray-100 dark:border-boxdark shadow-sm flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-gray-500 dark:text-bodydark mb-1">إجمالي ما سدده</p>
                        <p class="text-xl font-black text-emerald-500 dark:text-emerald-400 font-headline">{{ number_format($grandTotalPaid, 0) }}</p>
                    </div>
                </div>
                
                @if($unpaidShipmentsCount > 0)
                    <div class="flex gap-2.5 items-start px-4 py-3 mt-4 text-xs font-bold leading-relaxed text-rose-600 rounded-xl border border-rose-100 bg-rose-50/50 dark:bg-rose-500/5 dark:border-rose-500/20 dark:text-rose-400">
                        <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">info</span>
                        <div>يوجد لدى العميل <span class="px-1 font-black">{{ $unpaidShipmentsCount }}</span> شحنات غير مسددة أو مسددة جزئياً، يرجى المتابعة.</div>
                    </div>
                @endif
            </div>

          
        </div>

        {{-- ================= الجانب الأيسر: سجل الشحنات ================= --}}
        <div class="flex flex-col gap-6 xl:col-span-8">
            
            {{-- لوحة الفلترة والبحث --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm p-5 md:p-6">
                <div class="flex flex-col gap-4 justify-between items-start mb-5 md:flex-row md:items-center">
                    <h3 class="flex gap-2 items-center text-lg font-black text-on-surface dark:text-white font-headline">
                        <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                        سجل الشحنات
                    </h3>

                    {{-- شريط البحث --}}
                    <div class="relative w-full group md:w-64">
                        <span class="absolute right-3 top-1/2 text-gray-400 transition-colors -translate-y-1/2 material-symbols-outlined dark:text-bodydark group-focus-within:text-primary">search</span>
                        <input type="text" x-model="searchQuery" placeholder="ابحث برقم التتبع..."
                            class="pr-10 pl-4 w-full h-11 text-sm font-bold placeholder-gray-400 rounded-xl border border-gray-100 transition-all outline-none bg-surface dark:bg-boxdark-2 dark:border-boxdark focus:ring-2 focus:ring-primary/20 focus:border-primary dark:text-white dark:placeholder-bodydark">
                    </div>
                </div>

                {{-- شريط الفلترة الأفقي --}}
                <div class="flex overflow-x-auto gap-2 pb-2 custom-scrollbar">
                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'all', 'page' => null]) }}"
                        class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                        {{ $direction == 'all' ? 'bg-boxdark text-white border-boxdark shadow-md dark:bg-primary dark:border-primary dark:shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                        الكل
                    </a>
                    
                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'sent', 'page' => null]) }}"
                        class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                        {{ $direction == 'sent' ? 'bg-primary text-white border-primary shadow-md shadow-primary/20' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                        <span class="material-symbols-outlined text-[16px] mr-1.5">arrow_upward</span> مرسلة منه
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['direction' => 'received', 'page' => null]) }}"
                        class="shrink-0 px-5 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
                        {{ $direction == 'received' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/20 dark:bg-emerald-600 dark:border-emerald-600' : 'bg-surface text-gray-500 border-gray-100 hover:bg-gray-100 dark:bg-boxdark-2 dark:text-gray-400 dark:border-boxdark dark:hover:bg-boxdark' }}">
                        <span class="material-symbols-outlined text-[16px] mr-1.5">arrow_downward</span> واردة إليه
                    </a>
                </div>
            </div>

            {{-- ================= قائمة الشحنات (Desktop Table / Mobile Cards) ================= --}}
            <div class="bg-white dark:bg-boxdark rounded-[2rem] border border-gray-100 dark:border-boxdark-2 shadow-sm overflow-hidden">
                
                {{-- Desktop View (Table) --}}
                <div class="hidden overflow-x-auto p-5 md:block">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="text-[11px] font-black text-gray-400 uppercase tracking-[0.1em] bg-surface dark:bg-boxdark-2 dark:text-bodydark border-b border-gray-100 dark:border-boxdark">
                                <th class="px-6 py-4">رقم التتبع</th>
                                <th class="px-6 py-4">الاتجاه</th>
                                <th class="px-6 py-4 text-center">المبلغ المالي</th>
                                <th class="px-6 py-4 text-center">التاريخ</th>
                                <th class="px-6 py-4 text-center">التفاصيل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-boxdark-2">
                            @forelse($shipments as $shipment)
                                <tr x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery)" x-transition
                                    class="transition-all hover:bg-surface/50 dark:hover:bg-boxdark-2/50 group">
                                    
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3 items-center">
                                            <div class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark dark:border-boxdark-2 dark:text-bodydark group-hover:border-primary/30">
                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                            </div>
                                            <span class="font-mono text-sm font-black text-on-surface dark:text-white">
                                                {{ $shipment->bond_number }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($shipment->sender_customer_id == $customer->id)
                                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-black bg-primary/10 text-primary border border-primary/20 items-center gap-1.5">
                                                <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
                                                مرسل
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 items-center gap-1.5">
                                                <span class="material-symbols-outlined text-[14px]">arrow_downward</span>
                                                مستلم
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-black text-on-surface dark:text-white">{{ number_format($shipment->total_amount, 0) }} ريال</span>
                                            <span class="text-[10px] font-bold mt-1 px-1.5 py-0.5 rounded {{ $shipment->payment_method == 'customer_credit' ? 'text-rose-500 bg-rose-50 dark:bg-rose-500/10 dark:text-rose-400' : 'text-gray-500 bg-gray-50 dark:bg-boxdark-2 dark:text-gray-400' }}">
                                                @if($shipment->payment_method == 'customer_credit') آجل
                                                @elseif($shipment->payment_method == 'prepaid') مدفوع مقدماً
                                                @elseif($shipment->payment_method == 'cod') دفع عند الاستلام
                                                @else دفع جزئي @endif
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="flex gap-1.5 justify-center items-center text-xs font-bold text-gray-500 dark:text-bodydark">
                                            <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                            {{ $shipment->created_at->format('Y-m-d') }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('shipment.show', $shipment->id) }}"
                                            class="inline-flex p-2 text-gray-400 rounded-xl border border-gray-100 shadow-sm transition-all bg-surface hover:text-primary hover:bg-primary-container hover:border-primary/20 dark:bg-boxdark-2 dark:border-boxdark dark:hover:bg-primary/10 dark:hover:border-primary/30"
                                            title="عرض التفاصيل">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="flex flex-col justify-center items-center">
                                            <div class="flex justify-center items-center mb-4 w-16 h-16 rounded-full bg-surface dark:bg-boxdark-2">
                                                <span class="material-symbols-outlined text-[32px] text-gray-300 dark:text-gray-600">package_2</span>
                                            </div>
                                            <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا توجد شحنات مطابقة للفلتر أو البحث</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View (Cards) --}}
                <div class="flex flex-col gap-4 p-5 md:hidden">
                    @forelse($shipments as $shipment)
                        <div x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery)" x-transition
                            class="overflow-hidden relative rounded-2xl border border-gray-100 shadow-sm bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                            
                            {{-- تحديد بصري: مرسل أم مستقبل --}}
                            <div class="flex justify-between items-center px-4 py-2.5 bg-white border-b border-gray-100 dark:border-boxdark dark:bg-boxdark">
                                @if($shipment->sender_customer_id == $customer->id)
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-primary-container dark:bg-primary/10 text-primary border border-primary/20 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">arrow_upward</span>
                                        مرسل
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">arrow_downward</span>
                                        مستلم
                                    </span>
                                @endif
                                <span class="text-[10px] font-bold text-gray-400 dark:text-bodydark">{{ $shipment->created_at->format('Y-m-d') }}</span>
                            </div>

                            <div class="flex justify-between items-center p-4">
                                <div>
                                    <h3 class="font-mono text-sm font-black tracking-tight text-on-surface dark:text-white">{{ $shipment->bond_number }}</h3>
                                    <p class="text-[10px] font-bold text-gray-500 dark:text-bodydark mt-1 flex items-center gap-1.5">
                                        @if($shipment->payment_method == 'customer_credit')
                                            <span class="px-1.5 py-0.5 text-rose-500 bg-rose-50 rounded dark:bg-rose-500/10 dark:text-rose-400">آجل</span>
                                        @elseif($shipment->payment_method == 'prepaid')
                                            مدفوع مقدماً
                                        @elseif($shipment->payment_method == 'cod')
                                            دفع عند الاستلام
                                        @else
                                            دفع جزئي
                                        @endif
                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                        <span class="font-black text-on-surface dark:text-gray-200">{{ number_format($shipment->total_amount, 0) }} ريال</span>
                                    </p>
                                </div>

                                <a href="{{ route('shipment.show', $shipment->id) }}" class="flex justify-center items-center w-10 h-10 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-colors dark:bg-boxdark hover:bg-primary hover:text-white dark:border-boxdark">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 flex flex-col items-center justify-center bg-white dark:bg-boxdark rounded-[2rem] border-2 border-dashed border-gray-100 dark:border-boxdark-2 text-center">
                            <span class="mb-3 text-4xl text-gray-300 material-symbols-outlined dark:text-gray-600">package_2</span>
                            <p class="text-sm font-bold text-gray-500 dark:text-bodydark">لا توجد شحنات مطابقة للفلتر</p>
                        </div>
                    @endforelse
                </div>

                {{-- الترقيم --}}
                @if($shipments->hasPages())
                    <div class="p-5 md:p-6 border-t border-gray-50 dark:border-boxdark-2 bg-surface/50 dark:bg-boxdark-2/50 rounded-b-[2rem]">
                        {{ $shipments->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection