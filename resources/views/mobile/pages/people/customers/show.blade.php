@extends('mobile.layouts.app')

@section('title', 'ملف العميل | ' . $customer->name)

@section('content')
    <div class="flex flex-col gap-6 px-4 pt-4 pb-24 min-h-screen bg-slate-50/50">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex justify-between items-center">
            <div class="flex gap-3 items-center">
                <a href="{{ route('customers.index') }}"
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <h1 class="text-xl font-black font-headline text-slate-800">ملف العميل</h1>
            </div>
            
            {{-- زر مراسلة العميل واتساب --}}
            <a href="https://wa.me/{{ ltrim($customer->phone, '+') }}" target="_blank"
                class="flex gap-2 items-center px-4 h-10 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform active:scale-95">
                <svg class="w-4 h-4 fill-emerald-500" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                مراسلة
            </a>
            <a href="{{ route('receipt.generate', ['type' => 'CustomerAccountStatementReceipt', 'id' => $customer->id]) }}" target="_blank"  class="flex gap-2 items-center px-4 h-10 text-xs font-bold rounded-xl border transition-transform border-pri-100 bg-emeprald-50 text-p-600 active:scale-95">
                <span class="material-symbols-outlined text-[18px] text-primary">receipt_long</span>
                كشف الحساب
            </a>
        </div>

        {{-- ================= بطاقة الهوية ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] flex items-center gap-4 relative overflow-hidden">
            <div class="flex justify-center items-center w-16 h-16 text-2xl font-black rounded-2xl border shadow-inner bg-primary/10 text-primary border-primary/20 shrink-0">
                @php
                    $words = explode(' ', $customer->name);
                    echo mb_substr($words[0] ?? '', 0, 1, 'utf-8') . (isset($words[1]) ? mb_substr($words[1], 0, 1, 'utf-8') : '');
                @endphp
            </div>
            <div>
                <h2 class="text-lg font-black text-slate-800 font-headline">{{ $customer->name }}</h2>
                <div class="flex gap-2 items-center mt-1.5 text-slate-500">
                    <span class="material-symbols-outlined text-[14px]">phone_iphone</span>
                    <p class="font-mono text-xs font-bold tracking-wider">{{ $customer->phone }}</p>
                </div>
            </div>
        </div>

        {{-- ================= الداشبورد المالي للعميل ================= --}}
        <div>
            <h3 class="flex gap-2 items-center mb-3 text-sm font-black text-slate-800">
                <span class="material-symbols-outlined text-primary text-[20px]">account_balance_wallet</span>
                الرصيد والمديونية (للشحنات الآجلة)
            </h3>

            <div class="grid grid-cols-2 gap-3">
                {{-- إجمالي المستحق عليه (أحمر إذا كان عليه دين) --}}
                <div class="col-span-2 bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 mb-1">إجمالي المبلغ المتبقي عليه</p>
                        <p class="text-2xl font-black font-headline {{ $grandTotalRemaining > 0 ? 'text-rose-500' : 'text-slate-800' }}">
                            {{ number_format($grandTotalRemaining, 0) }} <span class="text-xs text-slate-400">ريال</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-xl {{ $grandTotalRemaining > 0 ? 'bg-rose-50 text-rose-500' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center">
                        <span class="material-symbols-outlined text-[24px]">warning</span>
                    </div>
                </div>

                {{-- إجمالي قيمة الشحنات --}}
                <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                    <p class="text-[9px] font-bold text-slate-400 mb-1">إجمالي قيمة شحناته</p>
                    <p class="text-lg font-black text-slate-700 font-headline">{{ number_format($grandTotalCost, 0) }}</p>
                </div>

                {{-- إجمالي ما تم سداده --}}
                <div class="bg-white p-4 rounded-[1.5rem] border border-slate-100 shadow-sm flex flex-col justify-center">
                    <p class="text-[9px] font-bold text-slate-400 mb-1">إجمالي ما سدده</p>
                    <p class="text-lg font-black text-emerald-500 font-headline">{{ number_format($grandTotalPaid, 0) }}</p>
                </div>
            </div>
            
            @if($unpaidShipmentsCount > 0)
                <div class="mt-3 px-4 py-2.5 bg-rose-50/50 border border-rose-100 rounded-xl flex items-center gap-2 text-rose-600 text-[10px] font-bold">
                    <span class="material-symbols-outlined text-[16px]">info</span>
                    لدى العميل {{ $unpaidShipmentsCount }} شحنات لم يتم سدادها بالكامل!
                </div>
            @endif
        </div>

        {{-- ================= سجل الشحنات مع الفلترة ================= --}}
        <div class="mt-2">
            <div class="flex justify-between items-end mb-3">
                <h3 class="flex gap-2 items-center text-sm font-black text-slate-800">
                    <span class="material-symbols-outlined text-primary text-[20px]">history</span>
                    سجل الشحنات
                </h3>
            </div>

            {{-- شريط الفلترة الأفقي --}}
            <div class="flex overflow-x-auto gap-2 pb-2 mb-2 custom-scrollbar snap-x snap-mandatory">
                <a href="{{ request()->fullUrlWithQuery(['direction' => 'all', 'page' => null]) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $direction == 'all' ? 'bg-slate-800 text-white border-slate-800 shadow-[0_4px_12px_rgba(30,41,59,0.2)]' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    جميع الشحنات
                </a>
                
                <a href="{{ request()->fullUrlWithQuery(['direction' => 'sent', 'page' => null]) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $direction == 'sent' ? 'bg-primary text-white border-primary shadow-[0_4px_12px_rgba(var(--color-primary-rgb),0.2)]' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[14px] mr-1">arrow_upward</span> طرود صادرة منه
                </a>

                <a href="{{ request()->fullUrlWithQuery(['direction' => 'received', 'page' => null]) }}"
                    class="snap-start shrink-0 px-4 h-9 flex items-center justify-center rounded-xl text-[11px] font-bold transition-all border 
                    {{ $direction == 'received' ? 'bg-emerald-500 text-white border-emerald-500 shadow-[0_4px_12px_rgba(16,185,129,0.2)]' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                    <span class="material-symbols-outlined text-[14px] mr-1">arrow_downward</span> طرود واردة إليه
                </a>
            </div>

            {{-- قائمة الطرود (تصميم Ticket) --}}
            <div class="space-y-4">
                @forelse($shipments as $shipment)
                    <div class="bg-white rounded-[1.5rem] border border-slate-200/60 shadow-sm overflow-hidden relative">
                        
                        {{-- تحديد بصري: هل هو مرسل أم مستقبل في هذه الشحنة؟ --}}
                        <div class="flex justify-between items-center px-4 py-2 border-b border-slate-100 bg-slate-50/50">
                            @if($shipment->sender_customer_id == $customer->id)
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-primary/10 text-primary border border-primary/20 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">arrow_upward</span>
                                    مرسل
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">arrow_downward</span>
                                    مستلم
                                </span>
                            @endif

                            <span class="text-[10px] font-bold text-slate-400">{{ $shipment->created_at->format('Y-m-d') }}</span>
                        </div>

                        <div class="flex justify-between items-center p-4">
                            <div>
                                <h3 class="font-mono text-sm font-black tracking-tight text-slate-800">{{ $shipment->bond_number }}</h3>
                                <p class="text-[10px] font-bold text-slate-500 mt-1">
                                    @if($shipment->payment_method == 'customer_credit')
                                        <span class="px-1.5 py-0.5 text-rose-500 bg-rose-50 rounded">آجل</span>
                                    @elseif($shipment->payment_method == 'prepaid')
                                        مدفوع مقدماً
                                    @elseif($shipment->payment_method == 'cod')
                                        دفع عند الاستلام
                                    @else
                                        دفع جزئي
                                    @endif
                                    •
                                    <span class="font-black text-slate-800">{{ number_format($shipment->total_amount, 0) }} ريال</span>
                                </p>
                            </div>

                            <a href="{{ route('shipment.show', $shipment->id) }}" class="flex justify-center items-center w-10 h-10 rounded-full border transition-colors bg-slate-50 text-slate-400 hover:bg-primary hover:text-white border-slate-100">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-12 flex flex-col items-center justify-center bg-white rounded-[2rem] border-2 border-dashed border-slate-100 text-center">
                        <span class="mb-2 text-4xl material-symbols-outlined text-slate-300">package_2</span>
                        <p class="text-sm font-bold text-slate-500">لا توجد شحنات مطابقة للفلتر</p>
                    </div>
                @endforelse
            </div>

            {{-- الترقيم --}}
            @if($shipments->hasPages())
                <div class="mt-5">
                    {{ $shipments->links('vendor.pagination.mobile') }}
                </div>
            @endif
        </div>

    </div>
@endsection