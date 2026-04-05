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
                            <input type="hidden" :name="`branches[${index}][phone]`" x-model="branch.fullPhone">
                            <div class="relative group flex items-center bg-slate-50 rounded-xl overflow-hidden ring-1 ring-slate-100">
                                <input type="tel" x-model="branch.localPhone" @input="updatePhone(index)" required placeholder="7XXXXXXXX" class="flex-1 bg-transparent border-0 px-4 py-3 pr-11 text-sm dir-ltr text-left">
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400"><span class="material-symbols-outlined text-lg">call</span></div>
                                <button type="button" @click="branch.open = !branch.open" class="flex items-center gap-2 px-3 h-12 bg-slate-100">
                                    <span class="text-xs font-bold dir-ltr" x-text="branch.selectedCountry?.dial_code"></span>
                                    <div class="w-5 h-auto rounded-sm overflow-hidden" x-html="branch.selectedCountry?.svg"></div>
                                </button>
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