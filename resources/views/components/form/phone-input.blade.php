@props(['name' => 'phone'])

<div x-data="{
         phoneOpen: false,
         searchCountry: '',
         localPhone: '',
         fullPhone: '', 
         selectedCountry: null,
         countries: @js(array_values(config('countries', []))),

         init() {
             // تعيين اليمن كافتراضي
             this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];
             
             // محاولة المعالجة الأولية في حال وجود بيانات مسبقة
             if(this.fullPhone) {
                 this.parseFullPhone(this.fullPhone);
             }

             // مراقبة البيانات القادمة من الأب (مهم جداً لحالة التعديل)
             this.$watch('fullPhone', (value) => {
                 if (!value) {
                     this.localPhone = '';
                     return;
                 }
                 // نقوم بالمعالجة فقط إذا كان الرقم القادم مختلفاً عن الرقم المدمج حالياً
                 let currentCombined = (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhone;
                 if (value !== currentCombined) {
                     this.parseFullPhone(value);
                 }
             });

             // تحديث الخارج عند إدخال المستخدم للرقم المحلي
             this.$watch('localPhone', (value) => {
                 this.fullPhone = (this.selectedCountry?.dial_code.replace('+', '') || '') + value;
             });

             // تحديث الخارج عند تغيير الدولة
             this.$watch('selectedCountry', (value) => {
                 this.fullPhone = (value?.dial_code.replace('+', '') || '') + this.localPhone;
                 this.$nextTick(() => { if(this.$refs.phone_input) this.$refs.phone_input.focus(); });
             });
         },

         parseFullPhone(full) {
             let phoneStr = String(full).replace(/[+ ]/g, '');
             // ترتيب الدول حسب طول المفتاح لتجنب مطابقة +1 قبل +123
             let sortedCountries = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);

             let matched = false;
             for (let country of sortedCountries) {
                 let cleanDialCode = country.dial_code.replace(/[+ ]/g, '');
                 if (phoneStr.startsWith(cleanDialCode)) {
                     this.selectedCountry = country;
                     this.localPhone = phoneStr.substring(cleanDialCode.length);
                     matched = true;
                     break;
                 }
             }
             if (!matched) {
                 this.localPhone = phoneStr;
             }
         }
     }"
     x-modelable="fullPhone"
     {{ $attributes->whereStartsWith('x-model') }}
     class="relative w-full">

    <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-gray-300">
        رقم الجوال <span class="text-[#dc6803]">*</span>
    </label>

    <input type="hidden" name="{{ $name }}" :value="fullPhone">

    <div class="flex overflow-hidden items-center w-full h-14 bg-gray-50 rounded-xl ring-1 ring-gray-200 transition-all focus-within:bg-white focus-within:ring-2 focus-within:ring-[#dc6803]/40 dark:bg-gray-900 dark:ring-gray-700 dark:focus-within:bg-black">
        
        <button type="button" @click="phoneOpen = !phoneOpen"
                class="flex gap-2 items-center px-4 h-full bg-gray-100 border-l border-gray-200 transition-colors shrink-0 hover:bg-gray-200 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
            <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform" :class="phoneOpen ? 'rotate-180' : ''">expand_more</span>
            <span class="text-sm font-bold text-gray-600 dark:text-gray-300" dir="ltr" x-text="'+' + (selectedCountry?.dial_code || '967').replace('+', '')"></span>
            
            <template x-if="selectedCountry?.svg">
                <div class="flex items-center justify-center w-6 h-auto overflow-hidden rounded-[2px] shadow-sm border border-gray-100 dark:border-gray-600" x-html="selectedCountry.svg"></div>
            </template>
        </button>

        <input type="tel" x-ref="phone_input" x-model="localPhone" placeholder="7XXXXXXXX" autocomplete="off" required
               class="flex-1 px-4 w-full h-full text-sm tracking-wider placeholder-gray-400 text-left bg-transparent border-0 outline-none dark:text-white focus:ring-0" dir="ltr">
    </div>

    <div x-cloak x-show="phoneOpen" @click.outside="phoneOpen = false" 
         x-transition class="overflow-hidden absolute z-50 mt-2 w-full bg-white rounded-xl border border-gray-100 shadow-xl dark:bg-gray-800 dark:border-gray-700">
        <div class="p-2 border-b border-gray-50 dark:border-gray-700">
            <input type="text" x-model="searchCountry" placeholder="ابحث عن الدولة..." class="px-4 py-2 w-full text-sm bg-gray-50 rounded-lg outline-none focus:bg-white dark:bg-gray-900 dark:text-white">
        </div>
        <div class="overflow-y-auto max-h-48 custom-scrollbar">
            <template x-for="c in (searchCountry === '' ? countries : countries.filter(x => (x.name && x.name.toLowerCase().includes(searchCountry.toLowerCase())) || (x.dial_code && x.dial_code.includes(searchCountry))))" :key="c.code">
                <button type="button" @click="selectedCountry = c; phoneOpen = false;" class="flex justify-between items-center px-4 py-3 w-full text-sm transition-colors hover:bg-[#dc6803]/5">
                    <div class="flex gap-3 items-center">
                        <template x-if="c.svg"><div class="w-5 h-auto overflow-hidden rounded-[2px] shadow-sm" x-html="c.svg"></div></template>
                        <span class="font-medium text-gray-700 dark:text-gray-200" x-text="c.name"></span>
                    </div>
                    <span class="font-mono text-xs font-bold text-gray-500" dir="ltr" x-text="'+' + (c.dial_code || '').replace('+', '')"></span>
                </button>
            </template>
        </div>
    </div>
</div>