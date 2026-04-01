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
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
    class="fixed top-0 w-full z-50 bg-slate-50/80 backdrop-blur-md shadow-sm no-line-logic tonal-shift bg-slate-100/50">
    <div class="flex flex-row-reverse justify-between items-center w-full px-6 py-4 max-w-screen-2xl mx-auto">

      <div class="flex items-center gap-2">
        <a href="/" class="transition-transform hover:scale-105 active:scale-95">
          <img src="{{ asset('assets/image/icon_without_bg.png') }}" alt="Mursal Logo" class="h-10 w-auto object-contain" />
        </a>
      </div>

      <div class="hidden md:flex flex-row-reverse gap-10">
        <a class="text-orange-600 font-extrabold font-['Almarai'] text-lg hover:text-orange-500 transition-colors duration-300"
          href="#">المميزات</a>
        <a class="text-slate-700 font-['Almarai'] text-lg hover:text-orange-500 transition-colors duration-300"
          href="#">الأسعار</a>
        <a class="text-slate-700 font-['Almarai'] text-lg hover:text-orange-500 transition-colors duration-300"
          href="#">التتبع</a>
        <a class="text-slate-700 font-['Almarai'] text-lg hover:text-orange-500 transition-colors duration-300"
          href="#">عن مرسل</a>
      </div>

      <div class="flex items-center gap-4">
        <a href="{{ route('login') }}">
          <button
            class="px-6 py-2 bg-primary text-on-primary font-bold rounded-lg scale-95 active:opacity-80 transition-transform">
            تسجيل دخول
          </button>
        </a>
      </div>
    </div>
  </nav>
  <main class="pt-24 overflow-x-hidden">
    <!-- Hero Section -->
    <section class="relative px-6 py-20 md:py-32 max-w-screen-2xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-7 space-y-8">
          <div class="inline-flex items-center gap-2 px-3 py-1 bg-surface-container-high rounded-full">
            <span class="w-2 h-2 rounded-full bg-secondary"></span>
            <span class="text-sm font-bold text-primary tracking-wide uppercase">مستقبل اللوجستيات</span>
          </div>
          <h1 class="text-5xl md:text-7xl font-extrabold leading-tight tracking-tighter text-on-background">
            الخدمات اللوجستية الذكية <br />
            <span class="text-secondary">لنمو أعمالك</span>
          </h1>
          <p class="text-xl md:text-2xl text-on-surface-variant max-w-2xl leading-relaxed">
            أدِر، تتبع، وطور وكالة الشحن الخاصة بك مع أكثر منصة SaaS بديهية في العالم. دقة معمارية في كل
            شحنة.
          </p>
          <div class="flex flex-wrap gap-4 pt-4">
            <button
              class="px-8 py-4 kinetic-gradient text-on-primary rounded-lg font-bold text-lg shadow-xl shadow-primary/10 transition-all hover:scale-[1.02] active:scale-95">
              ابدأ الشحن الآن
            </button>
            <button
              class="px-8 py-4 border-2 border-outline-variant text-primary rounded-lg font-bold text-lg hover:bg-surface-container-low transition-all">
              عرض توضيحي
            </button>
          </div>
        </div>
        <div class="lg:col-span-5 relative">
          <div
            class="relative z-10 bg-surface-container-lowest p-6 rounded-2xl shadow-2xl shadow-primary/5 transform rotate-3 hover:rotate-0 transition-transform duration-500">
            <img class="rounded-xl w-full"
              data-alt="modern industrial warehouse with organized shipping containers and sleek blue atmospheric lighting representing advanced logistics"
              src="{{ asset('assets/image/icon_with_name_AR.png') }}" />
            <div class="absolute -bottom-6 -left-6 bg-secondary text-on-secondary p-6 rounded-xl shadow-xl">
              <div class="text-3xl font-data font-black">99.9%</div>
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
      <div class="max-w-screen-2xl mx-auto px-6">
        <div class="flex flex-col md:flex-row-reverse justify-between items-end mb-16 gap-6">
          <div class="max-w-2xl text-right">
            <h2 class="text-4xl font-black mb-4">نظام هندسي متكامل</h2>
            <p class="text-xl text-on-surface-variant">صممنا مرسل ليكون العقل المدبر لعملياتك اللوجستية، مع
              التركيز على السرعة والدقة المتناهية.</p>
          </div>
          <div class="h-[2px] flex-grow bg-outline-variant/30 mb-4 hidden lg:block mx-12"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1 -->
          <div class="group p-10 bg-surface-container-lowest rounded-xl hover:bg-primary transition-all duration-500">
            <div
              class="w-16 h-16 bg-surface-container-high rounded-lg flex items-center justify-center mb-8 group-hover:bg-secondary transition-colors">
              <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-secondary"
                data-icon="location_on" style="font-variation-settings: 'FILL' 1;">location_on</span>
            </div>
            <h3 class="text-2xl font-bold mb-4 group-hover:text-on-primary transition-colors">تتبع حي وفوري
            </h3>
            <p class="text-on-surface-variant group-hover:text-on-primary/70 transition-colors leading-loose">
              مراقبة دقيقة لكل شحنة بمحرك GPS كينتيكي يوفر تحديثات في أجزاء من الثانية.
            </p>
          </div>
          <!-- Feature 2 -->
          <div class="group p-10 bg-surface-container-lowest rounded-xl hover:bg-primary transition-all duration-500">
            <div
              class="w-16 h-16 bg-surface-container-high rounded-lg flex items-center justify-center mb-8 group-hover:bg-secondary transition-colors">
              <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-secondary"
                data-icon="hub" style="font-variation-settings: 'FILL' 1;">hub</span>
            </div>
            <h3 class="text-2xl font-bold mb-4 group-hover:text-on-primary transition-colors">مزامنة الفروع
              آلياً</h3>
            <p class="text-on-surface-variant group-hover:text-on-primary/70 transition-colors leading-loose">
              ربط مراكز التوزيع والفروع بذكاء لضمان انسيابية البيانات وتوحيد سير العمل.
            </p>
          </div>
          <!-- Feature 3 -->
          <div class="group p-10 bg-surface-container-lowest rounded-xl hover:bg-primary transition-all duration-500">
            <div
              class="w-16 h-16 bg-surface-container-high rounded-lg flex items-center justify-center mb-8 group-hover:bg-secondary transition-colors">
              <span class="material-symbols-outlined text-3xl text-primary group-hover:text-on-secondary"
                data-icon="analytics" style="font-variation-settings: 'FILL' 1;">analytics</span>
            </div>
            <h3 class="text-2xl font-bold mb-4 group-hover:text-on-primary transition-colors">تحليلات
              وتقارير متقدمة</h3>
            <p class="text-on-surface-variant group-hover:text-on-primary/70 transition-colors leading-loose">
              تحويل البيانات اللوجستية الضخمة إلى رؤى قابلة للتنفيذ تدعم اتخاذ القرار الاستراتيجي.
            </p>
          </div>
        </div>
      </div>
    </section>
    <!-- Pricing Section -->
    <section class="py-24 px-6 max-w-screen-2xl mx-auto">
      <div class="text-center max-w-3xl mx-auto mb-20">
        <h2 class="text-4xl md:text-5xl font-black mb-6">خطط تسعير مرنة</h2>
        <p class="text-xl text-on-surface-variant">اختر الخطة التي تناسب حجم تطلعاتك ونمو أعمالك.</p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
        <!-- Basic -->
        <div class="p-10 bg-surface-container-low rounded-xl flex flex-col">
          <div class="mb-8">
            <h4 class="text-xl font-bold text-primary mb-2">الأساسية</h4>
            <div class="flex items-baseline gap-2">
              <span class="text-4xl font-data font-black text-primary">$49</span>
              <span class="text-on-surface-variant">/شهرياً</span>
            </div>
          </div>
          <ul class="space-y-4 mb-12 flex-grow">
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>حتى 500 شحنة شهرياً</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>تتبع حي محدود</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>تقارير أساسية</span>
            </li>
          </ul>
          <button
            class="w-full py-4 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-on-primary transition-all">اختر
            الخطة</button>
        </div>
        <!-- Premium (Highlighted) -->
        <div
          class="p-10 kinetic-gradient text-on-primary rounded-xl relative transform scale-105 shadow-2xl z-20 flex flex-col">
          <div
            class="absolute -top-4 left-1/2 -translate-x-1/2 bg-secondary text-on-secondary px-4 py-1 rounded-full text-sm font-bold">
            الأكثر شيوعاً
          </div>
          <div class="mb-8">
            <h4 class="text-xl font-bold text-secondary-fixed-dim mb-2">المتميزة</h4>
            <div class="flex items-baseline gap-2">
              <span class="text-5xl font-data font-black">$149</span>
              <span class="opacity-70">/شهرياً</span>
            </div>
          </div>
          <ul class="space-y-4 mb-12 flex-grow">
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary-fixed" data-icon="check_circle">check_circle</span>
              <span>شحنات غير محدودة</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary-fixed" data-icon="check_circle">check_circle</span>
              <span>تتبع GPS كينتيكي كامل</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary-fixed" data-icon="check_circle">check_circle</span>
              <span>تحليلات تنبؤية بالذكاء الاصطناعي</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary-fixed" data-icon="check_circle">check_circle</span>
              <span>دعم فني 24/7</span>
            </li>
          </ul>
          <button
            class="w-full py-4 bg-secondary text-on-secondary font-black rounded-lg hover:bg-secondary-container transition-all">ابدأ
            الآن</button>
        </div>
        <!-- Enterprise -->
        <div class="p-10 bg-surface-container-low rounded-xl flex flex-col">
          <div class="mb-8">
            <h4 class="text-xl font-bold text-primary mb-2">المؤسسات</h4>
            <div class="text-4xl font-bold text-primary">مخصص</div>
          </div>
          <ul class="space-y-4 mb-12 flex-grow">
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>بنية تحتية مخصصة</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>تكامل API كامل</span>
            </li>
            <li class="flex items-center gap-3">
              <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
              <span>مدير حساب مخصص</span>
            </li>
          </ul>
          <button
            class="w-full py-4 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-on-primary transition-all">تواصل
            معنا</button>
        </div>
      </div>
    </section>

  </main>
  <!-- Footer Section -->
  <footer class="w-full mt-20 bg-slate-50 border-t border-slate-200/15">
    <div
      class="flex flex-col md:flex-row-reverse justify-between items-center px-12 py-10 w-full max-w-screen-2xl mx-auto">

      <div class="mb-6 md:mb-0">
        <a href="/" class="opacity-100 transition-transform hover:scale-105 duration-300 block">
          <img src="{{ asset('assets/image/icon_with_name_AR.png') }}" alt="Mursal Logo"
            class="h-16 md:h-20 w-auto object-contain transition-all duration-300" />
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