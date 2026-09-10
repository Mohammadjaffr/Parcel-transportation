<div x-show="editModalOpen" x-cloak
    @keydown.escape.window="editModalOpen = false"
    class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm transition-opacity"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div @click.outside="editModalOpen = false"
        class="w-full max-w-md p-6 space-y-5 bg-white border border-gray-100 shadow-2xl dark:bg-gray-900 dark:border-gray-800 rounded-2xl">
        
        {{-- رأس المودال --}}
        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2.5">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-primary-container text-primary-hover dark:bg-primary/20 dark:text-primary">
                    <span class="material-symbols-outlined text-[22px]">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-black text-gray-900 dark:text-white">تعديل فئة الصندوق</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">تعديل بيانات الفئة المختارة</p>
                </div>
            </div>
            <button type="button" @click="editModalOpen = false" class="text-gray-400 transition-colors hover:text-rose-500">
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
                    اسم الفئة <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" x-model="activeCategory.name" required maxlength="100"
                    class="w-full h-11 px-3.5 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                
                @error('name')
                    <p class="mt-1.5 text-[11px] font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- حقل نوع الحركة (قابل للتعديل الآن) --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    نوع الحركة المالية <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center justify-center gap-2 p-3 text-xs font-bold transition-all border cursor-pointer rounded-xl"
                        :class="activeCategory.type === 'expense' ? 'bg-rose-50 border-rose-500 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'">
                        <input type="radio" name="type" value="expense" x-model="activeCategory.type" class="hidden">
                        <span class="material-symbols-outlined text-[18px]">arrow_upward</span>
                        <span>صرف (مصروف)</span>
                    </label>
                    
                    <label class="flex items-center justify-center gap-2 p-3 text-xs font-bold transition-all border cursor-pointer rounded-xl"
                        :class="activeCategory.type === 'income' ? 'bg-emerald-50 border-emerald-500 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'">
                        <input type="radio" name="type" value="income" x-model="activeCategory.type" class="hidden">
                        <span class="material-symbols-outlined text-[18px]">arrow_downward</span>
                        <span>قبض (إيراد)</span>
                    </label>
                </div>

                @error('type')
                    <p class="mt-1.5 text-[11px] font-bold text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- الأزرار --}}
            <div class="flex items-center gap-2 pt-4 border-t border-gray-100 dark:border-gray-800">
                <button type="submit"
                    class="flex items-center justify-center flex-1 gap-1.5 h-11 rounded-xl text-xs font-black text-white bg-primary hover:bg-primary-hover shadow-sm transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>حفظ التعديلات</span>
                </button>
                
                <button type="button" @click="editModalOpen = false"
                    class="px-6 h-11 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>