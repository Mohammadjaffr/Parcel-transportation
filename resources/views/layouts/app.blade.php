<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true', 
    sidebarToggle: false, 
    loaded: true 
}" x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', 'لوحة التحكم')</title>

    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@100;300;400;500;700;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    {{-- <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
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
                        "surface": "#f8fafc",
                        "boxdark": "#24303F",
                        "boxdark-2": "#1A222C",
                        "bodydark": "#AEB7C0",
                    },
                    fontFamily: {
                        "headline": ["IBM Plex Sans Arabic", "sans-serif"],
                        "body": ["IBM Plex Sans Arabic", "sans-serif"],
                    },
                },
            },
        }
    </script> --}}
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
    <style type="text/tailwindcss">
        @layer base {
            [x-cloak] { display: none !important; }
            body {
                @apply font-body bg-surface text-on-surface min-h-screen;
            }
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
        }
        @layer components {
            .glass-nav {
                @apply bg-white/80 backdrop-blur-md dark:bg-boxdark/80;
            }
            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }
        }
    </style>
    @yield('style')
</head>

<body class="dark:bg-boxdark-2 dark:text-bodydark">

    <div x-show="!loaded" x-init="window.onload = () => { setTimeout(() => loaded = true, 500) }"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-black">
        <div class="w-16 h-16 rounded-full border-4 animate-spin border-primary border-t-transparent"></div>
    </div>

    <div class="flex overflow-hidden h-screen">
        
        @include('layouts.sidebar')

        <div class="flex overflow-y-auto overflow-x-hidden relative flex-col flex-1">
            
            <div @click="sidebarToggle = false" 
                 x-show="sidebarToggle" 
                 class="fixed inset-0 z-40 transition-opacity bg-black/50 lg:hidden"
                 x-transition:enter="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            @include('layouts.header')

            <main class="container p-4 mx-auto md:p-6 2xl:p-10">
                @include('layouts.Breadcrumb')

                @yield('content')
            </main>

            <x-modals.warning-modal />
            <x-modals.error-modal-desktop />
            <x-modals.success-modal-desktop />
        </div>
    </div>

    <div class="hidden" aria-hidden="true">
        @include('closings.create-closing-modal')
        @include('transactions.create-transaction-modal')
    </div>

    @yield('script')
</body>
</html>