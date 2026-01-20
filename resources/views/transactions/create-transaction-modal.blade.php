<div x-data="{
    isModalOpen: @if ($errors->any() || session('transaction_modal_open')) true @else false @endif,
    isLoading: false,
    selectedType: '{{ old('type', '') }}',
    selectedCategory: '{{ old('transaction_category_id', '') }}'
}">

    {{-- Open Modal Button --}}
    <button @click="isModalOpen = true"
        class="inline-flex gap-2 items-center px-5 h-11 text-sm font-semibold text-white rounded-xl transition-all duration-200 bg-brand-500 hover:bg-brand-600 focus:ring-4 focus:ring-brand-500/20">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        إضافة معاملة
    </button>

    {{-- Modal Backdrop & Container --}}
    <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 z-99999" style="display: none;">

        {{-- Backdrop --}}
        <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
            @click="isModalOpen = false"></div>

        {{-- Modal Content --}}
        <div @click.outside="isModalOpen = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative p-6 w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-2xl dark:bg-gray-900 dark:border-gray-800">

            {{-- Modal Header --}}
            <div class="flex gap-3 items-center pb-4 mb-5 border-b border-gray-100 dark:border-gray-800">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">إضافة معاملة جديدة</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">تسجيل حركة مالية في صندوق النقدية</p>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('transactions.store') }}" @submit="isLoading = true"
                enctype="multipart/form-data">
                @csrf

                <div class="space-y-5">

                    {{-- Type Selection --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            نوع المعاملة <span class="text-error-500">*</span>
                        </label>

                        <div class="flex gap-3">
                            {{-- Income Option --}}
                            <button type="button" @click="selectedType = 'in'; selectedCategory = ''"
                                :class="selectedType === 'in'
                                    ?
                                    'bg-success-50 border-success-500 hover:bg--500 dark:border-success-400' :
                                    'bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                                class="flex flex-1 gap-3 items-center p-3 rounded-xl border-2 transition-all duration-200 cursor-pointer">
                                <div :class="selectedType === 'in' ? 'bg-success-100 dark:bg-success-500/20' :
                                    'bg-gray-100 dark:bg-gray-700'"
                                    class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors">
                                    <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                    </svg>
                                </div>
                                <span
                                    :class="selectedType === 'in' ? 'text-success-600 dark:text-success-400' :
                                        'text-gray-600 dark:text-gray-300'"
                                    class="text-sm font-bold transition-colors">إيراد / دخل</span>
                            </button>

                            {{-- Expense Option --}}
                            <button type="button" @click="selectedType = 'out'; selectedCategory = ''"
                                :class="selectedType === 'out'
                                    ?
                                    'bg-error-50 border-error-500 dark:bg-error-500/15 dark:border-error-400' :
                                    'bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                                class="flex flex-1 gap-3 items-center p-3 rounded-xl border-2 transition-all duration-200 cursor-pointer">
                                <div :class="selectedType === 'out' ? 'bg-error-100 dark:bg-error-500/20' :
                                    'bg-gray-100 dark:bg-gray-700'"
                                    class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors">
                                    <svg class="w-5 h-5 text-error-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                    </svg>
                                </div>
                                <span
                                    :class="selectedType === 'out' ? 'text-error-600 dark:text-error-400' :
                                        'text-gray-600 dark:text-gray-300'"
                                    class="text-sm font-bold transition-colors">مصروف / خروج</span>
                            </button>
                        </div>
                    </div>

                    {{-- Category Selection --}}
                    <div x-show="selectedType" x-transition>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            الفئة <span class="text-error-500">*</span>
                        </label>
                        <select name="transaction_category_id" x-model="selectedCategory"
                            class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white"
                            required>
                            <option value="">-- اختر الفئة --</option>
                            @foreach ($categories as $category)
                                <template x-if="selectedType === '{{ $category->type }}'">
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                </template>
                            @endforeach
                        </select>
                        @error('transaction_category_id')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            المبلغ <span class="text-error-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="amount"
                                class="pr-4 pl-14 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                                step="0.01" min="0.01" placeholder="0.00" value="{{ old('amount') }}" required>
                            <span
                                class="absolute left-4 top-1/2 text-xs font-bold -translate-y-1/2 text-brand-500">ر.ي</span>
                        </div>
                        @error('amount')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Reference Number --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            رقم السند/المرجع <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <input type="text" name="reference_number"
                            class="px-4 w-full h-11 text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                            placeholder="أدخل رقم السند أو المرجع" value="{{ old('reference_number') }}">
                        @error('reference_number')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Attachment --}}
                    <div x-data="{
                        fileName: '',
                        filePreview: null,
                        isDragging: false,
                        handleFile(file) {
                            if (file && file.type.startsWith('image/')) {
                                this.fileName = file.name;
                                const reader = new FileReader();
                                reader.onload = (e) => this.filePreview = e.target.result;
                                reader.readAsDataURL(file);
                            }
                        },
                        removeFile() {
                            this.fileName = '';
                            this.filePreview = null;
                            this.$refs.fileInput.value = '';
                        }
                    }">
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            صورة المرفق <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>

                        {{-- Upload Area --}}
                        <div x-show="!filePreview" @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="isDragging = false; handleFile($event.dataTransfer.files[0])"
                            :class="isDragging ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' :
                                'border-gray-200 dark:border-gray-700 hover:border-brand-300 dark:hover:border-brand-500/50'"
                            class="flex relative flex-col justify-center items-center p-5 bg-gray-50 rounded-xl border-2 border-dashed transition-all cursor-pointer dark:bg-gray-800/50 group">

                            <input type="file" name="attachment" accept="image/*" x-ref="fileInput"
                                @change="handleFile($event.target.files[0])"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer hover:border-brand-500 focus:border-brand-500">

                            <div
                                class="flex justify-center items-center mb-3 w-12 h-12 rounded-xl transition-all bg-brand-50 dark:bg-brand-500/10 group-hover:scale-110">
                                <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <span class="text-brand-500">اختر صورة</span> أو اسحب وأفلت هنا
                            </p>
                            <p class="mt-1 text-xs text-gray-400">PNG, JPG, JPEG • حد أقصى 2MB</p>
                        </div>

                        {{-- Preview Area --}}
                        <div x-show="filePreview" x-transition
                            class="overflow-hidden relative rounded-xl border-2 border-success-200 dark:border-success-500/30 bg-success-50 dark:bg-success-500/10">
                            <img :src="filePreview" alt="Preview" class="object-cover w-full h-32">
                            <div
                                class="flex justify-between items-center p-3 backdrop-blur-sm bg-white/90 dark:bg-gray-900/90">
                                <div class="flex gap-2 items-center">
                                    <div
                                        class="flex justify-center items-center w-8 h-8 rounded-lg bg-success-100 dark:bg-success-500/20">
                                        <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-xs font-semibold text-gray-700 dark:text-gray-300 truncate max-w-[150px]"
                                        x-text="fileName"></span>
                                </div>
                                <button type="button" @click="removeFile()"
                                    class="flex justify-center items-center w-8 h-8 rounded-lg transition-all text-error-500 bg-error-50 dark:bg-error-500/10 hover:bg-error-100 dark:hover:bg-error-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        @error('attachment')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                            الوصف <span class="text-xs font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <textarea name="description"
                            class="px-4 py-3 w-full text-sm bg-gray-50 rounded-xl border border-gray-200 transition-all resize-none dark:bg-gray-800 dark:border-gray-700 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:text-white placeholder:text-gray-400"
                            rows="2" placeholder="أضف ملاحظات إن وجدت...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Form Actions --}}
                <div class="flex gap-3 pt-4 mt-6 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" @click="isModalOpen = false"
                        class="flex flex-1 gap-2 justify-center items-center h-11 text-sm font-semibold text-gray-600 bg-gray-100 rounded-xl transition-all dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        إلغاء
                    </button>
                    <button type="submit" :disabled="isLoading || !selectedType || !selectedCategory"
                        class="inline-flex flex-1 gap-2 justify-center items-center h-11 text-sm font-semibold text-white rounded-xl shadow-lg transition-all duration-200 bg-brand-500 hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-brand-500/20">
                        {{-- Loading Spinner --}}
                        <svg x-show="isLoading" class="w-4 h-4 text-white animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-text="isLoading ? 'جاري الحفظ...' : 'حفظ المعاملة'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
