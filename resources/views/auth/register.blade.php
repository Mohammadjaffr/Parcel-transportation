<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
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
        [x-cloak] {
            display: none !important;
        }

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

<body class="flex overflow-x-hidden relative flex-col justify-center items-center p-4 min-h-screen sm:p-6 md:p-8">

    <!-- الخلفيات التجريدية -->
    <div class="overflow-hidden fixed inset-0 z-0 opacity-10 pointer-events-none">
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-secondary-container blur-[120px]">
        </div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-primary blur-[120px]"></div>
    </div>

    <!-- الحاوية الرئيسية -->
    <main
        class="relative z-10 w-full max-w-6xl flex flex-col md:flex-row-reverse items-stretch h-auto md:h-[85vh] min-h-[calc(100vh-8rem)] md:min-h-[750px] overflow-hidden rounded-2xl shadow-[0_20px_60px_rgba(11,29,45,0.08)] bg-white">

        <!-- القسم الأيمن (اللوجو والمعلومات) -->
        <section
            class="hidden overflow-hidden relative flex-col justify-between items-start p-8 text-white md:flex md:w-5/12 kinetic-gradient lg:p-12">
            <div class="relative z-20 space-y-6">
                <div class="flex gap-3 items-center">
                    <div
                        class="flex overflow-hidden justify-center items-center w-12 h-12 rounded-lg shadow-lg bg-secondary-container">
                        <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Mursal Logo"
                            class="object-contain w-10 h-10">
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase font-headline">مُرسَل</span>
                </div>
                <h1 class="text-4xl lg:text-5xl font-extrabold leading-[1.15] font-headline tracking-tight">
                    مستقبل <span class="text-secondary-fixed-dim">الخدمات</span> اللوجستية يبدأ من هنا.
                </h1>
                <p class="max-w-xs text-base leading-relaxed opacity-75 text-on-primary-container lg:text-lg">
                    انضم إلى شبكة مرسل العالمية وأدر شحناتك بدقة معمارية وتقنية متطورة.
                </p>
            </div>

            <div class="relative z-20 mt-auto">
                <div class="flex gap-2 items-center mb-4 text-sm font-medium text-on-primary-container/80">
                    <span class="w-8 h-[2px] bg-secondary-container"></span>
                    <span>موثوق به من قبل أكثر من 20 مكتب في اليمن</span>
                </div>
            </div>

            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[150%] h-[150%] opacity-10">
                <img class="object-cover w-full h-full mix-blend-overlay"
                    src="{{ asset('assets/image/abstract-bg.png') }}" alt="Background Pattern" />
            </div>
        </section>

        <!-- القسم الأيسر (نموذج التسجيل) -->
        <section class="flex overflow-y-auto flex-col flex-1 p-6 bg-white sm:p-8 md:p-10 lg:p-12 custom-scrollbar">
            <div class="py-2 mx-auto w-full max-w-md">

                <!-- لوجو الموبايل -->
                <div class="flex justify-center mb-6 md:hidden">
                    <div class="flex gap-2 items-center">
                        <div class="flex overflow-hidden justify-center items-center w-10 h-10 rounded shadow-md bg-primary">
                            <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Mursal Logo"
                                class="object-contain w-8 h-8">
                        </div>
                        <span class="text-2xl font-black uppercase text-primary font-headline">مُرسَل</span>
                    </div>
                </div>

                <div class="mb-6 text-right md:mb-8">
                    <h2 class="mb-2 text-2xl font-bold md:text-3xl text-on-background font-headline">إنشاء حساب جديد</h2>
                    <p class="text-sm text-slate-500 md:text-base font-body">ابدأ تجربتك اللوجستية المتطورة اليوم</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5 md:space-y-6">
                    @csrf

                    <!-- بيانات شخصية -->
                    <div class="space-y-4 md:space-y-5">
                        <div class="space-y-1.5">
                            <label for="name" class="block pr-1 text-sm font-bold text-on-background/80">الاسم الشخصي (المدير)</label>
                            <div class="relative group">
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                    class="px-4 py-3.5 pr-11 w-full text-base rounded-lg border-0 transition-all bg-surface-container-low sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container"
                                    placeholder="أدخل اسمك بالكامل" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">person</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>

                        <!-- رقم الجوال الشخصي -->
                        <div class="space-y-1.5" x-data="{
                            open: false,
                            search: '',
                            countries: @js(array_values(config('countries', []))),
                            selectedCountry: null,
                            localPhoneNumber: '',
                            fullPhone: '',
                            init() {
                                this.selectedCountry = this.countries.find(c => c.code === 'YE') || this.countries[0] || { name: 'Yemen', code: 'YE', dial_code: '+967', svg: '' };
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
                            <label for="phone_display" class="block pr-1 text-sm font-bold text-on-background/80">رقم الجوال (الشخصي)</label>
                            <input type="hidden" name="phone" :value="fullPhone">

                            <div class="relative">
                                <div class="flex overflow-hidden relative items-center rounded-lg transition-all group bg-surface-container-low focus-within:ring-2 focus-within:ring-secondary-container">
                                    <input id="phone_display" type="tel" x-model="localPhoneNumber" required
                                        inputmode="numeric"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-4 py-3.5 pr-11 w-full text-base text-left bg-transparent border-0 sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-0 font-headline dir-ltr"
                                        placeholder="7XXXXXXXX" />

                                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-secondary">
                                        <span class="material-symbols-outlined">smartphone</span>
                                    </div>

                                    <button type="button" @click="open = !open"
                                        class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 h-[52px] bg-slate-100 border-r border-slate-200 hover:bg-slate-200 transition-colors shrink-0">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                        <span class="text-sm font-bold text-on-surface font-headline dir-ltr" x-text="selectedCountry?.dial_code"></span>
                                        <template x-if="selectedCountry?.svg">
                                            <!-- أزلنا hidden sm:block ليبقى العلم ظاهراً دائماً -->
                                            <svg class="w-6 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                        </template>
                                    </button>
                                </div>

                                <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                    class="absolute top-[calc(100%+6px)] left-0 z-50 w-full max-w-[calc(100vw-3rem)] sm:w-[320px] bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden">
                                    <div class="p-2 border-b border-slate-50">
                                        <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                            class="px-4 py-2.5 w-full text-base rounded-xl transition-colors outline-none sm:text-sm bg-slate-50 hover:bg-slate-100 focus:bg-slate-100 font-headline">
                                    </div>
                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false; search = ''"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                                <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                                <span class="flex-grow text-sm font-medium truncate text-slate-700 font-headline" x-text="country.name"></span>
                                                <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr" x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredCountries.length === 0" class="p-4 text-sm font-medium text-center text-slate-500">لا توجد نتائج مطابقة</div>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5" x-data="{ showPassword: false }">
                            <label for="password" class="block pr-1 text-sm font-bold text-on-background/80">كلمة المرور</label>
                            <div class="relative group">
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                    class="px-4 py-3.5 pr-11 w-full text-base rounded-lg border-0 transition-all bg-surface-container-low sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container font-headline"
                                    placeholder="••••••••" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">lock</span>
                                </div>
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute left-3.5 top-1/2 transition-colors -translate-y-1/2 text-slate-400 hover:text-primary">
                                    <span class="material-symbols-outlined" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5" x-data="{ showConfirmPassword: false }">
                            <label for="password_confirmation" class="block pr-1 text-sm font-bold text-on-background/80">تأكيد كلمة المرور</label>
                            <div class="relative group">
                                <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required
                                    class="px-4 py-3.5 pr-11 w-full text-base rounded-lg border-0 transition-all bg-surface-container-low sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container font-headline"
                                    placeholder="••••••••" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">lock_clock</span>
                                </div>
                                <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute left-3.5 top-1/2 transition-colors -translate-y-1/2 text-slate-400 hover:text-primary">
                                    <span class="material-symbols-outlined" x-text="showConfirmPassword ? 'visibility_off' : 'visibility'">visibility</span>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>
                    </div>

                    <!-- فاصل بصري -->
                    <div class="flex gap-3 items-center py-1 md:py-2">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="px-3 py-1 text-xs font-bold text-center rounded-full border shadow-sm sm:text-sm text-primary/60 bg-surface-bright border-slate-100">
                            بيانات المكتب / الشركة
                        </span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <div class="p-3 space-y-4 rounded-xl border md:space-y-5 bg-slate-50/50 sm:p-4 border-slate-100">
                        <div class="space-y-1.5">
                            <label for="office_name" class="block pr-1 text-sm font-bold text-on-background/80">اسم المكتب أو الشركة</label>
                            <div class="relative group">
                                <input id="office_name" type="text" name="office_name" value="{{ old('office_name') }}" required
                                    class="px-4 py-3.5 pr-11 w-full text-base rounded-lg border transition-all bg-surface-container-lowest border-slate-200 sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container"
                                    placeholder="مثال: مؤسسة الأفق للشحن" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">domain</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('office_name')" class="mt-1" />
                        </div>
                    </div>

                    <!-- فاصل بصري -->
                    <div class="flex gap-3 items-center py-1 md:py-2">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="px-3 py-1 text-xs font-bold text-center rounded-full border shadow-sm sm:text-sm text-primary/60 bg-surface-bright border-slate-100">
                            بيانات الفرع الرئيسي
                        </span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    <div class="p-3 space-y-4 rounded-xl border md:space-y-5 bg-slate-50/50 sm:p-4 border-slate-100">
                        <div class="space-y-1.5">
                            <label for="branch_name" class="block pr-1 text-sm font-bold text-on-background/80">اسم الفرع</label>
                            <div class="relative group">
                                <input id="branch_name" type="text" name="branch_name" value="{{ old('branch_name') }}" required
                                    class="px-4 py-3.5 pr-11 w-full text-base rounded-lg border transition-all bg-surface-container-lowest border-slate-200 sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container"
                                    placeholder="مثال: الإدارة العامة" />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">storefront</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('branch_name')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5">
                            <label for="branch_city" class="block pr-1 text-sm font-bold text-on-background/80">المدينة</label>
                            <div class="relative group">
                                <input id="branch_city" type="text" name="branch_city" value="{{ old('branch_city') }}" required
                                    class="px-4 py-3.5 pr-11 w-full text-base rounded-lg border transition-all bg-surface-container-lowest border-slate-200 sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container"
                                    placeholder="صنعاء، عدن..." />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">location_city</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('branch_city')" class="mt-1" />
                        </div>

                        <div class="space-y-1.5">
                            <label for="branch_address" class="block pr-1 text-sm font-bold text-on-background/80">عنوان الفرع</label>
                            <div class="relative group">
                                <input id="branch_address" type="text" name="branch_address" value="{{ old('branch_address') }}" required
                                    class="px-4 py-3.5 pr-11 w-full text-base rounded-lg border transition-all bg-surface-container-lowest border-slate-200 sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container"
                                    placeholder="الحي، الشارع..." />
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                    <span class="material-symbols-outlined">location_on</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('branch_address')" class="mt-1" />
                        </div>

                        <!-- رقم هاتف الفرع -->
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
                            <label for="branch_phone_display" class="block pr-1 text-sm font-bold text-on-background/80">رقم جوال / هاتف الفرع</label>
                            <input type="hidden" name="branch_phone" :value="fullPhone">

                            <div class="relative">
                                <div class="flex overflow-hidden relative items-center rounded-lg border transition-all group bg-surface-container-lowest border-slate-200 focus-within:ring-2 focus-within:ring-secondary-container">
                                    <input id="branch_phone_display" type="tel" x-model="localPhoneNumber" required
                                        inputmode="numeric"
                                        :maxlength="selectedCountry?.code === 'YE' ? 9 : 15"
                                        class="flex-1 px-4 py-3.5 pr-11 w-full text-base text-left bg-transparent border-0 sm:text-sm text-on-background placeholder:text-slate-400 focus:ring-0 font-headline dir-ltr"
                                        placeholder="7XXXXXXXX" />

                                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-secondary">
                                        <span class="material-symbols-outlined">desk_phone</span>
                                    </div>

                                    <button type="button" @click="open = !open"
                                        class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 h-[52px] bg-slate-50 border-r border-slate-200 hover:bg-slate-100 transition-colors shrink-0">
                                        <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                                        <span class="text-sm font-bold text-on-surface font-headline dir-ltr" x-text="selectedCountry?.dial_code"></span>
                                        <template x-if="selectedCountry?.svg">
                                            <!-- أزلنا hidden sm:block ليبقى العلم ظاهراً دائماً -->
                                            <svg class="w-6 h-auto rounded-sm shadow-sm" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="selectedCountry.svg"></svg>
                                        </template>
                                    </button>
                                </div>

                                <div x-show="open" @click.outside="open = false" x-transition x-cloak
                                    class="absolute top-[calc(100%+6px)] left-0 z-50 w-full max-w-[calc(100vw-3rem)] sm:w-[320px] bg-white rounded-2xl border border-slate-100 shadow-2xl overflow-hidden">
                                    <div class="p-2 border-b border-slate-50">
                                        <input type="text" x-model="search" placeholder="ابحث عن الدولة..."
                                            class="px-4 py-2.5 w-full text-base rounded-xl transition-colors outline-none sm:text-sm bg-slate-50 hover:bg-slate-100 focus:bg-slate-100 font-headline">
                                    </div>
                                    <div class="overflow-y-auto max-h-48 custom-scrollbar">
                                        <template x-for="country in filteredCountries" :key="country.code">
                                            <div @click="selectedCountry = country; open = false; search = ''"
                                                class="flex gap-3 items-center p-3 px-4 transition-colors cursor-pointer hover:bg-primary/5">
                                                <svg class="w-5 h-auto rounded-sm shadow-sm shrink-0" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg" x-html="country.svg"></svg>
                                                <span class="flex-grow text-sm font-medium truncate text-slate-700 font-headline" x-text="country.name"></span>
                                                <span class="font-mono text-xs font-bold text-slate-500 shrink-0 dir-ltr" x-text="country.dial_code"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredCountries.length === 0" class="p-4 text-sm font-medium text-center text-slate-500">لا توجد نتائج مطابقة</div>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('branch_phone')" class="mt-1" />
                        </div>
                    </div>

                    <!-- زر الإرسال -->
                    <div class="pt-4 md:pt-6" x-data="{ isSubmitting: false }">
                        <button type="submit"  @click.prevent="
                            if($el.closest('form').checkValidity()) { 
                                isSubmitting = true; 
                                $el.closest('form').submit(); 
                            } else { 
                                $el.closest('form').reportValidity(); 
                            }
                        " :disabled="isSubmitting"
                            :class="{ 'opacity-75 cursor-not-allowed active:scale-100 hover:shadow-none': isSubmitting }"
                            class="w-full bg-secondary-container text-on-secondary-container font-extrabold py-3.5 md:py-4 rounded-lg shadow-[0_8px_20px_rgba(254,157,32,0.3)] hover:shadow-[0_12px_25px_rgba(254,157,32,0.4)] active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 group">

                            <div x-show="!isSubmitting" class="flex gap-3 items-center">
                                <span class="text-base md:text-lg">إنشاء الحساب والشركة</span>
                                <span class="transition-transform transform material-symbols-outlined group-hover:-translate-x-1">arrow_back</span>
                            </div>

                            <div x-show="isSubmitting" x-cloak class="flex gap-3 items-center">
                                <span class="text-base md:text-lg">جاري الإنشاء...</span>
                                <span class="animate-spin material-symbols-outlined">autorenew</span>
                            </div>

                        </button>
                    </div>
                </form>

                <div class="pt-5 pb-2 mt-6 text-center border-t md:mt-8 border-slate-100 md:pt-6">
                    <p class="text-sm text-slate-600 md:text-base font-body">
                        لديك حساب بالفعل؟
                        <a class="mr-1 font-bold transition-all text-secondary hover:underline underline-offset-4"
                            href="{{ route('login') }}">تسجيل الدخول</a>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- الفوتر -->
    <footer class="flex relative z-10 flex-col gap-3 justify-center items-center mt-6 text-xs font-medium text-center text-slate-400 md:text-sm sm:flex-row sm:gap-6">
        <span class="order-3 tracking-widest font-headline sm:order-1">MURSAL LOGISTICS</span>
        <span class="order-2 sm:order-2">© 2026 جميع الحقوق محفوظة</span>
        <div class="flex order-1 gap-4 sm:order-3">
            <a class="transition-colors hover:text-primary" href="#">سياسة الخصوصية</a>
            <a class="transition-colors hover:text-primary" href="#">الشروط والأحكام</a>
        </div>
    </footer>
</body>

</html>