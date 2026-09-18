<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Exports\CashTransactionExport;
use App\Models\Branch;
use App\Models\CashCategory;
use App\Models\CashTransaction;
use App\Services\CashTransactionService;
use App\Services\ImageService;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use TCPDF;

class CashTransactionController extends Controller
{

    public function __construct(
        private CashTransactionService $cashService,
        private ImageService $imageService
    ) {}

    /**
     * عرض دفتر الصندوق، الكروت الإحصائية، وجدول المعاملات
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->type === 'super_admin';
        $isAdmin = $user->type === 'admin';

        // الموظف محصور بفرعه، بينما الإدارة والمدير يمكنهما الفلترة بين الفروع
        $selectedBranchId = $request->filled('branch_id') && ($isAdmin || $isSuperAdmin)
            ? (int) $request->branch_id
            : ($isSuperAdmin || $isAdmin ? null : (int) $user->branch_id);

        // تحديد نطاق التاريخ: افتراضياً الشهر الحالي عند الدخول لأول مرة لسرعة الأداء
        $isDefaultLoad = !$request->has('start_date') && !$request->has('end_date');

        if ($isDefaultLoad) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        } else {
            $startDate = $request->filled('start_date') ? $request->start_date : null;
            $endDate = $request->filled('end_date') ? $request->end_date : null;
        }

        $query = CashTransaction::with(['category', 'user', 'branch', 'source']);

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        if ($selectedBranchId) {
            $query->where('branch_id', $selectedBranchId);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('cash_category_id')) {
            $query->where('cash_category_id', $request->cash_category_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                // البحث الذكي: الاستفادة من فهرس السندات ذرياً قبل البحث في النصوص العامة
                if (preg_match('/^[A-Za-z0-9\-]+$/', $search)) {
                    $q->where('receipt_number', 'like', "{$search}%")
                      ->orWhere('receipt_number', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%");
                } else {
                    $q->where('receipt_number', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                }
            });
        }

        // استخراج الإحصائيات الفورية عبر استعلام تجميعي واحد فائق السرعة
        $analytics = (clone $query)->selectRaw("
            COUNT(id) as total_count,
            COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as total_income,
            COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as total_expense
        ")->first();

        $totalIncome = (float) $analytics->total_income;
        $totalExpense = (float) $analytics->total_expense;
        $netPeriod = $totalIncome - $totalExpense;
        $totalCount = (int) $analytics->total_count;

        $transactions = $query->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $branches = ($isAdmin || $isSuperAdmin) ? Branch::all() : collect();
        $categories = CashCategory::active()->orderBy('name')->get();

        if ($request->isMobile) {
            return view('mobile.pages.finance.cash_ledger.index', compact(
                'transactions',
                'totalIncome',
                'totalExpense',
                'netPeriod',
                'totalCount',
                'branches',
                'categories',
                'selectedBranchId',
                'startDate',
                'endDate'
            ));
        }

        return view('pages.finance.cash_ledger.index', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'netPeriod',
            'totalCount',
            'branches',
            'categories',
            'selectedBranchId',
            'startDate',
            'endDate'
        ));
    }
    public function export(Request $request)
    {
        $filters = [
            'from_date'         => $request->input('start_date'),
            'to_date'           => $request->input('end_date'),
            'branch_id'         => $request->input('branch_id'),
            'type'              => $request->input('type'),
            'cash_category_id'  => $request->input('cash_category_id'),
            'payment_method'    => $request->input('payment_method'),
            'search'            => $request->input('search'),
        ];
        $fileName = 'cash_ledger_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new CashTransactionExport($filters), $fileName);
    }

    /**
     * حفظ سند وارد أو منصرف جديد يدوياً
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $branchId = ($user->type === 'admin' && $request->filled('branch_id'))
            ? $request->branch_id
            : $user->branch_id;

        $validator = Validator::make($request->all(), [
            'type'             => 'required|in:income,expense',
            'cash_category_id' => 'required|exists:cash_categories,id',
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,bank_transfer',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'attachment'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
            'notes'            => 'nullable|string|max:1000',
        ], [
            'type.required'             => 'نوع السند مطلوب.',
            'cash_category_id.required' => 'التصنيف المالي مطلوب.',
            'cash_category_id.exists'   => 'التصنيف غير صالح.',
            'amount.required'           => 'المبلغ مطلوب.',
            'amount.min'                => 'يجب أن يكون المبلغ أكبر من صفر.',
            'payment_method.required'   => 'طريقة الدفع مطلوبة.',
            'transaction_date.required' => 'تاريخ السند مطلوب.',
            'attachment.max'            => 'الحد الأقصى لحجم المرفق هو 3 ميجابايت.',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            $data = $validator->validated();
            $data['branch_id'] = $branchId;

            $category = CashCategory::findOrFail($data['cash_category_id']);
            if ($category->type !== $data['type']) {
                return WebResponseClass::sendError('التصنيف المختار لا يطابق نوع السند المحدد (وارد / منصرف).');
            }

            if ($request->hasFile('attachment')) {
                $data['attachment_path'] = $this->imageService->saveImage($request->file('attachment'), 'cash_attachments');
            }

            if ($data['type'] === 'income') {
                $this->cashService->recordIncome($data);
                $message = 'تم تسجيل سند القبض وإضافته للصندوق بنجاح.';
            } else {
                $this->cashService->recordExpense($data);
                $message = 'تم تسجيل سند الصرف وخصمه من الصندوق بنجاح.';
            }

            if ($request->isMobile) {
                return WebResponseClass::sendResponse('تم بنجاح', $message, 'العودة لدفتر الصندوق', 'cash.ledger.index');
            }

            return WebResponseClass::sendResponse('تم بنجاح', $message);
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }

    /**
     * إرجاع تفاصيل المعاملة عبر JSON لعرضها داخل نافذة الـ Modal
     */
    public function showDetails($id)
    {
        $trx = CashTransaction::with(['category', 'user', 'branch', 'source'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $trx->id,
                'receipt_number'   => $trx->receipt_number,
                'type'             => $trx->type,
                'type_text'        => $trx->type === 'income' ? 'وارد (قبض)' : 'منصرف (صرف)',
                'category'         => $trx->category->name,
                'amount'           => number_format($trx->amount, 2),
                'payment_method'   => $trx->payment_method === 'cash' ? 'نقداً ' : 'تحويل بنكي / حوالة',
                'transaction_date' => $trx->transaction_date->format('Y-m-d'),
                'created_at'       => $trx->created_at->format('Y-m-d h:i A'),
                'branch'           => $trx->branch->name ?? '-',
                'user'             => $trx->user->name ?? '-',
                'reference_number' => $trx->reference_number ?? '-',
                'notes'            => $trx->notes ?? '-',
                'attachment_url'   => $trx->attachment_path ? asset($trx->attachment_path) : null,
            ]
        ]);
    }

    /**
     * طباعة إيصال مالي حراري للسند (HTML فائق السرعة مع خيار تصدير PDF)
     */
    public function printReceipt(Request $request, $id)
    {
        $trx = CashTransaction::with(['category', 'branch', 'user'])->findOrFail($id);

        // إذا طُلب التصدير كملف PDF صراحةً
        if ($request->get('format') === 'pdf' || $request->has('pdf')) {
            $pdf = new TCPDF('P', 'mm', [80, 160], true, 'UTF-8', false);
            $pdf->SetMargins(4, 4, 4);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->setRTL(true);
            $pdf->SetFont('dejavusans', '', 9);
            $pdf->AddPage();

            $html = view('pdf.transaction-receipt', ['transaction' => $trx, 'isPdf' => true])->render();
            $pdf->writeHTML($html, true, false, true, false, '');

            return $pdf->Output("Receipt-{$trx->receipt_number}.pdf", 'I');
        }

        // افتراضياً: عرض حراري فوري للمتصفح بأداء فائق واستهلاك 0MB من الذاكرة
        return view('pdf.transaction-receipt', ['transaction' => $trx, 'isPdf' => false]);
    }
}