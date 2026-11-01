{{-- مودال عرض تفاصيل الشحنة داخل دفتر الحسابات (سطح المكتب) --}}
<div x-show="showShipmentModal" x-cloak 
     class="fixed inset-0 z-[99999] flex items-center justify-center p-4 overflow-y-auto bg-gray-900/60 backdrop-blur-sm transition-opacity"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
     
    <div class="w-full max-w-2xl bg-white border border-gray-100 shadow-2xl rounded-3xl dark:bg-gray-900 dark:border-gray-800 overflow-hidden flex flex-col max-h-[90vh]" 
         @click.outside="showShipmentModal = false">
        
        {{-- رأس المودال --}}
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
            <div class="flex gap-3 items-center">
                <div class="flex justify-center items-center w-10 h-10 rounded-2xl bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[22px]">package_2</span>
                </div>
                <div>
                    <h3 class="flex gap-2 items-center text-base font-black text-gray-900 dark:text-white">
                        <span>تفاصيل الشحنة:</span>
                        <span class="px-2.5 py-0.5 font-mono text-sm font-black rounded-lg bg-primary-container text-primary-hover dark:bg-primary/20 dark:text-primary" 
                              x-text="shipmentDetails?.bond_number || '-'"></span>
                    </h3>
                    <p class="mt-0.5 text-xs font-medium text-gray-400">
                        كود التتبع: <span class="font-mono font-bold text-gray-600 dark:text-gray-300" x-text="shipmentDetails?.bond_number || '-'"></span>
                    </p>
                </div>
            </div>

            <div class="flex gap-2 items-center">
                <template x-if="shipmentDetails?.status_text">
                    <span class="px-3 py-1 text-xs font-black rounded-full"
                          :class="{
                              'bg-amber-50 text-amber-600 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20': shipmentDetails.status === 'pending',
                              'bg-blue-50 text-blue-600 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20': shipmentDetails.status === 'in_transit',
                              'bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20': shipmentDetails.status === 'delivered',
                              'bg-rose-50 text-rose-600 border border-rose-200 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20': ['returned', 'cancelled'].includes(shipmentDetails.status)
                          }"
                          x-text="shipmentDetails.status_text">
                    </span>
                </template>

                <button @click="showShipmentModal = false" class="p-1 text-gray-400 rounded-lg transition-colors hover:text-rose-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>

        {{-- حالة التحميل --}}
        <template x-if="loadingShipment">
            <div class="p-12 text-center">
                <span class="text-4xl animate-spin material-symbols-outlined text-primary">progress_activity</span>
                <p class="mt-3 text-sm font-bold text-gray-500">جاري تحميل بيانات الشحنة...</p>
            </div>
        </template>

        {{-- جسم المودال القابل للتمرير --}}
        <template x-if="!loadingShipment && shipmentDetails">
            <div class="overflow-y-auto p-6 space-y-5 text-sm">

                {{-- 1. كروت المالية السريعة --}}
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="p-3.5 text-center bg-gray-50 rounded-2xl border border-gray-100 dark:bg-gray-800/60 dark:border-gray-800">
                        <span class="block text-[11px] font-bold text-gray-400 mb-1">إجمالي الأجور</span>
                        <span class="text-base font-black text-gray-900 dark:text-white" x-text="shipmentDetails.total_amount"></span>
                        <span class="text-[10px] font-bold text-gray-400 mr-1">ر.ي</span>
                    </div>

                    <div class="p-3.5 text-center rounded-2xl border border-emerald-100 bg-emerald-50/60 dark:bg-emerald-500/10 dark:border-emerald-500/20">
                        <span class="block text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mb-1">عمولة الفرع</span>
                        <span class="text-base font-black text-emerald-600 dark:text-emerald-400" x-text="shipmentDetails.total_commission"></span>
                        <span class="text-[10px] font-bold text-emerald-500 mr-1">ر.ي</span>
                    </div>

                    <div class="p-3.5 text-center rounded-2xl border border-blue-100 bg-blue-50/60 dark:bg-blue-500/10 dark:border-blue-500/20">
                        <span class="block text-[11px] font-bold text-blue-600 dark:text-blue-400 mb-1">تحصيل المستلم</span>
                        <span class="text-base font-black text-blue-600 dark:text-blue-400" x-text="shipmentDetails.amount_to_collect_receiver"></span>
                        <span class="text-[10px] font-bold text-blue-500 mr-1">ر.ي</span>
                    </div>

                    <div class="p-3.5 text-center rounded-2xl border border-amber-100 bg-amber-50/60 dark:bg-amber-500/10 dark:border-amber-500/20">
                        <span class="block text-[11px] font-bold text-amber-600 dark:text-amber-400 mb-1">طريقة الدفع</span>
                        <span class="block text-xs font-black text-amber-700 truncate dark:text-amber-300" x-text="shipmentDetails.payment_method"></span>
                    </div>
                </div>

                {{-- 2. أطراف الشحنة (المرسل والمستلم) --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    {{-- كرت المرسل --}}
                    <div class="p-4 space-y-2.5 rounded-2xl border border-gray-100 bg-gray-50/80 dark:bg-gray-800/40 dark:border-gray-800">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200/60 dark:border-gray-700/60">
                            <span class="flex gap-1.5 items-center text-xs font-black text-gray-500">
                                <span class="material-symbols-outlined text-[16px] text-emerald-600">outbox</span>
                                بيانات المُرسل
                            </span>
                            <span class="text-[11px] font-bold text-gray-400" x-text="shipmentDetails.sender_branch"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-gray-400">الاسم:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200" x-text="shipmentDetails.sender_name"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-gray-400">الهاتف:</span>
                            <span class="font-mono font-bold text-gray-700 dark:text-gray-300" dir="ltr" x-text="shipmentDetails.sender_phone"></span>
                        </div>
                        <template x-if="shipmentDetails.sender_whatsapp_link">
                            <div class="pt-1">
                                <a :href="shipmentDetails.sender_whatsapp_link" target="_blank"
                                   class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700">
                                    <span class="material-symbols-outlined text-[14px]">chat</span>
                                    مراسلة المرسل عبر الواتساب
                                </a>
                            </div>
                        </template>
                    </div>

                    {{-- كرت المستلم --}}
                    <div class="p-4 space-y-2.5 rounded-2xl border border-gray-100 bg-gray-50/80 dark:bg-gray-800/40 dark:border-gray-800">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200/60 dark:border-gray-700/60">
                            <span class="flex gap-1.5 items-center text-xs font-black text-gray-500">
                                <span class="material-symbols-outlined text-[16px] text-blue-600">move_to_inbox</span>
                                بيانات المُستلم
                            </span>
                            <span class="text-[11px] font-bold text-gray-400" x-text="shipmentDetails.receiver_branch"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-gray-400">الاسم:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200" x-text="shipmentDetails.receiver_name"></span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-gray-400">الهاتف:</span>
                            <span class="font-mono font-bold text-gray-700 dark:text-gray-300" dir="ltr" x-text="shipmentDetails.receiver_phone"></span>
                        </div>
                        <template x-if="shipmentDetails.receiver_whatsapp_link">
                            <div class="pt-1">
                                <a :href="shipmentDetails.receiver_whatsapp_link" target="_blank"
                                   class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700">
                                    <span class="material-symbols-outlined text-[14px]">chat</span>
                                    مراسلة المستلم عبر الواتساب
                                </a>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- 3. بيانات السائق والمانفست والطرد --}}
                <div class="p-4 space-y-2.5 rounded-2xl border border-gray-100 bg-gray-50/80 dark:bg-gray-800/40 dark:border-gray-800">
                    <span class="flex gap-1.5 items-center pb-2 text-xs font-black text-gray-500 border-b border-gray-200/60 dark:border-gray-700/60">
                        <span class="material-symbols-outlined text-[16px] text-amber-600">local_shipping</span>
                        بيانات الرحلة والنقل
                    </span>

                    <div class="grid grid-cols-1 gap-3 text-xs sm:grid-cols-3">
                        <div>
                            <span class="block mb-0.5 font-bold text-gray-400">السائق:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200" x-text="shipmentDetails.driver_name"></span>
                            <template x-if="shipmentDetails.driver_phone && shipmentDetails.driver_phone !== '-'">
                                <span class="block font-mono text-[11px] text-gray-400" dir="ltr" x-text="shipmentDetails.driver_phone"></span>
                            </template>
                        </div>

                        <div>
                            <span class="block mb-0.5 font-bold text-gray-400">رقم المانفست:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200" x-text="shipmentDetails.package_number || 'غير مخصص بعد'"></span>
                        </div>

                        <div>
                            <span class="block mb-0.5 font-bold text-gray-400">الكمية والوزن:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">
                                <span x-text="shipmentDetails.parcels_quantity"></span> طرد 
                                <span class="font-normal text-gray-400" x-show="shipmentDetails.weight && shipmentDetails.weight !== '-'" x-text="'(' + shipmentDetails.weight + ')'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 4. معلومات إضافية والملاحظات --}}
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center font-bold text-gray-400">
                        <span>تاريخ التسجيل: <span class="text-gray-700 dark:text-gray-300" x-text="shipmentDetails.created_at"></span></span>
                        <span>بواسطة: <span class="text-gray-700 dark:text-gray-300" x-text="shipmentDetails.creator_name"></span></span>
                    </div>

                    <div class="py-1">
                        <span class="block mb-1 text-[11px] font-bold text-gray-400">ملاحظات الشحنة:</span>
                        <p class="p-3 text-xs font-medium leading-relaxed text-gray-700 bg-gray-50 rounded-xl dark:bg-gray-800/50 dark:text-gray-300" 
                           x-text="shipmentDetails.notes || 'لا توجد ملاحظات مسجلة.'"></p>
                    </div>
                </div>

            </div>
        </template>

        {{-- أزرار الإجراءات السفلية --}}
        <div class="flex flex-wrap gap-2 justify-between items-center p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
            <div class="flex gap-2 items-center">
                <template x-if="shipmentDetails?.show_url">
                    <a :href="shipmentDetails.show_url" target="_blank"
                       class="inline-flex gap-1.5 items-center px-4 py-2 text-xs font-bold rounded-xl transition-colors text-primary bg-primary/10 hover:bg-primary/20">
                        <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                        فتح صفحة الشحنة
                    </a>
                </template>

                <template x-if="shipmentDetails?.invoice_url">
                    <a :href="shipmentDetails.invoice_url" target="_blank"
                       class="inline-flex gap-1.5 items-center px-4 py-2 text-xs font-bold text-gray-700 bg-white rounded-xl border border-gray-200 transition-colors hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700">
                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                        فاتورة A4
                    </a>
                </template>

                <template x-if="shipmentDetails?.thermal_url">
                    <a :href="shipmentDetails.thermal_url" target="_blank"
                       class="inline-flex gap-1.5 items-center px-4 py-2 text-xs font-bold text-gray-700 bg-white rounded-xl border border-gray-200 transition-colors hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        طباعة حرارية
                    </a>
                </template>
            </div>

            <button type="button" @click="showShipmentModal = false"
                    class="px-5 py-2 text-xs font-bold text-gray-500 bg-gray-100 rounded-xl transition-colors hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                إغلاق
            </button>
        </div>

    </div>
</div>

