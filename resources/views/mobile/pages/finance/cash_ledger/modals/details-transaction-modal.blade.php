{{-- مودال عرض تفاصيل السند (Bottom Sheet) --}}
<div x-show="showDetailsModal" x-cloak 
    class="fixed inset-0 z-[99999] flex items-end sm:items-center justify-center bg-slate-900/50 backdrop-blur-sm sm:p-4 transition-opacity"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div class="w-full sm:max-w-md bg-white rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl relative flex flex-col max-h-[92vh] overflow-hidden"
        @click.outside="showDetailsModal = false"
        x-transition:enter="ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full sm:translate-y-4 sm:scale-95"
        x-transition:enter-end="translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 sm:scale-100"
        x-transition:leave-end="translate-y-full sm:translate-y-4 sm:scale-95">

        {{-- مقبض السحب للموبايل (Swipe Indicator) --}}
        <div class="flex justify-center w-full pt-3 pb-2 sm:hidden cursor-pointer" @click="showDetailsModal = false">
            <div class="w-12 h-1.5 bg-slate-200 rounded-full"></div>
        </div>

        {{-- رأس المودال --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 relative">
            <div class="absolute inset-x-0 bottom-0 h-0.5" :class="details.type === 'income' ? 'bg-emerald-500' : 'bg-rose-500'"></div>
            <h3 class="flex items-center gap-2 text-lg font-black text-slate-800 font-headline">
                تفاصيل السند
            </h3>
            <button @click="showDetailsModal = false" type="button" class="flex justify-center items-center w-8 h-8 rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- المحتوى القابل للتمرير (تفاصيل كأنها فاتورة) --}}
        <div class="overflow-y-auto px-6 py-5 custom-scrollbar">
            
            {{-- الرقم والمبلغ --}}
            <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-[1.5rem] mb-6">
                <span class="px-3 py-1 mb-3 text-xs font-mono font-black bg-white shadow-sm rounded-lg text-slate-600 border border-slate-100" x-text="details.receipt_number"></span>
                <span class="text-3xl font-black font-headline tracking-tight" :class="details.type === 'income' ? 'text-emerald-500' : 'text-rose-500'">
                    <span x-text="details.amount"></span> <span class="text-sm font-bold text-slate-400">ر.ي</span>
                </span>
                <span class="mt-2 text-xs font-bold px-3 py-1 rounded-full" 
                      :class="details.type === 'income' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'" 
                      x-text="details.type_text"></span>
            </div>

            {{-- قائمة التفاصيل --}}
            <div class="space-y-4 text-sm px-2">
                <div class="flex justify-between items-center border-b border-dashed border-slate-200 pb-3">
                    <div class="flex items-center gap-2 text-slate-400 font-bold">
                        <span class="material-symbols-outlined text-[16px]">category</span>
                        <span class="text-xs">التصنيف</span>
                    </div>
                    <span class="font-black text-slate-700" x-text="details.category"></span>
                </div>
                
                <div class="flex justify-between items-center border-b border-dashed border-slate-200 pb-3">
                    <div class="flex items-center gap-2 text-slate-400 font-bold">
                        <span class="material-symbols-outlined text-[16px]">payments</span>
                        <span class="text-xs">طريقة الدفع</span>
                    </div>
                    <span class="font-black text-slate-700" x-text="details.payment_method"></span>
                </div>
                
                <div class="flex justify-between items-center border-b border-dashed border-slate-200 pb-3">
                    <div class="flex items-center gap-2 text-slate-400 font-bold">
                        <span class="material-symbols-outlined text-[16px]">store</span>
                        <span class="text-xs">الفرع</span>
                    </div>
                    <span class="font-black text-slate-700" x-text="details.branch"></span>
                </div>
                
                <div class="flex justify-between items-center border-b border-dashed border-slate-200 pb-3">
                    <div class="flex items-center gap-2 text-slate-400 font-bold">
                        <span class="material-symbols-outlined text-[16px]">person</span>
                        <span class="text-xs">المسؤول</span>
                    </div>
                    <span class="font-black text-slate-700" x-text="details.user"></span>
                </div>
                
                <div class="flex justify-between items-center border-b border-dashed border-slate-200 pb-3">
                    <div class="flex items-center gap-2 text-slate-400 font-bold">
                        <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                        <span class="text-xs">تاريخ الحركة</span>
                    </div>
                    <span class="font-black text-slate-700" x-text="details.transaction_date"></span>
                </div>
                
                <div class="flex justify-between items-center border-b border-dashed border-slate-200 pb-3" x-show="details.reference_number">
                    <div class="flex items-center gap-2 text-slate-400 font-bold">
                        <span class="material-symbols-outlined text-[16px]">tag</span>
                        <span class="text-xs">رقم المرجع</span>
                    </div>
                    <span class="font-mono font-black text-slate-700" x-text="details.reference_number"></span>
                </div>
                
                <div class="pt-2">
                    <div class="flex items-center gap-2 text-slate-400 font-bold mb-2">
                        <span class="material-symbols-outlined text-[16px]">subject</span>
                        <span class="text-xs">البيان والملاحظات</span>
                    </div>
                    <p class="p-4 text-xs font-bold leading-relaxed bg-slate-50 rounded-2xl text-slate-700" 
                       x-text="details.notes || 'لا توجد ملاحظات مسجلة.'"></p>
                </div>

                {{-- المرفقات إن وجدت --}}
                <template x-if="details.attachment_url">
                    <div class="pt-4">
                        <a :href="details.attachment_url" target="_blank" 
                           class="flex items-center justify-center w-full gap-2 h-12 text-xs font-bold text-blue-600 transition-colors bg-blue-50 rounded-2xl active:scale-95">
                            <span class="material-symbols-outlined text-[18px]">attach_file</span>
                            عرض المرفق / المستند
                        </a>
                    </div>
                </template>
            </div>

            {{-- أزرار الإجراءات السفلية --}}
            <div class="flex gap-3 pt-6 mt-6 border-t border-slate-100">
                <a :href="'{{ url('finance/cash-ledger') }}/' + details.id + '/receipt'" 
                   target="_blank"
                   class="flex items-center justify-center flex-1 gap-2 h-14 text-sm font-black text-white transition-all shadow-[0_8px_20px_rgb(0,0,0,0.15)] rounded-2xl bg-slate-800 active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    طباعة السند
                </a>
                
                <button type="button" @click="showDetailsModal = false" 
                        class="px-8 h-14 text-sm font-bold text-slate-600 transition-all bg-slate-100 rounded-2xl active:scale-95">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>