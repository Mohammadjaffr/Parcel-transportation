<?php

namespace App\Exports;

use App\Models\CashTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CashTransactionExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents,
    WithChunkReading,
    WithColumnFormatting
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    // ─── حجم كل chunk: 500 صف لتوازن الذاكرة مع السرعة ─────────────────────
    public function chunkSize(): int
    {
        return 500;
    }

    // ─── تنسيق عمود المبلغ (E) كرقم بدل نص ─────────────────────────────────
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2, // #,##0.00
        ];
    }

    // ─── الاستعلام: select الأعمدة المطلوبة فقط + join بدل with() ──────────
    public function query(): Builder
    {
        $query = CashTransaction::query()
            ->select([
                'cash_transactions.id',
                'cash_transactions.receipt_number',
                'cash_transactions.transaction_date',
                'cash_transactions.type',
                'cash_transactions.payment_method',
                'cash_transactions.amount',
                'cash_transactions.reference_number',
                'cash_transactions.notes',
                'cash_categories.name   as category_name',
                'branches.name          as branch_name',
                'users.name             as user_name',
            ])
            ->leftJoin('cash_categories', 'cash_categories.id', '=', 'cash_transactions.cash_category_id')
            ->leftJoin('branches', 'branches.id', '=', 'cash_transactions.branch_id')
            ->leftJoin('users', 'users.id', '=', 'cash_transactions.user_id')
            ->orderByDesc('cash_transactions.transaction_date')
            ->orderByDesc('cash_transactions.id');

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('cash_transactions.transaction_date', '>=', $this->filters['from_date']);
        }
        if (!empty($this->filters['to_date'])) {
            $query->whereDate('cash_transactions.transaction_date', '<=', $this->filters['to_date']);
        }
        if (!empty($this->filters['branch_id'])) {
            $query->where('cash_transactions.branch_id', $this->filters['branch_id']);
        }
        if (!empty($this->filters['type'])) {
            $query->where('cash_transactions.type', $this->filters['type']);
        }
        if (!empty($this->filters['cash_category_id'])) {
            $query->where('cash_transactions.cash_category_id', $this->filters['cash_category_id']);
        }
        if (!empty($this->filters['payment_method'])) {
            $query->where('cash_transactions.payment_method', $this->filters['payment_method']);
        }
        if (!empty($this->filters['search'])) {
            $search = trim($this->filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('cash_transactions.receipt_number',  'like', "%{$search}%")
                  ->orWhere('cash_transactions.reference_number', 'like', "%{$search}%")
                  ->orWhere('cash_transactions.notes',           'like', "%{$search}%");
            });
        }

        return $query;
    }

    // ─── تعيين قيم كل صف ────────────────────────────────────────────────────
    public function headings(): array
    {
        return [
            'رقم السند',
            'التاريخ',
            'النوع',
            'طريقة الدفع',
            'المبلغ',
            'التصنيف',
            'الفرع',
            'المستخدم',
            'رقم المرجع',
            'ملاحظات',
        ];
    }

    public function map($row): array
    {
        return [
            $row->receipt_number,
            $row->transaction_date
                ? \Carbon\Carbon::parse($row->transaction_date)->format('Y-m-d')
                : '',
            $row->type === 'income' ? 'قبض ' : 'صرف ',
            $row->payment_method === 'cash' ? 'نقدي' : 'تحويل بنكي',
            (float) $row->amount,           // رقم حقيقي لا نص → أسرع وأدق
            $row->category_name ?? '—',
            $row->branch_name   ?? '—',
            $row->user_name     ?? '—',
            $row->reference_number ?? '—',
            $row->notes ?? '—',
        ];
    }

    // ─── الأحداث: تنسيق كامل بدون حلقات ────────────────────────────────────
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet   = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // ═══════════════════════════════════════════════════════
                // 1. RTL + تنسيق صف الرأس (استدعاء واحد)
                // ═══════════════════════════════════════════════════════
                $sheet->setRightToLeft(true);
                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E78']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF3A6EA3']]],
                ]);

                // ═══════════════════════════════════════════════════════
                // 2. تنسيق كامل النطاق دفعة واحدة (لا حلقة)
                //    - محاذاة + حدود خفيفة لكل صفوف البيانات
                // ═══════════════════════════════════════════════════════
                if ($lastRow >= 2) {
                    $dataRange = "A2:J{$lastRow}";
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_HAIR,
                                'color'       => ['argb' => 'FFE2E8F0'],
                            ],
                        ],
                    ]);
                }

                // ═══════════════════════════════════════════════════════
                // 3. ConditionalFormatting — يعمل داخل Excel لا PHP
                //    → لا حلقة → يتحمل ملايين الصفوف بدون تأثير
                // ═══════════════════════════════════════════════════════
                if ($lastRow >= 2) {

                    // ── وارد: صف أخضر فاتح (بناءً على قيمة عمود C) ──────────
                    $cfIncome = new Conditional();
                    $cfIncome->setConditionType(Conditional::CONDITION_EXPRESSION);
                    $cfIncome->addCondition('=TRIM($C2)="قبض"');
                    $cfIncome->getStyle()->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF166534']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDCFCE7']],
                    ]);

                    // ── منصرف: صف أحمر فاتح ──────────────────────────────────
                    $cfExpense = new Conditional();
                    $cfExpense->setConditionType(Conditional::CONDITION_EXPRESSION);
                    $cfExpense->addCondition('=TRIM($C2)="صرف"');
                    $cfExpense->getStyle()->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => 'FF991B1B']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFEE2E2']],
                    ]);

                    // ── Zebra Striping: صفوف متبادلة ─────────────────────────
                    $cfZebra = new Conditional();
                    $cfZebra->setConditionType(Conditional::CONDITION_EXPRESSION);
                    $cfZebra->addCondition('=MOD(ROW(),2)=0');
                    $cfZebra->getStyle()->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFF8FAFC');

                    // تطبيق على عمود C فقط للألوان (أسرع من A:J)
                    $sheet->setConditionalStyles(
                        "C2:C{$lastRow}",
                        [$cfIncome, $cfExpense]
                    );

                    // Zebra على كامل النطاق
                    $sheet->setConditionalStyles(
                        "A2:J{$lastRow}",
                        [$cfZebra]
                    );
                }

                // ═══════════════════════════════════════════════════════
                // 4. حساب الإحصائيات (استعلام تجميعي واحد)
                // ═══════════════════════════════════════════════════════
                $statsQuery = CashTransaction::query();
                if (!empty($this->filters['from_date'])) {
                    $statsQuery->whereDate('transaction_date', '>=', $this->filters['from_date']);
                }
                if (!empty($this->filters['to_date'])) {
                    $statsQuery->whereDate('transaction_date', '<=', $this->filters['to_date']);
                }
                if (!empty($this->filters['branch_id'])) {
                    $statsQuery->where('branch_id', $this->filters['branch_id']);
                }
                if (!empty($this->filters['cash_category_id'])) {
                    $statsQuery->where('cash_category_id', $this->filters['cash_category_id']);
                }
                if (!empty($this->filters['payment_method'])) {
                    $statsQuery->where('payment_method', $this->filters['payment_method']);
                }

                $stats = $statsQuery->selectRaw("
                    COALESCE(SUM(CASE WHEN type = 'income'  THEN amount ELSE 0 END), 0) AS total_income,
                    COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense
                ")->first();

                $totalIncome  = (float) $stats->total_income;
                $totalExpense = (float) $stats->total_expense;
                $net          = $totalIncome - $totalExpense;
                $isProfit     = $net >= 0;

                // ═══════════════════════════════════════════════════════
                // 5. الملخص المالي الاحترافي في نهاية الورقة
                // ═══════════════════════════════════════════════════════
                $r = $lastRow + 2;

                // فاصل بصري
                $sheet->mergeCells("A{$r}:J{$r}");
                $sheet->getStyle("A{$r}:J{$r}")->getFill()
                      ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFECF1F7');
                $sheet->getRowDimension($r)->setRowHeight(8);
                $r++;

                // بانر العنوان
                $sheet->mergeCells("A{$r}:J{$r}");
                $sheet->setCellValue("A{$r}", '  الملخص المالي للفترة');
                $sheet->getStyle("A{$r}:J{$r}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 13],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => [
                        'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF2E5F9A']],
                        'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF2E5F9A']],
                    ],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(32);
                $r++;

                // مسافة علوية
                $sheet->mergeCells("A{$r}:J{$r}");
                $sheet->getStyle("A{$r}:J{$r}")->getFill()
                      ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFAFCFF');
                $sheet->getRowDimension($r)->setRowHeight(5);
                $r++;

                // دالة مساعدة لصفوف الملخص
                $summaryRow = function (
                    int    $rowNum,
                    string $icon,
                    string $label,
                    string $value,
                    string $accentColor,
                    string $labelBg,
                    string $valueBg,
                    string $valueFg,
                    int    $fontSize  = 11,
                    bool   $emphasize = false
                ) use ($sheet): void {
                    $sheet->setCellValue("A{$rowNum}", $icon);
                    $sheet->getStyle("A{$rowNum}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => $fontSize + 1],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $accentColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $accentColor]]],
                    ]);

                    $sheet->mergeCells("B{$rowNum}:E{$rowNum}");
                    $sheet->setCellValue("B{$rowNum}", $label);
                    $sheet->getStyle("B{$rowNum}:E{$rowNum}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => 'FF1E293B'], 'size' => $fontSize],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $labelBg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sheet->mergeCells("F{$rowNum}:J{$rowNum}");
                    $sheet->setCellValue("F{$rowNum}", $value);
                    $sheet->getStyle("F{$rowNum}:J{$rowNum}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['argb' => $valueFg], 'size' => $emphasize ? $fontSize + 3 : $fontSize + 1],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $valueBg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $accentColor]]],
                    ]);
                    $sheet->getStyle("A{$rowNum}:J{$rowNum}")->getBorders()
                        ->getBottom()->setBorderStyle(Border::BORDER_HAIR)->getColor()->setARGB('FFDDE6F0');
                    $sheet->getRowDimension($rowNum)->setRowHeight($emphasize ? 36 : 28);
                };

                $summaryRow($r++, '↓', 'إجمالي الوارد',
                    number_format($totalIncome, 2) . '  ر.ي',
                    'FF059669', 'FFF0FDF8', 'FFD1FAE5', 'FF065F46');

                $summaryRow($r++, '↑', 'إجمالي المنصرف',
                    number_format($totalExpense, 2) . '  ر.ي',
                    'FFE11D48', 'FFFFF1F3', 'FFFECDD3', 'FF9F1239');

                // فاصل رقيق
                $sheet->mergeCells("A{$r}:J{$r}");
                $sheet->getStyle("A{$r}:J{$r}")->getFill()
                      ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE2EBF5');
                $sheet->getRowDimension($r)->setRowHeight(3);
                $r++;

                $summaryRow($r++,
                    $isProfit ? '✓' : '!',
                    $isProfit ? 'صافي الربح' : 'صافي الخسارة',
                    ($isProfit ? '+' : '-') . number_format(abs($net), 2) . '  ر.ي',
                    $isProfit ? 'FF047857' : 'FFBE123C',
                    $isProfit ? 'FFD1FAE5' : 'FFFECDD3',
                    $isProfit ? 'FFECFDF5' : 'FFFFF1F2',
                    $isProfit ? 'FF022C22' : 'FF4C0519',
                    12, true
                );

                // مسافة سفلية
                $sheet->mergeCells("A{$r}:J{$r}");
                $sheet->getStyle("A{$r}:J{$r}")->getFill()
                      ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFAFCFF');
                $sheet->getRowDimension($r)->setRowHeight(5);
                $r++;

                // تاريخ الإصدار
                $sheet->mergeCells("A{$r}:J{$r}");
                $sheet->setCellValue("A{$r}", '🕒   تاريخ إصدار الكشف:   ' . now()->format('Y-m-d  |  H:i'));
                $sheet->getStyle("A{$r}:J{$r}")->applyFromArray([
                    'font'      => ['color' => ['argb' => 'FF64748B'], 'size' => 10, 'italic' => true],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8FAFC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(20);
                $r++;

                // تذييل النظام
                $sheet->mergeCells("A{$r}:J{$r}");
                $sheet->setCellValue("A{$r}", 'تم التصدير باستخدام نظام مرسل   |   للتواصل أو طلب النظام:  967781152674');
                $sheet->getStyle("A{$r}:J{$r}")->applyFromArray([
                    'font'      => ['italic' => true, 'color' => ['argb' => 'FFCBD5E1'], 'size' => 9],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF2E5F9A']]],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(20);
            },
        ];
    }
}
