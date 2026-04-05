@extends('mobile.layouts.app')

@section('title', 'إضافة مكتب جديد')

@section('content')
    <div class="flex flex-col gap-6 pb-24 min-h-screen" 
         x-data="{ 
            isSubmitting: false,
            {{-- جلب قائمة الدول من الإعدادات --}}
            allCountries: @js(array_values(config('countries', []))),
            
            {{-- دالة لإنشاء كائن فرع جديد ببياناته الخاصة --}}
            newBranch() {
                let defaultCountry = this.allCountries.find(c => c.code === 'YE') || this.allCountries[0];
                return { 
                    name: '', 
                    city: '', 
                    address: '', 
                    localPhone: '', 
                    fullPhone: '',
                    selectedCountry: defaultCountry,
                    open: false,
                    search: ''
                };
            },
            branches: [],
            init() {
                {{-- إضافة أول فرع تلقائياً عند تحميل الصفحة --}}
                this.branches.push(this.newBranch());
            },
            addBranch() {
                this.branches.push(this.newBranch());
            },
            removeBranch(index) {
                if (this.branches.length > 1) {
                    this.branches.splice(index, 1);
                }
            },
            {{-- دالة لتحديث الرقم الكامل (المفتاح + الرقم المحلي) --}}
            updatePhone(index) {
                let b = this.branches[index];
                let dCode = b.selectedCountry.dial_code.replace('+', '');
                b.fullPhone = b.localPhone ? dCode + b.localPhone : '';
            }
         }">

        <div class="flex items-center gap-4 px-2">
            <a href="{{ route('offices.unverified.index') }}"
                class="flex justify-center items-center w-10 h-10 rounded-xl bg-white shadow-sm text-slate-400">
                <span class="material-symbols-outlined">arrow_forward</span>
            </a>
            <h1 class="text-xl font-black font-headline text-slate-800">إضافة مكتب جديد</h1>
        </div>

        <form action="{{ route('offices.store') }}" method="POST" @submit="isSubmitting = true" class="space-y-6">
            @csrf

            <div class="px-2">
                <div class="p-6 bg-white rounded-[2rem] shadow-sm border border-slate-100 space-y-4">
                    <div class="flex items-center gap-2 mb-2 text-primary">
                        <span class="material-symbols-outlined">domain</span>
                        <span class="font-bold font-headline">بيانات المكتب</span>
                    </div>

                    <div>
                        <label class="block px-1 mb-2 text-sm font-bold text-slate-600 font-headline">اسم المكتب الرئيسي
                            <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="مثلاً: شركة الصقر للمقاولات"
                            class="w-full h-14 px-4 text-sm rounded-2xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                        @error('name') <p class="mt-1 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="px-2 space-y-4">
                <div class="flex justify-between items-center px-2">
                    <div class="flex items-center gap-2 text-slate-500">
                        <span class="material-symbols-outlined text-primary">location_city</span>
                        <span class="font-bold font-headline">الفروع التابعة للمكتب</span>
                    </div>
                    <button type="button" @click="addBranch()"
                        class="flex items-center gap-1 text-xs font-bold text-primary bg-primary/5 px-3 py-2 rounded-lg active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-sm">add</span> إضافة فرع
                    </button>
                </div>

                <template x-for="(branch, index) in branches" :key="index">
                    <div class="p-5 bg-white rounded-[2rem] shadow-sm border border-slate-100 space-y-4 relative">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-widest text-primary bg-primary/5 px-2 py-0.5 rounded"
                                x-text="'الفرع #' + (index + 1)"></span>
                            <button type="button" x-show="branches.length > 1" @click="removeBranch(index)"
                                class="text-rose-400 hover:text-rose-600 active:scale-90 transition-transform">
                                <span class="material-symbols-outlined text-xl">delete_sweep</span>
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" :name="`branches[${index}][name]`" x-model="branch.name" required
                                    placeholder="اسم الفرع"
                                    class="w-full h-12 px-4 text-xs rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">

                                <input type="text" :name="`branches[${index}][city]`" x-model="branch.city" required
                                    placeholder="المدينة"
                                    class="w-full h-12 px-4 text-xs rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                            </div>

                            <div class="relative">
                                {{-- الحقل المخفي الذي سيتم إرساله للسيرفر --}}
                                <input type="hidden" :name="`branches[${index}][phone]`" x-model="branch.fullPhone">

                                <div class="relative group flex items-center bg-slate-50 rounded-xl overflow-hidden ring-1 ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                                    {{-- حقل إدخال الرقم المحلي --}}
                                    <input type="tel" x-model="branch.localPhone" @input="updatePhone(index)" required placeholder="7XXXXXXXX" inputmode="numeric"
                                        class="flex-1 bg-transparent border-0 px-4 py-3 pr-11 text-sm text-left font-headline dir-ltr focus:ring-0">

                                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary pointer-events-none">
                                        <span class="material-symbols-outlined text-lg">call</span>
                                    </div>

                                    {{-- زر اختيار الدولة --}}
                                    <button type="button" @click="branch.open = !branch.open" 
                                        class="flex items-center gap-2 px-3 h-12 bg-slate-100 border-r border-slate-200 hover:bg-slate-200 transition-colors shrink-0">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                        <span class="text-xs font-bold text-slate-700 dir-ltr" x-text="branch.selectedCountry?.dial_code"></span>
                                        <template x-if="branch.selectedCountry?.svg">
                                            <div class="w-5 h-auto rounded-sm overflow-hidden" x-html="branch.selectedCountry.svg"></div>
                                        </template>
                                    </button>
                                </div>

                                {{-- قائمة الدول --}}
                                <div x-show="branch.open" @click.outside="branch.open = false" x-transition x-cloak
                                    class="absolute top-[calc(100%+6px)] left-0 z-50 w-full bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden">
                                    <div class="p-2 border-b border-slate-50">
                                        <input type="text" x-model="branch.search" placeholder="بحث عن دولة..."
                                            class="px-3 py-2 w-full text-xs outline-none bg-slate-50 focus:bg-slate-100 rounded-lg font-headline">
                                    </div>
                                    <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                        <template x-for="country in allCountries.filter(c => c.name.toLowerCase().includes(branch.search.toLowerCase()) || c.dial_code.includes(branch.search))" :key="country.code">
                                            <div @click="branch.selectedCountry = country; branch.open = false; branch.search = ''; updatePhone(index)"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                                <div class="w-5 h-auto shrink-0" x-html="country.svg"></div>
                                                <span class="flex-grow text-xs font-medium text-slate-700 truncate" x-text="country.name"></span>
                                                <span class="font-mono text-[10px] font-bold text-slate-500 dir-ltr" x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <input type="text" :name="`branches[${index}][address]`" x-model="branch.address"
                                    placeholder="العنوان التفصيلي"
                                    class="w-full h-12 px-4 text-xs rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-2 pt-4">
                <button type="submit" :disabled="isSubmitting"
                    class="w-full h-16 flex justify-center items-center gap-3 bg-primary text-white rounded-2xl shadow-xl shadow-primary/20 font-black font-headline active:scale-95 disabled:opacity-70 transition-all">
                    <span x-show="!isSubmitting" class="material-symbols-outlined text-2xl">check_circle</span>
                    <span x-show="isSubmitting" class="animate-spin material-symbols-outlined">progress_activity</span>
                    <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ المكتب وفروعه'"></span>
                </button>
            </div>
        </form>
    </div>
@endsection