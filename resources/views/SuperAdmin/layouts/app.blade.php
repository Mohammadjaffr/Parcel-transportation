<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'لوحة تحكم الإدارة العليا')</title>

    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@100;300;400;500;700;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

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
                        "surface": "#f8fafc",
                        "on-surface": "#191c1d",
                    },
                    fontFamily: {
                        "headline": ["IBM Plex Sans Arabic", "sans-serif"],
                        "body": ["IBM Plex Sans Arabic", "sans-serif"],
                    },
                },
            },
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        /* إخفاء شريط التمرير مع الحفاظ على إمكانية التمرير */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    @yield('styles')
</head>

<body class="min-h-screen bg-surface text-on-surface" x-data="{ sidebarOpen: false }">

    @include('SuperAdmin.layouts.sidebar')

    <div class="flex flex-col min-h-screen transition-all duration-300 lg:mr-72">
        
        @include('SuperAdmin.layouts.header')

        <main class="flex-1 p-4 mt-20 lg:p-8 lg:mt-24">
            <div class="mx-auto max-w-7xl">
                @yield('content')
            </div>
        </main>
        
    </div>

    <x-modals.warning-modal />
    <x-modals.error-modal />
    <x-modals.success-modal />

    @yield('scripts')
</body>
</html>