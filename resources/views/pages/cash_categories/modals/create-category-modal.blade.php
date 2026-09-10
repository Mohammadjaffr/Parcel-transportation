<div x-data="{ openCreateModal: false }"
    @open-create-category-modal.window="openCreateModal = true">

    <div x-show="openCreateModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div @click.outside="openCreateModal = false"
            class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 w-full max-w-md shadow-2xl space-y-5">
            
            {{-- رأس المودال --}}
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[22px]">add_circle</span>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-gray-900 dark:text-white">إضافة فئة صندوق جديدة</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">حدد اسم الفئة ونوع السند المالي المرتبط بها</p>
                    </div>
                </div>
                <button type="button" @click="openCreateModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- فورم الإضافة --}}
            <form action="{{ route('cash-categories.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- حقل اسم الفئة --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        اسم الفئة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required maxlength="100" placeholder="مثال: إيجار فرع، محروقات، نقل طرود..."
                        class="w-full h-11 px-3.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>

                {{-- حقل نوع الحركة --}}
                <div x-data="{ selectedType: 'expense' }">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        نوع الحركة المالية <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold transition-all"
                            :class="selectedType === 'expense' ? 'bg-red-50 border-red-500 text-red-600 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'">
                            <input type="radio" name="type" value="expense" x-model="selectedType" class="hidden">
                            <span class="material-symbols-outlined text-[18px]">arrow_upward</span>
                            <span>صرف (مصروف)</span>
                        </label>
                        <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold transition-all"
                            :class="selectedType === 'income' ? 'bg-emerald-50 border-emerald-500 text-emerald-600 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'">
                            <input type="radio" name="type" value="income" x-model="selectedType" class="hidden">
                            <span class="material-symbols-outlined text-[18px]">arrow_downward</span>
                            <span>قبض (إيراد)</span>
                        </label>
                    </div>
                </div>

                {{-- حالة التفعيل --}}
                <div class="pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked
                            class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-700">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300">تفعيل هذه الفئة فوراً في سندات الصندوق</span>
                    </label>
                </div>

                {{-- الأزرار --}}
                <div class="flex items-center gap-2 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="openCreateModal = false"
                        class="flex-1 h-11 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit"
                        class="flex-1 h-11 rounded-xl text-xs font-extrabold text-white bg-primary hover:opacity-95 shadow-md shadow-primary/20 flex items-center justify-center gap-1.5 transition-all">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span>حفظ الفئة</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>