@extends('layouts.app')
@section('title', 'تعديل مكتب')

@section('Breadcrumb')
    <a href="{{ route('offices.unverified.index') }}" class="text-gray-500 transition-colors hover:text-primary">
        إدارة المكاتب غير الموثوقة
    </a>
    <span class="text-gray-400">/</span>
    <span class="font-medium text-on-surface dark:text-gray-100">تعديل مكتب</span>
@endsection

@section('content')
    <div class="pb-28 min-h-screen bg-slate-50/50 font-body" dir="rtl" x-data="{
        isSubmitting: false,
        activeItem: 0,

        branches: @js(old('branches', $office->branches->count() > 0
            ? $office->branches->map(fn($branch) => [
                'id' => $branch->id,
                'name' => $branch->name,
                'city' => $branch->city,
                'phone' => $branch->phone,
                'address' => $branch->address,
            ])->values()
            : [['name' => '', 'city' => '', 'phone' => '', 'address' => '']]
        )),

        errorIndices: @js(collect($errors->keys())->map(fn($key) => preg_match('/^branches\.(\d+)/', $key, $m) ? (int) $m[1] : null)->filter(fn($v) => !is_null($v))->unique()->values()),

        addBranch() {
            this.branches.push({ name: '', city: '', phone: '', address: '' });
            this.activeItem = this.branches.length - 1;

            this.$nextTick(() => {
                const cards = document.querySelectorAll('.branch-card');
                if (cards.length > 0) {
                    cards[cards.length - 1].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        },

        removeBranch(index) {
            if (this.branches.length > 1) {
                this.branches.splice(index, 1);

                if (this.activeItem === index) {
                    this.activeItem = Math.max(0, index - 1);
                } else if (this.activeItem > index) {
                    this.activeItem--;
                }
            }
        }
    }" x-init="if(errorIndices.length > 0) activeItem = errorIndices[0];">

        {{-- ================= الشريط العلوي العائم ================= --}}
        <div class="sticky top-0 z-[100] w-full bg-white/90 backdrop-blur-md border-b border-slate-100 shadow-sm">
            <div class="flex justify-between items-center px-4 mx-auto max-w-7xl h-20 sm:px-6 lg:px-8">

                {{-- جهة العنوان --}}
                <div class="flex gap-4 items-center min-w-0">
                    <a href="{{ route('offices.unverified.index') }}"
                        class="flex justify-center items-center w-10 h-10 bg-white rounded-full border shadow-sm transition-all border-slate-100 text-slate-500 hover:text-primary active:scale-90 shrink-0">
                        <span class="material-symbols-outlined text-[20px] mr-1">arrow_forward_ios</span>
                    </a>

                    <div class="min-w-0">
                        <h1 class="text-lg font-black tracking-tight truncate text-slate-800 md:text-xl font-headline">
                            تعديل بيانات المكتب
                        </h1>
                        <p class="text-[11px] font-bold text-slate-500 mt-0.5 truncate">
                            تحديث بيانات {{ $office->name }} والفروع التابعة له
                        </p>
                    </div>
                </div>

                {{-- جهة الأزرار والحالة --}}
                <div class="flex gap-4 items-center">
                    <div class="hidden flex-col items-end md:flex">
                        <span class="text-[10px] font-bold text-slate-400">عدد الفروع</span>
                        <span class="text-sm font-black text-primary" x-text="branches.length"></span>
                    </div>

                    <button type="button" @click="document.getElementById('officeForm').requestSubmit()"
                        :disabled="isSubmitting"
                        class="flex gap-2 justify-center items-center px-6 h-12 text-sm font-bold rounded-2xl shadow-lg transition-all active:scale-95 shadow-primary/20 disabled:cursor-not-allowed"
                        :class="isSubmitting ? 'bg-slate-100 text-slate-400' : 'bg-primary text-white hover:bg-primary-hover'">
                        <span x-show="isSubmitting" class="animate-spin material-symbols-outlined">progress_activity</span>
                        <span x-show="!isSubmitting" class="material-symbols-outlined">save</span>
                        <span x-text="isSubmitting ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="px-4 pt-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <form id="officeForm" action="{{ route('offices.update', $office->id) }}" method="POST"
                @submit="if($el.checkValidity()) { setTimeout(() => isSubmitting = true, 50); }">
                @csrf
                @method('PUT')

                <div class="space-y-8">

                    {{-- ================= 1. كارد البيانات الأساسية ================= --}}
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden max-w-3xl mx-auto w-full">
                        <div class="p-8">
                            <div class="flex flex-col items-center mb-8 text-center">
                                <div class="flex justify-center items-center mb-3 w-12 h-12 rounded-2xl bg-primary/10 text-primary">
                                    <span class="material-symbols-outlined text-[28px]">corporate_fare</span>
                                </div>

                                <h3 class="text-lg font-black text-slate-800 font-headline">
                                    البيانات الأساسية للمكتب
                                </h3>

                                <p class="mt-1 text-[11px] font-bold text-slate-400">
                                    يمكنك تعديل اسم المكتب من هنا
                                </p>
                            </div>

                            <div class="relative mx-auto max-w-md group">
                                <label for="office_name" class="block mb-2 text-[11px] font-bold text-slate-500 mr-2">
                                    اسم المكتب <span class="text-rose-500">*</span>
                                </label>

                                <input type="text" id="office_name" name="name"
                                    value="{{ old('name', $office->name) }}" required
                                    placeholder="مثال: مكتب الإنجاز السريع"
                                    class="pr-12 pl-4 w-full h-14 text-sm font-bold rounded-2xl border transition-all outline-none border-slate-200 focus:border-primary focus:ring-4 focus:ring-primary/10 bg-slate-50/50 focus:bg-white text-slate-700">

                                <span class="absolute right-4 bottom-4 transition-colors text-slate-400 material-symbols-outlined group-focus-within:text-primary">
                                    domain
                                </span>

                                @error('name')
                                    <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ================= 2. قسم الفروع ================= --}}
                    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-visible">
                        <div class="flex flex-col gap-4 justify-between items-start px-6 py-6 mb-6 border-b border-slate-50 sm:flex-row sm:items-center md:px-8">
                            <div class="flex gap-3 items-center">
                                <div class="flex justify-center items-center w-10 h-10 text-emerald-500 bg-emerald-50 rounded-xl">
                                    <span class="material-symbols-outlined">share_location</span>
                                </div>

                                <div>
                                    <h3 class="text-base font-black text-slate-800 font-headline">
                                        الفروع التابعة للمكتب
                                    </h3>
                                    <p class="mt-0.5 text-[11px] font-bold text-slate-400">
                                        عدّل بيانات الفروع أو أضف فرع جديد
                                    </p>
                                </div>
                            </div>

                            <button type="button" @click="addBranch()"
                                class="flex gap-2 justify-center items-center px-5 w-full h-11 text-xs font-bold text-white bg-emerald-500 rounded-xl shadow-md transition-all sm:w-auto hover:bg-emerald-600 active:scale-95 shadow-emerald-500/20">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                                إضافة فرع جديد
                            </button>
                        </div>

                        <div class="px-4 pb-8 grid grid-cols-1 lg:grid-cols-2 gap-6 items-start bg-slate-50/30 rounded-b-[2.5rem] pt-6 md:px-6">
                            <template x-for="(branch, index) in branches" :key="index">
                                <div :class="activeItem === index ? 'ring-2 ring-primary/40 shadow-xl z-30 scale-[1.02]' : 'border border-slate-100 z-10 opacity-90'"
                                    class="branch-card relative bg-white rounded-[2rem] transition-all duration-500 overflow-visible">

                                    {{-- هيدر الفرع --}}
                                    <div @click="activeItem = activeItem === index ? null : index"
                                        class="flex justify-between items-center p-5 cursor-pointer select-none">
                                        <div class="flex gap-4 items-center min-w-0">
                                            <div class="flex justify-center items-center w-10 h-10 text-xs font-black rounded-xl shadow-inner transition-colors shrink-0"
                                                :class="activeItem === index ? 'bg-primary text-white' : 'bg-slate-100 text-slate-400'"
                                                x-text="index + 1">
                                            </div>

                                            <div class="flex flex-col min-w-0">
                                                <h4 class="text-sm font-black truncate transition-colors"
                                                    :class="activeItem === index ? 'text-primary' : 'text-slate-700'"
                                                    x-text="branch.name ? branch.name : 'فرع جديد'"></h4>

                                                <p class="text-[10px] font-bold text-slate-400 truncate"
                                                    x-text="branch.city || 'يرجى تحديد المدينة'"></p>
                                            </div>
                                        </div>

                                        <div class="flex gap-2 items-center shrink-0">
                                            <button type="button" @click.stop="removeBranch(index)" x-show="branches.length > 1"
                                                class="flex justify-center items-center w-9 h-9 text-rose-400 rounded-xl transition-all hover:bg-rose-50 hover:text-rose-600">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>

                                            <span class="transition-transform duration-300 material-symbols-outlined text-slate-300"
                                                :class="activeItem === index ? 'rotate-180 text-primary' : ''">
                                                expand_more
                                            </span>
                                        </div>
                                    </div>

                                    {{-- محتوى الفرع --}}
                                    <div x-show="activeItem === index" x-collapse>
                                        <div class="p-6 pt-2 space-y-5 border-t border-slate-50">

                                            {{-- مهم: نحافظ على id الفرع القديم لو الكنترولر يستخدمه --}}
                                            <input type="hidden" :name="`branches[${index}][id]`" :value="branch.id || ''">

                                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                                                {{-- اسم الفرع --}}
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-black text-slate-400 mr-2 uppercase">
                                                        اسم الفرع <span class="text-rose-500">*</span>
                                                    </label>

                                                    <input type="text" x-model="branch.name"
                                                        :name="`branches[${index}][name]`" required
                                                        placeholder="مثلاً: فرع الحصبة"
                                                        class="px-4 w-full h-12 text-xs font-bold rounded-2xl border transition-all outline-none border-slate-200 focus:border-primary focus:ring-4 focus:ring-primary/10 bg-slate-50/50 focus:bg-white text-slate-700">
                                                </div>

                                                {{-- المدينة --}}
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-black text-slate-400 mr-2 uppercase">
                                                        المدينة <span class="text-rose-500">*</span>
                                                    </label>

                                                    <input type="text" x-model="branch.city"
                                                        :name="`branches[${index}][city]`" required
                                                        placeholder="صنعاء، عدن..."
                                                        class="px-4 w-full h-12 text-xs font-bold rounded-2xl border transition-all outline-none border-slate-200 focus:border-primary focus:ring-4 focus:ring-primary/10 bg-slate-50/50 focus:bg-white text-slate-700">
                                                </div>

                                                {{-- هاتف الفرع --}}
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-black text-slate-400 mr-2 uppercase">
                                                        رقم الهاتف
                                                    </label>

                                                    <div x-data="phoneComponent()"
                                                        x-init="initPhone(branch.phone)"
                                                        x-effect="branch.phone = localPhoneNumber ? ((selectedCountry?.dial_code.replace('+', '') || '') + localPhoneNumber) : ''"
                                                        class="relative z-[60]"
                                                        @click.outside="open = false">

                                                        <input type="hidden"
                                                            :name="`branches[${index}][phone]`"
                                                            :value="branch.phone">

                                                        <div class="flex overflow-hidden items-center w-full h-12 rounded-2xl border border-slate-200 focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10 bg-slate-50/50 focus-within:bg-white">

                                                            <input type="tel" x-model="localPhoneNumber" dir="ltr"
                                                                placeholder="7XXXXXXXX"
                                                                @input="localPhoneNumber = localPhoneNumber.replace(/[^0-9]/g, '')"
                                                                class="flex-grow px-4 h-full font-mono text-xs font-bold text-left bg-transparent border-none outline-none focus:ring-0 text-slate-700">

                                                            <button type="button" @click="open = !open"
                                                                class="flex gap-1 items-center px-3 h-full bg-white border-r transition-colors border-slate-200 hover:bg-slate-50">
                                                                <template x-if="selectedCountry">
                                                                    <div class="flex overflow-hidden items-center w-6 h-4 rounded-sm shadow-sm"
                                                                        x-html="selectedCountry.svg"></div>
                                                                </template>

                                                                <span class="material-symbols-outlined text-[16px] text-slate-400">
                                                                    expand_more
                                                                </span>
                                                            </button>
                                                        </div>

                                                        {{-- قائمة الدول --}}
                                                        <div x-show="open" x-transition x-cloak
                                                            class="absolute left-0 right-0 top-full mt-2 max-h-48 overflow-y-auto bg-white border border-slate-100 shadow-2xl rounded-2xl z-[100] p-1">
                                                            <template x-for="country in filteredCountries" :key="country.code">
                                                                <button type="button"
                                                                    @click="selectedCountry = country; open = false;"
                                                                    class="flex justify-between items-center px-4 py-3 w-full text-right rounded-xl transition-colors hover:bg-primary/5 group">
                                                                    <div class="flex gap-3 items-center">
                                                                        <div class="flex overflow-hidden items-center w-5 h-3.5 rounded-sm"
                                                                            x-html="country.svg"></div>
                                                                        <span class="text-xs font-bold text-slate-700 group-hover:text-primary"
                                                                            x-text="country.name"></span>
                                                                    </div>

                                                                    <span class="font-mono text-[10px] text-slate-400 group-hover:text-primary/70"
                                                                        x-text="country.dial_code"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- العنوان التفصيلي --}}
                                                <div class="space-y-1.5">
                                                    <label class="text-[10px] font-black text-slate-400 mr-2 uppercase">
                                                        العنوان التفصيلي
                                                    </label>

                                                    <input type="text" x-model="branch.address"
                                                        :name="`branches[${index}][address]`"
                                                        placeholder="الشارع، المعلم..."
                                                        class="px-4 w-full h-12 text-xs font-bold rounded-2xl border transition-all outline-none border-slate-200 focus:border-primary focus:ring-4 focus:ring-primary/10 bg-slate-50/50 focus:bg-white text-slate-700">
                                                </div>
                                            </div>

                                            <div class="flex justify-end pt-1">
                                                <button type="button" @click="activeItem = null"
                                                    class="hidden sm:flex gap-1 items-center px-4 h-10 text-[11px] font-bold rounded-xl text-slate-500 bg-slate-50 hover:bg-slate-100 transition-all">
                                                    <span class="material-symbols-outlined text-[16px]">keyboard_arrow_up</span>
                                                    إغلاق البطاقة
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- أزرار أسفل الصفحة للموبايل والاحتياط --}}
            
                </div>
            </form>
        </div>
    </div>

    {{-- جافا سكريبت المكونات --}}
    <script>
        function phoneComponent() {
            return {
                open: false,
                countries: @js(array_values(config('countries', []))),
                selectedCountry: null,
                localPhoneNumber: '',

                initPhone(phone) {
                    this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0];

                    if (phone) {
                        let p = String(phone);
                        const sorted = [...this.countries].sort((a, b) => b.dial_code.length - a.dial_code.length);

                        for (const c of sorted) {
                            const code = c.dial_code.replace('+', '');

                            if (p.startsWith(code)) {
                                this.selectedCountry = c;
                                this.localPhoneNumber = p.substring(code.length);
                                break;
                            }

                            if (p.startsWith('+' + code)) {
                                this.selectedCountry = c;
                                this.localPhoneNumber = p.substring(code.length + 1);
                                break;
                            }

                            if (p.startsWith('00' + code)) {
                                this.selectedCountry = c;
                                this.localPhoneNumber = p.substring(code.length + 2);
                                break;
                            }
                        }

                        if (!this.localPhoneNumber) {
                            this.localPhoneNumber = p.replace(/[^0-9]/g, '');
                        }
                    }
                },

                get filteredCountries() {
                    return this.countries;
                }
            }
        }
    </script>
@endsection