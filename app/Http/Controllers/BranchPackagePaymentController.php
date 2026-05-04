<?php

namespace App\Http\Controllers;

use App\Classes\WebResponseClass;
use App\Models\BranchPackagePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BranchPackagePaymentController extends Controller
{
    /**
     * تسجيل دفعة جديدة للحزمة
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'branch_shipment_package_id' => 'required|exists:branch_shipment_package,id',
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check,other',
            'bond_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'branch_shipment_package_id.required' => 'معرف الحزمة مطلوب',
            'branch_shipment_package_id.exists' => 'الحزمة غير موجودة',
            'paid_amount.required' => 'المبلغ المدفوع مطلوب',
            'paid_amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'paid_amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
            'payment_date.required' => 'تاريخ الدفع مطلوب',
            'payment_date.date' => 'تاريخ الدفع غير صحيح',
        ]);
        
        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }
        
        try {
            DB::beginTransaction();
            
            $data = $validator->validated();
            
            // إنشاء سجل الدفعة
            $payment = BranchPackagePayment::create([
                'branch_shipment_package_id' => $data['branch_shipment_package_id'],
                'paid_amount' => $data['paid_amount'],
                'payment_method' => $data['payment_method'],
                'bond_number' => $data['bond_number'] ?? null,
                'payment_date' => $data['payment_date'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $user->id,
            ]);
            
            DB::commit();
            
            return WebResponseClass::sendResponse(
                'تم التسجيل!',
                'تم تسجيل الدفعة بنجاح.',
                'حسناً'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            return WebResponseClass::sendExceptionError($e);
        }
    }
    
    /**
     * عرض دفعات حزمة معينة
     */
    public function index($branchShipmentPackageId)
    {
        $payments = BranchPackagePayment::where('branch_shipment_package_id', $branchShipmentPackageId)
            ->with('creator')
            ->latest()
            ->get();
        
        $totalPaid = $payments->sum('paid_amount');
        
        return response()->json([
            'success' => true,
            'payments' => $payments,
            'total_paid' => $totalPaid,
        ]);
    }
    
    /**
     * حذف دفعة
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $payment = BranchPackagePayment::findOrFail($id);
            $payment->delete();
            
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
