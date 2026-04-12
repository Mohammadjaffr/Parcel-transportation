@extends('mobile.layouts.app')

@section('title', 'تفاصيل الطرد - ' . $shipment->bond_number)

@section('content')
    {{-- تم تقليل pb-24 إلى pb-8 لأننا أزلنا الزر العائم --}}
    <div class="flex flex-col gap-5 px-4 pb-8 relative min-h-screen bg-slate-50/50 pt-6">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('mobile.shipment') }}"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-slate-500 hover:text-primary active:scale-90 transition-all">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-lg font-black font-headline text-slate-800">رقم الطرد</h1>
                    <p class="text-sm font-bold text-primary tracking-wider">{{ $shipment->bond_number }}</p>
                </div>
            </div>

            {{-- حالة الطرد --}}
            @php
                $statusColors = [
                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'in_transit' => 'bg-blue-50 text-blue-600 border-blue-200',
                    'delivered' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                    'returned' => 'bg-rose-50 text-rose-600 border-rose-200',
                ];
                $statusIcons = [
                    'pending' => 'schedule',
                    'in_transit' => 'local_shipping',
                    'delivered' => 'check_circle',
                    'returned' => 'assignment_return',
                ];
                $statusNames = [
                    'pending' => 'قيد الانتظار',
                    'in_transit' => 'في الطريق',
                    'delivered' => 'تم التسليم',
                    'returned' => 'مرتجع',
                ];
                $colorClass = $statusColors[$shipment->status] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                $icon = $statusIcons[$shipment->status] ?? 'info';
                $name = $statusNames[$shipment->status] ?? $shipment->status;
            @endphp
            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-bold text-xs shadow-sm {{ $colorClass }}">
                <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
                {{ $name }}
            </div>
        </div>

        {{-- ================= أزرار الإجراءات الراقية (Premium Actions) ================= --}}
        <div class="flex items-center gap-3 mt-1">
            
            @php
                $currentStatus = $shipment->status;
                $availableStatuses = [];

                // أضفنا الأيقونات والألوان لكل حالة لتبدو القائمة فخمة!
                if ($currentStatus === 'pending') {
                    $availableStatuses = [
                        'in_transit' => ['label' => 'شحن الطرد (في الطريق)', 'icon' => 'local_shipping', 'text_color' => 'text-blue-500', 'bg_color' => 'bg-blue-50'],
                        'returned'   => ['label' => 'إلغاء / مرتجع', 'icon' => 'assignment_return', 'text_color' => 'text-rose-500', 'bg_color' => 'bg-rose-50']
                    ];
                } elseif ($currentStatus === 'in_transit') {
                    $availableStatuses = [
                        'delivered' => ['label' => 'تم التسليم بنجاح', 'icon' => 'check_circle', 'text_color' => 'text-emerald-500', 'bg_color' => 'bg-emerald-50'],
                        'returned'  => ['label' => 'مرتجع', 'icon' => 'assignment_return', 'text_color' => 'text-rose-500', 'bg_color' => 'bg-rose-50']
                    ];
                }
            @endphp

           @if(!empty($availableStatuses))
                {{-- قائمة التحديث المخصصة --}}
                <div class="flex-[2] relative" x-data="{ openStatusMenu: false }">
                    <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" x-ref="statusForm">
                        @csrf
                        {{-- 1. التعديل الأول: استخدمنا x-ref بدلاً من x-model --}}
                        <input type="hidden" name="status" x-ref="statusInput">

                        {{-- الزر الأنيق (Trigger) --}}
                        <button type="button" @click="openStatusMenu = !openStatusMenu" @click.outside="openStatusMenu = false"
                                class="w-full flex items-center justify-between px-4 h-12 bg-slate-800 text-white rounded-2xl font-bold text-xs shadow-[0_8px_20px_rgba(30,41,59,0.2)] hover:bg-slate-700 active:scale-95 transition-all">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">update</span>
                                <span>تحديث الحالة</span>
                            </div>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-300" 
                                  :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        {{-- القائمة المنسدلة (Dropdown Panel) --}}
                        <div x-show="openStatusMenu" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                             class="absolute top-full right-0 mt-2 w-full bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_15px_40px_-10px_rgba(0,0,0,0.15)] border border-slate-100 p-1.5 z-[60]">
                            
                            @foreach($availableStatuses as $value => $data)
                                {{-- خيارات الحالة --}}
                                {{-- 2. التعديل الثاني: نضع القيمة يدوياً في الحقل ثم نرسل الفورم مباشرة --}}
                                <button type="button" 
                                        @click="$refs.statusInput.value = '{{ $value }}'; $refs.statusForm.submit()"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-all text-right group active:scale-[0.98]">
                                    
                                    <div class="w-8 h-8 rounded-lg {{ $data['bg_color'] }} {{ $data['text_color'] }} flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">{{ $data['icon'] }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700">{{ $data['label'] }}</span>
                                    
                                </button>
                            @endforeach
                        </div>
                    </form>
                </div>
            @else
                {{-- زر مغلق أنيق --}}
                <button disabled class="flex-[2] flex items-center justify-center gap-2 h-12 bg-slate-50 text-slate-400 rounded-2xl font-bold text-xs border border-slate-200 cursor-not-allowed">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                    مكتمل (لا يمكن التحديث)
                </button>
            @endif

            {{-- زر الطباعة --}}
            <a href="#" class="flex-1 flex items-center justify-center gap-2 h-12 bg-white text-slate-600 rounded-2xl font-bold text-xs shadow-sm border border-slate-100 hover:bg-slate-50 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span>
                طباعة
            </a>
        </div>

        {{-- ================= بطاقة المسار (من -> إلى) ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] relative overflow-hidden">
            {{-- تأثيرات الإضاءة الخلفية (Glassmorphism Glow) --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-emerald-500/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center text-primary shadow-inner">
                        <span class="material-symbols-outlined text-[20px]">route</span>
                    </div>
                    <h3 class="font-black text-slate-800 text-sm font-headline">مسار الرحلة</h3>
                </div>
                <span class="px-3 py-1 bg-slate-50 text-slate-500 rounded-lg text-[10px] font-bold border border-slate-100">
                    {{ $shipment->created_at->format('Y-m-d h:i A') }}
                </span>
            </div>

            @php
                // تجهيز رسائل الواتساب الديناميكية
                $senderMsg = "مرحباً *" . ($shipment->senderCustomer?->name ?? 'عميلنا العزيز') . "*،\nتم إصدار بوليصة شحن طردك برقم: *" . $shipment->bond_number . "*";
                
                // حساب المبلغ المتبقي للمستلم
                $remainingAmount = $shipment->total_amount - $shipment->partial_amount;
                $receiverMsg = "مرحباً *" . ($shipment->receiverCustomer?->name ?? 'عميلنا العزيز') . "*،\nلديك طرد قادم إليك برقم بوليصة: *" . $shipment->bond_number . "*\n";
                if(in_array($shipment->payment_method, ['cod', 'partial_payment'])) {
                    $receiverMsg .= "المبلغ المطلوب عند الاستلام: *" . number_format($remainingAmount, 0) . "* ريال.";
                } else {
                    $receiverMsg .= "الطرد مدفوع مسبقاً، لا توجد رسوم إضافية عند الاستلام.";
                }
            @endphp

            {{-- التايم لاين (Timeline) الحديث --}}
            <div class="relative pl-2 pr-6 space-y-8">
                {{-- الخط المتصل المتدرج --}}
                <div class="absolute right-[11px] top-2 bottom-2 w-0.5 bg-gradient-to-b from-slate-200 via-primary/30 to-primary rounded-full"></div>

                {{-- نقطة الانطلاق (المرسل) --}}
                <div class="relative z-10">
                    <div class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-white border-4 border-slate-300 rounded-full shadow-sm"></div>
                    <div class="bg-slate-50/50 p-3.5 rounded-2xl border border-slate-100/50 hover:bg-slate-50 transition-colors">
                        <span class="text-[10px] font-black text-slate-400 mb-1 block">المرسل • {{ $shipment->senderBranch?->name ?? 'مكتب خارجي' }}</span>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-black text-sm text-slate-800 block">{{ $shipment->senderCustomer?->name ?? 'عميل نقدي' }}</span>
                                <span class="text-xs text-slate-500 dir-ltr text-right mt-0.5 block font-medium">{{ $shipment->senderCustomer?->phone }}</span>
                            </div>
                            @if($shipment->senderCustomer?->phone)
                                <div class="flex items-center gap-1.5">
                                    {{-- زر الواتساب للمرسل --}}
                                    <a href="https://wa.me/{{ ltrim($shipment->senderCustomer->phone, '+') }}?text={{ urlencode($senderMsg) }}" target="_blank" 
                                       class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all group">
                                        <svg class="w-5 h-5 fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                    </a>
                                    {{-- زر الاتصال للمرسل --}}
                                    <a href="tel:{{ $shipment->senderCustomer?->phone }}" class="w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center text-slate-600 hover:text-primary hover:border-primary/30 active:scale-95 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">call</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- نقطة الوصول (المستلم) --}}
                <div class="relative z-10">
                    <div class="absolute -right-[31px] top-1.5 w-3.5 h-3.5 bg-primary ring-4 ring-primary/20 rounded-full {{ $shipment->status == 'in_transit' ? 'animate-pulse' : '' }}"></div>
                    <div class="bg-primary/[0.02] p-3.5 rounded-2xl border border-primary/10 hover:bg-primary/5 transition-colors">
                        <span class="text-[10px] font-black text-primary/60 mb-1 block">المستلم • {{ $shipment->receiverBranch?->name ?? 'مكتب خارجي' }}</span>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-black text-sm text-primary block">{{ $shipment->receiverCustomer?->name ?? 'عميل نقدي' }}</span>
                                <span class="text-xs text-primary/70 dir-ltr text-right mt-0.5 block font-medium">{{ $shipment->receiverCustomer?->phone }}</span>
                            </div>
                            @if($shipment->receiverCustomer?->phone)
                                <div class="flex items-center gap-1.5">
                                    {{-- زر الواتساب للمستلم --}}
                                    <a href="https://wa.me/{{ ltrim($shipment->receiverCustomer->phone, '+') }}?text={{ urlencode($receiverMsg) }}" target="_blank" 
                                       class="w-10 h-10 bg-white rounded-xl shadow-sm border border-primary/10 flex items-center justify-center hover:bg-[#25D366]/10 hover:border-[#25D366]/30 active:scale-95 transition-all">
                                        <svg class="w-5 h-5 fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                    </a>
                                    {{-- زر الاتصال للمستلم --}}
                                    <a href="tel:{{ $shipment->receiverCustomer?->phone }}" class="w-10 h-10 bg-primary text-white rounded-xl shadow-sm shadow-primary/30 flex items-center justify-center hover:bg-primary/90 active:scale-95 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">call</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= بطاقة المحتويات (Bento Style) ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-50 flex items-center justify-center text-slate-600 shadow-inner">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
                <h3 class="font-black text-slate-800 text-sm font-headline">محتويات الطرد</h3>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1.5 bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">نوع التغليف</span>
                    <span class="text-sm font-black text-slate-800 flex items-center gap-1">
                        @if($shipment->package_type == 'carton') كرتون 📦
                        @elseif($shipment->package_type == 'bag') كيس 🛍️
                        @elseif($shipment->package_type == 'envelope') مغلف ✉️
                        @else أخرى 📦 @endif
                    </span>
                </div>
                <div class="flex flex-col gap-1.5 bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">الوزن الفعلي</span>
                    <span class="text-sm font-black text-slate-800">{{ $shipment->weight }} <span class="text-xs text-slate-500 font-bold">كجم</span> ⚖️</span>
                </div>
                
                @if($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0)
                    <div class="col-span-2 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100/50 p-4 rounded-2xl flex items-center justify-around relative overflow-hidden">
                        {{-- زخرفة للخلفية --}}
                        <span class="material-symbols-outlined absolute -left-4 -bottom-4 text-[80px] text-amber-500/5 rotate-12">hive</span>
                        
                        @if($shipment->no_gallons_honey > 0)
                            <div class="flex flex-col items-center gap-1 relative z-10">
                                <span class="text-[10px] font-black text-amber-600/70">دباب عسل</span>
                                <span class="text-xl font-black text-amber-700 flex items-center gap-1">{{ $shipment->no_gallons_honey }} <span class="text-sm">🍯</span></span>
                            </div>
                        @endif
                        
                        @if($shipment->no_gallons_honey > 0 && $shipment->no_honey_jars > 0)
                            <div class="w-px h-10 bg-amber-200/50 rounded-full relative z-10"></div>
                        @endif

                        @if($shipment->no_honey_jars > 0)
                            <div class="flex flex-col items-center gap-1 relative z-10">
                                <span class="text-[10px] font-black text-amber-600/70">قوارير عسل</span>
                                <span class="text-xl font-black text-amber-700 flex items-center gap-1">{{ $shipment->no_honey_jars }} <span class="text-sm">🏺</span></span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if($shipment->notes)
                <div class="mt-3 p-4 bg-rose-50/50 border border-rose-100 rounded-2xl flex gap-3 items-start">
                    <div class="bg-rose-100 text-rose-500 rounded-full p-1 shrink-0 mt-0.5">
                        <span class="material-symbols-outlined text-[14px]">priority_high</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-rose-500 block mb-0.5 uppercase tracking-wider">ملاحظات هامة</span>
                        <p class="text-xs font-bold text-rose-800 leading-relaxed">{{ $shipment->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- ================= بطاقة المالية (Digital Wallet Style) ================= --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center text-emerald-600 shadow-inner">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                    </div>
                    <h3 class="font-black text-slate-800 text-sm font-headline">الخلاصة المالية</h3>
                </div>
                <div class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-[10px] font-black flex items-center gap-1.5 shadow-sm">
                    @if($shipment->payment_method == 'prepaid') مدفوع مقدماً 💵
                    @elseif($shipment->payment_method == 'cod') دفع عند الاستلام 🚚
                    @elseif($shipment->payment_method == 'partial_payment') دفع جزئي 💳
                    @elseif($shipment->payment_method == 'customer_credit') آجل (حساب) 📝
                    @endif
                </div>
            </div>

            <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100 space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-xs font-black text-slate-500">إجمالي رسوم الشحن</span>
                    <span class="text-sm font-black text-slate-800">{{ number_format($shipment->total_amount, 0) }} <span class="text-[10px] text-slate-400">ريال</span></span>
                </div>
                
                @if($shipment->payment_method == 'partial_payment')
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-black text-emerald-600">المبلغ المدفوع</span>
                        <span class="text-sm font-black text-emerald-600">{{ number_format($shipment->partial_amount, 0) }} <span class="text-[10px]">ريال</span></span>
                    </div>
                    
                    {{-- فاصل --}}
                    <div class="border-t border-dashed border-slate-200 my-2"></div>
                    
                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-rose-500 uppercase tracking-wider mb-1">المبلغ المتبقي للتحصيل</span>
                            <span class="text-xs font-bold text-slate-400">يدفع عند التسليم</span>
                        </div>
                        <span class="text-2xl font-black text-rose-600">{{ number_format($shipment->total_amount - $shipment->partial_amount, 0) }} <span class="text-xs font-bold text-rose-400">ريال</span></span>
                    </div>
                @elseif($shipment->payment_method == 'cod')
                    {{-- فاصل --}}
                    <div class="border-t border-dashed border-slate-200 my-2"></div>
                    
                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-rose-500 uppercase tracking-wider mb-1">المطلوب تحصيله</span>
                            <span class="text-xs font-bold text-slate-400">يدفع عند التسليم</span>
                        </div>
                        <span class="text-2xl font-black text-rose-600">{{ number_format($shipment->total_amount, 0) }} <span class="text-xs font-bold text-rose-400">ريال</span></span>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection