<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'لوحة التحكم')</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@100;300;400;500;700;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f79009",
                        "primary-hover": "#dc6803",
                        "primary-container": "#fffaeb",
                        "on-primary-container": "#b54708",
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#191c1d",
                        "on-surface-variant": "#454652",
                        "error": "#ba1a1a",
                        "surface-container-low": "#f3f4f5",
                        "secondary-container": "#ff9800",
                        "on-secondary-fixed-variant": "#693c00",
                        "secondary": "#8b5000",
                        "tertiary-fixed": "#94f990",
                        "on-tertiary-fixed-variant": "#005313",
                        "tertiary": "#004e11",
                        "surface-container-highest": "#e1e3e4",
                        "secondary-fixed": "#ffdcbe",
                        "surface": "#f8fafc",
                    },
                    fontFamily: {
                        "headline": ["IBM Plex Sans Arabic", "Be Vietnam Pro", "sans-serif"],
                        "body": ["IBM Plex Sans Arabic", "Be Vietnam Pro", "sans-serif"],
                    },
                },
            },
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            min-height: max(884px, 100dvh);
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-surface text-on-surface min-h-screen bg-gray-50">

    @include('mobile.layouts.header')

    <main class="pt-24 pb-32 px-6">
        @yield('content')
    </main>

    @include('mobile.layouts.sidebar')
    <a href="#" class="fixed bottom-28 left-6 z-50 flex items-center justify-center w-14 h-14 bg-primary/80 backdrop-blur-md text-white rounded-2xl shadow-xl shadow-primary/20 hover:scale-105 active:scale-95 transition-all duration-200 group border border-white/20">
    <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1, 'wght' 500;">add_box</span>

    <span class="absolute right-16 bg-slate-800/90 backdrop-blur-sm text-white text-xs px-3 py-1.5 rounded-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap shadow-lg">
        إضافة طرد جديد
    </span>
</a>

<script>
        function toggleNotifications() {
            const dropdown = document.getElementById('notif-dropdown');
            const btn = document.getElementById('notif-btn');

            // تبديل الكلاسات المسؤولة عن الظهور والأنيميشن
            dropdown.classList.toggle('opacity-0');
            dropdown.classList.toggle('invisible');
            dropdown.classList.toggle('scale-95');
            dropdown.classList.toggle('translate-y-2');

            // تلوين الزر نفسه ليدل على أنه نشط
            btn.classList.toggle('bg-primary/10');
            btn.classList.toggle('text-primary');
        }

        // إغلاق القائمة عند الضغط في أي مكان خارجها (UX Best Practice)
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('notif-dropdown');
            const wrapper = document.getElementById('header-actions');

            if (!wrapper.contains(event.target) && !dropdown.classList.contains('invisible')) {
                toggleNotifications();
            }
        });
    </script>
</body>

</html>