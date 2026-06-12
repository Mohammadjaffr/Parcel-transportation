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
<body class="flex justify-center items-center p-4 min-h-screen antialiased sm:p-8 print:p-0 print:block">

    <!-- html2canvas & jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Floating Action Buttons (Hidden on Print) -->
    <div class="flex fixed bottom-8 left-8 z-50 flex-col gap-3 print-hide">
        {{-- Share PDF Button --}}
        {{-- <button id="sharePdfBtn" onclick="sharePDF()" class="flex gap-2 items-center px-6 py-4 font-bold text-white bg-indigo-600 rounded-full shadow-lg transition-all transform hover:bg-indigo-700 hover:scale-105 group">
            <svg class="w-6 h-6 transition-transform group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
            </svg>
            <span id="shareBtnText">مشاركة PDF</span>
        </button> --}}

        {{-- Print Button --}}
        <button onclick="window.print()" class="flex gap-2 items-center px-6 py-4 font-bold text-white rounded-full shadow-lg transition-all transform bg-brand-600 hover:bg-brand-700 hover:scale-105">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            طباعة السند
        </button>
    </div>

    <script>
        async function sharePDF() {
            const btn = document.getElementById('sharePdfBtn');
            const btnText = document.getElementById('shareBtnText');
            const originalText = btnText.textContent;

            try {
                // Show loading state
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-wait');
                btnText.textContent = 'جاري التحويل...';

                // Hide the action buttons temporarily
                const actionBtns = btn.parentElement;
                actionBtns.style.display = 'none';

                // Capture the page content
                const content = document.body;
                const canvas = await html2canvas(content, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    scrollX: 0,
                    scrollY: -window.scrollY,
                    windowWidth: document.body.scrollWidth,
                    windowHeight: document.body.scrollHeight,
                });

                // Show buttons again
                actionBtns.style.display = 'flex';

                // Determine PDF orientation based on canvas dimensions
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;
                const isLandscape = imgWidth > imgHeight;

                const { jsPDF } = window.jspdf;
                const orientation = isLandscape ? 'landscape' : 'portrait';
                const pdf = new jsPDF(orientation, 'mm', 'a4');

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();

                // Calculate scaling to fit content
                const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
                const finalWidth = imgWidth * ratio;
                const finalHeight = imgHeight * ratio;

                // Center the content on the page
                const xOffset = (pdfWidth - finalWidth) / 2;

                const imgData = canvas.toDataURL('image/jpeg', 0.95);

                // If content is taller than one page, split across multiple pages
                if (finalHeight > pdfHeight) {
                    const pageContentHeight = pdfHeight / ratio;
                    let position = 0;
                    let pageNum = 0;

                    while (position < imgHeight) {
                        if (pageNum > 0) {
                            pdf.addPage();
                        }

                        // Create a temporary canvas for each page slice
                        const sliceHeight = Math.min(pageContentHeight, imgHeight - position);
                        const tempCanvas = document.createElement('canvas');
                        tempCanvas.width = imgWidth;
                        tempCanvas.height = sliceHeight;
                        const ctx = tempCanvas.getContext('2d');
                        ctx.drawImage(canvas, 0, position, imgWidth, sliceHeight, 0, 0, imgWidth, sliceHeight);

                        const sliceData = tempCanvas.toDataURL('image/jpeg', 0.95);
                        const sliceRatio = Math.min(pdfWidth / imgWidth, pdfHeight / sliceHeight);
                        const sliceW = imgWidth * sliceRatio;
                        const sliceH = sliceHeight * sliceRatio;
                        const sliceX = (pdfWidth - sliceW) / 2;

                        pdf.addImage(sliceData, 'JPEG', sliceX, 0, sliceW, sliceH);
                        position += sliceHeight;
                        pageNum++;
                    }
                } else {
                    pdf.addImage(imgData, 'JPEG', xOffset, 0, finalWidth, finalHeight);
                }

                // Generate the PDF blob
                const pdfBlob = pdf.output('blob');
                const now = new Date();
                const dateStr = now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0') + '_' +
                    String(now.getHours()).padStart(2, '0') +
                    String(now.getMinutes()).padStart(2, '0') +
                    String(now.getSeconds()).padStart(2, '0');
                const fileName = 'سند_' + dateStr + '.pdf';

                // Try Web Share API first (works on mobile & modern browsers)
                if (navigator.share && navigator.canShare) {
                    const file = new File([pdfBlob], fileName, { type: 'application/pdf' });

                    if (navigator.canShare({ files: [file] })) {
                        await navigator.share({
                            title: document.title || 'سند',
                            text: 'مشاركة السند',
                            files: [file],
                        });
                        btnText.textContent = 'تمت المشاركة ✓';
                        setTimeout(() => { btnText.textContent = originalText; }, 2000);
                        return;
                    }
                }

                // Fallback: Download the PDF
                const url = URL.createObjectURL(pdfBlob);
                const link = document.createElement('a');
                link.href = url;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);

                btnText.textContent = 'تم التنزيل ✓';
                setTimeout(() => { btnText.textContent = originalText; }, 2000);

            } catch (error) {
                console.error('Error generating PDF:', error);
                btnText.textContent = 'حدث خطأ!';
                setTimeout(() => { btnText.textContent = originalText; }, 2000);

                // Make sure buttons are visible
                btn.parentElement.style.display = 'flex';
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-wait');
            }
        }
    </script>

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
