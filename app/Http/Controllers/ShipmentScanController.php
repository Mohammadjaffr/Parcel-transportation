<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ShipmentScanController extends Controller
{
    /**
     * عرض صفحة استلام الشحنات بالباركود
     */
    public function incoming()
    {
        return view('pages.shipment.scan.index');
    }


    /**
     * استلام الشحنة في الفرع الحالي بواسطة bond_number
     */
    public function receive(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bond_number' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'bond_number.required' =>
                'يرجى مسح باركود سند الشحنة.',
        ]);


        /** @var \App\Models\User $user */
        $user = $request->user();


        $bondNumber = mb_strtoupper(
            trim($validated['bond_number'])
        );


        try {

            $result = DB::transaction(function () use (
                $bondNumber,
                $user
            ) {

                /*
                 * نبحث فقط عن شحنة:
                 *
                 * 1- رقم سندها مطابق.
                 * 2- موجهة إلى فرع الموظف الحالي.
                 */

                $shipment = Shipment::query()
                    ->with([
                        'senderCustomer:id,name,phone',
                        'receiverCustomer:id,name,phone',
                        'senderBranch:id,name,code',
                        'receiverBranch:id,name,code',
                    ])
                    ->where(
                        'bond_number',
                        $bondNumber
                    )
                    ->where(
                        'receiver_branch_id',
                        $user->branch_id
                    )
                    ->lockForUpdate()
                    ->first();


                /*
                 * السند غير موجود
                 * أو الشحنة ليست موجهة لهذا الفرع.
                 */
                if (!$shipment) {

                    return [
                        'status' => 404,

                        'body' => [
                            'success' => false,

                            'message' =>
                                'السند غير موجود أو أن هذه الشحنة ليست موجهة إلى فرعك.',
                        ],
                    ];

                }


                /*
                 * تم استلام الشحنة سابقاً.
                 */
                if (
                    $shipment->status ===
                    'received_at_branch'
                ) {

                    return [
                        'status' => 409,

                        'body' => [
                            'success' => false,

                            'already_received' => true,

                            'message' =>
                                'تم استلام هذه الشحنة في الفرع مسبقاً.',

                            'shipment' => [
                                'bond_number' =>
                                    $shipment->bond_number,

                                'status' =>
                                    $shipment->status,
                            ],
                        ],
                    ];

                }


                /*
                 * لا نسمح باستلام شحنة منتهية.
                 */
                if (
                    in_array(
                        $shipment->status,
                        [
                            'delivered',
                            'cancelled',
                            'returned',
                        ],
                        true
                    )
                ) {

                    return [
                        'status' => 422,

                        'body' => [
                            'success' => false,

                            'message' =>
                                'لا يمكن استلام هذه الشحنة لأن حالتها الحالية منتهية.',
                        ],
                    ];

                }


                /*
                 * الاستلام مسموح فقط عندما تكون الشحنة في الطريق.
                 */
                if (
                    $shipment->status !==
                    'in_transit'
                ) {

                    return [
                        'status' => 422,

                        'body' => [
                            'success' => false,

                            'message' =>
                                'لا يمكن استلام الشحنة حالياً. يجب أن تكون حالتها "في الطريق".',
                        ],
                    ];

                }


                /*
                 * تنفيذ الاستلام.
                 */
                $shipment->update([
                    'status' =>
                        'received_at_branch',
                ]);

                // 4. الإشعارات والإجراءات الجانبية + Audit Trail
                $statusService = new \App\Services\ShipmentStatusService();
                $statusService->handleNotifications($shipment, 'received_at_branch', $user);
                $statusService->handlePackageSideEffects($shipment, 'received_at_branch', $user);

                \App\Services\AdminLoggerService::log(
                    'barcode_receive',
                    'Shipment',
                    $shipment->id,
                    "استلام شحنة بالباركود #{$shipment->bond_number}"
                );


                return [
                    'status' => 200,

                    'body' => [
                        'success' => true,

                        'message' =>
                            'تم استلام الشحنة في الفرع بنجاح.',

                        'shipment' => [

                            'id' =>
                                $shipment->id,

                            'bond_number' =>
                                $shipment->bond_number,

                            'status' =>
                                'received_at_branch',

                            'status_label' =>
                                'مستلمة في الفرع',

                            'sender' => [
                                'name' =>
                                    $shipment
                                        ->senderCustomer?->name
                                    ?? '---',

                                'phone' =>
                                    $shipment
                                        ->senderCustomer?->phone
                                    ?? '---',
                            ],

                            'receiver' => [
                                'name' =>
                                    $shipment
                                        ->receiverCustomer?->name
                                    ?? '---',

                                'phone' =>
                                    $shipment
                                        ->receiverCustomer?->phone
                                    ?? '---',
                            ],

                            'sender_branch' =>
                                $shipment
                                    ->senderBranch?->name
                                ?? '---',

                            'receiver_branch' =>
                                $shipment
                                    ->receiverBranch?->name
                                ?? '---',

                            'package_type' =>
                                $shipment->package_type
                                ?? 'طرد',

                            'details_url' =>
                                route(
                                    'shipment.incoming.show',
                                    $shipment->id
                                ),
                        ],
                    ],
                ];

            });


            return response()->json(
                $result['body'],
                $result['status']
            );

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'success' => false,

                'message' =>
                    'حدث خطأ أثناء تسجيل استلام الشحنة.',
            ], 500);

        }
    }
}