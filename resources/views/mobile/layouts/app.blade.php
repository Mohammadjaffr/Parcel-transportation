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

    <script defer src="{{ asset('assets/js/cdn.min.js') }}"></script>
    <script src="{{ asset('assets/js/cdn.tailwindcss.js') }}"></script>

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
        [x-cloak] {
            display: none !important;
        }

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

<body class="min-h-screen bg-gray-50 bg-surface text-on-surface">
    
    @include('mobile.layouts.header')

    <main class="px-6 pt-24 pb-32">
        @yield('content')
    </main>
    <x-modals.warning-modal />
    <x-modals.error-modal />
    <x-modals.success-modal />
    @include('mobile.layouts.sidebar')
    <a href="{{ route('shipment.create') }}"
        class="flex fixed left-6 bottom-28 z-50 justify-center items-center w-14 h-14 text-white rounded-2xl border shadow-xl backdrop-blur-md transition-all duration-200 bg-primary/80 shadow-primary/20 hover:scale-105 active:scale-95 group border-white/20">
        <span class="text-3xl material-symbols-outlined"
            style="font-variation-settings: 'FILL' 1, 'wght' 500;">add_box</span>
    </a>

    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById('notif-dropdown');
            const btn = document.getElementById('notif-btn');

            dropdown.classList.toggle('opacity-0');
            dropdown.classList.toggle('invisible');
            dropdown.classList.toggle('scale-95');
            dropdown.classList.toggle('translate-y-2');

            btn.classList.toggle('bg-primary/10');
            btn.classList.toggle('text-primary');
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notif-dropdown');
            const wrapper = document.getElementById('header-actions');

            // الحل هنا: نتأكد أولاً أن العناصر موجودة في الصفحة قبل أن نطبق عليها أي دوال
            if (wrapper && dropdown) {
                if (!wrapper.contains(event.target) && !dropdown.classList.contains('invisible')) {
                    toggleNotifications();
                }
            }
        });
    </script>

    @yield('script')
</body>

</html>
