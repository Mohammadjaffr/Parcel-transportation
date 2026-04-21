<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        @page { 
            size: 70mm 120mm; 
            margin: 2mm; /* هوامش صغيرة جداً لاستغلال المساحة */
        }
        body { 
            font-family: {!! $design['font_family'] ?? "'aealarabiya', 'dejavusans', sans-serif" !!};
            font-size: 6.5pt;
            direction: rtl;
            color: #0f172a;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 0.5mm; line-height: 1.2; }

        .text-brand { color: {{ $design['primary_color'] ?? '#ea580c' }}; }
        .bg-brand { background-color: {{ $design['primary_color'] ?? '#ea580c' }}; color: #ffffff; }

        .box-border { border: 0.5px solid #cbd5e1; }
        .box-header { background-color: #f1f5f9; text-align: center; font-weight: bold; border-bottom: 0.5px solid #cbd5e1; padding: 1mm 0; }
        
        .label { color: {{ $design['primary_color'] ?? '#ea580c' }}; font-weight: bold; width: 28%; }
        .value { font-weight: bold; width: 72%; }
        
        /* مربع رقم السند المميز */
        .bond-box { border: 1px solid {{ $design['primary_color'] ?? '#ea580c' }}; text-align: center; }
        .bond-header { background-color: {{ $design['primary_color'] ?? '#ea580c' }}; color: #fff; font-weight: bold; font-size: 6.5pt; padding: 0.5mm; }
        .bond-value { font-size: 11pt; font-weight: bold; padding: 1mm; }
    </style>
</head>
<body>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="40%" align="right" valign="top">
                <span style="font-size: 13pt; font-weight: bold;" class="text-brand">{{ $company['name'] ?? 'اسم الشركة' }}</span><br>
                <span style="font-size: 6pt; color: #64748b;">للنقل والشحن السريع</span><br>
                <span style="font-size: 6.5pt; font-weight: bold;" dir="ltr">{{ $company['headquarters']['phones'] ?? '---' }}</span>
            </td>
            
            <td width="25%" align="center" valign="middle">
                @if(isset($company['logo']) && $company['logo'])
                    <img src="{{ $company['logo'] }}" height="22" />
                @endif
            </td>
            
            <td width="35%" align="left" valign="top">
                <table class="bond-box" cellpadding="0" cellspacing="0">
                    <tr><td class="bond-header">رقم السند</td></tr>
                    <tr><td class="bond-value" dir="ltr">{{ $bond_number ?? '---' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table><tr><td height="2"></td></tr></table>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="48%" valign="top">
                <table class="box-border" cellpadding="1" cellspacing="0">
                    <tr><td colspan="2" class="box-header">بيانات المستلم</td></tr>
                    <tr>
                        <td class="label">الاسم:</td>
                        <td class="value">{{ $receiver_name ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الجوال:</td>
                        <td class="value" dir="ltr">{{ $receiver_phone ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الوجهة:</td>
                        <td class="value text-brand">{{ $receiver_branch ?? '---' }}</td>
                    </tr>
                </table>
            </td>
            
            <td width="4%"></td> <td width="48%" valign="top">
                <table class="box-border" cellpadding="1" cellspacing="0">
                    <tr><td colspan="2" class="box-header">بيانات المرسل</td></tr>
                    <tr>
                        <td class="label">الاسم:</td>
                        <td class="value">{{ $sender_name ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الجوال:</td>
                        <td class="value" dir="ltr">{{ $sender_phone ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الفرع:</td>
                        <td class="value">{{ $sender_branch ?? '---' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table><tr><td height="2"></td></tr></table>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="48%" valign="top">
                <table class="box-border" cellpadding="1" cellspacing="0">
                    <tr><td colspan="2" class="box-header">المالية</td></tr>
                    <tr>
                        <td class="label">الدفع:</td>
                        <td class="value text-brand">{{ $payment_method ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td class="label">الإجمالي:</td>
                        <td class="value" style="font-size: 8pt;">{{ $total_amount ?? '0' }}</td>
                    </tr>
                </table>
            </td>
            
            <td width="4%"></td> <td width="48%" valign="top">
                <table class="box-border" cellpadding="1" cellspacing="0">
                    <tr><td colspan="2" class="box-header">تفاصيل الشحنة</td></tr>
                    <tr>
                        <td class="label">التاريخ:</td>
                        <td class="value">{{ \Carbon\Carbon::parse($date ?? now())->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <td class="label">النوع/الوزن:</td>
                        <td class="value" dir="ltr">{{ $package_type ?? '-' }} | {{ $weight ?? '0.00' }} كجم</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table><tr><td height="2"></td></tr></table>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="font-size: 6.5pt;">
                <span class="font-bold text-brand">ملاحظات:</span> 
                <span style="font-weight: bold;">{{ (isset($notes) && $notes !== '-' && $notes !== '') ? $notes : 'لا توجد ملاحظات إضافية' }}</span>
            </td>
        </tr>
        <tr>
            <td align="center" class="footer-text" style="color: #475569; padding-top: 1.5mm;">
                @if(!empty($terms_and_conditions) && is_array($terms_and_conditions))
                    @foreach($terms_and_conditions as $term)
                        * {{ $term }} &nbsp;
                    @endforeach
                @else
                    * نحن غير مسؤولين عن الإجراءات الأمنية الخارجة عن إرادتنا. * نحن غير مسؤولين عن الأشياء الثمينة الممنوع إرسالها في الطرود. * التأكد من بيانات السند قبل المغادرة.
                @endif
            </td>
        </tr>
    </table>

    <table><tr><td height="4"></td></tr></table>

    <table width="100%" cellpadding="0" cellspacing="0" style="text-align: center; font-weight: bold; font-size: 6.5pt; color: #334155;">
        <tr>
            <td width="33%" class="text-brand">توقيع المستلم<br><span style="color:#cbd5e1">.............</span></td>
            <td width="34%">الموظف: {{ $creator_name ?? '---' }}<br><span style="color:#cbd5e1">.............</span></td>
            <td width="33%" class="text-brand">توقيع المرسل<br><span style="color:#cbd5e1">.............</span></td>
        </tr>
    </table>

</body>
</html>