<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        /* * Typography & Base Setup */
        body {
            font-family: {!! $design['font_family'] ?? "'aealarabiya', 'dejavusans', sans-serif" !!};
            font-size: 10pt;
            color: #334155;
            direction: rtl;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        
        .text-brand { color: {{ $design['primary_color'] ?? '#ea580c' }}; }
        .bg-brand { background-color: {{ $design['primary_color'] ?? '#ea580c' }}; }
        
        .text-slate-900 { color: #0f172a; }
        .text-slate-500 { color: #64748b; }
        .text-slate-400 { color: #94a3b8; }
        
        .bg-slate-50 { background-color: #f8fafc; }
        .bg-slate-800 { background-color: #1e293b; }
        
        .font-bold { font-weight: bold; }
        .text-xs { font-size: 8.5pt; }
        .text-sm { font-size: 9.5pt; }
        .text-base { font-size: 11pt; }
        .text-lg { font-size: 13pt; }
        .text-xl { font-size: 16pt; }
        .text-2xl { font-size: 24pt; }
        
        .label { color: #64748b; font-size: 9pt; font-weight: normal; }
        .value { color: #0f172a; font-size: 11pt; font-weight: bold; }
        .value-ltr { color: #0f172a; font-size: 11pt; font-weight: bold; direction: ltr; display: inline-block; }
    </style>
</head>
<body>

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="35%" align="right" valign="top">
                <span class="text-2xl font-bold text-brand" style="line-height: 1;">{{ $company['name'] ?? 'الشركة العالمية' }}</span><br>
                <br><br>
                <span class="text-sm font-bold text-slate-500" style="letter-spacing: 0.5px;">للنقل والشحن السريع</span>
                <br><br>
                <span class="text-xs text-slate-400">تاريخ الطباعة: <span dir="ltr">{{ $print_date ?? date('Y-m-d H:i') }}</span></span>
            </td>

            <td width="30%" align="center" valign="top">
                @if(isset($company['logo']) && $company['logo'])
                    <img src="{{ $company['logo'] }}" height="75" alt="Logo" />
                    <div style="height: 12px; line-height: 12px;">&nbsp;</div>
                @endif
                <span class="text-xl font-bold text-brand" style="background-color: {{ $design['bg_color'] ?? '#fffaf5' }}; padding: 4px 12px; border-radius: 4px; border: 1px solid #ffedd5;">إيصال استلام شحنة</span>
            </td>

            <td width="35%" align="center" valign="top" style="line-height: 1.6;">
                @if(!empty($company['main_branch']))
                    <div class="text-sm font-bold text-slate-900">{{ $company['main_branch']['title'] }}</div>
                    <div class="text-sm text-slate-700" style="margin-bottom: 8px; margin-top: 2px;">
                        <span dir="ltr" class="font-bold">{{ $company['main_branch']['phones'] }}</span>
                    </div>
                @endif
                
                @if(!empty($company['headquarters']))
                    <div class="text-xs font-bold text-slate-900">{{ $company['headquarters']['title'] }}</div>
                    <div class="text-xs text-slate-700" style="margin-bottom: 4px; margin-top: 2px;">
                        <span dir="ltr" class="font-bold">{{ $company['headquarters']['phones'] }}</span>
                    </div>
                @endif

                @if(!empty($company['other_phones']))
                    <div class="text-xs text-slate-700" style="margin-bottom: 4px;">
                        <span class="text-slate-500">أرقام الفروع:</span>
                        <span dir="ltr" class="font-bold text-brand" style="line-height: 1.6;">{{ $company['other_phones'] }}</span>
                    </div>
                @endif
                
                <div class="text-xs font-bold text-slate-900" style="margin-top: 6px;">خدمة الشحن إلى جميع المحافظات ودول الخليج</div>
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" cellpadding="12" cellspacing="0" border="0" class="bg-slate-50" style="border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
        <tr>
            <td width="33%" align="center" valign="middle">
                <span class="label">رقم الشحنة</span><br>
                <span class="text-lg value-ltr" style="letter-spacing: 1px; color: #0f172a;">{{ $bond_number ?? '---' }}</span>
            </td>
            
            <td width="34%" align="center" valign="middle" style="border-right: 1px solid #e2e8f0; border-left: 1px solid #e2e8f0;">
                <span class="label">تاريخ السند</span><br>
                <span class="text-lg value-ltr">{{ $date ?? '---' }}</span>
            </td>
            
            <td width="33%" align="center" valign="middle">
                <span class="label">المبلغ الإجمالي</span><br>
                <span class="text-xl font-bold text-brand">{{ $total_amount ?? '0' }}</span><br>
                <span class="text-xs text-slate-500">({{ $payment_method ?? '---' }})</span>
            </td>
        </tr>
    </table>

    <br><br>

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="48%" valign="top">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr><td height="4" class="bg-brand"></td></tr>
                    <tr>
                        <td class="bg-slate-50" style="padding: 16px; border: 1px solid #f1f5f9; border-top: none;">
                            <div class="text-base font-bold text-slate-900" style="margin-bottom: 12px; text-align: center;">بيانات المرسل</div>
                            <table width="100%" cellpadding="6" cellspacing="0" border="0">
                                <tr>
                                    <td width="25%" align="right" class="label">الاسم:</td>
                                    <td width="75%" align="right" class="value">{{ $sender_name ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <td width="25%" align="right" class="label">الجوال:</td>
                                    <td width="75%" align="right" class="value-ltr">{{ $sender_phone ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <td width="25%" align="right" class="label">الفرع:</td>
                                    <td width="75%" align="right" class="value text-brand">{{ $sender_branch ?? '---' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            
            <td width="4%"></td>
            
            <td width="48%" valign="top">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr><td height="4" class="bg-slate-800"></td></tr>
                    <tr>
                        <td class="bg-slate-50" style="padding: 16px; border: 1px solid #f1f5f9; border-top: none;">
                            <div class="text-base font-bold text-slate-900" style="margin-bottom: 12px; text-align: center;">بيانات المستلم</div>
                            <table width="100%" cellpadding="6" cellspacing="0" border="0">
                                <tr>
                                    <td width="25%" align="right" class="label">الاسم:</td>
                                    <td width="75%" align="right" class="value">{{ $receiver_name ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <td width="25%" align="right" class="label">الجوال:</td>
                                    <td width="75%" align="right" class="value-ltr">{{ $receiver_phone ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <td width="25%" align="right" class="label">الوجهة:</td>
                                    <td width="75%" align="right" class="value text-slate-900">{{ $receiver_branch ?? '---' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

    <div class="text-base font-bold text-slate-900" style="margin-bottom: 8px;">تفاصيل ومحتويات الشحنة</div>
    
    <table width="100%" cellpadding="10" cellspacing="0" border="0">
        <tr>
            <td width="15%" align="right" class="label" style="border-bottom: 1px solid #f1f5f9;">نوع الطرد:</td>
            <td width="35%" align="right" class="value" style="border-bottom: 1px solid #f1f5f9;">{{ $package_type ?? '---' }}</td>
            
            <td width="15%" align="right" class="label" style="border-bottom: 1px solid #f1f5f9;">الوزن / الكمية:</td>
            <td width="35%" align="right" class="value" style="border-bottom: 1px solid #f1f5f9;">{{ $weight ?? 'غير محدد' }}</td>
        </tr>

        @if(isset($honey_details) && $honey_details)
        <tr>
            <td width="15%" align="right" class="font-bold label text-brand" style="border-bottom: 1px solid #f1f5f9;">تفاصيل إضافية:</td>
            <td width="85%" align="right" class="value" colspan="3" style="border-bottom: 1px solid #f1f5f9;">{{ $honey_details }}</td>
        </tr>
        @endif

        @if(isset($payment_key) && $payment_key === 'partial_payment')
        <tr>
            <td width="15%" align="right" class="label" style="border-bottom: 1px solid #f1f5f9;">حالة الدفع:</td>
            <td width="85%" align="right" class="value" colspan="3" style="border-bottom: 1px solid #f1f5f9;">
                المبلغ المدفوع: <span style="color: #16a34a;">{{ $partial_amount ?? '0' }}</span> &nbsp;&nbsp;|&nbsp;&nbsp; 
                المبلغ المتبقي: <span style="color: #dc2626;">{{ $remaining_amount ?? '0' }}</span>
            </td>
        </tr>
        @endif

        <tr>
            <td width="15%" align="right" class="label" valign="top">الملاحظات:</td>
            <td width="85%" align="right" colspan="3" class="font-bold text-slate-500" style="line-height: 1.6;">
                {{ (isset($notes) && $notes !== '-' && $notes !== '') ? $notes : 'لا توجد ملاحظات إضافية مسجلة لهذه الشحنة.' }}
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" cellpadding="4" cellspacing="0" border="0" style="border-top: 1px solid #e2e8f0; padding-top: 10px;">
        <tr>
            <td colspan="2" class="text-sm font-bold text-slate-500" style="padding-bottom: 8px;">الشروط والأحكام (سياسة الشحن):</td>
        </tr>
  
        <tr>
            @php
                $termsCount = count($terms_and_conditions);
                $half = ceil($termsCount / 2);
                $firstHalf = array_slice($terms_and_conditions, 0, $half, true);
                $secondHalf = array_slice($terms_and_conditions, $half, null, true);
            @endphp
            <td width="50%" align="right" valign="top" class="text-xs text-slate-400" style="line-height: 1.6; padding-left: 10px;">
                @foreach($firstHalf as $index => $term)
                    {{ $index + 1 }}. {{ $term }}<br>
                @endforeach
            </td>
            <td width="50%" align="right" valign="top" class="text-xs text-slate-400" style="line-height: 1.6;">
                @foreach($secondHalf as $index => $term)
                    {{ $index + 1 }}. {{ $term }}<br>
                @endforeach
            </td>
        </tr>
    </table>

    <br><br>

    <table width="100%" cellpadding="2" border="0" style="text-align: center;">
        <tr>
            <td width="33%" class="text-sm font-bold text-slate-900">
                توقيع المستلم<br><br><br>
                <span style="color: #cbd5e1;">________________________</span>
            </td>
            <td width="34%" class="text-sm font-bold text-slate-900">
                مسؤول الصادر<br><br>
                <span class="text-xs text-slate-500">{{ $creator_name ?? '---' }}</span><br>
                <span style="color: #cbd5e1;">________________________</span>
            </td>
            <td width="33%" class="text-sm font-bold text-slate-900">
                توقيع المرسل<br><br><br>
                <span style="color: #cbd5e1;">________________________</span>
            </td>
        </tr>
    </table>

    <br>

    <table width="100%" cellpadding="6" border="0" style="border-top: 1px solid #f8fafc;">
        <tr>
            <td align="center" class="text-xs text-slate-400">
                تم إنشاء هذا المستند إلكترونياً عبر نظام {{ $company['name'] ?? 'الشركة' }} &nbsp;|&nbsp; 
                طبع بواسطة: {{ $creator_name ?? '---' }} &nbsp;|&nbsp; 
                رقم التتبع للعميل: <span dir="ltr" class="font-bold">{{ $bond_number ?? '---' }}</span>
            </td>
        </tr>
    </table>

</body>
</html>