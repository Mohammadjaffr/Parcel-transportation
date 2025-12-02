<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Repositories\SpecialOrderRepository;

class SpecialOrderController extends Controller
{
    public function __Construct(private SpecialOrderRepository $specialOrderRepository)
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
        try {
            $validated['user_id'] = auth('sanctum')->id();
            $validated['created_by'] = 'app';
            // ارسال اشعار الى الداش بورد
            //
            //
            $specialOrder = $this->specialOrderRepository->store($validated);
            return ApiResponseClass::sendResponse($specialOrder, 'Special order created successfully');
        } catch (Exception $e) {
            return ApiResponseClass::sendError('Failed to create special order', $e->getMessage(), 500);
        }
    }


}
