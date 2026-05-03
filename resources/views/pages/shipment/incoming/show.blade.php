@extends('layouts.app')

@section('title', 'تفاصيل الطرد الوارد #' . $shipment->bond_number)

@section('content')
    <div class="flex flex-col gap-6 p-4 rounded-3xl bg-slate-50/50 dark:bg-boxdark-2 lg:p-6 font-body" x-data="{ 
        isSubmitting: false, 
        openStatusMenu: false, 
        showPaymentModal: false 
    }" dir="rtl">
        
        {{-- ================= الهيدر العلوي ================= --}}
        <div class="flex flex-col gap-6 justify-between p-5 bg-white rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] md:flex-row md:items-center dark:bg-boxdark dark:border-boxdark-2">
            <div class="flex gap-4 items-center">
                <a href="javascript:history.back()" 
                    class="flex justify-center items-center w-14 h-14 rounded-2xl border shadow-sm transition-all text-slate-400 bg-slate-50 border-slate-100 hover:bg-primary hover:text-white dark:bg-boxdark-2 dark:border-boxdark-2 active:scale-95">
                    <span class="material-symbols-outlined text-[28px]">move_to_inbox</span>
                </a>
                <div>
                    <div class="flex gap-3 items-center">
                        <h1 class="text-xl font-black text-slate-800 dark:text-white font-headline">طرد وارد</h1>
                        <span class="px-3 py-1 font-mono text-xs font-black rounded-lg border bg-primary/10 text-primary border-primary/20">
                            #{{ $shipment->bond_number }}
                        </span>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 items-center mt-2">
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400',
                                'in_transit' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400',
                                'received_at_branch' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400',
                                'out_for_delivery' => 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400',
                                'delivered' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400',
                                'cancelled' => 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-500/10 dark:text-slate-400',
                                'returned' => 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400',
                            ];

                            $statusIcons = [
                                'pending' => 'schedule',
                                'in_transit' => 'local_shipping',
                                'received_at_branch' => 'inventory_2',
                                'out_for_delivery' => 'two_wheeler',
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

                            if ($shipment->is_returned) {
                                $colorClass = 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-500/10 dark:text-rose-400';
                                $icon = 'keyboard_return';
                                if ($shipment->status === 'returned') {
                                    $name = 'مرتجع (تم التسليم للتاجر)';
                                } else {
                                    $name = 'مرتجع بالمصدر (لم يُسلم للتاجر)';
                                }
                            } else {
                                $colorClass = $statusColors[$shipment->status] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                                $icon = $statusIcons[$shipment->status] ?? 'info';
                                $name = $statusNames[$shipment->status] ?? $shipment->status;
                            }
                        @endphp

                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-xl border font-black text-[11px] shadow-sm {{ $colorClass }}">
                            <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
                            {{ $name }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('receipt.generate', ['type' => 'receiver', 'id' => $shipment->uuid]) }}" target="_blank"
                    class="flex flex-1 gap-2 justify-center items-center px-6 h-12 text-sm font-black bg-white rounded-xl border shadow-sm transition-all md:flex-none text-slate-600 border-slate-200 dark:bg-boxdark-2 dark:text-slate-300 dark:border-boxdark hover:bg-slate-50 dark:hover:bg-boxdark hover:text-primary active:scale-95">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                    طباعة السند
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                @php
                    $whatsappMsg = "مرحباً *" . ($shipment->receiverCustomer->name ?? 'عميلنا العزيز') . "*،\nيسعدنا إبلاغك بوصول طردك رقم: *" . $shipment->id . "* إلى فرعنا.\nيرجى التفضل بزيارتنا لاستلامه.";
                    if ($shipment->payment_method !== 'prepaid') {
                        $whatsappMsg .= "\n*ملاحظة:* يرجى تجهيز مبلغ وقدره " . number_format($shipment->total_amount, 2) . " عند الاستلام.";
                    }
                @endphp

                <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                    {{-- بطاقة المُرسل --}}
                    <div class="flex flex-col p-6 bg-white rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:bg-boxdark dark:border-boxdark-2">
                        <div class="flex gap-4 items-center mb-6">
                            <div class="flex justify-center items-center w-12 h-12 text-amber-500 bg-amber-50 rounded-xl dark:bg-amber-500/10 dark:text-amber-400">
                                <span class="material-symbols-outlined text-[24px]">person_check</span>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-white">بيانات المُرسل</h3>
                                <p class="text-[11px] font-bold text-slate-400">الشخص أو الجهة المرسلة للطرد</p>
                            </div>
                        </div>

                        <div class="flex flex-col flex-1 justify-center p-4 rounded-2xl border bg-slate-50/50 border-slate-100 dark:bg-boxdark-2/50 dark:border-boxdark">
                            <p class="text-base font-black text-slate-800 dark:text-white">{{ $shipment->senderCustomer->name ?? 'غير مسجل' }}</p>
                            <p class="mt-1 text-sm font-bold text-right text-slate-500 dir-ltr dark:text-slate-400">
                                {{ $shipment->senderCustomer->phone ?? 'لا يوجد رقم' }}
                            </p>
                        </div>
                    </div>

                    {{-- بطاقة المُستلم --}}
                    <div class="flex flex-col p-6 bg-white rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:bg-boxdark dark:border-boxdark-2 relative overflow-hidden group">
                        <div class="absolute -top-6 -right-6 z-0 w-24 h-24 rounded-full transition-transform pointer-events-none bg-primary/5 group-hover:scale-110"></div>
                        <div class="flex relative z-10 gap-4 items-center mb-6">
                            <div class="flex justify-center items-center w-12 h-12 rounded-xl bg-primary/10 text-primary dark:bg-primary/20">
                                <span class="material-symbols-outlined text-[24px]">person_pin_circle</span>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-800 dark:text-white">بيانات المستلم</h3>
                                <p class="text-[11px] font-bold text-slate-400">العميل صاحب الطرد</p>
                            </div>
                        </div>

                        <div class="flex relative z-10 flex-col flex-1 justify-between p-4 rounded-2xl border bg-slate-50/50 border-slate-100 dark:bg-boxdark-2/50 dark:border-boxdark">
                            <div>
                                <p class="text-base font-black text-slate-800 dark:text-white">{{ $shipment->receiverCustomer->name ?? 'غير مسجل' }}</p>
                                <p class="mt-1 text-sm font-bold text-right text-slate-500 dir-ltr dark:text-slate-400">
                                    {{ $shipment->receiverCustomer->phone ?? 'لا يوجد رقم' }}
                                </p>
                            </div>

                            @if($shipment->receiverCustomer && $shipment->receiverCustomer->phone)
                                <div class="flex gap-3 pt-5 mt-5 border-t border-slate-200 dark:border-boxdark">
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $shipment->receiverCustomer->phone) }}?text={{ urlencode($whatsappMsg) }}" target="_blank"
                                        class="flex-1 flex justify-center items-center gap-2 py-2.5 bg-[#25D366]/10 text-[#25D366] rounded-xl font-black text-sm border border-[#25D366]/20 hover:bg-[#25D366]/20 hover:border-[#25D366]/30 active:scale-95 transition-all">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.305-.885-.653-1.48-1.459-1.653-1.756-.173-.298-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51h-.57c-.198 0-.52.074-.792.347-.272.273-1.04 1.02-1.04 2.482s1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                        </svg>
                                        إشعار عبر واتساب
                                    </a>
                                    <a href="tel:{{ $shipment->receiverCustomer->phone }}"
                                        class="flex flex-1 gap-2 justify-center items-center py-2.5 text-sm font-black bg-white rounded-xl border shadow-sm transition-colors text-slate-700 border-slate-200 hover:bg-slate-50 dark:bg-boxdark-2 dark:text-white dark:border-boxdark dark:hover:bg-boxdark active:scale-95">
                                        <span class="material-symbols-outlined text-[20px]">call</span>
                                        اتصال
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:bg-boxdark dark:border-boxdark-2">
                    <div class="flex gap-4 items-center mb-6">
                        <div class="flex justify-center items-center w-12 h-12 rounded-xl text-slate-500 bg-slate-50 dark:bg-boxdark-2 dark:text-slate-400">
                            <span class="material-symbols-outlined text-[24px]">storefront</span>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-800 dark:text-white">مصدر الطرد</h3>
                            <p class="text-[11px] font-bold text-slate-400">الفرع أو المكتب المُرسِل</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-4 rounded-2xl border bg-slate-50/50 border-slate-100 dark:bg-boxdark-2/50 dark:border-boxdark">
                        <div>
                            <p class="text-sm font-black text-slate-800 dark:text-white">
                                @if($shipment->sender_office_branch_id && $shipment->senderOfficeBranch)
                                    <span class="text-primary">{{ $shipment->senderOfficeBranch->office->name ?? 'مكتب خارجي' }}</span>
                                    -
                                @endif
                                {{ $shipment->sender->name ?? 'غير معروف' }}
                            </p>
                            <span class="inline-flex items-center mt-1.5 px-2 py-0.5 text-[10px] font-bold text-slate-500 bg-white border border-slate-200 rounded-md dark:bg-boxdark dark:border-boxdark-2">
                                {{ $shipment->sender_office_branch_id ? 'مكتب وكيل خارجي' : 'فرع داخلي' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- الخلاصة المالية --}}
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:bg-boxdark dark:border-boxdark-2">
                    <div class="flex gap-3 items-center mb-6">
                        <div class="flex justify-center items-center w-12 h-12 rounded-xl border text-slate-600 bg-slate-50 border-slate-100 dark:bg-boxdark-2 dark:border-boxdark dark:text-slate-300">
                            <span class="material-symbols-outlined text-[24px]">account_balance_wallet</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white font-headline">الخلاصة المالية</h3>
                    </div>

                    @if($shipment->payment_method === 'prepaid')
                        <div class="flex justify-between items-center p-5 bg-emerald-50 rounded-2xl border border-emerald-100 dark:bg-emerald-500/10 dark:border-emerald-500/20">
                            <div class="flex gap-3 items-center">
                                <span class="material-symbols-outlined text-[32px] text-emerald-500">check_circle</span>
                                <div>
                                    <p class="text-[10px] font-black text-emerald-600/80 dark:text-emerald-400">حالة الدفع</p>
                                    <p class="text-base font-black text-emerald-700 dark:text-emerald-300">خالص (دفع مسبق)</p>
                                </div>
                            </div>
                            <span class="px-3 py-1.5 text-[10px] font-black rounded-lg text-emerald-700 bg-white shadow-sm border border-emerald-100 dark:bg-emerald-500/20 dark:text-emerald-300 dark:border-emerald-500/30">لا يوجد تحصيل</span>
                        </div>
                    @else
                        <div class="flex justify-between items-center p-5 bg-rose-50 rounded-2xl border border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20">
                            <div class="flex gap-3 items-center">
                                <span class="material-symbols-outlined text-[32px] text-rose-500">payments</span>
                                <div>
                                    <p class="text-[10px] font-black text-rose-600/80 dark:text-rose-400">المبلغ المطلوب تحصيله</p>
                                    <p class="font-mono text-3xl font-black text-right text-rose-700 dir-ltr dark:text-rose-300 font-headline">
                                        {{ number_format($shipment->total_amount, 0) }}
                                        <span class="text-sm font-bold text-rose-500">ر.ي</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- الإجراءات والتحكم --}}
                <div class="p-6 bg-white rounded-[2rem] border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:bg-boxdark dark:border-boxdark-2">
                    <h3 class="mb-6 text-lg font-black text-slate-800 dark:text-white font-headline">الإجراءات والتحكم</h3>

                    @if(in_array($shipment->status, ['delivered', 'returned', 'cancelled']))
                        <div class="flex gap-2 justify-center items-center h-14 text-sm font-black rounded-xl border shadow-inner text-slate-500 bg-slate-50 border-slate-200 dark:bg-boxdark-2 dark:border-boxdark dark:text-slate-400">
                            <span class="material-symbols-outlined text-[20px]">lock</span>
                            تم إغلاق هذا الطرد
                        </div>

                    @elseif($shipment->status === 'pending')
                        <div class="flex gap-2 justify-center items-center h-14 text-sm font-black text-amber-600 bg-amber-50 rounded-xl border border-amber-200 shadow-sm dark:bg-amber-500/10 dark:border-amber-500/20 dark:text-amber-400">
                            <span class="material-symbols-outlined text-[20px]">schedule</span>
                            الطرد لا يزال قيد التجهيز
                        </div>

                    @elseif($shipment->status === 'in_transit')
                        <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" @submit="isSubmitting = true">
                            @csrf
                            <input type="hidden" name="status" value="received_at_branch">
                            <button type="submit" :disabled="isSubmitting"
                                class="flex gap-2 justify-center items-center px-4 w-full h-14 text-sm font-black text-white bg-blue-500 rounded-xl shadow-lg transition-all shadow-blue-500/20 hover:bg-blue-600 active:scale-95">
                                <span class="material-symbols-outlined text-[22px]" x-show="!isSubmitting">inventory_2</span>
                                <span class="material-symbols-outlined animate-spin text-[22px]" x-show="isSubmitting">progress_activity</span>
                                <span x-text="isSubmitting ? 'جاري التأكيد...' : 'تأكيد وصول الطرد للمستودع'"></span>
                            </button>
                        </form>

                    @else
                        <div class="relative">
                            <form action="{{ route('shipment.updateStatus', $shipment->id) }}" method="POST" x-ref="statusForm" @submit="isSubmitting = true">
                                @csrf
                                <input type="hidden" name="status" x-ref="statusInput">

                                <button type="button" @click="openStatusMenu = !openStatusMenu" @click.outside="openStatusMenu = false"
                                    class="w-full flex items-center justify-between px-5 h-14 {{ $shipment->is_returned ? 'bg-rose-500 hover:bg-rose-600 shadow-rose-500/20' : 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-500/20' }} shadow-lg text-white rounded-xl font-black text-sm transition-all active:scale-95">
                                    <div class="flex gap-2 items-center">
                                        <span class="material-symbols-outlined text-[22px]">{{ $shipment->is_returned ? 'assignment_returned' : 'how_to_reg' }}</span>
                                        <span>{{ $shipment->is_returned ? 'إجراءات تسليم المرتجع' : 'إجراءات تسليم العميل' }}</span>
                                    </div>
                                    <span class="material-symbols-outlined text-[22px] transition-transform duration-300" :class="openStatusMenu ? 'rotate-180' : ''">expand_more</span>
                                </button>

                                <div x-show="openStatusMenu" x-cloak x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    class="absolute top-full mt-2 right-0 w-full bg-white rounded-2xl shadow-[0_20px_50px_-15px_rgba(0,0,0,0.15)] border border-slate-100 p-2 z-[60] dark:bg-boxdark-2 dark:border-boxdark">

                                    @if($shipment->is_returned)
                                        <button type="button" @click="$refs.statusInput.value = 'delivered'; $refs.statusForm.submit()" :disabled="isSubmitting"
                                            class="flex gap-3 items-center px-3 py-3 w-full text-right rounded-xl transition-all hover:bg-emerald-50 dark:hover:bg-emerald-500/10 group">
                                            <div class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform dark:bg-emerald-500/20 dark:text-emerald-400 group-hover:scale-110 shrink-0">
                                                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                                            </div>
                                            <span class="text-xs font-black text-slate-800 dark:text-slate-200">تأكيد تسليم المرتجع للتاجر</span>
                                        </button>
                                    @else
                                        <button type="button" @click="
                                            @if($shipment->payment_method !== 'prepaid')
                                                showPaymentModal = true; 
                                                openStatusMenu = false;
                                            @else
                                                $refs.statusInput.value = 'delivered'; 
                                                $refs.statusForm.submit();
                                            @endif
                                        " :disabled="isSubmitting"
                                            class="flex gap-3 items-center px-3 py-3 w-full text-right rounded-xl transition-all hover:bg-emerald-50 dark:hover:bg-emerald-500/10 group">
                                            <div class="flex justify-center items-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl border border-emerald-100 transition-transform dark:bg-emerald-500/20 dark:text-emerald-400 group-hover:scale-110 shrink-0">
                                                <span class="material-symbols-outlined text-[20px]">task_alt</span>
                                            </div>
                                            <span class="text-xs font-black text-slate-800 dark:text-slate-200">تأكيد تسليم الطرد للعميل</span>
                                        </button>

                                        <button type="button" @click="$refs.statusInput.value = 'returned'; $refs.statusForm.submit()" :disabled="isSubmitting"
                                            class="flex gap-3 items-center px-3 py-3 mt-1 w-full text-right rounded-xl transition-all hover:bg-rose-50 dark:hover:bg-rose-500/10 group">
                                            <div class="flex justify-center items-center w-10 h-10 text-rose-600 bg-rose-50 rounded-xl border border-rose-100 transition-transform dark:bg-rose-500/20 dark:text-rose-400 group-hover:scale-110 shrink-0">
                                                <span class="material-symbols-outlined text-[20px]">assignment_return</span>
                                            </div>
                                            <span class="text-xs font-black text-slate-800 dark:text-slate-200">رفض الاستلام (إرجاع الطرد)</span>
                                        </button>
                                    @endif
                                </div>

                                {{-- نافذة الدفع --}}
                                @if(!$shipment->is_returned && $shipment->payment_method !== 'prepaid')
                                    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                        <div x-show="showPaymentModal" x-transition.opacity duration.300ms @click="showPaymentModal = false" class="absolute inset-0 backdrop-blur-sm bg-slate-900/40"></div>

                                        <div x-show="showPaymentModal" x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 scale-90 translate-y-8"
                                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                            class="relative p-6 w-full max-w-sm text-center bg-white rounded-3xl border shadow-2xl border-slate-100 dark:bg-boxdark dark:border-boxdark-2">
                                            
                                            <div class="flex justify-center items-center mx-auto mb-4 w-20 h-20 text-rose-500 bg-rose-50 rounded-full animate-bounce dark:bg-rose-500/10 dark:text-rose-400">
                                                <span class="material-symbols-outlined text-[40px]">payments</span>
                                            </div>

                                            <h3 class="mb-2 text-lg font-black text-slate-800 dark:text-white font-headline">تنبيه تحصيل مالي!</h3>
                                            <p class="mb-6 text-xs font-bold leading-relaxed text-slate-500 dark:text-slate-400">
                                                هذا الطرد غير مدفوع مسبقاً. الرجاء استلام المبلغ التالي من العميل قبل تأكيد عملية التسليم.
                                            </p>

                                            <div class="p-5 mb-6 bg-rose-50 rounded-2xl border border-rose-100 dark:bg-rose-500/10 dark:border-rose-500/20">
                                                <p class="mb-1 text-[10px] font-black text-rose-600/70 dark:text-rose-400">المبلغ المطلوب تحصيله</p>
                                                <p class="font-mono text-3xl font-black text-rose-700 dir-ltr dark:text-rose-300">
                                                    {{ number_format($shipment->total_amount, 0) }}
                                                </p>
                                            </div>

                                            <div class="flex gap-2">
                                                <button type="button" @click="showPaymentModal = false"
                                                    class="flex-1 h-12 text-xs font-black rounded-xl transition-colors text-slate-600 bg-slate-100 hover:bg-slate-200 dark:bg-boxdark-2 dark:text-slate-300 dark:hover:bg-gray-600">
                                                    تراجع
                                                </button>
                                                <button type="button" @click="$refs.statusInput.value = 'delivered'; $refs.statusForm.submit()"
                                                    class="flex-[2] h-12 bg-emerald-500 text-white hover:bg-emerald-600 rounded-xl font-black text-xs shadow-sm transition-all flex items-center justify-center gap-2 active:scale-95">
                                                    <span class="material-symbols-outlined text-[18px]">verified</span>
                                                    تم استلام المبلغ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection