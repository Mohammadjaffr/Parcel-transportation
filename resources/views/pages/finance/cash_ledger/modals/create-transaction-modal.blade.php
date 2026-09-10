{{-- مودال تسجيل سند قبض / سند صرف جديد --}}
<!-- تم إضافة: bg-gray-900/50 و backdrop-blur-sm وإزالة pointer-events-none -->
<div x-show="showCreateModal" x-cloak
    class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-gray-900/50 backdrop-blur-sm transition-opacity">

    <div class="w-full max-w-lg p-6 bg-white border border-gray-100 shadow-2xl rounded-2xl dark:bg-gray-900 dark:border-gray-800"
        @click.outside="showCreateModal = false">

        <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-bold text-gray-900 dark:text-white"
                x-text="createType === 'income' ? 'تسجيل سند قبض جديد (وارد للصندوق)' : 'تسجيل سند صرف جديد (منصرف من الصندوق)'">
            </h3>
            <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>

        <form method="POST" action="{{ route('cash.ledger.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="type" :value="createType">

            {{-- اختيار الفرع للمدير فقط في حال الإيداع لفرع معين --}}
            @if (auth()->user()->type === 'admin' && $branches->isNotEmpty())
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-600 dark:text-gray-300">الفرع المسجل عليه
                        الحركة *</label>
                    <select name="branch_id"
                        class="w-full text-sm border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ auth()->user()->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- التصنيف المالي الديناميكي المتوافق مع نوع الحركة (قابل للبحث والفلترة) --}}
            <div class="relative" @click.outside="categoryDropdownOpen = false">
                <label class="block mb-1 text-xs font-bold text-gray-600 dark:text-gray-300">
                    التصنيف المالي للعملية *
                </label>

                {{-- الحقل الفعلي المرتبط بالفورم والتحقق من الصحة --}}
                <select name="cash_category_id" x-model="selectedCategoryId" required
                    class="absolute inset-0 w-full h-full opacity-0 pointer-events-none -z-10" tabindex="-1">
                    <option value="">اختر التصنيف</option>
                    <template x-for="cat in availableCategories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name"></option>
                    </template>
                </select>

                {{-- زر اختيار التصنيف وفتح القائمة --}}
                <button type="button" @click="toggleCategoryDropdown()"
                    class="flex items-center justify-between w-full px-4 py-2.5 text-sm border rounded-xl bg-gray-50 dark:bg-gray-800 transition-all duration-150 text-right focus:outline-none focus:ring-2 focus:ring-primary/20"
                    :class="selectedCategoryId
                        ?
                        'border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white font-bold' :
                        'border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500'">

                    <div class="flex items-center gap-2 truncate">
                        <span class="material-symbols-outlined text-[18px]"
                            :class="selectedCategoryId ? 'text-primary' : 'text-gray-400'">
                            category
                        </span>
                        <span x-text="selectedCategoryName ? selectedCategoryName : 'اختر التصنيف المالي...'"></span>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <template x-if="selectedCategoryId">
                            <span @click.stop="clearCategory()" title="إلغاء التحديد"
                                class="flex items-center justify-center w-5 h-5 text-gray-400 transition-colors rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200">
                                <span class="material-symbols-outlined text-[14px]">close</span>
                            </span>
                        </template>
                        <span
                            class="material-symbols-outlined text-[18px] text-gray-400 transition-transform duration-200"
                            :class="{ 'rotate-180': categoryDropdownOpen }">
                            expand_more
                        </span>
                    </div>
                </button>

                {{-- القائمة المنبثقة للبحث والاختيار --}}
                <div x-show="categoryDropdownOpen" x-cloak x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute z-50 w-full mt-1 overflow-hidden bg-white border border-gray-200 shadow-xl rounded-xl dark:bg-gray-900 dark:border-gray-700">

                    {{-- حقل البحث المباشر --}}
                    <div class="p-2 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="relative">
                            <span
                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <span class="material-symbols-outlined text-[18px]">search</span>
                            </span>
                            <input type="text" x-ref="categorySearchInput" x-model="categorySearchText"
                                @keydown.escape="categoryDropdownOpen = false"
                                @keydown.enter.prevent="if (filteredCategories.length > 0) selectCategory(filteredCategories[0])"
                                placeholder="اكتب للبحث في التصنيفات..."
                                class="w-full py-1.5 pr-9 pl-3 text-xs bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary">
                        </div>
                    </div>

                    {{-- قائمة التصنيفات المفلترة --}}
                    <ul
                        class="overflow-y-auto max-h-48 p-1 space-y-0.5 custom-scrollbar text-sm divide-y divide-gray-50 dark:divide-gray-800/40">
                        <template x-for="cat in filteredCategories" :key="cat.id">
                            <li>
                                <button type="button" @click="selectCategory(cat)"
                                    class="flex items-center justify-between w-full px-3 py-2 text-xs font-semibold rounded-lg transition-colors text-right"
                                    :class="selectedCategoryId === cat.id ?
                                        'bg-primary/10 text-primary dark:bg-primary/20 dark:text-white font-bold' :
                                        'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'">
                                    <span x-text="cat.name"></span>
                                    <span x-show="selectedCategoryId === cat.id"
                                        class="material-symbols-outlined text-[16px] text-primary">
                                        check
                                    </span>
                                </button>
                            </li>
                        </template>

                        {{-- لا توجد نتائج مطابقة --}}
                        <li x-show="filteredCategories.length === 0"
                            class="p-3 text-xs font-bold text-center text-gray-400">
                            لا توجد تصنيفات مطابقة للبحث
                        </li>
                    </ul>
                </div>
            </div>

            {{-- المبلغ وطريقة الدفع --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-600 dark:text-gray-300">المبلغ الإجمالي
                        *</label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00"
                            class="w-full text-sm font-bold border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <span class="absolute text-xs font-bold text-gray-400 left-3 top-2.5">ر.ي</span>
                    </div>
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-600 dark:text-gray-300">طريقة الاستلام / الصرف
                        *</label>
                    <select name="payment_method" required
                        class="w-full text-sm border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        <option value="cash">نقداً (كاش الخزينة)</option>
                        <option value="bank_transfer">تحويل بنكي / حوالة</option>
                    </select>
                </div>
            </div>

            {{-- التاريخ ورقم المرجع --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-600 dark:text-gray-300">تاريخ المعاملة
                        *</label>
                    <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required
                        class="w-full text-sm border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-600 dark:text-gray-300">رقم المرجع / الإيداع
                        (اختياري)</label>
                    <input type="text" name="reference_number" placeholder="مثلاً: 98741"
                        class="w-full text-sm border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                </div>
            </div>

            {{-- المرفق --}}
            <div x-data="{ fileName: '' }">
                <label class="block mb-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                    إرفاق مستند أو صورة السند (اختياري)
                </label>

                <div class="relative w-full">
                    <label for="file-upload"
                        class="flex flex-col items-center justify-center w-full py-5 transition-all border-2 border-gray-200 border-dashed cursor-pointer rounded-xl bg-gray-50 dark:bg-gray-800/50 dark:border-gray-700 hover:bg-gray-100 hover:border-primary/50 dark:hover:bg-gray-800 dark:hover:border-primary/50 group">

                        {{-- الحالة قبل اختيار الملف --}}
                        <div class="flex flex-col items-center justify-center" x-show="!fileName">
                            <span
                                class="material-symbols-outlined text-3xl text-gray-400 mb-1.5 transition-colors group-hover:text-primary">
                                cloud_upload
                            </span>
                            <p class="mb-1 text-xs font-bold text-gray-600 dark:text-gray-400">
                                اضغط لاختيار مستند أو قم بسحبه هنا
                            </p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">
                                الصيغ المدعومة: PDF, JPG, PNG
                            </p>
                        </div>

                        {{-- الحالة بعد اختيار الملف (يعرض اسم الملف) --}}
                        <div class="flex flex-col items-center justify-center" x-show="fileName" x-cloak>
                            <div
                                class="flex items-center justify-center w-10 h-10 mb-2 bg-emerald-100 rounded-full dark:bg-emerald-500/20">
                                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">
                                    description
                                </span>
                            </div>
                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 truncate max-w-[200px]"
                                x-text="fileName"></p>
                        </div>

                        {{-- حقل الإدخال المخفي --}}
                        <input id="file-upload" type="file" name="attachment" accept="image/*,application/pdf"
                            class="hidden" x-ref="fileInput"
                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                    </label>
                </div>

                {{-- زر الإزالة يظهر فقط عند وجود ملف --}}
                <div class="flex justify-end mt-1.5" x-show="fileName" x-cloak>
                    <button type="button" @click="fileName = ''; $refs.fileInput.value = ''"
                        class="flex items-center gap-1 text-[10px] font-bold text-rose-500 hover:text-rose-600 transition-colors">
                        <span class="material-symbols-outlined text-[14px]">delete</span>
                        إلغاء المرفق
                    </button>
                </div>
            </div>

            {{-- البيان والملاحظات --}}
            <div>
                <label class="block mb-1 text-xs font-bold text-gray-600 dark:text-gray-300">البيان / تفاصيل
                    السند</label>
                <textarea name="notes" rows="2" placeholder="اكتب وصفاً مختصراً للعملية..."
                    class="w-full text-sm border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-white"></textarea>
            </div>

            {{-- أزرار التحكم في الحفظ --}}
            <div class="flex gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                <button type="submit"
                    class="flex-1 py-2.5 text-sm font-bold text-white rounded-xl shadow-sm transition-colors"
                    :class="createType === 'income' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'">
                    تأكيد وحفظ السند
                </button>
                <button type="button" @click="showCreateModal = false"
                    class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>
