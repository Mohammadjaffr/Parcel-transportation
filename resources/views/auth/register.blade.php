<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>إنشاء حساب - مرسل</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Manrope:wght@200;400;600;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script src="{{ asset('assets/js/cdn.min.js') }}"></script>
    <script src="{{asset('assets/js/cdn.tailwindcss.js')}}"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "secondary-fixed-dim": "#ffb86e",
                        "on-tertiary-container": "#a88c69",
                        "on-surface-variant": "#44474c",
                        "tertiary-container": "#38260b",
                        "surface-container-high": "#d9eaff",
                        "secondary-fixed": "#ffdcbd",
                        "on-primary-fixed-variant": "#38485a",
                        "on-error": "#ffffff",
                        "on-primary-container": "#8192a7",
                        "surface-container": "#e4efff",
                        "inverse-on-surface": "#e9f1ff",
                        "on-primary-fixed": "#0b1d2d",
                        "on-background": "#0b1d2d",
                        "primary-fixed-dim": "#b7c8de",
                        "tertiary": "#211200",
                        "inverse-primary": "#b7c8de",
                        "on-secondary": "#ffffff",
                        "surface-bright": "#f7f9ff",
                        "surface": "#f7f9ff",
                        "inverse-surface": "#213243",
                        "surface-container-highest": "#d2e4fb",
                        "secondary": "#8a5100",
                        "surface-container-low": "#eef4ff",
                        "outline-variant": "#c4c6cd",
                        "background": "#f7f9ff",
                        "surface-variant": "#d2e4fb",
                        "error-container": "#ffdad6",
                        "on-secondary-container": "#673b00",
                        "secondary-container": "#fe9d20", 
                        "on-tertiary-fixed": "#281802",
                        "on-primary": "#ffffff",
                        "on-surface": "#0b1d2d",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#feddb5",
                        "primary": "#041627", 
                        "surface-dim": "#cadcf2",
                        "on-error-container": "#93000a",
                        "surface-container-lowest": "#ffffff",
                        "tertiary-fixed-dim": "#e1c29b",
                        "on-tertiary-fixed-variant": "#584326",
                        "on-secondary-fixed-variant": "#693c00",
                        "outline": "#74777d",
                        "on-secondary-fixed": "#2c1600",
                        "primary-container": "#1a2b3c",
                        "surface-tint": "#4f6073",
                        "primary-fixed": "#d2e4fb",
                        "error": "#ba1a1a"
                    },
                    fontFamily: {
                        "headline": ["Manrope", "Almarai"],
                        "body": ["Almarai", "Manrope"],
                        "label": ["Almarai", "Manrope"]
                    },
                    borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
                },
            },
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }

        body {
            font-family: 'Almarai', sans-serif;
            background-color: #f7f9ff;
        }

        .kinetic-gradient {
            background: linear-gradient(135deg, #041627 0%, #1a2b3c 100%);
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #d2e4fb;
            border-radius: 10px;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center overflow-x-hidden">

    <div class="fixed inset-0 z-0 opacity-10 pointer-events-none overflow-hidden">
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-secondary-container blur-[120px]">
        </div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-primary blur-[120px]"></div>
    </div>

    <main class="relative z-10 w-full max-w-6xl flex flex-col md:flex-row-reverse items-stretch h-[85vh] min-h-[750px] m-4 md:m-8 overflow-hidden rounded-xl shadow-[0_20px_60px_rgba(11,29,45,0.08)] bg-surface-container-lowest">

        <section class="hidden md:flex md:w-5/12 kinetic-gradient relative overflow-hidden p-12 flex-col justify-between items-start text-white">
            <div class="relative z-20 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-secondary-container flex items-center justify-center rounded-lg shadow-lg">
                        <span class="material-symbols-outlined text-primary text-3xl"
                            style="font-variation-settings: 'FILL' 1;">package_2</span>
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase font-headline">Mursal</span>
                </div>
                <h1 class="text-5xl font-extrabold leading-[1.1] font-headline tracking-tight">
                    مستقبل <span class="text-secondary-fixed-dim">الخدمات</span> اللوجستية يبدأ من هنا.
                </h1>
                <p class="text-on-primary-container text-lg leading-relaxed max-w-xs opacity-75">
                    انضم إلى شبكة مرسل العالمية وأدر شحناتك بدقة معمارية وتقنية متطورة.
                </p>
            </div>

            <div class="relative z-20 mt-auto">
                <div class="flex gap-2 items-center text-sm font-medium text-on-primary-container/80 mb-4">
                    <span class="w-8 h-[2px] bg-secondary-container"></span>
                    <span>موثوق به من قبل أكثر من 20 مكتب في اليمن</span>
                </div>
            </div>

            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[150%] h-[150%] opacity-10">
                <img class="w-full h-full object-cover mix-blend-overlay"
                    src="{{ asset('assets/image/abstract-bg.png') }}" alt="Background Pattern" />
            </div>
        </section>

        <section class="flex-1 p-8 md:p-12 flex flex-col bg-white overflow-y-auto custom-scrollbar">
            <div class="max-w-md mx-auto w-full py-4">

                <div class="md:hidden flex justify-center mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-primary flex items-center justify-center rounded overflow-hidden">
                            <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Mursal Logo" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-black text-primary uppercase font-headline">مُرسَل</span>
                    </div>
                </div>

                <div class="mb-8 text-right">
                    <h2 class="text-3xl font-bold text-on-background font-headline mb-2">إنشاء حساب جديد</h2>
                    <p class="text-slate-500 text-base font-body">ابدأ تجربتك اللوجستية المتطورة اليوم</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-5">
                        <div class="space-y-1.5">
                            <label for="name" class="block text-sm font-bold text-on-background/80 pr-1">الاسم الشخصي (المدير)</label>
                            <div class="relative group">
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                    class="w-full bg-surface-container-low border-0 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all"
                                    placeholder="أدخل اسمك بالكامل" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5" x-data="{
                            open: false,
                            search: '',
                            countries: @js(array_values(config('countries', []))),
                            selectedCountry: null,
                            localPhoneNumber: '',
                            fullPhone: '',
                            init() {
                                this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0] || { name: 'Yemen', code: 'YE', dial_code: '+967', svg: '' };
                                
                                // معالجة القيمة القديمة
                                let oldVal = '{{ old('phone') }}';
                                if(oldVal) {
                                    let dCode = this.selectedCountry?.dial_code.replace('+', '') || '967';
                                    if(oldVal.startsWith(dCode)) {
                                        this.localPhoneNumber = oldVal.substring(dCode.length);
                                    } else {
                                        this.localPhoneNumber = oldVal;
                                    }
                                }
                                
                                this.updateFullPhone();
                                this.$watch('localPhoneNumber', () => this.updateFullPhone());
                                this.$watch('selectedCountry', () => this.updateFullPhone());
                            },
                            updateFullPhone() {
                                this.fullPhone = this.localPhoneNumber ? (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhoneNumber : '';
                            },
                            get filteredCountries() {
                                if (this.search === '') return this.countries;
                                return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                            }
                        }">
                            <label for="phone_display" class="block text-sm font-bold text-on-background/80 pr-1">رقم الجوال (الشخصي)</label>
                            
                            <input type="hidden" name="phone" :value="fullPhone">
                            
                            <div class="relative">
                                <div class="relative group flex items-center bg-surface-container-low rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-secondary-container transition-all">
                                    <input id="phone_display" type="tel" x-model="localPhoneNumber" required inputmode="numeric"
                                        class="flex-1 bg-transparent border-0 px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-0 font-headline dir-ltr text-left"
                                        placeholder="7XXXXXXXX" />
                                    
                                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary pointer-events-none">
                                        <span class="material-symbols-outlined">smartphone</span>
                                    </div>
                                    
                                    <button type="button" @click="open = !open" 
                                        class="flex items-center gap-2 px-3 h-[52px] bg-slate-100 border-r border-slate-200 hover:bg-slate-200 transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                        <span class="text-sm font-bold text-on-surface font-headline dir-ltr" x-text="selectedCountry?.dial_code"></span>
                                        <template x-if="selectedCountry?.svg">
                                            <svg class="w-6 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                        </template>
                                    </button>
                                </div>
                                
                                <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                    class="absolute top-[calc(100%+6px)] left-0 z-50 w-full sm:w-[320px] bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden">
                                    <div class="p-2 border-b border-slate-50">
                                        <input type="text" x-model="search" placeholder="ابحث عن الدولة أو الرمز..."
                                            class="px-4 py-2.5 w-full text-sm outline-none bg-slate-50 hover:bg-slate-100 focus:bg-slate-100 rounded-xl transition-colors font-headline">
                                    </div>
                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false; search = ''"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                                <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                                <span class="flex-grow text-sm font-medium text-slate-700 font-headline truncate" x-text="country.name"></span>
                                                <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr" x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredCountries.length === 0" class="p-4 text-center text-sm font-medium text-slate-500">
                                            لا توجد نتائج مطابقة
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5" x-data="{ showPassword: false }">
                            <label for="password" class="block text-sm font-bold text-on-background/80 pr-1">كلمة المرور</label>
                            <div class="relative group">
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                    class="w-full bg-surface-container-low border-0 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all font-headline"
                                    placeholder="••••••••" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">lock</span>
                                </div>
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5" x-data="{ showConfirmPassword: false }">
                            <label for="password_confirmation" class="block text-sm font-bold text-on-background/80 pr-1">تأكيد كلمة المرور</label>
                            <div class="relative group">
                                <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required
                                    class="w-full bg-surface-container-low border-0 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all font-headline"
                                    placeholder="••••••••" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">lock_clock</span>
                                </div>
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined" x-text="showConfirmPassword ? 'visibility_off' : 'visibility'">visibility</span>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-2">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <span class="text-sm font-bold text-primary/60 bg-surface-bright px-3 py-1 rounded-full border border-slate-100 shadow-sm">بيانات المكتب / الشركة</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <div class="space-y-5 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                        <div class="space-y-1.5">
                            <label for="office_name" class="block text-sm font-bold text-on-background/80 pr-1">اسم المكتب أو الشركة</label>
                            <div class="relative group">
                                <input id="office_name" type="text" name="office_name" value="{{ old('office_name') }}" required
                                    class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all"
                                    placeholder="مثال: مؤسسة الأفق للشحن واللوجستيات" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">domain</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('office_name')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3 py-2">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <span class="text-sm font-bold text-primary/60 bg-surface-bright px-3 py-1 rounded-full border border-slate-100 shadow-sm">بيانات الفرع الرئيسي</span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <div class="space-y-5 bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                        <div class="space-y-1.5">
                            <label for="branch_name" class="block text-sm font-bold text-on-background/80 pr-1">اسم الفرع</label>
                            <div class="relative group">
                                <input id="branch_name" type="text" name="branch_name" value="{{ old('branch_name') }}" required
                                    class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all"
                                    placeholder="مثال: الإدارة العامة - فرع صنعاء" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">storefront</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('branch_name')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5">
                            <label for="branch_city" class="block text-sm font-bold text-on-background/80 pr-1">المدينة</label>
                            <div class="relative group">
                                <input id="branch_city" type="text" name="branch_city" value="{{ old('branch_city') }}" required
                                    class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all"
                                    placeholder="مثال: صنعاء، عدن، تعز..." />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">location_city</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('branch_city')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5">
                            <label for="branch_address" class="block text-sm font-bold text-on-background/80 pr-1">عنوان الفرع</label>
                            <div class="relative group">
                                <input id="branch_address" type="text" name="branch_address" value="{{ old('branch_address') }}" required
                                    class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all"
                                    placeholder="الحي، الشارع، المعلم البارز..." />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">location_on</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('branch_address')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5" x-data="{
                            open: false,
                            search: '',
                            countries: @js(array_values(config('countries', []))),
                            selectedCountry: null,
                            localPhoneNumber: '',
                            fullPhone: '',
                            init() {
                                this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0] || { name: 'Yemen', code: 'YE', dial_code: '+967', svg: '' };
                                
                                let oldVal = '{{ old('branch_phone') }}';
                                if(oldVal) {
                                    let dCode = this.selectedCountry?.dial_code.replace('+', '') || '967';
                                    if(oldVal.startsWith(dCode)) {
                                        this.localPhoneNumber = oldVal.substring(dCode.length);
                                    } else {
                                        this.localPhoneNumber = oldVal;
                                    }
                                }
                                
                                this.updateFullPhone();
                                this.$watch('localPhoneNumber', () => this.updateFullPhone());
                                this.$watch('selectedCountry', () => this.updateFullPhone());
                            },
                            updateFullPhone() {
                                this.fullPhone = this.localPhoneNumber ? (this.selectedCountry?.dial_code.replace('+', '') || '') + this.localPhoneNumber : '';
                            },
                            get filteredCountries() {
                                if (this.search === '') return this.countries;
                                return this.countries.filter(c => c.name.toLowerCase().includes(this.search.toLowerCase()) || c.dial_code.includes(this.search));
                            }
                        }">
                            <label for="branch_phone_display" class="block text-sm font-bold text-on-background/80 pr-1">رقم جوال / هاتف الفرع</label>
                            
                            <input type="hidden" name="branch_phone" :value="fullPhone">
                            
                            <div class="relative">
                                <div class="relative group flex items-center bg-surface-container-lowest border border-slate-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-secondary-container transition-all">
                                    <input id="branch_phone_display" type="tel" x-model="localPhoneNumber" required inputmode="numeric"
                                        class="flex-1 bg-transparent border-0 px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-0 font-headline dir-ltr text-left"
                                        placeholder="7XXXXXXXX" />
                                    
                                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary pointer-events-none">
                                        <span class="material-symbols-outlined">desk_phone</span>
                                    </div>
                                    
                                    <button type="button" @click="open = !open" 
                                        class="flex items-center gap-2 px-3 h-[52px] bg-slate-50 border-r border-slate-200 hover:bg-slate-100 transition-colors">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                        <span class="text-sm font-bold text-on-surface font-headline dir-ltr" x-text="selectedCountry?.dial_code"></span>
                                        <template x-if="selectedCountry?.svg">
                                            <svg class="w-6 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                        </template>
                                    </button>
                                </div>

                                <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                    class="absolute top-[calc(100%+6px)] left-0 z-50 w-full sm:w-[320px] bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden">
                                    <div class="p-2 border-b border-slate-50">
                                        <input type="text" x-model="search" placeholder="ابحث عن الدولة أو الرمز..."
                                            class="px-4 py-2.5 w-full text-sm outline-none bg-slate-50 hover:bg-slate-100 focus:bg-slate-100 rounded-xl transition-colors font-headline">
                                    </div>
                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false; search = ''"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                                <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                                <span class="flex-grow text-sm font-medium text-slate-700 font-headline truncate" x-text="country.name"></span>
                                                <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr" x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredCountries.length === 0" class="p-4 text-center text-sm font-medium text-slate-500">
                                            لا توجد نتائج مطابقة
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('branch_phone')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="w-full bg-secondary-container text-on-secondary-container font-extrabold py-4 rounded-lg shadow-[0_8px_20px_rgba(254,157,32,0.3)] hover:shadow-[0_12px_25px_rgba(254,157,32,0.4)] active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 group">
                            <span class="text-lg">إنشاء الحساب والشركة</span>
                            <span class="material-symbols-outlined transform group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center border-t border-slate-100 pt-6 pb-4">
                    <p class="text-slate-600 font-body">
                        لديك حساب بالفعل؟
                        <a class="text-secondary font-bold hover:underline underline-offset-4 mr-1"
                            href="{{ route('login') }}">تسجيل الدخول</a>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer class="mt-4 mb-8 text-slate-400 text-xs font-medium space-x-reverse space-x-6 flex items-center justify-center">
        <span class="font-headline tracking-widest">MURSAL LOGISTICS</span>
        <span>© 2026 جميع الحقوق محفوظة</span>
        <div class="flex gap-4">
            <a class="hover:text-primary transition-colors" href="#">سياسة الخصوصية</a>
            <a class="hover:text-primary transition-colors" href="#">الشروط والأحكام</a>
        </div>
    </footer>
</body>
</html>