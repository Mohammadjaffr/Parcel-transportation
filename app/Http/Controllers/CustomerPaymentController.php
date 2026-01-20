<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\CustomerPayment;
use App\Models\Shipment;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerPaymentController extends Controller
{
    /**
     * تسجيل دفعة جديدة للشحنة
     */
    public function store(Request $request, $shipmentId)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // جلب الشحنة
        $shipment = Shipment::findOrFail($shipmentId);
        
        // التحقق من أن الشحنة بنظام آجل (دين)
        if ($shipment->payment_method !== 'customer_credit') {
            return WebResponseClass::sendError(
                'هذه الشحنة ليست بنظام الدين',
                'خطأ'
            );
        }
        
        // حساب المبلغ المتبقي
        $totalPaid = $shipment->payments()->sum('amount');
        $remainingAmount = $shipment->total_amount - $totalPaid;
        
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . $remainingAmount
            ],
            'payment_method' => 'required|in:cash,bank_transfer',
            'reference_number' => 'required_if:payment_method,bank_transfer|nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'amount.max' => 'المبلغ أكبر من المبلغ المتبقي',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
            'reference_number.required_if' => 'رقم المرجع مطلوب عند التحويل البنكي',
            'attachment.mimes' => 'الملف يجب أن يكون صورة أو PDF',
            'attachment.max' => 'حجم الملف يجب ألا يتجاوز 2 ميجابايت',
        ]);
        
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        
        try {
            DB::beginTransaction();
            
            $data = $validator->validated();
            
            // رفع المرفق إذا كان موجوداً
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $shipmentId . '.' . $file->getClientOriginalExtension();
                $attachmentPath = $file->storeAs('customer_payments', $fileName, 'public');
            }
            
            // إنشاء سجل الدفعة
            $payment = CustomerPayment::create([
                'shipment_id' => $shipmentId,
                'customer_id' => $shipment->sender_customer_id,
                'branch_code' => $user->branch_code,
                'amount' => $data['amount'],
                'payment_date' => now()->toDateString(),
                'payment_method' => $data['payment_method'],
                'attachment_path' => $attachmentPath,
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
            
            // تسجيل الدفعة في الصندوق المالي (Cash Box Integration)
            // Note: This needs to propagate exception to rollback DB transaction if it fails
            TransactionService::recordShipmentPayment(
                shipment: $shipment,
                amount: $data['amount'],
                branchCode: $user->branch_code
            );
            
            // تحديث حالة الدين للشحنة إذا تم السداد بالكامل
            $newTotalPaid = $shipment->payments()->sum('amount');
            if ($newTotalPaid >= $shipment->total_amount) {
                $shipment->update([
                    'customer_debt_status' => 'fully_paid'
                ]);
            } elseif ($newTotalPaid > 0) {
                $shipment->update([
                    'customer_debt_status' => 'partially_paid'
                ]);
            }

            
            DB::commit();
            
            return WebResponseClass::sendResponse(
                'تم التسجيل!',
                'تم تسجيل الدفعة بنجاح.',
                'حسناً'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // حذف الملف المرفوع في حالة الخطأ
            if (isset($attachmentPath) && $attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            
            return WebResponseClass::sendExceptionError($e);
        }
    }
    
    /**
     * عرض دفعات شحنة معينة
     */
    public function index($shipmentId)
    {
        $shipment = Shipment::with(['payments' => function ($query) {
            $query->latest();
        }])->findOrFail($shipmentId);
        
        $totalPaid = $shipment->payments->sum('amount');
        $remainingAmount = $shipment->total_amount - $totalPaid;
        
        return view('pages.customer_payments.index', compact('shipment', 'totalPaid', 'remainingAmount'));
    }
    
    /**
     * حذف دفعة
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $payment = CustomerPayment::findOrFail($id);
            $shipment = $payment->shipment;
            
            // حذف الملف المرفق إذا كان موجوداً
            if ($payment->attachment_path) {
                Storage::disk('public')->delete($payment->attachment_path);
            }
            
            $payment->delete();
            
            // تحديث حالة الدين للشحنة
            $totalPaid = $shipment->payments()->sum('amount');
            $remainingAmount = $shipment->total_amount - $totalPaid;
            
            if ($remainingAmount <= 0) {
                // تم الدفع بالكامل
                $shipment->update([
                    'customer_debt_status' => 'fully_paid'
                ]);
            } elseif ($totalPaid > 0) {
                // دفع جزئي
                $shipment->update([
                    'customer_debt_status' => 'partially_paid'
                ]);
            } else {
                // لم يتم الدفع
                $shipment->update([
                    'customer_debt_status' => 'pending'
                ]);
            }

            
            DB::commit();
            
            return WebResponseClass::sendResponse(
                'تم الحذف!',
                'تم حذف الدفعة بنجاح.',
                'حسناً'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
