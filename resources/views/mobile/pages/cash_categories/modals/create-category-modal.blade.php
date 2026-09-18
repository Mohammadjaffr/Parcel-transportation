<div x-data="{ showCreateModal: false }"
     @open-create-category-modal.window="showCreateModal = true"
     x-show="showCreateModal" 
     x-cloak 
    class="fixed inset-0 z-[99999] flex items-end sm:items-center justify-center bg-slate-900/50 backdrop-blur-sm sm:p-4 transition-opacity"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div class="w-full sm:max-w-md bg-white rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl relative flex flex-col overflow-hidden"
        @click.outside="showCreateModal = false"
        x-transition:enter="ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full sm:translate-y-4 sm:scale-95"
        x-transition:enter-end="translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 sm:scale-100"
        x-transition:leave-end="translate-y-full sm:translate-y-4 sm:scale-95">

        {{-- مقبض السحب للموبايل (Swipe Indicator) --}}
        <div class="flex justify-center w-full pt-3 pb-2 sm:hidden cursor-pointer" @click="showCreateModal = false">
            <div class="w-12 h-1.5 bg-slate-200 rounded-full"></div>
        </div>

        {{-- رأس المودال --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 relative">
            <h3 class="flex items-center gap-2 text-lg font-black text-slate-800 font-headline">
                إضافة فئة جديدة
            </h3>
            <button @click="showCreateModal = false" type="button" class="flex justify-center items-center w-8 h-8 rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- محتوى الفورم --}}
        <div class="px-6 py-5">
            <form action="{{ route('cash-categories.store') }}" method="POST" class="space-y-5">
                @csrf
                
                {{-- اسم الفئة --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-slate-600">اسم الفئة *</label>
                    <input type="text" name="name" required placeholder="مثال: إيجار المحل، مبيعات مباشرة..."
                        class="w-full h-14 text-sm font-bold border-none rounded-2xl bg-slate-50 focus:ring-2 focus:ring-primary/20 text-slate-800 px-4">
                </div>

                {{-- نوع الحركة --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-slate-600">نوع الحركة *</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex items-center justify-center h-14 rounded-2xl border-2 border-transparent bg-slate-50 cursor-pointer transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 text-slate-600 has-[:checked]:text-emerald-700 font-bold text-sm">
                            <input type="radio" name="type" value="income" class="sr-only" required>
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">south_west</span>
                                قبض (إيراد)
                            </span>
                        </label>
                        
                        <label class="relative flex items-center justify-center h-14 rounded-2xl border-2 border-transparent bg-slate-50 cursor-pointer transition-all has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 text-slate-600 has-[:checked]:text-rose-700 font-bold text-sm">
                            <input type="radio" name="type" value="expense" class="sr-only" required>
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">north_east</span>
                                صرف (مصروف)
                            </span>
                        </label>
                    </div>
                </div>

                {{-- حالة التفعيل --}}
                <div class="flex items-center gap-3 py-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:-translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                    <span class="text-sm font-bold text-slate-700">تفعيل الفئة تلقائياً</span>
                </div>

                {{-- أزرار التحكم --}}
                <div class="pt-4 flex gap-3">
                    <button type="submit" class="flex-1 h-14 text-white rounded-2xl font-black text-sm shadow-[0_8px_20px_rgb(0,0,0,0.15)] active:scale-95 transition-all bg-primary shadow-primary/30">
                        حفظ الفئة
                    </button>
                    <button type="button" @click="showCreateModal = false" class="px-8 h-14 text-slate-600 bg-slate-100 rounded-2xl font-bold text-sm active:scale-95 transition-all">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>