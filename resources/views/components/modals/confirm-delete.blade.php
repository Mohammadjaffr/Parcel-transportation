{{-- Delete Confirmation Modal --}}
<div x-show="showDeleteModal" class="fixed inset-0 flex items-center justify-center p-5 z-99999" style="display: none;">
    {{-- Overlay --}}
    <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" @click="showDeleteModal = false"></div>

    {{-- Modal Content --}}
    <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900 shadow-xl overflow-hidden">

        <div class="text-center py-4">
            {{-- Icon --}}
            <div class="relative flex items-center justify-center z-1 mb-7">
                {{-- Background Circle Pattern --}}
                <svg style="color:#fee2e2" width="90" height="90" viewBox="0 0 90 90" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M34.364 6.85053C38.6205 -2.28351 51.3795 -2.28351 55.636 6.85053C58.0129 11.951 63.5594 14.6722 68.9556 13.3853C78.6192 11.0807 86.5743 21.2433 82.2185 30.3287C79.7862 35.402 81.1561 41.5165 85.5082 45.0122C93.3019 51.2725 90.4628 63.9451 80.7747 66.1403C75.3648 67.3661 71.5265 72.2695 71.5572 77.9156C71.6123 88.0265 60.1169 93.6664 52.3918 87.3184C48.0781 83.7737 41.9219 83.7737 37.6082 87.3184C29.8831 93.6664 18.3877 88.0266 18.4428 77.9156C18.4735 72.2695 14.6352 67.3661 9.22531 66.1403C-0.462787 63.9451 -3.30193 51.2725 4.49185 45.0122C8.84391 41.5165 10.2138 35.402 7.78151 30.3287C3.42572 21.2433 11.3808 11.0807 21.0444 13.3853C26.4406 14.6722 31.9871 11.951 34.364 6.85053Z"
                        fill="currentColor" />
                </svg>

                {{-- Trash Icon --}}
                <span class="absolute -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2">
                    <svg class="text-error-600" width="38" height="38" viewBox="0 0 38 38" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M9.62684 11.7496C9.04105 11.1638 9.04105 10.2141 9.62684 9.6283C10.2126 9.04252 11.1624 9.04252 11.7482 9.6283L18.9985 16.8786L26.2485 9.62851C26.8343 9.04273 27.7841 9.04273 28.3699 9.62851C28.9556 10.2143 28.9556 11.164 28.3699 11.7498L21.1198 18.9999L28.3699 26.25C28.9556 26.8358 28.9556 27.7855 28.3699 28.3713C27.7841 28.9571 26.8343 28.9571 26.2485 28.3713L18.9985 21.1212L11.7482 28.3715C11.1624 28.9573 10.2126 28.9573 9.62684 28.3715C9.04105 27.7857 9.04105 26.836 9.62684 26.2502L16.8771 18.9999L9.62684 11.7496Z"
                            fill="currentColor" />
                    </svg>
                </span>
            </div>

            {{-- Text --}}
            <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90 sm:text-title-sm">
                تأكيد الحذف
            </h4>
            <p class="text-sm leading-6 text-gray-500 dark:text-gray-400">
                هل أنت متأكد من أنك تريد حذف هذا العنصر؟ لا يمكن التراجع عن هذا الإجراء.
            </p>

            {{-- Actions --}}
            <div class="flex items-center justify-center gap-3 mt-8">
                <button @click="showDeleteModal = false" type="button"
                    class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    إلغاء
                </button>

                <form method="POST" :action="deleteUrl" class="inline-block" x-data="{ isLoading: false } "
                    @submit="isLoading = true">
                    @csrf
                    @method('DELETE')
                    <button type="submit" :disabled="isLoading" style="background-color: #FEF2F2; color: #DC2626;"
                        class="px-6 py-2 text-sm font-medium rounded-lg hover:bg-error-200 hover:text-white disabled:cursor-not-allowed transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg x-show="isLoading" class="animate-spin h-4 w-4 text-current"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isLoading ? 'جاري الحذف...' : 'حذف'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>