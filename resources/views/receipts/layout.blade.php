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

<body class="flex justify-center items-center p-4 min-h-screen antialiased sm:p-8 print:p-0 print:block">

    <!-- تعريف متغيرات السند ديناميكياً لاستخدامها في اسم ملف الـ PDF المولد -->
    <script>
        window.receiptTitle = "{{ $title ?? '' }}";
        window.receiptNumber = "{{ $bond_number ?? ($package_number ?? ($tracking_code ?? ($trip_number ?? ''))) }}";
    </script>

    <!-- html-to-image & jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-to-image/1.11.11/html-to-image.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Floating Action Buttons (Hidden on Print) -->
    <div class="flex fixed bottom-8 left-8 z-50 flex-col gap-3 print-hide">
        {{-- Share PDF Button --}}
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

        {{-- Print Button --}}
        <button onclick="window.print()"
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
        async function sharePDF() {
            const btn = document.getElementById('sharePdfBtn');
            const btnText = document.getElementById('shareBtnText');
            const originalText = btnText.textContent;

            try {
                // Show loading state
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-wait');
                btnText.textContent = 'جاري التحويل...';

                // تحديد الحاوية الخاصة بالسند فقط وتجاهل الهوامش والأزرار الجانبية
                const content = document.getElementById('receipt-content') || document.body;

                // الحصول على أبعاد العنصر الحقيقية
                const rect = content.getBoundingClientRect();
                const imgWidth = rect.width;
                const imgHeight = rect.height;
                const isLandscape = imgWidth > imgHeight;

                const {
                    jsPDF
                } = window.jspdf;
                const orientation = isLandscape ? 'landscape' : 'portrait';
                const pdf = new jsPDF(orientation, 'mm', 'a4');

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();

                // حساب التناسب الملائم لاحتواء المحتوى بالكامل في الصفحة
                const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
                const finalWidth = imgWidth * ratio;
                const finalHeight = imgHeight * ratio;

                // توسيط السند عمودياً وأفقياً داخل صفحة الـ PDF
                const xOffset = (pdfWidth - finalWidth) / 2;
                const yOffset = (pdfHeight - finalHeight) / 2;

                // التقاط السند كصورة عالية الدقة باستخدام html-to-image لضمان سلامة ووضوح النصوص العربية
                const imgData = await htmlToImage.toJpeg(content, {
                    quality: 0.98,
                    pixelRatio: 3,
                    backgroundColor: '#ffffff',
                });

                pdf.addImage(imgData, 'JPEG', xOffset, yOffset, finalWidth, finalHeight);

                // توليد ملف الـ PDF كـ Blob
                const pdfBlob = pdf.output('blob');

                // الحصول على اسم الملف المناسب باللغة العربية
                let rawTitle = window.receiptTitle || document.title || 'سند';
                let rawNumber = window.receiptNumber || '';
                let fileBaseName = rawTitle.trim();
                if (rawNumber) {
                    fileBaseName += ' - رقم ' + rawNumber.trim();
                }

                // تنظيف اسم الملف من الرموز غير الصالحة للملفات (مع إبقاء الحروف العربية والمسافات)
                const fileName = fileBaseName.replace(/[\/\\?%*:|"<>\r\n]+/g, '').trim() + '.pdf';
                const normalizedFileName = fileName.normalize('NFC');

                // التحقق مما إذا كان المستخدم يتصفح من هاتف محمول لدعم مشاركة الويب
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                    .userAgent);

                let shareFileName = normalizedFileName;
                if (!isMobile) {
                    // على الكمبيوتر (Windows/Mac)، نستخدم اسماً إنجليزياً معبراً لتجنب مشكلة تشوه الحروف الناتجة عن نظام التشغيل
                    let englishTitle = 'Sanad';
                    if (rawTitle.includes('كشف') && rawTitle.includes('ركاب')) {
                        englishTitle = 'Passenger_List';
                    } else if (rawTitle.includes('طرد') || rawTitle.includes('استلام')) {
                        englishTitle = 'Shipment_Receipt';
                    } else if (rawTitle.includes('إرسالية') || rawTitle.includes('مكتب')) {
                        englishTitle = 'Office_Manifest';
                    }
                    shareFileName = englishTitle + (rawNumber ? '_' + rawNumber.trim() : '') + '.pdf';
                }

                // مشاركة الملف إن كانت الخدمة مدعومة
                if (navigator.share) {
                    const file = new File([pdfBlob], shareFileName, {
                        type: 'application/pdf'
                    });

                    if (navigator.canShare && navigator.canShare({
                            files: [file]
                        })) {
                        await navigator.share({
                            title: fileBaseName, // الاسم العربي الكامل يظهر كعنوان للمشاركة
                            text: fileBaseName, // يظهر النص بالعربي في الرسالة المرفقة
                            files: [file],
                        });
                        btnText.textContent = 'تمت المشاركة ✓';
                        setTimeout(() => {
                            btnText.textContent = originalText;
                        }, 2000);
                        return;
                    }
                }

                // الخيار البديل: تحميل الملف للمتصفح بالاسم العربي الصحيح
                const url = URL.createObjectURL(pdfBlob);
                const link = document.createElement('a');
                link.href = url;
                link.download = normalizedFileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);

                btnText.textContent = 'تم التنزيل ✓';
                setTimeout(() => {
                    btnText.textContent = originalText;
                }, 2000);

            } catch (error) {
                console.error('Error generating PDF:', error);
                btnText.textContent = 'حدث خطأ!';
                setTimeout(() => {
                    btnText.textContent = originalText;
                }, 2000);
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-wait');
            }
        }
    </script>

    <!-- Main Content -->
    <div id="receipt-content" class="w-full max-w-6xl">
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
