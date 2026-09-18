{{-- مودال تسجيل سند قبض / سند صرف جديد (Bottom Sheet Style) --}}
<div x-show="showCreateModal" x-cloak 
    class="fixed inset-0 z-[99999] flex items-end sm:items-center justify-center bg-slate-900/50 backdrop-blur-sm sm:p-4 transition-opacity"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">

    <div class="w-full sm:max-w-lg bg-white rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl relative flex flex-col max-h-[92vh] overflow-hidden"
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

        {{-- الرأس --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 relative">
            <div class="absolute inset-x-0 bottom-0 h-0.5" :class="createType === 'income' ? 'bg-emerald-500' : 'bg-rose-500'"></div>
            <h3 class="text-lg font-black text-slate-800 font-headline flex items-center gap-2">
                <span class="material-symbols-outlined" :class="createType === 'income' ? 'text-emerald-500' : 'text-rose-500'" x-text="createType === 'income' ? 'south_west' : 'north_east'"></span>
                <span x-text="createType === 'income' ? 'تسجيل سند قبض (وارد)' : 'تسجيل سند صرف (منصرف)'"></span>
            </h3>
            <button @click="showCreateModal = false" type="button" class="flex justify-center items-center w-8 h-8 rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- المحتوى القابل للتمرير --}}
        <div class="overflow-y-auto px-6 py-5 custom-scrollbar">
            <form method="POST" action="{{ route('cash.ledger.store') }}" enctype="multipart/form-data" class="space-y-5 pb-6">
                @csrf
                <input type="hidden" name="type" :value="createType">

                {{-- اختيار الفرع للمدير فقط --}}
                @if (auth()->user()->type === 'admin' && isset($branches) && $branches->isNotEmpty())
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-slate-600">الفرع المسجل عليه الحركة *</label>
                        <select name="branch_id" class="w-full h-14 text-sm bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-slate-800 px-4 font-bold">
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ auth()->user()->branch_id == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- التصنيف المالي الديناميكي --}}
                <div class="relative" @click.outside="categoryDropdownOpen = false">
                    <label class="block mb-1.5 text-xs font-bold text-slate-600">التصنيف المالي للعملية *</label>
                    <select name="cash_category_id" x-model="selectedCategoryId" required class="absolute inset-0 w-full h-full opacity-0 pointer-events-none -z-10" tabindex="-1">
                        <option value="">اختر التصنيف</option>
                        <template x-for="cat in availableCategories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>

                    <button type="button" @click="toggleCategoryDropdown()"
                        class="flex items-center justify-between w-full h-14 px-4 bg-slate-50 border-none rounded-2xl transition-all focus:ring-2 focus:ring-primary/20 text-right"
                        :class="selectedCategoryId ? 'text-slate-800 font-bold' : 'text-slate-400'">
                        <div class="flex items-center gap-2 truncate">
                            <span class="material-symbols-outlined text-[20px]" :class="selectedCategoryId ? 'text-primary' : 'text-slate-400'">category</span>
                            <span x-text="selectedCategoryName ? selectedCategoryName : 'اختر التصنيف المالي...'"></span>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <template x-if="selectedCategoryId">
                                <span @click.stop="clearCategory()" title="إلغاء التحديد" class="flex items-center justify-center w-6 h-6 text-slate-400 transition-colors rounded-full hover:bg-slate-200">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </span>
                            </template>
                            <span class="material-symbols-outlined text-[20px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': categoryDropdownOpen }">expand_more</span>
                        </div>
                    </button>

                    {{-- القائمة المنبثقة --}}
                    <div x-show="categoryDropdownOpen" x-cloak x-transition.opacity
                        class="absolute z-50 w-full mt-2 overflow-hidden bg-white border border-slate-100 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.15)] rounded-2xl">
                        <div class="p-2 border-b border-slate-50 bg-slate-50/50">
                            <div class="relative">
                                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[18px]">search</span>
                                </span>
                                <input type="text" x-ref="categorySearchInput" x-model="categorySearchText"
                                    @keydown.escape="categoryDropdownOpen = false"
                                    @keydown.enter.prevent="if (filteredCategories.length > 0) selectCategory(filteredCategories[0])"
                                    placeholder="ابحث..."
                                    class="w-full h-10 pr-10 pl-3 text-sm bg-white border border-slate-200 rounded-xl outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                            </div>
                        </div>
                        <ul class="overflow-y-auto max-h-48 p-1 space-y-1 custom-scrollbar text-sm">
                            <template x-for="cat in filteredCategories" :key="cat.id">
                                <li>
                                    <button type="button" @click="selectCategory(cat)"
                                        class="flex items-center justify-between w-full px-4 py-2.5 text-xs font-bold rounded-xl transition-colors text-right"
                                        :class="selectedCategoryId === cat.id ? 'bg-primary/10 text-primary' : 'text-slate-700 hover:bg-slate-50'">
                                        <span x-text="cat.name"></span>
                                        <span x-show="selectedCategoryId === cat.id" class="material-symbols-outlined text-[18px]">check</span>
                                    </button>
                                </li>
                            </template>
                            <li x-show="filteredCategories.length === 0" class="p-4 text-xs font-bold text-center text-slate-400">لا توجد تصنيفات مطابقة</li>
                        </ul>
                    </div>
                </div>

                {{-- المبلغ وطريقة الدفع --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-slate-600">المبلغ *</label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00"
                                class="w-full h-14 text-lg font-black font-headline border-none rounded-2xl bg-slate-50 focus:ring-2 focus:ring-primary/20 text-slate-800 text-left pl-10 pr-4" dir="ltr">
                            <span class="absolute text-xs font-bold text-slate-400 left-4 top-1/2 -translate-y-1/2">ر.ي</span>
                        </div>
                    </div>
                    <div>
                        <label class="block mb-1.5 text-xs font-bold text-slate-600">طريقة الدفع *</label>
                        <select name="payment_method" required class="w-full h-14 text-sm font-bold border-none rounded-2xl bg-slate-50 focus:ring-2 focus:ring-primary/20 text-slate-800 px-4">
                            <option value="cash">نقداً (كاش)</option>
                            <option value="bank_transfer">حوالة بنكية</option>
                        </select>
                    </div>
                </div>

                {{-- التاريخ والبيان --}}
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-slate-600">تاريخ المعاملة *</label>
                    <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required
                        class="w-full h-14 text-sm font-bold border-none rounded-2xl bg-slate-50 focus:ring-2 focus:ring-primary/20 text-slate-800 px-4">
                </div>

                <div>
                    <label class="block mb-1.5 text-xs font-bold text-slate-600">البيان / الملاحظات</label>
                    <textarea name="notes" rows="2" placeholder="تفاصيل إضافية عن العملية..."
                        class="w-full text-sm font-bold border-none rounded-2xl bg-slate-50 focus:ring-2 focus:ring-primary/20 text-slate-800 p-4 resize-none"></textarea>
                </div>

                {{-- أزرار التحكم --}}
                <div class="pt-2 flex gap-3">
                    <button type="submit" class="flex-1 h-14 text-white rounded-2xl font-black text-sm shadow-[0_8px_20px_rgb(0,0,0,0.15)] active:scale-95 transition-all"
                        :class="createType === 'income' ? 'bg-emerald-500 shadow-emerald-500/30' : 'bg-rose-500 shadow-rose-500/30'">
                        تأكيد وحفظ
                    </button>
                    <button type="button" @click="showCreateModal = false" class="px-8 h-14 text-slate-600 bg-slate-100 rounded-2xl font-bold text-sm active:scale-95 transition-all">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
