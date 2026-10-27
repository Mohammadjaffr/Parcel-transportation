{{-- مودال عرض تفاصيل السند بالكامل --}}
<div x-show="showDetailsModal" x-cloak 
     class="fixed inset-0 z-[99999] flex items-center justify-center p-4 overflow-y-auto bg-gray-900/50 backdrop-blur-sm transition-opacity">
     
    <div class="w-full max-w-md p-6 bg-white border border-gray-100 shadow-2xl rounded-2xl dark:bg-gray-900 dark:border-gray-800" @click.outside="showDetailsModal = false">
        
        {{-- رأس المودال --}}
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="flex items-center gap-2 text-base font-bold text-gray-900 dark:text-white">
                بيانات السند: 
                <span class="px-2 py-0.5 text-sm font-mono font-black bg-primary-container text-primary-hover dark:bg-primary/20 dark:text-primary rounded-lg" x-text="details.receipt_number"></span>
            </h3>
            <button @click="showDetailsModal = false" class="text-gray-400 transition-colors hover:text-rose-500">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- تفاصيل السند --}}
        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-xs font-bold text-gray-400">نوع السند:</span>
                <span class="font-bold" :class="details.type === 'income' ? 'text-emerald-600' : 'text-rose-600'" x-text="details.type_text"></span>
            </div>
            
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-xs font-bold text-gray-400">المبلغ:</span>
                <span class="text-lg font-black text-gray-900 dark:text-white">
                    <span x-text="details.amount"></span> <span class="text-xs text-gray-500">ر.ي</span>
                </span>
            </div>
            
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-xs font-bold text-gray-400">التصنيف:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.category"></span>
            </div>
            
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-xs font-bold text-gray-400">طريقة الدفع:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.payment_method"></span>
            </div>
            
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-xs font-bold text-gray-400">الفرع:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.branch"></span>
            </div>
            
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-xs font-bold text-gray-400">المسؤول عن الحركة:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.user"></span>
            </div>
            
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-xs font-bold text-gray-400">تاريخ الحركة:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.transaction_date"></span>
            </div>
            
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50" x-show="details.reference_number">
                <span class="text-xs font-bold text-gray-400">رقم المرجع:</span>
                <span class="font-mono font-bold text-gray-700 dark:text-gray-200" x-text="details.reference_number"></span>
            </div>
            
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50" x-show="details.driver_name">
                <span class="text-xs font-bold text-gray-400">السائق:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200">
                    <span x-text="details.driver_name"></span>
                    <template x-if="details.driver_phone">
                        <span class="text-xs font-mono text-gray-400 font-normal mr-1" dir="ltr" x-text="'(' + details.driver_phone + ')'"></span>
                    </template>
                </span>
            </div>
            
            <div class="py-1">
                <span class="block mb-1.5 text-xs font-bold text-gray-400">البيان والملاحظات:</span>
                <p class="p-3 text-xs font-medium leading-relaxed bg-gray-50 rounded-xl text-gray-700 dark:bg-gray-800/50 dark:text-gray-300" 
                   x-text="details.notes || 'لا توجد ملاحظات مسجلة.'"></p>
            </div>

            {{-- المرفقات إن وجدت --}}
            <template x-if="details.attachment_url">
                <div class="pt-2">
                    <a :href="details.attachment_url" target="_blank" 
                       class="flex items-center justify-center w-full gap-2 py-2.5 text-xs font-bold text-blue-600 transition-colors bg-blue-50 rounded-xl hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:hover:bg-blue-500/20">
                        <span class="material-symbols-outlined text-[18px]">attach_file</span>
                        عرض المرفق / المستند
                    </a>
                </div>
            </template>
        </div>

        {{-- أزرار الإجراءات السفلية --}}
        <div class="flex gap-2 pt-4 mt-5 border-t border-gray-100 dark:border-gray-800">
            <a :href="'{{ url('finance/cash-ledger') }}/' + details.id + '/receipt'" 
               target="_blank"
               class="flex items-center justify-center flex-1 gap-1.5 py-2.5 text-xs font-bold text-white transition-colors shadow-sm rounded-xl bg-primary hover:bg-primary-hover">
                <span class="material-symbols-outlined text-[18px]">print</span>
                طباعة
            </a>

            <button type="button" 
                    @click="sendCashReceiptDirect(details.id, details.driver_phone, $event.currentTarget)"
                    class="flex items-center justify-center flex-1 gap-1.5 py-2.5 text-xs font-bold text-white transition-colors shadow-sm rounded-xl bg-emerald-600 hover:bg-emerald-700">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                واتساب
            </button>
            
            <button type="button" @click="showDetailsModal = false" 
                    class="px-4 py-2.5 text-xs font-bold text-gray-600 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                إغلاق
            </button>
        </div>
    </div>
</div>