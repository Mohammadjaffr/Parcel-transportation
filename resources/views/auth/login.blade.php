<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>تسجيل الدخول - مرسل</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Manrope:wght@200;400;600;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

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
                        "secondary-container": "#fe9d20", // اللون البرتقالي الأساسي
                        "on-tertiary-fixed": "#281802",
                        "on-primary": "#ffffff",
                        "on-surface": "#0b1d2d",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#feddb5",
                        "primary": "#041627", // اللون الكحلي الغامق
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
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center overflow-x-hidden">

    <div class="fixed inset-0 z-0 opacity-10 pointer-events-none overflow-hidden">
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-secondary-container blur-[120px]">
        </div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-primary blur-[120px]"></div>
    </div>

    <main
        class="relative z-10 w-full max-w-6xl flex flex-col md:flex-row-reverse items-stretch min-h-[750px] m-4 md:m-8 overflow-hidden rounded-xl shadow-[0_20px_60px_rgba(11,29,45,0.08)] bg-surface-container-lowest">

        <section
            class="hidden md:flex md:w-5/12 kinetic-gradient relative overflow-hidden p-12 flex-col justify-between items-start text-white">
            <div class="relative z-20 space-y-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-12 h-12 bg-secondary-container flex items-center justify-center rounded-lg shadow-lg overflow-hidden">
                        <img src="{{ asset('assets/image/icon_without_bg.png') }}" al t="Mursal Logo"
                            class="w-10 h-10 object-contain">
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase font-headline">مُرسَل</span>
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
                    <span>موثوق به من قبل أكثر من 500 شركة عالمية</span>
                </div>
            </div>

            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[150%] h-[150%] opacity-10">
                <img class="w-full h-full object-cover mix-blend-overlay"
                    src="{{ asset('assets/image/abstract-bg.png') }}" alt="Background Pattern" />
            </div>
        </section>

        <section class="flex-1 p-8 md:p-16 flex flex-col justify-center bg-white">
            <div class="max-w-md mx-auto w-full">

                <div class="md:hidden flex justify-center mb-10">
                    
                    <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-primary flex items-center justify-center rounded">
                        <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Mursal Logo"
                            class="w-10 h-10 object-contain">
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase font-headline">مُرسَل</span>
                </div>
                </div>

                <div class="mb-10 text-right">
                    <h2 class="text-3xl font-bold text-on-background font-headline mb-2">تسجيل الدخول إلى حسابك</h2>
                    <p class="text-slate-500 text-base font-body">أدخل رقم الجوال وكلمة المرور للمتابعة</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div x-data="{
                        selectedCountry: { name: 'Yemen', code: 'YE', dial_code: '+967' },
                        localPhoneNumber: '{{ old('phone') }}'.startsWith('967') ? '{{ old('phone') }}'.substring(3) : '{{ old('phone') }}'
                    }" class="space-y-1.5">

                        <label for="phone_display" class="block text-sm font-bold text-on-background/80 pr-1">رقم
                            الجوال</label>

                        <input type="hidden" name="phone"
                            :value="selectedCountry.dial_code.replace('+', '') + localPhoneNumber">

                        <div
                            class="relative group flex items-center bg-surface-container-low rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-secondary-container transition-all">

                            <input id="phone_display" type="tel" x-model="localPhoneNumber" required autofocus
                                inputmode="numeric"
                                class="flex-1 bg-transparent border-0 px-4 py-3.5 pr-11 text-on-background placeholder:text-outline/60 focus:ring-0 font-headline dir-ltr text-left"
                                placeholder="7XXXXXXXX" />

                            <div
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-secondary">
                                <span class="material-symbols-outlined">call</span>
                            </div>

                            <div class="flex items-center gap-2 px-4 h-[52px] bg-slate-100 border-r border-slate-200">
                                <img :src="`https://flagcdn.com/w20/${selectedCountry.code.toLowerCase()}.png`"
                                    alt="Flag" class="w-5 h-auto rounded-sm shadow-sm">
                                <span class="text-sm font-bold text-on-surface font-headline dir-ltr"
                                    x-text="selectedCountry.dial_code"></span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                    </div>

                    <div class="space-y-1.5" x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm font-bold text-on-background/80 pr-1">كلمة
                            المرور</label>
                        <div class="relative group">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                autocomplete="current-password"
                                class="w-full bg-surface-container-low border-0 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-outline/60 focus:ring-2 focus:ring-secondary-container transition-all font-headline"
                                placeholder="••••••••" />
                            <div
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-secondary">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute left-3.5 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors">
                                <span class="material-symbols-outlined"
                                    x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-slate-300 text-secondary shadow-sm focus:ring-secondary-container">
                            <span class="ms-2 text-sm text-on-background/70 font-body">{{ __('تذكرني') }}</span>
                        </label>

                        {{-- يمكن تفعيل نسيت كلمة المرور بحذف الـ تعليق --}}
                        {{-- @if (Route::has('password.request'))
                        <a class="text-sm text-secondary hover:underline underline-offset-4 font-body"
                            href="{{ route('password.request') }}">
                            {{ __('نسيت كلمة المرور؟') }}
                        </a>
                        @endif --}}
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-secondary-container text-on-secondary-container font-extrabold py-4 rounded-lg shadow-[0_8px_20px_rgba(254,157,32,0.3)] hover:shadow-[0_12px_25px_rgba(254,157,32,0.4)] active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 group">
                            <span class="text-lg">تسجيل الدخول</span>
                            <span
                                class="material-symbols-outlined transform group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        </button>
                    </div>
                </form>

                <div class="mt-10 text-center border-t border-slate-100 pt-6">
                    <p class="text-slate-600 font-body">
                        ليس لديك حساب؟
                        <a class="text-secondary font-bold hover:underline underline-offset-4 mr-1"
                            href="{{ route('register') }}">إنشاء حساب جديد</a>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer
        class="mt-4 mb-8 text-slate-400 text-xs font-medium space-x-reverse space-x-6 flex items-center justify-center">
        <span class="font-headline tracking-widest">MURSAL LOGISTICS</span>
        <span>© 2026 جميع الحقوق محفوظة</span>
        <div class="flex gap-4">
            <a class="hover:text-primary transition-colors" href="#">سياسة الخصوصية</a>
            <a class="hover:text-primary transition-colors" href="#">الشروط والأحكام</a>
        </div>
    </footer>
</body>

</html>