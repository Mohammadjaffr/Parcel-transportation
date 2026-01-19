<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #4f46e5;
            font-size: 20px;
            margin: 5px 0;
        }

        .header p {
            margin: 3px 0;
            color: #666;
        }

        .info-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid #e0e0e0;
        }

        .info-row {
            margin: 5px 0;
        }

        .label {
            font-weight: bold;
            color: #4f46e5;
            display: inline-block;
            width: 100px;
        }

        .financial-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }

        .financial-item {
            display: inline-block;
            margin: 0 15px;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .debt-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 11px;
        }

        .debt-status.debtor {
            background: #ef4444;
            color: white;
        }

        .debt-status.cleared {
            background: #10b981;
            color: white;
        }

        .section-title {
            background: #4f46e5;
            color: white;
            padding: 10px;
            margin: 20px 0 10px 0;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 9px;
        }

        th {
            background: #f3f4f6;
            color: #374151;
            padding: 8px 5px;
            text-align: center;
            border: 1px solid #e5e7eb;
            font-weight: bold;
            font-size: 9px;
        }

        td {
            border: 1px solid #e5e7eb;
            padding: 6px 5px;
            text-align: center;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .payment-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .payment-prepaid {
            background: #dbeafe;
            color: #1e40af;
        }

        .payment-cod {
            background: #fef3c7;
            color: #92400e;
        }

        .payment-credit {
            background: #fce7f3;
            color: #9f1239;
        }

        .stats-grid {
            margin: 15px 0;
        }

        .stat-item {
            display: inline-block;
            width: 30%;
            text-align: center;
            padding: 10px;
            margin: 5px;
            background: #f3f4f6;
            border-radius: 5px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #4f46e5;
        }

        .stat-label {
            font-size: 8px;
            color: #6b7280;
            margin-top: 3px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #9ca3af;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #9ca3af;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <h1>تقرير شامل للعميل</h1>
        <p style="font-size: 12px; font-weight: bold;">{{ $customer->name }}</p>
        <p>الهاتف: {{ $customer->phone }} | الفرع: {{ $customer->branch->name ?? $customer->branch_code }}</p>
    </div>

    <!-- Financial Summary -->
    <div class="financial-summary">
        <div class="financial-item">
            <div style="font-size: 8px;">مدين</div>
            <div style="font-size: 14px; font-weight: bold;">{{ number_format($debit, 2) }}</div>
        </div>
        <div class="financial-item">
            <div style="font-size: 8px;">دائن</div>
            <div style="font-size: 14px; font-weight: bold;">{{ number_format($credit, 2) }}</div>
        </div>
        <div class="financial-item">
            <div style="font-size: 8px;">الرصيد</div>
            <div style="font-size: 14px; font-weight: bold;">{{ number_format(abs($balance), 2) }}</div>
        </div>
        <br>
        <span class="debt-status {{ $isDebtor ? 'debtor' : 'cleared' }}">
            {{ $isDebtor ? 'مديون' : 'رصيد خالص' }}
        </span>
    </div>

    <!-- Statistics -->
    <div class="section-title">إحصائيات عامة</div>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-value">{{ $sentCount + $receivedCount }}</div>
            <div class="stat-label">إجمالي الشحنات</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $sentCount }}</div>
            <div class="stat-label">شحنات مرسلة</div>
        </div>
        <div class="stat-item">
            <div class="stat-value">{{ $receivedCount }}</div>
            <div class="stat-label">شحنات مستقبلة</div>
        </div>
    </div>

    <!-- Sent Shipments -->
    <div class="section-title">الشحنات المرسلة ({{ $sentCount }})</div>
    @if($sentShipments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 12%;">التاريخ</th>
                    <th style="width: 20%;">الفرع المستقبل</th>
                    <th style="width: 15%;">المبلغ الإجمالي</th>
                    <th style="width: 18%;">طريقة الدفع</th>
                    <th style="width: 12%;">المدفوع</th>
                    <th style="width: 15%;">المتبقي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sentShipments as $shipment)
                    @php
                        $paid = $shipment->payments->sum('amount');
                        $remaining = ($shipment->total_amount ?? 0) - $paid;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $shipment->created_at->format('Y-m-d') }}</td>
                        <td>{{ $shipment->receiverBranch->name ?? 'غير محدد' }}</td>
                        <td>{{ number_format($shipment->total_amount ?? 0, 2) }}</td>
                        <td>
                            @if($shipment->payment_method == 'prepaid')
                                <span class="payment-badge payment-prepaid">مدفوع مقدماً</span>
                            @elseif($shipment->payment_method == 'cod')
                                <span class="payment-badge payment-cod">الدفع عند الاستلام</span>
                            @else
                                <span class="payment-badge payment-credit">آجل</span>
                            @endif
                        </td>
                        <td>{{ number_format($paid, 2) }}</td>
                        <td style="color: {{ $remaining > 0 ? '#ef4444' : '#10b981' }}; font-weight: bold;">
                            {{ number_format($remaining, 2) }}
                        </td>
                    </tr>
                @endforeach
                <tr style="background: #f3f4f6; font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding-right: 10px;">الإجمالي</td>
                    <td>{{ number_format($sentTotal, 2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>

        <!-- Sent Shipments Payment Statistics -->
        <div style="margin: 10px 0; padding: 8px; background: #f9fafb; border-radius: 5px;">
            <span style="margin-left: 15px;"><strong>مدفوع مقدماً:</strong> {{ $sentPrepaid }}</span>
            <span style="margin-left: 15px;"><strong>الدفع عند الاستلام:</strong> {{ $sentCod }}</span>
            <span><strong>آجل:</strong> {{ $sentCustomerCredit }}</span>
        </div>
    @else
        <div class="no-data">لا توجد شحنات مرسلة</div>
    @endif

    <!-- Received Shipments -->
    <div class="section-title">الشحنات المستقبلة ({{ $receivedCount }})</div>
    @if($receivedShipments->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th style="width: 12%;">التاريخ</th>
                    <th style="width: 20%;">الفرع المرسل</th>
                    <th style="width: 15%;">المبلغ الإجمالي</th>
                    <th style="width: 18%;">طريقة الدفع</th>
                    <th style="width: 12%;">المدفوع</th>
                    <th style="width: 15%;">المتبقي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receivedShipments as $shipment)
                    @php
                        $paid = $shipment->payments->sum('amount');
                        $remaining = ($shipment->total_amount ?? 0) - $paid;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $shipment->created_at->format('Y-m-d') }}</td>
                        <td>{{ $shipment->senderBranch->name ?? 'غير محدد' }}</td>
                        <td>{{ number_format($shipment->total_amount ?? 0, 2) }}</td>
                        <td>
                            @if($shipment->payment_method == 'prepaid')
                                <span class="payment-badge payment-prepaid">مدفوع مقدماً</span>
                            @elseif($shipment->payment_method == 'cod')
                                <span class="payment-badge payment-cod">الدفع عند الاستلام</span>
                            @else
                                <span class="payment-badge payment-credit">آجل</span>
                            @endif
                        </td>
                        <td>{{ number_format($paid, 2) }}</td>
                        <td style="color: {{ $remaining > 0 ? '#ef4444' : '#10b981' }}; font-weight: bold;">
                            {{ number_format($remaining, 2) }}
                        </td>
                    </tr>
                @endforeach
                <tr style="background: #f3f4f6; font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding-right: 10px;">الإجمالي</td>
                    <td>{{ number_format($receivedTotal, 2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>

        <!-- Received Shipments Payment Statistics -->
        <div style="margin: 10px 0; padding: 8px; background: #f9fafb; border-radius: 5px;">
            <span style="margin-left: 15px;"><strong>مدفوع مقدماً:</strong> {{ $receivedPrepaid }}</span>
            <span style="margin-left: 15px;"><strong>الدفع عند الاستلام:</strong> {{ $receivedCod }}</span>
            <span><strong>آجل:</strong> {{ $receivedCustomerCredit }}</span>
        </div>
    @else
        <div class="no-data">لا توجد شحنات مستقبلة</div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>تم إنشاء التقرير في: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>هذا التقرير تم إنشاؤه بواسطة نظام إدارة الطرود</p>
    </div>

</body>

</html>