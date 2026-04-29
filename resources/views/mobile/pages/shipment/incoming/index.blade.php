@extends('mobile.layouts.app')

@section('title', 'الطرود الواردة')

@section('content')
    <div x-data="{ searchQuery: '' }" class="flex relative flex-col gap-6 px-4 pb-24 min-h-screen bg-slate-50/50">

        {{-- ================= الرأس (Header) ================= --}}
        <div class="flex justify-between items-center mt-6">
            <div class="flex gap-3 items-center">
                {{-- زر الرجوع --}}
                <a href="javascript:history.back()"
                    class="flex justify-center items-center w-11 h-11 bg-white rounded-2xl border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div class="flex flex-col">
                    <h1 class="text-2xl font-black font-headline text-slate-800">الطرود الواردة</h1>
                    <p class="mt-0.5 text-xs font-bold text-slate-500">
                        إجمالي <span class="font-black text-primary">{{ $shipments->total() ?? 0 }}</span> طرد بالمستودع
                    </p>
                </div>
            </div>

            {{-- أيقونة جمالية بدلاً من زر الإضافة --}}
            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-[1rem] flex items-center justify-center border border-emerald-100 shrink-0">
                <span class="material-symbols-outlined text-[26px]">inventory_2</span>
            </div>
        </div>

        {{-- ================= شريط البحث ================= --}}
        <div class="relative">
            <input type="text" x-model="searchQuery" placeholder="ابحث برقم السند، أو هاتف المستلم..."
                class="pr-4 pl-12 w-full h-14 text-sm font-bold bg-white rounded-2xl border-none shadow-sm outline-none focus:ring-2 focus:ring-emerald-500/20 text-slate-700 placeholder-slate-400">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
        </div>

        {{-- ================= شريط الفلترة حسب الحالة ================= --}}
<div class="flex overflow-x-auto gap-2 pb-2 mt-2 custom-scrollbar snap-x snap-mandatory">
    
    {{-- الكل --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => null]) }}"
        class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ !request('status') ? 'bg-slate-800 text-white border-slate-800 shadow-[0_4px_12px_rgba(30,41,59,0.2)]' : 'bg-white text-slate-500 border-slate-100 hover:bg-slate-50' }}">
        الكل
    </a>

    {{-- 1. قيد التجهيز (بالمصدر) --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => null]) }}"
        class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-[0_4px_12px_rgba(245,158,11,0.2)]' : 'bg-white text-amber-600 border-amber-100 hover:bg-amber-50' }}">
        قيد التجهيز بالمصدر
    </a>

    {{-- 2. في الطريق إلينا --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'in_transit', 'page' => null]) }}"
        class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'in_transit' ? 'bg-blue-500 text-white border-blue-500 shadow-[0_4px_12px_rgba(59,130,246,0.2)]' : 'bg-white text-blue-600 border-blue-100 hover:bg-blue-50' }}">
        في الطريق إلينا
    </a>

    {{-- 3. بالمستودع (وصل فرعنا) --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'received_at_branch', 'page' => null]) }}"
        class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'received_at_branch' ? 'bg-purple-500 text-white border-purple-500 shadow-[0_4px_12px_rgba(168,85,247,0.2)]' : 'bg-white text-purple-600 border-purple-100 hover:bg-purple-50' }}">
        بالمستودع (جاهز للتسليم)
    </a>

    {{-- 5. تم التسليم --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'delivered', 'page' => null]) }}"
        class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'delivered' ? 'bg-emerald-500 text-white border-emerald-500 shadow-[0_4px_12px_rgba(16,185,129,0.2)]' : 'bg-white text-emerald-600 border-emerald-100 hover:bg-emerald-50' }}">
        تم تسليمه للعميل
    </a>

    {{-- 6. مرتجع --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'returned', 'page' => null]) }}"
        class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'returned' ? 'bg-rose-500 text-white border-rose-500 shadow-[0_4px_12px_rgba(244,63,94,0.2)]' : 'bg-white text-rose-600 border-rose-100 hover:bg-rose-50' }}">
        مرتجع
    </a>

    {{-- 7. ملغي --}}
    <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled', 'page' => null]) }}"
        class="snap-start shrink-0 px-4 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all border 
            {{ request('status') == 'cancelled' ? 'bg-slate-500 text-white border-slate-500 shadow-[0_4px_12px_rgba(100,116,139,0.2)]' : 'bg-white text-slate-600 border-slate-100 hover:bg-slate-50' }}">
        ملغي
    </a>
</div>
        {{-- ================= قائمة الطرود ================= --}}
        <div class="space-y-5">
            @forelse($shipments as $shipment)
                <div x-show="searchQuery === '' || '{{ $shipment->code }}'.includes(searchQuery) || '{{ $shipment->receiverCustomer?->phone }}'.includes(searchQuery)"
                    class="bg-white rounded-[24px] border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] overflow-visible transition-all duration-300 relative group">

                    {{-- شريط لوني علوي خفيف يعطي طابعاً مميزاً للوارد (أخضر إلى أزرق) --}}
                    <div
                        class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-400/80 to-blue-400/80 rounded-t-[24px] opacity-70">
                    </div>

                    {{-- ================= 1. الرأس (Header) ================= --}}
                    <div class="flex justify-between items-start p-5">
                        <div class="flex gap-3 items-center">
                            <div
                                class="w-11 h-11 rounded-[14px] bg-slate-50 flex items-center justify-center border border-slate-100/80 group-hover:scale-105 transition-transform duration-300">
                                <span class="material-symbols-outlined text-slate-500 text-[22px]">inventory_2</span>
                            </div>
                            <div class="flex flex-col">
                                <h3 class="text-sm font-black tracking-tight text-slate-900 font-headline">
                                    {{ $shipment->code }}
                                </h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">schedule</span>
                                    {{ $shipment->created_at->format('Y/m/d - H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2 items-center">
                            {{-- شارة الحالة --}}
                            @if($shipment->status == 'pending')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-600 ring-1 ring-amber-500/20 ring-inset">بالمستودع</span>
                            @elseif($shipment->status == 'in_transit')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 ring-1 ring-blue-500/20 ring-inset">في الطريق</span>
                            @elseif($shipment->status == 'delivered')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20 ring-inset">تم التسليم</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-50 text-slate-500 ring-1 ring-slate-500/20 ring-inset">مرتجع</span>
                            @endif

                            {{-- قائمة الثلاث نقاط --}}
                            <div x-data="{ openMenu: false }" class="relative">
                                <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                    class="flex justify-center items-center w-8 h-8 rounded-full transition-colors text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                    <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                </button>

                                <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                    class="absolute top-full left-0 mt-1.5 w-44 bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100/50 z-50 overflow-hidden py-1.5">

                                    {{-- عرض التفاصيل (توجيه للصفحة المخصصة للوارد) --}}
                                    <a href="{{ route('shipment.incoming.show', $shipment->id) }}"
                                        class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-slate-50 hover:text-emerald-600">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        التفاصيل والتسليم
                                    </a>

                                    {{-- طباعة السند --}}
                                    <a href="{{ route('receipt.generate', ['type' => 'Shipment', 'id' => $shipment->id]) }}" target="_blank"
                                        class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-slate-50 hover:text-emerald-600">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                        طباعة السند
                                    </a>

                                    <div class="mx-3 my-1 h-px bg-slate-100/80"></div>

                                    {{-- إرسال للمستلم عبر الواتساب --}}
                                    @if($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                        @php
                                            $receiverMsg = "مرحباً *" . $shipment->receiverCustomer->name . "*،\nنفيدك بوصول طردك برقم السند: *" . $shipment->code . "* إلى فرعنا.\n" . ($shipment->payment_method !== 'prepaid' ? "المبلغ المطلوب عند الاستلام: *" . number_format($shipment->total_amount, 0) . "* ريال." : "الطرد خالص الدفع.");
                                        @endphp
                                        <a href="https://wa.me/{{ ltrim($shipment->receiverCustomer->phone, '+') }}?text={{ urlencode($receiverMsg) }}"
                                            target="_blank"
                                            class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold transition-colors text-slate-600 hover:bg-emerald-50 hover:text-emerald-700">
                                            <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                            </svg>
                                            إشعار المستلم
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ================= 2. الفاصل المقطّع ================= --}}
                    <div class="flex overflow-hidden relative items-center h-4">
                        <div class="absolute -right-2 w-4 h-4 rounded-full border-l shadow-inner bg-slate-50/50 border-slate-200/60"></div>
                        <div class="w-full border-t-[1.5px] border-dashed border-slate-200/70"></div>
                        <div class="absolute -left-2 w-4 h-4 rounded-full border-r shadow-inner bg-slate-50/50 border-slate-200/60"></div>
                    </div>

                    {{-- ================= 3. جسد البطاقة (The Journey & Data) ================= --}}
                    <div class="p-5 pt-4 space-y-5">
                        <div class="flex gap-4 justify-between items-start">

                            {{-- العمود الأيمن: خط السير (المصدر -> المستلم) --}}
                            <div class="flex gap-3 items-stretch w-1/2">
                                <div class="flex flex-col items-center mt-1">
                                    <div class="w-2.5 h-2.5 rounded-full border-[2.5px] border-slate-300 bg-white z-10"></div>
                                    <div class="w-[1.5px] h-10 bg-slate-200 my-0.5"></div>
                                    <div class="w-2.5 h-2.5 rounded-full border-[2.5px] border-emerald-500 bg-white z-10 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                                </div>

                                <div class="flex flex-col flex-1 justify-between space-y-4">
                                    {{-- المصدر --}}
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">وارد من</p>
                                        <p class="text-xs font-bold text-slate-800 truncate max-w-[100px]">
                                            {{ $shipment->sender->name ?? 'مصدر غير محدد' }}
                                        </p>
                                    </div>

                                    {{-- المستلم (العميل) --}}
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide flex items-center gap-1">
                                                المستلم
                                            </p>
                                            <p class="text-xs font-black text-slate-800 truncate max-w-[100px]">
                                                {{ $shipment->receiverCustomer?->name ?? 'غير مسجل' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- العمود الأيسر: المساحة المستغلة (تفاصيل الطرد الدقيقة) --}}
                            <div class="flex flex-col gap-2.5 p-3 w-1/2 rounded-xl border bg-slate-50/70 border-slate-100/80">
                                {{-- الوزن والنوع --}}
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] text-slate-400 font-bold">المحتوى:</span>
                                    <span class="text-[10px] font-black text-slate-700 bg-white px-2 py-0.5 rounded-md border border-slate-100 shadow-sm">
                                        @if($shipment->package_type == 'carton') كرتون @elseif($shipment->package_type == 'bag') كيس @elseif($shipment->package_type == 'envelope') مغلف @else أخرى @endif
                                        @if($shipment->weight > 0) <span class="text-slate-400">({{ $shipment->weight }} كجم)</span> @endif
                                    </span>
                                </div>

                                {{-- تفاصيل العسل (إن وجدت) --}}
                                @if($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0)
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] text-amber-500 font-bold">عسل:</span>
                                        <span class="text-[10px] font-bold text-slate-600">
                                            @if($shipment->no_gallons_honey > 0) {{ $shipment->no_gallons_honey }} دباب @endif
                                            @if($shipment->no_gallons_honey > 0 && $shipment->no_honey_jars > 0) + @endif
                                            @if($shipment->no_honey_jars > 0) {{ $shipment->no_honey_jars }} قوارير @endif
                                        </span>
                                    </div>
                                @endif

                                {{-- الدفع الجزئي --}}
                                @if($shipment->payment_method == 'partial_payment')
                                    <div class="flex justify-between items-center pt-2 mt-1 border-t border-slate-200/50">
                                        <span class="text-[10px] text-rose-500 font-bold">المتبقي:</span>
                                        <span class="text-[11px] font-black text-rose-600">
                                            {{ number_format($shipment->total_amount - $shipment->partial_amount, 0) }} ريال
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ================= 4. كبسولة المالية (Footer Module) ================= --}}
                        <div class="bg-slate-800 rounded-[18px] p-3.5 flex justify-between items-center shadow-lg shadow-slate-900/10">
                            {{-- تفاصيل الدفع --}}
                            <div class="flex gap-2.5 items-center">
                                <div class="flex justify-center items-center w-9 h-9 rounded-xl bg-slate-700 text-slate-300">
                                    <span class="material-symbols-outlined text-[18px]">wallet</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-300 mb-0.5">الدفع (تحصيل)</p>
                                    <p class="text-[11px] font-bold {{ $shipment->payment_method == 'prepaid' ? 'text-emerald-400' : 'text-white' }} tracking-wide">
                                        @if($shipment->payment_method == 'prepaid') خالص (مدفوع)
                                        @elseif($shipment->payment_method == 'cod') الدفع عند الاستلام
                                        @elseif($shipment->payment_method == 'partial_payment') دفع جزئي @else آجل @endif
                                    </p>
                                </div>
                            </div>

                            {{-- الإجمالي (بارز) --}}
                            <div class="pl-2 text-left">
                                <p class="text-[9px] font-bold text-slate-400 mb-0.5">المطلوب</p>
                                <p class="text-lg font-black tracking-tight leading-none {{ $shipment->payment_method == 'prepaid' ? 'text-emerald-400' : 'text-amber-400' }} font-headline">
                                    {{ $shipment->payment_method == 'prepaid' ? '0' : number_format($shipment->total_amount, 0) }} <span class="text-[10px] font-bold text-slate-300">ريال</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-20 bg-white rounded-[24px] border-2 border-dashed border-slate-200/70 mt-4 shadow-sm">
                    <div class="relative mb-4">
                        <div class="absolute inset-0 rounded-full blur-xl bg-emerald-500/20"></div>
                        <div class="w-16 h-16 bg-gradient-to-br from-slate-50 to-slate-100 rounded-[18px] flex items-center justify-center border border-white shadow-sm relative z-10">
                            <span class="material-symbols-outlined text-[32px] text-slate-300">inbox</span>
                        </div>
                    </div>
                    <h3 class="text-sm font-black text-slate-700 font-headline">لا توجد طرود واردة</h3>
                    <p class="text-[11px] font-bold text-slate-400 mt-1">لم نعثر على أي طرود تطابق بحثك حالياً.</p>
                </div>
            @endforelse

            {{-- الترقيم --}}
            @if(method_exists($shipments, 'hasPages') && $shipments->hasPages())
                <div class="mt-8">
                    {{ $shipments->links('vendor.pagination.mobile') }}
                </div>
            @endif
        </div>
    </div>
@endsection