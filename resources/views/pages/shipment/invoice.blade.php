<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند استلام - {{ $shipment->bond_number }}</title>
    
    <style>
        /* === الإعدادات الأساسية === */
        body {
            font-family: 'aealarabiya', 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 12px;
            color: #111;
            margin: 0;
            padding: 15px;
            line-height: 1.5;
        }

        table { width: 100%; border-collapse: collapse; }
        th, td { vertical-align: middle; }
        
        .text-orange { color: #fb6514; }
        
        /* === الترويسة === */
        .header-title { font-size: 36px; font-weight: bold; color: #fb6514; margin: 0 0 5px 0; line-height: 1; }
        .header-subtitle { font-size: 15px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .header-info { font-size: 11px; color: #555; font-weight: bold; line-height: 1.6; }
        .logo-img { width: 180px; height: auto; max-height: 90px; object-fit: contain; }

        /* === عنوان الفاتورة === */
        .doc-title-container { text-align: center; margin: 20px 0; }
        .doc-title {
            background-color: #fb6514;
            color: #fff;
            padding: 8px 30px;
            border-radius: 6px;
            font-size: 20px;
            font-weight: bold;
            display: inline-block;
            box-shadow: 0 3px 6px rgba(251, 101, 20, 0.3);
        }

        /* === شريط الخلاصة (Summary Box) === */
        .summary-table {
            width: 100%;
            background-color: #fff8f4;
            border: 2px solid #fb6514;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .summary-table td {
            padding: 12px 10px;
            text-align: center;
            border-left: 1px solid #fecba1; /* فاصل برتقالي فاتح */
        }
        .summary-table td:last-child { border-left: none; }
        .summary-label { font-size: 12px; font-weight: bold; color: #fb6514; display: block; margin-bottom: 5px; }
        .summary-val { font-size: 18px; font-weight: bold; color: #000; }
        .summary-sub { font-size: 11px; color: #555; font-weight: bold; display: block; margin-top: 2px; }

        /* === الجداول المنظمة (Data Tables) === */
        .data-table {
            width: 100%;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #fff8f4;
            color: #fb6514;
            font-weight: bold;
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        .data-table td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 13px;
            font-weight: bold;
            color: #222;
        }
        .label-cell {
            width: 15%;
            color: #666 !important;
            background-color: #fafafa;
            font-weight: normal !important;
        }

        /* === التواقيع والشروط === */
        .signatures { margin-top: 25px; margin-bottom: 30px; text-align: center; font-weight: bold; color: #555; }
        .sig-line { border-bottom: 1.5px dashed #fb6514; width: 60%; margin: 40px auto 0; }
        
        .terms-box {
            font-size: 11px;
            line-height: 1.8;
            color: #555;
            background: #fff8f4;
            padding: 12px 15px;
            border-right: 4px solid #fb6514;
            border-radius: 4px;
            font-weight: bold;
        }
        .footer-strip {
            background-color: #fb6514;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 15px;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <table style="margin-bottom: 10px;">
        <tr>
            <td style="width: 35%; text-align: right;">
                <h1 class="header-title">الزاجل</h1>
                <div class="header-subtitle">للنقل والشحن السريع</div>
                <div class="header-info">
                    خدمة الشحن إلى جميع المحافظات ودول الخليج<br>
                    <span style="color: #fb6514;">للتواصل:</span> 774996316 - 772038561 - 735637947
                </div>
            </td>

            <td style="width: 30%; text-align: center;">
                <img src="{{ public_path('storage/' . auth()->user()->app->logo) }}" class="logo-img" alt="Logo">
            </td>

            <td style="width: 35%; text-align: left;">
                <div class="header-info" style="text-align: right; display: inline-block;">
                    <span style="color: #fb6514; font-size: 13px;">فرع القطن:</span><br>
                    عمارة شظي - خلف بنك التضامن<br>
                    <span dir="ltr">781216757 - 773136727 - 730831802</span><br><br>
                    
                    <span style="color: #fb6514; font-size: 13px;">فرع المكلا:</span><br>
                    اربعين شقة - بجانب بنك أمجاد<br>
                    <span dir="ltr">774996316 - 772038561</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="doc-title-container">
        <div class="doc-title">سند إستلام بضاعة</div>
    </div>

    <table class="summary-table">
        <tr>
            <td style="width: 33%;">
                <span class="summary-label">رقم السند</span>
                <span class="summary-val text-orange" style="font-size: 22px;">#{{ $shipment->bond_number }}</span>
            </td>
            <td style="width: 34%;">
                <span class="summary-label">المبلغ الإجمالي</span>
                <span class="summary-val">{{ number_format($shipment->total_amount, 2) }} <span style="font-size: 14px;">ريال</span></span>
                
                <span class="summary-sub text-orange">
                    @if ($shipment->payment_method == 'prepaid') (مدفوع نقداً)
                    @elseif ($shipment->payment_method == 'cod') (آجل على المستلم - COD)
                    @elseif ($shipment->payment_method == 'customer_credit') (آجل على العميل)
                    @elseif ($shipment->payment_method == 'partial_payment')
                        @php
                            $paid = $shipment->payments->sum('amount');
                            $remaining = $shipment->total_amount - $paid;
                        @endphp
                        (نقد: {{ number_format($paid) }} | متبقي: {{ number_format($remaining) }})
                    @endif
                </span>
            </td>
            <td style="width: 33%;">
                <span class="summary-label">تاريخ الإصدار</span>
                <span class="summary-val" style="font-size: 16px;">{{ $shipment->created_at->format('Y/m/d') }}</span>
                <span class="summary-sub">{{ $shipment->created_at->format('h:i A') }}</span>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <tr>
            <th colspan="2" style="width: 50%; text-align: right;">بيانات المُرسل</th>
            <th colspan="2" style="width: 50%; text-align: right; border-right: 2px solid #fb6514;">بيانات المُستلم</th>
        </tr>
        <tr>
            <td class="label-cell">الاسم:</td>
            <td>{{ $shipment->senderCustomer->name ?? ($shipment->sender_name ?? '---') }}</td>
            
            <td class="label-cell" style="border-right: 2px solid #fb6514;">الاسم:</td>
            <td>{{ $shipment->receiverCustomer->name ?? ($shipment->receiver_name ?? '---') }}</td>
        </tr>
        <tr>
            <td class="label-cell">رقم الجوال:</td>
            <td dir="ltr" style="text-align: right;">{{ $shipment->senderCustomer->phone ?? ($shipment->sender_phone ?? '---') }}</td>
            
            <td class="label-cell" style="border-right: 2px solid #fb6514;">رقم الجوال:</td>
            <td dir="ltr" style="text-align: right;">{{ $shipment->receiverCustomer->phone ?? ($shipment->receiver_phone ?? '---') }}</td>
        </tr>
        <tr>
            <td class="label-cell">فرع الإرسال:</td>
            <td>{{ $shipment->senderBranch->name ?? '---' }}</td>
            
            <td class="label-cell" style="border-right: 2px solid #fb6514;">الوجهة:</td>
            <td>{{ $shipment->receiverBranch->name ?? '---' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <tr>
            <th colspan="4" style="text-align: right;">تفاصيل ومحتوى الطرد</th>
        </tr>
        <tr>
            <td class="label-cell">نوع الرسالة:</td>
            <td>{{ $shipment->package_type ?? 'غير محدد' }}</td>
            <td class="label-cell">الرمز الخاص:</td>
            <td style="color: #fb6514; font-family: monospace; font-size: 15px;">{{ $shipment->code ?? '---' }}</td>
        </tr>
        <tr>
            <td class="label-cell">عدد الجوالين:</td>
            <td>{{ $shipment->no_gallons_honey ?: '0' }}</td>
            <td class="label-cell">عدد القروف:</td>
            <td>{{ $shipment->no_honey_jars ?: '0' }}</td>
        </tr>
        <tr>
            <td class="label-cell">الملاحظات:</td>
            <td colspan="3" style="color: #444;">{{ $shipment->notes ?? 'لا توجد ملاحظات مسجلة.' }}</td>
        </tr>
    </table>

    <table class="signatures">
        <tr>
            <td style="width: 33%;">
                توقيع المُرسل
                <div class="sig-line"></div>
            </td>
            <td style="width: 34%;">
                ختم الشركة
                <div class="sig-line"></div>
            </td>
            <td style="width: 33%;">
                توقيع وتختيم الموظف
                <div class="sig-line"></div>
            </td>
        </tr>
    </table>

    <div class="terms-box">
        * نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا.<br>
        * نحن غير مسؤولين عن الأشياء الثمينة الممنوع إرسالها في الطرود.<br>
        * نحن غير مسؤولين عن بقاء الطرود أكثر من شهر.<br>
        * نحن غير مسؤولين عن الحريق وحوادث السير.<br>
        * الرجاء التأكد من بيانات السند قبل المغادرة.
    </div>

    <div class="footer-strip">
        أرقام الإدارة العامة لجميع الفروع: <span dir="ltr" style="letter-spacing: 1px;">781216757 - 773136727 - 774996316 - 773374176</span>
    </div>

</body>
</html>