@extends('mobile.layouts.app')

@section('title', 'تفاصيل الطرد الوارد - ' . $shipment->code)

@section('content')
    <div class="flex relative flex-col gap-5 px-4 pt-6 pb-8 min-h-screen bg-slate-50/50" x-data="{ 
                        isSubmitting: false 
                    }">

        {{-- ================= الهيدر السريع ================= --}}
        <div class="flex justify-between items-center">
            <div class="flex gap-3 items-center">
                <a href="{{ route('shipment.incoming.index') }}" {{-- تأكد من اسم الراوت الخاص بـ index الوارد --}}
                    class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward_ios</span>
                </a>
                <div>
                    <h1 class="text-lg font-black font-headline text-slate-800">طرد وارد</h1>
                    <p class="text-sm font-bold tracking-wider text-primary font-mono">{{ $shipment->code }}</p>
                </div>
            </div>

            {{-- حالة الطرد --}}
            @php
                $statusColors = [
                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                    'in_transit' => 'bg-blue-50 text-blue-600 border-blue-200',
                    'received_at_branch' => 'bg-purple-50 text-purple-600 border-purple-200',
                    'out_for_delivery' => 'bg-teal-50 text-teal-600 border-teal-200',
                    'delivered' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                    'cancelled' => 'bg-slate-50 text-slate-600 border-slate-200',
                    'returned' => 'bg-rose-50 text-rose-600 border-rose-200',
                ];

                $statusIcons = [
                    'pending' => 'schedule',
                    'in_transit' => 'local_shipping',
                    'received_at_branch' => 'inventory_2', // أيقونة المستودع
                    'out_for_delivery' => 'two_wheeler', // أيقونة المندوب
                    'delivered' => 'task_alt',
                    'cancelled' => 'block',
                    'returned' => 'assignment_return',
                ];

                $statusNames = [
                    'pending' => 'قيد التجهيز بالمصدر',
                    'in_transit' => 'في الطريق إلينا',
                    'received_at_branch' => 'بالمستودع (جاهز للتسليم)',
                    'out_for_delivery' => 'خرج للتوصيل للعميل',
                    'delivered' => 'تم التسليم للعميل',
                    'cancelled' => 'ملغي',
                    'returned' => 'مرتجع',
                ];

                $colorClass = $statusColors[$shipment->status] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                $icon = $statusIcons[$shipment->status] ?? 'info';
                $name = $statusNames[$shipment->status] ?? $shipment->status;
            @endphp
            <div
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-bold text-xs shadow-sm {{ $colorClass }}">
                <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
                {{ $name }}
            </div>
        </div>

        {{-- ================= أزرار الإجراءات ================= --}}
<div class="flex gap-3 items-center mt-1">

    @if(in_array($shipment->status, ['delivered', 'returned', 'cancelled']))
        {{-- 1. حالة الإغلاق (مقفلة) --}}
        <div class="flex-[2] flex items-center justify-center gap-2 h-12 bg-slate-50 text-slate-400 rounded-2xl font-bold text-[10px] border border-slate-100">
            <span class="material-symbols-outlined text-[16px]">lock</span>
            تم إغلاق هذا الطرد
        </div>

    @elseif($shipment->status === 'pending')
        {{-- 2. حالة قيد التجهيز --}}
        <div class="flex-[2] flex items-center justify-center gap-2 h-12 bg-amber-50 text-amber-500 rounded-2xl font-bold text-[10px] border border-amber-100">
            <span class="material-symbols-outlined text-[16px]">schedule</span>
            الطرد لا يزال قيد التجهيز
        </div>

    @elseif($shipment->status === 'in_transit')
        {{-- 3. حالة في الطريق (يجب استلامه في الفرع أولاً) 🚚 --}}
        <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" class="flex-[2]" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf
            {{-- نرسل القيمة المطلوبة للتحويل إلى المستودع --}}
            <input type="hidden" name="status" value="received_at_branch">
            <button type="submit" :disabled="isSubmitting"
                class="w-full flex items-center justify-center gap-2 px-4 h-12 bg-blue-500 text-white rounded-2xl font-bold text-xs shadow-[0_8px_20px_rgba(59,130,246,0.3)] hover:bg-blue-600 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                تأكيد وصول الطرد للفرع
            </button>
        </form>

    @else
        {{-- 4. حالة وصل المستودع (received_at_branch) أو مع المندوب 📦 -> تظهر أزرار التسليم للعميل --}}
        <div class="flex-[2] relative" x-data="{ openStatusMenu: false, showPaymentModal: false }">
            <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" x-ref="statusForm"
                @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="status" x-ref="statusInput">

                <button type="button" @click="openStatusMenu = !openStatusMenu" @click.outside="openStatusMenu = false"
                    class="w-full flex items-center justify-between px-4 h-12 bg-primary text-white rounded-2xl font-bold text-xs shadow-[0_8px_20px_rgba(var(--color-primary),0.2)] hover:bg-primary/90 active:scale-95 transition-all">
                    <div class="flex gap-2 items-center">
                        <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                        <span>إجراءات تسليم العميل</span>
                    </div>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-300"
                        :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                </button>

                <div x-show="openStatusMenu" x-cloak x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="absolute bottom-full mb-2 right-0 w-full bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_-15px_40px_-10px_rgba(0,0,0,0.15)] border border-slate-100 p-1.5 z-[60]">

                    {{-- زر التأكيد الذكي (يفحص نوع الدفع أولاً) --}}
                    <button type="button" @click="
                            @if($shipment->payment_method !== 'prepaid')
                                showPaymentModal = true; 
                                openStatusMenu = false;
                            @else
                                $refs.statusInput.value = 'delivered'; 
                                $refs.statusForm.submit();
                            @endif
                        " :disabled="isSubmitting"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-emerald-50 transition-all text-right group active:scale-[0.98]">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                        </div>
                        <span class="text-xs font-black text-slate-700">تأكيد تسليم الطرد للعميل</span>
                    </button>

                    {{-- زر المرتجع --}}
                    <button type="button" @click="$refs.statusInput.value = 'returned'; $refs.statusForm.submit()"
                        :disabled="isSubmitting"
                        class="w-full flex items-center gap-3 px-3 py-2.5 mt-1 rounded-xl hover:bg-rose-50 transition-all text-right group active:scale-[0.98]">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform shrink-0">
                            <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                        </div>
                        <span class="text-xs font-black text-slate-700">رفض الاستلام (إرجاع الطرد)</span>
                    </button>
                </div>

                {{-- ================= Modal: نافذة التنبيه المالي بوب أب ================= --}}
                @if($shipment->payment_method !== 'prepaid')
                    <div x-show="showPaymentModal" x-cloak
                        class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div x-show="showPaymentModal" x-transition.opacity duration.300ms @click="showPaymentModal = false"
                            class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

                        <div x-show="showPaymentModal" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            class="relative bg-white w-full max-w-sm rounded-[2rem] shadow-2xl p-6 text-center border border-slate-100">

                            <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                                <span class="material-symbols-outlined text-[40px]">payments</span>
                            </div>

                            <h3 class="text-lg font-black text-slate-800 font-headline mb-2">تنبيه تحصيل مالي!</h3>
                            <p class="text-xs font-bold text-slate-500 mb-6 leading-relaxed">
                                هذا الطرد غير مدفوع مسبقاً. الرجاء استلام المبلغ التالي من العميل قبل تأكيد عملية التسليم.
                            </p>

                            <div class="bg-rose-50 border border-rose-100 rounded-2xl p-4 mb-6">
                                <p class="text-[10px] font-bold text-rose-600/70 mb-1">المبلغ المطلوب تحصيله</p>
                                <p class="text-3xl font-black font-mono text-rose-700 dir-ltr">
                                    {{ number_format($shipment->total_amount, 2) }}</p>
                            </div>

                            <div class="flex gap-2">
                                <button type="button" @click="showPaymentModal = false"
                                    class="flex-1 h-12 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-xl font-bold text-xs transition-colors">
                                    تراجع
                                </button>
                                <button type="button"
                                    @click="$refs.statusInput.value = 'delivered'; $refs.statusForm.submit()"
                                    class="flex-[2] h-12 bg-emerald-500 text-white hover:bg-emerald-600 rounded-xl font-bold text-xs shadow-[0_8px_20px_rgba(16,185,129,0.3)] transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">verified</span>
                                    تم استلام المبلغ
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- ================= نهاية الـ Modal ================= --}}

            </form>
        </div>
    @endif

    {{-- طباعة --}}
    <a href="{{ route('receipt.generate', ['type' => 'Shipment', 'id' => $shipment->id]) }}" target="_blank"
        class="flex flex-1 gap-2 justify-center items-center h-12 text-xs font-bold bg-white rounded-2xl border shadow-sm transition-all text-slate-600 border-slate-100 hover:bg-slate-50 active:scale-95">
        <span class="material-symbols-outlined text-[18px]">print</span>
        طباعة
    </a>
</div>

        {{-- ================= بطاقة المالية (الأهم في الوارد) ================= --}}
        <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="flex justify-center items-center w-10 h-10 bg-gradient-to-br rounded-xl shadow-inner from-slate-100 to-slate-50 text-slate-600">
                    <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                </div>
                <h3 class="text-sm font-black text-slate-800 font-headline">الخلاصة المالية</h3>
            </div>

            @if($shipment->payment_method === 'prepaid')
                <div class="flex items-center justify-between p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[24px] text-emerald-500">check_circle</span>
                        <div>
                            <p class="text-[10px] font-bold text-emerald-600/70">حالة الدفع</p>
                            <p class="text-sm font-black text-emerald-700">خالص (دفع مسبق)</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-lg">لا يوجد
                        تحصيل</span>
                </div>
            @else
                <div class="flex items-center justify-between p-4 bg-rose-50/50 border border-rose-100 rounded-2xl">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[24px] text-rose-500">payments</span>
                        <div>
                            <p class="text-[10px] font-bold text-rose-600/70">المبلغ المطلوب تحصيله من العميل</p>
                            <p class="text-lg font-black font-mono text-rose-700 dir-ltr text-right">
                                {{ number_format($shipment->total_amount, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ================= بطاقات بيانات المرسل والمستلم ================= --}}
        @php
            $whatsappMsg = "مرحباً *" . ($shipment->receiverCustomer->name ?? 'عميلنا العزيز') . "*،\nيسعدنا إبلاغك بوصول طردك رقم: *" . $shipment->code . "* إلى فرعنا.\nيرجى التفضل بزيارتنا لاستلامه.";
            if ($shipment->payment_method !== 'prepaid') {
                $whatsappMsg .= "\n*ملاحظة:* يرجى تجهيز مبلغ وقدره " . number_format($shipment->total_amount, 2) . " عند الاستلام.";
            }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- 1. بطاقة المرسل (الجهة المُصدِرة) --}}
            <div
                class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] h-full flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-10 h-10 bg-amber-50 rounded-xl text-amber-500">
                            <span class="material-symbols-outlined text-[20px]">person_check</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 font-headline">بيانات المُرسل</h3>
                            <p class="text-[10px] font-bold text-slate-400">الشخص أو الجهة المرسلة للطرد</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-2xl flex-1 flex flex-col justify-center">
                    <p class="text-sm font-black text-slate-800">{{ $shipment->senderCustomer->name ?? 'غير مسجل' }}</p>
                    <p class="text-xs font-bold text-slate-500 mt-1 dir-ltr text-right">
                        {{ $shipment->senderCustomer->phone ?? 'لا يوجد رقم' }}
                    </p>
                </div>
            </div>

            {{-- 2. بطاقة المستلم (الوجهة) مع أزرار التواصل --}}
            <div
                class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)] h-full flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <div class="flex gap-3 items-center">
                        <div class="flex justify-center items-center w-10 h-10 bg-primary/10 rounded-xl text-primary">
                            <span class="material-symbols-outlined text-[20px]">person_pin_circle</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 font-headline">بيانات المستلم</h3>
                            <p class="text-[10px] font-bold text-slate-400">العميل صاحب الطرد</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50/50 border border-slate-100 rounded-2xl flex-1 flex flex-col justify-between">
                    <div>
                        <p class="text-sm font-black text-slate-800">{{ $shipment->receiverCustomer->name ?? 'غير مسجل' }}
                        </p>
                        <p class="text-xs font-bold text-slate-500 mt-1 dir-ltr text-right">
                            {{ $shipment->receiverCustomer->phone ?? 'لا يوجد رقم' }}
                        </p>
                    </div>

                    @if($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                        <div class="flex gap-2 mt-4 pt-4 border-t border-slate-200/60">
                            <a href="https://wa.me/{{ ltrim($shipment->receiverCustomer->phone, '+') }}?text={{ urlencode($whatsappMsg) }}"
                                target="_blank"
                                class="flex-1 h-10 bg-[#25D366]/10 text-[#25D366] rounded-xl flex items-center justify-center gap-2 hover:bg-[#25D366]/20 transition-colors font-bold text-xs active:scale-95">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                                إشعار العميل
                            </a>
                            <a href="tel:{{ $shipment->receiverCustomer->phone }}"
                                class="flex-1 h-10 bg-slate-800 text-white rounded-xl flex items-center justify-center gap-2 hover:bg-slate-700 transition-colors font-bold text-xs active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">call</span>
                                اتصال
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= بطاقة المصدر (من أين أتى) ================= --}}
        <div class="bg-white p-5 rounded-[2rem] border border-slate-100 shadow-[0_10px_40px_-10px_rgba(0,0,0,0.05)]">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex justify-center items-center w-10 h-10 bg-slate-50 rounded-xl text-slate-400">
                    <span class="material-symbols-outlined text-[20px]">storefront</span>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800 font-headline">مصدر الطرد</h3>
                    <p class="text-[10px] font-bold text-slate-400">الفرع أو المكتب المُرسِل</p>
                </div>
            </div>

            <div class="p-3 bg-slate-50/50 border border-slate-100 rounded-xl flex justify-between items-center">
                <div>
                    <p class="text-xs font-black text-slate-700">
                        {{-- 💡 إضافة اسم المكتب الرئيسي هنا --}}
                        @if($shipment->sender_office_branch_id && $shipment->senderOfficeBranch)
                            <span class="text-primary">{{ $shipment->senderOfficeBranch->office->name ?? 'مكتب خارجي' }}</span>
                            -
                        @endif

                        {{ $shipment->sender->name ?? 'غير معروف' }}
                    </p>
                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">
                        {{ $shipment->sender_office_branch_id ? 'مكتب وكيل خارجي' : 'فرع داخلي' }}
                    </p>
                </div>
            </div>
        </div>

    </div>
@endsection