<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>إنشاء حساب - مرسل</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Manrope:wght@200;400;600;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
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
                    borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
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
        <div class="absolute top-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-secondary-container blur-[120px]"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-primary blur-[120px]"></div>
    </div>

    <main class="relative z-10 w-full max-w-6xl flex flex-col md:flex-row-reverse items-stretch min-h-[750px] m-4 md:m-8 overflow-hidden rounded-xl shadow-[0_20px_60px_rgba(11,29,45,0.08)] bg-surface-container-lowest">
        
        <section class="hidden md:flex md:w-5/12 kinetic-gradient relative overflow-hidden p-12 flex-col justify-between items-start text-white">
            <div class="relative z-20 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-secondary-container flex items-center justify-center rounded-lg shadow-lg">
                        <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">package_2</span>
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
                    <span>موثوق به من قبل أكثر من 500 شركة عالمية</span>
                </div>
            </div>

            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[150%] h-[150%] opacity-10">
                <img class="w-full h-full object-cover mix-blend-overlay" src="{{ asset('assets/image/abstract-bg.png') }}" alt="Background Pattern"/>
            </div>
        </section>

        <section class="flex-1 p-8 md:p-16 flex flex-col justify-center bg-white">
            <div class="max-w-md mx-auto w-full">
                
                <div class="md:hidden flex justify-center mb-10">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-primary flex items-center justify-center rounded">
                            <span class="material-symbols-outlined text-secondary-container text-2xl">package_2</span>
                        </div>
                        <span class="text-2xl font-black text-primary uppercase font-headline">Mursal</span>
                    </div>
                </div>

                <div class="mb-10 text-right">
                    <h2 class="text-3xl font-bold text-on-background font-headline mb-2">إنشاء حساب جديد</h2>
                    <p class="text-slate-500 text-base font-body">ابدأ تجربتك اللوجستية المتطورة اليوم</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="name" class="block text-sm font-bold text-on-background/80 pr-1">الاسم الكامل</label>
                        <div class="relative group">
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                                class="w-full bg-surface-container-low border-0 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all" 
                                placeholder="أدخل اسمك بالكامل"/>
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-bold text-on-background/80 pr-1">البريد الإلكتروني</label>
                        <div class="relative group">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                                class="w-full bg-surface-container-low border-0 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all font-headline" 
                                placeholder="example@mursal.com"/>
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                <span class="material-symbols-outlined">mail</span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <div class="space-y-1.5" x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm font-bold text-on-background/80 pr-1">كلمة المرور</label>
                        <div class="relative group">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" 
                                class="w-full bg-surface-container-low border-0 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all font-headline" 
                                placeholder="••••••••"/>
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                            <button type="button" @click="showPassword = !showPassword" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="space-y-1.5" x-data="{ showConfirmPassword: false }">
                        <label for="password_confirmation" class="block text-sm font-bold text-on-background/80 pr-1">تأكيد كلمة المرور</label>
                        <div class="relative group">
                            <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" 
                                class="w-full bg-surface-container-low border-0 rounded-lg px-4 py-3.5 pr-11 text-on-background placeholder:text-slate-400 focus:ring-2 focus:ring-secondary-container transition-all font-headline" 
                                placeholder="••••••••"/>
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-secondary">
                                <span class="material-symbols-outlined">lock_clock</span>
                            </div>
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined" x-text="showConfirmPassword ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-secondary-container text-on-secondary-container font-extrabold py-4 rounded-lg shadow-[0_8px_20px_rgba(254,157,32,0.3)] hover:shadow-[0_12px_25px_rgba(254,157,32,0.4)] active:scale-95 transition-all duration-300 flex items-center justify-center gap-3 group">
                            <span class="text-lg">إنشاء حساب</span>
                            <span class="material-symbols-outlined transform group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        </button>
                    </div>
                </form>

                <div class="mt-10 text-center border-t border-slate-100 pt-6">
                    <p class="text-slate-600 font-body">
                        لديك حساب بالفعل؟
                        <a class="text-secondary font-bold hover:underline underline-offset-4 mr-1" href="{{ route('login') }}">تسجيل الدخول</a>
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