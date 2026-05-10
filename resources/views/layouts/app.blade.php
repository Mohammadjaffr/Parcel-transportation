<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    sidebarToggle: false,
    loaded: true
}"
    x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    @php
        $appName = $currentApp->name ?? 'نظام مرسل';
        $appLogo = (isset($currentApp) && $currentApp->logo) 
                    ? asset('storage/' . $currentApp->logo) 
                    : asset('assets/images/mursal-preview.png');
    @endphp

    <title>@yield('title', $appName . ' | لإدارة ونقل الطرود وتتبع الشحنات')</title>

    <meta name="description" content="{{ $appName }} هو الحل الأمثل لإدارة عمليات نقل الطرود وتتبع الشحنات باحترافية. نوفر حلولاً لوجستية متكاملة لتسهيل إدارة المناديب، تتبع الطلبات، وضمان سرعة التوصيل." />
    <meta name="keywords" content="نظام مرسل, نقل طرود, تتبع الشحنات, إدارة التوصيل, نظام لوجستي, شحن وتوصيل, إدارة المناديب, برنامج نقل طرود" />
    <meta name="author" content="{{ $appName }}" />
    <meta name="robots" content="index, follow" />

    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:locale" content="{{ app()->getLocale() == 'ar' ? 'ar_AR' : 'en_US' }}" />
    <meta property="og:site_name" content="{{ $appName }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title', $appName . ' | لإدارة ونقل الطرود وتتبع الشحنات')" />
    <meta property="og:description" content="الحل الأمثل لإدارة عمليات نقل الطرود وتتبع الشحنات باحترافية. سرعة، أمان، وسهولة في إدارة العمليات اللوجستية." />
    <meta property="og:url" content="{{ url()->current() }}" />

    <meta property="og:image" content="{{ $appLogo }}" />
    <meta property="og:image:secure_url" content="{{ $appLogo }}" />
    <meta property="og:image:type" content="image/png" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:image:alt" content="{{ $appName }}" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('title', $appName)" />
    <meta name="twitter:description" content="الحل الأمثل لإدارة عمليات نقل الطرود وتتبع الشحنات باحترافية." />
    <meta name="twitter:image" content="{{ $appLogo }}" />

    <link rel="icon" type="image/png" href="{{ $appLogo }}" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicons/favicon.ico') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicons/favicon-96x96.png') }}" />
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicons/favicon-96x96.png') }}" />
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/favicons/web-app-manifest-192x192.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('assets/favicons/apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ asset('assets/favicons/site.webmanifest') }}" />

    <meta name="msapplication-TileImage" content="{{ asset('assets/favicons/favicon-96x96.png') }}" />
    <meta name="msapplication-TileColor" content="{{ $currentApp->color ?? '#f79009' }}" />

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
                        // يمكنك لاحقاً ربط هذه الألوان بـ $currentApp->theme إذا أردت تخصيص الألوان لكل تطبيق
                        "primary": "{{ $currentApp->color ?? '#f79009' }}",
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
            body { @apply font-body bg-surface text-on-surface min-h-screen; }
            .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        }
        @layer components {
            .glass-nav { @apply bg-white/80 backdrop-blur-md dark:bg-boxdark/80; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
            .scrollbar-hide::-webkit-scrollbar { display: none; }
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

        <div class="flex flex-col flex-1 relative overflow-x-hidden overflow-y-auto bg-surface dark:bg-boxdark-2">

            @include('layouts.header')

            <main class="container p-4 mx-auto md:p-6 2xl:p-10 flex-1 flex flex-col">
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
