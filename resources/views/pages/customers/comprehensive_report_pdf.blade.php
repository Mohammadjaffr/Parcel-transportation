<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        /* إعدادات الخط والصفحة */
        body {
            font-family: 'aealarabiya', 'dejavusans', sans-serif;
            direction: rtl;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.5;
            font-size: 10pt;
        }

        /* الهيدر الاحترافي */
        .header-table {
            width: 100%;
            border-bottom: 3.5px solid #fb6514;
            margin-bottom: 25px;
            padding-bottom: 10px;
        }

        .brand-name {
            color: #fb6514;
            font-size: 26pt;
            font-weight: bold;
            margin: 0;
            line-height: 1;
        }

        .brand-subtitle {
            color: #333;
            font-size: 11pt;
            font-weight: bold;
            margin-top: 2px;
        }

        .header-extra {
            font-size: 8pt;
            color: #555;
            margin-top: 3px;
        }

        .header-phones {
            font-size: 9pt;
            font-weight: bold;
            color: #222;
            margin-top: 5px;
            direction: ltr;
        }

        .document-title-box {
            background-color: #333;
            color: #fff;
            padding: 6px 15px;
            font-size: 12pt;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
        }

        /* صناديق المعلومات (المالية والإحصائيات) */
        .info-box {
            width: 100%;
            background-color: #fdfdfd;
            border: 1px solid #e5e7eb;
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 6px;
        }

        .info-table {
            width: 100%;
        }

        .label {
            color: #fb6514;
            font-weight: bold;
            font-size: 10.5pt;
        }

        .value {
            font-weight: bold;
            font-size: 11.5pt;
            color: #111;
        }

        /* العناوين الفرعية للأقسام */
        .section-header {
            background-color: #f3f4f6;
            color: #111;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 12pt;
            border-right: 6px solid #fb6514;
            margin-top: 30px;
            margin-bottom: 15px;
            border-radius: 2px;
        }

        /* جداول البيانات */
        .manifest-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .manifest-table th {
            background-color: #374151;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: 1px solid #1f2937;
            padding: 10px 5px;
            font-size: 10pt;
        }

        .manifest-table td {
            border: 1px solid #e5e7eb;
            padding: 8px 5px;
            text-align: center;
            font-size: 9.5pt;
            vertical-align: middle;
        }

        /* الbadges */
        .debt-badge {
            padding: 4px 12px;
            border-radius: 20px;
            color: #fff;
            font-size: 9.5pt;
            font-weight: bold;
            text-align: center;
            display: block;
        }

        .debt-danger {
            background-color: #ef4444;
        }

        .debt-success {
            background-color: #10b981;
        }

        /* التواقيع */
        .signatures-table {
            width: 100%;
            margin-top: 50px;
        }

        .sig-title {
            font-size: 11pt;
            font-weight: bold;
            color: #374151;
            margin-bottom: 45px;
        }

        .sig-line {
            width: 70%;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <!-- الهيدر -->
    <table class="header-table" cellpadding="0">
        <tr>
            <!-- معلومات الشركة -->
            <td width="38%" style="vertical-align: top;">
                <h1 class="brand-name">الـزاجـل</h1>
                <div class="brand-subtitle">للنقل والشحن السريع</div>
                <div class="header-extra">إلى جميع المحافظات ودول الخليج</div>
                <div class="header-extra">الفرع الرئيسي: حضرموت - القطن</div>
                <div class="header-phones">781216757 - 730831802</div>
                <div class="header-phones">773136727 - 781989021</div>
            </td>

            <!-- اللوجو في المنتصف -->
            <td width="24%" style="text-align: center; vertical-align: middle;">
                <img src="{{ public_path('images/new.svg') }}" style="width: 80px; height: auto;">
                <br>
                <div class="document-title-box">تقرير عميل شامل</div>
            </td>

            <!-- معلومات العميل والفرع -->
            <td width="38%" style="text-align: left; vertical-align: top;">
                <div style="font-weight: bold; color: #111; font-size: 11pt;">الفرع:
                    {{ $customer->branch->name ?? $customer->branch_code }}</div>
                <div style="margin-top: 8px; color: #666; font-size: 9pt;">بيانات العميل:</div>
                <div style="font-size: 13pt; font-weight: bold; color: #000; margin-top: 2px;">{{ $customer->name }}
                </div>
                <div style="color: #fb6514; font-weight: bold; font-size: 12pt; margin-top: 4px; direction: ltr;">
                    {{ $customer->phone }}</div>
                <div style="font-size: 8.5pt; color: #999; margin-top: 10px;">
                    تاريخ الطباعة: {{ date('Y-m-d H:i') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- ملخص الرصيد المالي والإحصائيات -->
    <div class="info-box">
        <table class="info-table" cellpadding="5">
            <tr>
                <td width="25%" class="label">إجمالي الشحنات:</td>
                <td width="25%" class="value" style="font-size: 13pt;">{{ $sentCount + $receivedCount }}</td>

                @if ($isDebtor)
                    <!-- حالة وجود مديونية -->
                    <td width="25%" class="label">مرسلة / مستقبلة:</td>
                    <td width="25%" class="value">{{ $receivedCount }} / {{ $sentCount }}</td>
                    
                @else
                    <!-- حالة الرصيد المسدد -->
                    <td width="15%" class="label">مرسلة / مستقبلة:</td>
                    <td width="15%" class="value">{{ $receivedCount }} / {{ $sentCount }}</td>
                    <td width="15%" class="label">المبلغ:</td>
                    <td width="35%" style="text-align: right; color: #10b981; font-weight: bold;">
                        الرصيد صافي (لا يوجد مديونيه)
                    </td>
                @endif
            </tr>
        </table>
    </div>

    <!-- جداول البيانات التفصيلية -->
    <div class="section-header">الشحنات المرسلة ({{ $sentCount }})</div>
    @if ($sentShipments->count() > 0)
        <table class="manifest-table" border="0.5" cellpadding="6">
            <thead>
                <tr>
                    <th width="13%">#</th>
                    <th width="12%">التاريخ</th>
                    <th width="12%">رقم السند</th>
                    <th width="12%">فرع المستلم</th>
                    <th width="12%">قيمة الشحنة</th>
                    <th width="13%">طريقة الدفع</th>
                    <th width="13%">المدفوع</th>
                    <th width="13%">المتبقي</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sentShipments as $shipment)
                    @php
                        $paid = $shipment->payments->sum('amount');
                        $remaining = ($shipment->total_amount ?? 0) - $paid;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $shipment->created_at->format('Y-m-d') }}</td>
                        <td style="color: #fb6514; font-weight: bold;">{{ $shipment->bond_number }}</td>
                        <td>{{ $shipment->receiverBranch->name ?? '---' }}</td>
                        <td style="font-weight: bold;">{{ number_format($shipment->total_amount ?? 0) }}</td>
                        <td>
                            @if ($shipment->payment_method == 'prepaid')
                                دفع مسبق
                            @elseif($shipment->payment_method == 'cod')
                                دفع عند الاستلام
                            @elseif($shipment->payment_method == 'partial_payment')
                                دفع جزئي
                            @elseif($shipment->payment_method == 'customer_credit')
                                آجل (دين)
                            @else
                                {{ $shipment->payment_method }}
                            @endif
                        </td>
                        <td>
                            @if ($shipment->payment_method == 'cod')
                                ---
                            @else
                                {{ number_format($paid) }}
                            @endif
                        </td>
                        <td style="color: {{ $remaining > 0 ? '#dc2626' : '#10b981' }}; font-weight: bold;">
                            @if ($shipment->payment_method == 'cod')
                                ---
                            @elseif ($remaining > 0)
                                {{-- {{ number_format($remaining) }} --}}
                                ---
                            @else
                                مسدد
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr style="background-color: #f9fafb; font-weight: bold;">
                    <td colspan="4" style="text-align: left; padding-left: 15px;">إجمالي المبلغ :</td>
                    
                    <td style="color: #111;">{{ number_format($sentTotal) }}</td>
                    <td colspan="3" style="text-align: center; color: {{ $balance > 0 ? '#dc2626' : '#10b981' }}; font-weight: bold;"><span class="lable">الاجمالي المتبقي: </span>{{ number_format(abs($balance)) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div style="text-align: center; border: 1.5px dashed #e5e7eb; padding: 20px; color: #9ca3af; font-size: 11pt;">
            لا توجد شحنات مرسلة مسجلة</div>
    @endif

 

    <!-- قسم التواقيع والاعتمادات -->
    <table class="signatures-table">
        <tr>
            <td width="33.3%" style="text-align: center;">
                <div class="sig-title">توقيع وموافقة العميل</div>
                <div class="sig-line"></div>
            </td>
            <td width="33.4%" style="text-align: center;">
                <div class="sig-title">توقيع المحاسب المختص</div>
                <div class="sig-line"></div>
            </td>
            <td width="33.3%" style="text-align: center;">
                <div class="sig-title">ختم فرع الشركة</div>
                <div style="height: 60px;"></div>
            </td>
        </tr>
    </table>

    <div
        style="position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8.5pt; color: #6b7280; padding-top: 15px; border-top: 1px solid #e5e7eb;">
        نظام الزاجل الذكي - تم إنشاء هذا التقرير آلياً بواسطة: {{ auth()->user()->name ?? 'نظام الإدارة' }}
    </div>

</body>

</html>
