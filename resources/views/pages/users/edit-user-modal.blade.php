<div x-show="editModalOpen" class="flex overflow-y-auto fixed inset-0 justify-center items-center p-5 modal z-99999"
    x-cloak>
    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
    </div>

    <div @click.outside="editModalOpen = false"
        class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10" dir="rtl">

        <form method="POST" :action="`/users/${editUser.id}`" @submit="isUpdating = true">
            @csrf
            @method('PUT')
            <h4 class="mb-6 text-lg font-bold text-gray-800 dark:text-white/90 font-outfit">
                تعديل بيانات المستخدم
            </h4>

            <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-2">

                <div class="sm:col-span-2">
                    <label for="edit_name" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                        أسم المستخدم <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                    </label>
                    <input type="text" id="edit_name" name="name" required x-model="editUser.name"
                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                </div>

                <div class="sm:col-span-1">
                    <label for="edit_type" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                        نوع الحساب <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                    </label>
                    <select id="edit_type" name="type" required x-model="editUser.type"
                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                        <option value="user">مستخدم</option>
                        <option value="admin">مدير نظام</option>
                    </select>
                </div>

                {{-- رقم الجوال مع العلم --}}
                <div class="sm:col-span-1" x-data="{ open: false, search: '' }">
                    <label for="edit_phone_display"
                        class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                        رقم الجوال <span class="mt-1 text-xs text-warning-500 dark:text-warning/90">*</span>
                    </label>
                    <div class="relative">
                        <input type="hidden" name="phone"
                            :value="editUser.phone_country.dial_code + editUser.phone_local">

                        <div
                            class="flex w-full h-11 rounded-lg border border-gray-300 dark:border-gray-600 shadow-theme-xs">
                            <button type="button" @click="open = !open"
                                class="flex gap-2 items-center px-3 bg-gray-50 rounded-r-lg border-l border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                <img :src="`https://flagcdn.com/w20/${editUser.phone_country.code.toLowerCase()}.png`"
                                    alt="Flag" class="w-5 h-auto">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <input id="edit_phone_display" type="tel" x-model="editUser.phone_local"
                                placeholder="780236551" required
                                class="flex-grow px-3 text-sm text-left text-gray-800 bg-transparent rounded-l-lg border-none dark:text-white focus:outline-none focus:ring-0"
                                dir="ltr">
                        </div>

                        <div x-show="open" @click.outside="open = false" x-transition
                            class="overflow-hidden absolute mt-1 w-full max-h-60 bg-white rounded-lg border border-gray-200 shadow-lg z-60 dark:bg-gray-800 dark:border-gray-700">
                            <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                class="px-4 py-2 w-full border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500">
                            <div class="overflow-y-auto max-h-48">
                                <template
                                    x-for="country in countries.filter(c => c.name.toLowerCase().includes(search.toLowerCase()) || c.dial_code.includes(search))"
                                    :key="country.code">
                                    <div @click="editUser.phone_country = country; open = false"
                                        class="flex gap-3 items-center p-2 px-4 transition-colors duration-150 cursor-pointer hover:bg-sky-50 dark:hover:bg-gray-700">
                                        <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                            alt="" class="w-5">
                                        <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="country.name"></span>
                                        <span class="text-xs tracking-wider text-gray-500 dark:text-gray-400"
                                            x-text="'+' + country.dial_code"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- رقم الواتساب مع العلم --}}
                <div class="sm:col-span-1" x-data="{ open: false, search: '' }">
                    <label for="edit_whatsapp_display"
                        class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                        رقم الواتساب <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">(اختياري)</span>
                    </label>
                    <div class="relative">
                        <input type="hidden" name="whatsapp_number"
                            :value="editUser.whatsapp_country.dial_code + editUser.whatsapp_local">

                        <div
                            class="flex w-full h-11 rounded-lg border border-gray-300 dark:border-gray-600 shadow-theme-xs">
                            <button type="button" @click="open = !open"
                                class="flex gap-2 items-center px-3 bg-gray-50 rounded-r-lg border-l border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                <img :src="`https://flagcdn.com/w20/${editUser.whatsapp_country.code.toLowerCase()}.png`"
                                    alt="Flag" class="w-5 h-auto">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <input id="edit_whatsapp_display" type="tel" x-model="editUser.whatsapp_local"
                                placeholder="780236551"
                                class="flex-grow px-3 text-sm text-left text-gray-800 bg-transparent rounded-l-lg border-none dark:text-white focus:outline-none focus:ring-0"
                                dir="ltr">
                        </div>

                        <div x-show="open" @click.outside="open = false" x-transition
                            class="overflow-hidden absolute mt-1 w-full max-h-60 bg-white rounded-lg border border-gray-200 shadow-lg z-60 dark:bg-gray-800 dark:border-gray-700">
                            <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                class="px-4 py-2 w-full border-b dark:bg-gray-900 dark:border-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-500">
                            <div class="overflow-y-auto max-h-48">
                                <template
                                    x-for="country in countries.filter(c => c.name.toLowerCase().includes(search.toLowerCase()) || c.dial_code.includes(search))"
                                    :key="country.code">
                                    <div @click="editUser.whatsapp_country = country; open = false"
                                        class="flex gap-3 items-center p-2 px-4 transition-colors duration-150 cursor-pointer hover:bg-sky-50 dark:hover:bg-gray-700">
                                        <img :src="`https://flagcdn.com/w20/${country.code.toLowerCase()}.png`"
                                            alt="" class="w-5">
                                        <span class="flex-grow text-sm font-medium text-gray-900 dark:text-gray-100"
                                            x-text="country.name"></span>
                                        <span class="text-xs tracking-wider text-gray-500 dark:text-gray-400"
                                            x-text="'+' + country.dial_code"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-1">
                    <label for="edit_password"
                        class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-400">
                        كلمة السر الجديدة <span class="mt-1 text-xs text-gray-500 dark:text-gray-400">(اختياري)</span>
                    </label>
                    <input type="password" id="edit_password" name="password"
                        placeholder="اتركها فارغة لعدم التغيير" x-model="editUser.password"
                        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
                </div>

                <div class="flex flex-col justify-end sm:col-span-1">
                    <label class="block mb-4 text-sm font-medium text-gray-700 dark:text-gray-400">
                        حالة الحساب
                    </label>
                    <div class="flex gap-3 items-center">
                        <input type="hidden" name="is_banned" :value="editUser.is_banned ? 1 : 0">
                        <button type="button" @click="editUser.is_banned = !editUser.is_banned"
                            :class="editUser.is_banned ? 'bg-error-500' : 'bg-brand-500'"
                            class="inline-flex relative w-11 h-6 rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out cursor-pointer shrink-0">
                            <span :class="editUser.is_banned ? 'translate-x-0' : 'translate-x-5'"
                                class="inline-block w-5 h-5 bg-white rounded-full ring-0 shadow transition duration-200 ease-in-out transform pointer-events-none"></span>
                        </button>
                        <span class="text-sm font-medium"
                            :class="editUser.is_banned ? 'text-error-600' : 'text-success-600'"
                            x-text="editUser.is_banned ? 'محظور' : 'نشط'"></span>
                    </div>
                </div>
              <div>
    <label for="branch_id" class="block mb-1 w-full text-sm font-medium text-gray-700 dark:text-gray-400">
        الفرع
    </label>
    <select name="branch_id" id="branch_id" x-model="editUser.branch_id"
        class="px-4 py-2.5 w-full h-11 text-sm text-gray-800 bg-transparent rounded-lg border border-gray-300 hover:border-brand-500 dark:bg-dark-900 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
        <option value="">اختر الفرع</option>
        @foreach ($branches as $branch)
            <option :value="{{ $branch->id }}">{{ $branch->name }}</option>
        @endforeach
    </select>
</div>

            </div>

            <div class="flex gap-3 justify-end items-center mt-6 w-full">
                <button @click="editModalOpen = false" type="button"
                    class="flex justify-center px-4 py-3 w-full text-sm font-medium text-gray-700 bg-white rounded-lg border border-gray-300 hover:border-brand-500 sm:w-auto">
                    إغلاق
                </button>
                <button type="submit" :disabled="isUpdating"
                    class="flex gap-2 justify-center items-center px-4 py-3 w-full text-sm font-medium text-white rounded-lg transition-all hover:bg-brand-600 bg-brand-500 disabled:opacity-75 disabled:cursor-not-allowed">
                    <svg x-show="isUpdating" class="w-5 h-5 text-white animate-spin"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isUpdating ? 'جاري التحديث...' : 'تحديث البيانات'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
