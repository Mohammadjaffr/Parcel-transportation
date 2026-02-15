<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\ReceiptHeader;
use Illuminate\Http\Request;
use App\Classes\WebResponseClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReceiptHeaderController extends Controller
{
    /**
     * عرض صفحة إنشاء بيان استلام جديد
     */
    public function create()
    {
        $branches = Branch::where('code','!=',auth()->user()->branch_code)->get();
        $drivers  = Driver::all();

        return view('pages.receipts.create', compact('branches', 'drivers'));
    }

    /**
     * حفظ بيان الاستلام مع الطرود
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Header
            'number'             => ['required', 'string', 'max:255', 'unique:receipt_headers,number'],
            'source_branch_code' => ['required', 'exists:branches,code'],
            'driver_id'          => ['nullable', 'exists:drivers,id'],
            'driver_name'        => ['required', 'string', 'max:255'],
            'driver_phone'       => ['nullable', 'string', 'max:20'],
            'general_notes'      => ['nullable', 'string', 'max:1000'],

            // Items
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.number'         => ['required', 'string', 'max:255', 'distinct'],
            'items.*.sender_name'    => ['nullable', 'string', 'max:255'],
            'items.*.receiver_name'  => ['required', 'string', 'max:255'],
            'items.*.receiver_phone' => ['required', 'string', 'max:20'],
            'items.*.package_type'   => ['required', 'string', 'max:255'],
            'items.*.item_notes'     => ['nullable', 'string', 'max:500'],
        ], [
            'items.required'              => 'يجب إضافة طرد واحد على الأقل.',
            'items.min'                   => 'يجب إضافة طرد واحد على الأقل.',
            'items.*.number.required'     => 'رقم السند مطلوب لكل طرد.',
            'items.*.number.distinct'     => 'رقم السند مكرر، يجب أن تكون الأرقام مختلفة.',
            'items.*.receiver_name.required'  => 'اسم المستلم مطلوب.',
            'items.*.receiver_phone.required' => 'رقم هاتف المستلم مطلوب.',
            'items.*.package_type.required'   => 'نوع الطرد مطلوب.',
            'items.*.package_type.in'         => 'نوع الطرد غير صالح.',
            'source_branch_code.required'     => 'الفرع المرسل مطلوب.',
            'driver_name.required'            => 'اسم السائق مطلوب.',
            'number.required'                 => 'رقم السند مطلوب.',
            'number.unique'                   => 'رقم السند مستخدم مسبقاً.',
        ]);

        if ($validator->fails()) {
            return WebResponseClass::sendValidationError($validator);
        }

        try {
            DB::transaction(function () use ($request) {
                // Resolve driver: existing selection or auto-create
                if ($request->filled('driver_id')) {
                    $driverId = $request->driver_id;
                } else {
                    $driver = Driver::create([
                        'name'  => $request->driver_name,
                        'phone' => $request->driver_phone,
                    ]);
                    $driverId = $driver->id;
                }

                // Create header
                $header = ReceiptHeader::create([
                    'number'                 => $request->number,
                    'source_branch_code'     => $request->source_branch_code,
                    'driver_id'              => $driverId,
                    'destination_branch_code' => auth()->user()->branch_code,
                    'created_by'             => auth()->id(),
                    'general_notes'          => $request->general_notes,
                    'received_at'            => now(),
                ]);

                // Create items
                foreach ($request->items as $item) {
                    $header->items()->create([
                        'number'         => $item['number'],
                        'sender_name'    => $item['sender_name'] ?? null,
                        'receiver_name'  => $item['receiver_name'],
                        'receiver_phone' => $item['receiver_phone'],
                        'package_type'   => $item['package_type'],
                        'item_notes'     => $item['item_notes'] ?? null,
                    ]);
                }
            });

            return WebResponseClass::sendResponse(
                'تم الحفظ!',
                'تم حفظ بيان الاستلام والطرود بنجاح.',
                'حسناً',
                'receipts.create'
            );
        } catch (Exception $e) {
            return WebResponseClass::sendExceptionError($e);
        }
    }
}
