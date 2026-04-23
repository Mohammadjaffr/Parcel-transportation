@extends('layouts.app')

@section('title', 'تفاصيل الإرسالية الواردة')
@section('Breadcrumb', 'إدارة الشحنات / الإرساليات الواردة / تفاصيل الإرسالية')

@section('content')
<div x-data="{ 
    isSubmitting: false,
    confirmDelivery() {
        if(confirm('هل أنت متأكد من استلام جميع الطرود في هذه الإرسالية؟')) {
            this.isSubmitting = true;
            this.$refs.deliveryForm.submit();
        }
    }
}" class="flex flex-col gap-6 p-4 rounded-3xl bg-surface dark:bg-boxdark-2 lg:p-6 font-body" dir="rtl">

    {{-- الهيدر العلوي --}}
    <div class="flex flex-col gap-4 justify-between items-start p-4 mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm md:flex-row md:items-center dark:bg-boxdark dark:border-boxdark-2 lg:p-6">
        <div class="flex gap-4 items-center">
            <a href="{{ route('shipmentpackage.incoming.index') }}" 
                class="flex justify-center items-center w-12 h-12 text-gray-400 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm transition-all hover:bg-primary hover:text-white dark:bg-boxdark-2 dark:border-boxdark-2 active:scale-95">
                <span class="material-symbols-outlined text-[24px]">arrow_forward</span>
            </a>
            <div>
                <h1 class="text-2xl font-black md:text-3xl font-headline text-on-surface dark:text-white">{{ $package->tracking_number }}</h1>
                <div class="flex gap-2 items-center mt-1">
                    <span class="px-3 py-1 rounded-lg text-[10px] font-black {{ $package->status == 'delivered' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                        {{ $package->status == 'delivered' ? 'مستلمة بالكامل' : 'قيد الانتظار' }}
                    </span>
                    <span class="px-2 py-1 text-xs font-bold text-gray-400 rounded-lg border border-gray-50 dark:border-boxdark">
                        {{ $package->created_at->format('Y/m/d H:i') }}
                    </span>
                </div>
            </div>
        </div>

        @if($package->status != 'delivered')
            <form x-ref="deliveryForm" action="{{ route('shipmentpackage.mark-all-delivered', $package->id) }}" method="POST" class="w-full md:w-auto">
                @csrf
                @method('PATCH')
                <button type="button" @click="confirmDelivery()" :disabled="isSubmitting"
                    class="flex gap-2 justify-center items-center px-6 h-12 w-full md:w-auto text-sm font-black text-white rounded-xl shadow-lg transition-all bg-emerald-500 hover:bg-emerald-600 hover:scale-[1.02] shadow-emerald-500/20 disabled:opacity-50 disabled:scale-100">
                    <span x-show="!isSubmitting" class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                    <span x-text="isSubmitting ? 'جاري التأكيد...' : 'تأكيد استلام الإرسالية'"></span>
                </button>
            </form>
        @else
            <div class="flex gap-3">
                <a href="{{ route('shipmentpackage.print',[type=> $package->id] }}" target="_blank"
                    class="flex gap-2 justify-center items-center px-6 h-12 text-sm font-black text-white rounded-xl shadow-lg transition-all bg-boxdark hover:bg-black hover:scale-[1.02] shadow-boxdark/20 dark:bg-primary dark:shadow-primary/20">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                    طباعة المانيفست
                </a>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- العمود الأيمن: معلومات الرحلة --}}
        <div class="space-y-6 lg:col-span-1">
            {{-- بطاقة السائق والجهة المرسلة --}}
            <div class="p-6 bg-white rounded-[2rem] border border-gray-100 shadow-sm dark:bg-boxdark dark:border-boxdark-2">
                <h3 class="flex gap-2 items-center mb-6 text-sm font-black text-on-surface dark:text-white">
                    <span class="material-symbols-outlined text-primary text-[20px]">info</span>
                    معلومات الرحلة
                </h3>

                <div class="space-y-6">
                    <div class="flex gap-4 items-center">
                        <div class="flex justify-center items-center w-12 h-12 text-blue-500 bg-blue-50 rounded-2xl dark:bg-boxdark-2">
                            <span class="material-symbols-outlined text-[24px]">outbound</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">الفرع المرسل</p>
                            <p class="text-sm font-black text-on-surface dark:text-white">{{ $package->senderBranch->name ?? 'مكتب خارجي' }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 items-center">
                        <div class="flex justify-center items-center w-12 h-12 text-emerald-500 bg-emerald-50 rounded-2xl dark:bg-boxdark-2">
                            <span class="material-symbols-outlined text-[24px]">sports_motorsports</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">السائق</p>
                            <div class="flex justify-between items-center">
                                <p class="text-sm font-black text-on-surface dark:text-white">{{ $package->driver->name ?? 'سائق خارجي' }}</p>
                                @if($package->driver && $package->driver->phone)
                                    <div class="flex gap-1">
                                        <a href="tel:{{ $package->driver->phone }}" class="flex justify-center items-center w-8 h-8 text-gray-400 bg-gray-50 rounded-lg transition-colors hover:text-primary">
                                            <span class="material-symbols-outlined text-[18px]">call</span>
                                        </a>
                                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $package->driver->phone) }}" target="_blank" class="flex justify-center items-center w-8 h-8 text-emerald-500 bg-emerald-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-[18px]">chat</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($package->notes)
                    <div class="p-4 mt-6 bg-gray-50 rounded-2xl border border-gray-100 dark:bg-boxdark-2 dark:border-boxdark">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">ملاحظات إضافية</p>
                        <p class="text-xs font-bold leading-relaxed text-gray-600 dark:text-bodydark">
                            {{ $package->notes }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- إحصائيات سريعة --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="p-6 bg-boxdark rounded-[2rem] text-white shadow-lg shadow-boxdark/20 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-16 h-16 rounded-full blur-xl transition-transform bg-white/5 group-hover:scale-110"></div>
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">الطرود</p>
                    <p class="text-3xl font-black">{{ $package->shipments->count() }}</p>
                </div>
                <div class="p-6 bg-primary rounded-[2rem] text-white shadow-lg shadow-primary/20 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 w-16 h-16 rounded-full blur-xl transition-transform bg-white/5 group-hover:scale-110"></div>
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">الوزن التقديري</p>
                    <p class="text-3xl font-black">--</p>
                </div>
            </div>
        </div>

        {{-- العمود الأيسر: قائمة الطرود --}}
        <div class="space-y-4 lg:col-span-2">
            <div class="flex justify-between items-center px-4">
                <h3 class="text-base font-black text-on-surface dark:text-white">قائمة الطرود المضمنة</h3>
                <span class="text-[11px] font-bold text-gray-400">اتبع حالة كل طرد عند التفريغ</span>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach($package->shipments as $shipment)
                    <div class="flex flex-col p-5 bg-white rounded-[2rem] border border-gray-100 shadow-sm transition-all hover:shadow-md dark:bg-boxdark dark:border-boxdark-2 relative overflow-hidden group">
                        @if($shipment->status == 'delivered')
                            <div class="flex absolute -top-6 -left-6 justify-center items-end pb-1 w-12 h-12 rotate-45 bg-emerald-500/10">
                                <span class="material-symbols-outlined text-[14px] text-emerald-500">done_all</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="mb-1 font-mono text-sm font-black text-on-surface dark:text-white">#{{ $shipment->code }}</h4>
                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-gray-50 text-[9px] font-black text-gray-400 dark:bg-boxdark-2 uppercase">
                                    <span class="material-symbols-outlined text-[12px]">{{ $shipment->package_type == 'carton' ? 'inventory_2' : 'package_2' }}</span>
                                    {{ $shipment->package_type ?? 'طرد' }}
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="text-[9px] font-bold text-gray-400 uppercase mb-1">المستلم</div>
                                <div class="text-[11px] font-black text-on-surface dark:text-white">{{ $shipment->receiverCustomer->name ?? $shipment->receiver_name }}</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center p-3 mt-auto rounded-2xl border border-gray-50 bg-gray-50/50 dark:bg-boxdark-2/50 dark:border-boxdark">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-0.5 tracking-tighter">الحساب</span>
                                @php
                                    $isPaid = $shipment->payment_method === 'prepaid';
                                @endphp
                                <span class="text-[11px] font-black {{ $isPaid ? 'text-emerald-500' : 'text-rose-500 font-headline' }}">
                                    {{ $isPaid ? 'مدفوع مسبقاً' : (number_format($shipment->total_amount) . ' ر.ي') }}
                                </span>
                            </div>
                            
                            @if($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                <div class="flex gap-1">
                                    <a href="tel:{{ $shipment->receiverCustomer->phone }}" class="flex justify-center items-center w-8 h-8 text-gray-400 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark hover:text-primary active:scale-95 dark:border-boxdark-2">
                                        <span class="material-symbols-outlined text-[18px]">call</span>
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $shipment->receiverCustomer->phone) }}" target="_blank" class="flex justify-center items-center w-8 h-8 text-emerald-500 bg-white rounded-xl border border-gray-100 shadow-sm transition-all dark:bg-boxdark hover:bg-emerald-500 hover:text-white active:scale-95 dark:border-boxdark-2">
                                        <span class="material-symbols-outlined text-[18px]">chat</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($package->shipments->count() == 0)
                <div class="flex flex-col justify-center items-center py-20 bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:bg-boxdark-2/30 dark:border-boxdark">
                    <span class="material-symbols-outlined text-[48px] text-gray-300">inventory_2</span>
                    <p class="mt-4 text-sm font-black text-gray-400">لا توجد طرود مضمنة في هذه الإرسالية</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
