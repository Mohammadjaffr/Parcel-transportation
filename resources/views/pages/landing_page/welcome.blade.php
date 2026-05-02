<!DOCTYPE html>

<html class="scroll-smooth" dir="rtl" lang="ar">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <link
    href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&amp;family=Manrope:wght@400;500;600;700;800&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
    rel="stylesheet" />
  <script src="{{asset('assets/js/cdn.tailwindcss.js')}}"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary": "#041627",
            "primary-container": "#1A2B3C",
            "on-primary": "#FFFFFF",
            "secondary": "#E67E22",
            "secondary-container": "#FE9D20",
            "on-secondary": "#FFFFFF",
            "background": "#F8FAFC",
            "surface": "#FFFFFF",
            "surface-container-low": "#F1F5F9",
            "surface-container": "#E2E8F0",
            "surface-container-high": "#CBD5E1",
            "on-surface": "#0F172A",
            "on-surface-variant": "#475569",
            "error": "#EF4444",
            "outline": "#E2E8F0",
            "outline-variant": "#CBD5E1",
          },
          fontFamily: {
            "headline": ["Almarai", "sans-serif"],
            "body": ["Almarai", "sans-serif"],
          },
          borderRadius: {
            "DEFAULT": "0.5rem",
            "lg": "0.75rem",
            "xl": "1rem",
            "full": "9999px"
          },
        },
      },
    }
  </script>
  <style>
    body {
      font-family: 'Almarai', sans-serif;
    }

    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .kinetic-gradient {
      background: linear-gradient(135deg, #041627 0%, #1a2b3c 100%);
    }

    .glass-nav {
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
    }

    .no-line-logic {
      border: none !important;
    }
  </style>
</head>

<body class="bg-surface text-on-background selection:bg-secondary-container selection:text-on-secondary-container">
  <!-- TopNavBar Section -->
  <nav
    class="fixed top-0 z-50 w-full shadow-sm backdrop-blur-md bg-slate-50/80 no-line-logic tonal-shift bg-slate-100/50">
    <div class="flex flex-row-reverse justify-between items-center px-6 py-4 mx-auto w-full max-w-screen-2xl">

      <div class="flex gap-2 items-center">
        <a href="/" class="transition-transform hover:scale-105 active:scale-95">
          <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Mursal Logo" class="object-contain w-auto h-10" />
        </a>
      </div>

      <div class="hidden flex-row-reverse gap-10 md:flex">
        <a class="text-orange-600 font-extrabold font-['Almarai'] text-lg hover:text-orange-500 transition-colors duration-300"
          href="#">المميزات</a>
        <a class="text-slate-700 font-['Almarai'] text-lg hover:text-orange-500 transition-colors duration-300"
          href="#">الأسعار</a>
        <a class="text-slate-700 font-['Almarai'] text-lg hover:text-orange-500 transition-colors duration-300"
          href="#">التتبع</a>
        <a class="text-slate-700 font-['Almarai'] text-lg hover:text-orange-500 transition-colors duration-300"
          href="#">عن مرسل</a>
      </div>

      <div class="flex gap-4 items-center">
        @if(Auth::check())
          <a href="{{ route('dashboard.index') }}">
            <button
              class="px-6 py-2 font-bold rounded-lg transition-transform scale-95 bg-primary text-on-primary active:opacity-80">
              لوحة التحكم
            </button>
          </a>
        @else
          <a href="{{ route('login') }}">
            <button
              class="px-6 py-2 font-bold rounded-lg transition-transform scale-95 bg-primary text-on-primary active:opacity-80">
              تسجيل دخول
            </button>
          </a>
        @endif

        @if(!Auth::check())
          <a href="{{ route('register') }}">
            <button
              class="px-6 py-2 font-bold rounded-lg transition-transform scale-95 bg-secondary text-on-secondary active:opacity-80">
              إنشاء حساب
            </button>
          </a>
        @endif
      </div>
    </div>
  </nav>
  <main class="overflow-x-hidden pt-24">
    <!-- Hero Section -->
    <section class="relative px-6 py-20 mx-auto max-w-screen-2xl md:py-32">
      <div class="grid grid-cols-1 gap-12 items-center lg:grid-cols-12">
        <div class="space-y-8 lg:col-span-7">
          <div class="inline-flex gap-2 items-center px-3 py-1 rounded-full bg-surface-container-high">
            <span class="w-2 h-2 rounded-full bg-secondary"></span>
            <span class="text-sm font-bold tracking-wide uppercase text-primary">مستقبل اللوجستيات</span>
          </div>
          <h1 class="text-5xl font-extrabold tracking-tighter leading-tight md:text-7xl text-on-background">
            الخدمات اللوجستية الذكية <br />
            <span class="text-secondary">لنمو أعمالك</span>
          </h1>
          <p class="max-w-2xl text-xl leading-relaxed md:text-2xl text-on-surface-variant">
            أدِر، تتبع، وطور وكالة الشحن الخاصة بك مع أكثر منصة SaaS بديهية في العالم. دقة معمارية في كل
            شحنة.
          </p>
          <div class="flex flex-wrap gap-4 pt-4">
            <button
              class="px-8 py-4 kinetic-gradient text-on-primary rounded-lg font-bold text-lg shadow-xl shadow-primary/10 transition-all hover:scale-[1.02] active:scale-95">
              ابدأ الشحن الآن
            </button>
            <button
              class="px-8 py-4 text-lg font-bold rounded-lg border-2 transition-all border-outline-variant text-primary hover:bg-surface-container-low">
              عرض توضيحي
            </button>
          </div>
        </div>
        <div class="relative lg:col-span-5">
          <div
            class="relative z-10 p-6 rounded-2xl shadow-2xl transition-transform duration-500 transform rotate-3 bg-surface-container-lowest shadow-primary/5 hover:rotate-0">
            <img class="w-full rounded-xl"
              data-alt="modern industrial warehouse with organized shipping containers and sleek blue atmospheric lighting representing advanced logistics"
              src="{{ asset('assets/image/icon_with_name_AR.png') }}" />
            <div class="absolute -bottom-6 -left-6 p-6 rounded-xl shadow-xl bg-secondary text-on-secondary">
              <div class="text-3xl font-black font-data">99.9%</div>
              <div class="text-xs font-bold opacity-80">دقة التتبع الفوري</div>
            </div>
          </div>
          <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] bg-surface-container-high/30 rounded-full blur-3xl -z-10">
          </div>
        </div>
      </div>
    </section>
    <!-- Features Section -->
    <section class="py-24 bg-surface-container-low">
      <div class="px-6 mx-auto max-w-screen-2xl">
        <div class="flex flex-col gap-6 justify-between items-end mb-16 md:flex-row-reverse">
          <div class="max-w-2xl text-right">
            <h2 class="mb-4 text-4xl font-black">نظام هندسي متكامل</h2>
            <p class="text-xl text-on-surface-variant">صممنا مرسل ليكون العقل المدبر لعملياتك اللوجستية، مع
              التركيز على السرعة والدقة المتناهية.</p>
          </div>
          <div class="h-[2px] flex-grow bg-outline-variant/30 mb-4 hidden lg:block mx-12"></div>
        </div>
        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
          <!-- Feature 1 -->
          <div class="p-10 rounded-xl transition-all duration-500 group bg-surface-container-lowest hover:bg-primary">
            <div
              class="flex justify-center items-center mb-8 w-16 h-16 rounded-lg transition-colors bg-surface-container-high group-hover:bg-secondary">
              <span class="text-3xl material-symbols-outlined text-primary group-hover:text-on-secondary"
                data-icon="location_on" style="font-variation-settings: 'FILL' 1;">location_on</span>
            </div>
            <h3 class="mb-4 text-2xl font-bold transition-colors group-hover:text-on-primary">تتبع حي وفوري
            </h3>
            <p class="leading-loose transition-colors text-on-surface-variant group-hover:text-on-primary/70">
              مراقبة دقيقة لكل شحنة بمحرك GPS كينتيكي يوفر تحديثات في أجزاء من الثانية.
            </p>
          </div>
          <!-- Feature 2 -->
          <div class="p-10 rounded-xl transition-all duration-500 group bg-surface-container-lowest hover:bg-primary">
            <div
              class="flex justify-center items-center mb-8 w-16 h-16 rounded-lg transition-colors bg-surface-container-high group-hover:bg-secondary">
              <span class="text-3xl material-symbols-outlined text-primary group-hover:text-on-secondary"
                data-icon="hub" style="font-variation-settings: 'FILL' 1;">hub</span>
            </div>
            <h3 class="mb-4 text-2xl font-bold transition-colors group-hover:text-on-primary">مزامنة الفروع
              آلياً</h3>
            <p class="leading-loose transition-colors text-on-surface-variant group-hover:text-on-primary/70">
              ربط مراكز التوزيع والفروع بذكاء لضمان انسيابية البيانات وتوحيد سير العمل.
            </p>
          </div>
          <!-- Feature 3 -->
          <div class="p-10 rounded-xl transition-all duration-500 group bg-surface-container-lowest hover:bg-primary">
            <div
              class="flex justify-center items-center mb-8 w-16 h-16 rounded-lg transition-colors bg-surface-container-high group-hover:bg-secondary">
              <span class="text-3xl material-symbols-outlined text-primary group-hover:text-on-secondary"
                data-icon="analytics" style="font-variation-settings: 'FILL' 1;">analytics</span>
            </div>
            <h3 class="mb-4 text-2xl font-bold transition-colors group-hover:text-on-primary">تحليلات
              وتقارير متقدمة</h3>
            <p class="leading-loose transition-colors text-on-surface-variant group-hover:text-on-primary/70">
              تحويل البيانات اللوجستية الضخمة إلى رؤى قابلة للتنفيذ تدعم اتخاذ القرار الاستراتيجي.
            </p>
          </div>
        </div>
      </div>
    </section>
    <!-- Pricing Section -->
    <section class="px-6 py-24 mx-auto max-w-screen-2xl">
      <div class="mx-auto mb-20 max-w-3xl text-center">
        <h2 class="mb-6 text-4xl font-black md:text-5xl">خطط تسعير مرنة</h2>
        <p class="text-xl text-on-surface-variant">اختر الخطة التي تناسب حجم تطلعاتك ونمو أعمالك.</p>
      </div>
      <div class="grid grid-cols-1 gap-8 items-stretch md:grid-cols-3">
        <!-- Basic -->
        <div class="flex flex-col p-10 rounded-xl bg-surface-container-low">
          <div class="mb-8">
            <h4 class="mb-2 text-xl font-bold text-primary">الأساسية</h4>
            <div class="flex gap-2 items-baseline">
              <span class="text-4xl font-black font-data text-primary">$49</span>
              <span class="text-on-surface-variant">/شهرياً</span>
            </div>
          </div>
          <ul class="flex-grow mb-12 space-y-4">
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>حتى 500 شحنة شهرياً</span>
            </li>
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>تتبع حي محدود</span>
            </li>
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>تقارير أساسية</span>
            </li>
          </ul>
          <button
            class="py-4 w-full font-bold rounded-lg border-2 transition-all border-primary text-primary hover:bg-primary hover:text-on-primary">اختر
            الخطة</button>
        </div>
        <!-- Premium (Highlighted) -->
        <div
          class="flex relative z-20 flex-col p-10 rounded-xl shadow-2xl transform scale-105 kinetic-gradient text-on-primary">
          <div
            class="absolute -top-4 left-1/2 px-4 py-1 text-sm font-bold rounded-full -translate-x-1/2 bg-secondary text-on-secondary">
            الأكثر شيوعاً
          </div>
          <div class="mb-8">
            <h4 class="mb-2 text-xl font-bold text-secondary-fixed-dim">المتميزة</h4>
            <div class="flex gap-2 items-baseline">
              <span class="text-5xl font-black font-data">$149</span>
              <span class="opacity-70">/شهرياً</span>
            </div>
          </div>
          <ul class="flex-grow mb-12 space-y-4">
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary-fixed" data-icon="check_circle">check_circle</span>
              <span>شحنات غير محدودة</span>
            </li>
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary-fixed" data-icon="check_circle">check_circle</span>
              <span>تتبع GPS كينتيكي كامل</span>
            </li>
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary-fixed" data-icon="check_circle">check_circle</span>
              <span>تحليلات تنبؤية بالذكاء الاصطناعي</span>
            </li>
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary-fixed" data-icon="check_circle">check_circle</span>
              <span>دعم فني 24/7</span>
            </li>
          </ul>
          <button
            class="py-4 w-full font-black rounded-lg transition-all bg-secondary text-on-secondary hover:bg-secondary-container">ابدأ
            الآن</button>
        </div>
        <!-- Enterprise -->
        <div class="flex flex-col p-10 rounded-xl bg-surface-container-low">
          <div class="mb-8">
            <h4 class="mb-2 text-xl font-bold text-primary">المؤسسات</h4>
            <div class="text-4xl font-bold text-primary">مخصص</div>
          </div>
          <ul class="flex-grow mb-12 space-y-4">
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>بنية تحتية مخصصة</span>
            </li>
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>تكامل API كامل</span>
            </li>
            <li class="flex gap-3 items-center">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>مدير حساب مخصص</span>
            </li>
          </ul>
          <button
            class="py-4 w-full font-bold rounded-lg border-2 transition-all border-primary text-primary hover:bg-primary hover:text-on-primary">تواصل
            معنا</button>
        </div>
      </div>
    </section>

  </main>
  <!-- Footer Section -->
  <footer class="mt-20 w-full border-t bg-slate-50 border-slate-200/15">
    <div
      class="flex flex-col justify-between items-center px-12 py-10 mx-auto w-full max-w-screen-2xl md:flex-row-reverse">

      <div class="mb-6 md:mb-0">
        <a href="/" class="block opacity-100 transition-transform duration-300 hover:scale-105">
          <img src="{{ asset('assets/image/icon_with_name_AR.png') }}" alt="Mursal Logo"
            class="object-contain w-auto h-16 transition-all duration-300 md:h-20" />
        </a>
      </div>

      <div class="flex flex-row-reverse gap-8 mb-6 md:mb-0">
        <a class="font-['Almarai'] text-sm text-slate-500 hover:underline hover:text-blue-700 transition-all"
          href="#">سياسة الخصوصية</a>
        <a class="font-['Almarai'] text-sm text-slate-500 hover:underline hover:text-blue-700 transition-all"
          href="#">الشروط والأحكام</a>
        <a class="font-['Almarai'] text-sm text-slate-500 hover:underline hover:text-blue-700 transition-all"
          href="#">اتصل بنا</a>
      </div>

      <div class="font-['Almarai'] text-sm text-slate-500 text-center md:text-right">
        جميع الحقوق محفوظة لنظام <span class="font-bold text-blue-900">مرسل</span> © 2026
      </div>
    </div>
  </footer>
</body>

</html>