<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'لوحة التحكم')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@100;300;400;500;700;900&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-lowest": "#ffffff",
                        "on-surface": "#191c1d",
                        "primary": "#24389c",
                        "on-surface-variant": "#454652",
                        "error": "#ba1a1a",
                        "primary-container": "#3f51b5",
                        "surface-container-low": "#f3f4f5",
                        "secondary-container": "#ff9800",
                        "on-secondary-fixed-variant": "#693c00",
                        "secondary": "#8b5000",
                        "tertiary-fixed": "#94f990",
                        "on-tertiary-fixed-variant": "#005313",
                        "tertiary": "#004e11",
                        "surface-container-highest": "#e1e3e4",
                        "secondary-fixed": "#ffdcbe",
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
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; min-height: max(884px, 100dvh); }
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
    </style>
</head>
<body class="bg-surface text-on-surface min-h-screen bg-gray-50">

    @include('mobile.layouts.header')

    <main class="pt-24 pb-32 px-6">
        @yield('content')
    </main>

    @include('mobile.layouts.sidebar') 
    </body>
</html>