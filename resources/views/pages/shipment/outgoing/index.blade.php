@extends('layouts.app')

@section('title', 'الطرود المرسلة')
@section('Breadcrumb', 'الطرود المرسلة')

@section('content')
<style type="text/tailwindcss">
    @layer components {
        /* تلوين الصفحة النشطة */
        .pagination-container span[aria-current="page"] > span {
            @apply bg-primary border-primary text-white font-black !important;
        }

        /* تلوين أرقام الصفحات العادية والأسهم */
        .pagination-container a, 
        .pagination-container span[aria-disabled="true"] > span {
            @apply text-primary border-primary/30 dark:border-primary/20 !important;
        }

        /* تأثير عند تمرير الماوس */
        .pagination-container a:hover {
            @apply bg-primary-container text-primary-hover dark:bg-primary/10 dark:text-primary !important;
        }

        /* تدوير الحواف وإزالة الظل */
        .pagination-container .isolate > * {
            @apply rounded-lg mx-0.5 !important;
        }
        
        .pagination-container .isolate {
            @apply shadow-none !important;
        }
    }
</style>

<div x-data="{ searchQuery: '' }" class="flex relative flex-col gap-6 p-4 rounded-3xl bg-surface dark:bg-boxdark-2 lg:p-6 font-body" dir="rtl">

    {{-- الهيدر العلوي --}}
    <div class="flex justify-between items-center mt-6">
        <div class="flex flex-col">
            <h1 class="text-3xl font-black font-headline text-on-surface dark:text-white">الطرود</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-bodydark">
                إجمالي <span class="font-bold text-primary">{{ $shipments->total() ?? 0 }}</span> طرد مسجل
            </p>
        </div>

        <a href="{{ route('shipment.create') }}"
            class="flex justify-center items-center p-2 h-12 text-white rounded-2xl shadow-lg transition-transform w-50 shrink-0 bg-primary hover:bg-primary-hover shadow-primary/30 active:scale-90">
            <span class="text-[26px] material-symbols-outlined">add_box</span>
            <span class="text-sm font-bold text-white">إضافة طرد</span>
        </a>
    </div>

    {{-- شريط البحث --}}
    <div class="relative">
        <input type="text" x-model="searchQuery" placeholder="ابحث برقم السند، أو هاتف العميل..."
            class="pr-12 pl-4 w-full h-14 text-sm placeholder-gray-400 bg-white rounded-2xl border border-gray-200 shadow-sm transition-all outline-none text-on-surface focus:ring-2 focus:ring-primary/20 focus:border-primary dark:bg-boxdark dark:border-boxdark dark:text-white dark:placeholder-bodydark">
        <span class="absolute right-4 top-1/2 text-gray-400 -translate-y-1/2 material-symbols-outlined dark:text-bodydark">search</span>
    </div>

    {{-- شبكة الكروت --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 2xl:grid-cols-3">
        @forelse($shipments as $shipment)
            <div x-show="searchQuery === '' || '{{ $shipment->bond_number }}'.includes(searchQuery) || '{{ $shipment->receiverCustomer?->phone }}'.includes(searchQuery)"
                 class="overflow-visible relative transition-all duration-300 bg-white rounded-[24px] border border-gray-200/60 shadow-sm group hover:shadow-md hover:border-primary/30 dark:bg-boxdark dark:border-boxdark-2 dark:hover:border-primary/50">
                
                {{-- شريط لوني علوي خفيف يعطي طابعاً مميزاً --}}
                <div class="absolute inset-x-0 top-0 h-1 rounded-t-[24px] opacity-70 bg-gradient-to-r from-primary to-primary-hover"></div>

                {{-- ================= 1. الرأس (Header) ================= --}}
                <div class="flex justify-between items-start p-5">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-11 h-11 rounded-[14px] bg-surface border border-gray-100 transition-transform duration-300 group-hover:scale-105 dark:bg-boxdark-2 dark:border-boxdark">
                            <span class="text-[22px] text-gray-500 material-symbols-outlined dark:text-bodydark">package_2</span>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-sm font-black tracking-tight text-on-surface font-headline dark:text-gray-100">{{ $shipment->bond_number }}</h3>
                            <p class="flex gap-1 items-center mt-0.5 text-[10px] font-bold text-gray-400 dark:text-gray-500">
                                <span class="text-[12px] material-symbols-outlined">schedule</span>
                                {{ $shipment->created_at->format('Y/m/d - H:i') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 items-center">
                        {{-- شارة الحالة --}}
                        @if($shipment->status == 'pending')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-600 ring-1 ring-amber-500/20 ring-inset dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20">قيد الانتظار</span>
                        @elseif($shipment->status == 'in_transit')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-600 ring-1 ring-blue-500/20 ring-inset dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">في الطريق</span>
                        @elseif($shipment->status == 'delivered')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20 ring-inset dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">تم التسليم</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-gray-50 text-gray-500 ring-1 ring-gray-500/20 ring-inset dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700">ملغي / مرتجع</span>
                        @endif

                        {{-- قائمة الثلاث نقاط --}}
                        <div x-data="{ openMenu: false }" class="relative">
                            <button type="button" @click="openMenu = !openMenu" @click.away="openMenu = false"
                                    class="flex justify-center items-center w-8 h-8 text-gray-400 rounded-full transition-colors hover:bg-surface hover:text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20 dark:text-bodydark dark:hover:bg-boxdark-2 dark:hover:text-white">
                                <span class="text-[20px] material-symbols-outlined">more_vert</span>
                            </button>
                            
                            <div x-show="openMenu" x-transition.opacity.duration.200ms x-cloak
                                 class="overflow-hidden absolute left-0 top-full z-50 py-1.5 mt-1.5 w-44 rounded-2xl border shadow-lg backdrop-blur-md bg-white/90 border-gray-100/50 dark:bg-boxdark-2/95 dark:border-boxdark dark:shadow-black/40">
                                
                                <a href="{{ route('shipment.show', $shipment->id) }}" class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold text-gray-600 transition-colors hover:bg-surface hover:text-primary dark:text-gray-300 dark:hover:bg-boxdark">
                                    <span class="text-[18px] material-symbols-outlined">visibility</span>
                                    التفاصيل
                                </a>
                                
                                <a href="{{ route('receipt.generate', ['type' => 'sender', 'id' => $shipment->uuid]) }}" target="_blank" class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold text-gray-600 transition-colors hover:bg-surface hover:text-primary dark:text-gray-300 dark:hover:bg-boxdark">
                                    <span class="text-[18px] material-symbols-outlined">print</span>
                                    طباعة السند
                                </a>

                                <div class="mx-3 my-1 h-px bg-gray-100 dark:bg-boxdark"></div>

                                @if($shipment->senderCustomer && $shipment->senderCustomer->phone)
                                    @php
                                        $senderMsg = "مرحباً *" . $shipment->senderCustomer->name . "*،\nتم إصدار بوليصة شحن طردك برقم: *" . $shipment->bond_number . "*\nالإجمالي: *" . number_format($shipment->total_amount, 0) . "* ريال.";
                                    @endphp
                                    <a href="https://wa.me/{{ ltrim($shipment->senderCustomer->phone, '+') }}?text={{ urlencode($senderMsg) }}" target="_blank" 
                                       class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold text-gray-600 transition-colors hover:bg-surface hover:text-on-surface dark:text-gray-300 dark:hover:bg-boxdark dark:hover:text-white">
                                        <svg class="w-[16px] h-[16px] fill-[#25D366]" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                        إرسال للمرسل
                                    </a>
                                @endif

                                @if($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                    @php
                                        $receiverMsg = "مرحباً *" . $shipment->receiverCustomer->name . "*،\nلديك طرد قادم برقم بوليصة: *" . $shipment->bond_number . "*\nالإجمالي المطلوب: *" . number_format($shipment->total_amount - $shipment->partial_amount, 0) . "* ريال.";
                                    @endphp
                                    <a href="https://wa.me/{{ ltrim($shipment->receiverCustomer->phone, '+') }}?text={{ urlencode($receiverMsg) }}" target="_blank" 
                                       class="flex gap-2.5 items-center px-4 py-2 text-xs font-bold text-gray-600 transition-colors hover:bg-surface hover:text-on-surface dark:text-gray-300 dark:hover:bg-boxdark dark:hover:text-white">
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
                <div class="flex overflow-hidden relative items-center h-4">
                    <div class="absolute -right-2 w-4 h-4 rounded-full border-l shadow-inner bg-surface border-gray-200/60 dark:bg-boxdark-2 dark:border-boxdark"></div>
                    <div class="w-full border-t-[1.5px] border-dashed border-gray-200/70 dark:border-boxdark-2"></div>
                    <div class="absolute -left-2 w-4 h-4 rounded-full border-r shadow-inner bg-surface border-gray-200/60 dark:bg-boxdark-2 dark:border-boxdark"></div>
                </div>

                {{-- ================= 3. جسد البطاقة ================= --}}
                <div class="p-5 pt-4 space-y-5">
                    <div class="flex gap-4 justify-between items-start">
                        
                        {{-- خط السير --}}
                        <div class="flex gap-3 items-stretch w-1/2">
                            <div class="flex flex-col items-center mt-1">
                                <div class="w-2.5 h-2.5 rounded-full border-[2.5px] border-gray-300 bg-white z-10 dark:border-gray-500 dark:bg-boxdark"></div>
                                <div class="w-[1.5px] h-10 bg-gray-200 my-0.5 dark:bg-boxdark-2"></div>
                                <div class="w-2.5 h-2.5 rounded-full border-[2.5px] border-primary bg-white z-10 shadow-[0_0_8px_rgba(247,144,9,0.4)] dark:bg-boxdark"></div>
                            </div>
                            
                            <div class="flex flex-col flex-1 justify-between space-y-4">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 mb-0.5 tracking-wide dark:text-gray-500">المرسل</p>
                                    <p class="text-xs font-bold truncate max-w-[100px] text-on-surface dark:text-gray-200">{{ $shipment->senderCustomer?->name ?? 'عميل نقدي' }}</p>
                                </div>
                                
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-[9px] font-black text-gray-400 mb-0.5 tracking-wide flex items-center gap-1 dark:text-gray-500">
                                            الوجهة: 
                                            <span class="inline-block align-bottom truncate max-w-[120px] text-primary">
                                                @if($shipment->receiverOfficeBranch)
                                                    {{ $shipment->receiverOfficeBranch->office->name ?? 'مكتب خارجي' }} - {{ $shipment->receiverOfficeBranch->name }}
                                                @elseif($shipment->receiverBranch)
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
                                        <p class="text-xs font-bold truncate max-w-[100px] text-on-surface dark:text-gray-200">{{ $shipment->receiverCustomer?->name ?? 'عميل نقدي' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- التفاصيل الدقيقة --}}
                        <div class="flex flex-col gap-2.5 p-3 w-1/2 rounded-xl border border-gray-100 bg-surface dark:bg-boxdark-2 dark:border-boxdark">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-gray-400 font-bold dark:text-gray-500">المحتوى:</span>
                                <span class="px-2 py-0.5 text-[10px] font-black bg-white rounded-md border shadow-sm text-on-surface border-gray-100 dark:bg-boxdark dark:text-gray-200 dark:border-boxdark-2">
                                    @if($shipment->package_type == 'carton') كرتون @elseif($shipment->package_type == 'bag') كيس @elseif($shipment->package_type == 'envelope') مغلف @else أخرى @endif
                                    @if($shipment->weight > 0) <span class="text-gray-400 dark:text-bodydark">({{ $shipment->weight }} كجم)</span> @endif
                                </span>
                            </div>

                            @if($shipment->no_gallons_honey > 0 || $shipment->no_honey_jars > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] text-primary font-bold">عسل:</span>
                                    <span class="text-[10px] font-bold text-gray-600 dark:text-gray-300">
                                        @if($shipment->no_gallons_honey > 0) {{ $shipment->no_gallons_honey }} دباب @endif
                                        @if($shipment->no_gallons_honey > 0 && $shipment->no_honey_jars > 0) + @endif
                                        @if($shipment->no_honey_jars > 0) {{ $shipment->no_honey_jars }} قوارير @endif
                                    </span>
                                </div>
                            @endif

                            @if($shipment->payment_method == 'partial_payment')
                                <div class="flex justify-between items-center pt-2 mt-1 border-t border-gray-200/50 dark:border-boxdark">
                                    <span class="text-[10px] text-error font-bold">المتبقي:</span>
                                    <span class="text-[11px] font-black text-error">
                                        {{ number_format($shipment->total_amount - $shipment->partial_amount, 0) }} ريال
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ================= 4. كبسولة المالية ================= --}}
                    {{-- حافظت على لونها الداكن في كلا الوضعين لأنها تعطي لمسة جمالية وفخامة في الـ UI المالي --}}
                    <div class="flex justify-between items-center p-3.5 bg-boxdark rounded-[18px] shadow-lg shadow-gray-900/10 dark:bg-black/30 dark:border dark:border-boxdark-2">
                        <div class="flex gap-2.5 items-center">
                            <div class="flex justify-center items-center w-9 h-9 text-gray-300 rounded-xl bg-boxdark-2 dark:bg-boxdark dark:text-gray-400">
                                <span class="text-[18px] material-symbols-outlined">wallet</span>
                            </div>
                            <div>
                                <p class="mb-0.5 text-[10px] font-black text-gray-400">طريقة الدفع</p>
                                <p class="text-[11px] font-bold tracking-wide text-white">
                                    @if($shipment->payment_method == 'prepaid') مدفوع مقدماً @elseif($shipment->payment_method == 'cod') الدفع عند الاستلام @elseif($shipment->payment_method == 'partial_payment') دفع جزئي @else آجل @endif
                                </p>
                            </div>
                        </div>

                        <div class="pl-2 text-left">
                            <p class="mb-0.5 text-[9px] font-bold text-gray-400">الإجمالي</p>
                            <p class="text-lg font-black tracking-tight leading-none text-primary font-headline">
                                {{ number_format($shipment->total_amount, 0) }} <span class="text-[10px] font-bold text-gray-300">ريال</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="flex flex-col col-span-full justify-center items-center py-20 bg-white rounded-[24px] border-2 border-dashed shadow-sm border-gray-200/70 dark:bg-boxdark dark:border-boxdark-2">
                <div class="relative mb-4">
                    <div class="absolute inset-0 rounded-full blur-xl bg-primary/20"></div>
                    <div class="flex relative z-10 justify-center items-center w-16 h-16 bg-gradient-to-br border shadow-sm from-surface to-white rounded-[18px] border-white dark:from-boxdark-2 dark:to-boxdark dark:border-boxdark-2">
                        <span class="text-[32px] text-gray-300 material-symbols-outlined dark:text-bodydark">search_off</span>
                    </div>
                </div>
                <h3 class="text-sm font-black font-headline text-on-surface dark:text-white">لا توجد طرود</h3>
                <p class="mt-1 text-[11px] font-bold text-gray-400 dark:text-bodydark">لم نعثر على أي طرود تطابق بحثك حالياً.</p>
            </div>
        @endforelse
    </div>

    @if($shipments->hasPages())
        <div class="flex col-span-full justify-center items-center pt-6 mt-4 w-full">
            <div class="w-full p-4 transition-all bg-white border shadow-sm pagination-container rounded-[2rem] border-primary/50 dark:bg-boxdark dark:border-primary/30 hover:shadow-md lg:w-fit lg:min-w-[50%]">
                <div class="flex overflow-x-auto justify-center w-full custom-scrollbar text-primary">
                    {{ $shipments->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection