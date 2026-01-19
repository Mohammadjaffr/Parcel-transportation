<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
        @yield('title', 'لوحة التحكم')
    </title>
    <link rel="icon" href="{{ asset('tailadmin/build/favicon.ico') }}">
    <link href="{{ asset('tailadmin/build/style.css') }}" rel="stylesheet">
    @yield('style')

</head>

<body
    x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{ 'dark bg-gray-900': darkMode === true }">
    <!-- ===== Preloader Start ===== -->
    <div x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => { setTimeout(() => loaded = false, 500) })"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black">
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent">
        </div>
    </div>

    <!-- ===== Preloader End ===== -->

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">
        <!-- ===== Sidebar Start ===== -->
        @include('layouts.sidebar')

        <!-- ===== Sidebar End ===== -->

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Small Device Overlay Start -->
            <div @click="sidebarToggle = false" :class="sidebarToggle ? 'block lg:hidden' : 'hidden'"
                class="fixed w-full h-screen z-9 bg-gray-900/50"></div>
            <!-- Small Device Overlay End -->

            <!-- ===== Header Start ===== -->
            @include('layouts.header')
            <!-- ===== Header End ===== -->



            <!-- ===== Main Content Start ===== -->
            <main class="p-4 md:p-6">
                <!-- ===== Breadcrumb Start -->
                @include('layouts.Breadcrumb')
                <!-- ===== Breadcrumb End -->
                @yield('content')
                <x-modals.warning-modal />
            </main>

            <!-- ===== Main Content End ===== -->
        </div>
        <!-- ===== Content Area End ===== -->
    </div>
    <!-- ===== Page Wrapper End ===== -->
    @yield('script')
    <script defer src="{{ asset('tailadmin/build/bundle.js') }}"></script>
    
</body>

</html>