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
               class="flex items-center justify-center flex-1 gap-2 py-2.5 text-sm font-bold text-white transition-colors shadow-sm rounded-xl bg-primary hover:bg-primary-hover">
                <span class="material-symbols-outlined text-[18px]">print</span>
                طباعة السند
            </a>
            
            <button type="button" @click="showDetailsModal = false" 
                    class="px-6 py-2.5 text-sm font-bold text-gray-600 transition-colors bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                إغلاق
            </button>
        </div>
    </div>
</div>