@extends('mobile.layouts.app')

@section('title', 'الطرود المرسلة')

@section('content')
    <div x-data="{ searchQuery: '' }" class="flex flex-col gap-6 px-4 pb-24 relative min-h-screen bg-slate-50/50">

        <div class="flex justify-between items-center mt-6">
            <div class="flex flex-col">
                <h1 class="text-3xl font-black font-headline text-slate-800">الطرود</h1>
                <p class="text-sm text-slate-500 font-medium mt-1">
                    إجمالي <span class="text-primary font-bold">{{ $shipments->total() ?? 0 }}</span> طرد مسجل
                </p>
            </div>

            <a href="{{ route('shipment.create') }}"
                class="w-12 h-12 bg-orange-400 text-white rounded-[1rem] flex items-center justify-center shadow-[0_8px_20px_rgba(251,146,60,0.4)] active:scale-90 transition-transform shrink-0">
                <span class="material-symbols-outlined text-[26px]">add_box</span>
            </a>
        </div>

        <div class="relative">
            <input type="text" x-model="searchQuery" placeholder="ابحث برقم السند، أو هاتف العميل..."
                class="w-full h-14 pr-4 pl-12 text-sm bg-white rounded-2xl border-none shadow-sm focus:ring-2 focus:ring-primary/20 outline-none text-slate-700 placeholder-slate-400">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">search</span>
        </div>

       <div class="space-y-5">
    @forelse($shipments as $shipment)
        <div x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery) || '{{ $shipment->receiverCustomer?->phone }}'.includes(searchQuery)"
             class="bg-white rounded-[24px] border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] overflow-visible transition-all duration-300 relative group">
            
            {{-- شريط لوني علوي خفيف يعطي طابعاً مميزاً --}}
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-primary/80 to-amber-400/80 rounded-t-[24px] opacity-70"></div>

            {{-- ================= 1. الرأس (Header) ================= --}}
            <div class="p-5 flex justify-between items-start">
                <div class="flex gap-3 items-center">
                    {{-- أيقونة السند بشكل عصري --}}
                    <div class="w-11 h-11 rounded-[14px] bg-slate-50 flex items-center justify-center border border-slate-100/80 group-hover:scale-105 transition-transform duration-300">
                        <span class="material-symbols-outlined text-slate-500 text-[22px]">package_2</span>
                    </div>
                    <div class="flex flex-col">
                        <h3 class="text-sm font-black text-slate-900 font-headline tracking-tight">{{ $shipment->bond_number }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                            {{ $shipment->created_at->format('Y/m/d - H:i') }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    {{-- شارة الحالة (Pill Badge) بتصميم FinTech --}}
                    @if($shipment->status == 'pending')
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-600 ring-1 ring-amber-500/20 ring-inset">قيد الانتظار</span>
                    @elseif($shipment->status == 'in_transit')
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 ring-1 ring-blue-500/20 ring-inset">في الطريق</span>
                    @elseif($shipment->status == 'delivered')
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20 ring-inset">تم التسليم</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-50 text-slate-500 ring-1 ring-slate-500/20 ring-inset">ملغي / مرتجع</span>
                    @endif

                    {{-- قائمة الثلاث نقاط (Kebab Menu) الاحترافية --}}
                    <div x-data="{ openMenu: false }" class="relative">
                        <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                        </button>
                        
                        <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                             class="absolute top-full left-0 mt-1.5 w-44 bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] border border-slate-100/50 z-50 overflow-hidden py-1.5">
                            
                            <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                التفاصيل
                            </a>
                            
                            <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[18px]">print</span>
                                طباعة السند
                            </a>

                            {{-- فاصل --}}
                            <div class="h-px bg-slate-100/80 my-1 mx-3"></div>

                            {{-- إرسال السند للمرسل (واتساب) --}}
                            @if($shipment->senderCustomer && $shipment->senderCustomer->phone)
                                @php
                                    $senderMsg = "مرحباً *" . $shipment->senderCustomer->name . "*،\nتم إصدار بوليصة شحن طردك برقم: *" . $shipment->bond_number . "*\nالإجمالي: *" . number_format($shipment->total_amount, 0) . "* ريال.";
                                @endphp
                                <a href="https://wa.me/{{ ltrim($shipment->senderCustomer->phone, '+') }}?text={{ urlencode($senderMsg) }}" target="_blank" 
                                   class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                    {{-- أيقونة واتساب باللون الأخضر الرسمي --}}
                                    <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    إرسال للمرسل
                                </a>
                            @endif

                            {{-- إرسال السند للمستلم (واتساب) --}}
                            @if($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                @php
                                    $receiverMsg = "مرحباً *" . $shipment->receiverCustomer->name . "*،\nلديك طرد قادم برقم بوليصة: *" . $shipment->bond_number . "*\nالإجمالي المطلوب: *" . number_format($shipment->total_amount - $shipment->partial_amount, 0) . "* ريال.";
                                @endphp
                                <a href="https://wa.me/{{ ltrim($shipment->receiverCustomer->phone, '+') }}?text={{ urlencode($receiverMsg) }}" target="_blank" 
                                   class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                                    {{-- أيقونة واتساب باللون الأخضر الرسمي --}}
                                    <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    إرسال للمستلم
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= 2. الفاصل المقطّع (Ticket Divider) ================= --}}
            <div class="relative flex items-center h-4 overflow-hidden">
                <div class="absolute -right-2 w-4 h-4 bg-slate-50/50 rounded-full border-l border-slate-200/60 shadow-inner"></div>
                <div class="w-full border-t-[1.5px] border-dashed border-slate-200/70"></div>
                <div class="absolute -left-2 w-4 h-4 bg-slate-50/50 rounded-full border-r border-slate-200/60 shadow-inner"></div>
            </div>

            {{-- ================= 3. جسد البطاقة (The Journey & Data) ================= --}}
            <div class="p-5 pt-4 space-y-5">
                
                {{-- تقسيم المساحة لعمودين: يمين (خط السير) ويسار (التفاصيل الإضافية) --}}
                <div class="flex items-start justify-between gap-4">
                    
                    {{-- العمود الأيمن: خط السير (المرسل -> المستلم) --}}
                    <div class="flex items-stretch gap-3 w-1/2">
                        {{-- الخط البصري --}}
                        <div class="flex flex-col items-center mt-1">
                            <div class="w-2.5 h-2.5 rounded-full border-[2.5px] border-slate-300 bg-white z-10"></div>
                            <div class="w-[1.5px] h-10 bg-slate-200 my-0.5"></div>
                            <div class="w-2.5 h-2.5 rounded-full border-[2.5px] border-primary bg-white z-10 shadow-[0_0_8px_rgba(247,144,9,0.4)]"></div>
                        </div>
                        
                        {{-- بيانات المرسل والمستلم --}}
                        <div class="flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide">المرسل</p>
                                <p class="text-xs font-bold text-slate-800 truncate max-w-[100px]">{{ $shipment->senderCustomer?->name ?? 'عميل نقدي' }}</p>
                            </div>
                            
                            {{-- بيانات المستلم والوجهة --}}
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[9px] font-black text-slate-400 mb-0.5 tracking-wide flex items-center gap-1">
                                    الوجهة: 
                                    <span class="text-primary truncate max-w-[120px] inline-block align-bottom">
                                        @if($shipment->receiverOfficeBranch)
                                            {{-- إذا كان مكتب خارجي يدوي --}}
                                            {{ $shipment->receiverOfficeBranch->office->name ?? 'مكتب خارجي' }} - {{ $shipment->receiverOfficeBranch->name }}
                                        
                                        @elseif($shipment->receiverBranch)
                                            {{-- إذا كان مكتب مسجل بالنظام (نقارن هل ينتمي لنفس تطبيقنا أم لا) --}}
                                            @if($shipment->senderBranch?->app_id == $shipment->receiverBranch->app_id)
                                                <span class="text-emerald-500">مكتبنا</span> - {{ $shipment->receiverBranch->name }}
                                            @else
                                                {{ $shipment->receiverBranch->app->name ?? 'مكتب موثوق' }} - {{ $shipment->receiverBranch->name }}
                                            @endif
                                            
                                        @else
                                            غير محدد
                                        @endif
                                    </span>
                                </p>
                                <p class="text-xs font-bold text-slate-800 truncate max-w-[100px]">{{ $shipment->receiverCustomer?->name ?? 'عميل نقدي' }}</p>
                            </div>
                        </div>
                        </div>
                    </div>

                    {{-- العمود الأيسر: المساحة المستغلة (تفاصيل الطرد الدقيقة) --}}
                    <div class="w-1/2 bg-slate-50/70 rounded-xl p-3 border border-slate-100/80 flex flex-col gap-2.5">
                        
                        {{-- الوزن والنوع --}}
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-bold">المحتوى:</span>
                            <span class="text-[10px] font-black text-slate-700 bg-white px-2 py-0.5 rounded-md border border-slate-100 shadow-sm">
                                @if($shipment->package_type == 'carton') كرتون @elseif($shipment->package_type == 'bag') كيس @elseif($shipment->package_type == 'envelope') مغلف @else أخرى @endif
                                @if($shipment->weight > 0) <span class="text-slate-400">({{ $shipment->weight }} كجم)</span> @endif
                            </span>
                        </div>

                        {{-- تفاصيل العسل (إن وجدت) --}}
                        @if($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-amber-500 font-bold">عسل:</span>
                                <span class="text-[10px] font-bold text-slate-600">
                                    @if($shipment->no_gallons_honey > 0) {{ $shipment->no_gallons_honey }} دباب @endif
                                    @if($shipment->no_gallons_honey > 0 && $shipment->no_honey_jars > 0) + @endif
                                    @if($shipment->no_honey_jars > 0) {{ $shipment->no_honey_jars }} قوارير @endif
                                </span>
                            </div>
                        @endif

                        {{-- الدفع الجزئي (إن وجد) --}}
                        @if($shipment->payment_method == 'partial_payment')
                            <div class="flex items-center justify-between mt-1 pt-2 border-t border-slate-200/50">
                                <span class="text-[10px] text-rose-500 font-bold">المتبقي:</span>
                                <span class="text-[11px] font-black text-rose-600">
                                    {{ number_format($shipment->total_amount - $shipment->partial_amount, 0) }} ريال
                                </span>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- ================= 4. كبسولة المالية والمحتوى (Footer Module) ================= --}}
                <div class="bg-slate-800 rounded-[18px] p-3.5 flex justify-between items-center shadow-lg shadow-slate-900/10">
                    
                    {{-- تفاصيل الدفع --}}
                    <div class="flex gap-2.5 items-center">
                        <div class="w-9 h-9 rounded-xl bg-slate-700 flex items-center justify-center text-slate-300">
                            <span class="material-symbols-outlined text-[18px]">wallet</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-300 mb-0.5">طريقة الدفع</p>
                            <p class="text-[11px] font-bold text-white tracking-wide">
                                @if($shipment->payment_method == 'prepaid') مدفوع مقدماً @elseif($shipment->payment_method == 'cod') الدفع عند الاستلام @elseif($shipment->payment_method == 'partial_payment') دفع جزئي @else آجل @endif
                            </p>
                        </div>
                    </div>

                    {{-- الإجمالي (بارز جداً) --}}
                    <div class="text-left pl-2">
                        <p class="text-[9px] font-bold text-slate-400 mb-0.5">الإجمالي</p>
                        <p class="text-lg font-black text-amber-400 font-headline tracking-tight leading-none">
                            {{ number_format($shipment->total_amount, 0) }} <span class="text-[10px] font-bold text-slate-300">ريال</span>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    @empty
        {{-- Empty State بتصميم أنيق ومريح --}}
        <div class="flex flex-col items-center justify-center py-20 bg-white rounded-[24px] border-2 border-dashed border-slate-200/70 mt-4 shadow-sm">
            <div class="relative mb-4">
                <div class="absolute inset-0 bg-primary/20 blur-xl rounded-full"></div>
                <div class="w-16 h-16 bg-gradient-to-br from-slate-50 to-slate-100 rounded-[18px] flex items-center justify-center border border-white shadow-sm relative z-10">
                    <span class="material-symbols-outlined text-[32px] text-slate-300">search_off</span>
                </div>
            </div>
            <h3 class="text-sm font-black text-slate-700 font-headline">لا توجد طرود</h3>
            <p class="text-[11px] font-bold text-slate-400 mt-1">لم نعثر على أي طرود تطابق بحثك حالياً.</p>
        </div>
    @endforelse

    @if(method_exists($shipments, 'hasPages') && $shipments->hasPages())
        <div class="mt-8">
            {{ $shipments->links('vendor.pagination.mobile') }}
        </div>
    @endif
</div>


    </div>
@endsection