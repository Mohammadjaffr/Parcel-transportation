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
                size: A4 portrait;
                /* or landscape depending on the receipt */
                margin: 0.5cm;
            }

            /* Hide print dialog URL and Page numbers */
            @page {
                margin-top: 0;
                margin-bottom: 0;
            }

            body {
                padding-top: 1cm;
                padding-bottom: 1cm;
            }
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

<body class="{{ !empty($is_pdf) ? 'block p-0 bg-white' : 'flex justify-center items-center p-4 sm:p-8' }} min-h-screen antialiased print:p-0 print:block">

    <!-- تعريف متغيرات السند ديناميكياً لاستخدامها في اسم ملف الـ PDF المولد -->
    <script>
        window.receiptTitle = "{{ $title ?? '' }}";
        window.receiptNumber = "{{ $bond_number ?? ($package_number ?? ($tracking_code ?? ($trip_number ?? ''))) }}";
    </script>

    <!-- Floating Action Buttons (Hidden on Print) -->
    <div class="flex fixed bottom-8 left-8 z-50 flex-col gap-3 print-hide">
        {{-- Share PDF Button (Mobile Only) --}}
        @if (!empty($isMobile))
            <button id="sharePdfBtn" onclick="sharePDF()"
                class="flex gap-2 items-center px-6 py-4 font-bold text-white bg-indigo-600 rounded-full shadow-lg transition-all transform hover:bg-indigo-700 hover:scale-105 group">
                <svg class="w-6 h-6 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                    </path>
                </svg>
                <span id="shareBtnText">مشاركة PDF</span>
            </button>
        @endif

        {{-- Print Button --}}
        <button onclick="printHeadless(this)"
            class="flex gap-2 items-center px-6 py-4 font-bold text-white rounded-full shadow-lg transition-all transform bg-brand-600 hover:bg-brand-700 hover:scale-105">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            طباعة السند
        </button>
    </div>

    <script>
        async function printHeadless(btn) {
            const originalHtml = btn.innerHTML;
            try {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-wait');
                btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> جاري التجهيز...`;

                const pdfUrl = window.location.pathname.replace(/\/$/, '') + '/pdf' + window.location.search;
                const response = await fetch(pdfUrl, { cache: 'no-store' });
                if (!response.ok) throw new Error('فشل التنزيل');
                
                const blob = await response.blob();
                const blobUrl = URL.createObjectURL(blob);
                
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = blobUrl;
                document.body.appendChild(iframe);
                
                iframe.onload = () => {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                        URL.revokeObjectURL(blobUrl);
                    }, 15000); 
                };
            } catch (error) {
                console.error(error);
                alert('حدث خطأ أثناء التجهيز للطباعة.');
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-wait');
                btn.innerHTML = originalHtml;
            }
        }

        let preparedShareFile = null;
        let preparedShareTitle = '';

        async function sharePDF() {
            const btn = document.getElementById('sharePdfBtn');
            const btnText = document.getElementById('shareBtnText');
            const originalText = 'مشاركة PDF';

            // إذا كان الملف جاهزاً مسبقاً (النقرة الثانية)، نشارك فوراً متجاوزين حظر المتصفح
            if (preparedShareFile && navigator.share) {
                try {
                    await navigator.share({
                        title: preparedShareTitle,
                        text: preparedShareTitle,
                        files: [preparedShareFile],
                    });
                    btnText.textContent = 'تمت المشاركة ✓';
                    setTimeout(() => { 
                        btnText.textContent = originalText; 
                        btn.classList.replace('bg-emerald-600', 'bg-indigo-600');
                        btn.classList.replace('hover:bg-emerald-700', 'hover:bg-indigo-700');
                        preparedShareFile = null; 
                    }, 3000);
                } catch (e) {
                    console.log('Share cancelled', e);
                }
                return;
            }

            try {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-wait');
                btnText.textContent = 'جاري تجهيز الملف...';

                const pdfUrl = window.location.pathname.replace(/\/$/, '') + '/pdf' + window.location.search;
                const response = await fetch(pdfUrl, { cache: 'no-store' });
                if (!response.ok) throw new Error('فشل التنزيل');
                
                const blob = await response.blob();

                let rawTitle = window.receiptTitle || document.title || 'سند';
                let rawNumber = window.receiptNumber || '';
                let fileBaseName = rawTitle.trim();
                if (rawNumber) fileBaseName += ' - رقم ' + rawNumber.trim();

                let englishTitle = 'Sanad';
                if (rawTitle.includes('كشف')) englishTitle = 'Manifest';
                else if (rawTitle.includes('طرد')) englishTitle = 'Receipt';
                
                const shareFileName = englishTitle + (rawNumber ? '_' + rawNumber.trim() : '') + '.pdf';

                if (navigator.share && navigator.canShare && navigator.canShare({ files: [new File([blob], shareFileName, { type: 'application/pdf' })] })) {
                    // المتصفح يدعم المشاركة: نحفظ الملف ونطلب من المستخدم النقر مرة أخرى
                    preparedShareFile = new File([blob], shareFileName, { type: 'application/pdf' });
                    preparedShareTitle = fileBaseName;
                    
                    btn.classList.replace('bg-indigo-600', 'bg-emerald-600');
                    btn.classList.replace('hover:bg-indigo-700', 'hover:bg-emerald-700');
                    btnText.textContent = 'الملف جاهز! انقر للمشاركة';
                } else {
                    // المتصفح لا يدعم المشاركة: نقوم بالتنزيل المباشر
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = shareFileName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    btnText.textContent = 'تم التنزيل ✓';
                    setTimeout(() => { btnText.textContent = originalText; }, 3000);
                }
            } catch (error) {
                console.error('Error:', error);
                btnText.textContent = 'حدث خطأ!';
                setTimeout(() => { btnText.textContent = originalText; }, 2000);
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-wait');
            }
        }
    </script>

    <!-- Main Content -->
    <div id="receipt-content" class="w-full {{ !empty($is_pdf) ? 'max-w-none' : 'max-w-6xl' }}">
        @yield('content')
    </div>

    @if (empty($is_pdf))
        <script>
            // Auto print when the page loads (disabled by default)
            window.onload = function() {
                setTimeout(() => {
                    // window.print();
                }, 500);
            }
        </script>
    @endif
    @stack('scripts')
</body>

</html>
