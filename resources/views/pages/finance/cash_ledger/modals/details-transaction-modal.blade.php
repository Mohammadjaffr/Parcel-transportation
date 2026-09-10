{{-- مودال عرض تفاصيل السند بالكامل --}}
<div x-show="showDetailsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-black/50 backdrop-blur-sm">
    <div class="w-full max-w-md p-6 bg-white border border-gray-100 shadow-2xl rounded-2xl dark:bg-gray-900 dark:border-gray-800" @click.outside="showDetailsModal = false">
        
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white">
                بيانات السند: <span class="font-mono text-brand-500" x-text="details.receipt_number"></span>
            </h3>
            <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <div class="space-y-2.5 text-sm">
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-gray-400">نوع السند:</span>
                <span class="font-bold" :class="details.type === 'income' ? 'text-emerald-600' : 'text-rose-600'" x-text="details.type_text"></span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-gray-400">المبلغ:</span>
                <span class="text-lg font-black text-gray-900 dark:text-white"><span x-text="details.amount"></span> ر.ي</span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-gray-400">التصنيف:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.category"></span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-gray-400">طريقة الدفع:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.payment_method"></span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-gray-400">الفرع:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.branch"></span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-gray-400">المسؤول عن الحركة:</span>
                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="details.user"></span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-gray-400">تاريخ الحركة:</span>
                <span class="text-gray-700 dark:text-gray-200" x-text="details.transaction_date"></span>
            </div>
            <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800/50">
                <span class="text-gray-400">رقم المرجع:</span>
                <span class="font-mono text-gray-700 dark:text-gray-200" x-text="details.reference_number"></span>
            </div>
            <div class="py-1">
                <span class="block mb-1 text-gray-400">البيان والملاحظات:</span>
                <p class="p-3 text-xs bg-gray-50 rounded-xl text-gray-700 dark:bg-gray-800 dark:text-gray-300" x-text="details.notes"></p>
            </div>

            <template x-if="details.attachment_url">
                <div class="pt-2">
                    <a :href="details.attachment_url" target="_blank" class="block w-full py-2 text-xs font-bold text-center text-blue-600 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400">
                        عرض المرفق / سند التحويل 📎
                    </a>
                </div>
            </template>
        </div>

        <div class="pt-4 mt-5 border-t border-gray-100 dark:border-gray-800">
            <button type="button" @click="showDetailsModal = false" class="w-full py-2 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                إغلاق
            </button>
        </div>
    </div>
</div>
