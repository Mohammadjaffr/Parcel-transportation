@props(['shipment'])

{{-- Return Shipment Modal --}}
<div x-show="returnModalOpen" class="flex fixed inset-0 justify-center items-center p-4 z-99999" x-cloak x-transition>
    <div class="fixed inset-0 w-full h-full bg-gray-400/50 backdrop-blur-[32px]" @click="returnModalOpen = false">
    </div>
    <div class="relative p-6 w-full max-w-md bg-white rounded-2xl shadow-2xl dark:bg-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">إرجاع الشحنة</h3>
            <button @click="returnModalOpen = false"
                class="p-2 text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <div class="p-4 mb-4 rounded-lg bg-warning-50 dark:bg-warning-500/10">
            <p class="text-sm text-warning-700 dark:text-warning-400">
                <strong>تنبيه:</strong> سيتم عكس جميع القيود المالية المرتبطة بهذه الشحنة.
            </p>
        </div>

        <form action="{{ route('shipment.return', $shipment->id) }}" method="POST" @submit="returnLoading = true">
            @csrf
            @method('PATCH')

            <div class="mb-6">
                <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">سبب الإرجاع *</label>
                <textarea name="reason" x-model="returnReason" rows="3" required
                    class="px-4 py-3 w-full text-sm rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-warning-500 focus:border-transparent"
                    placeholder="مثال: رفض المستلم، عنوان خاطئ، تلف الشحنة..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="returnModalOpen = false"
                    class="flex-1 px-4 py-3 text-sm font-bold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    إلغاء
                </button>
                <button type="submit" :disabled="returnLoading"
                    class="flex flex-1 gap-2 justify-center items-center px-4 py-3 text-sm font-bold text-white rounded-xl bg-warning-500 hover:bg-warning-600 disabled:opacity-50">
                    <svg x-show="returnLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="returnLoading ? 'جاري الإرجاع...' : 'تأكيد الإرجاع'"></span>
                </button>
            </div>
        </form>
    </div>
</div>