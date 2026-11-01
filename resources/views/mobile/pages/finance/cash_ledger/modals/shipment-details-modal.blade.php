{{-- مودال عرض تفاصيل الشحنة (Bottom Sheet للموبايل داخل دفتر الحسابات) --}}
<div x-show="showShipmentModal" x-cloak 
    class="fixed inset-0 z-[99999] flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm sm:p-4 transition-opacity"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div class="w-full sm:max-w-lg bg-white rounded-t-[2.5rem] sm:rounded-[2rem] shadow-2xl relative flex flex-col max-h-[92vh] overflow-hidden"
        @click.outside="showShipmentModal = false"
        x-transition:enter="ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full sm:translate-y-4 sm:scale-95"
        x-transition:enter-end="translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 sm:scale-100"
        x-transition:leave-end="translate-y-full sm:translate-y-4 sm:scale-95">

        {{-- مقبض السحب للموبايل (Swipe Indicator) --}}
        <div class="flex justify-center w-full pt-3 pb-2 sm:hidden cursor-pointer" @click="showShipmentModal = false">
            <div class="w-12 h-1.5 bg-slate-200 rounded-full"></div>
        </div>

        {{-- رأس المودال --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 relative">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">package_2</span>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-800 font-headline">
                        تفاصيل الشحنة
                    </h3>
                    <p class="text-[11px] font-mono font-bold text-primary" dir="ltr" x-text="shipmentDetails?.bond_number || '-'"></p>
                </div>
            </div>

            <button @click="showShipmentModal = false" type="button" class="flex justify-center items-center w-8 h-8 rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- حالة التحميل --}}
        <template x-if="loadingShipment">
            <div class="p-12 text-center">
                <span class="material-symbols-outlined text-4xl animate-spin text-primary">progress_activity</span>
                <p class="mt-3 text-xs font-bold text-slate-500">جاري تحميل بيانات الشحنة...</p>
            </div>
        </template>

        {{-- المحتوى القابل للتمرير --}}
        <template x-if="!loadingShipment && shipmentDetails">
            <div class="overflow-y-auto px-6 py-4 space-y-4 custom-scrollbar text-xs">

                {{-- كرت الحالة والمبالغ المالية الأساسية --}}
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 font-bold">الحالة:</span>
                        <span class="px-2.5 py-1 text-[11px] font-black rounded-full"
                              :class="{
                                  'bg-amber-100 text-amber-700': shipmentDetails.status === 'pending',
                                  'bg-blue-100 text-blue-700': shipmentDetails.status === 'in_transit',
                                  'bg-emerald-100 text-emerald-700': shipmentDetails.status === 'delivered',
                                  'bg-rose-100 text-rose-700': ['returned', 'cancelled'].includes(shipmentDetails.status)
                              }"
                              x-text="shipmentDetails.status_text">
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-slate-200/60 text-center">
                        <div>
                            <span class="block text-[10px] text-slate-400 font-bold mb-0.5">إجمالي الأجور</span>
                            <span class="font-black text-slate-800 text-sm" x-text="shipmentDetails.total_amount"></span>
                            <span class="text-[9px] text-slate-400">ر.ي</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-emerald-600 font-bold mb-0.5">عمولة الفرع</span>
                            <span class="font-black text-emerald-600 text-sm" x-text="shipmentDetails.total_commission"></span>
                            <span class="text-[9px] text-emerald-500">ر.ي</span>
                        </div>
                        <div>
                            <span class="block text-[10px] text-blue-600 font-bold mb-0.5">تحصيل المستلم</span>
                            <span class="font-black text-blue-600 text-sm" x-text="shipmentDetails.amount_to_collect_receiver"></span>
                            <span class="text-[9px] text-blue-500">ر.ي</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200/60 flex justify-between items-center text-[11px]">
                        <span class="text-slate-400 font-bold">طريقة الدفع:</span>
                        <span class="font-black text-slate-700" x-text="shipmentDetails.payment_method"></span>
                    </div>
                </div>

                {{-- المرسل والمستلم --}}
                <div class="space-y-3">
                    {{-- المرسل --}}
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-2">
                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                            <span class="flex items-center gap-1 font-black text-slate-600 text-[11px]">
                                <span class="material-symbols-outlined text-[15px] text-emerald-600">outbox</span>
                                المُرسل: <span class="text-slate-900 font-bold" x-text="shipmentDetails.sender_name"></span>
                            </span>
                            <span class="text-[10px] font-bold text-slate-400" x-text="shipmentDetails.sender_branch"></span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-400 font-bold">رقم الهاتف:</span>
                            <a :href="'tel:' + shipmentDetails.sender_phone" class="font-mono font-bold text-primary" dir="ltr" x-text="shipmentDetails.sender_phone"></a>
                        </div>
                        <template x-if="shipmentDetails.sender_whatsapp_link">
                            <a :href="shipmentDetails.sender_whatsapp_link" target="_blank"
                               class="flex items-center justify-center gap-1.5 py-1.5 text-[11px] font-bold text-emerald-600 bg-emerald-50 rounded-xl">
                                <span class="material-symbols-outlined text-[14px]">chat</span>
                                مراسلة المرسل واتساب
                            </a>
                        </template>
                    </div>

                    {{-- المستلم --}}
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-sm space-y-2">
                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                            <span class="flex items-center gap-1 font-black text-slate-600 text-[11px]">
                                <span class="material-symbols-outlined text-[15px] text-blue-600">move_to_inbox</span>
                                المُستلم: <span class="text-slate-900 font-bold" x-text="shipmentDetails.receiver_name"></span>
                            </span>
                            <span class="text-[10px] font-bold text-slate-400" x-text="shipmentDetails.receiver_branch"></span>
                        </div>
                        <div class="flex justify-between items-center text-[11px]">
                            <span class="text-slate-400 font-bold">رقم الهاتف:</span>
                            <a :href="'tel:' + shipmentDetails.receiver_phone" class="font-mono font-bold text-primary" dir="ltr" x-text="shipmentDetails.receiver_phone"></a>
                        </div>
                        <template x-if="shipmentDetails.receiver_whatsapp_link">
                            <a :href="shipmentDetails.receiver_whatsapp_link" target="_blank"
                               class="flex items-center justify-center gap-1.5 py-1.5 text-[11px] font-bold text-emerald-600 bg-emerald-50 rounded-xl">
                                <span class="material-symbols-outlined text-[14px]">chat</span>
                                مراسلة المستلم واتساب
                            </a>
                        </template>
                    </div>
                </div>

                {{-- تفاصيل السائق والمانفست --}}
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-[11px]">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 font-bold">السائق:</span>
                        <span class="font-bold text-slate-800" x-text="shipmentDetails.driver_name"></span>
                    </div>
                    <div class="flex justify-between items-center" x-show="shipmentDetails.package_number">
                        <span class="text-slate-400 font-bold">رقم المانفست:</span>
                        <span class="font-bold text-slate-800" x-text="shipmentDetails.package_number"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 font-bold">الطرود:</span>
                        <span class="font-bold text-slate-800">
                            <span x-text="shipmentDetails.parcels_quantity"></span> طرد
                            <span class="text-slate-400 font-normal" x-show="shipmentDetails.weight && shipmentDetails.weight !== '-'" x-text="'(' + shipmentDetails.weight + ')'"></span>
                        </span>
                    </div>
                    <div class="pt-1.5 border-t border-slate-200/60" x-show="shipmentDetails.notes && shipmentDetails.notes !== '-'">
                        <span class="block text-slate-400 font-bold mb-1">الملاحظات:</span>
                        <p class="p-2.5 bg-white rounded-xl text-slate-700" x-text="shipmentDetails.notes"></p>
                    </div>
                </div>

            </div>
        </template>

        {{-- أزرار الإجراءات السفلية --}}
        <div class="flex gap-2 p-4 border-t border-slate-100 bg-white">
            <template x-if="shipmentDetails?.show_url">
                <a :href="shipmentDetails.show_url" target="_blank"
                   class="flex items-center justify-center flex-1 gap-1.5 h-11 text-xs font-black text-white transition-all shadow-[0_8px_20px_rgb(0,0,0,0.1)] rounded-2xl bg-primary active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                    فتح الشحنة
                </a>
            </template>

            <template x-if="shipmentDetails?.invoice_url">
                <a :href="shipmentDetails.invoice_url" target="_blank"
                   class="flex items-center justify-center gap-1.5 px-3.5 h-11 text-xs font-bold text-slate-700 bg-slate-100 rounded-2xl active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                    فاتورة A4
                </a>
            </template>

            <template x-if="shipmentDetails?.thermal_url">
                <a :href="shipmentDetails.thermal_url" target="_blank"
                   class="flex items-center justify-center gap-1.5 px-3.5 h-11 text-xs font-bold text-slate-700 bg-slate-100 rounded-2xl active:scale-95">
                    <span class="material-symbols-outlined text-[16px]">print</span>
                    حراري
                </a>
            </template>

            <button type="button" @click="showShipmentModal = false"
                    class="px-5 h-11 text-xs font-bold text-slate-500 bg-slate-100 rounded-2xl active:scale-95">
                إغلاق
            </button>
        </div>

    </div>
</div>

