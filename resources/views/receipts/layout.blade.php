<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'سند مرسال')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Tajawal', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6', // Primary Teal
                            600: '#0d9488',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts: Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* 🖨️ Print Specific Styles */
        @media print {
            body {
                background-color: white !important;
            }
            .print-hide {
                display: none !important;
            }
            .print-no-shadow {
                box-shadow: none !important;
            }
            .print-border {
                border: 1px solid #e2e8f0 !important;
            }
            @page {
                size: A4 portrait; /* or landscape depending on the receipt */
                margin: 0.5cm;
            }
            /* Hide print dialog URL and Page numbers */
            @page { margin-top: 0; margin-bottom: 0; }
            body { padding-top: 1cm; padding-bottom: 1cm; }
        }

        /* Table Aesthetics */
        .premium-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .premium-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-weight: 500;
        }
        .premium-table tr:last-child td {
            border-bottom: none;
        }
        .premium-table tbody tr:hover {
            background-color: #f8fafc;
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased flex items-center justify-center min-h-screen p-4 sm:p-8 print:p-0 print:block">

    <!-- Floating Print Button (Hidden on Print) -->
    <div class="fixed bottom-8 left-8 print-hide z-50">
        <button onclick="window.print()" class="bg-brand-600 hover:bg-brand-700 text-white font-bold py-4 px-6 rounded-full shadow-lg transition-transform transform hover:scale-105 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            طباعة السند
        </button>
    </div>

    <!-- Main Content -->
    @yield('content')

    <script>
        // Auto print when the page loads
        window.onload = function() {
            setTimeout(() => {
                // window.print();
            }, 500);
        }
    </script>
    @stack('scripts')
</body>
</html>
