<div x-data="{ 
    isModalEditOpen: @if(session('openModalEdit')) true @else false @endif,
    imagePreview: null 
}">
    <div x-show="isModalEditOpen"
        class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-99999"
        style="display: none;">

        <!-- الخلفية مع حدث النقر -->
        <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
             @click="isModalEditOpen = false">
        </div>

        <!-- المودال بدون @click.outside -->
        <div class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
            @php
                $vehicle = session('vehicle');
            @endphp
            @if($vehicle)
            <form method="POST" action="{{ route('Vehicle.update', $vehicle->id) }}" enctype="multipart/form-data" id="editVehicleForm">
                @csrf
                @method('put')
                <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">
                    تعديل المركبة
                </h4>
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            النوع
                        </label>
                        <input type="text" placeholder="مثال: فوكسي" name="type" required value="{{ old('type', $vehicle->type) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            عدد الأشخاص
                        </label>
                        <input type="number" placeholder="مثال: 4" min="1" max="100" name="max_passengers" required
                            value="{{ old('max_passengers', $vehicle->max_passengers) }}"
                            class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        @error('max_passengers')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            الوصف
                        </label>
                        <textarea placeholder="وصف المركبه" rows="4" name="description" 
                            class="hover:border-brand-500 dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs resize-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">{{ old('description', $vehicle->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                صورة المركبه
                            </label>

                            <div class="flex items-center gap-4">
                                <!-- عرض الصورة القديمة -->
                                @if($vehicle && $vehicle->image)
                                <div class="flex-shrink-0">
                                    <div class="relative">
                                        <img src="{{ Storage::exists($vehicle->image) ? Storage::url($vehicle->image) : url($vehicle->image) }}"
                                            class="h-16 w-16 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                                            alt="الصورة الحالية">
                                        <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 text-center">الحالية</p>
                                </div>
                                @endif

                                <!-- new file -->
                                <div class="flex-1">
                                    <div class="relative">
                                        <div @click="$refs.fileInput.click()"
                                            class="cursor-pointer flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-600 dark:bg-gray-800 hover:border-brand-500 dark:hover:border-brand-500 transition-colors duration-200 min-h-[120px] w-full">

                                            <template x-if="imagePreview">
                                                <div class="flex flex-col items-center justify-center w-full">
                                                    <img :src="imagePreview"
                                                        class="h-16 w-16 rounded-lg object-cover border border-gray-200 dark:border-gray-600 mb-2"
                                                        alt="معاينة الصورة الجديدة">
                                                    <span class="text-xs text-green-600 font-medium">صورة جديدة</span>
                                                </div>
                                            </template>

                                            <template x-if="!imagePreview">
                                                <div class="text-center">
                                                    <div class="mb-2 flex justify-center">
                                                        <div
                                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                                            <svg class="fill-current w-5 h-5" viewBox="0 0 29 28" fill="none">
                                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                                    d="M14.5019 3.91699C14.2852 3.91699 14.0899 2.00891 13.953 4.15589L8.57363 9.53186C8.28065 9.82466 8.2805 10.2995 8.5733 10.5925C8.8661 10.8855 9.34097 10.8857 9.63396 10.5929L13.7519 6.47752V18.667C13.7519 19.0812 14.0877 19.417 14.5019 19.417C14.9161 19.417 15.2519 19.0812 15.2519 18.667V6.48234L19.3653 10.5929C19.6583 10.8857 20.1332 10.8855 20.426 10.5925C20.7188 10.2995 20.7186 9.82463 20.4256 9.53184L15.0838 4.19378C14.9463 4.02488 14.7367 3.91699 14.5019 3.91699ZM5.91626 18.667C5.91626 18.2528 5.58047 17.917 5.16626 17.917C4.75205 17.917 4.41626 18.2528 4.41626 18.667V21.8337C4.41626 23.0763 5.42362 24.0837 6.66626 24.0837H22.3339C23.5766 24.0837 24.5839 23.0763 24.5839 21.8337V18.667C24.5839 18.2528 24.2482 17.917 23.8339 17.917C23.4197 17.917 23.0839 18.2528 23.0839 18.667V21.8337C23.0839 22.2479 22.7482 22.5837 22.3339 22.5837H6.66626C6.25205 22.5837 5.91626 22.2479 5.91626 21.8337V18.667Z"
                                                                    fill="" />
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    <span class="text-xs text-brand-500 font-medium">
                                                        اضغط لرفع صورة جديدة
                                                    </span>
                                                    <p class="text-xs text-gray-500 mt-1">سيتم استبدال الصورة الحالية</p>
                                                </div>
                                            </template>
                                        </div>

                                        <input x-ref="fileInput" 
                                            id="fileUpload_{{ Str::slug('image') }}" 
                                            name="image" 
                                            type="file" 
                                            class="hidden"
                                            accept="image/*" 
                                            @change="imagePreview = URL.createObjectURL($event.target.files[0])" />
                                        @error('image')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                <div class="flex items-center justify-end w-full gap-3 mt-6">
                    <button @click="isModalEditOpen = false" type="button"
                        class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
                        إغلاق
                    </button>
                    <button type="submit"
                        class="flex justify-center hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
                        تحديث المركبة
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>