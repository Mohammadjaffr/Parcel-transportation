{{-- Edit Category Modal --}}
<div x-show="editModalOpen" x-cloak @keydown.escape.window="closeEditModal()"
    class="flex fixed inset-0 z-50 justify-center items-center px-4" style="display: none;">
    {{-- Overlay --}}
    <div class="fixed inset-0 backdrop-blur-sm transition-opacity bg-gray-900/50 dark:bg-gray-900/80"
        @click="closeEditModal()"></div>

    {{-- Modal Content --}}
    <div class="relative w-full max-w-md bg-white rounded-2xl border border-gray-100 shadow-2xl dark:bg-gray-900 dark:border-gray-800"
        @click.away="closeEditModal()">
        {{-- Header --}}
        <div class="flex gap-4 justify-between items-center p-6 border-b border-gray-100 dark:border-gray-800">
            <div class="flex gap-3 items-center">
                <div class="flex justify-center items-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">تعديل اسم الفئة</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">قم بتحديث اسم الفئة أدناه</p>
                </div>
            </div>
            <button type="button" @click="closeEditModal()"
                class="text-gray-400 transition-colors hover:text-gray-500 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form :action="`{{ route('transaction-categories.index') }}/${categoryToEdit.id}`" method="POST" class="p-6"
            x-data="{ isLoading: false }" @submit="isLoading = true">
            @csrf
            @method('PUT')

            {{-- Category Name Input --}}
            <div>
                <label for="edit_category_name" class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
                    اسم الفئة <span class="text-error-500">*</span>
                </label>
                <input type="text" id="edit_category_name" name="name" x-model="categoryToEdit.name" required
                    maxlength="255"
                    class="px-4 py-3 w-full placeholder-gray-400 text-gray-900 bg-white rounded-xl border border-gray-200 transition-all dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500"
                    placeholder="أدخل اسم الفئة">
                @error('name')
                    <p class="mt-1 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 justify-end mt-6">
                <button type="button" @click="closeEditModal()"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl transition-all dark:text-gray-300 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">
                    إلغاء
                </button>
                <button type="submit" :disabled="isLoading" :class="isLoading ? 'opacity-75 cursor-not-allowed' : ''"
                    class="flex gap-2 items-center px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow-lg transition-all bg-brand-500 hover:bg-brand-600 shadow-brand-500/30">
                    <svg x-show="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                        style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isLoading ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
                </button>
            </div>
        </form>
    </div>
</div>