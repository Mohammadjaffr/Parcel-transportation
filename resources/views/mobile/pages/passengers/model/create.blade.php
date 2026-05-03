<div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full"
    class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

    <div
        class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto overflow-y-auto max-h-[90vh] custom-scrollbar">
        <div @click="closeModals()"
            class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
        </div>

        <div class="flex justify-between items-center px-2 mb-8">
            <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات الراكب</h3>
            <button type="button" @click="closeModals()"
                class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors bg-slate-50 text-slate-400 hover:bg-slate-100">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <template x-if="showEditModal">
            <form :action="editPassengerData.url" method="POST" class="px-2 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">التاريخ <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="date" name="date" x-model="editPassengerData.date" required
                                x-init="$watch('editPassengerData.date', value => editPassengerData.day = getArabicDayName(value))"
                                @change="editPassengerData.day = getArabicDayName($event.target.value)"
                                class="px-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                        </div>
                    </div>
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">اليوم <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select name="day" x-model="editPassengerData.day" required readonly
                                class="px-4 w-full h-14 text-sm rounded-2xl border-none ring-1 opacity-80 transition-all outline-none pointer-events-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                                <option value="">(يتم تحديده تلقائياً)</option>
                                <option value="السبت">السبت</option>
                                <option value="الاحد">الاحد</option>
                                <option value="الاثنين">الاثنين</option>
                                <option value="الثلاثاء">الثلاثاء</option>
                                <option value="الاربعاء">الاربعاء</option>
                                <option value="الخميس">الخميس</option>
                                <option value="الجمعة">الجمعة</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الراكب <span
                            class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span
                            class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">pin</span>
                        <input type="text" name="passenger_number" x-model="editPassengerData.passenger_number"
                            required placeholder="أدخل رقم الراكب"
                            class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                    </div>
                </div>

                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">المكان <span
                            class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span
                            class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">location_on</span>
                        <input type="text" name="location" x-model="editPassengerData.location" required
                            placeholder="أدخل المكان"
                            class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">العدد <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">group</span>
                            <input type="number" name="count" x-model="editPassengerData.count" required
                                placeholder="عدد الركاب"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                        </div>
                    </div>
                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">العمولة <span
                                class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">payments</span>
                            <input type="number" name="total_commission" x-model="editPassengerData.total_commission"
                                step="0.01" required placeholder="العمولة"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الوسيط</label>
                    <div class="relative">
                        <span
                            class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">handshake</span>
                        <input type="text" name="broker" x-model="editPassengerData.broker"
                            placeholder="اسم الوسيط (اختياري)"
                            class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                    </div>
                </div>

                <div class="relative pt-4 border-t border-slate-100">
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">السائق <span
                            class="text-rose-500">*</span></label>

                    <div x-data="driverSelect({
                        drivers: {{ Js::from($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values()) }},
                        countries: {{ Js::from(array_values(config('countries', []))) }},
                        initialId: editPassengerData.driver_id,
                        initialName: editPassengerData.driver_name,
                        initialPhone: editPassengerData.driver_phone
                    })" class="relative space-y-4">

                        <input type="hidden" name="driver_id" :value="selectedDriverId">
                        <input type="hidden" name="driver_phone" :value="fullPhone">
                        <input type="hidden" name="driver_name" :value="driverName">

                        <div class="flex relative rounded-2xl ring-1 transition-all bg-slate-50 focus-within:bg-white ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20"
                            :class="selectedDriverId ? 'bg-primary/5 ring-primary/30' : ''">

                            <button type="button" @click="openDropdown = !openDropdown"
                                class="flex gap-2 items-center px-4 bg-transparent rounded-r-2xl border-l transition-colors border-slate-200 shrink-0 hover:bg-slate-100">
                                <template x-if="selectedCountry?.svg">
                                    <svg class="w-5 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                </template>
                                <span class="text-xs font-bold text-slate-600 dir-ltr"
                                    x-text="selectedCountry?.dial_code"></span>
                                <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                            </button>

                            <input type="tel" x-model="phone" @input="handlePhoneInput"
                                @focus="showDriverDropdown = true" placeholder="رقم الهاتف" required
                                class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent rounded-l-2xl border-none outline-none font-headline dir-ltr"
                                :class="selectedDriverId ? 'font-bold text-primary' : ''"
                                :maxlength="selectedCountry?.code === 'YE' ? 9 : 15">

                            <button type="button" x-show="selectedDriverId" @click="resetSelection"
                                class="absolute left-3 top-1/2 z-10 p-0.5 bg-white rounded-full -translate-y-1/2 text-slate-400 hover:text-red-500">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>

                            <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition x-cloak
                                class="absolute top-[calc(100%+4px)] right-0 z-50 w-full max-h-60 bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden">
                                <div class="p-2 border-b border-slate-50">
                                    <input type="text" x-model="searchCountry" placeholder="ابحث عن الدولة..."
                                        class="px-4 py-2 w-full text-sm rounded-xl outline-none bg-slate-50 font-headline">
                                </div>
                                <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                    <template
                                        x-for="country in countries.filter(c => c.name.includes(searchCountry) || c.dial_code.includes(searchCountry))"
                                        :key="country.code">
                                        <div @click="selectCountry(country)"
                                            class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                            <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg"
                                                x-html="country.svg"></svg>
                                            <span
                                                class="flex-grow text-sm font-medium truncate text-slate-700 font-headline"
                                                x-text="country.name"></span>
                                            <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr"
                                                x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div x-show="showDriverDropdown && phone.length > 0 && !selectedDriverId"
                            @click.outside="showDriverDropdown = false" x-transition x-cloak
                            class="absolute top-[4rem] right-0 z-[50] w-full bg-white border border-slate-100 rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden max-h-48 overflow-y-auto">
                            <template x-for="driver in filteredDrivers" :key="driver.id">
                                <button type="button" @click="selectDriver(driver)"
                                    class="flex justify-between items-center px-4 py-3 w-full text-right border-b transition-colors hover:bg-slate-50 border-slate-50">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-sm font-bold text-slate-800" x-text="driver.name"></span>
                                        <span class="text-xs text-right text-slate-500 dir-ltr"
                                            x-text="driver.phone"></span>
                                    </div>
                                    <span
                                        class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                                </button>
                            </template>
                            <div x-show="filteredDrivers.length === 0" class="px-4 py-3 text-center bg-slate-50/50">
                                <span class="text-xs font-bold text-slate-500">سائق جديد، يرجى إدخال اسمه.</span>
                            </div>
                        </div>

                        <div class="relative">
                            <span
                                class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">person</span>
                            <input type="text" x-model="driverName" :readonly="isExistingDriver"
                                :class="isExistingDriver ? 'bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed' :
                                    'bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 text-slate-700'"
                                class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none font-headline"
                                placeholder="اسم السائق...">
                        </div>

                        <div x-show="driverName && !isExistingDriver && phone" style="display: none;"
                            class="flex gap-2 items-center p-3 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border font-headline border-emerald-100/50">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            <span>سائق جديد، سيتم حفظه تلقائياً.</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">ملاحظات</label>
                    <div class="relative">
                        <span class="absolute top-4 right-4 text-gray-400 material-symbols-outlined dark:text-gray-500">description</span>
                        <textarea name="note" x-model="editPassengerData.note" rows="3" placeholder="ملاحظات إضافية..."
                            class="py-4 pr-12 pl-4 w-full text-sm rounded-2xl border-none ring-1 transition-all outline-none resize-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline"></textarea>
                    </div>
                </div>

                <button type="submit"
                    class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95">
                    <span class="material-symbols-outlined">update</span>
                    حفظ التعديلات
                </button>
            </form>
        </template>
    </div>
</div>

<div x-show="showDeleteModal" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full"
    class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

    <div
        class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto text-center">
        <div @click="closeModals()"
            class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90">
        </div>

        <div class="flex justify-center items-center mx-auto mb-6 w-20 h-20 bg-red-50 text-red-500 rounded-[1.5rem]">
            <span class="text-4xl material-symbols-outlined">delete_forever</span>
        </div>

        <h3 class="mb-3 text-2xl font-black font-headline text-slate-800">تأكيد الحذف</h3>

        <p class="mb-8 text-sm font-semibold leading-relaxed text-slate-500">
            هل أنت متأكد من أنك تريد حذف الراكب رقم <br>
            <span class="text-base font-bold text-slate-800 font-headline"
                x-text="deletePassengerData.passenger_number"></span>؟<br>
            <span class="text-red-500/80">لا يمكن التراجع عن هذا الإجراء.</span>
        </p>

        <form :action="deletePassengerData.url" method="POST" class="flex gap-3 px-2">
            @csrf
            @method('DELETE')

            <button type="button" @click="closeModals()"
                class="flex-1 py-4 text-sm font-bold rounded-2xl transition-all text-slate-600 bg-slate-100 hover:bg-slate-200 active:scale-95 font-headline">
                تراجع
            </button>

            <button type="submit"
                class="flex-1 py-4 text-sm font-bold text-white bg-red-500 rounded-2xl shadow-lg transition-all hover:bg-red-600 shadow-red-500/30 active:scale-95 font-headline">
                نعم، احذف
            </button>
        </form>
    </div>
</div>

<div x-show="showCreateModal" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full"
    class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">
    
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto" @click="closeModals()"></div>

    <div class="relative w-full bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 max-w-xl mx-auto border-t border-white/20 pointer-events-auto overflow-y-auto max-h-[90vh] custom-scrollbar">
        <div @click="closeModals()" class="mx-auto mb-8 w-12 h-1.5 rounded-full transition-transform cursor-pointer bg-slate-200 active:scale-90"></div>

        <div class="flex justify-between items-center px-2 mb-8">
            <h3 class="text-xl font-black font-headline text-slate-800">إضافة راكب جديد</h3>
            <button type="button" @click="closeModals()" class="flex justify-center items-center w-10 h-10 rounded-xl transition-colors bg-slate-50 text-slate-400 hover:bg-slate-100">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('passengers.store') }}" method="POST" class="px-2 space-y-6">
            @csrf
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">التاريخ <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        {{-- استخدمنا $watch للمراقبة التلقائية --}}
                        <input type="date" name="date" required 
                            x-model="createPassengerData.date"
                            x-init="
                                $watch('createPassengerData.date', value => createPassengerData.day = getArabicDayName(value));
                                if(!createPassengerData.date) { createPassengerData.date = '{{ date('Y-m-d') }}'; }
                            "
                            @change="createPassengerData.day = getArabicDayName($event.target.value)"
                            class="px-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                    </div>
                </div>
                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">اليوم <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select name="day" x-model="createPassengerData.day" required readonly
                            class="px-4 w-full h-14 text-sm rounded-2xl border-none ring-1 opacity-80 transition-all outline-none pointer-events-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                            <option value="">(يتم تحديده تلقائياً)</option>
                            <option value="السبت">السبت</option>
                            <option value="الاحد">الاحد</option>
                            <option value="الاثنين">الاثنين</option>
                            <option value="الثلاثاء">الثلاثاء</option>
                            <option value="الاربعاء">الاربعاء</option>
                            <option value="الخميس">الخميس</option>
                            <option value="الجمعة">الجمعة</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">رقم الراكب <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">pin</span>
                    <input type="text" name="passenger_number" required placeholder="أدخل رقم الراكب"
                        class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                </div>
            </div>

            <div>
                <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">المكان <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">location_on</span>
                    <input type="text" name="location" required placeholder="أدخل المكان"
                        class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">العدد <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">group</span>
                        <input type="number" name="count" required placeholder="عدد الركاب"
                            class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                    </div>
                </div>
                <div>
                    <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">العمولة <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">payments</span>
                        <input type="number" name="total_commission" step="0.01" required placeholder="العمولة"
                            class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                    </div>
                </div>
            </div>

            <div>
                <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">الوسيط</label>
                <div class="relative">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">handshake</span>
                    <input type="text" name="broker" placeholder="اسم الوسيط (اختياري)"
                        class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline">
                </div>
            </div>

            <div class="relative pt-4 border-t border-slate-100">
                <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">السائق <span class="text-rose-500">*</span></label>
                
                <div x-data="driverSelect({ 
                        drivers: {{ Js::from($drivers->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values()) }}, 
                        countries: {{ Js::from(array_values(config('countries', []))) }},
                        initialId: null,
                        initialName: '',
                        initialPhone: ''
                    })" class="relative space-y-4">
                    
                    <input type="hidden" name="driver_id" :value="selectedDriverId">
                    <input type="hidden" name="driver_phone" :value="fullPhone">
                    <input type="hidden" name="driver_name" :value="driverName">

                    <div class="flex relative rounded-2xl ring-1 transition-all bg-slate-50 focus-within:bg-white ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20"
                         :class="selectedDriverId ? 'bg-primary/5 ring-primary/30' : ''">
                        
                        <button type="button" @click="openDropdown = !openDropdown"
                            class="flex gap-2 items-center px-4 bg-transparent rounded-r-2xl border-l transition-colors border-slate-200 shrink-0 hover:bg-slate-100">
                            <template x-if="selectedCountry?.svg">
                                <svg class="w-5 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                            </template>
                            <span class="text-xs font-bold text-slate-600 dir-ltr" x-text="selectedCountry?.dial_code"></span>
                            <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                        </button>
                        
                        <input type="tel" x-model="phone" @input="handlePhoneInput" @focus="showDriverDropdown = true" placeholder="رقم الهاتف" required
                            class="flex-1 px-4 w-full h-14 text-sm text-left bg-transparent rounded-l-2xl border-none outline-none font-headline dir-ltr"
                            :class="selectedDriverId ? 'font-bold text-primary' : ''"
                            :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                            >
                        
                        <button type="button" x-show="selectedDriverId" @click="resetSelection"
                            class="absolute left-3 top-1/2 z-10 p-0.5 bg-white rounded-full -translate-y-1/2 text-slate-400 hover:text-red-500">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>

                        <div x-show="openDropdown" @click.outside="openDropdown = false" x-transition x-cloak
                            class="absolute top-[calc(100%+4px)] right-0 z-50 w-full max-h-60 bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden">
                            <div class="p-2 border-b border-slate-50">
                                <input type="text" x-model="searchCountry" placeholder="ابحث عن الدولة..."
                                    class="px-4 py-2 w-full text-sm rounded-xl outline-none bg-slate-50 font-headline">
                            </div>
                            <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                <template x-for="country in countries.filter(c => c.name.includes(searchCountry) || c.dial_code.includes(searchCountry))" :key="country.code">
                                    <div @click="selectCountry(country)"
                                        class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                        <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                        <span class="flex-grow text-sm font-medium truncate text-slate-700 font-headline" x-text="country.name"></span>
                                        <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr" x-text="country.dial_code"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div x-show="showDriverDropdown && phone.length > 0 && !selectedDriverId"
                        @click.outside="showDriverDropdown = false" x-transition x-cloak
                        class="absolute top-[4rem] right-0 z-[50] w-full bg-white border border-slate-100 rounded-2xl shadow-[0_10px_40px_-15px_rgba(0,0,0,0.1)] overflow-hidden max-h-48 overflow-y-auto">
                        <template x-for="driver in filteredDrivers" :key="driver.id">
                            <button type="button" @click="selectDriver(driver)"
                                class="flex justify-between items-center px-4 py-3 w-full text-right border-b transition-colors hover:bg-slate-50 border-slate-50">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-sm font-bold text-slate-800" x-text="driver.name"></span>
                                    <span class="text-xs text-right text-slate-500 dir-ltr" x-text="driver.phone"></span>
                                </div>
                                <span class="material-symbols-outlined text-slate-300 text-[18px]">arrow_back_ios</span>
                            </button>
                        </template>
                        <div x-show="filteredDrivers.length === 0" class="px-4 py-3 text-center bg-slate-50/50">
                            <span class="text-xs font-bold text-slate-500">سائق جديد، يرجى إدخال اسمه.</span>
                        </div>
                    </div>

                    <div class="relative">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-slate-400">person</span>
                        <input type="text" x-model="driverName" :readonly="isExistingDriver"
                            :class="isExistingDriver ? 'bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed' : 'bg-slate-50 ring-slate-100 focus:bg-white focus:ring-2 focus:ring-primary/20 text-slate-700'"
                            class="pr-12 pl-4 w-full h-14 text-sm rounded-2xl border-none ring-1 transition-all outline-none font-headline"
                            placeholder="اسم السائق...">
                    </div>

                    <div x-show="driverName && !isExistingDriver && phone" style="display: none;"
                        class="flex gap-2 items-center p-3 text-xs font-bold text-emerald-600 bg-emerald-50 rounded-xl border font-headline border-emerald-100/50">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span>سائق جديد، سيتم حفظه تلقائياً.</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">ملاحظات</label>
                <div class="relative">
                    <span class="absolute top-4 right-4 text-gray-400 material-symbols-outlined dark:text-gray-500">description</span>
                    <textarea name="note" rows="3" placeholder="ملاحظات إضافية..."
                        class="py-4 pr-12 pl-4 w-full text-sm rounded-2xl border-none ring-1 transition-all outline-none resize-none bg-slate-50 focus:bg-white ring-slate-100 focus:ring-2 focus:ring-primary/20 font-headline"></textarea>
                </div>
            </div>

            <button type="submit" 
                class="flex gap-2 justify-center items-center mt-6 w-full h-14 font-black text-white rounded-2xl shadow-lg transition-all bg-primary font-headline shadow-primary/30 active:scale-95">
                <span class="material-symbols-outlined">save</span>
                حفظ وإضافة
            </button>
        </form>
    </div>
</div>