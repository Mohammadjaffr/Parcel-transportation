<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', 'تطبيق بساط')</title>
    
    <link rel="icon" href="{{ asset('tailadmin/build/favicon.ico') }}">
    <link href="{{ asset('tailadmin/build/style.css') }}" rel="stylesheet">
    
    <style>
        body { overflow-x: hidden; -webkit-tap-highlight-color: transparent; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
    @yield('style')
</head>

<body x-data="{ 'darkMode': false, 'sidebarToggle': false, 'loaded': true }" 
      x-init="darkMode = JSON.parse(localStorage.getItem('darkMode')); 
              $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" 
      :class="{ 'dark bg-gray-900 text-white': darkMode === true, 'bg-gray-50': !darkMode }">

    <div x-show="loaded" x-init="window.onload = () => { setTimeout(() => loaded = false, 400) }"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-black">
        <div class="h-12 w-12 animate-spin rounded-full border-4 border-orange-500 border-t-transparent"></div>
    </div>

    <div class="flex h-screen overflow-hidden flex-col">
        
        {{-- هنا نضع نسخة السايد بار التي عدلناها للجوال --}}
        @include('mobile.layouts.sidebar') 

        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden pt-16 pb-20">
            
            <header class="fixed top-0 left-0 right-0 z-30 flex h-16 items-center justify-between border-b border-gray-200 bg-white/80 px-4 backdrop-blur-md dark:border-gray-800 dark:bg-black/80">
                {{-- زر فتح القائمة الجانبية --}}
                <button @click.stop="sidebarToggle = !sidebarToggle" class="p-2 text-gray-600 dark:text-gray-300">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                </button>

                {{-- اللوجو في المنتصف --}}
                <img src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" class="h-8 w-auto" alt="Logo">

                {{-- زر الإشعارات أو البروفايل المصغر --}}
                <div class="flex items-center gap-2">
                    @include('mobile.layouts.header') {{-- تأكد من وجود ملف مصغر للهيدر --}}
                </div>
            </header>

            <main class="p-4">
                @include('layouts.Breadcrumb')
                
                <div class="mt-2">
                    @yield('content')
                </div>
            </main>

            {{-- <x-modals.warning-modal /> --}}
        </div>

        <nav class="fixed bottom-0 left-0 right-0 z-30 flex h-16 items-center justify-around border-t border-gray-200 bg-white px-2 dark:border-gray-800 dark:bg-black shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
            <a href="{{ route('dashboard.index') }}" class="flex flex-col items-center gap-1 text-orange-500">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.69-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.06 1.06l8.69-8.69z" /></svg>
                <span class="text-[10px]">الرئيسية</span>
            </a>
            <a href="{{ route('shipment.index') }}" class="flex flex-col items-center gap-1 text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span class="text-[10px]">الطرود</span>
            </a>
            <button @click="window.dispatchEvent(new CustomEvent('open-transaction-modal'))" class="flex -translate-y-4 flex-col items-center justify-center rounded-full bg-orange-500 p-3 text-white shadow-lg ring-4 ring-white dark:ring-gray-900">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </button>
            <a href="{{ route('receipts.index') }}" class="flex flex-col items-center gap-1 text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                <span class="text-[10px]">استلام</span>
            </a>
            <button @click="sidebarToggle = true" class="flex flex-col items-center gap-1 text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                <span class="text-[10px]">المزيد</span>
            </button>
        </nav>

    </div>

    {{-- Modals --}}
    {{-- <div class="hidden">
        @include('closings.create-closing-modal')
        @include('transactions.create-transaction-modal')
    </div> --}}

    @yield('script')
    <script defer src="{{ asset('tailadmin/build/bundle.js') }}"></script>
</body>
</html>