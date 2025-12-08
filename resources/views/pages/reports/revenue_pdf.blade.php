<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            margin: 20px;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4f46e5;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            color: #4f46e5;
            margin: 10px 0;
        }
        
        .report-info {
            font-size: 12px;
            color: #6b7280;
            margin-top: 10px;
        }
        
        .filters-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 15px;
            margin: 15px 0;
            font-size: 12px;
        }
        
        .filter-item {
            margin: 5px 0;
        }
        
        .filter-label {
            font-weight: bold;
            color: #4b5563;
            display: inline-block;
            width: 80px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
            page-break-inside: auto;
        }
        
        thead {
            display: table-header-group;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        th {
            background-color: #4f46e5;
            color: white;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #4338ca;
        }
        
        td {
            padding: 8px 6px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tbody tr:hover {
            background-color: #f3f4f6;
        }
        
        .amount-cell {
            font-weight: bold;
            color: #059669;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
        
        .page-number:after {
            content: counter(page);
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
            font-style: italic;
        }
        
        .timestamp {
            font-size: 10px;
            color: #6b7280;
            text-align: left;
            float: left;
        }
        
        .report-meta {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }
    </style>
</head>

<body>

<div class="header">
    <div class="company-name">نظام إدارة الشحنات</div>
    <div class="report-title">تقرير الإيرادات المالية</div>
    
    <div class="report-meta">
        <div class="timestamp">تم الإنشاء: {{ \Carbon\Carbon::now()->format('Y-m-d h:i A') }}</div>
        <div class="page-info">الصفحة <span class="page-number"></span></div>
    </div>
</div>

{{-- <!-- معلومات الفلترة -->
<div class="filters-box">
    <div style="font-weight: bold; margin-bottom: 8px; color: #4f46e5;">معايير البحث:</div>
    <div class="filter-item">
        <span class="filter-label">الفترة:</span>
        {{ $request->from_date ? $request->from_date : 'بداية' }} 
        إلى 
        {{ $request->to_date ? $request->to_date : 'اليوم' }}
    </div>
    @if($request->branch)
    <div class="filter-item">
        <span class="filter-label">الفرع:</span>
        {{ $request->branch }}
    </div>
    @endif
</div> --}}

<!-- ملخص الإيرادات -->
<div class="summary-card">
    <div style="font-size: 14px; margin-bottom: 5px;">إجمالي الإيرادات</div>
    <div class="total-amount">{{ number_format($totalRevenue, 2) }} ر.ي</div>
    <div style="font-size: 12px; margin-top: 5px;">عدد الشحنات: {{ $shipments->count() }}</div>
</div>

<!-- جدول البيانات -->
@if($shipments->count() > 0)
<table>
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="12%">التاريخ</th>
            <th width="18%">المرسل</th>
            <th width="18%">المستلم</th>
            <th width="12%">المبلغ</th>
            <th width="35%">ملاحظات</th>
        </tr>
    </thead>

    <tbody>
        @foreach($shipments as $shipment)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $shipment->created_at->format('Y-m-d') }}<br>
                <small style="color: #6b7280; font-size: 9px;">{{ $shipment->created_at->format('h:i A') }}</small>
            </td>
            <td>{{ $shipment->sender_name }}</td>
            <td>{{ $shipment->receiver_name }}</td>
            <td class="amount-cell">{{ number_format($shipment->cod_amount, 2) }} ر.ي</td>
            <td style="text-align: right; font-size: 10px;">{{ $shipment->notes ?: '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    
    <tfoot>
        <tr style="background-color: #f1f5f9;">
            <td colspan="4" style="text-align: center; font-weight: bold; padding: 10px;">الإجمالي</td>
            <td colspan="2" style="text-align: right; font-weight: bold; color: #059669; padding: 10px;">
                {{ number_format($totalRevenue, 2) }} ر.ي
            </td>
        </tr>
    </tfoot>
</table>
@else
<div class="no-data">
    <p style="font-size: 16px; margin-bottom: 10px;">⚠️ لا توجد بيانات</p>
    <p>لم يتم العثور على شحنات تطابق معايير البحث المحددة</p>
</div>
@endif

<!-- التذييل -->
<div class="footer">
    <div>تم إنشاء هذا التقرير تلقائياً بواسطة نظام إدارة الشحنات</div>
    <div>© {{ date('Y') }} جميع الحقوق محفوظة</div>
    <div style="margin-top: 5px;">تاريخ الطباعة: {{ \Carbon\Carbon::now()->format('Y-m-d h:i A') }}</div>
</div>

</body>
</html>