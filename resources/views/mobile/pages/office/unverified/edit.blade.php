@extends('mobile.layouts.app')

@section('title', 'تعديل مكتب')

@section('content')
<div class="flex flex-col gap-6 pb-24 min-h-screen" 
     x-data="{ 
        isSubmitting: false,
        allCountries: @js(array_values(config('countries', []))),
        
        {{-- دالة لتفكيك رقم الهاتف المخزن إلى (كود دولة + رقم محلي) --}}
        parsePhone(fullPhone) {
            if(!fullPhone) return { country: null, local: '' };
            let matched = this.allCountries.find(c => fullPhone.startsWith(c.dial_code.replace('+', '')));
            if(matched) {
                return { 
                    country: matched, 
                    local: fullPhone.substring(matched.dial_code.replace('+', '').length) 
                };
            }
            return { country: this.allCountries.find(c => c.code === 'YE'), local: fullPhone };
        },

        branches: [],
        
        init() {
            {{-- تحميل الفروع الموجودة مسبقاً --}}
            let existingBranches = @js($office->branches);
            this.branches = existingBranches.map(b => {
                let phoneData = this.parsePhone(b.phone);
                return {
                    name: b.name,
                    city: b.city,
                    address: b.address,
                    localPhone: phoneData.local,
                    fullPhone: b.phone,
                    selectedCountry: phoneData.country,
                    open: false,
                    search: ''
                };
            });
        },

        addBranch() {
            let defaultCountry = this.allCountries.find(c => c.code === 'YE');
            this.branches.push({ name: '', city: '', address: '', localPhone: '', fullPhone: '', selectedCountry: defaultCountry, open: false, search: '' });
        },

        removeBranch(index) {
            if (this.branches.length > 1) this.branches.splice(index, 1);
        },

        updatePhone(index) {
            let b = this.branches[index];
            let dCode = b.selectedCountry.dial_code.replace('+', '');
            b.fullPhone = b.localPhone ? dCode + b.localPhone : '';
        }
     }">

    <div class="flex items-center gap-4 px-2">
        <a href="{{ route('offices.unverified.index') }}" class="flex justify-center items-center w-10 h-10 rounded-xl bg-white shadow-sm text-slate-400">
            <span class="material-symbols-outlined">arrow_forward</span>
        </a>
        <h1 class="text-xl font-black font-headline text-slate-800">تعديل مكتب</h1>
    </div>

    <form action="{{ route('offices.update', $office->id) }}" method="POST" @submit="isSubmitting = true" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="px-2">
            <div class="p-6 bg-white rounded-[2rem] shadow-sm border border-slate-100">
                <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">اسم المكتب الرئيسي</label>
                <input type="text" name="name" value="{{ $office->name }}" required
                       class="w-full h-14 px-4 text-sm rounded-2xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
            </div>
        </div>

        <div class="px-2 space-y-4">
            <div class="flex justify-between items-center px-2">
                <span class="font-bold font-headline text-slate-500">تعديل الفروع</span>
                <button type="button" @click="addBranch()" class="text-xs font-bold text-primary bg-primary/5 px-3 py-2 rounded-lg">
                    + إضافة فرع جديد
                </button>
            </div>

            <template x-for="(branch, index) in branches" :key="index">
                <div class="p-5 bg-white rounded-[2rem] shadow-sm border border-slate-100 space-y-4 relative">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-black text-primary bg-primary/5 px-2 py-0.5 rounded" x-text="'فرع #' + (index + 1)"></span>
                        <button type="button" x-show="branches.length > 1" @click="removeBranch(index)" class="text-rose-400">
                            <span class="material-symbols-outlined text-xl">delete_sweep</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" :name="`branches[${index}][name]`" x-model="branch.name" required placeholder="اسم الفرع"
                                   class="w-full h-12 px-4 text-xs rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 font-headline">
                            <input type="text" :name="`branches[${index}][city]`" x-model="branch.city" required placeholder="المدينة"
                                   class="w-full h-12 px-4 text-xs rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 font-headline">
                        </div>

                        <div class="relative">
                            {{-- الحقل المخفي الذي سيتم إرساله للسيرفر --}}
                            <input type="hidden" :name="`branches[${index}][phone]`" x-model="branch.fullPhone">

                            <div class="flex overflow-hidden relative items-center rounded-xl ring-1 transition-all group bg-slate-50 ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20">
                                
                                {{-- حقل إدخال الرقم المحلي --}}
                                <input type="tel" 
                                    x-model="branch.localPhone" 
                                    @input="
                                        branch.localPhone = branch.localPhone.replace(/[^0-9]/g, ''); 
                                        if(branch.selectedCountry?.dial_code === '+967' && branch.localPhone.length > 9) {
                                            branch.localPhone = branch.localPhone.substring(0, 9);
                                        }
                                        updatePhone(index);
                                    " 
                                    :maxlength="branch.selectedCountry?.dial_code === '+967' ? 9 : 15"
                                    required 
                                    placeholder="7XXXXXXXX" 
                                    inputmode="numeric"
                                    class="flex-1 min-w-0 w-full px-4 py-3 pr-11 text-sm text-left bg-transparent border-0 font-headline dir-ltr focus:ring-0">

                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-primary">
                                    <span class="text-lg material-symbols-outlined">call</span>
                                </div>

                                {{-- زر اختيار الدولة (معدل ليبقى ثابتاً في الجوالات الصغيرة) --}}
                                <button type="button" @click="branch.open = !branch.open" 
                                    class="flex gap-1.5 sm:gap-2 items-center px-2 sm:px-3 h-12 border-r transition-colors bg-slate-100 border-slate-200 hover:bg-slate-200 shrink-0 whitespace-nowrap">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400 shrink-0">expand_more</span>
                                    <span class="text-xs font-bold text-slate-700 dir-ltr shrink-0" x-text="branch.selectedCountry?.dial_code"></span>
                                    <template x-if="branch.selectedCountry?.svg">
                                        <div class="overflow-hidden w-5 h-auto rounded-sm shrink-0" x-html="branch.selectedCountry.svg"></div>
                                    </template>
                                </button>
                            </div>

                            {{-- قائمة الدول (السليكت الذي كان مختفياً) --}}
                            <div x-show="branch.open" @click.outside="branch.open = false" x-transition x-cloak
                                class="absolute top-[calc(100%+6px)] left-0 z-50 w-full bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden">
                                <div class="p-2 border-b border-slate-50">
                                    <input type="text" x-model="branch.search" placeholder="بحث عن دولة..."
                                        class="px-3 py-2 w-full text-xs rounded-lg outline-none bg-slate-50 focus:bg-slate-100 font-headline">
                                </div>
                                <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                    <template x-for="country in allCountries.filter(c => c.name.toLowerCase().includes(branch.search.toLowerCase()) || c.dial_code.includes(branch.search))" :key="country.code">
                                        <div @click="branch.selectedCountry = country; branch.open = false; branch.search = ''; updatePhone(index)"
                                            class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                            <div class="w-5 h-auto shrink-0" x-html="country.svg"></div>
                                            <span class="flex-grow text-xs font-medium truncate text-slate-700" x-text="country.name"></span>
                                            <span class="font-mono text-[10px] font-bold text-slate-500 dir-ltr" x-text="country.dial_code"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <input type="text" :name="`branches[${index}][address]`" x-model="branch.address" placeholder="العنوان"
                               class="w-full h-12 px-4 text-xs rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 font-headline">
                    </div>
                </div>
            </template>
        </div>

        <div class="px-2 pt-4">
            <button type="submit" :disabled="isSubmitting" class="w-full h-16 flex justify-center items-center gap-3 bg-primary text-white rounded-2xl shadow-xl font-black font-headline">
                <span x-text="isSubmitting ? 'جاري الحفظ...' : 'تحديث بيانات المكتب'"></span>
            </button>
        </div>
    </form>
</div>
@endsection