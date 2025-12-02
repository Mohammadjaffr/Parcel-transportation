<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- خط عربي إضافي -->
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* تحسينات للغة العربية */
            body {
                font-family: 'Tajawal', 'Figtree', sans-serif;
            }
            
            /* تحسينات للاتجاه RTL */
            .rtl-support {
                text-align: right;
            }
            
            /* تحسينات للعناصر النصية */
            .text-right {
                text-align: right;
            }
            
            .text-left {
                text-align: left;
            }

            .orange-focus:focus {
                border-color: #f79009 !important;
                box-shadow: 0 0 0 2px #f79009 !important;
                outline: none !important;
            }

            .orange-checkbox {
                border-color: #f79009 !important;
                color: #f79009 !important;
            }


        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased rtl-support" style="background-color: #fffaeb;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg text-right">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>