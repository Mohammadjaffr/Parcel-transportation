<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'نظام مرسل') }} @if (isset($title))
            | {{ $title }}
        @endif
    </title>

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
                    },
                    fontFamily: {
                        "headline": ["IBM Plex Sans Arabic", "sans-serif"],
                        "body": ["IBM Plex Sans Arabic", "sans-serif"],
                    }
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer base {
            [x-cloak] {
                display: none !important;
            }

            body {
                @apply font-body bg-slate-50 text-slate-800 antialiased;
            }

            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }

            .dir-ltr {
                direction: ltr;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-body text-slate-800 bg-slate-50">

    {{-- المحتوى بالكامل (بدون غلاف افتراضي، لكي تأخذ صفحات الـ OTP وتسجيل الدخول راحتها في التصميم بكامل الشاشة) --}}
    {{ $slot }}

</body>

</html>
