<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند رقم {{ $transaction->receipt_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 2mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'dejavusans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            direction: rtl;
            margin: 0;
            padding: 12px;
            background-color: #f1f5f9;
            color: #111827;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .border-bottom { border-bottom: 1px dashed #94a3b8; padding-bottom: 8px; margin-bottom: 8px; }
        .row { width: 100%; margin-bottom: 6px; display: table; }
        .row-cell { display: table-cell; }
        .bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            background-color: #e2e8f0;
        }
        .receipt-card {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 14px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .no-print {
            max-width: 80mm;
            margin: 0 auto 12px auto;
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 7px 12px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-print {
            background-color: #0284c7;
            color: #ffffff;
        }
        .btn-print:hover {
            background-color: #0369a1;
        }
        .btn-pdf {
            background-color: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }
        .btn-pdf:hover {
            background-color: #f8fafc;
        }
        .btn-close {
            background-color: #f1f5f9;
            color: #64748b;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .receipt-card {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body @if(!($isPdf ?? false)) onload="if(!window.location.search.includes('noprint')) { setTimeout(() => window.print(), 300); }" @endif>

    @if(!($isPdf ?? false))
    {{-- شريط أزرار الطباعة والتحميل السريع (يختفي في الطباعة تلقائياً) --}}
    <div class="no-print">
        <button type="button" onclick="window.print()" class="btn btn-print">
            🖨️ طباعة
        </button>
        <a href="{{ request()->fullUrlWithQuery(['format' => 'pdf']) }}" class="btn btn-pdf">
            📄 ملف PDF
        </a>
        <button type="button" onclick="window.close()" class="btn btn-close">
            ✕ إغلاق
        </button>
    </div>
    @endif

    <div class="receipt-card">
        {{-- رأس السند والشعار --}}
        <div class="text-center border-bottom">
            <h3 style="margin: 0 0 4px 0; font-size: 13px;">{{ $transaction->app->name ?? 'مُرسَل للنقل اللوجستي' }}</h3>
            <p style="margin: 0 0 4px 0; font-size: 10px; color: #475569;">فرع: {{ $transaction->branch->name ?? '-' }}</p>
            <div style="margin: 6px 0;">
                <span class="badge" style="{{ $transaction->type === 'income' ? 'background:#dcfce7; color:#15803d;' : 'background:#ffe4e6; color:#be123c;' }}">
                    {{ $transaction->type === 'income' ? 'سند قبض نقدية (وارد)' : 'سند صرف نقدية (منصرف)' }}
                </span>
            </div>
            <div class="bold" style="font-size: 12px; margin-top: 4px; letter-spacing: 0.5px;">
                {{ $transaction->receipt_number }}
            </div>
        </div>

        {{-- تفاصيل السند --}}
        <div style="margin: 10px 0;">
            <div class="row">
                <div class="row-cell bold" style="width: 35%;">التاريخ:</div>
                <div class="row-cell text-left">{{ $transaction->transaction_date->format('Y-m-d') }}</div>
            </div>
            <div class="row">
                <div class="row-cell bold" style="width: 35%;">التصنيف:</div>
                <div class="row-cell text-left">{{ $transaction->category->name ?? '-' }}</div>
            </div>
            <div class="row">
                <div class="row-cell bold" style="width: 35%;">طريقة الدفع:</div>
                <div class="row-cell text-left">{{ $transaction->payment_method === 'cash' ? 'نقداً (كاش الخزينة)' : 'تحويل بنكي' }}</div>
            </div>
            
            @if($transaction->reference_number)
            <div class="row">
                <div class="row-cell bold" style="width: 35%;">رقم المرجع:</div>
                <div class="row-cell text-left">{{ $transaction->reference_number }}</div>
            </div>
            @endif

            @if($transaction->notes)
            <div class="row">
                <div class="row-cell bold" style="width: 35%;">البيان:</div>
                <div class="row-cell text-left">{{ $transaction->notes }}</div>
            </div>
            @endif

            <div class="row">
                <div class="row-cell bold" style="width: 35%;">المسؤول:</div>
                <div class="row-cell text-left">{{ $transaction->user->name ?? '-' }}</div>
            </div>
        </div>

        {{-- المبلغ الإجمالي --}}
        <div class="border-bottom" style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #94a3b8; background: #fafafa; padding: 8px; border-radius: 6px;">
            <div class="row" style="margin: 0;">
                <div class="row-cell bold" style="font-size: 11px; vertical-align: middle;">المبلغ:</div>
                <div class="row-cell text-left bold" style="font-size: 14px; color: {{ $transaction->type === 'income' ? '#15803d' : '#be123c' }};">
                    {{ number_format($transaction->amount, 2) }} <span style="font-size: 10px; font-weight: normal;">ر.ي</span>
                </div>
            </div>
        </div>

        {{-- تذييل السند --}}
        <div class="text-center" style="margin-top: 12px; font-size: 9px; color: #64748b;">
            <p style="margin: 0 0 2px 0;">شكراً لتعاملكم معنا</p>
            <p style="margin: 0;">تاريخ الطباعة: {{ now()->format('Y-m-d h:i A') }}</p>
        </div>
    </div>

</body>
</html>