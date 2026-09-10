<div x-show="editModalOpen" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div @click.outside="editModalOpen = false"
        class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-5">
        
        {{-- رأس المودال --}}
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-black text-gray-900 dark:text-white">تعديل فئة الصندوق</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">تعديل بيانات الفئة المختارة</p>
                </div>
            </div>
            <button type="button" @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- فورم التعديل --}}
        <form :action="'{{ url('cash-categories') }}/' + activeCategory.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- حقل اسم الفئة --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    اسم الفئة <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" x-model="activeCategory.name" required maxlength="100"
                    class="w-full h-11 px-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>

            {{-- بيان توضيحي لنوع الحركة --}}
            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">نوع الحركة المالي:</span>
                <span class="text-xs font-black"
                    :class="activeCategory.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'"
                    x-text="activeCategory.type === 'income' ? 'قبض (إيراد)' : 'صرف (مصروف)'"></span>
            </div>

            {{-- الأزرار --}}
            <div class="flex items-center gap-2 pt-4 border-t border-gray-100 dark:border-gray-800">
                <button type="button" @click="editModalOpen = false"
                    class="flex-1 h-11 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    إلغاء
                </button>
                <button type="submit"
                    class="flex-1 h-11 rounded-xl text-xs font-extrabold text-white bg-primary hover:opacity-95 shadow-md shadow-primary/20 flex items-center justify-center gap-1.5 transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>حفظ التعديلات</span>
                </button>
            </div>
        </form>
    </div>
</div>