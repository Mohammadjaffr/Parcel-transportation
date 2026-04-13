@extends('mobile.layouts.app')

@section('title', 'إعدادات الشركة')

@section('content')
    <x-modals.success-modal />
    <x-modals.error-modal />

    <div x-data="{ 
                        showAddBranchModal: false, 
                        showEditCompanyModal: false,
                        showEditBranchModal: false,
                        editBranchForm: { name: '', code: '', city: '', address: '', map_link: '', is_main: false },
                        editBranchAction: '',

                        openEditBranchModal(branch) {
                            this.editBranchForm = {
                                name: branch.name,
                                code: branch.code,
                                city: branch.city,
                                address: branch.address || '',
                                map_link: branch.map_link || '',
                                is_main: branch.is_main == 1
                            };
                            this.editBranchAction = '/branch/' + branch.id;
                            this.showEditBranchModal = true;
                            this.$dispatch('load-edit-phone', { phone: branch.phone });
                        }
                    }" class="flex flex-col gap-6 pb-24 min-h-screen">

        {{-- <div class="flex items-center gap-4 px-4 pt-4">
            <button onclick="history.back()"
                class="flex justify-center items-center w-10 h-10 rounded-xl bg-white shadow-sm text-slate-400 active:scale-90 transition-transform">
                <span class="material-symbols-outlined">arrow_forward</span>
            </button>
            <h1 class="text-xl font-black font-headline text-slate-800">إعدادات الشركة</h1>
        </div> --}}

        <div class="px-4">
            <div
                class="relative bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden pt-12 pb-6 px-6 text-center">

                <div class="absolute top-0 inset-x-0 h-20 bg-gradient-to-r from-primary/20 to-primary/5"></div>

                <button @click="showEditCompanyModal = true"
                    class="absolute top-4 left-4 w-8 h-8 flex items-center justify-center bg-white/50 backdrop-blur-md rounded-full text-slate-600 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[18px]">edit_square</span>
                </button>

                <div
                    class="relative mx-auto w-24 h-24 rounded-[1.5rem] bg-white shadow-lg border-4 border-white p-1 z-10 -mt-16 mb-4">
                    <div class="w-full h-full rounded-xl bg-slate-50 overflow-hidden flex items-center justify-center">
                        <img src="{{ $company->logo ? asset('storage/' . $company->logo) : asset('assets/image/icon_4K.png') }}"
                            alt="شعار الشركة" class="w-full h-full object-cover">
                    </div>
                </div>

                <h2 class="text-2xl font-black font-headline text-slate-800 mb-2 flex items-center justify-center gap-2">
                    {{ $company->name ?? 'اسم الشركة غير محدد' }}
                    <span
                        class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_6px_rgba(16,185,129,0.5)]"></span>
                </h2>

                <div class="flex items-center justify-center mb-6">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 text-amber-600 border border-amber-100/50 text-xs font-bold font-headline">
                        <span class="material-symbols-outlined text-[16px]"
                            style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                        الباقة الاحترافية
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                    <div class="bg-slate-50 rounded-2xl p-3 flex flex-col items-center">
                        <span class="material-symbols-outlined text-primary mb-1">domain</span>
                        <span class="text-lg font-black text-slate-800">
                            {{ $company->branches_count ?? 0 }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400">إجمالي الفروع</span>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-3 flex flex-col items-center">
                        <span class="material-symbols-outlined text-primary mb-1">group</span>
                        <span class="text-lg font-black text-slate-800">
                            {{ $company->users_count ?? 0 }}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400">موظفي الشركة</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-black font-headline text-lg text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">account_tree</span>
                    فروعي
                </h3>

                <button @click="showAddBranchModal = true"
                    class="flex items-center justify-center w-10 h-10 bg-primary/10 text-primary rounded-xl active:scale-90 transition-transform">
                    <span class="material-symbols-outlined">add</span>
                </button>
            </div>

            <div class="space-y-3">
                @if(isset($company))
                    @forelse($company->branches as $branch)
                        <div
                            class="flex items-center gap-4 p-4 bg-white rounded-[1.5rem] shadow-sm border border-slate-100 relative overflow-hidden">

                            @if($branch->is_main)
                                <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-primary"></div>
                            @endif

                            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                                <span class="material-symbols-outlined text-2xl">store</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-slate-800 font-headline text-sm truncate">{{ $branch->name }}</h4>
                                    @if($branch->is_main)
                                        <span
                                            class="bg-primary/10 text-primary text-[9px] px-1.5 py-0.5 rounded-md font-bold shrink-0">رئيسي</span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    {{ $branch->city ?? 'غير محدد' }}
                                    <span class="text-slate-300 px-1">|</span>
                                    <span class="font-mono text-[10px]">{{ $branch->code }}</span>
                                </p>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                @if($branch->map_link)
                                    <a href="{{ $branch->map_link }}" target="_blank"
                                        class="w-9 h-9 rounded-full text-emerald-500 hover:bg-emerald-50 flex items-center justify-center transition-colors active:scale-90">
                                        <span class="material-symbols-outlined text-[20px]">map</span>
                                    </a>
                                @endif
                                <button @click="openEditBranchModal({{ $branch }})"
                                    class="w-9 h-9 rounded-full text-slate-400 hover:text-primary hover:bg-primary/5 flex items-center justify-center transition-colors active:scale-90">
                                    <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div
                            class="py-10 flex flex-col items-center justify-center bg-white rounded-[2rem] border border-dashed border-slate-200">
                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">store_off</span>
                            <p class="text-sm font-bold text-slate-400">لا توجد فروع مسجلة حتى الآن</p>
                        </div>
                    @endforelse
                @else
                    <div
                        class="py-10 flex flex-col items-center justify-center bg-white rounded-[2rem] border border-dashed border-slate-200">
                        <p class="text-sm font-bold text-slate-400">لا تتوفر بيانات الشركة</p>
                    </div>
                @endif
            </div>
        </div>
        {{-- ================= بطاقة الشروط والأحكام (Company Terms & Conditions) ================= --}}
        <div class="px-4 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-black font-headline text-lg text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">gavel</span>
                    الشروط والأحكام
                </h3>
                <button @click="showEditCompanyModal = true"
                    class="flex items-center justify-center w-10 h-10 bg-slate-100 text-slate-500 rounded-xl active:scale-90 transition-all hover:text-primary hover:bg-primary/10">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                </button>
            </div>

            {{-- 💡 التنبيه الذكي للمستخدم --}}
            <div class="bg-blue-50/70 border border-blue-100/80 rounded-xl p-3 flex gap-2.5 items-start shadow-sm">
                <div class="bg-blue-100/50 text-blue-500 rounded-lg p-1 shrink-0">
                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                </div>
                <p class="text-[11px] font-bold text-blue-800 leading-relaxed pt-0.5">
                    <span class="font-black text-blue-600">ملاحظة:</span> سيتم طباعة هذه الشروط والأحكام تلقائياً في أسفل
                    جميع الفواتير المصدرة للعملاء.
                </p>
            </div>

            @if(isset($company) && !empty($company->terms_and_conditions) && count(array_filter($company->terms_and_conditions)) > 0)
                <div class="bg-white p-6 rounded-[1.5rem] shadow-sm border border-slate-100 relative overflow-hidden">
                    {{-- زخرفة خلفية توحي بالرسمية --}}
                    <div class="absolute top-0 right-0 w-16 h-16 bg-primary/5 rounded-bl-[3rem] -z-0"></div>
                    <span
                        class="material-symbols-outlined absolute top-4 right-4 text-[40px] text-primary/10 -z-0 -rotate-12">policy</span>

                    <div class="relative z-10">
                        <ul class="space-y-3">
                            @foreach(array_filter($company->terms_and_conditions) as $index => $term)
                                <li class="flex items-start gap-3 group">
                                    <div
                                        class="mt-0.5 w-5 h-5 rounded-md bg-slate-50 border border-slate-200 flex items-center justify-center text-[10px] font-black text-slate-400 group-hover:bg-primary group-hover:text-white group-hover:border-primary transition-colors shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-xs font-bold text-slate-600 leading-relaxed pt-0.5">
                                        {{ $term }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @else
                {{-- حالة عدم وجود شروط مسجلة --}}
                <div
                    class="py-10 flex flex-col items-center justify-center bg-white rounded-[1.5rem] border border-dashed border-slate-200">
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-3">
                        <span class="material-symbols-outlined text-[24px]">description</span>
                    </div>
                    <p class="text-sm font-bold text-slate-500">لم يتم إضافة شروط وأحكام للشركة</p>
                    <button @click="showEditCompanyModal = true"
                        class="mt-2 text-[11px] font-black text-primary hover:underline underline-offset-4">
                        أضف شروطك الآن
                    </button>
                </div>
            @endif
        </div>

        <div x-data="{ 
                            isSubmitting: false,
                            allCountries: @js(array_values(config('countries', []))),
                            selectedCountry: null,
                            localPhone: '',
                            fullPhone: '',
                            openCountry: false,
                            search: '',
                            init() {
                                this.selectedCountry = this.allCountries.find(c => c.code === 'YE') || this.allCountries[0];
                                this.$watch('localPhone', () => this.updatePhone());
                                this.$watch('selectedCountry', () => this.updatePhone());
                            },
                            updatePhone() {
                                let dCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';
                                this.fullPhone = this.localPhone ? dCode + this.localPhone : '';
                            }
                         }" x-show="showAddBranchModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div x-show="showAddBranchModal" x-transition.opacity.duration.300ms
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto"
                @click="showAddBranchModal = false"></div>

            <div x-show="showAddBranchModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-full"
                class="relative w-full max-w-lg bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 pointer-events-auto flex flex-col max-h-[90vh]">

                <div @click="showAddBranchModal = false"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full bg-slate-200 cursor-pointer active:scale-95 transition-transform">
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined"
                            style="font-variation-settings: 'FILL' 1;">add_business</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black font-headline text-slate-800">إضافة فرع جديد</h3>
                        <p class="text-xs font-bold text-slate-400">سيتم ربط هذا الفرع بشركتك مباشرة</p>
                    </div>
                </div>

                <form action="{{ route('branch.store') }}" method="POST" @submit="isSubmitting = true"
                    class="overflow-y-auto custom-scrollbar pr-2 space-y-4">
                    @csrf

                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl ring-1 ring-slate-100">
                        <div>
                            <label class="text-sm font-bold text-slate-700 font-headline">الفرع الرئيسي</label>
                            <p class="text-[10px] text-slate-500 mt-0.5">تعيين هذا الفرع كمركز رئيسي للشركة</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_main" value="1" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">اسم الفرع <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="مثال: فرع الرياض الرئيسي"
                            class="w-full h-12 px-4 text-sm rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">كود الفرع (مميز)
                                <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" required placeholder="مثال: RUH-01" dir="ltr"
                                class="w-full h-12 px-4 text-sm text-left rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline uppercase">
                        </div>
                        <div>
                            <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">المدينة <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="city" required placeholder="مثال: الرياض"
                                class="w-full h-12 px-4 text-sm rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                        </div>
                    </div>

                    <div class="relative z-40">
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">رقم هاتف
                            الفرع</label>
                        <input type="hidden" name="phone" x-model="fullPhone">

                        <div
                            class="relative group flex items-center bg-slate-50 rounded-xl overflow-hidden ring-1 ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                            <input type="tel" x-model="localPhone" placeholder="7XXXXXXXX" inputmode="numeric"
                                class="flex-1 bg-transparent border-0 px-4 py-3 pr-11 text-sm text-left font-headline dir-ltr focus:ring-0">

                            <div
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary pointer-events-none">
                                <span class="material-symbols-outlined text-lg">call</span>
                            </div>

                            <button type="button" @click="openCountry = !openCountry"
                                class="flex items-center gap-2 px-3 h-12 bg-slate-100 border-r border-slate-200 hover:bg-slate-200 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                <span class="text-xs font-bold text-slate-700 dir-ltr"
                                    x-text="selectedCountry?.dial_code"></span>
                                <template x-if="selectedCountry?.svg">
                                    <div class="w-5 h-auto rounded-sm overflow-hidden" x-html="selectedCountry.svg"></div>
                                </template>
                            </button>
                        </div>

                        <div x-show="openCountry" @click.outside="openCountry = false" x-transition x-cloak
                            class="absolute top-[calc(100%+6px)] left-0 w-full bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden">
                            <div class="p-2 border-b border-slate-50">
                                <input type="text" x-model="search" placeholder="بحث عن دولة..."
                                    class="px-3 py-2 w-full text-xs outline-none bg-slate-50 focus:bg-slate-100 rounded-lg font-headline">
                            </div>
                            <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                <template
                                    x-for="country in allCountries.filter(c => c.name.toLowerCase().includes(search.toLowerCase()) || c.dial_code.includes(search))"
                                    :key="country.code">
                                    <div @click="selectedCountry = country; openCountry = false; search = ''"
                                        class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                        <div class="w-5 h-auto shrink-0" x-html="country.svg"></div>
                                        <span class="flex-grow text-xs font-medium text-slate-700 truncate"
                                            x-text="country.name"></span>
                                        <span class="font-mono text-[10px] font-bold text-slate-500 dir-ltr"
                                            x-text="country.dial_code"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">العنوان
                            التفصيلي</label>
                        <input type="text" name="address" placeholder="الشارع، الحي، المبنى..."
                            class="w-full h-12 px-4 text-sm rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                    </div>

                    <div>
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">رابط موقع الفرع
                            (Google Maps)</label>
                        <div class="relative">
                            <input type="url" name="map_link" placeholder="http://googleusercontent.com/maps..." dir="ltr"
                                class="w-full h-12 pr-11 pl-4 text-sm text-left rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-lg">map</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <button type="submit" :disabled="isSubmitting"
                            class="w-full h-14 flex justify-center items-center gap-3 bg-primary text-white rounded-xl shadow-lg shadow-primary/30 font-black font-headline active:scale-95 disabled:opacity-70 transition-all">
                            <span x-show="!isSubmitting" class="material-symbols-outlined text-xl">save</span>
                            <span x-show="isSubmitting"
                                class="animate-spin material-symbols-outlined text-xl">progress_activity</span>
                            <span x-text="isSubmitting ? 'جاري الحفظ...' : 'اعتماد الفرع الجديد'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-data="{ 
                isSubmittingCompany: false, 
                terms: {{ json_encode($company->terms_and_conditions ?? ['']) }} 
            }" x-show="showEditCompanyModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div x-show="showEditCompanyModal" x-transition.opacity.duration.300ms
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto"
                @click="showEditCompanyModal = false"></div>

            <div x-show="showEditCompanyModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-full"
                class="relative w-full max-w-lg bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 pointer-events-auto flex flex-col max-h-[90vh]">

                <div @click="showEditCompanyModal = false"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full bg-slate-200 cursor-pointer active:scale-95 transition-transform">
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined"
                            style="font-variation-settings: 'FILL' 1;">edit_document</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات الشركة</h3>
                        <p class="text-xs font-bold text-slate-400">تحديث المعلومات الأساسية الخاصة بشركتك</p>
                    </div>
                </div>

                <form action="{{ route('app.update') }}" method="POST" enctype="multipart/form-data"
                    @submit="isSubmittingCompany = true" class="overflow-y-auto custom-scrollbar pr-2 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">شعار الشركة
                            (اختياري)</label>
                        <input type="file" name="logo" accept="image/*"
                            class="w-full h-12 px-4 py-3 text-sm rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                    </div>

                    <div>
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">اسم الشركة <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="name" required value="{{ $company->name }}"
                            placeholder="مثال: شركة النقل السريع"
                            class="w-full h-12 px-4 text-sm rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">رقم هاتف
                                الشركة</label>
                            <input type="tel" name="phone" value="{{ $company->phone }}"
                                placeholder="الرقم الموحد أو المحمول" dir="ltr"
                                class="w-full h-12 px-4 text-sm text-left rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                        </div>

                        <div>
                            <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">البريد
                                الإلكتروني</label>
                            <input type="email" name="email" value="{{ $company->email }}" placeholder="info@company.com"
                                dir="ltr"
                                class="w-full h-12 px-4 text-sm text-left rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                        </div>
                    </div>
                    {{-- ================= الشروط والأحكام الديناميكية ================= --}}
                    <div class="pt-2 border-t border-slate-100 mt-2">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block px-1 text-xs font-bold text-slate-600 font-headline">الشروط والأحكام
                                للشركة</label>
                            {{-- زر إضافة شرط جديد --}}
                            <button type="button" @click="terms.push('')"
                                class="flex items-center gap-1 px-2 py-1 bg-primary/10 text-primary rounded-lg text-[10px] font-bold hover:bg-primary/20 transition-colors">
                                <span class="material-symbols-outlined text-[14px]">add</span>
                                إضافة شرط
                            </button>
                        </div>

                        <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                            <template x-for="(term, index) in terms" :key="index">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 relative">
                                        <input type="text" x-model="terms[index]" name="terms_and_conditions[]"
                                            placeholder="اكتب الشرط هنا..."
                                            class="w-full h-10 px-3 text-xs rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                                    </div>
                                    {{-- زر الحذف --}}
                                    <button type="button" @click="terms.splice(index, 1)"
                                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-100 transition-colors shrink-0">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </template>

                            {{-- رسالة إذا كانت القائمة فارغة --}}
                            <div x-show="terms.length === 0"
                                class="text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                <p class="text-xs text-slate-400 font-bold">لا توجد شروط مسجلة.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <button type="submit" :disabled="isSubmittingCompany"
                            class="w-full h-14 flex justify-center items-center gap-3 bg-primary text-white rounded-xl shadow-lg shadow-primary/30 font-black font-headline active:scale-95 disabled:opacity-70 transition-all">
                            <span x-show="!isSubmittingCompany" class="material-symbols-outlined text-xl">save</span>
                            <span x-show="isSubmittingCompany"
                                class="animate-spin material-symbols-outlined text-xl">progress_activity</span>
                            <span x-text="isSubmittingCompany ? 'جاري الحفظ...' : 'حفظ التعديلات'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-data="{ 
                            isSubmittingEdit: false,
                            allCountries: @js(array_values(config('countries', []))),
                            selectedCountry: null,
                            localPhone: '',
                            fullPhone: '',
                            openCountry: false,
                            search: '',
                            init() {
                                this.$watch('localPhone', () => this.updatePhone());
                                this.$watch('selectedCountry', () => this.updatePhone());
                            },
                            updatePhone() {
                                let dCode = this.selectedCountry ? this.selectedCountry.dial_code.replace('+', '') : '';
                                this.fullPhone = this.localPhone ? dCode + this.localPhone : '';
                            }
                         }" @load-edit-phone.window="
                            let phone = $event.detail.phone || '';
                            if(!phone) {
                                selectedCountry = allCountries.find(c => c.code === 'YE') || allCountries[0];
                                localPhone = '';
                            } else {
                                let matched = allCountries.find(c => phone.startsWith(c.dial_code.replace('+', '')));
                                if(matched) {
                                    selectedCountry = matched;
                                    localPhone = phone.substring(matched.dial_code.replace('+', '').length);
                                } else {
                                    selectedCountry = allCountries.find(c => c.code === 'YE') || allCountries[0];
                                    localPhone = phone;
                                }
                            }
                            updatePhone();
                         " x-show="showEditBranchModal" x-cloak
            class="fixed inset-0 z-[99999] flex items-end justify-center pointer-events-none">

            <div x-show="showEditBranchModal" x-transition.opacity.duration.300ms
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] pointer-events-auto"
                @click="showEditBranchModal = false"></div>

            <div x-show="showEditBranchModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-full"
                class="relative w-full max-w-lg bg-white rounded-t-[2.5rem] shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 pb-12 pointer-events-auto flex flex-col max-h-[90vh]">

                <div @click="showEditBranchModal = false"
                    class="mx-auto mb-6 w-12 h-1.5 rounded-full bg-slate-200 cursor-pointer active:scale-95 transition-transform">
                </div>

                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined"
                            style="font-variation-settings: 'FILL' 1;">edit_square</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-black font-headline text-slate-800">تعديل بيانات الفرع</h3>
                        <p class="text-xs font-bold text-slate-400" x-text="'تحديث معلومات: ' + editBranchForm.name"></p>
                    </div>
                </div>

                <form :action="editBranchAction" method="POST" @submit="isSubmittingEdit = true"
                    class="overflow-y-auto custom-scrollbar pr-2 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl ring-1 ring-slate-100">
                        <div>
                            <label class="text-sm font-bold text-slate-700 font-headline">الفرع الرئيسي</label>
                            <p class="text-[10px] text-slate-500 mt-0.5">تعيين هذا الفرع كمركز رئيسي للشركة</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_main" value="0">
                            <input type="checkbox" name="is_main" value="1" x-model="editBranchForm.is_main"
                                class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">اسم الفرع <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editBranchForm.name" required placeholder="مثال: فرع الرياض"
                            class="w-full h-12 px-4 text-sm rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">كود الفرع (مميز)
                                <span class="text-rose-500">*</span></label>
                            <input type="text" name="code" x-model="editBranchForm.code" required dir="ltr"
                                class="w-full h-12 px-4 text-sm text-left rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline uppercase">
                        </div>
                        <div>
                            <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">المدينة <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="city" x-model="editBranchForm.city" required
                                class="w-full h-12 px-4 text-sm rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                        </div>
                    </div>

                    <div class="relative z-40">
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">رقم هاتف
                            الفرع</label>
                        <input type="hidden" name="phone" x-model="fullPhone">

                        <div
                            class="relative group flex items-center bg-slate-50 rounded-xl overflow-hidden ring-1 ring-slate-100 focus-within:ring-2 focus-within:ring-primary/20 transition-all">
                            <input type="tel" x-model="localPhone" placeholder="7XXXXXXXX" inputmode="numeric"
                                class="flex-1 bg-transparent border-0 px-4 py-3 pr-11 text-sm text-left font-headline dir-ltr focus:ring-0">

                            <div
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary pointer-events-none">
                                <span class="material-symbols-outlined text-lg">call</span>
                            </div>

                            <button type="button" @click="openCountry = !openCountry"
                                class="flex items-center gap-2 px-3 h-12 bg-slate-100 border-r border-slate-200 hover:bg-slate-200 transition-colors shrink-0">
                                <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                <span class="text-xs font-bold text-slate-700 dir-ltr"
                                    x-text="selectedCountry?.dial_code"></span>
                                <template x-if="selectedCountry?.svg">
                                    <div class="w-5 h-auto rounded-sm overflow-hidden" x-html="selectedCountry.svg"></div>
                                </template>
                            </button>
                        </div>

                        <div x-show="openCountry" @click.outside="openCountry = false" x-transition x-cloak
                            class="absolute top-[calc(100%+6px)] left-0 w-full bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden">
                            <div class="p-2 border-b border-slate-50">
                                <input type="text" x-model="search" placeholder="بحث عن دولة..."
                                    class="px-3 py-2 w-full text-xs outline-none bg-slate-50 focus:bg-slate-100 rounded-lg font-headline">
                            </div>
                            <div class="overflow-y-auto max-h-40 custom-scrollbar">
                                <template
                                    x-for="country in allCountries.filter(c => c.name.toLowerCase().includes(search.toLowerCase()) || c.dial_code.includes(search))"
                                    :key="country.code">
                                    <div @click="selectedCountry = country; openCountry = false; search = ''"
                                        class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                        <div class="w-5 h-auto shrink-0" x-html="country.svg"></div>
                                        <span class="flex-grow text-xs font-medium text-slate-700 truncate"
                                            x-text="country.name"></span>
                                        <span class="font-mono text-[10px] font-bold text-slate-500 dir-ltr"
                                            x-text="country.dial_code"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">العنوان
                            التفصيلي</label>
                        <input type="text" name="address" x-model="editBranchForm.address"
                            placeholder="الشارع، الحي، المبنى..."
                            class="w-full h-12 px-4 text-sm rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                    </div>

                    <div>
                        <label class="block px-1 mb-1.5 text-xs font-bold text-slate-600 font-headline">رابط موقع الفرع
                            (Google Maps)</label>
                        <div class="relative">
                            <input type="url" name="map_link" x-model="editBranchForm.map_link"
                                placeholder="http://googleusercontent.com..." dir="ltr"
                                class="w-full h-12 pr-11 pl-4 text-sm text-left rounded-xl border-none ring-1 ring-slate-100 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all font-headline">
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <span class="material-symbols-outlined text-lg">map</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <button type="submit" :disabled="isSubmittingEdit"
                            class="w-full h-14 flex justify-center items-center gap-3 bg-blue-500 text-white rounded-xl shadow-lg shadow-blue-500/30 font-black font-headline active:scale-95 disabled:opacity-70 transition-all">
                            <span x-show="!isSubmittingEdit" class="material-symbols-outlined text-xl">update</span>
                            <span x-show="isSubmittingEdit"
                                class="animate-spin material-symbols-outlined text-xl">progress_activity</span>
                            <span x-text="isSubmittingEdit ? 'جاري التحديث...' : 'حفظ التعديلات'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection